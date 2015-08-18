<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require($_SERVER['DOCUMENT_ROOT'] . '/CRAWLER/application/libraries/Snoopy.class.php');

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

    function run ()
    {
        $ids = $this->product_model->get_only_id();

        $snoopy = new Snoopy;
        $this->login($snoopy);

        $page = 1;

        while(true) {
            $url = 'http://innofun.com/mall/m_mall_list.php?ps_ctid=03000000&ps_ctid_2=&ps_search=&ps_company=&ps_brand=&ps_min_money=&ps_max_money=&ps_line=&sType=m&sType=m&skinmode=&ps_page=';
            $snoopy->fetch($url.$page);
            $check_last_page = explode('href="m_mall_detail.php?', $snoopy->results);

            if (count($check_last_page) > 3) {
                $this->parsing($page);
                $page++;
            } else {
                break;
            }
        }
    }

    function parsing($page)
    {
        $debug_val = false;
        $snoopy = new Snoopy;

        $this->login($snoopy);

        $snoopy->fetch('http://innofun.com/mall/m_mall_list.php?ps_ctid=03000000&ps_ctid_2=&ps_search=&ps_company=&ps_brand=&ps_min_money=&ps_max_money=&ps_line=&sType=m&sType=m&skinmode=&ps_page='.$page);

        $split_table = '<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center" style="border-bottom:1 solid #EFEFEF">';

        $arr_split_by_table = explode($split_table, $snoopy->results);

        $split_for_href = '<a href="m_mall_detail.php';

        $split_no_item = '/img/p.gif';

        $idx = 0;

        foreach ($arr_split_by_table as $each) {
            if ($idx != 0) {
                $arr_split_by_href = explode($split_for_href, $each);
                if (count($arr_split_by_href) == 4) {
                    $this->__handle_put_item($idx, $debug_val, $arr_split_by_href);
                } else {
                    $check_no_item = explode($split_no_item, $arr_split_by_href[4]);
                    if (count($check_no_item) > 1) {
                        echo 'no item';
                    } else {
                        $this->__handle_put_item($idx, $debug_val, $arr_split_by_href);
                    }
                }


            }
            $idx++;
        }
    }

    function __handle_put_item ($idx, $debug_val, $arr_split_by_href)
    {
        $split_for_img_uri = 'src="';
        $split_for_category = '<b>';
        $split_for_category_1 = '<font';
        $split_for_label = '">';
        $split_for_item_url = "\">";

        $arr_for_category_label = explode('</b>', explode($split_for_category, $arr_split_by_href[2])[1]);

        $item_url = explode($split_for_item_url, $arr_split_by_href[1])[0];
        $img_uri = explode('" border', explode($split_for_img_uri, $arr_split_by_href[1])[1])[0];
        $img_uri = substr($img_uri, 1, strlen($img_uri) - 1);
        $category = $arr_for_category_label[0];
        $category_1 = explode($split_for_category_1, $arr_for_category_label[1])[0];
        $label = explode("</a>", explode($split_for_label, $arr_split_by_href[3])[1])[0];


        $price = "";
        if (count($arr_split_by_href) == 4) {
            $price = $this->__parse_price($arr_split_by_href[3]);
        } else {
            $price = $this->__parse_price($arr_split_by_href[4]);
        }

        $options = $this->__parse_option($arr_split_by_href[3]);

        if ($debug_val == true) {
            $this->__print_debug($idx, $item_url, $img_uri, $category . trim($category_1), $label, $options, $price);
        } else {
            $this->__insert_db($idx, $item_url, $img_uri, $category . trim($category_1), $label, $options, $price);
        }
    }

    function __parse_price($data)
    {
        $split_for_price = '<font color="#FF6484">';
        $price_literal_won = trim(explode('</b>', explode($split_for_price, $data)[1])[0]);
        $price_literal = str_replace('원', '', $price_literal_won);
        $price = str_replace(',', '', $price_literal);

        return $price;
    }

    function __parse_option($data)
    {
        $options = array();

        $option_list = explode('<select name="uid_goods_option1[]" onchange="javascript:option_view();">', $data);
        if (isset($option_list[1])) {
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
    }

    function __insert_db($idx, $item_url, $img_uri, $category, $label, $options, $price)
    {
        $input_data = array(
            'category' => $category,
            'label' => $label,
            'img_url' => $img_uri,
            'item_url' => $item_url,
            'options' => $options,
            'price' => $price
        );

        $rtv = $this->product_model->add($input_data);

        if ($rtv === 'NO RELATION') {
            //echo 'NO RELATION';
            echo "\n";
        } else if ($rtv == true) {
            //$this->__print_data($input_data);
            //echo 'SUCCESS';
            echo "\n";
        } else {
            //echo '#########    ERROR   #############';
            echo "\n";
            //$this->__print_debug($idx, $item_url, $img_uri, $category, $label, $options, $price);
        }
    }

    function __print_debug($idx, $item_url, $img_uri, $category, $label, $options, $price)
    {
        echo $idx;
        echo "<br>";
        var_dump($item_url);
        echo "<br>";
        var_dump($img_uri);
        echo "<br>";
        var_dump($category);
        echo "<br>";
        var_dump($label);
        echo "<br>";
        var_dump($options);
        echo "<br>";
        var_dump($price);

        echo "<br>";
        echo "<br>";
    }

    function login($snoopy)
    {
        $vars['login_id'] = "goqual";
        $vars['login_pass'] = "rhznjfflxl1";

        $submit_url = "http://innofun.com/mall/m_login_ok.php";
        $snoopy->submit($submit_url, $vars);
        $snoopy->setcookies();
    }




    /* get each item */
    function parse_each_item()
    {
        $is_degub = false;
        $test_idx = 146;
        $ids = $this->product_model->get_only_id();

        $snoopy = new Snoopy;
        $this->login($snoopy);


        if ($is_degub) {
            echo $ids[$test_idx]->item_url;
            $this->__handle_parse_each_item($snoopy, $ids[$test_idx]);
        } else {
            foreach($ids as $idx => $value) {
                $this->__handle_parse_each_item($snoopy, $value);
            }
        }
    }

    function __handle_parse_each_item($snoopy, $value)
    {
        $is_older = false;

        // 1
        $split_for_img_src ='src="http://innofun.com/detail/';
        // 2: http://innofun.com/mall/editor/upload/editor/admin/
        $split_for_img_src_older ='/editor/upload/editor/admin/';
        // 3: http://innofun.com/mall/upload/editor/
        $split_for_img_src_older_not_admin ='/upload/editor/';
        // 4: http://innofun.com/detail/art/
        $split_for_img_src_art = '/detail/art/';
        // 5: http://innofun.com/mall/shop_image/
        $split_for_img_shop_img = '/mall/shop_image/';
        // 6: http://www.innofun.com/detail/gaze/stude_gold_diamond_iphone6.jpg
        $split_for_img_detail = 'http://www.innofun.com/detail/';

        try {
            $snoopy->fetch('http://innofun.com/mall/m_mall_detail.php' . $value->item_url);
            $arr_split_by_img_src = explode($split_for_img_src, $snoopy->results);
            $is_older = 1;
            if (count($arr_split_by_img_src) == 1) {
                $arr_split_by_img_src = explode($split_for_img_src_older, $snoopy->results);
                $is_older = 2;
                if (count($arr_split_by_img_src) == 1) {
                    $arr_split_by_img_src = explode($split_for_img_src_older_not_admin, $snoopy->results);
                    $is_older = 3;
                    if (count($arr_split_by_img_src) == 1) {
                        $arr_split_by_img_src = explode($split_for_img_src_art, $snoopy->results);
                        $is_older = 4;
                        if (count($arr_split_by_img_src) == 1) {
                            $arr_split_by_img_src = explode($split_for_img_shop_img, $snoopy->results);
                            $is_older = 5;
                            if (count($arr_split_by_img_src) == 1) {
                                $arr_split_by_img_src = explode($split_for_img_detail, $snoopy->results);
                                $is_older = 6;
                            }
                        }
                    }
                }
            }

            $detail_img_url = "";

            if (isset($arr_split_by_img_src[2])) {
                $detail_img_url = explode('" />', $arr_split_by_img_src[2])[0];
            } else {
                $detail_img_url = explode('" />', $arr_split_by_img_src[1])[0];
            }

            $arr_img_detail = explode('_01.jpg', $detail_img_url);

            if (count($arr_img_detail) > 1) {
                $fixed_url = $arr_img_detail[0];
                $idx = 1;

                while (true) {
                    $detail_img_url = $fixed_url . '_0' . $idx . '.jpg';
                    $snoopy->fetch('http://innofun.com/detail/' . $detail_img_url);
                    $rtv_length = strlen($snoopy->results);
                    if ($rtv_length < 400) {
                        break;
                    } else {
                        $real_detail_img_url = $this->__check_real_detail_img($detail_img_url);
                        $this->product_model->put_detail_img($value->_productid, $real_detail_img_url, $is_older);
                        $idx++;
                    }
                }
            } else {
                $real_detail_img_url = $this->__check_real_detail_img($detail_img_url);
                $this->product_model->put_detail_img($value->_productid, $real_detail_img_url, $is_older);
            }
        } catch(Exception $e) {

        }
    }

    function __check_real_detail_img($url)
    {
        $split_png = explode('.png', $url);
        $split_jpg = explode('.jpg', $url);
        $split_jpeg = explode('.jpeg', $url);

        if (count($split_png) > 1) {
            return $split_png[0].'.png';
        }
        if (count($split_jpg) > 1) {
            return $split_jpg[0].'.jpg';
        }
        if (count($split_jpeg) > 1) {
            return $split_jpeg[0].'jpeg';
        }

        var_dump($url);
    }


    function delete_all_data()
    {
        $this->product_model->delete_all_data();
    }
    function delete_all_detail_data()
    {
        $this->product_model->delete_all_detail_data();
    }





    function test()
    {
        $page = 36;
        $debug_val = false;
        $snoopy = new Snoopy;

        $this->login($snoopy);

        $snoopy->fetch('http://innofun.com/mall/m_mall_list.php?ps_ctid=03000000&ps_ctid_2=&ps_search=&ps_company=&ps_brand=&ps_min_money=&ps_max_money=&ps_line=&sType=m&sType=m&skinmode=&ps_page='.$page);

        $split_table = '<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center" style="border-bottom:1 solid #EFEFEF">';

        $arr_split_by_table = explode($split_table, $snoopy->results);

        $split_for_href = '<a href="m_mall_detail.php';

        $split_no_item = '/img/p.gif';

        $idx = 0;

        foreach ($arr_split_by_table as $each) {
            if ($idx != 0) {
                $arr_split_by_href = explode($split_for_href, $each);
                if (count($arr_split_by_href) == 4) {
                    $this->__handle_put_item($idx, $debug_val, $arr_split_by_href);
                } else {
                    $check_no_item = explode($split_no_item, $arr_split_by_href[4]);
                    if (count($check_no_item) > 1) {
                        echo 'no item';
                    } else {
                        $this->__handle_put_item($idx, $debug_val, $arr_split_by_href);
                    }
                }


            }
            $idx++;
        }
    }
}