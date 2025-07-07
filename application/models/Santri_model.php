<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Santri_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get_santri($id = NULL, $kantin_id = NULL)
    {
        if ($id !== NULL) {
            $this->db->select('santri.*, users.username');
            $this->db->from('santri');
            $this->db->join('users', 'users.id = santri.user_id', 'left');
            $this->db->where('santri.id', $id);

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

        $this->db->select('santri.*, users.username');
        $this->db->from('santri');
        $this->db->join('users', 'users.id = santri.user_id', 'left');

        // Filter berdasarkan kantin (kecuali untuk admin)
        if ($kantin_id !== NULL) {
            if ($kantin_id == 1) { // Kantin Putra
                $this->db->where('santri.jenis_kelamin', 'L');
            } elseif ($kantin_id == 2) { // Kantin Putri
                $this->db->where('santri.jenis_kelamin', 'P');
            }
        }

        return $this->db->get()->result();
    }

    public function get_santri_by_nomor_induk($nomor_induk, $kantin_id = NULL)
    {
        $this->db->where('nomor_induk', $nomor_induk);

        // Filter berdasarkan kantin
        if ($kantin_id !== NULL) {
            if ($kantin_id == 1) { // Kantin Putra
                $this->db->where('jenis_kelamin', 'L');
            } elseif ($kantin_id == 2) { // Kantin Putri
                $this->db->where('jenis_kelamin', 'P');
            }
        }

        return $this->db->get('santri')->row();
    }

    public function create_santri($data)
    {
        $this->db->insert('santri', $data);
        return $this->db->insert_id();
    }

    public function update_santri($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('santri', $data);
    }

    public function delete_santri($id)
    {
        return $this->db->delete('santri', ['id' => $id]);
    }

    public function get_santri_with_tabungan($id = NULL, $kantin_id = NULL)
    {
        $this->db->select('santri.*, tabungan.saldo_tabungan, tabungan.saldo_jajan, users.username');
        $this->db->from('santri');
        $this->db->join('tabungan', 'tabungan.santri_id = santri.id', 'left');
        $this->db->join('users', 'users.id = santri.user_id', 'left');

        if ($id !== NULL) {
            $this->db->where('santri.id', $id);
        }

        // Filter berdasarkan kantin (kecuali untuk admin)
        if ($kantin_id !== NULL) {
            if ($kantin_id == 1) { // Kantin Putra
                $this->db->where('santri.jenis_kelamin', 'L');
            } elseif ($kantin_id == 2) { // Kantin Putri
                $this->db->where('santri.jenis_kelamin', 'P');
            }
        }

        $this->db->order_by('santri.nama', 'ASC');

        if ($id !== NULL) {
            return $this->db->get()->row();
        } else {
            return $this->db->get()->result();
        }
    }

    public function get_tabungan_santri($santri_id)
    {
        $this->db->select('transaksi.*, users.username as admin_username');
        $this->db->from('transaksi');
        $this->db->join('users', 'users.id = transaksi.admin_id', 'left');
        $this->db->where('transaksi.santri_id', $santri_id);
        $this->db->order_by('transaksi.created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function get_santri_without_user($kantin_id = NULL)
    {
        $this->db->select('santri.*');
        $this->db->from('santri');
        $this->db->where('santri.user_id IS NULL');

        // Filter berdasarkan kantin (kecuali untuk admin)
        if ($kantin_id !== NULL) {
            if ($kantin_id == 1) { // Kantin Putra
                $this->db->where('santri.jenis_kelamin', 'L');
            } elseif ($kantin_id == 2) { // Kantin Putri
                $this->db->where('santri.jenis_kelamin', 'P');
            }
        }

        $this->db->order_by('santri.nama', 'ASC');
        return $this->db->get()->result();
    }

    public function get_all_santri($kantin_id = NULL)
    {
        return $this->get_santri(NULL, $kantin_id);
    }

    public function get_all($kantin_id = NULL)
    {
        $this->db->select('santri.id, santri.nomor_induk, santri.nama, tabungan.saldo_tabungan, tabungan.saldo_jajan');
        $this->db->from('santri');
        $this->db->join('tabungan', 'tabungan.santri_id = santri.id', 'left');
        if ($kantin_id !== NULL) {
            if ($kantin_id == 1) {
                $this->db->where('santri.jenis_kelamin', 'L');
            } elseif ($kantin_id == 2) {
                $this->db->where('santri.jenis_kelamin', 'P');
            }
        }
        return $this->db->get()->result();
    }

    public function create_wali_santri($data)
    {
        return $this->db->insert('wali_santri', $data);
    }

    public function get_wali_santri($santri_id)
    {
        return $this->db->get_where('wali_santri', ['santri_id' => $santri_id])->row();
    }

    public function update_wali_santri($santri_id, $data)
    {
        $this->db->where('santri_id', $santri_id);
        return $this->db->update('wali_santri', $data);
    }

    public function search_santri($keyword, $kantin_id = NULL)
    {
        $this->db->select('santri.*, tabungan.saldo_tabungan, tabungan.saldo_jajan');
        $this->db->from('santri');
        $this->db->join('tabungan', 'tabungan.santri_id = santri.id', 'left');
        $this->db->group_start();
        $this->db->like('santri.nama', $keyword);
        $this->db->or_like('santri.nomor_induk', $keyword);
        $this->db->group_end();

        // Filter berdasarkan kantin (kecuali untuk admin)
        if ($kantin_id !== NULL) {
            if ($kantin_id == 1) { // Kantin Putra
                $this->db->where('santri.jenis_kelamin', 'L');
            } elseif ($kantin_id == 2) { // Kantin Putri
                $this->db->where('santri.jenis_kelamin', 'P');
            }
        }

        $this->db->order_by('santri.nama', 'ASC');
        $this->db->limit(10);
        return $this->db->get()->result();
    }

    public function get_pengeluaran_hari_ini($santri_id)
    {
        $this->db->select_sum('jumlah');
        $this->db->where('santri_id', $santri_id);
        $this->db->where('jenis', 'penarikan');
        $this->db->where('kategori', 'jajan');
        $this->db->where('DATE(created_at)', date('Y-m-d'));
        $result = $this->db->get('transaksi')->row();
        return $result->jumlah ?? 0;
    }

    public function get_pengeluaran_pos_hari_ini($santri_id)
    {
        $this->db->select_sum('jumlah');
        $this->db->where('santri_id', $santri_id);
        $this->db->where('jenis', 'penarikan');
        $this->db->where('kategori', 'jajan');
        $this->db->where('DATE(created_at)', date('Y-m-d'));
        $this->db->like('keterangan', 'POS (Saldo Jajan)');
        $result = $this->db->get('transaksi')->row();
        return $result->jumlah ?? 0;
    }

    public function count_santri($kantin_id = NULL)
    {
        if ($kantin_id !== NULL) {
            // Filter berdasarkan jenis kelamin sesuai kantin
            if ($kantin_id == 1) { // Kantin Putra
                $this->db->where('jenis_kelamin', 'L');
            } elseif ($kantin_id == 2) { // Kantin Putri
                $this->db->where('jenis_kelamin', 'P');
            }
        }
        return $this->db->count_all_results('santri');
    }

    public function get_santri_by_id($id, $kantin_id = NULL)
    {
        $this->db->where('id', $id);

        if ($kantin_id !== NULL) {
            // Filter berdasarkan jenis kelamin sesuai kantin
            if ($kantin_id == 1) { // Kantin Putra
                $this->db->where('jenis_kelamin', 'L');
            } elseif ($kantin_id == 2) { // Kantin Putri
                $this->db->where('jenis_kelamin', 'P');
            }
        }

        return $this->db->get('santri')->row();
    }

    public function get_santri_with_wali($id = NULL, $kantin_id = NULL)
    {
        $this->db->select('santri.*, wali_santri.nama as nama_wali, wali_santri.kontak as kontak_wali, wali_santri.hubungan as hubungan_wali');
        $this->db->from('santri');
        $this->db->join('wali_santri', 'wali_santri.santri_id = santri.id', 'left');

        if ($id !== NULL) {
            $this->db->where('santri.id', $id);
        }

        if ($kantin_id !== NULL) {
            // Filter berdasarkan jenis kelamin sesuai kantin
            if ($kantin_id == 1) { // Kantin Putra
                $this->db->where('santri.jenis_kelamin', 'L');
            } elseif ($kantin_id == 2) { // Kantin Putri
                $this->db->where('santri.jenis_kelamin', 'P');
            }
        }

        $this->db->order_by('santri.nama', 'ASC');

        if ($id !== NULL) {
            return $this->db->get()->row();
        } else {
            return $this->db->get()->result();
        }
    }

    public function get_santri_with_tabungan_and_wali($id = NULL, $kantin_id = NULL)
    {
        $this->db->select('santri.*, tabungan.saldo_tabungan, tabungan.saldo_jajan, users.username, wali_santri.nama as nama_wali, wali_santri.kontak as kontak_wali, wali_santri.hubungan as hubungan_wali');
        $this->db->from('santri');
        $this->db->join('tabungan', 'tabungan.santri_id = santri.id', 'left');
        $this->db->join('users', 'users.id = santri.user_id', 'left');
        $this->db->join('wali_santri', 'wali_santri.santri_id = santri.id', 'left');

        if ($id !== NULL) {
            $this->db->where('santri.id', $id);
        }

        if ($kantin_id !== NULL) {
            // Filter berdasarkan jenis kelamin sesuai kantin
            if ($kantin_id == 1) { // Kantin Putra
                $this->db->where('santri.jenis_kelamin', 'L');
            } elseif ($kantin_id == 2) { // Kantin Putri
                $this->db->where('santri.jenis_kelamin', 'P');
            }
        }

        $this->db->order_by('santri.nama', 'ASC');

        if ($id !== NULL) {
            return $this->db->get()->row();
        } else {
            return $this->db->get()->result();
        }
    }

    public function count_all()
    {
        return $this->db->count_all('santri');
    }

    public function check_nomor_induk_exists($nomor_induk)
    {
        $this->db->where('nomor_induk', $nomor_induk);
        return $this->db->get('santri')->num_rows() > 0;
    }

    public function count_active()
    {
        // Semua santri dianggap aktif karena tidak ada kolom is_active
        return $this->db->count_all('santri');
    }

    public function count_by_gender($jenis_kelamin)
    {
        $this->db->where('jenis_kelamin', $jenis_kelamin);
        return $this->db->count_all_results('santri');
    }

    public function get_santri_by_user_id($user_id)
    {
        return $this->db->get_where('santri', ['user_id' => $user_id])->row();
    }
}
