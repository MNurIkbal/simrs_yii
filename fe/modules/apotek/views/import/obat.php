<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\FileInput;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Apotek', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<style type="text/css" media="screen">
.custom-file-upload {
    border: 1px solid #ccc;
    display: inline-block;
    padding: 6px 12px;
    cursor: pointer;
}

::-webkit-file-upload-button {
    color: #fff;
    background-color: #5cb85c;
    border: 1px solid #5cb85c;
    padding: 4px 8px;
    font-size: 12px;
    line-height: 1.5;
    border-radius: 3px;
}
</style>
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
                    <?=DocoHelpers::generateToolbar([
                    'upload' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Upload'),
                        'icon' => 'fa fa-upload',
                        'attributes' => [
                            'id' => 'btn-upload',
                            'data-options' => 'click'
                        ]
                    ],
                    'download' => [
                        'type' => 'button',
                        'attributes' => [
                            'id' => 'btn-download-template',
                            'data-target' => Url::home().'apotek/import/download-template',
                            'data-options' => 'click'
                        ]
                    ],
                ]);?>
                </div>
            </div>
            <div class="panel-body">
                <div class="col-md-12">
                	<?php
                    $form = ActiveForm::begin([
                        'id' => 'import-obat-form',
                        'enableAjaxValidation' => false,
                        'enableClientValidation' => false,
                        'type' => ActiveForm::TYPE_HORIZONTAL,
                        'formConfig' => [
                            'labelSpan' => 3,
                            'deviceSize' => ActiveForm::SIZE_SMALL
                        ],
                        'options' => [
                            'role' => 'form',
                            'enctype' => 'multipart/form-data'
                        ]
                    ]);
                    ?>
                	<div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'attachment', [
                                    'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-8',
                                    'id' => 'file-logo-header',
                                    'class' => 'custom-file-upload'
                                ]])->fileInput([
                                    'class' => 'custom-file-upload'
                                ])->label(Yii::t('fe', 'Import Excel').' <i>(Max 2MB)</i>');
                            ?>
                        </div>
                    </div>
                </div>
                <!-- <button id="btn-upload" type="submit" class="btn btn-info ">Upload</button> -->
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs($this->render("obat.js"));