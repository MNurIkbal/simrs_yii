<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\web\View;
use yii\helpers\Url;

// $this->title = $judulLayarAntrian;
$this->context->layout = 'display-antrian';
$arrLoket = '';

// $minLoket = count($loketList) >= 3 ? 0 : 3 - count($loketList);
?>

<style>

    #loader {
        background-position: center center;
        /* top: 50%;
        bottom: 50%;
        padding: 50%; */
        position: fixed;
        margin-top:450px;
        margin-left:750px;
        z-index: 9999999;
    }
    .listGroupKelas { 
        padding-top: 80px; 
        width: auto;
        /*z-index: 9999;*/
    }
    .panel {
        background-color: #e8ebff;
    }
    .groupKamar {
        margin-top: 0px; /*50px*/
        padding-left: 1px;
        padding-right: 1px;
        margin-bottom: 50px;
    }
    .detailKamar {
        min-height: auto;
        /*min-height: 800px;*/
        height: auto;
    }
    .separator {
        margin-top: -60px !important;
    }
    .bg-panel {
        background-position: center;
        background-size: cover;
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        height: 80px;
    }
    .imgDetail {    
        padding-right: 0px;
        padding-left: 0px;
        margin-right: 8px;
    }
    #loading {
      display: block;
      position: absolute;
      top: 0;
      left: 0;
      z-index: 100;
      width: 100vw;
      height: 100vh;
      background-color: rgba(192, 192, 192, 0.5);
      background-image: url("https://i.stack.imgur.com/MnyxU.gif");
      background-repeat: no-repeat;
      background-position: center;
    }
    nav#mainNav {
        /*background: rgba(56,142,60,1); */
        /*#0d3d5f;*/
    }
    .navbar-default .navbar-nav > li > a:hover, .navbar-default .navbar-nav > li > a:focus {
        color: #606060;
        background-color: #00CC66; /*rgba(76,175,80,1);*/
    }
   .navbar-default .navbar-nav > .active > a, .navbar-default .navbar-nav > .active > a:hover, .navbar-default .navbar-nav > .active > a:focus {
        color: #606060;
        background-color: rgba(76,175,80,1);
    }
    .panel-primary > .panel-heading {
        color: #fff;
        background-color: rgba(59,156,60,1);
        border-color: #2196F3;
    }
    .panel-footer {
        color: #606060;
    }
    .navbar-default.navbar-fixed-bottom {
        /*max-height: 55px;*/
        padding: 10px;
        margin: 5px;
    }
    div#clock1 {
        font-size: 12px;
    }
    #bs-example-navbar-collapse-1, li{
        font-size: 15px;
        font: -webkit-mini-control;
        font-family: "Roboto", Helvetica Neue, Helvetica, Arial, sans-serif;
        color: #000;
        background-color: #f8f8f8;
    }
    #bs-example-navbar-collapse-1, p{
        font-size:14px;
    }
    .square {
        width: 30px !important;
        height: 30px !important;
    }
    .set-bed {
        position: relative;
        min-height: 1px;
        padding: 2px;
        display: inline-flex;
    }
    .color-bed {
        padding: 10px;
        margin: auto;
    }
</style>
<!-- Navigation -->
<nav id="mainNav" class="navbar navbar-default navbar-fixed-top topnav" role="navigation">
    <div class="container topnav">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header page-scroll">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1-header">
                <span class="sr-only">Toggle navigation</span> Menu <i class="fa fa-bars"></i>
            </button>            
            <a class="navbar-brand topnav" style="font-weight: bold;">
                Dashboard Kamar Rawat Inap - <?= Yii::$app->docoVars->identity("nama_rumahsakit") ?>
            </a> 
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1-header">
            <ul class="nav navbar-nav navbar-right">
                    <li class="">
                        <a class="page-scroll" href="#groupKelas-1"><?php echo "ICU"; ?></a>
                    </li>
                    <li class="">
                        <a class="page-scroll" href="#groupKelas-2"><?php echo "VIP"; ?></a>
                    </li>
                    <li class="">
                        <a class="page-scroll" href="#groupKelas-3"><?php echo "Kelas III"; ?></a>
                    </li>
                    <li class="">
                        <a class="page-scroll" href="#groupKelas-4"><?php echo "Kelas II"; ?></a>
                    </li>
                    <li class="">
                        <a class="page-scroll" href="#groupKelas-5"><?php echo "Kelas I"; ?></a>
                    </li>
                    <li class="">
                        <a class="page-scroll" href="#groupKelas-6"><?php echo "ISOLASI"; ?></a>
                    </li>
                    <li class="">
                        <a class="page-scroll" href="#groupKelas-7"><?php echo "PERINATOLOGI"; ?></a>
                    </li>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container-fluid -->
