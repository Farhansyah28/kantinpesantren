<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activity_logs extends CI_Controller {
    
    // Property declarations for autoloaded models to prevent PHP 8.2+ deprecation warnings
    public $Activity_log_model;
    public $Ustadz_model;
    public $Santri_model;
    public $Tabungan_model;
    public $User_model;
    public $Menu_model;
    public $Transaksi_model;
    public $Kantin_model;
    
    public function __construct() {
        parent::__construct();
        $this->load->model('Activity_log_model');
        
        // Cek login dan role admin/keuangan (kecuali untuk test_log)
        $current_method = $this->router->fetch_method();
        if ($current_method !== 'test_log' && (!$this->session->userdata('logged_in') || !in_array($this->session->userdata('role'), ['admin', 'keuangan']))) {
            redirect('auth/login');
        }
    }
    
    public function index() {
        $data['title'] = 'Activity Logs';
        
        // Get filters from query string
        $filters = [
            'category' => $this->input->get('category'),
            'action' => $this->input->get('action'),
            'status' => $this->input->get('status'),
            'user_id' => $this->input->get('user_id'),
            'date_from' => $this->input->get('date_from'),
            'date_to' => $this->input->get('date_to'),
            'ip_address' => $this->input->get('ip_address')
        ];
        
        // Pagination
        $page = $this->input->get('page') ? $this->input->get('page') : 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;
        
        // Get logs
        $data['logs'] = $this->Activity_log_model->get_logs($filters, $limit, $offset);
        
        // Get total count for pagination
        $total_logs = $this->Activity_log_model->get_logs($filters, 10000, 0); // Get all for count
        $data['total_logs'] = count($total_logs);
        $data['total_pages'] = ceil($data['total_logs'] / $limit);
        $data['current_page'] = $page;
        
        // Get statistics
        $data['stats'] = $this->Activity_log_model->get_log_stats($filters['date_from'], $filters['date_to']);
        
        // Get unique categories for filter dropdown
        $data['categories'] = ['AUTH', 'FINANCIAL', 'INVENTORY', 'USER', 'SYSTEM', 'SECURITY', 'BUSINESS'];
        $data['statuses'] = ['success', 'warning', 'error', 'critical'];
        
        // Pass filters back to view
        $data['filters'] = $filters;
        
        $this->load->view('templates/header', $data);
        $this->load->view('activity_logs/index', $data);
        $this->load->view('templates/footer');
    }
    
    public function view($id) {
        $data['title'] = 'Detail Activity Log';
        
        $this->db->select('activity_logs.*, users.username as user_username');
        $this->db->from('activity_logs');
        $this->db->join('users', 'users.id = activity_logs.user_id', 'left');
        $this->db->where('activity_logs.id', $id);
        
        $data['log'] = $this->db->get()->row();
        
        if (!$data['log']) {
            $this->session->set_flashdata('error', 'Activity log tidak ditemukan');
            redirect('activity_logs');
        }
        
        // Decode JSON details
        if ($data['log']->details) {
            $data['log']->details_array = json_decode($data['log']->details, true);
        }
        
        $this->load->view('templates/header', $data);
        $this->load->view('activity_logs/view', $data);
        $this->load->view('templates/footer');
    }
    
    public function export() {
        // Get filters from query string
        $filters = [
            'category' => $this->input->get('category'),
            'action' => $this->input->get('action'),
            'status' => $this->input->get('status'),
            'user_id' => $this->input->get('user_id'),
            'date_from' => $this->input->get('date_from'),
            'date_to' => $this->input->get('date_to'),
            'ip_address' => $this->input->get('ip_address')
        ];
        
        $filepath = $this->Activity_log_model->export_logs($filters);
        
        if (file_exists($filepath)) {
            $filename = basename($filepath);
            
            // Set headers for download
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . filesize($filepath));
            
            // Output file
            readfile($filepath);
            
            // Delete temporary file
            unlink($filepath);
        } else {
            $this->session->set_flashdata('error', 'Gagal mengexport data');
            redirect('activity_logs');
        }
    }
    
    public function dashboard() {
        $data['title'] = 'Activity Logs Dashboard';
        
        // Get recent activities
        $data['recent_activities'] = $this->Activity_log_model->get_recent_activities(20);
        
        // Get statistics for last 30 days
        $date_from = date('Y-m-d', strtotime('-30 days'));
        $data['stats_30_days'] = $this->Activity_log_model->get_log_stats($date_from);
        
        // Get statistics for last 7 days
        $date_from_7 = date('Y-m-d', strtotime('-7 days'));
        $data['stats_7_days'] = $this->Activity_log_model->get_log_stats($date_from_7);
        
        // Get top users by activity
        $data['top_users'] = $this->get_top_users_by_activity();
        
        // Get activity by hour (last 24 hours)
        $data['activity_by_hour'] = $this->get_activity_by_hour();
        
        $this->load->view('templates/header', $data);
        $this->load->view('activity_logs/dashboard', $data);
        $this->load->view('templates/footer');
    }
    
    public function clean_old_logs() {
        $days = $this->input->post('days') ? $this->input->post('days') : 90;
        
        $deleted_count = $this->Activity_log_model->clean_old_logs($days);
        
        $this->session->set_flashdata('success', "Berhasil menghapus {$deleted_count} log lama (lebih dari {$days} hari)");
        redirect('activity_logs');
    }
    
    private function get_top_users_by_activity() {
        $this->db->select('user_id, username, COUNT(*) as activity_count');
        $this->db->from('activity_logs');
        $this->db->where('created_at >=', date('Y-m-d H:i:s', strtotime('-30 days')));
        $this->db->group_by('user_id, username');
        $this->db->order_by('activity_count', 'DESC');
        $this->db->limit(10);
        
        return $this->db->get()->result();
    }
    
    private function get_activity_by_hour() {
        $this->db->select('HOUR(created_at) as hour, COUNT(*) as count');
        $this->db->from('activity_logs');
        $this->db->where('created_at >=', date('Y-m-d H:i:s', strtotime('-24 hours')));
        $this->db->group_by('HOUR(created_at)');
        $this->db->order_by('hour', 'ASC');
        
        return $this->db->get()->result();
    }
    
    public function ajax_get_logs() {
        // AJAX endpoint for DataTables
        $draw = $this->input->post('draw');
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $search = $this->input->post('search')['value'];
        
        // Build filters
        $filters = [];
        if ($search) {
            $filters['action'] = $search;
        }
        
        // Get logs
        $logs = $this->Activity_log_model->get_logs($filters, $length, $start);
        $total_logs = $this->Activity_log_model->get_logs($filters, 10000, 0);
        $total_count = count($total_logs);
        
        // Format data for DataTables
        $data = [];
        foreach ($logs as $log) {
            $data[] = [
                $log->id,
                $log->user_username ?? $log->username,
                $log->role,
                $log->category,
                $log->action,
                $log->status,
                $log->ip_address,
                date('d/m/Y H:i:s', strtotime($log->created_at)),
                '<a href="' . base_url('activity_logs/view/' . $log->id) . '" class="btn btn-sm btn-info">View</a>'
            ];
        }
        
        echo json_encode([
            'draw' => $draw,
            'recordsTotal' => $total_count,
            'recordsFiltered' => $total_count,
            'data' => $data
        ]);
    }
    
    public function test_log() {
        // Test method untuk mengecek apakah logging berfungsi
        echo "<h2>Test Activity Logging</h2>";
        echo "<p>✅ Controller Activity_logs berhasil diakses!</p>";
        echo "<p>✅ Method test_log berhasil dipanggil!</p>";
        
        // Cek koneksi database
        echo "<p><strong>Koneksi database:</strong> ";
        if ($this->db->conn_id) {
            echo "✅ Terhubung";
        } else {
            echo "❌ Tidak terhubung";
        }
        echo "</p>";
        
        // Cek apakah tabel activity_logs ada
        $table_exists = $this->db->table_exists('activity_logs');
        echo "<p><strong>Tabel activity_logs ada:</strong> " . ($table_exists ? "✅ Ya" : "❌ Tidak") . "</p>";
        
        if (!$table_exists) {
            echo "<p style='color: red;'>❌ Tabel activity_logs belum dibuat! Silakan jalankan query CREATE TABLE terlebih dahulu.</p>";
            echo "<p>Query yang perlu dijalankan:</p>";
            echo "<pre>";
            echo "CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    username VARCHAR(100),
    role VARCHAR(50),
    category VARCHAR(50),
    action VARCHAR(255),
    details TEXT,
    status VARCHAR(20),
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);";
            echo "</pre>";
            return;
        }
        
        // Cek apakah model bisa diakses
        echo "<p><strong>Activity_log_model bisa diakses:</strong> " . (isset($this->Activity_log_model) ? "✅ Ya" : "❌ Tidak") . "</p>";
        
        // Test insert log sederhana
        $data = [
            'user_id' => 1,
            'username' => 'test_user',
            'role' => 'admin',
            'category' => 'SYSTEM',
            'action' => 'TEST_LOG',
            'details' => json_encode(['test_message' => 'Ini adalah test logging']),
            'status' => 'success',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Browser'
        ];
        
        $this->db->insert('activity_logs', $data);
        
        if ($this->db->affected_rows() > 0) {
            echo "<p style='color: green;'>✅ Test logging berhasil! Log berhasil disimpan ke database.</p>";
            echo "<p>Silakan cek di halaman <a href='" . base_url('activity-logs') . "'>Activity Logs</a></p>";
        } else {
            echo "<p style='color: red;'>❌ Test logging gagal! Log tidak berhasil disimpan.</p>";
            $error = $this->db->error();
            echo "<p><strong>Error:</strong> " . $error['message'] . "</p>";
        }
        
        // Tampilkan query terakhir untuk debugging
        echo "<h3>Query Terakhir:</h3>";
        echo "<pre>" . $this->db->last_query() . "</pre>";
        
        // Tampilkan error database jika ada
        if ($this->db->error()['code'] != 0) {
            echo "<h3>Database Error:</h3>";
            echo "<pre>";
            print_r($this->db->error());
            echo "</pre>";
        }
    }
} 