<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-09 13:39:38
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-09-12 16:52:46
 */

use app\components\DocoConstants;
use yii\web\View;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\DateTimePicker;
use kartik\widgets\Select2;
use yii\web\JsExpression;
?>
<style>
.space {
  /* width: 4px; */
  height: auto;
  display: inline-block;
  margin-left: 10px;
}
</style>
<?php
$form = ActiveForm::begin([
   'id' => 'form-laporan-operasi',
   'type' => ActiveForm::TYPE_VERTICAL,
   'action' => '\bedah\informasi-pasien-operasi\simpan-laporan?id=' . $id
]);
?>
<div class="row form-group">
   <div class="col-md-12 dokter_bedah">
   </div>
</div><br>
<div class="row">
   <div class="col-md-4">
   <?=Html::activeHiddenInput($model, 'laporanoperasi_id',['id'=>'laporanoperasi_id'])?>

      <?= $form->field($model, 'jam_masuk_rec')->widget(DateTimePicker::classname(), [
         'options' => ['placeholder' => '', 'readonly' => true],
         'pluginOptions' => [
            'language' => 'en',
            'autoclose' => true,
            'format' => 'yyyy-mm-dd hh:ii',
            'endDate' => date('Y-m-d H:i'),
            'todayHighlight' => true,
         ]
      ]); ?>
   </div>
   <div class="col-md-4">
      <?= $form->field($model, 'jam_keluar_rec')->widget(DateTimePicker::classname(), [
         'options' => ['placeholder' => '', 'readonly' => true],
         'pluginOptions' => [
            'language' => 'en',
            'autoclose' => true,
            'format' => 'yyyy-mm-dd hh:ii',
            'endDate' => date('Y-m-d H:i'),
            'todayHighlight' => true,
         ]
      ]); ?>
   </div>
   <div class="col-md-4">
      <?= $form->field($model, 'lama_pembedahan')->staticInput(['style' => 'font-weight:bold;', 'id' => 'lama-pembedahan']); ?>
   </div>
