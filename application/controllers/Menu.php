<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu extends CI_Controller {
    
    // Property declarations for autoloaded models to prevent PHP 8.2+ deprecation warnings
    public $Santri_model;
    public $Tabungan_model;
    public $User_model;
    public $Menu_model;
    public $Transaksi_model;
    public $Ustadz_model;
    public $Kantin_model;
    public $Activity_log_model;
    
    public function __construct() {
        parent::__construct();
        $this->load->model(['Menu_model', 'Kantin_model', 'Santri_model', 'User_model']);
        $this->load->library(['session', 'form_validation']);
        
        // Cek login
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }
    }

    // Helper function untuk mengubah format rupiah ke angka
    private function rupiah_to_number($rupiah_string) {
        if (empty($rupiah_string)) return 0;
        // Hapus semua karakter kecuali angka
        return (int) preg_replace('/[^\d]/', '', $rupiah_string);
    }

    public function index() {
        $role = $this->session->userdata('role');
        $user_id = $this->session->userdata('user_id');
        
        if ($role === 'admin' || $role === 'keuangan') {
            // Admin dan Keuangan bisa akses semua kantin
            $kantin_id = $this->input->get('kantin_id');
            if (!$kantin_id) {
                $kantin_id = null; // Tampilkan semua kantin
            }
            $data['title'] = 'Daftar Menu Semua Kantin';
            $data['kantin_info'] = null;
            $data['menu'] = $this->Menu_model->get_all_menus_with_kantin_info();
        } elseif ($role === 'operator') {
            // Operator hanya bisa akses kantin sesuai gender
            $user = $this->User_model->get_by_id($user_id);
            if ($user && isset($user->gender)) {
                if ($user->gender === 'L') {
                    $kantin = $this->Kantin_model->get_kantin_by_jenis('putra');
                } else {
                    $kantin = $this->Kantin_model->get_kantin_by_jenis('putri');
                }
                $kantin_id = $kantin ? $kantin->id : null;
                $data['menu'] = $this->Menu_model->get_all_menu($kantin_id);
                $data['title'] = 'Daftar Menu Kantin - ' . ($kantin ? $kantin->nama : '-');
                $data['kantin_info'] = $kantin;
            } else {
                $data['menu'] = [];
                $data['title'] = 'Daftar Menu Kantin';
                $data['kantin_info'] = null;
            }
        } else {
            // Role lain (jika ada)
            $kantin_id = $this->session->userdata('kantin_id');
            $data['title'] = 'Daftar Menu Kantin - ' . $this->session->userdata('kantin_nama');
            $data['kantin_info'] = $this->Kantin_model->get_kantin($kantin_id);
            $data['menu'] = $this->Menu_model->get_all_menu($kantin_id);
        }
        
        $this->load->view('templates/header', $data);
        $this->load->view('menu/index', $data);
        $this->load->view('templates/footer');
    }

    public function create() {
        if (!in_array($this->session->userdata('role'), ['admin', 'keuangan', 'operator'])) {
            $this->session->set_flashdata('error', 'Hanya admin, keuangan, dan operator yang dapat mengakses halaman ini.');
            redirect('menu');
        }

        $kantin_id = $this->session->userdata('kantin_id');
        $data['title'] = 'Tambah Menu Baru - ' . $this->session->userdata('kantin_nama');
        $data['kantin_info'] = $this->Kantin_model->get_kantin($kantin_id);
        $data['all_kantin'] = $this->Kantin_model->get_all_kantin();
        $data['pemilik_list'] = $this->Menu_model->get_all_pemilik($kantin_id);
        $this->load->view('templates/header', $data);
        $this->load->view('menu/create', $data);
        $this->load->view('templates/footer');
    }

    public function store() {
        if (!in_array($this->session->userdata('role'), ['admin', 'keuangan', 'operator'])) {
            $this->session->set_flashdata('error', 'Aksi ini hanya diizinkan untuk admin, keuangan, dan operator.');
            redirect('menu');
        }

        // Unformat input harga sebelum validasi agar validasi numeric tidak gagal
        $_POST['harga_beli'] = $this->rupiah_to_number($this->input->post('harga_beli'));
        $_POST['harga_jual'] = $this->rupiah_to_number($this->input->post('harga_jual'));

        $this->form_validation->set_rules('nama_menu', 'Nama Menu', 'required');
        $this->form_validation->set_rules('pemilik', 'Pemilik', 'required');
        $this->form_validation->set_rules('harga_beli', 'Harga Beli', 'required|numeric');
        $this->form_validation->set_rules('harga_jual', 'Harga Jual', 'required|numeric');
        // Jika pemilik baru, validasi input baru
        if ($this->input->post('pemilik') === '__new__') {
            $this->form_validation->set_rules('pemilik_baru', 'Pemilik Baru', 'required');
        }

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('menu/create');
            return;
        }

        $role = $this->session->userdata('role');
        $user_id = $this->session->userdata('user_id');
        $all_kantin = [];
        if ($role === 'operator') {
            // Ambil gender operator dari tabel users
            $user = $this->User_model->get_by_id($user_id);
            if ($user && isset($user->gender)) {
                if ($user->gender === 'L') {
                    $kantin = $this->Kantin_model->get_kantin_by_jenis('putra');
                    if ($kantin) $all_kantin[] = $kantin;
                } elseif ($user->gender === 'P') {
                    $kantin = $this->Kantin_model->get_kantin_by_jenis('putri');
                    if ($kantin) $all_kantin[] = $kantin;
                }
            }
        } else {
            // Admin/keuangan: semua kantin aktif
            $all_kantin = $this->Kantin_model->get_kantin_aktif();
        }
        if (empty($all_kantin)) {
            $this->session->set_flashdata('error', 'Tidak ada kantin aktif yang ditemukan untuk role Anda.');
            redirect('menu/create');
            return;
        }

        // Ambil nama pemilik
        $pemilik = $this->input->post('pemilik');
        if ($pemilik === '__new__') {
            $pemilik = $this->input->post('pemilik_baru');
        }

        // Cek duplikat menu (nama_menu & kantin_id & pemilik)
        foreach ($all_kantin as $kantin) {
            $nama_menu = $this->input->post('nama_menu');
            if ($this->Menu_model->is_duplicate_menu($nama_menu, $kantin->id, $pemilik)) {
                $this->session->set_flashdata('error', 'Menu dengan nama dan pemilik yang sama sudah ada di kantin ini.');
                redirect('menu/create');
                return;
            }
        }

        $this->db->trans_start();
        foreach ($all_kantin as $kantin) {
            $data = [
                'kantin_id' => $kantin->id,
                'nama_menu' => $this->input->post('nama_menu'),
                'pemilik' => $pemilik,
                'harga_beli' => $this->input->post('harga_beli'),
                'harga_jual' => $this->input->post('harga_jual'),
                'stok' => 0, // Stok awal selalu 0
                'created_at' => date('Y-m-d H:i:s')
            ];
            $this->Menu_model->add_menu($data);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            // Log error penambahan menu
            $this->Activity_log_model->log_system('MENU_CREATE_FAILED', [
                'nama_menu' => $this->input->post('nama_menu'),
                'pemilik' => $pemilik,
                'harga_beli' => $this->input->post('harga_beli'),
                'harga_jual' => $this->input->post('harga_jual'),
                'kantin_count' => count($all_kantin),
                'error' => 'Database transaction failed'
            ], 'error');
            
            $this->session->set_flashdata('error', 'Gagal menambahkan menu ke kantin.');
            redirect('menu/create');
        } else {
            // Log sukses penambahan menu
            $this->Activity_log_model->log_system('MENU_CREATE_SUCCESS', [
                'nama_menu' => $this->input->post('nama_menu'),
                'pemilik' => $pemilik,
                'harga_beli' => $this->input->post('harga_beli'),
                'harga_jual' => $this->input->post('harga_jual'),
                'kantin_count' => count($all_kantin),
                'kantin_list' => array_map(function($kantin) {
                    return ['id' => $kantin->id, 'nama' => $kantin->nama];
                }, $all_kantin)
            ], 'success');
            
            $this->session->set_flashdata('success', 'Menu berhasil ditambahkan.');
            redirect('menu');
        }
    }

    public function edit($id) {
        if ($this->session->userdata('role') !== 'admin' && $this->session->userdata('role') !== 'keuangan') {
            $this->session->set_flashdata('error', 'Hanya admin dan keuangan yang dapat mengakses halaman ini.');
            redirect('menu');
            return;
        }
        
        $this->form_validation->set_rules('nama_menu', 'Nama Menu', 'required');
        $this->form_validation->set_rules('pemilik', 'Pemilik', 'required');
        $this->form_validation->set_rules('harga_beli', 'Harga Beli', 'required|numeric');
        $this->form_validation->set_rules('harga_jual', 'Harga Jual', 'required|numeric');

        if ($this->form_validation->run() === FALSE) {
            $data['title'] = 'Edit Menu';
            $data['menu'] = $this->Menu_model->get_menu_by_id_unrestricted($id);
            
            if (!$data['menu']) {
                $this->session->set_flashdata('error', 'Menu tidak ditemukan');
                redirect('menu');
                return;
            }
            
            // Untuk breadcrumb, kita bisa ambil info kantin
            $data['kantin_info'] = $this->Kantin_model->get_kantin($data['menu']->kantin_id);
            
            $this->load->view('templates/header', $data);
            $this->load->view('menu/edit', $data);
            $this->load->view('templates/footer');
        } else {
            $data = [
                'nama_menu' => $this->input->post('nama_menu'),
                'pemilik' => $this->input->post('pemilik'),
                'harga_beli' => $this->input->post('harga_beli'),
                'harga_jual' => $this->input->post('harga_jual'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if ($this->Menu_model->update_menu_unrestricted($id, $data)) {
                $this->session->set_flashdata('success', 'Menu berhasil diperbarui');
                redirect('menu');
            } else {
                $this->session->set_flashdata('error', 'Gagal memperbarui menu');
                redirect('menu/edit/' . $id);
            }
        }
    }

    public function update($id) {
        if ($this->session->userdata('role') !== 'admin' && $this->session->userdata('role') !== 'keuangan') {
            $this->session->set_flashdata('error', 'Aksi ini hanya diizinkan untuk admin dan keuangan.');
            redirect('menu');
            return;
        }
        
        $this->form_validation->set_rules('nama_menu', 'Nama Menu', 'required');
        $this->form_validation->set_rules('pemilik', 'Pemilik', 'required');
        $this->form_validation->set_rules('harga_beli', 'Harga Beli', 'required|numeric');
        $this->form_validation->set_rules('harga_jual', 'Harga Jual', 'required|numeric');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('menu/edit/' . $id);
            return;
        }

        $data = [
            'nama_menu' => $this->input->post('nama_menu'),
            'pemilik' => $this->input->post('pemilik'),
            'harga_beli' => $this->input->post('harga_beli'),
            'harga_jual' => $this->input->post('harga_jual'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->Menu_model->update_menu_unrestricted($id, $data)) {
            $this->session->set_flashdata('success', 'Menu berhasil diperbarui');
            redirect('menu');
        } else {
            $this->session->set_flashdata('error', 'Gagal memperbarui menu');
            redirect('menu/edit/' . $id);
        }
    }

    public function delete($id) {
        if ($this->session->userdata('role') !== 'admin' && $this->session->userdata('role') !== 'keuangan') {
            $this->session->set_flashdata('error', 'Aksi ini hanya diizinkan untuk admin dan keuangan.');
            redirect('menu');
            return;
        }
        
        if ($this->Menu_model->delete_menu_unrestricted($id)) {
            $this->session->set_flashdata('success', 'Menu berhasil dihapus');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus menu. Menu mungkin tidak ditemukan.');
        }
        redirect('menu');
    }

    public function tambah_stok_form($menu_id) {
        if (!in_array($this->session->userdata('role'), ['admin', 'keuangan', 'operator'])) {
            $this->session->set_flashdata('error', 'Akses ke tambah stok hanya untuk admin, keuangan, dan operator.');
            redirect('menu');
            return;
        }
        
        $kantin_id = $this->session->userdata('kantin_id');
        $role = $this->session->userdata('role');
        
        if ($role === 'keuangan') {
            $menu = $this->Menu_model->get_menu_by_id_unrestricted($menu_id);
        } else {
        $menu = $this->Menu_model->get_menu($menu_id, $kantin_id);
        }
        if (!$menu) {
            $this->session->set_flashdata('error', 'Menu tidak ditemukan');
            redirect('menu');
        }
        
        $data['title'] = 'Tambah Stok Menu - ' . $this->session->userdata('kantin_nama');
        $data['kantin_info'] = $this->Kantin_model->get_kantin($kantin_id);
        $data['menu'] = $menu;
        
        $this->load->view('templates/header', $data);
        $this->load->view('menu/tambah_stok', $data);
        $this->load->view('templates/footer');
    }

    public function tambah_stok() {
        if (!in_array($this->session->userdata('role'), ['admin', 'keuangan', 'operator'])) {
            $this->session->set_flashdata('error', 'Akses ke tambah stok hanya untuk admin, keuangan, dan operator.');
            redirect('menu');
            return;
        }
        
        $kantin_id = $this->session->userdata('kantin_id');
        
        $this->form_validation->set_rules('menu_id', 'Menu', 'required|numeric');
        $this->form_validation->set_rules('jumlah_tambah', 'Jumlah', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('harga_beli_baru', 'Harga Beli Baru', 'required');
        
        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('menu');
        }
        
        $menu_id = $this->input->post('menu_id');
        $jumlah = $this->input->post('jumlah_tambah');
        $harga_beli = $this->rupiah_to_number($this->input->post('harga_beli_baru'));
        $keterangan = $this->input->post('keterangan');
        $admin_id = $this->session->userdata('user_id');
        
        // Ambil info menu untuk logging
        $menu = $this->Menu_model->get_menu($menu_id, $kantin_id);
        
        if ($this->Menu_model->tambah_stok($menu_id, $jumlah, $harga_beli, $keterangan, $admin_id, $kantin_id)) {
            // Log sukses tambah stok
            $this->Activity_log_model->log_system('STOK_TAMBAH_SUCCESS', [
                'menu_id' => $menu_id,
                'menu_nama' => $menu ? $menu->nama_menu : 'Unknown',
                'jumlah_tambah' => $jumlah,
                'harga_beli' => $harga_beli,
                'keterangan' => $keterangan,
                'kantin_id' => $kantin_id
            ], 'success');
            
            $this->session->set_flashdata('success', 'Stok berhasil ditambahkan');
        } else {
            // Log error tambah stok
            $this->Activity_log_model->log_system('STOK_TAMBAH_FAILED', [
                'menu_id' => $menu_id,
                'menu_nama' => $menu ? $menu->nama_menu : 'Unknown',
                'jumlah_tambah' => $jumlah,
                'harga_beli' => $harga_beli,
                'keterangan' => $keterangan,
                'kantin_id' => $kantin_id,
                'error' => 'Database operation failed'
            ], 'error');
            
            $this->session->set_flashdata('error', 'Gagal menambahkan stok');
        }
        
        redirect('menu');
    }
    
    public function riwayat_stok($menu_id = null) {
        if (!in_array($this->session->userdata('role'), ['admin', 'keuangan', 'operator'])) {
            $this->session->set_flashdata('error', 'Akses ke riwayat stok hanya untuk admin, keuangan, dan operator.');
            redirect('menu');
            return;
        }
        
        $kantin_id = $this->session->userdata('kantin_id');
        
        if ($menu_id) {
            // Riwayat stok untuk menu tertentu
            $data['title'] = 'Riwayat Stok Menu - ' . $this->session->userdata('kantin_nama');
            $data['kantin_info'] = $this->Kantin_model->get_kantin($kantin_id);
            if ($this->session->userdata('role') === 'keuangan') {
                $data['menu'] = $this->Menu_model->get_menu_by_id_unrestricted($menu_id);
            } else {
            $data['menu'] = $this->Menu_model->get_menu($menu_id, $kantin_id);
            }
            $data['riwayat'] = $this->Menu_model->get_riwayat_stok($menu_id, null, $kantin_id);
            $data['summary'] = $this->Menu_model->get_stok_summary($menu_id, $kantin_id);
            
            if (!$data['menu']) {
                $this->session->set_flashdata('error', 'Menu tidak ditemukan');
                redirect('menu');
            }
            
            $this->load->view('templates/header', $data);
            $this->load->view('menu/riwayat_stok', $data);
            $this->load->view('templates/footer');
        } else {
            // Riwayat stok untuk semua menu
            $data['title'] = 'Riwayat Stok Semua Menu - ' . $this->session->userdata('kantin_nama');
            $data['kantin_info'] = $this->Kantin_model->get_kantin($kantin_id);
            $data['riwayat'] = $this->Menu_model->get_riwayat_stok(null, null, $kantin_id);
            $data['summary'] = $this->Menu_model->get_stok_summary(null, $kantin_id);
            
            $this->load->view('templates/header', $data);
            $this->load->view('menu/riwayat_stok_all', $data);
            $this->load->view('templates/footer');
        }
    }

    public function stok_management() {
        if ($this->session->userdata('role') !== 'admin' && $this->session->userdata('role') !== 'keuangan') {
            $this->session->set_flashdata('error', 'Akses ke manajemen stok hanya untuk admin dan keuangan.');
            redirect('menu');
            return;
        }
        $kantin_id = $this->session->userdata('kantin_id');
        
        $data['title'] = 'Manajemen Stok - ' . $this->session->userdata('kantin_nama');
        $data['kantin_info'] = $this->Kantin_model->get_kantin($kantin_id);
        $data['menu'] = $this->Menu_model->get_all_menus_with_kantin_info();
        
        // Hitung statistik stok
        $data['stok_aman'] = 0;
        $data['stok_menipis'] = 0;
        $data['stok_habis'] = 0;
        $data['total_menu'] = count($data['menu']);
        
        foreach ($data['menu'] as $menu) {
            if ($menu->stok == 0) {
                $data['stok_habis']++;
            } elseif ($menu->stok <= 10) {
                $data['stok_menipis']++;
            } else {
                $data['stok_aman']++;
            }
        }
        
        $this->load->view('templates/header', $data);
        $this->load->view('menu/stok_management', $data);
        $this->load->view('templates/footer');
    }

    public function kurangi_stok($menu_id = null) {
        if ($this->session->userdata('role') !== 'admin' && $this->session->userdata('role') !== 'keuangan') {
            $this->session->set_flashdata('error', 'Akses ke kurangi stok hanya untuk admin dan keuangan.');
            redirect('menu');
            return;
        }
        
        if ($menu_id === null) {
            $menu_id = $this->input->post('menu_id');
        }
        $kantin_id = $this->session->userdata('kantin_id');
        
        $this->form_validation->set_rules('jumlah_kurang', 'Jumlah', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('keterangan', 'Keterangan', 'required');
        
        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('menu/stok_management');
        }
        
        $jumlah = $this->input->post('jumlah_kurang');
        $keterangan = $this->input->post('keterangan');
        $admin_id = $this->session->userdata('user_id');
        
        // Ambil info menu untuk logging
        $menu = $this->Menu_model->get_menu($menu_id, $kantin_id);
        
        if ($this->Menu_model->kurangi_stok($menu_id, $jumlah, $keterangan, $admin_id, $kantin_id)) {
            // Log sukses kurangi stok
            $this->Activity_log_model->log_system('STOK_KURANGI_SUCCESS', [
                'menu_id' => $menu_id,
                'menu_nama' => $menu ? $menu->nama_menu : 'Unknown',
                'jumlah_kurang' => $jumlah,
                'keterangan' => $keterangan,
                'kantin_id' => $kantin_id
            ], 'success');
            
            $this->session->set_flashdata('success', 'Stok berhasil dikurangi');
        } else {
            // Log error kurangi stok
            $this->Activity_log_model->log_system('STOK_KURANGI_FAILED', [
                'menu_id' => $menu_id,
                'menu_nama' => $menu ? $menu->nama_menu : 'Unknown',
                'jumlah_kurang' => $jumlah,
                'keterangan' => $keterangan,
                'kantin_id' => $kantin_id,
                'error' => 'Database operation failed'
            ], 'error');
            
            $this->session->set_flashdata('error', 'Gagal mengurangi stok');
        }
        
        redirect('menu/stok_management');
    }

    public function tambah() {
        $kantin_id = $this->session->userdata('kantin_id');
        $data['title'] = 'Form Tambah Menu';
        $data['pemilik_list'] = $this->Menu_model->get_all_pemilik($kantin_id);
        $this->load->view('templates/header', $data);
        $this->load->view('menu/tambah', $data);
        $this->load->view('templates/footer');
    }
} 