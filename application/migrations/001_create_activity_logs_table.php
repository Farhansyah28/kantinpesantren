<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_activity_logs_table extends CI_Migration {

    public function up() {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => TRUE,
                'comment' => 'ID user yang melakukan aktivitas'
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE,
                'comment' => 'Username saat aktivitas dilakukan'
            ],
            'role' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => TRUE,
                'comment' => 'Role user saat aktivitas'
            ],
            'category' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => FALSE,
                'comment' => 'Kategori aktivitas: AUTH, FINANCIAL, INVENTORY, USER, SYSTEM, SECURITY, BUSINESS'
            ],
            'action' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE,
                'comment' => 'Deskripsi aksi yang dilakukan'
            ],
            'amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0,
                'null' => TRUE,
                'comment' => 'Jumlah uang untuk transaksi keuangan'
            ],
            'item_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => TRUE,
                'comment' => 'ID item untuk aktivitas inventory'
            ],
            'quantity' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'null' => TRUE,
                'comment' => 'Jumlah untuk aktivitas inventory'
            ],
            'target_user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => TRUE,
                'comment' => 'ID user target untuk aktivitas user management'
            ],
            'details' => [
                'type' => 'TEXT',
                'null' => TRUE,
                'comment' => 'Detail tambahan dalam format JSON'
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => TRUE,
                'comment' => 'IP address user'
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => TRUE,
                'comment' => 'User agent browser'
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'success',
                'null' => FALSE,
                'comment' => 'Status aktivitas: success, warning, error, critical'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => FALSE,
                'comment' => 'Waktu aktivitas dilakukan'
            ]
        ]);
        
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('user_id');
        $this->dbforge->add_key('category');
        $this->dbforge->add_key('created_at');
        $this->dbforge->add_key('status');
        
        $this->dbforge->create_table('activity_logs', TRUE);
        
        // Add indexes for better performance
        $this->db->query('CREATE INDEX idx_activity_logs_user_category ON activity_logs(user_id, category)');
        $this->db->query('CREATE INDEX idx_activity_logs_date_status ON activity_logs(created_at, status)');
        $this->db->query('CREATE INDEX idx_activity_logs_ip_address ON activity_logs(ip_address)');
    }

    public function down() {
        $this->dbforge->drop_table('activity_logs', TRUE);
    }
} 