<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product_model extends CI_Model {
    public function __construct()
    {
        parent::__construct();
    }

    function add($data) {
        $input_data = array(
            'category'     =>  $data['category'],
            'label'    =>  $data['label'],
            'price'    =>  $data['price'],
            'img_url'    =>  $data['img_url'],
            'isdeprecated' => FALSE,
        );

        $this->db->insert('inno_product', $input_data);
        $result = $this->db->insert_id();

        return $result;
    }
}