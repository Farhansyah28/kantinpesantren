<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migrate extends CI_Controller {
    
    public $migration;
    
    public function __construct() {
        parent::__construct();
        
        // Allow in development environment or if accessed directly
        if (ENVIRONMENT !== 'development' && !$this->input->is_cli_request()) {
            show_404();
        }
        
        $this->load->library('migration');
    }
    
    public function index() {
        echo "<h2>Running Migrations...</h2>";
        
        if ($this->migration->current() === FALSE) {
            show_error($this->migration->error_string());
        } else {
            echo "<p>Migration completed successfully!</p>";
            echo "<p>Current version: " . $this->migration->current() . "</p>";
        }
    }
    
    public function reset() {
        echo "<h2>Resetting Migrations...</h2>";
        
        if ($this->migration->version(0) === FALSE) {
            show_error($this->migration->error_string());
        } else {
            echo "<p>Migration reset completed!</p>";
        }
    }
} 