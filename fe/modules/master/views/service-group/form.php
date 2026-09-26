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
        <?= $model->servicegroup_id == null
                ? Yii::t('fe', 'Tambah Data') : Yii::t('fe', 'Ubah Data') ?>
    </h5>
</div>

<!-- Modal body -->
<div class="modal-body">
    <?= $form->field($model, 'servicegroup_nama',
            [
                'labelOptions' => [
                    'class' => 'text-left'
                ]
            ])->textInput([
                'class' => 'form-control input-sm'
    ]) ?>
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
var id = '<?= DocoHelpers::encrypt($model->servicegroup_id) ?>';
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
        url: '/master/service-group/'+event,
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