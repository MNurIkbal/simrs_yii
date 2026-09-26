<?php

/**
 * @author Randy Vianda Putra
 * @todo Transaksi Penyimpanan Dokumen Rm
 * @copyright 31 Mei 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;

$this->title = Yii::t('fe','Penyimpanan Dokumen Rekam Medik');
$this->params['breadcrumbs'][] = ['label' => 'Rekam Medis', 'url' => ['/rm/inf-dokumen']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="panel panel-white">
        <div class="panel-heading">
            <!-- breadcrumbs replace with this -->
            <div class="row">
                <div class="column-1">
                    <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                </div>
                <div class="column-2">
                    <h3 class="panel-title"><b><?= Yii::t('fe','Penyimpanan Dokumen Rekam Medik'); ?></b></h3>
                    <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                </div>
            </div>
            <!-- end -->
        </div>
        <div class="panel-toolbar clearfix">
            <?php
                if (!empty($model->status_indexing)) :
            ?>
                <?= Html::button('<b><i class="fa fa-save"></i></b>' . \Yii::t('fe', 'Simpan'), ['class' => 'btn bg-teal btn-labeled btn-xs', 'id' => 'btn-save', 'disabled' => 'disabled']) ?>
            <?php
                else :
            ?>
                <?= Html::button('<b><i class="fa fa-save"></i></b>' . \Yii::t('fe', 'Simpan'), ['class' => 'btn bg-teal btn-labeled btn-xs', 'id' => 'btn-save']) ?>
            <?php endif; ?>
            <?= Html::button('<b><i class="fa fa-repeat"></i></b>' . \Yii::t('fe', 'Ulang'), ['class' => 'btn btn-labeled btn-xs btn-aqua', 'id' => 'btn-ulang']) ?>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <?php
                        $form = ActiveForm::begin([
                            'id' => 'penyimpanan-form',
                            'enableAjaxValidation' => false,
                            'enableClientValidation' => false,
                            // 'type' => ActiveForm::TYPE_INLINE,
                            'type' => ActiveForm::TYPE_HORIZONTAL,
                            'formConfig' => [
                                'labelSpan' => 3,
                                'deviceSize' => ActiveForm::SIZE_SMALL
                            ],
                            'options' => [
                                'role' => 'form',
                                'enctype'=>'multipart/form-data'
                            ]
                        ]);
                    ?>
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <?= $form->field($model, 'no_rekam_medik', [
                                        'horizontalCssClasses' => ['label' => 'text-left control-label col-sm-4',
                                            'wrapper' => 'col-md-8'
                                        ]
                                    ])->dropDownList($data_norm, [
                                        'class' => 'form-control input-sm',
                                        'prompt' => Yii::t('fe', '-- Pilih --'),
                                        'disabled' => 'disabled'
                                    ]);
                                ?>
                                <?= Html::hiddenInput('dokrm_id', $id); ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'no_rak', [
                                        'horizontalCssClasses' => ['label' => 'text-left control-label col-sm-4',
                                            'wrapper' => 'col-md-8'
                                        ]
                                    ])->dropDownList($data_rak, [
                                        'class' => 'form-control input-sm',
                                        'prompt' => Yii::t('fe', '-- Pilih --'),
                                        'id' => 'no_rak',
                                    ]);
                                ?>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <?= $form->field($model, 'no_pengiriman', [
                                        'horizontalCssClasses' => ['label' => 'text-left control-label col-sm-4',
                                            'wrapper' => 'col-md-8'
                                        ]
                                    ])->dropDownList($data_no_kirimdokrm, [
                                        'class' => 'form-control input-sm',
                                        'prompt' => Yii::t('fe', '-- Pilih --'),
                                        'disabled' => 'disabled'
                                    ]);
                                ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'no_sub_rak', [
                                        'horizontalCssClasses' => ['label' => 'text-left control-label col-sm-4',
                                            'wrapper' => 'col-md-8'
                                        ]
                                    ])->widget(
                                        DepDrop::classname(),
                                        [
                                            'name' => 'subrak_id',
                                            'options' => [
                                                'disabled' => false,
                                                'class' => 'form-control subrak select2',
                                                'id' => 'subrak',
                                                'prompt' => Yii::t('fe', '-- Pilih --')
                                            ],
                                            'pluginOptions' => [
                                                'depends' => ['no_rak'],
                                                'placeholder' => Yii::t('fe', '-- Pilih --'),
                                                'url' => '/rm/transaksi-penyimpanan/get-subrak'
                                            ]
                                        ]
                                    );
                                ?>
                                <?= Html::activeHiddenInput($model, 'no_subrak', [
                                        'class' => 'no_sub_rak',
                                        'value' => isset($model->no_sub_rak) ? $model->no_sub_rak : ''
                                    ]);
                                ?>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <?php $model->status_indexing = 0; ?>
                                <?=
                                    $form->field($model, 'status_indexing', [
                                        'horizontalCssClasses' => ['label' => 'text-left control-label col-sm-4',
                                            'wrapper' => 'col-md-8'
                                        ]
                                    ])->radioList([
                                        '0' => Yii::t('fe','sudah'),
                                        '1' => Yii::t('fe','belum')
                                    ], [
                                        'inline' => true
                                    ]); 
                                ?>	
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'tgl_akhir_masuk', [
                                        'horizontalCssClasses' => ['label' => 'text-left control-label col-sm-4',
                                            'wrapper' => 'col-md-8'
                                        ]
                                    ])->textInput([
                                        'placeholder' => $model->getAttributeLabel('tgl_akhir_masuk'),
                                        'class' => 'form-control input-sm typeahead pickadate tgl_akhir',
                                        'data-tgl' => date('Y-m-d')
                                    ]);
                                ?>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <?php $model->status_assembling = 0; ?>
                                <?= $form->field($model, 'status_assembling', [
                                        'horizontalCssClasses' => ['label' => 'text-left control-label col-sm-4',
                                            'wrapper' => 'col-md-8'
                                        ]
                                    ])->radioList([
                                        '0' => Yii::t('fe','sudah'),
                                        '1' => Yii::t('fe','belum')
                                    ], [
                                        'inline' => true
                                    ]); 
                                ?>	
                            </div>
                        </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs($this->render('js/penyimpanan-dokumen.js'));
?>