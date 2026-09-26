<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

$this->title = Yii::$app->docoVars->identity("nama_rumahsakit");
?>

<div class="site-index">
    <!-- List styles -->
    <!--
    <h6 class="content-group text-semibold">
        <?= Yii::$app->docoVars->identity("nama_rumahsakit"); ?>
        <small class="display-block"><?= Yii::t('fe','List Module')?></small>
    </h6>
-->
    <div class= "module-section">
        <div class="row row-flex">
           <?php $extend_module_count = 0;$row = 1; ?>

           <?php
           //Mengurutkan Menu Index - Dirubah di FE karena bentrok orderby di AuthController.php
            function aasort (&$array, $key) {
                $sorter=array();
                $ret=array();
                reset($array);
                foreach ($array as $ii => $va) {
                    $sorter[$ii]=$va[$key];
                }
                asort($sorter);
                foreach ($sorter as $ii => $va) {
                    $ret[$ii]=$array[$ii];
                }
                $array=$ret;
            }

            aasort($module,"name2");
           ?>
           

        <div class="menu-grid">
            <?php foreach ($module as $key => $value) : ?>
                <div class="menu-card list-modules" data-index="<?= $key ?>" data-value="<?= $value['name2'] ?>" data-image="<?= $value['icon']?>">
                    <div class="icon-container">
                        <img src="/media/img/modules/ambulance.png" alt="">
                    </div>
                    <p><?= $value['name2']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
            
        </div>
        <!-- <div class="row" style="margin-top:20px;">
            <center>
                <button class="btn btn-more" onclick="showmore()"><?//= Yii::t('fe','Tampilkan Lebih Banyak')?></button>
                <button class="btn btn-less" onclick="showless()" style="display:none;"><?//= Yii::t('fe','Tampilkan Lebih Sedikit')?></button>
            </center>
        </div> -->
        <!-- /list styles -->
        
</div>



<!--begin carousel-->
<div id="newsimrsCaraousel" class="carousel slide" data-interval="false">
    <div class="carousel-inner cr-inner middle">
    
    </div>
</div>
<!--Begin Previous and Next buttons-->
<!--
<a class="left carousel-control cr-control" href="#newsimrsCaraousel" role="button" data-slide="prev"> 
    <span class="glyphicon glyphicon-chevron-left"></span>
</a> 
<a class="right carousel-control cr-control" href="#newsimrsCaraousel" role="button" data-slide="next"> 
    <span class="glyphicon glyphicon-chevron-right"></span>
