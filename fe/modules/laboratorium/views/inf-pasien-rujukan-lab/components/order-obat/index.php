<?php

use yii\web\View;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use app\components\DocoHelpers;
use app\components\DocoConstants;

$id = DocoHelpers::encrypt($id);
$pelayananId = DocoHelpers::encrypt($pelayananId);
$tindakanId = DocoHelpers::encrypt($tindakanId);

?>

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
                     'data-render' => 'hasil-lab?id='.$id,
                     'data-tab' => 'tab-non-rujukan',
                     'data-target' => '#view-non-rujukan',
                     'id' => 'btn-kembali-obat',
                  ]
               ],
               'save' => [
                  'title' => \Yii::t('fe', 'Simpan'),
                  'icon' => 'fa fa-check-square-o',
                  'attributes' => [
                     'class' => 'btn btn-info btn-labeled btn-xs',
                     'id' => 'btn-simpan-obat',
                  ]
               ],
            ]) ?>
         </div>
         <div class="panel-body">
            <div class="row col-md-12">
               <h6 class="panel-title"><b>Tindakan dan Pemakaian Obat / Alkes Non - Reseptur</b></h6>
            </div><br><br>
            <div class="row">
               <?php $form = ActiveForm::begin([
                     'id' => 'form-order-tindakan',
                     'action'=> '/laboratorium/inf-pasien-rujukan-lab/simpan-order-obat?id='.$id.'&pelayananId='.$pelayananId,
                     'enableAjaxValidation'=>false,
                     'enableClientValidation'=>false,
                     'type' => ActiveForm::TYPE_VERTICAL,
                  ]);
               ?>
            </div>
            <div class="row">
               <div class="col-md-3">
                  <?= $form->field($model, 'pemeriksaan_id', [
                     'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-2',
                        'wrapper' => 'col-md-4'
                     ]
                  ])->dropDownList(ArrayHelper::map($list_pemeriksaan, 'tindakanpelayanan_id', 'daftartindakan_nama'),[
                     'class' => 'select2',
                     'id' => 'pemeriksaan_id',
                     'prompt' => Yii::t('fe','--Pilih Pemeriksaan--')
                     ]);
                  ?>
               </div>
               <div class="col-md-3">
                  <?= $form->field($model, 'tindakan_id', [
                     'horizontalCssClasses' => [
                     'label' => 'text-left control-label col-sm-2',
                     'wrapper' => 'col-md-4'
                     ]
                  ])->dropDownList([],[
                     'class' => 'select2',
                     'id' => 'tindakan_id',
                     'data-kelas' => isset($info_pasien['kelaspelayanan_id']) ? $info_pasien['kelaspelayanan_id'] : null,
                     'data-penjamin' => isset($info_pasien['penjamin_id']) ? $info_pasien['penjamin_id'] : null,
                     'prompt' => Yii::t('fe','--Pilih Tindakan--')
                     ]);
                  ?>
               </div>
               <div class="col-md-3">
                  <?= $form->field($model, 'qty_tindakan', [
                     'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-2',
                        'wrapper' => 'col-md-2'
                     ]
                     ])->textInput([
                     'placeholder' => $model->getAttributeLabel('Jumlah'),
                     'class' => 'form-control input-sm text-right doco-number',
                     'autocomplete' => "off",
                     'id' => 'pemakaian-barang-qty_tindakan',
                  ]); ?>
                  </div>
               <div class="col-md-3">
                  <?= $form->field($model, 'jumlah_tarif', [
                     'horizontalCssClasses' => [
                           'label' => 'text-left control-label col-sm-2',
                           'wrapper' => 'col-md-2'
                        ]
                     ])->textInput([
                        'placeholder' => $model->getAttributeLabel('jumlah_tarif'),
                        'class' => 'form-control input-sm text-right',
                        'autocomplete' => "off",
                        'id' => 'pemakaian-barang-jumlah_tarif',
                        'readonly' => true
                  ]); ?>
               </div>
            </div>
            <div class="row">
               <div class="col-md-3">
                  <?= $form->field($model, 'petugas_satu',[
                        'horizontalCssClasses' => [
                           'label' => 'text-left control-label col-sm-2',
                           'wrapper' => 'col-md-3'
                        ]
                     ])->dropDownList(ArrayHelper::map($list_pegawai, 'pegawai_id', 'nama_pegawai'),[
                        'class' => 'select2',
                        'id' => 'petugas_satu',
                        'prompt' => Yii::t('fe','--Pilih--')
                     ]);
                  ?>
               </div>
               <div class="col-md-3">
                  <?= $form->field($model, 'petugas_dua',[
                        'horizontalCssClasses' => [
                           'label' => 'text-left control-label col-sm-2',
                           'wrapper' => 'col-md-3'
                        ]
                     ])->dropDownList(ArrayHelper::map($list_pegawai, 'pegawai_id', 'nama_pegawai'),[
                        'class' => 'select2',
                        'id' => 'petugas_dua',
                        'prompt' => Yii::t('fe','--Pilih--')
                     ]);
                  ?>
               </div>
               <div class="col-md-3" style="margin-top: 20px;">
                  <?= DocoHelpers::generateToolbar([
                     'add-obat' => [
                        'title' => \Yii::t('fe', 'Tambah Obat / Alkes'),
                        'icon' => 'fa fa-plus',
                        'attributes' => [
                           'class' => 'btn btn-info btn-labeled btn-xs spa',
                           'data-options' => 'click',
                           'data-render' => 'tambah-obat?id='.$id.'&pelayananId='.$pelayananId.'&tindakanId='.$tindakanId.'&pendaftaran_id='.DocoHelpers::encrypt($pendaftaran_id),
                           'data-tab' => 'tab-non-rujukan',
                           'data-target' => '#view-non-rujukan',
                        ]
                     ],
                  ]); ?>
               </div>
            </div>
            <?php ActiveForm::end(); ?>  
            <br><br>
            <div class="row col-md-12">
               <h6 class="panel-title"><b>Tabel Tindakan dan Pemakaian Obat / Alkes Non - Reseptur</b></h6> 
               <div class="table-wrapper table-scroll-x">
               <table id="example-obat" class="table table-condensed" style="width:100%">
                  <thead>
                     <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
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
   var _statusSelesai = "'.DocoConstants::ST_SELESAI_PNNJG.'";
   var _status = "'.$status_periksa.'";
   var _id = "'.DocoHelpers::decrypt($id).'";
   var _pendaftaran_id = "'.$pendaftaran_id.'";
   var _kelaspelayanan_id = "'.$listInfo['kelaspelayanan_id'].'";
   var _penjamin_id = "'.$listInfo['penjamin_id'].'";
   var _pelayananId = "'.DocoHelpers::decrypt($pelayananId).'";
   var _endPoint = "'.$endPoint.'";
', View::POS_END, 'b-index');
$this->registerJs($this->render('js/tindakan.js'), VIEW::POS_END);
?>
