<?php

/**
 * @Author: Sigit
 * @Date:   2018-07-24 16:41:42
 */

// Usings
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
?>

<?php $form = ActiveForm::begin([
    'id' => 'form-pemberian-obat', 
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'enableClientValidation' => false,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL],
    'action' => Url::to(['simpan-pemberian-obat']),
]) ?>
<?= Html::activeHiddenInput($pemberianObatForm, 'pendaftaran_id') ?>
<?= Html::activeHiddenInput($pemberianObatForm, 'pasienadmisi_id') ?>
<div class="row">
    <div class="col-lg-6">
        <div class="form-group">
            <?=Html::label(Yii::t('fe', 'Dokter DPJP'), '', [
                'class' => 'control-label col-sm-4'
            ]);?>
            <div class=" col-sm-8">
                <?= Html::textInput('nama_dokter', $dataPasien['admisi_dokter'], [
                    'class' => 'form-control input-sm',
                    'id' => 'nama_dokter_dpjp',
                    'disabled' => 'disabled',
                ]) ?>
                <?= Html::activeHiddenInput($pemberianObatForm, 'dokterdpjp_id') ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <!-- Berat badan -->
        <?= $form->field($pemberianObatForm, 'berat_badan', [
            'template' => '{label}<div class="input-group">{input}<span class="input-group-addon" id="basic-addon2">Kg</span></div>{error}{hint}'
        ])->textInput(['class' => 'form-control input-sm docoNumberOnly bb_tb', 'id' => 'berat_badan']) ?>
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        <?php 
        $pemberianObatForm->diagnosa_id = $text_diagnosa;
        echo $form->field($pemberianObatForm, 'diagnosa_id')->widget(Select2::classname(), [
            // 'initValueText' => isset($text_diagnosa) && $text_diagnosa != '' ? $text_diagnosa : null,
            'data' => [
                $text_diagnosa => $tmpDiag
            ],
            'options' => [
                'placeholder' => '-- Pilih --',
                'class' => 'form-control input-sm select2'
            ],
            'pluginOptions' => [
                'tags' => true,
                'tokenSeparators' => [',', '_'],
                'minimumInputLength' => 3,
                'language' => [
                    'errorLoading' => new JsExpression("function () {return 'Loading...';}"),
                ],
                'ajax' => [
                    'url' => \yii\helpers\Url::to(['/ranap/end-point/get-new-diagnosa']),
                    'dataType' => 'json',
                    'data' => new JsExpression('
                        function(params) {
                            return {
                                q: params.term,
                                type: "diagnosa_utama",
                                all_text: 0,
                                id_with_text: 1,
                            }; 
                        }
                    ')
                ],
                'escapeMarkup' => new JsExpression ('function (markup) {return markup;}'),
                'templateResult' => new JsExpression ('function (diagnosa) {return diagnosa.text;}'),
                'templateSelection' => new JsExpression ('function (subject) {return subject.text;}') ,
            ],
        ]) ?>
    </div>
    <div class="col-lg-6">
        <!-- Tinggi badan -->
        <?= $form->field($pemberianObatForm, 'tinggi_badan', [
            'template' => '{label}<div class="input-group">{input}<span class="input-group-addon" id="basic-addon2">Cm</span></div>{error}{hint}'
        ])->textInput(['class' => 'form-control input-sm docoNumberOnly bb_tb', 'id' => 'tinggi_badan']) ?>
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        <?= $form->field($pemberianObatForm, 'is_alergi')->radioList(
            [1 => Yii::t('fe', 'Ya'), 0 => Yii::t('fe', 'Tidak')],
            ['inline' => true]
        )->label(Yii::t('fe', 'Alergi')) ?>
    </div>
    <div class="col-lg-6">
        <!-- Luas tubuh -->
        <?= $form->field($pemberianObatForm, 'luas_tubuh', [
            'template' => '{label}<div class="input-group">{input}<span class="input-group-addon" id="basic-addon2">m<sup>2</sup></span></div>{error}{hint}'
        ])->textInput(['class' => 'form-control input-sm', 'id' => 'luas_tubuh', 'readonly' => 'readonly']) ?>
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        <?= $form->field($pemberianObatForm, 'is_hamil')->radioList(
            [1 => Yii::t('fe', 'Ya'), 0 => Yii::t('fe', 'Tidak')],
            ['inline' => true]
        )->label(Yii::t('fe', 'Hamil')) ?>
    </div>
</div>
<div>
    <hr>
</div>
<?php if ($jenisObat): ?>
<?php $count = 0; ?>
<?php foreach ($jenisObat as $key => $value): ?>
<div class="panel panel-white">
    <div class="panel-heading">
        <h5 class="panel-title"><?= str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower('Pemberian '.$value)))); ?></h5>
    </div>
    <div class="panel-body">
        <table class="table table-bordered" id="tb-pemberian-<?= str_replace(' ', '-', strtolower($value)) ?>" style="width:200%">
            <thead>
                <tr class="bg-inverse">
                    <th class="text-center"><?= Yii::t('fe', 'No Resep') ?><span style="color: red;"> *</span></th>
                    <th class="text-center"><?= Yii::t('fe', 'Nama Obat') ?><span style="color: red;"> *</span></th>
                    <th class="text-center"><?= Yii::t('fe', 'Signa') ?><span style="color: red;"> *</span></th>
                    <th class="text-center"><?= Yii::t('fe', 'Jumlah') ?><span style="color: red;"> *</span></th>
                    <th class="text-center"><?= Yii::t('fe', 'Dokter') ?><span style="color: red;"> *</span></th>
                    <th class="text-center"><?= Yii::t('fe', 'Waktu Pemberian Obat') ?><span style="color: red;"> *</span></th>
                    <th class="text-center"><?= Yii::t('fe', 'Pemberi Obat 1') ?><span style="color: red;"> *</span></th>
                    <th class="text-center"><?= Yii::t('fe', 'Pemberi Obat 2') ?></th>
                    <th class="text-center"><?= Yii::t('fe', 'Efek') ?><span style="color: red;"> *</span></th>
                    <th class="text-center"><?= Yii::t('fe', 'Keterangan') ?><span style="color: red;"> *</span></th>
                </tr>
            </thead>
            <tbody>
                <tr class="cloning-tr">
                    <?= Html::hiddenInput('jenis_obat', $value, ['class' => 'jenis_obat']); ?>
                    <?= Html::hiddenInput('PemberianObatDetailForm['.$count.'][stokobatpasien_id]', '', ['class' => 'stokobatpasien_id']); ?>
                    <?= Html::hiddenInput('PemberianObatDetailForm['.$count.'][jenisobat_id]', '', ['class' => 'jenisobat_id']); ?>
                    <?= Html::hiddenInput('PemberianObatDetailForm['.$count.'][is_resep]', '', ['class' => 'is_resep']); ?>
                    <?= Html::hiddenInput('sisa', '', ['class' => 'sisa']); ?>
                    <td width="10%">
                        <select class="newselect no_res_rekon" name="PemberianObatDetailForm[<?= $count ?>][no_res_rekon]">
                            <option value=""><?= Yii::t('fe', '-- Pilih --') ?></option>
                            <?php if (!empty($listStokObatPasien[$key])): ?>
                            <?php foreach ($listStokObatPasien[$key] as $content): ?>
                            <option value="<?= $content ?>"><?= $content ?></option>
                            <?php endforeach ?>
                            <?php endif ?>
                        </select>
                        <?= Html::hiddenInput('PemberianObatDetailForm['.$count.'][nama_obat]', '', ['class' => 'nama_obat']); ?>
                    </td>
                    <td width="10%">
                        <select class="newselect obatalkes_id" name="PemberianObatDetailForm[<?= $count ?>][obatalkes_id]" disabled>
                            <option value=""><?= Yii::t('fe', '-- Pilih --') ?></option>
                        </select>
                    </td>
                    <td width="10%">
                        <?= Html::tag('span', '', ['class' => 'signa']); ?>
                        <?= Html::hiddenInput('PemberianObatDetailForm['.$count.'][signa_obat]', '', ['class' => 'signa_obat']) ?>
                    </td>
                    <td width="5%">
                        <?= Html::textInput('PemberianObatDetailForm['.$count.'][jumlah]', '', [
                            'class' => 'form-control input-sm docoNumberOnly jumlah'
                        ]) ?>
                    </td>
                    <td width="10%">
                        <?= Html::tag('span', '', ['class' => 'nama_dokter']); ?>
                        <?= Html::hiddenInput('PemberianObatDetailForm['.$count.'][dokter_id]', '', ['class' => 'dokter_id']) ?>
                    </td>
                    <td width="10%">
                        <?= DateTimePicker::widget([
                            'name' => 'PemberianObatDetailForm['.$count.'][wkt_pemberian]',
                            'value' => date('d-m-Y h:i:s', strtotime('NOW')),
                            'type' => DateTimePicker::TYPE_INPUT,
                            'options' => [
                                'class' => 'wkt_pemberian'
                            ],
                            'pluginOptions' => [
                                'format' => 'dd-mm-yyyy HH:mm:ss',
                                'locale' => 'id',
                                'language' => 'id',
                                'showMeridian' => true,
                                'autoclose' => true,
                                'todayBtn' => true
                            ]
                        ]); ?>
                    </td>
                    <td width="10%">
                        <select class="newselect pemberi1_id" name="PemberianObatDetailForm[<?= $count ?>][pemberi1_id]">
                            <option value=""><?= Yii::t('fe', '-- Pilih --') ?></option>
                            <?php if (!empty($listPegawai)): ?>
                            <?php foreach ($listPegawai as $key => $content): ?>
                            <?php if ($key == $pegawai_id): ?>
                            <option value="<?= $key ?>" selected="selected"><?= $content ?></option>
                            <?php else: ?>
                            <option value="<?= $key ?>"><?= $content ?></option>
                            <?php endif ?>
                            <?php endforeach ?>
                            <?php endif ?>
                        </select>
                    </td>
                    <td width="10%">
                        <select class="newselect pemberi2_id" name="PemberianObatDetailForm[<?= $count ?>][pemberi2_id]">
                            <option value=""><?= Yii::t('fe', '-- Pilih --') ?></option>
                            <?php if (!empty($listPegawai)): ?>
                            <?php foreach ($listPegawai as $key => $content): ?>
                            <option value="<?= $key ?>"><?= $content ?></option>
                            <?php endforeach ?>
                            <?php endif ?>
                        </select>
                    </td>
                    <td width="10%">
                        <select class="newselect efek" name="PemberianObatDetailForm[<?= $count ?>][efek]">
                            <option value=""><?= Yii::t('fe', '-- Pilih --') ?></option>
                            <?php if (!empty($listEfek)): ?>
                            <?php foreach ($listEfek as $key => $content): ?>
                            <option value="<?= $key ?>"><?= $content ?></option>
                            <?php endforeach ?>
                            <?php endif ?>
                        </select>
                    </td>
                    <td width="10%">
                        <select class="newselect keterangan" name="PemberianObatDetailForm[<?= $count ?>][keterangan]">
                            <option value=""><?= Yii::t('fe', '-- Pilih --') ?></option>
                            <?php if (!empty($listKeterangan)): ?>
                            <?php foreach ($listKeterangan as $key => $content): ?>
                            <option value="<?= $key ?>"><?= $content ?></option>
                            <?php endforeach ?>
                            <?php endif ?>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="panel-footer">
        <div class="col-md-12 text-right">
            <?= Html::button('<b><i class="fa fa-plus"></i></b> '.Yii::t('fe', 'Tambah'), [
                'class' => 'btn btn-info btn-labeled btn-xs btn-tambah-pemberian-obat',
                'onclick' => 'tambahPemberianObat(this)',
                'value' => count($jenisObat),
            ]) ?>
        </div>
    </div>
