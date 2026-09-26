<?php
use kartik\date\DatePicker;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$classCenter = 'text-center';
$dateFormat = "yyyy-mm-dd";
$classFormNumber = 'form-control doco-number';
$styleTable = 'text-align:center;font-weight:bold;';
$styleCells = 'width:800px;margin-top:10px';
?>

<div class="row">
    <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;font-weight:bold;">Informasi Anak </p>
</div>
<div class="row" style="margin-bottom:10px;margin-left:15px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'nama')->textInput(['class' => 'form-control']); ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'alamat')->textInput(['class' => 'form-control']); ?>
    </div>
</div>
<div class="row" style="margin-bottom:10px;margin-left:15px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'noTelepon')->textInput(['class' => 'form-control']); ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'jenisKelamin')
            ->radioList(['Laki-Laki' => 'Laki-Laki', 'Perempuan' => 'Perempuan'], ['inline' => true]); ?>
    </div>
</div>
<div class="row" style="margin-bottom:10px;margin-left:15px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'diagnosaMedis')->textInput(['class' => 'form-control']); ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'dikirim')->textInput(['class' => 'form-control']); ?>
    </div>
</div>
<div class="row" style="margin-bottom:10px;margin-left:15px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'bangsa')->textInput(['class' => 'form-control']); ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'agama')->textInput(['class' => 'form-control']); ?>
    </div>
</div>
<div class="row" style="margin-bottom:10px;margin-left:15px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'tanggalLahir')->widget(DatePicker::classname(), [
            'options' => ['placeholder' => 'Tanggal Lahir'],
            'pluginOptions' => [
                'todayHighlight' => true,
                'autoclose' => true,
                'format' => $dateFormat,
                'orientation' => "bottom",
            ]
        ]); ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'beratBadan', ['addon' => ['append' => ['content' => 'Kg']]])
            ->textInput(['class' => $classFormNumber]); ?>
    </div>
</div>
<div class="row" style="margin-bottom:10px;margin-left:15px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'pbtb')->textInput(['class' => 'form-control']); ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'cukupBulan')->textInput(['class' => 'form-control']); ?>
    </div>
</div>
<div class="row" style="margin-bottom:10px;margin-left:15px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'dirumahrsrb')->textInput(['class' => 'form-control']); ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'susahBiasa')->textInput(['class' => 'form-control']); ?>
    </div>
</div>
<div class="row" style="margin-bottom:10px;margin-left:15px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'ditolongOleh')->textInput(['class' => 'form-control']); ?>
    </div>
    <div class="col-sm-3">
        <?= $form->field($model, 'anakKe')->textInput(['class' => 'form-control']); ?>
    </div>
    <div class="col-sm-3">
        <?= $form->field($model, 'dari', ['addon' => ['append' => ['content' => 'Anak']]])->textInput(['class' => 'form-control']); ?>
    </div>
</div>
<div class="row" style="margin-bottom:10px;margin-left:15px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'keguguran', ['addon' => ['append' => ['content' => 'Kali']]])->textInput(['class' => 'form-control']); ?>
    </div>
</div>
<br>
<div class="row">
    <div class="col-sm-12">
        <table style="width:100%" class="table-anak">
            <thead>
                <tr>
                    <th class="<?= $classCenter ?>" id="no">No</th>
                    <th class="<?= $classCenter ?>" id="sexHeader">Sex</th>
                    <th class="<?= $classCenter ?>" id="umurHeader">Umur (Tahun)</th>
                    <th class="<?= $classCenter ?>" id="sakitHeader">Sehat/Sakit Apa</th>
                    <th class="<?= $classCenter ?>" id="karenaHeader">Karena</th>
                    <th class="<?= $classCenter ?>" id="aksi">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr style="margin-bottom:5px;margin-top:30px;">
                    <td class="<?= $classCenter ?>">#</td>
                    <td class="<?= $classCenter ?>">
                        <input type="text" name="KesehatanAnakForm[sex][]"
                            class="form-control input-sm sex input-margin">
                    </td>
                    <td class="<?= $classCenter ?>">
                        <input type="text" name="KesehatanAnakForm[umur][]"
                            class="form-control input-sm umur input-margin">
                    </td>
                    <td class="<?= $classCenter ?>">
                        <input type="text" name="KesehatanAnakForm[sehatSakit][]"
                            class="form-control input-sm sehatSakit input-margin">
                    </td>
                    <td class="<?= $classCenter ?>">
                        <input type="text" name="KesehatanAnakForm[karena][]"
                            class="form-control input-sm karena input-margin">
                    </td>
                    <td style="text-align: center;width:15%;">
                        <button type="button" class="btn btn-success addRowAnak" name="addRowAnak"
                            id="addRowAnak">
                            <i class="fa fa-plus"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<br>
