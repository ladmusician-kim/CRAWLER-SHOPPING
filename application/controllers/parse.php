<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require($_SERVER['DOCUMENT_ROOT'].'/CRAWLER/application/libraries/Snoopy.class.php');

class Parse extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('product_model');
        $this->output->set_header("Content-Type: text/html; charset=UTF-8;");

    }

    public function index()
    {
        echo "test";
    }

    function parsing()
    {
        $debug_val = true;
        $snoopy = new Snoopy;

        $this->login($snoopy);

        $snoopy->fetch('http://innofun.com/mall/m_mall_list.php?ps_ctid=03000000&ps_ctid_2=&ps_search=&ps_company=&ps_brand=&ps_min_money=&ps_max_money=&ps_line=&sType=m&sType=m&skinmode=&ps_page=1');

        var_dump($snoopy->results);

        $split_table = '<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center" style="border-bottom:1 solid #EFEFEF">';
        $arr_split_by_table = explode($split_table, $snoopy->results);

        $split_for_href = '<a href="m_mall_detail.php';
        $split_for_img_uri = 'src="';
        $split_for_category = '<b>';
        $split_for_category_1 = '<font';
        $split_for_label = '">';
        $split_for_item_url = "\">";

        $idx = 0;

        foreach ($arr_split_by_table as $each) {
            if ($idx != 0) {
                $arr_split_by_href = explode($split_for_href, $each);

                $arr_for_category_label = explode('</b>', explode($split_for_category, $arr_split_by_href[2])[1]);

                $item_url = explode($split_for_item_url, $arr_split_by_href[1])[0];
                $img_uri = explode('" border', explode($split_for_img_uri, $arr_split_by_href[1])[1])[0];
                $img_uri = substr($img_uri, 1, strlen($img_uri) - 1);
                $category = $arr_for_category_label[0];
                $category_1 = explode($split_for_category_1, $arr_for_category_label[1])[0];
                $label = explode("</a>", explode($split_for_label, $arr_split_by_href[3])[1])[0];

                $options = $this->__parse_option($arr_split_by_href[3]);

                if ($debug_val == true) {
                    $this->__print_debug($idx, $item_url, $img_uri, $category . trim($category_1), $label, $options);
                } else {
                    $this->__insert_db($idx, $item_url, $img_uri, $category . trim($category_1), $label, $options);
                }
            }
            $idx++;
        }
    }

    function __parse_option($data)
    {
        $options = array();
        $total_options = explode('<select name="uid_goods_option1[]" onchange="javascript:option_view();">', $data)[1];

        $option_with_dummy = explode('</option>', $total_options);
        for ($i = 0; $i < count($option_with_dummy); $i++) {
            $each = $option_with_dummy[$i];
            if (isset(explode("\">", $each)[1]) && strlen(trim(explode("\">", $each)[1])) < 50) {
                $option = trim(explode("\">", $each)[1]);
                array_push($options, $option);
            }
        }
        return $options;
    }

    function __insert_db($idx, $item_url, $img_uri, $category, $label, $options)
    {
        $input_data = array(
            'category' => $category,
            'label' => $label,
            'img_url' => $img_uri,
            'item_url' => $item_url,
            'options' => $options
        );

        $rtv = $this->product_model->add($input_data);

        if ($rtv) {
            //$this->__print_data($input_data);
        } else {
            echo '######################';
            $this->__print_debug($idx, $item_url, $img_uri, $category, $label, $options);
        }
    }

    function __print_debug($idx, $item_url, $img_uri, $category, $label, $options)
    {
        echo $idx . "\n";
        echo '<p></p>';
        var_dump($item_url);
        echo '<p></p>';
        var_dump($img_uri);
        echo '<p></p>';
        var_dump($category);
        echo '<p></p>';
        var_dump($label);
        echo '<p></p>';
        var_dump($options);

        echo '<p></p>';
        echo '<p></p>';
        echo '<p></p>';
        echo '<p></p>';
    }

    function login($snoopy)
    {
        $vars['login_id'] = "goqual";
        $vars['login_pass'] = "rhznjfflxl1";

        $submit_url = "http://innofun.com/mall/m_login_ok.php";
        $snoopy->submit($submit_url, $vars);
        $snoopy->setcookies();
    }
}