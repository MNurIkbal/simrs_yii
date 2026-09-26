<?php

/**
 * @Author: Sigit
 * @Date:   2018-08-21 10:31:52
 */

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
?>

<div id="div-riwayat-retur-obat">
    <div class="panel panel-white">
        <div class="panel-heading">
            <h5 class="panel-title"><?= Yii::t('fe', 'Riwayat Retur Obat') ?></h5>
        </div>
        <div class="panel-toolbar clearfix">
            <?= DocoHelpers::generateToolbar([
                'permintaan-retur' => [
                    'type' => 'button',
                    'title' => Yii::t('fe', 'Permintaan Retur'),
                    'icon' => 'fa fa-pencil',
                    'attributes' => [
                        'id' => 'btn-permintaan-retur',
                        'data-options' => 'click',
                        'onclick' => 'showFormPermintaanRetur()',
                        'disabled' => $disablePermintaanRetur
                    ],
                ],
                'cetak-retur' => [
                    'type' => 'button',
                    'title' => Yii::t('fe', 'Cetak'),
                    'icon' => 'fa fa-print',
                    'attributes' => [
                        'id' => 'btn-cetak-retur',
                        'onclick' => 'cetakRiwayatRetur()',
                        'data-options' => 'click'
                    ],
                ],
                'detail-retur' => [
                    'type' => 'button',
                    'title' => Yii::t('fe', 'Detail'),
                    'icon' => 'fa fa-eye',
                    'attributes' => [
                        'id' => 'btn-detail-retur',
                        'data-options' => 'click',
                        'disabled' => 'disabled',
                        'onclick' => 'detailPermintaanRetur()',
                    ],
                ],
                'ubah-retur' => [
                    'type' => 'button',
                    'title' => Yii::t('fe', 'Ubah'),
                    'icon' => 'fa fa-pencil',
                    'attributes' => [
                        'id' => 'btn-ubah-retur',
                        'data-options' => 'click',
                        'disabled' => 'disabled',
                        'onclick' => 'ubahPermintaanRetur()',
                    ],
                ],
                'hapus-retur' => [
                    'type' => 'button',
                    'title' => Yii::t('fe', 'Hapus'),
                    'icon' => 'fa fa-trash',
                    'attributes' => [
                        'id' => 'btn-hapus-retur',
                        'data-options' => 'click',
                        'disabled' => 'disabled',
                        'onclick' => 'hapusPermintaanRetur()',
                        'data-additional' => 'data-rm',
                    ],
                ],
            ], '#tb-riwayat-retur-obat') ?>
        </div>
        <div class="panel-body">
            <table class="table table-bordered datatable tb-riwayat-retur-obat" id="tb-riwayat-retur-obat" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th width="5%">&nbsp;</th>
                        <th class="text-center"><?= Yii::t('fe', 'No') ?></th>
                        <th class="text-center"><?= Yii::t('fe', 'No Retur') ?></th>
                        <th class="text-center"><?= Yii::t('fe', 'Tanggal Retur') ?></th>
                        <th class="text-center"><?= Yii::t('fe', 'Ruang') ?></th>
                        <th class="text-center"><?= Yii::t('fe', 'Status') ?></th>
                        <th class="text-center"><?= Yii::t('fe', 'User Retur') ?></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<div id="div-form-retur-obat" hidden>
    <?php $form = ActiveForm::begin([
        'id' => 'form-permintaan-retur-detail',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'enableClientValidation' => false,
        'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL],
        'action' => Url::to(['simpan-permintaan-retur']),
    ]) ?>
    <div class="panel panel-white">
        <div class="panel-heading">
            <h5 class="panel-title"><?= Yii::t('fe', 'Form Retur Obat/Alkes') ?></h5>
        </div>
        <div class="panel-toolbar clearfix">
            <?= DocoHelpers::generateToolbar([
                'retur' => [
                    'type' => 'button',
                    'title' => Yii::t('fe', 'Retur'),
                    'icon' => 'fa fa-save',
                    'attributes' => [
                        'id' => 'btn-simpan-permintaan-retur',
                        'data-options' => 'click',
                        'onclick' => 'simpanPermintaanRetur()',
                    ],
                ],
                'kembali-retur' => [
                    'type' => 'button',
                    'title' => Yii::t('fe', 'Kembali'),
                    'icon' => 'fa fa-arrow-left',
                    'attributes' => [
                        'id' => 'btn-kembali-retur',
                        'data-options' => 'click',
                        'onclick' => 'hideFormPermintaanRetur()',
                    ],
                ],
            ]) ?>
        </div>
        <div class="panel-body">
            <table class="table table-bordered datatable tb-form-retur-obat" id="tb-form-retur-obat" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th class="text-center"><?= Yii::t('fe', 'No') ?></th>
                        <th class="text-center"><?= Yii::t('fe', 'No Resep') ?><span style="color: red;"> *</span></th>
                        <th class="text-center"><?= Yii::t('fe', 'Nama Obat') ?><span style="color: red;"> *</span></th>
                        <th class="text-center"><?= Yii::t('fe', 'Signa') ?><span style="color: red;"> *</span></th>
                        <th class="text-center"><?= Yii::t('fe', 'Sisa Obat') ?><span style="color: red;"> *</span></th>
                        <th class="text-center"><?= Yii::t('fe', 'Jumlah Retur') ?><span style="color: red;"> *</span></th>
                        <th class="text-center"><?= Yii::t('fe', 'Harga Satuan') ?><span style="color: red;"> *</span></th>
                        <th class="text-center"><?= Yii::t('fe', 'Total') ?><span style="color: red;"> *</span></th>
                        <th class="text-center"><?= Yii::t('fe', 'Alasan Retur Perawat') ?><<span style="color: red;"> *</span>/th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    <?php ActiveForm::end() ?>
