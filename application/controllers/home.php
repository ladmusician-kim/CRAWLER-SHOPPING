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

        //로그인 정보를 저장할 배열 auth를 만듭니다
        //배열의 key는 해당 폼에서 넘겨줄 name이 되겠습니다
        $auth['email'] = 'goqual';
        $auth['password'] = 'rhznjfflxl1';

        //스누피의 submit함수로 폼정보를 넘겨줍시다
        $snoopy->submit($uri, $auth);

        //로그인에 관련하여 쿠키를 사용하는 경우가 있으니 쿠키정보를 저장해둡니다
        $snoopy->setcookies();

        //이제 로그인 정보를 가지고 있으니 다시 uri로 접속해봅시다
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
                    $cost = explode("원", $split_str[24])[0];
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
                if (strpos($split_str[$i], '-옵션선택-')) {
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

        //로그인 정보를 저장할 배열 auth를 만듭니다
        //배열의 key는 해당 폼에서 넘겨줄 name이 되겠습니다
        $auth['login_id'] = 'goqual';
        $auth['login_pass'] = 'rhznjfflxl1';

        //스누피의 submit함수로 폼정보를 넘겨줍시다
        $snoopy->submit($uri, $auth);

        //로그인에 관련하여 쿠키를 사용하는 경우가 있으니 쿠키정보를 저장해둡니다
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

}
