<?php

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\web\View;
use kartik\widgets\ActiveForm;

?>

<div class="panel-toolbar clearfix">
    <?= DocoHelpers::generateToolbar([
        'save' => [
            'attributes' => [
                'form_id' => 'form-asmed-ranap',
                'id' => 'submit-asmed-ranap',
            ]
        ],
    ]); ?>
</div>

<h1 style="text-align:center;"><?= $title ?></h1>
<hr style="margin-bottom:25px;">
<?php $form = ActiveForm::begin([
    'enableClientValidation' => false,
    'enableAjaxValidation' => false,
    'id' => 'form-asmed-ranap',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 5, 'deviceSize' => ActiveForm::SIZE_SMALL],
]);
?>

<br>
<div class="row">
    <div class="col-md-12 col-header">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">I. Asesmen Keperawatan</h5>
            </div>
            <div class="panel-body">
                <br>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'keluhan_utama', ['labelOptions' => ['class' => '']])
                            ->textInput(['class' => 'form-control input-sm']); ?>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <span> Riwayat Penyakit Sekarang</span>
                    </div>
                    <div class="col-sm-8">
                        <?= $form->field($model, 'riwayat_penyakit_sekarang', ['labelOptions' => ['class' => ''], 'template' => '{input}{error}'])
                            ->label(false)
                            ->checkboxList(
                                [
                                    'Hepatitis B dan C' => 'Hepatitis B dan C',
                                    'TB Paru' => 'TB Paru',
                                    'Malaria' => 'Malaria',
                                    'Difteri' => 'Difteri',
                                    'Flu Burung (H5N1)' => 'Flu Burung (H5N1)',
                                    'HIV/AIDS' => 'HIV/AIDS',
                                    'Meningitis' => 'Meningitis',
                                    'Kolera' => 'Kolera',
                                    'Campak/Rubeola' => 'Campak/Rubeola',
                                    'Lainnya' => 'Lainnya',
                                ],
                                [
                                    'itemOptions' => [],
                                    'style' => 'display:grid; grid-template-columns: repeat(5, 1fr); gap: 10px; margin-left: 25px;',
                                ]
                            );
                        ?>
                    </div>
                    <div id="sekarang-lainnya-textinput" class="col-sm-4" style="; margin-top: 10px;">
                        <?= $form->field($model, 'riwayat_penyakit_sekarang_lainnya', [
                            'labelOptions' => ['class' => '']
                        ])->textInput([
                                    'id' => 'riwayat_penyakit_sekarang_lainnya',
                                    'class' => 'form-control',
                                    'readonly' => true,
                                    'placeholder' => 'Riwayat Penyakit Sekarang Lainnya',
                                    'style' => 'translate:-40px 25px;'
                                ])->label(false); ?>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <span> Riwayat Penyakit Dahulu</span>
                    </div>
                    <div class="col-sm-8">
                        <?= $form->field($model, 'riwayat_penyakit_dahulu', ['labelOptions' => ['class' => ''], 'template' => '{input}{error} '])
                            ->label(false)
                            ->checkboxList(
                                [
                                    'Hepatitis B dan C' => 'Hepatitis B dan C',
                                    'TB Paru' => 'TB Paru',
                                    'Malaria' => 'Malaria',
                                    'Difteri' => 'Difteri',
                                    'Flu Burung (H5N1)' => 'Flu Burung (H5N1)',
                                    'HIV/AIDS' => 'HIV/AIDS',
                                    'Meningitis' => 'Meningitis',
                                    'Kolera' => 'Kolera',
                                    'Campak/Rubeola' => 'Campak/Rubeola',
                                    'Lainnya' => 'Lainnya',
                                ],
                                [
                                    'itemOptions' => [],
                                    'style' => 'display:grid; grid-template-columns: repeat(5, 1fr); gap: 10px; margin-left: 25px;',
                                ]
                            );
                        ?>
                    </div>
                    <div id="dahulu-lainnya-textinput" class="col-sm-4" style="margin-top: 10px;">
                        <?= $form->field($model, 'riwayat_penyakit_dahulu_lainnya', [
                            'labelOptions' => ['class' => '']
                        ])->textInput([
                                    'id' => 'riwayat_penyakit_dahulu_lainnya',
                                    'class' => 'form-control',
                                    'readonly' => true,
                                    'placeholder' => 'Riwayat Penyakit Dahulu Lainnya',
                                    'style' => 'translate:-40px 25px;'
                                ])->label(false); ?>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-sm-8">
                                <span>Diagnosis ditegakkan : </span>
                                <?= $form->field($model, 'diagnosis_ditegakkan', ['labelOptions' => ['class' => ''], 'template' => '{input}{error} '])
                                    ->label(false)
                                    ->radioList(
                                        [
                                            'Baru' => 'Baru',
                                            'Lama' => 'Lama',
                                        ],
                                        [
                                            'itemOptions' => [],
                                            'style' => 'display:grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-left: 25px;',
                                        ]
                                    ); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-8">
                                <span>Pasien mengetahui penyakit saat ini :</span>
                                <?= $form->field($model, 'pasien_mengetahui_penyakit', ['labelOptions' => ['class' => ''], 'template' => '{input}{error} '])
                                    ->label(false)
                                    ->radioList(
                                        [
                                            'Ya' => 'Ya',
                                            'Tidak' => 'Tidak',
                                        ],
                                        [
                                            'itemOptions' => [],
                                            'style' => 'display:grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-left: 25px;',
                                        ]
                                    ); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-8">
                                <span>Sumber informasi penyakit yang diperoleh :</span>
                                <?= $form->field($model, 'sumber_info', ['labelOptions' => ['class' => ''], 'template' => '{input}{error} '])
                                    ->label(false)
                                    ->checkboxList(
                                        [
                                            'Dokter' => 'Dokter',
                                            'Perawat' => 'Perawat',
                                            'Keluarga' => 'Keluarga',
                                            'Lainnya' => 'Lainnya',
                                        ],
                                        [
                                            'itemOptions' => [],
                                            'style' => 'display:grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-left: 25px;',
                                        ]
                                    ); ?>
                            </div>
                            <div id="sumber-info-lainnya-textinput" class="col-sm-4" style="margin-top: 10px;">
                                <?= $form->field($model, 'sumber_info_lainnya', [
                                    'labelOptions' => ['class' => ''],
                                    'template' => '{input}{error}'
                                ])->textInput([
                                            'id' => 'sumber_info_lainnya',
                                            'class' => 'form-control',
                                            'readonly' => true,
                                            'placeholder' => 'Sumber Informasi Lainnya',
                                            'style' => 'translate: -135px 45px;'
                                        ])->label(false); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-8">
                                <span>Menerima informasi jangka waktu pengobatan :</span>
                                <?= $form->field($model, 'info_jangka_waktu_pengobatan', ['labelOptions' => ['class' => ''], 'template' => '{input}{error} '])
                                    ->label(false)
                                    ->radioList(
                                        [
                                            'Tidak' => 'Tidak',
                                            'Ya' => 'Ya',

                                        ],
                                        [
                                            'itemOptions' => [],
                                            'style' => 'display:grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-left: 25px;',
                                        ]
                                    ); ?>
                            </div>
                            <div id="info-jangka-waktu-pengobatan-textinput" class="col-sm-4" style="margin-top: 10px;">
                                <?= $form->field($model, 'info_jangka_waktu_pengobatan_text', [
                                    'labelOptions' => ['class' => ''],
                                    'template' => '{input}{error} '
                                ])->textInput([
                                            'id' => 'info_jangka_waktu_pengobatan_text',
                                            'class' => 'form-control',
                                            'readonly' => true,
                                            'placeholder' => 'Berapa Lama?',
                                            'style' => 'translate: -135px 10px;'
                                        ])->label(false); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-8">
                                <span>Melakukan Pemeriksaan Rutin :</span>
                                <?= $form->field($model, 'pemeriksaan_rutin', ['labelOptions' => ['class' => ''], 'template' => '{input}{error} '])
                                    ->label(false)
                                    ->radioList(
                                        [
                                            'Tidak' => 'Tidak',
                                            'Ya, di' => 'Ya, di',
                                        ],
                                        [
                                            'itemOptions' => [],
                                            'style' => 'display:grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-left: 25px;',
                                        ]
                                    ); ?>
                            </div>
                            <div id="pemeriksaan-rutin-textinput" class="col-sm-4" style="margin-top: 10px;">
                                <?= $form->field($model, 'pemeriksaan_rutin_tempat', [
                                    'labelOptions' => ['class' => ''],
                                    'template' => '{input}{error} '
                                ])->textInput([
                                            'id' => 'pemeriksaan_rutin_tempat',
                                            'class' => 'form-control',
                                            'readonly' => true,
                                            'placeholder' => 'Dimana?',
                                            'style' => 'translate: -135px 10px;'
                                        ])->label(false); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6" style="border-left: 1px solid rgba(0, 0, 0, 0.1);">
                        <div class="row">
                            <div class="col-sm-8">
                                <span>Cara Penularan :</span>
                                <?= $form->field($model, 'cara_penularan', ['labelOptions' => ['class' => ''], 'template' => '{input}{error} '])
                                    ->label(false)
                                    ->checkboxList(
                                        [
                                            'Airbone' => 'Airbone',
                                            'Droplet' => 'Droplet',
                                            'Kontak Langsung' => 'Kontak Langsung',
                                            'Cairan Tubuh' => 'Cairan Tubuh',
                                        ],
                                        [
                                            'itemOptions' => [],
                                            'style' => 'display:grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-left: 25px;',
                                        ]
                                    ); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-8">
                                <span>Dirawat diruang isolasi bertekanan negative :</span>
                                <?= $form->field($model, 'isolasi', ['labelOptions' => ['class' => ''], 'template' => '{input}{error} '])
                                    ->label(false)
                                    ->checkboxList(
                                        [
                                            'Ya' => 'Ya',
                                            'Tidak' => 'Tidak',
                                            'Kohorting' => 'Kohorting',
                                            'Tersendiri' => 'Tersendiri',
                                            'Lainnya' => 'Lainnya',
                                        ],
                                        [
                                            'itemOptions' => [],
                                            'style' => 'display:grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-left: 25px;',
                                        ]
                                    ); ?>
                            </div>
                            <div id="isolasi-lainnya-textinput" class="col-sm-3" style="margin-top: 10px;">
                                <?= $form->field($model, 'isolasi_lainnya', [
                                    'labelOptions' => ['class' => ''],
                                    'template' => '{input}{error} '
                                ])->textInput([
                                            'id' => 'isolasi_lainnya',
                                            'class' => 'form-control',
                                            'readonly' => true,
                                            'placeholder' => 'Dimana?',
                                            'style' => 'translate: -200px 45px;'
                                        ])->label(false); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-8">
                                <span> Penggunaan APC :</span>
                                <?= $form->field($model, 'penggunaan_apc', ['labelOptions' => ['class' => ''], 'template' => '{input}{error} '])
                                    ->label(false)
                                    ->checkboxList(
                                        [
                                            'Tidak' => 'Tidak',
                                            'Masker' => 'Masker',
                                            'Sarungtangan' => 'Sarung Tangan',
                                            'Baju' => 'Baju',
                                            'Scort' => 'Scort',
                                            'Sepatu Boot' => 'Sepatu Boot',
                                            'Kacamata' => 'Kacamata',
                                            'Lainnya' => 'Lainnya',
                                        ],
                                        [
                                            'itemOptions' => [],
                                            'style' => 'display:grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-left: 25px;',
                                        ]
                                    ); ?>
                            </div>
                            <div id="penggunaan-apc-lainnya-textinput" class="col-sm-3" style="margin-top: 10px;">
                                <?= $form->field($model, 'penggunaan_apc_lainnya', [
                                    'labelOptions' => ['class' => ''],
                                    'template' => '{input}{error} '
                                ])->textInput([
                                            'id' => 'penggunaan_apc_lainnya',
                                            'class' => 'form-control',
                                            'placeholder' => 'APC apa?',
                                            'style' => 'translate: -200px 85px;'
                                        ])->label(false); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-8">
                                <span> Penyakit Penyerta :</span>
                                <?= $form->field($model, 'penyakit_penyerta', ['labelOptions' => ['class' => ''], 'template' => '{input}{error} '])
                                    ->label(false)
                                    ->radioList(
                                        [
                                            'Tidak' => 'Tidak',
                                            'Ya' => 'Ya',
                                        ],
                                        [
                                            'itemOptions' => [],
                                            'style' => 'display:grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-left: 25px;',
                                        ]
                                    ); ?>
                            </div>
                            <div id="penyakit-penyerta-textinput" class="col-sm-3" style="margin-top: 10px;">
                                <?= $form->field($model, 'penyakit_penyerta_text', [
                                    'labelOptions' => ['class' => ''],
                                    'template' => '{input}{error} '
                                ])->textInput([
                                            'id' => 'penyakit_penyerta_text',
                                            'class' => 'form-control',
                                            'readonly' => true,
                                            'placeholder' => 'Penyakit apa?',
                                            'style' => 'translate: -200px 8px;'
                                        ])->label(false); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-sm-8">
                                <span> Pengkajian Psikologi :</span>
                                <?= $form->field($model, 'pengkajian_psikologi', ['labelOptions' => ['class' => ''], 'template' => '{input}{error} '])
                                    ->label(false)
                                    ->checkboxList(
                                        [
                                            'Cemas' => 'Cemas',
                                            'Sedih' => 'Sedih',
                                            'Takut' => 'Takut',
                                            'Marah' => 'Marah',
                                            'Gelisah' => 'Gelisah',
                                            'Tenang' => 'Tenang',
                                        ],
                                        [
                                            'itemOptions' => [],
                                            'style' => 'display:grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-left: 25px;',
                                        ]
                                    ); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6" style="border-left: 1px solid rgba(0, 0, 0, 0.1);">
                        <div class="row">
                            <div class="col-sm-8">
                                <span> Respon Penerimaan Pasien terhadap Penyakit :</span>
                                <?= $form->field($model, 'respon_pasien', ['labelOptions' => ['class' => ''], 'template' => '{input}{error} '])
                                    ->label(false)
                                    ->checkboxList(
                                        [
                                            'Pasrah' => 'Pasrah',
                                            'Tawakkal' => 'Tawakkal',
                                            'Menolak' => 'Menolak',
                                            'Marah' => 'Marah',
                                        ],
                                        [
                                            'itemOptions' => [],
                                            'style' => 'display:grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-left: 25px;',
                                        ]
                                    ); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-sm-8">
                                <p style="margin-bottom:5px;"> Keadaan Umum :</p>
                                <?= $form->field($model, 'kesadaran', ['labelOptions' => ['class' => '']])
                                    ->textInput(
                                        [
                                            'id' => 'kesadaran',
                                            'class' => 'form-control',
                                            'placeholder' => 'Kesadaran',
                                        ]
                                    ); ?>
                                <div class="row">
                                    <div class="col-sm-5">
                                        <p>GCS</p>
                                    </div>
                                    <div class="col-sm-7">
                                        <div class="col-sm-4">
                                            <?= $form->field($model, 'gcsE', [
                                                'labelOptions' => ['class' => '', 'style' => 'translate: 0 8px;']
                                            ])
                                                ->textInput(
                                                    [
                                                        'id' => 'gcsE',
                                                        'class' => 'form-control',
                                                        'style' => 'width:45px; translate: -15px;'
                                                    ]
                                                );
                                            ?>
                                        </div>
                                        <div class="col-sm-4">
                                            <?= $form->field($model, 'gcsM', [
                                                'labelOptions' => ['class' => '', 'style' => 'translate: 0 8px;']
                                            ])
                                                ->textInput(
                                                    [
                                                        'id' => 'gcsM',
                                                        'class' => 'form-control',
                                                        'style' => 'width:45px; translate: -15px;'
                                                    ]
                                                );
                                            ?>
                                        </div>
                                        <div class="col-sm-4">
                                            <?= $form->field($model, 'gcsV', [
                                                'labelOptions' => ['class' => '', 'style' => 'translate: 0 8px;']
                                            ])
                                                ->textInput(
                                                    [
                                                        'id' => 'gcsV',
                                                        'class' => 'form-control',
                                                        'style' => 'width:45px; translate: -15px;'
                                                    ]
                                                );
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <?= $form->field($model, 'berat_badan', [
                                    'labelOptions' => ['class' => ''],
                                    'addon' => ['append' => ['content' => 'kg']],
                                ])
                                    ->textInput(
                                        [
                                            'id' => 'berat_badan',
                                            'class' => 'form-control doco-decimal-wcomma',
                                            'placeholder' => 'Berat Badan',
                                        ]
                                    );
                                ?>
                                <?= $form->field($model, 'tinggi_badan', [
                                    'labelOptions' => ['class' => ''],
                                    'addon' => ['append' => ['content' => 'cm']],
                                ])
                                    ->textInput(
                                        [
                                            'id' => 'tinggi_badan',
                                            'class' => 'form-control doco-decimal-wcomma',
                                            'placeholder' => 'Tinggi Badan',
                                        ]
                                    );
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6" style="border-left: 1px solid rgba(0, 0, 0, 0.1);">
                        <div class="row">
                            <div class="col-sm-8">
                                <p style="margin-bottom:5px;"> Tanda-tanda Vital :</p>
                                <?= $form->field($model, 'tekanan_darah', [
                                    'labelOptions' => ['class' => ''],
                                    'addon' => ['append' => ['content' => 'mmHg']],
                                ])
                                    ->textInput([
                                        'id' => 'tekanan_darah',
                                        'class' => 'form-control',
                                        'placeholder' => 'Tekanan Darah',
                                        'pattern' => '^\\d{1,3}/\\d{1,3}$',
                                        'oninput' => "validateInput(this)",
                                    ])
                                    ->hint('Format tekanan darah : XXX/XXX, contoh: 120/80.');
                                ?>
                                <?= $form->field($model, 'frekuensi_nadi', [
                                    'labelOptions' => ['class' => ''],
                                    'addon' => ['append' => ['content' => 'x/Menit']],
                                ])
                                    ->textInput(
                                        [
                                            'id' => 'frekuensi_nadi',
                                            'class' => 'form-control doco-number',
                                            'placeholder' => 'Nadi',
                                        ]
                                    );
                                ?>
                                <?= $form->field($model, 'frekuensi_nafas', [
                                    'labelOptions' => ['class' => ''],
                                    'addon' => ['append' => ['content' => 'x/Menit']],
                                ])
                                    ->textInput(
                                        [
                                            'id' => 'frekuensi_nafas',
                                            'class' => 'form-control doco-number',
                                            'placeholder' => 'Nafas',
                                        ]
                                    );
                                ?>
                                <?= $form->field($model, 'suhu_badan', [
                                    'labelOptions' => ['class' => ''],
                                    'addon' => ['append' => ['content' => '&deg; Celcius']],
                                ])
                                    ->textInput(
                                        [
                                            'id' => 'suhu_badan',
                                            'class' => 'form-control doco-decimal-wcomma',
                                            'placeholder' => 'Suhu',
                                        ]
                                    );
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <p> Pemeriksaan Fisik</p>
                    </div>
                    <div class="col-sm-8">
                        <?= $form->field($model, 'pemeriksaan_fisik', ['labelOptions' => ['class' => ''], 'template' => '{input}{error}'])
                            ->label(false)
                            ->checkboxList(
                                [
                                    'Sesak' => 'Sesak',
                                    'Demam' => 'Demam',
                                    'Edema' => 'Edema',
                                    'Insomnia' => 'Insomnia',
                                    'Penurunan Berat Badan' => 'Penurunan Berat Badan',
                                    'Lemas' => 'Lemas',
                                    'Anoreksia' => 'Anoreksia',
                                    'Anemia' => 'Anemia',
                                    'Nyeri' => 'Nyeri',
                                    'Lainnya' => 'Lainnya'
                                ],
                                [
                                    'itemOptions' => [],
                                    'style' => 'display:grid; grid-template-columns: repeat(5, 1fr); gap: 10px; margin-left: 25px;',
                                ]
                            );
                        ?>
                    </div>
                    <div id="pemeriksaan-fisik-lainnya-textinput" class="col-sm-4" style="margin-top: 10px;">
                        <?= $form->field($model, 'pemeriksaan_fisik_lainnya', [
                            'labelOptions' => ['class' => '']
                        ])->textInput([
                                    'id' => 'pemeriksaan_fisik_lainnya',
                                    'class' => 'form-control',
                                    'readonly' => true,
                                    'placeholder' => 'Pemeriksaan Fisik Lainnya',
                                    'style' => 'translate:-40px 25px;'
                                ])->label(false); ?>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <p> Pemenuhan Kebutuhan Aktifitas Hidup Sehari-hari (ADL) :</p>
                    </div>
                    <div class="col-sm-8">
                        <?= $form->field($model, 'adlMakanMinum', ['labelOptions' => ['class' => '']])
                            ->radioList(
                                [
                                    'Bantuan Minimal' => 'Bantuan Minimal',
                                    'Bantuan Total' => 'Bantuan Total',
                                ],
                                [
                                    'itemOptions' => [],
                                    'style' => 'display:grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-left: 25px;',
                                ]
                            );
                        ?>
                        <?= $form->field($model, 'adlMandi', ['labelOptions' => ['class' => '']])
                            ->radioList(
                                [
                                    'Bantuan Minimal' => 'Bantuan Minimal',
                                    'Bantuan Total' => 'Bantuan Total',
                                ],
                                [
                                    'itemOptions' => [],
                                    'style' => 'display:grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-left: 25px;',
                                ]
                            );
                        ?>
                        <?= $form->field($model, 'adlBuangAir', ['labelOptions' => ['class' => '']])
                            ->radioList(
                                [
                                    'Bantuan Minimal' => 'Bantuan Minimal',
                                    'Bantuan Total' => 'Bantuan Total',
                                ],
                                [
                                    'itemOptions' => [],
                                    'style' => 'display:grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-left: 25px;',
                                ]
                            );
                        ?>
                        <?= $form->field($model, 'adlBerpakaian', ['labelOptions' => ['class' => '']])
                            ->radioList(
                                [
                                    'Bantuan Minimal' => 'Bantuan Minimal',
                                    'Bantuan Total' => 'Bantuan Total',
                                ],
                                [
                                    'itemOptions' => [],
                                    'style' => 'display:grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-left: 25px;',
                                ]
                            );
                        ?>
                        <?= $form->field($model, 'adlIstirahat', ['labelOptions' => ['class' => '']])
                            ->radioList(
                                [
                                    'Bantuan Minimal' => 'Bantuan Minimal',
                                    'Bantuan Total' => 'Bantuan Total',
                                ],
                                [
                                    'itemOptions' => [],
                                    'style' => 'display:grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-left: 25px;',
                                ]
                            );
                        ?>
                        <?= $form->field($model, 'adlPenggunaanObat', ['labelOptions' => ['class' => '']])
                            ->radioList(
                                [
                                    'Bantuan Minimal' => 'Bantuan Minimal',
                                    'Bantuan Total' => 'Bantuan Total',
                                ],
                                [
                                    'itemOptions' => [],
                                    'style' => 'display:grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-left: 25px;',
                                ]
                            );
                        ?>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <p> Diagnosa Keperawatan :</p>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'diagnosa_keperawatan', ['labelOptions' => ['class' => ''], 'template' => '{input}{error}'])
                            ->label(false)
                            ->textarea(
                                ['rows' => 5],
                                ['class' => 'form-control']
                            ); ?>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <p> Rencana Keperawatan dan Tindakan :</p>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'rencana_keperawatan_dan_tindakan', ['labelOptions' => ['class' => ''], 'template' => '{input}{error}'])
                            ->label(false)
                            ->textarea(
                                ['rows' => 5],
                                ['class' => 'form-control']
                            ); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title">II. Asesmen Medis</h5>
            </div>
            <div class="panel-body">
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <p>Pengkajian Sistem :</p>
                    </div>
                    <div class="col-md-12">
                        <?= $form->field($model, 'pengkajian_sistem', ['labelOptions' => ['class' => '']])->label(false)->textarea(['rows' => 5]) ?>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <p>Diagnosa Medis :</p>
                    </div>
                    <div class="col-md-12">
                        <?= $form->field($model, 'diagnosa_medis', ['labelOptions' => ['class' => '']])->label(false)->textarea(['rows' => 3]) ?>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-5">
                        <p>Rencana Terapi dan Tindakan :</p>
                    </div>
                    <div class="col-md-12">
                        <?= $form->field($model, 'rencana_terapi_dan_tindakan', ['labelOptions' => ['class' => '']])->label(false)->textarea(['rows' => 5]) ?>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <p>Saran/Nasehat Dokter :</p>
                    </div>
                    <div class="col-md-12">
                        <?= $form->field($model, 'saran_nasehat_dokter', ['labelOptions' => ['class' => '']])->label(false)->textarea(['rows' => 5]) ?>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12">
                        <p>Data Penunjang :</p>
                    </div>
                    <div class="col-md-7">
                        <?= $form->field($model, 'laboratorium', ['labelOptions' => ['class' => '']])
                            ->textInput(
                                [
                                    'id' => 'laboratorium',
                                    'class' => 'form-control',
                                    'placeholder' => 'Laboratorium',
                                ]
                            ); ?>
                    </div>
                    <div class="col-md-7">
                        <?= $form->field($model, 'radiologi', ['labelOptions' => ['class' => '']])
                            ->textInput(
                                [
                                    'id' => 'radiologi',
                                    'class' => 'form-control',
                                    'placeholder' => 'Radiologi',
                                ]
                            ); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>

