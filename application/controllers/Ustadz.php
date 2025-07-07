<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ustadz extends CI_Controller
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
        $this->load->model('Ustadz_model');
        $this->load->library('form_validation');

        // Cek login
        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }

        // Cek role (hanya admin dan keuangan yang bisa akses)
        $allowed_roles = ['admin', 'keuangan'];
        if (!in_array($this->session->userdata('role'), $allowed_roles)) {
            redirect('dashboard');
        }
    }

    public function index()
    {
        $data['title'] = 'Data Ustadz/Ustadzah';
        $data['ustadz'] = $this->Ustadz_model->get_all_ustadz();

        $this->load->view('templates/header', $data);
        $this->load->view('ustadz/index', $data);
        $this->load->view('templates/footer');
    }

    public function create()
    {
        $data['title'] = 'Tambah Ustadz/Ustadzah';

        $this->form_validation->set_rules('nama', 'Nama', 'required|trim|htmlspecialchars|min_length[3]|max_length[100]|is_unique[ustadz.nama]');
        $this->form_validation->set_rules('nomor_telepon', 'Nomor Telepon', 'required|trim|htmlspecialchars|min_length[10]|max_length[15]|regex_match[/^[0-9+\-\s()]+$/]');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('ustadz/create', $data);
            $this->load->view('templates/footer');
        } else {
            $data_ustadz = [
                'nama' => $this->input->post('nama', TRUE),
                'nomor_telepon' => $this->input->post('nomor_telepon', TRUE),
                'created_at' => date('Y-m-d H:i:s')
            ];

            if ($this->Ustadz_model->create_ustadz($data_ustadz)) {
                // Log sukses tambah ustadz
                $this->Activity_log_model->log_system('USTADZ_CREATE_SUCCESS', [
                    'nama' => $this->input->post('nama', TRUE),
                    'nomor_telepon' => $this->input->post('nomor_telepon', TRUE)
                ], 'success');
                $this->session->set_flashdata('success', 'Data ustadz/ustadzah berhasil ditambahkan');
                redirect('ustadz');
            } else {
                // Log gagal tambah ustadz
                $this->Activity_log_model->log_system('USTADZ_CREATE_FAILED', [
                    'nama' => $this->input->post('nama', TRUE),
                    'nomor_telepon' => $this->input->post('nomor_telepon', TRUE)
                ], 'error');
                $this->session->set_flashdata('error', 'Gagal menambahkan data ustadz/ustadzah');
                redirect('ustadz/create');
            }
        }
    }

    public function edit($id)
    {
        // Validasi ID
        if (!is_numeric($id) || $id <= 0) {
            show_404();
        }

        $data['title'] = 'Edit Ustadz/Ustadzah';
        $data['ustadz'] = $this->Ustadz_model->get_ustadz($id);

        if (empty($data['ustadz'])) {
            show_404();
        }

        $this->form_validation->set_rules('nama', 'Nama', 'required|trim|htmlspecialchars|min_length[3]|max_length[100]');
        $this->form_validation->set_rules('nomor_telepon', 'Nomor Telepon', 'required|trim|htmlspecialchars|min_length[10]|max_length[15]|regex_match[/^[0-9+\-\s()]+$/]');

        // Custom validation untuk nama unik (kecuali untuk record yang sedang diedit)
        $this->form_validation->set_rules('nama', 'Nama', 'callback_check_nama_unique[' . $id . ']');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('ustadz/edit', $data);
            $this->load->view('templates/footer');
        } else {
            $data_ustadz = [
                'nama' => $this->input->post('nama', TRUE),
                'nomor_telepon' => $this->input->post('nomor_telepon', TRUE),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if ($this->Ustadz_model->update_ustadz($id, $data_ustadz)) {
                // Log sukses edit ustadz
                $this->Activity_log_model->log_system('USTADZ_UPDATE_SUCCESS', [
                    'id' => $id,
                    'nama' => $this->input->post('nama', TRUE),
                    'nomor_telepon' => $this->input->post('nomor_telepon', TRUE)
                ], 'success');
                $this->session->set_flashdata('success', 'Data ustadz/ustadzah berhasil diperbarui');
                redirect('ustadz');
            } else {
                // Log gagal edit ustadz
                $this->Activity_log_model->log_system('USTADZ_UPDATE_FAILED', [
                    'id' => $id,
                    'nama' => $this->input->post('nama', TRUE),
                    'nomor_telepon' => $this->input->post('nomor_telepon', TRUE)
                ], 'error');
                $this->session->set_flashdata('error', 'Gagal memperbarui data ustadz/ustadzah');
                redirect('ustadz/edit/' . $id);
            }
        }
    }

    public function delete($id)
    {
        // Validasi ID
        if (!is_numeric($id) || $id <= 0) {
            show_404();
        }

        $ustadz = $this->Ustadz_model->get_ustadz($id);

        if (empty($ustadz)) {
            show_404();
        }

        // Cek apakah ada transaksi terkait
        $this->load->model('Transaksi_model');
        $transaksi_count = $this->Transaksi_model->count_transaksi_ustadz_hari_ini();

        if ($transaksi_count > 0) {
            $this->session->set_flashdata('error', 'Tidak dapat menghapus ustadz/ustadzah yang memiliki riwayat transaksi');
            redirect('ustadz');
        }

        if ($this->Ustadz_model->delete_ustadz($id)) {
            // Log sukses hapus ustadz
            $this->Activity_log_model->log_system('USTADZ_DELETE_SUCCESS', [
                'id' => $id,
                'nama' => $ustadz->nama,
                'nomor_telepon' => $ustadz->nomor_telepon
            ], 'success');
            $this->session->set_flashdata('success', 'Data ustadz/ustadzah berhasil dihapus');
        } else {
            // Log gagal hapus ustadz
            $this->Activity_log_model->log_system('USTADZ_DELETE_FAILED', [
                'id' => $id,
                'nama' => $ustadz->nama,
                'nomor_telepon' => $ustadz->nomor_telepon
            ], 'error');
            $this->session->set_flashdata('error', 'Gagal menghapus data ustadz/ustadzah');
        }
        redirect('ustadz');
    }

    public function search()
    {
        $keyword = $this->input->get('keyword', TRUE);

        // Validasi keyword
        if (empty($keyword) || strlen($keyword) < 2) {
            $this->session->set_flashdata('error', 'Kata kunci pencarian minimal 2 karakter');
            redirect('ustadz');
        }

        // Sanitasi keyword
        $keyword = htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8');

        $data['title'] = 'Pencarian Ustadz/Ustadzah';
        $data['ustadz'] = $this->Ustadz_model->search_ustadz($keyword);
        $data['keyword'] = $keyword;

        $this->load->view('templates/header', $data);
        $this->load->view('ustadz/index', $data);
        $this->load->view('templates/footer');
    }

    public function import()
    {
        if ($this->input->method() === 'post') {
            if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
                $this->session->set_flashdata('error', 'Tidak ada file yang dipilih atau terjadi error saat upload.');
                redirect('ustadz/import');
            }

            $file = $_FILES['csv_file'];

            // Validasi ekstensi file
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if ($ext !== 'csv') {
                $this->session->set_flashdata('error', 'File harus berformat .csv');
                redirect('ustadz/import');
            }

            // Validasi MIME type
            $allowed_mimes = ['text/csv', 'text/plain', 'application/csv', 'application/octet-stream'];
            if (!in_array($file['type'], $allowed_mimes)) {
                $this->session->set_flashdata('error', 'Tipe file tidak diizinkan.');
                redirect('ustadz/import');
            }

            // Validasi ukuran file (max 5MB)
            if ($file['size'] > 5 * 1024 * 1024) {
                $this->session->set_flashdata('error', 'Ukuran file terlalu besar. Maksimal 5MB.');
                redirect('ustadz/import');
            }

            // Sanitasi nama file
            $filename = basename($file['name']);
            if (preg_match('/[^a-zA-Z0-9._-]/', $filename)) {
                $this->session->set_flashdata('error', 'Nama file mengandung karakter tidak diizinkan.');
                redirect('ustadz/import');
            }

            $upload_path = FCPATH . 'uploads/ustadz/';
            if (!is_dir($upload_path)) mkdir($upload_path, 0755, true);

            // Generate nama file yang aman
            $safe_filename = 'import_ustadz_' . uniqid() . '_' . time() . '.csv';
            $target = $upload_path . $safe_filename;

            if (!move_uploaded_file($file['tmp_name'], $target)) {
                $this->session->set_flashdata('error', 'Gagal upload file.');
                redirect('ustadz/import');
            }

            $success = 0;
            $fail = 0;
            $errors = [];

            if (($handle = fopen($target, 'r')) !== FALSE) {
                $row = 0;
                while (($data = fgetcsv($handle, 0, ';')) !== FALSE) {
                    $row++;
                    if ($row == 1) continue; // skip header

                    $nama = trim($data[0] ?? '');
                    $nomor_telepon = trim($data[1] ?? '');

                    // Validasi data
                    if (!$nama || !$nomor_telepon) {
                        $fail++;
                        $errors[] = "Baris $row: Nama atau Nomor Telepon kosong";
                        continue;
                    }

                    // Validasi panjang nama
                    if (strlen($nama) < 3 || strlen($nama) > 100) {
                        $fail++;
                        $errors[] = "Baris $row: Nama harus 3-100 karakter";
                        continue;
                    }

                    // Validasi format nomor telepon
                    if (!preg_match('/^[0-9+\-\s()]+$/', $nomor_telepon) || strlen($nomor_telepon) < 10 || strlen($nomor_telepon) > 15) {
                        $fail++;
                        $errors[] = "Baris $row: Format nomor telepon tidak valid";
                        continue;
                    }

                    // Cek duplikat nama
                    if ($this->Ustadz_model->get_ustadz_by_nama($nama)) {
                        $fail++;
                        $errors[] = "Baris $row: Nama '$nama' sudah ada";
                        continue;
                    }

                    // Insert data ustadz
                    $ustadz_data = [
                        'nama' => $nama,
                        'nomor_telepon' => $nomor_telepon,
                        'created_at' => date('Y-m-d H:i:s')
                    ];

                    if ($this->Ustadz_model->create_ustadz($ustadz_data)) {
                        $success++;

                        // Log sukses import ustadz
                        $this->Activity_log_model->log_system('USTADZ_IMPORT_SUCCESS', [
                            'nama' => $nama,
                            'nomor_telepon' => $nomor_telepon,
                            'row' => $row
                        ], 'success');
                    } else {
                        $fail++;
                        $errors[] = "Baris $row: Gagal menyimpan data";
                    }
                }
                fclose($handle);
            }

            // Hapus file temporary
            unlink($target);

            // Log hasil import
            $this->Activity_log_model->log_system('USTADZ_IMPORT_COMPLETE', [
                'success' => $success,
                'fail' => $fail,
                'total_rows' => $success + $fail
            ], $fail == 0 ? 'success' : 'warning');

            $msg = "Import selesai. Berhasil: $success, Gagal: $fail";
            if ($fail > 0) {
                $msg .= '<br><br>Detail error:<br>' . implode('<br>', array_slice($errors, 0, 10));
                if (count($errors) > 10) {
                    $msg .= '<br>... dan ' . (count($errors) - 10) . ' error lainnya';
                }
            }

            $this->session->set_flashdata($fail == 0 ? 'success' : 'error', $msg);
            redirect('ustadz/import');
        }

        $data['title'] = 'Import Data Ustadz/Ustadzah';
        $this->load->view('templates/header', $data);
        $this->load->view('ustadz/import', $data);
        $this->load->view('templates/footer');
    }

    public function download_template()
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment;filename="Template_Import_Ustadz.csv"');
        echo "\xEF\xBB\xBF"; // BOM untuk UTF-8
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Nama Lengkap', 'Nomor Telepon'], ';');
        fputcsv($output, ['Ust. Ahmad', '081234567890'], ';');
        fputcsv($output, ['Usth. Fatimah', '081234567891'], ';');
        fputcsv($output, ['Ust. Muhammad', '081234567891'], ';');
        fclose($output);
        exit;
    }

    public function export_csv()
    {
        $ustadz_data = $this->Ustadz_model->get_all_ustadz();
        $filename = 'Data_Ustadz_' . date('Y-m-d_H-i-s') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        echo "\xEF\xBB\xBF"; // BOM untuk UTF-8

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Nama Lengkap', 'Nomor Telepon', 'Tanggal Dibuat'], ';');

        foreach ($ustadz_data as $u) {
            fputcsv($output, [
                $u->nama,
                $u->nomor_telepon,
                date('d/m/Y H:i', strtotime($u->created_at))
            ], ';');
        }

        fclose($output);
        exit;
    }

    /**
     * Custom validation untuk nama unik
     */
    public function check_nama_unique($nama, $id)
    {
        $ustadz = $this->Ustadz_model->get_ustadz_by_nama($nama);

        if ($ustadz && $ustadz->id != $id) {
            $this->form_validation->set_message('check_nama_unique', 'Nama ustadz/ustadzah sudah ada');
            return FALSE;
        }

        return TRUE;
    }
}
