<?php

/**
 * @Author: Sigit
 * @Date:   2019-01-21 10:57:31
 */

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use yii\helpers\Html;
use yii\web\View;
use kartik\widgets\FileInput;
use yii\helpers\Url;

$this->title = Yii::t('fe', 'Konfigurasi Sistem Pendaftaran');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white" style="margin-top: 0px !important">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b></h3>
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
                            'id' => 'btn-ubah-konfig-pendaftaran',
                            'form_id' => 'konfigpendaftaran-form',
                        ] 
                    ],
                ]) ?>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin([
                    'id' => 'konfigpendaftaran-form',
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => [
                        'labelSpan' => 4,
                        'deviceSize' => ActiveForm::SIZE_SMALL
                    ],
                    'enableClientValidation' => false,
                    'enableAjaxValidation' => false,
                    'options' => [
                        'enctype' => 'multipart/form-data'
                    ]
                ]) ?>

                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-white" style="margin-top: 10px;">
                            <div class="panel-heading">
                                <h5 class="panel-title"><b><?= Yii::t('fe', 'Konfigurasi Sistem Pendaftaran') ?></b></h5>
                            </div>
                            <div class="panel-body">
                                <div class="col-md-6">
                                    <?= $form->field($model, 'is_pemilihandokter')->radioList([
                                        1 => Yii::t('fe', 'Ya'),
                                        0 => Yii::t('fe', 'Tidak')
                                    ]) ?>
                                    <?= $form->field($model, 'reservasi_awal', [
                                        'addon' => [
                                            'append' => [
                                                'content' => Yii::t('fe', 'Hari')
                                            ],
                                        ]
                                    ])->textInput([
                                        'class' => 'docoNumberOnly'
                                    ]) ?>
                                    <?= $form->field($model, 'reservasi_akhir', [
                                        'addon' => [
                                            'append' => [
                                                'content' => Yii::t('fe', 'Hari')
                                            ],
                                        ]
                                    ])->textInput([
                                        'class' => 'docoNumberOnly'
                                    ]) ?>
                                    <?= $form->field($model, 'is_limit_tagihan')->radioList([
                                        1 => Yii::t('fe', 'Ya'),
                                        0 => Yii::t('fe', 'Tidak')
                                    ]) ?>
                                    <?= $form->field($model, 'is_reservasi')->radioList([
                                        1 => Yii::t('fe', 'Kuota Terpisah (kuota walkin dan reservasi mempunyai kuota terpisah)'),
                                        0 => Yii::t('fe', 'Kuota Gabung (kuota walkin dan reservasi menjadi satu kuota)')
                                    ]) ?>    
                                    <?= $form->field($model, 'is_slot_dokter')->radioList([
                                        1 => Yii::t('fe', 'Sloting dokter di aktifkan'),
                                        0 => Yii::t('fe', 'Sloting dokter di nonaktifkan')
                                    ]) ?>    
                                    <?= $form->field($model, 'support_multipayer')->radioList([
                                        1 => Yii::t('fe', 'Ya'),
                                        0 => Yii::t('fe', 'Tidak')
                                    ]) ?>
                                    <?= $form->field($model, 'is_set_igdkeri')->radioList([
                                        0 => Yii::t('fe', 'Gabung No Pendaftaran'),
                                        1 => Yii::t('fe', 'Pisah No Pendaftaran')
                                    ]) ?>
                                    <?= $form->field($model, 'is_sep_mandatory_on_edit')->radioList([
                                        0 => Yii::t('fe', 'Tidak Mandatory'),
                                        1 => Yii::t('fe', 'Mandatory')
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-white" style="margin-top: 10px;">
                                <div class="panel-heading">
                                    <h5 class="panel-title"><b><?= Yii::t('fe', 'Konfigurasi Dashboard Info Kamar') ?></b></h5>
                                </div>
                                
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'dash_kamarheader', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->textInput([
                                            'placeholder' => $model->getAttributeLabel('dash_kamarheader'),
                                            'class' => 'form-control input-sm',
                                            'hint' => $model->getAttributeHint('dash_kamarheader'),
                                            'maxLength' => 30
                                        ]) ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'dash_kamardetail', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->textInput([
                                            'placeholder' => $model->getAttributeLabel('dash_kamardetail'),
                                            'class' => 'form-control input-sm',
                                            'hint' => $model->getAttributeHint('dash_kamardetail'),
                                            'maxLength' => 55
                                        ]) ?>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'dash_kamarfooter', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->textInput([
                                            'placeholder' => $model->getAttributeLabel('dash_kamarfooter'),
                                            'class' => 'form-control input-sm',
                                            'autocomplete' => "off"
                                        ]) ?>
                                    </div>
                                </div>

                                <div class="row input-file-logo">
                                    <div class="col-md-12">
                                        <?= $form->field($model, 'dash_logo', [
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
                                                'deleteUrl' => Url::to(['/pendaftaran/konfig-pendaftaran/hapus-logo-header']),
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
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-white" style="margin-top: 10px;">
                            <div class="panel-heading">
                                <h5 class="panel-title"><b><?= Yii::t('fe', 'Konfigurasi Validasi Antrian') ?></b></h5>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <?= $form->field($model, 'is_validasipendaftaranrj', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-3',
                                                    'wrapper' => 'col-md-4'
                                                ]
                                            ])->radioList([
                                                1 => Yii::t('fe', 'Ya'),
                                                0 => Yii::t('fe', 'Tidak')
                                            ], [
                                                'inline' => true
                                            ]) ?>
                                    </div>
                                    <div class="col-md-12">
                                        <?= $form->field($model, 'is_validasipendaftaranri', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-3',
                                                    'wrapper' => 'col-md-4'
                                                ]
                                            ])->radioList([
                                                1 => Yii::t('fe', 'Ya'),
                                                0 => Yii::t('fe', 'Tidak')
                                            ], [
                                                'inline' => true
                                            ]) ?>
                                    </div>
                                    <div class="col-md-12">
                                        <?= $form->field($model, 'is_validasipendaftaranrd', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-3',
                                                    'wrapper' => 'col-md-4'
                                                ]
                                            ])->radioList([
                                                1 => Yii::t('fe', 'Ya'),
                                                0 => Yii::t('fe', 'Tidak')
                                            ], [
                                                'inline' => true
                                            ]) ?>
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

$this->registerJs("
    // $('#konfigpendaftaran-form').docoForm('submit', {
    //     success : function(data) {
    //         location.reload();
    //     }
    // });
" . $this->render('js/form.js'), View::POS_END, 'form');
?>