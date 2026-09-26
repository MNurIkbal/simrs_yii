<?php

/**
 * @author : Anggoro (tri.anggoro@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\DepDrop;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Apotek', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<style>
    .worklist-header{
        font-weight: bold;
        font-size: 20px;
    }

    .worklist-no-resep{
        font-weight: bold;
        font-size: 16px;
    }

    .table-modal > tbody > tr > td,
    .table-modal > tbody > tr > th {
        padding: 5px !important;
        border: none;
    }

    .worklist-info-pasien{
        font-size: 14px;
    }

    #worklist-log {
        padding-top: 15px;
    }

    #worklist-log ul{
        list-style: none;
    }

    #worklist-log ul li{
        padding-bottom: 10px;
        text-align: left;
    }

    .badge-custom {
        background-color: transparent;
        border: 1px solid #28a745 !important;
        color: #28a745;
        border-radius: 0px !important;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= $title; ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <div class="col-md-12">
                    <?= DocoHelpers::generateToolbar([
                        'search',
                        'reset'
                    ]) ?>
                </div>
            </div>
            <div class="panel-body">
                <div class="row" style="margin-bottom: 15px">
                    <div class="col-md-4">
                        <div class="form-input" style="margin-left: 0; margin-right: 0">
                            <label class="control-label">Tanggal Transaksi</label>
                            <div class="input-group">
                                <input type="text" class="form-control startDate" id="rangeDemoStart" data-default='<?= date("d-M-Y") ?>' value='<?= date("d-M-Y") ?>' readonly="true">
                                <span class="input-group-addon" style="border-left: 0; border-right:0">-</span>
                                <input type="text" id="rangeDemoFinish" class="form-control endDate" data-default='<?= date("d-M-Y", strtotime("+1 days")) ?>' value='<?= date("d-M-Y") ?>' readonly="true">
                                <input type="text" class="targetDate" style="display:none">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-input" style="margin-left: 0; margin-right: 0">
                            <label class="control-label">No. Resep / No. Reseptur / Nama Pasien</label>
                            <input type="text" class="form-control searching no-autofill" autocomplete="off" id="search_noresep" placeholder="No. Resep / No. Reseptur / Nama Pasien">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="panel panel-worklist panel-penunjang">
                            <div class="panel-heading panel-title worklist-header">Penunjang</div>
                            <div class="panel-body panel-body-penunjang"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-worklist panel-rj">
                            <div class="panel-heading panel-title worklist-header">Rawat Jalan</div>
                            <div class="panel-body panel-body-rj"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-worklist panel-ranap">
                            <div class="panel-heading panel-title worklist-header">Rawat Inap</div>
                            <div class="panel-body panel-body-ranap"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="panel panel-worklist panel-igd">
                            <div class="panel-heading panel-title worklist-header">IGD</div>
                            <div class="panel-body panel-body-igd"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="resep-card-example" class="panel card-worklist" style="display: none">
    <div class="panel-body">
        <p class="worklist-no-resep"></p>
        <p class="worklist-info-pasien"><br>-</p>
        <p class="worklist-tipe-resep"></p>
        <p class="worklist-badges"></p>
        <p class="worklist-payment-status"></p>
    </div>
</div>

<!-- <a class="btn btn-primary" data-toggle="modal" href='#modal-id'>Trigger modal</a> -->
<div class="modal fade" id="modal-detail-worklist">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                <table class="table table-modal" style="border-bottom: 1px solid #000">
                    <tr>
                        <th>Reseptur</th>
                        <td>: <span id="detail-reseptur"></span></td>
                        <th>No. Resep</th>
                        <td>: <span id="detail-noresep"></span></td>
                        <th>Status</th>
                        <td id="detail-status">: <span class="badge badge-default"></span></td>
                    </tr>
                    <tr>
                        <th>Pasien</th>
                        <td>: <span id="detail-namaPasien"></span></td>
                        <th>Pendaftaran</th>
                        <td>: <span id="detail-nopendaftaran"></span></td>
                        <th>TB / BB</th>
                        <td>: <span id="detail-tb">-</span> cm / <span id="detail-bb">-</span> kg</td>
                    </tr>
                    <tr>
                        <th>Tanggal Lahir</th>
                        <td>: <span id="detail-tgllahir"></span></td>
                        <th>Dokter</th>
                        <td>: <span id="detail-dokter"></span></td>
                        <th>Alergi</th>
                        <td style="width: 200px">: <span id="detail-alergi"></span></td>
                    </tr>
                </table>

                <table class="table" id="table-detail-worklist">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Racikan</th>
                            <th>R Ke</th>
                            <th>Nama Obat Alkes</th>
                            <th>Signa</th>
                            <th>Qty</th>
                            <th>Satuan</th>
                            <th>DET</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer" style="padding-bottom: 0">
                <div class="col-md-6">
                    <div class="pull-left">
                        <button class="btn btn-default btn-cetak-etiket btn-info" data-jenis="oral" id="btn-oral">Cetak Oral</button>
                        <button class="btn btn-default btn-cetak-etiket btn-info" data-jenis="non-oral" id="btn-non-oral">Cetak Non Oral</button>
                        <button class="btn btn-default btn-cetak-resep btn-info" id="btn-resep">Cetak Resep</button>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-xs-12">
                            <button class="btn btn-default btn-status-worklist" data-work-stat="674" data-status="ditelaah" disabled="disabled" id="btn-ditelaah">Ditelaah</button>
                            <button class="btn btn-default btn-status-worklist" data-work-stat="676" data-status="disiapkan" disabled="disabled" id="btn-disiapkan">Disiapkan</button>
                            <button class="btn btn-default btn-status-worklist" data-work-stat="675" data-status="qc" disabled="disabled" id="btn-qc">QC</button>
                            <button class="btn btn-default btn-status-worklist" data-work-stat="677" data-status="siapSerahkan" disabled="disabled" id="btn-siapSerahkan">Siap diserahkan</button>
                        </div>
                        <div class="col-xs-12">
                            <div id="worklist-log" style="clear: both">
                                <ul>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    $this->registerJs($this->render('../assets/js/worklist/index.js'));
?>
