<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tabungan extends CI_Controller
{

    // Property declarations for autoloaded models to prevent PHP 8.2+ deprecation warnings
    public $Santri_model;
    public $Tabungan_model;
    public $User_model;
    public $Menu_model;
    public $Transaksi_model;
    public $Ustadz_model;
    public $Kantin_model;
    public $Activity_log_model;

    public function __construct()
    {
        parent::__construct();
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['url', 'form']);
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
        if (!in_array($this->session->userdata('role'), ['admin', 'keuangan'])) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses ke halaman ini');
            redirect('dashboard');
        }
        $this->load->model(['Tabungan_model', 'Santri_model', 'Kantin_model']);
    }

    // Helper function untuk mengubah format rupiah ke angka
    private function rupiah_to_number($rupiah_string)
    {
        if (empty($rupiah_string)) return 0;
        // Hapus semua karakter kecuali angka
        return (int) preg_replace('/[^\d]/', '', $rupiah_string);
    }

    public function index()
    {
        $kantin_id = $this->session->userdata('kantin_id');

        $data['title'] = 'Data Tabungan - ' . $this->session->userdata('kantin_nama');
        $data['kantin_info'] = $this->Kantin_model->get_kantin($kantin_id);

        if ($this->session->userdata('role') == 'admin') {
            $data['santri'] = $this->Santri_model->get_santri_with_tabungan(NULL, $kantin_id);
        } else {
            $data['santri'] = $this->Santri_model->get_santri_with_tabungan($this->session->userdata('santri_id'), $kantin_id);
        }

        $this->load->view('templates/header', $data);
        $this->load->view('tabungan/index', $data);
        $this->load->view('templates/footer');
    }
    public function setoran()
    {
        $kantin_id = $this->session->userdata('kantin_id');

        $data['title'] = 'Setoran Tabungan - ' . $this->session->userdata('kantin_nama');
        $data['kantin_info'] = $this->Kantin_model->get_kantin($kantin_id);
        $data['santri'] = $this->Santri_model->get_santri_with_tabungan(NULL, $kantin_id);

        if ($this->input->method() === 'post') {
            // Unformat input sebelum validasi
            $_POST['jumlah_tabungan'] = $this->rupiah_to_number($this->input->post('jumlah_tabungan'));
            $_POST['jumlah_jajan'] = $this->rupiah_to_number($this->input->post('jumlah_jajan'));

            $this->form_validation->set_rules('santri_id', 'Santri', 'required|numeric');
            $this->form_validation->set_rules('jumlah_tabungan', 'Jumlah Tabungan', 'numeric|greater_than_equal_to[0]');
            $this->form_validation->set_rules('jumlah_jajan', 'Jumlah Jajan', 'numeric|greater_than_equal_to[0]');
            if ($this->form_validation->run()) {
                $santri_id = $this->input->post('santri_id');
                $jumlah_tabungan = $this->input->post('jumlah_tabungan');
                $jumlah_jajan = $this->input->post('jumlah_jajan');
                $keterangan = $this->input->post('keterangan');

                // Validasi santri milik kantin ini
                $santri = $this->Santri_model->get_santri_with_tabungan($santri_id, $kantin_id);
                if (!$santri) {
                    $this->session->set_flashdata('error', 'Santri tidak ditemukan');
                    redirect('tabungan/setoran');
                }

                if ($jumlah_tabungan == 0 && $jumlah_jajan == 0) {
                    $this->session->set_flashdata('error', 'Minimal salah satu jumlah (Tabungan atau Jajan) harus diisi');
                    redirect('tabungan/setoran');
                }

                $this->db->trans_start();
                $current_balance = $this->Tabungan_model->get_saldo($santri_id);
                $new_balance = [
                    'saldo_tabungan' => $current_balance['saldo_tabungan'] + $jumlah_tabungan,
                    'saldo_jajan' => $current_balance['saldo_jajan'] + $jumlah_jajan
                ];
                $update_saldo = $this->Tabungan_model->update_saldo($santri_id, $new_balance);
                if ($update_saldo) {
                    if ($jumlah_tabungan > 0) {
                        $transaksi_tabungan = [
                            'santri_id' => $santri_id,
                            'jenis' => 'setoran',
                            'kategori' => 'tabungan',
                            'jumlah' => $jumlah_tabungan,
                            'keterangan' => $keterangan,
                            'admin_id' => $this->session->userdata('user_id')
                        ];
                        $this->Tabungan_model->record_transaksi($transaksi_tabungan);
                    }
                    if ($jumlah_jajan > 0) {
                        $transaksi_jajan = [
                            'santri_id' => $santri_id,
                            'jenis' => 'setoran',
                            'kategori' => 'jajan',
                            'jumlah' => $jumlah_jajan,
                            'keterangan' => $keterangan,
                            'admin_id' => $this->session->userdata('user_id')
                        ];
                        $this->Tabungan_model->record_transaksi($transaksi_jajan);
                    }
                }
                $this->db->trans_complete();
                if ($this->db->trans_status() === FALSE) {
                    $this->session->set_flashdata('error', 'Gagal menyimpan setoran. Silakan coba lagi.');
                } else {
                    // Log activity untuk setoran tabungan
                    $total_setoran = $jumlah_tabungan + $jumlah_jajan;
                    $log_result = $this->Activity_log_model->log_financial('SETORAN_TABUNGAN', $total_setoran, [
                        'santri_id' => $santri_id,
                        'santri_nama' => $santri->nama,
                        'jumlah_tabungan' => $jumlah_tabungan,
                        'jumlah_jajan' => $jumlah_jajan,
                        'keterangan' => $keterangan,
                        'saldo_sebelum' => $current_balance,
                        'saldo_sesudah' => $new_balance
                    ], 'success');

                    // Debug: log hasil insert
                    if ($log_result) {
                        log_message('info', 'Activity log berhasil disimpan untuk setoran tabungan');
                    } else {
                        log_message('error', 'Activity log gagal disimpan untuk setoran tabungan');
                    }

                    $this->session->set_flashdata('success', 'Setoran berhasil disimpan');
                    redirect('tabungan/setoran');
                }
            }
        }
        $this->load->view('templates/header', $data);
        $this->load->view('tabungan/setoran', $data);
        $this->load->view('templates/footer');
    }
    public function penarikan()
    {
        if (!in_array($this->session->userdata('role'), ['admin', 'keuangan'])) {
            redirect('tabungan');
        }

        $kantin_id = $this->session->userdata('kantin_id');

        // Unformat jumlah sebelum validasi
        if ($this->input->method() === 'post') {
            $_POST['jumlah'] = $this->rupiah_to_number($this->input->post('jumlah'));
        }
        $this->form_validation->set_rules('santri_id', 'Santri', 'required');
        $this->form_validation->set_rules('jumlah', 'Jumlah', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('kategori', 'Kategori', 'required|in_list[tabungan,jajan]');
        $this->form_validation->set_rules('keterangan', 'Keterangan', 'required');

        if ($this->form_validation->run() == FALSE) {
            $data['title'] = 'Penarikan Tabungan - ' . $this->session->userdata('kantin_nama');
            $data['kantin_info'] = $this->Kantin_model->get_kantin($kantin_id);
            $data['santri'] = $this->Santri_model->get_santri_with_tabungan(NULL, $kantin_id);
            $this->load->view('templates/header', $data);
            $this->load->view('tabungan/penarikan', $data);
            $this->load->view('templates/footer');
        } else {
            $santri_id = $this->input->post('santri_id');
            $jumlah = $this->input->post('jumlah');
            $kategori = $this->input->post('kategori');
            $keterangan = $this->input->post('keterangan');

            // Validasi santri milik kantin ini
            $santri = $this->Santri_model->get_santri_with_tabungan($santri_id, $kantin_id);
            if (!$santri) {
                $this->session->set_flashdata('error', 'Santri tidak ditemukan');
                redirect('tabungan/penarikan');
            }

            $tabungan = $this->Tabungan_model->get_tabungan($santri_id);
            $saldo = $kategori == 'tabungan' ? $tabungan->saldo_tabungan : $tabungan->saldo_jajan;
            if ($saldo < $jumlah) {
                $this->session->set_flashdata('error', 'Saldo ' . $kategori . ' tidak mencukupi');
                redirect('tabungan/penarikan');
            }

            $this->db->trans_start();
            $new_balance = [
                'saldo_tabungan' => $kategori == 'tabungan' ?
                    $tabungan->saldo_tabungan - $jumlah :
                    $tabungan->saldo_tabungan,
                'saldo_jajan' => $kategori == 'jajan' ?
                    $tabungan->saldo_jajan - $jumlah :
                    $tabungan->saldo_jajan
            ];
            $update_saldo = $this->Tabungan_model->update_saldo($santri_id, $new_balance);
            if ($update_saldo) {
                $transaksi = [
                    'santri_id' => $santri_id,
                    'jenis' => 'penarikan',
                    'kategori' => $kategori,
                    'jumlah' => $jumlah,
                    'keterangan' => $keterangan,
                    'admin_id' => $this->session->userdata('user_id')
                ];
                $this->Tabungan_model->record_transaksi($transaksi);
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('error', 'Penarikan gagal dilakukan');
            } else {
                // Log activity untuk penarikan tabungan
                $this->Activity_log_model->log_financial('PENARIKAN_TABUNGAN', $jumlah, [
                    'santri_id' => $santri_id,
                    'santri_nama' => $santri->nama,
                    'kategori' => $kategori,
                    'jumlah' => $jumlah,
                    'keterangan' => $keterangan,
                    'saldo_sebelum' => $saldo,
                    'saldo_sesudah' => $saldo - $jumlah
                ], 'success');

                $this->session->set_flashdata('success', 'Penarikan ' . $kategori . ' berhasil dilakukan');
            }
            redirect('tabungan/penarikan');
        }
    }
    public function transfer_kategori()
    {
        if (!in_array($this->session->userdata('role'), ['admin', 'keuangan'])) {
            redirect('tabungan');
        }

        $kantin_id = $this->session->userdata('kantin_id');

        $this->form_validation->set_rules('santri_id', 'Santri', 'required');
        $this->form_validation->set_rules('jumlah', 'Jumlah', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('dari_kategori', 'Kategori Asal', 'required|in_list[tabungan,jajan]');
        $this->form_validation->set_rules('ke_kategori', 'Kategori Tujuan', 'required|in_list[tabungan,jajan]');

        if ($this->form_validation->run() == FALSE) {
            $data['title'] = 'Transfer Antar Kategori - ' . $this->session->userdata('kantin_nama');
            $data['kantin_info'] = $this->Kantin_model->get_kantin($kantin_id);
            $data['santri'] = $this->Santri_model->get_santri_with_tabungan(NULL, $kantin_id);
            $this->load->view('templates/header', $data);
            $this->load->view('tabungan/transfer_kategori', $data);
            $this->load->view('templates/footer');
        } else {
            $santri_id = $this->input->post('santri_id');
            $jumlah = $this->input->post('jumlah');
            $dari_kategori = $this->input->post('dari_kategori');
            $ke_kategori = $this->input->post('ke_kategori');

            // Validasi santri milik kantin ini
            $santri = $this->Santri_model->get_santri_with_tabungan($santri_id, $kantin_id);
            if (!$santri) {
                $this->session->set_flashdata('error', 'Santri tidak ditemukan');
                redirect('tabungan/transfer_kategori');
            }

            $tabungan = $this->Tabungan_model->get_tabungan($santri_id);
            $saldo = $dari_kategori == 'tabungan' ? $tabungan->saldo_tabungan : $tabungan->saldo_jajan;
            if ($saldo < $jumlah) {
                $this->session->set_flashdata('error', 'Saldo ' . $dari_kategori . ' tidak mencukupi');
                redirect('tabungan/transfer_kategori');
            }

            if ($this->Tabungan_model->transfer_kategori($santri_id, $jumlah, $dari_kategori, $ke_kategori)) {
                // Log activity untuk transfer antar kategori
                $this->Activity_log_model->log_financial('TRANSFER_KATEGORI', $jumlah, [
                    'santri_id' => $santri_id,
                    'santri_nama' => $santri->nama,
                    'dari_kategori' => $dari_kategori,
                    'ke_kategori' => $ke_kategori,
                    'jumlah' => $jumlah,
                    'saldo_sebelum' => $saldo,
                    'saldo_sesudah' => $saldo - $jumlah
                ], 'success');

                $this->session->set_flashdata('success', 'Transfer dari ' . $dari_kategori . ' ke ' . $ke_kategori . ' berhasil dilakukan');
            } else {
                $this->session->set_flashdata('error', 'Transfer gagal dilakukan');
            }
            redirect('tabungan/transfer_kategori');
        }
    }
    public function transfer_antar_santri()
    {
        if (!in_array($this->session->userdata('role'), ['admin', 'keuangan'])) {
            redirect('tabungan');
        }

        $kantin_id = $this->session->userdata('kantin_id');

        $this->form_validation->set_rules('santri_pengirim_id', 'Santri Pengirim', 'required');
        $this->form_validation->set_rules('santri_penerima_id', 'Santri Penerima', 'required');
        $this->form_validation->set_rules('jumlah', 'Jumlah', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('kategori', 'Kategori', 'required|in_list[tabungan,jajan]');
        $this->form_validation->set_rules('keterangan', 'Keterangan', 'trim');

        if ($this->form_validation->run() == FALSE) {
            $data['title'] = 'Transfer Antar Santri - ' . $this->session->userdata('kantin_nama');
            $data['kantin_info'] = $this->Kantin_model->get_kantin($kantin_id);
            $data['santri'] = $this->Santri_model->get_santri_with_tabungan(NULL, $kantin_id);
            $this->load->view('templates/header', $data);
            $this->load->view('tabungan/transfer_antar_santri', $data);
            $this->load->view('templates/footer');
        } else {
            $santri_pengirim_id = $this->input->post('santri_pengirim_id');
            $santri_penerima_id = $this->input->post('santri_penerima_id');
            $jumlah = $this->input->post('jumlah');
            $kategori = $this->input->post('kategori');
            $keterangan = $this->input->post('keterangan');

            // Validasi santri pengirim dan penerima tidak sama
            if ($santri_pengirim_id == $santri_penerima_id) {
                $this->session->set_flashdata('error', 'Santri pengirim dan penerima tidak boleh sama');
                redirect('tabungan/transfer_antar_santri');
            }

            // Validasi santri pengirim milik kantin ini
            $santri_pengirim = $this->Santri_model->get_santri_with_tabungan($santri_pengirim_id, $kantin_id);
            if (!$santri_pengirim) {
                $this->session->set_flashdata('error', 'Santri pengirim tidak ditemukan');
                redirect('tabungan/transfer_antar_santri');
            }

            // Validasi santri penerima milik kantin ini
            $santri_penerima = $this->Santri_model->get_santri_with_tabungan($santri_penerima_id, $kantin_id);
            if (!$santri_penerima) {
                $this->session->set_flashdata('error', 'Santri penerima tidak ditemukan');
                redirect('tabungan/transfer_antar_santri');
            }

            // Cek saldo santri pengirim
            $tabungan_pengirim = $this->Tabungan_model->get_tabungan($santri_pengirim_id);
            $saldo_pengirim = $kategori == 'tabungan' ? $tabungan_pengirim->saldo_tabungan : $tabungan_pengirim->saldo_jajan;
            if ($saldo_pengirim < $jumlah) {
                $this->session->set_flashdata('error', 'Saldo ' . $kategori . ' santri pengirim tidak mencukupi');
                redirect('tabungan/transfer_antar_santri');
            }

            if ($this->Tabungan_model->transfer_antar_santri($santri_pengirim_id, $santri_penerima_id, $jumlah, $kategori, $keterangan)) {
                // Log activity untuk transfer antar santri
                $this->Activity_log_model->log_financial('TRANSFER_ANTAR_SANTRI', $jumlah, [
                    'santri_pengirim_id' => $santri_pengirim_id,
                    'santri_pengirim_nama' => $santri_pengirim->nama,
                    'santri_penerima_id' => $santri_penerima_id,
                    'santri_penerima_nama' => $santri_penerima->nama,
                    'kategori' => $kategori,
                    'jumlah' => $jumlah,
                    'keterangan' => $keterangan,
                    'saldo_pengirim_sebelum' => $saldo_pengirim,
                    'saldo_pengirim_sesudah' => $saldo_pengirim - $jumlah
                ], 'success');

                $this->session->set_flashdata('success', 'Transfer antar santri berhasil dilakukan');
            } else {
                $this->session->set_flashdata('error', 'Transfer antar santri gagal dilakukan');
            }
            redirect('tabungan/transfer_antar_santri');
        }
    }
    public function riwayat()
    {
        $kantin_id = $this->session->userdata('kantin_id');

        $data['title'] = 'Riwayat Transaksi - ' . $this->session->userdata('kantin_nama');
        $data['kantin_info'] = $this->Kantin_model->get_kantin($kantin_id);

        if ($this->session->userdata('role') == 'admin') {
            $data['transaksi'] = $this->Tabungan_model->get_riwayat_transaksi(NULL, NULL, $kantin_id);
        } else {
            $data['transaksi'] = $this->Tabungan_model->get_riwayat_transaksi($this->session->userdata('santri_id'), NULL, $kantin_id);
        }

        $this->load->view('templates/header', $data);
        $this->load->view('tabungan/riwayat', $data);
        $this->load->view('templates/footer');
    }

    // AJAX endpoint untuk mencari santri
    public function search_santri()
    {
        $keyword = $this->input->get('q');
        $kantin_id = $this->session->userdata('kantin_id');
        $santri = $this->Santri_model->search_santri($keyword, $kantin_id);

        $result = [];
        foreach ($santri as $s) {
            $result[] = [
                'id' => $s->id,
                'text' => $s->nama . ' (' . $s->nomor_induk . ') - ' . $s->kelas,
                'nama' => $s->nama,
                'nomor_induk' => $s->nomor_induk,
                'kelas' => $s->kelas,
                'saldo_jajan' => $s->saldo_jajan ?? 0
            ];
        }

        echo json_encode($result);
    }

    // AJAX endpoint untuk mendapatkan info santri
    public function get_santri_info()
    {
        $santri_id = $this->input->post('santri_id');
        $kantin_id = $this->session->userdata('kantin_id');
        $santri = $this->Santri_model->get_santri_with_tabungan($santri_id, $kantin_id);

        if (!$santri) {
            echo json_encode(['error' => 'Santri tidak ditemukan']);
            return;
        }

        $result = [
            'id' => $santri->id,
            'nama' => $santri->nama,
            'nomor_induk' => $santri->nomor_induk,
            'kelas' => $santri->kelas,
            'saldo_tabungan' => $santri->saldo_tabungan ?? 0,
            'saldo_jajan' => $santri->saldo_jajan ?? 0
        ];

        echo json_encode($result);
    }

    // AJAX endpoint untuk mendapatkan saldo santri
    public function get_saldo($santri_id = null)
    {
        if ($santri_id === null) {
            $santri_id = $this->input->post('santri_id');
        }

        $kantin_id = $this->session->userdata('kantin_id');

        // Validasi santri milik kantin ini
        $santri = $this->Santri_model->get_santri_with_tabungan($santri_id, $kantin_id);
        if (!$santri) {
            echo json_encode(['error' => 'Santri tidak ditemukan']);
            return;
        }

        $saldo = $this->Tabungan_model->get_saldo($santri_id);
        $total_saldo = (int)$saldo['saldo_tabungan'] + (int)$saldo['saldo_jajan'];
        echo json_encode([
            'saldo_tabungan' => (int)$saldo['saldo_tabungan'],
            'saldo_jajan' => (int)$saldo['saldo_jajan'],
            'total_saldo' => $total_saldo
        ]);
        return;
    }

    public function template_csv()
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="template_tabungan.csv"');
        echo "nomor_induk;nama_santri;saldo_tabungan;saldo_jajan\n";
        echo "2023001;Ahmad;50000;20000\n";
        echo "2023002;Budi;75000;15000\n";
        echo "2023003;Cici;100000;50000\n";
        exit;
    }

    public function export_csv()
    {
        $this->load->model('Santri_model');
        $santri = $this->Santri_model->get_all();
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="data_tabungan.csv"');
        echo "nomor_induk;nama_santri;saldo_tabungan;saldo_jajan\n";
        foreach ($santri as $s) {
            echo $s->nomor_induk . ';' . str_replace(';', '', $s->nama) . ';' . ($s->saldo_tabungan ?? 0) . ';' . ($s->saldo_jajan ?? 0) . "\n";
        }
        exit;
    }

    public function import_csv()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
            $file = $_FILES['csv_file']['tmp_name'];
            $handle = fopen($file, 'r');
            $row = 0;
            $success = 0;
            $failed = 0;
            $errors = [];
            while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
                if ($row == 0) {
                    $row++;
                    continue;
                } // skip header
                $nomor_induk = trim($data[0] ?? '');
                $saldo_tabungan = trim($data[2] ?? '');
                $saldo_jajan = trim($data[3] ?? '');
                if (!$nomor_induk) {
                    $failed++;
                    $row++;
                    continue;
                }
                $santri = $this->db->get_where('santri', ['nomor_induk' => $nomor_induk])->row();
                if ($santri) {
                    // Update/insert ke tabel tabungan
                    $tabungan = $this->db->get_where('tabungan', ['santri_id' => $santri->id])->row();
                    $data_update = [
                        'saldo_tabungan' => is_numeric($saldo_tabungan) ? $saldo_tabungan : 0,
                        'saldo_jajan' => is_numeric($saldo_jajan) ? $saldo_jajan : 0
                    ];
                    if ($tabungan) {
                        $this->db->where('santri_id', $santri->id)->update('tabungan', $data_update);
                    } else {
                        $data_update['santri_id'] = $santri->id;
                        $this->db->insert('tabungan', $data_update);
                    }
                    // Catat ke tabel transaksi (riwayat)
                    $admin_id = $this->session->userdata('user_id') ?? null;
                    $now = date('Y-m-d H:i:s');
                    $this->db->insert('transaksi', [
                        'santri_id'   => $santri->id,
                        'jenis'       => 'setoran_awal',
                        'kategori'    => 'tabungan',
                        'jumlah'      => is_numeric($saldo_tabungan) ? $saldo_tabungan : 0,
                        'keterangan'  => 'Import saldo tabungan via CSV',
                        'admin_id'    => $admin_id,
                        'created_at'  => $now
                    ]);
                    $this->db->insert('transaksi', [
                        'santri_id'   => $santri->id,
                        'jenis'       => 'setoran_awal',
                        'kategori'    => 'jajan',
                        'jumlah'      => is_numeric($saldo_jajan) ? $saldo_jajan : 0,
                        'keterangan'  => 'Import saldo jajan via CSV',
                        'admin_id'    => $admin_id,
                        'created_at'  => $now
                    ]);
                    $success++;
                } else {
                    $failed++;
                    $errors[] = $nomor_induk;
                }
                $row++;
            }
            fclose($handle);
            $msg = "$success data berhasil diimport. $failed gagal.";
            if (!empty($errors)) {
                $msg .= ' Nomor induk tidak ditemukan: ' . implode(', ', $errors);
            }
            $this->session->set_flashdata('success', $msg);
        } else {
            $this->session->set_flashdata('error', 'File tidak valid.');
        }
        redirect('tabungan');
    }

    public function drop_saldo()
    {
        // Ambil semua santri yang punya saldo tabungan/jajan > 0
        $santri_tabungan = $this->db->where('saldo_tabungan >', 0)->or_where('saldo_jajan >', 0)->get('tabungan')->result();
        $admin_id = $this->session->userdata('user_id') ?? null;
        $now = date('Y-m-d H:i:s');
        foreach ($santri_tabungan as $t) {
            if ($t->saldo_tabungan > 0) {
                $this->db->insert('transaksi', [
                    'santri_id'   => $t->santri_id,
                    'jenis'       => 'hapus_via_tombol',
                    'kategori'    => 'tabungan',
                    'jumlah'      => $t->saldo_tabungan,
                    'keterangan'  => 'Hapus saldo via tombol (ADMIN)',
                    'admin_id'    => $admin_id,
                    'created_at'  => $now
                ]);
            }
            if ($t->saldo_jajan > 0) {
                $this->db->insert('transaksi', [
                    'santri_id'   => $t->santri_id,
                    'jenis'       => 'hapus_via_tombol',
                    'kategori'    => 'jajan',
                    'jumlah'      => $t->saldo_jajan,
                    'keterangan'  => 'Hapus saldo via tombol (ADMIN)',
                    'admin_id'    => $admin_id,
                    'created_at'  => $now
                ]);
            }
        }
        // Set semua saldo tabungan dan saldo jajan menjadi 0
        $this->db->update('tabungan', ['saldo_tabungan' => 0, 'saldo_jajan' => 0]);
        $this->session->set_flashdata('success', 'Semua saldo tabungan dan saldo jajan berhasil dihapus.');
        redirect('tabungan');
    }

    // Method untuk menampilkan seluruh saldo tabungan
    public function overview()
    {
        // Cek login
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }

        $role = $this->session->userdata('role');
        $kantin_id = $this->session->userdata('kantin_id');

        $data['title'] = 'Overview Saldo Tabungan - E-Kantin';

        // Ambil data saldo keseluruhan
        $data['total_saldo_jajan'] = $this->Tabungan_model->get_total_saldo_jajan_semua_santri();
        $data['total_saldo_tabungan'] = $this->get_total_saldo_tabungan_semua_santri();
        $data['total_saldo_keseluruhan'] = $data['total_saldo_jajan'] + $data['total_saldo_tabungan'];

        // Ambil data per kantin
        $data['saldo_per_kantin'] = $this->Tabungan_model->get_saldo_per_kantin();

        // Ambil data santri dengan saldo (dibatasi untuk tampilan compact)
        $data['santri_with_saldo'] = $this->Tabungan_model->get_santri_with_saldo_compact();

        // Statistik
        $data['total_santri'] = $this->Santri_model->count_all();
        $data['santri_aktif'] = $this->Santri_model->count_active();
        $data['santri_putra'] = $this->Santri_model->count_by_gender('L');
        $data['santri_putri'] = $this->Santri_model->count_by_gender('P');

        // Rata-rata saldo
        $data['rata_saldo_jajan'] = $data['total_santri'] > 0 ? round($data['total_saldo_jajan'] / $data['total_santri']) : 0;
        $data['rata_saldo_tabungan'] = $data['total_santri'] > 0 ? round($data['total_saldo_tabungan'] / $data['total_santri']) : 0;

        $this->load->view('templates/header', $data);
        $this->load->view('tabungan/overview', $data);
        $this->load->view('templates/footer');
    }

    // Helper method untuk mendapatkan total saldo tabungan semua santri
    private function get_total_saldo_tabungan_semua_santri()
    {
        $this->db->select_sum('saldo_tabungan');
        $result = $this->db->get('tabungan')->row();
        return $result->saldo_tabungan ?? 0;
    }

    public function kurangi_saldo_jajan_massal()
    {
        if (!in_array($this->session->userdata('role'), ['admin', 'keuangan'])) {
            redirect('tabungan');
        }
        if ($this->input->method() !== 'post') {
            show_404();
        }
        $nominal = $this->rupiah_to_number($this->input->post('nominal'));
        $keterangan = $this->input->post('keterangan');
        if ($nominal <= 0 || empty($keterangan)) {
            $this->session->set_flashdata('error', 'Nominal dan keterangan wajib diisi!');
            redirect('tabungan');
        }
        $admin_id = $this->session->userdata('user_id');
        $santri_list = $this->Santri_model->get_all();
        $success = 0;
        $failed_santri = [];
        foreach ($santri_list as $s) {
            $tabungan = $this->Tabungan_model->get_tabungan($s->id);
            $saldo_jajan = $tabungan ? $tabungan->saldo_jajan : 0;
            if ($saldo_jajan >= $nominal) {
                $saldo_baru = $saldo_jajan - $nominal;
                $this->Tabungan_model->update_saldo_jajan($s->id, $saldo_baru);
                $transaksi = [
                    'santri_id' => $s->id,
                    'jenis' => 'penarikan',
                    'kategori' => 'jajan',
                    'jumlah' => $nominal,
                    'keterangan' => $keterangan,
                    'admin_id' => $admin_id
                ];
                $this->Tabungan_model->record_transaksi($transaksi);
                $success++;
            } else {
                $failed_santri[] = $s->nama . ' (Saldo: Rp ' . number_format($saldo_jajan, 0, ',', '.') . ')';
            }
        }
        if (!empty($failed_santri)) {
            $msg = 'Saldo jajan santri berikut tidak mencukupi dan tidak diproses:<br><ul>';
            foreach ($failed_santri as $nama) {
                $msg .= '<li>' . $nama . '</li>';
            }
            $msg .= '</ul>';
            if ($success > 0) {
                $msg = 'Sebagian saldo jajan berhasil dikurangi. ' . $success . ' santri diproses.<br>' . $msg;
            }
            $this->session->set_flashdata('error', $msg);
        } else {
            $this->session->set_flashdata('success', 'Saldo jajan seluruh santri berhasil dikurangi. Total santri diproses: ' . $success);
        }
        redirect('tabungan');
    }

    public function export_pdf_riwayat()
    {
        $tanggal_awal = $this->input->get('tanggal_awal');
        $tanggal_akhir = $this->input->get('tanggal_akhir');
        $kantin_id = $this->session->userdata('kantin_id');
        $role = $this->session->userdata('role');
        if ($role == 'admin') {
            $transaksi = $this->Tabungan_model->get_riwayat_transaksi(NULL, NULL, $kantin_id, $tanggal_awal, $tanggal_akhir);
        } else {
            $transaksi = $this->Tabungan_model->get_riwayat_transaksi($this->session->userdata('santri_id'), NULL, $kantin_id, $tanggal_awal, $tanggal_akhir);
        }
        $this->load->library('pdf');
        $pdf = new Pdf('L', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('E-Kantin System');
        $pdf->SetAuthor('Admin');
        $pdf->SetTitle('Export PDF Riwayat Tabungan');
        $pdf->SetMargins(10, 10, 10);
        $pdf->AddPage();
        $html = '<style>
            .judul { text-align:center; font-size:16pt; font-weight:bold; margin-bottom:0; }
            .periode { text-align:center; font-size:11pt; font-style:italic; margin-bottom:10px; }
            .tabel-riwayat { width:100%; font-size:9pt; border-collapse:collapse; margin-top:10px; }
            .tabel-riwayat th, .tabel-riwayat td { border:1px solid #000; padding:4px; }
            .tabel-riwayat th { background-color:#f0f0f0; font-weight:bold; text-align:center; }
            .text-center { text-align:center; }
            .text-right { text-align:right; }
        </style>';
        $html .= '<div class="judul">Export PDF Riwayat Tabungan</div>';
        $html .= '<div class="periode">Periode: ' . date('d/m/Y', strtotime($tanggal_awal)) . ' - ' . date('d/m/Y', strtotime($tanggal_akhir)) . '</div>';
        $html .= '<table class="tabel-riwayat">';
        $html .= '<tr>
            <th width="15%">Tanggal & Waktu</th>
            <th width="20%">Santri</th>
            <th width="10%">Jenis</th>
            <th width="15%">Jumlah</th>
            <th width="15%">Admin</th>
            <th width="25%">Keterangan</th>
        </tr>';
        if (empty($transaksi)) {
            $html .= '<tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data transaksi</td></tr>';
        } else {
            foreach ($transaksi as $t) {
                $html .= '<tr>';
                $html .= '<td>' . date('d/m/Y H:i', strtotime($t->created_at)) . '</td>';
                $html .= '<td><strong>' . $t->nama_santri . '</strong><br><small class="text-muted">' . $t->nomor_induk . ' - ' . $t->kelas . '</small></td>';
                $html .= '<td>';
                if ($t->jenis == 'setoran') {
                    $html .= '<span style="color:green;font-weight:bold;">Setoran</span>';
                } elseif ($t->jenis == 'penarikan') {
                    $html .= '<span style="color:red;font-weight:bold;">Penarikan</span>';
                } elseif ($t->jenis == 'transfer') {
                    $html .= '<span style="color:blue;font-weight:bold;">Transfer</span>';
                } else {
                    $html .= ucfirst($t->jenis);
                }
                $html .= '<br><small class="text-muted">' . ucfirst($t->kategori) . '</small>';
                $html .= '</td>';
                $html .= '<td class="text-right">Rp ' . number_format($t->jumlah, 0, ',', '.') . '</td>';
                $html .= '<td>' . $t->admin_username . '</td>';
                $html .= '<td>' . $t->keterangan . '</td>';
                $html .= '</tr>';
            }
        }
        $html .= '</table>';
        $pdf->writeHTML($html, true, false, true, false, '');
        if (ob_get_length()) ob_clean();
        $pdf->Output('Export_Riwayat_Tabungan_' . $tanggal_awal . '_sd_' . $tanggal_akhir . '.pdf', 'D');
    }
}