</a>
-->
<!--end carousel-->
<div class="modal fade draft-soap-modal" id="modal-draft-soap" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Informasi SOAP dan Resume Medis Belum Selesai</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <hr>
            <div class="modal-body">
                <div class="draft-soap--wrapper">
                    <div class="draft-soap" data-type="RD">
                        <a href="#" class="menu-card">
                            <div class="icon-container">
                                <img src="/media/img/newsimrs_image/logo-menu/09 MODUL RAWAT DARURAT.svg" style="height:35px;">
                            </div>
                            <div class="text-content">
                                <h3 class="draft-soap--header">Rawat Darurat</h3>
                                <p class="draft-soap--text" id="rd-soap">SOAP: 0</p>
                            </div>
                        </a>
                        <!-- <div class="col-md-8">
                            <p style="text-align: left" class="draft-soap--header">Rawat Darurat</p>
                            <p style="text-align: left" class="draft-soap--text" id="rd-soap">SOAP: 0</p>
                        </div>
                        <div class="col-md-4">
                            <br>
                            <img src="/media/img/newsimrs_image/logo-menu/09 MODUL RAWAT DARURAT.svg" style="height:70px;">
                        </div> -->
                    </div>
                    <div class="draft-soap" data-type="RJ">
                        <a href="#" class="menu-card">
                            <div class="icon-container">
                                <img src="/media/img/newsimrs_image/logo-menu/05 MODUL RAWAT JALAN.svg" style="height:35px;">
                            </div>
                            <div class="text-content">
                                <h3 class="draft-soap--header">Rawat Jalan</h3>
                                <p class="draft-soap--text" id="rj-soap">SOAP: 0</p>
                            </div>
                        </a>
                        <!-- <div class="col-md-8">
                            <p style="text-align: left" class="draft-soap--header">Rawat Jalan</p>
                            <p style="text-align: left" class="draft-soap--text" id="rj-soap">SOAP: 0</p>
                        </div>
                        <div class="col-md-4">
                            <br>
                            <img src="/media/img/newsimrs_image/logo-menu/05 MODUL RAWAT JALAN.svg" style="height:70px;">
                        </div> -->
                    </div>
                    <div class="draft-soap" data-type="RI">
                        <a href="#" class="menu-card">
                            <div class="icon-container">
                                <img src="/media/img/newsimrs_image/logo-menu/10 MODUL RAWAT INAP.svg" style="height:35px;">
                            </div>
                            <div class="text-content">
                                <h3 class="draft-soap--header">Rawat Inap</h3>
                                <p class="draft-soap--text" id="ri-soap">SOAP: 0</p>
                                <p class="draft-soap--text" id="ri-rm">Resume Medis: 0</p>
                            </div>
                        </a>
                        <!-- <div class="col-md-8">
                            <p style="text-align: left" class="draft-soap--header">Rawat Inap</p>
                            <p style="text-align: left" class="draft-soap--text" id="ri-soap">SOAP: 0</p>
                            <p style="text-align: left" class="draft-soap--text" id="ri-rm">Resume Medis: 0</p>
                        </div>
                        <div class="col-md-4">
                            <br>
                            <img src="/media/img/newsimrs_image/logo-menu/10 MODUL RAWAT INAP.svg" style="height:70px;">
                        </div> -->
                    </div>
                </div>
                <div id="tabtable" class='col-sm-12' style="display: none;">
                    <nav>
                        <div class="nav nav-tabs nav-tab-worklist" id="nav-tab" role="tablist">
                            <a id="tab_soap" class="nav-item nav-tab-type nav-link active">SOAP</a>
                            <a id="tab_rm" class="nav-item nav-tab-type nav-link">Resume Medis</a>
                        </div>
                    </nav>
                </div>
                <div class="table-wrapper">
                    <table class="table table-bordered" id="list-soap" style="display: none;">
                        <thead class="bg-inverse">
                            <tr>
                                <th class="text-left">No</th>
                                <th class="text-left">No Pendaftaran</th>
                                <th class="text-left">Nama Pasien</th>
                                <th class="text-left">Tanggal Cppt</th>
                                <th class="text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <br>
                    <table class="table table-bordered" id="list-rm" style="display: none;">
                        <thead class="bg-inverse">
                            <tr>
                                <th class="text-left">No</th>
                                <th class="text-left">No Pendaftaran</th>
                                <th class="text-left">Nama Pasien</th>
                                <th class="text-left">Tanggal Pendaftaran</th>
                                <th class="text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        
        var main_menu_encrypt = js_encrypt(<?= json_encode($module) ?>, true);
        sessionStorage.setItem('main_menu', main_menu_encrypt);
    }, false);

    function showmore () {
        $(".list-modules-hide").css('display','block');
        $(".btn-more").css('display','none');
        $(".btn-less").css('display','block');
    }

    function showless () {
        $(".list-modules-hide").css('display','none');
        $(".btn-more").css('display','block');
        $(".btn-less").css('display','none');
    }
</script>

<?php
    $this->registerJs("
        const notifications = " . json_encode($notifications) . "
        const draftSoap = " . json_encode($draftSoap) . "
        const draftRm = " . json_encode($draftRm) . "
    ", VIEW::POS_END, 'site-page');
    $this->registerJs($this->render('../assets/js/index.js'));
?>
