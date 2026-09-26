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
                <th><?=\Yii::t("fe", "Tanggal pendaftaran");?></th>
                <th><?=\Yii::t("fe", "No pendaftaran");?></th>
                <th><?=\Yii::t("fe", "No rekam medik");?></th>
                <th><?=\Yii::t("fe", "Nama pasien");?></th>
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
        var value = $(this).data("value");
        $(".filter-form")
            .find("select[name=no_pendaftaran]")
            .html("<option value=\""+value+"\" selected>"+value+"</option>");
        $(".filter-form")
            .find("input[name=no_pendaftaran]")
            .val(value);
            
        var value = $(this).data("value2");
        $(".filter-form")
            .find("select[name=nama_pasien]")
            .html("<option value=\""+value+"\" selected>"+value+"</option>");
        $(".filter-form")
            .find("input[name=nama_pasien]")
            .val(value);
            
        var value = $(this).data("value3");
        $(".filter-form")
            .find("select[name=no_rekam_medik]")
            .html("<option value=\""+value+"\" selected>"+value+"</option>");
        $(".filter-form")
            .find("input[name=no_rekam_medik]")
            .val(value);
        
        $("#modal_backdrop_search").modal("hide");
        $('.selectPendaftaran').select2('data', {id: 100, text: 'RJ00001'});

    });

    // Event Ready
    $(document).ready(function() {
        //Generate Table
        tableSearch = $("#exampleFilter").docoTabel({
            filter: true,
            sorting: [[1, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"<?=Yii::$app->controller->module->id;?>/modal-search/get-data-pendaftaran",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "<?=(\Yii::t("fe", "Tanggal pendaftaran"));?>", data: "tgl_pendaftaran", searchable:false},
                {title: "<?=(\Yii::t("fe", "No pendaftaran"));?>", data: "no_pendaftaran"},
                {title: "<?=(\Yii::t("fe", "No rekam medik"));?>", data: "pasien_m.no_rekam_medik"},
                {title: "<?=(\Yii::t("fe", "Nama pasien"));?>", data: "pasien_m.nama_pasien"},
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
