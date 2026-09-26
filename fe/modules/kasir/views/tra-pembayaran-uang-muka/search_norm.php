<?php
// Author : Budi

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
    </div>
    <table id="exampleFilter" class="table table-striped table-condensed table-hover" style="width:100%">
        <thead>
            <tr class="bg-inverse">
                <th width="1">No</th>
                <th><?=\Yii::t("fe", "No rekam medik");?></th>
                <th><?=\Yii::t("fe", "Nama pasien");?></th>
                <th><?=\Yii::t("fe", "Tanggal lahir");?></th>
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

<script>
    var tableSearch;

    // Event Reload
    $(document).on("click", ".data-check", function() {
        var value = $(this).attr("data-value");
        var nama_pasien = $(this).attr("data-nama");

        $(".horizontal-form")
            .find("select[name=no_rekam_medik]")
            .html("<option value=\""+value+"\" selected>"+value+"</option>");
        $(".horizontal-form")
            .find("input[name=no_rekam_medik]")
            .val(value);

        $(".horizontal-form")
            .find("select[name=nama_pasien]")
            .html("<option value=\""+nama_pasien+"\" selected>"+nama_pasien+"</option>");
        $(".horizontal-form")
            .find("input[name=nama_pasien]")
            .val(nama_pasien);
        
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
            ajax: baseUrl+"'.(Yii::$app->controller->module->id).'/tra-pembayaran-uang-muka/get-data-pasien",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "<?=(\Yii::t("fe", "No rekam medik"));?>", data: "no_rekam_medik", searchable:false},
                {title: "<?=(\Yii::t("fe", "Nama pasien"));?>", data: "nama_pasien"},
                {title: "<?=(\Yii::t("fe", "Tanggal lahir"));?>", data: "tanggal_lahir"},
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
                    3, 
                    '<div class="form-group"><div class="input-group"><span class="input-group-addon"><i class="icon-calendar22"></i></span>'+
                    '<input type="text" class="form-control pickadate" value="" placeholder="<?=Yii::t('fe', 'Tanggal lahir') ?>"></div></div>'
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
