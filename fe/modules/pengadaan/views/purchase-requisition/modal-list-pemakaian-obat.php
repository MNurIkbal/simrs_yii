<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\View;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
?>

<style type="text/css">
</style>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <strong><h5 class="modal-title"><?= $title ?></h5></strong>
</div>
<div class="modal-body">
    <div class="panel-body">
        <div class="row">
            <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="pemakaian" style="width: 100%">
                <thead>
                    <tr class="bg-inverse">
                        <th class="text-center" width="5%">No</th>
                        <th><?= Yii::t('fe', 'Tanggal') ?></th>
                        <th><?= Yii::t('fe', 'Ruangan') ?></th>
                        <th><?= Yii::t('fe', 'Jumlah Pemakaian') ?></th>
                        <th><?= Yii::t('fe', 'Satuan') ?></th>
                        <th><?= Yii::t('fe', 'Jenis Pemakaian') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center" colspan="11"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <hr>
    <div class="modal-footer">
        <?= Html::button("<b><i class='fa fa-arrow-left'></i></b>&nbsp;Kembali",[
            'class' => 'btn btn-info btn-labeled btn-xs',
            'data-dismiss' => 'modal'
        ]); ?>
    </div>
</div>
<script>
var id = "<?= $id ?>";
var dataPemakaian = "<?= $dataPemakaian ?>";
var tablesPemakaian;
$(document).ready(function() {
    tablesPemakaian = $('#pemakaian').docoTabel({
        filter: true,
        sorting: [[3, 'asc']],
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: "/pengadaan/purchase-requisition/get-data-list-pemakaian?id="+id+"&data_pemakaian="+dataPemakaian,
        columns: [
            {
                title: 'No.',
                data: 'rowNum',
                searchable: false,
                orderable: false,
                class: 'text-center'
            },
            {
                title: 'Tanggal',
                data: 'tanggal_transaksi',
                searchable: false,
            },
            {
                title: 'Ruangan',
                data: 'ruangan_nama',
                searchable: false,
            },
            {
                title: 'Jumlah Pemakaian',
                data: 'jumlah_pemakaian',
                searchable: false,
                class :'text-right'
            },
            {
                title: 'Satuan',
                data: 'satuanunit_nama',
                searchable: false,
            },
            {
                title: 'Jenis Pemakaian',
                data: 'jenis_pemakaian',
                searchable: false,
            },
        ]
    });
    $('.dataTables_filter').hide();
})
</script>