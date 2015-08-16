<?php
class GQ_Controller extends CI_Controller {
    function __construct() {
        parent::__construct();

        if(!$this->input->is_cli_request())
            $this->load->library('session');

        $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    }

    function __get_views($viewStr, $data = null) {
        $this->load->view('_Layout/_header.php');
        $this->load->view('_Layout/_navbar.php');

        if ($data != null) {
            $this->load->view($viewStr, $data);
        } else {
            $this->load->view($viewStr);
        }

        $this->load->view('_Layout/_footer.php');
    }

    function __get_mg_views($viewStr, $data = null) {
        $this->load->view('_Layout/header.php');
        $this->load->view('_Layout/left_mg_nav.php');
        $this->load->view('_Layout/profile_navbar.php');

        if ($data != null) {
            $this->load->view($viewStr, $data);
        } else {
            $this->load->view($viewStr);
        }

        $this->load->view('_Layout/footer.php');
    }

    function __get_partial_view($viewStr, $data=null) {
        if ($data != null) {
            $this->load->view($viewStr, $data);
        } else {
            $this->load->view($viewStr);
        }
    }


    function __require_login($return_url = "") {
        // 로그인이 되어 있지 않다면 로그인 페이지로 리다이렉션
        if(!$this->session->userdata('is_login')){
            if ($return_url == "") {
                redirect('/Auth/login');
            }
            redirect('/Auth/login?returnURL='.rawurlencode($return_url));
        }
    }

    function __require_admin_login($return_url = "") {
        // 로그인이 되어 있지 않다면 로그인 페이지로 리다이렉션
        if(!$this->session->userdata('is_login')){
            if ($return_url == "") {
                redirect('/Auth/login');
            }
            redirect('/Auth/login?returnURL='.rawurlencode($return_url));
        }

        if (!$this->session->userdata('is_admin')) {
            if ($return_url == "") {
                redirect('/Home/index');
            }
            redirect(rawurlencode($return_url));
        }
    }

    function __is_logined($return_url = "") {
        // 로그인이 되어 있지 않다면 로그인 페이지로 리다이렉션
        if($this->session->userdata('is_login')){
            if ($return_url == "") {
                redirect('/Home/index');
            }
            redirect($return_url);
        }
    }





    function handle_date ($date) {
        // 00/00/2015 -> 2015-00-00
        $arr_date = explode('/', $date);
        return $arr_date[2].'-'.$arr_date[1].'-'.$arr_date[0];
    }
}