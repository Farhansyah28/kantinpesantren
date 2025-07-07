<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tabungan_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get_tabungan($santri_id)
    {
        return $this->db->get_where('tabungan', ['santri_id' => $santri_id])->row();
    }

    public function update_saldo($santri_id, $data)
    {
        // Validasi input
        if (!is_array($data) || !isset($data['saldo_tabungan']) || !isset($data['saldo_jajan'])) {
            return false;
        }

        // Cek apakah santri exists
        $santri_exists = $this->db->get_where('santri', ['id' => $santri_id])->num_rows();
        if (!$santri_exists) {
            return false;
        }

        // Cek apakah record tabungan exists
        $tabungan_exists = $this->db->get_where('tabungan', ['santri_id' => $santri_id])->num_rows();

        if ($tabungan_exists) {
            // Update existing record
            $this->db->where('santri_id', $santri_id);
            return $this->db->update('tabungan', $data);
        } else {
            // Insert new record
            $data['santri_id'] = $santri_id;
            return $this->db->insert('tabungan', $data);
        }
    }

    public function get_riwayat_transaksi($santri_id = NULL, $limit = NULL, $kantin_id = NULL, $tanggal_awal = NULL, $tanggal_akhir = NULL)
    {
        $this->db->select('transaksi.*, 
            users.username as admin_username,
            santri.nama as nama_santri,
            santri.nomor_induk,
            santri.kelas');
        $this->db->from('transaksi');
        $this->db->join('users', 'users.id = transaksi.admin_id', 'left');
        $this->db->join('santri', 'santri.id = transaksi.santri_id', 'left');

        if ($santri_id !== NULL) {
            $this->db->where('transaksi.santri_id', $santri_id);
        }

        // Filter berdasarkan kantin (kecuali untuk admin)
        if ($kantin_id !== NULL) {
            if ($kantin_id == 1) { // Kantin Putra
                $this->db->where('santri.jenis_kelamin', 'L');
            } elseif ($kantin_id == 2) { // Kantin Putri
                $this->db->where('santri.jenis_kelamin', 'P');
            }
        }
        // Filter tanggal
        if ($tanggal_awal !== NULL) {
            $this->db->where('DATE(transaksi.created_at) >=', $tanggal_awal);
        }
        if ($tanggal_akhir !== NULL) {
            $this->db->where('DATE(transaksi.created_at) <=', $tanggal_akhir);
        }
        $this->db->order_by('transaksi.created_at', 'DESC');
        if ($limit !== NULL) {
            $this->db->limit($limit);
        }
        return $this->db->get()->result();
    }

    public function transfer_kategori($santri_id, $jumlah, $dari_kategori, $ke_kategori)
    {
        $this->db->trans_start();

        $tabungan = $this->get_tabungan($santri_id);

        if ($dari_kategori == 'tabungan') {
            $saldo_tabungan_baru = $tabungan->saldo_tabungan - $jumlah;
            $saldo_jajan_baru = $tabungan->saldo_jajan + $jumlah;
        } else {
            $saldo_tabungan_baru = $tabungan->saldo_tabungan + $jumlah;
            $saldo_jajan_baru = $tabungan->saldo_jajan - $jumlah;
        }

        $this->db->where('santri_id', $santri_id);
        $this->db->update('tabungan', [
            'saldo_tabungan' => $saldo_tabungan_baru,
            'saldo_jajan' => $saldo_jajan_baru
        ]);

        // Catat transaksi
        $transaksi = [
            'santri_id' => $santri_id,
            'admin_id' => $this->session->userdata('user_id'),
            'jenis' => 'setoran',
            'kategori' => $ke_kategori,
            'jumlah' => $jumlah,
            'keterangan' => "Transfer dari {$dari_kategori} ke {$ke_kategori}",
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert('transaksi', $transaksi);

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function get_saldo($santri_id)
    {
        $row = $this->db->get_where('tabungan', ['santri_id' => $santri_id])->row();
        return [
            'saldo_tabungan' => $row ? $row->saldo_tabungan : 0,
            'saldo_jajan' => $row ? $row->saldo_jajan : 0
        ];
    }

    public function get_saldo_jajan($santri_id)
    {
        $row = $this->db->get_where('tabungan', ['santri_id' => $santri_id])->row();
        return $row ? $row->saldo_jajan : 0;
    }

    public function record_transaksi($data)
    {
        // Validasi input
        $required_fields = ['santri_id', 'jenis', 'kategori', 'jumlah', 'admin_id'];
        foreach ($required_fields as $field) {
            if (!isset($data[$field])) {
                return false;
            }
        }

        // Validasi jenis dan kategori
        if (!in_array($data['jenis'], ['setoran', 'penarikan', 'transfer'])) {
            return false;
        }
        if (!in_array($data['kategori'], ['tabungan', 'jajan'])) {
            return false;
        }

        // Validasi jumlah
        if (!is_numeric($data['jumlah']) || $data['jumlah'] <= 0) {
            return false;
        }

        // Set created_at
        $data['created_at'] = date('Y-m-d H:i:s');

        return $this->db->insert('transaksi', $data);
    }

    public function update_saldo_jajan($santri_id, $new_saldo)
    {
        $this->db->where('santri_id', $santri_id);
        return $this->db->update('tabungan', ['saldo_jajan' => $new_saldo]);
    }

    public function kurangi_saldo_jajan($santri_id, $jumlah)
    {
        // Cek saldo saat ini
        $tabungan = $this->get_tabungan($santri_id);
        if (!$tabungan) {
            return false;
        }

        // Cek apakah saldo mencukupi
        if ($tabungan->saldo_jajan < $jumlah) {
            return false;
        }

        // Kurangi saldo jajan
        $saldo_baru = $tabungan->saldo_jajan - $jumlah;
        $this->db->where('santri_id', $santri_id);
        return $this->db->update('tabungan', ['saldo_jajan' => $saldo_baru]);
    }

    public function get_tabungan_by_santri($santri_id)
    {
        return $this->db->get_where('tabungan', ['santri_id' => $santri_id])->row();
    }

    public function add_transaksi($santri_id, $admin_id, $jenis, $jumlah, $keterangan, $kategori)
    {
        $data = [
            'santri_id' => $santri_id,
            'admin_id' => $admin_id,
            'jenis' => $jenis,
            'kategori' => $kategori,
            'jumlah' => $jumlah,
            'keterangan' => $keterangan,
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->db->insert('transaksi', $data);
    }

    public function get_tabungan_by_kantin($kantin_id)
    {
        $this->db->select('tabungan.*, santri.nama, santri.nomor_induk, santri.kelas, santri.jenis_kelamin');
        $this->db->from('tabungan');
        $this->db->join('santri', 'santri.id = tabungan.santri_id');

        // Filter berdasarkan kantin (kecuali untuk admin)
        if ($kantin_id == 1) { // Kantin Putra
            $this->db->where('santri.jenis_kelamin', 'L');
        } elseif ($kantin_id == 2) { // Kantin Putri
            $this->db->where('santri.jenis_kelamin', 'P');
        }

        $this->db->order_by('santri.nama', 'ASC');
        return $this->db->get()->result();
    }

    public function get_summary_tabungan($kantin_id = NULL)
    {
        $this->db->select('COUNT(*) as total_santri, SUM(saldo_tabungan) as total_tabungan, SUM(saldo_jajan) as total_jajan');
        $this->db->from('tabungan');
        $this->db->join('santri', 'santri.id = tabungan.santri_id');

        // Filter berdasarkan kantin (kecuali untuk admin)
        if ($kantin_id !== NULL) {
            if ($kantin_id == 1) { // Kantin Putra
                $this->db->where('santri.jenis_kelamin', 'L');
            } elseif ($kantin_id == 2) { // Kantin Putri
                $this->db->where('santri.jenis_kelamin', 'P');
            }
        }

        return $this->db->get()->row();
    }

    /**
     * Get total jajan balance for all santri or filtered by kantin_id
     */
    public function get_total_saldo($kantin_id = NULL)
    {
        $this->db->select('SUM(saldo_jajan) as total_saldo');
        $this->db->from('tabungan');
        $this->db->join('santri', 'santri.id = tabungan.santri_id');

        // Filter berdasarkan kantin (kecuali untuk admin)
        if ($kantin_id !== NULL) {
            if ($kantin_id == 1) { // Kantin Putra
                $this->db->where('santri.jenis_kelamin', 'L');
            } elseif ($kantin_id == 2) { // Kantin Putri
                $this->db->where('santri.jenis_kelamin', 'P');
            }
        }

        $result = $this->db->get()->row();
        return $result->total_saldo ?? 0;
    }

    public function get_total_saldo_jajan_semua_santri()
    {
        $this->db->select_sum('saldo_jajan');
        $result = $this->db->get('tabungan')->row();
        return $result->saldo_jajan ?? 0;
    }

    public function transfer_antar_santri($santri_pengirim_id, $santri_penerima_id, $jumlah, $kategori, $keterangan = '')
    {
        $this->db->trans_start();

        // Ambil data tabungan santri pengirim
        $tabungan_pengirim = $this->get_tabungan($santri_pengirim_id);
        if (!$tabungan_pengirim) {
            return false;
        }

        // Ambil data tabungan santri penerima
        $tabungan_penerima = $this->get_tabungan($santri_penerima_id);
        if (!$tabungan_penerima) {
            return false;
        }

        // Cek saldo santri pengirim
        $saldo_pengirim = $kategori == 'tabungan' ? $tabungan_pengirim->saldo_tabungan : $tabungan_pengirim->saldo_jajan;
        if ($saldo_pengirim < $jumlah) {
            return false;
        }

        // Update saldo santri pengirim (kurangi)
        $saldo_pengirim_baru = $kategori == 'tabungan' ?
            $tabungan_pengirim->saldo_tabungan - $jumlah :
            $tabungan_pengirim->saldo_jajan - $jumlah;

        $this->db->where('santri_id', $santri_pengirim_id);
        $this->db->update('tabungan', [
            'saldo_tabungan' => $kategori == 'tabungan' ? $saldo_pengirim_baru : $tabungan_pengirim->saldo_tabungan,
            'saldo_jajan' => $kategori == 'jajan' ? $saldo_pengirim_baru : $tabungan_pengirim->saldo_jajan
        ]);

        // Update saldo santri penerima (tambah)
        $saldo_penerima_baru = $kategori == 'tabungan' ?
            $tabungan_penerima->saldo_tabungan + $jumlah :
            $tabungan_penerima->saldo_jajan + $jumlah;

        $this->db->where('santri_id', $santri_penerima_id);
        $this->db->update('tabungan', [
            'saldo_tabungan' => $kategori == 'tabungan' ? $saldo_penerima_baru : $tabungan_penerima->saldo_tabungan,
            'saldo_jajan' => $kategori == 'jajan' ? $saldo_penerima_baru : $tabungan_penerima->saldo_jajan
        ]);

        // Catat transaksi untuk santri pengirim
        $transaksi_pengirim = [
            'santri_id' => $santri_pengirim_id,
            'admin_id' => $this->session->userdata('user_id'),
            'jenis' => 'transfer',
            'kategori' => $kategori,
            'jumlah' => $jumlah,
            'keterangan' => "Transfer ke " . $this->get_nama_santri($santri_penerima_id) . " - " . $keterangan,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('transaksi', $transaksi_pengirim);

        // Catat transaksi untuk santri penerima
        $transaksi_penerima = [
            'santri_id' => $santri_penerima_id,
            'admin_id' => $this->session->userdata('user_id'),
            'jenis' => 'setoran',
            'kategori' => $kategori,
            'jumlah' => $jumlah,
            'keterangan' => "Transfer dari " . $this->get_nama_santri($santri_pengirim_id) . " - " . $keterangan,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('transaksi', $transaksi_penerima);

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    private function get_nama_santri($santri_id)
    {
        $santri = $this->db->get_where('santri', ['id' => $santri_id])->row();
        return $santri ? $santri->nama : 'Santri Tidak Diketahui';
    }

    public function get_total_saldo_wajib_semua_santri()
    {
        // Method ini dihapus karena kolom saldo_wajib tidak ada di tabel
        return 0;
    }

    public function get_saldo_per_kantin()
    {
        $result = [];

        // Ambil data kantin
        $kantins = $this->db->get('kantin')->result();

        foreach ($kantins as $kantin) {
            $jenis_kelamin = ($kantin->jenis == 'putra') ? 'L' : 'P';

            // Hitung total santri per kantin
            $this->db->where('jenis_kelamin', $jenis_kelamin);
            $total_santri = $this->db->count_all_results('santri');

            // Hitung total saldo per kantin
            $this->db->select('SUM(t.saldo_jajan) as total_saldo_jajan, SUM(t.saldo_tabungan) as total_saldo_tabungan');
            $this->db->from('santri s');
            $this->db->join('tabungan t', 't.santri_id = s.id', 'left');
            $this->db->where('s.jenis_kelamin', $jenis_kelamin);
            $saldo = $this->db->get()->row();

            $result[] = [
                'nama_kantin' => $kantin->nama,
                'jenis' => $kantin->jenis,
                'total_santri' => $total_santri,
                'total_saldo_jajan' => $saldo->total_saldo_jajan ?? 0,
                'total_saldo_tabungan' => $saldo->total_saldo_tabungan ?? 0,
                'total_saldo' => ($saldo->total_saldo_jajan ?? 0) + ($saldo->total_saldo_tabungan ?? 0)
            ];
        }

        return $result;
    }

    public function get_santri_with_saldo_compact()
    {
        $this->db->select('
            s.nomor_induk,
            s.nama,
            s.kelas,
            s.jenis_kelamin,
            t.saldo_jajan,
            t.saldo_tabungan,
            (t.saldo_jajan + t.saldo_tabungan) as total_saldo
        ');
        $this->db->from('santri s');
        $this->db->join('tabungan t', 't.santri_id = s.id', 'left');
        $this->db->order_by('s.nama');
        $this->db->limit(50); // Batasi untuk tampilan compact
        return $this->db->get()->result_array();
    }
}
