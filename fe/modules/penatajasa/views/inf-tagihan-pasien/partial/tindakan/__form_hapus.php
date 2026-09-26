<?php 
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;

use app\components\DocoHelpers;

?>
<div class="modal-header bg-inverse">
    <h4 class="modal-title"><?= $title ?></h4>
</div>
<div class="modal-body">
    <?php
    $form = ActiveForm::begin([
            'id' => 'remove-tindakan-form',
            'enableAjaxValidation'=>false,
            'enableClientValidation'=>false,
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'formConfig' => [
                'labelSpan' => 3,
                'deviceSize' => ActiveForm::SIZE_SMALL
            ],
        ]);
    ?>
    <?=$form->field($modelRemove, 'alasan', [
         'horizontalCssClasses' => [
            'label' => 'text-left control-label col-sm-2',
            'wrapper' => 'col-md-10'
        ],
        'labelOptions' => [
            'class' => 'text-right col-sm-9']
        ])->textArea([
        'placeholder' => $modelRemove->getAttributeLabel('alasan'),
        'class' => 'form-control input-sm', 
        'id' => 'remove-alasan',
        'rows' => 5
    ])->label(Yii::t('fe', 'Alasan')); ?>
    <hr>
    <div class="modal-footer" style="padding:0px !important;">
        <?= Html::button("<b><i class='fa fa-floppy-o'></i></b>&nbsp;Simpan", [
                'class' => 'btn btn-info btn-labeled btn-xs',
                'id' => 'btn-delete-tindakan'
            ]) ?>
        <?= Html::button("<b><i class='fa fa-arrow-left'></i></b>&nbsp;Kembali",[
                'class' => 'btn btn-info btn-labeled btn-xs',
                'data-dismiss' => 'modal',
            ]); ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<script type="text/javascript">
    $('#btn-delete-tindakan').on('click', function(e){
        e.preventDefault();
        var _form = $('#remove-tindakan-form');
        $(this).docoForm('click', {
            data: _form.serializeArray(),
            url: _form.attr('action')+'&pendaftaran_id='+pendaftaran_id,
            success: function(res){
                table.draw();
                $('#modal_backdrop').modal('toggle');
            }
        })
    });
</script>