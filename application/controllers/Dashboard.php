<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
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
        $this->load->library('session');
        $this->load->helper('url');
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
    }

    public function index()
    {
        $data['title'] = 'Dashboard';
        $role = $this->session->userdata('role');
        $kantin_id = $this->session->userdata('kantin_id');

        if ($role === 'operator') {
            // Data untuk Dashboard Operator
            $data['kantin_info'] = $this->Kantin_model->get_kantin($kantin_id);

            // Statistik Stok
            $stok_stats = $this->Menu_model->get_stok_stats_by_kantin($kantin_id);

            // Statistik Transaksi (sinkron: santri + ustadz)
            $transaksi_stats = [
                'transaksi_hari_ini' => $this->Transaksi_model->count_transaksi_hari_ini($kantin_id) + $this->Transaksi_model->count_transaksi_ustadz_hari_ini($kantin_id),
                'pendapatan_hari_ini' => $this->Transaksi_model->get_pendapatan_hari_ini($kantin_id) + $this->Transaksi_model->get_pendapatan_ustadz_hari_ini($kantin_id),
                'keuntungan_hari_ini' => $this->Transaksi_model->get_keuntungan_hari_ini($kantin_id)
            ];

            // Gabungkan semua statistik
            $data['stats'] = array_merge($stok_stats, $transaksi_stats);

            // Data untuk tabel setoran harian
            $data['setoran_harian'] = $this->Transaksi_model->get_setoran_harian_per_pemilik($kantin_id);

            // 5 Transaksi Terakhir (gabungan santri+ustadz)
            $data['recent_transactions'] = $this->Transaksi_model->get_recent_all_transactions(5, $kantin_id);

            // Hitung total tunai yang dipegang saat ini (tunai santri + semua ustadz hari ini)s
            $tunai_santri = $this->Transaksi_model->get_pendapatan_tunai_hari_ini($kantin_id);
            $tunai_ustadz = $this->Transaksi_model->get_pendapatan_ustadz_hari_ini($kantin_id);
            $data['tunai_saat_ini'] = $tunai_santri + $tunai_ustadz;

            $this->load->view('templates/header', $data);
            $this->load->view('dashboard/operator_dashboard', $data);
            $this->load->view('templates/footer');
        } elseif ($role === 'keuangan') {
            // Data untuk Dashboard Keuangan
            $data['kantin_info'] = null; // Role keuangan bisa lihat semua kantin

            // Statistik Transaksi Hari Ini (semua kantin untuk role keuangan)
            $data['transaksi_hari_ini'] = $this->Transaksi_model->count_nota_hari_ini() + $this->Transaksi_model->count_nota_ustadz_hari_ini();
            $data['pendapatan_hari_ini'] = $this->Transaksi_model->get_pendapatan_hari_ini();
            $data['keuntungan_hari_ini'] = $this->Transaksi_model->get_keuntungan_hari_ini();

            // Total pendapatan kantin putra & putri (semua waktu)
            $kantin_putra = $this->Kantin_model->get_kantin_by_jenis('putra');
            $kantin_putri = $this->Kantin_model->get_kantin_by_jenis('putri');
            $data['pendapatan_kantin_putra'] = $kantin_putra ? $this->Transaksi_model->get_total_revenue(null, null, $kantin_putra->id) : 0;
            $data['pendapatan_kantin_putri'] = $kantin_putri ? $this->Transaksi_model->get_total_revenue(null, null, $kantin_putri->id) : 0;

            // Data Transaksi Tunai Hari Ini (semua kantin)
            $data['transaksi_tunai'] = $this->get_transaksi_tunai_hari_ini($kantin_id);
            $data['total_transaksi_tunai'] = count($data['transaksi_tunai']);
            $data['total_pendapatan_tunai'] = array_sum(array_column($data['transaksi_tunai'], 'total_harga'));

            // Data Transaksi Saldo Jajan Hari Ini (semua kantin)
            $data['transaksi_saldo'] = $this->get_transaksi_saldo_hari_ini($kantin_id);
            $data['total_transaksi_saldo'] = count($data['transaksi_saldo']);
            $data['total_pendapatan_saldo'] = array_sum(array_column($data['transaksi_saldo'], 'total_harga'));

            // Data Saldo yang Terpotong Hari Ini (semua kantin)
            $data['saldo_terpotong'] = $this->get_saldo_terpotong_hari_ini($kantin_id);
            $data['total_saldo_terpotong'] = array_sum(array_column($data['saldo_terpotong'], 'jumlah'));

            // Data Setoran Harian (semua kantin)
            $data['setoran_harian'] = $this->Transaksi_model->get_setoran_harian_per_pemilik();

            // Transaksi Terakhir (semua kantin)
            $data['recent_transactions'] = $this->Transaksi_model->get_recent_all_transactions(10);

            // Data Grafik Transaksi 7 Hari Terakhir (semua kantin)
            $data['transaksi_harian'] = $this->get_transaksi_harian($kantin_id);

            $this->load->view('templates/header', $data);
            $this->load->view('dashboard/keuangan_dashboard', $data);
            $this->load->view('templates/footer');
        } else {
            // Dashboard Admin (atau role lain)
            $log_result = $this->Activity_log_model->log_system('DASHBOARD_ADMIN_VIEW', [
                'user_id' => $this->session->userdata('user_id'),
                'username' => $this->session->userdata('username'),
                'role' => $role
            ], 'info');

            // Debug: log hasil insert
            if ($log_result) {
                log_message('info', 'Activity log berhasil disimpan untuk dashboard admin view');
            } else {
                log_message('error', 'Activity log gagal disimpan untuk dashboard admin view');
            }
            $data['total_users'] = $this->User_model->count_all();
            $data['total_menu'] = $this->Menu_model->count_menu(); // Menghitung semua menu di semua kantin
            $data['total_santri'] = $this->Santri_model->count_all();
            $data['total_kantin'] = $this->Kantin_model->count_all();
            $data['total_saldo_jajan'] = $this->Tabungan_model->get_total_saldo_jajan_semua_santri();
            $data['total_ustadz'] = $this->Ustadz_model->count_ustadz();

            // Statistik tambahan untuk admin
            $data['transaksi_hari_ini'] = $this->Transaksi_model->count_nota_hari_ini() + $this->Transaksi_model->count_nota_ustadz_hari_ini();
            $data['pendapatan_hari_ini'] = $this->Transaksi_model->get_pendapatan_hari_ini();
            $data['keuntungan_hari_ini'] = $this->Transaksi_model->get_keuntungan_hari_ini();

            // Tambahan: Transaksi hari ini per kantin putra/putri
            $kantin_putra = $this->Kantin_model->get_kantin_by_jenis('putra');
            $kantin_putri = $this->Kantin_model->get_kantin_by_jenis('putri');
            $data['transaksi_hari_ini_putra'] = $kantin_putra ? $this->Transaksi_model->count_nota_hari_ini($kantin_putra->id) + $this->Transaksi_model->count_nota_ustadz_hari_ini($kantin_putra->id) : 0;
            $data['transaksi_hari_ini_putri'] = $kantin_putri ? $this->Transaksi_model->count_nota_hari_ini($kantin_putri->id) + $this->Transaksi_model->count_nota_ustadz_hari_ini($kantin_putri->id) : 0;

            // Pendapatan hari ini per kantin (total, tunai, saldo jajan)
            if ($kantin_putra) {
                $data['pendapatan_hari_ini_putra'] = $this->Transaksi_model->get_pendapatan_hari_ini($kantin_putra->id);
                $data['pendapatan_tunai_putra'] = $this->Transaksi_model->get_pendapatan_tunai_hari_ini($kantin_putra->id);
                $data['pendapatan_saldo_putra'] = $this->Transaksi_model->get_pendapatan_hari_ini($kantin_putra->id, 'saldo_jajan');
            } else {
                $data['pendapatan_hari_ini_putra'] = 0;
                $data['pendapatan_tunai_putra'] = 0;
                $data['pendapatan_saldo_putra'] = 0;
            }
            if ($kantin_putri) {
                $data['pendapatan_hari_ini_putri'] = $this->Transaksi_model->get_pendapatan_hari_ini($kantin_putri->id);
                $data['pendapatan_tunai_putri'] = $this->Transaksi_model->get_pendapatan_tunai_hari_ini($kantin_putri->id);
                $data['pendapatan_saldo_putri'] = $this->Transaksi_model->get_pendapatan_hari_ini($kantin_putri->id, 'saldo_jajan');
            } else {
                $data['pendapatan_hari_ini_putri'] = 0;
                $data['pendapatan_tunai_putri'] = 0;
                $data['pendapatan_saldo_putri'] = 0;
            }

            // Data transaksi terakhir (semua kantin)
            $data['transaksi_terakhir'] = $this->Transaksi_model->get_recent_all_transactions(10);

            // Statistik per kantin
            $data['kantin_stats'] = $this->Kantin_model->get_kantin_with_stats();

            // Data grafik transaksi 7 hari terakhir
            $data['transaksi_harian'] = $this->get_transaksi_harian_admin();

            // Sinkronisasi total transaksi keseluruhan (santri + ustadz)
            $data['total_transaksi_keseluruhan'] = $this->Transaksi_model->count_nota_hari_ini() + $this->Transaksi_model->count_nota_ustadz_hari_ini();

            $data['total_saldo_keseluruhan'] = $data['total_saldo_jajan'];

            $this->load->view('templates/header', $data);
            $this->load->view('dashboard/index', $data);
            $this->load->view('templates/footer');
        }
    }

    private function get_transaksi_harian($kantin_id)
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = date('Y-m-d', strtotime("-$i days"));

            // Jika kantin_id ada, gunakan filter kantin
            if (!empty($kantin_id)) {
                $pendapatan = $this->Transaksi_model->get_total_revenue($tanggal, $tanggal, $kantin_id);
            } else {
                // Jika kantin_id kosong, ambil semua kantin
                $pendapatan = $this->Transaksi_model->get_total_revenue($tanggal, $tanggal);
            }

            $data[] = [
                'tanggal' => date('d/m', strtotime($tanggal)),
                'pendapatan' => $pendapatan
            ];
        }
        return $data;
    }

    private function get_transaksi_harian_admin()
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = date('Y-m-d', strtotime("-$i days"));
            $pendapatan = $this->Transaksi_model->get_total_revenue($tanggal, $tanggal); // Tanpa kantin_id untuk semua kantin
            $data[] = [
                'tanggal' => date('d/m', strtotime($tanggal)),
                'pendapatan' => $pendapatan
            ];
        }
        return $data;
    }

    public function profile()
    {
        $kantin_id = $this->session->userdata('kantin_id');
        $user_id = $this->session->userdata('user_id');
        $role = $this->session->userdata('role');

        $data['title'] = 'Profile - ' . $this->session->userdata('kantin_nama');
        $data['kantin_info'] = $this->Kantin_model->get_kantin($kantin_id);
        $data['user'] = $this->User_model->get_user($user_id);

        // Jika role santri, ambil data santri
        if ($role == 'santri') {
            $data['santri'] = $this->Santri_model->get_santri_with_tabungan($user_id, $kantin_id);
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('username', 'Username', 'required');
            $this->form_validation->set_rules('password', 'Password', 'min_length[4]');
            $this->form_validation->set_rules('confirm_password', 'Konfirmasi Password', 'matches[password]');

            if ($this->form_validation->run() === TRUE) {
                $update = [
                    'username' => $this->input->post('username')
                ];

                if ($this->input->post('password')) {
                    $update['password'] = $this->input->post('password');
                }

                if ($this->User_model->update_user($user_id, $update)) {
                    $this->session->set_flashdata('success', 'Profile berhasil diupdate.');
                    redirect('dashboard/profile');
                } else {
                    $this->session->set_flashdata('error', 'Gagal mengupdate profile.');
                }
            }
        }

        $this->load->view('templates/header', $data);
        $this->load->view('dashboard/profile', $data);
        $this->load->view('templates/footer');
    }

    private function get_transaksi_tunai_hari_ini($kantin_id)
    {
        $tanggal = date('Y-m-d');
        $where_kantin1 = $kantin_id ? "AND tk.kantin_id = " . intval($kantin_id) : "";
        $where_kantin2 = $kantin_id ? "AND mk2.kantin_id = " . intval($kantin_id) : "";

        $sql = "
            SELECT
                tk.created_at,
                s.nama as nama_pelanggan,
                s.nomor_induk,
                s.kelas,
                tk.total_harga,
                mk.nama_menu,
                'santri' as jenis
            FROM transaksi_kantin tk
            JOIN santri s ON tk.santri_id = s.id
            JOIN menu_kantin mk ON tk.menu_id = mk.id
            WHERE DATE(tk.created_at) = '$tanggal'
              AND tk.metode_pembayaran = 'tunai'
              $where_kantin1

            UNION ALL

            SELECT
                tu.created_at,
                u.nama as nama_pelanggan,
                '' as nomor_induk,
                '' as kelas,
                tu.total_harga,
                mk2.nama_menu,
                'ustadz' as jenis
            FROM transaksi_ustadz tu
            JOIN ustadz u ON tu.ustadz_id = u.id
            JOIN menu_kantin mk2 ON tu.menu_id = mk2.id
            WHERE DATE(tu.created_at) = '$tanggal'
            $where_kantin2

            ORDER BY created_at DESC
        ";
        return $this->db->query($sql)->result_array();
    }

    private function get_transaksi_saldo_hari_ini($kantin_id)
    {
        $this->db->select('transaksi_kantin.*, santri.nama as nama_santri, santri.nomor_induk, santri.kelas, menu_kantin.nama_menu');
        $this->db->from('transaksi_kantin');
        $this->db->join('santri', 'santri.id = transaksi_kantin.santri_id');
        $this->db->join('menu_kantin', 'menu_kantin.id = transaksi_kantin.menu_id');
        $this->db->where('DATE(transaksi_kantin.created_at)', date('Y-m-d'));
        $this->db->where('transaksi_kantin.status', 'selesai');
        $this->db->where('transaksi_kantin.metode_pembayaran', 'saldo_jajan');
        // Jika kantin_id ada, filter berdasarkan kantin_id
        if (!empty($kantin_id)) {
            $this->db->where('transaksi_kantin.kantin_id', $kantin_id);
        }
        $this->db->order_by('transaksi_kantin.created_at', 'DESC');
        return $this->db->get()->result_array();
    }

    private function get_saldo_terpotong_hari_ini($kantin_id)
    {
        $this->db->select('transaksi.*, santri.nama as nama_santri, santri.nomor_induk, santri.kelas, users.username as admin_nama');
        $this->db->from('transaksi');
        $this->db->join('santri', 'santri.id = transaksi.santri_id');
        $this->db->join('users', 'users.id = transaksi.admin_id', 'left');
        $this->db->where('DATE(transaksi.created_at)', date('Y-m-d'));
        $this->db->where('transaksi.jenis', 'penarikan');
        $this->db->where('transaksi.kategori', 'jajan');

        // Filter berdasarkan kantin hanya jika kantin_id ada
        if (!empty($kantin_id)) {
            if ($kantin_id == 1) { // Kantin Putra
                $this->db->where('santri.jenis_kelamin', 'L');
            } elseif ($kantin_id == 2) { // Kantin Putri
                $this->db->where('santri.jenis_kelamin', 'P');
            }
        }
        // Jika kantin_id kosong, tampilkan semua (untuk role keuangan)

        $this->db->order_by('transaksi.created_at', 'DESC');
        return $this->db->get()->result_array();
    }

    // Method debug sementara untuk memeriksa data transaksi real
    public function debug_real_data()
    {
        $kantin_id = $this->session->userdata('kantin_id');
        $role = $this->session->userdata('role');
        $user_id = $this->session->userdata('user_id');

        echo "<h3>Debug Data Real Dashboard Keuangan</h3>";
        echo "<p>Role: " . $role . "</p>";
        echo "<p>User ID: " . $user_id . "</p>";
        echo "<p>Kantin ID: " . $kantin_id . "</p>";
        echo "<p>Tanggal: " . date('Y-m-d') . "</p>";

        // Debug: Cek session data
        echo "<h4>0. Debug Session Data</h4>";
        echo "<p>Session data:</p>";
        echo "<ul>";
        foreach ($this->session->userdata() as $key => $value) {
            echo "<li><strong>{$key}:</strong> " . (is_array($value) ? json_encode($value) : $value) . "</li>";
        }
        echo "</ul>";

        // Debug: Cek apakah ada data di tabel transaksi_kantin
        echo "<h4>1. Cek Tabel transaksi_kantin</h4>";
        $this->db->select('COUNT(*) as total');
        $this->db->from('transaksi_kantin');
        $total_transaksi = $this->db->get()->row()->total;
        echo "<p>Total transaksi di tabel transaksi_kantin: <strong>{$total_transaksi}</strong></p>";

        if ($total_transaksi > 0) {
            // Tampilkan beberapa transaksi terbaru
            $this->db->select('transaksi_kantin.*, santri.nama as nama_santri, menu_kantin.nama_menu');
            $this->db->from('transaksi_kantin');
            $this->db->join('santri', 'santri.id = transaksi_kantin.santri_id', 'left');
            $this->db->join('menu_kantin', 'menu_kantin.id = transaksi_kantin.menu_id', 'left');
            $this->db->order_by('transaksi_kantin.created_at', 'DESC');
            $this->db->limit(5);
            $recent = $this->db->get()->result_array();

            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>Santri</th><th>Menu</th><th>Kantin ID</th><th>Keterangan</th><th>Total Harga</th><th>Created At</th></tr>";
            foreach ($recent as $t) {
                echo "<tr>";
                echo "<td>" . $t['id'] . "</td>";
                echo "<td>" . ($t['nama_santri'] ?? 'NULL') . "</td>";
                echo "<td>" . ($t['nama_menu'] ?? 'NULL') . "</td>";
                echo "<td>" . $t['kantin_id'] . "</td>";
                echo "<td>" . $t['keterangan'] . "</td>";
                echo "<td>" . $t['total_harga'] . "</td>";
                echo "<td>" . $t['created_at'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }

        // Debug: Cek transaksi hari ini tanpa filter kantin
        echo "<h4>2. Cek Transaksi Hari Ini (Semua Kantin)</h4>";
        $this->db->select('transaksi_kantin.*, santri.nama as nama_santri, menu_kantin.nama_menu');
        $this->db->from('transaksi_kantin');
        $this->db->join('santri', 'santri.id = transaksi_kantin.santri_id', 'left');
        $this->db->join('menu_kantin', 'menu_kantin.id = transaksi_kantin.menu_id', 'left');
        $this->db->where('DATE(transaksi_kantin.created_at)', date('Y-m-d'));
        $today_all = $this->db->get()->result_array();

        echo "<p>Total transaksi hari ini (semua kantin): <strong>" . count($today_all) . "</strong></p>";
        if (count($today_all) > 0) {
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>Santri</th><th>Menu</th><th>Kantin ID</th><th>Keterangan</th><th>Total Harga</th><th>Created At</th></tr>";
            foreach ($today_all as $t) {
                echo "<tr>";
                echo "<td>" . $t['id'] . "</td>";
                echo "<td>" . ($t['nama_santri'] ?? 'NULL') . "</td>";
                echo "<td>" . ($t['nama_menu'] ?? 'NULL') . "</td>";
                echo "<td>" . $t['kantin_id'] . "</td>";
                echo "<td>" . $t['keterangan'] . "</td>";
                echo "<td>" . $t['total_harga'] . "</td>";
                echo "<td>" . $t['created_at'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }

        // Debug: Cek transaksi hari ini dengan filter kantin
        echo "<h4>3. Cek Transaksi Hari Ini (Kantin ID: {$kantin_id})</h4>";
        $this->db->select('transaksi_kantin.*, santri.nama as nama_santri, menu_kantin.nama_menu');
        $this->db->from('transaksi_kantin');
        $this->db->join('santri', 'santri.id = transaksi_kantin.santri_id', 'left');
        $this->db->join('menu_kantin', 'menu_kantin.id = transaksi_kantin.menu_id', 'left');
        $this->db->where('DATE(transaksi_kantin.created_at)', date('Y-m-d'));
        $this->db->where('transaksi_kantin.kantin_id', $kantin_id);
        $today_kantin = $this->db->get()->result_array();

        echo "<p>Total transaksi hari ini (kantin {$kantin_id}): <strong>" . count($today_kantin) . "</strong></p>";
        if (count($today_kantin) > 0) {
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>Santri</th><th>Menu</th><th>Kantin ID</th><th>Keterangan</th><th>Total Harga</th><th>Created At</th></tr>";
            foreach ($today_kantin as $t) {
                echo "<tr>";
                echo "<td>" . $t['id'] . "</td>";
                echo "<td>" . ($t['nama_santri'] ?? 'NULL') . "</td>";
                echo "<td>" . ($t['nama_menu'] ?? 'NULL') . "</td>";
                echo "<td>" . $t['kantin_id'] . "</td>";
                echo "<td>" . $t['keterangan'] . "</td>";
                echo "<td>" . $t['total_harga'] . "</td>";
                echo "<td>" . $t['created_at'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }

        // Debug: Cek query transaksi tunai
        echo "<h4>4. Debug Query Transaksi Tunai</h4>";
        $this->db->select('transaksi_kantin.*, santri.nama as nama_santri, santri.nomor_induk, santri.kelas, menu_kantin.nama_menu');
        $this->db->from('transaksi_kantin');
        $this->db->join('santri', 'santri.id = transaksi_kantin.santri_id');
        $this->db->join('menu_kantin', 'menu_kantin.id = transaksi_kantin.menu_id');
        $this->db->where('DATE(transaksi_kantin.created_at)', date('Y-m-d'));
        $this->db->where('transaksi_kantin.kantin_id', $kantin_id);
        $this->db->where('transaksi_kantin.keterangan LIKE', '%Tunai%');
        $this->db->order_by('transaksi_kantin.created_at', 'DESC');

        echo "<p>Query SQL: " . $this->db->get_compiled_select() . "</p>";

        $tunai_result = $this->db->get()->result_array();
        echo "<p>Hasil query transaksi tunai: <strong>" . count($tunai_result) . "</strong></p>";

        // Debug: Cek query transaksi saldo
        echo "<h4>5. Debug Query Transaksi Saldo Jajan</h4>";
        $this->db->select('transaksi_kantin.*, santri.nama as nama_santri, santri.nomor_induk, santri.kelas, menu_kantin.nama_menu');
        $this->db->from('transaksi_kantin');
        $this->db->join('santri', 'santri.id = transaksi_kantin.santri_id');
        $this->db->join('menu_kantin', 'menu_kantin.id = transaksi_kantin.menu_id');
        $this->db->where('DATE(transaksi_kantin.created_at)', date('Y-m-d'));
        $this->db->where('transaksi_kantin.kantin_id', $kantin_id);
        $this->db->where('transaksi_kantin.keterangan LIKE', '%Saldo Jajan%');
        $this->db->order_by('transaksi_kantin.created_at', 'DESC');

        echo "<p>Query SQL: " . $this->db->get_compiled_select() . "</p>";

        $saldo_result = $this->db->get()->result_array();
        echo "<p>Hasil query transaksi saldo jajan: <strong>" . count($saldo_result) . "</strong></p>";

        // Debug: Cek semua keterangan yang ada
        echo "<h4>6. Cek Semua Keterangan yang Ada</h4>";
        $this->db->select('DISTINCT keterangan');
        $this->db->from('transaksi_kantin');
        $this->db->where('DATE(transaksi_kantin.created_at)', date('Y-m-d'));
        $this->db->where('transaksi_kantin.kantin_id', $kantin_id);
        $keterangan_list = $this->db->get()->result_array();

        echo "<p>Keterangan yang ada hari ini:</p>";
        if (count($keterangan_list) > 0) {
            echo "<ul>";
            foreach ($keterangan_list as $k) {
                echo "<li><strong>" . $k['keterangan'] . "</strong></li>";
            }
            echo "</ul>";
        } else {
            echo "<p>Tidak ada keterangan</p>";
        }

        // Debug: Cek tabel transaksi (untuk saldo terpotong)
        echo "<h4>7. Cek Tabel transaksi (Saldo Terpotong)</h4>";
        $this->db->select('COUNT(*) as total');
        $this->db->from('transaksi');
        $this->db->where('DATE(created_at)', date('Y-m-d'));
        $this->db->where('jenis', 'penarikan');
        $this->db->where('kategori', 'jajan');
        $total_penarikan = $this->db->get()->row()->total;
        echo "<p>Total penarikan saldo jajan hari ini: <strong>{$total_penarikan}</strong></p>";

        if ($total_penarikan > 0) {
            $this->db->select('transaksi.*, santri.nama as nama_santri, users.username as admin_nama');
            $this->db->from('transaksi');
            $this->db->join('santri', 'santri.id = transaksi.santri_id', 'left');
            $this->db->join('users', 'users.id = transaksi.admin_id', 'left');
            $this->db->where('DATE(transaksi.created_at)', date('Y-m-d'));
            $this->db->where('transaksi.jenis', 'penarikan');
            $this->db->where('transaksi.kategori', 'jajan');
            $penarikan_list = $this->db->get()->result_array();

            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>Santri</th><th>Jumlah</th><th>Keterangan</th><th>Admin</th><th>Created At</th></tr>";
            foreach ($penarikan_list as $p) {
                echo "<tr>";
                echo "<td>" . $p['id'] . "</td>";
                echo "<td>" . ($p['nama_santri'] ?? 'NULL') . "</td>";
                echo "<td>" . $p['jumlah'] . "</td>";
                echo "<td>" . $p['keterangan'] . "</td>";
                echo "<td>" . ($p['admin_nama'] ?? 'NULL') . "</td>";
                echo "<td>" . $p['created_at'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }

    public function filter_database()
    {
        $this->load->model('Santri_model');
        $data['title'] = 'Filter Database';
        // Ambil semua kelas unik
        $all_santri = $this->Santri_model->get_santri_with_tabungan();
        $data['kelas_list'] = array_unique(array_filter(array_map(function ($s) {
            return $s->kelas;
        }, $all_santri)));
        sort($data['kelas_list']);
        $data['filter'] = [
            'jenis_kelamin' => $this->input->get('jenis_kelamin'),
            'kelas' => $this->input->get('kelas'),
        ];
        $data['santri'] = $all_santri;
        if ($data['filter']['jenis_kelamin']) {
            $data['santri'] = array_filter($data['santri'], function ($s) use ($data) {
                return $s->jenis_kelamin == $data['filter']['jenis_kelamin'];
            });
        }
        if ($data['filter']['kelas']) {
            $data['santri'] = array_filter($data['santri'], function ($s) use ($data) {
                return $s->kelas == $data['filter']['kelas'];
            });
        }
        $this->load->view('templates/header', $data);
        $this->load->view('dashboard/filter_database', $data);
        $this->load->view('templates/footer');
    }

    public function histori_setoran()
    {
        $data['title'] = 'Histori Setoran Operator';
        $kantin_id = $this->session->userdata('kantin_id');

        // Validasi kantin_id
        if (!$kantin_id) {
            $this->session->set_flashdata('error', 'Kantin ID tidak ditemukan. Silakan login ulang.');
            redirect('dashboard');
            return;
        }

        // Ambil data kantin info
        $data['kantin_info'] = $this->Kantin_model->get_kantin($kantin_id);

        // Validasi kantin ditemukan
        if (!$data['kantin_info']) {
            $this->session->set_flashdata('error', 'Data kantin tidak ditemukan.');
            redirect('dashboard');
            return;
        }

        // Ambil tanggal dari input (GET), default hari ini
        $tanggal_harian = $this->input->get('tanggal_harian') ?: date('Y-m-d');
        $data['tanggal_harian'] = $tanggal_harian;

        // Ambil data setoran harian sesuai tanggal
        $data['setoran_harian'] = $this->Transaksi_model->get_setoran_harian_per_pemilik($kantin_id, $tanggal_harian);

        // Ambil histori setoran lengkap
        $data['histori_setoran'] = $this->Transaksi_model->get_setoran_histori_per_pemilik($kantin_id);

        $this->load->view('templates/header', $data);
        $this->load->view('dashboard/histori_setoran_operator', $data);
        $this->load->view('templates/footer');
    }

    public function detail_kantin($jenis = null)
    {
        if (!$jenis || !in_array($jenis, ['putra', 'putri'])) {
            redirect('dashboard');
        }

        $data['title'] = 'Detail Kantin ' . ucfirst($jenis);
        $data['jenis_kantin'] = $jenis;

        // Ambil data kantin berdasarkan jenis
        $kantin = $this->Kantin_model->get_kantin_by_jenis($jenis);
        $data['kantin_info'] = $kantin;

        if ($kantin) {
            $kantin_id = $kantin->id;

            // Statistik total pendapatan
            $data['total_pendapatan'] = $this->Transaksi_model->get_total_revenue(null, null, $kantin_id);

            // Statistik hari ini (sinkron: santri + ustadz)
            $data['transaksi_hari_ini'] = $this->Transaksi_model->count_nota_hari_ini($kantin_id) + $this->Transaksi_model->count_nota_ustadz_hari_ini($kantin_id);
            $data['pendapatan_hari_ini'] = $this->Transaksi_model->get_pendapatan_hari_ini($kantin_id) + $this->Transaksi_model->get_pendapatan_ustadz_hari_ini($kantin_id);
            $data['keuntungan_hari_ini'] = $this->Transaksi_model->get_keuntungan_hari_ini($kantin_id); // (jika ingin, bisa tambahkan keuntungan ustadz jika ada)

            // Statistik pembayaran (tampilkan tunai santri + semua ustadz sebagai tunai)
            $data['transaksi_tunai'] = array_merge(
                $this->get_transaksi_tunai_hari_ini($kantin_id),
                $this->get_transaksi_ustadz_tunai_hari_ini($kantin_id)
            );
            $data['transaksi_saldo'] = $this->get_transaksi_saldo_hari_ini($kantin_id);

            // Data transaksi 7 hari terakhir
            $data['transaksi_harian'] = $this->get_transaksi_harian($kantin_id);

            // Transaksi terakhir (20 transaksi)
            $data['recent_transactions'] = $this->Transaksi_model->get_recent_all_transactions(20, $kantin_id);

            // Statistik menu terlaris
            $data['menu_terlaris'] = $this->Transaksi_model->get_menu_terlaris($kantin_id, 10);

            // Total menu di kantin ini
            $data['total_menu'] = $this->Menu_model->count_menu_by_kantin($kantin_id);

            // Statistik stok
            $data['stok_stats'] = $this->Menu_model->get_stok_stats_by_kantin($kantin_id);

            // Hitung total tunai yang dipegang hari ini (tunai santri + ustadz)
            $tunai_santri = $this->Transaksi_model->get_pendapatan_tunai_hari_ini($kantin_id);
            $tunai_ustadz = $this->Transaksi_model->get_pendapatan_ustadz_hari_ini($kantin_id);
            $data['tunai_saat_ini'] = $tunai_santri + $tunai_ustadz;
        }

        $this->load->view('templates/header', $data);
        $this->load->view('dashboard/detail_kantin', $data);
        $this->load->view('templates/footer');
    }

    // Ambil transaksi tunai ustadz hari ini untuk detail_kantin
    private function get_transaksi_ustadz_tunai_hari_ini($kantin_id)
    {
        $tanggal = date('Y-m-d');
        $where_kantin = $kantin_id ? "AND mk.kantin_id = " . intval($kantin_id) : "";
        $sql = "
            SELECT
                tu.created_at,
                u.nama as nama_pelanggan,
                '' as nomor_induk,
                '' as kelas,
                tu.total_harga,
                mk.nama_menu,
                'ustadz' as jenis,
                tu.keterangan,
                tu.jumlah,
                tu.metode_pembayaran
            FROM transaksi_ustadz tu
            JOIN ustadz u ON tu.ustadz_id = u.id
            JOIN menu_kantin mk ON tu.menu_id = mk.id
            WHERE DATE(tu.created_at) = '$tanggal' $where_kantin
            ORDER BY tu.created_at DESC
        ";
        return $this->db->query($sql)->result_array();
    }

    public function debug_setoran_harian()
    {
        // Hanya untuk debugging
        $kantin_id = $this->session->userdata('kantin_id');

        echo "<h2>Debug Setoran Harian</h2>";
        echo "<p>Kantin ID: " . $kantin_id . "</p>";
        echo "<p>Tanggal: " . date('Y-m-d') . "</p>";

        // Cek transaksi hari ini
        $this->db->select('COUNT(*) as total');
        $this->db->from('transaksi_kantin');
        $this->db->where('kantin_id', $kantin_id);
        $this->db->where('DATE(created_at)', date('Y-m-d'));
        $this->db->where('status', 'selesai');
        $transaksi_hari_ini = $this->db->get()->row();
        echo "<p>Total Transaksi Hari Ini: " . $transaksi_hari_ini->total . "</p>";

        // Cek menu dengan pemilik
        $this->db->select('id, nama_menu, pemilik, harga_beli');
        $this->db->from('menu_kantin');
        $this->db->where('kantin_id', $kantin_id);
        $this->db->where('pemilik !=', '');
        $this->db->where('pemilik IS NOT NULL');
        $menu_dengan_pemilik = $this->db->get()->result();
        echo "<p>Menu dengan pemilik: " . count($menu_dengan_pemilik) . "</p>";

        // Cek transaksi dengan join menu
        $this->db->select('transaksi_kantin.*, menu_kantin.pemilik, menu_kantin.nama_menu');
        $this->db->from('transaksi_kantin');
        $this->db->join('menu_kantin', 'menu_kantin.id = transaksi_kantin.menu_id');
        $this->db->where('transaksi_kantin.kantin_id', $kantin_id);
        $this->db->where('DATE(transaksi_kantin.created_at)', date('Y-m-d'));
        $this->db->where('transaksi_kantin.status', 'selesai');
        $transaksi_dengan_menu = $this->db->get()->result();
        echo "<p>Transaksi dengan menu: " . count($transaksi_dengan_menu) . "</p>";

        // Cek setoran harian
        $setoran_harian = $this->Transaksi_model->get_setoran_harian_per_pemilik($kantin_id);
        echo "<p>Setoran harian: " . count($setoran_harian) . "</p>";

        if (!empty($setoran_harian)) {
            echo "<h3>Data Setoran Harian:</h3>";
            echo "<table border='1'>";
            echo "<tr><th>Pemilik</th><th>Total Item</th><th>Total Setoran</th></tr>";
            foreach ($setoran_harian as $setoran) {
                echo "<tr>";
                echo "<td>" . $setoran->pemilik . "</td>";
                echo "<td>" . $setoran->total_item_terjual . "</td>";
                echo "<td>" . $setoran->total_setoran . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }

        // Cek query SQL langsung
        $sql = "
            SELECT 
                menu_kantin.pemilik, 
                SUM(transaksi_kantin.jumlah) as total_item_terjual,
                SUM(transaksi_kantin.jumlah * menu_kantin.harga_beli) as total_setoran
            FROM transaksi_kantin
            JOIN menu_kantin ON menu_kantin.id = transaksi_kantin.menu_id
            WHERE transaksi_kantin.status = 'selesai'
            AND DATE(transaksi_kantin.created_at) = '" . date('Y-m-d') . "'
            AND transaksi_kantin.kantin_id = " . $kantin_id . "
            GROUP BY menu_kantin.pemilik
            ORDER BY menu_kantin.pemilik ASC
        ";

        $result = $this->db->query($sql);
        echo "<h3>Query SQL Langsung:</h3>";
        echo "<pre>" . $sql . "</pre>";
        echo "<p>Hasil: " . $result->num_rows() . " baris</p>";

        if ($result->num_rows() > 0) {
            echo "<table border='1'>";
            echo "<tr><th>Pemilik</th><th>Total Item</th><th>Total Setoran</th></tr>";
            foreach ($result->result() as $row) {
                echo "<tr>";
                echo "<td>" . $row->pemilik . "</td>";
                echo "<td>" . $row->total_item_terjual . "</td>";
                echo "<td>" . $row->total_setoran . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
}
