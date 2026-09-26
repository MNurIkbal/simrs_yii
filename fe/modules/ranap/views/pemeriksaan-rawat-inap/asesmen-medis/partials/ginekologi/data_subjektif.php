<?php
use yii\web\View;
use kartik\date\DatePicker;

$classFormNumber = 'form-control doco-number';
$classForm = 'form-control input-sm';
$styleTable = 'text-align:center;font-weight:bold;';
$classCenter = 'text-center';
$styleCells = 'margin-top:10px;margin-bottom:10px;';
$dateFormat = "yyyy-mm-dd";

?>
<style>
.box-scale {
    margin-top: 10px;
    padding-top: 10px;
    padding-bottom: 10px;
}

.box-scale-header {
    margin-bottom: 3px !important;
}

table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
  padding: 5px;
}
</style>
<div class="row" style="margin-top:15px;">
    <div class="col-md-12 form-group">
        <div class="col-sm-6">
            <?= $form->field($model, 'sumber_data')->radioList(
                ['1' => 'Pasien', '2' => 'Keluarga Terdekat', '3' => 'Lain-Lain'],
                [
                    'itemOptions' => [
                        'class' => 'sumber_data'
                    ],
                    'inline' => 'true',
                ]); ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'sumber_data_lainnya')->label(false)->textInput(['class' => $classForm]); ?>
        </div>
    </div>
</div>

