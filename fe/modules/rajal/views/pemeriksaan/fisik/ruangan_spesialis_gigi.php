<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use app\components\DHtml;
use app\components\DocoHelpers;
use app\components\DocoConstants;

?>


<div class="panel-toolbar clearfix">
    <?= DocoHelpers::generateToolbar([
        'save' => [
            'attributes' => [
                'form_id' => 'form-fisik',
                'id' => 'submit-fisik',
            ]
        ],
        'custom-print' => [
            'type' => 'button',
            'title' => Yii::t('fe', 'Cetak'),
            'icon' => 'fa fa-print',
            'attributes' => [
                'class' => 'print',
                'method' => 'json',
                'data-options' => 'link',
            ],
        ],
    ]); ?>
    <span class="draft mr-3" id="draft" style="color:red;display:none">Draft : anda harus menyimpan terlebih dulu</span>
</div>

<h1 style="text-align:center;">Riwayat Penyakit & Pemeriksaan Gigi</h1>
<hr style="margin-bottom:25px;">
<div class="help-block"></div>
<?php
$form = ActiveForm::begin([
    'enableClientValidation' => false,
    'enableAjaxValidation' => false,
    'id' => 'form-fisik',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 5, 'deviceSize' => ActiveForm::SIZE_SMALL],

]);
?>

<?= Yii::$app->controller->renderPartial('/pemeriksaan/fisik/__askep', [
    'modelFisik' => $modelFisik,
    'modelAskep' => $modelAskep,
    'form' => $form,
    'data_nama_dokter' => $data_nama_dokter,
    'gcs_list_eye' => $gcs_list_eye,
    'gcs_list_verbal' => $gcs_list_verbal,
    'gcs_list_motorik' => $gcs_list_motorik,
    'gcsEyeOptions' => $gcsEyeOptions,
    'gcsVerbalOptions' => $gcsVerbalOptions,
    'gcsMotorikOptions' => $gcsMotorikOptions,
    'list_perawat' => $list_perawat,
    'isDokter' => $isDokter,
    'configVal' => $configVal
]); ?>

<hr style="margin-top:15px;">

<h2>Asesmen Medis</h2>

<hr>

