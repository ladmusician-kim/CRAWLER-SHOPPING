<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Home extends GQ_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        $this->__get_views('_HOME/index');
    }
}