</div>
<?php $count++ ?>
<?php endforeach ?>
<?php endif ?>
<div class="row">
    <?= Html::button('<b><i class="fa fa-floppy-o"></i></b> '.Yii::t('fe', 'Simpan'), [
        'class' => 'btn btn-info btn-labeled btn-xs btn-simpan-pemberian-obat',
        'onclick' => 'simpanPemberianObat()',
    ]) ?>
    <?= Html::button('<b><i class="fa fa-arrow-left"></i></b> '.Yii::t('fe', 'Kembali'), [
        'class' => 'btn btn-info btn-labeled btn-xs btn-kembali-pemberian-obat',
    ]) ?>
</div>
<?php ActiveForm::end() ?>
<div>
    <hr>
</div>
<div class="panel panel-white">
    <div class="panel-heading">
        <h5 class="panel-title"><?= Yii::t('fe', 'Catatan Pemberian Obat') ?></h5>
    </div>
    <div class="panel-toolbar clearfix">
        <div class="row">
            <?= Html::button('<b><i class="fa fa-print"></i></b> '.Yii::t('fe', 'Cetak'), [
                'class' => 'btn btn-info btn-labeled btn-xs btn-cetak-pemberian-obat',
                'data-url' => Url::to(['cetak-pemberian-obat', 'id' => $pendaftaran_id]),
            ]) ?>
            <?= Html::button('<b><i class="fa fa-undo"></i></b> '.Yii::t('fe', 'Retur'), [
                'class' => 'btn btn-info btn-labeled btn-xs btn-retur-pemberian-obat',
                'onclick' => 'showHalamanRetur()',
            ]) ?>
        </div>
    </div>
    <div class="panel-body">
        <br>
        <?php if ($listJenisObatRiwayat): ?>
        <?php foreach ($listJenisObatRiwayat as $value): ?>
        <div class="panel panel-white">
            <div class="panel-heading">
                <h5 class="panel-title"><?= str_replace('\' ', '\'', ucwords(str_replace('\'', '\' ', strtolower('Pemberian '.$value)))); ?></h5>
            </div>
            <div class="panel-body">
                <table class="table table-bordered" id="tb-riwayat-pemberian-<?= str_replace(' ', '-', strtolower($value)) ?>" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th class="text-center"><?= Yii::t('fe', 'No Resep') ?></th>
                            <th class="text-center"><?= Yii::t('fe', 'Nama Obat') ?></th>
                            <th class="text-center"><?= Yii::t('fe', 'Signa') ?></th>
                            <th class="text-center"><?= Yii::t('fe', 'Jumlah') ?></th>
                            <th class="text-center"><?= Yii::t('fe', 'Dokter') ?></th>
                            <th class="text-center"><?= Yii::t('fe', 'Waktu Pembeian Obat') ?></th>
                            <th class="text-center"><?= Yii::t('fe', 'Pemberi Obat 1') ?></th>
                            <th class="text-center"><?= Yii::t('fe', 'Pemberi Obat 2') ?></th>
                            <th class="text-center"><?= Yii::t('fe', 'Efek') ?></th>
                            <th class="text-center"><?= Yii::t('fe', 'Keterangan') ?></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <?php endforeach ?>
        <?php endif ?>
    </div>
