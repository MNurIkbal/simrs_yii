<?php

/**
 * @author : Anggoro (tri.anggoro@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\DatePicker;
use kartik\widgets\FileInput;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Gudang', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= $title; ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <div class="col-md-12">
                </div>
            </div>
            <div class="panel-body">
                <?php
                $form = ActiveForm::begin([
                    'id' => 'migrasi-adjustment-obat-alkes',
                    'enableAjaxValidation' => false,
                    'enableClientValidation' => false,
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => [
                        'labelSpan' => 3,
                        'deviceSize' => ActiveForm::SIZE_SMALL
                    ],
                    'options' => [
                        'role' => 'form',
                        'enctype' => 'multipart/form-data',
                    ]
                ]);
                ?>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'tanggal', [
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                ],
                                'addon' => [
                                    'append' => [
                                        'content' => '<i class="fa fa-calendar" id="btnDatePick"></i>',
                                    ]
                                ]
                            ])->textInput()->label(Yii::t("fe", "Tanggal Adjustment")); ?>

                        <?= $form->field($model, 'ruangan_id', [
                            'horizontalCssClasses' => [
                                'label' => 'control-label col-sm-4',
                                'wrapper' => 'col-md-8',
                            ]
                        ])->dropDownList($ruangan, ['class' => 'select2 select-ruangan'])->label(Yii::t('fe', 'Ruangan')) ?>

                        <?= $form->field($model, 'jenis_adjustment', [
                            'horizontalCssClasses' => [
                                'label' => 'control-label col-sm-4',
                                'wrapper' => 'col-md-8',
                            ]
                        ])->dropDownList([0 => 'Adjusment Masuk', 1 => 'Adjustment Keluar'], ['class' => 'select2'])->label(Yii::t('fe', 'Jenis Adjustment')) ?>

                        <?= $form->field($model, 'attachment', [
                                'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-4',
                                'wrapper' => 'col-md-8',
                                'id' => 'file-logo-header',
                            ]])->fileInput()->label(Yii::t('fe', 'File Excel / CSV').' <i>(Max 2MB)</i>');
                        ?>
                    </div>
                    <div class="col-md-6">
                        <button id="btn-upload" type="submit" class="btn btn-success ">Upload</button>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs($this->render("js/adjusment-obat-alkes.js"), View::POS_END, 'sebuah-js');