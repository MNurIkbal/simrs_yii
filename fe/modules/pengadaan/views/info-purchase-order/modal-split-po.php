<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\View;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
?>

<style type="text/css">
    .table > tbody > tr > td {
        padding: 12px 9px;
    }
</style>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <strong><h5 class="modal-title"><?= $title ?></h5></strong>
</div>

<div class="modal-body">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h6 class="panel-title">Info Data Obat / Barang</h6>
        </div>
        <div class="panel-body">
            <br>
            <div class="row">
                <div class="col-md-6">
                    <label class="text-left control-label col-sm-3">
                        <b><?= Yii::t("fe", "Nama Obat / Barang") ?></b>
                    </label>
                    <div class="col-sm-9">
                        <p>
                            <b>:</b>&nbsp;<?= $data['kode_item'] . ' - ' . $data['obat_barang_nama'] ?>
                        </p>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="text-left control-label col-sm-3">
                        <b><?= Yii::t("fe", "Qty PO Asal") ?></b>
                    </label>
                    <div class="col-sm-9">
                        <p>
                            <b>:</b>&nbsp;<?= $data['qty_input'] . ' ' . $data['satuan_besar'] ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <table class="table table-striped table-condensed table-hover" style="width:100%" id="table-po">
        <thead>
            <tr class="bg-inverse">
                <th class="text-center" width="1">No</th>
                <th class="text-center" style="width: 40%">Nama Supplier</th>
                <th class="text-center" style="width: 15%">Qty</th>
                <th class="text-center" style="width: 10%">Satuan</th>
                <th class="text-center" style="width: 10%">Harga (Rp.)</th>
                <th class="text-center" style="width: 25%">Catatan</th>
                <th class="text-center" width="1">Aksi</th>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <?= Html::dropDownList(
                        'nama_supplier', '', ArrayHelper::map($list_supplier, 'supplier_id', 'supplier_nama'),[
                                'class' => 'form-control select2',
                                'prompt' => '-- Pilih --'
                            ]
                        ) 
                    ?>
                </td>
                <td><input name="qty" type="text" class="form-control doco-number" style="width: 100%"></td>
                <td><?= $data['satuan_besar'] ?></td>
                <td><p id="harga"> - </p></td>
                <td><textarea name="catatan" cols="1" rows="1" class="form-control" style="width: 100%; resize: none;"></textarea></td>
                <td>
                    <button class="btn btn-sm btn-success" id="btn-tambah"><i class="fa fa-plus"></i></button>
                </td>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    <hr>
</div>

<div class="modal-footer">
    <?= Html::button("<b><i class='fa fa-arrow-left'></i></b>&nbsp;Kembali",[
        'class' => 'btn btn-info btn-labeled btn-xs',
        'data-dismiss' => 'modal'
    ]); ?>
    <?= Html::button("<b><i class='fa fa-floppy-o'></i></b>&nbsp;Simpan",[
        'class' => 'btn btn-info btn-labeled btn-xs',
        'id' => "btn-submit-split-po"
    ]); ?>
</div>

<?php
$this->registerJs('
    var max_qty = '.$data['qty_input'].';
    var satuan = "'.$data['satuan_besar'].'";
    var satuan_id = "'.$data['satuan_besar_id'].'";
    var obat_barang_id = "'.$data['obat_barang_id'].'";
    var po_detail = "'.$data['id_detail'].'";
    var pr_nomor = "'.$data['no_pr'].'";
    var list_supplier = '.json_encode($list_supplier).';
    var list_konversi = '.json_encode($list_konversi).';
    var type_po = "'. $type_po .'";
', View::POS_END);

$this->registerJs($this->render("assets/js/split-po.js"));
?>
