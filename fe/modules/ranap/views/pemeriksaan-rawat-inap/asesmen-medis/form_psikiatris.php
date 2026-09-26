<?php

use app\components\DocoHelpers;
use yii\web\View;
use yii\helpers\Html;
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

<?php
$form = ActiveForm::begin([
    'enableClientValidation' => false,
    'enableAjaxValidation' => false,
    'id' => 'form-asmed-ranap',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL],
]);

$formControl = 'form-control input-sm';
$classNumber = 'doco-number';
$labelTidakAda = 'Tidak Ada';
$labelAda = 'Ada';
$labelBantuanMinimal = 'Bantuan Minimal';
$labelBantuanTotal = 'Bantuan Total';
?>

<h1 style="text-align:center;"><?= $title ?></h1>
<hr style="margin-top:15px;">

<div class="col-md-12 col-header">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">I. Asesmen Keperawatan</h5>
        </div>
        <div class="panel-body">
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'status_kesehatan')
                            ->textInput([
                                'class' => $formControl,
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'keluhan_utama')
                            ->textInput([
                                'class' => $formControl,
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'riwayat_penyakit_sekarang')
                            ->checkboxList(
                                [
                                    'Memori' => 'Gangguan Memori',
                                    'Orientasi' => 'Gangguan Orientasi',
                                    'Emosi' => 'Gangguan Emosi',
                                    'Afek' => 'Gangguan Afek',
                                    'Lainnya' => 'Lainnya',
                                ],
                                []
                            ); ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'riwayat_penyakit_sekarang_lainnya')
                            ->label(false)
                            ->textInput(['class' => $formControl, 'placeholder' => 'Riwayat Penyakit Sekarang Lainnya']); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'riwayat_penyakit_dahulu')
                            ->checkboxList(
                                [
                                    'Mental/Emosi' => 'Gangguan Mental/Emosi',
                                    'Psikosomatik' => 'Gangguan Psikosomatik',
                                    'Medis' => 'Gangguan Medis',
                                    'Neurologis' => 'Gangguan Neurologis',
                                    'Lainnya' => 'Lainnya',
                                ],
                                []
                            ); ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'riwayat_penyakit_dahulu_lainnya')
                            ->label(false)
                            ->textInput(['class' => $formControl, 'placeholder' => 'Riwayat Penyakit Dahulu Lainnya']); ?>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-bottom:20px;">Riwayat Keturunan/Keluarga </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'gangguan_jiwa')
                            ->label(Yii::t('fe', 'a. Gangguan jiwa yang diderita keluarga'))
                            ->radioList(
                                [
                                    '0' => 'Tidak Ada',
                                    '1' => 'Ada',
                                ],
                                ['inline' => true]
                            ); ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'gangguan_jiwa_lainnya')
                            ->label(false)
                            ->textInput(['class' => $formControl]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'penganiayaan_fisik')
                            ->label(Yii::t('fe', 'b. Penganiayaan fisik anggota keluarga'))
                            ->radioList(
                                [
                                    '0' => 'Tidak Ada',
                                    '1' => 'Ada',
                                ],
                                ['inline' => true]
                            ); ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'penganiayaan_fisik_lainnya')
                            ->label(false)
                            ->textInput(['class' => $formControl]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'penganiayaan_seksual')
                            ->label(Yii::t('fe', 'c. Penganiayaan seksual anggota keluarga'))
                            ->radioList(
                                [
                                    '0' => 'Tidak Ada',
                                    '1' => 'Ada',
                                ],
                                ['inline' => true]
                            ); ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'penganiayaan_seksual_lainnya')
                            ->label(false)
                            ->textInput(['class' => $formControl]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'kekerasan')
                            ->label(Yii::t('fe', 'd. Kekerasan dalam keluarga'))
                            ->radioList(
                                [
                                    '0' => 'Tidak Ada',
                                    '1' => 'Ada',
                                ],
                                ['inline' => true]
                            ); ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'kekerasan_lainnya')
                            ->label(false)
                            ->textInput(['class' => $formControl]); ?>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-bottom:20px;">Riwayat Psikososial </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'gangguan_konsep')
                            ->label(Yii::t('fe', 'a. Gangguan konsep diri'))
                            ->checkboxList(
                                [
                                    'Citra Tubuh' => 'Citra Tubuh',
                                    'Ideal' => 'Ideal Diri',
                                    'Identitas' => 'Identitas Diri',
                                    'Harga' => 'Harga Diri',
                                    'Peran' => 'Peran Diri',
                                ],
                                []
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'gangguan_sosial')
                            ->label(Yii::t('fe', 'b. Gangguan hubungan sosial'))
                            ->radioList(
                                [
                                    '0' => 'Tidak Ada',
                                    '1' => 'Ada',
                                ],
                                ['inline' => true]
                            ); ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'gangguan_sosial_lainnya')
                            ->label(false)
                            ->textInput(['class' => $formControl]); ?>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-bottom:20px;">Status Mental </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'penampilan')
                            ->label(Yii::t('fe', 'a. Penampilan'))
                            ->checkboxList(
                                [
                                    'Tidak Rapi' => 'Tidak Rapi',
                                    'Cara berpakaian tidak seperti biasanya' => 'Cara berpakaian tidak seperti biasanya',
                                    'Penggunaan pakaian tidak sesuai' => 'Penggunaan pakaian tidak sesuai',
                                ],
                                []
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pembicaraan')
                            ->label(Yii::t('fe', 'b. Pembicaraan'))
                            ->checkboxList(
                                [
                                    'Cepat' => 'Cepat',
                                    'Apatis' => 'Apatis',
                                    'Membisu' => 'Membisu',
                                    'Keras' => 'Keras',
                                    'Lembut' => 'Lembut',
                                    'Tidak Mampu Memulai' => 'Tidak Mampu Memulai',
                                    'Gagap' => 'Gagap',
                                    'Inkohoren' => 'Inkohoren',
                                ],
                                []
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'aktifitas_motorik')
                            ->label(Yii::t('fe', 'c. Aktifitas Motorik'))
                            ->checkboxList(
                                [
                                    'Lesu' => 'Lesu',
                                    'Tik' => 'Tik',
                                    'Tegang' => 'Tegang',
                                    'Grimasen' => 'Grimasen',
                                    'Gelisah' => 'Gelisah',
                                    'Tremor' => 'Tremor',
                                    'Agitasi' => 'Agitasi',
                                    'Kompulsif' => 'Kompulsif',
                                ],
                                []
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'alam_perasaan')
                            ->label(Yii::t('fe', 'd. Alam Perasaan'))
                            ->checkboxList(
                                [
                                    'Sedih' => 'Sedih',
                                    'Gembira Berlebihan' => 'Gembira Berlebihan',
                                    'Ketakutan' => 'Ketakutan',
                                    'Khawatir' => 'Khawatir',
                                    'Putus Asa' => 'Putus Asa',
                                    'Lainnya' => 'Lainnya',
                                ],
                                []
                            ); ?>
                        </div>
                        <div class="col-sm-4">
                            <?= $form->field($model, 'alam_perasaan_lainnya')
                            ->label(false)
                            ->textInput(['class' => $formControl]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'afek')
                            ->label(Yii::t('fe', 'e. Afek'))
                            ->checkboxList(
                                [
                                    'Datar' => 'Datar',
                                    'Tumpul' => 'Tumpul',
                                    'Labil' => 'Labil',
                                    'Tidak Sesuai' => 'Tidak Sesuai',
                                ],
                                []
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'interaksi')
                            ->label(Yii::t('fe', 'f. Interaksi Selama Wawancara'))
                            ->checkboxList(
                                [
                                    'Bermusuhan' => 'Bermusuhan',
                                    'Curiga' => 'Curiga',
                                    'Kontak Mata' => 'Kontak Mata',
                                    'Mudah Tersinggung' => 'Mudah Tersinggung',
                                    'Defensif' => 'Defensif',
                                    'Tidak Kooperatif' => 'Tidak Kooperatif',
                                ],
                                []
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'persepsi')
                            ->label(Yii::t('fe', 'g. Persepsi (Halusinasi)'))
                            ->checkboxList(
                                [
                                    'Pendengaran' => 'Pendengaran',
                                    'Pengecapan' => 'Pengecapan',
                                    'Penglihatan' => 'Penglihatan',
                                    'Penciuman' => 'Penciuman',
                                    'Perabaan' => 'Perabaan',
                                ],
                                []
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'proses_pikir')
                            ->label(Yii::t('fe', 'h. Proses Pikir'))
                            ->checkboxList(
                                [
                                    'Sirkumstansial' => 'Sirkumstansial',
                                    'Kehilangan Asosiasi' => 'Kehilangan Asosiasi',
                                    'Tangensial' => 'Tangensial',
                                    'Blocking' => 'Blocking',
                                    'Flight of idea' => 'Flight of idea',
                                    'Pengulangan Pembicaraan' => 'Pengulangan Pembicaraan',
                                ],
                                []
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'isi_pikir')
                            ->label(Yii::t('fe', 'i. Isi Pikir'))
                            ->checkboxList(
                                [
                                    'Obsesi' => 'Obsesi',
                                    'Ide Yang Terkait' => 'Ide Yang Terkait',
                                    'Fobia' => 'Fobia',
                                    'Pikiran Magic' => 'Pikiran Magic',
                                    'Hipokondria' => 'Hipokondria',
                                    'Dipersonalisasi' => 'Dipersonalisasi',
                                ],
                                []
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'tingkat_kesadaran')
                            ->label(Yii::t('fe', 'j. Tingkat Kesadaran'))
                            ->checkboxList(
                                [
                                    'Bingung' => 'Bingung',
                                    'Sedasi' => 'Sedasi',
                                    'Stupor' => 'Stupor',
                                ],
                                []
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'disorientasi')
                            ->label(Yii::t('fe', 'k. Disorientasi'))
                            ->checkboxList(
                                [
                                    'Waktu' => 'Waktu',
                                    'Tempat' => 'Tempat',
                                    'Orang' => 'Orang',
                                ],
                                []
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'memori')
                            ->label(Yii::t('fe', 'l. Memori'))
                            ->checkboxList(
                                [
                                    'Panjang' => 'Gangguan Daya Ingat Jangka Panjang',
                                    'Saat Ini' => 'Gangguan Daya Saat Ini',
                                    'Pendek' => 'Gangguan Daya Ingat Jangka Pendek',
                                    'Kofabulasi' => 'Kofabulasi',
                                ],
                                []
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'tingkat_konsentrasi')
                            ->label(Yii::t('fe', 'm. Tingkat Konsentrasi dan Berhitung'))
                            ->checkboxList(
                                [
                                    'Mudah Beralih' => 'Mudah Beralih',
                                    'Sederhana' => 'Tidak Mampu Berhitung Sederhana',
                                    'Berkonsentrasi' => 'Tidak Mampu Berkonsentrasi',
                                ],
                                []
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'kemampuan_penilaian')
                            ->label(Yii::t('fe', 'n. Kemampuan Penilaian'))
                            ->checkboxList(
                                [
                                    'Ringan' => 'Gangguan Ringan',
                                    'Bermakna' => 'Gangguan Bermakna',
                                ],
                                []
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'daya_tilik')
                            ->label(Yii::t('fe', 'o. Daya Tilik Diri'))
                            ->checkboxList(
                                [
                                    'Mengingkari' => 'Mengingkari Penyakit Yang di Derita',
                                    'Menyalahkan' => 'Menyalahkan Hal Diluar Dirinya',
                                ],
                                []
                            ); ?>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-bottom:20px;">Keadaan Umum </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'tinggi_badan', ['addon' => ['append' => ['content' => 'cm']]])
                                ->textInput(
                                    [
                                        'class' => $formControl.' '.$classNumber,
                                    ]
                                ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'berat_badan', ['addon' => ['append' => ['content' => 'kg']]])
                                ->textInput(
                                    [
                                        'class' => $formControl.' '.$classNumber,
                                    ]
                                ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'bb_naik')
                            ->label(false)
                            ->radioList(
                                [
                                    'Turun' => 'Turun',
                                    'Naik' => 'Naik',
                                ],
                                ['inline' => true]
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'keluhan_fisik')
                            ->radioList(
                                [
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak',
                                ],
                                ['inline' => true]
                            ); ?>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-bottom:20px;">Tanda-Tanda Vital </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'tekanan_darah', ['addon' => ['append' => ['content' => 'mmHg']]])
                            ->textInput(
                                [
                                    'class' => $formControl,
                                    'id' => 'tekanan_darah',
                                    'pattern' => '^\\d{1,3}/\\d{1,3}$',
                                    'oninput' => "validateInput(this)",
                                ]
                            )->hint('Format tekanan darah : XXX/XXX, contoh: 120/80.'); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'frekuensi_nadi', ['addon' => ['append' => ['content' => 'x/Menit']]])
                            ->textInput(
                                [
                                    'class' => $formControl.' '.$classNumber,
                                ]
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'frekuensi_nafas', ['addon' => ['append' => ['content' => 'x/Menit']]])
                                ->textInput(
                                    [
                                        'class' => $formControl.' '.$classNumber,
                                    ]
                                ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'suhu_tubuh', ['addon' => ['append' => ['content' => '°C']]])
                                ->label(Yii::t('fe', 'Suhu Badan'))
                                ->textInput(
                                    [
                                        'class' => $formControl.' doco-decimal-wcomma',
                                    ]
                                ); ?>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-bottom:20px;">Pemenuhan Kebutuhan Aktifitas Hidup Sehari-Hari (ADL)</p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'adl_makan_minum')
                            ->label(Yii::t('fe', 'a. Makan/Minum'))
                            ->radioList(
                                [
                                    $labelBantuanMinimal => $labelBantuanMinimal,
                                    $labelBantuanTotal => $labelBantuanTotal,
                                ],
                                ['inline' => true]
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'adl_mandi')
                            ->label(Yii::t('fe', 'b. Mandi'))
                            ->radioList(
                                [
                                    $labelBantuanMinimal => $labelBantuanMinimal,
                                    $labelBantuanTotal => $labelBantuanTotal,
                                ],
                                ['inline' => true]
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'adl_bab')
                            ->label(Yii::t('fe', 'c. BAB/BAK'))
                            ->radioList(
                                [
                                    $labelBantuanMinimal => $labelBantuanMinimal,
                                    $labelBantuanTotal => $labelBantuanTotal,
                                ],
                                ['inline' => true]
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'adl_berpakaian')
                            ->label(Yii::t('fe', 'd. Berpakaian/Berhias'))
                            ->radioList(
                                [
                                    $labelBantuanMinimal => $labelBantuanMinimal,
                                    $labelBantuanTotal => $labelBantuanTotal,
                                ],
                                ['inline' => true]
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'adl_istirahat')
                            ->label(Yii::t('fe', 'e. Istirahat dan Tidur'))
                            ->radioList(
                                [
                                    $labelBantuanMinimal => $labelBantuanMinimal,
                                    $labelBantuanTotal => $labelBantuanTotal,
                                ],
                                ['inline' => true]
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'adl_obat')
                            ->label(Yii::t('fe', 'f. Penggunaan Obat'))
                            ->radioList(
                                [
                                    $labelBantuanMinimal => $labelBantuanMinimal,
                                    $labelBantuanTotal => $labelBantuanTotal,
                                ],
                                ['inline' => true]
                            ); ?>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-bottom:20px;">Mekanisme Koping </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'adaptif')
                            ->checkboxList(
                                [
                                    'Bicara Dengan Orang Lain' => 'Bicara Dengan Orang Lain',
                                    'Mampu Menyelesaikan Masalah' => 'Mampu Menyelesaikan Masalah',
                                    'Aktifasi Konstruktif' => 'Aktifasi Konstruktif',
                                    'Olahraga' => 'Olahraga',
                                    'Teknik Relokasi' => 'Teknik Relokasi'
                                ],
                                []
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'maladaptif')
                            ->checkboxList(
                                [
                                    'Minum Alkohol' => 'Minum Alkohol',
                                    'Reaksi Lambat dan Berlebih' => 'Reaksi Lambat dan Berlebih',
                                    'Menghindar' => 'Menghindar',
                                    'Mencederai Diri' => 'Mencederai Diri',
                                    'Bekerja Berlebihan' => 'Bekerja Berlebihan'
                                ],
                                []
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'diagnosa_keperawatan')
                            ->textArea(['class' => $formControl, 'rows' => 5]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'rencana_keperawatan')
                            ->label(Yii::t('fe', 'Rencana Keperawatan dan Tindakan'))
                            ->textArea(['class' => $formControl, 'rows' => 5]); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-md-12 col-header">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">II. Asesmen Medis</h5>
        </div>
        <div class="panel-body">
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pengkajian_sistem')
                            ->textArea(
                                [
                                    'class' => $formControl,
                                    'rows' => 5,
                                ]
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'saran_dokter')
                            ->label(Yii::t('fe', 'Saran/Nasehat Dokter'))
                            ->textArea(['class' => $formControl, 'rows' => 5]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="row">
                                <p style="font-weight:bold;margin-left:20px;margin-bottom:20px;"> Data Penunjang </p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'laboratorium')
                            ->label(Yii::t('fe', 'a. Laboratorium'))
                            ->textInput(['class' => $formControl]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'radiologi')
                            ->label(Yii::t('fe', 'b. Radiologi'))
                            ->textInput(['class' => $formControl]); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
