<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_users_table extends CI_Migration
{

    public function up()
    {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
            ],
            'username' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => FALSE,
                'unique' => TRUE,
                'comment' => 'Username untuk login'
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => FALSE,
                'comment' => 'Password yang di-hash'
            ],
            'role' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => FALSE,
                'default' => 'santri',
                'comment' => 'Role user: admin, operator, keuangan, santri'
            ],
            'gender' => [
                'type' => 'ENUM',
                'constraint' => ['L', 'P'],
                'null' => TRUE,
                'comment' => 'Gender: L (Laki-laki), P (Perempuan)'
            ],
            'santri_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'null' => TRUE,
                'comment' => 'ID santri jika role adalah santri'
            ],
            'terakhir_login' => [
                'type' => 'DATETIME',
                'null' => TRUE,
                'comment' => 'Waktu terakhir login'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => FALSE,
                'default' => 'CURRENT_TIMESTAMP',
                'comment' => 'Waktu user dibuat'
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => TRUE,
                'comment' => 'Waktu terakhir update'
            ]
        ]);

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('username');
        $this->dbforge->add_key('role');
        $this->dbforge->add_key('santri_id');

        $this->dbforge->create_table('users', TRUE);

        // Add indexes for better performance
        $this->db->query('CREATE INDEX idx_users_username ON users(username)');
        $this->db->query('CREATE INDEX idx_users_role ON users(role)');
        $this->db->query('CREATE INDEX idx_users_santri_id ON users(santri_id)');
        $this->db->query('CREATE INDEX idx_users_created_at ON users(created_at)');
    }

    public function down()
    {
        $this->dbforge->drop_table('users', TRUE);
    }
}
