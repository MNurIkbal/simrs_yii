<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-23 14:11:00
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-04-23 15:51:45
 */

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
    <h5 class="modal-title"><?= $model->kode_kelompok == null ? Yii::t('fe', 'Tambah Data') : Yii::t('fe', 'Ubah Data') ?></h5>
</div>

<!-- Modal body -->
<div class="modal-body">
    <?php
        $is_disabled = $model->kode_kelompok == null ? false : true;
    ?>
    <?= $form->field($model, 'kode_kelompok', ['labelOptions' => ['class' => 'text-left']])->textInput(['class' => 'form-control input-sm kode_unique', 'readonly' => $is_disabled]) ?>
    <?= $form->field($model, 'nama_kelompok', ['labelOptions' => ['class' => 'text-left']])->textInput(['class' => 'form-control input-sm remove_space']) ?>
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

<!-- Javascript -->
<script type="text/javascript">
    $('#btn-submit').on('click', function (event) {
        var _form = $('#form');
        $(this).docoForm("click", {
            url : _form.attr('action'),
            data : _form.serializeArray(),
            success : function(data) {
                $("#form")[0].reset();
                // $("#btn-edit").prop("disabled", true);
                // $("#btn-delete").prop("disabled", true);
                $('#modal_backdrop').modal('toggle');
                table.draw();
            }
        });
    });
</script>