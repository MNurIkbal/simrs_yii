<?php

use yii\helpers\Html;
use yii\web\View;

$classForm = 'form-control';
$classFormNumber = 'form-control doco-number';
$labelAda = 'Ada';
$labelTidak = 'Tidak Ada';
$labelNyeriTekan = 'Nyeri Tekan';
$labelTidakNormal = 'Tidak Normal';
?>

<div class="col-md-12 col-header">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">J. Pemeriksaan Fisik</h5>
        </div>
        <div class="panel-body">
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-top:20px;margin-bottom:20px;">1. Sistem Respirasi </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'jalan_nafas')->radioList(
                            [
                                '1' => 'Bersih',
                                '0' => 'Ada Sumbatan',
                            ], ['inline' => true]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'irama')->radioList(
                            [
                                '1' => 'Teratur',
                                '0' => 'Tidak Teratur',
                            ], ['inline' => true]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'kedalaman')->radioList(
                            [
                                '1' => 'Normal',
                                '2' => 'Dangkal',
                                '3' => 'Dalam',
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pola_nafas')->radioList(
                            [
                                '1' => 'Normal',
                                '2' => 'Takipnoe',
                                '3' => "Kusmaul's",
                                '4' => 'Bradipnoe',
                                '5' => 'Cheyne Stokes',
                                '6' => 'Biots',
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'batuk')->radioList(
                            [
                                '1' => 'Ya',
                                '0' => 'Tidak',
                            ], ['inline' => true]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'bentuk_dada')->radioList(
                            [
                                '1' => 'Normal',
                                '2' => 'Funnel Chest',
                                '3' => 'Pigeon Chest',
                                '4' => 'Barrel Chest',
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'ekspansi_dada')->radioList(
                            [
                                '1' => 'Simetris',
                                '0' => 'Tidak Simetris',
                            ], ['inline' => true]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'otot_pernafasan')
                            ->label(Yii::t('fe', 'Penggunaan Otot-Otot Pernafasan'))
                            ->radioList(
                            [
                                '1' => 'Ya',
                                '0' => 'Tidak',
                            ], ['inline' => true]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'trauma')
                            ->radioList(
                            [
                                '1' => 'Ya',
                                '0' => 'Tidak',
                            ], ['inline' => true]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'lain_lain')
                            ->textInput(['class' => $classForm]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'sputum')->radioList(
                            [
                                '0' => $labelTidak,
                                '1' => 'Ada Warna',
                            ], ['inline' => true]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'sputum_warna')->radioList(
                            [
                                '1' => 'Putih',
                                '2' => 'Hijau',
                                '3' => 'Merah',
                                '4' => 'Kuning',
                                '5' => 'Purulent',
                                '6' => 'Kecoklatan',
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'clubbing_finge')->radioList(
                            [
                                '1' => 'Ya',
                                '0' => 'Tidak',
                            ], ['inline' => true]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'trakhea')
                            ->radioList(
                            [
                                '1' => 'Deviasi Kelateral',
                                '0' => 'Tidak',
                            ], ['inline' => true]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pembesaran_kelenjar')
                            ->label(Yii::t('fe', 'Pembesaran Kelenjar Getah Bening/Massa'))
                            ->radioList(
                            [
                                '1' => 'Ya',
                                '0' => 'Tidak',
                            ], ['inline' => true]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'perkusi')
                            ->radioList(
                            [
                                '1' => 'Sonor',
                                '2' => 'Timpani',
                                '3' => 'Redup',
                                '4' => 'Hipersonor',
                                '5' => 'Pekak',
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'auskultasi')
                            ->radioList(
                            [
                                '1' => 'Vesikuler',
                                '2' => 'Trakeal',
                                '3' => 'Wheezing',
                                '4' => 'Krepitasi',
                                '5' => 'Bronchovesikuler',
                                '6' => 'Bronchial',
                                '7' => 'Ronchi',
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-top:20px;margin-bottom:20px;">2. Sistem Kardiovaskuler </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'sianosis')
                            ->radioList(
                            [
                                '0' => $labelTidak,
                                '1' => $labelAda,
                            ], ['inline' => true]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pucat')
                            ->radioList(
                            [
                                '0' => $labelTidak,
                                '1' => $labelAda,
                            ], ['inline' => true]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'akral')
                            ->radioList(
                            [
                                '1' => 'Hangat',
                                '2' => 'Dingin',
                            ], ['inline' => true]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'lain_lain_kardiovaskuler')
                            ->label(Yii::t('fe', 'Lain-Lain'))
                            ->textInput(['class' => $classForm]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'irama_jantung')
                            ->radioList(
                            [
                                '1' => 'Teratur',
                                '0' => 'Tidak Teratur',
                            ], ['inline' => true]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-top:20px;margin-bottom:20px;">Distensi Vena Jungularis</p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'vena_kanan')
                            ->label(Yii::t('fe', 'Kanan'))
                            ->radioList(
                            [
                                '1' => 'Ya',
                                '0' => 'Tidak',
                            ], ['inline' => true]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'vena_kiri')
                            ->label(Yii::t('fe', 'Kiri'))
                            ->radioList(
                            [
                                '1' => 'Ya',
                                '0' => 'Tidak',
                            ], ['inline' => true]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-top:20px;margin-bottom:20px;">3. Sistem Gastrointestinal </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'mulut')->checkboxList(
                            [
                                '1' => 'Mukosa Lembab',
                                '2' => 'Stomatitis',
                                '3' => 'Mukosa Kering',
                                '4' => 'Pendarahan Gusi',
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'gigi_palsu')->radioList(
                            [
                                '1' => 'Sisi Atas',
                                '2' => 'Sisi Bawah',
                            ], ['inline' => true]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'mual')
                            ->radioList(
                            [
                                '1' => 'Ya',
                                '0' => 'Tidak',
                            ], ['inline' => true]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'muntah')
                            ->radioList(
                            [
                                '1' => 'Ya',
                                '0' => 'Tidak',
                            ], ['inline' => true]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'abdomen')
                            ->radioList(
                            [
                                '1' => $labelNyeriTekan,
                                '0' => 'Tidak',
                            ], ['inline' => true]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'bising_usus')
                            ->radioList(
                            [
                                '1' => 'Terdengar',
                                '2' => 'Hiperaktif',
                                '3' => 'Tidak Ada/Hipoaktif',
                                '4' => 'Sangat Lambat',
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'asites')
                            ->radioList(
                            [
                                '1' => $labelAda,
                                '2' => $labelTidak,
                            ], ['inline' => true]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'lingkar_perut')
                            ->checkbox(); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'panjang_lingkar_perut', ['addon' => ['append' => ['content' => 'cm']]])
                            ->label(false)
                            ->textInput(['class' => $classFormNumber]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pembesaran_hepar')
                            ->radioList(
                            [
                                '1' => $labelAda,
                                '0' => $labelTidak,
                            ], ['inline' => true]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pembesaran_limfa')
                            ->radioList(
                            [
                                '1' => $labelAda,
                                '0' => $labelTidak,
                            ], ['inline' => true]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-top:20px;margin-bottom:20px;">4. Sistem Neurosensori </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pendengaran')
                            ->radioList(
                            [
                                '1' => 'Normal',
                                '0' => $labelTidakNormal,
                            ], ['inline' => true]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pendengaran_lainnya')
                            ->label(Yii::t('fe', 'Sebutkan'))
                            ->textInput(['class' => $classForm]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'penglihatan')
                            ->radioList(
                            [
                                '1' => 'Normal',
                                '0' => $labelTidakNormal,
                            ], ['inline' => true]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'penglihatan_lainnya')
                            ->label(Yii::t('fe', 'Sebutkan'))
                            ->textInput(['class' => $classForm]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'penciuman')
                            ->radioList(
                            [
                                '1' => 'Normal',
                                '0' => $labelTidakNormal,
                            ], ['inline' => true]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'penciuman_lainnya')
                            ->label(Yii::t('fe', 'Sebutkan'))
                            ->textInput(['class' => $classForm]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pupil_isokor')
                            ->radioList(
                            [
                                '1' => 'Ya',
                                '0' => 'Tidak',
                            ], ['inline' => true]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'lain_lain_neurosensori')
                            ->label(Yii::t('fe', 'Lain-Lain'))
                            ->textInput(['class' => $classForm]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-top:20px;margin-bottom:20px;">5. Sistem Eliminasi </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'defekasi')->checkboxList(
                            [
                                '1' => 'Via Anus',
                                '2' => 'Stoma',
                                '3' => 'Cytostomy',
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'frekuensi')
                            ->textInput(['class' => $classForm]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'konsistensi')
                            ->textInput(['class' => $classForm]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'urinal')->radioList(
                            [
                                '1' => 'Spontan',
                                '2' => 'Kateter Urine',
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'kelainan')
                            ->radioList(
                            [
                                '0' => $labelTidak,
                                '1' => $labelAda,
                            ], ['inline' => true]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'kelainan_lainnya')
                            ->label(Yii::t('fe', 'Sebutkan'))
                            ->textInput(['class' => $classForm]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pola_urine')
                            ->label(Yii::t('fe', 'Pola Eliminasi Urine'))
                            ->radioList(
                            [
                                '1' => 'Urgency',
                                '2' => 'Dysuria',
                                '3' => 'Polyuria',
                                '4' => 'Urinary Supression',
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'frekuensi_urine', ['addon' => ['append' => ['content' => 'cc']]])
                            ->textInput(['class' => $classFormNumber]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'masalah_urine')
                            ->label(Yii::t('fe', 'Masalah Kebutuhan Eliminasi Urine'))
                            ->radioList(
                            [
                                '1' => 'Retency Urine',
                                '2' => 'Inkontinensia Urine',
                                '3' => 'Enuresis',
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-top:20px;margin-bottom:20px;">6. Sistem Kulit dan Kelamin </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'warna_kulit')
                            ->radioList(
                            [
                                '1' => 'Normal',
                                '2' => 'Kuning',
                                '3' => 'Pucat',
                                '4' => 'Cokelat',
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'turgor_kulit')
                            ->radioList(
                            [
                                '1' => 'Elastis',
                                '0' => 'Tidak Elastis',
                            ], ['inline' => true]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'terdapat_luka')
                            ->radioList(
                            [
                                '1' => 'Ya',
                                '0' => 'Tidak',
                            ], ['inline' => true]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'lesi_primer')
                            ->checkboxList(
                            [
                                '1' => 'Makula',
                                '2' => 'Tumor',
                                '3' => 'Papula',
                                '4' => 'Vesikula',
                                '5' => 'Pustula',
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'lesi_sekunder')
                            ->checkboxList(
                            [
                                '1' => 'Ulkus',
                                '2' => 'Atrofia',
                            ], ['inline' => true]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'lokasi_luka')
                            ->label(Yii::t('fe', 'Lokasi Luka/Lesi Lain'))
                            ->textInput(['class' => $classForm]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'kondisi_kuku')
                            ->checkboxList(
                            [
                                '1' => 'Normal',
                                '2' => 'Clubbing',
                                '3' => 'Splinter Hemorrhages',
                                '4' => 'Koilonychia',
                                '5' => "Beau's Line",
                                '6' => 'Paronychia',
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'genitalia_laki')
                            ->label(Yii::t('fe', 'Genitalia Laki-Laki'))
                            ->checkboxList(
                            [
                                '1' => 'Normal',
                                '2' => 'Nyeri',
                                '3' => 'Hipospadia',
                                '4' => 'Ulkus',
                                '5' => 'Epispadia',
                                '6' => 'Hernia',
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'genitalia_perempuan')
                            ->checkboxList(
                            [
                                '1' => 'Normal',
                                '2' => 'Nyeri',
                                '3' => 'Lesi',
                                '4' => 'Ulkus',
                                '5' => 'Eritema',
                                '6' => 'Ekskoriasi',
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-top:20px;margin-bottom:20px;">7. Muskuloskeletal </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'kesulitan_pergerakan')
                            ->label(Yii::t('fe', 'Kesulitan Dalam Pergerakan'))
                            ->radioList(
                            [
                                '1' => 'Ya',
                                '0' => 'Tidak',
                            ], ['inline' => true]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'ukuran_otot')
                            ->radioList(
                            [
                                '1' => 'Normal',
                                '2' => 'Atrofi',
                                '3' => 'Hipertrofi',
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'gerakan_otot')
                            ->radioList(
                            [
                                '1' => 'Normal',
                                '0' => 'Kontraksi Abnormal',
                            ], ['inline' => true]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'keadaan_otot')
                            ->label(Yii::t('fe', 'Keadaan Tonus Otot'))
                            ->radioList(
                            [
                                '1' => 'Baik',
                                '2' => 'Atoni',
                                '3' => 'Hypotoni',
                                '4' => 'Hypertoni',
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'keadaan_tulang')
                            ->radioList(
                            [
                                '1' => 'Normal',
                                '2' => $labelNyeriTekan,
                                '3' => 'Deformitas',
                                '4' => 'Edema',
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'keadaan_sendi')
                            ->radioList(
                            [
                                '1' => 'Normal',
                                '2' => $labelNyeriTekan,
                                '3' => 'Bengkak',
                                '4' => 'Krepitasi',
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'lain_lain_muskuloskeletal')
                            ->label(Yii::t('fe', 'Lain-Lain'))
                            ->textInput(['class' => $classForm]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-top:20px;margin-bottom:20px;">8. Tidur dan Istirahat </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'lama_tidur', ['addon' => ['append' => ['content' => 'Jam']]])
                            ->textInput(['class' => $classForm]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'kesulitan_tidur')
                            ->radioList(
                            [
                                '1' => 'Ya',
                                '0' => 'Tidak',
                            ], ['inline' => true]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'kesulitan_tidur_lainnya')
                            ->label(Yii::t('fe', 'Jika Ya, Sebutkan'))
                            ->textInput(['class' => $classForm]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-top:20px;margin-bottom:20px;">9. Perawatan Diri </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'rambut')
                            ->radioList(
                            [
                                '1' => 'Bersih',
                                '2' => 'Kotor',
                            ], ['inline' => true]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'badan')
                            ->radioList(
                            [
                                '1' => 'Bersih',
                                '2' => 'Kotor',
                            ], ['inline' => true]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'gigi_mulut')
                            ->label(Yii::t('fe', 'Gigi dan Mulut'))
                            ->radioList(
                            [
                                '1' => 'Bersih',
                                '2' => 'Kotor',
                            ], ['inline' => true]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'keadaan_kuku')
                            ->radioList(
                            [
                                '1' => 'Bersih',
                                '2' => 'Kotor',
                            ], ['inline' => true]); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
var asites = "'.$model->asites.'"
var sputum = "'.$model->sputum.'"
function validateInput(input) {
    input.value = input.value.replace(/[^0-9/]/g, "");
    const parts = input.value.split("/");
    if (parts.length > 2 || (parts[0] && parts[0].length > 3) || (parts[1] && parts[1].length > 3)) {
        input.value = input.value.slice(0, -1);
    }
}

$(document).ready(function(){
    $("#tekanan_darah").on("input", function () {
        validateInput(this);
    });

    const checkedSputum = $("input[name=\'AnakForm[sputum]\']");
    const checkedSputumWarna = $("input[name=\'AnakForm[sputum_warna]\']");
    const checkedAsites = $("input[name=\'AnakForm[asites]\']");
    const checkedLP = $("input[name=\'AnakForm[lingkar_perut]\']");
    const checkedPendengaran = $("input[name=\'AnakForm[pendengaran]\']");
    const checkedPenglihatan = $("input[name=\'AnakForm[penglihatan]\']");
    const checkedPenciuman = $("input[name=\'AnakForm[penciuman]\']");
    const checkedKelainan = $("input[name=\'AnakForm[kelainan]\']");
    const checkedKesulitanTidur = $("input[name=\'AnakForm[kesulitan_tidur]\']");

    const inputLP = $("input[name=\'AnakForm[panjang_lingkar_perut]\']");
    const inputPendengaran = $("input[name=\'AnakForm[pendengaran_lainnya]\']");
    const inputPenglihatan = $("input[name=\'AnakForm[penglihatan_lainnya]\']");
    const inputPenciuman = $("input[name=\'AnakForm[penciuman_lainnya]\']");
    const inputKelainan = $("input[name=\'AnakForm[kelainan_lainnya]\']");
    const inputKesulitanTidur = $("input[name=\'AnakForm[kesulitan_tidur_lainnya]\']");
    
    checkedLP.prop("disabled", true);
    inputLP.prop("readonly", true);
    checkedSputumWarna.prop("disabled", true);

    if(asesmenMedisId) {
        if(asites == "1") {
            checkedLP.prop("disabled", false);
            inputLP.prop("readonly", false);
        }
        
        if(sputum == "1") {
            checkedSputumWarna.prop("disabled", false);
        }
    }
    
    inputPendengaran.prop("readonly", true);
    inputPenglihatan.prop("readonly", true);
    inputPenciuman.prop("readonly", true);
    inputKelainan.prop("readonly", true);
    inputKesulitanTidur.prop("readonly", true);

    checkedAsites.on("change", function(){
        if ($(this).is(":checked")) {
            if($(this).val() == "1") {
                checkedLP.prop("disabled", false);
                inputLP.prop("readonly", false);
            }
            else {
                checkedLP.prop("disabled", true);
                inputLP.val("").prop("readonly", true);
            }
        }
    })

    if(checkedPendengaran.is(":checked")) {
        if($("input[name=\'AnakForm[pendengaran]\']:checked").val() == "0") {
            inputPendengaran.prop("readonly", false);
        }
        else {
            inputPendengaran.val("").prop("readonly", true);
        }
    }
    if(checkedPenglihatan.is(":checked")) {
        if($("input[name=\'AnakForm[penglihatan]\']:checked").val() == "0") {
            inputPenglihatan.prop("readonly", false);
        }
        else {
            inputPenglihatan.val("").prop("readonly", true);
        }
    }
    if(checkedPenciuman.is(":checked")) {
        if($("input[name=\'AnakForm[penciuman]\']:checked").val() == "0") {
            inputPenciuman.prop("readonly", false);
        }
        else {
            inputPenciuman.val("").prop("readonly", true);
        }
    }
    if(checkedKelainan.is(":checked")) {
        if($("input[name=\'AnakForm[kelainan]\']:checked").val() == "1") {
            inputKelainan.prop("readonly", false);
        }
        else {
            inputKelainan.val("").prop("readonly", true);
        }
    }
    if(checkedKesulitanTidur.is(":checked")) {
        if($("input[name=\'AnakForm[kesulitan_tidur]\']:checked").val() == "1") {
            inputKesulitanTidur.prop("readonly", false);
        }
        else {
            inputKesulitanTidur.val("").prop("readonly", true);
        }
    }

    checkedPendengaran.on("change", function(){
        if ($(this).is(":checked")) {
            if($(this).val() == "0") {
                inputPendengaran.prop("readonly", false);
            }
            else {
                inputPendengaran.val("").prop("readonly", true);
            }
        }
    })
    checkedPenglihatan.on("change", function(){
        if ($(this).is(":checked")) {
            if($(this).val() == "0") {
                inputPenglihatan.prop("readonly", false);
            }
            else {
                inputPenglihatan.val("").prop("readonly", true);
            }
        }
    })
    checkedPenciuman.on("change", function(){
        if ($(this).is(":checked")) {
            if($(this).val() == "0") {
                inputPenciuman.prop("readonly", false);
            }
            else {
                inputPenciuman.val("").prop("readonly", true);
            }
        }
    })
    checkedKelainan.on("change", function(){
        if ($(this).is(":checked")) {
            if($(this).val() == "1") {
                inputKelainan.prop("readonly", false);
            }
            else {
                inputKelainan.val("").prop("readonly", true);
            }
        }
    })
    checkedKesulitanTidur.on("change", function(){
        if ($(this).is(":checked")) {
            if($(this).val() == "1") {
                inputKesulitanTidur.prop("readonly", false);
            }
            else {
                inputKesulitanTidur.val("").prop("readonly", true);
            }
        }
    })
    
    checkedSputum.on("change", function(){
        if ($(this).is(":checked")) {
            if($(this).val() == "1") {
                checkedSputumWarna.prop("disabled", false);
            }
            else {
                checkedSputumWarna.prop("checked", false);
                checkedSputumWarna.prop("disabled", true);
            }
        }
    })
})

', View::POS_END);
?>
