<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {
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
        $this->load->library('session');
        $this->load->model(['User_model', 'Kantin_model']);
        $this->load->library('form_validation');
        $this->load->helper('url');
    }

    public function index() {
        if ($this->session->userdata('logged_in')) {
            // Redirect berdasarkan role
            if ($this->session->userdata('role') === 'operator') {
                redirect('pos/modern'); // Operator langsung ke POS modern
            } else {
                redirect('dashboard'); // Role lain ke dashboard
            }
        }
        $this->load->view('auth/login');
    }

    public function register() {
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('username', 'Username', 'required|min_length[4]|is_unique[users.username]');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
            $this->form_validation->set_rules('confirm_password', 'Konfirmasi Password', 'required|matches[password]');
            $this->form_validation->set_rules('role', 'Role', 'required|in_list[admin,operator,keuangan,santri]');
            
            // Gender hanya wajib untuk role selain admin dan keuangan
            if ($this->input->post('role') !== 'admin' && $this->input->post('role') !== 'keuangan') {
                $this->form_validation->set_rules('gender', 'Gender', 'required|in_list[L,P]');
            }

            if ($this->form_validation->run() === TRUE) {
                $data = array(
                    'username' => $this->input->post('username'),
                    'password' => $this->input->post('password'),
                    'role' => $this->input->post('role')
                );
                
                // Gender hanya untuk role selain admin dan keuangan
                if ($this->input->post('role') !== 'admin' && $this->input->post('role') !== 'keuangan') {
                    $data['gender'] = $this->input->post('gender');
                }

                if ($this->User_model->create_user($data)) {
                    $this->session->set_flashdata('success', 'Pendaftaran berhasil! Silakan login.');
                    redirect('auth/login');
                } else {
                    $this->session->set_flashdata('error', 'Terjadi kesalahan saat mendaftar.');
                }
            }
        }

        $this->load->view('auth/register');
    }

    public function login() {
        if ($this->session->userdata('logged_in')) {
            redirect('dashboard');
        }
        
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');
        
        if ($this->form_validation->run() === TRUE) {
            $username = $this->input->post('username');
            $password = $this->input->post('password');
            
            // Ambil user berdasarkan username (tanpa kantin_id)
            $user = $this->User_model->get_user_by_username($username);
            
            if ($user && password_verify($password, $user->password)) {
                // Log successful login
                $this->Activity_log_model->log_auth('LOGIN_SUCCESS', [
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'role' => $user->role
                ], 'success');
                
                // Set session data
                $session_data = [
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'role' => $user->role,
                    'logged_in' => TRUE
                ];
                
                // Gender hanya untuk role selain admin dan keuangan
                if ($user->role !== 'admin' && $user->role !== 'keuangan') {
                    $session_data['gender'] = $user->gender;
                }
                
                // Jika role santri, tambahkan santri_id
                if ($user->role == 'santri' && $user->santri_id) {
                    $session_data['santri_id'] = $user->santri_id;
                }
                
                // Tentukan kantin berdasarkan gender dan role
                if ($user->role == 'admin' || $user->role == 'keuangan') {
                    // Admin dan Keuangan tidak set kantin_id default, akan diminta pilih di dashboard
                    $session_data['kantin_id'] = null;
                    $session_data['kantin_nama'] = null;
                } else {
                    // Operator, Santri: L = Kantin Putra, P = Kantin Putri
                    if ($user->gender == 'L') {
                        $kantin_id = 1; // Kantin Putra
                        $kantin_nama = 'Kantin Putra';
                    } else {
                        $kantin_id = 2; // Kantin Putri
                        $kantin_nama = 'Kantin Putri';
                    }
                    
                    // Ambil info kantin
                    $kantin = $this->Kantin_model->get_kantin($kantin_id);
                    if ($kantin) {
                        $kantin_nama = $kantin->nama;
                    }
                    
                    $session_data['kantin_id'] = $kantin_id;
                    $session_data['kantin_nama'] = $kantin_nama;
                }
                
                $this->session->set_userdata($session_data);
                
                // Update last login
                $this->User_model->update_last_login($user->id);
                
                // Redirect berdasarkan role
                if ($user->role === 'operator') {
                    redirect('pos/modern'); // Operator langsung ke POS modern
                } else {
                    redirect('dashboard'); // Role lain ke dashboard
                }
            } else {
                // Log failed login attempt
                $this->Activity_log_model->log_auth('LOGIN_FAILED', [
                    'username' => $username,
                    'reason' => 'Invalid credentials'
                ], 'error');
                
                $this->session->set_flashdata('error', 'Username atau password salah');
            }
        }
        
        $data['title'] = 'Login';
        
        $this->load->view('auth/login', $data);
    }

    public function logout() {
        // Log logout
        $this->Activity_log_model->log_auth('LOGOUT', [
            'user_id' => $this->session->userdata('user_id'),
            'username' => $this->session->userdata('username')
        ], 'success');
        
        $this->session->sess_destroy();
        redirect('auth/login');
    }

    public function change_password() {
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
        
        $this->form_validation->set_rules('current_password', 'Password Saat Ini', 'required');
        $this->form_validation->set_rules('new_password', 'Password Baru', 'required|min_length[6]');
        $this->form_validation->set_rules('confirm_password', 'Konfirmasi Password', 'required|matches[new_password]');
        
        if ($this->form_validation->run() == FALSE) {
            $data['title'] = 'Change Password';
            $this->load->view('templates/header', $data);
            $this->load->view('auth/change_password');
            $this->load->view('templates/footer');
        } else {
            $current_password = $this->input->post('current_password');
            $new_password = $this->input->post('new_password');
            
            $user = $this->User_model->get_user($this->session->userdata('user_id'));
            
            if (password_verify($current_password, $user->password)) {
                $this->User_model->update_user($user->id, [
                    'password' => $new_password
                ]);
                
                $this->session->set_flashdata('success', 'Password berhasil diubah');
                redirect('auth/change_password');
            } else {
                $this->session->set_flashdata('error', 'Password saat ini salah');
                redirect('auth/change_password');
            }
        }
    }
} 