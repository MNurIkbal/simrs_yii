<?php

use app\components\DocoConstants;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
?>

<style>
    .required-field::after {
        color: red;
        content: " *";
    }
    .tindakan-harga{
      font-style: italic;
    }
    .detail-tindakan{
      font-size: 11px;
      border-top: 1px solid rgb(96,96,96);
    }
</style>

<?php $form = ActiveForm::begin([
    'id' => 'tindakan-form-wrapper',
    // 'type' => ActiveForm::TYPE_HORIZONTAL,
    'action' => '/rajal/pemeriksaan/simpan-terapi-penunjang',
    'enableClientValidation' => false,
    'formConfig' => ['deviceSize' => ActiveForm::SIZE_SMALL]
]) ?>

<div class="modal-header">
    <button type="button" class="close close-modal-jadwal" data-dismiss-confirmation="modal">&times;</button>
    <h5 class="modal-title"><b>Tambah Tindakan - <?= $infoPasien['nama_pasien']. ' / '. $infoPasien['penjamin_nama'] . ' / ' . $infoPasien['kelaspelayanan_nama'] ?></b></h5>
</div>
<div class="modal-body select2-md">
    <div class="row">
        <div class="col-sm-3">
            <div class="form-group">
                <label class="control-label">Jenis</label>
                <p class="label-value-form">Tindakan</p>
            </div>
        </div>
        <div class="col-sm-3" style="display:none">
            <div class="form-group">
                <input type="checkbox" class="uniform-part" name="is_puasa" id="fasting-checkbox"> Reminder Puasa
            </div>
        </div>
       <?php
            if ($typeData != 'RI') :
        ?>
        <div class="pull-right" style="margin-right:5px">
            <button type="button" id="btn-history-tindakan" class="btn btn-labeled btn-info btn-xs" data-width="80%" data-href="<?= $url['urlModalHistoryTindakan'] ?>">
                <b><i class='fa fa-history'></i></b> History Tindakan
            </button>
        </div>
        <?php
            endif;
        ?>
    </div>
    <div class="row">
        <div class="col-sm-3">
            <div class="form-group">
                <label for="catatan">Catatan</label>
                <textarea name="catatan" id="catatan-form" class="form-control" rows="5"></textarea>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label for="depo">Depo Tujuan</label>
                <select name="depo_id" id="depo-form" class="form-control">
                    <option value="<?= $defaultDepo['id'] ?>"><?= $defaultDepo['name'] ?></option>
                </select>
            </div>
        </div>
    </div>
    <div class="panel panel-default" id="tindakan-wrapper">
        <div class="panel-heading">
            <h5 class="text-bold panel-title">Tindakan</h5>
            <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse"></a></li>
                </ul>
            </div>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="tgl_tindakan">Tanggal Tindakan</label>
                        <p class="label-value-form"><?= date("d/m/Y") ?></p>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="tgl_tindakan" class="required-field">Dokter</label>
                        <?php
                        if ($user['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS && $useDefaultDpjp) :
                        ?>
                            <p class="label-value-form"><?= $user['nama_pegawai'] ?></p>
                        <?php
                        else :
                        ?>
                            <div class="select2-md">
                                <select name="dokter_id" id="dokter-form" class="doctor-form"></select>
                            </div>
                        <?php
                        endif;
                        ?>
                    </div>
                </div>
                <div class="col-sm-12">
                    <hr>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <?php if($konfig_spesialis) : ?>
                    <div class="form-group">
                        <label for="perawat1">Tindakan Spesialis<span class="text-danger"> *</span></label>
                        <select name="spesialis_id" class="spesialis-form form-control" id="spesialis-form"></select>
                    </div>
                    <?php endif ?>
                    <div class="form-group">
                        <label class="control-label" for="tindakan">Nama Tindakan / Paket</label>
                        <div class="input-group">
                            <span class="input-group-addon"><label><input type="checkbox" id="package-checkbox" name="paket" value="1"> Paket</label></span>
                            <select id="tindakan-form" class="form-control input-sm" name="daftartindakan_id">
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="qty">Jumlah Tindakan</label>
                        <input type="text" name="qty_tindakan" id="qty-tindakan-form" class="form-control doco-number nullable" placeholder="Masukkan Jumlah Tindakan" value="1">
                    </div>
                    <div class="form-group">
                        <div class="input-group" style="margin-top: 10px !important" id="group_cyto1">
                            <!-- <span class="input-group-addon"> -->
                            <label><input type="checkbox" id="cyto-checkbox" name="cyto_tindakan" value="1"> CITO</label>
                            <!-- </span> -->
                            <input type="text" id="cyto-rate" class="form-control" value="Rp. 0" readonly style="display:none">
                        </div>
                    </div>
                </div>
                <div class="col-sm-2" style="display:none">
                    <div class="form-group">
                        <label for="tarif_satuan">Tarif Satuan</label>
                        <p id="tarif-satuan" style="margin-top: 5px" class="label-value-form">Rp. 0</p>
                    </div>
                    <div class="form-group">
                        <label for="jml_tarif" style="margin-top: 10px">Jumlah Tarif</label>
                        <p id="jumlah-tarif" style="margin-top: 3px" class="label-value-form">Rp. 0</p>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="perawat1">Perawat 1</label>
                        <select name="perawat1_id" class="nurse-form form-control" id="perawat1-form"></select>
                    </div>
                    <div class="form-group">
                        <label for="perawat2">Tenaga Kesehatan</label>
                        <select name="perawat2_id" class="nurse-form form-control" id="perawat2-form"></select>
                    </div>
                    <div class="form-group" style="display: none;">
                        <label for="detail-package">Detail Paket</label>
                        <div id="detail-package-section"></div>
                    </div>
                    <div class="form-group">
                        <div class="input-group" style="margin-top: 10px !important">
                            <label><input type="checkbox" id="consent-checkbox" name="consent_tindakan" value="1"> Inform consent</label>
                            &emsp;
                            <?php if($konfig_spesialis) : ?>
                            <!-- <span class="input-group-addon"> -->
                            <label><input type="checkbox" id="cyto-checkbox" name="cyto_tindakan" value="1"> CITO</label>
                            <!-- </span> -->
                            <input type="text" id="cyto-rate" class="form-control" value="Rp. 0" readonly style="display:none">
                            <?php endif ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 text-right">
                    <button type='button' id="btn-add-tindakan" style='margin-right: 5px' class='btn btn-labeled btn-info btn-xs'><b><i class='fa fa-plus'></i></b> Tambah</button>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <hr>
                </div>
                <div class="col-sm-12">
                    <table class="table table-hover table-bordered" style="margin-bottom: 8px;" id="table-tindakan">
                        <thead>
                            <tr class="bg-inverse">
                                <th class="text-center">No</th>
                                <th class="text-center">Tanggal Tindakan</th>
                                <th class="text-center">Nama Tindakan / Paket</th>
                                <th class="text-center">Perawat 1</th>
                                <th class="text-center">Perawat 2</th>
                                <th class="text-center">Jumlah Tindakan</th>
                                <th class="text-center">Implementasi</th>
                                <th class="text-center">CITO</th>
                                <th class="text-center">Inform Consent</th>
                                <th class="text-center">&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="empty-row">
                                <td colspan="9" class="text-center">Data belum tersedia</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default" id="obat-wrapper">
        <div class="panel-heading">
            <h5 class="text-bold panel-title">Obat dan BMHP Tindakan</h5>
            <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse"></a></li>
                </ul>
            </div>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label" style="margin-bottom: 5px">Jenis Pemakaian</label>
                        <?php if (sizeof($typeMeds) == 1) : ?>
                            <p class="label-value-form"><?= reset($typeMeds)['lookup_name'] ?> </p>
                        <?php endif; ?>
                        <div id="obatalkespasienform-obat" role="radiogroup" aria-required="true" <?= sizeof($typeMeds) == 1 ? 'class="hidden"' : '' ?>>
                            <?php
                            foreach ($typeMeds as $indexType => $type) :
                            ?>
                                <label class="radio-inline">
                                    <input type="radio" name="jenis_obat" class="uniform-part jenis-form" value="<?= $type['lookup_id'] ?>" <?= $indexType == 0 ? 'checked' : '' ?>><span><?= $type['lookup_name'] ?></span>
                                </label>
                            <?php
                            endforeach;
                            ?>
                        </div>
                    </div>
                    <div class="form-group" id="related-tindakan-parent">
                        <label for="nama_tindakan" style="margin-top: 10px">Nama Tindakan</label>
                        <select name="related_tindakan" id="related-tindakan-form" class="form-control"></select>
                    </div>
                    <div class="form-group" id="obat-parent">
                        <label for="nama_tindakan">Pemakaian Obat/Alkes</label>
                        <select name="obat" id="obat-form" class="form-control"></select>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group" id="qty-parent">
                        <label class="control-label" for="qty">Jumlah</label>
                        <input type="text" class="form-control doco-decimal-wcomma" name="qty" id="qty-med-form" placeholder="Masukkan Jumlah Obat/Alkes" value="1">
                    </div>
                    <div class="form-group">
                        <div class="input-group" style="margin-top: 25px">
                            <input type="checkbox" id="tagihkan-checkbox" name="is_tagihkan" value="1" <?= isset($konfigFormulir['ditagihkan']['bmhp']) ? ($konfigFormulir['ditagihkan']['bmhp'] ? 'checked' : '') : 'checked' ?>>&nbsp Ditagihkan
                            <!-- <span class="input-group-addon"><label><input type="checkbox" id="tagihkan-checkbox" name="is_tagihkan" value="1" checked> Ditagihkan</label></span>
                            <input type="text" name="tarif_obat" class="form-control" value="0" readonly id="med-price"> -->
                        </div>
                    </div>
                    <div class="form-group" style="display:none">
                        <label for="stock">Stok</label>
                        <p id="stock-obat" class="label-value-form">0</p>
                    </div>
                    <div class="form-group">
                        <label for="perawat1bmhp">Perawat 1</label>
                        <select name="perawat1bmhp_id" class="nurse-form form-control" id="perawat1bmhp-form"></select>
                    </div>
                    <div class="form-group">
                        <label for="perawat2bmhp">Perawat 2</label>
                        <select name="perawat2bmhp_id" class="nurse-form form-control" id="perawat2bmhp-form"></select>
                    </div>
                </div>
                <div class="col-sm-3">
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 text-right">
                    <button type='button' style='margin-right: 5px' class='btn btn-labeled btn-info btn-xs' id="btn-add-med"><b><i class='fa fa-plus'></i></b> Tambah</button>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <hr>
                </div>

                <div class="col-md-12">
                    <div class="legend-index">
                        <div class="legend-header">Keterangan</div>
                        <div class="legend-wrapper">
                            <div class="legend-information">
                                <div class="legend-information__color unavailable-stock"></div>
                                <div class="legend-information__text">Stok tidak tersedia</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <table class="table table-bordered" style="margin-bottom: 8px;" id="table-obat">
                        <thead>
                            <tr class="bg-inverse">
                                <th>No</th>
                                <th>Tanggal Tindakan</th>
                                <th>Nama Tindakan/Paket</th>
                                <th>Obat/Alkes</th>
                                <th>Perawat 1</th>
                                <th>Perawat 2</th>
                                <th>Jumlah Obat</th>
                                <th>Ditagihkan</th>
                                <th>Implementasi</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="empty-row">
                                <td colspan="10" class="text-center">Data belum tersedia</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer text-right">
    <button id="btn-save" type='button' style='margin-right: 5px' class='btn btn-labeled btn-info btn-xs'><b><i class='fa fa-save'></i></b> Simpan</button>
</div>

<?php ActiveForm::end() ?>

<?php
$this->registerJs('
    var instalasiId = "'.$instalasi_id.'";
    var dropdownList = {
        dokter: ' . json_encode($dokter) . ',
        perawat: ' . json_encode($perawat) . ',
        tindakan: ' . json_encode($tindakan) . ',
        paket: ' . json_encode($paket) . ',
        spesialis: ' . json_encode($data_spesialis) . ',
    }
    var penjaminId = "' . $penjaminId . '"
    var kelasPelayananId = "' . $kelasPelayananId . '"
    var frontendUrl = "' . $frontendUrl . '"
    var cpptIdTindakanBmhp = "' . $cpptId . '"
    var type = "' . $typeData . '"
    var no_pendaftaran = "'.$no_pendaftaran.'"
    var pendaftaranId = "' . $pendaftaranId . '"
    var kelTindakanCathlab = ' . $constCathlab . '
    var dokterDpjp = ' . $dokterDpjp . '
    var konfig_spesialis = "' . $konfig_spesialis . '"
    var useDefaultDpjp = ' . $useDefaultDpjp . '
    var userSpesialisId = "'.$user["spesialis_id"].'"
    var konfig_tindakan_harga = '. $konfig_tindakan_harga .'
', View::POS_END);
$this->registerJs($this->render('__tindakan_bmhp.js'), View::POS_END);
?>
