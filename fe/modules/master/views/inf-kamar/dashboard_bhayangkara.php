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
            margin-top: 0px;
            margin-left: 500px;
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
            margin-top: -10px;
            /*50px*/
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
            /*height: 60px;*/
            box-shadow: 3px 3px #d5d5d5;
        }
        
        .imgDetail {
            padding-right: 0px;
            padding-left: 0px;
            /*margin-right: 8px;*/
            margin: 4px;
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
        
        .navbar-default .navbar-nav > li > a:hover,
        .navbar-default .navbar-nav > li > a:focus {
            color: #606060;
            background-color: #00CC66;
            /*rgba(76,175,80,1);*/
        }
        
        .navbar-default .navbar-nav > .active > a,
        .navbar-default .navbar-nav > .active > a:hover,
        .navbar-default .navbar-nav > .active > a:focus {
            color: #606060;
            background-color: #54be8b;
            /*rgba(76,175,80,1);*/
        }
        
        .panel-primary > .panel-heading {
            color: #fff;
            background-color: rgba(59,156,60,1);
            /*rgba(59,156,60,1);*/
            border-color: #2196F3;
            text-transform: uppercase;
            text-align: center;
        }
        
        .panel-footer {
            color: #606060;
            /*background-color: #54be8b;*/
            background-color: rgba(76,175,80,1);
            /*399e6d*/
            /*rgba(59,156,60,1);*/
            border-top: 0px solid #ddd;
            margin: -10px;
        }
        
        .navbar-default.navbar-fixed-bottom {
            /*max-height: 55px;*/
            /*padding: 10px;*/
            /*margin: 5px;*/
        }
        
        div#clock1 {
            font-size: 14px;
        }
        
        #bs-example-navbar-collapse-1,
        li {
            font-size: 15px;
            font: -webkit-mini-control;
            font-family: "Roboto", Helvetica Neue, Helvetica, Arial, sans-serif;
            color: #000;
            background-color: #f8f8f8;
        }
        
        #bs-example-navbar-collapse-1,
        p {
            font-size: 14px;
        }
        
        .square {
            width: 40px !important;
            height: 39px !important;
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
        
        .navbar-right {
            margin-right: -60px;
        }
        
        span.filtering.fa.fa-search {
            margin: auto;
            padding: 15px;
            cursor: pointer;
        }
        
        #style-switch {
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
            width: 300px;
            padding: 20px;
            position: fixed;
            top: 46px;
            right: 0;
            background: #fff;
            border: solid 1px #eee;
            z-index: 2000;
            height: 250px;
        }
        
        .collapse.in {
            display: block;
            visibility: visible;
        }
        
        .box {
            background-color: #e8ebff;
            color: #fff;
            border-radius: 5px;
            padding: 0px;
            font-size: 150%;
            box-shadow: 5px 5px #d5d5d5;
        }
        
        .box:nth-child(even) {
            /*background-color: #ccc;*/
            /*color: #000;*/
        }
        
        .wrapper {
            display: grid;
            border: 0px solid #000;
            grid-gap: 10px;
            grid-template-columns: repeat(4, 1fr);
            /*repeat(auto-fill, minmax(311px,1fr));*/
            /*grid-template-columns: repeat(auto-fill, minmax(100px,1fr) minmax(200px,2fr));*/
        }
        
        .data-kamar {
            margin: 15px;
            padding: 0px;
        }
        
        a.navbar-brand.topnav {
            display: -webkit-box;
            margin-right: 20px;
        }
        
        .label-default {
            /*background-color: #54be8b;*/
            background-color: rgba(76,175,80,1);
            border-color: rgba(76,175,80,1);
            /*999999*/
        }
        
        div#afterLoad {
            padding-bottom: 30px;
            margin-bottom: 40px;
        }
        
        .panel-heading {
            padding: 10px 10px !important;
        }
        
        .text-left {
            text-align: left;
            margin: 0px 5px;
            padding: 0px 10px;
        }
        
        .label {
            padding: 2px 3px 1px 5px !important;
        }
        .navbar-default .navbar-nav > .active > a, .navbar-default .navbar-nav > .active > a:hover, .navbar-default .navbar-nav > .active > a:focus{
            background-color: rgba(76,175,80,1);
        }
        h4 {
            margin-bottom: 20px;
        }

    </style>
    <!-- Navigation -->

    <body style="background: #f1f2f7;">
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
                        <li id="kelas-header-1" class="active">
                            <a class="page-scroll" href="#groupKelas-1">
                                <?php echo "ICU"; ?>
                            </a>
                        </li>
                        <li id="kelas-header-2" class="">
                            <a class="page-scroll" href="#groupKelas-2">
                                <?php echo "VIP"; ?>
                            </a>
                        </li>
                        <li id="kelas-header-3" class="">
                            <a class="page-scroll" href="#groupKelas-3">
                                <?php echo "Kelas III"; ?>
                            </a>
                        </li>
                        <li id="kelas-header-4" class="">
                            <a class="page-scroll" href="#groupKelas-4">
                                <?php echo "Kelas II"; ?>
                            </a>
                        </li>
                        <li id="kelas-header-5" class="">
                            <a class="page-scroll" href="#groupKelas-5">
                                <?php echo "Kelas I"; ?>
                            </a>
                        </li>
                        <li id="kelas-header-6" class="">
                            <a class="page-scroll" href="#groupKelas-6">
                                <?php echo "ISOLASI"; ?>
                            </a>
                        </li>
                        <li id="kelas-header-7" class="">
                            <a class="page-scroll" href="#groupKelas-7">
                                <?php echo "PERINATOLOGI"; ?>
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- /.navbar-collapse -->
            </div>
        </nav>

        <!-- /.container-fluid -->
        <div class='col-md-12 col-lg-12' id="afterLoad" style="margin-top: 0px; display: block;">
            <!-- start -->
            <div class="groupKelas" id="groupKelas-1">
                <!-- start -->
                <div class="wrapper">

                    <div class="box groupKamar panel-primary panel" id="part-1">
                        <div class="panel-heading">
                            <h4>
                                ICU<small><div id="baseUrl"></div></small>        
                            </h4>
                            <div class="panel-footer">
                                <div class="text-left">
                                    <div id="clock1" class="label label-default">Jumlah : 3</div>
                                    <div id="clock1" class="label label-default">Terisi : 2</div>
                                    <div id="clock1" class="label label-default">Kosong : 1</div>
                                </div>
                            </div>
                        </div>
                        <div class="data-kamar">
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">01</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                             <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">02</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #d9d9d9">
                                    <h5 style="font-size: 13px;">03</h5>
                                    <i class='fa'></i>
                                </section>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="groupKelas" id="groupKelas-2">
                <!-- start -->
                <div class="wrapper">

                    <div class="box groupKamar panel-primary panel" id="part-2">
                        <div class="panel-heading">
                            <h4>
                                NIFAS ANGGREK<small><div id="baseUrl"></div></small>        
                            </h4>
                            <div class="panel-footer">
                                <div class="text-left">
                                    <div id="clock1" class="label label-default">Jumlah : 1</div>
                                    <div id="clock1" class="label label-default">Terisi : 0</div>
                                    <div id="clock1" class="label label-default">Kosong : 1</div>
                                </div>
                            </div>
                        </div>
                        <div class="data-kamar">
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #d9d9d9">
                                    <h5 style="font-size: 13px;">01</h5>
                                    <i class='fa'></i>
                                </section>
                            </div>
                        </div>
                    </div>

                    <div class="box groupKamar panel-primary panel">
                        <div class="panel-heading">
                            <h4>
                                RAWAT INAP ANGGREK<small><div id="baseUrl"></div></small>        
                            </h4>
                            <div class="panel-footer">
                                <div class="text-left">
                                    <div id="clock1" class="label label-default">Jumlah : 1</div>
                                    <div id="clock1" class="label label-default">Terisi : 0</div>
                                    <div id="clock1" class="label label-default">Kosong : 1</div>
                                </div>
                            </div>
                        </div>
                        <div class="data-kamar">
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #d9d9d9">
                                    <h5 style="font-size: 13px;">01</h5>
                                    <i class='fa'></i>
                                </section>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- end -->
            </div>
            <div class="groupKelas" id="groupKelas-3">
                <!-- start -->
                <div class="wrapper">

                    <div class="box groupKamar panel-primary panel" id="part-3">
                        <div class="panel-heading">
                            <h4>
                                NIFAS CEMPAKA 2<small><div id="baseUrl"></div></small>        
                            </h4>
                            <div class="panel-footer">
                                <div class="text-left">
                                    <div id="clock1" class="label label-default">Jumlah : 16</div>
                                    <div id="clock1" class="label label-default">Terisi : 16</div>
                                    <div id="clock1" class="label label-default">Kosong : 0</div>
                                </div>
                            </div>
                        </div>
                        <div class="data-kamar">
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">01</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">02</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">03</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">04</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">05</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">06</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">07</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">08</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">09</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">10</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">11</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">12</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">13</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">14</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">15</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">16</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                        </div>
                    </div>

                    <div class="box groupKamar panel-primary panel">
                        <div class="panel-heading">
                            <h4>
                                RAWAT INAP CEMPAKA 2<small><div id="baseUrl"></div></small>        
                            </h4>
                            <div class="panel-footer">
                                <div class="text-left">
                                    <div id="clock1" class="label label-default">Jumlah : 3</div>
                                    <div id="clock1" class="label label-default">Terisi : 1</div>
                                    <div id="clock1" class="label label-default">Kosong : 2</div>
                                </div>
                            </div>
                        </div>
                        <div class="data-kamar">
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">01</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #d9d9d9">
                                    <h5 style="font-size: 13px;">02</h5>
                                    <i class='fa'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #d9d9d9">
                                    <h5 style="font-size: 13px;">03</h5>
                                    <i class='fa'></i>
                                </section>
                            </div>
                        </div>
                    </div>

                    <div class="box groupKamar panel-primary panel">
                        <div class="panel-heading">
                            <h4>
                                DAHLIA<small><div id="baseUrl"></div></small>        
                            </h4>
                            <div class="panel-footer">
                                <div class="text-left">
                                    <div id="clock1" class="label label-default">Jumlah : 7</div>
                                    <div id="clock1" class="label label-default">Terisi : 4</div>
                                    <div id="clock1" class="label label-default">Kosong : 3</div>
                                </div>
                            </div>
                        </div>
                        <div class="data-kamar">
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">01</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">02</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">03</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">04</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #d9d9d9">
                                    <h5 style="font-size: 13px;">05</h5>
                                    <i class='fa'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #d9d9d9">
                                    <h5 style="font-size: 13px;">06</h5>
                                    <i class='fa'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #d9d9d9">
                                    <h5 style="font-size: 13px;">07</h5>
                                    <i class='fa'></i>
                                </section>
                            </div>
                        </div>
                    </div>

                    <div class="box groupKamar panel-primary panel">
                        <div class="panel-heading">
                            <h4>
                                RAWAT INAP CEMPAKA 2 <small><div id="baseUrl"></div></small>        
                            </h4>
                            <div class="panel-footer">
                                <div class="text-left">
                                    <div id="clock1" class="label label-default">Jumlah : 6</div>
                                    <div id="clock1" class="label label-default">Terisi : 4</div>
                                    <div id="clock1" class="label label-default">Kosong : 2</div>
                                </div>
                            </div>
                        </div>
                        <div class="data-kamar">
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">01</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">02</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">03</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">04</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                             <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">04</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #d9d9d9">
                                    <h5 style="font-size: 13px;">06</h5>
                                    <i class='fa'></i>
                                </section>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="groupKelas" id="groupKelas-4">
                <!-- start -->
                <div class="wrapper">

                    <div class="box groupKamar panel-primary panel" id="part-4">
                        <div class="panel-heading">
                            <h4>
                                NIFAS FLAMBOYAN<small><div id="baseUrl"></div></small>        
                            </h4>
                            <div class="panel-footer">
                                <div class="text-left">
                                    <div id="clock1" class="label label-default">Jumlah : 3</div>
                                    <div id="clock1" class="label label-default">Terisi : 3</div>
                                    <div id="clock1" class="label label-default">Kosong : 0</div>
                                </div>
                            </div>
                        </div>
                        <div class="data-kamar">
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">01</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">02</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">03</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                        </div>
                    </div>

                    <div class="box groupKamar panel-primary panel">
                        <div class="panel-heading">
                            <h4>
                                RAWAT INAP FLAMBOYAN <small><div id="baseUrl"></div></small>        
                            </h4>
                            <div class="panel-footer">
                                <div class="text-left">
                                    <div id="clock1" class="label label-default">Jumlah : 3</div>
                                    <div id="clock1" class="label label-default">Terisi : 1</div>
                                    <div id="clock1" class="label label-default">Kosong : 2</div>
                                </div>
                            </div>
                        </div>
                        <div class="data-kamar">
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">01</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #d9d9d9">
                                    <h5 style="font-size: 13px;">02</h5>
                                    <i class='fa'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #d9d9d9">
                                    <h5 style="font-size: 13px;">03</h5>
                                    <i class='fa'></i>
                                </section>
                            </div>
                        </div>
                    </div>

                    <div class="box groupKamar panel-primary panel">
                        <div class="panel-heading">
                            <h4>
                                BOUGENVILE <small><div id="baseUrl"></div></small>        
                            </h4>
                            <div class="panel-footer">
                                <div class="text-left">
                                    <div id="clock1" class="label label-default">Jumlah : 4</div>
                                    <div id="clock1" class="label label-default">Terisi : 3</div>
                                    <div id="clock1" class="label label-default">Kosong : 1</div>
                                </div>
                            </div>
                        </div>
                        <div class="data-kamar">
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">01</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">02</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">03</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #d9d9d9">
                                    <h5 style="font-size: 13px;">04</h5>
                                    <i class='fa'></i>
                                </section>
                            </div>
                        </div>
                    </div>

                    <div class="box groupKamar panel-primary panel">
                        <div class="panel-heading">
                            <h4>
                                TULIP <small><div id="baseUrl"></div></small>        
                            </h4>
                            <div class="panel-footer">
                                <div class="text-left">
                                    <div id="clock1" class="label label-default">Jumlah : 3</div>
                                    <div id="clock1" class="label label-default">Terisi : 1</div>
                                    <div id="clock1" class="label label-default">Kosong : 2</div>
                                </div>
                            </div>
                        </div>
                        <div class="data-kamar">
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">01</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                           <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #d9d9d9">
                                    <h5 style="font-size: 13px;">02</h5>
                                    <i class='fa'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #d9d9d9">
                                    <h5 style="font-size: 13px;">03</h5>
                                    <i class='fa'></i>
                                </section>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- end -->
            </div>
            <div class="groupKelas" id="groupKelas-5">
                <!-- start -->
                <div class="wrapper">
                    <div class="box groupKamar panel-primary panel" id="part-5">
                        <div class="panel-heading">
                            <h4>
                                NIFAS MAWAR<small><div id="baseUrl"></div></small>        
                            </h4>
                            <div class="panel-footer">
                                <div class="text-left">
                                    <div id="clock1" class="label label-default">Jumlah : 1</div>
                                    <div id="clock1" class="label label-default">Terisi : 1</div>
                                    <div id="clock1" class="label label-default">Kosong : 0</div>
                                </div>
                            </div>
                        </div>
                        <div class="data-kamar">
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">01</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                        </div>
                    </div>

                    <div class="box groupKamar panel-primary panel">
                        <div class="panel-heading">
                            <h4>
                                RAWAT INAP MAWAR<small><div id="baseUrl"></div></small>        
                            </h4>
                            <div class="panel-footer">
                                <div class="text-left">
                                    <div id="clock1" class="label label-default">Jumlah : 1</div>
                                    <div id="clock1" class="label label-default">Terisi : 1</div>
                                    <div id="clock1" class="label label-default">Kosong : 0</div>
                                </div>
                            </div>
                        </div>
                        <div class="data-kamar">
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">01</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                        </div>
                    </div>

                    <div class="box groupKamar panel-primary panel">
                        <div class="panel-heading">
                            <h4>
                                NAFAS MELATI<small><div id="baseUrl"></div></small>        
                            </h4>
                            <div class="panel-footer">
                                <div class="text-left">
                                    <div id="clock1" class="label label-default">Jumlah : 1</div>
                                    <div id="clock1" class="label label-default">Terisi : 1</div>
                                    <div id="clock1" class="label label-default">Kosong : 0</div>
                                </div>
                            </div>
                        </div>
                        <div class="data-kamar">
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">01</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                        </div>
                    </div>

                    <div class="box groupKamar panel-primary panel">
                        <div class="panel-heading">
                            <h4>
                                RAWAT INAP MELATI<small><div id="baseUrl"></div></small>        
                            </h4>
                            <div class="panel-footer">
                                <div class="text-left">
                                    <div id="clock1" class="label label-default">Jumlah : 3</div>
                                    <div id="clock1" class="label label-default">Terisi : 2</div>
                                    <div id="clock1" class="label label-default">Kosong : 1</div>
                                </div>
                            </div>
                        </div>
                        <div class="data-kamar">
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">01</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee">
                                    <h5 style="font-size: 13px;">02</h5>
                                    <i class='fa fa-bed'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #d9d9d9">
                                    <h5 style="font-size: 13px;">03</h5>
                                    <i class='fa'></i>
                                </section>
                            </div>
                        </div>
                    </div>
                    

                </div>
                <!-- end -->
            </div>
            <div class="groupKelas" id="groupKelas-6">
                <!-- start -->
                <div class="wrapper">
                    <div class="box groupKamar panel-primary panel" id="part-6">
                        <div class="panel-heading">
                            <h4>
                                RAFLESIA<small><div id="baseUrl"></div></small>        
                            </h4>
                            <div class="panel-footer">
                                <div class="text-left">
                                    <div id="clock1" class="label label-default">Jumlah : 2</div>
                                    <div id="clock1" class="label label-default">Terisi : 0</div>
                                    <div id="clock1" class="label label-default">Kosong : 2</div>
                                </div>
                            </div>
                        </div>
                        <div class="data-kamar">
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #d9d9d9">
                                    <h5 style="font-size: 13px;">01</h5>
                                    <i class='fa'></i>
                                </section>
                            </div>
                            <div class='col-md-2 imgDetail'>
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #d9d9d9">
                                    <h5 style="font-size: 13px;">02</h5>
                                    <i class='fa'></i>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end -->
            </div>

            <!-- end -->
        </div>
        <nav id="mainNav1" class="navbar navbar-default navbar-fixed-bottom nav" role="navigation" style="height: 115px;">
            <div class="container nav">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-footer page-scroll">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1-footer">
                        <span class="sr-only">Toggle navigation</span> Menu <i class="fa fa-bars"></i>
                    </button>
                    <a class="navbar-brand nav" href="#">Keterangan:</a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1-footer">
                    <ul class="nav navbar-nav navbar-left" valign="middle">

                        <li class="" style="background-color: #fff;">
                            <div> <p style="padding: 20px 0;padding-right: 5px;font-weight:bold;margin-right: 15px ">Kamar Kosong</p></div>
                            <div class='col-md-2 imgDetail' style="top: -30px;">
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #d9d9d9;width: 45px;height: 50px;">
                                    <i class='fa'></i>
                                </section>
                            </div>
                        </li>
                        <li class="" style="background-color: #fff;">
                            <div> <p style="padding: 20px 0;padding-right: 5px;font-weight:bold;margin-right: 15px ">Kamar Terisi</p></div>
                            <div class='col-md-2 imgDetail' style="top: -30px;">
                                <section class='panel text-center bg-panel' id=7 data-status='Isi Perempuan' style="background-color: #ee82ee;width: 45px;height: 50px;">
                                        <br>
                                    <i class='fa fa-bed' style="margin-top: 15px;"></i>
                                </section>
                            </div>
                        </li>
                   </ul>
                </div>
                <!-- /.navbar-collapse -->
            </div>
            <!-- /.container-fluid -->
        </nav>
    </body>
    <?php
    // $this->registerCss($this->render('../assets/css/antrian.css'));
    $this->registerJs($this->render('js/__dashboard_bhayangkara.js'));
?>