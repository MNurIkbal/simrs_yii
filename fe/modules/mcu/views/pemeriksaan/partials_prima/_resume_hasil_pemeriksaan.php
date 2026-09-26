<?php

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;

use dosamigos\ckeditor\CKEditor;
use yii\helpers\Html;
use yii\web\View;

?>

<style type="text/css">
    .datepicker>div {
        display: block;
    }

    .kv-date-remove {
        display: none;
    }
</style>

<div class="panel panel-flat">
  <div class="panel-heading">
      <div class="row">
          <div class="col-md-7">
              <h5 class="panel-title"><?= $title ?></h5>   
          </div>    
          <div class="col-md-4">                   
              <?= Html::dropDownList('template_id', '', array(),
                  [
                      'id' => 'list-resume',
                      'class' => 'form-control select2  input-sm',
                      'prompt' => Yii::t('fe', '-- Pilih Template --'),
                  ]
              ); ?>
          </div>   
          <div class="col-md-1">  
            <button type="button" class="btn btn-sm btn-info" id="pilih-resume">Pilih</button>
          </div>   
      </div>
  </div>
    <div class="panel-toolbar clearfix">
        <div class="col-md-8">
            <?= DocoHelpers::generateToolbar([
                'save' => [
                    'attributes' => [
                        'form_id' => 'form-resume-hasil',
                        'id' => 'submit-resume-hasil',
                    ]
                ]
            ], '');
            ?>
        </div>
        <div class="col-md-4 template" align="right">
            <button  type="button" class="btn btn-sm btn-danger" id="hapus-template-resume" disabled><li class="fa fa-trash"></li> Hapus Template</button>
            <?= Html::button('<i class="fa fa-floppy-o"></i> '. Yii::t('fe', 'Simpan Template'), [
                'class' => 'btn btn-primary save-template btn-sm',
                'id' => 'save-template-resume',
                'data-toggle' => 'modal',
                'data-target' => '#modal_backdrop',
                'action'      => '/mcu/pemeriksaan/modal-template?id='.$pendaftaran_id.'&type=resume_pemeriksaan&modal=is_modal',true,
                'disabled'    => 'disabled'
            ]); ?>
        </div>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <?php
                $form = ActiveForm::begin([
                    'id' => 'form-resume-hasil',
                    'enableAjaxValidation' => false,
                    'enableClientValidation' => false,
                    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
                ]);
                ?>
                <!-- Start Section -->
                <?= $form->field($model, 'resume_hasil_pemeriksaan', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-8'
                    ],
                ])->widget(CKEditor::className(), [
                    'options' => ['rows' => 20, 'id' => "resume_editor"],
                    'preset' => 'custom',
                    'clientOptions' => [
                        'extraPlugins' => '',
                    ]
                ]) ?>
                <span id="count">0 karakter</span>
                <br>
                <span class="text-danger" id="danger-count"></span>
                <div>
                    <?= Html::activeHiddenInput($model, 'resume_hasil_pemeriksaan_hidden') ?>
                </div>
                <br>

                <!-- Tatalaksana Pemeriksaan -->
                <?= $form->field($model, 'tatalaksana_pemeriksaan', [
                    'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-8'
                    ],
                ])->widget(CKEditor::className(), [
                    'options' => ['rows' => 20, 'id' => "tatalaksana_editor"],
                    'preset' => 'custom',
                    'clientOptions' => [
                        'extraPlugins' => '',
                    ]
                ])->label("Tatalaksana Hasil Pemeriksaan") ?>
                <span id="countTatalaksana">0 karakter</span>
                <br>
                <span class="text-danger" id="danger-count-laksana"></span>
                <div>
                    <?= Html::activeHiddenInput($model, 'tatalaksana_pemeriksaan_hidden') ?>
                </div>

                <!-- End Section -->
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        CKEDITOR.config.basicEntities = false;
    });
</script>
<?php   
$this->registerJs("
    var type = 'resume';
    var detail_type = 'resume_pemeriksaan';
    var id_form = 'form-resume-hasil';
    var tab = 'tab-resume-pemeriksaan';
", View::POS_END);
$this->registerJs($this->render('../js/_resume_hasil_pemeriksaan.js'), View::POS_END);
$this->registerJs($this->render('../js/_template.js'), View::POS_END);
?>
