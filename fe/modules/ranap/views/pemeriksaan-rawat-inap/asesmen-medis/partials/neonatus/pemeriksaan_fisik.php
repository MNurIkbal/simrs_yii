<?php
use yii\web\View;

$classForm = 'form-control input-sm';
$classFormNumber = 'form-control doco-number';
$labelAda = 'Ada';
$labelTidakAda = 'Tidak Ada';
$labelBelumAda = 'Belum Ada';

$keadaanSaatIniLabel = 'keadaan_saat_ini_elektrolit[]';
$muntahLabel = 'Muntah';
$labioschizisLabel = 'Labioschizis';
$labioPalatoGnatoSchizisLabel = 'Labio Palato Gnato Schizis';
$palatoschizisLabel = 'Palatoschizis';
$gnatoschizisLabel = 'Gnatoschizis';
$cupFeedingLabel = 'Cup Feeding';
$breastFeedingLabel = 'Breast Feeding';
$residuLabel = 'Residu > 20%';
$puasaLabel = 'Puasa';
$volLabel = 'Vol';
$keadaanSaatIni = $model->keadaan_saat_ini_elektrolit;
$muntahChecked = $labioschizisChecked = $labioPalatoGnatoSchizisChecked = $palatoschizisChecked =
$gnatoschizisChecked = $cupFeedingChecked = $breastFeedingChecked = $residuChecked = $puasaChecked = $volChecked = false;
if($keadaanSaatIni && is_array($keadaanSaatIni)) {
    if(in_array($muntahLabel, $keadaanSaatIni)) {
        $muntahChecked = true;
    }
    if(in_array($labioschizisLabel, $keadaanSaatIni)) {
        $labioschizisChecked = true;
    }
    if(in_array($labioPalatoGnatoSchizisLabel, $keadaanSaatIni)) {
        $labioPalatoGnatoSchizisChecked = true;
    }
    if(in_array($palatoschizisLabel, $keadaanSaatIni)) {
        $palatoschizisChecked = true;
    }
    if(in_array($gnatoschizisLabel, $keadaanSaatIni)) {
        $gnatoschizisChecked = true;
    }
    if(in_array($cupFeedingLabel, $keadaanSaatIni)) {
        $cupFeedingChecked = true;
    }
    if(in_array($breastFeedingLabel, $keadaanSaatIni)) {
        $breastFeedingChecked = true;
    }
    if(in_array($residuLabel, $keadaanSaatIni)) {
        $residuChecked = true;
    }
    if(in_array($puasaLabel, $keadaanSaatIni)) {
        $puasaChecked = true;
    }
    if(in_array($volLabel, $keadaanSaatIni)) {
        $volChecked = true;
    }
}

?>

