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
                <th><?=\Yii::t("fe", "Nomor Resep");?></th>
                <th><?=\Yii::t("fe", "Tanggal Penjualan");?></th>
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
            .find("select.noresep")
            .html("<option value=\""+key+"\" selected>"+label+"</option>");
        $(sel_wrap)
            .find("input.noresep")
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
            ajax: baseUrl+"<?=Yii::$app->controller->module->id;?>/modal-search/get-data-penjualan-resep?sel_wrap=<?=$sel_wrap;?>",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "<?=(\Yii::t("fe", "Nomor Resep"));?>", data: "noresep"},
                {title: "<?=(\Yii::t("fe", "Tanggal Penjualan"));?>", data: "tglpenjualan"},
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
        $(".filter-form-modal").datatableBootstrapFilter(tableSearch,
            [
                [
                    2, 
                    '<div class="form-group"><div class="input-group"><span class="input-group-addon"><i class="icon-calendar22"></i></span>'+
                    '<input type="text" class="form-control pickadate" value="" placeholder="<?= Yii::t('fe', 'Tanggal Penjualan') ?>" col-index="2"></div></div>'
                ]
            ]
        );

        $(document).ready(function(){
            $(".pickadate").pickadate({
                format: "dd mmm yyyy",
            });
        })
    });
</script>