<div class="row">
    <div class="col-sm-8">
        <?= $form->field($modelFisik, 'kategoriPasien', [
            'labelOptions' => ['class' => '']
        ])->dropDownList(
                [
                    'dewasa' => 'Dewasa',
                    'anak' => 'Anak'
                ],
                [
                    'class' => 'form-control select2 input-sm',
                    'id' => 'kategoriPasien',
                ]
            );
        ?>
    </div>

    <div class="help-block" style="margin-bottom:50px;"></div>

    <div class="table table-responsive table-odontogram">
        <table style="width:100%;">
            <tbody>
                <tr>
                    <td colspan="2" style="text-align:center;" id="1151">
                        11
                    </td>
                    <td colspan="3">
                        <?= $form->field($modelFisik, 'odonto11', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control odonto-input',
                                    'disabled' => $isDokter ? false : true,
                                    'style' => 'margin-right:15px;',
                                    'data-field' => 'odonto1151',
                                ]); ?>
                    </td>
                    <td colspan="3">
                        <?= $form->field($modelFisik, 'odonto21', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control odonto-input',
                                    'disabled' => $isDokter ? false : true,
                                    'style' => 'margin-left:15px;',
                                    'data-field' => 'odonto6121'
                                ]); ?>
                    </td>
                    <td colspan="2" style="text-align:center;" id="6121">
                        21
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;" id="1252">
                        12
                    </td>
                    <td colspan="3">
                        <?= $form->field($modelFisik, 'odonto12', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control odonto-input',
                                    'disabled' => $isDokter ? false : true,
                                    'style' => 'margin-right:15px;',
                                    'data-field' => 'odonto1252'
                                ]); ?>
                    </td>
                    <td colspan="3">
                        <?= $form->field($modelFisik, 'odonto22', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control odonto-input',
                                    'disabled' => $isDokter ? false : true,
                                    'style' => 'margin-left:15px;',
                                    'data-field' => 'odonto6222'
                                ]); ?>
                    </td>
                    <td colspan="2" style="text-align:center;" id="6222">
                        22
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;" id="1353">
                        13
                    </td>
                    <td colspan="3">
                        <?= $form->field($modelFisik, 'odonto13', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control odonto-input',
                                    'disabled' => $isDokter ? false : true,
                                    'style' => 'margin-right:15px;',
                                    'data-field' => 'odonto1353'
                                ]); ?>
                    </td>
                    <td colspan="3">
                        <?= $form->field($modelFisik, 'odonto23', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control odonto-input',
                                    'disabled' => $isDokter ? false : true,
                                    'style' => 'margin-left:15px;',
                                    'data-field' => 'odonto6323'
                                ]); ?>
                    </td>
                    <td colspan="2" style="text-align:center;" id="6323">
                        23
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;" id="1454">
                        14
                    </td>
                    <td colspan="3">
                        <?= $form->field($modelFisik, 'odonto14', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control odonto-input',
                                    'disabled' => $isDokter ? false : true,
                                    'style' => 'margin-right:15px;',
                                    'data-field' => 'odonto1454'
                                ]); ?>
                    </td>
                    <td colspan="3">
                        <?= $form->field($modelFisik, 'odonto24', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control odonto-input',
                                    'disabled' => $isDokter ? false : true,
                                    'style' => 'margin-left:15px;',
                                    'data-field' => 'odonto6424'
                                ]); ?>
                    </td>
                    <td colspan="2" style="text-align:center;" id="6424">
                        24
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;" id="1555">
                        15
                    </td>
                    <td colspan="3">
                        <?= $form->field($modelFisik, 'odonto15', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control odonto-input',
                                    'disabled' => $isDokter ? false : true,
                                    'style' => 'margin-right:15px;',
                                    'data-field' => 'odonto1555'
                                ]); ?>
                    </td>
                    <td colspan="3">
                        <?= $form->field($modelFisik, 'odonto25', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control odonto-input',
                                    'disabled' => $isDokter ? false : true,
                                    'style' => 'margin-left:15px;',
                                    'data-field' => 'odonto6525'
                                ]); ?>
                    </td>
                    <td colspan="2" style="text-align:center;" id="6525">
                        25
                    </td>
                </tr>
                <tr class="notAnak">
                    <td colspan="2" style="text-align:center;">
                        16
                    </td>
                    <td colspan="3">
                        <?= $form->field($modelFisik, 'odonto16', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control odonto-input',
                                    'disabled' => $isDokter ? false : true,
                                    'style' => 'margin-right:15px;',
                                    'data-field' => 'odonto16'
                                ]); ?>
                    </td>
                    <td colspan="3">
                        <?= $form->field($modelFisik, 'odonto26', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control odonto-input',
                                    'disabled' => $isDokter ? false : true,
                                    'style' => 'margin-left:15px;',
                                    'data-field' => 'odonto26'
                                ]); ?>
                    </td>
                    <td colspan="2" style="text-align:center;">
                        26
                    </td>
                </tr>
                <tr class="notAnak">
                    <td colspan="2" style="text-align:center;">
                        17
                    </td>
                    <td colspan="3">
                        <?= $form->field($modelFisik, 'odonto17', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control odonto-input',
                                    'disabled' => $isDokter ? false : true,
                                    'style' => 'margin-right:15px;',
                                    'data-field' => 'odonto17'
                                ]); ?>
                    </td>
                    <td colspan="3">
                        <?= $form->field($modelFisik, 'odonto27', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control odonto-input',
                                    'disabled' => $isDokter ? false : true,
                                    'style' => 'margin-left:15px;',
                                    'data-field' => 'odonto27'
                                ]); ?>
                    </td>
                    <td colspan="2" style="text-align:center;">
                        27
                    </td>
                </tr>
                <tr class="notAnak">
                    <td colspan="2" style="text-align:center;">
                        18
                    </td>
                    <td colspan="3">
                        <?= $form->field($modelFisik, 'odonto18', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control odonto-input',
                                    'disabled' => $isDokter ? false : true,
                                    'style' => 'margin-right:15px;',
                                    'data-field' => 'odonto18'
                                ]); ?>
                    </td>
                    <td colspan="3">
                        <?= $form->field($modelFisik, 'odonto28', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control odonto-input',
                                    'disabled' => $isDokter ? false : true,
                                    'style' => 'margin-left:15px;',
                                    'data-field' => 'odonto28'
                                ]); ?>
                    </td>
                    <td colspan="2" style="text-align:center;">
                        28
                    </td>
                </tr>
                <tr>
                    <td colspan="10">
                        <?php
                        echo Html::img('@web/media/img/img-pemeriksaan/odontogram.png', [
                            'class' => 'img-responsive',
                            'style' => 'width:50%; height:auto; margin-left:auto; margin-right:auto;'
                        ]);
                        ?>
                    </td>
                </tr>
                <tr class="notAnak">
                    <td colspan="2" style="text-align:center;">
                        48
                    </td>
                    <td colspan="3">
                        <?= $form->field($modelFisik, 'odonto48', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control odonto-input',
                                    'disabled' => $isDokter ? false : true,
                                    'style' => 'margin-right:15px;',
                                    'data-field' => 'odonto48'
                                ]); ?>
                    </td>
                    <td colspan="3">
                        <?= $form->field($modelFisik, 'odonto38', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control odonto-input',
                                    'disabled' => $isDokter ? false : true,
                                    'style' => 'margin-left:15px;',
                                    'data-field' => 'odonto38'
                                ]); ?>
                    </td>
                    <td colspan="2" style="text-align:center;">
                        38
                    </td>
                </tr>
                <tr class="notAnak">
                    <td colspan="2" style="text-align:center;">
                        47
                    </td>
                    <td colspan="3">
                        <?= $form->field($modelFisik, 'odonto47', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control odonto-input',
                                    'disabled' => $isDokter ? false : true,
                                    'style' => 'margin-right:15px;',
                                    'data-field' => 'odonto47'
                                ]); ?>
                    </td>
                    <td colspan="3">
                        <?= $form->field($modelFisik, 'odonto37', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control odonto-input',
                                    'disabled' => $isDokter ? false : true,
                                    'style' => 'margin-left:15px;',
                                    'data-field' => 'odonto37'
                                ]); ?>
                    </td>
                    <td colspan="2" style="text-align:center;">
                        37
                    </td>
                </tr>
                <tr class="notAnak">
                    <td colspan="2" style="text-align:center;">
                        46
                    </td>
                    <td colspan="3">
                        <?= $form->field($modelFisik, 'odonto46', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control odonto-input',
                                    'disabled' => $isDokter ? false : true,
                                    'style' => 'margin-right:15px;',
                                    'data-field' => 'odonto46'
                                ]); ?>
                    </td>
                    <td colspan="3">
                        <?= $form->field($modelFisik, 'odonto36', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control odonto-input',
                                    'disabled' => $isDokter ? false : true,
                                    'style' => 'margin-left:15px;',
                                    'data-field' => 'odonto36'
                                ]); ?>
                    </td>
                    <td colspan="2" style="text-align:center;">
                        36
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;" id="4585">
                        45
                    </td>
                    <td colspan="3">
                        <?= $form->field($modelFisik, 'odonto45', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control odonto-input',
                                    'disabled' => $isDokter ? false : true,
                                    'style' => 'margin-right:15px;',
                                    'data-field' => 'odonto4585'
                                ]); ?>
                    </td>
                    <td colspan="3">
                        <?= $form->field($modelFisik, 'odonto35', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control odonto-input',
                                    'disabled' => $isDokter ? false : true,
                                    'style' => 'margin-left:15px;',
                                    'data-field' => 'odonto7535'
                                ]); ?>
                    </td>
                    <td colspan="2" style="text-align:center;" id="7535">
                        35
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;" id="4484">
                        44
                    </td>
                    <td colspan="3">
                        <?= $form->field($modelFisik, 'odonto44', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control odonto-input',
                                    'disabled' => $isDokter ? false : true,
                                    'style' => 'margin-right:15px;',
                                    'data-field' => 'odonto4484'
                                ]); ?>
                    </td>
                    <td colspan="3">
                        <?= $form->field($modelFisik, 'odonto34', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control odonto-input',
                                    'disabled' => $isDokter ? false : true,
                                    'style' => 'margin-left:15px;',
                                    'data-field' => 'odonto7434'
                                ]); ?>
                    </td>
                    <td colspan="2" style="text-align:center;" id="7434">
                        34
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;" id="4383">
                        43
                    </td>
                    <td colspan="3">
                        <?= $form->field($modelFisik, 'odonto43', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control odonto-input',
                                    'disabled' => $isDokter ? false : true,
                                    'style' => 'margin-right:15px;',
                                    'data-field' => 'odonto4383'
                                ]); ?>
                    </td>
                    <td colspan="3">
                        <?= $form->field($modelFisik, 'odonto33', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control odonto-input',
                                    'disabled' => $isDokter ? false : true,
                                    'style' => 'margin-left:15px;',
                                    'data-field' => 'odonto7333'
                                ]); ?>
                    </td>
                    <td colspan="2" style="text-align:center;" id="7333">
                        33
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;" id="4282">
                        42
                    </td>
                    <td colspan="3">
                        <?= $form->field($modelFisik, 'odonto42', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control odonto-input',
                                    'disabled' => $isDokter ? false : true,
                                    'style' => 'margin-right:15px;',
                                    'data-field' => 'odonto4282'
                                ]); ?>
                    </td>
                    <td colspan="3">
                        <?= $form->field($modelFisik, 'odonto32', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control odonto-input',
                                    'disabled' => $isDokter ? false : true,
                                    'style' => 'margin-left:15px;',
                                    'data-field' => 'odonto7232'
                                ]); ?>
                    </td>
                    <td colspan="2" style="text-align:center;" id="7232">
                        32
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;" id="4181">
                        41
                    </td>
                    <td colspan="3">
                        <?= $form->field($modelFisik, 'odonto41', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control odonto-input',
                                    'disabled' => $isDokter ? false : true,
                                    'style' => 'margin-right:15px;',
                                    'data-field' => 'odonto4181'
                                ]); ?>
                    </td>
                    <td colspan="3">
                        <?= $form->field($modelFisik, 'odonto31', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)->textInput([
                                    'class' => 'form-control odonto-input',
                                    'disabled' => $isDokter ? false : true,
                                    'style' => 'margin-left:15px;',
                                    'data-field' => 'odonto7131'
                                ]); ?>
                    </td>
                    <td colspan="2" style="text-align:center;" id="7131">
                        31
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="help-block" style="margin-bottom:50px;"></div>

    <div class="col-sm-8">
        <?= $form->field($modelFisik, 'occulasi', [
            'labelOptions' => ['class' => '']
        ])->dropDownList(
                [
                    'normalbite' => 'Normal Bite',
                    'crossbite' => 'Cross Bite',
                    'stepbite' => 'Step Bite',
                ],
                [
                    'class' => 'form-control select2 input-sm',
                    'prompt' => '— Jenis Occulasi —'
                ]
            ); ?>
    </div>

    <div class="help-block"></div>

    <div class="col-sm-8">
        <?= $form->field($modelFisik, 'torusPlatinus', [
            'labelOptions' => ['class' => '']
        ])->dropDownList(
                [
                    'tidak' => 'Tidak Ada',
                    'kecil' => 'Kecil',
                    'sedang' => 'Sedang',
                    'besar' => 'Besar',
                    'multiple' => 'Multiple',
                ],
                [
                    'class' => 'form-control select2 input-sm',
                    'prompt' => '— Jenis Torus Platinus —'
                ]
            ); ?>
    </div>

    <div class="col-sm-8">

        <?= $form->field($modelFisik, 'torusMandibularis', [
            'labelOptions' => ['class' => '']
        ])->dropDownList(
                [
                    'tidak' => 'Tidak Ada',
                    'sisikiri' => 'Sisi Kiri',
                    'sisikanan' => 'Sisi Kanan',
                    'keduasisi' => 'Kedua Sisi',
                ],
                [
                    'class' => 'form-control select2 input-sm',
                    'prompt' => '— Jenis Torus Mandibularis —'
                ]
            ); ?>
    </div>

    <div class="col-sm-8">
        <?= $form->field($modelFisik, 'palatum', [
            'labelOptions' => ['class' => '']
        ])->dropDownList(
                [
                    'dalam' => 'Dalam',
                    'sedang' => 'Sedang',
                    'rendah' => 'Rendah',
                ],
                [
                    'class' => 'form-control select2 input-sm',
                    'prompt' => '— Jenis Palatum —'
                ]
            ); ?>
    </div>


    <div class="col-sm-8">
        <?= $form->field($modelFisik, 'diastema', [
            'labelOptions' => ['class' => '']
        ])->dropDownList(
                [
                    'tidak' => 'Tidak Ada',
                    'ada' => 'Ada',
                ],
                [
                    'class' => 'form-control select2 input-sm',
                    'prompt' => '— Apakah ada Diastema? —',
                    'id' => 'diastema-dropdown',
                ]
            ); ?>
    </div>

    <div id="diastema-textinput" class="col-sm-4">
        <?= $form->field($modelFisik, 'keteranganDiastema', [
            'labelOptions' => ['class' => '']
        ])->textInput([
                    'id' => 'keteranganDiastema',
                    'class' => 'form-control',
                    'disabled' => $isDokter ? false : true,
                    'style' => 'margin-right:15px; width:400px;',
                    'placeholder' => 'Masukkan detail diastema',
                ])->label(false); ?>
    </div>


    <div class="col-sm-8">
        <?= $form->field($modelFisik, 'gigiAnomali', [
            'labelOptions' => ['class' => '']
        ])->dropDownList(
                [
                    'tidak' => 'Tidak Ada',
                    'ada' => 'Ada',
                ],
                [
                    'class' => 'form-control select2 input-sm',
                    'prompt' => '— Apakah ada Gigi Anomali? —'
                ]
            ); ?>
    </div>

    <div class="col-sm-8">
        <?= $form->field($modelFisik, 'lainlain', [
            'labelOptions' => ['class' => '']
        ])->textInput([
                    'class' => 'form-control',
                    'disabled' => $isDokter ? false : true,
                    'style' => 'margin-right:15px; width:400px;',
                ]); ?>
    </div>

    <div class="col-sm-8">
        <?= $form->field($modelFisik, 'd', [
            'labelOptions' => ['class' => '']
        ])->textInput([
                    'class' => 'form-control',
                    'disabled' => $isDokter ? false : true,
                    'style' => 'margin-right:15px; width:400px;',
                ]); ?>
    </div>

    <div class="col-sm-8">
        <?= $form->field($modelFisik, 'm', [
            'labelOptions' => ['class' => '']
        ])->textInput([
                    'class' => 'form-control',
                    'disabled' => $isDokter ? false : true,
                    'style' => 'margin-right:15px; width:400px;',
                ]); ?>
    </div>
    <div class="col-sm-8">
        <?= $form->field($modelFisik, 'f', [
            'labelOptions' => ['class' => '']
        ])->textInput([
                    'class' => 'form-control',
                    'disabled' => $isDokter ? false : true,
                    'style' => 'margin-right:15px; width:400px;',
                ]); ?>
    </div>

    <div class="col-sm-8">
        <?= $form->field($modelFisik, 'jumlahPhoto', [
            'labelOptions' => ['class' => '']
        ])->textInput([
                    'class' => 'form-control',
                    'disabled' => $isDokter ? false : true,
                    'style' => 'margin-right:15px; width:400px;',
                ]) ?>
    </div>

    <div class="col-sm-4" style="translate:-100px;">
        <?= $form->field($modelFisik, 'jenisPhoto', [
            'labelOptions' => ['class' => '']
        ])->label(false)->dropDownList(
                [
                    'digital' => 'Digital',
                    'intraoral' => 'Intraoral',
                ],
                [
                    'class' => 'form-control select2 input-sm',
                    'prompt' => '— Jenis Photo —',
                ]
            ); ?>
    </div>

    <div class="col-sm-8">
        <?= $form->field($modelFisik, 'jumlahPhotoRontgen', [
            'labelOptions' => ['class' => '']
        ])->textInput([
                    'class' => 'form-control',
                    'disabled' => $isDokter ? false : true,
                    'style' => 'margin-right:15px; width:400px;',

                ]) ?>
    </div>

    <div class="col-sm-4" style="translate:-100px;">
        <?= $form->field($modelFisik, 'jenisPhotoRontgen', [
            'labelOptions' => ['class' => '']
        ])->label(false)->dropDownList(
                [
                    'dental' => 'Dental',
                    'pa' => 'PA',
                    'opg' => 'OPG',
                    'ceph' => 'CEPH',
                ],
                [
                    'class' => 'form-control select2 input-sm',
                    'prompt' => '— Jenis Photo Rontgen —',
                ]
            ); ?>
    </div>

