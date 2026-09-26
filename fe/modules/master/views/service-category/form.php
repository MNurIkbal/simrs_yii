<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use app\components\DocoHelpers;
?>

<style type="text/css">
    .form-horizontal .checkbox {
        padding-top: 0!important;
    }
</style>

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
        <?= $model->servicecategory_id == null
                ? Yii::t('fe', 'Tambah Data') : Yii::t('fe', 'Ubah Data') ?>
    </h5>
</div>

<!-- Modal body -->
<div class="modal-body">
    <?= $form->field($model, 'servicecategory_nama',
        [
            'labelOptions' => [
                'class' => 'text-left'
            ]
        ])->textInput([
            'class' => 'form-control input-sm'
        ])
    ?>
    <div class="form-group highlight-addon">
        <label class="text-left col-sm-4" >Is Obat</label>
        <?php
            $model->is_obat = $model->is_obat ? '1' : '0';
            echo $form->field($model, 'is_obat', [])->checkbox();
        ?>
    </div>
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
var id = '<?= DocoHelpers::encrypt($model->servicecategory_id) ?>';
$("#btn-submit").on("click", function(event) {
    event.preventDefault();
    var data = $("#form").serializeArray();
    var event;
    if(id != '') {
        event = 'update?id='+id;
    } else {
        event = 'create';
    }
    $(this).docoForm('click',{
        url: '/master/service-category/'+event,
        data: data,
        success : function(data) {
            $('.data-reset').click()
            $("#form")[0].reset();
            table.draw();
            $('#modal_backdrop').modal('toggle');
        }
    });
});
</script>