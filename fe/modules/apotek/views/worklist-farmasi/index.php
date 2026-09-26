<?php

/**
 * @author : Novia Sukmasari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\DepDrop;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe',Yii::$app->docoVars->workspace("instalasi_name")), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<style type="text/css">
.search-no-resep,
.informasi-resep,
.status-worklist {
    margin-top: 1%;
}

.search-pegawai {
    margin: 1% 0%;
}

.worklist-resep,
.search-pegawai {
    display: none;
}

.tbl-header tr:nth-child(even) {
    font-weight: bold;
}

.tbl-header td {
    width: 16.5%;
}

.tbl-detail thead tr th {
    text-align: center;
}

.tbl-detail tbody tr td {
    text-align: center;
}

.tbl-proses tr td {
    text-align: center;
    width: 20%;
    vertical-align: top !important;
}

.tbl-proses tr:nth-child(1) td {
    font-weight: bold;
    font-size: 16pt;
}

label#no_resep,
label#pegawai {
    margin-top: 0.5%;
}

#alergi {
    color: red;
}

.button-ok{
    background-color: #34bfa3;
    color: #ffffff;
    flex: 1;
}
.button-ok:hover{
    background-color: #1ca189;
    color: #ffffff;
}

.bold {
    font-weight: bold;
}

.detail-resep {
    margin-bottom: 1%;
}

.cancel-receipt {
    display: none;
}

.not-approved {
    display: none;
}

.panel-warning > .panel-heading {
    background: #ffc107;
    color: #343a40;
}
</style>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= $title; ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
            </div>

            <div class="panel-body">
                <?php echo $this->render('_pencarian', ['model' => $model]); ?>

                <div class="worklist-resep">
                    <div class="col-md-12">
                        <hr>
                    </div>

                    <div class="row informasi-resep">
                        <table class="table table-borderless tbl-header">
                            <tr>
                                <td>Tanggal Resep</td>
                                <td>No. Pendaftaran</td>
                                <td>Dokter</td>
                                <td>Tinggi/Berat Badan</td>
                                <td>Tanggal Lahir/Umur</td>
                                <td>Cara Bayar/Penjamin</td>
                            </tr>
                            <tr>
                                <td><span class="tanggal"></span></td>
                                <td><span id="no_pendaftaran"></span></td>
                                <td><span id="dokter"></span></td>
                                <td><span id="tinggi_badan"></span> / <span id="berat_badan"></span></td>
                                <td><span id="tanggal_lahir"></span> / <span id="umur"></span></td>
                                <td><span id="carabayar"></span> - <span id="penjamin"></span></td>
                            </tr>

                            <tr>
                                <td>Nama Pasien</td>
                                <td>Ruangan-Kamar-No. Bed</td>
                                <td>Diagnosa Utama</td>
                                <td>Jenis Resep</td>
                                <td>Alergi</td>
                                <td>Tipe Resep</td>
                            </tr>
                            <tr>
                                <td><span id="nama_pasien"></span> / <span id="no_rm"></td>
                                <td><span id="ruangan_kamar_bed"></span></td>
                                <td><span id="diagnosa_utama"></span></td>
                                <td><span id="jenis_resep"></span></td>
                                <td id="alergi"><span id="alergi"></span></td>
                                <td><span id="tipe_resep"></span></td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-12">
                        <hr>
                    </div>

                    <div id="example" class="col-md-12 detail-resep">
                        <div class="cancel-receipt">
                            <div class="panel panel-danger">
                                <div class="panel-heading">
                                    <span class="panel-title" id="pesan_error"></span>
                                </div>
                            </div>
                        </div>

                        <div class="not-approved">
                            <div class="panel panel-warning">
                                <div class="panel-heading">
                                    <span class="panel-title" id="pesan_warning"></span>
                                </div>
                            </div>
                        </div>

                        <table class="table table-bordered table-condensed tbl-detail">
                            <thead class="bg-inverse">
                                <tr>
                                    <th width="1%">No</th>
                                    <th width="1%">Racikan</th>
                                    <th width="6%">R-ke</th>
                                    <th>Nama Obat Alkes</th>
                                    <th>Signa</th>
                                    <th>Qty Resep</th>
                                    <th>Qty Bayar</th>
                                    <th>Satuan</th>
                                    <th width="1%">Kronis</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody id="detail-resep">
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="status-worklist">
                        <div class="col-md-2 ">
                            <div class="panel">
                                <div class="panel-heading panel-title">Input</div>
                                <div class="panel-body">
                                    <div class="panel-text">
                                        <span class="tanggal"></span><br>
                                        <span class="pegawai_penginput bold"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2 ">
                            <div class="panel">
                                <div class="panel-heading panel-title">Bayar</div>
                                <div class="panel-body">
                                    <div class="panel-text">
                                        <span id="tgl_348"></span><br>
                                        <span id="pegawai_348" class="bold"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2 ">
                            <div class="panel">
                                <div class="panel-heading panel-title">Telaah</div>
                                <div class="panel-body">
                                    <div class="panel-text">
                                        <span id="tgl_676">-</span><br>
                                        <span id="pegawai_676" class="bold"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2 ">
                            <div class="panel">
                                <div class="panel-heading panel-title">Disiapkan</div>
                                <div class="panel-body">
                                    <div class="panel-text">
                                        <span id="tgl_675"></span><br>
                                        <span id="pegawai_675" class="bold"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2 ">
                            <div class="panel">
                                <div class="panel-heading panel-title">Diperiksa</div>
                                <div class="panel-body">
                                    <div class="panel-text">
                                        <span id="tgl_677"></span><br>
                                        <span id="pegawai_677" class="bold"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2 ">
                            <div class="panel">
                                <div class="panel-heading panel-title">Diserahkan</div>
                                <div class="panel-body">
                                    <div class="panel-text">
                                        <span id="tgl_660"></span><br>
                                        <span id="pegawai_660" class="bold"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12" style="margin-top: -1%; margin-bottom: -2%;">
                        <hr>
                    </div>

                    <div class="row search-pegawai">
                        <div class="col-md-12" style="margin-top: 1%">
                            <div class="row">
                                <div class="col-sm-3">
                                    <label for="search_pegawai" class="ol-form-label" id="pegawai">Pegawai :</label>
                                </div>
                                <div class="col-sm-3">
                                    <label for="waktu_tunggu_resep" class="col-form-label" id="waktu_tunggu_resep">Waktu Tunggu Resep :</label>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-3 select2-md">
                                    <select
                                        name="pegawai"
                                        id="search_pegawai"
                                        class="form-control select2"
                                        style="width: 100%">
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <h6><span id="waktu_tunggu"></span></h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    $this->registerJs($this->render('js/index.js'), View::POS_END);
?>
