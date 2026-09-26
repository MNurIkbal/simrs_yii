<?php

/**
 * @author Randy Vianda Putra
 * @todo Master Jenis Pemeriksaan Radiologi
 * @copyright 03 Juli 2018 aweutist
 */

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\widgets\Select2;
use yii\web\JsExpression;

use kartik\widgets\ActiveForm;

?>
<style type="text/css">
   .c-not-allowed:after{
      cursor: not-allowed !important;
   }
   .c-not-allowed-row{
      background-color: #e0e0e0 !important;
   }
</style>

<div class="panel panel-white">
   <div class="panel-toolbar clearfix">
         <?= DocoHelpers::generateToolbar([
            'back' => [
               'title' => \Yii::t('fe', 'Kembali'),
               'icon' => 'fa fa-arrow-left',
               'attributes' => [
                  'class' => 'btn btn-info btn-labeled btn-xs spa',
                  'data-options' => 'click',
                  'data-render' => 'rujukan',
                  'data-tab' => 'tab-rujukan',
                  'data-target' => '#view-rujukan',
               ]
            ],
            'save' => [
               'title' => \Yii::t('fe', 'Approve'),
               'icon' => 'fa fa-check-square-o',
               'attributes' => [
                  'class' => 'btn btn-info btn-labeled btn-xs spa',
                  'data-options' => 'click',
                  'form-id' => 'form-approve',
                  'data-render' => 'rujukan',
                  'data-tab' => 'tab-rujukan',
                  'data-target' => 'view-rujukan',
                  'id' => 'btn-simpan-approve',
                  'action' => '/laboratorium/inf-pasien-rujukan-lab/approve?id='.$id
               ]
            ],
            'add-tindakan' => [
               'title' => \Yii::t('fe', 'Tambah Tindakan'),
               'icon' => 'fa fa-plus',
               'attributes' => [
                   'data-toggle' => 'modal',
                   'data-target' => '#modal_backdrop',
                   'data-width' => '80%',
                   'action' => '/laboratorium/inf-pasien-rujukan-lab/form-modal-tindakan?penjamin_id='.$penjaminId.'&kelaspelayanan_id='.$kelasPelayananId.'&pasienkirimkeunitlain_id='.$id,
               ]
           ],
         ], '#tb-inf-pasien-rujukan-lab') ?>
   </div>
   <div class="panel-body"><br>
         <div class="col-md-12">
            <div class="col-md-8" id="informasi">
               <div class="panel panel-default">
                     <a id="info-heading" data-toggle="collapse" href="#infopasien" role="button" aria-expanded="false" aria-controls="infopasien" >
                        <div class="panel-heading flex-container">
                           <h6 class="panel-title"><?= Yii::t('fe', 'Informasi Pasien') ?></h6>
                           <p class="p-data" id="data-pasien">
                                 <?= isset($detail['no_rekam_medik']) ? $detail['no_rekam_medik'] : '-' ?> -
                                 <b class="font" ><?= isset($detail['nama_pasien']) ? $detail['nama_pasien'] : '-' ?></b>
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
                                 $filename = isset($detail['photopasien']) ? !empty($detail['photopasien']) ? '/media/img/pasien/'.$detail['photopasien']: '/media/img/icon-app/default.jpg' : '/media/img/icon-app/default.jpg';
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
                                       <?= isset($detail['no_rekam_medik']) ? $detail['no_rekam_medik'] : '-' ?> -
                                       <?= isset($detail['nama_pasien']) ? $detail['nama_pasien'] : '-' ?>
                                    </p>

                                    <b class="text-left control-label font-design"><?= Yii::t("fe", "No Telepon") ?></b>
                                    <p>
                                       <?= isset($detail['no_telepon_pasien']) ? $detail['no_telepon_pasien'] : '-' ?>
                                    </p>

                                 </div>
                                 <div class="col-xs-6">
                                    <b class="text-left control-label font-design"><?= Yii::t("fe", "Pendaftaran") ?></b>
                                    <p>
                                       <?= isset($detail['no_pendaftaran']) ? $detail['no_pendaftaran'] : '-' ?> -
                                       (<?= isset($detail['tgl_pendaftaran']) ? date('d-M-Y', strtotime($detail['tgl_pendaftaran'])) : '-' ?>)
                                    </p>

                                    <b class="text-left control-label font-design"><?= Yii::t("fe", "Kelas pelayanan") ?></b>
                                    <p>
                                       <?= isset($detail['kelaspelayanan_nama']) ? $detail['kelaspelayanan_nama'] : '-' ?> -
                                       <?= isset($detail['carabayar_nama']) ? $detail['carabayar_nama'] : '-' ?> -
                                       <?= isset($detail['penjamin_nama']) ? $detail['penjamin_nama'] : '-' ?>
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
                                    <?= isset($detail['instalasiasal_nama']) ? $detail['instalasiasal_nama'] : '-' ?>
                                 </p>
                           </div>
                           <div class="col-xs-6">
                                 <b class="text-left control-label font-design"><?= Yii::t("fe", "Ruangan akhir") ?></b>
                                 <p>
                                    <?= !empty($detail['ruanganasal_nama']) ? $detail['ruanganasal_nama'] : null ?>
                                 </p>
                           </div>
                        </div>
                     </div>
               </div>
            </div>
         </div>
         <div class="col-md-12">
            <div class="panel panel-default">
               <div class="panel-heading">
                     <h6 class="panel-title"><b><?= Yii::t('fe', 'Rencana Pemeriksaan Laboratorium'); ?></b></h6>
               </div>
               <div class="panel-body">
                     <div class="row">
                        <div class="col-md-12">
                           <?php $form = ActiveForm::begin(
                              [
                              'action'=> 'inf-pasien-rujukan-lab/approve?id='.$id,
                              'id' => 'form-approve',
                              'type' => ActiveForm::TYPE_HORIZONTAL,
                              'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL],
                              'fieldConfig' => ['enableLabel' => false],
                              'enableAjaxValidation' => false,
                              'enableClientValidation' => false,
                           ]) ?>
                           <?= Html::hiddenInput('pasienkirimkeunitlain_id', DocoHelpers::encrypt($id), ['id' => 'pasien-kirim-unit-lain-id', 'readonly' => 'readonly']) ?>
                           <br>
                           <div class="row">
                              <div class="col-md-2">
                                 <label class="text-left control-label required"><b><?= Yii::t("fe", "Dokter Laboratorium") ?></b> <span class="text-danger">*</span> </label>
                              </div>
                              <div class="col-md-4">
                                 <?= $form->field($model, 'pegawai_id')->dropdownList($dokter, [
                                    'class' => 'form-control select2 input-sm dokter-lab',
                                    'prompt' => \Yii::t('fe', '-- Pilih --'),
                                 ]);
                                 ?>
                              </div>
                           </div>
                           <div class="row">
                              <div class="col-md-2">
                                 <label class="text-left control-label required"><b><?= Yii::t("fe", "Catatan Dokter/Diagnosa Medis") ?></b> </label>
                              </div>
                              <div class="col-md-4">
                                 <?= $form->field($model, 'catatan_dokterpengirim')->textarea(["row"=>"6", 'class' => 'catatan_dokter']) ?>
                              </div>
                           </div>
                           <?php ActiveForm::end(); ?>
                           <br><br>
                        </div>
                     </div>
                     <div class="row">
                        <table id="tb-rencana-pemeriksaan-lab" class="table table-striped table-condensed table-hover" style="width:100%">
                           <thead>
                              <tr class="bg-inverse">
                                 <th width="1">&nbsp;</th>
                                 <th><?= Yii::t('fe', 'No') ?></th>
                                 <th><?= Yii::t("fe", "Jenis Pemeriksaan") ?></th>
                                 <th><?= Yii::t("fe", "Nama Pemeriksaan") ?></th>
                                 <th><?= Yii::t("fe", "Qty") ?></th>
                                 <th><?= Yii::t("fe", "Cyto") ?></th>
                              </tr>
                           </thead>
                           <tbody>
                           </tbody>
                        </table>
                     </div>
               </div>
            </div>
         </div>
   </div>
</div>
<?php
$this->registerJs('
    var id = "'.DocoHelpers::encrypt($id). '";
', View::POS_END, 'b-index');

$this->registerJs($this->render('js/form_aproval.js'), View::POS_END);
?>
