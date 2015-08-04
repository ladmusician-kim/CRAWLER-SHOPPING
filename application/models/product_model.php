<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product_model extends CI_Model {
    public function __construct()
    {
        parent::__construct();
    }

    function get() {
        $check_option = $this->db->get_where('inno_option', array('label' => '테스트'), 10, 0);
        var_dump($check_option);
        echo '<p></p>';
        var_dump($check_option->row()->_optionid);
        echo '<p></p>';
        var_dump($check_option->row()->_optionid);

    }

    function add($data)
    {
        echo iconv('EUC-KR', 'UTF-8', $data['category']);
        $input_data = array(
            'category' => iconv('EUC-KR', 'UTF-8', trim($data['category'])),
            'label' => iconv('EUC-KR', 'UTF-8', trim($data['label'])),
            'price' => 0,
            'img_url' => trim($data['img_url']),
            'item_url' => trim($data['item_url']),
            'isdeprecated' => FALSE,
        );

        $this->db->insert('inno_product', $input_data);
        $product_id = $this->db->insert_id();

        if ($product_id && $product_id > 0) {
            foreach($data['options'] as $option) {
                $check_option = $this->db->get_where('inno_option', array('label' => $option), 10, 0)->row();
                if(count($check_option) == 0){
                    $option_data = array (
                        'label' => iconv('EUC-KR', 'UTF-8', trim($option))
                    );
                    $this->db->insert('inno_option', $option_data);
                    $option_id = $this->db->insert_id();
                    if ($option_id && $option_id > 0) {
                        $this->__insert_relation($option_id, $product_id);
                    } else return false;

                } else {
                    $this->__insert_relation($check_option->_optionid, $product_id);
                    return true;
                }
            }
        } else return false;

    }

    function __insert_relation ($option_id, $product_id) {
        $relation_data = array (
            'for_productid' => $product_id,
            'for_optionid' => $option_id
        );

        $this->db->insert('inno_producttooption', $relation_data);
        $relation_id = $this->db->insert_id();

        if ($relation_id && $relation_id > 0) {
            return false;
        } else return false;
    }
}