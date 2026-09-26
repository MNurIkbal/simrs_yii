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
                <th><?=\Yii::t("fe", "Kode diagnosa");?></th>
                <th><?=\Yii::t("fe", "Nama diagnosa");?></th>
                <th width="1"><?=\Yii::t("fe", "");?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" colspan="4"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
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
            .find("select.diagnosa_nama")
            .html("<option value=\""+key+"\" selected>"+label+"</option>");
        $(sel_wrap)
            .find("input.diagnosa_nama")
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
            ajax: baseUrl+"<?=Yii::$app->controller->module->id;?>/modal-search/get-data-diagnosa?sel_wrap=<?=$sel_wrap;?>&assign_id=<?=$assign_id;?>",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "<?=(\Yii::t("fe", "Kode diagnosa"));?>", data: "diagnosa_kode"},
                {title: "<?=(\Yii::t("fe", "Nama diagnosa"));?>", data: "diagnosa_nama"},
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
