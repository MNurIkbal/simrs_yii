<?php
/**
 * @author: Indra Tiola
 * @description: ui untuk display antrian
**/

use app\components\DocoConstants;
use app\components\DocoHelpers;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use yii\web\View;
use yii\helpers\Url;

$this->title = $judulLayarAntrian;
?>
<div class="container">
    <div class="content-header">
        <div class="row">
            <div class="col-sm-3 wrapper-back-button">
                <a class="back-antrian" id="back-antrian" href="/antrian" ><img src="<?=Url::base(true).'/media/img/icon-antrian/arrow-left.png';?>"></a>
                <a class="back-form"><img src="<?=Url::base(true).'/media/img/icon-antrian/arrow-left.png';?>"></a>
            </div>
            <div class="col-sm-6">
                <h5 class="content-header-title">Pendaftaran Rawat Jalan</h5>
            </div>
            <div class="col-sm-3 text-right" style="font-size: 20px;">
                <a class="fa fa-chevron-up mr-2 hd-up"></a>
                <a class="fa fa-chevron-down mr-2 hd-down" style="display:none"></a>
            </div>
        </div>
    </div>

    <div class="content-body ">
        <?php
        $form = ActiveForm::begin([
            'id' => 'ambil-antrian-form',
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
        ]);
        ?>
            <fieldset title="1"  onmouseover="this.title='';">
                <legend>Pilih Poli</legend>
                <div id="slide-poli" class="carousel slide" data-ride="carousel" data-interval="false">
                    <?php
                        $total_card = 0;
                        $total_slide = 1;
                        $items = [];
                        $hide = 'hide';
                        if ($list_poly_antrian) {
                            $total_card = count($list_poly_antrian);
                            if ($total_card > 0) {
                                if ($total_card > 8) {
                                    $hide = '';
                                }

                                $pembagi = $total_card / 8;
                                $total_slide = ceil($pembagi);
                                $items = array_chunk($list_poly_antrian, 8);
                                
                            }
                        }
                    ?>

                    <ol class="carousel-indicators <?=$hide;?>">
                        <?php 
                            for ($i=0; $i < $total_slide; $i++) { 
                                $class = $i == 0 ? 'active' : '';
                                echo '<li data-target="#slide-poli" data-slide-to="'.$i.'" class="'.$class.'"></li>';
                            }
                        ?>
                    </ol>

                    <div class="carousel-inner">
                        <?php 
                            for ($i=0; $i < $total_slide; $i++) { 
                                $class = $i == 0 ? 'active' : '';
                        ?>

                            <div class="item <?= $class;?>">
                                 <?php if ($items) { ?>
                                    <div class="row get-parent">
                                    <?php
                                        foreach ($items[$i] as $key => $value) {
                                            $disable_card = $value['sisa_kuota'] == 0 ? 'inactive' : '';
                                            $icon_poli = $value['sisa_kuota'] == 0 ? 'inactive-poliklinik.png' : 'poliklinik.png';
                                    ?>
                                        <div class="col-sm-3">
                                            <div class="card card-poli <?= $disable_card;?> " data-poli-id="<?=$value['ruangan_id'];?>"  data-jadwalbukapoli-id="<?=$value['jadwalbukapoli_id'];?>">
                                                <!-- <img src="<?=Url::base(true).'/media/img/icon-antrian/'.$icon_poli;?>" class="rounded"> -->
                                                <h4 class="nama"><?= $value['ruangan_nama'];?></h4>
                                                <div class="jam-operasional">
                                                    <span>Jam Buka</span>
                                                    <h6><?= $value['waktu_pelayanan'];?></h6>
                                                </div>
                                                <div class="quota">
                                                    <span>Sisa Kuota</span>
                                                    <span class="sisa-kuota"><?= $value['sisa_kuota'];?></span>
                                                </div>
                                                <button type="button" class="btn btn-primary">Pilih</button>
                                            </div>
                                        </div>
                                    <?php }?>
                                    </div>
                                <?php } else { ?>
                                    <!-- Empty State -->
                                    <div class="wrapper-empty-state">
                                        <div class="card card-poli empty-state" >
                                            <img src="<?=Url::base(true).'/media/img/icon-antrian/inactive-poliklinik.png';?>">
                                            <h6>Data Poli Tidak ditemukan</h6>
                                            <p>Silahkan tambahkan data poli terlebih dahulu..</p>
                                        </div>
                                    </div>
                                <?php }?>
                            </div>
                        <?php } ?>
                    </div>
                    <a class="carousel-control left disabled <?= $hide;?>" href="#slide-poli" data-slide="prev">
                        <i class="fa fa-angle-left fa-lg"></i>
                    </a>
                    <a class="carousel-control right active<?= $hide;?>" href="#slide-poli" data-slide="next">
                        <i class="fa fa-angle-right fa-lg"></i>
                    </a>
                </div>
            </fieldset>
            <?php if ($kuatoAntrian == 1): ?>
                <fieldset title="2"  onmouseover="this.title='';">
                    <legend>Pilih Dokter</legend>
                    <div id="loading-content"></div>
                    <div id="slide-dokter" class="carousel slide" data-ride="carousel" data-interval="false">
                        
                        <!-- <ol class="carousel-indicators">
                            <li data-target="#slide-dokter" data-slide-to="0" class="active"></li>
                            <li data-target="#slide-dokter" data-slide-to="1" ></li>
                        </ol> -->

                        <div class="carousel-inner">
                           
                        </div>
                        <a class="carousel-control left disabled" href="#slide-dokter" data-slide="prev">
                            <i class="fa fa-angle-left fa-lg"></i>
                        </a>
                        <a class="carousel-control right active" href="#slide-dokter" data-slide="next">
                            <i class="fa fa-angle-right fa-lg"></i>
                        </a>
                    </div>
                </fieldset>
            <?php endif ?>

            <?php if ($isKeteranganPasien == 1): ?>
            <fieldset title="<?php echo $kuatoAntrian == 1 ? '3' : '2'; ?>"  onmouseover="this.title='';">
                <legend>Keterangan Pasien</legend>
                <div id="slide-pasien" class="carousel slide" data-ride="carousel" data-interval="false">
                    <div class="item active">
                        <div class="row get-parent">
                            <?php if ($list_klasifikasi_pasien) {
                                foreach ($list_klasifikasi_pasien as $key => $value) { 
                            ?>
                                <div class="col-sm-3">
                                    <div class="card card-pasien" data-klasifikasipasienid="<?= $value['klasifikasipasien_id'];?>" data-carabayar="<?= $value['cara_bayar'];?>" data-toggle="modal", data-target = "#modal_backdrop" action = "/antrian/dashboard/cara-bayar/">
                                        <!-- <img src="<?=Url::base(true).'/media/img/icon-antrian/pasien-bpjs.png';?>" class="rounded"> -->
                                        <h4 class="nama"><?= $value['klasifikasipasien_nama'];?></h4>
                                        <button type="button" class="btn btn-primary">Pilih</button>
                                    </div>
                                </div>
                            <?php }} else {?>
                                <div class="wrapper-empty-state">
                                    <div class="card card-poli empty-state" >
                                        <!-- <img src="<?=Url::base(true).'/media/img/icon-antrian/inactive-poliklinik.png';?>"> -->
                                        <h6>Data Poli Tidak ditemukan</h6>
                                        <p>Silahkan tambahkan data poli terlebih dahulu..</p>
                                    </div>
                                </div>
                            <?php }?>
                        </div>
                    </div>
                </div>
            </fieldset>
            <?php endif ?>
                    
                <button id="btn-cetak-antrian" type="submit" class="button-print stepy-finish"><i class="fa fa-print"></i> Cetak </button>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<?php
    $this->registerCss($this->render('../assets/css/antrian-pendaftaran-page.css'));
    $this->registerJs("
        var baseAsset = '". Url::base(true) . '/media/img/icon-antrian/' ."';
        var carabayar = ". json_encode($list_carabayar) .";
        var kuotAntrian = ". $konfigKuotaAntrian .";
        var konfig_url_cetak = '". $konfig_url_cetak ."';
        var isKeteranganPasien = ". $isKeteranganPasien .";
        var kuatoAntrian = ". $kuatoAntrian .";
        var page = 1;
        var fullscreen = 0;
        var urlParams = new URLSearchParams(window.location.search);
        var detail_id = urlParams.get('detail_id');
        var postData = {
            ruangan_id: '',
            kuota_antrian: kuotAntrian,
            poly_pilih: '',
            dokter_pilih: '',
            status_pilih: '',
            no_rm_pasien: '',
            pasien_id: '',
            jadwaldokter_pilih: '',
            jenisantrian_id: ''
        };

        var klasifikasiPasien = {
            klasifikasipasien_id: '',
            cara_bayar: [],
        }

    " .$this->render('../assets/js/antrian-pendaftaran-page.js'), View::POS_END, 'js' );
?>
