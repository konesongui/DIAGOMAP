<?php
class Product extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('product_model');
        $this->load->library('form_validation');
    }
    
    public function index() {

        var_dump($this->session->userdata('user_id'));
        exit;
        $data['products'] = $this->product_model->get_all_products();
        $this->load->view('products/list', $data);
    }
    
    public function add() {
        $this->form_validation->set_rules('code', 'Code', 'required|is_unique[products.code]');
        $this->form_validation->set_rules('name', 'Nom', 'required');
        $this->form_validation->set_rules('price', 'Prix', 'required|numeric');
        
        if ($this->form_validation->run() === FALSE) {
            $this->load->view('products/add');
        } else {
            $data = array(
                'code' => $this->input->post('code'),
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
                'price' => $this->input->post('price'),
                'category_id' => $this->input->post('category_id'),
                'tax_rate' => $this->input->post('tax_rate')
            );
            
            $this->product_model->create_product($data);
            redirect('products');
        }
    }
    
    public function edit($id) {
        $this->form_validation->set_rules('name', 'Nom', 'required');
        $this->form_validation->set_rules('price', 'Prix', 'required|numeric');
        
        if ($this->form_validation->run() === FALSE) {
            $data['product'] = $this->product_model->get_product($id);
            $this->load->view('products/edit', $data);
        } else {
            $data = array(
                'name' => $this->input->post('name'),
                'description' => $this->input->post('description'),
                'price' => $this->input->post('price'),
                'category_id' => $this->input->post('category_id'),
                'tax_rate' => $this->input->post('tax_rate')
            );
            
            $this->product_model->update_product($id, $data);
            redirect('products');
        }
    }
    
    public function delete($id) {
        $this->product_model->delete_product($id);
        redirect('products');
    }
}