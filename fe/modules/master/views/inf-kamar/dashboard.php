<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\web\View;
use yii\helpers\Url;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;

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
        margin-top:0px;
        margin-left:500px;
        z-index: 9999999;
    }
    .navbar-header-master{
        display: inline-block;
        width: 500px;
    }
    .nav-not-sticky{
        height:50px;
    }
    .navbar-collapse{
        float:right;
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
        margin-top: -10px; /*50px*/
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
        height: 60px;
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
    .navbar-default .navbar-nav > li > a:hover, .navbar-default .navbar-nav > li > a:focus {
        color: #606060;
        background-color: #00CC66; /*rgba(76,175,80,1);*/
    }
    .navbar-default .navbar-nav > .active > a, .navbar-default .navbar-nav > .active > a:hover, .navbar-default .navbar-nav > .active > a:focus {
        color: #606060;
        background-color: #54be8b; /*rgba(76,175,80,1);*/
    }
    .panel-primary > .panel-heading {
        color: #fff;
        background-color: #399e6d; /*rgba(59,156,60,1);*/
        border-color: #2196F3;
        text-transform: uppercase;
        text-align: center;
    }
    .panel-footer {
        color: #606060;
        background-color: #54be8b; /*399e6d*/ /*rgba(59,156,60,1);*/
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
        width: 40px !important;
        height: 39px !important;
    }
    .set-bed {
        position: relative;
        min-height: 1px;
        padding: 2px 2px 2px 2px;
        display: inline-flex;
    }
    .color-bed {
        padding: 5px 5px 5px 25px;
        margin: auto;
    }
    .navbar-right {
        /*margin-right: -60px;*/
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
      border:0px solid #000;
      grid-gap: 10px;
      grid-template-columns: repeat(4, 1fr); /*repeat(auto-fill, minmax(311px,1fr));*/
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
        background-color: #54be8b;
        border-color: #54be8b; /*999999*/
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
</style>
<body style="background: #f1f2f7;">
    <!-- Navigation -->
    <div class="nav-not-sticky">
    <nav id="mainNav" class="navbar navbar-default navbar-fixed-top topnav" role="navigation">
        <div class="container topnav">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header-master page-scroll">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1-header">
                    <span class="fa fa-searchsr-only">Toggle navigation</span> Menu <i class="fa fa-bars"></i>
                </button>            
                <a class="navbar-brand topnav" style="font-weight: bold;">
                  <img style="margin-right: 20px;" src="<?= Url::base(true).Yii::$app->docoVars->identity("path_logo_header") . Yii::$app->docoVars->identity("logo_header"); ?>">
                    Dashboard Kamar Rawat Inap - <?= Yii::$app->docoVars->identity("nama_rumahsakit") ?>
                </a> 
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1-header">
                <ul class="nav navbar-nav navbar-right">
                    <?php $no_kelas = 0; ?>
                    <?php foreach($getKelasPelayananHeader as $key => $val):?>
                        <?php $no_kelas ++; ?>
                        <li id="kelas-header-<?php echo $no_kelas; ?>" class="<?=$no_kelas==1?"active":"";?>">
                            <a class="page-scroll" href="#groupKelas-<?php echo $no_kelas; ?>"><?php echo strtoupper($val); ?></a>
                        </li>
                   <?php endforeach ?>
                    <span class="filtering fa fa-search" id="filtering" onclick="$('#style-switch').toggle();">
                    </span> 
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <div id="style-switch" class="collapse in" aria-expanded="true" style="display: none;">
          <h4 class="text-uppercase text-center">Filter</h4>
            <?php 
            $form = ActiveForm::begin([
                'id' => 'filter-form', 
                // 'type' => ActiveForm::TYPE_HORIZONTAL,
                // 'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
            ]); 
            ?>
            <div class="input-group" style="width: 100%;">
                    <?php echo  Html::dropDownList('kelas_pelayanan', '',$filterKelasPelayananHeader, [
                                                'class' => 'form-control select2 kelas-filter',
                                               'col-index'=>3,
                                                'prompt' => Yii::t('fe', '-- Pilih Kelas --')
                                       ])
                    ?>
            </div>
            <div class="input-group" style="width: 100%;">
                    <?php echo  Html::dropDownList('ruangan', '',$filterRuangan, [
                                                  'class' => 'form-control select2 ruangan-filter',
                                                   'col-index'=>3,
                                                    'prompt' => Yii::t('fe', '-- Pilih Ruangan --')
                                       ])
                    ?>
            </div>
            <button style="margin: 25px 85px 15px;" id="btn-filter-dashboard" type="submit" class="btn bg-success-600 btn-huge-finish stepy-finish">Submit</button> 
            <?php ActiveForm::end(); ?>
        </div>
        <!-- /.container-fluid -->
    </nav>
    </div>
    <div id="loader" style="display: none;"><img src="<?php echo $imgLoading ?>">  /></div>
    <div class='col-md-12 col-lg-12' id="afterLoad" style="margin-top: 0px; display: block;">
        <!-- start -->
        <?php $gk = 0; 
        $j = 0;
        ?>
        <?php foreach($getDasboarKamar as $keyz => $valkamar):?>
        <?php $gk ++; ?>
        <div class="groupKelas" id="groupKelas-<?php echo $gk; ?>">
            <!-- start -->
            <?php foreach($valkamar as $keyf => $valf):?>
            <?php $j++; ?>
                <?php $i = 0; 
                ?>
                <div class="wrapper">
                    <?php foreach($valf as $keydata => $valdata):?>
                        
                       <div class="box groupKamar panel-primary panel"   <?= $i % 4 == 0 ? "id = part-" . $j : ""; ?> >
                            <?php foreach($valdata['kamar'] as $k => $val):?>
                                <div class="panel-heading">
                                    <h4>
                                        <?php echo $k ?>
                                        <small><div id="baseUrl"><?php //echo Yii::app()->getBaseUrl(); ?></div></small>        
                                    </h4>
                                    <div class="panel-footer">
                                        <div class="text-left">
                                            <div id="clock1" class="label label-default">Jumlah : <?php echo $valdata['total']; ?></div>
                                            <div id="clock1" class="label label-default">Terisi : <?php echo $valdata['total_isi']; ?></div>
                                            <div id="clock1" class="label label-default">Kosong : <?php echo $valdata['total_kosong']; ?></div>                                        </div>
                                    </div>
                                </div>
                                  <div class="data-kamar">
                                    <?php foreach($val as $x => $y):?>
                                          <div class='col-md-2 imgDetail'>
                                              <section class='panel text-center bg-panel'
                                                      id=<?php echo $y['kamartempattidur_id'] ?>
                                                      data-status='<?php echo $y['kettempattidur_nama'] ?>'
                                                      style="background-color: <?php echo $y['kode_warna'] ?>">
                                                  <h5 style="font-size: 13px;"><?php echo $y['no_tempattidur'] ?></h5>
                                              </section> 
                                          </div>                                              
                                    <?php endforeach ?>
                                  </div>
                            <?php endforeach ?>
                        </div>
                        <?php 
                            $i++;
                            $j = $i % 4 == 0 ? $j + 1 : $j;
                        ?>                            
                    <?php endforeach?>
                </div>
            <?php endforeach?>
            <!-- end -->
        </div>
        <?php endforeach?>
        <!-- end -->
    </div>
    <nav id="mainNav1" class="navbar navbar-default navbar-fixed-bottom nav" role="navigation">
        <div class="container nav">
            <div class="navbar-footer page-scroll col-lg-1">
         
                <p class="navbar-brand nav" style="margin-left:-100px" href="#"><b>Keterangan:</b></p> 
            </div>
            <div class="color-bed col-lg-11" style="">
                <?php foreach($getTempatTidur as $data): ?>
                    <div class="set-bed col-md-2">
                        <div class="square" style="background: <?php echo $data['kode_warna']; ?>"></div>
                        <p> <?php echo $data['kettempattidur_nama']; ?></p>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
        <!-- /.container-fluid -->
    </nav>
</body>
<script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
<script type="text/javascript">
    
    var $ = jQuery;
    jQuery(document).ready(function($){
         /*$("span.filtering.fa.fa-search, #style-switch, .select2").hover(function(e){          
            if (e.type === "mouseenter") {
                $("#style-switch").show();
                // console.log("enter"); 
            }
        });
  
        $("#style-switch, .select2").hover(function(e){   
            if (e.type === "mouseleave") { 
                $("#style-switch").hide();
                // console.log("leave"); 
            }
        });
        $("#filtering").click(function(e){

        });*/
    });

</script>
<?php
    // $this->registerCss($this->render('../assets/css/antrian.css'));
    $this->registerJs($this->render('js/__dashboard.js'));
?>