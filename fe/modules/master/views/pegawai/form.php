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
            <div class="panel-body">
                <?php
                    $is_disabled = empty($id) ? false : true;
                    $form = ActiveForm::begin([
                        'id' => 'konfig-form',
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
                    <fieldset title="1">
                        <legend class="text-semibold">Step 1</legend>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'noidentitas', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                                ]
                                            ])->textInput([
                                                'tabindex' => 1
                                            ])->label(Yii::t('fe', 'NIK'));
                                        ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-opt-tgl" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'satusehat_pegawai_id', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                                ]
                                            ])->textInput([
                                                'tabindex' => 1,
                                                'disabled' => true
                                            ])->label(Yii::t('fe', 'Satu Sehat Practitioner ID'));
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
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'nomorindukpegawai', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                                ]
                                            ])->textInput([
                                                'readonly' => $is_disabled,
                                                'tabindex' => 1
                                            ])->label(Yii::t('fe', 'NIP'));
                                        ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-opt-tgl" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'warganegara_pegawai', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                            ])->dropDownList($warga_negara, ['prompt' => '-- Pilih --','class'=>'select2', 'tabindex' => 11])->label(Yii::t('fe', 'Warga negara'));
                                        ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-formula" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'gelardepan', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->dropDownList($gelar_depan, ['prompt' => '-- Pilih --', 'class' => 'select2', 'tabindex' => 2])->label(Yii::t('fe', 'Gelar depan'));
                                        ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-opt-tgl" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'suku_id', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->dropDownList($suku, ['prompt' => '-- Pilih --', 'class' => 'select2', 'tabindex' => 12])->label(Yii::t('fe', 'Suku'));
                                        ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-formula" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'nama_pegawai', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->textInput(['tabindex' => 3])->label(Yii::t('fe', 'Nama pegawai'));
                                        ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-opt-tgl" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'alamat_pegawai', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->textArea(['tabindex' => 13])->label(Yii::t('fe', 'Alamat pegawai'));
                                        ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-formula" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'gelarbelakang', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->dropDownList($gelar_belakang, ['prompt' => '-- Pilih --', 'class' => 'select2', 'tabindex' => 4])->label(Yii::t('fe', 'Gelar belakang'));
                                        ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-opt-tgl" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'propinsi_id')
                                            ->dropDownList(
                                                $provinsi,
                                                ['id' => 'frm-pasien-propinsi_id', 'class' => 'select2', 'prompt' => '-- Pilih --', 'tabindex' => 14]
                                            )
                                            ->label(Yii::t('fe', 'Propinsi'));
                                        ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-formula" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'jeniskelamin', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->dropDownList($jenis_kelamin, ['prompt' => '-- Pilih --', 'class' => 'select2', 'tabindex' => 5])->label(Yii::t('fe', 'Jenis kelamin'));
                                        ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-opt-tgl" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'kabupaten_id')->widget(DepDrop::classname(), [
                                            'options' => ['id' => 'frm-pasien-kabupaten_id', 'class' => 'select2', 'tabindex' => 15],
                                            'data' => [@$data_pegawai["kabupaten_id"] => @$data_pegawai["kabupaten_nama"]],
                                            'pluginOptions' => [
                                                'depends' => ['frm-pasien-propinsi_id'],
                                                'placeholder' => '-- Pilih --',
                                                'url' => Url::to(['/master/kabupaten/list-kabupaten'])
                                            ],
                                        ])->label(Yii::t('fe', 'Kabupaten')); ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-formula" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'status_kawin', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->dropDownList($status_perkawinan, ['prompt' => '-- Pilih --', 'class' => 'select2', 'tabindex' => 6])->label(Yii::t('fe', 'Status perkawinan'));
                                        ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-opt-tgl" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'kecamatan_id')->widget(DepDrop::classname(), [
                                            'options' => ['id' => 'frm-pasien-kecamatan_id', 'class' => 'select2', 'tabindex' => 16 ],
                                            'data' => [@$data_pegawai["kecamatan_id"] => @$data_pegawai["kecamatan_nama"]],
                                            'pluginOptions' => [
                                                'depends' => ['frm-pasien-kabupaten_id'],
                                                'placeholder' => '-- Pilih --',
                                                'url' => Url::to(['/master/kecamatan/list-kecamatan'])
                                            ]
                                        ])->label(Yii::t('fe', 'Kecamatan')); ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-formula" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'agama', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->dropDownList($agama, ['prompt' => '-- Pilih --', 'class' => 'select2', 'tabindex' => 7])->label(Yii::t('fe', 'Agama'));
                                        ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-opt-tgl" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'kelurahan_id')->widget(DepDrop::classname(), [
                                            'options' => ['id' => 'frm-pasien-kelurahan_id', 'class' => 'select2', 'tabindex' => 17 ],
                                            'data' => [@$data_pegawai["kelurahan_id"] => @$data_pegawai["kelurahan_nama"]],
                                            'pluginOptions' => [
                                                'depends' => ['frm-pasien-kecamatan_id'],
                                                'placeholder' => '-- Pilih --',
                                                'url' => Url::to(['/master/kelurahan/list-kelurahan'])
                                            ]
                                        ])->label(Yii::t('fe', 'Kelurahan')); ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-formula" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'golongan_darah', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->dropDownList($golongan_darah, ['prompt' => '-- Pilih --', 'class' => 'select2', 'tabindex' => 8])->label(Yii::t('fe', 'Golongan darah'));
                                        ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-opt-tgl" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'notelp_pegawai', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->textInput(['tabindex' => 18,'class'=>'docoNumberOnly'])->label(Yii::t('fe', 'Nomor telp'));
                                        ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-formula" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'tempatlahir_pegawai', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->textInput(['class'=> 'pickadate-w-month', 'tabindex' => 9])->label(Yii::t('fe', 'Tempat lahir'));
                                        ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-opt-tgl" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'nomobile_pegawai', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->textInput(['tabindex' => 19,'class'=>'docoNumberOnly'])->label(Yii::t('fe', 'Nomor handphone'));
                                        ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-formula" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'tgl_lahirpegawai', ['horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]])->widget(DatePicker::classname(), [
                                                'name' => 'date_12',
                                                'language' => 'en',
                                                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                                'options' => ['tabindex' => 10],
                                                'readonly' => true,
                                                'pluginOptions' => [
                                                    'autoclose' => true,
                                                    'format' => 'dd-M-yyyy',
                                                    'endDate' => "0d",
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
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'alamatemail', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->textInput(['tabindex' => 20])->label(Yii::t('fe', 'Alamat email'));
                                        ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-formula" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
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
                    </fieldset>
                    <fieldset title="2">
                        <legend class="text-semibold">Step 2</legend>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'pendidikan_id', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->dropDownList($pendidikan, ['prompt' => '-- Pilih --','class'=>'select2', 'id' => 'pendidikan-depend', 'tabindex' => 21])->label(Yii::t('fe', 'Pendidikan'));
                                        ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-opt-tgl" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'jabatan_id', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->dropDownList($jabatan, ['prompt' => '-- Pilih --', 'class' => 'select2', 'tabindex' => 28])->label(Yii::t('fe', 'Jabatan'));
                                        ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-formula" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'pendkualifikasi_id')->widget(DepDrop::classname(), [
                                            'options' => ['id' => 'pendidikan-kualifikasi', 'class' => 'select2', 'tabindex' => 22 ],
                                            'pluginOptions' => [
                                                'depends' => ['pendidikan-depend'],
                                                'placeholder' => '-- Pilih --',
                                                'url' => Url::to(['/master/pegawai/get-kualifikasi'])
                                            ],
                                        ])->label(Yii::t('fe', 'Kualifikasi pendidikan')); ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-opt-tgl" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'pangkat_id', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->dropDownList($pangkat, ['prompt' => '-- Pilih --', 'class' => 'select2', 'tabindex' => 29])->label(Yii::t('fe', 'Pangkat'));
                                        ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-formula" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'kemampuan_bahasa', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->dropDownList($kemampuan_bahasa, ['prompt' => '-- Pilih --', 'class' => 'select2', 'tabindex' => 23])->label(Yii::t('fe', 'Kemampuan Bahasa'));
                                        ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-opt-tgl" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'kelompokpegawai_id', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->dropDownList($kelompok_pegawai, ['prompt' => '-- Pilih --', 'class' => 'select2', 'tabindex' => 30])->label(Yii::t('fe', 'Kelompok Pegawai'));
                                        ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-formula" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'warna_kulit', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->dropDownList($warna_kulit, ['prompt' => '-- Pilih --', 'class' => 'select2', 'tabindex' => 24])->label(Yii::t('fe', 'Warna Kulit'));
                                        ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-opt-tgl" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'status_pegawai', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->dropDownList($status_pegawai, ['prompt' => '-- Pilih --', 'class' => 'select2', 'tabindex' => 31])->label(Yii::t('fe', 'Status Pegawai'));
                                        ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-formula" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'tinggibadan', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->textInput(['tabindex' => 25,'class' => 'docoNumberOnly',])->label(Yii::t('fe', 'Tinggi Badan'));
                                        ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-opt-tgl" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'npwp', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->textInput(['tabindex' => 32])->label(Yii::t('fe', 'NPWP'));
                                        ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-formula" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'beratbadan', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->textInput(['tabindex' => 26,'class' => 'docoNumberOnly'])->label(Yii::t('fe', 'Berat Badan'));
                                        ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-opt-tgl" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'bank_id', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->dropDownList($nama_bank, ['prompt' => '-- Pilih --', 'class' => 'select2', 'tabindex' => 33])->label(Yii::t('fe', 'Nama Bank'));
                                        ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-formula" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php //$form->field($model, 'photopegawai', [
                                        //     'horizontalCssClasses' => [
                                        //         'label' => 'text-left control-label col-sm-4',
                                        //         'wrapper' => 'col-md-8'
                                        //     ]
                                        // ])->fileInput(['tabindex' => 27, 'id' => 'file'])->label(Yii::t('fe', 'Foto Pegawai'));
                                        ?>
                                        <?php
                                            echo $form->field($model, 'photopegawai', ['horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8',
                                            ]])->widget(FileInput::classname(), [
                                                'pluginOptions' => [
                                                    'initialPreview' => $preview_file_gambar,
                                                    'initialPreviewAsData' => true,
                                                    'showUpload' => false,
                                                ],
                                                'options' => ['accept' => 'image/*', 'class' => 'file-input', 'id' => 'file'],
                                            ])->label(Yii::t('fe', 'Foto Pegawai <i>(Max 5MB)</i>'));
                                        ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-opt-tgl" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'no_rekening', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->textInput(['tabindex' => 34])->label(Yii::t('fe', 'No Rekening'));
                                        ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-formula" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php
                                            echo $form->field($model, 'tanda_tangan', ['horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8',
                                            ]])->widget(FileInput::classname(), [
                                                'pluginOptions' => [
                                                    'initialPreview' => $preview_file_ttd,
                                                    'initialPreviewAsData' => true,
                                                    'showUpload' => false,
                                                ],
                                                'options' => ['accept' => 'image/*', 'class' => 'file-input', 'id' => 'file-ttd'],
                                            ])->label(Yii::t('fe', 'Tanda Tangan Pegawai <i>(Max 5MB)</i>'));
                                        ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-opt-tgl" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'is_online',[
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->checkbox()->label(Yii::t('fe', 'Pegawai Online')); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'aktif', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->dropDownList($status_aktif, ['class' => 'select2', 'tabindex' => 35])->label(Yii::t('fe', 'Status Aktif'));
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
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'kode_dokter_bpjs')->dropDownList($model->kode_dokter_bpjs ?
                                            [$model->kode_dokter_bpjs => $model->nama_dokter_bpjs] : [],
                                            [
                                                'prompt' => Yii::t('fe', '-- Pilih --'),
                                            ])->label(Yii::t('fe', 'Mapping Bpjs'));
                                        ?>
                                        <?=
                                            // ini untuk kalau mau menyimpan nama_dokter_bpjs di database
                                            // untuk menanggulangi perbedaan penulisan nama pegawai di sistem dengan BPJS
                                            $form->field($model, 'nama_dokter_bpjs')->hiddenInput()->label(false)
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
                        </div>
                        
                        <div class="row">
                            <div class="col-lg-12">

                            </div>
                        </div>
                    </fieldset>
                    <button id="btn-submit" type="submit" class="btn bg-success-600 btn-huge-finish stepy-finish">Simpan <i class="icon-check position-right"></i></button>

                    <?php ActiveForm::end(); ?>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs("
    $(document).ready(function() {
        const url = document.URL;
        const patternAction = url.match(/update/g);
        const propinsi = $('#frm-pasien-propinsi_id').val();
        if (propinsi) {
            if (patternAction) {
                if (patternAction[0] == 'update') {
                    // setTimeout(() => {
                        $('#frm-pasien-propinsi_id').val($model->propinsi_id).trigger('change').trigger('depdrop:change');
                        // $('#frm-pasien-kabupaten_id').on('depdrop:afterChange', function (event, id, value) {
                        //     $(this).val($model->kabupaten_id).trigger('change').trigger('depdrop:change');
                        // });
                        // setTimeout(() => {
                        //     $('#frm-pasien-kecamatan_id').on('depdrop:afterChange', function (event, id, value) {
                        //         $(this).val($model->kecamatan_id).trigger('change').trigger('depdrop:change');
                        //     });
                        // }, 600);
                        // setTimeout(() => {
                        //     $('#frm-pasien-kelurahan_id').on('depdrop:afterChange', function (event, id, value) {
                        //         $(this).val($model->kelurahan_id).trigger('change');
                        //     });
                        // }, 600);
                    // }, 1200);

                    $('#pendidikan-depend').val($model->pendidikan_id).trigger('change').trigger('depdrop:change');
                    $('#pendidikan-kualifikasi').on('depdrop:afterChange', function (event, id, value) {
                        $(this).val($model->pendkualifikasi_id).trigger('change')
                    });
                }
            }
        }
    })

    $(document).ready(function(){
        $('#pegawaiform-kode_dokter_bpjs').select2({
            placeholder: 'Pilih Dokter DPJP',
            allowClear: true,
            ajax: {
                url: '/api/bpjs/referensi-dpjp',
                dataType: 'json',
                quietMillis: 250,
                data: function (params) {
                    var query = {
                        search: params.term,
                        type: 'public',
                    }
                    return query;
                },
            },
        });
        $('#pegawaiform-kode_dokter_bpjs').on('select2:selecting', function (e) {
          $('#pegawaiform-kode_dokter_bpjs').empty()
        });
        $('#pegawaiform-kode_dokter_bpjs').change(function(){
            if($('#pegawaiform-kode_dokter_bpjs').val()) {
                let data = $('#pegawaiform-kode_dokter_bpjs').select2('data')
                if(data.length > 0) {
                     $('#pegawaiform-nama_dokter_bpjs').val(data[0].text.trim())
                } else {
                     $('#pegawaiform-nama_dokter_bpjs').val(null)
                }
            } else {
                $('#pegawaiform-nama_dokter_bpjs').val(null)
            }
        });
        $('#pegawaiform-kelompokpegawai_id').change(function(){
            if($(this).val() == " . DocoConstants::KELOMPOK_MEDIS . ") {
                $('#pegawaiform-kode_dokter_bpjs').closest('.form-group').removeClass('hidden')
            } else {
                $('#pegawaiform-kode_dokter_bpjs').closest('.form-group').addClass('hidden')
                $('#pegawaiform-kode_dokter_bpjs').val(null).trigger('change')
            }
        });
        $('#pegawaiform-kelompokpegawai_id').trigger('change')
    })

");
    $this->registerCss($this->render('../assets/css/wizard.css'));
    $this->registerJs($this->render('../assets/js/konfig-pegawai.js'));
?>