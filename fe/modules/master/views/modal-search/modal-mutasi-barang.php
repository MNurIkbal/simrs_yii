<?php
// Author : Ramdhan Nurrachman

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = \Yii::t('fe', 'Pencarian');
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$this->title;?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12 filter-form-modal"></div>
        <div class="col-md-12" style="margin-top:-15px;">
            <button type="button" class="btn btn-info btn-labeled btn-xs data-filter" data-parent=".filter-form-modal"><b><i class="fa fa-search"></i></b>Cari</button>
        </div>
    </div>
    <table id="exampleFilter" class="table table-striped table-condensed table-hover" style="width:100%">
        <thead>
            <tr class="bg-inverse">
                <th width="1">No</th>
                <th><?=\Yii::t("fe", "No mutasi");?></th>
                <th><?=\Yii::t("fe", "Tanggal mutasi");?></th>
                <th><?=\Yii::t("fe", "Status mutasi");?></th>
                <th width="1"><?=\Yii::t("fe", "");?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" colspan="6"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
            </tr>
        </tbody>
    </table>
</div>
<!--div class="modal-footer">
    <?=Html::button(\Yii::t('fe', 'Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
</div-->
<script>
    var tableSearch;

    // Event Reload
    $(document).on("click", ".data-check", function() {
        var sel_wrap = $(this).data("sel_wrap");

        var key = $(this).data("key");
        var label = $(this).data("label");
        $(sel_wrap)
            .find("select.nomutasi_barang")
            .html("<option value=\""+key+"\" selected>"+label+"</option>");
        $(sel_wrap)
            .find("input.nomutasi_barang")
            .val(label);
        
        $("#modal_backdrop_search").modal("hide");
    });

    // Event Ready
    $(document).ready(function() {
        // Generate Table
        tableSearch = $("#exampleFilter").docoTabel({
            filter: true,
            sorting: [[1, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"<?=Yii::$app->controller->module->id;?>/modal-search/get-data-mutasi-barang?sel_wrap=<?=$sel_wrap;?>&assign_id=<?=$assign_id;?>",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "<?=(\Yii::t("fe", "No mutasi"));?>", data: "nomutasi_barang"},
                {title: "<?=(\Yii::t("fe", "Tanggal mutasi"));?>", data: "tgl_mutasibarang"},
                {title: "<?=(\Yii::t("fe", "Status mutasi"));?>", data: "status_mutasi"},
                {
                    title: "<?=(\Yii::t("fe", ""));?>",
                    data: "check",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                }
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form-modal").datatableBootstrapFilter(tableSearch);
    });
</script>