<div class="row" style="margin-bottom:10px;margin-left:15px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'riwayatPenyakit')->textInput(['class' => 'form-control']); ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'lamaPenyakit')->textArea(['class' => 'form-control']); ?>
    </div>
</div>
<div class="row" style="margin-bottom:10px;margin-left:15px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'berbalik', ['addon' => ['append' => ['content' => 'Bulan']]])->textInput(['class' => 'form-control']); ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'jalanSendiri', ['addon' => ['append' => ['content' => 'Bulan']]])->textInput(['class' => 'form-control']); ?>
    </div>
</div>
<div class="row" style="margin-bottom:10px;margin-left:15px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'gigiPertama', ['addon' => ['append' => ['content' => 'Bulan']]])->textInput(['class' => 'form-control']); ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'bicara', ['addon' => ['append' => ['content' => 'Bulan']]])->textInput(['class' => 'form-control']); ?>
    </div>
</div>
<div class="row" style="margin-bottom:10px;margin-left:15px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'duduk', ['addon' => ['append' => ['content' => 'Bulan']]])->textInput(['class' => 'form-control']); ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'makan', ['addon' => ['append' => ['content' => 'Bulan']]])->textInput(['class' => 'form-control']); ?>
    </div>
</div>
<div class="row" style="margin-bottom:10px;margin-left:15px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'berdiri', ['addon' => ['append' => ['content' => 'Bulan']]])->textInput(['class' => 'form-control']); ?>
    </div>
    <div class="col-sm-3">
        <?= $form->field($model, 'asi')
            ->radioList(['0' => 'Tidak', '1' => 'Pernah, sampai umur']); ?>
    </div>
    <div class="col-sm-3" style="margin-top: 20px;">
        <?= $form->field($model, 'sampaiUmur')->textInput(['class' => 'form-control sampaiUmur'])->label(false); ?>
    </div>
</div>
<div class="row" style="margin-bottom:10px;margin-left:15px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'masukBerapaKali', ['addon' => ['append' => ['content' => 'Kali']]])->textInput(['class' => 'form-control']); ?>
    </div>
</div>
<div class="row" style="margin-bottom:10px;margin-left:15px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'tanggalPeriksa')->textInput(['class' => 'form-control']); ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'jamPeriksa')->textInput(['class' => 'form-control']); ?>
    </div>
</div>
<div class="row" style="margin-bottom:10px;margin-left:15px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'tanggalMeninggal')->textInput(['class' => 'form-control']); ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'jamMeninggal')->textInput(['class' => 'form-control']); ?>
    </div>
</div>
<div class="row" style="margin-bottom:10px;margin-left:15px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'tanggalPulang')->textInput(['class' => 'form-control']); ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'jamPulang')->textInput(['class' => 'form-control']); ?>
    </div>
</div>
<div class="row" style="margin-bottom:10px;margin-left:15px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'kondisi')
            ->radioList(['1' => 'Sembuh', '2' => 'Tidak Sembuh', '3' => 'Permintaan', '4' => 'Pindah Ke']); ?>
    </div>
    <div class="col-sm-6" style="margin-top: 80px;">
        <?= $form->field($model, 'pindahKe')->textInput(['class' => 'form-control'])->label(false); ?>
    </div>
</div>
<div class="row">
    <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;">Dirawat Selama </p>
</div>
<div class="row" style="margin-bottom:10px;margin-left:15px;">
    <div class="col-sm-4">
        <?= $form->field($model, 'dirawatBulan', ['addon' => ['append' => ['content' => 'Bulan']]])->textInput(['class' => 'form-control'])->label(false); ?>
    </div>
    <div class="col-sm-4">
        <?= $form->field($model, 'dirawatHari', ['addon' => ['append' => ['content' => 'Hari']]])->textInput(['class' => 'form-control'])->label(false); ?>
    </div>
    <div class="col-sm-4">
        <?= $form->field($model, 'dirawatJam', ['addon' => ['append' => ['content' => 'Jam']]])->textInput(['class' => 'form-control'])->label(false); ?>
    </div>
