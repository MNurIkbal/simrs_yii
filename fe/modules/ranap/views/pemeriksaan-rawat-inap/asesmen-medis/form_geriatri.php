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
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">I. Asesmen Keperawatan</h5>
        </div>
        <div class="panel-body">
            <br>
            <div class="row">
                <div class="col-md-8">
                    <?= $form->field($model, 'status_kesehatan', ['labelOptions' => ['class' => '']])
                        ->label('Status Kesehatan')
                        ->textInput([
                            'class' => 'form-control input-sm',
                            'placeholder' => 'Status Kesehatan'
                        ]);
                    ?>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-8">
                    <?= $form->field($model, 'keluhan_utama', ['labelOptions' => ['class' => '']])
                        ->label('Keluhan Utama')
                        ->textInput([
                            'class' => 'form-control input-sm',
                            'placeholder' => 'Keluhan Utama'
                        ]); ?>
                </div>
                <div class="col-md-8">
                    <?= $form->field($model, 'keluhan_yang_menyertai', ['labelOptions' => ['class' => '']])
                        ->label('Keluhan yang menyertai')
                        ->textInput([
                            'class' => 'form-control input-sm',
                            'placeholder' => 'Keluhan yang menyertai'
                        ]);
                    ?>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <p> Riwayat Penyakit Sekarang</p>
                </div>
                <div class="col-md-8">
                    <?= $form->field($model, 'riwayat_penyakit_sekarang', ['labelOptions' => ['class' => ''], 'template' => '{input}{error}'])
                        ->label(false)
                        ->checkboxList(
                            [
                                'Diabetes Melitus' => 'Diabetes Melitus',
                                'Stroke' => 'Stroke',
                                'Alzheimer' => 'Alzheimer',
                                'Artritis (Radang Sendi)' => 'Artritis (Radang Sendi)',
                                'Hipertensi' => 'Hipertensi',
                                'PPOK' => 'PPOK',
                                'Glaukoma' => 'Glaukoma',
                                'Kanker' => 'Kanker',
                                'Gangguan Darah' => 'Gangguan Darah',
                                'Osteoporosis' => 'Osteoporosis',
                                'Penyakit Jantung' => 'Penyakit Jantung',
                                'Gangguan Jiwa' => 'Gangguan Jiwa',
                                'TB Paru' => 'TB Paru',
                                'Lainnya' => 'Lainnya',
                            ],
                            [
                                'itemOptions' => [],
                                'style' => 'display:grid; grid-template-columns: repeat(4, 1fr); gap: 3px; margin-left: 25px;',
                            ]
                        );
                    ?>
                </div>
                <div id="sekarang-lainnya-textinput" class="col-sm-4" style="margin-top: 10px;">
                    <?= $form->field($model, 'riwayat_penyakit_sekarang_lainnya', [
                        'labelOptions' => ['class' => '']
                    ])->textInput([
                                'id' => 'riwayat_penyakit_sekarang_lainnya',
                                'class' => 'form-control',
                                'readonly' => true,
                                'placeholder' => 'Riwayat Penyakit Sekarang Lainnya',
                                'style' => 'translate: -560px 85px;width: 215px;'
                            ])->label(false); ?>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-8">
                    <p> Riwayat Penyakit Dahulu</p>
                </div>
                <div class="col-md-8">
                    <?= $form->field($model, 'riwayat_penyakit_dahulu', ['labelOptions' => ['class' => ''], 'template' => '{input}{error}'])
                        ->label(false)
                        ->checkboxList(
                            [
                                'Diabetes Melitus' => 'Diabetes Melitus',
                                'Stroke' => 'Stroke',
                                'Alzheimer' => 'Alzheimer',
                                'Artritis (Radang Sendi)' => 'Artritis (Radang Sendi)',
                                'Hipertensi' => 'Hipertensi',
                                'PPOK' => 'PPOK',
                                'Glaukoma' => 'Glaukoma',
                                'Kanker' => 'Kanker',
                                'Gangguan Darah' => 'Gangguan Darah',
                                'Osteoporosis' => 'Osteoporosis',
                                'Penyakit Jantung' => 'Penyakit Jantung',
                                'Gangguan Jiwa' => 'Gangguan Jiwa',
                                'TB Paru' => 'TB Paru',
                                'Lainnya' => 'Lainnya',
                            ],
                            [
                                'itemOptions' => [],
                                'style' => 'display:grid; grid-template-columns: repeat(4, 1fr); gap: 3px; margin-left: 25px;',
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
                                'style' => 'translate: -560px 85px;width: 215px;'
                            ])->label(false); ?>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-8">
                    <p> Riwayat Imunokompromais</p>
                </div>
                <div class="col-md-12">
                    <?= $form->field($model, 'riwayat_imunokompromais', ['labelOptions' => ['class' => ''], 'template' => '{input}{error}'])
                        ->label(false)
                        ->checkboxList(
                            [
                                'Infeksi Telinga baru sebanyak 8 (delapan) kali atau lebih dalam setahun' => 'Infeksi Telinga baru sebanyak 8 (delapan) kali atau lebih dalam setahun',
                                'Infeksi berat pada sinus sebanyak 2 (dua) kali atau lebih dalam setahun' => 'Infeksi berat pada sinus sebanyak 2 (dua) kali atau lebih dalam setahun',
                                'Penggunaan antibiotika tanpa dampak selama 2 (dua) bulan atau lebih' => 'Penggunaan antibiotika tanpa dampak selama 2 (dua) bulan atau lebih',
                                'Pneumonia sebanyak 2 (dua) kali atau lebih dalam setahun' => 'Pneumonia sebanyak 2 (dua) kali atau lebih dalam setahun',
                                'Adanya abses dalam atau berulang pada kulit atau organ lainnya' => 'Adanya abses dalam atau berulang pada kulit atau organ lainnya',
                                'Adanya sariawan yang menetap atau luka pada kulit' => 'Adanya sariawan yang menetap atau luka pada kulit',
                                'Memerlukan antibiotika intra vena untuk injeksi' => 'Memerlukan antibiotika intra vena untuk injeksi',
                                'Terdapat 2 (dua) atau lebih infeksi dalam (misalnya meningitis osteomielitis, selulitis, sepsis)' => 'Terdapat 2 (dua) atau lebih infeksi dalam (misalnya meningitis osteomielitis, selulitis, sepsis)',
                                'Adanya riwayat keluarga terhadap imunodefisiensi primer' => 'Adanya riwayat keluarga terhadap imunodefisiensi primer',
                                'Adanya infeksi yang tidak berespon dengan terapi antibiotika' => 'Adanya infeksi yang tidak berespon dengan terapi antibiotika',
                                'Adanya proses pemulihan yang terlambat atau tak sempurna' => 'Adanya proses pemulihan yang terlambat atau tak sempurna',
                                'Adanya jenis kanker (misalnya sarkoma kaposi, atau limfoma Non-Hodgkins)' => 'Adanya jenis kanker (misalnya sarkoma kaposi, atau limfoma Non-Hodgkins)',
                                'Adanya infeksi oportunistik (misalnya pneumonia pneumocytis carinii atau infeksi jamur berulang)' => 'Adanya infeksi oportunistik (misalnya pneumonia pneumocytis carinii atau infeksi jamur berulang)',
                                'Lainnya' => 'Lainnya'

                            ],
                            [
                                'itemOptions' => [],
                                'style' => 'gap: 3px; margin-left: 25px;',
                            ]
                        );
                    ?>
                </div>
                <div id="imunokompromais-lainnya-textinput" class="col-sm-12" style="margin-top: 10px;">
                    <?= $form->field($model, 'riwayat_imunokompromais_lainnya', [
                        'labelOptions' => ['class' => ''],
                        'template' => '{input}{error}'
                    ])->textInput([
                                'id' => 'riwayat_imunokompromais_lainnya',
                                'class' => 'form-control',
                                'readonly' => true,
                                'placeholder' => 'Riwayat Imunokompromais Lainnya',
                                'style' => 'margin-left:40px; width:500px;margin-top:-10px;'
                            ])->label(false); ?>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-8">
                    <p> Pengkajian Psikologis</p>
                </div>
                <div class="col-md-8">
                    <?= $form->field($model, 'pengkajian_psikologis', ['labelOptions' => ['class' => ''], 'template' => '{input}{error}'])
                        ->label(false)
                        ->checkboxList(
                            [
                                'Cemas' => 'Cemas',
                                'Takut' => 'Takut',
                                'Gelisah' => 'Gelisah',
                                'Acuh tak acuh' => 'Acuh tak acuh',
                                'Sedih' => 'Sedih',
                                'Marah' => 'Marah',
                                'Tenang' => 'Tenang',
                                'Lainnya' => 'Lainnya',
                            ],
                            [
                                'itemOptions' => [],
                                'style' => 'display:grid; grid-template-columns: repeat(4, 1fr); gap: 3px; margin-left: 25px;',
                            ]
                        );
                    ?>
                </div>
                <div id="pengkajian-psikologis-lainnya-textinput" class="col-sm-4" style="margin-top: 10px;">
                    <?= $form->field($model, 'pengkajian_psikologis_lainnya', [
                        'labelOptions' => ['class' => '']
                    ])->textInput([
                                'id' => 'pengkajian_psikologis_lainnya',
                                'class' => 'form-control',
                                'readonly' => true,
                                'placeholder' => 'Pengkajian Psikologis Lainnya',
                                'style' => 'translate:-100px 20px;'
                            ])->label(false); ?>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-8">
                    <p> Respon Penerimaan Pasien terhadap Penyakit</p>
                </div>
                <div class="col-md-8">
                    <?= $form->field($model, 'respon_penerimaan_pasien', ['labelOptions' => ['class' => ''], 'template' => '{input}{error}'])
                        ->label(false)
                        ->checkboxList(
                            [
                                'Pasrah' => 'Pasrah',
                                'Tawakkal' => 'Tawakkal',
                                'Menolak' => 'Menolak',
                                'Marah' => 'Marah'
                            ],
                            [
                                'itemOptions' => [],
                                'style' => 'display:grid; grid-template-columns: repeat(2, 1fr); gap: 3px; margin-left: 25px;',
                            ]
                        );
                    ?>
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
                                        'pattern' => '^\\d+(\\.\\d{1,2})?$'
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
                                'Kurang Bergerak' => 'Kurang Bergerak',
                                'Inkontinensia/Beser' => 'Inkontinensia/Beser',
                                'Infeksi' => 'Infeksi',
                                'Gangguan Pancaindera' => 'Gangguan Pancaindera',
                                'Instabilitas' => 'Instabilitas',
                                'Demensia' => 'Demensia',
                                'Gangguan Kulit' => 'Gangguan Kulit',
                                'Gangguan Komunikasi' => 'Gangguan Komunikasi',
                                'Konstipasi' => 'Konstipasi',
                                'Depresi' => 'Depresi',
                                'Kurang Gizi' => 'Kurang Gizi',
                                'Gangguan Penyembuhan' => 'Gangguan Penyembuhan',
                                'Insomnia' => 'Insomnia',
                                'Impotensi' => 'Impotensi',
                                'Defesiensi Imun' => 'Defesiensi Imun',
                                'Latrogenesis (Penyakit akibat obat)' => 'Latrogenesis (Penyakit akibat obat)',
                            ],
                            [
                                'itemOptions' => [],
                                'style' => 'display:grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-left: 25px;',
                            ]
                        );
                    ?>
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
                    <p> Pola Aktivitas dan Istirahat</p>
                </div>
                <div class="col-md-12">
                    <div class="col-md-6">
                        <?= $form->field($model, 'pola_istirahat', [
                            'labelOptions' => ['class' => '']
                        ])
                            ->checkboxList(
                                [
                                    'Tidak ada kelainan' => 'Tidak ada kelainan',
                                    'Insomnia' => 'Insomnia',
                                    'Penggunaan obat tidur' => 'Penggunaan obat tidur',
                                ],
                                [
                                    'itemOptions' => [],
                                    'style' => 'display:grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-left: 25px; width:665px;',
                                ]
                            );
                        ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-6">
                        <?= $form->field($model, 'pola_olahraga', [
                            'labelOptions' => ['class' => '']
                        ])
                            ->radioList(
                                [
                                    'Tidak' => 'Tidak',
                                    'Ya, Jenis' => 'Ya, Jenis',
                                ],
                                [
                                    'itemOptions' => [],
                                    'style' => 'display:grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-left: 25px; width:665px;',
                                ]
                            );
                        ?>
                    </div>
                    <div id="jenis-olahraga-textinput" class="col-sm-3" style="margin-top: 10px;">
                        <?= $form->field($model, 'jenis_olahraga', [
                            'labelOptions' => ['class' => '']
                        ])->textInput([
                                    'id' => 'jenis_olahraga',
                                    'class' => 'form-control',
                                    'readonly' => true,
                                    'style' => 'translate:-10px -10px;',
                                    'placeholder' => 'Jenis Olahraga'
                                ])->label(false); ?>
                    </div>
                    <div id="frekuensi-olahraga-textinput" class="col-sm-3"
                        style="margin-top: 10px;translate:-150px -10px;">
                        <?= $form->field($model, 'frekuensi_olahraga', [
                            'labelOptions' => ['class' => ''],
                            'addon' => ['append' => ['content' => 'x/Minggu']],
                        ])->textInput([
                                    'id' => 'frekuensi_olahraga',
                                    'class' => 'form-control',
                                    'readonly' => true,
                                    'placeholder' => 'Frekuensi Olahraga'
                                ])->label(false); ?>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <p> Pola Kebiasaan Sehari-hari</p>
                </div>
                <div class="col-md-12">
                    <div class="col-md-6">
                        <?= $form->field($model, 'pola_merokok', ['labelOptions' => ['class' => '']])
                            ->radioList(
                                [
                                    'Tidak' => 'Tidak',
                                    'Ya' => 'Ya',
                                ],
                                [
                                    'itemOptions' => [],
                                    'style' => 'display:grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-left: 25px;',
                                ]
                            );
                        ?>
                    </div>
                    <div id="frekuensi-merokok-1-textinput" class="col-md-3"
                        style="margin-top: 10px;translate:-10px -10px;">
                        <?= $form->field($model, 'frekuensi_merokok_1', [
                            'labelOptions' => ['class' => ''],
                            'addon' => ['append' => ['content' => 'Btg/Bks/Hari']]
                        ])
                            ->label(false)
                            ->textInput([
                                'id' => 'frekuensi_merokok_1',
                                'class' => 'form-control',
                                'readonly' => true,
                                'placeholder' => 'Frekuensi Rokok'
                            ]);
                        ?>
                    </div>
                    <div id="frekuensi-merokok-2-textinput" class="col-md-3"
                        style="margin-top: 10px; translate:-150px -10px;">
                        <?= $form->field($model, 'frekuensi_merokok_2', [
                            'labelOptions' => ['class' => ''],
                            'addon' => ['append' => ['content' => 'Tahun']]
                        ])
                            ->label(false)
                            ->textInput([
                                'id' => 'frekuensi_merokok_2',
                                'class' => 'form-control',
                                'readonly' => true,
                                'placeholder' => 'Selama berapa lama?'
                            ]);
                        ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-6">
                        <?= $form->field($model, 'pola_kopi', ['labelOptions' => ['class' => '']])
                            ->radioList(
                                [
                                    'Tidak' => 'Tidak',
                                    'Ya' => 'Ya',
                                ],
                                [
                                    'itemOptions' => [],
                                    'style' => 'display:grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-left: 25px;',
                                ]
                            );
                        ?>
                    </div>
                    <div id="frekuensi-kopi--textinput" class="col-md-3"
                        style="margin-top: 10px;translate:-10px -10px;">
                        <?= $form->field($model, 'frekuensi_kopi', [
                            'labelOptions' => ['class' => ''],
                            'addon' => ['append' => ['content' => 'Gelas/Hari']]
                        ])
                            ->label(false)
                            ->textInput([
                                'id' => 'frekuensi_kopi',
                                'class' => 'form-control',
                                'readonly' => true,
                                'placeholder' => 'Frekuensi Kopi'
                            ]);
                        ?>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <p> Ketergantungan Terhadap Obat/Zat Tertentu</p>
                </div>
                <div class="col-md-12">
                    <div class="col-md-6">
                        <?= $form->field($model, 'ketergantungan_alkohol', ['labelOptions' => ['class' => '']])
                            ->radioList(
                                [
                                    'Tidak' => 'Tidak',
                                    'Ya' => 'Ya',
                                ],
                                [
                                    'itemOptions' => [],
                                    'style' => 'display:grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-left: 25px;',
                                ]
                            );
                        ?>
                    </div>
                    <div id="ketergantungan-alkohol-textinput" class="col-md-3"
                        style="margin-top: 10px; translate:-10px -10px;">
                        <?= $form->field($model, 'frekuensi_alkohol', [
                            'labelOptions' => ['class' => ''],
                            'addon' => ['append' => ['content' => 'Gelas/Hari']]
                        ])
                            ->label(false)
                            ->textInput([
                                'id' => 'frekuensi_alkohol',
                                'class' => 'form-control',
                                'readonly' => true,
                            ]);
                        ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-6">
                        <?= $form->field($model, 'ketergantungan_obat', ['labelOptions' => ['class' => '']])
                            ->radioList(
                                [
                                    'Tidak' => 'Tidak',
                                    'Ya, Jenis' => 'Ya, Jenis',
                                ],
                                [
                                    'itemOptions' => [],
                                    'style' => 'display:grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-left: 25px;',
                                ]
                            );
                        ?>
                    </div>
                    <div id="ketergantungan-obat-textinput" class="col-md-3" style="margin-top: 10px;">
                        <?= $form->field($model, 'jenis_obat', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])
                            ->label(false)
                            ->checkboxList(
                                [
                                    'Metadon' => 'Metadon',
                                    'Heroin' => 'Heroin',
                                    'Opiat lain/Analgesik' => 'Opiat Lain/Analgesik',
                                    'Kokain' => 'Kokain',
                                    'Amfetamin' => 'Amfetamin',
                                    'Lainnya' => 'Lainnya',
                                ],
                                [
                                    'itemOptions' => ['disabled' => true],
                                    'style' => 'display:grid; grid-template-columns: repeat(3, 1fr); gap: 3px; margin-left: 25px;',
                                ]
                            );
                        ?>
                    </div>
                    <div class="col-md-2">
                        <div id="ketergantungan-obat-lainnya-textinput"
                            style="margin-top: 10px; translate: 0 50px; width: 300px;">
                            <?= $form->field($model, 'jenis_obat_lainnya', [
                                'labelOptions' => ['class' => '']
                            ])->label(false)
                                ->textInput(
                                    [
                                        'id' => 'jenis_obat_lainnya',
                                        'class' => 'form-control',
                                        'readonly' => true,
                                        'placeholder' => 'Jenis Obat Lainnya'
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
                    <p> Orientasi Pada Pasien dan Keluarga</p>
                </div>
                <div class="col-md-8">
                    <?= $form->field($model, 'orientasi_pasien_keluarga', ['labelOptions' => ['class' => ''], 'template' => '{input}{error}'])
                        ->label(false)
                        ->checkboxList(
                            [
                                'Ruangan/Kamar' => 'Ruangan/Kamar',
                                'Pengatur tempat tidur' => 'Pengatur tempat tidur',
                                'WC/Kamar Mandi' => 'WC/Kamar Mandi',
                                'Apotek' => 'Apotek',
                                'Pengaman tempat tidur' => 'Pengaman tempat tidur',
                                'TV dan Remote control' => 'TV dan Remote control',
                                'Sistem Bel' => 'Sistem Bel',
                                'Telepon' => 'Telepon',
                                'Lemari' => 'Lemari',
                                'ATM dan Bank' => 'ATM dan Bank',
                                'Koran untuk VIP' => 'Koran untuk VIP',
                                'Lainnya' => 'Lainnya',
                            ],
                            [
                                'itemOptions' => [],
                                'style' => 'display:grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-left: 25px;',
                            ]
                        ); ?>
                </div>
                <div id="orientasi-pasien-keluarga-lainnya-textinput" class="col-md-4"
                    style="margin-top: 10px; translate:-100px 65px;">
                    <?= $form->field($model, 'orientasi_pasien_keluarga_lainnya', [
                        'labelOptions' => ['class' => ''],
                    ])->label(false)
                        ->textInput(
                            [
                                'id' => 'orientasi_pasien_keluarga_lainnya',
                                'class' => 'form-control',
                                'readonly' => true,
                                'placeholder' => 'Orientasi Lainnya'
                            ]
                        );
                    ?>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    Informasi Pada Pasien dan Keluarga
                </div>
                <div class="col-md-8">
                    <?= $form->field($model, 'informasi_pasien_keluarga', [
                        'labelOptions' => ['class' => '']
                    ])->label(false)
                        ->checkboxList(
                            [
                                'Perawat yang melakukan perawatan' => 'Perawat yang melakukan perawatan',
                                'Waktu dokter visite dan konsultasi' => 'Waktu dokter visite dan konsultasi',
                                'Jam berkunjung' => 'Jam berkunjung'
                            ],
                            [
                                'itemOptions' => [],
                                'style' => 'margin-left:25px;'
                            ]
                        ); ?>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    Penggunaan Alat Medik
                </div>
                <div class="col-md-8">
                    <div class="col-md-4">
                        <?= $form->field($model, 'penggunaan_alat_medik', [
                            'labelOptions' => ['class' => ''],
                            'template' => '{input}{error}'
                        ])->label(false)
                            ->radioList(
                                [
                                    'Tidak' => 'Tidak',
                                    'Ya' => 'Ya'
                                ],
                                [
                                    'itemOptions' => [],
                                    'style' => 'display:grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin-left: 25px;',
                                ]
                            ); ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'jenis_alat_medik', [
                            'labelOptions' => ['class' => '']
                        ])->label(false)
                            ->checkboxList(
                                [
                                    'Infus' => 'Infus',
                                    'Kateter' => 'Kateter',
                                    'NGT' => 'NGT'
                                ],
                                [
                                    'itemOptions' => ['readonly' => true],
                                    'style' => 'margin-left:25px; display:grid; gap:9px;',
                                ]
                            ); ?>
                    </div>
                    <div class="col-md-4" style="right:150px;">
                        <div class="row">
                            <?= $form->field($model, 'tgl_pasang_infus', [
                                'labelOptions' => ['class' => '', 'style' => 'top:8px;'],
                            ])->label('Tanggal pasang')
                                ->textInput(
                                    [
                                        'id' => 'tgl_pasang_infus',
                                        'class' => 'form-control',
                                        'readonly' => true,
                                        'placeholder' => 'Tanggal Pasang Infus'
                                    ]
                                );
                            ?>
                        </div>
                        <div class="row">
                            <?= $form->field($model, 'tgl_pasang_kateter', [
                                'labelOptions' => ['class' => '', 'style' => 'top:8px;']
                            ])->label('Tanggal pasang')
                                ->textInput(
                                    [
                                        'id' => 'tgl_pasang_kateter',
                                        'class' => 'form-control',
                                        'readonly' => true,
                                        'placeholder' => 'Tanggal Pasang Kateter'
                                    ]
                                );
                            ?>
                        </div>
                        <div class="row">
                            <?= $form->field($model, 'tgl_pasang_ngt', [
                                'labelOptions' => ['class' => '', 'style' => 'top:8px;']
                            ])->label('Tanggal pasang')
                                ->textInput(
                                    [
                                        'id' => 'tgl_pasang_ngt',
                                        'class' => 'form-control',
                                        'readonly' => true,
                                        'placeholder' => 'Tanggal Pasang NGT'
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
                    <p> Diagnosa Keperawatan :</p>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'diagnosa_keperawatan', ['labelOptions' => ['class' => ''], 'template' => '{input}{error}'])
                        ->label(false)
                        ->textarea(
                            ['rows' => 5],
                            ['class' => 'form-control', 'placeholder' => 'Diagnosa Keperawatan']
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
                            ['class' => 'form-control', 'placeholder' => 'Rencana Keperawatan dan Tindakan']
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
                    <?= $form->field($model, 'pengkajian_sistem', ['labelOptions' => ['class' => '']])->label(false)->textarea(['rows' => 5], ['class' => 'form-control', 'placeholder' => 'Pengkajian Sistem']) ?>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <p>Diagnosa Medis :</p>
                </div>
                <div class="col-md-12">
                    <?= $form->field($model, 'diagnosa_medis', ['labelOptions' => ['class' => '']])->label(false)->textarea(['rows' => 3], ['class' => 'form-control', 'placeholder' => 'Diagnosa Medis']) ?>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-5">
                    <p>Rencana Terapi dan Tindakan :</p>
                </div>
                <div class="col-md-12">
                    <?= $form->field($model, 'rencana_terapi_dan_tindakan', ['labelOptions' => ['class' => '']])->label(false)->textarea(['rows' => 5], ['class' => 'form-control', 'placeholder' => 'Rencana Terapi dan Tindakan']) ?>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <p>Saran/Nasehat Dokter :</p>
                </div>
                <div class="col-md-12">
                    <?= $form->field($model, 'saran_nasehat_dokter', ['labelOptions' => ['class' => '']])->label(false)->textarea(['rows' => 5], ['class' => 'form-control', 'placeholder' => 'Saran/Nasehat Dokter']) ?>
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

function toggleInput(checkbox, input) {
    if (checkbox.is(":checked")) {
        input.prop("readonly", false);  // Enable the input when checkbox is checked
    } else {
        input.val("").prop("readonly", true);  // Clear the value and disable the input when checkbox is unchecked
    }
}

function toggleInputRadioPolaOlahraga(radio, input) {
    const isYaJenisChecked = $("input[name=\'GeriatriForm[pola_olahraga]\'][value=\'Ya, Jenis\']").is(":checked");
    
    if (isYaJenisChecked) {
        input.prop("readonly", false);
    } else {
        input.prop("readonly", true).val("");
    }
}

function toggleInputRadioPolaMerokok(radio, input) {
    const isYaJenisChecked = $("input[name=\'GeriatriForm[pola_merokok]\'][value=\'Ya\']").is(":checked");
    
    if (isYaJenisChecked) {
        input.prop("readonly", false);
    } else {
        input.prop("readonly", true).val("");
    }
}

function toggleInputRadioPolaKopi(radio, input) {
    const isYaJenisChecked = $("input[name=\'GeriatriForm[pola_kopi]\'][value=\'Ya\']").is(":checked");
    
    if (isYaJenisChecked) {
        input.prop("readonly", false);
    } else {
        input.prop("readonly", true).val("");
    }
}

function toggleInputRadioKetergantunganAlkohol(radio, input) {
    const isYaJenisChecked = $("input[name=\'GeriatriForm[ketergantungan_alkohol]\'][value=\'Ya\']").is(":checked");
    
    if (isYaJenisChecked) {
        input.prop("readonly", false);
    } else {
        input.prop("readonly", true).val("");
    }
}

function toggleInputRadioKetergantunganObat(radio, checkboxes) {
    const isYaJenisChecked = $("input[name=\'GeriatriForm[ketergantungan_obat]\'][value=\'Ya, Jenis\']").is(":checked");
    const input = $("#jenis_obat_lainnya");

    if (isYaJenisChecked) {
        checkboxes.prop("disabled", false);
    } else {
        checkboxes.prop("disabled", true).prop("checked", false);
        input.prop("readonly", true).val("");
    }
}

function toggleInputRadioPenggunaanAlatMedik(radio, checkboxes) {
    const isYaJenisChecked = $("input[name=\'GeriatriForm[penggunaan_alat_medik]\'][value=\'Ya\']").is(":checked");
    const input1 = $("#tgl_pasang_infus");
    const input2 = $("#tgl_pasang_kateter");
    const input3 = $("#tgl_pasang_ngt");

    if (isYaJenisChecked) {
        checkboxes.prop("disabled", false);
    } else {
        checkboxes.prop("disabled", true).prop("checked", false);
        input1.prop("readonly", true).val("");
        input2.prop("readonly", true).val("");
        input3.prop("readonly", true).val("");
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

    // Checkbox

    const checkboxSekarangLainnya = $("input[name=\'GeriatriForm[riwayat_penyakit_sekarang][]\'][value=\'Lainnya\']");
    const inputSekarangLainnya = $("#riwayat_penyakit_sekarang_lainnya");

    const checkboxDahuluLainnya = $("input[name=\'GeriatriForm[riwayat_penyakit_dahulu][]\'][value=\'Lainnya\']");
    const inputDahuluLainnya = $("#riwayat_penyakit_dahulu_lainnya");

    const checkboxImunokompromaisLainnya = $("input[name=\'GeriatriForm[riwayat_imunokompromais][]\'][value=\'Lainnya\']");
    const inputImunokompromaisLainnya = $("#riwayat_imunokompromais_lainnya");

    const checkboxPengkajianPsikologisLainnya = $("input[name=\'GeriatriForm[pengkajian_psikologis][]\'][value=\'Lainnya\']");
    const inputPengkajianPsikologisLainnya = $("#pengkajian_psikologis_lainnya");

    const checkboxJenisObatLainnya = $("input[name=\'GeriatriForm[jenis_obat][]\'][value=\'Lainnya\']");
    const inputJenisObatLainnya = $("#jenis_obat_lainnya");

    const checkboxOrientasiPasienKeluarga = $("input[name=\'GeriatriForm[orientasi_pasien_keluarga][]\'][value=\'Lainnya\']");
    const inputOrientasiPasienKeluarga = $("#orientasi_pasien_keluarga_lainnya");

    const checkboxJenisAlatMedikInfus = $("input[name=\'GeriatriForm[jenis_alat_medik][]\'][value=\'Infus\']");
    const inputTglPasangInfus = $("#tgl_pasang_infus");

    const checkboxJenisAlatMedikKateter = $("input[name=\'GeriatriForm[jenis_alat_medik][]\'][value=\'Kateter\']");
    const inputTglPasangKateter = $("#tgl_pasang_kateter");
    
    const checkboxJenisAlatMedikNGT = $("input[name=\'GeriatriForm[jenis_alat_medik][]\'][value=\'NGT\']");
    const inputTglPasangNGT = $("#tgl_pasang_ngt");

    // Radio Button

    const radioPolaOlahraga = $("input[name=\'GeriatriForm[pola_olahraga]\']");
    const inputPolaOlahraga = $("#jenis_olahraga");
    const inputFrekuensiOlahraga = $("#frekuensi_olahraga");

    const radioPolaMerokok = $("input[name=\'GeriatriForm[pola_merokok]\']");
    const inputFrekuensiMerokok1 = $("#frekuensi_merokok_1");
    const inputFrekuensiMerokok2 = $("#frekuensi_merokok_2");

    const radioPolaKopi = $("input[name=\'GeriatriForm[pola_kopi]\']");
    const inputFrekuensiKopi = $("#frekuensi_kopi");

    const radioKetergantunganAlkohol = $("input[name=\'GeriatriForm[ketergantungan_alkohol]\']");
    const inputFrekuensiAlkohol = $("#frekuensi_alkohol");

    const radioKetergantunganObat = $("input[name=\'GeriatriForm[ketergantungan_obat]\']");
    const inputJenisObat = $("input[name=\'GeriatriForm[jenis_obat][]\']"); // Checkbox list

    const radioPenggunaanAlatMedik = $("input[name=\'GeriatriForm[penggunaan_alat_medik]\']");
    const inputJenisAlatMedik = $("input[name=\'GeriatriForm[jenis_alat_medik][]\']"); // Checkbox list

    // Check checkbox dan radio ketika load dan mengubah atribut readonly
    toggleInput(checkboxSekarangLainnya, inputSekarangLainnya);
    toggleInput(checkboxDahuluLainnya, inputDahuluLainnya);
    toggleInput(checkboxImunokompromaisLainnya, inputImunokompromaisLainnya);
    toggleInput(checkboxPengkajianPsikologisLainnya, inputPengkajianPsikologisLainnya);
    toggleInput(checkboxJenisObatLainnya, inputJenisObatLainnya);
    toggleInput(checkboxOrientasiPasienKeluarga, inputOrientasiPasienKeluarga);

    toggleInput(checkboxJenisAlatMedikInfus, inputTglPasangInfus);
    toggleInput(checkboxJenisAlatMedikKateter, inputTglPasangKateter);
    toggleInput(checkboxJenisAlatMedikNGT, inputTglPasangNGT);

    toggleInputRadioPolaOlahraga(radioPolaOlahraga, inputPolaOlahraga);
    toggleInputRadioPolaOlahraga(radioPolaOlahraga, inputFrekuensiOlahraga);
    toggleInputRadioPolaMerokok(radioPolaMerokok, inputFrekuensiMerokok1);
    toggleInputRadioPolaMerokok(radioPolaMerokok, inputFrekuensiMerokok2);
    toggleInputRadioPolaKopi(radioPolaKopi, inputFrekuensiKopi);
    toggleInputRadioKetergantunganAlkohol(radioKetergantunganAlkohol , inputFrekuensiAlkohol);
    toggleInputRadioKetergantunganObat(radioKetergantunganObat , inputJenisObat);
    toggleInputRadioKetergantunganObat(radioKetergantunganObat, inputJenisObatLainnya);
    toggleInputRadioPenggunaanAlatMedik(radioPenggunaanAlatMedik , inputJenisAlatMedik);


    // Checkbox

    checkboxSekarangLainnya.change(function () {
        toggleInput($(this), inputSekarangLainnya);
    });

    checkboxDahuluLainnya.change(function () {
        toggleInput($(this), inputDahuluLainnya);
    });

    checkboxImunokompromaisLainnya.change(function () {
        toggleInput($(this), inputImunokompromaisLainnya);
    });

    checkboxPengkajianPsikologisLainnya.change(function () {
        toggleInput($(this), inputPengkajianPsikologisLainnya);
    });

    checkboxJenisObatLainnya.change(function () {
        toggleInput($(this), inputJenisObatLainnya);
    });

    checkboxOrientasiPasienKeluarga.change(function () {
        toggleInput($(this), inputOrientasiPasienKeluarga);
    });

    checkboxJenisAlatMedikInfus.change(function () {
        toggleInput($(this), inputTglPasangInfus);
    });

    checkboxJenisAlatMedikKateter.change(function () {
        toggleInput($(this), inputTglPasangKateter);
    });

    checkboxJenisAlatMedikNGT.change(function () {
        toggleInput($(this), inputTglPasangNGT);
    });

    // Radio Button

    radioPolaOlahraga.change(function () {
        toggleInputRadioPolaOlahraga($(this), inputPolaOlahraga);
        toggleInputRadioPolaOlahraga($(this), inputFrekuensiOlahraga);
    });

    radioPolaMerokok.change(function () {
        toggleInputRadioPolaMerokok($(this), inputFrekuensiMerokok1);
        toggleInputRadioPolaMerokok($(this), inputFrekuensiMerokok2);
    });

    radioPolaKopi.change(function () {
        toggleInputRadioPolaKopi($(this), inputFrekuensiKopi);
    });

    radioKetergantunganAlkohol.change(function () {
        toggleInputRadioKetergantunganAlkohol($(this), inputFrekuensiAlkohol);
    });

    radioKetergantunganObat.change(function () {
        toggleInputRadioKetergantunganObat($(this), inputJenisObat);
        toggleInputRadioKetergantunganObat(radioKetergantunganObat, inputJenisObatLainnya);
    });

    radioPenggunaanAlatMedik.change(function () {
        toggleInputRadioPenggunaanAlatMedik($(this), inputJenisAlatMedik);
    });
});
', View::POS_END);

$this->registerJs($this->render('js/asmed-ranap.js'), View::POS_END);
?>