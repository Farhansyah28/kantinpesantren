<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Menu_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_all_menu($kantin_id = NULL)
    {
        // Jika bukan admin, filter berdasarkan kantin_id
        if ($this->session->userdata('role') !== 'admin') {
            $this->db->where('kantin_id', $kantin_id);
        }
        // Admin bisa melihat semua menu dari semua kantin
        $this->db->order_by('nama_menu', 'ASC');
        return $this->db->get('menu_kantin')->result();
    }

    public function get_menu_aktif($kantin_id = NULL)
    {
        $this->db->where('stok >', 0);
        // Jika bukan admin, filter berdasarkan kantin_id
        if ($this->session->userdata('role') !== 'admin') {
            $this->db->where('kantin_id', $kantin_id);
        }
        return $this->db->get('menu_kantin')->result();
    }

    public function get_menu($id, $kantin_id = NULL)
    {
        $this->db->where('id', $id);
        // Jika bukan admin, filter berdasarkan kantin_id
        if ($this->session->userdata('role') !== 'admin') {
            $this->db->where('kantin_id', $kantin_id);
        }
        return $this->db->get('menu_kantin')->row();
    }

    public function get_menu_by_id($id, $kantin_id = NULL)
    {
        return $this->get_menu($id, $kantin_id);
    }

    public function get_by_id($id, $kantin_id = NULL)
    {
        return $this->get_menu($id, $kantin_id);
    }

    public function get_all_available($kantin_id = NULL)
    {
        $this->db->where('stok >', 0);
        // Jika bukan admin, filter berdasarkan kantin_id
        if ($this->session->userdata('role') !== 'admin') {
            $this->db->where('kantin_id', $kantin_id);
        }
        $this->db->order_by('nama_menu', 'ASC');
        return $this->db->get('menu_kantin')->result();
    }

    public function add_menu($data)
    {
        return $this->db->insert('menu_kantin', $data);
    }

    public function update_menu($id, $data, $kantin_id = NULL)
    {
        $this->db->where('id', $id);
        // Jika bukan admin, filter berdasarkan kantin_id
        if ($this->session->userdata('role') !== 'admin') {
            $this->db->where('kantin_id', $kantin_id);
        }
        return $this->db->update('menu_kantin', $data);
    }

    public function delete_menu($id, $kantin_id = NULL)
    {
        $this->db->where('id', $id);
        // Jika bukan admin, filter berdasarkan kantin_id
        if ($this->session->userdata('role') !== 'admin') {
            $this->db->where('kantin_id', $kantin_id);
        }
        return $this->db->delete('menu_kantin');
    }

    public function update_stok($id, $jumlah, $kantin_id = NULL)
    {
        $this->db->set('stok', 'stok - ' . $jumlah, FALSE);
        $this->db->where('id', $id);
        if ($kantin_id !== NULL) {
            $this->db->where('kantin_id', $kantin_id);
        }
        return $this->db->update('menu_kantin');
    }

    public function count_all($kantin_id = NULL)
    {
        // Jika bukan admin, filter berdasarkan kantin_id
        if ($this->session->userdata('role') !== 'admin') {
            $this->db->where('kantin_id', $kantin_id);
        }
        return $this->db->count_all_results('menu_kantin');
    }

    public function count_menu($kantin_id = NULL)
    {
        if ($kantin_id !== NULL) {
            $this->db->where('kantin_id', $kantin_id);
        }
        return $this->db->count_all_results('menu_kantin');
    }

    // Method untuk manajemen stok
    public function tambah_stok($menu_id, $jumlah, $harga_beli, $keterangan, $admin_id, $kantin_id = NULL)
    {
        $this->db->trans_start();

        // Ambil stok sebelum penambahan
        $menu = $this->db->get_where('menu_kantin', ['id' => $menu_id])->row();
        $stok_sebelum = $menu ? $menu->stok : 0;
        $stok_sesudah = $stok_sebelum + $jumlah;

        // Update stok menu hanya berdasarkan id
        $this->db->set('stok', $stok_sesudah);
        $this->db->where('id', $menu_id);
        $this->db->update('menu_kantin');

        // Jika tidak ada baris yang berubah, return false
        if ($this->db->affected_rows() === 0) {
            $this->db->trans_complete();
            return false;
        }

        // Catat riwayat stok
        $riwayat_data = [
            'menu_id' => $menu_id,
            'jenis' => 'masuk',
            'jumlah' => $jumlah,
            'stok_sebelum' => $stok_sebelum,
            'stok_sesudah' => $stok_sesudah,
            'harga_beli' => $harga_beli,
            'total_harga' => $jumlah * $harga_beli,
            'keterangan' => $keterangan,
            'admin_id' => $admin_id,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('riwayat_stok', $riwayat_data);

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * Mengurangi stok menu dan mencatat riwayatnya.
     * Transaksi harus dikelola oleh method yang memanggilnya.
     */
    public function kurangi_stok($menu_id, $jumlah, $keterangan, $admin_id, $kantin_id = NULL)
    {
        // Log untuk debugging
        log_message('debug', 'Menu_model::kurangi_stok - menu_id: ' . $menu_id . ', jumlah: ' . $jumlah . ', kantin_id: ' . $kantin_id);

        // Get current stock dengan filter kantin_id yang benar
        $this->db->select('stok, nama_menu');
        $this->db->where('id', $menu_id);
        if ($kantin_id !== NULL) {
            $this->db->where('kantin_id', $kantin_id);
        }
        $menu = $this->db->get('menu_kantin')->row();

        // Log hasil query
        log_message('debug', 'Menu_model::kurangi_stok - Menu found: ' . ($menu ? 'Yes' : 'No') . ', Stok: ' . ($menu ? $menu->stok : 'N/A'));

        // Validasi menu ditemukan
        if (!$menu) {
            log_message('error', 'Menu_model::kurangi_stok - Menu tidak ditemukan. menu_id: ' . $menu_id . ', kantin_id: ' . $kantin_id);
            return false;
        }

        // Validasi stok mencukupi
        if ($menu->stok < $jumlah) {
            log_message('error', 'Menu_model::kurangi_stok - Stok tidak mencukupi. Stok: ' . $menu->stok . ', Dibutuhkan: ' . $jumlah . ', Menu: ' . $menu->nama_menu);
            return false;
        }

        // Kurangi stok
        $this->db->set('stok', 'stok - ' . (int)$jumlah, FALSE);
        $this->db->where('id', $menu_id);
        if ($kantin_id !== NULL) {
            $this->db->where('kantin_id', $kantin_id);
        }
        $this->db->update('menu_kantin');

        // Periksa apakah update berhasil
        if ($this->db->affected_rows() === 0) {
            log_message('error', 'Menu_model::kurangi_stok - Update stok gagal. menu_id: ' . $menu_id . ', affected_rows: ' . $this->db->affected_rows());
            return false;
        }

        // Catat riwayat stok
        $riwayat_data = [
            'menu_id' => $menu_id,
            'jenis' => 'keluar',
            'jumlah' => -$jumlah,
            'stok_sebelum' => $menu->stok,
            'stok_sesudah' => $menu->stok - $jumlah,
            'keterangan' => $keterangan,
            'admin_id' => $admin_id,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $riwayat_result = $this->db->insert('riwayat_stok', $riwayat_data);

        if (!$riwayat_result) {
            log_message('error', 'Menu_model::kurangi_stok - Insert riwayat stok gagal. menu_id: ' . $menu_id);
            return false;
        }

        log_message('debug', 'Menu_model::kurangi_stok - Berhasil kurangi stok. menu_id: ' . $menu_id . ', stok_sebelum: ' . $menu->stok . ', stok_sesudah: ' . ($menu->stok - $jumlah));

        return true;
    }

    public function get_riwayat_stok($menu_id = NULL, $limit = NULL, $kantin_id = NULL)
    {
        $this->db->select('
            riwayat_stok.*,
            menu_kantin.nama_menu,
            menu_kantin.stok,
            users.username as admin_nama
        ');
        $this->db->from('riwayat_stok');
        $this->db->join('menu_kantin', 'menu_kantin.id = riwayat_stok.menu_id');
        $this->db->join('users', 'users.id = riwayat_stok.admin_id', 'left');

        if ($menu_id !== NULL) {
            $this->db->where('riwayat_stok.menu_id', $menu_id);
        }

        $role = $this->session->userdata('role');
        if ($role === 'operator') {
            $this->db->where('menu_kantin.kantin_id', $kantin_id);
        }

        $this->db->order_by('riwayat_stok.created_at', 'DESC');

        if ($limit !== NULL) {
            $this->db->limit($limit);
        }

        return $this->db->get()->result();
    }

    public function get_stok_summary($menu_id = NULL, $kantin_id = NULL)
    {
        $this->db->select('
            menu_kantin.nama_menu,
            kantin.nama as nama_kantin,
            kantin.jenis as jenis_kantin,
            SUM(CASE WHEN riwayat_stok.jenis = "masuk" THEN riwayat_stok.jumlah ELSE 0 END) as total_masuk,
            SUM(CASE WHEN riwayat_stok.jenis = "keluar" THEN riwayat_stok.jumlah ELSE 0 END) as total_keluar,
            SUM(CASE WHEN riwayat_stok.jenis = "masuk" THEN riwayat_stok.total_harga ELSE 0 END) as total_pembelian,
            menu_kantin.stok as stok_sekarang
        ');
        $this->db->from('riwayat_stok');
        $this->db->join('menu_kantin', 'menu_kantin.id = riwayat_stok.menu_id');
        $this->db->join('kantin', 'kantin.id = menu_kantin.kantin_id', 'left');

        if ($menu_id !== NULL) {
            $this->db->where('riwayat_stok.menu_id', $menu_id);
        }

        // Hanya operator yang difilter berdasarkan kantin_id
        $role = $this->session->userdata('role');
        if ($role === 'operator') {
            $this->db->where('menu_kantin.kantin_id', $kantin_id);
        }

        $this->db->group_by('riwayat_stok.menu_id');
        return $this->db->get()->result();
    }

    public function search_menu($keyword, $kantin_id = NULL)
    {
        $this->db->like('nama_menu', $keyword);
        // Jika bukan admin, filter berdasarkan kantin_id
        if ($this->session->userdata('role') !== 'admin') {
            $this->db->where('kantin_id', $kantin_id);
        }
        $this->db->order_by('nama_menu', 'ASC');
        return $this->db->get('menu_kantin')->result();
    }

    public function get_all_menus_with_kantin_info()
    {
        $this->db->select('menu_kantin.*, kantin.nama as nama_kantin, kantin.jenis as jenis_kantin');
        $this->db->from('menu_kantin');
        $this->db->join('kantin', 'kantin.id = menu_kantin.kantin_id', 'left');
        $this->db->order_by('kantin.nama', 'ASC');
        $this->db->order_by('menu_kantin.nama_menu', 'ASC');
        return $this->db->get()->result();
    }

    // Fungsi-fungsi baru untuk admin (tidak dibatasi kantin_id)

    public function get_menu_by_id_unrestricted($id)
    {
        return $this->db->get_where('menu_kantin', ['id' => $id])->row();
    }

    public function update_menu_unrestricted($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('menu_kantin', $data);
    }

    public function delete_menu_unrestricted($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('menu_kantin');
    }

    public function tambah_stok_form($menu_id)
    {
        $kantin_id = $this->session->userdata('kantin_id');

        // ... existing code ...
    }

    public function get_stok_stats_by_kantin($kantin_id)
    {
        $menu_kantin = $this->get_all_menu($kantin_id);

        $stats = [
            'total_menu'  => count($menu_kantin),
            'stok_aman'   => 0,
            'stok_menipis' => 0,
            'stok_habis'  => 0
        ];

        foreach ($menu_kantin as $menu) {
            if ($menu->stok == 0) {
                $stats['stok_habis']++;
            } elseif ($menu->stok <= 10) {
                $stats['stok_menipis']++;
            } else {
                $stats['stok_aman']++;
            }
        }
        return $stats;
    }

    /**
     * Cek apakah sudah ada menu dengan nama dan kantin yang sama, tapi boleh jika pemiliknya berbeda
     */
    public function is_duplicate_menu($nama_menu, $kantin_id, $pemilik)
    {
        $this->db->where('nama_menu', $nama_menu);
        $this->db->where('kantin_id', $kantin_id);
        $this->db->where('pemilik', $pemilik);
        return $this->db->count_all_results('menu_kantin') > 0;
    }

    /**
     * Count menu by specific kantin
     */
    public function count_menu_by_kantin($kantin_id)
    {
        $this->db->where('kantin_id', $kantin_id);
        return $this->db->count_all_results('menu_kantin');
    }

    /**
     * Ambil daftar pemilik unik dari menu_kantin
     */
    public function get_all_pemilik($kantin_id = null)
    {
        $this->db->select('pemilik');
        $this->db->from('menu_kantin');
        if ($kantin_id) {
            $this->db->where('kantin_id', $kantin_id);
        }
        $this->db->where('pemilik !=', '');
        $this->db->group_by('pemilik');
        $this->db->order_by('pemilik', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }
}