</div>
<br>
<div class="row">
    <div class="col-sm-12">
        <table style="width:100%" class="table-vaksinasi">
            <thead>
                <tr>
                    <th class="<?= $classCenter ?>" id="vaksinasiHeader">Vaksinasi</th>
                    <th class="<?= $classCenter ?>" id="vaksinasi1Header">I</th>
                    <th class="<?= $classCenter ?>" id="vaksinasi2Header">II</th>
                    <th class="<?= $classCenter ?>" id="vaksinasi3Header">III</th>
                    <th class="<?= $classCenter ?>" id="vaksinasi4Header">IV</th>
                    <th class="<?= $classCenter ?>" id="vaksinasi5Header">V</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $vaccines = [
                    'BCG' => 'bcg',
                    'Polio' => 'polio',
                    'DPT' => 'dpt',
                    'Campak' => 'campak',
                    'Hepatitis B' => 'hepatitisb',
                    'Hepatitis A' => 'hepatitisa',
                    'Typhoid' => 'typhoid',
                    'MMR' => 'mmr'
                ];

                foreach ($vaccines as $vaccineName => $vaccineCode) {
                ?>
                    <tr style="margin-bottom:5px;margin-top:30px;">
                        <td><?= $vaccineName ?></td>
                        <?php for ($i = 1; $i <= 5; $i++) { ?>
                            <td class="<?= $classCenter ?>">
                                <?= $form->field($model, $vaccineCode . $i)
                                    ->checkbox(['class' => 'vaksinasi '.$vaccineCode . $i])
                                    ->label(false); ?>
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<br>
<div class="row">
    <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;">Pilih Diagnosa </p>
</div>
<div class="row">
    <div class="col-sm-12">
        <table style="width:100%" class="table-diagnosa">
            <thead>
                <tr>
                    <th class="<?= $classCenter ?>" id="diagnosaHeader">Diagnosa</th>
                    <th class="<?= $classCenter ?>" id="diagnosaKodeHeader">Kode</th>
                    <th class="<?= $classCenter ?>" id="aksiDiagnosa">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr style="margin-bottom:5px;margin-top:30px;">
                    <td style="width:50%;">
                    <?= $form->field($model, 'diagnosaNama[]')->widget(Select2::classname(), [
                            'options' => [
                                'placeholder' => '-- Pilih --',
                                'class' => 'form-control input-sm select2 selectDiagnosa',
                            ],
                            'pluginOptions' => [
                                // 'allowClear' => true,
                                'minimumInputLength' => 3,
                                'language' => [
                                    'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                                ],
                                'ajax' => [
                                    'url' => \yii\helpers\Url::to(['/ranap/end-point/get-new-diagnosa']),
                                    'dataType' => 'json',
                                    'data' => new JsExpression('
                                        function(params) {
                                            return {
                                                q: params.term,
                                                type: "diagnosa_utama",
                                                all_text: 1,
                                                id_with_text: 1,
                                            };
                                        }
                                    ')
                                ],
                                'escapeMarkup' => new JsExpression ('function(markup){ return markup;}'),
                                'templateResult' => new JsExpression ('function(diagnosa){ return diagnosa.text;}'),
                                'templateSelection' => new JsExpression ( 'function (subject) { return subject.text; }' ) ,
                            ],
                        ])->label(false) ?>
                    </td>
                    <td><span class="diagnosaKode"></span></td>
                    <td style="text-align: center;width:15%;">
                        <button style="margin-bottom:10px;" type="button" class="btn btn-success addRowDiagnosa" name="addRowDiagnosa"
                            id="addRowDiagnosa">
                            <i class="fa fa-plus"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<br>
<div class="row" style="margin-bottom:10px;margin-left:15px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'ringkasanPenyakit')->textArea(['class' => 'form-control']); ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'penyakitDiderita')->textArea(['class' => 'form-control']); ?>
    </div>
</div>
<br>
<div class="row">
    <div class="col-sm-12">
        <table style="width:100%" class="table-ortu">
            <thead>
                <tr>
                    <th class="<?= $classCenter ?>" id="informasiHeader">Informasi</th>
                    <th class="<?= $classCenter ?>" id="ayahHeader">Ayah</th>
                    <th class="<?= $classCenter ?>" id="ibuHeader">Ibu</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $fields = [
                'Nama' => ['namaAyah', 'namaIbu'],
                'Umur' => ['umurAyah', 'umurIbu'],
                'Pekerjaan' => ['pekerjaanAyah', 'pekerjaanIbu'],
            ];
            foreach ($fields as $label => $names) { ?>
                <tr style='margin-bottom:5px;margin-top:30px;'>
                    <td style="width:20%;"><?= $label ?></td>
                    <td class="<?= $classCenter ?>">
                        <?= $form->field($model, $names[0])->textInput(['class' => 'form-control input-sm'])->label(false); ?>
                    </td>
                    <td class="<?= $classCenter ?>">
                        <?= $form->field($model, $names[1])->textInput(['class' => 'form-control input-sm'])->label(false); ?>
                    </td>
                </tr>
            <?php }
            ?>
            </tbody>
        </table>
    </div>
</div>
<br>
<div class="row" style="margin-bottom:10px;margin-left:15px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'kesehatanAyah')->textInput(['class' => 'form-control']); ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'kesehatanIbu')->textInput(['class' => 'form-control']); ?>
    </div>
</div>
<div class="row" style="margin-bottom:10px;margin-left:15px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'kesehatanKeluargaLain')->textInput(['class' => 'form-control']); ?>
    </div>
</div>
