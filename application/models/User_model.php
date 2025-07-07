<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends CI_Model
{
    private $table = 'users';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_all($kantin_id = NULL)
    {
        return $this->db->get($this->table)->result();
    }

    public function get_by_id($id, $kantin_id = NULL)
    {
        $this->db->where('id', $id);
        return $this->db->get($this->table)->row();
    }

    public function get_user($id)
    {
        return $this->db->get_where('users', ['id' => $id])->row();
    }

    public function insert($data)
    {
        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data)
    {
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function delete($id)
    {
        return $this->db->delete($this->table, ['id' => $id]);
    }

    public function is_username_unique($username, $exclude_id = null)
    {
        $this->db->where('username', $username);
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        return $this->db->get($this->table)->num_rows() === 0;
    }

    public function get_user_by_username($username)
    {
        return $this->db->get_where('users', ['username' => $username])->row();
    }

    public function create_user($data)
    {
        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        // Tambahkan created_at jika belum ada
        if (!isset($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }

        return $this->db->insert($this->table, $data);
    }

    public function update_user($id, $data)
    {
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }

        // Tambahkan updated_at
        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function delete_user($id)
    {
        return $this->db->delete($this->table, ['id' => $id]);
    }

    public function update_last_login($id)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, ['terakhir_login' => date('Y-m-d H:i:s')]);
    }

    public function get_users_with_santri($kantin_id = NULL)
    {
        $this->db->select('users.*, santri.nama as nama_santri, santri.nomor_induk, santri.kelas');
        $this->db->from('users');
        $this->db->join('santri', 'santri.user_id = users.id', 'left');

        // Filter berdasarkan kantin (kecuali untuk admin)
        if ($kantin_id !== NULL) {
            if ($kantin_id == 1) { // Kantin Putra
                $this->db->where('santri.jenis_kelamin', 'L');
            } elseif ($kantin_id == 2) { // Kantin Putri
                $this->db->where('santri.jenis_kelamin', 'P');
            }
        }

        // Cek apakah field created_at ada, jika tidak gunakan id sebagai pengganti
        $this->db->order_by('users.id', 'DESC');
        return $this->db->get()->result();
    }

    public function get_users_by_kantin($kantin_id)
    {
        // Method ini tidak bisa menggunakan kantin_id langsung dari users
        // Gunakan filter berdasarkan jenis kelamin santri yang terkait
        $this->db->select('users.*, santri.nama as nama_santri, santri.nomor_induk, santri.kelas');
        $this->db->from('users');
        $this->db->join('santri', 'santri.user_id = users.id', 'left');

        if ($kantin_id == 1) { // Kantin Putra
            $this->db->where('santri.jenis_kelamin', 'L');
        } elseif ($kantin_id == 2) { // Kantin Putri
            $this->db->where('santri.jenis_kelamin', 'P');
        }

        return $this->db->get()->result();
    }

    public function get_admin_by_kantin($kantin_id)
    {
        // Method ini tidak bisa menggunakan kantin_id langsung dari users
        // Gunakan filter berdasarkan jenis kelamin santri yang terkait
        $this->db->select('users.*, santri.nama as nama_santri, santri.nomor_induk, santri.kelas');
        $this->db->from('users');
        $this->db->join('santri', 'santri.user_id = users.id', 'left');
        $this->db->where('users.role', 'admin');

        if ($kantin_id == 1) { // Kantin Putra
            $this->db->where('santri.jenis_kelamin', 'L');
        } elseif ($kantin_id == 2) { // Kantin Putri
            $this->db->where('santri.jenis_kelamin', 'P');
        }

        return $this->db->get()->result();
    }

    public function get_user_by_username_and_kantin($username, $kantin_id)
    {
        // Method ini tidak bisa menggunakan kantin_id langsung dari users
        // Gunakan filter berdasarkan jenis kelamin santri yang terkait
        $this->db->select('users.*, santri.nama as nama_santri, santri.nomor_induk, santri.kelas');
        $this->db->from('users');
        $this->db->join('santri', 'santri.user_id = users.id', 'left');
        $this->db->where('users.username', $username);

        if ($kantin_id == 1) { // Kantin Putra
            $this->db->where('santri.jenis_kelamin', 'L');
        } elseif ($kantin_id == 2) { // Kantin Putri
            $this->db->where('santri.jenis_kelamin', 'P');
        }

        return $this->db->get()->row();
    }

    public function count_all()
    {
        return $this->db->count_all('users');
    }
}
