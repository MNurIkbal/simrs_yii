<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-02-26 17:19:51
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-02-27 13:47:40
 */

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
                <th><?=\Yii::t("fe", "nama obat alkes");?></th>                                
                <th width="1"><?=\Yii::t("fe", "");?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" colspan="5"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
            </tr>
        </tbody>
    </table>
</div>
<script>
	  var tableSearch;

    // Event Reload
    $(document).on("click", ".data-check", function() {
        var sel_wrap = $(this).data("sel_wrap");        
        var key = $(this).data("key");
        var label = $(this).data("label");

        var newOption = new Option(label, key, false, false);		
        $(sel_wrap)
            .find("select.obatalkes_nama")
            .append(newOption).trigger('change');
        $('.obatalkes_nama').val(key).trigger('change');
        // $(sel_wrap)
        //     .find("input.nama_pegawai")
        //     .val(label);
        
        $("#modal_backdrop").modal("hide");
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
            ajax: "/apotek/laporan-pemakaian-obatalkes/get-data-obat",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "<?=(\Yii::t("fe", "Nama obat alkes"));?>", data: "obatalkes_namalain"},                
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
