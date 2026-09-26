<?php
// Author : Ramdhan Nurrachman

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = \Yii::t('fe', 'Detail');
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$this->title;?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <h3 class="text-center">
        <?=\Yii::t('fe', 'DETAIL STOK OPNAME');?>
        <br />
        <?=\Yii::t('fe', 'BILLING KASIR');?>
        <br />
        <?=\Yii::t('fe', 'Periode');?> <?=date("d-m-Y");?>
        </h3>
    </div>
    <br />
    <table id="exampleDetail" class="table table-striped table-condensed table-hover" style="width:100%">
        <thead>
            <tr class="bg-inverse">
                <th width="1">No</th>
                <th><?=\Yii::t("fe", "Nama obat alkes");?></th>
                <th><?=\Yii::t("fe", "Stok fisik");?></th>
                <th><?=\Yii::t("fe", "Stok sistem");?></th>
                <th><?=\Yii::t("fe", "Selisih");?></th>
                <th><?=\Yii::t("fe", "Kondisi");?></th>
                <th><?=\Yii::t("fe", "Tanggal kadaluarsa");?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" colspan="7"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
            </tr>
        </tbody>
    </table>
</div>
<div class="modal-footer">
    <?=Html::a('<i class="fa fa-file-pdf-o"></i> '.\Yii::t('fe', 'Cetak').'', '#', [
        'class' => 'btn btn-crimson btn-sm data-export-all',
        'action' =>  Url::home().(Yii::$app->controller->module->id).'/inf-formulir-stok-opname/print-all'
    ]);?>
</div>
<script>
    var tableDetail;

    // Event Reload
    $(document).on("click", ".data-check", function() {
        var value = $(this).attr("data-value");
        $(".filter-form")
            .find("select[name=volume_fisik]")
            .html("<option value=\""+value+"\" selected>"+value+"</option>");
        $(".filter-form")
            .find("input[name=volume_fisik]")
            .val(value);
        
        $("#modal_backdrop_search").modal("hide");
    });

    // Event Ready
    $(document).ready(function() {
        // Generate Table
        tableDetail = $("#exampleDetail").docoTabel({
            filter: true,
            sorting: [[1, "asc"]], 
            displayLength: 1000, // Force 1000 rows
            processing: true,
            serverSide: true,
            ajax: baseUrl+"'.(Yii::$app->controller->module->id).'/inf-formulir-stok-opname/get-data-detail?parent_id=<?=$parent_id;?>",
            columns: [
                {title: "No", data: "rowNum", searchable: false, sortable: false},
                {title: "<?=(\Yii::t("fe", "Nama obat alkes"));?>", data: "obatalkes_m.obatalkes_namalain", searchable:false},
                {title: "<?=(\Yii::t("fe", "Stok fisik"));?>", data: "volume_fisik", searchable:false, "class":"text-right"},
                {title: "<?=(\Yii::t("fe", "Stok sistem"));?>", data: "volume_sistem", searchable:false, "class":"text-right"},
                {title: "<?=(\Yii::t("fe", "Selisih"));?>", data: "jmlselisihstok", searchable:false},
                {title: "<?=(\Yii::t("fe", "Kondisi"));?>", data: "kondisibarang", searchable:false},
                {title: "<?=(\Yii::t("fe", "Tanggal kadaluarsa"));?>", data: "obatalkes_m.tglkadaluarsa", searchable:false},
            ],
        });
        
        // Hardcode Hide
        $("#exampleDetail_wrapper .dataTables_filter").hide();
        $("#exampleDetail_wrapper .dataTables_length").hide();
        $("#exampleDetail_wrapper .dataTables_info").hide();
        $("#exampleDetail_wrapper .dataTables_paginate").hide();
        // $(".filter-form-modal").datatableBootstrapFilter(tableSearch);
    });
</script>
