<?php

/**
 * @Author: afil
 * @Date:   2018-01-17 16:58:52
 * @Last Modified by: metafiliana
 * @Last Modified time: 2018-01-25 18:26:45
 * @Description: 
 */

use kartik\widgets\ActiveForm;
use yii\helpers\Html;
?>

<?php 
$form = ActiveForm::begin([
    'id' => 'ajax-form', 
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]); 
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><b><?=$title;?></b> - <?=$action?></h5>
</div>
<div class="modal-body">
    <?=$form->field($model, 'no_rekam_medik', ['labelOptions' => ['class' => 'text-left']])
        ->textInput([
            'placeholder' => $model->getAttributeLabel('no_rekam_medik'),
            'class' => 'form-control input-sm',
            'value' => $attributes["no_rekam_medik"],
            'readonly' => 'readonly'
            ]); ?>
    <?=$form->field($model, 'no_pendaftaran', ['labelOptions' => ['class' => 'text-left']])
        ->textInput([
            'placeholder' => $model->getAttributeLabel('no_pendaftaran'),
            'class' => 'form-control input-sm',
            'value' => $attributes["no_pendaftaran"],
            'readonly' => 'readonly'
            ]); ?>
    <?=$form->field($model, 'nama_pasien', ['labelOptions' => ['class' => 'text-left']])
        ->textInput([
            'placeholder' => $model->getAttributeLabel('nama_pasien'),
            'class' => 'form-control input-sm',
            'value' => $attributes["nama_pasien"],
            'readonly' => 'readonly'
        ]); ?>
    <?=$form->field($model, 'tgl_jadwal', ['labelOptions' => ['class' => 'text-left']])
        ->textInput([
            'placeholder' => $model->getAttributeLabel('tgl_jadwal'),
            'class' => 'form-control input-sm date'
        ]); ?>
</div>
<div class="modal-footer">
    <?= Html::submitButton("<i class='fa fa-floppy-o'></i> ". Yii::t('fe', 'Simpan'), ['class' => 'btn bg-teal btn-sm']) ?>
    <?= Html::button("<i class='fa fa-arrow-left'></i> ". Yii::t('fe', 'Kembali'),[
                        'class' => 'btn bg-slate btn-sm',
                        'data-dismiss' => 'modal'
                        ]); ?>
</div>
<?php ActiveForm::end(); ?>

<?php
$this->registerJs('
    $(".date").pickadate({
        applyClass: "bg-slate-600",
        cancelClass: "btn-default",
        locale: {
            format: "DD-MMMM-YYYY"
        }
    });

    $("#ajax-form").docoForm("submit",{
        success : function(data) {
            tabel.draw();
        }
    });
');
?>