</div>
<?php
$this->registerJs('
    var is_disabled = "'.$disabled.'";
    var status_disabled = "'.$status_disabled.'";
    var is_valid = false;

    $(document).ready(function(){
        $("#form-pemberian-obat :input").prop("disabled", is_valid);
        $(".input-group-addon").'.$hide.';
    });
    
    var pendaftaran_id = "'.$pendaftaran_id.'";
    var jenisObat = '.json_encode($jenisObat).';
    var listJenisObatRiwayat = '.json_encode($listJenisObatRiwayat).';
    var listObatPasien = '.json_encode($listStokObatPasien).';

    var noResep = "'.(\Yii::t("fe", "No Resep")).'";
    var namaObat = "'.(\Yii::t("fe", "Nama Obat")).'";
    var signa = "'.(\Yii::t("fe", "Signa")).'";
    var jumlah = "'.(\Yii::t("fe", "Jumlah")).'";
    var dokter = "'.(\Yii::t("fe", "Dokter")).'";
    var waktuPemberianObat = "'.(\Yii::t("fe", "Waktu Pemberian Obat")).'";
    var pemberiObat1 = "'.(\Yii::t("fe", "Pemberi Obat 1")).'";
    var pemberiObat2 = "'.(\Yii::t("fe", "Pemberi Obat 2")).'";
    var efek = "'.(\Yii::t("fe", "Efek")).'";
    var keterangan = "'.(\Yii::t("fe", "Keterangan")).'";

    // Datatable language
    var emptyTable = "'.(\Yii::t("fe", "Tidak ada data yang tersedia")).'";
    var info = "'.(\Yii::t("fe", "Menampilkan _START_ sampai _END_ dari _TOTAL_ data")).'";
    var infoEmpty = "'.(\Yii::t("fe", "Menampilkan 0 sampai 0 dari 0 data")).'";
    var infoFiltered = "'.(\Yii::t("fe", "(disaring dari _MAX_ total data)")).'";
    var lengthMenu = "'.(\Yii::t("fe", "Menampilkan _MENU_ data")).'";
    var loadingRecords = "'.(\Yii::t("fe", "Memuat...")).'";
    var processing = "'.(\Yii::t("fe", "Memproses...")).'";
    var search = "'.(\Yii::t("fe", "Cari:")).'";
    var zeroRecords = "'.(\Yii::t("fe", "Tidak ada data yang ditemukan")).'";
    var sortAscending = "'.(\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terkecil ke yang terbesar")).'";
    var sortDescending = "'.(\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terbesar ke yang terkecil")).'";
', View::POS_END, 'index');
$this->registerJs($this->render('js/form_pemberian_obat.js'), View::POS_END);
?>