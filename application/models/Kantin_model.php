<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kantin_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all_kantin()
    {
        return $this->db->get('kantin')->result();
    }

    public function get_kantin($id)
    {
        return $this->db->get_where('kantin', ['id' => $id])->row();
    }

    public function get_kantin_by_jenis($jenis)
    {
        return $this->db->get_where('kantin', ['jenis' => $jenis, 'status' => 'aktif'])->row();
    }

    public function get_kantin_aktif()
    {
        return $this->db->get_where('kantin', ['status' => 'aktif'])->result();
    }

    public function create_kantin($data)
    {
        return $this->db->insert('kantin', $data);
    }

    public function update_kantin($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('kantin', $data);
    }

    public function delete_kantin($id)
    {
        return $this->db->delete('kantin', ['id' => $id]);
    }

    public function get_kantin_with_stats($kantin_id = null)
    {
        $this->db->select('kantin.*, 
            COUNT(DISTINCT menu_kantin.id) as total_menu,
            SUM(transaksi_kantin.total_harga) as total_pendapatan');
        $this->db->from('kantin');
        $this->db->join('menu_kantin', 'menu_kantin.kantin_id = kantin.id', 'left');
        $this->db->join('transaksi_kantin', 'transaksi_kantin.kantin_id = kantin.id', 'left');
        if ($kantin_id !== null) {
            $this->db->where('kantin.id', $kantin_id);
        }
        $this->db->group_by('kantin.id');
        $this->db->order_by('kantin.nama', 'ASC');
        $result = $this->db->get()->result();

        // Tambahkan total_transaksi (jumlah nota/struk) manual
        foreach ($result as &$kantin) {
            $kantin->total_transaksi = $this->count_nota_by_kantin($kantin->id);
        }
        return $result;
    }

    public function count_nota_by_kantin($kantin_id)
    {
        $this->db->select('created_at, santri_id, kantin_id');
        $this->db->from('transaksi_kantin');
        $this->db->where('kantin_id', $kantin_id);
        $this->db->where('status', 'selesai');
        $this->db->group_by(['created_at', 'santri_id', 'kantin_id']);
        return $this->db->get()->num_rows();
    }

    public function get_kantin_by_user($user_id)
    {
        $this->db->select('kantin.*');
        $this->db->from('kantin');
        $this->db->join('santri', 'santri.user_id = users.id', 'left');
        $this->db->join('users', 'users.id = ' . $user_id);

        $this->db->where('santri.jenis_kelamin = "L" AND kantin.jenis = "putra" OR santri.jenis_kelamin = "P" AND kantin.jenis = "putri"');

        return $this->db->get()->row();
    }

    public function get_santri_by_kantin($kantin_id)
    {
        $this->db->select('santri.*, tabungan.saldo_jajan');
        $this->db->from('santri');
        $this->db->join('tabungan', 'tabungan.santri_id = santri.id', 'left');

        if ($kantin_id == 1) { // Kantin Putra
            $this->db->where('santri.jenis_kelamin', 'L');
        } elseif ($kantin_id == 2) { // Kantin Putri
            $this->db->where('santri.jenis_kelamin', 'P');
        }

        return $this->db->get()->result();
    }

    public function get_menu_by_kantin($kantin_id)
    {
        return $this->db->get_where('menu_kantin', ['kantin_id' => $kantin_id])->result();
    }

    public function get_transaksi_by_kantin($kantin_id, $limit = null)
    {
        $this->db->select('transaksi_kantin.*, santri.nama as nama_santri, menu_kantin.nama_menu');
        $this->db->from('transaksi_kantin');
        $this->db->join('santri', 'santri.id = transaksi_kantin.santri_id');
        $this->db->join('menu_kantin', 'menu_kantin.id = transaksi_kantin.menu_id');
        $this->db->where('transaksi_kantin.kantin_id', $kantin_id);
        $this->db->order_by('transaksi_kantin.created_at', 'DESC');

        if ($limit !== null) {
            $this->db->limit($limit);
        }

        return $this->db->get()->result();
    }

    public function count_all()
    {
        return $this->db->count_all('kantin');
    }
}
