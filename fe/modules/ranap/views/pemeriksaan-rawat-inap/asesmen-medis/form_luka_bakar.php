<?php

use app\components\DocoHelpers;
use yii\web\View;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;

$classForm = 'form-control input-sm';
$classFormNumber = 'form-control doco-number';
$styleTable = 'text-align:center;font-weight:bold;';
$classCenter = 'text-center';
$styleCells = 'width:800px;margin-top:10px';

?>

<div class="panel-toolbar clearfix">
    <?= DocoHelpers::generateToolbar([
        'save' => [
            'attributes' => [
                'form_id' => 'form-asmed-ranap',
                'id' => 'submit-asmed-ranap',
            ]
        ],
    ]); ?>
</div>

<?php
$form = ActiveForm::begin([
    'enableClientValidation' => false,
    'enableAjaxValidation' => false,
    'id' => 'form-asmed-ranap',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL],
]);
?>

<h1 style="text-align:center;"><?= $title ?></h1>
<hr style="margin-top:15px;">
<h3 style="margin-left:10px;">Asesmen Khusus Luka Bakar</h3>
<hr style="margin-top:15px;">
<div class="row" style="margin-right:5px;">
    <div class="col-md-12 col-header">
        <div class="panel panel-default">
            <div class="panel-body">
                <br>
                
                <div class="row" style="margin-bottom:10px;">
                    <div class="row" style="margin-bottom:10px;margin-left:15px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'tipe_luka')->label(Yii::t('fe', 'Type Luka : Luka Bakar'))
                            ->checkboxList([
                                1 => 'Listrik',
                                2 => 'Kimia',
                                3 => 'Api',
                                4 => 'Air Panas'
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'riwayat_luka')->label(Yii::t('fe', 'Riwayat Terjadinya Luka'))
                            ->textArea(['class' => 'form-control']); ?>
                        </div>
                    </div>
                    <div class="row" style="margin-bottom:10px;margin-left:15px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'faktor_penghambat')->label(Yii::t('fe', 'Faktor Penghambat Penyembuhan Luka'))
                            ->checkboxList([
                                1 => 'Immobilisasi',
                                2 => 'Nutrisi',
                                3 => 'Diabetes',
                                4 => 'Anemia',
                                5 => 'Kemoterapi',
                                6 => 'Incontinensia',
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-top:20px;margin-bottom:20px;">Lokasi Luka </p>
                    </div>
                    <br>
                    <div class="row" style="text-align:center;">
                        <div class="image-frame">
                            <?php
                                echo Html::img( '@web/media/img/img-pemeriksaan/luka-bakar.png', [
                                    'width'=> 700,
                                    'height'=> 520,
                                ]);
                            ?>
                        </div>
                    </div>
                </div>
                <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/luka-bakar/lokasi-luka',[
                    'form' => $form,
                    'model' => $model,
                ]); ?>
                <div class="row" style="margin-bottom:10px;">
                    <div class="row" style="margin-bottom:10px;margin-left:15px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'persentase_luka', ['addon' => ['append' => ['content' => '%']]])
                            ->label(Yii::t('fe', 'Luka Bakar'))
                            ->textInput(['class' => $classFormNumber]); ?>
                        </div>
                    </div>
                </div>
                <?= Yii::$app->controller->renderPartial('asesmen-medis/partials/luka-bakar/catatan-luka',[
                    'form' => $form,
                    'model' => $model,
                    'pegawaiId' => $pegawaiId,
                    'pegawaiNama' => $pegawaiNama,
                ]); ?>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?php
$this->registerJs("
", View::POS_END);
$this->registerJs($this->render('js/asmed-ranap.js'), View::POS_END);
?>
