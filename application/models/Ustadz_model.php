<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ustadz_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function get_ustadz($id = NULL) {
        if ($id !== NULL) {
            return $this->db->get_where('ustadz', ['id' => $id])->row();
        }
        return $this->db->get('ustadz')->result();
    }
    
    public function create_ustadz($data) {
        $this->db->insert('ustadz', $data);
        return $this->db->insert_id();
    }
    
    public function update_ustadz($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('ustadz', $data);
    }
    
    public function delete_ustadz($id) {
        return $this->db->delete('ustadz', ['id' => $id]);
    }
    
    public function get_ustadz_by_nama($nama) {
        return $this->db->get_where('ustadz', ['nama' => $nama])->row();
    }
    
    public function search_ustadz($keyword) {
        $this->db->like('nama', $keyword);
        $this->db->or_like('nomor_telepon', $keyword);
        return $this->db->get('ustadz')->result();
    }
    
    public function count_ustadz() {
        return $this->db->count_all('ustadz');
    }
    
    public function get_all_ustadz() {
        $this->db->order_by('nama', 'ASC');
        return $this->db->get('ustadz')->result();
    }
} 