<?php
// Author : Budi

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
$this->title = \Yii::t('fe', 'Pencarian');
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$this->title;?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <?=DocoHelpers::generateToolbar([
            'search'=>[
                'attributes'=>[
                    'data-parent'=>'.filter-form-modal',                    
                    ]
                ],
            'reset'
            ])?>     
        </div>
       
    </div>
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
        var sel_wrap  = '.penerimaan-form';
        var key = $(this).data("key");
        var label = $(this).data("label");
        $(sel_wrap)
            .find(".selectMengetahui")
            .html("<option value=\""+key+"\" selected>"+label+"</option>");       
        
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
            ajax: baseUrl+"apotek/informasi-mutasi/get-data-pegawai",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "<?=(\Yii::t("fe", "Nip"));?>", data: "nomorindukpegawai"},
                {title: "<?=(\Yii::t("fe", "Nama pegawai"));?>", data: "nama_pegawai"},
                {title: "<?=(\Yii::t("fe", "Jabatan"));?>", data: "jabatan_nama"},
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

        $(document).ready(function(){
            $(".pickadate").pickadate({
                format: "dd mmm yyyy",
            });
        })
    });
</script>
