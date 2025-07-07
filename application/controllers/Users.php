<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Users extends CI_Controller
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
        $this->load->model(['User_model', 'Kantin_model', 'Santri_model', 'Activity_log_model']);
        $this->load->library('form_validation');

        // Cek login dan role admin
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
            redirect('auth/login');
        }
    }

    public function index()
    {
        $kantin_id = $this->session->userdata('kantin_id');

        // Jika admin tidak memiliki kantin_id, tampilkan semua users
        if ($this->session->userdata('role') == 'admin' && $kantin_id === null) {
            $data['title'] = 'Manajemen User - Admin';
            $data['kantin_info'] = null;
            $data['users'] = $this->User_model->get_all();
        } else {
            $data['title'] = 'Manajemen User - ' . $this->session->userdata('kantin_nama');
            $data['kantin_info'] = $this->Kantin_model->get_kantin($kantin_id);

            // Tampilkan users sesuai kantin (kecuali admin)
            $data['users'] = $this->User_model->get_users_with_santri($kantin_id);
        }

        $this->load->view('templates/header', $data);
        $this->load->view('users/index', $data);
        $this->load->view('templates/footer');
    }

    public function create()
    {
        $kantin_id = $this->session->userdata('kantin_id');

        // Jika admin, tidak perlu kantin_id
        if ($this->session->userdata('role') == 'admin' && $kantin_id === null) {
            $data['title'] = 'Tambah User - Admin';
            $data['kantin_info'] = null;
            $data['santri_list'] = $this->Santri_model->get_santri_without_user();
        } else {
            $data['title'] = 'Tambah User - ' . $this->session->userdata('kantin_nama');
            $data['kantin_info'] = $this->Kantin_model->get_kantin($kantin_id);
            $data['santri_list'] = $this->Santri_model->get_santri_without_user($kantin_id);
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('username', 'Username', 'required|is_unique[users.username]');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[4]');
            $this->form_validation->set_rules('confirm_password', 'Konfirmasi Password', 'required|matches[password]');
            $this->form_validation->set_rules('role', 'Role', 'required|in_list[admin,operator,keuangan,santri]');

            // Gender hanya wajib untuk role selain admin dan keuangan
            if ($this->input->post('role') !== 'admin' && $this->input->post('role') !== 'keuangan') {
                $this->form_validation->set_rules('gender', 'Gender', 'required|in_list[L,P]');
            }

            if ($this->form_validation->run() === TRUE) {
                $insert = [
                    'username' => $this->input->post('username'),
                    'password' => $this->input->post('password'),
                    'role' => $this->input->post('role'),
                    'created_at' => date('Y-m-d H:i:s')
                ];

                // Gender hanya untuk role selain admin dan keuangan
                if ($this->input->post('role') !== 'admin' && $this->input->post('role') !== 'keuangan') {
                    $insert['gender'] = $this->input->post('gender');
                }

                // Jika role santri, tambahkan santri_id
                if ($this->input->post('role') == 'santri' && $this->input->post('santri_id')) {
                    $insert['santri_id'] = $this->input->post('santri_id');
                }

                if ($this->User_model->create_user($insert)) {
                    // Log sukses penambahan user
                    $this->Activity_log_model->log_system('USER_CREATE_SUCCESS', [
                        'user_id' => $this->db->insert_id(),
                        'username' => $this->input->post('username'),
                        'role' => $this->input->post('role'),
                        'gender' => $this->input->post('gender') ?? null,
                        'santri_id' => $this->input->post('santri_id') ?? null
                    ], 'success');

                    $this->session->set_flashdata('success', 'User berhasil ditambahkan.');
                    redirect('users');
                } else {
                    // Log error penambahan user
                    $this->Activity_log_model->log_system('USER_CREATE_FAILED', [
                        'username' => $this->input->post('username'),
                        'role' => $this->input->post('role'),
                        'error' => 'Database operation failed'
                    ], 'error');

                    $this->session->set_flashdata('error', 'Gagal menambahkan user.');
                }
            }
        }

        $this->load->view('templates/header', $data);
        $this->load->view('users/create', $data);
        $this->load->view('templates/footer');
    }

    public function edit($id)
    {
        $kantin_id = $this->session->userdata('kantin_id');

        // Jika admin, tidak perlu kantin_id
        if ($this->session->userdata('role') == 'admin' && $kantin_id === null) {
            $data['title'] = 'Edit User - Admin';
            $data['kantin_info'] = null;
            $data['santri_list'] = $this->Santri_model->get_santri_without_user();
        } else {
            $data['title'] = 'Edit User - ' . $this->session->userdata('kantin_nama');
            $data['kantin_info'] = $this->Kantin_model->get_kantin($kantin_id);
            $data['santri_list'] = $this->Santri_model->get_santri_without_user($kantin_id);
        }
        $data['user'] = $this->User_model->get_by_id($id);

        if (!$data['user']) {
            $this->session->set_flashdata('error', 'User tidak ditemukan.');
            redirect('users');
        }

        if ($this->input->post()) {
            $is_unique = ($this->input->post('username') != $data['user']->username) ? '|is_unique[users.username]' : '';
            $this->form_validation->set_rules('username', 'Username', 'required' . $is_unique);
            $this->form_validation->set_rules('password', 'Password', 'min_length[4]');
            $this->form_validation->set_rules('role', 'Role', 'required|in_list[admin,operator,keuangan,santri]');

            // Gender hanya wajib untuk role selain admin dan keuangan
            if ($this->input->post('role') !== 'admin' && $this->input->post('role') !== 'keuangan') {
                $this->form_validation->set_rules('gender', 'Gender', 'required|in_list[L,P]');
            }

            if ($this->form_validation->run() === TRUE) {
                $update = [
                    'username' => $this->input->post('username'),
                    'role' => $this->input->post('role')
                ];

                if ($this->input->post('password')) {
                    $update['password'] = $this->input->post('password');
                }

                // Gender hanya untuk role selain admin dan keuangan
                if ($this->input->post('role') !== 'admin' && $this->input->post('role') !== 'keuangan') {
                    $update['gender'] = $this->input->post('gender');
                } else {
                    // Jika role admin atau keuangan, hapus gender
                    $update['gender'] = null;
                }

                // Jika role santri, tambahkan santri_id
                if ($this->input->post('role') == 'santri' && $this->input->post('santri_id')) {
                    $update['santri_id'] = $this->input->post('santri_id');
                }

                if ($this->User_model->update_user($id, $update)) {
                    // Log sukses update user
                    $this->Activity_log_model->log_system('USER_UPDATE_SUCCESS', [
                        'user_id' => $id,
                        'username' => $this->input->post('username'),
                        'role' => $this->input->post('role'),
                        'gender' => $this->input->post('gender') ?? null,
                        'santri_id' => $this->input->post('santri_id') ?? null
                    ], 'success');

                    $this->session->set_flashdata('success', 'User berhasil diupdate.');
                    redirect('users');
                } else {
                    // Log error update user
                    $this->Activity_log_model->log_system('USER_UPDATE_FAILED', [
                        'user_id' => $id,
                        'username' => $this->input->post('username'),
                        'role' => $this->input->post('role'),
                        'error' => 'Database operation failed'
                    ], 'error');

                    $this->session->set_flashdata('error', 'Gagal mengupdate user.');
                }
            }
        }

        $this->load->view('templates/header', $data);
        $this->load->view('users/edit', $data);
        $this->load->view('templates/footer');
    }

    public function delete($id)
    {
        $kantin_id = $this->session->userdata('kantin_id');

        // Cek apakah user ada
        $user = $this->User_model->get_by_id($id);
        if (!$user) {
            $this->session->set_flashdata('error', 'User tidak ditemukan.');
            redirect('users');
        }

        if ($this->User_model->delete_user($id)) {
            // Log sukses hapus user
            $this->Activity_log_model->log_system('USER_DELETE_SUCCESS', [
                'user_id' => $id,
                'username' => $user->username,
                'role' => $user->role,
                'gender' => $user->gender ?? null,
                'santri_id' => $user->santri_id ?? null
            ], 'success');

            $this->session->set_flashdata('success', 'User berhasil dihapus.');
        } else {
            // Log error hapus user
            $this->Activity_log_model->log_system('USER_DELETE_FAILED', [
                'user_id' => $id,
                'username' => $user->username,
                'error' => 'Database operation failed'
            ], 'error');

            $this->session->set_flashdata('error', 'Gagal menghapus user.');
        }

        redirect('users');
    }
}
