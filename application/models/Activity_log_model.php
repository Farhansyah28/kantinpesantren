<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activity_log_model extends CI_Model {
    
    private $table = 'activity_logs';
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Log levels: DEBUG, INFO, WARNING, ERROR, CRITICAL
     * Categories: AUTH, FINANCIAL, INVENTORY, USER, SYSTEM, SECURITY, BUSINESS
     */
    
    /**
     * Get device information from user agent
     */
    private function get_device_info() {
        $user_agent = $this->input->user_agent();
        $device_info = [
            'browser' => 'Unknown',
            'browser_version' => 'Unknown',
            'os' => 'Unknown',
            'device_type' => 'Unknown',
            'mobile' => false
        ];
        
        // Detect browser
        if (preg_match('/Chrome\/([0-9.]+)/', $user_agent, $matches)) {
            $device_info['browser'] = 'Chrome';
            $device_info['browser_version'] = $matches[1];
        } elseif (preg_match('/Firefox\/([0-9.]+)/', $user_agent, $matches)) {
            $device_info['browser'] = 'Firefox';
            $device_info['browser_version'] = $matches[1];
        } elseif (preg_match('/Safari\/([0-9.]+)/', $user_agent, $matches)) {
            $device_info['browser'] = 'Safari';
            $device_info['browser_version'] = $matches[1];
        } elseif (preg_match('/Edge\/([0-9.]+)/', $user_agent, $matches)) {
            $device_info['browser'] = 'Edge';
            $device_info['browser_version'] = $matches[1];
        } elseif (preg_match('/MSIE\s([0-9.]+)/', $user_agent, $matches)) {
            $device_info['browser'] = 'Internet Explorer';
            $device_info['browser_version'] = $matches[1];
        }
        
        // Detect OS
        if (preg_match('/Windows NT ([0-9.]+)/', $user_agent, $matches)) {
            $device_info['os'] = 'Windows ' . $this->get_windows_version($matches[1]);
        } elseif (preg_match('/Mac OS X ([0-9._]+)/', $user_agent, $matches)) {
            $device_info['os'] = 'macOS ' . str_replace('_', '.', $matches[1]);
        } elseif (preg_match('/Linux/', $user_agent)) {
            $device_info['os'] = 'Linux';
        } elseif (preg_match('/Android ([0-9.]+)/', $user_agent, $matches)) {
            $device_info['os'] = 'Android ' . $matches[1];
            $device_info['mobile'] = true;
        } elseif (preg_match('/iPhone OS ([0-9._]+)/', $user_agent, $matches)) {
            $device_info['os'] = 'iOS ' . str_replace('_', '.', $matches[1]);
            $device_info['mobile'] = true;
        }
        
        // Detect device type
        if (preg_match('/Mobile|Android|iPhone|iPad|iPod/', $user_agent)) {
            $device_info['device_type'] = 'Mobile';
            $device_info['mobile'] = true;
        } elseif (preg_match('/Tablet|iPad/', $user_agent)) {
            $device_info['device_type'] = 'Tablet';
            $device_info['mobile'] = true;
        } else {
            $device_info['device_type'] = 'Desktop';
        }
        
        return $device_info;
    }
    
    /**
     * Get Windows version from NT version
     */
    private function get_windows_version($nt_version) {
        $versions = [
            '10.0' => '10/11',
            '6.3' => '8.1',
            '6.2' => '8',
            '6.1' => '7',
            '6.0' => 'Vista',
            '5.2' => 'Server 2003',
            '5.1' => 'XP',
            '5.0' => '2000'
        ];
        
        return isset($versions[$nt_version]) ? $versions[$nt_version] : $nt_version;
    }
    
    /**
     * Log authentication activities
     */
    public function log_auth($action, $details = [], $status = 'success') {
        $device_info = $this->get_device_info();
        $details['device_info'] = $device_info;
        
        $data = [
            'user_id' => $this->session->userdata('user_id'),
            'username' => $this->session->userdata('username'),
            'role' => $this->session->userdata('role'),
            'category' => 'AUTH',
            'action' => $action,
            'details' => json_encode($details),
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
            'status' => $status,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert($this->table, $data);
    }
    
    /**
     * Log financial transactions
     */
    public function log_financial($action, $amount = 0, $details = [], $status = 'success') {
        $device_info = $this->get_device_info();
        $details['device_info'] = $device_info;
        
        $data = [
            'user_id' => $this->session->userdata('user_id'),
            'username' => $this->session->userdata('username'),
            'role' => $this->session->userdata('role'),
            'category' => 'FINANCIAL',
            'action' => $action,
            'details' => json_encode($details),
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
            'status' => $status,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $result = $this->db->insert($this->table, $data);
        
        // Debug: log error if insert fails
        if (!$result) {
            log_message('error', 'Activity log insert failed: ' . $this->db->error()['message']);
        }
        
        return $result;
    }
    
    /**
     * Log inventory activities
     */
    public function log_inventory($action, $item_id = null, $quantity = 0, $details = [], $status = 'success') {
        $device_info = $this->get_device_info();
        $details['device_info'] = $device_info;
        
        $data = [
            'user_id' => $this->session->userdata('user_id'),
            'username' => $this->session->userdata('username'),
            'role' => $this->session->userdata('role'),
            'category' => 'INVENTORY',
            'action' => $action,
            'item_id' => $item_id,
            'quantity' => $quantity,
            'details' => json_encode($details),
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
            'status' => $status,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert($this->table, $data);
    }
    
    /**
     * Log user management activities
     */
    public function log_user_management($action, $target_user_id = null, $details = [], $status = 'success') {
        $device_info = $this->get_device_info();
        $details['device_info'] = $device_info;
        
        $data = [
            'user_id' => $this->session->userdata('user_id'),
            'username' => $this->session->userdata('username'),
            'role' => $this->session->userdata('role'),
            'category' => 'USER',
            'action' => $action,
            'target_user_id' => $target_user_id,
            'details' => json_encode($details),
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
            'status' => $status,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert($this->table, $data);
    }
    
    /**
     * Log system activities
     */
    public function log_system($action, $details = [], $status = 'success') {
        $device_info = $this->get_device_info();
        $details['device_info'] = $device_info;
        
        $data = [
            'user_id' => $this->session->userdata('user_id'),
            'username' => $this->session->userdata('username'),
            'role' => $this->session->userdata('role'),
            'category' => 'SYSTEM',
            'action' => $action,
            'details' => json_encode($details),
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
            'status' => $status,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert($this->table, $data);
    }
    
    /**
     * Log security events
     */
    public function log_security($action, $details = [], $status = 'warning') {
        $device_info = $this->get_device_info();
        $details['device_info'] = $device_info;
        
        $data = [
            'user_id' => $this->session->userdata('user_id'),
            'username' => $this->session->userdata('username'),
            'role' => $this->session->userdata('role'),
            'category' => 'SECURITY',
            'action' => $action,
            'details' => json_encode($details),
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
            'status' => $status,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert($this->table, $data);
    }
    
    /**
     * Log business intelligence activities
     */
    public function log_business($action, $details = [], $status = 'success') {
        $device_info = $this->get_device_info();
        $details['device_info'] = $device_info;
        
        $data = [
            'user_id' => $this->session->userdata('user_id'),
            'username' => $this->session->userdata('username'),
            'role' => $this->session->userdata('role'),
            'category' => 'BUSINESS',
            'action' => $action,
            'details' => json_encode($details),
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
            'status' => $status,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert($this->table, $data);
    }
    
    /**
     * Generic log method
     */
    public function log($category, $action, $details = [], $status = 'success') {
        $device_info = $this->get_device_info();
        $details['device_info'] = $device_info;
        
        $data = [
            'user_id' => $this->session->userdata('user_id'),
            'username' => $this->session->userdata('username'),
            'role' => $this->session->userdata('role'),
            'category' => strtoupper($category),
            'action' => $action,
            'details' => json_encode($details),
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent(),
            'status' => $status,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert($this->table, $data);
    }
    
    /**
     * Get logs with filters
     */
    public function get_logs($filters = [], $limit = 100, $offset = 0) {
        $this->db->select('activity_logs.*, users.username as user_username');
        $this->db->from($this->table);
        $this->db->join('users', 'users.id = activity_logs.user_id', 'left');
        
        // Apply filters
        if (!empty($filters['category'])) {
            $this->db->where('category', $filters['category']);
        }
        
        if (!empty($filters['action'])) {
            $this->db->like('action', $filters['action']);
        }
        
        if (!empty($filters['status'])) {
            $this->db->where('status', $filters['status']);
        }
        
        if (!empty($filters['user_id'])) {
            $this->db->where('activity_logs.user_id', $filters['user_id']);
        }
        
        if (!empty($filters['date_from'])) {
            $this->db->where('DATE(created_at) >=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $this->db->where('DATE(created_at) <=', $filters['date_to']);
        }
        
        if (!empty($filters['ip_address'])) {
            $this->db->like('ip_address', $filters['ip_address']);
        }
        
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit, $offset);
        
        $query = $this->db->get();
        if ($query && $query->num_rows() > 0) {
            return $query->result();
        }
        return [];
    }
    
    /**
     * Get log statistics
     */
    public function get_log_stats($date_from = null, $date_to = null) {
        if ($date_from) {
            $this->db->where('DATE(created_at) >=', $date_from);
        }
        if ($date_to) {
            $this->db->where('DATE(created_at) <=', $date_to);
        }
        
        $this->db->select('category, status, COUNT(*) as count');
        $this->db->group_by('category, status');
        $this->db->order_by('category, status');
        
        $query = $this->db->get($this->table);
        if ($query && $query->num_rows() > 0) {
            return $query->result();
        }
        return [];
    }
    
    /**
     * Get recent activities for dashboard
     */
    public function get_recent_activities($limit = 10) {
        $this->db->select('activity_logs.*, users.username as user_username');
        $this->db->from($this->table);
        $this->db->join('users', 'users.id = activity_logs.user_id', 'left');
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit);
        
        $query = $this->db->get();
        if ($query && $query->num_rows() > 0) {
            return $query->result();
        }
        return [];
    }
    
    /**
     * Clean old logs (retention policy)
     */
    public function clean_old_logs($days = 90) {
        $cutoff_date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        $this->db->where('created_at <', $cutoff_date);
        return $this->db->delete($this->table);
    }
    
    /**
     * Export logs to CSV
     */
    public function export_logs($filters = []) {
        $logs = $this->get_logs($filters, 10000, 0); // Get up to 10k records
        
        $filename = 'activity_logs_' . date('Y-m-d_H-i-s') . '.csv';
        $filepath = APPPATH . 'logs/exports/' . $filename;
        
        // Create directory if not exists
        if (!is_dir(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }
        
        $fp = fopen($filepath, 'w');
        
        // CSV headers
        fputcsv($fp, [
            'ID', 'User', 'Role', 'Category', 'Action', 'Details', 
            'IP Address', 'Status', 'Created At'
        ]);
        
        foreach ($logs as $log) {
            fputcsv($fp, [
                $log->id,
                $log->user_username ?? $log->username,
                $log->role,
                $log->category,
                $log->action,
                $log->details,
                $log->ip_address,
                $log->status,
                $log->created_at
            ]);
        }
        
        fclose($fp);
        return $filepath;
    }
} 