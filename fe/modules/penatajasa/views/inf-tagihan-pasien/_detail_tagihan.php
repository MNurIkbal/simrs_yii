<?php 
use kartik\widgets\ActiveForm;
?>

<div id="detail-penjamin-bebas-tarif">
   <?php
      $form = ActiveForm::begin([
         'id' => 'ajax-form',
         'action' => '/kasir/pembayaran-tagihan/simpan?id='.$pendaftaran_id,
         'enableAjaxValidation'=>false,
         'enableClientValidation'=>false,
         'type' => ActiveForm::TYPE_HORIZONTAL,
         'formConfig' => [
         'labelSpan' => 3,
         'deviceSize' => ActiveForm::SIZE_SMALL
         ],
         'options' => [
         'skip-confirm' => "true"
         ]
      ]);
   ?>
   <div class="col-md-6">
      <div class="detail-total_tagihan" id="detail-total_tagihan">
         <div class="col-md-4 text-right">
         <div><h5>Total Tagihan</h5></div>
         </div>
         <div class="col-md-8 mr-1">
         <div id="t_tagihan">
            <?= $form->field($model, 'total_tagihan',[
            'addon' => ['prepend' => ['content'=>'Rp.']],
            'template' => '{input}',
            'options' => [
                  'tag' => false
            ]
            ])->textInput([
            'placeholder' => @$model->getAttributeLabel('total_tagihan'),
            'class' => 'form-control input-sm text-right doco-number',
            'autocomplete' => "off",
            'readonly' => true,
            'id'=> 'total_tagihan'
            ])->label(false); ?>
         </div>
         </div>
         <div class="col-md-4 text-right">
         <div><h5>Total Dijamin</h5></div>
         </div>
         <div class="col-md-8 mr-1">
         <div>
            <?= $form->field($model, 'subsidi_asuransi',[
            'addon' => ['prepend' => ['content'=>'Rp.']],
            'template' => '{input}',
            'options' => [
                  'tag' => false
            ]
            ])->textInput([
            'placeholder' => @$model->getAttributeLabel('subsidi_asuransi'),
            'class' => 'form-control input-sm text-right doco-number',
            'autocomplete' => "off",
            'readonly' => true,
            'id'=> 'subsidi_asuransi'
            ])->label(false); ?>
         </div>
         </div>
         <div class="col-md-4 text-right">
         <div><h5>Total Tagihan Pasien</h5></div>
         </div>
         <div class="col-md-8 mr-1">
         <div>
            <?= $form->field($model, 'tagihan_pasien',[
            'addon' => ['prepend' => ['content'=>'Rp.']],
            'template' => '{input}',
            'options' => [
                  'tag' => false
            ]
            ])->textInput([
            'placeholder' => @$model->getAttributeLabel('tagihan_pasien'),
            'class' => 'form-control input-sm text-right doco-number',
            'autocomplete' => "off",
            'readonly' => true,
            'id'=> 'tagihan_pasien'
            ])->label(false); ?>
         </div>
         </div>
      </div>
   </div>
   <div class="col-md-6">
   <?=$form->field($model, 'keterangan', [
      'horizontalCssClasses' => [
      'label' => 'text-left control-label col-sm-0',
      'wrapper' => 'col-md-12'
      ],
      'labelOptions' => [
      'class' => 'text-right col-sm-9']
      ])->textArea([
      'placeholder' => $model->getAttributeLabel('keterangan'),
      'class' => 'form-control input-sm', 
      'rows' => 5,
      'id' => 'keterangan',
   ])->label(false); ?>
   </div>
   <?php ActiveForm::end(); ?>      
</div>
            