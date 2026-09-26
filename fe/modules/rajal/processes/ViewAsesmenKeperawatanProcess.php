<?php

use app\components\DHtml;
use yii\web\View;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use app\components\DocoHelpers;
use kartik\datetime\DateTimePicker;
use kartik\widgets\DatePicker;
echo "<pre>";var_dump($_GET);die();
?>

<style>
    .datepicker>div {
        display: block;
    }

    .form-row {
        margin-bottom: 4px;
    }

    .panel-heading {
        padding: 3px 20px !important;
    }

    hr {
        margin-top: 3px;
        margin-bottom: 3px;
    }

    .panel {
        margin-bottom: 10px;
    }

    .box-scale {
        margin-top: 10px;
        padding-top: 10px;
        padding-bottom: 10px;
    }

    p {
        margin-bottom: 3px;
    }

    td {
        padding-top: 0px !important;
    }

    .form-horizontal .control-label {
        padding-bottom: 3px;
        padding-top: 3px !important;
    }

    .box-scale-header {
        margin-bottom: 3px !important;
    }
</style>


<div class="panel panel-body">
    <div class="panel panel-default panel-shadow">
        <div class="panel-heading">
            <h5 class="panel-title"><?= Yii::t('fe', 'Asesmen') ?></h5>
            <div class="heading-elements">
                <a data-action="collapse" id="collapseClickFormAnamnesa">
                    <i class="morefilterForm fa fa-chevron-down"></i>
                </a>
            </div>
        </div>
        <div class="panel-toolbar clearfix">
            <?= DocoHelpers::generateToolbar([
                'custom-save' => [
                    'title' => \Yii::t('fe', 'Simpan'),
                    'icon' => 'fa fa-save',
                    'attributes' => [
                        'id' => 'submit-anamnesa',
                        'data-options' => 'click'
                    ]
                ],
                // 'reset' => ['attributes' => ['data-parent' => '#form-asesmen', 'id' => 'reset']],
                'custom-print' => [
                    'title' => Yii::t('fe', 'Cetak'),
                    'icon' => 'fa fa-print',
                    'attributes' => [
                        'data-options' => 'click',
                        'id' => 'btn-print-asesmen-perawat',
                        'disabled' => !empty($askep_id) ? false : true,
                    ],
                ],
            ], ''); ?>
        </div>
        <nav class="navbar navbar-default">
            <ul class="nav nav-tabs">
                <li <?php if ($status == 0) {
                        echo "class='active'";
                    } ?> id="tab-awal">
                    <a href="#view-anamnesa" data-toggle="tab" aria-expanded="true">Asesmen Awal</a>
                </li>
                <li <?php if ($status == 1) {
                        echo "class='active'";
                    } ?> id="tab-ulang">
                    <a href="#view-anamnesa" data-toggle="tab" aria-expanded="true">Asesmen Ulang</a>
                </li>
            </ul>
        </nav>
        <div class="panel-body panel-body-collapse-form-asesmen">
            <div class="row">
                <div class="form-asesmen" style="padding: 12px;">
                    <?php
                    $form = ActiveForm::begin([
                        'id' => 'form-asesmen',
                        'enableAjaxValidation' => false,
                        'enableClientValidation' => false,
                        'type' => ActiveForm::TYPE_HORIZONTAL,
                        'formConfig' => [
                            'labelSpan' => 4,
                            'deviceSize' => ActiveForm::SIZE_SMALL,
                        ],
                    ]);
                    ?>
                    <div class="row form-row">
                        <div class="col-sm-1">
                            <label for="control-label text-label">Agama</label>
                        </div>
                        <div class="col-sm-1">
                            <label for="control-label text-label">: <?= $pasienData['agama'] ?></label>
                        </div>

                        <div class="col-sm-1">
                            <label for="control-label text-label">Gol Darah</label>
                        </div>
                        <div class="col-sm-1">
                            <label for="control-label text-label">: <?= $pasienData['golongan_darah'] ?></label>
                        </div>

                        <div class="col-sm-1">
                            <label for="control-label text-label">Pendidikan</label>
                        </div>
                        <div class="col-sm-1">
                            <label for="control-label text-label">: <?= $pasienData['pendidikan_nama'] ?></label>
                        </div>
                    </div>
                    <hr>
                    <div class="row form-row">
                        <!--
                            <div class="col-sm-2">
                                <label for="control-label text-label">Tanggal Anamesa 
                            <?= $model->tgl_anamnesis ?> <span style="color:red; "><sup>*</sup></span></label>
                            </div>
                            -->
                        <div class="col-lg-6 form-group">
                            <?= $form->field($model, 'tgl_anamnesis', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-5'
                                ]
                            ])->widget(DatePicker::classname(), [
                                'name' => 'tgl_anamnesis',
                                'readonly' => true,
                                'language' => 'en',
                                'value' => date('dd/mm/yyyy'),
                                'pluginOptions' => [
                                    'autoclose' => true,
                                    'format' => 'dd/mm/yyyy',
                                    'endDate' => "0d",
                                ]
                            ])->label(Yii::t('fe', 'Tanggal Anamesa')); ?>

                        </div>
                    </div>

                    <div class="row form-row">
                        <div class="col-lg-12 form-group">
                            <div class="col-sm-2">
                                <label for="control-label text-label">Sumber Data</label>
                            </div>
                            <?php
                            foreach ($configVal['sumber_data'] as $keySumberData => $sumberData) :
                            ?>
                                <div class="col-sm-1">
                                    <?=
                                    Html::activeRadio($model, 'sumber_data', [
                                        'value' => $keySumberData,
                                        'id' => 'sumber_data-' . $keySumberData,
                                        'label' => '<span class="">' . $sumberData . '</span>',
                                        'data-fieldname' => 'sumber_data',
                                        'inline' => true
                                    ]);
                                    ?>
                                </div>
                            <?php
                            endforeach;
                            ?>
                            <div class="col-sm-3">
                                <?=
                                Html::activeTextInput($model, 'sumber_data', [
                                    'class' => 'form-control default-disabled',
                                    'id' => 'other-sumber_data'
                                ]);
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="row form-row">
                        <div class="col-lg-12 form-group">
                            <div class="col-sm-2">
                                <label for="control-label text-label">Rujukan</label>
                            </div>

                            <?=
                            DHtml::trueFalseRadio($model, 'rujukan', [
                                'childDependent' => [
                                    'class' => 'rujukan--dependent',
                                    'id' => 'diagnosa_rujukan-form'
                                ]
                            ]);
                            ?>

                            <?php
                            foreach ($configVal['tujuan_rujukan'] as $keyTujuan => $tujuanRujukan) :
                                if ($keyTujuan == 'rs') :
                            ?>
                                    <div class="col-sm-1">
                                        <?=
                                        Html::activeRadio($model, 'tujuan_rujukan', [
                                            'value' => $keyTujuan,
                                            'id' => 'rujukan-' . $keyTujuan,
                                            'class' => 'rujukan--dependent default-disabled',
                                            'data-dependent' => json_encode([
                                                'id' => 'other-rujukan_rs',
                                                'onValue' => 'rs'
                                            ]),
                                            'label' => '<span class="">' . $tujuanRujukan . '</span>',
                                            'inline' => true
                                        ]);
                                        ?>
                                    </div>
                                    <div class="col-sm-3">
                                        <?=
                                        Html::activeTextInput($model, 'rujukan_rs', [
                                            'class' => 'form-control default-disabled',
                                            'id' => 'other-rujukan_rs'
                                        ]);
                                        ?>
                                    </div>
                                <?php
                                else :
                                ?>
                                    <div class="col-sm-1" style="width: 10.499999995%">
                                        <?=
                                        Html::activeRadio($model, 'tujuan_rujukan', [
                                            'value' => $keyTujuan,
                                            'id' => 'rujukan-' . $keyTujuan,
                                            'class' => 'rujukan--dependent default-disabled',
                                            'data-dependent' => json_encode([
                                                'id' => 'other-rujukan_rs',
                                                'onValue' => 'rs'
                                            ]),
                                            'label' => '<span class="">' . $tujuanRujukan . '</span>',
                                            'inline' => true
                                        ]);
                                        ?>
                                    </div>
                            <?php
                                endif;
                            endforeach;
                            ?>
                        </div>
                    </div>
                    <div class="row form-row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'diagnosa_rujukan', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-6'
                                ]
                            ])->textInput(['id' => 'diagnosa_rujukan-form']) ?>
                        </div>
                    </div>
                    <hr>
                    <div class="row form-row">
                        <div class="col-sm-5" style="width: 38%">
                            <?= $form->field($model, 'pegawaidokter_id')->dropdownList([], ['id' => 'pegawaidokter_id-form']); ?>
                        </div>
                        <div class="col-sm-5" style="width: 38%">
                            <?= $form->field($model, 'pegawaiperawat_id')->dropdownList([], ['id' => 'pegawaiperawat_id-form']); ?>
                        </div>
                    </div>
                    <hr>
                    <p class="header-form">Data (Diisi Oleh Perawat)</p>
                    <div class="form-group highlight-addon has-size-sm field-asesmenkeperawatan-keluhan form-vertical">
                        <label class="control-label has-star" for="asesmenkeperawatan-keluhan">
                            <p class="sub-header-form">1. Keluhan Utama <span style="color:red; "><sup>*</sup></span></p>
                        </label>
                        <?=
                        Html::activeTextarea($model, 'keluhan_utama', ['class' => 'form-control']);
                        ?>
                        <div class="help-block">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <p class="sub-header-form">2. Pemeriksaan Fisik</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3" style="width: 24%">
                            <?= $form->field($model, 'berat_badan', [
                                'addon' => [
                                    'append' => [
                                        'content' => 'kg'
                                    ]
                                ]
                            ])->textInput(['class' => 'doco-decimal-wcomma']); ?>
                        </div>
                        <div class="col-sm-3">
                            <?= $form->field($model, 'nadi', ['addon' => ['append' => ['content' => 'x/menit']]])->textInput(); ?>
                        </div>
                        <div class="col-sm-3">
                            <?= $form->field($model, 'td', ['addon' => ['append' => ['content' => 'mmHg']]])->textInput(); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4" style="width: 24%">
                            <?= $form->field($model, 'tinggi_badan', ['addon' => ['append' => ['content' => 'cm']]])->textInput(['class' => 'doco-decimal-wcomma']); ?>
                        </div>

                        <div class="col-sm-3">
                            <?= $form->field($model, 'rr', ['addon' => ['append' => ['content' => 'x/menit']]])->textInput(['class' => 'doco-number']); ?>
                        </div>
                        <div class="col-sm-3">
                            <?= $form->field($model, 'suhu', ['addon' => ['append' => ['content' => '°C']]])->textInput(['class' => 'doco-decimal-wcomma']); ?>
                        </div>
                    </div>
                    <p class="sub-header-form">3. Riwayat Kesehatan Dahulu</p>
                    <p class="header-form">A. Riwayat penyakit dahulu</p>
                    <div class="row">
                        <?=
                        DHtml::trueFalseRadio($model, 'riwayat_penyakit', [
                            'childDependent' => [
                                'id' => 'riwayat_penyakit_nama-form'
                            ]
                        ]);
                        ?>
                        <div class="col-sm-3">
                            <?=
                            Html::activeTextInput($model, 'riwayat_penyakit_nama', [
                                'class' => 'form-control default-disabled',
                                'id' => 'riwayat_penyakit_nama-form'
                            ]);
                            ?>
                        </div>
                    </div>
                    <p class="header-form">
                        - Pernah Dirawat
                    </p>
                    <div class="row">
                        <?=
                        DHtml::trueFalseRadio($model, 'dirawat', [
                            'childDependent' => [
                                'class' => 'dependent-dirawat'
                            ]
                        ]);
                        ?>
                        <div class="col-sm-3">
                            <?=
                            $form->field($model, 'dirawat_diagnosa')->textInput(['class' => 'dependent-dirawat default-disabled'])->label('Diagnosa', ['class' => 'control-label has-star col-sm-3']);
                            ?>
                        </div>
                        <div class="col-sm-3">
                            <?=
                            $form->field($model, 'dirawat_waktu')->textInput(['class' => 'dependent-dirawat default-disabled'])->label('Kapan', ['class' => 'control-label has-star col-sm-2']);
                            ?>
                        </div>
                        <div class="col-sm-3">
                            <?=
                            $form->field($model, 'dirawat_tempat')->textInput(['class' => 'dependent-dirawat default-disabled'])->label('Di', ['class' => 'control-label has-star col-sm-1']);
                            ?>
                        </div>
                    </div>
                    <p class="header-form">
                        - Pernah Dioperasi
                    </p>
                    <div class="row">
                        <?=
                        DHtml::trueFalseRadio($model, 'dioperasi', [
                            'childDependent' => [
                                'class' => 'dependent-dioperasi'
                            ]
                        ]);
                        ?>
                        <div class="col-sm-3">
                            <?=
                            $form->field($model, 'dioperasi_diagnosa')->textInput(['class' => 'dependent-dioperasi default-disabled'])->label('Jenis Operasi', ['class' => 'control-label has-star col-sm-4']);
                            ?>
                        </div>
                        <div class="col-sm-3">
                            <?=
                            $form->field($model, 'dioperasi_waktu')->textInput(['class' => 'dependent-dioperasi default-disabled'])->label('Kapan', ['class' => 'control-label has-star col-sm-2']);
                            ?>
                        </div>
                    </div>
                    <p class="header-form">
                        - Obat-obatan yang dikonsumsi
                    </p>
                    <div class="row">
                        <?=
                        DHtml::trueFalseRadio($model, 'obat_dikonsumsi', [
                            'childDependent' => [
                                'id' => 'obat_dikonsumsi_nama-form'
                            ]
                        ]);
                        ?>
                        <div class="col-sm-3">
                            <?=
                            $form->field($model, 'obat_dikonsumsi_nama')->textInput(['id' => 'obat_dikonsumsi_nama-form', 'class' => 'default-disabled input-tag'])->label('Obat', ['class' => 'control-label has-star col-sm-2']);
                            ?>
                        </div>
                    </div>
                    <p class="header-form">B. Riwayat penyakit keluarga</p>
                    <div class="row">
                        <?= DHtml::trueFalseRadio($model, 'riwayat_penyakit_keluarga', [
                            'childDependent' => [
                                'class' => 'riwayat_penyakit_keluarga--dependent'
                            ]
                        ]); ?>
                        <?= DHtml::multipleCheckbox([
                            'model' => $model,
                            'fieldName' => 'riwayat_penyakit_keluarga_list',
                            'class' => 'riwayat_penyakit_keluarga--dependent default-disabled',
                            'data' => $configVal['riwayat_penyakit_keluarga_list']
                        ]);
                        ?>
                    </div>
                    <p class="header-form">C. Ketergantungan terhadap</p>
                    <div class="row">
                        <?= DHtml::trueFalseRadio($model, 'ketergantungan', [
                            'childDependent' => [
                                'class' => 'ketergantungan--dependent',
                                'multiple' => true
                            ]
                        ]); ?>
                        <?= DHtml::multipleCheckbox([
                            'model' => $model,
                            'fieldName' => 'ketergantungan_jenis',
                            'class' => 'ketergantungan--dependent default-disabled',
                            'data' => $configVal['ketergantungan_jenis']
                        ]);
                        ?>
                    </div>
                    <p class="header-form">D. Riwayat pekerjaan (apakah berhubungan dengan zat-zat berbahaya?)</p>
                    <div class="row">
                        <?=
                        DHtml::trueFalseRadio($model, 'riwayat_pekerjaan', [
                            'childDependent' => [
                                'class' => 'riwayat_pekerjaan--dependent'
                            ]
                        ]);
                        ?>
                        <div class="col-sm-3">
                            <?=
                            $form->field($model, 'riwayat_pekerjaan_nama')->textInput(['class' => 'riwayat_pekerjaan--dependent default-disabled'])->label('Sebutkan');
                            ?>
                        </div>
                    </div>
                    <p class="header-form">E. Riwayat Alergi</p>
                    <div class="row">
                        <?=
                        DHtml::trueFalseRadio($model, 'alergi', [
                            'childDependent' => [
                                'id' => 'reaksi_alergi-form',
                                'class' => 'alergi-check'
                            ]
                        ]);
                        ?>
                        <div class="col-sm-1">
                            <input type="checkbox" disabled name="alergi_obat_check" class="alergi-check" data-type="obat"> Obat
                        </div>
                        <div class="col-sm-2">
                            <?=
                            Html::activeTextInput($model, 'alergi_obat', [
                                'class' => 'form-control default-disabled input-tag',
                            ]);
                            ?>
                        </div>
                        <div class="col-sm-1">
                            <input type="checkbox" disabled name="alergi_makanan_check" class="alergi-check" data-type="makanan"> Makanan
                        </div>
                        <div class="col-sm-2">
                            <?=
                            Html::activeTextInput($model, 'alergi_makanan', [
                                'class' => 'form-control default-disabled input-tag',
                            ]);
                            ?>
                        </div>
                        <div class="col-sm-1">
                            <input type="checkbox" disabled name="alergi_lainnya_check" class="alergi-check" data-type="lainnya"> Lainnya
                        </div>
                        <div class="col-sm-2">
                            <?=
                            Html::activeTextInput($model, 'alergi_lainnya', [
                                'class' => 'form-control default-disabled input-tag'
                            ]);
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <?= $form->field($model, 'reaksi_alergi', [
                                'horizontalCssClasses' => [
                                    'label' => 'col-sm-3',
                                    'wrapper' => 'col-md-8'
                                ]
                            ])->textInput(['id' => 'reaksi_alergi-form', 'class' => 'default-disabled'])->label('Reaksi') ?>
                        </div>
                    </div>
                    <p class="sub-header-form">4. Riwayat Psikososial dan Spiritual</p>
                    <p class="header-form">A. Status psikologi</p>
                    <div class="row">
                        <!-- status_psikologi -->
                        <?= DHtml::multipleCheckbox([
                            'model' => $model,
                            'fieldName' => 'status_psikologi',
                            'data' => $configVal['status_psikologi']
                        ]);
                        ?>
                    </div>
                    <p class="header-form">B. Status Sosial</p>
                    <p class="header-form">Hubungan pasien dan keluarga</p>
                    <div class="row">
                        <div class="col-sm-1">
                            <?= Html::activeRadio($model, 'status_sosial', [
                                'label' => 'Tidak baik',
                                'id' => 'status_sosial-tidak_baik',
                                'value' => '0'
                            ]) ?>
                        </div>
                        <div class="col-sm-1">
                            <?= Html::activeRadio($model, 'status_sosial', [
                                'label' => 'Baik',
                                'id' => 'status_sosial-baik',
                                'value' => '1'
                            ]) ?>
                        </div>
                    </div>
                    <p class="header-form">Kerabat terdekat yang dapat dihubungi</p>
                    <div class="row">
                        <div class="col-sm-3">
                            <?= $form->field($model, 'nama_kerabat_terdekat', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-3',
                                    'wrapper' => 'col-md-9'
                                ]
                            ]) ?>
                        </div>
                        <div class="col-sm-3">
                            <?= $form->field($model, 'hubungan_kerabat_terdekat', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-3',
                                    'wrapper' => 'col-md-9'
                                ]
                            ]) ?>
                        </div>
                        <div class="col-sm-3">
                            <?= $form->field($model, 'kontak_kerabat_terdekat', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-8'
                                ]
                            ])->textInput(['class' => 'phonenumber', 'maxlength' => 13]) ?>
                        </div>
                    </div>
                    <p class="header-form">C. Status ekonomi</p>
                    <div class="row">
                        <?php
                        foreach ($configVal['status_ekonomi'] as $keyStatusEkonomi => $statusEkonomi) :
                        ?>
                            <div class="col-sm-1">
                                <?=
                                Html::activeRadio($model, 'status_ekonomi', [
                                    'value' => $keyStatusEkonomi,
                                    'id' => 'status_ekonomi-' . $keyStatusEkonomi,
                                    'label' => '<span class="">' . $statusEkonomi . '</span>',
                                    'data-fieldname' => 'status_ekonomi',
                                    'inline' => true
                                ]);
                                ?>
                            </div>
                        <?php
                        endforeach;
                        ?>
                        <div class="col-sm-3">
                            <?=
                            Html::activeTextInput($model, 'status_ekonomi', [
                                'class' => 'form-control default-disabled',
                                'id' => 'other-status_ekonomi'
                            ]);
                            ?>
                        </div>
                    </div>
                    <p class="header-form">D. Nilai-nilai</p>
                    <div class="row">
                        <div class="col-sm-10">
                            <?= $form->field($model, 'nilai_kebudayaan', ['horizontalCssClasses' => [
                                'label' => 'col-sm-4',
                                'wrapper' => 'col-md-4'
                            ]])->label('Nilai-nilai budaya dan kepercayaan yang diyakini Pasien')
                            ?>
                        </div>
                    </div>
                    <p class="header-form">E. Kultural</p>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'suku_id', [
                                'horizontalCssClasses' => [
                                    'label' => 'col-sm-2',
                                    'wrapper' => 'col-md-4'
                                ]
                            ])->dropdownList([], ['id' => 'suku_id-form']); ?>
                        </div>
                    </div>
                    <p class="sub-header-form">5. Kebutuhan Komunikasi dan Edukasi</p>
                    <p class="header-form">Kesediaan menerima informasi</p>
                    <div class="row">
                        <?=
                        DHtml::trueFalseRadio($model, 'kesediaan_menerima_informasi');
                        ?>
                    </div>
                    <p class="header-form">Kemampuan Membaca</p>
                    <div class="row">
                        <div class="col-sm-1">
                            <?= Html::activeRadio($model, 'kemampuan_membaca', [
                                'label' => 'Mampu',
                                'id' => 'kemampuan_membaca-mampu',
                                'value' => '0'
                            ]) ?>
                        </div>
                        <div class="col-sm-1">
                            <?= Html::activeRadio($model, 'kemampuan_membaca', [
                                'label' => 'Tidak Mampu',
                                'id' => 'kemampuan_membaca-tidak_mampu',
                                'value' => '1'
                            ]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-10">
                            <?= $form->field($model, 'bahasa', ['horizontalCssClasses' => [
                                'label' => 'col-sm-1',
                                'wrapper' => 'col-md-4'
                            ]])
                                ->label('Bahasa')
                            ?>
                        </div>
                    </div>
                    <p class="header-form">Dibutuhkan penerjemah</p>
                    <div class="row">
                        <?=
                        DHtml::trueFalseRadio($model, 'butuh_penerjemah', [
                            'childDependent' => [
                                'class' => 'penerjemah--dependent'
                            ]
                        ]);
                        ?>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'butuh_penerjemah_nama')->textInput(['class' => 'penerjemah--dependent default-disabled'])->label('Sebutkan') ?>
                        </div>
                        <div class="col-sm-2">Bahasa Isyarat</div>
                        <?=
                        DHtml::trueFalseRadio($model, 'bahasa_isyarat', [
                            'class' => 'penerjemah--dependent default-disabled'
                        ]);
                        ?>
                    </div>
                    <p class="header-form">Terdapat hambatan dalam pembelajaran</p>
                    <div class="row">
                        <?=
                        DHtml::trueFalseRadio($model, 'hambatan', [
                            'childDependent' => [
                                'class' => 'hambatan--dependent'
                            ]
                        ]);
                        ?>
                        <?= DHtml::multipleCheckbox([
                            'model' => $model,
                            'fieldName' => 'jenis_hambatan',
                            'data' => $configVal['jenis_hambatan']['first_row'],
                            'class' => 'hambatan--dependent default-disabled',
                            'colSize' => '2'
                        ]);
                        ?>
                    </div>
                    <div class="row">
                        <div class="col-sm-2">&nbsp;</div>
                        <?= DHtml::multipleCheckbox([
                            'model' => $model,
                            'fieldName' => 'jenis_hambatan',
                            'data' => $configVal['jenis_hambatan']['second_row'],
                            'class' => 'hambatan--dependent default-disabled',
                            'colSize' => '2'
                        ]);
                        ?>
                    </div>
                    <p class="header-form">Kebutuhan edukasi (pilih topik edukasi pada kotak yang tersedia)</p>
                    <div class="row">
                        <?= DHtml::multipleCheckbox([
                            'model' => $model,
                            'fieldName' => 'kebutuhan_edukasi',
                            'data' => $configVal['kebutuhan_edukasi']['first_row'],
                            'colSize' => '2',
                            'childDependent' => [
                                'id' => 'kebutuhan_edukasi_keperawatan-form',
                                'onValue' => 'tindakan_keperawatan'
                            ]
                        ]);
                        ?>
                        <div class="col-sm-2">
                            <?=
                            Html::activeTextInput($model, 'kebutuhan_edukasi_keperawatan', [
                                'class' => 'form-control default-disabled input-tag',
                                'id' => 'kebutuhan_edukasi_keperawatan-form'
                            ]);
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <?= DHtml::multipleCheckbox([
                            'model' => $model,
                            'fieldName' => 'kebutuhan_edukasi',
                            'data' => $configVal['kebutuhan_edukasi']['second_row'],
                            'colSize' => '2'
                        ]);
                        ?>
                    </div>
                    <p class="sub-header-form">6. Resiko Cedera/Jatuh</p>
                    <p class="header-form">A. Perhatikan cara berjalan pasien saat akan duduk di kursi. Apakah pasien tampat tidak seimbang (sempoyongan)</p>
                    <div class="row">
                        <?=
                        DHtml::trueFalseRadio($model, 'resiko_cedera_pertama');
                        ?>
                    </div>
                    <p class="header-form">B. Apakah pasien memegang pinggiran kursi atau meja atau benda lain sebagai penopang saat akan duduk</p>
                    <div class="row">
                        <?=
                        DHtml::trueFalseRadio($model, 'resiko_cedera_kedua');
                        ?>
                    </div>
                    <p class="header-form">Hasil</p>
                    <div class="row">
                        <?=
                        DHtml::multipleRadio([
                            'model' => $model,
                            'fieldName' => 'hasil_resiko',
                            'data' => $configVal['hasil_resiko']
                        ]);
                        ?>
                    </div>
                    <p class="sub-header-form">7. Status Fungsional</p>
                    <div class="row">
                        <label for="" class="col-sm-2">Aktivitas dan mobilisasi</label>
                        <div class="col-sm-1">
                            <?= Html::activeRadio($model, 'aktivitas', [
                                'label' => 'Mandiri',
                                'id' => 'aktivitas-mandiri',
                                'data-dependent' => json_encode([
                                    'id' => 'bantuan_aktivitas-form'
                                ]),
                                'value' => '0'
                            ]) ?>
                        </div>
                        <div class="col-sm-1">
                            <?= Html::activeRadio($model, 'aktivitas', [
                                'label' => 'Perlu Bantuan',
                                'id' => 'aktivitas-bantuan',
                                'data-dependent' => json_encode([
                                    'id' => 'bantuan_aktivitas-form'
                                ]),
                                'value' => '1'
                            ]) ?>
                        </div>
                        <div class="col-sm-2">
                            <?=
                            Html::activeTextInput($model, 'bantuan_aktivitas', [
                                'class' => 'form-control default-disabled',
                                'id' => 'bantuan_aktivitas-form'
                            ]);
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <?= $form->field($model, 'alat_bantu_jalan') ?>
                        </div>
                    </div>
                    <p class="sub-header-form">8. Skala Nyeri</p>
                    <div class="row">
                        <div class="col-sm-1">Nyeri</div>
                        <?=
                        DHtml::trueFalseRadio($model, 'is_nyeri', [
                            'class' => 'is_nyeri-checkbox'
                        ]);
                        ?>
                    </div>
                    <div id="nyeri-wrapper">
                        <div class="row">
                            <div class="col-sm-12" style="padding-bottom:10px;">
                                <div class="box-scale">
                                    <div class="box-scale-header">
                                        <img src="/media/img/all-emote.svg" alt="">
                                    </div>
                                    <div class="box-scale-info row">
                                        <div class="col-sm-4 text-left">Tidak Nyeri</div>
                                        <div class="col-sm-4 text-center">Nyeri Mengganggu</div>
                                        <div class="col-sm-4 text-right">Nyeri Berat</div>
                                    </div>
                                    <div class="box-scale-line box-scale-line__separator">
                                        <div class="box-scale-line__point">&nbsp;</div>
                                        <div class="box-scale-line__point">&nbsp;</div>
                                        <div class="box-scale-line__point">&nbsp;</div>
                                        <div class="box-scale-line__point">&nbsp;</div>
                                        <div class="box-scale-line__point">&nbsp;</div>
                                        <div class="box-scale-line__point">&nbsp;</div>
                                        <div class="box-scale-line__point">&nbsp;</div>
                                        <div class="box-scale-line__point">&nbsp;</div>
                                        <div class="box-scale-line__point">&nbsp;</div>
                                        <div class="box-scale-line__point">&nbsp;</div>
                                        <div class="box-scale-line__point">&nbsp;</div>
                                        <div class="box-scale-line__hidePercentage"></div>
                                    </div>
                                    <div class="box-scale-line">
                                        <div data-percentage="0" class="box-scale-line__point">
                                            <div class="box-scale-line__btn box-scale-line__0">0</div>
                                        </div>
                                        <div data-percentage="10" class="box-scale-line__point">
                                            <div class="box-scale-line__btn box-scale-line__1">1</div>
                                        </div>
                                        <div data-percentage="20" class="box-scale-line__point">
                                            <div class="box-scale-line__btn box-scale-line__2">2</div>
                                        </div>
                                        <div data-percentage="30" class="box-scale-line__point">
                                            <div class="box-scale-line__btn box-scale-line__3">3</div>
                                        </div>
                                        <div data-percentage="40" class="box-scale-line__point">
                                            <div class="box-scale-line__btn box-scale-line__4">4</div>
                                        </div>
                                        <div data-percentage="50" class="box-scale-line__point">
                                            <div class="box-scale-line__btn box-scale-line__5">5</div>
                                        </div>
                                        <div data-percentage="60" class="box-scale-line__point">
                                            <div class="box-scale-line__btn box-scale-line__6">6</div>
                                        </div>
                                        <div data-percentage="70" class="box-scale-line__point">
                                            <div class="box-scale-line__btn box-scale-line__7">7</div>
                                        </div>
                                        <div data-percentage="80" class="box-scale-line__point">
                                            <div class="box-scale-line__btn box-scale-line__8">8</div>
                                        </div>
                                        <div data-percentage="90" class="box-scale-line__point">
                                            <div class="box-scale-line__btn box-scale-line__9">9</div>
                                        </div>
                                        <div data-percentage="100" class="box-scale-line__point">
                                            <div class="box-scale-line__btn box-scale-line__10">10</div>
                                        </div>
                                    </div>
                                    <div class="box-scale-info row">
                                        <div class="col-sm-12 text-center">
                                            <span>1-3 Nyeri ringan, analgetik oral &emsp; &emsp;</span>
                                            <span>4-7 : Nyeri sedang, perlu analgetik injeksi&emsp;&emsp;</span>
                                            <span>8-10 : Nyeri berat, konsul Tim Nyeri&emsp;&emsp;</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-1">
                                <?= Html::activeCheckbox($model, 'nyeri_kronis_pertama', [
                                    'label' => 'Nyeri kronis',
                                    'value' => '1',
                                    'data-dependent' => json_encode([
                                        'class' => 'nyeri_kronis--dependent'
                                    ])
                                ]) ?>
                            </div>
                            <div class="col-sm-3">
                                <?= $form->field($model, 'lokasi_nyeri_kronis_pertama')->textInput(['class' => 'nyeri_kronis--dependent default-disabled']) ?>
                            </div>
                            <div class="col-sm-1">Frekuensi</div>
                            <?= Dhtml::multipleRadio([
                                'model' => $model,
                                'fieldName' => 'frekuensi_nyeri_kronis_pertama',
                                'data' => $configVal['frekuensi_nyeri_kronis'],
                                'class' => 'nyeri_kronis--dependent default-disabled'
                            ]) ?>
                            <div class="col-sm-3">
                                <?= $form->field($model, 'durasi_nyeri_kronis_pertama')->textInput(['class' => 'nyeri_kronis--dependent default-disabled']) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-1">
                                <?= Html::activeCheckbox($model, 'nyeri_kronis_kedua', [
                                    'label' => 'Nyeri akut',
                                    'value' => '1',
                                    'data-dependent' => json_encode([
                                        'class' => 'nyeri_kronis_second--dependent'
                                    ])
                                ]) ?>
                            </div>
                            <div class="col-sm-3">
                                <?= $form->field($model, 'lokasi_nyeri_kronis_kedua')->textInput(['class' => 'nyeri_kronis_second--dependent default-disabled']) ?>
                            </div>
                            <div class="col-sm-1">Frekuensi</div>
                            <?= Dhtml::multipleRadio([
                                'model' => $model,
                                'fieldName' => 'frekuensi_nyeri_kronis_kedua',
                                'data' => $configVal['frekuensi_nyeri_kronis'],
                                'class' => 'nyeri_kronis_second--dependent default-disabled'
                            ]) ?>
                            <div class="col-sm-3">
                                <?= $form->field($model, 'durasi_nyeri_kronis_kedua')->textInput(['class' => 'nyeri_kronis_second--dependent default-disabled']) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <?= $form->field($model, 'skor_nyeri') ?>
                            </div>
                        </div>
                        <div class="row form-rom">
                            <div class="col-sm-1">Menjalar</div>
                            <?= Dhtml::trueFalseRadio($model, 'nyeri_menjalar') ?>
                        </div>
                        <div class="row">
                            <div class="col-sm-1">Kualitas Nyeri</div>
                            <?=
                            DHtml::multipleRadio([
                                'model' => $model,
                                'fieldName' => 'kualitas_nyeri',
                                'data' => $configVal['kualitas_nyeri']
                            ])
                            ?>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">Faktor-faktor yang mengurangi / menghilangkan nyeri</div>
                        </div>
                        <div class="row">
                            <?=
                            Dhtml::multipleCheckbox([
                                'model' => $model,
                                'fieldName' => 'faktor_pereda_nyeri',
                                'data' => $configVal['faktor_pereda_nyeri']
                            ])
                            ?>
                        </div>
                    </div>
                    <p class="sub-header-form">9. Nutrisi <span style="color:red; "><sup>*</sup></span></p>
                    <p class="header-form">Skrining Gizi (Berdasarkan Malnutrition Screening Tool/MST)</p>
                    <p class="header-form" style="font-weight: normal;">(Pilih skor seseuai dengan jawaban, Total skor adalah jumlah skor yang diinginkan)</p>
                    <p class="header-form">1. Apakah pasien mengalami penurunan berat badan yang tidak diinginkan dalam 6 bulan terakhir?</p>
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table-nutrition">
                                <tr>
                                    <td>a. Tidak penuruanan berat badan</td>
                                    <td><?= Html::activeRadio($model, 'nutrisi_1b', ['class' => 'nutrisi-check', 'label' => '0', 'data-score' => 0, 'value' => '0', 'id' => 'nutrisi_1a-0']) ?></td>
                                </tr>
                                <tr>
                                    <td>b. Tidak yakin / tidak tahu /terasa baju lebih longgar</td>
                                    <td><?= Html::activeRadio($model, 'nutrisi_1b', ['class' => 'nutrisi-check', 'label' => '2', 'data-score' => 2, 'value' => '2b', 'id' => 'nutrisi_1a-2']) ?></td>
                                </tr>
                                <tr>
                                    <td>c. Jika ya, berapa penurunan berat badan tersebut</td>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>1-5 Kg</td>
                                    <td><?= Html::activeRadio($model, 'nutrisi_1b', ['class' => 'nutrisi-check', 'label' => '1', 'data-score' => 1, 'value' => '1', 'id' => 'nutrisi_1b-1']) ?></td>
                                </tr>
                                <tr>
                                    <td>6-10 Kg</td>
                                    <td><?= Html::activeRadio($model, 'nutrisi_1b', ['class' => 'nutrisi-check', 'label' => '2', 'data-score' => 2, 'value' => '2', 'id' => 'nutrisi_1b-2']) ?></td>
                                </tr>
                                <tr>
                                    <td>10-15 Kg</td>
                                    <td><?= Html::activeRadio($model, 'nutrisi_1b', ['class' => 'nutrisi-check', 'label' => '3', 'data-score' => 3, 'value' => '3', 'id' => 'nutrisi_1b-3']) ?></td>
                                </tr>
                                <tr>
                                    <td>>15 Kg</td>
                                    <td><?= Html::activeRadio($model, 'nutrisi_1b', ['class' => 'nutrisi-check', 'label' => '4', 'data-score' => 4, 'value' => '4', 'id' => 'nutrisi_1b-4']) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <p class="header-form">2. Apakah asupan makan berkurang karena berkurangnya nafsu makan?</p>
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table-nutrition">
                                <tr>
                                    <td>a. Ya</td>
                                    <td><?= Html::activeRadio($model, 'nutrisi_2', ['class' => 'nutrisi-check', 'label' => '1', 'data-score' => 1, 'value' => '1', 'id' => 'nutrisi_2-1']) ?></td>
                                </tr>
                                <tr>
                                    <td>b. Tidak</td>
                                    <td><?= Html::activeRadio($model, 'nutrisi_2', ['class' => 'nutrisi-check', 'label' => '0', 'data-score' => 0, 'value' => '0', 'id' => 'nutrisi_2-0']) ?></td>
                                </tr>
                                <tfoot>
                                    <tr>
                                        <td>Total Skor</td>
                                        <td id="score-section">0</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <p class="header-form">3. Pasien dengan diagnosa khusus</p>
                    <div class="row">
                        <?= Dhtml::trueFalseRadio($model, 'diagnosa_khusus', [
                            'childDependent' => [
                                'class' => 'diagnosa_khusus--dependent'
                            ]
                        ]) ?>
                        <?= DHtml::multipleCheckbox([
                            'model' => $model,
                            'fieldName' => 'jenis_diagnosa_khusus',
                            'data' => $configVal['jenis_diagnosa_khusus']['first_row'],
                            'class' => 'diagnosa_khusus--dependent default-disabled'
                            // 'colSize' => '2'
                        ]);
                        ?>
                    </div>
                    <div class="row">
                        <div class="col-sm-2">&nbsp;</div>
                        <?= DHtml::multipleCheckbox([
                            'model' => $model,
                            'fieldName' => 'jenis_diagnosa_khusus',
                            'data' => $configVal['jenis_diagnosa_khusus']['second_row'],
                            'class' => 'diagnosa_khusus--dependent default-disabled'
                            // 'colSize' => '2' 
                        ]);
                        ?>
                    </div>
                    <hr>
                    <p class="header-form">Skrining Gizi (Adaptasi <i>STRONG-kids</i>)</p>
                    <p class="header-form">1. Apakah pasien tampak Kurus?</p>
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table-nutrition">
                                <tr>
                                    <td>a. Ya</td>
                                    <td><?= Html::activeRadio($model, 'strongkids_kurus', ['class' => 'strongkids-check', 'label' => '1', 'data-score' => 1, 'value' => '1', 'id' => 'strongkids_kurus-1']) ?></td>
                                </tr>
                                <tr>
                                    <td>b. Tidak</td>
                                    <td><?= Html::activeRadio($model, 'strongkids_kurus', ['class' => 'strongkids-check', 'label' => '0', 'data-score' => 0, 'value' => '0', 'id' => 'strongkids_kurus-0']) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <p class="header-form">2. Apakah terdapat penurunan BB selama 1 bulan terakhir? <br>( berdasarkan data penurunan BB objektif bila ada/penilaian subjektif dari orang tua ATAU untuk bayi < 1 tahun: BB tidak naik dalam 3 bulan terakhir )</p>
                            <div class="row">
                                <div class="col-sm-12 text-left">
                                    <table class="table-nutrition">
                                        <tr>
                                            <td>a. Ya</td>
                                            <td><?= Html::activeRadio($model, 'strongkids_turunbb', ['class' => 'strongkids-check', 'label' => '1', 'data-score' => 1, 'value' => '1', 'id' => 'strongkids_turunbb-1']) ?></td>
                                        </tr>
                                        <tr>
                                            <td>b. Tidak</td>
                                            <td><?= Html::activeRadio($model, 'strongkids_turunbb', ['class' => 'strongkids-check', 'label' => '0', 'data-score' => 0, 'value' => '0', 'id' => 'strongkids_turunbb-0']) ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <p class="header-form">3. Apakah terdapat salah satu kondisi berikut? <br>( diare >= 5 kali sehari atau muntah >= 3 kali sehari dalam satu minggu terakhir atau asupan makan berkurang dalam satu minggu terakhir )</p>
                            <div class="row">
                                <table class="table-nutrition">
                                    <tr>
                                        <td>a. Ya</td>
                                        <td><?= Html::activeRadio($model, 'strongkids_kondisikhusus', ['class' => 'strongkids-check', 'label' => '1', 'data-score' => 1, 'value' => '1', 'id' => 'strongkids_kondisikhusus-1']) ?></td>
                                    </tr>
                                    <tr>
                                        <td>b. Tidak</td>
                                        <td><?= Html::activeRadio($model, 'strongkids_kondisikhusus', ['class' => 'strongkids-check', 'label' => '0', 'data-score' => 0, 'value' => '0', 'id' => 'strongkids_kondisikhusus-0']) ?></td>
                                    </tr>
                                </table>
                            </div>
                            <p class="header-form">4. Apakah terdapat penyakit/keadaan yang mengakibatkan berisiko mengalami malnutrisi?</p>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table-nutrition">
                                        <tr>
                                            <td>a. Ya</td>
                                            <td><?= Html::activeRadio($model, 'strongkids_keadaan_beresiko', ['class' => 'strongkids-check', 'label' => '2', 'data-score' => 2, 'value' => '2', 'id' => 'strongkids_keadaan_beresiko-1']) ?></td>
                                        </tr>
                                        <tr>
                                            <td>b. Tidak</td>
                                            <td><?= Html::activeRadio($model, 'strongkids_keadaan_beresiko', ['class' => 'strongkids-check', 'label' => '0', 'data-score' => 0, 'value' => '0', 'id' => 'strongkids_keadaan_beresiko-0']) ?></td>
                                        </tr>
                                        <tfoot>
                                            <tr>
                                                <td>Total Skor</td>
                                                <td id="strongkids-score-section">0</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <?php if (isset($asesmenData['is_verifikasigizi']) && !$asesmenData['is_verifikasigizi']) : ?>
                                    <div class="col-sm-5">
                                        <p class="header-form" style="font-weight: normal;">Sudah dibaca dan diketahui oleh .... pada tanggal ....</p>
                                    </div>
                                    <div class="col-sm-5">
                                        <p class="header-form"><?= Html::button("<b><i class='fa fa-check'></i></b> Verifikasi", ['class' => 'btn btn-xs btn-labeled btn-info btn-verifikasi-gizi ' . ($disableGiziBtn ? 'disabled' : '')]) ?></p>
                                    </div>
                                <?php else : ?>
                                    <div class="col-sm-12">
                                        <p class="header-form" style="font-weight: normal;">Sudah dibaca dan diketahui oleh <?= isset($asesmenData['pegawaiverifikasigizi_nama']) && !empty($asesmenData['pegawaiverifikasigizi_nama']) ? $asesmenData['pegawaiverifikasigizi_nama'] : '....' ?> pada tanggal <?= isset($asesmenData['tgl_verifikasigizi']) && !empty($asesmenData['tgl_verifikasigizi']) ? date('d/m/Y H:i:s', strtotime($asesmenData['tgl_verifikasigizi'])) : '....' ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <p class="sub-header-form">10. Daftar Diagnosa / Masalah Keperawatan Prioritas</p>
                            <div class="row formr">
                                <div class="col-sm-12">
                                    <table class="table table-condensed" id="table-diagnosa">
                                        <thead>
                                            <tr class="bg-inverse">
                                                <th class="text-center">Diagnosa Keperawatan / Masalah Keperawatan</th>
                                                <th class="text-center">Tujuan Terukur</th>
                                                <th>&nbsp;</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <input type="text" name="diagnosa" class="form-control">
                                                </td>
                                                <td>
                                                    <input type="text" name="tujuan_terukur" class="form-control">
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-success btn-xs btn-action btn-add-diagnosa"><i class="fa fa-plus"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    // if(pasienpulang_id != ""){
    //     $("#submit-anamnesa").prop("disabled", true);
    // }
    var pendaftaranId = "' . $pendaftaranId . '"
    var asesmenData   = ' . json_encode($asesmenData) . '
    var status        = "' . $status . '";
    var statusUpdate = "' . $status_update . '";
    var is_perawatasesmenawal = ' . $is_perawat . ';

    $(document).ready(function(){
        if(!is_perawatasesmenawal || statusUpdate){
            $("#form-asesmen :input").not(".btn-verifikasi-gizi").prop("disabled", true);
            $("#submit-anamnesa, .data-reset").prop("disabled", true);
        }
    });
');
$this->registerJs($this->render('js/_asesmen_keperawatan.js'), View::POS_END);
?>
