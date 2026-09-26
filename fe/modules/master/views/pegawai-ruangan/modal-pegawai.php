<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-02-23 11:12:34
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-02-23 14:44:18
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
    <div class="panel-body">
        <table id="exampleFilter" class="table table-striped table-condensed table-hover" style="width:100%">
            <div class="row">
                <div class="col-md-12 filter-form-modal"></div>
            </div>
            <thead>
                <tr class="bg-inverse">
                    <th width="1">No</th>
                    <th><?=\Yii::t("fe", "Instalasi");?></th>
                    <th><?=\Yii::t("fe", "Ruangan");?></th>
                    <th><?=\Yii::t("fe", "Nama pegawai");?></th>
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

        var newOption = new Option(label, key, false, false);		
        $(sel_wrap)
            .find("select.pegawai_nama")
            .append(newOption).trigger('change');
        $('.pegawai_nama').val(key).trigger('change');
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
            ajax: baseUrl+"master/pegawai-ruangan/get-pegawai",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "<?=(\Yii::t("fe", "Instalasi"));?>", data: "instalasi_nama"},
                {title: "<?=(\Yii::t("fe", "Ruangan"));?>", data: "ruangan_nama"},
                {title: "<?=(\Yii::t("fe", "Nama pegawai"));?>", data: "nama_pegawai"},
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