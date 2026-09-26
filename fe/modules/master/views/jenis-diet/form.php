<?php

/**
 * @Author: Sigit
 * @Date:   2018-11-28 10:47:57
 */

use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
?>

<?php $form = ActiveForm::begin([
    'id' => 'form', 
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => [
        'labelSpan' => 4,
        'deviceSize' => ActiveForm::SIZE_SMALL
    ],
]) ?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= Yii::t('fe', 'Tambah Jenis Diet') ?></h5>
</div>

<div class="modal-body">
    <?= $form->field($model, 'jenisdiet_kode')->textInput(['class' => 'form-control']); ?>
    <?= $form->field($model, 'jenisdiet_nama')->textInput(['class' => 'form-control']); ?>
    <?= $form->field($model, 'jenisdiet_keterangan')->textArea(['class' => 'form-control']); ?>
    <?= $form->field($model, 'is_active')->checkBox(['label' => Yii::t('fe', 'Aktif')]); ?>
</div>

<div class="modal-footer">
    <?= Html::button("<i class='fa fa-arrow-left'></i> ". Yii::t('fe', 'Kembali'),[
        'class' => 'btn bg-slate btn-sm',
        'data-dismiss' => 'modal'
    ]) ?>
    <?= Html::submitButton("<i class='fa fa-floppy-o'></i> ".Yii::t('fe', 'Simpan'), ['class' => 'btn bg-teal btn-sm']) ?>
</div>
<?php ActiveForm::end(); ?>

<script type="text/javascript">
    $("#form").docoForm("submit", {
        success : function(data) {
            $("#form")[0].reset();
            $("#modal_backdrop").modal("toggle");

            table.draw();
        }
    });
</script>