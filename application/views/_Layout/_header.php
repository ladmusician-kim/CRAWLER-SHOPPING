<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <meta content="initial-scale=1.0, width=device-width" name="viewport">
    <title>CRAWLER</title>

    <!-- css -->
    <link href="<?php echo base_url()?>library/package/material/css/base.min.css" rel="stylesheet">
    <link href="<?php echo base_url()?>library/css/common.css" rel="stylesheet">

    <?php
    $total_url = $_SERVER['PHP_SELF'];
    $arr_splitted_url = explode('/', $total_url);

    $ctrl_name = $arr_splitted_url[count($arr_splitted_url) - 2];
    $view_name = $arr_splitted_url[count($arr_splitted_url) - 1];
    $filename = "";

    if ($ctrl_name == 'index.php') {
        $filename = 'library/css/'.strtolower($view_name).'/index.css';
    } else {
        $filename = 'library/css/'.strtolower($ctrl_name).'/'.strtolower($view_name).'.css';
    }

    if(file_exists($filename)) {
        ?>
        <link href="/CRAWLER/<?php echo $filename; ?>" rel="stylesheet">

        <?php
    }
    if (strpos($filename, 'project_create')) {
        ?>
        <link href="http://daemonite.github.io/material/css/project.min.css" rel="stylesheet">
        <?php
    }
    ?>

    <!-- favicon -->
    <!-- ... -->

    <!-- ie -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="avoid-fout">
<?php
$flashdata = $this->session->flashdata('message');
if ($flashdata != null) {
    ?>

    <script type="text/javascript">
        alert('<?=$this->session->flashdata('message')?>');
    </script>
    <?php
}
?>
<div class="avoid-fout-indicator avoid-fout-indicator-fixed">
    <div class="progress-circular progress-circular-alt progress-circular-center">
        <div class="progress-circular-wrapper">
            <div class="progress-circular-inner">
                <div class="progress-circular-left">
                    <div class="progress-circular-spinner"></div>
                </div>
                <div class="progress-circular-gap"></div>
                <div class="progress-circular-right">
                    <div class="progress-circular-spinner"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<header class="header">
    <ul class="nav nav-list pull-left">
        <li>
            <a data-toggle="menu" href="#menu">
                <span class="icon icon-lg">menu</span>
            </a>
        </li>
    </ul>
    <a class="header-logo" href="<?=site_url('/Home/index')?>">CRAWLER</a>
    <ul class="nav nav-list pull-right">
        <?php
        $isLogin = $this->session->userdata('is_login');
        if(!$isLogin) {
            ?>
            <li>
                <a href="/CRAWLER/Auth/register">가입하기</a>
            </li>
            <li>
                <a href="/CRAWLER/Auth/login?returnURL=<?php echo rawurlencode(substr($_SERVER['REQUEST_URI'], 14))?>">로그인</a>
            </li>
            <?php
        } else {
            ?>
            <li>
                <a data-toggle="menu" href="#profile">
                    <span class="access-hide">John Smith</span>
                    <span class="avatar"><img alt="alt text for John Smith avatar" src="/CRAWLER/library/img/avatar-001.jpg"></span>
                </a>
            </li>
            <?php
        }
        ?>
    </ul>
</header>

