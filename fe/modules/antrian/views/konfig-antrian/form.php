<?php

/**
 * @Author: Sigit
 * @Date:   2019-01-15 08:57:45
 */

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\FileInput;
use yii\widgets\Breadcrumbs;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

$this->title = Yii::t('fe', 'Konfigurasi Sistem Antrian');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white" style="margin-top: 0px !important">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
                <?= Breadcrumbs::widget([
                    'homeLink' => [
                        'label' => Yii::t('yii', 'Home'),
                        'url' => Yii::$app->homeUrl,
                    ],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]) ?>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'save' => [
                        'icon' => 'fa fa-floppy-o',
                        'title' => \Yii::t('fe', 'Ubah'),
                        'attributes' => [
                            'id' => 'btn-ubah-konfig-antrian',
                            'form_id' => 'konfigantrian-form',
                        ]
                    ],
                ]) ?>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin([
                    'id' => 'konfigantrian-form',
                    'enableAjaxValidation' => false,
                    'enableClientValidation' => false,
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => [
                        'labelSpan' => 4,
                        'deviceSize' => ActiveForm::SIZE_SMALL
                    ],
                    'options' => [
                        'enctype' => 'multipart/form-data'
                    ]
                ]) ?>

                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-white" style="margin-top: 10px;">
                            <div class="panel-heading">
                                <h5 class="panel-title"><b><?= Yii::t('fe', 'Konfigurasi Antrian') ?></b></h5>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'kuota_antrian')->dropDownList($listKuotaAntrian, [
                                            'class' => 'select2',
                                            'id' => 'kuota_antrian'
                                        ]) ?>
                                    </div>
                                    <div class="col-md-12">
                                    <?= $form->field($model, 'is_pilihketeranganpasien', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-2',
                                                'wrapper' => 'col-md-4'
                                            ]
                                        ])->radioList([
                                            1 => Yii::t('fe', 'Tampilkan'),
                                            0 => Yii::t('fe', 'Tidak Ditampilkan')
                                        ], [
                                            'inline' => true
                                        ]) ?>
                                    </div>
                                    <div class="col-md-12">
                                        <?= $form->field($model, 'is_nourut', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-2',
                                                'wrapper' => 'col-md-4'
                                            ]
                                        ])->radioList([
                                            0 => Yii::t('fe', 'Otomatis'),
                                            1 => Yii::t('fe', 'Manual')
                                        ], [
                                            'inline' => true
                                        ]) ?>
                                    </div>
                                    <div class="col-md-12 pisahCabar" <?= $attr?> >
                                        <?= $form->field($model, 'is_pisah_cabar', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-2',
                                                'wrapper' => 'col-md-4'
                                            ]
                                        ])->radioList([
                                            0 => Yii::t('fe', 'Gabung'),
                                            1 => Yii::t('fe', 'Pisah')
                                        ], [
                                            'inline' => true
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-white" style="margin-top: 10px;">
                            <div class="panel-heading">
                                <h5 class="panel-title"><b><?= Yii::t('fe', 'Konfigurasi Layar Antrian') ?></b></h5>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'header', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->textInput([
                                            'placeholder' => $model->getAttributeLabel('header'),
                                            'class' => 'form-control input-sm',
                                            'hint' => $model->getAttributeHint('header'),
                                            'maxLength' => 30
                                        ]) ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'header_detail', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->textInput([
                                            'placeholder' => $model->getAttributeLabel('header_detail'),
                                            'class' => 'form-control input-sm',
                                            'hint' => $model->getAttributeHint('header_detail'),
                                            'maxLength' => 55
                                        ]) ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'footer', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->textInput([
                                            'placeholder' => $model->getAttributeLabel('footer'),
                                            'class' => 'form-control input-sm',
                                            'autocomplete' => "off"
                                        ]) ?>
                                    </div>
                                </div>

                                <div class="row input-file-logo">
                                    <div class="col-md-12">
                                        <?= $form->field($model, 'logo', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-2'
                                            ]
                                        ])->widget(FileInput::classname(), [
                                            'options' => [
                                                'id' => 'logo',
                                            ],
                                            'pluginOptions' => [
                                                'allowedFileTypes' => ['image'],
                                                'allowedFileExtensions' => ['png'],
                                                'allowedPreviewTypes' => ['image'],
                                                'initialPreviewFileType' => 'image',
                                                'previewFileType' => 'image',
                                                'deleteUrl' => Url::to(['/antrian/konfig-antrian/hapus-logo-header']),
                                                'initialPreview' => $logoPath,
                                                'initialPreviewConfig' => $logoName,
                                                'showBrowse' => $showBrowseLogo,
                                                'initialPreviewAsData' => true,
                                                'showUpload' => false,
                                                'overwriteInitial' => false,
                                                'previewSettings' => [
                                                    'image' => [
                                                        'width' => 'auto',
                                                        'height' => '120px',
                                                        'max-width' => '100%',
                                                        'max-height' => '100%'
                                                    ]
                                                ]
                                            ]
                                        ]) ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <?= $form->field($model, 'is_banyakloket', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-2',
                                                'wrapper' => 'col-md-4'
                                            ]
                                        ])->radioList([
                                            0 => Yii::t('fe', '4 (Empat)'),
                                            1 => Yii::t('fe', '8 (Delapan)')
                                        ], [
                                            'inline' => true
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-white" style="margin-top: 10px;">
                            <div class="panel-heading">
                                <h5 class="panel-title"><b><?= Yii::t('fe', 'Tampilan Slide Layar Antrian') ?></b></h5>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <?= $form->field($model, 'is_slider', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-2',
                                                'wrapper' => 'col-md-4'
                                            ]
                                        ])->radioList([
                                            0 => Yii::t('fe', 'Foto'),
                                            1 => Yii::t('fe', 'Video'),
                                            2 => Yii::t('fe', 'URL')
                                        ], [
                                            'inline' => true
                                        ]) ?>
                                    </div>
                                </div>

                                <div class="row input-url-slider">
                                    <div class="col-md-12">
                                        <?= $form->field($model, 'url_slider', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-2',
                                                'wrapper' => 'col-md-4'
                                            ]
                                        ])->textInput([
                                            'class' => 'form-control',
                                            'placeholder' => $model->getAttributeLabel('url_slider')
                                        ]) ?>
                                    </div>
                                </div>

                                <div class="row input-file">
                                    <div class="col-md-12">
                                        <?= $form->field($model, 'slides', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-2'
                                            ]
                                        ])->widget(FileInput::classname(), [
                                            'options' => [
                                                'id' => 'slides',
                                                'multiple' => true
                                            ],
                                            'pluginOptions' => [
                                                'allowedFileTypes' => ['image', 'video'],
                                                'allowedFileExtensions' => ['jpg', 'png', 'mp4', 'mkv', 'avi'],
                                                'allowedPreviewTypes' => ['image', 'video'],
                                                'initialPreviewFileType' => 'image',
                                                'previewFileType' => 'any',
                                                'deleteUrl' => Url::to(['/antrian/konfig-antrian/hapus-slideshow']),
                                                'initialPreview' => $slidePaths,
                                                'initialPreviewConfig' => $slideNames,
                                                'initialPreviewAsData' => true,
                                                'showUpload' => false,
                                                'overwriteInitial' => false,
                                                'previewSettings' => [
                                                    'image' => [
                                                        'width' => 'auto',
                                                        'height' => '120px',
                                                        'max-width' => '100%',
                                                        'max-height' => '100%'
                                                    ],
                                                    'video' => [
                                                        'width' => 'auto',
                                                        'height' => '120px',
                                                        'max-width' => '100%',
                                                        'max-height' => '100%'
                                                    ],
                                                ]
                                            ]
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs($this->render('js/form.js'));
?>