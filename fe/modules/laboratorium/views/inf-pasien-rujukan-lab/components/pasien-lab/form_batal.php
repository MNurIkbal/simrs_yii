<?php

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use kartik\widgets\DatePicker;

?>

<style>
   .datepicker>div{
      display:block;
   }
   .fs-15{
      font-size: 15px;
   }
   .fs-20{
      font-size: 20px;
   }
   .c-not-allowed{
      cursor: not-allowed !important;
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
                  ]
               ],
               'save' => [
                  'title' => \Yii::t('fe', 'Simpan'),
                  'icon' => 'fa fa-save',
                  'attributes' => [
                     'class' => 'btn btn-info btn-labeled btn-xs spa',
                     'data-options' => 'click',
                     'form-id' => 'form-batal',
                     'data-tab' => 'tab-non-rujukan',
                     'data-target' => 'view-non-rujukan',
                     'id' => 'btn-simpan-batal',
                     'action' => 'inf-pasien-rujukan-lab/batal-order?id='.$id
                  ]
               ],
            ]) ?>
         </div>
         <div class="panel-body">
            <div class="col-md-12">
               <div class="panel panel-default">
                  <div class="panel-heading">
                     <h6 class="panel-title"><b><?= Yii::t('fe', 'Informasi Pasien'); ?></b></h6>
                  </div>
                  <div class="panel-body">
                     <div class="form-group">
                        <div class="col-md-4">
                           <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "No rujukan") ?></b></label>
                           <div class="col-sm-7">
                                 <p><b>:</b>&nbsp;<?= isset($detail['no_rujukan']) ? $detail['no_rujukan'] : '-' ?> </p>
                           </div>
                        </div>
                        <div class="col-md-4">
                           <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Asal rujukan") ?></b></label>
                           <div class="col-sm-7">
                                 <p><b>:</b>&nbsp;<?= isset($detail['ruangan_nama']) ? $detail['ruangan_nama'] : '-' ?> </p>
                           </div>
                        </div>
                        <div class="col-md-4">
                           <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Tanggal Rujukan") ?></b></label>
                           <div class="col-sm-7">
                                 <p><b>:</b>&nbsp;<?= isset($detail['tgl_rujukan']) ? DocoHelpers::convDateTime($detail['tgl_rujukan']) : '-' ?> </p>
                           </div>
                        </div>
                     </div>
                     <div class="form-group">
                        <div class="col-md-4">
                           <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Perujuk") ?></b></label>
                           <div class="col-sm-7">
                                 <p><b>:</b>&nbsp;<?= isset($detail['dokter_perujuk']) ? $detail['dokter_perujuk'] : '-' ?>  </p>
                           </div>
                        </div>
                        <div class="col-md-4">
                           <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Nama Pasien") ?></b></label>
                           <div class="col-sm-7">
                                 <p><b>:</b>&nbsp; <?= isset($detail['nama_pasien']) ? $detail['nama_pasien'] : '-' ?> </p>
                           </div>
                        </div>
                        <div class="col-md-4">
                           <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Catatan Dokter") ?></b></label>
                           <div class="col-sm-7">
                                 <p><b>:</b>&nbsp; <?= isset($detail['catatan_dokterpengirim']) ? $detail['catatan_dokterpengirim'] : '-' ?> </p>
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
                        <div class="col-md-12 filter-form"></div>
                     </div>
                     <table id="tb-rencana-pemeriksaan-lab" class="table datatable-basic table-striped table-hover dataTable no-footer" style="width:100%">
                        <thead>
                           <tr class="bg-inverse">
                              <th></th>
                              <th><?= Yii::t('fe', 'No.') ?></th>
                              <th><?= Yii::t("fe", "Jenis Pemeriksaan") ?></th>
                              <th><?= Yii::t("fe", "Nama Pemeriksaan") ?></th>
                              <th><?= Yii::t("fe", "Approve") ?></th>
                           </tr>
                        </thead>
                        <tbody>
                        </tbody>
                     </table>
                  </div>
               </div>
            </div>
            <div class="col-md-12">
               <div class="panel panel-default">
                  <div class="panel-heading">
                     <h6 class="panel-title"><b><?= Yii::t('fe', 'Form Pembatalan'); ?></b></h6>
                  </div>
                  <div class="panel-body">
                     <?php $form = ActiveForm::begin(
                        [
                           'action' => 'batal-order',
                           'id' => 'form-batal',
                           'type' => ActiveForm::TYPE_VERTICAL,
                           'enableAjaxValidation' => false,
                           'enableClientValidation' => false,
                        ]
                     ) ?>
                     <?= Html::hiddenInput('pasienmasukpenunjang_id', DocoHelpers::encrypt($id), ['id' => 'pasien-kirim-unit-lain-id', 'readonly' => 'readonly']) ?>
                     <div class="form-group">
                        <div class="col-md-6">
                           <?= $form->field($model, 'tgl_batalorder')->widget(DatePicker::classname(), [
                              'name' => 'date_12',
                              'value' => date('dd-M-yyyy'),
                              'readonly' => true,
                              'pluginOptions' => [
                                 'autoclose' => true,
                                 'format' => 'dd-M-yyyy',
                                 'endDate' => "0d",
                                 'startDate' => "0d",
                              ]
                           ])->label(Yii::t('fe', 'Tanggal Batal')); ?>
                        </div>
                        <div class="col-sm-6">
                           <?= $form->field($model, 'peg_menyetujui_id')->dropdownList($dataPegawai, [
                              'class' => 'form-control select2 input-sm',
                              'prompt' => \Yii::t('fe', '-- Pilih --'),
                           ])->label(Yii::t("fe", "Disetujui Oleh"));
                           ?>
                        </div>
                     </div>
                     <div class="form-group">
                        <div class="col-md-12">
                           <?= $form->field($model, 'alasan')->textarea(['rows' => '4']) ?>
                        </div>
                     </div>
                     <?php ActiveForm::end(); ?>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<?php
$this->registerJsFile(
   '/js/dataTables.checkboxes.min.js',
   [
      'depends' => [
         'app\assets\AppAsset',
      ]
   ]
);

$this->registerJs('
   var id = "'.DocoHelpers::encrypt($id). '";
   var pegawai_id = "'.@$dataPegawai['pegawai_id']. '";
   var nama_pegawai = "'.@$dataPegawai['nama_pegawai']. '";
', View::POS_END, 'b-index');

$this->registerJs($this->render('js/form_batal.js'), View::POS_END);
?>