</div>
<?php ActiveForm::end(); ?>

<?php
$jsonBagianTubuh = json_encode(ArrayHelper::map($optBagianTubuh, 'bagiantubuh_id', 'namabagtubuh'));
$this->registerJs('
    var data_bmi = ' . $data_bmi . ';

    var data_tekanandarah = ' . $data_tekanandarah . ';

    var jeniskelamin = "' . $jeniskelamin . '";

    var umur = ' . $umur . ';

    var bagianTubuh = ' . $jsonBagianTubuh . ';
    var tmpData = ' . $dataAnatomi . ';
    var counter = ' . $counter . ';
    var dataGcs = ' . json_encode($data_gcs) . ';
    var is_draft = ' . $is_draft . ';
    var pendaftaranId = "' . $pendaftaran_id . '";
    var _model = "' . $modelName . '";
    var _modelName = _model.toLowerCase();
    var pemeriksaanfisik_id = "' . $modelFisik->pemeriksaanfisik_id . '";

    var displayAngka1151 = document.getElementById("1151");
    var displayAngka6121 = document.getElementById("6121");
    var displayAngka1252 = document.getElementById("1252");
    var displayAngka6222 = document.getElementById("6222");
    var displayAngka1353 = document.getElementById("1353");
    var displayAngka6323 = document.getElementById("6323");
    var displayAngka1454 = document.getElementById("1454");
    var displayAngka6424 = document.getElementById("6424");
    var displayAngka1555 = document.getElementById("1555");
    var displayAngka6525 = document.getElementById("6525");

    var displayAngka4585 = document.getElementById("4585");
    var displayAngka7535 = document.getElementById("7535");
    var displayAngka4484 = document.getElementById("4484");
    var displayAngka7434 = document.getElementById("7434");
    var displayAngka4383 = document.getElementById("4383");
    var displayAngka7333 = document.getElementById("7333");
    var displayAngka4282 = document.getElementById("4282");
    var displayAngka7232 = document.getElementById("7232");
    var displayAngka4181 = document.getElementById("4181");
    var displayAngka7131 = document.getElementById("7131");

    var dataAnak = {};
    var dataDewasa = {};
    var previousCategory = "";

    function updateAngkaKategori(data) {
        displayAngka1151.innerText = data[0];
        displayAngka6121.innerText = data[1];
        displayAngka6121.style.paddingLeft = "7px";
        displayAngka1252.innerText = data[2];
        displayAngka6222.innerText = data[3];
        displayAngka6222.style.paddingLeft = "7px";
        displayAngka1353.innerText = data[4];
        displayAngka6323.innerText = data[5];
        displayAngka6323.style.paddingLeft = "7px";
        displayAngka1454.innerText = data[6];
        displayAngka6424.innerText = data[7];
        displayAngka6424.style.paddingLeft = "7px";
        displayAngka1555.innerText = data[8];
        displayAngka6525.innerText = data[9];
        displayAngka6525.style.paddingLeft = "7px";
        
        displayAngka4585.innerText = data[10];
        displayAngka7535.innerText = data[11];
        displayAngka7535.style.paddingLeft = "7px";
        displayAngka4484.innerText = data[12];
        displayAngka7434.innerText = data[13];
        displayAngka7434.style.paddingLeft = "7px";
        displayAngka4383.innerText = data[14];
        displayAngka7333.innerText = data[15];
        displayAngka7333.style.paddingLeft = "7px";
        displayAngka4282.innerText = data[16];
        displayAngka7232.innerText = data[17];
        displayAngka7232.style.paddingLeft = "7px";
        displayAngka4181.innerText = data[18];
        displayAngka7131.innerText = data[19];
        displayAngka7131.style.paddingLeft = "7px";
    }

    $(document).ready(function () {
        var loadKategoriPasien = document.getElementById("kategoriPasien").value;

        if ($("#diastema-dropdown").val() === "ada") {
            $("#keteranganDiastema").prop("disabled", false);
        } else {
            $("#keteranganDiastema").prop("disabled", true);
            $("#keteranganDiastema").val("");
        }

        if (loadKategoriPasien === "anak") {
            updateAngkaKategori([
                "51", "61", "52", "62", "53", "63", "54", "64", "55", "65",
                "85", "75", "84", "74", "83", "73", "82", "72", "81", "71"
            ]);
            document.querySelectorAll(".notAnak").forEach(function(element) {
                element.style.display = "none";
            });
            $(".odonto-input").each(function() {
                const field = $(this).data("field");
                dataAnak[field] = $(this).val();
            });
        } else if (loadKategoriPasien === "dewasa") {
            updateAngkaKategori([
                "11", "21", "12", "22", "13", "23", "14", "24", "15", "25",
                "45", "35", "44", "34", "43", "33", "42", "32", "41", "31"
            ]);
            document.querySelectorAll(".notAnak").forEach(function(element) {
                element.style.display = "table-row";
            });
            const field = $(this).data("field");
            dataDewasa[field] = $(this).val();
        }

        $("#diastema-dropdown").on("change", function() {
            if ($(this).val() === "ada") {
                $("#keteranganDiastema").prop("disabled", false);
            } else {
                $("#keteranganDiastema").prop("disabled", true);
                $("#keteranganDiastema").val("");
            }
        });        
    
        $("#kategoriPasien").on("change", function() {
            const selectedValue = this.value;
            const currentCategory = $("#kategoriPasien").val();

            $(".odonto-input").each(function() {
                const field = $(this).data("field");
                if (currentCategory === "anak") {
                    dataDewasa[field] = $(this).val();
                } else if (currentCategory === "dewasa") {
                    dataAnak[field] = $(this).val();
                }
            });
    
            $(".odonto-input").val("");
    
            // Isi kembali dengan data sesuai kategori yang dipilih
            $(".odonto-input").each(function() {
                const field = $(this).data("field");
                if (selectedValue === "anak") {
                    $(this).val(dataAnak[field] || "");
                } else if (selectedValue === "dewasa") {
                    $(this).val(dataDewasa[field] || "");
                }
            });
            
            if (selectedValue === "anak") {
                updateAngkaKategori([
                    "51", "61", "52", "62", "53", "63", "54", "64", "55", "65",
                    "85", "75", "84", "74", "83", "73", "82", "72", "81", "71"
                ]);
                document.querySelectorAll(".notAnak").forEach(function(element) {
                    element.style.display = "none";
                });
            } else if (selectedValue === "dewasa") {
                updateAngkaKategori([
                    "11", "21", "12", "22", "13", "23", "14", "24", "15", "25",
                    "45", "35", "44", "34", "43", "33", "42", "32", "41", "31"
                ]);
                document.querySelectorAll(".notAnak").forEach(function(element) {
                    element.style.display = "table-row";
                });
            } else {
                updateAngkaKategori([
                    "11 [51]", "[61] 21", "12 [52]", "[62] 22", "13 [53]", "[63] 23", 
                    "14 [54]", "[64] 24", "15 [55]", "[65] 25",
                    "45 [85]", "[75] 35", "44 [84]", "[74] 34", "43 [83]", "[73] 33", 
                    "42 [82]", "[72] 32", "41 [81]", "[71] 31"
                ]);
                document.querySelectorAll(".notAnak").forEach(function(element) {
                    element.style.display = "table-row";
                });
            }
        });
    });
    ', View::POS_END);
$this->registerJs($this->render('js/__fisik.js'), View::POS_END);
?>
