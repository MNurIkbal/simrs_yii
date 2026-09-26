<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
?>

<!-- Start avtive form -->
<?php $form = ActiveForm::begin([
    'id' => 'form',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]) ?>

<!-- Modal header -->
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">
        <?= $model->jenisobatalkes_id == null
                ? Yii::t('fe', 'Tambah Data') : Yii::t('fe', 'Ubah Data') ?>
    </h5>
</div>

<!-- Modal body -->
<div class="modal-body">
    <?= $form->field($model, 'jenisobatalkes_kode',
            [
                'labelOptions' => [
                    'class' => 'text-left'
                ]
            ])->textInput([
                'class' => 'form-control input-sm'
    ]) ?>
    <?= $form->field($model, 'jenisobatalkes_nama',
            [
                'labelOptions' => [
                    'class' => 'text-left'
                    ]
                ])->textInput([
                    'class' => 'form-control input-sm',
            ]) ?>
    <?= $form->field($model, 'jenisobatalkes_namalain',
            [
                'labelOptions' => [
                    'class' => 'text-left'
                ]
            ])->textInput([
                'class' => 'form-control input-sm'
    ]) ?>
    <?= $form->field($model, 'group_jenisobat')->dropDownList($group_jenisobat,[
        'class' => 'form-control select2',
        'prompt' => Yii::t('fe', '— Pilih  —'),
        'id' => 'group_jenisobat',
    ])->label(Yii::t('fe', 'Group')); ?>

    <?= $form->field($model, 'service_group')->dropDownList($service_group,[
        'class' => 'form-control select2',
        'prompt' => Yii::t('fe', '— Pilih  —'),
        'id' => 'service_group',
    ])->label(Yii::t('fe', 'Service Group')); ?>

    <?= $form->field($model, 'service_category')->dropDownList($service_category,[
        'class' => 'form-control select2',
        'prompt' => Yii::t('fe', '— Pilih  —'),
        'id' => 'service_category',
    ])->label(Yii::t('fe', 'Service Category')); ?>
</div>

<!-- Modal footer -->
<div class="modal-footer">
    <?= Html::button("<b><i class='fa fa-floppy-o'></i></b>&nbsp;Simpan", [
        'class' => 'btn btn-info btn-labeled btn-xs',
        'id' => 'btn-submit'
    ]) ?>
    <?= Html::button("<b><i class='fa fa-arrow-left'></i></b>&nbsp;Kembali",[
        'class' => 'btn btn-info btn-labeled btn-xs',
        'data-dismiss' => 'modal'
    ]); ?>
</div>
<?php ActiveForm::end(); ?>

<script type="text/javascript">
/*var data = $("#form").serializeArray();
$("#form").docoForm("submit", {
    data: data,
    url : '/master/jenis-obat-alkes/create',
    success : function(data) {
        $('.data-reset').click()
        if (data.metadata.status == 201) {
            $("#form")[0].reset();
            table.draw();
            $('#modal_backdrop').modal('toggle');
        }
        else if (data.metadata.status == 200) {
            // Draw tableSatuan
            table.draw();
            $('#modal_backdrop').modal('toggle');
        }
    }
});*/

$("#service_group").on("change", function() {
    $("#service_category").val(null).trigger("change");
    $("#service_category").select2("destroy");
    $("#service_category").docoPaginationSelec2(
        config = {
            _api : "/master/master-api/get-service-category?servicegroup_id=" + $(this).val(),   // get data
        }
    )
});


$("#btn-submit").on("click", function(event) {
    event.preventDefault();
    var data = $("#form").serializeArray();
    $(this).docoForm('click',{
        url: '/master/jenis-obat-alkes/create-jenis-obat',
        data: data,
        success : function(data) {
            $('.data-reset').click()
            $("#form")[0].reset();
            table.draw();
            $('#modal_backdrop').modal('toggle');
        }
    });
});

$("#jenisobatalkesform-jenisobatalkes_nama").on("change", function(){
    var nama = $(this).val();
    var nama_lainnya = $("#jenisobatalkesform-jenisobatalkes_namalain").val();
    if(nama_lainnya == '') {
        $("#jenisobatalkesform-jenisobatalkes_namalain").val(nama);
    }
    else {
        $("#jenisobatalkesform-jenisobatalkes_namalain").val(nama_lainnya);
    }
});
</script>