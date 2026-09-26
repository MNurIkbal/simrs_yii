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
        <?= $model->satuanunit_id == null 
                ? Yii::t('fe', 'Tambah Data') : Yii::t('fe', 'Ubah Data') ?>
    </h5>
</div>

<!-- Modal body -->
<div class="modal-body">
    <?= $form->field($model, 'satuanunit_nama', 
            [
                'labelOptions' => [
                    'class' => 'text-left'
                    ]
                ])->textInput([
                    'class' => 'form-control input-sm', 
            ]) ?>
    <?= $form->field($model, 'satuanunit_namalain', 
            [
                'labelOptions' => [
                    'class' => 'text-left'
                ]
            ])->textInput([
                'class' => 'form-control input-sm'
    ]) ?>
    <hr>
    <div class="modal-footer" style="padding:0px !important;">
        <?= Html::button("<b><i class='fa fa-floppy-o'></i></b>&nbsp;Simpan", [
                'class' => 'btn btn-info btn-labeled btn-xs',
                'id' => 'btn-submit'
            ]) ?>
        <?= Html::button("<b><i class='fa fa-arrow-left'></i></b>&nbsp;Kembali",[
                            'class' => 'btn btn-info btn-labeled btn-xs',
                            'data-dismiss' => 'modal'
            ]); ?>
    </div>
</div>
<?php ActiveForm::end(); ?>

<script type="text/javascript">
$('#btn-submit').on('click', function (event) {
    var _form = $('#form');
    $(this).docoForm("click", {
        url : _form.attr('action'),
        data : _form.serializeArray(),
        success : function(data) {
            btnEditHapus(data.metadata.status);
            $('.data-reset').click()
            if (data.metadata.status == 201) {
                $("#form")[0].reset();
                tableSatuan.draw();
                $('#modal_backdrop').modal('toggle');
            }
            else if (data.metadata.status == 200) {
                tableSatuan.draw();
                $('#modal_backdrop').modal('toggle');
            }
        }
    });
});
    
$("#satuanunitform-satuanunit_nama").on("change", function(){
    var nama = $(this).val();
    var nama_lainnya = $("#satuanunitform-satuanunit_namalain").val();
    if(nama_lainnya == '') {
        $("#satuanunitform-satuanunit_namalain").val(nama);
    }
    else {
        $("#satuanunitform-satuanunit_namalain").val(nama_lainnya);
    }
});
</script>