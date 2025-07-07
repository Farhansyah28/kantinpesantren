<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/home
     *	- or -
     * 		http://example.com/index.php/home/index
     *	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/home/<method_name>
     * @see https://codeigniter.com/userguide3/general/urls.html
     */
    public function index()
    {
        // Cek apakah user sudah login
        if ($this->session->userdata('logged_in')) {
            // Jika sudah login, redirect ke dashboard
            redirect('dashboard');
        } else {
            // Jika belum login, redirect ke login
            redirect('auth/login');
        }
    }
}
