<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-08-10 11:15:12
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-08-15 16:13:04
 */
use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\web\View;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<style type="text/css">
body>.ui-pnotify {
    z-index: 1039 !important;
}
</style>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
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
                <?=
                    DocoHelpers::generateToolbar([
                        'custom-save' => [
                            'type' => 'button',
                            'title' => Yii::t('fe', 'Simpan'),
                            'icon' => 'fa fa-floppy-o',
                            'attributes' => [
                                'data-options' => 'click',
                                'id' => 'simpan-adjustment'
                            ],
                        ],
                    ]);
                ?>
            </div>
            <div class="panel-body" style="min-height: 400px;">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h6 class="panel-title"><b><?= $title ?></b></h6>
                    </div>
                    <div class="panel-body">
                        <?php
                            $form = ActiveForm::begin([
                                'id' => 'ajax-form',
                                'enableAjaxValidation'=>false,
                                'enableClientValidation'=>false,
                                'type' => ActiveForm::TYPE_HORIZONTAL,
                                'formConfig' => [
                                    'labelSpan' => 3,
                                    'deviceSize' => ActiveForm::SIZE_SMALL
                                ],
                                'options' => [
                                    'skip-confirm' => "true"
                                ]
                            ]);
                            echo Html::hiddenInput('tab-aktif', '', [
                                'class' => 'tab-aktif'
                            ]);
                        ?>
                        <div class="col-md-6">
                            <?= $form->field($model, 'peg_mengetahui_id',[
                            'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-8'
                                ],
                            ])->dropDownList([],[
                                'class' => '',
                                'id' => 'peg_mengetahui_id',
                            ])->label(Yii::t('fe', 'Pegawai Mengetahui')); ?>

                            <?= $form->field($model, 'tgl_adjusmen', [
                            'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-8'
                                ]
                            ])->textInput([
                                'placeholder' => Yii::t('fe', 'Tanggal Adjustment'),
                                'class' => 'form-control input-sm',
                                'readOnly' => true,
                                'value' => date('d-M-Y'),
                            ])->label(Yii::t('fe', 'Tanggal Adjustment')); ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'peg_menyetujui_id',[
                            'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-8'
                                ],
                            ])->dropDownList([],[
                                'class' => '',
                                'id' => 'peg_menyetujui_id',
                            ])->label(Yii::t('fe', 'Pegawai Menyetujui')); ?>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
                <div class="col-md-12" style="overflow-x:auto;">
                    <?=Yii::$app->controller->renderPartial('adjustment_tab', [

                    ]);?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$phpVars = [
    'phpform' => [
        'user' => [
            'id' => isset($pegawai_id) ? $pegawai_id : null,
            'name' => isset($pegawai_nama) ? $pegawai_nama : null
        ],
        'defaultuser' => $adjustForm['current_user']
    ]
];
$this->registerJsVar('phpVariables', $phpVars);
$this->registerJs($this->render('/assets/js/adjustment.js').$this->render('/alert-perubahan-harga/alert-harga.js', ["url"=>"adjustment-obat-alkes"]), View::POS_END);
?>