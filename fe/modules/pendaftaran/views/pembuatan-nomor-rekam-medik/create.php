<?php

use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Pendaftaran'), 'url' => ['/pendaftaran/pembuatan-nomor-rekam-medik']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style type="text/css">
    .btn-link {
        color: green;
    }

    .select2 {
        width:100% !important;
    }

    /****  floating-Lable style start ****/
    .floating-label { 
        position:relative !important; 
        margin-bottom:20px !important; 
    }

    .floating-label .has-error { 
        outline:none !important;
        border:1px solid #F95454 !important; 
    }

    .floating-input , .floating-select {
        font-size:12px !important;
        padding:4px 4px !important;
        display:block !important;
        width:100% !important;
        height:35px !important;
        background-color: transparent !important;
        border:1px solid #ddd !important;
    }

    .floating-input:focus , .floating-select:focus {
        outline:none !important;
        border:1px solid #4fbfa3 !important; 
    }

    label {
        color:#999 !important; 
        font-size:12px !important;
        font-weight:normal !important;
        position:absolute !important;
        pointer-events:none !important;
        left:5px !important;
        top:5px !important;
        transition:0.6s ease all !important; 
        -moz-transition:0.6s ease all !important; 
        -webkit-transition:0.6s ease all !important;
    }

    .floating-input:focus ~ label, .floating-input:not(:placeholder-shown) ~ label {
        top:-18px !important;
        font-size:14px;
        color:#4fbfa3 !important;
    }

    .floating-select:focus ~ label , .floating-select:not([value=""]):valid ~ label {
        top:-18px !important;
        font-size:9px !important;
        color:#4fbfa3 !important;
    }

    /* active state */
    .floating-input:focus ~ .bar:before, .floating-input:focus ~ .bar:after, .floating-select:focus ~ .bar:before, .floating-select:focus ~ .bar:after {
        width:50% !important;
    }

    *, *:before, *:after {
        -webkit-box-sizing: border-box !important;
        -moz-box-sizing: border-box !important;
        box-sizing: border-box !important;
    }

    .floating-textarea {
        min-height: 30px !important;
        max-height: 260px !important; 
        overflow:hidden !important;
        overflow-x: hidden !important; 
    }

    /* highlighter */
    .highlight {
        position:absolute !important;
        height:50% !important; 
        width:100% !important; 
        top:15% !important; 
        left:0 !important;
        pointer-events:none !important;
        opacity:0.5 !important;
    }

    .help-block {
        color: red;
    }

    /* active state */
    .floating-input:focus ~ .highlight , .floating-select:focus ~ .highlight {
        -webkit-animation:inputHighlighter 1s ease !important;
        -moz-animation:inputHighlighter 1s ease !important;
        animation:inputHighlighter 1s ease !important;
    }

    /* animation */
    @-webkit-keyframes inputHighlighter {
        from {
            background:#4fbfa3 !important;
        }
        to {
            width:0; background:transparent !important;
        }
    }
    @-moz-keyframes inputHighlighter {
        from {
            background:#4fbfa3 !important;
        }
        to {
            width:0; background:transparent !important;
        }
    }
    @keyframes inputHighlighter {
        from {
            background:#4fbfa3 !important;
        }
        to {
            width:0; background:transparent !important;
        }
    }

    #copy-me {
        display:none
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon") ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= $title ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])) ?>
                    </div>
                </div>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <?php $form = ActiveForm::begin([
                'id' => 'form-pasien',
                'type' => ActiveForm::TYPE_HORIZONTAL,
                'enableClientValidation' => false,
                'enableAjaxValidation' => false,
                'action' => ['pembuatan-nomor-rekam-medik/create']
            ]) ?>
            <br>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="row identitas">
                            <div class="col-md-6">
                                <?= $form->field($model, 'jenisidentitas[]', [
                                    'horizontalCssClasses' => [
                                        'wrapper' => 'col-md-12'
                                    ]
                                ])->dropDownList($dataOptions['jenisIdentitas'], [
                                        'id' => 'jenisidentitas',
                                        'class' => 'select2 jenis_identitas'
                                    ]
                                )->label(false) ?>
                            </div>
                            <div class="col-md-5">
                                <div class="floating-label">      
                                    <input class="floating-input no_identitas_pasien" type="text" name="PasienForm[no_identitas_pasien][]" placeholder=" ">
                                    <span class="highlight"></span>
                                    <label>No Identitas Pasien</label>
                                    <div class="help-block"></div>
                                </div>
                            </div>
                            <div class="col-sm-1">
                                <div class="form-group highlight-addon has-size-sm">
                                    <label class="control-label"><b style="color:white;float:right;">Aksi</b></label>
                                    <?= Html::button('+', [
                                        'class' => 'btn btn-success tambah-jenis'
                                    ]) ?>
                                </div>
                                <div class="help-block"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="floating-label">      
                                    <input class="floating-input" type="text" name="PasienForm[tempat_lahir]" placeholder=" ">
                                    <span class="highlight"></span>
                                    <label>Tempat Lahir</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="floating-label">      
                                    <input class="floating-input" id="tanggal_lahir" type="text" name="PasienForm[tanggal_lahir]" placeholder=" " data-mask="99-99-9999" required>
                                    <span class="highlight"></span>
                                    <label>Tanggal Lahir *</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'statusperkawinan', [
                                    'horizontalCssClasses' => [
                                        'wrapper' => 'col-md-12'
                                    ]
                                ])->dropDownList(
                                    $dataOptions['statusPerkawinan'], [
                                        'id' => 'statusperkawinan',
                                        'class' => 'select2'
                                    ]
                                )->label(false) ?>
                            </div>
                            <div class="col-md-6">
                                <div class="floating-label">      
                                    <input class="floating-input" type="text" name="PasienForm[no_telepon_pasien]" placeholder=" ">
                                    <span class="highlight"></span>
                                    <label>No Telepon Pasien</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="floating-label">      
                                    <input class="floating-input" type="text" name="PasienForm[nama_pasien]" placeholder=" " required>
                                    <span class="highlight"></span>
                                    <label>Nama Pasien *</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'jeniskelamin', [
                                    'horizontalCssClasses' => [
                                        'wrapper' => 'col-md-12'
                                    ]
                                ])->dropDownList($dataOptions['jenisKelamin'], [
                                        'id' => 'jeniskelamin',
                                        'class' => 'select2'
                                    ]
                                )->label(false) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'umur', [
                                    'inputOptions' => [
                                        'id' => 'umur',
                                        'readonly' => true,
                                        'placeholder' => $model->getAttributeLabel('umur')
                                    ],
                                    'horizontalCssClasses' => [
                                        'wrapper' => 'col-md-12'
                                    ]
                                ])->label(false) ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'golongandarah', [
                                    'horizontalCssClasses' => [
                                        'wrapper' => 'col-md-12'
                                    ]
                                ])->dropDownList($dataOptions['golonganDarah'], [
                                        'id' => 'golongandarah',
                                        'class' => 'select2'
                                    ]
                                )->label(false) ?>
                            </div>
                        </div>
                        <div class="row" style="margin-top:2%;">
                            <div class="col-md-12">
                                <div class="floating-label">      
                                    <textarea class="floating-input floating-textarea" name="PasienForm[alamat_pasien]" placeholder=" "></textarea>
                                    <span class="highlight"></span>
                                    <label>Alamat Pasien</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <a class="btn btn-link btn-sm" data-toggle="collapse" href="#collapse-advanced" aria-expanded="false" aria-controls="collapse-advanced">
                            Opsi Lainnya <i class="fa fa-sort-down"></i>
                        </a>
                    </div>
                </div>
                <div class="collapse" id="collapse-advanced">
                    <div class="row">
                        <div class="col-md-3">
                            <?= $form->field($model, 'propinsi_id', [
                                'horizontalCssClasses' => [
                                    'wrapper' => 'col-md-12'
                                ]
                            ])->dropDownList($dataOptions['propinsi'], [
                                    'id' => 'propinsi_id',
                                    'class' => 'select2'
                                ]
                            )->label(false) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'kabupaten_id', [
                                'horizontalCssClasses' => [
                                    'wrapper' => 'col-md-12'
                                ]
                            ])->widget(DepDrop::classname(), [
                                'options' => [
                                    'id' => 'kabupaten_id',
                                    'class' => 'select2'
                                ],
                                'pluginOptions' => [
                                    'depends' => [
                                        'propinsi_id',
                                        'jenisidentitas',
                                        'no_identitas_pasien'
                                    ],
                                    'palceholder' => $model->getAttributeLabel('kabupaten_id'),
                                    'url' => Url::to(['/pendaftaran/end-point/list-kabupaten'])
                                ]
                            ])->label(false) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'kecamatan_id', [
                                'horizontalCssClasses' => [
                                    'wrapper' => 'col-md-12'
                                ]
                            ])->widget(DepDrop::classname(), [
                                'options' => [
                                    'id' => 'kecamatan_id',
                                    'class' => 'select2'
                                ],
                                'pluginOptions' => [
                                    'depends' => [
                                        'kabupaten_id',
                                        'jenisidentitas',
                                        'no_identitas_pasien'
                                    ],
                                    'palceholder' => $model->getAttributeLabel('kecamatan_id'),
                                    'url'=>Url::to(['/pendaftaran/end-point/list-kecamatan'])
                                ]
                            ])->label(false) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'kelurahan_id', [
                                'horizontalCssClasses' => [
                                    'wrapper' => 'col-md-12'
                                ]
                            ])->widget(DepDrop::classname(), [
                                'options' => [
                                    'id' => 'kelurahan_id',
                                    'class' => 'select2'
                                ],
                                'pluginOptions' => [
                                    'depends' => ['kecamatan_id'],
                                    'palceholder' => $model->getAttributeLabel('kelurahan_id'),
                                    'url' => Url::to(['/pendaftaran/end-point/list-kelurahan'])
                                ]
                            ])->label(false) ?>
                        </div>
                    </div>
                    <div class="row" style="margin-top:1%;">
                        <div class="col-md-3">
                            <div class="floating-label">      
                                <input class="floating-input" type="text" name="PasienForm[rt]" placeholder=" ">
                                <span class="highlight"></span>
                                <label>RT</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="floating-label">      
                                <input class="floating-input" type="text" name="PasienForm[rw]" placeholder=" ">
                                <span class="highlight"></span>
                                <label>RW</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="floating-label">      
                                <input class="floating-input" type="text" name="PasienForm[nama_ibu]" placeholder=" ">
                                <span class="highlight"></span>
                                <label>Nama Ibu</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="floating-label">      
                                <input class="floating-input" type="text" name="PasienForm[nama_ayah]" placeholder=" ">
                                <span class="highlight"></span>
                                <label>Nama Ayah</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="floating-label">      
                                <input class="floating-input" id="anakke" type="text" name="PasienForm[anakke]" placeholder=" ">
                                <span class="highlight"></span>
                                <label>Anak Ke</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="floating-label">      
                                <input class="floating-input" id="jumlah_bersaudara" type="text" name="PasienForm[jumlah_bersaudara]" placeholder=" ">
                                <span class="highlight"></span>
                                <label>Jumlah Bersaudara</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="floating-label">      
                                <input class="floating-input" id="alamatemail" type="text" name="PasienForm[alamatemail]" placeholder=" ">
                                <span class="highlight"></span>
                                <label>Email</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'pendidikan_id', [
                                'horizontalCssClasses' => [
                                    'wrapper' => 'col-md-12'
                                ]
                            ])->dropDownList($dataOptions['pendidikan'], [
                                    'id' => 'pendidikan_id',
                                    'class' => 'select2'
                                ]
                            )->label(false) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <?= $form->field($model, 'pekerjaan_id', [
                                'horizontalCssClasses' => [
                                    'wrapper' => 'col-md-12'
                                ]
                            ])->dropDownList($dataOptions['pekerjaan'], [
                                    'id' => 'pekerjaan_id',
                                    'class' => 'select2'
                                ]
                            )->label(false) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'suku_id', [
                                'horizontalCssClasses' => [
                                    'wrapper' => 'col-md-12'
                                ]
                            ])->dropDownList($dataOptions['suku'], [
                                    'id' => 'suku_id',
                                    'class' => 'select2'
                                ]
                            )->label(false) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'warga_negara', [
                                'horizontalCssClasses' => [
                                    'wrapper' => 'col-md-12'
                                ]
                            ])->dropDownList($dataOptions['wargaNegara'], [
                                    'id' => 'warga_negara',
                                    'class' => 'select2'
                                ]
                            )->label(false) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'agama', [
                                'horizontalCssClasses' => [
                                    'wrapper' => 'col-md-12'
                                ]
                            ])->dropDownList($dataOptions['agama'], [
                                    'id' => 'agama',
                                    'class' => 'select2'
                                ]
                            )->label(false) ?>
                        </div>
                        <input type="text"id="copy-me" value=""/>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-12" style="text-align:right;">
                        <?= Html::button('<b><i class="fa fa-refresh"></i></b> Muat Ulang', [
                            'id' => 'btn-muat-ulang',
                            'class' => 'btn btn-danger btn-labeled btn-xs'
                        ]) ?>
                        <?= Html::submitButton('<b><i class="fa fa-floppy-o"></i></b> Simpan', [
                            'class' => 'btn btn-info btn-labeled btn-xs'
                        ]) ?>
                    </div>
                </div>
                <br>
            </div>
            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>

<?php
    $this->registerJs($this->render('js/create.js'));
?>