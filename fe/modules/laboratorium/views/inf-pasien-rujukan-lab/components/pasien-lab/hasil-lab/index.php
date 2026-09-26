<?php

/**
 * @author Randy Vianda Putra
 * @todo View Input Hasil laboratorium
 * @copyright 13 Juli 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use yii\widgets\Breadcrumbs;
use app\components\DocoTableHelper;
use kartik\datetime\DateTimePicker;

$this->title = \Yii::t('fe', 'Input Hasil Laboratorium');
$this->params['breadcrumbs'][] = ['label' => \Yii::$app->docoVars->workspace("modul_alias"), 'url' => ['/laboratorium']];
$this->params['breadcrumbs'][] = $title;
?>

<style lang="">
   .info-pasien {
      width: 70px;
      height: 30px;
      border-radius: 5px;
      border: 1px solid black;
      float: left;
      margin: 3px;
   }
   .unverifBtn,.unverifBtn:hover,.unverifBtn:active,.unverifBtn:focus{
      color: #fff !important;
      background-color: #FF5722 !important;
      border-color: #FF5722 !important;
   }
</style>

<div class="row">
   <div class="col-md-12">
      <div class="panel panel-white">
         <div class="panel-toolbar clearfix">
            <?= DocoHelpers::generateToolbar([
               'kembali' => [
                  'title' => \Yii::t('fe', 'Kembali'),
                  'icon' => 'fa fa-arrow-left',
                  'attributes' => [
                     'class' => 'btn btn-info btn-labeled btn-xs spa',
                     'data-options' => 'click',
                     'data-render' => $dataRender,
                     'data-tab' => $dataTab,
                     'data-target' => $dataTarget,
                     'id' => 'btn-kembali-hasil',
                  ]
               ],
               'input' => [
                  'title' => \Yii::t('fe', 'Input Hasil'),
                  'icon' => 'fa fa-pencil',
                  'attributes' => [
                     'class' => 'btn btn-info btn-labeled btn-xs spa',
                     'data-options' => 'click',
                     'data-render' => 'input-hasil?id='.$id,
                     'data-tab' => $dataTab,
                     'data-target' => $dataTarget,
                     'id' => 'btn-input',
                  ]
               ],
               'obat' => [
                  'title' => \Yii::t('fe', 'Tindakan/BMHP'),
                  'icon' => 'fa fa-medkit',
                  'attributes' => [
                     'class' => 'btn btn-info btn-labeled btn-xs spa',
                     'data-options' => 'click',
                     'data-render' => 'order-obat?id='.$id.'&pendaftaran_id='.$pendaftaran_id,
                     'data-tab' => $dataTab,
                     'data-target' => $dataTarget,
                     'id' => 'obat-alkes',
                  ]
               ],
               'verifikasi' => [
                  'title' => \Yii::t('fe', 'Verifikasi'),
                  'icon' => 'fa fa-key',
                  'attributes' => [
                     'data-options' => 'click',
                     'id' => 'verifikasi',
                     'data-target' => '/laboratorium/hasil-lab/verifikasi?id='.$id,
                  ]
               ],
               'cetak' => [
                  'title' => \Yii::t('fe', 'Cetak Pemeriksaan'),
                  'icon' => 'fa fa-print',
                  'attributes' => [
                     'data-target' => '/laboratorium/hasil-lab/cetak-hasil?id=',
                     'data-conditions' => 'penunjang_id',
                     'data-pages' => '_blank',
                     'id' => 'print-result',
                     'disabled' => 'disabled',
                  ]
               ],
               'unverifikasi' => [
                  'title' => \Yii::t('fe', 'Unverifikasi'),
                  'icon' => 'fa fa-key',
                  'attributes' => [
                     'data-options' => 'click',
                     'id' => 'unverifikasi',
                     'data-target' => '/laboratorium/hasil-lab/unverifikasi?id='.$id,
                     'style' => "display:none;"
                  ]
               ],
               'batal' => [
                  'title' => \Yii::t('fe', 'Batal Input Hasil'),
                  'icon' => 'fa fa-times-circle-o',
                  'attributes' => [
                     'data-options' => 'click',
                     'id' => 'btn-batal-input',
                     'data-target' => '/laboratorium/hasil-lab/batal-input?id='.$id,
                  ]
               ],
               'upload_dokumen' => [
                 'type'  => 'button',
                  'title' => Yii::t('fe', 'Upload Dokumen'),
                  'icon'  => 'fa fa-upload',
                  'attributes' => [
                     'disabled' => false,
                     'id'          => 'btn-upload-dokumen',
                     'data-width'  => '90%',
                     'data-toggle' => 'modal',
                     'data-target' => '#modal_riwayat',
                     'action'      => '/laboratorium/inf-pasien-rujukan-lab/upload-dokumen?id='.$pendaftaran_id.'&pasienadmisi_id='.$pasienadmisi_id.'&is_modal=true',
                  ]
               ],
            ],'#table-hasil-lab');
            ?>
         </div>
         <div class="panel-body">
            <div class="row row-eq-height " style="margin-top:10px;">
               <div class="col-md-8" id="informasi">
                  <div class="panel panel-default">
                     <a id="info-heading" data-toggle="collapse" href="#infopasien" role="button" aria-expanded="false" aria-controls="infopasien" >
                        <div class="panel-heading flex-container">
                           <h6 class="panel-title"><?= Yii::t('fe', 'Informasi Pasien') ?></h6>
                           <p class="p-data" id="data-pasien">
                              <?= isset($data_pasien['no_rekam_medik']) ? $data_pasien['no_rekam_medik'] : '-' ?> -
                              <b class="font" ><?= isset($data_pasien['nama_pasien']) ? $data_pasien['nama_pasien'] : '-' ?></b>
                           </p>
                           <ul class="icons-list">
                              <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                           </ul>
                        </div>
                     </a>
                     <div class="panel-body collapse multi-collapse info-card" id="infopasien">
                        <div class="col-xs-2">
                           <div class="border-img">
                              <?php
                              $filename = isset($data_pasien['photopasien']) ? !empty($data_pasien['photopasien']) ? '/media/img/pasien/'.$data_pasien['photopasien']: '/media/img/icon-app/default.jpg' : '/media/img/icon-app/default.jpg';
                              ?>
                              <?=Html::img($filename, [ 'style'=>'width: 100%;height: auto;max-width: 114px;', 'class'=>'img-responsive'])?>
                           </div>
                        </div>
                        <div class="col-xs-9">
                           <div class="row">
                              <br>
                              <div class="col-xs-6">
                                 <b class="text-left control-label font-design"><?= Yii::t("fe", "Pasien") ?></b>
                                 <br>
                                 <p>
                                    <?= isset($data_pasien['no_rekam_medik']) ? $data_pasien['no_rekam_medik'] : '-' ?> -
                                    <?= isset($data_pasien['nama_pasien']) ? $data_pasien['nama_pasien'] : '-' ?>
                                 </p>
                                 <b class="text-left control-label font-design"><?= Yii::t("fe", "No Telepon") ?></b>
                                 <p>
                                    <?= isset($data_pasien['no_telepon_pasien']) ? $data_pasien['no_telepon_pasien'] : '-' ?>
                                 </p>
                              </div>
                              <div class="col-xs-6">
                                 <b class="text-left control-label font-design"><?= Yii::t("fe", "Pendaftaran") ?></b>
                                 <p>
                                    <?= isset($data_pasien['no_pendaftaran']) ? $data_pasien['no_pendaftaran'] : '-' ?> -
                                    (<?= isset($data_pasien['tglmasukpenunjang']) ? date('d-M-Y', strtotime($data_pasien['tglmasukpenunjang'])) : '-' ?>)
                                 </p>
                                 <b class="text-left control-label font-design"><?= Yii::t("fe", "Kelas pelayanan") ?></b>
                                 <p>
                                    <?= isset($data_pasien['kelaspelayanan_nama']) ? $data_pasien['kelaspelayanan_nama'] : '-' ?> -
                                    <?= isset($data_pasien['carabayar_nama']) ? $data_pasien['carabayar_nama'] : '-' ?> -
                                    <?= isset($data_pasien['penjamin_nama']) ? $data_pasien['penjamin_nama'] : '-' ?>
                                 </p>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="panel panel-default">
                     <a id="info-heading" data-toggle="collapse" href="#infodetail" role="button" aria-expanded="false" aria-controls="infopasien" >
                        <div class="panel-heading flex-container">
                           <h6 class="panel-title"><b><?= Yii::t('fe', 'Detail Informasi Pasien'); ?></b></h6>
                           <ul class="icons-list">
                              <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                           </ul>
                        </div>
                     </a>
                     <div class="panel-body column-info collapse multi-collapse info-card" id="infodetail">
                        <div class="row row-eq-height">
                           <br>
                           <div class="col-xs-6">
                              <b class="text-left control-label font-design"><?= Yii::t("fe", " Instalasi Akhir") ?></b>
                              <p>
                                 <?= isset($data_pasien['asalrujukan_nama']) ? $data_pasien['asalrujukan_nama'] : '-' ?>
                              </p>
                           </div>
                           <div class="col-xs-6">
                              <b class="text-left control-label font-design"><?= Yii::t("fe", "Ruangan akhir") ?></b>
                              <p>
                                 <?= !empty($data_pasien['ruangan_nama']) ? $data_pasien['ruangan_nama'] : null ?>
                              </p>
                           </div>
                           <div class="col-md-12">
                              <?php
                                 $kuning = !empty($data_pasien['kuning']) ? 'block' : 'none';
                                 $warna_kuning = !empty($data_pasien['kuning']) ? $data_pasien['kuning'] : '';
                                 $ungu = !empty($data_pasien['ungu']) ? 'block' : 'none';
                                 $warna_ungu = !empty($data_pasien['ungu']) ? $data_pasien['ungu'] : '';
                                 $merah = !empty($data_pasien['merah']) ? 'block' : 'none';
                                 $warna_merah = !empty($data_pasien['merah']) ? $data_pasien['merah'] : '';
                                 $coklat = !empty($data_pasien['coklat']) ? 'block' : 'none';
                                 $warna_coklat = !empty($data_pasien['coklat']) ? $data_pasien['coklat'] : '';
                              ?>
                              <div class="info-pasien" style="background-color:<?= $warna_kuning; ?>; display:<?= $kuning; ?>;"></div>
                              <div class="info-pasien" style="background-color:<?= $warna_ungu; ?>; display:<?= $ungu; ?>;"></div>
                              <div class="info-pasien" style="background-color:<?= $warna_merah; ?>; display:<?= $merah; ?>;"></div>
                              <div class="info-pasien" style="background-color:<?= $warna_coklat; ?>; display:<?= $coklat; ?>;"></div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <?= Yii::$app->controller->renderPartial('components/pasien-lab/hasil-lab/_info_rujukan', [
               'data_rujukan' => $data_rujukan,
               'isReferred' => $isReferred
            ]) ?>
            <div class="col-md-12 panel panel-default" id="informasi" style="margin-top:10px;">
               <div class="panel-heading">
                  <h6 class="panel-title text-bold"><?= Yii::t('fe', 'Hasil Pemeriksaan Laboratorium') ?>
                     <a class="heading-elements-toggle"><i class="icon-more"></i></a>
                  </h6>
               </div>
               <div class="panel-body">
                  <div class="col-md-12 filter-form"></div>
                  <h6 class="text-bold"><?= Yii::t('fe', 'Catatan') ?></h6>
                  <textarea class="form-control" name="" id="" cols="20" rows="5" readonly=""><?= $catatan ?></textarea>
                  <br>
                  <span style="display:none;color:green;font-size:14px;" class="info-verifikasi"></span>
                  <?php
                     if ($count_expertise == 0 && $data['data_pasien']['status_periksa'] == DocoConstants::ST_SELESAI_PNNJG) {
                     $tanggalVerifikasi = date('d M Y H:i:s', strtotime($data['data_pasien']['tanggal_verifikasi']));
                        echo "<div style='color:green;font-size:14px;' class='terverifikasi'>Hasil sudah terverifikasi pada tanggal {$tanggalVerifikasi}</div>";
                     }
                  ?>
                  <table id="table-hasil-lab" class="table datatable-basic table-striped table-hover dataTable no-footer">
                     <thead>
                        <tr class="bg-inverse">
                           <th></th>
                           <th><?= Yii::t('fe', 'No') ?></th>
                           <th><?= Yii::t('fe', 'Sample') ?></th>
                           <th><?= Yii::t('fe', 'Nama pemeriksaan') ?></th>
                           <th><?= Yii::t('fe', 'Expertise') ?></th>
                        </tr>
                     </thead>
                     <tbody id="list-sample">
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<div id="modal-preview" class="modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-header bg-inverse" style="z-index: 1050">
            <button type="button" id="dismiss-preview-btn" class="close" data-dismiss="modal">&times;</button>
            <h5 class="modal-title">Preview</h5>
        </div>
        <div class="modal-content">
            <div class="preview-wrapper" style="position: relative;" id="preview-wrapper">
                <!-- <div class="overlay-preview"></div> -->
                <iframe frameborder="0" id="preview-content" style="width:100%;height:85vh"></iframe>
            </div>
        </div>
    </div>
</div>

<div id="modal-preview-img" class="modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

        </div>
    </div>
</div>

<div id="modal_riwayat" class="modal">
    <div class="modal-dialog modal-xl" style="width: 90%;">
        <div class="modal-content">
        </div>
    </div>
</div>

<?php
   $data_pasien = json_encode($data_pasien);
   $id = DocoHelpers::decrypt($id);
   $this->registerJs("
      var id = '$id'; 
      var isFinish = '$isFinish';
      var dataPasien = $data_pasien;
      var pasienMasukPenunjangId = '$pasienmasukpenunjang_id';
      var isBayar = $is_bayar;
      var is_periksa = $is_periksa;
      var isException = '".$isException."';
   ", View::POS_END);
   $this->registerJs($this->render('js/index.js'), View::POS_END);
?>
