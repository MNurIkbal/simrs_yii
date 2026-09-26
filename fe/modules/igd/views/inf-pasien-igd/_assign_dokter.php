<?php
    use kartik\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\ArrayHelper;
    use yii\web\View;
?>
<?php
$this->registerCss('
    span.select2.select2-container.select2-container--default {
        width: 100% !important;
    }
    span.select2.select2-container.select2-container--default.select2-container--focus{
        width: 100% !important;
    }

');
?>
<?php
$form = ActiveForm::begin([
    'id' => 'assign-dokter-form',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    // 'enableAjaxValidation' => false,
    // 'enableClientValidation'=>false,
    'action' => isset($urlSubmit) ? $urlSubmit : '/igd/inf-pasien-igd/set-dokter',
    'formConfig' => ['labelSpan' => 3,'showErrors'=>false, 'deviceSize' => ActiveForm::SIZE_SMALL]
]);
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <div class="form-group">
        <div class="col-md-3">
            <label class="control-label text-black" style="padding-left:0px;"><?= Yii::t('fe', 'Waktu periksa'); ?></label>
        </div>
        <div class="col-md-9">
            <b><span id="tgl_masukperiksa"></span></b>
            <?= Html::hiddenInput('AturDokterForm[tgl_masukperiksa]', $model->tgl_masukperiksa); ?>
        </div>
    </div>
    <?= $form->field($model, 'dokter_id')->dropDownList(
        ArrayHelper::map($listDokterJaga, 'pegawai_id', 'nama_pegawai'),
        [
            'class' => 'form-control select2 ',
            'id' => 'dokter_id',
            'value' => $default_id
        ]
    ); 
    ?>
    <?= Html::hiddenInput('AturDokterForm[pendaftaran_id]', $pendaftaran_id);?>    
</div>
<div class="modal-footer">
    <?=Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['id'=>'btn-simpan','class' => 'btn btn bg-teal btn-sm']); ?>
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
</div>
<?php ActiveForm::end(); ?>


<?php

$this->registerJs("
    var crossModule = ".($crossModule) ? $crossModule : false."
");

$this->registerJs($this->render('js/_assign_dokter.js'), View::POS_END, 'js')
?>
