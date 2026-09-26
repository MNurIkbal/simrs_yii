<?php

/**
 * @Author: Maulana Muhammad Rizky
 * @Date:   2024-06-20
 */

use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use yii\web\View;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Informasi Produksi Obat', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<style>
    .tooltip {
        margin-top: 100px;
        z-index: 99999 !important;

    }

    .tooltip-inner {
        max-width: 500px !important;
        text-align: left !important;
        display: grid;
        flex-wrap: wrap;
        word-break: break-all;
        z-index: 99999 !important;
        position: relative !important;
        text-align: justify;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>" alt="icon-logo">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= $title; ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 my-5">
                        <div style="width: 20%;">
                            <label><?= Yii::t('fe', 'Depo / Gudang') ?></label>
                            <select name="depofarmasi" id="depofarmasi" class="form-control">
                                <?php foreach ($ruangan as $value) : ?>
                                    <option value="<?= $value['ruangan_id'] ?>" <?= $headerValue['ruangan_id'] == $value['ruangan_id'] ? 'selected' : '' ?>><?= $value['ruangan_nama'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" name="default_ruangan" id="default_ruangan" value="<?= $headerValue['ruangan_id'] ?>">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <b>
                                    <h6 class="panel-title"><?= Yii::t('fe', 'Informasi Data Produksi') ?></h6>
                                </b>
                            </div>
                            <div class="panel-body" style="padding: 20px !important;">
                                <div class="row">
                                    <div class="col-md-7">
                                        <div class="row">
                                            <div class="col-md-6" style="margin-bottom: 30px;">
                                                <span class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "No. Pesanan") ?></b></span>
                                                <div class="col-sm-5">
                                                    <p><b>:</b>&nbsp;<?= isset($headerValue['nopemesanan']) ? $headerValue['nopemesanan'] : '-' ?> </p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <span class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Pemesan") ?></b></span>
                                                <div class="col-sm-5">
                                                    <p><b>:</b>&nbsp;<?= isset($headerValue['pegawai_pemesanan']) ? $headerValue['pegawai_pemesanan'] : '-' ?> </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <span class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Tanggal Pesanan") ?></b></span>
                                                <div class="col-sm-5">
                                                    <p><b>:</b>&nbsp;<?= isset($headerValue['tglpemesanan']) ? date('d-F-Y', strtotime($headerValue['tglpemesanan'])) : '-' ?> </p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <span class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Status Pemesanan") ?></b></span>
                                                <div class="col-sm-5">
                                                    <p><b>:</b>&nbsp;<?= isset($headerValue['status_produksi_nama']) ? $headerValue['status_produksi_nama'] : $headerValue['status_produksi_nama'] ?> </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="row">
                                            <span class="text-left control-label col-sm-3"><b><?= Yii::t("fe", "Catatan") ?></b></span>
                                            <div class="col-sm-9">
                                                <textarea class="form-control" readonly class="form-control" style="text-align: justify; text-indent: 0px" cols="40" rows="5"><?= isset($headerValue['catatan_bahanbaku']) ? $headerValue['catatan_bahanbaku'] : '-' ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <b>
                                    <h6 class="panel-title"><?= Yii::t('fe', 'Permintaan Produksi') ?></h6>
                                </b>
                            </div>
                            <div class="panel-body" style="padding: 20px !important;">
                                <div>
                                    <table id="produksi-obat-alkes" class="table table-striped table-condensed table-hover">
                                        <thead class="bg-inverse">
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Obat Produksi</th>
                                                <th>Qty Produksi</th>
                                                <th>Satuan</th>
                                                <th>Nama Item Obat</th>
                                                <th>Qty Item</th>
                                                <th>Satuan Item</th>
                                                <th>Harga Per Item</th>
                                                <th>Total Harga</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="data-produksi-obat-alkes">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div>
                            <div style="display: flex; justify-content: end">
                                <button class="btn btn-info btn-labeled btn-xs" id="btn-kembali" style="width: 7%">
                                    <b><i class="fa fa-arrow-left"></i></b>Kembali
                                </button>
                                <button class="btn btn-success btn-labeled btn-xs" id="btn-simpan-produksi-obat" style="width: 7%">
                                    <b><i class="fa fa-save"></i></b> Define
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs("
    let defaultValue = '" . $defaultValue . "'
    let pemesananId = '" . $pemesananId . "'
    let detailObat = [];
    let cleanObat = [];
    let existObat = [];
    let produksiObatArray = [];
    let isEdit = '" . $isEdit . "';
    let produksiObatAlkesId = '" . $produksiObatAlkesId . "';

", View::POS_END, 'b-index');
$this->registerJs($this->render('js/define-material.js'));
?>