<?php

use app\components\DocoConstants;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

?>
<hr>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default panel-bordered">
            <div class="panel-heading ">
                <h6 class="panel-title"><?= Yii::t('fe', 'Identitas Pasien') ?></h6>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                    </ul>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <table class="table-custom" style="border: none;width:100%;">
                        <tr>
                            <td>Tanggal Pendaftaran </td>
                            <td> : </td>
                            <td>
                                <?php if (isset($dataKunjungan['tgl_pendaftaran'])) : ?>
                                    <?= date('d-m-Y h:i:s', strtotime(ArrayHelper::getValue($dataKunjungan, 'tgl_pendaftaran'))) ?>
                                <?php else : ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>Tanggal Lahir / Umur </td>
                            <td> : </td>
                            <td>
                                <?php if (isset($dataKunjungan['tgl_lahir'])) : ?>
                                    <?= date('d-m-Y', strtotime(ArrayHelper::getValue($dataKunjungan, 'tgl_lahir'))) ?> / <?= ArrayHelper::getValue($dataKunjungan, 'umur') ?>
                                <?php else : ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>No Pendaftaran </td>
                            <td> : </td>
                            <td><?= ArrayHelper::getValue($dataKunjungan, 'no_pendaftaran') ?></td>
                        </tr>
                        <tr>
                            <td>Tanggal Pulang</td>
                            <td> : </td>
                            <td>
                                <?php if (isset($dataKunjungan['tgl_pulang'])) : ?>
                                    <?= date('d-m-Y h:i:s', strtotime(ArrayHelper::getValue($dataKunjungan, 'tgl_pulang'))) ?>
                                <?php else : ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>Jenis Kelamin </td>
                            <td> : </td>
                            <td><?= ArrayHelper::getValue($dataKunjungan, 'jenis_kelamin') == DocoConstants::LOOKUP_LAKI ? 'Laki-laki' : 'Perempuan' ?></td>
                            <td>No. SEP </td>
                            <td> : </td>
                            <td><?= ArrayHelper::getValue($dataKunjungan, 'nosep') ?></td>
                        </tr>
                        <tr>
                            <td>Nama Peserta </td>
                            <td> : </td>
                            <td><?= ArrayHelper::getValue($dataKunjungan, 'nama_pasien') ?></td>
                            <td>Cara Bayar </td>
                            <td> : </td>
                            <td><?= ArrayHelper::getValue($dataKunjungan, 'carabayar_nama') ?></td>
                            <td>Nilai Tagihan (Billing Pasien) </td>
                            <td> : </td>
                            <td>Rp. <?= number_format($totalBilling) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
<div class="row">
    <div class="col-md-12">
        <table class="table bg-inverse mb-1">
            <thead>
                <tr>
                    <th>INACBGS</th>
                </tr>
            </thead>
        </table>
        <table class="table" style="border: none;width:100%;">
            <thead>
                <tr>
                    <th>Status Klaim</th>
                    <th>Kode INA-CBGS</th>
                    <th>Tarif INACBGS</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($kodeInacbgs) && isset($tarifInacbgs)) : ?>
                    <tr>
                        <td><?= $statusKunjungan ?></td>
                        <td><?= $kodeInacbgs ?></td>
                        <td>Rp. <?= number_format($tarifInacbgs) ?></td>
                    </tr>
                <?php else : ?>
                    <tr>
                        <td><?= $statusKunjungan ?></td>
                        <td>-</td>
                        <td>-</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td></td>
                    <td></td>
                    <td colspan="3" style="font-weight: bold;">Total COB: Rp. <?= number_format($totalCob) ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<hr>
<div class="row mb-20">
    <div class="col-md-12">
        <table class="table bg-inverse mb-1">
            <thead>
                <tr>
                    <th>Daftar COB</th>
                </tr>
            </thead>
        </table>
        <div class="mt-20">
            <form>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group" style="display: flex; align-items: center">
                            <div class="mr-2">
                                <span for="noPendaftaran" class="col-form-label">Pilih Penjamin</span>
                            </div>
                            <div style="width: 200px;">
                                <?= Html::dropDownList('penjamin_id', null, $penjamin, ['class' => 'form-control input-sm', 'id' => 'penjamin_id', 'prompt' => 'Pilih Penjamin']) ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" style="display: flex; align-items: center">
                            <div class="mr-2">
                                <span for="noPendaftaran" class="col-form-label">Nomor Kartu Peserta</span>
                            </div>
                            <div>
                                <input type="text" class="form-control" style="width: 100%;" id="no_kartu" name="no_kartu" placeholder="Nomor Kartu">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-15">
                        <div class="form-group" style="display: flex; align-items: center">
                            <button type="button" id="btn-muatulang" class="btn btn-danger btn-xs btn-labeled"><b><i class="fa fa-times"></i></b> Muat Ulang</button>
                            <button type="button" id="btn-cek-eligible" class="btn btn-success btn-xs btn-labeled"><b><i class="fa fa-search"></i></b> Cek Eligible</button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <table class="table-custom mt-15" style="border: none;width:100%;">
                            <tr>
                                <th style="width: 20%;">Nama Peserta</th>
                                <th style="width: 10px;">:</th>
                                <th>
                                    <span id="namapeserta_asuransi"></span>
                                </th>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-4">
                        <table class="table-custom mt-15" style="border: none;width:100%;">
                            <tr>
                                <th style="width: 30%;">Tanggal Lahir / Umur</th>
                                <th style="width: 10px;">:</th>
                                <th>
                                    <span id="tgllahir_asuransi"></span>
                                </th>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-4">
                        <table class="table-custom mt-15" style="border: none;width:100%;">
                            <tr>
                                <th style="width: 20%;">Jenis Kelamin</th>
                                <th style="width: 10px;">:</th>
                                <th>
                                    <span id="jeniskelamin_asuransi"></span>
                                </th>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-12">
                        <hr>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group" style="display: flex; align-items: center">
                            <div class="mr-2">
                                <span for="noPendaftaran" class="col-form-label">Pilih Benefit</span>
                            </div>
                            <div style="min-width: 200px;">
                                <?= Html::dropDownList('penjamin_id', null, [], ['class' => 'form-control input-sm', 'id' => 'benefit_id', 'prompt' => 'Pilih Benefit']) ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div style="display: flex; justify-content: end;">
                            <div>
                                <button type="button" id="btn-back" class="btn btn-info btn-xs btn-labeled"><b><i class="fa fa-arrow-left"></i></b> Kembali</button>
                            </div>
                            <div>
                                <button type="button" id="btn-daftar" class="btn btn-info btn-xs btn-labeled" disabled><b><i class="fa fa-save"></i></b> Daftar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$this->registerJs("
    var dataKunjungan = '" . json_encode($dataKunjungan) . "';
    var tarifInacbgs = '" . $tarifInacbgs . "';
    var diagnosaPrimer = '" . $diagnosaPrimer . "';
    var kodeInacbgs = '" . $kodeInacbgs . "';
    var statusKunjunganId = '" . $statusKunjunganId . "';
", View::POS_END);
$this->registerJs($this->render('_cob_pasien.js'), View::POS_END);
?>