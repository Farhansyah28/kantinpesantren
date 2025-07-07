<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Santri extends CI_Controller
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
        $this->load->library(['session', 'form_validation', 'upload']);
        $this->load->helper(['url', 'form']);

        // Cek apakah user sudah login
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }

        // Cek role untuk akses ke fitur santri
        if (!in_array($this->session->userdata('role'), ['admin', 'keuangan'])) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses ke halaman ini');
            redirect('dashboard');
        }

        $this->load->model(['Santri_model', 'User_model', 'Tabungan_model', 'Kantin_model']);
    }

    public function index()
    {
        $kantin_id = $this->session->userdata('kantin_id');

        $data['title'] = 'Data Santri - ' . $this->session->userdata('kantin_nama');
        $data['kantin_info'] = $this->Kantin_model->get_kantin($kantin_id);
        $data['santri'] = $this->Santri_model->get_santri_with_tabungan(NULL, $kantin_id);

        $this->load->view('templates/header', $data);
        $this->load->view('santri/index', $data);
        $this->load->view('templates/footer');
    }

    public function create()
    {
        $kantin_id = $this->session->userdata('kantin_id');

        $this->form_validation->set_rules('nama', 'Nama', 'required');
        $this->form_validation->set_rules('nomor_induk', 'Nomor Induk', 'required|is_unique[santri.nomor_induk]');
        $this->form_validation->set_rules('kelas', 'Kelas', 'required');
        $this->form_validation->set_rules('jenis_kelamin', 'Jenis Kelamin', 'required|in_list[L,P]');
        $this->form_validation->set_rules('nama_wali', 'Nama Wali', 'trim');
        $this->form_validation->set_rules('kontak_wali', 'Kontak Wali', 'trim|min_length[10]');
        $this->form_validation->set_rules('hubungan_wali', 'Hubungan Wali', 'trim');

        if ($this->form_validation->run() == FALSE) {
            $data['title'] = 'Tambah Santri - ' . $this->session->userdata('kantin_nama');
            $data['kantin_info'] = $this->Kantin_model->get_kantin($kantin_id);
            $this->load->view('templates/header', $data);
            $this->load->view('santri/create', $data);
            $this->load->view('templates/footer');
        } else {
            // Validasi jenis kelamin sesuai kantin
            $jenis_kelamin = $this->input->post('jenis_kelamin');
            if (($kantin_id == 1 && $jenis_kelamin != 'L') || ($kantin_id == 2 && $jenis_kelamin != 'P')) {
                $this->session->set_flashdata('error', 'Jenis kelamin tidak sesuai dengan kantin');
                redirect('santri/create');
            }

            // Buat data santri
            $santri_data = [
                'nama' => $this->input->post('nama'),
                'nomor_induk' => $this->input->post('nomor_induk'),
                'kelas' => $this->input->post('kelas'),
                'jenis_kelamin' => $jenis_kelamin
            ];

            $santri_id = $this->Santri_model->create_santri($santri_data);

            // Buat data wali santri
            $wali_data = [
                'santri_id' => $santri_id,
                'nama' => $this->input->post('nama_wali'),
                'kontak' => $this->input->post('kontak_wali'),
                'hubungan' => $this->input->post('hubungan_wali')
            ];

            $this->Santri_model->create_wali_santri($wali_data);

            // Log sukses penambahan santri
            $this->Activity_log_model->log_system('SANTRI_CREATE_SUCCESS', [
                'santri_id' => $santri_id,
                'nama' => $this->input->post('nama'),
                'nomor_induk' => $this->input->post('nomor_induk'),
                'kelas' => $this->input->post('kelas'),
                'jenis_kelamin' => $jenis_kelamin,
                'kantin_id' => $kantin_id
            ], 'success');

            $this->session->set_flashdata('success', 'Data santri berhasil ditambahkan');
            redirect('santri');
        }
    }

    public function edit($id)
    {
        $kantin_id = $this->session->userdata('kantin_id');

        $data['title'] = 'Edit Santri - ' . $this->session->userdata('kantin_nama');
        $data['kantin_info'] = $this->Kantin_model->get_kantin($kantin_id);
        $data['santri'] = $this->Santri_model->get_santri_with_tabungan_and_wali($id, $kantin_id);

        if (!$data['santri']) {
            $this->session->set_flashdata('error', 'Santri tidak ditemukan');
            redirect('santri');
        }

        $this->load->view('templates/header', $data);
        $this->load->view('santri/edit', $data);
        $this->load->view('templates/footer');
    }

    public function update($id)
    {
        $kantin_id = $this->session->userdata('kantin_id');

        $santri = $this->Santri_model->get_santri_with_tabungan($id, $kantin_id);
        if (!$santri) {
            $this->session->set_flashdata('error', 'Santri tidak ditemukan');
            redirect('santri');
        }

        $this->form_validation->set_rules('nama', 'Nama', 'required');
        $this->form_validation->set_rules('kelas', 'Kelas', 'required');
        $this->form_validation->set_rules('jenis_kelamin', 'Jenis Kelamin', 'required|in_list[L,P]');
        $this->form_validation->set_rules('nama_wali', 'Nama Wali', 'trim');
        $this->form_validation->set_rules('kontak_wali', 'Kontak Wali', 'trim|min_length[10]');
        $this->form_validation->set_rules('hubungan_wali', 'Hubungan Wali', 'trim');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('santri/edit/' . $id);
        }

        // Validasi jenis kelamin sesuai kantin
        $jenis_kelamin = $this->input->post('jenis_kelamin');
        if (($kantin_id == 1 && $jenis_kelamin != 'L') || ($kantin_id == 2 && $jenis_kelamin != 'P')) {
            $this->session->set_flashdata('error', 'Jenis kelamin tidak sesuai dengan kantin');
            redirect('santri/edit/' . $id);
        }

        // Update data santri
        $santri_data = [
            'nama' => $this->input->post('nama'),
            'kelas' => $this->input->post('kelas'),
            'jenis_kelamin' => $jenis_kelamin
        ];

        $this->Santri_model->update_santri($id, $santri_data);

        // Update data wali santri
        $wali_data = [
            'nama' => $this->input->post('nama_wali'),
            'kontak' => $this->input->post('kontak_wali'),
            'hubungan' => $this->input->post('hubungan_wali')
        ];

        $this->Santri_model->update_wali_santri($id, $wali_data);

        // Log sukses update santri
        $this->Activity_log_model->log_system('SANTRI_UPDATE_SUCCESS', [
            'santri_id' => $id,
            'nama' => $this->input->post('nama'),
            'kelas' => $this->input->post('kelas'),
            'jenis_kelamin' => $jenis_kelamin,
            'kantin_id' => $kantin_id
        ], 'success');

        $this->session->set_flashdata('success', 'Data santri berhasil diperbarui');
        redirect('santri');
    }

    public function delete($id)
    {
        $kantin_id = $this->session->userdata('kantin_id');

        $santri = $this->Santri_model->get_santri_with_tabungan($id, $kantin_id);

        if (!$santri) {
            $this->session->set_flashdata('error', 'Santri tidak ditemukan');
            redirect('santri');
        }

        // Hapus data santri (akan menghapus data terkait karena foreign key cascade)
        $this->Santri_model->delete_santri($id);

        // Hapus user terkait jika ada
        if ($santri->user_id) {
            $this->User_model->delete_user($santri->user_id);
        }

        // Log sukses hapus santri
        $this->Activity_log_model->log_system('SANTRI_DELETE_SUCCESS', [
            'santri_id' => $id,
            'nama' => $santri->nama,
            'nomor_induk' => $santri->nomor_induk,
            'kelas' => $santri->kelas,
            'jenis_kelamin' => $santri->jenis_kelamin,
            'kantin_id' => $kantin_id
        ], 'success');

        $this->session->set_flashdata('success', 'Data santri berhasil dihapus');
        redirect('santri');
    }

    public function store()
    {
        $kantin_id = $this->session->userdata('kantin_id');

        $this->form_validation->set_rules('nomor_induk', 'Nomor Induk', 'required|is_unique[santri.nomor_induk]');
        $this->form_validation->set_rules('nama', 'Nama', 'required');
        $this->form_validation->set_rules('kelas', 'Kelas', 'required');
        $this->form_validation->set_rules('jenis_kelamin', 'Jenis Kelamin', 'required|in_list[L,P]');
        $this->form_validation->set_rules('nama_wali', 'Nama Wali', 'trim');
        $this->form_validation->set_rules('kontak_wali', 'Kontak Wali', 'trim|min_length[10]');
        $this->form_validation->set_rules('hubungan_wali', 'Hubungan Wali', 'trim');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('santri/create');
        }

        // Validasi jenis kelamin sesuai kantin
        $jenis_kelamin = $this->input->post('jenis_kelamin');
        if (($kantin_id == 1 && $jenis_kelamin != 'L') || ($kantin_id == 2 && $jenis_kelamin != 'P')) {
            $this->session->set_flashdata('error', 'Jenis kelamin tidak sesuai dengan kantin');
            redirect('santri/create');
        }

        // Buat data santri
        $santri_data = [
            'nama' => $this->input->post('nama'),
            'nomor_induk' => $this->input->post('nomor_induk'),
            'kelas' => $this->input->post('kelas'),
            'jenis_kelamin' => $jenis_kelamin
        ];

        $santri_id = $this->Santri_model->create_santri($santri_data);

        if ($santri_id) {
            // Buat data wali santri
            $wali_data = [
                'santri_id' => $santri_id,
                'nama' => $this->input->post('nama_wali'),
                'kontak' => $this->input->post('kontak_wali'),
                'hubungan' => $this->input->post('hubungan_wali')
            ];

            $this->Santri_model->create_wali_santri($wali_data);

            $this->session->set_flashdata('success', 'Data santri berhasil ditambahkan');
            redirect('santri');
        } else {
            $this->session->set_flashdata('error', 'Gagal menambahkan data santri');
            redirect('santri/create');
        }
    }

    public function check_username($id = null)
    {
        $username = $this->input->post('username');
        $user = $this->User_model->get_user_by_username($username);

        if ($user && $user->id != $id) {
            echo 'false';
        } else {
            echo 'true';
        }
    }

    public function view($id)
    {
        $kantin_id = $this->session->userdata('kantin_id');

        $data['title'] = 'Detail Santri - ' . $this->session->userdata('kantin_nama');
        $data['kantin_info'] = $this->Kantin_model->get_kantin($kantin_id);
        $data['santri'] = $this->Santri_model->get_santri_with_tabungan_and_wali($id, $kantin_id);
        $data['transaksi'] = $this->Santri_model->get_tabungan_santri($id);

        // Tambahkan transaksi kantin untuk santri ini
        $this->load->model('Transaksi_model');
        $transaksi_kantin_raw = $this->Transaksi_model->get_by_santri($id, 50, $kantin_id);

        // Kelompokkan transaksi berdasarkan waktu (dalam 1 menit yang sama)
        $transaksi_kantin_grouped = [];
        foreach ($transaksi_kantin_raw as $tk) {
            $time_key = date('Y-m-d H:i', strtotime($tk->created_at));
            if (!isset($transaksi_kantin_grouped[$time_key])) {
                $transaksi_kantin_grouped[$time_key] = [
                    'created_at' => $tk->created_at,
                    'status' => $tk->status,
                    'items' => [],
                    'total_transaksi' => 0
                ];
            }
            $transaksi_kantin_grouped[$time_key]['items'][] = $tk;
            $transaksi_kantin_grouped[$time_key]['total_transaksi'] += $tk->total_harga;
        }

        $data['transaksi_kantin'] = $transaksi_kantin_grouped;

        if (!$data['santri']) {
            $this->session->set_flashdata('error', 'Santri tidak ditemukan');
            redirect('santri');
        }

        $this->load->view('templates/header', $data);
        $this->load->view('santri/view', $data);
        $this->load->view('templates/footer');
    }

    public function import()
    {
        if ($this->input->method() === 'post') {
            if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
                $this->session->set_flashdata('error', 'Tidak ada file yang dipilih atau terjadi error saat upload.');
                redirect('santri/import');
            }
            $file = $_FILES['csv_file'];

            // Validasi ekstensi file
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if ($ext !== 'csv') {
                $this->session->set_flashdata('error', 'File harus berformat .csv');
                redirect('santri/import');
            }

            // Validasi MIME type
            $allowed_mimes = ['text/csv', 'text/plain', 'application/csv', 'application/octet-stream'];
            if (!in_array($file['type'], $allowed_mimes)) {
                $this->session->set_flashdata('error', 'Tipe file tidak diizinkan.');
                redirect('santri/import');
            }

            // Validasi ukuran file (max 5MB)
            if ($file['size'] > 5 * 1024 * 1024) {
                $this->session->set_flashdata('error', 'Ukuran file terlalu besar. Maksimal 5MB.');
                redirect('santri/import');
            }

            // Sanitasi nama file
            $filename = basename($file['name']);
            if (preg_match('/[^a-zA-Z0-9._-]/', $filename)) {
                $this->session->set_flashdata('error', 'Nama file mengandung karakter tidak diizinkan.');
                redirect('santri/import');
            }

            $upload_path = FCPATH . 'uploads/import_santri/';
            if (!is_dir($upload_path)) mkdir($upload_path, 0755, true);

            // Generate nama file yang aman
            $safe_filename = 'import_' . uniqid() . '_' . time() . '.csv';
            $target = $upload_path . $safe_filename;

            if (!move_uploaded_file($file['tmp_name'], $target)) {
                $this->session->set_flashdata('error', 'Gagal upload file.');
                redirect('santri/import');
            }
            $success = 0;
            $fail = 0;
            $errors = [];
            if (($handle = fopen($target, 'r')) !== FALSE) {
                $row = 0;
                while (($data = fgetcsv($handle, 0, ';')) !== FALSE) {
                    $row++;
                    if ($row == 1) continue; // skip header
                    $nomor_induk = trim($data[0] ?? '');
                    $nama = trim($data[1] ?? '');
                    $angkatan = trim($data[2] ?? '');
                    $jenis_kelamin = strtoupper(trim($data[3] ?? ''));
                    if ($jenis_kelamin != 'L' && $jenis_kelamin != 'P') $jenis_kelamin = 'L';
                    if (!$nomor_induk || !$nama) {
                        $fail++;
                        $errors[] = "Baris $row: Nomor Induk/Nama kosong";
                        continue;
                    }
                    // Cek duplikat
                    if ($this->Santri_model->check_nomor_induk_exists($nomor_induk)) {
                        $fail++;
                        $errors[] = "Baris $row: Nomor Induk $nomor_induk sudah ada";
                        continue;
                    }
                    $this->Santri_model->create_santri([
                        'nomor_induk' => $nomor_induk,
                        'nama' => $nama,
                        'kelas' => $angkatan,
                        'jenis_kelamin' => $jenis_kelamin
                    ]);
                    $success++;
                }
                fclose($handle);
            }
            unlink($target);
            $msg = "Import selesai. Berhasil: $success, Gagal: $fail";
            if ($fail > 0) $msg .= '<br>Detail error:<br>' . implode('<br>', $errors);
            $this->session->set_flashdata($fail == 0 ? 'success' : 'error', $msg);
            redirect('santri/import');
        }
        $data['title'] = 'Import Data Santri - ' . $this->session->userdata('kantin_nama');
        $data['kantin_info'] = $this->Kantin_model->get_kantin($this->session->userdata('kantin_id'));
        $this->load->view('templates/header', $data);
        $this->load->view('santri/import');
        $this->load->view('templates/footer');
    }

    public function export_csv()
    {
        $santri_data = $this->Santri_model->get_all_santri();
        $filename = 'Data_Santri_' . date('Y-m-d_H-i-s') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        echo "\xEF\xBB\xBF";
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Nomor Induk', 'Nama', 'Angkatan', 'Jenis Kelamin'], ';');
        foreach ($santri_data as $s) {
            fputcsv($output, [
                $s->nomor_induk,
                $s->nama,
                $s->kelas,
                $s->jenis_kelamin
            ], ';');
        }
        fclose($output);
        exit;
    }

    public function download_template()
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment;filename="Template_Import_Santri.csv"');
        echo "\xEF\xBB\xBF";
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Nomor Induk', 'Nama', 'Angkatan', 'Jenis Kelamin (L/P)'], ';');
        fputcsv($output, ['2024001', 'Ahmad Santri', '2024', 'L'], ';');
        fputcsv($output, ['2024002', 'Fatimah Santri', '2024', 'P'], ';');
        fclose($output);
        exit;
    }

    public function export_pdf($id)
    {
        $this->load->library('session');
        $this->load->model(['Santri_model', 'Tabungan_model']);
        $santri = $this->Santri_model->get_santri_with_tabungan($id);
        $transaksi = $this->Tabungan_model->get_riwayat_transaksi($id);
        if (!$santri) show_404();
        // Ambil limit dari GET
        $limit = (int)($this->input->get('limit') ?? 0);
        // Hitung saldo akhir per transaksi (backward, seperti di view)
        $saldo_tabungan = $santri->saldo_tabungan ?? 0;
        $saldo_jajan = $santri->saldo_jajan ?? 0;
        $trans_sorted = $transaksi;
        usort($trans_sorted, function ($a, $b) {
            return strtotime($b->created_at) <=> strtotime($a->created_at);
        });
        if ($limit > 0) {
            $trans_sorted = array_slice($trans_sorted, 0, $limit);
        }
        foreach ($trans_sorted as $t) {
            if ($t->kategori == 'tabungan') {
                $t->_saldo_akhir_tabungan = $saldo_tabungan;
                if ($t->jenis == 'setoran') $saldo_tabungan -= $t->jumlah;
                elseif ($t->jenis == 'penarikan') $saldo_tabungan += $t->jumlah;
            } else if ($t->kategori == 'jajan') {
                $t->_saldo_akhir_jajan = $saldo_jajan;
                if ($t->jenis == 'setoran') $saldo_jajan -= $t->jumlah;
                elseif ($t->jenis == 'penarikan') $saldo_jajan += $t->jumlah;
            }
        }
        // Load TCPDF
        require_once APPPATH . 'third_party/tcpdf/tcpdf.php';
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('E-Kantin');
        $pdf->SetAuthor('E-Kantin');
        $pdf->SetTitle('Rekening Koran Tabungan Santri');
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(TRUE, 15);
        $pdf->AddPage();
        $html = $this->load->view('santri/pdf_koran', [
            'santri' => $santri,
            'transaksi' => $trans_sorted
        ], true);
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('Rekening_Koran_' . $santri->nama . '.pdf', 'I');
    }
}
