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
        <div class="row" style="margin-bottom:20px; margin-top: -40px;">
            <h3 style="color: #ffffff">Menu Layanan Rumah Sakit</h3>
        </div>
        <div class="row row-flex">
            <?php
        //    $module['1148']['desc'] = 'Manajemen layanan dan jadwal ambulan';
        //    $module['139']['desc'] = 'Pengaturan antrian KIOSK/APM'; 
        //    $module['1167']['desc'] = 'Stok dan distribusi darah';
        //    $module['5']['desc'] = 'Jadwal dan laporan tindakan operasi';
        //    $module['3']['desc'] = 'Sterilisasi alat medis dan logistik';
        //    $module['21']['desc'] = 'Pengelolaan obat dan resep pasien';
        //    $module['44']['desc'] = 'Catatan dan jadwal terapi pasien';
        //    $module['963']['desc'] = 'Perencanaan dan distribusi diet pasien';
        //    $module['446']['desc'] = 'Persediaan obat dan bahan medis';
        //    $module['777']['desc'] = 'Stok barang non-medis rumah sakit';
        //    $module['945']['desc'] = 'Administrasi dan pengelolaan jenazah';
        //    $module['23']['desc'] = 'Pembayaran dan transaksi pasien';
        //    $module['518']['desc'] = 'Pemeriksaan dan hasil uji laboratorium';
        //    $module['1168']['desc'] = 'Pemeriksaan kesehatan menyeluruh pasien';
        //    $module['1169']['desc'] = 'Pengelolaan jasa medis dan tarif';
        //    $module['24']['desc'] = 'Registrasi pasien baru dan lama';
        //    $module['799']['desc'] = 'Proses pembelian obat dan non medis';
        //    $module['722']['desc'] = 'Klaim asuransi dan BPJS pasien';
        //    $module['426']['desc'] = 'Pemeriksaan dan hasil foto medis';
        //    $module['464']['desc'] = 'Pelayanan pasien gawat darurat';
        //    $module['2']['desc'] = 'Manajemen kamar dan perawatan pasien';
        //    $module['42']['desc'] = 'Pendaftaran dan pelayanan poliklinik';
        //    $module['22']['desc'] = 'Riwayat medis dan data pasien';
        //    $module['617']['desc'] = 'Pengaturan master, user dan hak akses';
           ?>
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
                    <?php 
                        $is_external_link = isset($value['is_external_link']) ? (int) $value['is_external_link'] : 0; 
                        $open_newtab = isset($value['open_newtab']) ? (int) $value['open_newtab'] : 0; 
                    ?>
                    <div class="list-modules" data-index="<?= $key ?>" data-value="<?= isset($value['name2']) ? $value['name2'] : '' ?>"
                        data-image="<?= isset($value['icon']) ? $value['icon'] : '' ?>" external-link="<?= $is_external_link; ?>">
                        <a href="<?= $is_external_link ? $value['url'] : '#'; ?>" class="menu-card" <?= $open_newtab ? 'target="_blank"' : ''; ?>>
                            <div class="icon-container">
                                <span class="icon">
                                    <img src="<?= isset($value['icon']) ? $value['icon'] : '' ?>" style="height:35px;">
                                </span>
                            </div>
                            <div class="text-content" >
                                <h3 style="color: #00008B "><?= isset($value['name2']) ? $value['name2'] : ''; ?></h3>
                                <p><?= isset($module[$key]['desc']) ? $module[$key]['desc'] : '' ?></p>
                            </div>
                        </a>
                    </div>
                    <?php $row++ ?>
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

<div class="modal fade pemilihan-ruangan" id="modal-pemilihan-ruangan" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel" style="font-weight: bold;">Test</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <hr>
            <div class="modal-body">

            </div>
        </div>
    </div>
</div>

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
                <div class="menu-grid">
                    <a href="#" class="menu-card draft-soap" data-type="RD">
                        <div class="icon-container">
                            <span class="icon">
                                <img src="/media/img/newsimrs_image/logo-menu/09 MODUL RAWAT DARURAT.svg" style="height:35px;">
                            </span>
                        </div>
                        <div class="text-content" >
                            <h3 class="draft-soap--header" style="color: #00008B">Rawat Darurat</h3>
                            <p class="draft-soap--text" id="rd-soap" style="color: #00008B">SOAP: 0</p>
                        </div>
                    </a>
                    <a href="#" class="menu-card draft-soap" data-type="RJ">
                        <div class="icon-container">
                            <span class="icon">
                                <img src="/media/img/newsimrs_image/logo-menu/05 MODUL RAWAT JALAN.svg" style="height:35px;">
                            </span>
                        </div>
                        <div class="text-content" >
                            <h3 class="draft-soap--header" style="color: #00008B">Rawat Jalan</h3>
                            <p class="draft-soap--text" id="rj-soap" style="color: #00008B">SOAP: 0</p>
                        </div>
                    </a>
                    <a href="#" class="menu-card draft-soap" data-type="RI">
                        <div class="icon-container">
                            <span class="icon">
                                <img src="/media/img/newsimrs_image/logo-menu/10 MODUL RAWAT INAP.svg" style="height:35px;">
                            </span>
                        </div>
                        <div class="text-content" >
                            <h3 class="draft-soap--header" style="color: #00008B">Rawat Inap</h3>
                            <p class="draft-soap--text" id="ri-soap" style="color: #00008B">SOAP: 0</p>
                            <p class="draft-soap--text" id="ri-rm" style="color: #00008B">Resume Medis: 0</p>
                        </div>
                    </a>
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
        const kelompokPegawai = {$kelompok_pegawai};
    ", VIEW::POS_END, 'site-page');
    $this->registerJs($this->render('../assets/js/index.js'));
?>
