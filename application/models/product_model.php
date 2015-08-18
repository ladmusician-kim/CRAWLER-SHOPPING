<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Product_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    function get_items($page = 1, $per_page = 12)
    {
        $base_dto = new BASE_DTO;


        if ($page === 1) {
            $this->db->limit($per_page);

        } else {
            $this->db->limit($per_page, ($page - 1) * $per_page);
        }

        $this->db->select('*');
        $this->db->from('inno_product');
        $this->db->where('inno_product.isdeprecated = false');
        $this->db->order_by("inno_product._productid", "asc");
        //$this->db->order_by("inno_product._productid", "desc");

        $result = $this->db->get()->result();

        $base_dto->set_value($result, true);

        return $base_dto;
    }

    function get_all_count()
    {
        return $this->db->count_all_results('inno_product');
    }

    function get_by_id($product_id)
    {
        $this->db->select('*');
        $this->db->where(array('_productid' => $product_id));
        $rtv = $this->db->get('inno_product');
        $designers = array_shift($rtv->result());

        return $designers;
    }

    function get_detail_imgs($product_id)
    {
        $this->db->select('*');
        $this->db->where(array('for_productid' => $product_id));
        $rtv = $this->db->get('inno_producttodetail');
        return $rtv->result();
    }

    function get_options_by_productid($product_id)
    {
        $this->db->select('*');
        $this->db->from('inno_producttooption');
        $this->db->where(array('for_productid' => $product_id));
        $this->db->join('inno_option', 'inno_option._optionid = inno_producttooption.for_optionid');
        $rtv = $this->db->get();

        return $rtv->result();
    }













    /* parsing for detail page */
    function get_only_id()
    {
        $this->db->select('_productid, item_url');
        $this->db->from('inno_product');
        $this->db->where('inno_product.isdeprecated = false');
        $this->db->order_by("inno_product._productid", "desc");

        return $this->db->get()->result();
    }

    function put_detail_img($productid, $detail_img_url, $is_older)
    {
        $input_data = array(
            'for_productid' => $productid,
            'detail_url' => $detail_img_url,
            'is_older' => $is_older
        );

        $this->db->insert('inno_producttodetail', $input_data);
        $product_id = $this->db->insert_id();
    }

    /* parsing for all item per page */
    function add($data)
    {
        $img_small_url = trim($data['img_url']);
        $arr_split_img_small_url = explode('1_', $img_small_url);
        $img_big_url = "";
        if (count($arr_split_img_small_url) == 2) {
            $img_big_url = $arr_split_img_small_url[0].'1_' .$arr_split_img_small_url[1];
        } else {
            foreach($arr_split_img_small_url as $idx => $value) {
                if ($idx <= 1) {
                    $img_big_url = $img_big_url .$value;
                } else {
                    $img_big_url = $img_big_url.'1_' .$value;
                }
            }
        }


        $input_data = array(
            'category' => iconv('EUC-KR', 'UTF-8', trim($data['category'])),
            'label' => iconv('EUC-KR', 'UTF-8', trim($data['label'])),
            'price' => iconv('EUC-KR', 'UTF-8', trim($data['price'])),
            'img_small_url' => $img_small_url,
            'img_big_url' => $img_big_url,
            'item_url' => trim($data['item_url']),
            'isdeprecated' => FALSE,
        );

        $this->db->insert('inno_product', $input_data);
        $product_id = $this->db->insert_id();

        return $this->__add_option($product_id, $data);
    }

    function __add_option($product_id, $data)
    {
        if ($product_id && $product_id > 0) {
            if ($data['options'] != null && count($data['options']) > 0) {
                foreach ($data['options'] as $option) {
                    $check_option = $this->db->get_where('inno_option', array('label' => iconv('EUC-KR', 'UTF-8', trim($option))), 10, 0)->row();

                    //var_dump($check_option);
                    if (count($check_option) == 0) {

                        $option_data = array(
                            'label' => iconv('EUC-KR', 'UTF-8', trim($option))
                        );
                        $this->db->insert('inno_option', $option_data);
                        $option_id = $this->db->insert_id();
                        if ($option_id && $option_id > 0) {
                            $rtv = $this->__insert_relation($option_id, $product_id);
                        } else {
                            echo " ERROR IN INSERT OPTION ";
                            echo "\n";
                        }
                    } else {
                        $this->__insert_relation($check_option->_optionid, $product_id);
                    }

                }
                return true;
            } else {
                //return false;
                return 'NO RELATION';
            }
        } else {
            return false;
        }
    }

    function __insert_relation($option_id, $product_id)
    {
        $relation_data = array(
            'for_productid' => $product_id,
            'for_optionid' => $option_id
        );

        $this->db->insert('inno_producttooption', $relation_data);
        $relation_id = $this->db->insert_id();

        if ($relation_id && $relation_id > 0)
            return true;
        else
            return false;
    }


    function delete_all_data()
    {
        $this->db->select('_productid');
        $productids = $this->db->get('inno_product')->result();

        foreach ($productids as $id) {
            $this->db->delete('inno_product', array('_productid' => $id->_productid));
        }

        $this->db->select('_optionid');
        $optionids = $this->db->get('inno_option')->result();

        foreach ($optionids as $id) {
            $this->db->delete('inno_option', array('_optionid' => $id->_optionid));
        }

        $this->db->select('for_productid');
        $relationids = $this->db->get('inno_producttooption')->result();

        foreach ($relationids as $id) {
            $this->db->delete('inno_producttooption', array('for_productid' => $id->for_productid));
        }
    }
    function delete_all_detail_data()
    {
        $this->db->select('_detailid');
        $ids = $this->db->get('inno_producttodetail')->result();

        foreach ($ids as $id) {
            $this->db->delete('inno_producttodetail', array('_detailid' => $id->_detailid));
        }
    }
}