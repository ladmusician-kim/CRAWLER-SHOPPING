<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Product extends GQ_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('product_model');
    }

    function index()
    {
        $this->__get_views('_PRODUCT/index');
    }

    function detail()
    {
        $product_id = $this->input->get('productid');

        $product = $this->product_model->get_by_id($product_id);
        $detail_imgs = $this->product_model->get_detail_imgs($product_id);
        $options = $this->product_model->get_options_by_productid($product_id);

        $this->__get_views('_PRODUCT/detail', array ( 'item' => $product, 'detail_imgs' => $detail_imgs, 'options' => $options ));
    }
}
