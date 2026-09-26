<?php 
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use app\components\DocoHelpers;
?>
<style>
     .my-legend .legend-title {
        margin-bottom: 8px;
        font-weight: bold;
        font-size: 11px;
    }

    .my-legend .legend-scale ul {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .my-legend .legend-scale ul li {
        display: block;
        float: left;
        margin-bottom: 6px;
        margin-right: 5px;
        text-align: center;
        font-size: 10px;
        list-style: none;
    }

    .my-legend ul.legend-labels li span {
        display: block;
        float: left;
        border: 1px solid #616161;
        padding: 4px 10px;
        color: #191919;
    }

    .my-legend .legend-source {
        font-size: 70%;
        color: #999;
        clear: both;
    }

    .my-legend a {
        color: #777;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class='my-legend'>
            <div class='legend-title'>Keterangan</div>
            <div class='legend-scale'>
                <ul class='legend-labels'>
                    <li><span style='background:#FFC0CB;'>Item Tidak Ditemukan</span></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="panel panel-default panel-bordered">
            <div class="panel-heading">
                <h6 class="panel-title"><?= Yii::t('fe', 'Item Integrasi') ?></h6>
                <div class="heading-elements">
                    <ul class="icons-list"></ul>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-striped table-condensed table-hover" id="table-tindakan" style="width: 100%">
                            <thead>
                                <tr class="bg-inverse">
                                    <th>No</th>
                                    <th>Detail</th>
                                    <th>Item</th>
                                    <th>No Pendaftaran</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Sub Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <br><br>
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-3">
                            <h5>Total Biaya DH (Rp)</h5>
                            <input type="text" id="sumTotal" class="form-control" readonly>
                        </div>
                        <div class="col-md-3">
                            <h5>Total Biaya Asuransi Yang Sudah Dikirim (Rp)</h5>
                            <input type="text" id="sumSudahKirim" class="form-control" readonly>
                        </div>
                        <?php if (! $isCob) : ?>
                            <div class="col-md-3">
                                <h5>Total Dijamin (Rp)</h5>
                                <input type="text" id="sumDijamin" class="form-control" readonly>
                            </div>
                        <?php endif; ?>
                        <?php if ($isCob) : ?>
                            <div class="col-md-3">
                                <h5>Total Dijamin BPJS (Rp)</h5>
                                <input type="text" id="sumInacbgs" class="form-control" readonly>
                            </div>
                            <div class="col-md-3">
                                <h5>Total Dijamin Asuransi (COB) (Rp)</h5>
                                <input type="text" id="sumCob" class="form-control" readonly>
                            </div>
                        <?php endif; ?>
                        <div class="col-md-3">
                            <h5>Total Harus Dibayar Pasien (Rp)</h5>
                            <input type="text" id="sumDibayarPasien" class="form-control" readonly>
                        </div>
                        <div class="col-md-3">
                            <h5>Selisih (Data Yang Belum Dikirim)</h5>
                            <input type="text" id="selisih" class="form-control" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
