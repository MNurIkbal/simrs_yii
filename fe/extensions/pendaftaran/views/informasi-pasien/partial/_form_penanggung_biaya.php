
<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use kartik\typeahead\Typeahead;
use kartik\widgets\DepDrop;
?>

<?= Html::activeHiddenInput($modelPenanggungBiaya, 'penanggungbiaya_id', ['id' => 'penanggungbiaya_id']); ?>
<?= Html::activeHiddenInput($modelPenanggungBiaya, 'pasien_id', ['id' => 'pasien_id']); ?>

<?= $form->field($modelPenanggungBiaya, 'instansi')->textInput()->label('Instansi'); ?>
<?= $form->field($modelPenanggungBiaya, 'penanggungbiaya_nama')->textInput()->label('Nama'); ?>
<?= $form->field($modelPenanggungBiaya, 'namabagian')->textInput()->label('Nama Bagian'); ?>
<?= $form->field($modelPenanggungBiaya, 'ruangcarabayar_id', [
        'horizontalCssClasses' => [
            'label' => 'col-md-4',
            'wrapper' => 'col-md-8'
        ]
    ])->widget(DepDrop::classname(), [
        'name' => 'ruangcarabayar_id',
        'data' => $bagianList,
        'options' => [
            'class' => 'form-control select2 selectRuangCaraBayar',
            'id' => 'ruangcarabayar_id',
        ],
        'pluginOptions' => [
            'depends' => ['selectCarabayar'],
            'placeholder' => Yii::t('fe', '-- PILIH --'),
            'url' => Url::to(['end-point/list-bagian'])
        ],
    ])->label('Nama Bagian'); ?>
<?= $form->field($modelPenanggungBiaya, 'noindukkaryawan')->textInput()->label('NIP'); ?>
        	