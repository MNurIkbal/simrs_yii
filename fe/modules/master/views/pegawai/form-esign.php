<?php

/**
 * @author Randy Vianda Putra
 * @todo Konfig Farmasi
 * @copyright 23 April 2018 aweutist
 */


// use yii\web\View;
// use yii\helpers\Html;
// use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use yii\web\View;
use yii\helpers\Url;
use kartik\widgets\DatePicker;
use yii\helpers\ArrayHelper;
use kartik\widgets\FileInput;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', Yii::$app->docoVars->workspace("instalasi_name")), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<style>
    .datepicker>div{
        display:block;
    }
    .file-preview-image{
        width: 213px !important;
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
                        <h3 class="panel-title"><b><?= $this->title ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?=
                    DocoHelpers::generateToolbar([
                        'back' => [
                            'attributes' => [
                                'id' => 'btn-back',
                            ]
                        ],
                    ]);
                ?>
            </div>
        <?php if (!empty($message_tolak)) : ?>
            <div class="alert alert-warning" role="alert">
                <strong>Peringatan!</strong> sertifikat atas nama <?= $model->name ?> telah ditolak sebelumnya. Info : <?= $message_tolak ?>
            </div>
        <?php endif;?>
            <div class="panel-body">
                <?php
                    $form = ActiveForm::begin([
                        'id' => 'reg-esign-form',
                        'type' => ActiveForm::TYPE_HORIZONTAL,
                        'enableAjaxValidation' => false,
                        'enableClientValidation' => false,
                        'validateOnSubmit' => false,
                        'formConfig' => [
                            'labelSpan' => 4,
                            'deviceSize' => ActiveForm::SIZE_MEDIUM
                        ],
                        'options' => [
                            'class' => 'form-horizontal',
                            'role' => 'form',
                            'enctype' => 'multipart/form-data'
                        ]
                    ]);
                ?>

                <?= Html::hiddenInput('tnc', strip_tags($this->render('tnc_body'))); ?>

                <div class="col-md-10 col-md-offset-1">
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'email', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-8'
                                    ]
                                ])->textInput([
                                    'disabled' => $type == 'reenroll'
                                ]);
                            ?>
                            <div class="form-group">
                                <div class="col-md-4"></div>
                                <div class="col-md-8">
                                    <span id="error-opt-tgl" align="center"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'name', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-8'
                                    ]
                                ])->textInput([
                                    'disabled' => $type == 'reenroll'
                                ]);
                            ?>
                            <div class="form-group">
                                <div class="col-md-4"></div>
                                <div class="col-md-8">
                                    <span id="error-opt-tgl" align="center"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="sec-wna-reenroll" class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'nik', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-8'
                                    ]
                                ])->textInput([
                                ]);
                            ?>
                            <div class="form-group">
                                <div class="col-md-4"></div>
                                <div class="col-md-8">
                                    <span id="error-opt-tgl" align="center"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'photo_ktp', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-8'
                                    ]
                                ])->fileInput();
                            ?>
                            <div class="form-group">
                                <div class="col-md-4"></div>
                                <div class="col-md-8">
                                    <span id="error-opt-tgl" align="center"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'nationality_type', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-8'
                                    ]
                                ])->radioList([
                                    'WNI' => 'WNI',
                                    'WNA' => 'WNA',
                                ],[
                                    'id' => 'nationality_type',
                                ]);
                            ?>
                            <div class="form-group">
                                <div class="col-md-4"></div>
                                <div class="col-md-8">
                                    <span id="error-opt-tgl" align="center"></span>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div id="sec-wna">
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'identity_type', [
                                    'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                        ]
                                    ])->dropDownList([
                                        'PASSPORT' => 'KTP',
                                        'KITAS' => 'KITAS',
                                        'KITAP' => 'KITAP',
                                    ],[
                                        'id' => 'identity_type',
                                        'class'=>'select2',
                                    ]);
                                ?>
                                <div class="form-group">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-8">
                                        <span id="error-opt-tgl" align="center"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'country_code', [
                                    'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                        ]
                                    ])->textInput([
                                    ]);
                                ?>
                                <div class="form-group">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-8">
                                        <span id="error-opt-tgl" align="center"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'passport_number', [
                                    'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                        ]
                                    ])->textInput();
                                ?>
                                <div class="form-group">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-8">
                                        <span id="error-opt-tgl" align="center"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'passport_file', [
                                    'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                        ]
                                    ])->fileInput([
                                    ]);
                                ?>
                                <div class="form-group">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-8">
                                        <span id="error-opt-tgl" align="center"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'passport_date_expire', [
                                    'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                        ]
                                    ])->widget(DatePicker::classname(), [
                                        'language' => 'en',
                                        'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                        'options' => ['tabindex' => 10],
                                        'readonly' => true,
                                        'pluginOptions' => [
                                            'autoclose' => true,
                                            'format' => 'dd-M-yyyy',
                                        ]
                                    ]);
                                ?>
                                <div class="form-group">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-8">
                                        <span id="error-opt-tgl" align="center"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="sec-kitas-kitap" class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'company_supporting_document', [
                                    'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                        ]
                                    ])->fileInput([
                                    ]);
                                ?>
                                <div class="form-group">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-8">
                                        <span id="error-opt-tgl" align="center"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'identity_date_expire', [
                                    'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                        ]
                                    ])->widget(DatePicker::classname(), [
                                        'language' => 'en',
                                        'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                        'options' => ['tabindex' => 10],
                                        'readonly' => true,
                                        'pluginOptions' => [
                                            'autoclose' => true,
                                            'format' => 'dd-M-yyyy',
                                        ]
                                    ]);
                                ?>
                                <div class="form-group">
                                    <div class="col-md-4"></div>
                                    <div class="col-md-8">
                                        <span id="error-opt-tgl" align="center"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <input type="checkbox" id="tnc-checkbox" disabled> Saya setuju dengan <a href="#" id="tnc-link" style="color: #36a3f7">Syarat dan Ketentuan</a>
                        </div>
                    </div>
                </div>



                <div class="row">
                    <div class="col-md-4 col-md-offset-4">
                        <span id="error-opt-poly" align="center"></span>
                    </div>
                    <div class="col-md-4">&nbsp;</div>
                    &nbsp;
                </div>
            
                <button id="btn-submit" type="submit" class="btn bg-success-600 btn-huge-finish stepy-finish pull-right">Simpan <i class="icon-check position-right"></i></button>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<div id="modal-tnc" class="modal fade" style="z-index: 1041 !important; overflow-y:auto !important" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <?= $this->render('tnc') ?>
        </div>
    </div>
</div>

<?php
    $this->registerJs("
        var error = '$error'
        var type = '$type'
        if(error != '') {
            docoNotification('error', 'Pendaftaran Gagal!', error);

            setTimeout(function() {
                window.location.href = \"/master/pegawai\";
            }, 2000);
        }
    ");
    $this->registerJs($this->render('js/pegawai-esign.js'));
?>