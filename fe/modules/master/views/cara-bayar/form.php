<?php
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
?>

<?php 
$form = ActiveForm::begin([
    'id' => 'cara-bayar-form', 
     'type' => ActiveForm::TYPE_HORIZONTAL,
    'options' => ['enctype'=>'multipart/form-data'],
    'formConfig' => ['showErrors' => true,'labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL]
]); 
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <?=$form->field($model, 'carabayar_nama', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('carabayar_nama'),'class' => 'form-control input-sm']); ?>
    <?=$form->field($model, 'carabayar_namalainnya', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('carabayar_namalainnya'),'class' => 'form-control input-sm']); ?>
    <?=$form
        ->field($model, 'metode_pembayaran', ['labelOptions' => ['class' => 'text-left']])
        ->dropDownList($metode_bayar, [
            'class' => 'form-control input-sm select2',
            'prompt' => Yii::t('fe', '-- Pilih --'),
        ]);
    ?>

    <?= $form->field($model, 'is_active')
        ->radioList(
            [
                1=> Yii::t('fe', 'Aktif'),
                0=> Yii::t('fe', 'Tidak Aktif'),
             ], 
            ['id'=>'is_active', 'inline'=>true, 'value'=> $model->is_active ]
        ); 
    ?> 
    <?= $form->field($model, 'is_subsidiasuransi')
        ->radioList(
            [
                1=> Yii::t('fe', 'Ya'),
                0=> Yii::t('fe', 'Tidak'),
             ], 
            ['id'=>'is_subsidiasuransi', 'inline'=>true, 'value'=> $model->is_subsidiasuransi ]
        ); 
    ?> 
    <?= $form->field($model, 'is_subsidipemerintah')
        ->radioList(
            [
                1=> Yii::t('fe', 'Ya'),
                0=> Yii::t('fe', 'Tidak'),
             ], 
            ['id'=>'is_subsidipemerintah', 'inline'=>true, 'value'=> $model->is_subsidipemerintah ]
        ); 
    ?> 
    <?= $form->field($model, 'is_subsidirs')
        ->radioList(
            [
                1=> Yii::t('fe', 'Ya'),
                0=> Yii::t('fe', 'Tidak'),
             ], 
            ['id'=>'is_subsidirs', 'inline'=>true, 'value'=> $model->is_subsidirs ]
        ); 
    ?> 


    <div class="modal-footer">
        <button type="submit" id="btn-simpan" class="btn btn-info btn-labeled btn-xs data-save" data-target="ajax-form" onclick=""><b><i class="fa fa-floppy-o"></i></b>Simpan</button>
        <button type="button" class="btn btn-info btn-labeled btn-xs data-back" data-dismiss="modal"><b><i class="fa fa-arrow-left"></i></b>Kembali</button>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<script type="text/javascript">

    // $("span.bootstrap-switch-handle-off.bootstrap-switch-danger").attr('style','background-color:#fff;');
    $(document).on('keydown', null, 'alt+s', function (event) {
        $("#btn-simpan").click();
    });
    $("#cara-bayar-form").docoForm("submit",{
        success : function(data) {
            var form = $("#cara-bayar-form");
            form[0].reset();
            table.draw();
            $("#modal_backdrop").modal('toggle');
        },
    });
</script>