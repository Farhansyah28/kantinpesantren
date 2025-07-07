<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Transaksi_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all canteen transactions
     */
    public function get_all($limit = NULL, $offset = NULL, $kantin_id = NULL)
    {
        $this->db->select('transaksi_kantin.*, santri.nama as nama_santri, menu_kantin.nama_menu');
        $this->db->from('transaksi_kantin');
        $this->db->join('santri', 'santri.id = transaksi_kantin.santri_id');
        $this->db->join('menu_kantin', 'menu_kantin.id = transaksi_kantin.menu_id');

        if ($kantin_id !== NULL) {
            $this->db->where('transaksi_kantin.kantin_id', $kantin_id);
        }

        $this->db->order_by('transaksi_kantin.created_at', 'DESC');

        if ($limit) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get()->result();
    }

    /**
     * Get transactions by santri ID
     */
    public function get_by_santri($santri_id, $limit = NULL, $kantin_id = NULL)
    {
        $this->db->select('transaksi_kantin.*, menu_kantin.nama_menu, menu_kantin.harga_jual');
        $this->db->from('transaksi_kantin');
        $this->db->join('menu_kantin', 'menu_kantin.id = transaksi_kantin.menu_id');
        $this->db->where('transaksi_kantin.santri_id', $santri_id);

        if ($kantin_id !== NULL) {
            $this->db->where('transaksi_kantin.kantin_id', $kantin_id);
        }

        $this->db->order_by('transaksi_kantin.created_at', 'DESC');

        if ($limit) {
            $this->db->limit($limit);
        }

        return $this->db->get()->result();
    }

    /**
     * Get transaction by ID
     */
    public function get_by_id($id, $kantin_id = NULL)
    {
        $this->db->select('transaksi_kantin.*, santri.nama as nama_santri, menu_kantin.nama_menu');
        $this->db->from('transaksi_kantin');
        $this->db->join('santri', 'santri.id = transaksi_kantin.santri_id');
        $this->db->join('menu_kantin', 'menu_kantin.id = transaksi_kantin.menu_id');
        $this->db->where('transaksi_kantin.id', $id);

        if ($kantin_id !== NULL) {
            $this->db->where('transaksi_kantin.kantin_id', $kantin_id);
        }

        return $this->db->get()->row();
    }

    /**
     * Create new transaction
     */
    public function create($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('transaksi_kantin', $data);
    }

    /**
     * Update transaction
     */
    public function update($id, $data, $kantin_id = NULL)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);

        if ($kantin_id !== NULL) {
            $this->db->where('kantin_id', $kantin_id);
        }

        return $this->db->update('transaksi_kantin', $data);
    }

    /**
     * Delete transaction
     */
    public function delete($id, $kantin_id = NULL)
    {
        $this->db->where('id', $id);

        if ($kantin_id !== NULL) {
            $this->db->where('kantin_id', $kantin_id);
        }

        return $this->db->delete('transaksi_kantin');
    }

    /**
     * Count all transactions
     */
    public function count_all($kantin_id = NULL)
    {
        if ($kantin_id !== NULL) {
            $this->db->where('kantin_id', $kantin_id);
        }
        return $this->db->count_all_results('transaksi_kantin');
    }

    /**
     * Count transactions by status
     */
    public function count_by_status($status, $kantin_id = NULL)
    {
        $this->db->where('status', $status);

        if ($kantin_id !== NULL) {
            $this->db->where('kantin_id', $kantin_id);
        }

        return $this->db->count_all_results('transaksi_kantin');
    }

    /**
     * Get total revenue
     */
    public function get_total_revenue($date_from = NULL, $date_to = NULL, $kantin_id = NULL)
    {
        $this->db->select('SUM(total_harga) as total_revenue');
        $this->db->from('transaksi_kantin');
        $this->db->where('status', 'selesai');

        if ($kantin_id !== NULL) {
            $this->db->where('kantin_id', $kantin_id);
        }

        if ($date_from) {
            $this->db->where('DATE(created_at) >=', $date_from);
        }
        if ($date_to) {
            $this->db->where('DATE(created_at) <=', $date_to);
        }

        $result = $this->db->get()->row();
        return $result->total_revenue ?? 0;
    }

    /**
     * Get transactions by date range
     */
    public function get_by_date_range($date_from, $date_to, $kantin_id = NULL)
    {
        $this->db->select('transaksi_kantin.*, santri.nama as nama_santri, menu_kantin.nama_menu');
        $this->db->from('transaksi_kantin');
        $this->db->join('santri', 'santri.id = transaksi_kantin.santri_id');
        $this->db->join('menu_kantin', 'menu_kantin.id = transaksi_kantin.menu_id');
        $this->db->where('DATE(transaksi_kantin.created_at) >=', $date_from);
        $this->db->where('DATE(transaksi_kantin.created_at) <=', $date_to);

        if ($kantin_id !== NULL) {
            $this->db->where('transaksi_kantin.kantin_id', $kantin_id);
        }

        $this->db->order_by('transaksi_kantin.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get today's transactions
     */
    public function get_today_transactions($kantin_id = NULL)
    {
        $this->db->select('transaksi_kantin.*, santri.nama as nama_santri, menu_kantin.nama_menu');
        $this->db->from('transaksi_kantin');
        $this->db->join('santri', 'santri.id = transaksi_kantin.santri_id');
        $this->db->join('menu_kantin', 'menu_kantin.id = transaksi_kantin.menu_id');
        $this->db->where('DATE(transaksi_kantin.created_at)', date('Y-m-d'));

        if ($kantin_id !== NULL) {
            $this->db->where('transaksi_kantin.kantin_id', $kantin_id);
        }

        $this->db->order_by('transaksi_kantin.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get transactions by specific date
     */
    public function get_transaksi_by_date($date, $kantin_id = NULL)
    {
        $this->db->select('transaksi_kantin.*, 
                          santri.nama as santri_nama, 
                          santri.nomor_induk as santri_nis, 
                          santri.kelas as santri_kelas,
                          menu_kantin.nama_menu as menu_nama, 
                          menu_kantin.pemilik as menu_pemilik,
                          menu_kantin.harga_jual as harga_jual,
                          menu_kantin.harga_beli as harga_beli,
                          users.username as admin_nama');
        $this->db->from('transaksi_kantin');
        $this->db->join('santri', 'santri.id = transaksi_kantin.santri_id');
        $this->db->join('menu_kantin', 'menu_kantin.id = transaksi_kantin.menu_id');
        $this->db->join('users', 'users.id = transaksi_kantin.admin_id', 'left');
        $this->db->where('DATE(transaksi_kantin.created_at)', $date);

        if ($kantin_id !== NULL) {
            $this->db->where('transaksi_kantin.kantin_id', $kantin_id);
        }

        $this->db->order_by('transaksi_kantin.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get recent transactions
     */
    public function get_recent_transactions($limit = 10, $kantin_id = NULL)
    {
        $this->db->select('transaksi_kantin.*, santri.nama as nama_santri, santri.kelas, santri.nomor_induk, menu_kantin.nama_menu, kantin.nama as nama_kantin, kantin.jenis as jenis_kantin');
        $this->db->from('transaksi_kantin');
        $this->db->join('santri', 'santri.id = transaksi_kantin.santri_id');
        $this->db->join('menu_kantin', 'menu_kantin.id = transaksi_kantin.menu_id');
        $this->db->join('kantin', 'kantin.id = transaksi_kantin.kantin_id', 'left');

        if ($kantin_id !== NULL) {
            $this->db->where('transaksi_kantin.kantin_id', $kantin_id);
        }

        $this->db->order_by('transaksi_kantin.created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    /**
     * Get transactions for specific santri on specific date
     */
    public function get_transaksi_hari_ini($santri_id, $date, $kantin_id = NULL)
    {
        $this->db->select('transaksi_kantin.*');
        $this->db->from('transaksi_kantin');
        $this->db->where('santri_id', $santri_id);
        $this->db->where('DATE(created_at)', $date);
        $this->db->where('status', 'selesai');

        if ($kantin_id !== NULL) {
            $this->db->where('kantin_id', $kantin_id);
        }

        return $this->db->get()->result();
    }

    /**
     * Create new canteen transaction
     */
    public function create_transaksi_kantin($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('transaksi_kantin', $data);
    }

    /**
     * Get transaction statistics by kantin
     */
    public function get_statistics($kantin_id = NULL)
    {
        $this->db->select('
            COUNT(*) as total_transaksi,
            SUM(total_harga) as total_pendapatan,
            COUNT(DISTINCT santri_id) as total_santri,
            COUNT(DISTINCT menu_id) as total_menu
        ');
        $this->db->from('transaksi_kantin');
        $this->db->where('status', 'selesai');

        if ($kantin_id !== NULL) {
            $this->db->where('kantin_id', $kantin_id);
        }

        return $this->db->get()->row();
    }

    /**
     * Get top selling menu by kantin
     */
    public function get_top_menu($limit = 5, $kantin_id = NULL)
    {
        $this->db->select('
            menu_kantin.nama_menu,
            SUM(transaksi_kantin.jumlah) as total_terjual,
            SUM(transaksi_kantin.total_harga) as total_pendapatan
        ');
        $this->db->from('transaksi_kantin');
        $this->db->join('menu_kantin', 'menu_kantin.id = transaksi_kantin.menu_id');
        $this->db->where('transaksi_kantin.status', 'selesai');

        if ($kantin_id !== NULL) {
            $this->db->where('transaksi_kantin.kantin_id', $kantin_id);
        }

        $this->db->group_by('transaksi_kantin.menu_id');
        $this->db->order_by('total_terjual', 'DESC');
        $this->db->limit($limit);

        return $this->db->get()->result();
    }

    /**
     * Count today's transactions
     */
    public function count_transaksi_hari_ini($kantin_id = NULL)
    {
        $this->db->where('DATE(created_at)', date('Y-m-d'));
        $this->db->where('status', 'selesai');

        if ($kantin_id !== NULL) {
            $this->db->where('kantin_id', $kantin_id);
        }

        return $this->db->count_all_results('transaksi_kantin');
    }

    /**
     * Get today's revenue
     */
    public function get_pendapatan_hari_ini($kantin_id = NULL, $metode_pembayaran = NULL)
    {
        $this->db->select('SUM(total_harga) as total_pendapatan');
        $this->db->from('transaksi_kantin');
        $this->db->where('DATE(created_at)', date('Y-m-d'));
        $this->db->where('status', 'selesai');
        if ($kantin_id !== NULL) {
            $this->db->where('kantin_id', $kantin_id);
        }
        if ($metode_pembayaran !== NULL) {
            $this->db->where('metode_pembayaran', $metode_pembayaran);
        }
        $result = $this->db->get()->row();
        return $result->total_pendapatan ?? 0;
    }

    /**
     * Get today's profit by canteen
     */
    public function get_keuntungan_hari_ini($kantin_id = NULL)
    {
        $this->db->select('SUM((menu_kantin.harga_jual - menu_kantin.harga_beli) * transaksi_kantin.jumlah) as total_keuntungan');
        $this->db->from('transaksi_kantin');
        $this->db->join('menu_kantin', 'menu_kantin.id = transaksi_kantin.menu_id');
        $this->db->where('DATE(transaksi_kantin.created_at)', date('Y-m-d'));
        if ($kantin_id !== NULL) {
            $this->db->where('transaksi_kantin.kantin_id', $kantin_id);
        }
        $result = $this->db->get()->row();
        return $result->total_keuntungan ?? 0;
    }

    /**
     * Get daily settlement data per owner
     */
    public function get_setoran_harian_per_pemilik($kantin_id = NULL, $tanggal = NULL)
    {
        $this->db->select('menu_kantin.pemilik, 
                          SUM(transaksi_kantin.jumlah) as total_item_terjual,
                          SUM(transaksi_kantin.jumlah * menu_kantin.harga_beli) as total_setoran');
        $this->db->from('transaksi_kantin');
        $this->db->join('menu_kantin', 'menu_kantin.id = transaksi_kantin.menu_id');
        $this->db->where('transaksi_kantin.status', 'selesai');
        if ($tanggal === NULL) {
            $tanggal = date('Y-m-d');
        }
        $this->db->where('DATE(transaksi_kantin.created_at)', $tanggal);
        if ($kantin_id !== NULL) {
            $this->db->where('transaksi_kantin.kantin_id', $kantin_id);
        }
        $this->db->group_by('menu_kantin.pemilik');
        $this->db->order_by('menu_kantin.pemilik', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Get histori setoran ke pemilik jajanan (group by tanggal dan pemilik)
     */
    public function get_setoran_histori_per_pemilik($kantin_id = NULL)
    {
        $this->db->select('
            menu_kantin.pemilik,
            SUM(transaksi_kantin.total_harga) as total_setoran,
            COUNT(transaksi_kantin.id) as jumlah_transaksi,
            DATE(transaksi_kantin.created_at) as tanggal
        ');
        $this->db->from('transaksi_kantin');
        $this->db->join('menu_kantin', 'menu_kantin.id = transaksi_kantin.menu_id');
        $this->db->where('transaksi_kantin.status', 'selesai');

        if ($kantin_id !== NULL) {
            $this->db->where('transaksi_kantin.kantin_id', $kantin_id);
        }

        $this->db->group_by('menu_kantin.pemilik, DATE(transaksi_kantin.created_at)');
        $this->db->order_by('tanggal', 'DESC');
        $this->db->order_by('total_setoran', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Get menu terlaris berdasarkan jumlah transaksi
     */
    public function get_menu_terlaris($kantin_id = NULL, $limit = 10)
    {
        $this->db->select('
            menu_kantin.nama_menu,
            menu_kantin.pemilik,
            COUNT(transaksi_kantin.id) as jumlah_transaksi,
            SUM(transaksi_kantin.jumlah) as total_terjual,
            SUM(transaksi_kantin.total_harga) as total_pendapatan
        ');
        $this->db->from('transaksi_kantin');
        $this->db->join('menu_kantin', 'menu_kantin.id = transaksi_kantin.menu_id');
        $this->db->where('transaksi_kantin.status', 'selesai');

        if ($kantin_id !== NULL) {
            $this->db->where('transaksi_kantin.kantin_id', $kantin_id);
        }

        $this->db->group_by('menu_kantin.id, menu_kantin.nama_menu, menu_kantin.pemilik');
        $this->db->order_by('jumlah_transaksi', 'DESC');
        $this->db->limit($limit);

        return $this->db->get()->result();
    }

    /**
     * Rekap transaksi per santri hari ini (jumlah transaksi & total bayar), bisa difilter metode pembayaran
     */
    public function get_rekap_transaksi_hari_ini($metode = null, $kantin_id = null)
    {
        $this->db->select('
            santri.id as santri_id,
            santri.nama as nama_santri,
            santri.nomor_induk,
            santri.kelas,
            COUNT(transaksi_kantin.id) as jumlah_transaksi,
            SUM(transaksi_kantin.total_harga) as total_bayar
        ');
        $this->db->from('transaksi_kantin');
        $this->db->join('santri', 'santri.id = transaksi_kantin.santri_id');
        $this->db->where('DATE(transaksi_kantin.created_at)', date('Y-m-d'));
        $this->db->where('transaksi_kantin.status', 'selesai');
        if ($metode !== null) {
            $this->db->where('transaksi_kantin.metode_pembayaran', $metode);
        }
        if ($kantin_id !== null) {
            $this->db->where('transaksi_kantin.kantin_id', $kantin_id);
        }
        $this->db->group_by(['santri.id', 'santri.nama', 'santri.nomor_induk', 'santri.kelas']);
        $this->db->order_by('total_bayar', 'DESC');
        return $this->db->get()->result_array();
    }

    /**
     * ========================================
     * FUNGSI UNTUK TRANSAKSI USTADZ/USTADZAH
     * ========================================
     */

    /**
     * Create transaction for ustadz/ustadzah (cash only)
     */
    public function create_transaksi_ustadz($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('transaksi_ustadz', $data);
    }

    /**
     * Get all ustadz transactions
     */
    public function get_transaksi_ustadz($ustadz_id = NULL, $kantin_id = NULL)
    {
        $this->db->select('
            transaksi_ustadz.*,
            ustadz.nama as nama_ustadz,
            ustadz.nomor_telepon,
            menu_kantin.nama_menu,
            users.username as operator_nama
        ');
        $this->db->from('transaksi_ustadz');
        $this->db->join('ustadz', 'ustadz.id = transaksi_ustadz.ustadz_id');
        $this->db->join('menu_kantin', 'menu_kantin.id = transaksi_ustadz.menu_id');
        $this->db->join('users', 'users.id = transaksi_ustadz.operator_id');

        if ($ustadz_id !== NULL) {
            $this->db->where('transaksi_ustadz.ustadz_id', $ustadz_id);
        }

        if ($kantin_id !== NULL) {
            $this->db->where('menu_kantin.kantin_id', $kantin_id);
        }

        $this->db->order_by('transaksi_ustadz.created_at', 'DESC');

        if ($ustadz_id !== NULL) {
            return $this->db->get()->row();
        } else {
            return $this->db->get()->result();
        }
    }

    /**
     * Get today's ustadz transactions
     */
    public function get_transaksi_ustadz_hari_ini($kantin_id = NULL)
    {
        $this->db->select('
            transaksi_ustadz.*,
            ustadz.nama as nama_ustadz,
            menu_kantin.nama_menu,
            users.username as operator_nama
        ');
        $this->db->from('transaksi_ustadz');
        $this->db->join('ustadz', 'ustadz.id = transaksi_ustadz.ustadz_id');
        $this->db->join('menu_kantin', 'menu_kantin.id = transaksi_ustadz.menu_id');
        $this->db->join('users', 'users.id = transaksi_ustadz.operator_id');
        $this->db->where('DATE(transaksi_ustadz.created_at)', date('Y-m-d'));
        $this->db->where('transaksi_ustadz.status', 'selesai');

        if ($kantin_id !== NULL) {
            $this->db->where('menu_kantin.kantin_id', $kantin_id);
        }

        $this->db->order_by('transaksi_ustadz.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get ustadz transaction statistics
     */
    public function get_statistik_transaksi_ustadz($kantin_id = NULL)
    {
        $this->db->select('
            COUNT(*) as total_transaksi,
            SUM(total_harga) as total_pendapatan,
            COUNT(DISTINCT ustadz_id) as total_ustadz
        ');
        $this->db->from('transaksi_ustadz');
        $this->db->join('menu_kantin', 'menu_kantin.id = transaksi_ustadz.menu_id');
        $this->db->where('transaksi_ustadz.status', 'selesai');

        if ($kantin_id !== NULL) {
            $this->db->where('menu_kantin.kantin_id', $kantin_id);
        }

        return $this->db->get()->row();
    }

    /**
     * Get today's ustadz revenue
     */
    public function get_pendapatan_ustadz_hari_ini($kantin_id = NULL)
    {
        $this->db->select('SUM(transaksi_ustadz.total_harga) as total_pendapatan');
        $this->db->from('transaksi_ustadz');
        $this->db->join('menu_kantin', 'menu_kantin.id = transaksi_ustadz.menu_id');
        $this->db->where('DATE(transaksi_ustadz.created_at)', date('Y-m-d'));
        $this->db->where('transaksi_ustadz.status', 'selesai');

        if ($kantin_id !== NULL) {
            $this->db->where('menu_kantin.kantin_id', $kantin_id);
        }

        $result = $this->db->get()->row();
        return $result->total_pendapatan ?? 0;
    }

    /**
     * Count today's ustadz transactions
     */
    public function count_transaksi_ustadz_hari_ini($kantin_id = NULL)
    {
        $this->db->from('transaksi_ustadz');
        $this->db->join('menu_kantin', 'menu_kantin.id = transaksi_ustadz.menu_id');
        $this->db->where('DATE(transaksi_ustadz.created_at)', date('Y-m-d'));
        $this->db->where('transaksi_ustadz.status', 'selesai');

        if ($kantin_id !== NULL) {
            $this->db->where('menu_kantin.kantin_id', $kantin_id);
        }

        return $this->db->count_all_results();
    }

    public function get_transaksi_pos_modern_by_date($date, $kantin_id = NULL)
    {
        $where_kantin1 = $kantin_id ? "AND m.kantin_id = " . intval($kantin_id) : "";
        $where_kantin2 = $kantin_id ? "AND m2.kantin_id = " . intval($kantin_id) : "";
        $where_tgl1 = $date ? "AND DATE(t.created_at) = '" . $this->db->escape_str($date) . "'" : "";
        $where_tgl2 = $date ? "AND DATE(t2.created_at) = '" . $this->db->escape_str($date) . "'" : "";

        $sql = "
            SELECT
                CAST('santri' AS CHAR CHARACTER SET utf8mb4) AS jenis_pelanggan,
                t.id,
                t.santri_id AS pelanggan_id,
                CAST(s.nama AS CHAR CHARACTER SET utf8mb4) AS nama_pelanggan,
                t.menu_id,
                CAST(m.nama_menu AS CHAR CHARACTER SET utf8mb4) AS nama_menu,
                t.jumlah,
                t.harga_satuan,
                t.total_harga,
                CAST(t.metode_pembayaran AS CHAR CHARACTER SET utf8mb4) AS metode_pembayaran,
                CAST(t.status AS CHAR CHARACTER SET utf8mb4) AS status,
                t.admin_id AS operator_id,
                CAST(u.username AS CHAR CHARACTER SET utf8mb4) AS operator_nama,
                CAST(t.keterangan AS CHAR CHARACTER SET utf8mb4) AS keterangan,
                t.created_at,
                m.harga_jual,
                m.harga_beli
            FROM transaksi_kantin t
            JOIN santri s ON t.santri_id = s.id
            JOIN menu_kantin m ON t.menu_id = m.id
            JOIN users u ON t.admin_id = u.id
            WHERE 1=1 $where_kantin1 $where_tgl1

            UNION ALL

            SELECT
                CAST('ustadz' AS CHAR CHARACTER SET utf8mb4) AS jenis_pelanggan,
                t2.id,
                t2.ustadz_id AS pelanggan_id,
                CAST(s2.nama AS CHAR CHARACTER SET utf8mb4) AS nama_pelanggan,
                t2.menu_id,
                CAST(m2.nama_menu AS CHAR CHARACTER SET utf8mb4) AS nama_menu,
                t2.jumlah,
                t2.harga_satuan,
                t2.total_harga,
                CAST(t2.metode_pembayaran AS CHAR CHARACTER SET utf8mb4) AS metode_pembayaran,
                CAST(t2.status AS CHAR CHARACTER SET utf8mb4) AS status,
                t2.operator_id,
                CAST(u2.username AS CHAR CHARACTER SET utf8mb4) AS operator_nama,
                CAST(t2.keterangan AS CHAR CHARACTER SET utf8mb4) AS keterangan,
                t2.created_at,
                m2.harga_jual,
                m2.harga_beli
            FROM transaksi_ustadz t2
            JOIN ustadz s2 ON t2.ustadz_id = s2.id
            JOIN menu_kantin m2 ON t2.menu_id = m2.id
            JOIN users u2 ON t2.operator_id = u2.id
            WHERE 1=1 $where_kantin2 $where_tgl2

            ORDER BY created_at DESC
        ";
        $result = $this->db->query($sql);
        if ($result === false) {
            log_message('error', '[POS MODERN] SQL ERROR: ' . (method_exists($this->db, 'error') ? $this->db->error()['message'] : $this->db->_error_message()));
            log_message('error', '[POS MODERN] SQL: ' . $sql);
            return [];
        }
        return $result->result();
    }

    public function get_recent_all_transactions($limit = 10, $kantin_id = NULL)
    {
        $where_kantin1 = $kantin_id ? "AND mk.kantin_id = " . intval($kantin_id) : "";
        $where_kantin2 = $kantin_id ? "AND mk2.kantin_id = " . intval($kantin_id) : "";

        $sql = "
            SELECT
                tk.created_at,
                tk.jumlah,
                tk.total_harga,
                CAST(mk.nama_menu AS CHAR CHARACTER SET utf8mb4) as nama_menu,
                CAST(tk.metode_pembayaran AS CHAR CHARACTER SET utf8mb4) as metode_pembayaran,
                CAST(tk.keterangan AS CHAR CHARACTER SET utf8mb4) as keterangan,
                CAST(s.nama AS CHAR CHARACTER SET utf8mb4) as nama_pelanggan,
                CAST('santri' AS CHAR CHARACTER SET utf8mb4) as jenis,
                k.nama as nama_kantin,
                tk.status
            FROM transaksi_kantin tk
            JOIN santri s ON tk.santri_id = s.id
            JOIN menu_kantin mk ON tk.menu_id = mk.id
            JOIN kantin k ON mk.kantin_id = k.id
            WHERE 1=1 $where_kantin1

            UNION ALL

            SELECT
                tu.created_at,
                tu.jumlah,
                tu.total_harga,
                CAST(mk2.nama_menu AS CHAR CHARACTER SET utf8mb4) as nama_menu,
                CAST(tu.metode_pembayaran AS CHAR CHARACTER SET utf8mb4) as metode_pembayaran,
                CAST(tu.keterangan AS CHAR CHARACTER SET utf8mb4) as keterangan,
                CAST(u.nama AS CHAR CHARACTER SET utf8mb4) as nama_pelanggan,
                CAST('ustadz' AS CHAR CHARACTER SET utf8mb4) as jenis,
                k2.nama as nama_kantin,
                tu.status
            FROM transaksi_ustadz tu
            JOIN ustadz u ON tu.ustadz_id = u.id
            JOIN menu_kantin mk2 ON tu.menu_id = mk2.id
            JOIN kantin k2 ON mk2.kantin_id = k2.id
            WHERE 1=1 $where_kantin2

            ORDER BY created_at DESC
            LIMIT $limit
        ";
        $result = $this->db->query($sql);
        if ($result === false) {
            log_message('error', '[DASHBOARD] SQL ERROR: ' . (method_exists($this->db, 'error') ? $this->db->error()['message'] : $this->db->_error_message()));
            log_message('error', '[DASHBOARD] SQL: ' . $sql);
            return [];
        }
        return $result->result();
    }

    public function get_pendapatan_tunai_hari_ini($kantin_id = NULL)
    {
        // Santri (transaksi_kantin, metode_pembayaran = 'tunai')
        $this->db->select('SUM(total_harga) as total');
        $this->db->from('transaksi_kantin');
        $this->db->where('DATE(created_at)', date('Y-m-d'));
        $this->db->where('status', 'selesai');
        $this->db->where('metode_pembayaran', 'tunai');
        if ($kantin_id !== NULL) {
            $this->db->where('kantin_id', $kantin_id);
        }
        $result1 = $this->db->get()->row();
        $total1 = $result1->total ?? 0;

        // Ustadz (transaksi_ustadz, semua transaksi = tunai)/
        $this->db->select('SUM(total_harga) as total');
        $this->db->from('transaksi_ustadz');
        $this->db->join('menu_kantin', 'menu_kantin.id = transaksi_ustadz.menu_id');
        $this->db->where('DATE(transaksi_ustadz.created_at)', date('Y-m-d'));
        $this->db->where('transaksi_ustadz.status', 'selesai');
        if ($kantin_id !== NULL) {
            $this->db->where('menu_kantin.kantin_id', $kantin_id);
        }
        $result2 = $this->db->get()->row();
        $total2 = $result2->total ?? 0;

        return $total1 + $total2;
    }

    public function count_nota_hari_ini($kantin_id = NULL)
    {
        $this->db->select('created_at, santri_id, kantin_id');
        $this->db->from('transaksi_kantin');
        $this->db->where('DATE(created_at)', date('Y-m-d'));
        $this->db->where('status', 'selesai');
        if ($kantin_id !== NULL) {
            $this->db->where('kantin_id', $kantin_id);
        }
        $this->db->group_by(['created_at', 'santri_id', 'kantin_id']);
        return $this->db->get()->num_rows();
    }
    public function count_nota_ustadz_hari_ini($kantin_id = NULL)
    {
        $this->db->select('transaksi_ustadz.created_at, transaksi_ustadz.ustadz_id, transaksi_ustadz.menu_id');
        $this->db->from('transaksi_ustadz');
        $this->db->where('DATE(transaksi_ustadz.created_at)', date('Y-m-d'));
        $this->db->where('transaksi_ustadz.status', 'selesai');
        if ($kantin_id !== NULL) {
            $this->db->join('menu_kantin', 'menu_kantin.id = transaksi_ustadz.menu_id');
            $this->db->where('menu_kantin.kantin_id', $kantin_id);
        }
        $this->db->group_by(['transaksi_ustadz.created_at', 'transaksi_ustadz.ustadz_id', 'transaksi_ustadz.menu_id']);
        return $this->db->get()->num_rows();
    }
}