</nav>
<div class='col-md-12 col-lg-12' id="afterLoad" style="margin-top: 0px; display: none;">
        <div class='row' id="groupKelas-1">
            <div class="col-md-3 groupKamar" id="part-1">
                <div class="panel panel-primary group-1" data-id-kamar-detail="["485","486","487","488","489","490","491","492","493","494","495","496","497","498","499","500","597","598","599"]" onclick="showDetail(39);">
                    <div class="panel-heading">
                        <h5>
                            <?php echo "ICU" ?>
                            <small>
                                <div id="baseUrl">
                                    <?php //echo Yii::app()->getBaseUrl(); ?>
                                </div>
                            </small>        
                        </h5>
                    </div>
                    <div class="panel-body detailKamar">
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel'
                                        id=<?php echo '485' ?>
                                        data-status='<?php echo "terpakai_wanita"; ?>'
                                        style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                    <h5 style="padding-top: 40px;font-size: 15px;"><?php echo "01"; ?></h5>
                                </section> 
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel'
                                        id=<?php echo '485' ?>
                                        data-status='<?php echo "terpakai_wanita"; ?>'
                                        style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                    <h5 style="padding-top: 40px;font-size: 15px;"><?php echo "02"; ?></h5>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel'
                                        id=<?php echo '485' ?>
                                        data-status='<?php echo "terpakai_wanita"; ?>'
                                        style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/kosong_flexibel.jpg);">
                                    <h5 style="padding-top: 40px;font-size: 15px;"><?php echo "03"; ?></h5>
                                </section> 
                            </div>
                    </div>
                    <div class="panel-footer" style="background-color: #407e7a;border-color: #407e7a;">
                        <div class="text-left" style="margin-left: 15px;">
                            <div id="clock1" class="label label-rounded label-default">Jumlah : 03</div>
                            <div id="clock1" class="label label-rounded label-default">Terisi : 02</div>
                            <div id="clock1" class="label label-rounded label-default">Kosong : 01</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class='row' id="groupKelas-2">
            <div class="col-md-3 groupKamar" id="part-1">
                <div class="panel panel-primary group-1" data-id-kamar-detail="["485","486","487","488","489","490","491","492","493","494","495","496","497","498","499","500","597","598","599"]" onclick="showDetail(39);">
                    <div class="panel-heading">
                        <h5>
                            <?php echo "NIFAS ANGGREK" ?>
                            <small>
                                <div id="baseUrl">
                                    <?php //echo Yii::app()->getBaseUrl(); ?>
                                </div>
                            </small>        
                        </h5>
                    </div>
                    <div class="panel-body detailKamar">
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/kosong_flexibel.jpg);">
                                <h5 style="padding-top: 40px;font-size: 15px;"><?php echo "01"; ?></h5>
                            </section> 
                        </div>
                    </div>
                    <div class="panel-footer" style="background-color: #407e7a;border-color: #407e7a;">
                        <div class="text-left" style="margin-left: 15px;">
                            <div id="clock1" class="label label-rounded label-default">Jumlah : 01</div>
                            <div id="clock1" class="label label-rounded label-default">Terisi : 0</div>
                            <div id="clock1" class="label label-rounded label-default">Kosong : 01</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 groupKamar" id="part-2">
                <div class="panel panel-primary group-1" data-id-kamar-detail="["485","486","487","488","489","490","491","492","493","494","495","496","497","498","499","500","597","598","599"]" onclick="showDetail(40);">
                    <div class="panel-heading">
                        <h5>
                            <?php echo "RAWAT INAP ANGGREK" ?>
                            <small>
                                <div id="baseUrl">
                                    <?php //echo Yii::app()->getBaseUrl(); ?>
                                </div>
                            </small>        
                        </h5>
                    </div>
                    <div class="panel-body detailKamar">
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/kosong_flexibel.jpg);">
                                <h5 style="padding-top: 40px;font-size: 15px;"><?php echo "01"; ?></h5>
                            </section> 
                        </div>
                    </div>
                    <div class="panel-footer" style="background-color: #407e7a;border-color: #407e7a;">
                        <div class="text-left" style="margin-left: 15px;">
                            <div id="clock1" class="label label-rounded label-default">Jumlah : 01</div>
                            <div id="clock1" class="label label-rounded label-default">Terisi : 0</div>
                            <div id="clock1" class="label label-rounded label-default">Kosong : 01</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class='row' id="groupKelas-3">
            <div class="col-md-3 groupKamar" id="part-1">
                <div class="panel panel-primary group-1" data-id-kamar-detail="["485","486","487","488","489","490","491","492","493","494","495","496","497","498","499","500","597","598","599"]" onclick="showDetail(39);">
                    <div class="panel-heading">
                        <h5>
                            <?php echo "NIFAS CEMPAKA 2" ?>
                            <small>
                                <div id="baseUrl">
                                    <?php //echo Yii::app()->getBaseUrl(); ?>
                                </div>
                            </small>        
                        </h5>
                    </div>
                    <div class="panel-body detailKamar">
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px;font-size: 15px;"><?php echo "01"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "02"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "03"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "04"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "05"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "06"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "07"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "08"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "09"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "10"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "11"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "12"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "13"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "14"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "15"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "16"; ?></h5>
                            </section> 
                        </div>
                    </div>
                    <div class="panel-footer" style="background-color: #407e7a;border-color: #407e7a;">
                        <div class="text-left" style="margin-left: 15px;">
                            <div id="clock1" class="label label-rounded label-default">Jumlah : 16</div>
                            <div id="clock1" class="label label-rounded label-default">Terisi : 16</div>
                            <div id="clock1" class="label label-rounded label-default">Kosong : 0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 groupKamar" id="part-2">
                <div class="panel panel-primary group-2" data-id-kamar-detail="["485","486","487","488","489","490","491","492","493","494","495","496","497","498","499","500","597","598","599"]" onclick="showDetail(39);">
                    <div class="panel-heading">
                        <h5>
                            <?php echo "RAWAT INAP CEMPAKA 2" ?>
                            <small>
                                <div id="baseUrl">
                                    <?php //echo Yii::app()->getBaseUrl(); ?>
                                </div>
                            </small>        
                        </h5>
                    </div>
                    <div class="panel-body detailKamar">
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px;font-size: 15px;"><?php echo "01"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "02"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "03"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "04"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "05"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "06"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "07"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "08"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "09"; ?></h5>
                            </section> 
                        </div>
                    </div>
                    <div class="panel-footer" style="background-color: #407e7a;border-color: #407e7a;">
                        <div class="text-left" style="margin-left: 15px;">
                            <div id="clock1" class="label label-rounded label-default">Jumlah : 09</div>
                            <div id="clock1" class="label label-rounded label-default">Terisi : 09</div>
                            <div id="clock1" class="label label-rounded label-default">Kosong : 0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 groupKamar" id="part-3">
                <div class="panel panel-primary group-3" data-id-kamar-detail="["485","486","487","488","489","490","491","492","493","494","495","496","497","498","499","500","597","598","599"]" onclick="showDetail(39);">
                    <div class="panel-heading">
                        <h5>
                            <?php echo "DAHLIA" ?>
                            <small>
                                <div id="baseUrl">
                                    <?php //echo Yii::app()->getBaseUrl(); ?>
                                </div>
                            </small>        
                        </h5>
                    </div>
                    <div class="panel-body detailKamar">
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px;font-size: 15px;"><?php echo "01"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "02"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "03"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "04"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/kosong_flexibel.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "05"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/kosong_flexibel.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "06"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/kosong_flexibel.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "07"; ?></h5>
                            </section> 
                        </div>
                    </div>
                    <div class="panel-footer" style="background-color: #407e7a;border-color: #407e7a;">
                        <div class="text-left" style="margin-left: 15px;">
                            <div id="clock1" class="label label-rounded label-default">Jumlah : 07</div>
                            <div id="clock1" class="label label-rounded label-default">Terisi : 04</div>
                            <div id="clock1" class="label label-rounded label-default">Kosong : 03</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 groupKamar" id="part-4">
                <div class="panel panel-primary group-4" data-id-kamar-detail="["485","486","487","488","489","490","491","492","493","494","495","496","497","498","499","500","597","598","599"]" onclick="showDetail(39);">
                    <div class="panel-heading">
                        <h5>
                            <?php echo "RAWAT INAP CEMPAKA 2" ?>
                            <small>
                                <div id="baseUrl">
                                    <?php //echo Yii::app()->getBaseUrl(); ?>
                                </div>
                            </small>        
                        </h5>
                    </div>
                    <div class="panel-body detailKamar">
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px;font-size: 15px;"><?php echo "01"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "02"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "03"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "04"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/kosong_flexibel.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "05"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/kosong_flexibel.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "06"; ?></h5>
                            </section> 
                        </div>
                    </div>
                    <div class="panel-footer" style="background-color: #407e7a;border-color: #407e7a;">
                        <div class="text-left" style="margin-left: 15px;">
                            <div id="clock1" class="label label-rounded label-default">Jumlah : 06</div>
                            <div id="clock1" class="label label-rounded label-default">Terisi : 04</div>
                            <div id="clock1" class="label label-rounded label-default">Kosong : 02</div>
                        </div>
                    </div>
                </div>
            </div>              
        </div>
        <div class='row' id="groupKelas-4">
            <div class="col-md-3 groupKamar" id="part-1">
                <div class="panel panel-primary group-1" data-id-kamar-detail="["485","486","487","488","489","490","491","492","493","494","495","496","497","498","499","500","597","598","599"]" onclick="showDetail(39);">
                    <div class="panel-heading">
                        <h5>
                            <?php echo "NIFAS FLAMBOYAN" ?>
                            <small>
                                <div id="baseUrl">
                                    <?php //echo Yii::app()->getBaseUrl(); ?>
                                </div>
                            </small>        
                        </h5>
                    </div>
                    <div class="panel-body detailKamar">
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px;font-size: 15px;"><?php echo "01"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "02"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "03"; ?></h5>
                            </section> 
                        </div>
                    </div>
                    <div class="panel-footer" style="background-color: #407e7a;border-color: #407e7a;">
                        <div class="text-left" style="margin-left: 15px;">
                            <div id="clock1" class="label label-rounded label-default">Jumlah : 03</div>
                            <div id="clock1" class="label label-rounded label-default">Terisi : 03</div>
                            <div id="clock1" class="label label-rounded label-default">Kosong : 0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 groupKamar" id="part-2">
                <div class="panel panel-primary group-2" data-id-kamar-detail="["485","486","487","488","489","490","491","492","493","494","495","496","497","498","499","500","597","598","599"]" onclick="showDetail(39);">
                    <div class="panel-heading">
                        <h5>
                            <?php echo "RAWAT INAP FLAMBOYAN" ?>
                            <small>
                                <div id="baseUrl">
                                    <?php //echo Yii::app()->getBaseUrl(); ?>
                                </div>
                            </small>        
                        </h5>
                    </div>
                    <div class="panel-body detailKamar">
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px;font-size: 15px;"><?php echo "01"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/kosong_flexibel.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "02"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/kosong_flexibel.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "03"; ?></h5>
                            </section> 
                        </div>
                    </div>
                    <div class="panel-footer" style="background-color: #407e7a;border-color: #407e7a;">
                        <div class="text-left" style="margin-left: 15px;">
                            <div id="clock1" class="label label-rounded label-default">Jumlah : 03</div>
                            <div id="clock1" class="label label-rounded label-default">Terisi : 01</div>
                            <div id="clock1" class="label label-rounded label-default">Kosong : 02</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 groupKamar" id="part-3">
                <div class="panel panel-primary group-3" data-id-kamar-detail="["485","486","487","488","489","490","491","492","493","494","495","496","497","498","499","500","597","598","599"]" onclick="showDetail(39);">
                    <div class="panel-heading">
                        <h5>
                            <?php echo "BOUGENVILE" ?>
                            <small>
                                <div id="baseUrl">
                                    <?php //echo Yii::app()->getBaseUrl(); ?>
                                </div>
                            </small>        
                        </h5>
                    </div>
                    <div class="panel-body detailKamar">
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px;font-size: 15px;"><?php echo "01"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px;font-size: 15px;"><?php echo "02"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "03"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/kosong_flexibel.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "04"; ?></h5>
                            </section> 
                        </div>
                    </div>
                    <div class="panel-footer" style="background-color: #407e7a;border-color: #407e7a;">
                        <div class="text-left" style="margin-left: 15px;">
                            <div id="clock1" class="label label-rounded label-default">Jumlah : 04</div>
                            <div id="clock1" class="label label-rounded label-default">Terisi : 03</div>
                            <div id="clock1" class="label label-rounded label-default">Kosong : 01</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 groupKamar" id="part-4">
                <div class="panel panel-primary group-4" data-id-kamar-detail="["485","486","487","488","489","490","491","492","493","494","495","496","497","498","499","500","597","598","599"]" onclick="showDetail(39);">
                    <div class="panel-heading">
                        <h5>
                            <?php echo "TULIP" ?>
                            <small>
                                <div id="baseUrl">
                                    <?php //echo Yii::app()->getBaseUrl(); ?>
                                </div>
                            </small>        
                        </h5>
                    </div>
                    <div class="panel-body detailKamar">
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px;font-size: 15px;"><?php echo "01"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/kosong_flexibel.jpg);">
                                <h5 style="padding-top: 40px;font-size: 15px;"><?php echo "02"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/kosong_flexibel.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "03"; ?></h5>
                            </section> 
                        </div>
                    </div>
                    <div class="panel-footer" style="background-color: #407e7a;border-color: #407e7a;">
                        <div class="text-left" style="margin-left: 15px;">
                            <div id="clock1" class="label label-rounded label-default">Jumlah : 03</div>
                            <div id="clock1" class="label label-rounded label-default">Terisi : 01</div>
                            <div id="clock1" class="label label-rounded label-default">Kosong : 02</div>
                        </div>
                    </div>
                </div>
            </div>              
        </div>
        <div class='row' id="groupKelas-5">
            <div class="col-md-3 groupKamar" id="part-1">
                <div class="panel panel-primary group-1" data-id-kamar-detail="["485","486","487","488","489","490","491","492","493","494","495","496","497","498","499","500","597","598","599"]" onclick="showDetail(39);">
                    <div class="panel-heading">
                        <h5>
                            <?php echo "NIFAS MAWAR" ?>
                            <small>
                                <div id="baseUrl">
                                    <?php //echo Yii::app()->getBaseUrl(); ?>
                                </div>
                            </small>        
                        </h5>
                    </div>
                    <div class="panel-body detailKamar">
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px;font-size: 15px;"><?php echo "01"; ?></h5>
                            </section> 
                        </div>
                    </div>
                    <div class="panel-footer" style="background-color: #407e7a;border-color: #407e7a;">
                        <div class="text-left" style="margin-left: 15px;">
                            <div id="clock1" class="label label-rounded label-default">Jumlah : 01</div>
                            <div id="clock1" class="label label-rounded label-default">Terisi : 01</div>
                            <div id="clock1" class="label label-rounded label-default">Kosong : 0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 groupKamar" id="part-2">
                <div class="panel panel-primary group-2" data-id-kamar-detail="["485","486","487","488","489","490","491","492","493","494","495","496","497","498","499","500","597","598","599"]" onclick="showDetail(39);">
                    <div class="panel-heading">
                        <h5>
                            <?php echo "RAWAT INAP MAWAR" ?>
                            <small>
                                <div id="baseUrl">
                                    <?php //echo Yii::app()->getBaseUrl(); ?>
                                </div>
                            </small>        
                        </h5>
                    </div>
                    <div class="panel-body detailKamar">
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px;font-size: 15px;"><?php echo "01"; ?></h5>
                            </section> 
                        </div>
                    </div>
                    <div class="panel-footer" style="background-color: #407e7a;border-color: #407e7a;">
                        <div class="text-left" style="margin-left: 15px;">
                            <div id="clock1" class="label label-rounded label-default">Jumlah : 01</div>
                            <div id="clock1" class="label label-rounded label-default">Terisi : 01</div>
                            <div id="clock1" class="label label-rounded label-default">Kosong : 0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 groupKamar" id="part-3">
                <div class="panel panel-primary group-3" data-id-kamar-detail="["485","486","487","488","489","490","491","492","493","494","495","496","497","498","499","500","597","598","599"]" onclick="showDetail(39);">
                    <div class="panel-heading">
                        <h5>
                            <?php echo "NAFAS MELATI" ?>
                            <small>
                                <div id="baseUrl">
                                    <?php //echo Yii::app()->getBaseUrl(); ?>
                                </div>
                            </small>        
                        </h5>
                    </div>
                    <div class="panel-body detailKamar">
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px;font-size: 15px;"><?php echo "01"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px;font-size: 15px;"><?php echo "02"; ?></h5>
                            </section> 
                        </div>
                    </div>
                    <div class="panel-footer" style="background-color: #407e7a;border-color: #407e7a;">
                        <div class="text-left" style="margin-left: 15px;">
                            <div id="clock1" class="label label-rounded label-default">Jumlah : 02</div>
                            <div id="clock1" class="label label-rounded label-default">Terisi : 02</div>
                            <div id="clock1" class="label label-rounded label-default">Kosong : 0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 groupKamar" id="part-4">
                <div class="panel panel-primary group-4" data-id-kamar-detail="["485","486","487","488","489","490","491","492","493","494","495","496","497","498","499","500","597","598","599"]" onclick="showDetail(39);">
                    <div class="panel-heading">
                        <h5>
                            <?php echo "RAWAT INAP MELATI" ?>
                            <small>
                                <div id="baseUrl">
                                    <?php //echo Yii::app()->getBaseUrl(); ?>
                                </div>
                            </small>        
                        </h5>
                    </div>
                    <div class="panel-body detailKamar">
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px;font-size: 15px;"><?php echo "01"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/terpakai_pria.jpg);">
                                <h5 style="padding-top: 40px;font-size: 15px;"><?php echo "02"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/kosong_flexibel.jpg);">
                                <h5 style="padding-top: 40px"><?php echo "03"; ?></h5>
                            </section> 
                        </div>
                    </div>
                    <div class="panel-footer" style="background-color: #407e7a;border-color: #407e7a;">
                        <div class="text-left" style="margin-left: 15px;">
                            <div id="clock1" class="label label-rounded label-default">Jumlah : 03</div>
                            <div id="clock1" class="label label-rounded label-default">Terisi : 02</div>
                            <div id="clock1" class="label label-rounded label-default">Kosong : 01</div>
                        </div>
                    </div>
                </div>
            </div>              
        </div>
        <div class='row' id="groupKelas-6">
            <div class="col-md-3 groupKamar" id="part-1">
                <div class="panel panel-primary group-1" data-id-kamar-detail="["485","486","487","488","489","490","491","492","493","494","495","496","497","498","499","500","597","598","599"]" onclick="showDetail(39);">
                    <div class="panel-heading">
                        <h5>
                            <?php echo "RAFLESIA" ?>
                            <small>
                                <div id="baseUrl">
                                    <?php //echo Yii::app()->getBaseUrl(); ?>
                                </div>
                            </small>        
                        </h5>
                    </div>
                    <div class="panel-body detailKamar">
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/kosong_flexibel.jpg);">
                                <h5 style="padding-top: 40px;font-size: 15px;"><?php echo "01"; ?></h5>
                            </section> 
                        </div>
                        <div class='col-md-2 imgDetail'>
                            <section class='panel text-center bg-panel'
                                    id=<?php echo '485' ?>
                                    data-status='<?php echo "terpakai_wanita"; ?>'
                                    style="background-image: url(https://sim.rsmmbogor.com/images/dashboard_kamar/kosong_flexibel.jpg);">
                                <h5 style="padding-top: 40px;font-size: 15px;"><?php echo "01"; ?></h5>
                            </section> 
                        </div>
                    </div>
                    <div class="panel-footer" style="background-color: #407e7a;border-color: #407e7a;">
                        <div class="text-left" style="margin-left: 15px;">
                            <div id="clock1" class="label label-rounded label-default">Jumlah : 02</div>
                            <div id="clock1" class="label label-rounded label-default">Terisi : 0</div>
                            <div id="clock1" class="label label-rounded label-default">Kosong : 02</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class='row' id="groupKelas-7">
        </div>
</div>
<nav id="mainNav1" class="navbar navbar-default navbar-fixed-bottom nav" role="navigation">
    <div class="container nav">
        <div class="color-bed col-lg-12" style="">
            <div class="navbar-footer page-scroll">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1-footer">
                    <span class="sr-only">Toggle navigation</span> Menu <i class="fa fa-bars"></i>
                </button>
                <a class="navbar-brand nav" href="#"><b>Keterangan:</b></a> 
            </div>
            <?php foreach($getTempatTidur as $data): ?>
                <div class="set-bed">
                    <div class="square" style="background: <?php echo $data['kode_warna']; ?>"></div>
                    <p> <?php echo $data['kettempattidur_nama']; ?></p>
                </div>
            <?php endforeach ?>
        </div>
    </div>
    <!-- /.container-fluid -->
</nav>
<?php
    // $this->registerCss($this->render('../assets/css/antrian.css'));
    $this->registerJs($this->render('js/__dashboard.js'));
?>
