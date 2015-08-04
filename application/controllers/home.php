<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require($_SERVER['DOCUMENT_ROOT'].'/CRAWLER/application/libraries/Snoopy.class.php');

class Home extends CI_Controller {
    function __construct () {
        parent::__construct();
        $this->load->model('product_model');
    }

    public function index()
    {
        $snoopy = new Snoopy;

        $uri = 'http://innofun.com/mall/m_login.php';

        //�α��� ������ ������ �迭 auth�� ����ϴ�
        //�迭�� key�� �ش� �� �Ѱ��� name�� �ǰڽ��ϴ�
        $auth['email'] = 'goqual';
        $auth['password'] = 'rhznjfflxl1';

        //�������� submit�Լ��� �������� �Ѱ��ݽô�
        $snoopy->submit($uri, $auth);

        //�α��ο� ����Ͽ� ��Ű�� ����ϴ� ��찡 ������ ��Ű������ �����صӴϴ�
        $snoopy->setcookies();

        //���� �α��� ������ ������ ������ �ٽ� uri�� �����غ��ô�
        $snoopy->fetch($uri);

        var_dump($snoopy->results);
    }

    public function insert_db () {
        $snoopy = new Snoopy;

        //$this->__login($snoopy);

        $snoopy->fetch('http://innofun.com/mall/m_mall_list.php?ps_ctid=03000000&ps_ctid_2=&ps_search=&ps_company=&ps_brand=&ps_min_money=&ps_max_money=&ps_line=&sType=m&sType=m&skinmode=&ps_page=1');
        $test = explode('<table width="1000" border="0" cellspacing="0" cellpadding="4" align="center">', $snoopy->results);
        $test1 = explode('</form>', $test[1]);
        $test2 = explode('<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center" style="border-bottom:1 solid #EFEFEF">', $test1[0]);
        $index = 0;
        echo $test2[0];
        foreach($test2 as $each) {
            if ($each !== "" && $each) {
                $split_str= explode("\">", $each);

                if ($split_str && count($split_str) > 8) {-
                //var_dump($split_str);
                    $category = explode("<font", $split_str[15])[0];
                    $category = trim(explode("<b>", $category)[1]);
                    $label = explode("</a", $split_str[18])[0];
                    $cost = explode("��", $split_str[24])[0];
                    $img_split = $split_str[9]."\"/>";
                    $img_url = explode("./", $img_split);

                    //$if_option = explode("0'>", $split_str[$i]);

                    if (isset($img_url[1])) {
                        //$img = $img_url[0]."http://innofun.com/mall/".$img_url[1];
                        $img_src = "http://innofun.com/mall/".explode('" border',$img_url[1])[0];

                        $this->__handle_db_product($category, $label, $cost, $img_src);

                        $this->__option($split_str);
                        echo "<p></p>";
                    }
                }

            }
            $index++;
        }
    }

    function test () {

        $snoopy = new Snoopy;

        //$this->__login($snoopy);

        $snoopy->fetch('http://innofun.com/mall/m_mall_list.php?ps_ctid=03000000&ps_ctid_2=&ps_search=&ps_company=&ps_brand=&ps_min_money=&ps_max_money=&ps_line=&sType=m&sType=m&skinmode=&ps_page=1');

        //var_dump($arr_split_by_href);

        $split_table = '<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center" style="border-bottom:1 solid #EFEFEF">';
        $arr_split_by_table = explode($split_table, $snoopy->results);

       // var_dump($arr_split_by_table[1]);

        $split_for_href = '<a href="m_mall_detail.php';
        $split_for_img_uri = 'src="';
        $split_for_category = '<b>';
        $split_for_category_1 = '<font';
        $split_for_label = '">';

        $idx = 0;

        foreach($arr_split_by_table as $each) {
            if ($idx != 0) {
                $arr_split_by_href = explode($split_for_href, $each);

                var_dump($arr_split_by_href);

                $arr_for_category_label = explode('</b>', explode($split_for_category, $arr_split_by_href[2])[1]);

                $img_uri = explode('" border',explode($split_for_img_uri, $arr_split_by_href[1])[1])[0];
                $category = $arr_for_category_label[0];
                $category_1 = explode($split_for_category_1, $arr_for_category_label[1])[0];
                $label = explode("</a>", explode($split_for_label, $arr_split_by_href[3])[1])[0];

                //$this->__print_debug($idx, $img_uri, $category .trim($category_1), $label);
            }
            $idx++;
        }
    }

    function __print_debug ($idx, $img_uri, $category, $label) {
        echo $idx ."\n";
        echo '<p></p>';
        var_dump($img_uri);
        echo '<p></p>';
        var_dump($category);
        echo '<p></p>';
        var_dump($label);

        echo '<p></p>';
        echo '<p></p>';
        echo '<p></p>';
    }

    function __handle_db_product($category, $label, $price, $img_url) {

        $input_data = array (
            'category' => $category,
            'label' => $label,
            'price' => $price,
            'img_url' => $img_url,
        );

        $rtv = $this->product_model->add($input_data);

        if ($rtv) {
            $this->__print_data($input_data);
        } else {

        }
    }

    function __option ($split_str) {
        for($i = 32; $i < 38; $i++) {
            if(isset($split_str[$i])) {
                if (strpos($split_str[$i], '-�ɼǼ���-')) {
                    $option_idx = $i + 1;
                    while(isset($split_str[$option_idx]) && strpos($split_str[$option_idx], "</option>")) {
                        var_dump($split_str[$option_idx++]);
                        echo "<p></p>";
                    }
                }
            }
        }
    }

    function __login($snoopy) {
        $uri = 'http://innofun.com/mall/m_login.php';

        $auth['login_id'] = 'goqual';
        $auth['login_pass'] = 'rhznjfflxl1';

        $snoopy->submit($uri, $auth);

        $snoopy->setcookies();

        $snoopy->fetch($uri);
        var_dump($snoopy->results);
    }

    function __print_data ($data) {
        echo $data['category'];
        echo '<p></p>';
        echo $data['label'];
        echo '<p></p>';
        echo $data['price'];

        echo '<p></p>';
        echo '<p></p>';
        echo '<p></p>';
    }

    function login_test () {
        $snoopy = new Snoopy();
        $uri = 'http://innofun.com/mall/m_login_ok.php';

        $auth['ps_ssl'] = 1;
        $auth['morning_main_login[0].login_id'] = 'goqual';
        $auth['login_pass'] = 'rhznjfflxl1';
        $auth['morning_main_login.login_id'] = 'goqual';
        $auth['morning_main_login.login_pass'] = 'rhznjfflxl1';
        //$auth['ps_murl'] = 'https://innofun.com:455/mall/m_login_ok.php';
        //$auth['url'] = 'index.php';

        var_dump($auth);

        $snoopy->submit($uri, $auth);

        $snoopy->setcookies();

        $snoopy->fetch($uri);
        var_dump($snoopy->results);
    }

}
