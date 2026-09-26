<?php
    use kartik\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\web\View;
    use yii\helpers\ArrayHelper;
?>
<?php
$form = ActiveForm::begin([
    'id' => 'form-diagnosa',
    'enableAjaxValidation'=>false,
    'enableClientValidation'=>false,
    'type' => ActiveForm::TYPE_HORIZONTAL,
]);
echo $form->field($model, 'kelaspelayanan_id')->hiddenInput()->label(false);
echo Html::hiddenInput('data-diagnosa', null, ['id' => 'data-diagnosa']);
?>

<div class="modal-header">
    <h5 class="modal-title"><?= $title;?></h5>
</div>
<div class="modal-body">
    <div class="col-md-12">
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h6 class="panel-title"><b>Informasi Pasien</b></h6>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <label class="text-left control-label col-sm-3"><b>
                            <?= Yii::t("fe", "No Pendaftaran/ No RM/ Nama Pasien") ?></b>
                        </label>
                        <div class="col-sm-9" style="margin-top: 10px;">
                            <p><b>:</b>&nbsp;<?= ArrayHelper::getValue($dataMonitoring, 'no_pendaftaran').' / '.ArrayHelper::getValue($dataMonitoring, 'no_rekam_medik').' / '.ArrayHelper::getValue($dataMonitoring, 'nama_pasien') ?></p>
                        </div>
                    </div>
                    <div class="row">
                        <label class="text-left control-label col-sm-3"><b>
                            <?= Yii::t("fe", "Dokter DPJP") ?></b>
                        </label>
                        <div class="col-sm-9" style="margin-top: 10px;">
                            <p><b>:</b>&nbsp;<?= ArrayHelper::getValue($dataMonitoring, 'dokter_dpjp') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <?php echo $form->field($model, 'diag_utama_id', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-md-3',
                        'wrapper' => 'col-md-9'
                    ]
                ])->dropDownList($callbackDiagUtama, [
                'class' => 'select2 selectDiagUtama',
                'id'=>'diag_utama_id',
            ])->label(Yii::t('fe', 'Diagnosa Utama (ICD 10)')); ?>

            <div class="form-group highlight-addon">
                <label class="control-label has-star col-md-3"></label>
                <div class="col-md-9 help-block"><p><strong><i>
                    <?= $keteranganDokter ?></i></strong></p>
                </div>
            </div>

            <?php echo $form->field($model, 'diag_penyerta[]', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-md-3',
                        'wrapper' => 'col-md-9'
                    ]
                ])->dropDownList([], [
                'class' => 'select2 word-wrapper',
                'id'=>'diag_penyerta',
                'multiple' => 'multiple'
            ])->label(Yii::t('fe', 'Diagnosa Penyerta (ICD 10)')); ?>

            <?php echo $form->field($model, 'diag_tindakan[]', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-md-3',
                        'wrapper' => 'col-md-9'
                    ]
                ])->dropDownList([], [
                'class' => 'select2 word-wrapper',
                'id'=>'diag_tindakan',
                'multiple' => 'multiple'
            ])->label(Yii::t('fe', 'Tindakan (ICD 9)')); ?>

            <?php echo $form->field($model, 'hak_kelas', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-md-3',
                        'wrapper' => 'col-md-9'
                    ]
            ])->textInput(['readonly' => true]); ?>
            <?php echo $form->field($model, 'kelaspelayanan_nama', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-md-3',
                        'wrapper' => 'col-md-9'
                    ]
            ])->textInput(['readonly' => true])->label(Yii::t('fe', 'Kelas Saat Ini')); ?>

            <div class="form-group highlight-addon">
                <label class="control-label has-star col-md-3"></label>
                <div class="col-md-9"><p style="color:<?= $fontColor ?>"><?= $keteranganNaikKelas ?></p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?=Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['class' => 'btn btn bg-teal btn-sm']); ?>
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
</div>
<?php ActiveForm::end(); ?>
<?php
    $this->registerJs("
        var callbackDiagPenyerta = ".json_encode($callbackDiagPenyerta).";
        var callbackDiagTindakan = ".json_encode($callbackDiagTindakan).";
    ");
    $this->registerJs(""
        .$this->render('js/form.js')
    , View::POS_END, "js-index");
 ?>