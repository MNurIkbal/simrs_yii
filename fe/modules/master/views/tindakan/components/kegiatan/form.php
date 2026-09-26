<?php

/**
 * @Author: Sigit
 * @Date:   2019-03-22 16:10:01
 */
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'simpan' => [
                        'title' => \Yii::t('fe', 'Simpan'),
                        'icon' => 'fa fa-save',
                        'attributes' => [
                            'class' => 'spa',
                            'action' => $action,
                            'data-options' => 'click',
                            'form-id' => 'kegiatan-form',
                            'data-render' => 'kegiatan',
                            'data-tab' => 'tab-kegiatan',
                            'data-target' => '#view-kegiatan',
                            'id' => 'btn-simpan-kegiatan'
                        ]
                    ],
                    'kembali' => [
                        'title' => \Yii::t('fe', 'Kembali'),
                        'icon' => 'fa fa-arrow-left',
                        'attributes' => [
                            'class' => 'spa',
                            'data-options' => 'click',
                            'data-render' => 'kegiatan',
                            'data-tab' => 'tab-kegiatan',
                            'data-target' => '#view-kegiatan',
                        ]
                    ],
                ]) ?>
            </div>
            
            <div class="panel-body">
                <?php
                    $form = ActiveForm::begin([
                        'id' => 'kegiatan-form',
                        'enableAjaxValidation' => false,
                        'enableClientValidation' => false,
                        'type' => ActiveForm::TYPE_HORIZONTAL,
                        'formConfig' => [
                            'labelSpan' => 3,
                            'deviceSize' => ActiveForm::SIZE_SMALL
                        ],
                        'options' => [
                            'role' => 'form',
                        ]
                    ]);
                ?>
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label text-left control-label col-sm-12">
                            <h3><strong><?= $title ?></strong></h3>
                        </label>
                    </div>
                    <?= $form->field($model, 'jeniskegiatantindakan_kode', [
                        'horizontalCssClasses' => [
                            'label' => 'text-left control-label col-sm-2',
                            'wrapper' => 'col-md-5'
                        ]
                    ])->textInput() ?>

                    <?= $form->field($model, 'jeniskegiatantindakan_nama', [
                        'horizontalCssClasses' => [
                            'label' => 'text-left control-label col-sm-2',
                            'wrapper' => 'col-md-5'
                        ]
                    ])->textInput() ?>

                    <?= $form->field($model, 'jeniskegiatan_namalainnya', [
                        'horizontalCssClasses' => [
                            'label' => 'text-left control-label col-sm-2',
                            'wrapper' => 'col-md-5'
                        ]
                    ])->textInput() ?>
                    
                    <?= $form->field($model, 'is_active', [
                        'horizontalCssClasses' => [
                            'label' => 'text-left control-label col-sm-2',
                            'wrapper' => 'col-md-5'
                        ]
                    ])->checkbox(['label' => 'Aktif']) ?>

                    <?= $form->field($model, 'jeniskegiatan_keterangan', [
                        'horizontalCssClasses' => [
                            'label' => 'text-left control-label col-sm-2',
                            'wrapper' => 'col-md-5'
                        ]
                    ])->textarea() ?>
                    
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php
    $this->registerJs($this->render('js/kegiatan.js'), View::POS_END);
?>