</div>
<div class="row">
   <div class="col-md-4">
      <?= $form->field($model, 'dokter_bedah')->dropDownList(
         [],
         [
            'class' => 'form-control selectDokterBedah',
            'prompt' => '--Pilih Dokter Bedah --',
         ]
      ); ?>
   </div>
   <div class="col-md-4">
      <?= $form->field($model, 'asisten')->widget(Select2::classname(), [
         'showToggleAll' => false,
         'data' => [],
         'options' => [
            'multiple' => true,
            'placeholder' => '-- Pilih --',
            'class' => 'form-control input-sm select2 selectAsisten'
         ],
         'pluginOptions' => [
            'tags' => true,
            'tokenSeparators' => [',', '_'],
            'maximumInputLength' => 50,
            'language' => [
               'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
            ],
            'ajax' => [
               'url' => \yii\helpers\Url::to(['/bedah/informasi-pasien-operasi/get-data-multiple']),
               'dataType' => 'json',
               'data' => new JsExpression('
                  function(params) {
                     return {
                        payload: {
                           type: "asisten",
                           q: params.term,
                           page:params.page || 1,
                           limit:params.limit || 10,
                        }
                     };
                  }
               '),
               'processResults' => new JsExpression('
                  function (data, params) {
                     params.page = params.page || 1;
                     return {
                     results: data.result,
                        pagination: {
                              more: data.pagination.more
                        }
                     }
                  }
               ')
            ],
            'escapeMarkup' => new JsExpression('function(markup){ return markup;}'),
            'templateResult' => new JsExpression('function(res){ return res.text;}'),
            'templateSelection' => new JsExpression('function (subject) { return subject.text; }'),
         ],
      ]);
      ?>
   </div>
   <div class="col-md-4">
      <?= $form->field($model, 'asisten_instrumen')->widget(Select2::classname(), [
         'showToggleAll' => false,
         'data' => [],
         'options' => [
            'multiple' => true,
            'placeholder' => '-- Pilih --',
            'class' => 'form-control input-sm select2 selectAsistenInstrumen'
         ],
         'pluginOptions' => [
            'tags' => true,
            'tokenSeparators' => [',', '_'],
            'maximumInputLength' => 50,
            'language' => [
               'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
            ],
            'ajax' => [
               'url' => \yii\helpers\Url::to(['/bedah/informasi-pasien-operasi/get-data-multiple']),
               'dataType' => 'json',
               'data' => new JsExpression('
                  function(params) {
                     return {
                        payload: {
                           type: "asisten",
                           q: params.term,
                           page:params.page || 1,
                           limit:params.limit || 10,
                        }
                     };
                  }
               '),
               'processResults' => new JsExpression('
                  function (data, params) {
                     params.page = params.page || 1;
                     return {
                     results: data.result,
                        pagination: {
                              more: data.pagination.more
                        }
                     }
                  }
               ')
            ],
            'escapeMarkup' => new JsExpression('function(markup){ return markup;}'),
            'templateResult' => new JsExpression('function(res){ return res.text;}'),
            'templateSelection' => new JsExpression('function (subject) { return subject.text; }'),
         ],
      ]);
      ?>
   </div>
</div>
<div class="row">
   <div class="col-md-4">
      <?= $form->field($model, 'kategori_operasi')->dropDownList(
         [],
         [
            'class' => 'form-control selectKategori',
            'prompt' => '--Pilih Kategori Operasi--',
         ]
      ); ?>
   </div>
   <div class="col-md-4">
      <?= $form->field($model, 'diagnosis_prabedah')->textInput(); ?>
   </div>
   <div class="col-md-4">
      <?= $form->field($model, 'prosedur_bedah')->widget(Select2::classname(), [
         'showToggleAll' => false,
         'data' => [],
         'options' => [
            'multiple' => true,
            'placeholder' => '-- Pilih --',
            'class' => 'form-control input-sm select2 selectProsedurBedah'
         ],
         'pluginOptions' => [
            'tags' => true,
            'tokenSeparators' => [',', '_'],
            'maximumInputLength' => 450,
            'language' => [
               'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
            ],
            'ajax' => [
               'url' => \yii\helpers\Url::to(['/bedah/informasi-pasien-operasi/get-data-multiple']),
               'dataType' => 'json',
               'data' => new JsExpression('
                  function(params) {
                     return {
                        payload: {
                           type: "prosedur",
                           q: params.term,
                           page:params.page || 1,
                           limit:params.limit || 10,
                        }
                     };
                  }
               '),
               'processResults' => new JsExpression('
                  function (data, params) {
                     params.page = params.page || 1;
                     return {
                     results: data.result,
                        pagination: {
                              more: data.pagination.more
                        }
                     }
                  }
               ')
            ],
            'escapeMarkup' => new JsExpression('function(markup){ return markup;}'),
            'templateResult' => new JsExpression('function(diagnosa){ return diagnosa.text;}'),
            'templateSelection' => new JsExpression('function (subject) { return subject.text; }'),
         ],
      ]);
      ?>
   </div>
</div>
<div class="row">
   <div class="col-md-4">
      <?= $form->field($model, 'diagnosis_paska_bedah')->textInput(); ?>
   </div>
   <div class="col-md-4">
      <?= $form->field($model, 'dokter_anestesi')->widget(Select2::classname(), [
         'showToggleAll' => false,
         'data' => [],
         'options' => [
            'multiple' => true,
            'placeholder' => '-- Pilih --',
            'class' => 'form-control input-sm select2 selectDokterAnestesi'
         ],
         'pluginOptions' => [
            'tags' => true,
            'tokenSeparators' => [',', '_'],
            'maximumInputLength' => 50,
            'language' => [
               'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
            ],
            'ajax' => [
               'url' => \yii\helpers\Url::to(['/bedah/informasi-pasien-operasi/get-data-multiple']),
               'dataType' => 'json',
               'data' => new JsExpression('
                  function(params) {
                     return {
                        payload: {
                           type: "dokter_anastesi",
                           q: params.term,
                           page:params.page || 1,
                           limit:params.limit || 10,
                        }
                     };
                  }
               '),
               'processResults' => new JsExpression('
                  function (data, params) {
                     params.page = params.page || 1;
                     return {
                     results: data.result,
                        pagination: {
                              more: data.pagination.more
                        }
                     }
                  }
               ')
            ],
            'escapeMarkup' => new JsExpression('function(markup){ return markup;}'),
            'templateResult' => new JsExpression('function(res){ return res.text;}'),
            'templateSelection' => new JsExpression('function (subject) { return subject.text; }'),
         ],
      ]);
      ?>
   </div>
   <div class="col-md-4">
      <?= $form->field($model, 'cara_pembiusan')->dropDownList(
         [],
         [
            'prompt' => '--Pilih Cara Pembiusan--',
            'class' => 'form-control selectCaraPembiusan'
         ]
      ); ?>
   </div>
</div>
<div class="row">
   <div class="col-md-4">
      <?= $form->field($model, 'posisi_pasien')->textInput(); ?>
   </div>
   <div class="col-md-4">
      <?= $form->field($model, 'mulai_pembiusan')->widget(DateTimePicker::classname(), [
         'options' => ['placeholder' => '', 'readonly' => true],
         'pluginOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd hh:ii',
            'endDate' => date('Y-m-d H:i'),
            'todayHighlight' => true,
         ]
      ]); ?>
   </div>
   <div class="col-md-4">
      <?= $form->field($model, 'selesai_pembiusan')->widget(DateTimePicker::classname(), [
         'options' => ['placeholder' => '', 'readonly' => true],
         'pluginOptions' => [
            'language' => 'en',
            'autoclose' => true,
            'format' => 'yyyy-mm-dd hh:ii',
            'endDate' => date('Y-m-d H:i'),
            'todayHighlight' => true,
         ]
      ]); ?>
   </div>
</div>
<div class="row">
   <div class="col-md-4">
      <?= $form->field($model, 'komplikasi')->textInput(); ?>
   </div>
   <div class="col-md-4">
      <?= $form->field($model, 'perdarahan')->textInput(); ?>
   </div>
   <div class="col-md-4">
      <?= $form->field($model, 'is_jaringan_dikirim')->radioList([1 => 'Ya', 0 => 'Tidak'], [
      'item' => function($index, $label, $name, $checked, $value) {
         $checked = $checked ? 'checked' : '';
         return "<label>
            <input type='radio' {$checked}
               name='{$name}'
               value='{$value}'
               id='is_jaringan_dikirim_{$value}'
            >
            {$label}</label>";
         }
      ])?>
   </div>
</div>
<div class="row" style="position: relative;">
  <div class="col-md-4">
     <?= $form->field($model, 'ukuran_implant')->textInput(); ?>
  </div>
  <div class="col-md-4">
     <?= $form->field($model, 'jumlah_darah_masuk')->textInput(); ?>
  </div>
  <div class="col-md-4" style="position:absolute; top:0; right:0;">
     <?= $form->field($model, 'intruksi_post_operasi')->textArea(['rows' => 4]); ?>
  </div>
</div>
<div class="row">
   <div class="col-md-4">
      <?= $form->field($model, 'uraian_pembedahan')->textArea(['rows' => 3]); ?>
   </div>
   <div class="col-md-4">
      <?= $form->field($model, 'asal_jaringan')->textArea(['rows' => 3]); ?>
   </div>
</div>
<?php
ActiveForm::end();
?>
<?php
$this->registerJs('
var penunjangId = ' . $id . ';
var ruanganId = ' . $ruanganId . ';
var arrDokterOp = ' . json_encode($dokter_operator) . ';
var asisten_anastesi1 = "'.DocoConstants::TIM_ASS_ANASTESI1.'"
var asisten_anastesi2 = "'.DocoConstants::TIM_ASS_ANASTESI2.'"
var asisten_bedah1 = "'.DocoConstants::TIM_ASS_BEDAH1.'"
var asisten_bedah2 = "'.DocoConstants::TIM_ASS_BEDAH2.'"
var dokter_anastesi = "'.DocoConstants::TIM_DOKTER_ANASTESI.'"
var jam_masuk = "'.$model->jam_masuk_rec.'"
var jam_keluar = "'.$model->jam_keluar_rec.'"
', View::POS_END, 'b-index');
$this->registerJs($this->render('../js/laporan.js'), View::POS_END)
?>
