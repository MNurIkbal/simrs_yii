<?php

   /**
    * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
    * A product of PT. Docotel Teknologi
    * Powered by Sirs
    */

   use yii\web\View;
   use yii\helpers\ArrayHelper;
   use kartik\widgets\ActiveForm;
   use yii\helpers\Html;
   use yii\helpers\Url;
   use yii\widgets\Breadcrumbs;
   use app\components\DocoHelpers;
   use kartik\widgets\DatePicker;
   use kartik\widgets\DateTimePicker;
   use kartik\widgets\DepDrop;
?>
<style>
    .datepicker>div{
        display:block;
    }
    .btn-custom {
        padding : 5.5px 12px!important;
    }
    .modal {
      overflow: auto;
    }

    .modal-body {
      overflow: visible;
    }

</style>
<div class="modal-header bg-inverse">
   <button type="button" class="close close-modal" >&times;</button>
   <h5 class="modal-title"><?= $title ?></h5>
</div>
<div class="modal-body">
   <?php $form = ActiveForm::begin([
      'id' => 'tindakan-form',
      'action' => '/penatajasa/inf-tagihan-pasien/show-popup-simpan-tindakan?pendaftaran_id='.$pendaftaran_id,
      // 'action' => '/penatajasa/inf-tagihan-pasien/save-tindakan?pendaftaran_id='.$pendaftaran_id,
      'enableAjaxValidation'=>false,
      'enableClientValidation'=>false,
      'type' => ActiveForm::TYPE_VERTICAL,
      'formConfig' => [
         'labelSpan' => 3,
         'deviceSize' => ActiveForm::SIZE_SMALL
      ],
   ]);
   ?>
   
   <?php if (!empty($dataPasien['tglpasienpulang'])) { ?>
      <div class="alert alert-warning" role="alert">
         <p>Informasi Pasien:</p>
         <p>Tanggal Pendaftaran Pasien : <b><?= date('d M Y H:i:s', strtotime($tglPendaftaran)) ?></b></p>
         <p>Pasien Pulang / Stop Akomodasi tanggal  : <b><?= date('d M Y H:i:s', strtotime($tglpasienpulang)) ?></b></p>
      </div>
   <?php } ?>

   <div class="row">
      <div class= "col-md-4">
         <?= $form->field($model, 'tanggal_tindakan', [
            'horizontalCssClasses' => [
               'label' => 'text-left control-label col-sm-4',
               'wrapper' => 'col-md-5'
               ]
            ])->widget(DateTimePicker::classname(), [
               'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
               'readonly' => true,
               'language' => 'en',
               'pluginOptions' => [
                  'startDate' => date('d-M-Y H:i:s', strtotime($startDate)),
                  'endDate' => date('d-M-Y H:i:s', strtotime($endDate)),
                  'autoclose' => true,
                  'todayBtn' => true,
                  'format' => 'dd-M-yyyy hh:ii:ss',
               ]
         ]); ?>
      </div>
      <div class= "col-md-4">
         <?= $form->field($model, 'kelas_pelayanan',[
               'horizontalCssClasses' => [
                  'label' => 'text-left control-label col-sm-4',
                  'wrapper' => 'col-md-5'
               ]
            ])->dropDownList($kelasPelayanan,[
               'class' => 'select2',
               'id' => 'kelas_pelayanan',
               'prompt' => '— Pilih —'
            ]);
         ?>
      </div>
      <div class= "col-md-4">                
         <?= $form->field($model, 'dokter_pj',[
            'horizontalCssClasses' => [
               'label' => 'text-left control-label col-sm-4',
               'wrapper' => 'col-md-5'
            ]
         ])->widget(DepDrop::classname(), [
               'options' => [
                  'id'=>'dokter_pj',
                  'class' => 'form-control select2',
               ],
               'pluginOptions'=>[
                  'depends' => ['instalasi_id','ruangan_id'],
                  'placeholder' => Yii::t('fe', '— Pilih —'),
                  'url'=> Url::to(['inf-tagihan-pasien/get-dokter?ruangan_id='.$dataPasien['ruangan_id'].'&instalasi_id='.$dataPasien['instalasi_id'].'&dokter_pj='.$dataPasien['pegawai_id'] ]),
                  'prompt' => Yii::t('fe', '— Pilih —'),
               ],
         ]);
            ?>
      </div>
   </div>

   <div class="row">
      <div class="col-md-4">
         <?= $form->field($model, 'instalasi_id',[
               'horizontalCssClasses' => [
                  'label' => 'text-left control-label col-sm-4',
                  'wrapper' => 'col-md-5'
               ]
            ])->dropDownList($instalasi,[
               'class' => 'select2',
               'id' => 'instalasi_id',
               'prompt' => '— Pilih —'
            ]);
         ?>
      </div>
      <div class="col-md-4">
         <?= $form->field($model, 'ruangan_id',[
               'horizontalCssClasses' => [
                  'label' => 'text-left control-label col-sm-4',
                  'wrapper' => 'col-md-5'
               ]
               ])->widget(DepDrop::classname(), [
               'options' => [
                  'id'=>'ruangan_id',
                  'class' => 'form-control select2',
               ],
               'pluginOptions'=>[
                  'depends' => ['instalasi_id'],
                  'placeholder' => Yii::t('fe', '— Pilih Ruangan —'),
                  'initialize' => true,
                  'url'=> Url::to(['inf-tagihan-pasien/get-ruangan?ruangan_id='.$dataPasien['ruangan_id'].'&instalasi_id='.$dataPasien['instalasi_id']]),
                  'prompt' => Yii::t('fe', '— Pilih Ruangan —'),
               ],
            ]);
         ?>
      </div>
      <div class="col-md-4">
         <?= $form->field($model, 'perawat',[
               'horizontalCssClasses' => [
                  'label' => 'text-left control-label col-sm-4',
                  'wrapper' => 'col-md-5'
               ]
            ])->widget(DepDrop::classname(), [
               'options' => [
                  'id'=>'perawat',
                  'class' => 'form-control select2',
               ],
               'pluginOptions'=>[
                  'depends' => ['instalasi_id','ruangan_id'],
                  'placeholder' => Yii::t('fe', '— Pilih —'),
                  'url'=> Url::to(['inf-tagihan-pasien/get-perawat?ruangan_id='.$dataPasien['ruangan_id'].'&instalasi_id='.$dataPasien['instalasi_id'].'&perawat='.$dataPasien['pegawai_id'] ]),
                  'prompt' => Yii::t('fe', '— Pilih —'),
               ],
            ]);
         ?>
      </div>            
   </div>
   <div class="row">
      <div class="col-md-4">
      </div>
      <div class="col-md-4 div_kamar_ruangan">
          <?= 
         $form->field($model, 'kamarruangan_id',[
               'horizontalCssClasses' => [
                  'label' => 'text-left control-label col-sm-4',
                  'wrapper' => 'col-md-5'
               ]
               ])->widget(DepDrop::classname(), [
               'options' => [
                  'id'=>'kamarruangan_id',
                  'class' => 'form-control select2',
               ],
               'pluginOptions'=>[
                  'depends' => ['ruangan_id','kelas_pelayanan'],
                  'placeholder' => Yii::t('fe', '— Pilih Kamar —'),
                  'url'=> Url::to(['inf-tagihan-pasien/get-kamar?ruangan_id='.$dataPasien['ruangan_id'].'&instalasi_id='.$dataPasien['instalasi_id'].'&kamarruangan_id='.$dataPasien['pegawai_id']]),
                  'prompt' => Yii::t('fe', '— Pilih Kamar —'),
               ],
            ]);
         ?> 
      </div>
      <div class="col-md-4 div_tempat_tidur">
         <?= 
            $form->field($model, 'kamartempattidur_id',[
                  'horizontalCssClasses' => [
                     'label' => 'text-left control-label col-sm-4',
                     'wrapper' => 'col-md-5'
                  ]
                  ])->widget(DepDrop::classname(), [
                  'options' => [
                     'id'=>'kamartempattidur_id',
                     'class' => 'form-control select2',
                  ],
                  'pluginOptions'=>[
                     'depends' => ['kamarruangan_id','ruangan_id'],
                     'placeholder' => Yii::t('fe', '— Pilih No Tempat Tidur —'),
                     'url'=> Url::to(['inf-tagihan-pasien/get-no-tempat-tidur?ruangan_id='.$dataPasien['ruangan_id']]),
                     'prompt' => Yii::t('fe', '— Pilih No Tempat Tidur —'),
                     'params' => ['kamarruanganID'],
                     'loadingText' => Yii::t('fe', '— Pilih —'),
                  ],
               ]);
         ?> 
      </div>
   </div>        
   <hr>

   <div class="row">
      <div class="col-md-9">
            <?= $form->field($model, 'tindakan_pelayanan_id',[
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-5',
                ],
                'addon' => [
                    'prepend' => [
                        'content' => Html::checkbox('Paket', false, [
                            'id' => 'is_paket', 
                            'label' => Yii::t('fe', 'Paket')
                        ]),
                    ],
                ]
                ])->dropDownList([],[
                    'class' => 'select2',
                    'id' => 'tindakan_paket_id',
                    'prompt' => 'Pilih',
                ])->label(Yii::t('fe', 'Tindakan/Paket/Akomodasi'));
            ?>
      </div>            
      <div class="col-md-1">
         <?= $form->field($model, 'qty', [
            'horizontalCssClasses' => [
                  'label' => 'text-left control-label col-sm-4',
                  'wrapper' => 'col-md-5'
               ]
            ])->textInput([
               'placeholder' => Yii::t('fe', 'Qty'),
               'id' => 'tindakan_qty',
               'class' => 'form-control text-right doco-number',
            ]); 
         ?>
      </div>
      <div class="col-md-2">
         <?= $form->field($model, 'harga_satuan', [
            'addon' => ['prepend' => ['content'=>'Rp.']],
            'horizontalCssClasses' => [
                  'label' => 'text-left control-label col-sm-4',
                  'wrapper' => 'col-md-5'
               ]
            ])->textInput([
               'placeholder' => Yii::t('fe', 'Harga Satuan'),
               'id' => 'harga_satuan',
               'readonly' => $isHargaReadOnly,
               'value' => '0',
               'class' => 'form-control text-right doco-number',
            ]); 
         ?>
      </div>
   </div>

   <div class="row">
      <div class="col-md-4">
         <?= $form->field($model, 'total', [
            'addon' => ['prepend' => ['content'=>'Rp.']],
            'horizontalCssClasses' => [
                  'label' => 'text-left control-label col-sm-4',
                  'wrapper' => 'col-md-5'
               ]
            ])->textInput([
               'placeholder' => Yii::t('fe', 'Sub Total'),
               'id' => 'total',
               'readonly' => true,
               'class' => 'form-control input-sm text-right doco-number',
            ]); 
         ?>
      </div>
      <div class="col-md-4">
         <div id="is_half_akomodasi" style="margin-bottom: -25px;">
            <?= $form->field($model, 'is_half')->checkbox([
               'id' => 'is_half',
               'class' => 'styled',
            ])->label(Yii::t('fe', 'Akomodasi 0,5 hari')); ?>
         </div>
         <div class="row">
            <div class="col-md-4" style="margin-top:25px;">        
               <?= $form->field($model, 'is_cyto')->checkbox([
                  'id' => 'is_cyto',
                  'class' => 'styled',
               ])->label(Yii::t('fe', 'Cyto')); ?>
            </div>
            <div class="col-md-4" style="margin-top:25px;">
               <?= $form->field($model, 'is_penyulit')->checkbox([
                  'id' => 'is_penyulit',
                  'class' => 'styled',
               ])->label(Yii::t('fe', 'Penyulit')); 
               ?>
            </div>
         </div>
      </div>

      <div class="modal-footer" style="padding:0px !important; margin-top:50px;">
         <?= Html::button("<b><i class='fa fa-plus'></i></b>&nbsp;Tambah", [
            'class' => 'btn btn-info btn-labeled btn-xs',
            'style' => 'margin-right:30px',
            'id' => 'tambah-tindakan'
         ]) ?>
      </div>
   </div>

   <?= Html::hiddenInput('TindakanForm[instalasi_nama]', '',['id' => 'instalasi_nama']); ?>
   <?= Html::hiddenInput('TindakanForm[ruangan_nama]', '',['id' => 'ruangan_nama']); ?>
   <?= Html::hiddenInput('TindakanForm[dokterdpjp_nama]', '',['id' => 'dokterdpjp_nama']); ?>
   <?= Html::hiddenInput('TindakanForm[tindakan_nama]', '',['id' => 'tindakan_nama']); ?>  
   <?= Html::hiddenInput('TindakanForm[harga_tariftindakan]', '',['id' => 'harga_tariftindakan']); ?>
   <?= Html::hiddenInput('TindakanForm[persencyto_tindakan]', '',['id' => 'persencyto_tindakan']); ?>
   <?= Html::hiddenInput('TindakanForm[daftartindakan_id]', '',['id' => 'daftartindakan_id']); ?>
   <?= Html::hiddenInput('TindakanForm[tipepaket_id]', '',['id' => 'tipepaket_id']); ?>
   <?= Html::hiddenInput('TindakanForm[penjamin_id]', $penjamin_id,['id' => 'penjamin_id']); ?>
   <?= Html::hiddenInput('TindakanForm[no_pendaftaran]', $no_pendaftaran,['id' => 'no_pendaftaran']); ?>
   <?= Html::hiddenInput('TindakanForm[tglpasienpulang]', $tglpasienpulang,['id' => 'tglpasienpulang']); ?>
   <?= Html::hiddenInput('TindakanForm[tglPendaftaran]', $tglPendaftaran,['id' => 'tglPendaftaran']); ?>
   <?= Html::hiddenInput('TindakanForm[status_periksa]', $status_periksa,['id' => 'status_periksa']); ?>
   <?= Html::hiddenInput('TindakanForm[persen_penyulit]', '',['id' => 'persen_penyulit']); ?>

   <!-- Tambahan -->
   <?= Html::hiddenInput('TindakanForm[jenis_pelayanan]', '',['id' => 'jenis_pelayanan_form']); ?>
   <?= Html::hiddenInput('TindakanForm[id_obat]', '',['id' => 'id_obat']); ?>
   <?= Html::hiddenInput('TindakanForm[nama_obat]', '',['id' => 'nama_obat']); ?>
   <?= Html::hiddenInput('TindakanForm[tindakan_akomodasi]', '',['id' => 'tindakan_akomodasi']); ?>
   <?= Html::hiddenInput('TindakanForm[penjamin_tindakan_id]','',['id' => 'penjamin_tindakan_id']); ?>
   <?= Html::hiddenInput('TindakanForm[harga_satuan_origin]', '',['id' => 'harga_satuan_origin']); ?>
   <?= Html::hiddenInput('TindakanForm[alasan_edit_harga]', '',['id' => 'alasan_edit_harga']); ?>
   
   <table id="table-tindakan" class="table-striped table-condensed table-hover" style="width:100%">
      <thead>
         <tr class="bg-inverse">
            <th> </th>
            <th> </th>
            <th> </th>
            <th> </th>
            <th> </th>
            <th> </th>
            <th> </th>
         </tr>
      </thead>
      <tbody>
      </tbody>
   </table>

   <hr>

   <?php
   $isShowObatForm = isset($isShowObatForm) ? $isShowObatForm : true;
   if($isShowObatForm){
      echo Yii::$app->controller->renderPartial('partial/tindakan/_table_bmhp', [
         'form' => $form,
         'model' => $model,
         'listDepo' => $listDepo,
      ]);
   }?>

   <div class="modal-footer" style="padding:0px !important;">
      <?= Html::button("<b><i class='fa fa-floppy-o'></i></b>&nbsp;Simpan", [
         'class' => 'btn btn-info btn-labeled btn-xs',
         'id' => 'simpan-tindakan-proses',
         'action'    => '/penatajasa/inf-tagihan-pasien/show-popup-simpan-tindakan?pendaftaran_id='.$pendaftaran_id,
         'data-target' => '#modal_riwayat',
         'data-toggle' => 'modal',
         'data-width' => '75%',
         'data-backdrop' => 'static',
         'data-keyboard' => 'false'
      ]) ?>

      <?= Html::button("<b><i class='fa fa-arrow-left'></i></b>&nbsp;Kembali",[
         'class' => 'btn btn-info btn-labeled btn-xs close-modal',
         'id' => 'close-tindakan'
      ]); ?>
   </div>

<?php ActiveForm::end(); ?>
</div>

<?php 
$isShowObatForm = $isShowObatForm  ? "1" : "0";
$isValidasiTglAkomodasi = isset($isValidasiTglAkomodasi) && $isValidasiTglAkomodasi == 'false' ? 0 : 1;
$this->registerJs("
   var table_bmhp;
   var _id = ".$pendaftaran_id.";
   var _penjamin_id = ".$penjamin_id.";
   var _is_pasien_ri = '".$isPasienRI."';
   var _daftartindakan_akomodasi = '".$tindakanAkomodasi['daftartindakan_id']."';
   var _daftartindakan_nama_akomodasi = '".$tindakanAkomodasi['daftartindakan_nama']."';
   var instalasi_ri = ".$instalasi_ri.";
   var isChangePrice = false;
   var modulId = ".$modul_id.";
   var _isShowObatForm = ".$isShowObatForm.";
   var isValidasiTglAkomodasi = ".$isValidasiTglAkomodasi.";
".$this->render("_form.js"), View::POS_END, "js");
?>