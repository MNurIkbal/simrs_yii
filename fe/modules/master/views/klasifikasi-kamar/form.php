<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
?>

<?php 
$form = ActiveForm::begin([
    'id' => 'klasifikasi-kamar-form',
    'type' => ActiveForm::TYPE_VERTICAL,
    'options' => ['enctype'=>'multipart/form-data'],
    'formConfig' => ['showErrors' => true,'labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL]
]); 

?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <?=$form->field($model, 'klasifikasikamar_nama', ['labelOptions' => ['class' => 'text-left']])->textInput(['placeholder' => $model->getAttributeLabel('klasifikasikamar_nama'),'class' => 'form-control input-sm']); ?>
    
    <?=$form
        ->field($model, 'sirsonline_id', ['labelOptions' => ['class' => 'text-left']])
        ->dropDownList( $sirs, [
            'class' => 'form-control input-sm select2',
            'prompt' => \Yii::t('fe', 'Pilih'),
        ]);
    ?>

    <?=$form
        ->field($model, 'eiscovid_id', ['labelOptions' => ['class' => 'text-left']])
        ->dropDownList( $eis, [
            'class' => 'form-control input-sm select2',
            'prompt' => \Yii::t('fe', 'Pilih'),
        ]);
    ?>

    <?=$form
        ->field($model, 'kodekelas_aplicare', ['labelOptions' => ['class' => 'text-left']])
        ->dropDownList( $listReferensiAplicare, [
            'class' => 'form-control input-sm select2',
            'prompt' => \Yii::t('fe', 'Pilih'),
            'disabled' => !empty($model->kodekelas_aplicare)
        ]);
    ?>

    <?=$form
        ->field($model, 'spgdt_id', ['labelOptions' => ['class' => 'text-left']])
        ->dropDownList( $spdgt, [
            'class' => 'form-control input-sm select2',
            'prompt' => \Yii::t('fe', 'Pilih'),
        ]);
    ?>

    <script type="text/javascript">
        $(".form-group").find(".col-sm-offset-3").removeClass('col-sm-offset-3');
        $(".switch").bootstrapSwitch();
        $(document).on("switchChange.bootstrapSwitch", ".switch", function (e, state) {
            if (e.target.checked == true) {
                $value = '1';
                $("span.bootstrap-switch-handle-off.bootstrap-switch-danger").attr('style','display:none !important;');
                $('input.prop_state').val($value);
            } else {
                $value = '0';
                $('input.prop_state').val($value);
            }
        });
    </script>
</div>
<div class="modal-footer">
<?=Html::button(\Yii::t('fe', '<i class="fa"></i> Batal'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
    <?php if(!empty($model->klasifikasikamar_id)){ ?>
        <?=Html::submitButton(\Yii::t('fe', '<i class="fa"></i> Ubah'), ['class' => 'btn btn bg-teal btn-sm btn-edit']); ?>
    <?php }else{
    ?>
    <?=Html::submitButton(\Yii::t('fe', '<i class="fa"></i> Simpan'), ['class' => 'btn btn bg-teal btn-sm btn-save']); ?>
    <?php } ?>
</div>
<?php ActiveForm::end(); ?>
<?php
    $this->registerJs($this->render('js/_index.js'));
?>