<?php
$this->registerJs('
        var isDokumenEklaim = "' . $isDokumenEklaim . '";
        var _model = "' . $modelName . '";
        var _modelName = _model.toLowerCase();

        // Fungsi untuk mengubah tampilan input berdasarkan checkbox
            function toggleInput(checkbox, container, input) {
                if (checkbox.is(":checked")) {
                    input.prop("readonly", false);
                } else {
                    input.val("").prop("readonly", true);
                }
            }

        // Fungsi untuk mengubah tampilan input berdasarkan radiobutton
            function toggleInputRadio(radio, container, input) {
                if (radio.is(":checked")) {
                    input.prop("readonly", false); // Aktifkan input
                } else {
                    input.val("").prop("readonly", true); // Reset dan nonaktifkan input
                }
            }

            function validateInput(input) {
                input.value = input.value.replace(/[^0-9/]/g, "");
                const parts = input.value.split("/");
                if (parts.length > 2 || (parts[0] && parts[0].length > 3) || (parts[1] && parts[1].length > 3)) {
                    input.value = input.value.slice(0, -1);
                }
            }

        $(document).ready(function () {
            $("#tekanan_darah").on("input", function () {
                validateInput(this);
            });

            const checkboxSekarangLainnya = $("input[name=\'InspeksiusForm[riwayat_penyakit_sekarang][]\'][value=\'Lainnya\']");
            const inputSekarangContainer = $("#sekarang-lainnya-textinput");
            const inputSekarangLainnya = $("#riwayat_penyakit_sekarang_lainnya");

            const checkboxDahuluLainnya = $("input[name=\'InspeksiusForm[riwayat_penyakit_dahulu][]\'][value=\'Lainnya\']");
            const inputDahuluContainer = $("#dahulu-lainnya-textinput");
            const inputDahuluLainnya = $("#riwayat_penyakit_dahulu_lainnya");

            const checkboxSumberInfoLainnya = $("input[name=\'InspeksiusForm[sumber_info][]\'][value=\'Lainnya\']");
            const inputSumberInfoContainer = $("#sumber-info-lainnya-textinput");
            const inputSumberInfoLainnya = $("#sumber_info_lainnya");

            const radioJangkaWaktuPengobatan = $("input[name=\'InspeksiusForm[info_jangka_waktu_pengobatan]\'][value=\'Ya\']");
            const inputJangkaWaktuPengobatanContainer = $("#info-jangka-waktu-pengobatan-textinput");
            const inputJangkaWaktuPengobatanLainnya = $("#info_jangka_waktu_pengobatan_text");

            const radioPemeriksaanRutin = $("input[name=\'InspeksiusForm[pemeriksaan_rutin]\'][value=\'Ya, di\']");
            const inputPemeriksaanRutinContainer = $("#pemeriksaan-rutin-textinput");
            const inputPemeriksaanRutinLainnya = $("#pemeriksaan_rutin_tempat");

            const checkboxIsolasi = $("input[name=\'InspeksiusForm[isolasi][]\'][value=\'Lainnya\']");
            const inputIsolasiContainer = $("#isolasi-lainnya-textinput");
            const inputIsolasiLainnya = $("#isolasi_lainnya");

            const checkboxAPC = $("input[name=\'InspeksiusForm[penggunaan_apc][]\'][value=\'Lainnya\']");
            const inputAPCContainer = $("#penggunaan-apc-lainnya-textinput");
            const inputAPCLainnya = $("#penggunaan_apc_lainnya");

            const radioPenyakitPenyerta = $("input[name=\'InspeksiusForm[penyakit_penyerta]\'][value=\'Ya\']");
            const inputPenyakitPenyertaContainer = $("#penyakit-penyerta-textinput");
            const inputPenyakitPenyertaLainnya = $("#penyakit_penyerta_text");

            const checkboxPemeriksaanFisikLainnya = $("input[name=\'InspeksiusForm[pemeriksaan_fisik][]\'][value=\'Lainnya\']");
            const inputPemeriksaanFisikLainnyaContainer = $("#pemeriksaan-fisik-lainnya-textinput");
            const inputPemeriksaanFisikLainnya = $("#pemeriksaan_fisik_lainnya");

            toggleInput(checkboxSekarangLainnya, inputSekarangContainer, inputSekarangLainnya);
            toggleInput(checkboxDahuluLainnya, inputDahuluContainer, inputDahuluLainnya);
            toggleInput(checkboxSumberInfoLainnya, inputSumberInfoContainer, inputSumberInfoLainnya);
            toggleInput(checkboxIsolasi, inputIsolasiContainer, inputIsolasiLainnya);
            toggleInput(checkboxAPC, inputAPCContainer, inputAPCLainnya);
            toggleInput(checkboxPemeriksaanFisikLainnya, inputPemeriksaanFisikLainnyaContainer, inputPemeriksaanFisikLainnya);

            toggleInputRadio(radioJangkaWaktuPengobatan, inputJangkaWaktuPengobatanContainer, inputJangkaWaktuPengobatanLainnya);
            toggleInputRadio(radioPemeriksaanRutin, inputPemeriksaanRutinContainer, inputPemeriksaanRutinLainnya);
            toggleInputRadio(radioPenyakitPenyerta, inputPenyakitPenyertaContainer, inputPenyakitPenyertaLainnya);

            $("input[name=\'InspeksiusForm[pemeriksaan_rutin]\']").on("change", function () {
                toggleInputRadio(radioPemeriksaanRutin, inputPemeriksaanRutinContainer, inputPemeriksaanRutinLainnya);
            });

            $("input[name=\'InspeksiusForm[info_jangka_waktu_pengobatan]\']").on("change", function () {
                toggleInputRadio(radioJangkaWaktuPengobatan, inputJangkaWaktuPengobatanContainer, inputJangkaWaktuPengobatanLainnya);
            });

            $("input[name=\'InspeksiusForm[penyakit_penyerta]\']").on("change", function() {
                toggleInputRadio(radioPenyakitPenyerta, inputPenyakitPenyertaContainer, inputPenyakitPenyertaLainnya);
            });

            checkboxSekarangLainnya.change(function () {
                toggleInput($(this), inputSekarangContainer, inputSekarangLainnya);
            });

            checkboxDahuluLainnya.change(function () {
                toggleInput($(this), inputDahuluContainer, inputDahuluLainnya);
            });

            checkboxSumberInfoLainnya.change(function () {
                toggleInput($(this), inputSumberInfoContainer, inputSumberInfoLainnya);
            });

            checkboxIsolasi.change(function () {
                toggleInput($(this), inputIsolasiContainer, inputIsolasiLainnya);
            });

            checkboxAPC.change(function () {
                toggleInput($(this), inputAPCContainer, inputAPCLainnya);
            });

            checkboxPemeriksaanFisikLainnya.change(function () {
                toggleInput($(this), inputPemeriksaanFisikLainnyaContainer, inputPemeriksaanFisikLainnya);
            });
        });
', View::POS_END);
$this->registerJs($this->render('js/asmed-ranap.js'), View::POS_END);
?>