</div>
<div id="div-detail-retur-obat" hidden>
    <div class="panel panel-white">
        <div class="panel-heading">
            <h5 class="panel-title"><?= Yii::t('fe', 'Detail Retur Obat/Alkes') ?></h5>
        </div>
        <div class="panel-toolbar clearfix">
            <?= DocoHelpers::generateToolbar([
                'cetak-detail-retur' => [
                    'type' => 'button',
                    'title' => Yii::t('fe', 'Cetak'),
                    'icon' => 'fa fa-print',
                    'attributes' => [
                        'id' => 'btn-cetak-detail-retur',
                        'data-options' => 'click',
                        'onclick' => 'cetakDetailRetur()',
                    ],
                ],
                'kembali-retur' => [
                    'type' => 'button',
                    'title' => Yii::t('fe', 'Kembali'),
                    'icon' => 'fa fa-arrow-left',
                    'attributes' => [
                        'id' => 'btn-kembali-retur',
                        'data-options' => 'click',
                        'onclick' => 'hideDetailPermintaanRetur()',
                    ],
                ],
            ]) ?>
        </div>
        <div class="panel-body">
            <table class="table table-bordered datatable tb-detail-retur-obat" id="tb-detail-retur-obat" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th class="text-center"><?= Yii::t('fe', 'No') ?></th>
                        <th class="text-center"><?= Yii::t('fe', 'Nama Obat/Alkes') ?></th>
                        <th class="text-center"><?= Yii::t('fe', 'Satuan') ?></th>
                        <th class="text-center"><?= Yii::t('fe', 'Jumlah Retur') ?></th>
                        <th class="text-center"><?= Yii::t('fe', 'Alasan Retur Perawat') ?></th>
                        <th class="text-center"><?= Yii::t('fe', 'Jumlah Retur Acc') ?></th>
                        <th class="text-center"><?= Yii::t('fe', 'Alasan UF') ?></th>
                        <th class="text-center"><?= Yii::t('fe', 'Harga Satuan') ?></th>
                        <th class="text-center"><?= Yii::t('fe', 'Total') ?></th>
                        <th class="text-center"><?= Yii::t('fe', 'Tanggal Acc') ?></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<?php $form = ActiveForm::begin([
    'id' => 'form-permintaan-retur',
]) ?>
<?= Html::hiddenInput('permintaanretur_id', '', ['class' => 'permintaanretur_id', 'readonly' => 'readonly']); ?>
<?= Html::hiddenInput('pendaftaran_id', $data_pasien['pendaftaran_id'], ['readonly' => 'readonly']); ?>
<?= Html::hiddenInput('ruangan_id', $ruangan_id, ['readonly' => 'readonly']); ?>
<?= Html::hiddenInput('pegawairetur_id', $pegawai_id, ['readonly' => 'readonly']); ?>
<?= Html::hiddenInput('status_permintaanretur', 0, ['readonly' => 'readonly']); ?>
<?php ActiveForm::end() ?>

<?php
$this->registerJs('
    var pendaftaran_id = "'.$id.'";

    var no = "'.(\Yii::t("fe", "No")).'";
    var noRetur = "'.(\Yii::t("fe", "No Retur")).'";
    var tanggalRetur = "'.(\Yii::t("fe", "Tanggal Retur")).'";
    var ruang = "'.(\Yii::t("fe", "Ruang")).'";
    var status = "'.(\Yii::t("fe", "Status")).'";
    var userRetur = "'.(\Yii::t("fe", "User Retur")).'";
    var noForm = "'.(\Yii::t("fe", "No")).'";
    var noResep = "'.(\Yii::t("fe", "No Resep")).' *";
    var namaObat = "'.(\Yii::t("fe", "Nama Obat")).' *";
    var signa = "'.(\Yii::t("fe", "Signa")).' *";
    var sisaObat = "'.(\Yii::t("fe", "Sisa Obat")).' *";
    var jumlahRetur = "'.(\Yii::t("fe", "Jumlah Retur")).' *";
    var hargaSatuan = "'.(\Yii::t("fe", "Harga Satuan")).' *";
    var total = "'.(\Yii::t("fe", "Total")).' *";
    var alasan = "'.(\Yii::t("fe", "Alasan Retur Perawat")).' *";
    var namaObatAlkes = "'.(\Yii::t("fe", "Nama Obat/Alkes")).'";
    var satuan = "'.(\Yii::t("fe", "Satuan")).'";
    var qtyApprove = "'.(\Yii::t("fe", "Jumlah Retur Acc")).'";
    var alasanUF = "'.(\Yii::t("fe", "Alasan UF")).'";
    var tglApprove = "'.(\Yii::t("fe", "Tanggal Acc")).'";

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
$this->registerJs($this->render('js/form.js'), View::POS_END);
?>