<div class="col-md-12 col-header">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">F. Pemeriksaan Fisik</h5>
        </div>
        <div class="panel-body">
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-bottom:20px;">1. Pernafasan </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'jalan_nafas', ['labelOptions' => ['class' => '']])
                            ->label()
                            ->radioList(
                                [
                                    'Bersih' => 'Bersih',
                                    'Ada Sumbatan' => 'Ada Sumbatan',
                                ],
                                [
                                    'itemOptions' => [],
                                    'inline' => 'true',
                                ]
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'spontan', ['labelOptions' => ['class' => '']])
                            ->label()
                            ->radioList(
                                [
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak',
                                ],
                                [
                                    'itemOptions' => [],
                                    'inline' => 'true',
                                ]
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'oksigen', ['labelOptions' => ['class' => '']])
                            ->label()
                            ->radioList(
                                [
                                    'Nasal' => 'Nasal',
                                    'Headbox' => 'Headbox',
                                    'Cat' => 'Cat',
                                ],
                                [
                                    'itemOptions' => [],
                                    'inline' => 'true',
                                ]
                            ); ?>
                        </div>
                    </div>
                    <div class="row" style="margin-top:10px;margin-bottom:10px;">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'saturasi', ['addon' => ['append' => ['content' => '%']]])
                            ->textInput(['class' => $classFormNumber]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'down_score')
                            ->textInput(['class' => $classFormNumber]); ?>
                        </div>
                        
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'umur_kehamilan', ['addon' => ['append' => ['content' => 'Minggu']]])
                            ->textInput(['class' => $classFormNumber]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'keadaan_saat_ini')->checkboxList(
                            [
                                'Lendir' => 'Lendir',
                                'Sesak' => 'Sesak',
                                'Retraksi' => 'Retraksi',
                                'Sianosis' => 'Sianosis',
                                'Wheezing' => 'Wheezing',
                                'Ronchi' => 'Ronchi',
                                'Terpasang WSD' => 'Terpasang WSD',
                                'Continuous Suction' => 'Continuous Suction',
                            ], [
                            ]); ?>
                        </div>
                    </div>
                    <div class="row" style="margin-left:20px;margin-bottom:5px;"></div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'alat_bantu_nafas', ['labelOptions' => ['class' => '']])
                            ->label()
                            ->checkboxList(
                                [
                                    'ETT' => 'ETT',
                                    'Ventilator' => 'Ventilator',
                                    'CPAP' => 'CPAP',
                                ],
                                [
                                    'itemOptions' => [],
                                    'inline' => true
                                ]
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pemeriksaan_agd', ['labelOptions' => ['class' => '']])
                            ->label()
                            ->radioList(
                                [
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak',
                                ],
                                [
                                    'itemOptions' => [],
                                    'inline' => true
                                ]
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                        <?= $form->field($model, 'asidosis', ['labelOptions' => ['class' => '']])
                            ->label()
                            ->checkboxList(
                                [
                                    'Respiratorik' => 'Respiratorik',
                                    'Metabolik' => 'Metabolik',
                                ],
                                [
                                    'itemOptions' => [],
                                    'inline' => true
                                ]
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'alkalalosis', ['labelOptions' => ['class' => '']])
                            ->label(Yii::t('fe', 'Alkalosis'))
                            ->checkboxList(
                                [
                                    'Respiratorik' => 'Respiratorik',
                                    'Metabolik' => 'Metabolik',
                                ],
                                [
                                    'itemOptions' => [],
                                    'inline' => true
                                ]
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                        <?= $form->field($model, 'keterangan')
                            ->textInput(['class' => $classForm]); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-bottom:20px;">2. Sirkulasi </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'waktu_pengisian_kapiler', ['addon' => ['append' => ['content' => 'detik']]])
                            ->textInput(['class' => $classFormNumber]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pengisian_kapiler')
                            ->label()
                            ->radioList(
                                [
                                    'Regular' => 'Regular',
                                    'Irregular' => 'Irregular',
                                ],
                                [
                                    'itemOptions' => [],
                                    'inline' => 'true',
                                ]
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-bottom:20px;">Denyut Arten Femoralis</p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'denyut_arten_kanan')
                            ->label()
                            ->radioList(
                                [
                                    'Kuat' => 'Kuat',
                                    'Lemah' => 'Lemah',
                                ],
                                [
                                    'itemOptions' => [],
                                    'inline' => 'true',
                                ]
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'denyut_arten_kiri')
                            ->label()
                            ->radioList(
                                [
                                    'Kuat' => 'Kuat',
                                    'Lemah' => 'Lemah',
                                ],
                                [
                                    'itemOptions' => [],
                                    'inline' => 'true',
                                ]
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'ekstremitas')
                            ->label()
                            ->checkboxList(
                                [
                                    'Hangat' => 'Hangat',
                                    'Dingin' => 'Dingin',
                                    'Siasonis' => 'Siasonis',
                                ],
                                [
                                    'itemOptions' => [],
                                    'inline' => 'true',
                                ]
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'perdarahan')
                            ->label()
                            ->radioList(
                                [
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak',
                                ],
                                [
                                    'itemOptions' => [],
                                    'inline' => true
                                ]
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'keadaan_saat_ini_sirkulasi')
                            ->label()
                            ->checkboxList(
                                [
                                    'Edema' => 'Edema',
                                    'Lemah' => 'Lemah',
                                    'Pucat' => 'Pucat',
                                ],
                                [
                                    'itemOptions' => [],
                                    'inline' => true
                                ]
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'uvc')
                            ->label()
                            ->radioList(
                                [
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak',
                                ],
                                [
                                    'itemOptions' => [],
                                    'inline' => true
                                ]
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-bottom:20px;">Perifer</p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                        <?= $form->field($model, 'intra_vena')
                            ->label()
                            ->radioList(
                                [
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak',
                                ],
                                [
                                    'itemOptions' => [],
                                    'inline' => true
                                ]
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'intra_arteri')
                            ->label()
                            ->radioList(
                                [
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak',
                                ],
                                [
                                    'itemOptions' => [],
                                    'inline' => true
                                ]
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                        <?= $form->field($model, 'hasil_lab')
                            ->label(Yii::t('fe', 'Hasil Laboratorium'))
                            ->checkboxList(
                                [
                                    'Anemia' => 'Anemia',
                                    'Leukositosis' => 'Leukositosis',
                                    'Trombositonemia' => 'Trombositonemia',
                                    'Hipoproteinemia' => 'Hipoproteinemia',
                                ],
                                [
                                    'itemOptions' => [],
                                ]
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'keterangan_sirkulasi')
                            ->textInput(['class' => $classForm]); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-bottom:20px;">3. Makanan, Cairan dan Elektrolit </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'umur_elektrolit', ['addon' => ['append' => ['content' => 'Hari']]])
                            ->textInput(['class' => $classFormNumber]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'usia_gestasi_elektrolit', ['addon' => ['append' => ['content' => 'Minggu']]])
                            ->textInput(['class' => $classFormNumber]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'bb_lahir', ['addon' => ['append' => ['content' => 'gram']]])
                            ->textInput(['class' => $classFormNumber]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'bb_masuk', ['addon' => ['append' => ['content' => 'gram']]])
                            ->textInput(['class' => $classFormNumber]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'refleks_isap', ['labelOptions' => ['class' => '']])
                            ->label()
                            ->radioList(
                                [
                                    'Kuat' => 'Kuat',
                                    'Lemah' => 'Lemah',
                                    $labelTidakAda => $labelTidakAda,
                                    $labelBelumAda => $labelBelumAda,
                                ],
                                [
                                    'itemOptions' => [],
                                    'inline' => 'true',
                                ]
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'refleks_telan', ['labelOptions' => ['class' => '']])
                            ->label()
                            ->radioList(
                                [
                                    'Kuat' => 'Kuat',
                                    'Lemah' => 'Lemah',
                                    $labelTidakAda => $labelTidakAda,
                                    $labelBelumAda => $labelBelumAda,
                                ],
                                [
                                    'itemOptions' => [],
                                    'inline' => 'true',
                                ]
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, $keadaanSaatIniLabel)
                            ->checkbox(
                            [
                                'label' => Yii::t('fe', $muntahLabel),
                                'value' => Yii::t('fe', $muntahLabel),
                                'checked' => $muntahChecked,
                            ], [
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, $keadaanSaatIniLabel)
                            ->checkbox(
                            [
                                'label' => Yii::t('fe', $labioschizisLabel),
                                'value' => Yii::t('fe', $labioschizisLabel),
                                'checked' => $labioschizisChecked,
                            ], [
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, $keadaanSaatIniLabel)
                            ->checkbox(
                            [
                                'label' => Yii::t('fe', $labioPalatoGnatoSchizisLabel),
                                'value' => Yii::t('fe', $labioPalatoGnatoSchizisLabel),
                                'checked' => $labioPalatoGnatoSchizisChecked,
                            ], [
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, $keadaanSaatIniLabel)
                            ->checkbox(
                            [
                                'label' => Yii::t('fe', $palatoschizisLabel),
                                'value' => Yii::t('fe', $palatoschizisLabel),
                                'checked' => $palatoschizisChecked,
                            ], [
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, $keadaanSaatIniLabel)
                            ->checkbox(
                            [
                                'label' => Yii::t('fe', $gnatoschizisLabel),
                                'value' => Yii::t('fe', $gnatoschizisLabel),
                                'checked' => $gnatoschizisChecked,
                            ], [
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, $keadaanSaatIniLabel)
                            ->checkbox(
                            [
                                'label' => Yii::t('fe', $cupFeedingLabel),
                                'value' => Yii::t('fe', $cupFeedingLabel),
                                'checked' => $cupFeedingChecked,
                            ], [
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, $keadaanSaatIniLabel)
                            ->checkbox(
                            [
                                'label' => Yii::t('fe', $breastFeedingLabel),
                                'value' => Yii::t('fe', $breastFeedingLabel),
                                'checked' => $breastFeedingChecked,
                            ], [
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, $keadaanSaatIniLabel)
                            ->checkbox(
                            [
                                'label' => Yii::t('fe', $residuLabel),
                                'value' => Yii::t('fe', $residuLabel),
                                'checked' => $residuChecked,
                            ], [
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, $keadaanSaatIniLabel)
                            ->checkbox(
                            [
                                'label' => Yii::t('fe', $puasaLabel),
                                'value' => Yii::t('fe', $puasaLabel),
                                'checked' => $puasaChecked,
                            ], [
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, $keadaanSaatIniLabel)
                            ->checkbox(
                            [
                                'label' => Yii::t('fe', $volLabel),
                                'value' => Yii::t('fe', $volLabel),
                                'checked' => $volChecked,
                            ], [
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'vol_lainnya', ['addon' => ['append' => ['content' => 'cc']]])
                            ->textInput(
                            [
                                'class' => $classFormNumber,
                            ])->label(false); ?>
                        </div>
                    </div>
                    <div class="row" style="margin-left:20px;margin-bottom:5px;"></div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'abdomen')
                            ->label()
                            ->checkboxList(
                                [
                                    'Supel' => 'Supel',
                                    'Kembung' => 'Kembung',
                                    'Tegang' => 'Tegang',
                                ],
                                [
                                    'itemOptions' => [],
                                    'inline' => true
                                ]
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'cara_minum')
                            ->label()
                            ->checkboxList(
                                [
                                    'Dot' => 'Dot',
                                    'Menyusui' => 'Menyusui',
                                    'Sonde Lambung' => 'Sonde Lambung',
                                ],
                                [
                                    'itemOptions' => [],
                                    'inline' => true
                                ]
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'lidah')
                            ->label()
                            ->checkboxList(
                                [
                                    'Lembab' => 'Lembab',
                                    'Kotor' => 'Kotor',
                                    'Kering' => 'Kering',
                                    'Lainnya' => 'Lainnya',
                                ],
                                [
                                    'itemOptions' => [],
                                    'inline' => true
                                ]
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'lidah_lainnya')->textInput([
                                'class' => $classForm,
                            ])->label(false); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'selaput_lendir')
                            ->label()
                            ->checkboxList(
                                [
                                    'Lembab' => 'Lembab',
                                    'Lesi' => 'Lesi',
                                    'Kering' => 'Kering',
                                    'Lainnya' => 'Lainnya',
                                ],
                                [
                                    'itemOptions' => [],
                                    'inline' => true
                                ]
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'selaput_lendir_lainnya')->textInput([
                                'class' => $classForm,
                            ])->label(false); ?>
                        </div>
                        
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'turgor')
                            ->label()
                            ->radioList(
                                [
                                    'Baik' => 'Baik',
                                    'Sedang' => 'Sedang',
                                    'Buruk' => 'Buruk',
                                ],
                                [
                                    'itemOptions' => [],
                                    'inline' => true
                                ]
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'hasil_lab_fisik')
                            ->label()
                            ->checkboxList(
                                [
                                    'Hipoproteinimia' => 'Hipoproteinimia',
                                    'Hipoalbuminemia' => 'Hipoalbuminemia',
                                    'Hipokalemia' => 'Hipokalemia',
                                    'Hipokaisemia' => 'Hipokaisemia',
                                    'Hiponatremia' => 'Hiponatremia',
                                    'Hipoglikemia' => 'Hipoglikemia',
                                    'Asidosis Metabolik' => 'Asidosis Metabolik',
                                    'Alkalosis Metabolik' => 'Alkalosis Metabolik',
                                ],
                                [
                                    'itemOptions' => [],
                                ]
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'keterangan_fisik')->textInput(['class' => $classForm]); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-bottom:20px;">4. Neuro Sensori </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'tingkat_kesadaran')->radioList(
                            [
                                1 => 'Berespon Terhadap Nyeri',
                                0 => 'Tidak Berespon Terhadap Nyeri',
                            ], [
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'tangisan')->checkboxList(
                            [
                                $labelTidakAda => $labelTidakAda,
                                'Kuat' => 'Kuat',
                                'Kurang' => 'Kurang Kuat',
                                'Ya' => 'Ya',
                                'Merintih' => 'Merintih',
                            ], [
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                        <?= $form->field($model, 'kepala')->checkboxList(
                            [
                                'Lingkar Kepala' => 'Lingkar Kepala',
                                'Tidak Ada Kelainan' => 'Tidak Ada Kelainan',
                                'Hidrocephalus' => 'Hidrocephalus',
                                'Caput Succadeneum' => 'Caput Succadeneum',
                                'An Encephal' => 'An Encephal',
                                'Cepal Hematom' => 'Cepal Hematom',
                                'Perdarahan Ventrikel' => 'Perdarahan Ventrikel',
                            ], [
                            ]); ?>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <?= $form->field($model, 'kepala_lainnya', ['addon' => ['append' => ['content' => 'cm']]])
                                ->textInput(['class' => $classFormNumber])->label(false); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'ubun')->radioList(
                            [
                                'Datar' => 'Datar',
                                'Cekung' => 'Cekung',
                                'Cembung' => 'Cembung',
                            ], [
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pupil', ['labelOptions' => ['class' => '']])
                            ->checkboxList(
                                [
                                    'Tidak Bereaksi Terhadap Cahaya' => 'Tidak Bereaksi Terhadap Cahaya',
                                    'An Isokor' => 'An Isokor',
                                    'Isokor' => 'Isokor',
                                    'Lemah' => 'Lemah',
                                    'Dilatasi' => 'Dilatasi',
                                ],
                                [
                                    'itemOptions' => [],
                                ]
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'gerakan', ['labelOptions' => ['class' => '']])
                            ->label()
                            ->radioList(
                                [
                                    'Paralisis' => 'Paralisis',
                                    'Subtle' => 'Subtle',
                                    'Aktif' => 'Aktif',
                                ],
                                [
                                    'itemOptions' => [],
                                    'inline' => 'true',
                                ]
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'kejang')->checkboxList(
                            [
                                'Tonik/Klonik' => 'Tonik/Klonik',
                            ], [
                                'itemOptions' => [],
                                'inline' => 'true',
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'keterangan_sensori')->textInput(['class' => $classForm]); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-bottom:20px;">5. Kebersihan Kulit </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'warna_kulit')->radioList(
                            [
                                'Kemerahan' => 'Kemerahan',
                                'Kekuningan' => 'Kekuningan',
                                'Pucat' => 'Pucat',
                            ], [
                                'inline' => true,
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'suhu_kulit')->radioList(
                            [
                                'Hangat' => 'Hangat',
                                'Dingin' => 'Dingin',
                                'Ikterus' => 'Ikterus',
                            ], [
                                'inline' => true,
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                        <?= $form->field($model, 'turgor_kulit')->radioList(
                            [
                                'Baik' => 'Baik',
                                'Buruk' => 'Buruk',
                                'Naik Turun' => 'Naik Turun',
                            ], [
                                'inline' => true,
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                        <?= $form->field($model, 'integritas_kulit')->radioList(
                            [
                                'Kering' => 'Kering',
                                'Utuh' => 'Utuh',
                                'Pustula' => 'Pustula',
                                'Rash' => 'Rash',
                                'Mengelupas' => 'Mengelupas',
                                'Ptechiae' => 'Ptechiae',
                                'Bullae' => 'Bullae',
                                'Kemerahan' => 'Kemerahan',
                                'Lesi' => 'Lesi',
                            ], [
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'kepala_kulit')
                            ->radioList(
                                [
                                    'Bersih' => 'Bersih',
                                    'Kotor' => 'Kotor',
                                    'Bau' => 'Bau',
                                ],
                                [
                                    'itemOptions' => [],
                                    'inline' => true,
                                ]
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'kuku_kulit')
                            ->radioList(
                                [
                                    'Pendek' => 'Pendek',
                                    'Panjang' => 'Panjang',
                                    'Kotor' => 'Kotor',
                                ],
                                [
                                    'itemOptions' => [],
                                    'inline' => 'true',
                                ]
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'mata_kulit')->radioList(
                            [
                                'Bersih' => 'Bersih',
                                'Kotor' => 'Kotor',
                            ], [
                                'itemOptions' => [],
                                'inline' => 'true',
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'sekret_kulit')->radioList(
                            [
                                'Ya' => 'Ya',
                                'Tidak' => 'Tidak',
                            ], [
                                'itemOptions' => [],
                                'inline' => 'true',
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'tali_pusat_kulit')->radioList(
                            [
                                'Bersih' => 'Bersih',
                                'Kotor' => 'Kotor',
                                'Bau' => 'Bau',
                            ], [
                                'itemOptions' => [],
                                'inline' => 'true',
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'puntung_umbilikal_kulit')->checkboxList(
                            [
                                'Kering' => 'Kering',
                                'Basah' => 'Basah',
                                'Kemerahan' => 'Kemerahan',
                                'Pus' => 'Pus',
                                'Bau' => 'Bau',
                            ], [
                                'itemOptions' => [],
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'abdomen_kulit')->checkboxList(
                            [
                                'Kolostomi' => 'Kolostomi',
                                'Luka Operasi' => 'Luka Operasi',
                            ], [
                                'itemOptions' => [],
                                'inline' => 'true',
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'keterangan_kulit')->textInput(
                            [
                                'class' => $classForm,
                            ]); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-bottom:20px;">6. Alat Genital </p>
                    </div>
                    <div class="row">
                        <div class="row" style="font-weight:bold;margin-left:20px;margin-bottom:5px;">Perempuan</div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'vagina')->radioList(
                            [
                                'Bersih' => 'Bersih',
                                'Kotor' => 'Kotor',
                            ], [
                                'inline' => true,
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pseudo_menstruasi')->radioList(
                            [
                                'Ya' => 'Ya',
                                'Tidak' => 'Tidak',
                            ], [
                                'inline' => true,
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'kateter')->radioList(
                            [
                                'Ya' => 'Ya',
                                'Tidak' => 'Tidak',
                            ], [
                                'inline' => true,
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'labia')->radioList(
                            [
                                'Ya' => 'Ya',
                                'Tidak' => 'Tidak',
                            ], [
                                'inline' => true,
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'ambigus')
                            ->radioList(
                                [
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak',
                                ],
                                [
                                    'itemOptions' => [],
                                    'inline' => true,
                                ]
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="row" style="font-weight:bold;margin-left:20px;margin-bottom:5px;">Laki-Laki</div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'preputium')->radioList(
                            [
                                'Bersih' => 'Bersih',
                                'Kotor' => 'Kotor',
                            ], [
                                'inline' => true,
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'hipospadia')->radioList(
                            [
                                'Ya' => 'Ya',
                                'Tidak' => 'Tidak',
                            ], [
                                'inline' => true,
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'keterangan_genital')->textInput(
                            [
                                'class' => $classForm,
                            ]); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-bottom:20px;">7. Eliminasi </p>
                    </div>
                    <div class="row">
                        <div class="row" style="font-weight:bold;margin-left:20px;margin-bottom:5px;">a. Buang Air Kecil (BAK)</div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'frekuensi_bak', ['addon' => ['append' => ['content' => 'x/Hari']]])->textInput(
                            [
                                'class' => $classFormNumber,
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'produksi_urine', ['addon' => ['append' => ['content' => 'cc/kgBB/jam']]])->textInput(
                            [
                                'class' => $classFormNumber,
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'keadaan_bak')->checkboxList(
                            [
                                'Retensi Urine' => 'Retensi Urine',
                                'Pekat' => 'Pekat',
                                'Inkontinentia' => 'Inkontinentia',
                                'Hematuria' => 'Hematuria',
                                'Jernih' => 'Jernih',
                            ], [
                            ]); ?>
                        </div>
                        <div class="col-sm-3">
                            <?= $form->field($model, 'alat_bantu_bak')->radioList(
                            [
                                'Ya' => 'Ya',
                                'Tidak' => 'Tidak',
                            ], [
                                'inline' => true,
                            ]); ?>
                        </div>
                        <div class="col-sm-3">
                            <?= $form->field($model, 'alat_bantu_bak_lainnya')->textInput(
                            [
                                'class' => $classForm,
                            ])->label(false); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'keterangan_bak')->textInput(
                            [
                                'class' => $classForm,
                            ]); ?>
                        </div>
                    </div>
                    <div class="row"></div>
                    <div class="row">
                        <div class="row" style="font-weight:bold;margin-left:20px;margin-bottom:5px;">b. Buang Air Besar (BAB)</div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'bab')->checkboxList(
                            [
                                1 => $labelAda,
                                0 => $labelTidakAda,
                                'Anus' => 'Anus',
                            ], [
                                'inline' => true,
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'keluar_mekonium', ['addon' => ['append' => ['content' => 'Jam setelah lahir']]])->textInput(
                            [
                                'class' => $classFormNumber,
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'frekuensi_bab', ['addon' => ['append' => ['content' => 'x/hari']]])->textInput(
                            [
                                'class' => $classFormNumber,
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'konsistensi_faeces')->checkboxList(
                            [
                                'Lembek' => 'Lembek',
                                'Berampas' => 'Cair Berampas',
                                'Tidak' => 'Cair Tak Berampas',
                            ],
                            [
                                'inline' => true,
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'keadaan_bab')->checkboxList(
                            [
                                'Kembung' => 'Kembung',
                                'Konstipasi' => 'Konstipasi',
                                'Ileustomi' => 'Ileustomi',
                                'Kolostomi' => 'Kolostomi',
                            ],
                            [
                            ]); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-bottom:20px;">8. Keamanan/Mobilisasi </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'gerakan_mobilisasi')->radioList(
                            [
                                1 => 'Aktif',
                                0 => 'Tidak Aktif',
                            ], [
                                'inline' => true,
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'keterangan_mobilisasi')->textInput(
                            [
                                'class' => $classForm,
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'keadaan_mobilisasi')->checkboxList(
                            [
                                'Lemah' => 'Lemah',
                                'Kejang' => 'Kejang',
                                'Gerakan Terbalas' => 'Gerakan Terbalas',
                                'Paralise' => 'Paralise',
                                'Kelemahan Otot' => 'Kelemahan Otot',
                            ], [
                            ]); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-bottom:20px;">9. Tidur dan Istirahat (Untuk Bayi > 1 Hari) </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pasien_lebih_banyak')->checkboxList(
                            [
                                1 => 'Tidak Tidur',
                                2 => 'Menangis',
                                3 => 'Tidur Lebih Banyak Siang Hari',
                                4 => 'Tidur Lebih Banyak Malam Hari',
                            ], [
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'keterangan_pasien')->textInput(
                            [
                                'class' => $classForm,
                            ]); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
var lidah_lainnya = "'.$model->lidah_lainnya.'"
var selaput_lendir_lainnya = "'.$model->selaput_lendir_lainnya.'"
var vol_lainnya = "'.$model->vol_lainnya.'"

$(document).ready(function(){
    const checkboxLidah = $("input[name=\'NeonatusForm[lidah][]\'][value=\'Lainnya\']");
    const checkboxSelaput = $("input[name=\'NeonatusForm[selaput_lendir][]\'][value=\'Lainnya\']");
    const checkboxVol = $("input[name=\'NeonatusForm[keadaan_saat_ini_elektrolit][]\'][value=\'Vol\']");

    const lidahLainnya = $("#neonatusform-lidah_lainnya");
    const selaputLainnya = $("#neonatusform-selaput_lendir_lainnya");
    const volLainnya = $("#neonatusform-vol_lainnya");

    lidahLainnya.prop("readonly", true);
    selaputLainnya.prop("readonly", true);
    volLainnya.prop("readonly", true);

    if(asesmenMedisId) {
        if(lidah_lainnya) {
            lidahLainnya.prop("readonly", false);
        }
        if(selaput_lendir_lainnya) {
            selaputLainnya.prop("readonly", false);
        }
        if(vol_lainnya) {
            volLainnya.prop("readonly", false);
        }
    }

    checkboxLidah.change(function () {
        if(checkboxLidah.is(":checked")) {
            lidahLainnya.prop("readonly", false);
        }
        else {
            lidahLainnya.val("").prop("readonly", true);
        }
    });

    checkboxSelaput.change(function () {
        if(checkboxSelaput.is(":checked")) {
            selaputLainnya.prop("readonly", false);
        }
        else {
            selaputLainnya.val("").prop("readonly", true);
        }
    });
    checkboxVol.change(function () {
        if(checkboxVol.is(":checked")) {
            volLainnya.prop("readonly", false);
        }
        else {
            volLainnya.val("").prop("readonly", true);
        }
    });
})

', View::POS_END);
?>
