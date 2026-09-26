<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\select2\Select2;
use yii\web\JsExpression;
use app\components\DocoHelpers;

?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>

<div class="modal-body">
    <h5 class="panel-title text-center mb-10"><?= $subtitle ?></h5>

    <?php
    $form = ActiveForm::begin([
        'id' => 'assesment-form',
        'type' => ActiveForm::TYPE_VERTICAL,
        'enableAjaxValidation'=>false, 
        'enableClientValidation'=>false,
        'action' => '',    
        'formConfig' => [
            'labelSpan' => 3, 
            'deviceSize' => ActiveForm::SIZE_SMALL
        ],
    ]);
    ?>

    <?= $form->field($model, 'assesmentPel')
        ->dropDownList($assesmentList,
            [
                'id'=>'assesment_pel_1',
                'class'=>'select2',
                'prompt'=>'— PILIH —',
            ]
        )->label(false);
    ?>
</div>

<div class="modal-footer">
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['class' => 'btn btn btn-simpan-assesment bg-teal btn-sm btn-save']); ?>
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
</div>

<script type="text/javascript">
    $(".btn-simpan-assesment").on("click", function(event) {
        event.preventDefault();
        var assesment_pel = $("#assesment_pel_1").val();

        if (assesment_pel != 0 && assesment_pel != null || assesment_pel != "" && assesment_pel != "undefined") {
            $("#assesment_pel").val(assesment_pel);
            $("#is_tujuan_kunj").val(1);

            // Reset prosedur param
            $("#flag_procedure").val('');
            $("#kd_penunjang").val('');

            $('#modal_backdrop').modal('hide');
            $(".wizard").find("a[href='#next']").trigger("click");
        } else {
            docoNotification("warning", "Peringatan!", "Assesment pelayanan belum dipilih");
            return false;
        }
    });
</script>
