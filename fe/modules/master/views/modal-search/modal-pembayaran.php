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
                <th><?=\Yii::t("fe", "No pembayaran");?></th>
                <th><?=\Yii::t("fe", "Tanggal pembayaran");?></th>
                <th><?=\Yii::t("fe", "Tanggal pendaftaran");?></th>
                <th><?=\Yii::t("fe", "No pendaftaran");?></th>
                <th><?=\Yii::t("fe", "No rekam medik");?></th>
                <th><?=\Yii::t("fe", "Nama pasien");?></th>
                <th width="1"><?=\Yii::t("fe", "");?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" colspan="8"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
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

        var no_pembayaran = $(this).data("no_pembayaran");
        $(sel_wrap)
            .find("select.no_pembayaran")
            .html("<option value=\""+no_pembayaran+"\" selected>"+no_pembayaran+"</option>");
        $(sel_wrap)
            .find("input.no_pembayaran")
            .val(no_pendaftaran);

        var tgl_pembayaran = $(this).data("tgl_pembayaran");
        $(sel_wrap)
            .find("select.tgl_pembayaran")
            .html("<option value=\""+no_pembayaran+"\" selected>"+tgl_pembayaran+"</option>");
        $(sel_wrap)
            .find("input.tgl_pembayaran")
            .val(no_pendaftaran);

        var no_pendaftaran = $(this).data("no_pendaftaran");
        $(sel_wrap)
            .find("select.no_pendaftaran")
            .html("<option value=\""+no_pendaftaran+"\" selected>"+no_pendaftaran+"</option>");
        $(sel_wrap)
            .find("input.no_pendaftaran")
            .val(no_pendaftaran);

        var no_pendaftaran = $(this).data("no_pendaftaran");
        $(sel_wrap)
            .find("select.no_pendaftaran")
            .html("<option value=\""+no_pendaftaran+"\" selected>"+no_pendaftaran+"</option>");
        $(sel_wrap)
            .find("input.no_pendaftaran")
            .val(no_pendaftaran);
            
        var nama_pasien = $(this).data("nama_pasien");
        $(sel_wrap)
            .find("select.nama_pasien")
            .html("<option value=\""+nama_pasien+"\" selected>"+nama_pasien+"</option>");
        $(sel_wrap)
            .find("input.nama_pasien")
            .val(nama_pasien);
            
        var no_rekam_medik = $(this).data("no_rekam_medik");
        $(sel_wrap)
            .find("select.no_rekam_medik")
            .html("<option value=\""+no_rekam_medik+"\" selected>"+no_rekam_medik+"</option>");
        $(sel_wrap)
            .find("input.no_rekam_medik")
            .val(no_rekam_medik);
        
        $("#modal_backdrop_search_full").modal("hide");
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
            ajax: baseUrl+"<?=Yii::$app->controller->module->id;?>/modal-search/get-data-pembayaran",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "<?=(\Yii::t("fe", "No pembayaran"));?>", data: "no_pembayaran"},
                {title: "<?=(\Yii::t("fe", "Tanggal pembayaran"));?>", data: "tgl_pembayaran", searchable:false},
                {title: "<?=(\Yii::t("fe", "Tanggal pendaftaran"));?>", data: "pendaftaran_t.tgl_pendaftaran", searchable:false},
                {title: "<?=(\Yii::t("fe", "No pendaftaran"));?>", data: "pendaftaran_t.no_pendaftaran"},
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
