<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaksi extends CI_Controller {
    
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
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['url', 'form']);
        
        // Cek login
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
        
        // Cek role untuk akses transaksi
        if (!in_array($this->session->userdata('role'), ['admin', 'keuangan'])) {
            $this->session->set_flashdata('error', 'Anda tidak memiliki akses ke halaman ini');
            redirect('dashboard');
        }
        
        $this->load->model(['Santri_model', 'Menu_model', 'Tabungan_model', 'Transaksi_model']);
    }
    
    public function index() {
        $data['title'] = 'Transaksi Kantin';
        
        // Load sample data for testing
        $data['santri'] = $this->Santri_model->get_santri_with_tabungan();
        $data['menu'] = $this->Menu_model->get_menu_aktif();
        
        $this->load->view('templates/header', $data);
        $this->load->view('transaksi/index', $data);
        $this->load->view('templates/footer');
    }
    
    // AJAX endpoint untuk search santri
    public function search_santri() {
        $keyword = $this->input->get('q');
        
        // Debug: log the search
        error_log("Searching santri with keyword: " . $keyword);
        
        $santri = $this->Santri_model->search_santri($keyword);
        
        // Debug: log the results
        error_log("Found " . count($santri) . " santri");
        
        $result = [];
        foreach ($santri as $s) {
            $result[] = [
                'id' => $s->id,
                'text' => $s->nama . ' - ' . $s->nomor_induk . ' (' . $s->kelas . ')',
                'nama' => $s->nama,
                'nomor_induk' => $s->nomor_induk,
                'kelas' => $s->kelas,
                'saldo_jajan' => $s->saldo_jajan ?? 0
            ];
        }
        
        // Set proper headers for JSON response
        header('Content-Type: application/json');
        echo json_encode($result);
    }
    
    // AJAX endpoint untuk search menu
    public function search_menu() {
        $keyword = $this->input->get('q');
        
        // Debug: log the search
        error_log("Searching menu with keyword: " . $keyword);
        
        $menu = $this->Menu_model->search_menu($keyword);
        
        // Debug: log the results
        error_log("Found " . count($menu) . " menu");
        
        $result = [];
        foreach ($menu as $m) {
            if ($m->stok > 0) {
                $result[] = [
                    'id' => $m->id,
                    'text' => $m->nama_menu . ' - Rp ' . number_format($m->harga, 0, ',', '.') . ' (Stok: ' . $m->stok . ')',
                    'nama_menu' => $m->nama_menu,
                    'harga' => $m->harga,
                    'stok' => $m->stok
                ];
            }
        }
        
        // Set proper headers for JSON response
        header('Content-Type: application/json');
        echo json_encode($result);
    }
    
    // AJAX endpoint untuk get info santri
    public function get_santri_info() {
        $santri_id = $this->input->post('santri_id');
        $santri = $this->Santri_model->get_santri_with_tabungan($santri_id);
        
        if ($santri) {
            // Hitung limit harian (Rp 12.000 per hari)
            $today = date('Y-m-d');
            $transaksi_hari_ini = $this->Transaksi_model->get_transaksi_hari_ini($santri_id, $today);
            $total_hari_ini = 0;
            
            foreach ($transaksi_hari_ini as $t) {
                $total_hari_ini += $t->total_harga;
            }
            
            $limit_harian = 12000;
            $sisa_limit = $limit_harian - $total_hari_ini;
            
            $response = [
                'success' => true,
                'data' => [
                    'nama' => $santri->nama,
                    'nomor_induk' => $santri->nomor_induk,
                    'kelas' => $santri->kelas,
                    'saldo_jajan' => $santri->saldo_jajan ?? 0,
                    'limit_harian' => $limit_harian,
                    'total_hari_ini' => $total_hari_ini,
                    'sisa_limit' => $sisa_limit
                ]
            ];
        } else {
            $response = ['success' => false, 'message' => 'Santri tidak ditemukan'];
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }
    
    // Proses transaksi
    public function process_transaction() {
        $this->form_validation->set_rules('santri_id', 'Santri', 'required|numeric');
        $this->form_validation->set_rules('items', 'Items', 'required');
        
        if ($this->form_validation->run() === FALSE) {
            $response = ['success' => false, 'message' => validation_errors()];
        } else {
            $santri_id = $this->input->post('santri_id');
            $items = json_decode($this->input->post('items'), true);
            $total_amount = $this->input->post('total_amount');
            
            // Validasi saldo
            $santri = $this->Santri_model->get_santri_with_tabungan($santri_id);
            if ($santri->saldo_jajan < $total_amount) {
                $response = ['success' => false, 'message' => 'Saldo jajan tidak mencukupi'];
            } else {
                // Validasi limit harian
                $today = date('Y-m-d');
                $transaksi_hari_ini = $this->Transaksi_model->get_transaksi_hari_ini($santri_id, $today);
                $total_hari_ini = 0;
                
                foreach ($transaksi_hari_ini as $t) {
                    $total_hari_ini += $t->total_harga;
                }
                
                if (($total_hari_ini + $total_amount) > 12000) {
                    $response = ['success' => false, 'message' => 'Transaksi melebihi limit harian (Rp 12.000)'];
                } else {
                    // Proses transaksi
                    $this->db->trans_start();
                    
                    $success = true;
                    foreach ($items as $item) {
                        // Kurangi stok
                        if (!$this->Menu_model->kurangi_stok($item['menu_id'], $item['quantity'], 'Transaksi kantin', $this->session->userdata('user_id'))) {
                            $success = false;
                            break;
                        }
                        
                        // Catat transaksi kantin
                        $transaksi_data = [
                            'santri_id' => $santri_id,
                            'menu_id' => $item['menu_id'],
                            'jumlah' => $item['quantity'],
                            'total_harga' => $item['total'],
                            'status' => 'selesai',
                            'created_at' => date('Y-m-d H:i:s')
                        ];
                        
                        if (!$this->Transaksi_model->create_transaksi_kantin($transaksi_data)) {
                            $success = false;
                            break;
                        }
                    }
                    
                    if ($success) {
                        // Log sukses transaksi kantin
                        $this->Activity_log_model->log_system('KANTIN_TRANSACTION_SUCCESS', [
                            'santri_id' => $santri_id,
                            'santri_nama' => $santri->nama,
                            'total_amount' => $total_amount,
                            'item_count' => count($items),
                            'items' => $items
                        ], 'success');
                        // Kurangi saldo jajan
                        $new_saldo = $santri->saldo_jajan - $total_amount;
                        $this->Tabungan_model->update_saldo_jajan($santri_id, $new_saldo);
                        
                        $this->db->trans_complete();
                        
                        if ($this->db->trans_status() === FALSE) {
                            $response = ['success' => false, 'message' => 'Gagal memproses transaksi'];
                        } else {
                            $response = [
                                'success' => true, 
                                'message' => 'Transaksi berhasil diproses',
                                'new_saldo' => $new_saldo
                            ];
                        }
                    } else {
                        // Log error transaksi kantin
                        $this->Activity_log_model->log_system('KANTIN_TRANSACTION_FAILED', [
                            'santri_id' => $santri_id,
                            'santri_nama' => $santri->nama,
                            'total_amount' => $total_amount,
                            'item_count' => count($items),
                            'items' => $items,
                            'error' => 'Database operation failed'
                        ], 'error');
                        
                        $this->db->trans_rollback();
                        $response = ['success' => false, 'message' => 'Gagal memproses transaksi'];
                    }
                }
            }
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }
    
    // Debug method untuk testing
    public function debug() {
        $this->load->view('transaksi/debug');
    }
} 