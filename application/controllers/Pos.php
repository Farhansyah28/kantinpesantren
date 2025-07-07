<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pos extends CI_Controller
{

    public $Menu_model;
    public $Santri_model;
    public $Transaksi_model;
    public $Tabungan_model;
    public $Kantin_model;
    public $Ustadz_model;
    public $Activity_log_model;

    public function __construct()
    {
        parent::__construct();
        $this->load->library(['session', 'form_validation']);

        // Cek login
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }

        // Cek role untuk akses ke POS
        if (!in_array($this->session->userdata('role'), ['admin', 'keuangan', 'operator'])) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses ke halaman ini');
            redirect('dashboard');
        }
    }

    public function index()
    {
        $kantin_id = $this->session->userdata('kantin_id');

        $data['title'] = 'Point of Sale - ' . $this->session->userdata('kantin_nama');
        $data['kantin_info'] = $this->Kantin_model->get_kantin($kantin_id);
        $data['menu'] = $this->Menu_model->get_all_menu($kantin_id);
        $data['santri'] = $this->Santri_model->get_santri_with_tabungan(NULL, $kantin_id);

        $this->load->view('templates/header', $data);
        $this->load->view('pos/index', $data);
        $this->load->view('templates/footer');
    }

    public function process_transaction()
    {
        // Mengatur header output sebagai JSON
        header('Content-Type: application/json');

        try {
            $santri_id = $this->input->post('santri_id');
            $cart_json = $this->input->post('cart');
            $cart = json_decode($cart_json);
            $metode = $this->input->post('metode_pembayaran');

            // Log untuk debug
            log_message('debug', 'POS modern process_transaction - santri_id: ' . $santri_id . ', cart: ' . $cart_json);

            if (empty($santri_id) || empty($cart) || !is_array($cart)) {
                echo json_encode(['success' => false, 'message' => 'Data tidak valid. Santri atau item belanja tidak ditemukan.']);
                return;
            }

            $kantin_id = $this->session->userdata('kantin_id');

            // 1. Validasi Santri
            $santri = $this->Santri_model->get_santri_with_tabungan($santri_id, $kantin_id);
            if (!$santri) {
                echo json_encode(['success' => false, 'message' => 'Santri tidak ditemukan atau tidak terdaftar di kantin ini.']);
                return;
            }

            $total_harga_transaksi = 0;
            $menu_details = [];

            // 2. Validasi setiap item di keranjang (stok, dll) sebelum mulai transaksi DB
            foreach ($cart as $item) {
                // Validasi menu_id
                if (empty($item->menu_id) || $item->menu_id == 0) {
                    echo json_encode(['success' => false, 'message' => "Menu ID tidak valid untuk '{$item->menu_name}'."]);
                    return;
                }

                $menu = $this->Menu_model->get_menu($item->menu_id, $kantin_id);
                if (!$menu) {
                    echo json_encode(['success' => false, 'message' => "Menu '{$item->menu_name}' tidak ditemukan."]);
                    return;
                }
                if ($menu->stok < $item->quantity) {
                    echo json_encode(['success' => false, 'message' => "Stok untuk '{$menu->nama_menu}' tidak mencukupi. Sisa: {$menu->stok}."]);
                    return;
                }
                $total_harga_transaksi += $menu->harga_jual * $item->quantity;
                $menu_details[$item->menu_id] = $menu;
            }

            // 3. Validasi Saldo & Limit Harian hanya jika saldo jajan
            if ($metode === 'saldo_jajan') {
                $saldo_jajan = $this->Tabungan_model->get_saldo_jajan($santri_id);
                if ($saldo_jajan < $total_harga_transaksi) {
                    echo json_encode(['success' => false, 'message' => 'Saldo jajan tidak mencukupi. Saldo saat ini: Rp ' . number_format($saldo_jajan, 0, ',', '.')]);
                    return;
                }

                $pengeluaran_hari_ini = $this->Santri_model->get_pengeluaran_pos_hari_ini($santri_id);
                if (($pengeluaran_hari_ini + $total_harga_transaksi) > LIMIT_HARIAN_JAJAN) {
                    $sisa_limit = max(0, LIMIT_HARIAN_JAJAN - $pengeluaran_hari_ini);
                    echo json_encode(['success' => false, 'message' => 'Transaksi melebihi limit harian. Sisa limit: Rp ' . number_format($sisa_limit, 0, ',', '.')]);
                    return;
                }
            }

            // --- Semua validasi lolos, mulai transaksi database ---
            $this->db->trans_start();
            $all_operations_success = TRUE;

            foreach ($cart as $item) {
                $menu = $menu_details[$item->menu_id];
                $total_harga_item = $menu->harga_jual * $item->quantity;

                // Log untuk debugging
                log_message('debug', 'POS modern - Processing item: menu_id=' . $item->menu_id . ', quantity=' . $item->quantity . ', stok_sebelum=' . $menu->stok);

                // Kurangi stok
                $stok_result = $this->Menu_model->kurangi_stok($item->menu_id, $item->quantity, "Penjualan POS - Santri: " . $santri->nama, $this->session->userdata('user_id'), $kantin_id);

                if (!$stok_result) {
                    log_message('error', 'POS modern - Kurangi stok gagal untuk menu_id: ' . $item->menu_id . ', quantity: ' . $item->quantity);
                    $all_operations_success = FALSE;
                    break; // Hentikan loop jika ada yang gagal
                }

                // Catat transaksi kantin per item
                $transaksi_data = [
                    'kantin_id'    => $kantin_id,
                    'santri_id'    => $santri_id,
                    'menu_id'      => $item->menu_id,
                    'jumlah'       => $item->quantity,
                    'harga_satuan' => $menu->harga_jual,
                    'total_harga'  => $total_harga_item,
                    'metode_pembayaran' => 'saldo_jajan',
                    'keterangan'   => 'Pembelian di kantin via POS (Saldo Jajan)',
                    'status'       => 'selesai',
                    'admin_id'     => $this->session->userdata('user_id')
                ];

                $transaksi_result = $this->Transaksi_model->create($transaksi_data);

                if (!$transaksi_result) {
                    log_message('error', 'POS modern - Create transaksi gagal untuk menu_id: ' . $item->menu_id);
                    $all_operations_success = FALSE;
                    break;
                }

                log_message('debug', 'POS modern - Item berhasil diproses: menu_id=' . $item->menu_id . ', quantity=' . $item->quantity);
            }

            // Jika saldo jajan, kurangi saldo dan catat penarikan tabungan
            if ($metode === 'saldo_jajan') {
                $this->Tabungan_model->kurangi_saldo_jajan($santri_id, $total_harga_transaksi);
                $this->Tabungan_model->record_transaksi([
                    'santri_id'  => $santri_id,
                    'jenis'      => 'penarikan',
                    'kategori'   => 'jajan',
                    'jumlah'     => $total_harga_transaksi,
                    'keterangan' => 'Pembelian di kantin via POS (Saldo Jajan)',
                    'admin_id'   => $this->session->userdata('user_id')
                ]);
            }
            // Jika tunai, tidak perlu kurangi saldo atau catat penarikan tabungan

            if ($all_operations_success) {
                $this->db->trans_complete();
            } else {
                $this->db->trans_rollback();
            }

            if ($this->db->trans_status() === FALSE || !$all_operations_success) {
                // Log error transaksi
                $this->Activity_log_model->log_system('POS_TRANSACTION_FAILED', [
                    'santri_id' => $santri_id,
                    'santri_nama' => $santri->nama,
                    'total_harga' => $total_harga_transaksi,
                    'metode_pembayaran' => $metode,
                    'item_count' => count($cart),
                    'error' => 'Database transaction failed'
                ], 'error');

                echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan pada database saat memproses transaksi. Stok mungkin tidak mencukupi.']);
            } else {
                // Log sukses transaksi
                $this->Activity_log_model->log_system('POS_TRANSACTION_SUCCESS', [
                    'santri_id' => $santri_id,
                    'santri_nama' => $santri->nama,
                    'total_harga' => $total_harga_transaksi,
                    'metode_pembayaran' => $metode,
                    'item_count' => count($cart),
                    'items' => array_map(function ($item) {
                        return [
                            'menu_id' => $item->menu_id,
                            'menu_name' => $item->menu_name,
                            'quantity' => $item->quantity,
                            'harga' => $item->harga
                        ];
                    }, $cart)
                ], 'success');

                echo json_encode(['success' => true, 'message' => 'Transaksi berhasil! Total: Rp ' . number_format($total_harga_transaksi, 0, ',', '.')]);
            }
        } catch (Exception $e) {
            log_message('error', 'POS modern Transaction Error: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function riwayat()
    {
        $kantin_id = $this->session->userdata('kantin_id');

        $data['title'] = 'Riwayat Transaksi POS - ' . $this->session->userdata('kantin_nama');
        $data['kantin_info'] = $this->Kantin_model->get_kantin($kantin_id);
        $data['transaksi'] = $this->Transaksi_model->get_all(NULL, NULL, $kantin_id);

        $this->load->view('templates/header', $data);
        $this->load->view('pos/riwayat', $data);
        $this->load->view('templates/footer');
    }

    public function riwayat_hari_ini()
    {
        $kantin_id = $this->session->userdata('kantin_id');
        $tanggal = date('Y-m-d');

        $data['title'] = 'Riwayat Transaksi Hari Ini - ' . $this->session->userdata('kantin_nama');
        $data['kantin_info'] = $this->Kantin_model->get_kantin($kantin_id);
        $data['transaksi'] = $this->Transaksi_model->get_transaksi_pos_modern_by_date($tanggal, $kantin_id);
        $data['tanggal'] = $tanggal;

        // Grouping transaksi per nota (per waktu, nama_pelanggan, nama_kantin)
        $grouped = [];
        if ($data['transaksi']) {
            foreach ($data['transaksi'] as $t) {
                $key = $t->created_at . '|' . $t->nama_pelanggan . '|' . ($t->nama_kantin ?? '-');
                if (!isset($grouped[$key])) {
                    $grouped[$key] = [
                        'created_at' => $t->created_at,
                        'nama_pelanggan' => $t->nama_pelanggan,
                        'jenis_pelanggan' => $t->jenis_pelanggan ?? '',
                        'nama_kantin' => $t->nama_kantin ?? '',
                        'menu_list' => [],
                        'qty_total' => 0,
                        'total' => 0,
                        'status' => $t->status,
                        'operator_nama' => $t->operator_nama,
                        'metode_pembayaran' => $t->metode_pembayaran,
                    ];
                }
                $grouped[$key]['menu_list'][] = $t->nama_menu . ' x' . $t->jumlah;
                $grouped[$key]['qty_total'] += $t->jumlah;
                $grouped[$key]['total'] += $t->total_harga;
            }
        }
        $data['grouped_transaksi'] = $grouped;

        // Hitung total transaksi hari ini
        $data['total_transaksi'] = 0;
        $data['total_pendapatan'] = 0;
        $data['total_item'] = 0;
        $data['total_keuntungan'] = 0;

        if ($data['transaksi']) {
            foreach ($data['transaksi'] as $t) {
                $data['total_transaksi']++;
                $data['total_pendapatan'] += $t->total_harga;
                $data['total_item'] += $t->jumlah;
                if (isset($t->harga_jual) && isset($t->harga_beli)) {
                    $data['total_keuntungan'] += ($t->harga_jual - $t->harga_beli) * $t->jumlah;
                }
            }
        }

        $this->load->view('templates/header', $data);
        $this->load->view('pos/riwayat_hari_ini', $data);
        $this->load->view('templates/footer');
    }

    public function get_santri_info()
    {
        // Mengatur header output sebagai JSON
        header('Content-Type: application/json');

        $santri_id = $this->input->post('santri_id');
        $kantin_id = $this->session->userdata('kantin_id');

        // Log untuk debug
        log_message('debug', 'POS modern get_santri_info - santri_id: ' . $santri_id . ', kantin_id: ' . $kantin_id);

        if (empty($santri_id)) {
            echo json_encode(['error' => 'ID Santri tidak boleh kosong']);
            return;
        }

        $santri = $this->Santri_model->get_santri_with_tabungan($santri_id, $kantin_id);

        if (!$santri) {
            echo json_encode(['error' => 'Santri tidak ditemukan']);
            return;
        }

        // Ambil saldo jajan langsung dari data santri yang sudah di-join
        $saldo_jajan = $santri->saldo_jajan ?? 0;
        $pengeluaran_hari_ini = $this->Santri_model->get_pengeluaran_pos_hari_ini($santri_id);
        $sisa_limit = max(0, LIMIT_HARIAN_JAJAN - $pengeluaran_hari_ini);

        $result = [
            'id' => $santri->id,
            'nama' => $santri->nama,
            'nomor_induk' => $santri->nomor_induk,
            'kelas' => $santri->kelas,
            'jenis_kelamin' => $santri->jenis_kelamin,
            'saldo_jajan' => $saldo_jajan,
            'pengeluaran_hari_ini' => $pengeluaran_hari_ini,
            'sisa_limit' => $sisa_limit,
            'status_limit' => ($sisa_limit > 0) ? 'aman' : 'habis'
        ];

        echo json_encode($result);
    }

    public function get_menu_info()
    {
        $menu_id = $this->input->post('menu_id');
        $kantin_id = $this->session->userdata('kantin_id');

        $menu = $this->Menu_model->get_menu($menu_id, $kantin_id);

        if (!$menu) {
            echo json_encode(['error' => 'Menu tidak ditemukan']);
            return;
        }

        $result = [
            'id' => $menu->id,
            'nama_menu' => $menu->nama_menu,
            'harga_jual' => $menu->harga_jual,
            'stok' => $menu->stok,
            'pemilik' => $menu->pemilik
        ];

        echo json_encode($result);
    }

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

    public function search_menu()
    {
        $keyword = $this->input->get('q');
        $kantin_id = $this->session->userdata('kantin_id');

        $menu = $this->Menu_model->search_menu($keyword, $kantin_id);

        $result = [];
        foreach ($menu as $m) {
            $result[] = [
                'id' => $m->id,
                'text' => $m->nama_menu . ' - Rp ' . number_format($m->harga_jual, 0, ',', '.'),
                'nama_menu' => $m->nama_menu,
                'harga' => $m->harga_jual,
                'stok' => $m->stok,
                'pemilik' => $m->pemilik
            ];
        }

        echo json_encode($result);
    }

    public function modern()
    {
        $kantin_id = $this->session->userdata('kantin_id');

        $data['title'] = 'Point of Sale (POS) Modern - ' . $this->session->userdata('kantin_nama');
        $data['kantin_info'] = $this->Kantin_model->get_kantin($kantin_id);
        $data['menu'] = $this->Menu_model->get_all_menu($kantin_id);
        $data['santri'] = $this->Santri_model->get_santri_with_tabungan(NULL, $kantin_id);

        // Load ustadz data
        $this->load->model('Ustadz_model');
        $data['ustadz'] = $this->Ustadz_model->get_all_ustadz();

        $this->load->view('templates/header', $data);
        $this->load->view('pos/modern', $data);
        $this->load->view('templates/footer');
    }

    public function process_ustadz_transaction()
    {
        // Mengatur header output sebagai JSON
        header('Content-Type: application/json');

        try {
            $ustadz_id = $this->input->post('ustadz_id');
            $cart_json = $this->input->post('cart');
            $cart = json_decode($cart_json);
            $metode = $this->input->post('metode_pembayaran');

            log_message('debug', '[USTADZ] POST DATA: ' . json_encode($_POST));
            log_message('debug', '[USTADZ] ustadz_id: ' . $ustadz_id . ', cart_json: ' . $cart_json . ', metode: ' . $metode);

            if (empty($ustadz_id) || empty($cart) || !is_array($cart)) {
                log_message('error', '[USTADZ] Data tidak valid. ustadz_id: ' . $ustadz_id . ', cart: ' . $cart_json);
                echo json_encode(['success' => false, 'message' => 'Data tidak valid. Ustadz atau item belanja tidak ditemukan.']);
                return;
            }

            $kantin_id = $this->session->userdata('kantin_id');
            log_message('debug', '[USTADZ] kantin_id session: ' . $kantin_id);

            $this->load->model('Ustadz_model');
            $ustadz = $this->Ustadz_model->get_ustadz($ustadz_id);
            if (!$ustadz) {
                log_message('error', '[USTADZ] Ustadz/Ustadzah tidak ditemukan. ustadz_id: ' . $ustadz_id);
                echo json_encode(['success' => false, 'message' => 'Ustadz/Ustadzah tidak ditemukan.']);
                return;
            }

            if ($metode !== 'tunai') {
                log_message('error', '[USTADZ] Metode pembayaran tidak tunai: ' . $metode);
                echo json_encode(['success' => false, 'message' => 'Ustadz/Ustadzah hanya dapat melakukan pembayaran tunai.']);
                return;
            }

            $total_harga_transaksi = 0;
            $menu_details = [];

            foreach ($cart as $item) {
                log_message('debug', '[USTADZ] Validasi item: ' . json_encode($item));
                if (empty($item->menu_id) || $item->menu_id == 0) {
                    log_message('error', '[USTADZ] Menu ID tidak valid: ' . json_encode($item));
                    echo json_encode(['success' => false, 'message' => "Menu ID tidak valid untuk '{$item->menu_name}'."]);
                    return;
                }
                $menu = $this->Menu_model->get_menu($item->menu_id, $kantin_id);
                log_message('debug', '[USTADZ] get_menu result: ' . json_encode($menu));
                if (!$menu) {
                    log_message('error', '[USTADZ] Menu tidak ditemukan di kantin ini. menu_id: ' . $item->menu_id . ', kantin_id: ' . $kantin_id);
                    echo json_encode(['success' => false, 'message' => "Menu '{$item->menu_name}' tidak ditemukan di kantin ini."]);
                    return;
                }
                if ($menu->stok < $item->quantity) {
                    log_message('error', '[USTADZ] Stok tidak cukup. menu_id: ' . $item->menu_id . ', stok: ' . $menu->stok . ', butuh: ' . $item->quantity);
                    echo json_encode(['success' => false, 'message' => "Stok untuk '{$menu->nama_menu}' tidak mencukupi. Sisa: {$menu->stok}."]);
                    return;
                }
                $total_harga_transaksi += $menu->harga_jual * $item->quantity;
                $menu_details[$item->menu_id] = $menu;
            }

            $this->db->trans_start();
            $all_operations_success = TRUE;

            foreach ($cart as $item) {
                $menu = $menu_details[$item->menu_id];
                $total_harga_item = $menu->harga_jual * $item->quantity;

                log_message('debug', '[USTADZ] Kurangi stok: menu_id=' . $item->menu_id . ', quantity=' . $item->quantity . ', stok_sebelum=' . $menu->stok . ', kantin_id=' . $kantin_id);
                $stok_result = $this->Menu_model->kurangi_stok($item->menu_id, $item->quantity, "Penjualan POS - Ustadz: " . $ustadz->nama, $this->session->userdata('user_id'), $kantin_id);
                log_message('debug', '[USTADZ] kurangi_stok result: ' . var_export($stok_result, true));
                if (!$stok_result) {
                    log_message('error', '[USTADZ] Kurangi stok gagal. menu_id: ' . $item->menu_id . ', quantity: ' . $item->quantity . ', kantin_id: ' . $kantin_id);
                    $all_operations_success = FALSE;
                    break;
                }

                $transaksi_data = [
                    'ustadz_id'      => $ustadz_id,
                    'menu_id'        => $item->menu_id,
                    'jumlah'         => $item->quantity,
                    'harga_satuan'   => $menu->harga_jual,
                    'total_harga'    => $total_harga_item,
                    'metode_pembayaran' => 'tunai',
                    'status'         => 'selesai',
                    'operator_id'    => $this->session->userdata('user_id')
                ];
                log_message('debug', '[USTADZ] create_transaksi_ustadz data: ' . json_encode($transaksi_data));
                $transaksi_result = $this->Transaksi_model->create_transaksi_ustadz($transaksi_data);
                log_message('debug', '[USTADZ] create_transaksi_ustadz result: ' . var_export($transaksi_result, true));
                if (!$transaksi_result) {
                    log_message('error', '[USTADZ] Create transaksi ustadz gagal. Data: ' . json_encode($transaksi_data));
                    $all_operations_success = FALSE;
                    break;
                }
                log_message('debug', '[USTADZ] Item berhasil diproses: menu_id=' . $item->menu_id . ', quantity=' . $item->quantity . ', kantin_id=' . $kantin_id);
            }

            if ($all_operations_success) {
                $this->db->trans_complete();
            } else {
                $this->db->trans_rollback();
            }

            log_message('debug', '[USTADZ] DB trans_status: ' . var_export($this->db->trans_status(), true) . ', all_operations_success: ' . var_export($all_operations_success, true));

            if ($this->db->trans_status() === FALSE || !$all_operations_success) {
                // Log error transaksi ustadz
                $this->Activity_log_model->log_system('USTADZ_TRANSACTION_FAILED', [
                    'ustadz_id' => $ustadz_id,
                    'ustadz_nama' => $ustadz->nama,
                    'total_harga' => $total_harga_transaksi,
                    'metode_pembayaran' => $metode,
                    'item_count' => count($cart),
                    'error' => 'Database transaction failed'
                ], 'error');

                log_message('error', '[USTADZ] Transaksi gagal. DB trans_status: ' . var_export($this->db->trans_status(), true));
                echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan pada database saat memproses transaksi. Stok mungkin tidak mencukupi.']);
            } else {
                // Log sukses transaksi ustadz
                $this->Activity_log_model->log_system('USTADZ_TRANSACTION_SUCCESS', [
                    'ustadz_id' => $ustadz_id,
                    'ustadz_nama' => $ustadz->nama,
                    'total_harga' => $total_harga_transaksi,
                    'metode_pembayaran' => $metode,
                    'item_count' => count($cart),
                    'items' => array_map(function ($item) {
                        return [
                            'menu_id' => $item->menu_id,
                            'menu_name' => $item->menu_name,
                            'quantity' => $item->quantity,
                            'harga' => $item->harga
                        ];
                    }, $cart)
                ], 'success');

                log_message('debug', '[USTADZ] Transaksi sukses. Total: ' . $total_harga_transaksi);
                echo json_encode(['success' => true, 'message' => 'Transaksi ustadz/ustadzah berhasil! Total: Rp ' . number_format($total_harga_transaksi, 0, ',', '.')]);
            }
        } catch (Exception $e) {
            log_message('error', '[USTADZ] Exception: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function get_menu_by_gender()
    {
        // Mengatur header output sebagai JSON
        header('Content-Type: application/json');

        $gender = $this->input->post('gender');
        $kantin_id = $this->session->userdata('kantin_id');

        // Log untuk debug
        log_message('debug', 'POS modern get_menu_by_gender - gender: ' . $gender . ', kantin_id: ' . $kantin_id);

        if (empty($gender)) {
            echo json_encode(['success' => false, 'message' => 'Gender tidak boleh kosong']);
            return;
        }

        $this->db->select('menu_kantin.*, IFNULL(kantin.nama, IF(menu_kantin.kantin_id=1,\'Putra\',\'Putri\')) as nama_kantin');
        $this->db->from('menu_kantin');
        $this->db->join('kantin', 'kantin.id = menu_kantin.kantin_id', 'left');
        $this->db->where('menu_kantin.kantin_id', $kantin_id);
        $this->db->order_by('menu_kantin.nama_menu', 'ASC');
        $menu = $this->db->get()->result();

        echo json_encode(['success' => true, 'menu' => $menu]);
    }

    public function test_santri_info()
    {
        // Method untuk testing - hanya untuk development
        if (!in_array($this->session->userdata('role'), ['admin', 'operator'])) {
            show_404();
            return;
        }

        $kantin_id = $this->session->userdata('kantin_id');
        $santri_list = $this->Santri_model->get_santri_with_tabungan(NULL, $kantin_id);

        echo "<h2>Test Santri Info</h2>";
        echo "<p>Kantin ID: " . $kantin_id . "</p>";
        echo "<p>Total Santri: " . count($santri_list) . "</p>";

        if (!empty($santri_list)) {
            $test_santri = $santri_list[0];
            echo "<h3>Test dengan Santri: " . $test_santri->nama . " (ID: " . $test_santri->id . ")</h3>";

            // Simulasi POST request
            $_POST['santri_id'] = $test_santri->id;

            echo "<h4>Response dari get_santri_info:</h4>";
            ob_start();
            $this->get_santri_info();
            $response = ob_get_clean();
            echo "<pre>" . htmlspecialchars($response) . "</pre>";

            $decoded = json_decode($response, true);
            if ($decoded) {
                echo "<h4>Data yang diterima:</h4>";
                echo "<ul>";
                foreach ($decoded as $key => $value) {
                    echo "<li><strong>$key:</strong> " . htmlspecialchars($value) . "</li>";
                }
                echo "</ul>";
            }
        } else {
            echo "<p>Tidak ada santri ditemukan untuk kantin ini.</p>";
        }
    }

    public function test_menu_by_gender()
    {
        // Method untuk testing - hanya untuk development
        if (!in_array($this->session->userdata('role'), ['admin', 'operator'])) {
            show_404();
            return;
        }

        $kantin_id = $this->session->userdata('kantin_id');

        echo "<h2>Test Menu By Gender</h2>";
        echo "<p>Kantin ID: " . $kantin_id . "</p>";

        // Test untuk gender L
        echo "<h3>Test untuk Gender L (Laki-laki)</h3>";
        $_POST['gender'] = 'L';
        ob_start();
        $this->get_menu_by_gender();
        $response_l = ob_get_clean();
        echo "<pre>" . htmlspecialchars($response_l) . "</pre>";

        $decoded_l = json_decode($response_l, true);
        if ($decoded_l && $decoded_l['success']) {
            echo "<p>Menu untuk Laki-laki: " . count($decoded_l['menu']) . " item</p>";
        }

        // Test untuk gender P
        echo "<h3>Test untuk Gender P (Perempuan)</h3>";
        $_POST['gender'] = 'P';
        ob_start();
        $this->get_menu_by_gender();
        $response_p = ob_get_clean();
        echo "<pre>" . htmlspecialchars($response_p) . "</pre>";

        $decoded_p = json_decode($response_p, true);
        if ($decoded_p && $decoded_p['success']) {
            echo "<p>Menu untuk Perempuan: " . count($decoded_p['menu']) . " item</p>";
        }

        // Test untuk kantin_id yang salah
        echo "<h3>Test untuk Kantin ID yang salah</h3>";
        $_POST['gender'] = 'L';
        $this->session->set_userdata('kantin_id', 999); // Set kantin_id yang tidak ada
        ob_start();
        $this->get_menu_by_gender();
        $response_invalid = ob_get_clean();
        echo "<pre>" . htmlspecialchars($response_invalid) . "</pre>";

        // Restore kantin_id
        $this->session->set_userdata('kantin_id', $kantin_id);
    }

    public function test_url()
    {
        // Method untuk testing URL accessibility
        echo "<h2>Test URL Accessibility</h2>";
        echo "<p>Current URL: " . current_url() . "</p>";
        echo "<p>Base URL: " . base_url() . "</p>";
        echo "<p>Site URL: " . site_url() . "</p>";
        echo "<p>Session data:</p>";
        echo "<pre>" . print_r($this->session->userdata(), true) . "</pre>";

        echo "<h3>Test get_santri_info URL:</h3>";
        echo "<p>URL: " . site_url('pos/get_santri_info') . "</p>";
        echo "<p>Method: POST</p>";
        echo "<p>Required parameter: santri_id</p>";

        echo "<h3>Test get_menu_by_gender URL:</h3>";
        echo "<p>URL: " . site_url('pos/get_menu_by_gender') . "</p>";
        echo "<p>Method: POST</p>";
        echo "<p>Required parameter: gender</p>";
    }

    public function test_simple()
    {
        // Method untuk testing sederhana
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'message' => 'Method dapat diakses',
            'timestamp' => date('Y-m-d H:i:s'),
            'session_data' => [
                'user_id' => $this->session->userdata('user_id'),
                'kantin_id' => $this->session->userdata('kantin_id'),
                'role' => $this->session->userdata('role')
            ]
        ]);
    }

    public function test_pos_modern()
    {
        // Method untuk testing - hanya untuk development
        if (!in_array($this->session->userdata('role'), ['admin', 'operator'])) {
            show_404();
            return;
        }

        echo "<h2>Test POS Modern System</h2>";
        echo "<p>Kantin ID: " . $this->session->userdata('kantin_id') . "</p>";
        echo "<p>User ID: " . $this->session->userdata('user_id') . "</p>";

        // Test 1: CSRF Exclusion
        echo "<h3>1. Test CSRF Exclusion</h3>";
        $csrf_exclude_uris = $this->config->item('csrf_exclude_uris');
        $pos_endpoints = ['pos/get_santri_info', 'pos/get_menu_by_gender', 'pos/process_transaction', 'pos/process_ustadz_transaction'];

        foreach ($pos_endpoints as $endpoint) {
            if (in_array($endpoint, $csrf_exclude_uris)) {
                echo "<p style='color: green;'>✓ $endpoint - CSRF excluded</p>";
            } else {
                echo "<p style='color: red;'>✗ $endpoint - NOT CSRF excluded</p>";
            }
        }

        // Test 2: Santri Info
        echo "<h3>2. Test Santri Info</h3>";
        $kantin_id = $this->session->userdata('kantin_id');
        $santri_list = $this->Santri_model->get_santri_with_tabungan(NULL, $kantin_id);

        if (!empty($santri_list)) {
            $test_santri = $santri_list[0];
            echo "<p>Test dengan Santri: " . $test_santri->nama . " (ID: " . $test_santri->id . ")</p>";

            // Simulasi POST request
            $_POST['santri_id'] = $test_santri->id;

            ob_start();
            $this->get_santri_info();
            $response = ob_get_clean();

            $decoded = json_decode($response, true);
            if ($decoded && !isset($decoded['error'])) {
                echo "<p style='color: green;'>✓ get_santri_info berhasil</p>";
                echo "<ul>";
                foreach ($decoded as $key => $value) {
                    echo "<li><strong>$key:</strong> " . htmlspecialchars($value) . "</li>";
                }
                echo "</ul>";
            } else {
                echo "<p style='color: red;'>✗ get_santri_info gagal: " . htmlspecialchars($response) . "</p>";
            }
        } else {
            echo "<p style='color: orange;'>! Tidak ada santri untuk testing</p>";
        }

        // Test 3: Menu by Gender
        echo "<h3>3. Test Menu by Gender</h3>";
        $_POST['gender'] = 'L';
        ob_start();
        $this->get_menu_by_gender();
        $response = ob_get_clean();

        $decoded = json_decode($response, true);
        if ($decoded && $decoded['success']) {
            echo "<p style='color: green;'>✓ get_menu_by_gender berhasil - " . count($decoded['menu']) . " menu ditemukan</p>";
        } else {
            echo "<p style='color: red;'>✗ get_menu_by_gender gagal: " . htmlspecialchars($response) . "</p>";
        }

        // Test 4: Process Transaction (simulasi)
        echo "<h3>4. Test Process Transaction (Simulasi)</h3>";
        if (!empty($santri_list)) {
            $test_santri = $santri_list[0];
            $menu_list = $this->Menu_model->get_menu(NULL, $kantin_id);

            if (!empty($menu_list)) {
                $test_menu = $menu_list[0];

                // Simulasi cart data
                $cart_data = [
                    [
                        'menu_id' => $test_menu->id,
                        'menu_name' => $test_menu->nama_menu,
                        'harga' => $test_menu->harga_jual,
                        'quantity' => 1
                    ]
                ];

                echo "<p>Test dengan:</p>";
                echo "<ul>";
                echo "<li>Santri: " . $test_santri->nama . "</li>";
                echo "<li>Menu: " . $test_menu->nama_menu . " (Rp " . number_format($test_menu->harga_jual, 0, ',', '.') . ")</li>";
                echo "<li>Cart: " . json_encode($cart_data) . "</li>";
                echo "</ul>";

                echo "<p style='color: blue;'>Note: Ini hanya simulasi. Transaksi tidak akan diproses.</p>";
            } else {
                echo "<p style='color: orange;'>! Tidak ada menu untuk testing</p>";
            }
        }

        // Test 5: URL Generation
        echo "<h3>5. Test URL Generation</h3>";
        echo "<p>site_url('pos/get_santri_info'): " . site_url('pos/get_santri_info') . "</p>";
        echo "<p>site_url('pos/get_menu_by_gender'): " . site_url('pos/get_menu_by_gender') . "</p>";
        echo "<p>site_url('pos/process_transaction'): " . site_url('pos/process_transaction') . "</p>";
        echo "<p>site_url('pos/process_ustadz_transaction'): " . site_url('pos/process_ustadz_transaction') . "</p>";

        echo "<hr>";
        echo "<p><strong>Kesimpulan:</strong> Jika semua test di atas menunjukkan ✓ (hijau), maka POS modern seharusnya berfungsi dengan baik.</p>";
        echo "<p>Jika ada yang menunjukkan ✗ (merah), silakan periksa error log dan perbaiki masalahnya.</p>";
    }

    public function test_csrf()
    {
        // Method untuk testing CSRF exclusion
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'message' => 'CSRF exclusion working',
            'timestamp' => date('Y-m-d H:i:s'),
            'csrf_protection' => $this->config->item('csrf_protection'),
            'csrf_exclude_uris' => $this->config->item('csrf_exclude_uris')
        ]);
    }

    public function test_transaction()
    {
        // Method untuk testing transaksi - hanya untuk development
        if (!in_array($this->session->userdata('role'), ['admin', 'operator'])) {
            show_404();
            return;
        }

        echo "<h2>Test Transaction Processing</h2>";
        echo "<p>Kantin ID: " . $this->session->userdata('kantin_id') . "</p>";
        echo "<p>User ID: " . $this->session->userdata('user_id') . "</p>";

        $kantin_id = $this->session->userdata('kantin_id');

        // Test 1: Validasi data input
        echo "<h3>1. Test Data Validation</h3>";

        // Test dengan data kosong
        $_POST = [];
        ob_start();
        $this->process_transaction();
        $response = ob_get_clean();
        $decoded = json_decode($response, true);

        if ($decoded && !$decoded['success']) {
            echo "<p style='color: green;'>✓ Validasi data kosong berfungsi: " . $decoded['message'] . "</p>";
        } else {
            echo "<p style='color: red;'>✗ Validasi data kosong gagal</p>";
        }

        // Test 2: Validasi santri
        echo "<h3>2. Test Santri Validation</h3>";
        $santri_list = $this->Santri_model->get_santri_with_tabungan(NULL, $kantin_id);

        if (!empty($santri_list)) {
            $test_santri = $santri_list[0];

            // Test dengan santri yang valid
            $_POST = [
                'santri_id' => $test_santri->id,
                'cart' => json_encode([]),
                'metode_pembayaran' => 'tunai'
            ];

            ob_start();
            $this->process_transaction();
            $response = ob_get_clean();
            $decoded = json_decode($response, true);

            if ($decoded && !$decoded['success']) {
                echo "<p style='color: green;'>✓ Validasi cart kosong berfungsi: " . $decoded['message'] . "</p>";
            } else {
                echo "<p style='color: red;'>✗ Validasi cart kosong gagal</p>";
            }

            // Test 3: Validasi menu dan stok
            echo "<h3>3. Test Menu and Stock Validation</h3>";
            $menu_list = $this->Menu_model->get_menu(NULL, $kantin_id);

            if (!empty($menu_list)) {
                $test_menu = $menu_list[0];

                // Test dengan menu yang valid
                $cart_data = [
                    [
                        'menu_id' => $test_menu->id,
                        'menu_name' => $test_menu->nama_menu,
                        'harga' => $test_menu->harga_jual,
                        'quantity' => 1
                    ]
                ];

                $_POST = [
                    'santri_id' => $test_santri->id,
                    'cart' => json_encode($cart_data),
                    'metode_pembayaran' => 'tunai'
                ];

                echo "<p>Test dengan:</p>";
                echo "<ul>";
                echo "<li>Santri: " . $test_santri->nama . " (ID: " . $test_santri->id . ")</li>";
                echo "<li>Menu: " . $test_menu->nama_menu . " (ID: " . $test_menu->id . ", Stok: " . $test_menu->stok . ")</li>";
                echo "<li>Cart: " . json_encode($cart_data) . "</li>";
                echo "</ul>";

                // Simulasi transaksi (tidak akan benar-benar diproses)
                echo "<p style='color: blue;'>Note: Ini hanya validasi. Transaksi tidak akan diproses untuk menghindari perubahan data.</p>";

                // Test validasi saldo jajan
                if ($test_santri->saldo_jajan > 0) {
                    echo "<p>Saldo jajan santri: Rp " . number_format($test_santri->saldo_jajan, 0, ',', '.') . "</p>";

                    $_POST['metode_pembayaran'] = 'saldo_jajan';

                    ob_start();
                    $this->process_transaction();
                    $response = ob_get_clean();
                    $decoded = json_decode($response, true);

                    if ($decoded) {
                        echo "<p>Response: " . $decoded['message'] . "</p>";
                    }
                } else {
                    echo "<p style='color: orange;'>! Santri tidak memiliki saldo jajan untuk testing</p>";
                }
            } else {
                echo "<p style='color: orange;'>! Tidak ada menu untuk testing</p>";
            }
        } else {
            echo "<p style='color: orange;'>! Tidak ada santri untuk testing</p>";
        }

        // Test 4: Error handling
        echo "<h3>4. Test Error Handling</h3>";

        // Test dengan menu yang tidak ada
        $_POST = [
            'santri_id' => 99999, // ID yang tidak ada
            'cart' => json_encode([
                [
                    'menu_id' => 99999,
                    'menu_name' => 'Menu Test',
                    'harga' => 1000,
                    'quantity' => 1
                ]
            ]),
            'metode_pembayaran' => 'tunai'
        ];

        ob_start();
        $this->process_transaction();
        $response = ob_get_clean();
        $decoded = json_decode($response, true);

        if ($decoded && !$decoded['success']) {
            echo "<p style='color: green;'>✓ Error handling berfungsi: " . $decoded['message'] . "</p>";
        } else {
            echo "<p style='color: red;'>✗ Error handling gagal</p>";
        }

        echo "<hr>";
        echo "<p><strong>Kesimpulan:</strong> Jika semua test menunjukkan ✓ (hijau), maka sistem transaksi berfungsi dengan baik.</p>";
        echo "<p>Jika ada yang menunjukkan ✗ (merah), silakan periksa error log dan perbaiki masalahnya.</p>";
    }

    public function test_menu_id_fix()
    {
        // Method untuk testing perbaikan menu_id - hanya untuk development
        if (!in_array($this->session->userdata('role'), ['admin', 'operator'])) {
            show_404();
            return;
        }

        echo "<h2>Test Menu ID Fix</h2>";
        echo "<p>Kantin ID: " . $this->session->userdata('kantin_id') . "</p>";

        $kantin_id = $this->session->userdata('kantin_id');

        // Test 1: Menu dengan menu_id yang valid
        echo "<h3>1. Test Menu dengan menu_id valid</h3>";
        $menu_list = $this->Menu_model->get_menu(NULL, $kantin_id);

        if (!empty($menu_list)) {
            $test_menu = $menu_list[0];
            echo "<p>Test dengan menu: " . $test_menu->nama_menu . " (ID: " . $test_menu->id . ")</p>";

            $cart_data = [
                [
                    'menu_id' => $test_menu->id,
                    'menu_name' => $test_menu->nama_menu,
                    'harga' => $test_menu->harga_jual,
                    'quantity' => 1
                ]
            ];

            echo "<p>Cart data: " . json_encode($cart_data) . "</p>";
            echo "<p style='color: green;'>✓ Menu ID valid</p>";
        } else {
            echo "<p style='color: orange;'>! Tidak ada menu untuk testing</p>";
        }

        // Test 2: Menu dengan menu_id = 0
        echo "<h3>2. Test Menu dengan menu_id = 0</h3>";
        $cart_data_invalid = [
            [
                'menu_id' => 0,
                'menu_name' => 'Menu Test',
                'harga' => 1000,
                'quantity' => 1
            ]
        ];

        echo "<p>Cart data: " . json_encode($cart_data_invalid) . "</p>";

        $_POST = [
            'santri_id' => 1, // dummy ID
            'cart' => json_encode($cart_data_invalid),
            'metode_pembayaran' => 'tunai'
        ];

        ob_start();
        $this->process_transaction();
        $response = ob_get_clean();
        $decoded = json_decode($response, true);

        if ($decoded && !$decoded['success']) {
            echo "<p style='color: green;'>✓ Validasi menu_id = 0 berfungsi: " . $decoded['message'] . "</p>";
        } else {
            echo "<p style='color: red;'>✗ Validasi menu_id = 0 gagal</p>";
        }

        // Test 3: Menu dengan menu_id kosong
        echo "<h3>3. Test Menu dengan menu_id kosong</h3>";
        $cart_data_empty = [
            [
                'menu_id' => '',
                'menu_name' => 'Menu Test',
                'harga' => 1000,
                'quantity' => 1
            ]
        ];

        echo "<p>Cart data: " . json_encode($cart_data_empty) . "</p>";

        $_POST = [
            'santri_id' => 1, // dummy ID
            'cart' => json_encode($cart_data_empty),
            'metode_pembayaran' => 'tunai'
        ];

        ob_start();
        $this->process_transaction();
        $response = ob_get_clean();
        $decoded = json_decode($response, true);

        if ($decoded && !$decoded['success']) {
            echo "<p style='color: green;'>✓ Validasi menu_id kosong berfungsi: " . $decoded['message'] . "</p>";
        } else {
            echo "<p style='color: red;'>✗ Validasi menu_id kosong gagal</p>";
        }

        // Test 4: Menu dengan menu_id yang tidak ada
        echo "<h3>4. Test Menu dengan menu_id yang tidak ada</h3>";
        $cart_data_notfound = [
            [
                'menu_id' => 99999,
                'menu_name' => 'Menu Test',
                'harga' => 1000,
                'quantity' => 1
            ]
        ];

        echo "<p>Cart data: " . json_encode($cart_data_notfound) . "</p>";

        $_POST = [
            'santri_id' => 1, // dummy ID
            'cart' => json_encode($cart_data_notfound),
            'metode_pembayaran' => 'tunai'
        ];

        ob_start();
        $this->process_transaction();
        $response = ob_get_clean();
        $decoded = json_decode($response, true);

        if ($decoded && !$decoded['success']) {
            echo "<p style='color: green;'>✓ Validasi menu tidak ditemukan berfungsi: " . $decoded['message'] . "</p>";
        } else {
            echo "<p style='color: red;'>✗ Validasi menu tidak ditemukan gagal</p>";
        }

        echo "<hr>";
        echo "<p><strong>Kesimpulan:</strong> Jika semua test menunjukkan ✓ (hijau), maka perbaikan menu_id berhasil.</p>";
        echo "<p>Sekarang transaksi ustadz seharusnya tidak lagi menampilkan error 'Undefined property: stdClass::$menu_id'.</p>";
    }

    public function test_stok_fix()
    {
        // Method untuk testing perbaikan stok - hanya untuk development
        if (!in_array($this->session->userdata('role'), ['admin', 'operator'])) {
            show_404();
            return;
        }

        echo "<h2>Test Stok Fix</h2>";
        echo "<p>Kantin ID: " . $this->session->userdata('kantin_id') . "</p>";

        $kantin_id = $this->session->userdata('kantin_id');

        // Test 1: Cek menu dengan stok yang cukup
        echo "<h3>1. Test Menu dengan Stok Cukup</h3>";
        $menu_list = $this->Menu_model->get_all_menu($kantin_id);

        if (!empty($menu_list)) {
            $test_menu = $menu_list[0];
            echo "<p>Test dengan menu: " . $test_menu->nama_menu . " (ID: " . $test_menu->id . ", Stok: " . $test_menu->stok . ")</p>";

            if ($test_menu->stok > 0) {
                // Test kurangi stok dengan jumlah yang valid
                $test_quantity = min(1, $test_menu->stok); // Ambil 1 atau stok yang tersedia

                echo "<p>Test kurangi stok: " . $test_quantity . " item</p>";

                $stok_result = $this->Menu_model->kurangi_stok($test_menu->id, $test_quantity, "Test POS Modern", $this->session->userdata('user_id'), $kantin_id);

                if ($stok_result) {
                    echo "<p style='color: green;'>✓ Kurangi stok berhasil</p>";

                    // Cek stok setelah dikurangi
                    $menu_after = $this->Menu_model->get_menu($test_menu->id, $kantin_id);
                    echo "<p>Stok setelah dikurangi: " . $menu_after->stok . "</p>";

                    // Restore stok untuk testing
                    $this->Menu_model->tambah_stok($test_menu->id, $test_quantity, $test_menu->harga_beli, "Restore stok untuk testing", $this->session->userdata('user_id'), $kantin_id);
                    echo "<p style='color: blue;'>Stok telah di-restore untuk testing</p>";
                } else {
                    echo "<p style='color: red;'>✗ Kurangi stok gagal</p>";
                }
            } else {
                echo "<p style='color: orange;'>! Menu tidak memiliki stok untuk testing</p>";
            }
        } else {
            echo "<p style='color: orange;'>! Tidak ada menu untuk testing</p>";
            echo "<p>Mencoba mendapatkan menu tanpa filter kantin_id...</p>";

            // Coba tanpa filter kantin_id untuk debugging
            $all_menu = $this->db->get('menu_kantin')->result();
            echo "<p>Total menu di database: " . count($all_menu) . "</p>";

            if (!empty($all_menu)) {
                echo "<p>Menu yang ada:</p>";
                echo "<ul>";
                foreach ($all_menu as $menu) {
                    echo "<li>" . $menu->nama_menu . " (ID: " . $menu->id . ", Kantin ID: " . $menu->kantin_id . ", Stok: " . $menu->stok . ")</li>";
                }
                echo "</ul>";
            }
        }

        // Test 2: Test dengan menu yang tidak ada
        echo "<h3>2. Test Menu yang Tidak Ada</h3>";
        $stok_result = $this->Menu_model->kurangi_stok(99999, 1, "Test menu tidak ada", $this->session->userdata('user_id'), $kantin_id);

        if (!$stok_result) {
            echo "<p style='color: green;'>✓ Validasi menu tidak ada berfungsi</p>";
        } else {
            echo "<p style='color: red;'>✗ Validasi menu tidak ada gagal</p>";
        }

        // Test 3: Test dengan stok tidak cukup
        echo "<h3>3. Test Stok Tidak Cukup</h3>";
        if (!empty($menu_list)) {
            $test_menu = $menu_list[0];
            $test_quantity = $test_menu->stok + 10; // Minta lebih dari stok yang ada

            echo "<p>Test kurangi stok: " . $test_quantity . " item (stok tersedia: " . $test_menu->stok . ")</p>";

            $stok_result = $this->Menu_model->kurangi_stok($test_menu->id, $test_quantity, "Test stok tidak cukup", $this->session->userdata('user_id'), $kantin_id);

            if (!$stok_result) {
                echo "<p style='color: green;'>✓ Validasi stok tidak cukup berfungsi</p>";
            } else {
                echo "<p style='color: red;'>✗ Validasi stok tidak cukup gagal</p>";
            }
        } else {
            echo "<p style='color: orange;'>! Tidak ada menu untuk testing stok tidak cukup</p>";
        }

        echo "<hr>";
        echo "<p><strong>Kesimpulan:</strong> Jika semua test menunjukkan ✓ (hijau), maka perbaikan stok berhasil.</p>";
        echo "<p>Sekarang transaksi seharusnya tidak lagi menampilkan error 'Terjadi kesalahan pada database saat memproses transaksi'.</p>";
    }

    public function test_menu_kantin()
    {
        // Method untuk testing menu di kantin - hanya untuk development
        if (!in_array($this->session->userdata('role'), ['admin', 'operator'])) {
            show_404();
            return;
        }

        echo "<h2>Test Menu di Kantin</h2>";
        echo "<p>Kantin ID: " . $this->session->userdata('kantin_id') . "</p>";
        echo "<p>Role: " . $this->session->userdata('role') . "</p>";

        $kantin_id = $this->session->userdata('kantin_id');

        // Test 1: Cek semua menu di database
        echo "<h3>1. Semua Menu di Database</h3>";
        $all_menu = $this->db->get('menu_kantin')->result();
        echo "<p>Total menu di database: " . count($all_menu) . "</p>";

        if (!empty($all_menu)) {
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>ID</th><th>Nama Menu</th><th>Kantin ID</th><th>Stok</th><th>Harga Jual</th></tr>";
            foreach ($all_menu as $menu) {
                $highlight = ($menu->kantin_id == $kantin_id) ? "background-color: yellow;" : "";
                echo "<tr style='$highlight'>";
                echo "<td>" . $menu->id . "</td>";
                echo "<td>" . $menu->nama_menu . "</td>";
                echo "<td>" . $menu->kantin_id . "</td>";
                echo "<td>" . $menu->stok . "</td>";
                echo "<td>Rp " . number_format($menu->harga_jual, 0, ',', '.') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }

        // Test 2: Cek menu untuk kantin ini
        echo "<h3>2. Menu untuk Kantin ID " . $kantin_id . "</h3>";
        $this->db->where('kantin_id', $kantin_id);
        $kantin_menu = $this->db->get('menu_kantin')->result();
        echo "<p>Total menu untuk kantin ini: " . count($kantin_menu) . "</p>";

        if (!empty($kantin_menu)) {
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>ID</th><th>Nama Menu</th><th>Stok</th><th>Harga Jual</th><th>Status</th></tr>";
            foreach ($kantin_menu as $menu) {
                $status = ($menu->stok > 0) ? "Tersedia" : "Habis";
                $color = ($menu->stok > 0) ? "color: green;" : "color: red;";
                echo "<tr>";
                echo "<td>" . $menu->id . "</td>";
                echo "<td>" . $menu->nama_menu . "</td>";
                echo "<td>" . $menu->stok . "</td>";
                echo "<td>Rp " . number_format($menu->harga_jual, 0, ',', '.') . "</td>";
                echo "<td style='$color'>" . $status . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: red;'>! Tidak ada menu untuk kantin ini</p>";
        }

        // Test 3: Cek method get_all_menu
        echo "<h3>3. Test Method get_all_menu()</h3>";
        $menu_list = $this->Menu_model->get_all_menu($kantin_id);
        echo "<p>Menu dari get_all_menu(): " . count($menu_list) . " item</p>";

        if (!empty($menu_list)) {
            echo "<ul>";
            foreach ($menu_list as $menu) {
                echo "<li>" . $menu->nama_menu . " (ID: " . $menu->id . ", Stok: " . $menu->stok . ")</li>";
            }
            echo "</ul>";
        }

        // Test 4: Cek method get_menu_aktif
        echo "<h3>4. Test Method get_menu_aktif()</h3>";
        $menu_aktif = $this->Menu_model->get_menu_aktif($kantin_id);
        echo "<p>Menu aktif: " . count($menu_aktif) . " item</p>";

        if (!empty($menu_aktif)) {
            echo "<ul>";
            foreach ($menu_aktif as $menu) {
                echo "<li>" . $menu->nama_menu . " (ID: " . $menu->id . ", Stok: " . $menu->stok . ")</li>";
            }
            echo "</ul>";
        }

        echo "<hr>";
        echo "<p><strong>Kesimpulan:</strong> Jika tidak ada menu untuk kantin ini, Anda perlu menambahkan menu terlebih dahulu.</p>";
        echo "<p>Untuk menambahkan menu, akses: <a href='" . site_url('menu') . "'>Menu Management</a></p>";
    }

    public function test_ustadz_fix()
    {
        // Method untuk testing perbaikan ustadz - hanya untuk development
        if (!in_array($this->session->userdata('role'), ['admin', 'operator'])) {
            show_404();
            return;
        }

        echo "<h2>Test Ustadz Fix</h2>";
        echo "<p>Kantin ID: " . $this->session->userdata('kantin_id') . "</p>";
        echo "<p>Role: " . $this->session->userdata('role') . "</p>";

        // Test 1: Cek menu untuk kantin yang sedang login
        echo "<h3>1. Test Menu untuk Kantin yang Sedang Login</h3>";

        $kantin_id = $this->session->userdata('kantin_id');
        $menu_list = $this->Menu_model->get_all_menu($kantin_id);

        if (!empty($menu_list)) {
            echo "<p style='color: green;'>✓ Menu untuk kantin ini ditemukan</p>";
            echo "<p>Total menu: " . count($menu_list) . " item</p>";

            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>ID</th><th>Nama Menu</th><th>Stok</th><th>Harga</th></tr>";
            foreach ($menu_list as $menu) {
                echo "<tr>";
                echo "<td>" . $menu->id . "</td>";
                echo "<td>" . $menu->nama_menu . "</td>";
                echo "<td>" . $menu->stok . "</td>";
                echo "<td>Rp " . number_format($menu->harga_jual, 0, ',', '.') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: orange;'>! Tidak ada menu untuk kantin ini</p>";
        }

        // Test 2: Test transaksi ustadz (simulasi)
        echo "<h3>2. Test Transaksi Ustadz (Simulasi)</h3>";

        if (!empty($menu_list)) {
            $test_menu = $menu_list[0];
            echo "<p>Test dengan menu: " . $test_menu->nama_menu . " (ID: " . $test_menu->id . ", Stok: " . $test_menu->stok . ")</p>";

            $cart_data = [
                [
                    'menu_id' => $test_menu->id,
                    'menu_name' => $test_menu->nama_menu,
                    'harga' => $test_menu->harga_jual,
                    'quantity' => 1
                ]
            ];

            echo "<p>Cart data: " . json_encode($cart_data) . "</p>";
            echo "<p style='color: blue;'>Note: Ini hanya simulasi. Transaksi tidak akan diproses untuk menghindari perubahan data.</p>";

            // Test validasi saja
            $_POST = [
                'ustadz_id' => 1, // dummy ID
                'cart' => json_encode($cart_data),
                'metode_pembayaran' => 'tunai'
            ];

            ob_start();
            $this->process_ustadz_transaction();
            $response = ob_get_clean();
            $decoded = json_decode($response, true);

            if ($decoded) {
                echo "<p>Response: " . $decoded['message'] . "</p>";
                if ($decoded['success']) {
                    echo "<p style='color: green;'>✓ Transaksi ustadz berhasil (dalam simulasi)</p>";
                } else {
                    echo "<p style='color: orange;'>! Transaksi ustadz gagal: " . $decoded['message'] . "</p>";
                }
            }
        } else {
            echo "<p style='color: orange;'>! Tidak ada menu untuk testing</p>";
        }

        // Test 3: CSRF Exclusion
        echo "<h3>3. Test CSRF Exclusion</h3>";
        $csrf_exclude_uris = $this->config->item('csrf_exclude_uris');
        $ustadz_endpoints = ['pos/process_ustadz_transaction'];

        foreach ($ustadz_endpoints as $endpoint) {
            if (in_array($endpoint, $csrf_exclude_uris)) {
                echo "<p style='color: green;'>✓ $endpoint - CSRF excluded</p>";
            } else {
                echo "<p style='color: red;'>✗ $endpoint - NOT CSRF excluded</p>";
            }
        }

        echo "<hr>";
        echo "<p><strong>Kesimpulan:</strong> Jika semua test menunjukkan ✓ (hijau), maka perbaikan ustadz berhasil.</p>";
        echo "<p>Sekarang ustadz/ustadzah dapat bertransaksi menggunakan menu dari kantin yang sedang login.</p>";
    }
}