<?php
$this->registerJs(
    '
    var isDokumenEklaim = "' . $isDokumenEklaim . '";
    function validateInput(input) {
        input.value = input.value.replace(/[^0-9/]/g, "");
        const parts = input.value.split("/");
        if (parts.length > 2 || (parts[0] && parts[0].length > 3) || (parts[1] && parts[1].length > 3)) {
            input.value = input.value.slice(0, -1);
        }
    }

    function toggleInput(checkbox, input) {
        if (checkbox.is(":checked")) {
            input.prop("readonly", false);
        } else {
            input.val("").prop("readonly", true);
        }
    }

    $(document).ready(function () {
        $("#tekanan_darah").on("input", function () {
            validateInput(this);
        });

        // checkbox
        const checkboxRPS = $("input[name=\'PsikiatrisForm[riwayat_penyakit_sekarang][]\'][value=\'Lainnya\']");
        const checkboxRPD = $("input[name=\'PsikiatrisForm[riwayat_penyakit_dahulu][]\'][value=\'Lainnya\']");
        const checkboxAP = $("input[name=\'PsikiatrisForm[alam_perasaan][]\'][value=\'Lainnya\']");
        const inputRPS = $("#psikiatrisform-riwayat_penyakit_sekarang_lainnya");
        const inputRPD = $("#psikiatrisform-riwayat_penyakit_dahulu_lainnya");
        const inputAP = $("#psikiatrisform-alam_perasaan_lainnya");

        toggleInput(checkboxRPS, inputRPS);
        toggleInput(checkboxRPD, inputRPD);
        toggleInput(checkboxAP, inputAP);

        checkboxRPS.on("change", function () {
            toggleInput($(this), inputRPS);
        });
        checkboxRPD.on("change", function () {
            toggleInput($(this), inputRPD);
        });
        checkboxAP.on("change", function () {
            toggleInput($(this), inputAP);
        });
        
        // radio
        const checkboxGJ = $("input[name=\'PsikiatrisForm[gangguan_jiwa]\']");
        const checkboxPF = $("input[name=\'PsikiatrisForm[penganiayaan_fisik]\']");
        const checkboxPS = $("input[name=\'PsikiatrisForm[penganiayaan_seksual]\']");
        const checkboxGS = $("input[name=\'PsikiatrisForm[gangguan_sosial]\']");
        const checkboxKekerasan = $("input[name=\'PsikiatrisForm[kekerasan]\']");

        const inputGJ = $("#psikiatrisform-gangguan_jiwa_lainnya");
        const inputPF = $("#psikiatrisform-penganiayaan_fisik_lainnya");
        const inputPS = $("#psikiatrisform-penganiayaan_seksual_lainnya");
        const inputKekerasan = $("#psikiatrisform-kekerasan_lainnya");
        const inputGS = $("#psikiatrisform-gangguan_sosial_lainnya");

        if(checkboxGJ.is(":checked")) {
            if($("input[name=\'PsikiatrisForm[gangguan_jiwa]\']:checked").val() == "1") {
                inputGJ.prop("readonly", false);
            }
            else {
                inputGJ.val("").prop("readonly", true);
            }
        }
        if(checkboxPF.is(":checked")) {
            if($("input[name=\'PsikiatrisForm[penganiayaan_fisik]\']:checked").val() == "1") {
                inputPF.prop("readonly", false);
            }
            else {
                inputPF.val("").prop("readonly", true);
            }
        }
        if(checkboxPS.is(":checked")) {
            if($("input[name=\'PsikiatrisForm[penganiayaan_seksual]\']:checked").val() == "1") {
                inputPS.prop("readonly", false);
            }
            else {
                inputPS.val("").prop("readonly", true);
            }
        }
        if(checkboxGS.is(":checked")) {
            if($("input[name=\'PsikiatrisForm[gangguan_sosial]\']:checked").val() == "1") {
                inputGS.prop("readonly", false);
            }
            else {
                inputGS.val("").prop("readonly", true);
            }
        }
        if(checkboxKekerasan.is(":checked")) {
            if($("input[name=\'PsikiatrisForm[kekerasan]\']:checked").val() == "1") {
                inputKekerasan.prop("readonly", false);
            }
            else {
                inputKekerasan.val("").prop("readonly", true);
            }
        }

        checkboxGJ.on("change", function(){
            if ($(this).is(":checked")) {
                if($(this).val() == 1) {
                    inputGJ.prop("readonly", false);
                }
                else {
                    inputGJ.val("").prop("readonly", true);
                }
            }
        })
        checkboxPF.on("change", function(){
            if ($(this).is(":checked")) {
                if($(this).val() == 1) {
                    inputPF.prop("readonly", false);
                }
                else {
                    inputPF.val("").prop("readonly", true);
                }
            }
        })
        checkboxPS.on("change", function(){
            if ($(this).is(":checked")) {
                if($(this).val() == 1) {
                    inputPS.prop("readonly", false);
                }
                else {
                    inputPS.val("").prop("readonly", true);
                }
            }
        })
        checkboxGS.on("change", function(){
            if ($(this).is(":checked")) {
                if($(this).val() == 1) {
                    inputGS.prop("readonly", false);
                }
                else {
                    inputGS.val("").prop("readonly", true);
                }
            }
        })
        checkboxKekerasan.on("change", function(){
            if ($(this).is(":checked")) {
                if($(this).val() == 1) {
                    inputKekerasan.prop("readonly", false);
                }
                else {
                    inputKekerasan.val("").prop("readonly", true);
                }
            }
        })
    });'
);
$this->registerJs($this->render('js/asmed-ranap.js'), View::POS_END);
?>
