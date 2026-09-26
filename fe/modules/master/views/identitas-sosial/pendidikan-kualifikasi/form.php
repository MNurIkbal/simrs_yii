<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
// use yii\widgets\ActiveForm;
use yii\web\JsExpression;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use kartik\widgets\Depdrop;
use kartik\widgets\ActiveForm;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = [ 'label' => Yii::t('fe', 'Master Identitas Sosial'), 'url' => ['/master/identitas-sosial#view-Pekerjaan'] ];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php 
    $form = ActiveForm::begin([
        'id'=>'pendidikan-kualifikasi-form',
         'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
    ]);
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <?= $form->field($model, 'pendidikan_id', [
                            'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-4',
                                'wrapper' => 'col-md-8'
                            ]
                        ])->dropDownList($ddl_pendidikan, ['prompt' => '-- Pilih --', 'class' => 'select2']);
                        ?>
    <?= $form->field($model, 'kelompokpegawai_id', [
                            'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-4',
                                'wrapper' => 'col-md-8'
                            ]
                        ])->dropDownList($ddl_pegawai, ['prompt' => '-- Pilih --', 'class' => 'select2']);
                        ?>
    <?= $form->field($model, 'pendkualifikasi_kode')->textInput(['class' => 'form-control','placeholder' => $model->getAttributeLabel('pendkualifikasi_kode')]); ?>
    <?= $form->field($model, 'pendkualifikasi_nama')->textInput(['class' => 'form-control','placeholder' => $model->getAttributeLabel('pendkualifikasi_nama')]); ?>
    <?= $form->field($model, 'pendkualifikasi_namalainnya')->textInput(['class' => 'form-control','placeholder' => $model->getAttributeLabel('pendkualifikasi_namalainnya')]); ?>
    <?= $form->field($model, 'jmlkeblaki')->textInput(['class' => 'form-control docoNumberOnly','placeholder' => $model->getAttributeLabel('jmlkeblaki')]); ?>
    <?= $form->field($model, 'jmlkebperempuan')->textInput(['class' => 'form-control docoNumberOnly','placeholder' => $model->getAttributeLabel('jmlkebperempuan')]); ?>
    <?= $form->field($model, 'is_active')
        ->radioList(
            [
                1=> Yii::t('fe', 'Aktif'),
                0=> Yii::t('fe', 'Tidak Aktif'),
             ], 
            ['id'=>'is_active', 'inline'=>true, 'value'=> $model->is_active ]
        ); 
    ?>
    <div class="modal-footer">
        <?=Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['id'=>'btn-simpan','class' => 'btn btn bg-teal btn-sm']); ?>
        <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
    </div>
    <?php ActiveForm::end() ?>
</div>

<?php
$script = <<< JS
	$('#pendidikan-kualifikasi-form').docoForm('submit',{
		success : function(data) {
            var form = $("#pendidikan-kualifikasi-form");
            form[0].reset();
            tablePendidikanKualifikasi.draw();
            $("#modal_backdrop").modal('toggle');
        },
	});
JS;

$this->registerJs($script,View::POS_END,'jkun');
?>