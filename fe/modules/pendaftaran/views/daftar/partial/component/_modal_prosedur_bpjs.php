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
        'id' => 'prosedur-form',
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

    <?= $form->field($model, 'kdPenunjang')
        ->dropDownList($prosedurList,
            [
                'id'=>'kd_penunjang_1',
                'class'=>'select2',
                'prompt'=>'— PILIH —',
            ]
        )->label(false);
    ?>
</div>

<div class="modal-footer">
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['class' => 'btn btn btn-simpan-prosedur bg-teal btn-sm btn-save']); ?>
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn btn-kembali-prosedur bg-slate btn-sm']); ?>
</div>

<script type="text/javascript">
    $(".btn-simpan-prosedur").on("click", function(event) {
        event.preventDefault();
        var kd_penunjang = $("#kd_penunjang_1").val();

        if (kd_penunjang != 0 && kd_penunjang != null || kd_penunjang != "" && kd_penunjang != "undefined") {
            $("#kd_penunjang").val(kd_penunjang);
            $("#is_tujuan_kunj").val(1);

            // Reset assesment pelayanan param
            $("#assesment_pel").val('');

            $('#modal_backdrop').modal('hide');
            $(".wizard").find("a[href='#next']").trigger("click");
        } else {
            docoNotification("warning", "Peringatan!", "Prosedur kunjungan belum dipilih");
            return false;
        }
    });

    $(".btn-kembali-prosedur").on("click", function(event) {
        event.preventDefault();            
        modalTujuanKunjunganBpjs("/pendaftaran/daftar-rajal/tujuan-prosedur-bpjs");
    });
</script>
