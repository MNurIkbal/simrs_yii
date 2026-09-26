<?php

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
$this->params['breadcrumbs'][] = ['label' => 'Asuransi Penjamin', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<style type="text/css">
    /*Style*/
    #table-detail {
        display: none;
        cursor: pointer;
    }

    td.colspan {
        padding: 0px !important;
    }

    .table-expand {
        font-size: 14px;
        margin-top: 0px;
        color: #2d2929;
        font-weight: 500;
    }

    #table-user-info {
        border-spacing: 0px;
    }

    #table-user-info .header-tbl {
        border-top: 2px solid #777;
    }

    #table-user-info>tbody>tr>td {
        border-top: 1px solid #bbb;
        padding: 10px !important;
    }

    #table-user-info>tbody>tr>td+td {
        border-top: 1px solid #bbb;
        border-left: 1px solid #bbb;
    }

    #table-user-info tr td.title {
        width: 17%;
        text-align: right;
    }

    #table-prosedur {
        border: 0 !important;
        border-spacing: 0;
    }

    #table-prosedur tr td.title {
        width: 17%;
        text-align: right;
    }

    #table-prosedur>tbody>tr>td {
        border-top: 1px solid #bbb;
        padding: 10px !important;
    }

    #table-prosedur>tbody>tr>td,
    #table-prosedur>th,
    #table-prosedur>thead>tr>td {
        border-top: 1px solid #ccc;
    }

    #table-prosedur>tbody>tr>td,
    #table-prosedur>th,
    #table-prosedur>thead>tr>td {
        border-left: 1px solid #ccc;
        border-top: 1px solid #ccc;
        padding: 0.6em;
    }

    .no-border-left {
        border-left: 0 !important;
    }

    tr td.contain {
        padding: 0px !important;
        border-top: 0 !important;
    }

    ul.prosedur-list {
        list-style: none;
        margin: 0px;
    }

    ul.prosedur-list li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 5px 0px;
    }

    #table-grouper {
        background: none;
    }

    #table-grouper>tbody>tr>td {
        border-top: 1px solid #bbb;
        padding: 10px !important;
    }

    .no-border-top {
        border-top: 0 !important;
    }

    .border-left {
        border-left: 1px solid #bbb;
    }

    .wrapper-top {
        width: 100%;
        margin-left: auto;
        margin-right: auto;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .info {
        display: flex;
        justify-content: flex-start;
        flex-direction: column;
        margin-right: 20px;
    }

    .info h3 {
        margin: 0;
    }

    .info label {
        text-align: left;
    }

    .diagnosa-label {
        padding: 0px 4px;
        border: 1px solid #888;
        margin-left: 5px;
        background-color: #ddffff;
        -moz-border-radius: 3px;
        border-radius: 3px;
        font-weight: bold;
    }

    .inner-column {
        width: 100%;
        display: flex;
        justify-content: space-around;
        align-items: center;
    }

    .action-click {
        text-decoration: underline;
    }

    .action-click:hover {
        color: #3b68d0;
    }

    .m-b-10 {
        margin-bottom: 10px !important;
    }

    .wrapper-total {
        width: 100%;
        height: 70px;
        display: flex;
        justify-content: flex-start;
        align-items: center;
        font-weight: bold;
        color: #37474f;
    }

    .select2-container .select2-selection--single {
        height: 35px !important;
    }

    .diagnosa-primer {
        background-color: #ffc09f;
    }

    .wrapper-label {
        display: flex;
        justify-content: flex-end;
        align-items: center;
    }

    .grand-total {
        color: #2d2929;
    }

    .success {
        background: #b5e4b5;
    }

    .success {
        background: #b5e4b5;
    }

    .warning {
        background: #ffcccc;
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
                        <h3 class="panel-title"><b>Transaksi Kirim Online</b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12 m-b-10">
                        <div class="form-action">
                            <button type="button" id="btn-cari" class="btn btn-info btn-labeled btn-xs btn-toolbar" data-options="click">
                                <b><i class="fa fa-search"></i></b>
                                Cari
                            </button>
                            <button type="button" id="btn-kirim-online" class="btn btn-info btn-labeled btn-xs btn-toolbar" data-options="click" disabled>
                                <b><i class="fa fa-paper-plane"></i></b>
                                Kirim Klaim (Online)
                            </button>
                            <button type="button" id="btn-kirim-online-batch" class="btn btn-info btn-labeled btn-xs btn-toolbar" data-options="click" disabled>
                                <b><i class="fa fa-paper-plane"></i></b>
                                Kirim Klaim Batch(Online)
                            </button>
                            <button type="button" id="btn-refresh" class="btn btn-info btn-labeled btn-xs btn-toolbar" data-options="click">
                                <b><i class="fa fa-refresh"></i></b>
                                Muat Ulang
                            </button>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">Tipe Klaim</label>
                            <?= Html::dropDownList(
                                'tipe',
                                '',
                                $tipe_klaim,
                                [
                                    'id' => 'tipe-klaim',
                                    'class' => 'form-control select2',
                                    'prompt' => '--- Pilih Tipe Klaim ---',
                                ]
                            ) ?>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">Filter</label>
                            <?= Html::dropDownList(
                                'filter',
                                'tgl_keluar',
                                $filter,
                                [
                                    'id' => 'tipe-tanggal',
                                    'class' => 'form-control select2',
                                    'prompt' => '--- Pilih Tanggal ---',
                                ]
                            ) ?>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="control-label">Tanggal</label>
                            <div class='input-group'>
                                <input type="text" value='<?php echo date('d-M-Y'); ?>' id='tanggal' class='form-control startDate' />
                                <span class='input-group-addon' style='border-left: 0; border-right: 0;'>
                                    <i class='fa fa-calendar'></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group wrapper-total">
                            <p>Total Klaim: <span id="total-klaim">0</span></p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <table id="table-total" class="table table-striped table-condensed table-hover" style="width: 100%;">
                            <thead>
                                <tr class="bg-inverse">
                                    <th colspan="3" class="text-center">Jumlah Klaim</th>
                                    <th colspan="2" class="text-center">Status Pengiriman</th>
                                    <th rowspan="2" class="text-center">Aksi</th>
                                </tr>
                                <tr class="bg-inverse">
                                    <th class="text-center">Rawat Jalan</th>
                                    <th class="text-center">Rawat Inap</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Belum Terkirim</th>
                                    <th class="text-center">Sudah Terkirim</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6" class="text-center"> Tidak ada data</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <table id="table-detail" class="table table-striped table-condensed table-hover" style="width: 100%;">
                            <thead>
                                <tr class="bg-inverse">
                                    <th class="text-center">No</th>
                                    <th class="text-center" width="120">Masuk</th>
                                    <th class="text-center" width="120">Pulang</th>
                                    <th class="text-center">No. SEP</th>
                                    <th class="text-center" width="200">Pasien</th>
                                    <th class="text-center">CBG / ICD Primary</th>
                                    <th class="text-center">Special Group</th>
                                    <th class="text-center">Tarif Klaim (Rp)</th>
                                    <th class="text-center">Tarif RS (Rp)</th>
                                    <th class="text-center">DC Kemkes / Status Kirim Online</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center" colspan="10"> Sedang Memuat Data...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-12">
                        <div id="modal_progress" class="modal fade" style="z-index:1065;" data-backdrop="static">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h5 class="modal-title">Kirim Klaim Batch</h5>
                                    </div>
                                    <hr>
                                    <center><span class="populate-data" style="font-size:16px;font-weight:bold;margin-bottom:10px;"></span></center>
                                    <div class="modal-body">
                                        <div class="progress progress-striped active">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"  aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                            <span class="label-persentase"></span>%</div>
                                        </div>
                                        <span class="help-block label-progress"></span>
                                    </div>
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
$this->registerJs("
    dateRangeHelper('.startDate');
    " . $this->render('_js.js'), View::POS_END, 'js');
?>
