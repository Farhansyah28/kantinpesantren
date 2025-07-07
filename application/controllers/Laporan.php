<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Laporan extends CI_Controller
{

    // Property declarations for autoloaded models to prevent PHP 8.2+ deprecation warningss
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
        $this->load->model(['Transaksi_model', 'Santri_model', 'Menu_model', 'Kantin_model']);

        // Cek login dan role admin, keuangan, atau operator
        if (!$this->session->userdata('logged_in') || !in_array($this->session->userdata('role'), ['admin', 'keuangan', 'operator'])) {
            redirect('auth/login');
        }
    }

    public function index()
    {
        $role = $this->session->userdata('role');
        $kantin_id = $this->session->userdata('kantin_id');

        $data['title'] = 'Laporan Transaksi - ' . $this->session->userdata('kantin_nama');
        $data['kantin_info'] = $this->Kantin_model->get_kantin($kantin_id);

        // Default ke hari ini
        $tanggal = $this->input->get('tanggal') ? $this->input->get('tanggal') : date('Y-m-d');
        $data['tanggal'] = $tanggal;

        // Filter berdasarkan role dan query string
        $kantin_param = $this->input->get('kantin');
        if ($role === 'admin') {
            // Admin bisa lihat semua kantin atau filter berdasarkan parameter
            if ($kantin_param === 'putra') {
                $kantin_putra = $this->Kantin_model->get_kantin_by_jenis('putra');
                $kantin_id = $kantin_putra ? $kantin_putra->id : null;
            } elseif ($kantin_param === 'putri') {
                $kantin_putri = $this->Kantin_model->get_kantin_by_jenis('putri');
                $kantin_id = $kantin_putri ? $kantin_putri->id : null;
            } else {
                $kantin_id = null; // Tampilkan semua kantin
            }
        } elseif ($role === 'keuangan') {
            // Keuangan bisa lihat semua kantin atau filter berdasarkan parameter
            if ($kantin_param === 'putra') {
                $kantin_putra = $this->Kantin_model->get_kantin_by_jenis('putra');
                $kantin_id = $kantin_putra ? $kantin_putra->id : null;
            } elseif ($kantin_param === 'putri') {
                $kantin_putri = $this->Kantin_model->get_kantin_by_jenis('putri');
                $kantin_id = $kantin_putri ? $kantin_putri->id : null;
            }
            // Jika tidak ada filter, gunakan kantin_id dari session
        } elseif ($role === 'operator') {
            // Operator hanya bisa lihat kantin mereka sendiri
            // kantin_id sudah diambil dari session, tidak perlu diubah
        }

        // Ambil data transaksi harian
        $data['transaksi'] = $this->get_transaksi_harian($tanggal, $kantin_id);
        $summary = $this->get_summary_harian($tanggal, $kantin_id);

        // Hitung total saldo jajan dan tunai (seperti harian, tapi cek juga field metode_pembayaran)
        $total_saldo_jajan = 0;
        $total_tunai = 0;
        if (!empty($data['transaksi'])) {
            foreach ($data['transaksi'] as $t) {
                // Saldo Jajan
                if ((isset($t->metode_pembayaran) && $t->metode_pembayaran === 'saldo_jajan') || (isset($t->keterangan) && stripos($t->keterangan, 'Saldo Jajan') !== false)) {
                    $total_saldo_jajan += $t->total_harga;
                }
                // Tunai
                elseif ((isset($t->metode_pembayaran) && $t->metode_pembayaran === 'tunai') || (isset($t->jenis) && $t->jenis === 'ustadz') || (isset($t->keterangan) && stripos($t->keterangan, 'Tunai') !== false)) {
                    $total_tunai += $t->total_harga;
                }
            }
        }
        $data['total_saldo_jajan'] = $total_saldo_jajan;
        $data['total_tunai'] = $total_tunai;

        // Set variable untuk view
        $data['total_pendapatan'] = $summary['total_pendapatan'];
        $data['total_item'] = $summary['total_item'];
        $data['total_transaksi'] = $summary['total_transaksi'];
        $data['santri_transaksi'] = $summary['santri_transaksi'];
        $data['top_menu'] = $summary['menu_terlaris'];

        // Hitung rata-rata per transaksi
        $data['rata_rata'] = $summary['total_transaksi'] > 0 ? $summary['total_pendapatan'] / $summary['total_transaksi'] : 0;

        // Data untuk chart
        $data['chart_labels'] = $this->get_chart_labels($tanggal, $kantin_id);
        $data['chart_data'] = $this->get_chart_data($tanggal, $kantin_id);

        // Data untuk pie chart (top menu)
        $data['pie_labels'] = [];
        $data['pie_data'] = [];
        if ($summary['menu_terlaris']) {
            foreach ($summary['menu_terlaris'] as $menu) {
                $data['pie_labels'][] = $menu->nama_menu;
                $data['pie_data'][] = $menu->total_terjual;
            }
        }

        $this->load->view('templates/header', $data);
        $this->load->view('laporan/index', $data);
        $this->load->view('templates/footer');
    }

    public function mingguan()
    {
        $role = $this->session->userdata('role');
        $kantin_id = $this->session->userdata('kantin_id');

        $data['title'] = 'Laporan Transaksi Mingguan - ' . $this->session->userdata('kantin_nama');
        $data['kantin_info'] = $this->Kantin_model->get_kantin($kantin_id);

        // Default ke minggu ini
        $tahun = date('Y');
        $nomor_minggu = date('W');
        $minggu = $this->input->get('minggu') ? $this->input->get('minggu') : sprintf('%04d-W%02d', $tahun, $nomor_minggu);
        $data['minggu'] = $minggu;

        // Parse minggu (format: YYYY-WWW)
        $tahun = substr($minggu, 0, 4);
        $nomor_minggu = substr($minggu, 6, 2);

        // Hitung tanggal awal dan akhir minggu
        $dto = new DateTime();
        $dto->setISODate($tahun, $nomor_minggu);
        $tanggal_awal = $dto->format('Y-m-d');
        $dto->modify('+6 days');
        $tanggal_akhir = $dto->format('Y-m-d');

        $data['tanggal_awal'] = $tanggal_awal;
        $data['tanggal_akhir'] = $tanggal_akhir;

        // Filter berdasarkan role
        if ($role === 'admin') {
            // Admin bisa lihat semua kantin
            $kantin_id = null;
        } elseif ($role === 'keuangan') {
            // Keuangan bisa lihat semua kantin atau filter berdasarkan parameter
            $kantin_param = $this->input->get('kantin');
            if ($kantin_param === 'putra') {
                $kantin_putra = $this->Kantin_model->get_kantin_by_jenis('putra');
                $kantin_id = $kantin_putra ? $kantin_putra->id : null;
            } elseif ($kantin_param === 'putri') {
                $kantin_putri = $this->Kantin_model->get_kantin_by_jenis('putri');
                $kantin_id = $kantin_putri ? $kantin_putri->id : null;
            }
            // Jika tidak ada filter, gunakan kantin_id dari session
        } elseif ($role === 'operator') {
            // Operator hanya bisa lihat kantin mereka sendiri
            // kantin_id sudah diambil dari session, tidak perlu diubah
        }

        // Ambil data transaksi mingguan
        $data['transaksi'] = $this->get_transaksi_mingguan($tanggal_awal, $tanggal_akhir, $kantin_id);
        $summary = $this->get_summary_mingguan($tanggal_awal, $tanggal_akhir, $kantin_id);

        // Hitung total saldo jajan dan tunai (seperti harian, tapi cek juga field metode_pembayaran)
        $total_saldo_jajan = 0;
        $total_tunai = 0;
        if (!empty($data['transaksi'])) {
            foreach ($data['transaksi'] as $t) {
                // Saldo Jajan
                if ((isset($t->metode_pembayaran) && $t->metode_pembayaran === 'saldo_jajan') || (isset($t->keterangan) && stripos($t->keterangan, 'Saldo Jajan') !== false)) {
                    $total_saldo_jajan += $t->total_harga;
                }
                // Tunai
                elseif ((isset($t->metode_pembayaran) && $t->metode_pembayaran === 'tunai') || (isset($t->jenis) && $t->jenis === 'ustadz') || (isset($t->keterangan) && stripos($t->keterangan, 'Tunai') !== false)) {
                    $total_tunai += $t->total_harga;
                }
            }
        }
        $data['total_saldo_jajan'] = $total_saldo_jajan;
        $data['total_tunai'] = $total_tunai;

        // Set variable untuk view
        $data['total_pendapatan'] = $summary['total_pendapatan'];
        $data['total_item'] = $summary['total_item'];
        $data['total_transaksi'] = $summary['total_transaksi'];
        $data['santri_transaksi'] = $summary['santri_transaksi'];
        $data['top_menu'] = $summary['menu_terlaris'];

        // Hitung rata-rata per transaksi
        $data['rata_rata'] = $summary['total_transaksi'] > 0 ? $summary['total_pendapatan'] / $summary['total_transaksi'] : 0;

        // Data untuk chart
        $data['chart_labels'] = $this->get_chart_labels_mingguan($tanggal_awal, $tanggal_akhir, $kantin_id);
        $data['chart_data'] = $this->get_chart_data_mingguan($tanggal_awal, $tanggal_akhir, $kantin_id);

        // Data untuk pie chart (top menu)
        $data['pie_labels'] = [];
        $data['pie_data'] = [];
        if ($summary['menu_terlaris']) {
            foreach ($summary['menu_terlaris'] as $menu) {
                $data['pie_labels'][] = $menu->nama_menu;
                $data['pie_data'][] = $menu->total_terjual;
            }
        }

        $this->load->view('templates/header', $data);
        $this->load->view('laporan/mingguan', $data);
        $this->load->view('templates/footer');
    }

    public function bulanan()
    {
        $role = $this->session->userdata('role');
        $kantin_id = $this->session->userdata('kantin_id');

        $data['title'] = 'Laporan Transaksi Bulanan - ' . $this->session->userdata('kantin_nama');
        $data['kantin_info'] = $this->Kantin_model->get_kantin($kantin_id);

        // Default ke bulan ini
        $bulan = $this->input->get('bulan') ? $this->input->get('bulan') : date('Y-m');
        $data['bulan'] = $bulan;

        // Parse bulan (format: YYYY-MM)
        $tahun = substr($bulan, 0, 4);
        $nomor_bulan = substr($bulan, 5, 2);

        // Hitung tanggal awal dan akhir bulan
        $tanggal_awal = $bulan . '-01';
        $tanggal_akhir = date('Y-m-t', strtotime($tanggal_awal));

        $data['tanggal_awal'] = $tanggal_awal;
        $data['tanggal_akhir'] = $tanggal_akhir;

        // Filter berdasarkan role
        if ($role === 'admin') {
            // Admin bisa lihat semua kantin
            $kantin_id = null;
        } elseif ($role === 'keuangan') {
            // Keuangan bisa lihat semua kantin atau filter berdasarkan parameter
            $kantin_param = $this->input->get('kantin');
            if ($kantin_param === 'putra') {
                $kantin_putra = $this->Kantin_model->get_kantin_by_jenis('putra');
                $kantin_id = $kantin_putra ? $kantin_putra->id : null;
            } elseif ($kantin_param === 'putri') {
                $kantin_putri = $this->Kantin_model->get_kantin_by_jenis('putri');
                $kantin_id = $kantin_putri ? $kantin_putri->id : null;
            }
            // Jika tidak ada filter, gunakan kantin_id dari session
        } elseif ($role === 'operator') {
            // Operator hanya bisa lihat kantin mereka sendiri
            // kantin_id sudah diambil dari session, tidak perlu diubah
        }

        // Ambil data transaksi bulanan
        $data['transaksi'] = $this->get_transaksi_bulanan($tanggal_awal, $tanggal_akhir, $kantin_id);
        $summary = $this->get_summary_bulanan($tanggal_awal, $tanggal_akhir, $kantin_id);

        // Hitung total saldo jajan dan tunai (seperti harian, tapi cek juga field metode_pembayaran)
        $total_saldo_jajan = 0;
        $total_tunai = 0;
        if (!empty($data['transaksi'])) {
            foreach ($data['transaksi'] as $t) {
                // Saldo Jajan
                if ((isset($t->metode_pembayaran) && $t->metode_pembayaran === 'saldo_jajan') || (isset($t->keterangan) && stripos($t->keterangan, 'Saldo Jajan') !== false)) {
                    $total_saldo_jajan += $t->total_harga;
                }
                // Tunai
                elseif ((isset($t->metode_pembayaran) && $t->metode_pembayaran === 'tunai') || (isset($t->jenis) && $t->jenis === 'ustadz') || (isset($t->keterangan) && stripos($t->keterangan, 'Tunai') !== false)) {
                    $total_tunai += $t->total_harga;
                }
            }
        }
        $data['total_saldo_jajan'] = $total_saldo_jajan;
        $data['total_tunai'] = $total_tunai;

        // Set variable untuk view
        $data['total_pendapatan'] = $summary['total_pendapatan'];
        $data['total_item'] = $summary['total_item'];
        $data['total_transaksi'] = $summary['total_transaksi'];
        $data['santri_transaksi'] = $summary['santri_transaksi'];
        $data['top_menu'] = $summary['menu_terlaris'];

        // Hitung rata-rata per transaksi
        $data['rata_rata'] = $summary['total_transaksi'] > 0 ? $summary['total_pendapatan'] / $summary['total_transaksi'] : 0;

        // Data untuk chart
        $data['chart_labels'] = $this->get_chart_labels_bulanan($tanggal_awal, $tanggal_akhir, $kantin_id);
        $data['chart_data'] = $this->get_chart_data_bulanan($tanggal_awal, $tanggal_akhir, $kantin_id);

        // Data untuk pie chart (top menu)
        $data['pie_labels'] = [];
        $data['pie_data'] = [];
        if ($summary['menu_terlaris']) {
            foreach ($summary['menu_terlaris'] as $menu) {
                $data['pie_labels'][] = $menu->nama_menu;
                $data['pie_data'][] = $menu->total_terjual;
            }
        }

        $this->load->view('templates/header', $data);
        $this->load->view('laporan/bulanan', $data);
        $this->load->view('templates/footer');
    }

    public function export_pdf_harian()
    {
        $role = $this->session->userdata('role');
        $kantin_id = $this->session->userdata('kantin_id');

        // Default ke hari ini
        $tanggal = $this->input->get('tanggal') ? $this->input->get('tanggal') : date('Y-m-d');

        // Filter berdasarkan role dan query string
        $kantin_param = $this->input->get('kantin');
        if ($role === 'admin') {
            // Admin bisa lihat semua kantin atau filter berdasarkan parameter
            if ($kantin_param === 'putra') {
                $kantin_putra = $this->Kantin_model->get_kantin_by_jenis('putra');
                $kantin_id = $kantin_putra ? $kantin_putra->id : null;
            } elseif ($kantin_param === 'putri') {
                $kantin_putri = $this->Kantin_model->get_kantin_by_jenis('putri');
                $kantin_id = $kantin_putri ? $kantin_putri->id : null;
            } else {
                $kantin_id = null; // Tampilkan semua kantin
            }
        } elseif ($role === 'keuangan') {
            // Keuangan bisa lihat semua kantin atau filter berdasarkan parameter
            if ($kantin_param === 'putra') {
                $kantin_putra = $this->Kantin_model->get_kantin_by_jenis('putra');
                $kantin_id = $kantin_putra ? $kantin_putra->id : null;
            } elseif ($kantin_param === 'putri') {
                $kantin_putri = $this->Kantin_model->get_kantin_by_jenis('putri');
                $kantin_id = $kantin_putri ? $kantin_putri->id : null;
            }
        }

        $kantin_info = $this->Kantin_model->get_kantin($kantin_id);
        $kantin_nama = $kantin_info && isset($kantin_info->nama) ? $kantin_info->nama : 'Semua Kantin';

        // Ambil data transaksi harian
        $transaksi = $this->get_transaksi_harian($tanggal, $kantin_id);
        $summary = $this->get_summary_harian($tanggal, $kantin_id);

        // Hitung total saldo jajan dan tunai
        $total_saldo_jajan = 0;
        $total_tunai = 0;
        if (!empty($transaksi)) {
            foreach ($transaksi as $t) {
                // Saldo Jajan
                if ((isset($t->metode_pembayaran) && $t->metode_pembayaran === 'saldo_jajan') || (isset($t->keterangan) && stripos($t->keterangan, 'Saldo Jajan') !== false)) {
                    $total_saldo_jajan += $t->total_harga;
                }
                // Tunai
                elseif ((isset($t->metode_pembayaran) && $t->metode_pembayaran === 'tunai') || (isset($t->jenis) && $t->jenis === 'ustadz') || (isset($t->keterangan) && stripos($t->keterangan, 'Tunai') !== false)) {
                    $total_tunai += $t->total_harga;
                }
            }
        }

        // Grouping transaksi per nota
        $grouped = [];
        foreach ($transaksi as $t) {
            $key = $t->created_at . '|' . $t->nama_pelanggan . '|' . $t->nama_kantin;
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'created_at' => $t->created_at,
                    'nama_pelanggan' => $t->nama_pelanggan,
                    'nama_kantin' => $t->nama_kantin,
                    'menu_list' => [],
                    'qty_total' => 0,
                    'total' => 0
                ];
            }
            $grouped[$key]['menu_list'][] = $t->nama_menu . ' x' . $t->jumlah;
            $grouped[$key]['qty_total'] += $t->jumlah;
            $grouped[$key]['total'] += $t->total_harga;
        }

        // Load library PDF
        $this->load->library('pdf');
        $pdf = new Pdf('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('E-Kantin System');
        $pdf->SetAuthor('Admin');
        $pdf->SetTitle('Laporan Transaksi Harian ' . $kantin_nama);
        $pdf->SetMargins(15, 15, 15);
        $pdf->AddPage();

        $html = '<style>
            .judul { text-align:center; font-size:16pt; font-weight:bold; margin-bottom:0; }
            .periode { text-align:center; font-size:11pt; font-style:italic; margin-bottom:10px; }
            .box { border:1px solid #000; padding:10px; margin-top:10px; }
            .tabel-summary { width:100%; font-size:10pt; margin-top:10px; }
            .tabel-summary td { padding:2px 4px; }
            .right { text-align:right; }
            .bold { font-weight:bold; }
            .border-top { border-top:1px solid #000; }
            .tabel-transaksi { width:100%; font-size:9pt; margin-top:10px; border-collapse:collapse; }
            .tabel-transaksi th, .tabel-transaksi td { border:1px solid #000; padding:3px; }
            .tabel-transaksi th { background-color:#f0f0f0; font-weight:bold; text-align:center; }
            .text-center { text-align:center; }
            .text-right { text-align:right; }
        </style>';

        $html .= '<div class="judul">Laporan Transaksi Harian ' . strtoupper($kantin_nama) . ' Daar El Manshur</div>';
        $html .= '<div class="periode">Tanggal: ' . date('d M Y', strtotime($tanggal)) . '</div>';

        // Detail transaksi section
        if (!empty($grouped)) {
            $html .= '<div style="margin-top:15px;">';
            $html .= '<table class="tabel-transaksi">';
            $html .= '<tr>
                        <th width="5%">No</th>
                        <th width="12%">Waktu</th>
                        <th width="20%">Nama</th>
                        <th width="35%">Menu</th>
                        <th width="8%">Qty</th>
                        <th width="12%">Total</th>
                        <th width="8%">Kantin</th>
                      </tr>';

            $no = 1;
            foreach ($grouped as $row) {
                $html .= '<tr>
                            <td class="text-center">' . $no++ . '</td>
                            <td class="text-center">' . date('H:i', strtotime($row['created_at'])) . '</td>
                            <td>' . $row['nama_pelanggan'] . '</td>
                            <td>' . implode(', ', $row['menu_list']) . '</td>
                            <td class="text-center">' . $row['qty_total'] . '</td>
                            <td class="text-right">Rp ' . number_format($row['total'], 0, ',', '.') . '</td>
                            <td class="text-center">' . $row['nama_kantin'] . '</td>
                          </tr>';
            }
            $html .= '</table>';
            // Tambahkan total saldo jajan dan tunai di bawah tabel
            $html .= '<table style="width:100%; font-size:10pt; margin-top:10px;">
                <tr>
                    <td style="text-align:right; font-weight:bold;">Total Saldo Jajan Terpotong:</td>
                    <td style="text-align:right; width:150px;">Rp ' . number_format($total_saldo_jajan, 0, ',', '.') . '</td>
                </tr>
                <tr>
                    <td style="text-align:right; font-weight:bold;">Total Tunai yang Didapat:</td>
                    <td style="text-align:right; width:150px;">Rp ' . number_format($total_tunai, 0, ',', '.') . '</td>
                </tr>
            </table>';
        } else {
            $html .= '<div style="margin-top:15px; text-align:center; font-style:italic; color:#666;">Tidak ada transaksi pada tanggal ' . date('d/m/Y', strtotime($tanggal)) . '</div>';
        }

        $pdf->writeHTML($html, true, false, true, false, '');
        if (ob_get_length()) ob_clean();
        $pdf->Output('Laporan_Transaksi_Harian_' . $kantin_nama . '_' . date('d M Y', strtotime($tanggal)) . '.pdf', 'D');
    }

    public function export_pdf_mingguan()
    {
        $role = $this->session->userdata('role');
        // Ambil parameter kantin dari GET (putra/putri)
        $kantin_param = $this->input->get('kantin');
        $kantin_id = null;
        if ($kantin_param === 'putra') {
            $kantin_putra = $this->Kantin_model->get_kantin_by_jenis('putra');
            $kantin_id = $kantin_putra ? $kantin_putra->id : null;
        } elseif ($kantin_param === 'putri') {
            $kantin_putri = $this->Kantin_model->get_kantin_by_jenis('putri');
            $kantin_id = $kantin_putri ? $kantin_putri->id : null;
        } else {
            // fallback: semua kantin
            $kantin_id = null;
        }
        $kantin_info = $this->Kantin_model->get_kantin($kantin_id);
        $kantin_nama = $kantin_info && isset($kantin_info->nama) ? $kantin_info->nama : '-';

        // Ambil parameter minggu
        $tahun = date('Y');
        $nomor_minggu = date('W');
        $minggu = $this->input->get('minggu') ? $this->input->get('minggu') : sprintf('%04d-W%02d', $tahun, $nomor_minggu);
        $tahun = substr($minggu, 0, 4);
        $minggu_ke = (int)substr($minggu, 6, 2);
        $tanggal_awal = date('Y-m-d', strtotime($tahun . "W" . str_pad($minggu_ke, 2, '0', STR_PAD_LEFT)));
        $tanggal_akhir = date('Y-m-d', strtotime($tanggal_awal . ' +6 days'));

        $transaksi = $this->get_transaksi_mingguan($tanggal_awal, $tanggal_akhir, $kantin_id);

        $penjualan_bersih_kantin = 0;
        $harga_pokok_kantin = 0;
        $penjualan_bersih_konsinyasi = 0;
        $pembayaran_konsinyasi = 0;

        foreach ($transaksi as $t) {
            // Pemilik Kantin
            if (strtolower(trim($t->pemilik)) === 'kantin') {
                $penjualan_bersih_kantin += $t->total_harga;
                $harga_pokok_kantin += $t->harga_beli * $t->jumlah;
            }
            // Konsinyasi (bukan Kantin)
            else if (strtolower(trim($t->pemilik)) !== 'kantin') {
                $penjualan_bersih_konsinyasi += $t->total_harga;
                $pembayaran_konsinyasi += $t->harga_beli * $t->jumlah;
            }
        }

        $laba_bersih_kantin = $penjualan_bersih_kantin - $harga_pokok_kantin;
        $laba_bersih_konsinyasi = $penjualan_bersih_konsinyasi - $pembayaran_konsinyasi;
        $total_laba = $laba_bersih_kantin + $laba_bersih_konsinyasi;

        // Load library PDF
        $this->load->library('pdf');
        $pdf = new Pdf('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('E-Kantin System');
        $pdf->SetAuthor('Admin');
        $pdf->SetTitle('Laporan Laba Mingguan ' . $kantin_nama);
        $pdf->SetMargins(15, 15, 15);
        $pdf->AddPage();

        $username = $this->session->userdata('username');
        $nama_kantin = $username ? $username : '-';
        $nama_keuangan = 'Ust M. Syaifullah. S.E';
        $nama_sdi = 'Ummi Sri Utami S.Pt.,M.M.';
        $tanggal_ttd = date('d/m/Y');

        $html = '<style>
            .judul { text-align:center; font-size:16pt; font-weight:bold; margin-bottom:0; }
            .periode { text-align:center; font-size:11pt; font-style:italic; margin-bottom:10px; }
            .box { border:1px solid #000; padding:10px; margin-top:10px; }
            .tabel-laba { width:100%; font-size:11pt; margin-top:10px; }
            .tabel-laba td { padding:2px 4px; }
            .right { text-align:right; }
            .bold { font-weight:bold; }
            .border-top { border-top:1px solid #000; }
        </style>';
        $html .= '<div class="judul">Laporan Mingguan '   . strtoupper($kantin_nama) . ' Daar El Manshur</div>';
        $html .= '<div class="periode">per ' . date('d M Y', strtotime($tanggal_awal)) . ' - ' . date('d M Y', strtotime($tanggal_akhir)) . '</div>';
        $html .= '<div class="box">';
        $html .= '<table class="tabel-laba">';
        $html .= '<tr><td colspan="3" class="right"><span class="bold">Minggu ke - ' . intval($nomor_minggu) . '</span></td></tr>';
        $html .= '<tr><td>Penjualan bersih kantin</td><td class="right">Rp</td><td class="right">' . number_format($penjualan_bersih_kantin, 0, ',', '.') . '</td></tr>';
        $html .= '<tr><td style="padding-left:20px;">Dikurangi :</td><td></td><td></td></tr>';
        $html .= '<tr><td style="padding-left:40px;">Harga Pokok kantin</td><td class="right">Rp</td><td class="right">' . number_format($harga_pokok_kantin, 0, ',', '.') . '</td></tr>';
        $html .= '<tr><td class="bold">Laba Bersih</td><td class="right bold">Rp</td><td class="right bold">' . number_format($laba_bersih_kantin, 0, ',', '.') . '</td></tr>';
        $html .= '<tr><td colspan="3"></td></tr>';
        $html .= '<tr><td>Penjualan Bersih Konsinyasi</td><td class="right">Rp</td><td class="right">' . number_format($penjualan_bersih_konsinyasi, 0, ',', '.') . '</td></tr>';
        $html .= '<tr><td style="padding-left:20px;">Dikurangi :</td><td></td><td></td></tr>';
        $html .= '<tr><td style="padding-left:40px;">Pembayaran ke Konsinyee</td><td class="right">Rp</td><td class="right">' . number_format($pembayaran_konsinyasi, 0, ',', '.') . '</td></tr>';
        $html .= '<tr><td class="bold">Laba Bersih Konsinyasi</td><td class="right bold">Rp</td><td class="right bold">' . number_format($laba_bersih_konsinyasi, 0, ',', '.') . '</td></tr>';
        $html .= '<tr><td colspan="3"></td></tr>';
        $html .= '<tr><td colspan="2" class="right bold border-top">Lab. per ' . date('d M Y', strtotime($tanggal_awal)) . ' - ' . date('d M Y', strtotime($tanggal_akhir)) . '</td><td class="right bold border-top">' . number_format($total_laba, 0, ',', '.') . '</td></tr>';
        $html .= '</table>';
        $html .= '</div>';
        $html .= '<br><br><table border="1" cellpadding="6" cellspacing="0" style="width:100%; font-size:10pt; border-collapse:collapse; text-align:center;">
            <tr style="font-weight:bold;">
                <td>Dibuat oleh</td>
                <td>Diperiksa oleh</td>
                <td>Disetujui oleh</td>
            </tr>
            <tr>
                <td>Bagian Kantin</td>
                <td>Bagian Keuangan</td>
                <td>Sumber Daya Insani</td>
            </tr>
            <tr>
                <td style="height:120px;"></td>
                <td style="height:120px;"></td>
                <td style="height:120px;"></td>
            </tr>
            <tr style="font-weight:bold;">
                <td>' . $nama_kantin . '</td>
                <td>' . $nama_keuangan . '</td>
                <td>' . $nama_sdi . '</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align:center;">Tanggal: ' . $tanggal_ttd . '</td>
            </tr>
        </table>';

        $pdf->writeHTML($html, true, false, true, false, '');
        if (ob_get_length()) ob_clean();
        $pdf->Output('Laporan_Laba_Mingguan_' . $kantin_nama . '_' . date('d M Y', strtotime($tanggal_akhir)) . '.pdf', 'D');
    }

    public function export_excel_mingguan()
    {
        // Simple autoloader for PhpSpreadsheet
        spl_autoload_register(function ($class) {
            $prefix = 'PhpOffice\\PhpSpreadsheet\\';
            $base_dir = APPPATH . 'third_party/PhpSpreadsheet/src/PhpSpreadsheet/';
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                return;
            }
            $relative_class = substr($class, $len);
            $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
            if (file_exists($file)) {
                require_once $file;
            }
        });

        // Ambil parameter minggu & kantin
        $kantin_id = $this->session->userdata('kantin_id');
        $kantin_info = $this->Kantin_model->get_kantin($kantin_id);
        $kantin_nama = $kantin_info && isset($kantin_info->nama) ? $kantin_info->nama : '-';
        $tahun = date('Y');
        $nomor_minggu = date('W');
        $minggu = $this->input->get('minggu') ? $this->input->get('minggu') : sprintf('%04d-W%02d', $tahun, $nomor_minggu);
        $tahun = substr($minggu, 0, 4);
        $minggu_ke = (int)substr($minggu, 6, 2);
        $tanggal_awal = date('Y-m-d', strtotime($tahun . "W" . str_pad($minggu_ke, 2, '0', STR_PAD_LEFT)));
        $tanggal_akhir = date('Y-m-d', strtotime($tanggal_awal . ' +6 days'));

        $transaksi = $this->get_transaksi_mingguan($tanggal_awal, $tanggal_akhir, $kantin_id);
        $penjualan_bersih_kantin = 0;
        $harga_pokok_kantin = 0;
        $penjualan_bersih_konsinyasi = 0;
        $pembayaran_konsinyasi = 0;
        foreach ($transaksi as $t) {
            if (strtolower(trim($t->pemilik)) === 'kantin') {
                $penjualan_bersih_kantin += $t->total_harga;
                $harga_pokok_kantin += $t->harga_beli * $t->jumlah;
            } else if (strtolower(trim($t->pemilik)) !== 'kantin') {
                $penjualan_bersih_konsinyasi += $t->total_harga;
                $pembayaran_konsinyasi += $t->harga_beli * $t->jumlah;
            }
        }
        $laba_bersih_kantin = $penjualan_bersih_kantin - $harga_pokok_kantin;
        $laba_bersih_konsinyasi = $penjualan_bersih_konsinyasi - $pembayaran_konsinyasi;
        $total_laba = $laba_bersih_kantin + $laba_bersih_konsinyasi;

        // Tanda tangan
        $username = $this->session->userdata('username');
        $nama_kantin = $username ? $username : '-';
        $nama_keuangan = 'Ust M. Syaifullah. S.E';
        $nama_sdi = 'Ummi Sri Utami S.Pt.,M.M.';
        $tanggal_ttd = date('d/m/Y');

        // PhpSpreadsheet classes will be loaded by autoloader


        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $row = 1;
        // Judul
        $sheet->mergeCells('A' . $row . ':C' . $row);
        $sheet->setCellValue('A' . $row, 'Laporan Mingguan ' . strtoupper($kantin_nama) . ' Daar El Manshur');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $row++;
        $sheet->mergeCells('A' . $row . ':C' . $row);
        $sheet->setCellValue('A' . $row, 'per ' . date('d M Y', strtotime($tanggal_awal)) . ' - ' . date('d M Y', strtotime($tanggal_akhir)));
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $row += 2;
        // Minggu ke
        $sheet->mergeCells('A' . $row . ':C' . $row);
        $sheet->setCellValue('A' . $row, 'Minggu ke - ' . $minggu_ke);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $row++;
        // Tabel laba
        $sheet->setCellValue('A' . $row, 'Penjualan bersih kantin');
        $sheet->setCellValue('B' . $row, 'Rp');
        $sheet->setCellValue('C' . $row, $penjualan_bersih_kantin);
        $row++;
        $sheet->setCellValue('A' . $row, '  Dikurangi :');
        $row++;
        $sheet->setCellValue('A' . $row, '    Harga Pokok kantin');
        $sheet->setCellValue('B' . $row, 'Rp');
        $sheet->setCellValue('C' . $row, $harga_pokok_kantin);
        $row++;
        $sheet->setCellValue('A' . $row, 'Laba Bersih');
        $sheet->setCellValue('B' . $row, 'Rp');
        $sheet->setCellValue('C' . $row, $laba_bersih_kantin);
        $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
        $row++;
        $sheet->setCellValue('A' . $row, 'Penjualan Bersih Konsinyasi');
        $sheet->setCellValue('B' . $row, 'Rp');
        $sheet->setCellValue('C' . $row, $penjualan_bersih_konsinyasi);
        $row++;
        $sheet->setCellValue('A' . $row, '  Dikurangi :');
        $row++;
        $sheet->setCellValue('A' . $row, '    Pembayaran ke Konsinyee');
        $sheet->setCellValue('B' . $row, 'Rp');
        $sheet->setCellValue('C' . $row, $pembayaran_konsinyasi);
        $row++;
        $sheet->setCellValue('A' . $row, 'Laba Bersih Konsinyasi');
        $sheet->setCellValue('B' . $row, 'Rp');
        $sheet->setCellValue('C' . $row, $laba_bersih_konsinyasi);
        $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
        $row += 2;
        $sheet->mergeCells('A' . $row . ':B' . $row);
        $sheet->setCellValue('A' . $row, 'Lab. per ' . date('d M Y', strtotime($tanggal_awal)) . ' - ' . date('d M Y', strtotime($tanggal_akhir)));
        $sheet->setCellValue('C' . $row, $total_laba);
        $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
        $row += 2;
        // Tabel tanda tangan
        $sheet->setCellValue('A' . $row, 'Dibuat oleh');
        $sheet->setCellValue('B' . $row, 'Diperiksa oleh');
        $sheet->setCellValue('C' . $row, 'Disetujui oleh');
        $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
        $row++;
        $sheet->setCellValue('A' . $row, 'Bagian Kantin');
        $sheet->setCellValue('B' . $row, 'Bagian Keuangan');
        $sheet->setCellValue('C' . $row, 'Sumber Daya Insani');
        $row++;
        // Baris kosong untuk tanda tangan
        $sheet->getRowDimension($row)->setRowHeight(60);
        $row++;
        $sheet->setCellValue('A' . $row, $nama_kantin);
        $sheet->setCellValue('B' . $row, $nama_keuangan);
        $sheet->setCellValue('C' . $row, $nama_sdi);
        $sheet->getStyle('A' . $row . ':C' . $row)->getFont()->setBold(true);
        $row++;
        $sheet->mergeCells('A' . $row . ':C' . $row);
        $sheet->setCellValue('A' . $row, 'Tanggal: ' . $tanggal_ttd);
        $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Output Excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Laporan_Laba_Mingguan_' . $kantin_nama . '_' . date('d_M_Y', strtotime($tanggal_akhir)) . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    // Method untuk mendapatkan transaksi harian
    private function get_transaksi_harian($tanggal, $kantin_id)
    {
        $where_kantin1 = $kantin_id ? "AND mk.kantin_id = " . intval($kantin_id) : "";
        $where_kantin2 = $kantin_id ? "AND mk2.kantin_id = " . intval($kantin_id) : "";

        $sql = "
            SELECT
                t.created_at,
                t.jumlah,
                t.total_harga,
                CAST(mk.nama_menu AS CHAR CHARACTER SET utf8mb4) as nama_menu,
                CAST(t.keterangan AS CHAR CHARACTER SET utf8mb4) as keterangan,
                CAST(s.nama AS CHAR CHARACTER SET utf8mb4) as nama_pelanggan,
                s.nomor_induk,
                s.kelas,
                CAST(mk.pemilik AS CHAR CHARACTER SET utf8mb4) as pemilik,
                mk.harga_jual,
                CAST(kantin.nama AS CHAR CHARACTER SET utf8mb4) as nama_kantin,
                CAST('santri' AS CHAR CHARACTER SET utf8mb4) as jenis
            FROM transaksi_kantin t
            JOIN santri s ON t.santri_id = s.id
            JOIN menu_kantin mk ON t.menu_id = mk.id
            JOIN kantin ON kantin.id = t.kantin_id
            WHERE DATE(t.created_at) = '" . $this->db->escape_str($tanggal) . "' AND t.status = 'selesai' $where_kantin1

            UNION ALL

            SELECT
                t2.created_at,
                t2.jumlah,
                t2.total_harga,
                CAST(mk2.nama_menu AS CHAR CHARACTER SET utf8mb4) as nama_menu,
                CAST(t2.keterangan AS CHAR CHARACTER SET utf8mb4) as keterangan,
                CAST(u.nama AS CHAR CHARACTER SET utf8mb4) as nama_pelanggan,
                '' as nomor_induk,
                '' as kelas,
                CAST(mk2.pemilik AS CHAR CHARACTER SET utf8mb4) as pemilik,
                mk2.harga_jual,
                CAST(kantin2.nama AS CHAR CHARACTER SET utf8mb4) as nama_kantin,
                CAST('ustadz' AS CHAR CHARACTER SET utf8mb4) as jenis
            FROM transaksi_ustadz t2
            JOIN ustadz u ON t2.ustadz_id = u.id
            JOIN menu_kantin mk2 ON t2.menu_id = mk2.id
            JOIN kantin kantin2 ON kantin2.id = mk2.kantin_id
            WHERE DATE(t2.created_at) = '" . $this->db->escape_str($tanggal) . "' AND t2.status = 'selesai' $where_kantin2

            ORDER BY created_at ASC
        ";
        $result = $this->db->query($sql);
        if ($result === false) {
            log_message('error', '[LAPORAN] SQL ERROR: ' . (method_exists($this->db, 'error') ? $this->db->error()['message'] : $this->db->_error_message()));
            log_message('error', '[LAPORAN] SQL: ' . $sql);
            return [];
        }
        return $result->result();
    }

    // Method untuk mendapatkan summary harian
    private function get_summary_harian($tanggal, $kantin_id)
    {
        if ($kantin_id) {
            $this->db->where('kantin_id', $kantin_id);
        }
        $total_transaksi = $this->db->where('DATE(created_at)', $tanggal)
            ->where('status', 'selesai')
            ->count_all_results('transaksi_kantin');

        if ($kantin_id) {
            $this->db->where('kantin_id', $kantin_id);
        }
        $total_pendapatan = $this->db->select('SUM(total_harga) as total')
            ->from('transaksi_kantin')
            ->where('DATE(created_at)', $tanggal)
            ->where('status', 'selesai')
            ->get()
            ->row()
            ->total ?? 0;

        if ($kantin_id) {
            $this->db->where('kantin_id', $kantin_id);
        }
        $total_item = $this->db->select('SUM(jumlah) as total')
            ->from('transaksi_kantin')
            ->where('DATE(created_at)', $tanggal)
            ->where('status', 'selesai')
            ->get()
            ->row()
            ->total ?? 0;

        if ($kantin_id) {
            $this->db->where('kantin_id', $kantin_id);
        }
        $santri_transaksi = $this->db->select('COUNT(DISTINCT santri_id) as total')
            ->from('transaksi_kantin')
            ->where('DATE(created_at)', $tanggal)
            ->where('status', 'selesai')
            ->get()
            ->row()
            ->total ?? 0;

        // Menu terlaris
        $this->db->select('
            menu_kantin.nama_menu,
            SUM(transaksi_kantin.jumlah) as total_terjual,
            SUM(transaksi_kantin.total_harga) as total_pendapatan
        ');
        $this->db->from('transaksi_kantin');
        $this->db->join('menu_kantin', 'menu_kantin.id = transaksi_kantin.menu_id');
        $this->db->where('DATE(transaksi_kantin.created_at)', $tanggal);
        $this->db->where('transaksi_kantin.status', 'selesai');
        if ($kantin_id) {
            $this->db->where('transaksi_kantin.kantin_id', $kantin_id);
        }
        $this->db->group_by('transaksi_kantin.menu_id');
        $this->db->order_by('total_terjual', 'DESC');
        $this->db->limit(5);
        $menu_terlaris = $this->db->get()->result();

        return [
            'total_transaksi' => $total_transaksi,
            'total_pendapatan' => $total_pendapatan,
            'total_item' => $total_item,
            'santri_transaksi' => $santri_transaksi,
            'menu_terlaris' => $menu_terlaris
        ];
    }

    // Method untuk mendapatkan transaksi mingguan
    private function get_transaksi_mingguan($tanggal_awal, $tanggal_akhir, $kantin_id)
    {
        $where_kantin1 = $kantin_id ? "AND mk.kantin_id = " . intval($kantin_id) : "";
        $where_kantin2 = $kantin_id ? "AND mk2.kantin_id = " . intval($kantin_id) : "";

        $sql = "
            SELECT
                t.created_at,
                t.jumlah,
                t.total_harga,
                CAST(mk.nama_menu AS CHAR CHARACTER SET utf8mb4) as nama_menu,
                CAST(t.keterangan AS CHAR CHARACTER SET utf8mb4) as keterangan,
                CAST(s.nama AS CHAR CHARACTER SET utf8mb4) as nama_pelanggan,
                s.nomor_induk,
                s.kelas,
                CAST(mk.pemilik AS CHAR CHARACTER SET utf8mb4) as pemilik,
                mk.harga_jual,
                mk.harga_beli,
                CAST(kantin.nama AS CHAR CHARACTER SET utf8mb4) as nama_kantin,
                CAST('santri' AS CHAR CHARACTER SET utf8mb4) as jenis
            FROM transaksi_kantin t
            JOIN santri s ON t.santri_id = s.id
            JOIN menu_kantin mk ON t.menu_id = mk.id
            JOIN kantin ON kantin.id = t.kantin_id
            WHERE DATE(t.created_at) >= '" . $this->db->escape_str($tanggal_awal) . "' AND DATE(t.created_at) <= '" . $this->db->escape_str($tanggal_akhir) . "' AND t.status = 'selesai' $where_kantin1

            UNION ALL

            SELECT
                t2.created_at,
                t2.jumlah,
                t2.total_harga,
                CAST(mk2.nama_menu AS CHAR CHARACTER SET utf8mb4) as nama_menu,
                CAST(t2.keterangan AS CHAR CHARACTER SET utf8mb4) as keterangan,
                CAST(u.nama AS CHAR CHARACTER SET utf8mb4) as nama_pelanggan,
                '' as nomor_induk,
                '' as kelas,
                CAST(mk2.pemilik AS CHAR CHARACTER SET utf8mb4) as pemilik,
                mk2.harga_jual,
                mk2.harga_beli,
                CAST(kantin2.nama AS CHAR CHARACTER SET utf8mb4) as nama_kantin,
                CAST('ustadz' AS CHAR CHARACTER SET utf8mb4) as jenis
            FROM transaksi_ustadz t2
            JOIN ustadz u ON t2.ustadz_id = u.id
            JOIN menu_kantin mk2 ON t2.menu_id = mk2.id
            JOIN kantin kantin2 ON kantin2.id = mk2.kantin_id
            WHERE DATE(t2.created_at) >= '" . $this->db->escape_str($tanggal_awal) . "' AND DATE(t2.created_at) <= '" . $this->db->escape_str($tanggal_akhir) . "' AND t2.status = 'selesai' $where_kantin2

            ORDER BY created_at ASC
        ";
        $result = $this->db->query($sql);
        if ($result === false) {
            log_message('error', '[LAPORAN] SQL ERROR: ' . (method_exists($this->db, 'error') ? $this->db->error()['message'] : $this->db->_error_message()));
            log_message('error', '[LAPORAN] SQL: ' . $sql);
            return [];
        }
        return $result->result();
    }

    // Method untuk mendapatkan transaksi bulanan
    private function get_transaksi_bulanan($tanggal_awal, $tanggal_akhir, $kantin_id)
    {
        $where_kantin1 = $kantin_id ? "AND mk.kantin_id = " . intval($kantin_id) : "";
        $where_kantin2 = $kantin_id ? "AND mk2.kantin_id = " . intval($kantin_id) : "";

        $sql = "
            SELECT
                t.created_at,
                t.jumlah,
                t.total_harga,
                CAST(mk.nama_menu AS CHAR CHARACTER SET utf8mb4) as nama_menu,
                CAST(t.keterangan AS CHAR CHARACTER SET utf8mb4) as keterangan,
                CAST(s.nama AS CHAR CHARACTER SET utf8mb4) as nama_pelanggan,
                s.nomor_induk,
                s.kelas,
                CAST(mk.pemilik AS CHAR CHARACTER SET utf8mb4) as pemilik,
                mk.harga_jual,
                CAST(kantin.nama AS CHAR CHARACTER SET utf8mb4) as nama_kantin,
                CAST('santri' AS CHAR CHARACTER SET utf8mb4) as jenis
            FROM transaksi_kantin t
            JOIN santri s ON t.santri_id = s.id
            JOIN menu_kantin mk ON t.menu_id = mk.id
            JOIN kantin ON kantin.id = t.kantin_id
            WHERE DATE(t.created_at) >= '" . $this->db->escape_str($tanggal_awal) . "' AND DATE(t.created_at) <= '" . $this->db->escape_str($tanggal_akhir) . "' AND t.status = 'selesai' $where_kantin1

            UNION ALL

            SELECT
                t2.created_at,
                t2.jumlah,
                t2.total_harga,
                CAST(mk2.nama_menu AS CHAR CHARACTER SET utf8mb4) as nama_menu,
                CAST(t2.keterangan AS CHAR CHARACTER SET utf8mb4) as keterangan,
                CAST(u.nama AS CHAR CHARACTER SET utf8mb4) as nama_pelanggan,
                '' as nomor_induk,
                '' as kelas,
                CAST(mk2.pemilik AS CHAR CHARACTER SET utf8mb4) as pemilik,
                mk2.harga_jual,
                CAST(kantin2.nama AS CHAR CHARACTER SET utf8mb4) as nama_kantin,
                CAST('ustadz' AS CHAR CHARACTER SET utf8mb4) as jenis
            FROM transaksi_ustadz t2
            JOIN ustadz u ON t2.ustadz_id = u.id
            JOIN menu_kantin mk2 ON t2.menu_id = mk2.id
            JOIN kantin kantin2 ON kantin2.id = mk2.kantin_id
            WHERE DATE(t2.created_at) >= '" . $this->db->escape_str($tanggal_awal) . "' AND DATE(t2.created_at) <= '" . $this->db->escape_str($tanggal_akhir) . "' AND t2.status = 'selesai' $where_kantin2

            ORDER BY created_at ASC
        ";
        $result = $this->db->query($sql);
        if ($result === false) {
            log_message('error', '[LAPORAN] SQL ERROR: ' . (method_exists($this->db, 'error') ? $this->db->error()['message'] : $this->db->_error_message()));
            log_message('error', '[LAPORAN] SQL: ' . $sql);
            return [];
        }
        return $result->result();
    }

    // Method untuk mendapatkan summary mingguan
    private function get_summary_mingguan($tanggal_awal, $tanggal_akhir, $kantin_id)
    {
        if ($kantin_id) {
            $this->db->where('kantin_id', $kantin_id);
        }
        $total_transaksi = $this->db->where('DATE(created_at) >=', $tanggal_awal)
            ->where('DATE(created_at) <=', $tanggal_akhir)
            ->where('status', 'selesai')
            ->count_all_results('transaksi_kantin');

        if ($kantin_id) {
            $this->db->where('kantin_id', $kantin_id);
        }
        $total_pendapatan = $this->db->select('SUM(total_harga) as total')
            ->from('transaksi_kantin')
            ->where('DATE(created_at) >=', $tanggal_awal)
            ->where('DATE(created_at) <=', $tanggal_akhir)
            ->where('status', 'selesai')
            ->get()
            ->row()
            ->total ?? 0;

        if ($kantin_id) {
            $this->db->where('kantin_id', $kantin_id);
        }
        $total_item = $this->db->select('SUM(jumlah) as total')
            ->from('transaksi_kantin')
            ->where('DATE(created_at) >=', $tanggal_awal)
            ->where('DATE(created_at) <=', $tanggal_akhir)
            ->where('status', 'selesai')
            ->get()
            ->row()
            ->total ?? 0;

        if ($kantin_id) {
            $this->db->where('kantin_id', $kantin_id);
        }
        $santri_transaksi = $this->db->select('COUNT(DISTINCT santri_id) as total')
            ->from('transaksi_kantin')
            ->where('DATE(created_at) >=', $tanggal_awal)
            ->where('DATE(created_at) <=', $tanggal_akhir)
            ->where('status', 'selesai')
            ->get()
            ->row()
            ->total ?? 0;

        // Menu terlaris
        $this->db->select('
            menu_kantin.nama_menu,
            SUM(transaksi_kantin.jumlah) as total_terjual,
            SUM(transaksi_kantin.total_harga) as total_pendapatan
        ');
        $this->db->from('transaksi_kantin');
        $this->db->join('menu_kantin', 'menu_kantin.id = transaksi_kantin.menu_id');
        $this->db->where('DATE(transaksi_kantin.created_at) >=', $tanggal_awal);
        $this->db->where('DATE(transaksi_kantin.created_at) <=', $tanggal_akhir);
        $this->db->where('transaksi_kantin.status', 'selesai');
        if ($kantin_id) {
            $this->db->where('transaksi_kantin.kantin_id', $kantin_id);
        }
        $this->db->group_by('transaksi_kantin.menu_id');
        $this->db->order_by('total_terjual', 'DESC');
        $this->db->limit(5);
        $menu_terlaris = $this->db->get()->result();

        return [
            'total_transaksi' => $total_transaksi,
            'total_pendapatan' => $total_pendapatan,
            'total_item' => $total_item,
            'santri_transaksi' => $santri_transaksi,
            'menu_terlaris' => $menu_terlaris
        ];
    }

    // Method untuk mendapatkan summary bulanan
    private function get_summary_bulanan($tanggal_awal, $tanggal_akhir, $kantin_id)
    {
        if ($kantin_id) {
            $this->db->where('kantin_id', $kantin_id);
        }
        $total_transaksi = $this->db->where('DATE(created_at) >=', $tanggal_awal)
            ->where('DATE(created_at) <=', $tanggal_akhir)
            ->where('status', 'selesai')
            ->count_all_results('transaksi_kantin');

        if ($kantin_id) {
            $this->db->where('kantin_id', $kantin_id);
        }
        $total_pendapatan = $this->db->select('SUM(total_harga) as total')
            ->from('transaksi_kantin')
            ->where('DATE(created_at) >=', $tanggal_awal)
            ->where('DATE(created_at) <=', $tanggal_akhir)
            ->where('status', 'selesai')
            ->get()
            ->row()
            ->total ?? 0;

        if ($kantin_id) {
            $this->db->where('kantin_id', $kantin_id);
        }
        $total_item = $this->db->select('SUM(jumlah) as total')
            ->from('transaksi_kantin')
            ->where('DATE(created_at) >=', $tanggal_awal)
            ->where('DATE(created_at) <=', $tanggal_akhir)
            ->where('status', 'selesai')
            ->get()
            ->row()
            ->total ?? 0;

        if ($kantin_id) {
            $this->db->where('kantin_id', $kantin_id);
        }
        $santri_transaksi = $this->db->select('COUNT(DISTINCT santri_id) as total')
            ->from('transaksi_kantin')
            ->where('DATE(created_at) >=', $tanggal_awal)
            ->where('DATE(created_at) <=', $tanggal_akhir)
            ->where('status', 'selesai')
            ->get()
            ->row()
            ->total ?? 0;

        // Menu terlaris
        $this->db->select('
            menu_kantin.nama_menu,
            SUM(transaksi_kantin.jumlah) as total_terjual,
            SUM(transaksi_kantin.total_harga) as total_pendapatan
        ');
        $this->db->from('transaksi_kantin');
        $this->db->join('menu_kantin', 'menu_kantin.id = transaksi_kantin.menu_id');
        $this->db->where('DATE(transaksi_kantin.created_at) >=', $tanggal_awal);
        $this->db->where('DATE(transaksi_kantin.created_at) <=', $tanggal_akhir);
        $this->db->where('transaksi_kantin.status', 'selesai');
        if ($kantin_id) {
            $this->db->where('transaksi_kantin.kantin_id', $kantin_id);
        }
        $this->db->group_by('transaksi_kantin.menu_id');
        $this->db->order_by('total_terjual', 'DESC');
        $this->db->limit(5);
        $menu_terlaris = $this->db->get()->result();

        return [
            'total_transaksi' => $total_transaksi,
            'total_pendapatan' => $total_pendapatan,
            'total_item' => $total_item,
            'santri_transaksi' => $santri_transaksi,
            'menu_terlaris' => $menu_terlaris
        ];
    }

    // Method untuk chart labels mingguan
    private function get_chart_labels_mingguan($tanggal_awal, $tanggal_akhir, $kantin_id)
    {
        $labels = [];
        $current = new DateTime($tanggal_awal);
        $end = new DateTime($tanggal_akhir);

        while ($current <= $end) {
            $labels[] = $current->format('d/m');
            $current->modify('+1 day');
        }

        return $labels;
    }

    // Method untuk chart data mingguan
    private function get_chart_data_mingguan($tanggal_awal, $tanggal_akhir, $kantin_id)
    {
        $data = [];
        $current = new DateTime($tanggal_awal);
        $end = new DateTime($tanggal_akhir);

        while ($current <= $end) {
            $tanggal = $current->format('Y-m-d');

            if ($kantin_id) {
                $this->db->where('kantin_id', $kantin_id);
            }
            $pendapatan = $this->db->select('SUM(total_harga) as total')
                ->from('transaksi_kantin')
                ->where('DATE(created_at)', $tanggal)
                ->where('status', 'selesai')
                ->get()
                ->row()
                ->total ?? 0;

            $data[] = $pendapatan;
            $current->modify('+1 day');
        }

        return $data;
    }

    // Method untuk chart labels bulanan
    private function get_chart_labels_bulanan($tanggal_awal, $tanggal_akhir, $kantin_id)
    {
        $labels = [];
        $current = new DateTime($tanggal_awal);
        $end = new DateTime($tanggal_akhir);

        while ($current <= $end) {
            $labels[] = $current->format('d/m');
            $current->modify('+1 day');
        }

        return $labels;
    }

    // Method untuk chart data bulanan
    private function get_chart_data_bulanan($tanggal_awal, $tanggal_akhir, $kantin_id)
    {
        $data = [];
        $current = new DateTime($tanggal_awal);
        $end = new DateTime($tanggal_akhir);

        while ($current <= $end) {
            $tanggal = $current->format('Y-m-d');

            if ($kantin_id) {
                $this->db->where('kantin_id', $kantin_id);
            }
            $pendapatan = $this->db->select('SUM(total_harga) as total')
                ->from('transaksi_kantin')
                ->where('DATE(created_at)', $tanggal)
                ->where('status', 'selesai')
                ->get()
                ->row()
                ->total ?? 0;

            $data[] = $pendapatan;
            $current->modify('+1 day');
        }

        return $data;
    }

    private function get_chart_labels($tanggal, $kantin_id)
    {
        $labels = [];
        for ($i = 0; $i < 24; $i++) {
            $labels[] = sprintf('%02d:00', $i);
        }
        return $labels;
    }

    private function get_chart_data($tanggal, $kantin_id)
    {
        $data = array_fill(0, 24, 0);

        $this->db->select('HOUR(created_at) as jam, SUM(total_harga) as total')
            ->from('transaksi_kantin')
            ->where('DATE(created_at)', $tanggal)
            ->where('status', 'selesai')
            ->where('kantin_id', $kantin_id)
            ->group_by('HOUR(created_at)')
            ->order_by('jam', 'ASC');

        $result = $this->db->get()->result();

        foreach ($result as $row) {
            $data[$row->jam] = (int)$row->total;
        }

        return $data;
    }
}
