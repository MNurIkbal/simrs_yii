<?php

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\web\View;
?>

<?php $form = ActiveForm::begin([
    'id' => 'batal-operasi-form', 
    'type' => ActiveForm::TYPE_VERTICAL,
    'action' => '/bedah/jadwal-operasi/batal?id='.$id,
    'enableClientValidation' => false,
    'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL]
]) ?>

<div class="modal-header bg-inverse">
    <button type="button" class="close close-modal-batal" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <div class='row'>
        <?= $form->field($model, 'catatan')->textArea([
            'class' => 'form-control input-sm ',
            'autocomplete' => "off",
            'rows' => 6,
            'placeholder' => Yii::t('fe', 'Catatan Persetujuan / Penolakan Jadwal Operasi'),
        ]); ?>
    </div>

    <br>
    <div class='row'>
        <div class="col-lg-12">
            <div class="pull-right">
                <button type="submit" id="operasi_set_jadwal" class="btn bg-teal">
                    <i class="fa fa-floppy-o"></i> <?= Yii::t('fe', 'Simpan'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end() ?>
<script type="text/javascript">
    $('#batal-operasi-form').submit(function(event){
        event.preventDefault();
        var _value = $(this).serializeArray();
        $(this).docoForm("submit", { 
            data: _value,
            success: function (response) {
                $('.close-modal-batal').click();
                location.reload();
            }
        });
    });
</script>