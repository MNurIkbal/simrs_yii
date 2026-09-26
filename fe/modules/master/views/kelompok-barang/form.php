<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use yii\web\View;
?>

<!-- Start avtive form -->
<?php $form = ActiveForm::begin([
    'id' => 'form',
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]) ?>

<!-- Modal header -->
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">
        <?= $title ?>
    </h5>
</div>

<!-- Modal body -->
<div class="modal-body">
    <?= $form->field($model, 'kelompokbarang_nama',
    [
        'labelOptions' => [
            'class' => 'text-left'
            ]
        ])->textInput([
            'class' => 'form-control input-sm',
    ]) ?>
    <?= $form->field($model, 'kelompokbarang_namalain',
            [
                'labelOptions' => [
                    'class' => 'text-left'
                ]
            ])->textInput([
                'class' => 'form-control input-sm'
    ]) ?>
    <?= $form->field($model, 'kelompokbarang_kode',
    [
        'labelOptions' => [
            'class' => 'text-left'
            ]
        ])->textInput([
            'class' => 'form-control input-sm',
    ]) ?>

    <?= $form->field($model, 'servicegroup_id')->dropDownList($service_group,
    [
        'class' => 'form-control select2',
        'prompt' => Yii::t('fe', '— Pilih  —'),
        'id' => 'servicegroup_id',
    ])->label(Yii::t('fe', 'Service Group')); ?>

    <?= $form->field($model, 'servicecategory_id')->dropDownList($service_category,
    [
        'class' => 'form-control select2',
        'prompt' => Yii::t('fe', '— Pilih  —'),
        'id' => 'servicecategory_id',
    ])->label(Yii::t('fe', 'Service Category')); ?>

    <?= $form->field($model, 'is_active', [
            'horizontalCssClasses' => [
                'label' => 'text-left control-label col-sm-4',
                'wrapper' => 'col-md-5'
            ]
        ])->checkbox(['label' => 'Aktif'])->label(Yii::t('fe', 'Status'))
    ?>
</div>

<!-- Modal footer -->
<div class="modal-footer">
    <?= Html::submitButton("<i class='fa fa-floppy-o'></i> ". Yii::t('fe', 'Simpan'), ['class' => 'btn bg-teal btn-sm']) ?>
    <?= Html::button("<i class='fa fa-arrow-left'></i> ". Yii::t('fe', 'Kembali'),[
        'class' => 'btn bg-slate btn-sm',
        'data-dismiss' => 'modal'
    ]) ?>
</div>
<?php ActiveForm::end(); ?>

<?php $this->registerJs('
$("#form").docoForm("submit", {
    success : function(data) {
        $(".data-reset").trigger("click");
        this.formInput[0].reset();
        $("#modal_backdrop").modal("toggle");
        table.draw();
    }
});

$(document).ready(function() {
    $("#servicegroup_id").docoPaginationSelec2(
        config = {
            _api : "/master/master-api/get-service-group"
        }
    )
});

$("#servicegroup_id").on("change", function() {
    $("#servicecategory_id").val(null).trigger("change");
    $("#servicecategory_id").select2("destroy");
    $("#servicecategory_id").docoPaginationSelec2(
        config = {
            _api : "/master/master-api/get-service-category?servicegroup_id=" + $(this).val(),   // get data
        }
    )
});

$("#kelompokbarangform-kelompokbarang_nama").on("change", function(){
    var nama = $(this).val();
    var nama_lainnya = $("#kelompokbarangform-kelompokbarang_namalain").val();
    if(nama_lainnya == "") {
        $("#kelompokbarangform-kelompokbarang_namalain").val(nama);
    }
    else {
        $("#kelompokbarangform-kelompokbarang_namalain").val(nama_lainnya);
    }
});

',  View::POS_END);
