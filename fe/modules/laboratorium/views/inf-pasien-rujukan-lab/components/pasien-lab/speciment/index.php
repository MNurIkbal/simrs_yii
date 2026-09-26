<?php

/**
 * @author Randy Vianda Putra
 * @todo View Input speciment laboratorium
 * @copyright 09 Juli 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\modules\master\models\DisplayAntrianForm;
use Doco\master\controllers\DisplayAntrianController;
use yii\widgets\Breadcrumbs;
use app\components\DocoTableHelper;
use kartik\datetime\DateTimePicker;

$this->title = \Yii::t('fe', $title);
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
</style>

<div class="row">
   <div class="col-md-12">
      <div class="panel panel-white">
         <div class="panel-toolbar clearfix">
            <?= DocoHelpers::generateToolbar([
            'back' => [
               'title' => \Yii::t('fe', 'Kembali'),
               'icon' => 'fa fa-arrow-left',
               'attributes' => [
                  'class' => 'btn btn-info btn-labeled btn-xs spa',
                  'data-options' => 'click',
                  'data-render' => 'pasien-lab?id=',
                  'data-tab' => 'tab-non-rujukan',
                  'data-target' => '#view-non-rujukan',
                  'id' => 'btn-kembali-speciment',
               ]
            ],
            'save' => [
               'title' => \Yii::t('fe', 'Simpan'),
               'icon' => 'fa fa-check-square-o',
               'attributes' => [
                  'class' => 'btn btn-info btn-labeled btn-xs spa',
                  'id' => 'btn-simpan-speciment',
                  'data-options' => 'click',
               ]
            ],
            'batal' => [
               'title' => \Yii::t('fe', 'Batal Ambil Speciment'),
               'icon' => 'fa fa-times-circle-o',
               'attributes' => [
                  'class' => 'btn btn-info btn-labeled btn-xs ',
                  'id' => 'btn-batal-speciment',
                  'data-options' => 'click',
                  'data-confirm-message' => 'Apakah Anda yakin akan membatalkan input hasil Laboratorium?'
               ]
            ],
            ]) ?>
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
               <div class="col-md-12 panel panel-default" id="informasi" style="margin-top:10px;">
                  <div class="panel-heading">
                     <h6 class="panel-title text-bold"><?= Yii::t('fe', 'Data Specimen') ?>
                        <a class="heading-elements-toggle"><i class="icon-more"></i></a>
                     </h6>
                  </div>
                  <div class="panel-body">
                     <div style="margin-bottom: 5px;">
                        <?php 
                           $disabled = ($count_tindakan > 0) ? false : true;
                           echo Html::button('<i class="fa fa-plus"></i> ' . \Yii::t('fe', 'Tambah'), [
                              'class' => 'btn btn-success btn-md',
                              'id' => 'btn-add-modal',
                              'data-toggle' => 'modal',
                              'disabled' => $disabled,
                              'data-target' => '#modal_backdrop',
                              'action' => '/laboratorium/inf-pasien-rujukan-lab/add-speciment?id='.$id,
                           ]);
                        ?>
                     </div>
                     <div id="temp-sample">
                        <h6><?= Yii::t('fe', 'Tambah Sample'); ?></h6>
                        <table class="table datatable-basic table-striped table-hover dataTable no-footer">
                           <thead>
                              <tr class="bg-inverse">
                                 <th><?= Yii::t('fe', 'No') ?></th>
                                 <th><?= Yii::t('fe', 'Speciment') ?></th>
                                 <th><?= Yii::t('fe', 'Tanggal') ?></th>
                                 <th><?= Yii::t('fe', 'Jam') ?></th>
                                 <th><?= Yii::t('fe', 'Jumlah') ?></th>
                                 <th><?= Yii::t('fe', 'Satuan') ?></th>
                                 <th><?= Yii::t('fe', 'Keterangan') ?></th>
                                 <th><?= Yii::t('fe', 'Nama pemeriksaan') ?></th>
                                 <th><?= Yii::t('fe', 'Hapus') ?></th>
                              </tr>
                           </thead>
                           <tbody id="list-sample">
                           </tbody>
                        </table>
                     </div>
                     <h6><?= Yii::t('fe', 'History Sample'); ?></h6>
                     <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="table-sample">
                        <thead>
                           <tr class="bg-inverse">
                              <th><?= Yii::t('fe', 'No') ?></th>
                              <th><?= Yii::t('fe', 'Speciment') ?></th>
                              <th><?= Yii::t('fe', 'Tanggal') ?></th>
                              <th><?= Yii::t('fe', 'Jam') ?></th>
                              <th><?= Yii::t('fe', 'Jumlah') ?></th>
                              <th><?= Yii::t('fe', 'Satuan') ?></th>
                              <th><?= Yii::t('fe', 'Keterangan') ?></th>
                              <th><?= Yii::t('fe', 'Nama pemeriksaan') ?></th>
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

<?php
$this->registerJs("
    var transLab = ".$transLab.";
    var id = ".$pasienmasukpenunjang_id.";
    var count_tindakan = ".$count_tindakan.";
    var is_ambil_sample = $is_ambil_sample;
    ", VIEW::POS_END, 'js-kunings');

    $this->registerJs($this->render('js/speciment.js'), View::POS_END);
?>