<div class="col-md-12 col-header">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">A. Data Subyektif</h5>
        </div>
        <div class="panel-body">
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'keluhan_utama')->label(Yii::t('fe', '1. Keluhan Utama'))
                        ->textInput(['class' => $classForm]); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;">2. Riwayat Menstruasi </p>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'umur_menarche', ['addon' => ['append' => ['content' => 'Tahun']]])
                        ->textInput(['class' => $classFormNumber]); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'lama_haid', ['addon' => ['append' => ['content' => 'Hari']]])
                        ->label(Yii::t('fe', 'Lamanya Haid'))->textInput(['class' => $classFormNumber]); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'jumlah_haid', ['addon' => ['append' => ['content' => 'ml']]])
                        ->label(Yii::t('fe', 'Jumlah Darah Haid'))->textInput(['class' => $classFormNumber]); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'haid_terakhir')->widget(DatePicker::classname(), [
                            'options' => ['placeholder' => 'Haid Terakhir'],
                            'pluginOptions' => [
                                'todayHighlight' => true,
                                'autoclose' => true,
                                'format' => $dateFormat
                            ]
                        ]); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'perkiraan_partus')->widget(DatePicker::classname(), [
                            'options' => ['placeholder' => 'Perkiraan Partus'],
                            'pluginOptions' => [
                                'todayHighlight' => true,
                                'autoclose' => true,
                                'format' => $dateFormat
                            ]
                        ]); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-9">
                        <?= $form->field($model, 'riwayat_mens')->label(false)->checkboxList(
                            [
                                '1' => 'Dismonorroe',
                                '2' => 'Spoting',
                                '3' => 'Menorragia',
                                '4' => 'Metroragia',
                                '5' => 'Pra Menstruasi Syndrom',
                            ], ['inline' => true]); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;">3. Riwayat Perkawinan </p>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'kawin', ['addon' => ['append' => ['content' => 'Kali']]])
                        ->textInput(['class' => $classFormNumber]); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'suami1', ['addon' => ['append' => ['content' => 'Tahun']]])
                        ->label(Yii::t('fe', 'Dengan Suami I'))->textInput(['class' => $classFormNumber]); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'umur_kawin', ['addon' => ['append' => ['content' => 'Tahun']]])
                        ->label(Yii::t('fe', 'Umur'))->textInput(['class' => $classFormNumber]); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'suami2', ['addon' => ['append' => ['content' => 'Tahun']]])
                        ->label(Yii::t('fe', 'Suami ke II'))->textInput(['class' => $classFormNumber]); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;">4. Riwayat Kehamilan, Persalinan dan Nifas Yang Lalu </p>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'riwayat_hamil_g')
                        ->label(Yii::t('fe', 'G'))->textInput(['class' => $classForm]); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'riwayat_hamil_p')
                        ->label(Yii::t('fe', 'P'))->textInput(['class' => $classForm]); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'riwayat_hamil_a')
                        ->label(Yii::t('fe', 'A'))->textInput(['class' => $classForm]); ?>
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-sm-12">
                    <table style="width:100%" class="table-kehamilan">
                        <thead>
                            <tr>
                                <th style="<?= $styleTable?>" id="header_kehamilan1">No</th>
                                <th style="<?= $styleTable?>" id="header_kehamilan2">Tanggal, Tahun Partus</th>
                                <th style="<?= $styleTable?>" id="header_kehamilan3">Tempat Partus</th>
                                <th style="<?= $styleTable?>" id="header_kehamilan4">Umur Hamil</th>
                                <th style="<?= $styleTable?>" id="header_kehamilan5">Jenis Persalinan</th>
                                <th style="<?= $styleTable?>" id="header_kehamilan6">Penolong Persalinan</th>
                                <th style="<?= $styleTable?>" id="header_kehamilan7">Penyulit</th>
                                <th style="<?= $styleTable?>" id="header_kehamilan8">BB Anak</th>
                                <th style="<?= $styleTable?>" id="header_kehamilan9">Keadaan Anak Sekarang</th>
                                <th style="<?= $styleTable?>" id="header_kehamilan10">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="margin-bottom:5px;margin-top:30px;">
                                <td class="<?=$classCenter?>">#</td>
                                <td class="<?=$classCenter?>">
                                    <?= DatePicker::widget([
                                    'name' => 'GinekologiForm[tanggal_partus][]',
                                    'type' => DatePicker::TYPE_INPUT,
                                    'readonly' => true,
                                    'options' => ['class' => 'tanggal_partus', 'id' => 'tanggal_partus_0'],
                                    'pluginOptions' => [
                                        'todayHighlight' => true,
                                        'autoclose' => true,
                                        'format' => $dateFormat,
                                        'orientation' => 'bottom'
                                    ]
                                ]); ?>
                                </td>
                                <td class="<?=$classCenter?>">
                                    <input type="text" name="GinekologiForm[tempat_partus][]" class="form-control input-sm tempat_partus" style="<?=$styleCells?>">
                                </td>
                                <td class="<?=$classCenter?>">
                                    <input type="text" name="GinekologiForm[umur_hamil][]" class="form-control input-sm umur_hamil" style="<?=$styleCells?>">
                                </td>
                                <td class="<?=$classCenter?>">
                                    <input type="text" name="GinekologiForm[jenis_persalinan][]" class="form-control input-sm jenis_persalinan" style="<?=$styleCells?>">
                                </td>
                                <td class="<?=$classCenter?>">
                                    <input type="text" name="GinekologiForm[penolong_persalinan][]" class="form-control input-sm penolong_persalinan" style="<?=$styleCells?>">
                                </td>
                                <td class="<?=$classCenter?>">
                                    <input type="text" name="GinekologiForm[penyulit][]" class="form-control input-sm penyulit" style="<?=$styleCells?>">
                                </td>
                                <td class="<?=$classCenter?>">
                                    <input type="text" name="GinekologiForm[bb_anak][]" class="form-control input-sm bb_anak" style="<?=$styleCells?>">
                                </td>
                                <td class="<?=$classCenter?>">
                                    <input type="text" name="GinekologiForm[keadaan_anak][]" class="form-control input-sm keadaan_anak" style="<?=$styleCells?>">
                                </td>
                                <td style="text-align: center;width:5%;">
                                    <button type="button" class="btn btn-success addRowKehamilan" name="addRowKehamilan"
                                        id="addRowKehamilan">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;">5. Riwayat Hamil Ini </p>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'hamil_muda')->checkboxList(
                            [
                                '1' => 'Mual',
                                '2' => 'Muntah',
                                '3' => 'Perdarahan',
                                '4' => 'Lain-Lain',
                            ],
                            [
                            'itemOptions' => [
                                'class' => 'hamil_muda'
                            ],
                            'inline' => 'true',
                        ]); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'hamil_muda_lainnya')->label(false)->textInput(['class' => $classForm]); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'hamil_tua')->checkboxList(
                            [
                                '1' => 'Pusing',
                                '2' => 'Sakit Kepala',
                                '3' => 'Perdarahan',
                                '4' => 'Lain-Lain',
                            ],
                            [
                            'itemOptions' => [
                                'class' => 'hamil_tua'
                            ],
                            'inline' => 'true',
                        ]); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'hamil_tua_lainnya')->label(false)->textInput(['class' => $classForm]); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;">6. Riwayat Penyakit Yang Lalu/Operasi </p>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'pernah_dirawat')->textInput(['class' => $classForm]); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'kapan_dirawat')->label(Yii::t('fe', 'Kapan'))->textInput(['class' => $classForm]); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'dimana_dirawat')->label(Yii::t('fe', 'Dimana'))->textInput(['class' => $classForm]); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'pernah_dioperasi')->textInput(['class' => $classForm]); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'kapan_dioperasi')->label(Yii::t('fe', 'Kapan'))->textInput(['class' => $classForm]); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'dimana_dioperasi')->label(Yii::t('fe', 'Dimana'))->textInput(['class' => $classForm]); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;">7. Riwayat Penyakit Keluarga (Ayah, Ibu, Adik, Kakak, Paman, Bibi) Yang Pernah Menderita Sakit</p>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'riwayat_penyakit_keluarga')->label(false)->checkboxList(
                            [
                                '1' => 'Kanker',
                                '2' => 'Penyakit Hati',
                                '3' => 'Hipertensi',
                                '4' => 'DM',
                                '5' => 'Penyakit Ginjal',
                                '6' => 'TBC',
                                '7' => 'Penyakit Jiwa',
                                '8' => 'Kelainan Bawaan',
                                '9' => 'Alergi',
                                '10' => 'Epilepsi',
                                '11' => 'Hamil Kembar',
                                '12' => 'Lain-Lain',
                            ]); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'riwayat_penyakit_keluarga_lainnya')->label(false)->textInput(['class' => $classForm]); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;">8. Riwayat Ginekologi</p>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'riwayat_ginekologi')->label(false)->checkboxList(
                            [
                                '1' => 'Infertilitas',
                                '2' => 'Infeksi Virus',
                                '3' => 'PMS',
                                '4' => 'Endometriosis',
                                '5' => 'Kanker Kandungan',
                                '6' => 'Myoma',
                                '7' => 'Polip Serviks',
                                '8' => 'Perkosaan',
                                '9' => 'Operasi Kandungan',
                                '10' => 'Lain-Lain',
                            ]); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'riwayat_ginekologi_lainnya')->label(false)->textInput(['class' => $classForm]); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;">9. Riwayat Keluarga Berencana</p>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'metode_kb')->label(Yii::t('fe', 'Metode KB Yang Pernah Dipakai'))
                        ->textInput(['class' => $classForm]); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'lama_kb')->label(Yii::t('fe', 'Lama'))
                        ->textInput(['class' => $classForm]); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'komplikasi_kb')->label(Yii::t('fe', 'Komplikasi Dari KB'))->checkboxList(
                            [
                                '1' => 'Perdarahan',
                                '2' => 'PID/Radang Pinggul',
                            ], ['inline' => true]); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;">10. Pola Makan/Minum/Eliminasi/Istirahat/Psikososial</p>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'pola_makan', ['addon' => ['append' => ['content' => 'Kali/Hari']]])
                        ->label()->textInput(['class' => $classForm]); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;">Pola Eliminasi</p>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'pola_bab')
                        ->label(Yii::t('fe', 'BAB'))->textInput(['class' => $classForm]); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'pola_bak')
                        ->label(Yii::t('fe', 'BAK'))->textInput(['class' => $classForm]); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'pola_minum', ['addon' => ['append' => ['content' => 'cc/Hari']]])
                        ->label()->textInput(['class' => $classForm]); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'pola_tidur', ['addon' => ['append' => ['content' => 'Jam/Hari']]])
                        ->label(Yii::t('fe', 'Pola Istirahat : Tidur'))->textInput(['class' => $classForm]); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'penerimaan_kehamilan')
                        ->label(Yii::t('fe', 'Psikososial : Penerimaan Klien Terhadap Kehamilan'))
                        ->textInput(['class' => $classForm]); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'sosial_support')
                        ->label(Yii::t('fe', 'Sosial Support Dari'))->checkboxList([
                            '1' => 'Suami',
                            '2' => 'Orang Tua',
                            '3' => 'Mertua',
                            '4' => 'Keluarga Lain',
                        ], ['inline' => true]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
var tanggal_partus = '.json_encode($model->tanggal_partus).'
var tempat_partus = '.json_encode($model->tempat_partus).'
var umur_hamil = '.json_encode($model->umur_hamil).'
var jenis_persalinan = '.json_encode($model->jenis_persalinan).'
var penolong_persalinan = '.json_encode($model->penolong_persalinan).'
var penyulit = '.json_encode($model->penyulit).'
var bb_anak = '.json_encode($model->bb_anak).'
var keadaan_anak = '.json_encode($model->keadaan_anak).'

var sumber_data_lainnya = "'.$model->sumber_data_lainnya.'"
var hamil_muda_lainnya = "'.$model->hamil_muda_lainnya.'"
var hamil_tua_lainnya = "'.$model->hamil_tua_lainnya.'"
var riwayat_penyakit_keluarga_lainnya = "'.$model->riwayat_penyakit_keluarga_lainnya.'"
var riwayat_ginekologi_lainnya = "'.$model->riwayat_ginekologi_lainnya.'"

var _classForm = "'.$classForm.'"
var _classCenter = "'.$classCenter.'"
var _styleCells = "'.$styleCells.'"
var _modelForm = "GinekologiForm"

$(document).ready(function(){
    const otherSumberData = $(`#${_modelIdForm}-sumber_data_lainnya`);
    const checkboxHamilMuda = $("input[name=\'GinekologiForm[hamil_muda][]\'][value=\'4\']");
    const checkboxHamilTua = $("input[name=\'GinekologiForm[hamil_tua][]\'][value=\'4\']");
    const checkboxPenyakitKeluarga = $("input[name=\'GinekologiForm[riwayat_penyakit_keluarga][]\'][value=\'12\']");
    const checkboxGinekologi = $("input[name=\'GinekologiForm[riwayat_ginekologi][]\'][value=\'10\']");

    const otherHamilMuda = $(`#${_modelIdForm}-hamil_muda_lainnya`);
    const otherHamilTua = $(`#${_modelIdForm}-hamil_tua_lainnya`);
    const otherPenyakitKeluarga = $(`#${_modelIdForm}-riwayat_penyakit_keluarga_lainnya`);
    const otherGinekologi = $(`#${_modelIdForm}-riwayat_ginekologi_lainnya`);

    otherSumberData.prop("readonly", true);
    otherHamilMuda.prop("readonly", true);
    otherHamilTua.prop("readonly", true);
    otherPenyakitKeluarga.prop("readonly", true);
    otherGinekologi.prop("readonly", true);

    if(asesmenMedisId) {
        if(sumber_data_lainnya) {
            otherSumberData.prop("readonly", false);
        }
        if(hamil_muda_lainnya) {
            otherHamilMuda.prop("readonly", false);
        }
        if(hamil_tua_lainnya) {
            otherHamilTua.prop("readonly", false);
        }
        if(riwayat_penyakit_keluarga_lainnya) {
            otherPenyakitKeluarga.prop("readonly", false);
        }
        if(riwayat_ginekologi_lainnya) {
            otherGinekologi.prop("readonly", false);
        }
    }
    
    $(document).on("change", ".sumber_data", function(){
        if($(this).is(":checked")) {
            if($(this).val() == "3") {
                otherSumberData.prop("readonly", false);
            }
            else {
                otherSumberData.val("").prop("readonly", true);
            }
        }
    })
    checkboxHamilMuda.change(function () {
        if(checkboxHamilMuda.is(":checked")) {
            otherHamilMuda.prop("readonly", false);
        }
        else {
            otherHamilMuda.val("").prop("readonly", true);
        }
    });
    checkboxHamilTua.change(function () {
        if(checkboxHamilTua.is(":checked")) {
            otherHamilTua.prop("readonly", false);
        }
        else {
            otherHamilTua.val("").prop("readonly", true);
        }
    });
    checkboxPenyakitKeluarga.change(function () {
        if(checkboxPenyakitKeluarga.is(":checked")) {
            otherPenyakitKeluarga.prop("readonly", false);
        }
        else {
            otherPenyakitKeluarga.val("").prop("readonly", true);
        }
    });
    checkboxGinekologi.change(function () {
        if(checkboxGinekologi.is(":checked")) {
            otherGinekologi.prop("readonly", false);
        }
        else {
            otherGinekologi.val("").prop("readonly", true);
        }
    });
    
    if (tanggal_partus) {
        let rowDataKehamilan;
        let rowCountKehamilan = 0;
                
        $.each(tanggal_partus, function(index, value) {
            if (value) {
                rowCountKehamilan++;
    
                let __tanggalPartus = value;
                let __tempatPartus = tempat_partus[index] || "";
                let __umurHamil = umur_hamil[index] || "";
                let __jenisPersalinan = jenis_persalinan[index] || "";
                let __penolongPersalinan = penolong_persalinan[index] || "";
                let __penyulit = penyulit[index] || "";
                let __bbAnak = bb_anak[index] || "";
                let __keadaanAnak = keadaan_anak[index] || "";
        
                let _btnContent = rowCountKehamilan === "#"
                    ? `<button type="button" class="btn btn-success addRowKehamilan" name="addRowKehamilan">
                            <i class="fa fa-plus"></i>
                        </button>`
                    : `<button type="button" class="btn btn-danger deleteRowKehamilan ms-1">
                            <i class="fa fa-trash"></i>
                        </button>`;
                
                rowDataKehamilan = `
                    <tr>
                        <td class="${_classCenter}">${rowCountKehamilan}</td>
                        <td>
                            <input type="text" readonly="true" name="${_modelForm}[tanggal_partus][]" class="${_classForm} tanggal_partus" style="${_styleCells}" value="${__tanggalPartus}">
                        </td>
                        <td>
                            <input type="text" name="${_modelForm}[tempat_partus][]" class="${_classForm}" style="${_styleCells}" value="${__tempatPartus}">
                        </td>
                        <td>
                            <input type="text" name="${_modelForm}[umur_hamil][]" class="${_classForm}" style="${_styleCells}" value="${__umurHamil}">
                        </td>
                        <td>
                            <input type="text" name="${_modelForm}[jenis_persalinan][]" class="${_classForm}" style="${_styleCells}" value="${__jenisPersalinan}">
                        </td>
                        <td>
                            <input type="text" name="${_modelForm}[penolong_persalinan][]" class="${_classForm}" style="${_styleCells}" value="${__penolongPersalinan}">
                        </td>
                        <td>
                            <input type="text" name="${_modelForm}[penyulit][]" class="${_classForm}" style="${_styleCells}" value="${__penyulit}">
                        </td>
                        <td>
                            <input type="text" name="${_modelForm}[bb_anak][]" class="${_classForm}" style="${_styleCells}" value="${__bbAnak}">
                        </td>
                        <td>
                            <input type="text" name="${_modelForm}[keadaan_anak][]" class="${_classForm}" style="${_styleCells}" value="${__keadaanAnak}">
                        </td>
                        <td style="text-align: center;">
                            ${_btnContent}
                        </td>
                    </tr>
                `;
    
                $(".table-kehamilan tbody").append(rowDataKehamilan);
            }
            
            $(".tanggal_partus").datepicker({
                format: "yyyy-mm-dd",
                autoclose: true,
                todayHighlight: true,
                orientation: "bottom",
            })
        });
    }

    $("table tbody").on("click", ".addRowKehamilan", function () {
        const $table = $(this).closest("table");
        let counterKehamilan = $table.find("tbody tr").length;
        const _tanggalPartus = $(".tanggal_partus").val()
        const _tempatPartus = $(".tempat_partus").val()
        const _umurHamil = $(".umur_hamil").val()
        const _jenisPersalinan = $(".jenis_persalinan").val()
        const _penolongPersalinan = $(".penolong_persalinan").val()
        const _penyulit = $(".penyulit").val()
        const _bbAnak = $(".bb_anak").val()
        const _keadaanAnak = $(".keadaan_anak").val()

        const newRowKehamilan = `
            <tr style="margin-bottom:5px;margin-top:30px;">
                <td class="${_classCenter}">${counterKehamilan}</td>
                <td>
                    <input type="text" name="${_modelForm}[tanggal_partus][]" class="${_classForm} tanggal_partus" style="${_styleCells}" value=${_tanggalPartus}>
                </td>
                <td>
                    <input type="text" name="${_modelForm}[tempat_partus][]" class="${_classForm}" style="${_styleCells}" value=${_tempatPartus}>
                </td>
                <td>
                    <input type="text" name="${_modelForm}[umur_hamil][]" class="${_classForm}" style="${_styleCells}" value=${_umurHamil}>
                </td>
                <td>
                    <input type="text" name="${_modelForm}[jenis_persalinan][]" class="${_classForm}" style="${_styleCells}" value=${_jenisPersalinan}>
                </td>
                <td>
                    <input type="text" name="${_modelForm}[penolong_persalinan][]" class="${_classForm}" style="${_styleCells}" value=${_penolongPersalinan}>
                </td>
                <td>
                    <input type="text" name="${_modelForm}[penyulit][]" class="${_classForm}" style="${_styleCells}" value=${_penyulit}>
                </td>
                <td>
                    <input type="text" name="${_modelForm}[bb_anak][]" class="${_classForm}" style="${_styleCells}" value=${_bbAnak}>
                </td>
                <td>
                    <input type="text" name="${_modelForm}[keadaan_anak][]" class="${_classForm}" style="${_styleCells}" value=${_keadaanAnak}>
                </td>
                <td style="text-align: center;">
                    <button type="button" class="btn btn-danger deleteRowKehamilan ms-1">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        $table.find("tbody").append(newRowKehamilan);
        $(".tanggal_partus").datepicker({
            format: "yyyy-mm-dd",
            autoclose: true,
            todayHighlight: true,
            orientation: "bottom",
        })
        resetForm()
    });
    
    $("table tbody").on("click", ".deleteRowKehamilan", function () {
        const $table = $(this).closest("table");
        $(this).closest("tr").remove();
    
        $table.find("tbody tr").each(function (index) {
            $(this).find("td:first-child").text(index);
            if($(this).find("td:first-child").text() == "0") {
                $(this).find("td:first-child").text("#")
            }
        });
    });

    function resetForm() {
        $("#tanggal_partus_0").val(null)
        $(".tempat_partus").val(null)
        $(".umur_hamil").val(null)
        $(".jenis_persalinan").val(null)
        $(".penolong_persalinan").val(null)
        $(".penyulit").val(null)
        $(".bb_anak").val(null)
        $(".keadaan_anak").val(null)
    }
})
', View::POS_END);
?>
