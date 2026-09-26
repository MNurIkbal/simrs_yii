<?php
// Author : Naufal Ziyad L

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = Yii::t('fe', 'Asal Rujukan');
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel-heading">
           <h3 class="panel-title"><b><?=$this->title;?></b></h3>
            <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse"></a></li>
                </ul>
            </div>
        </div>
        <div class="panel-toolbar clearfix">
            <?=DocoHelpers::generateToolbar([
                    'search'=> [
                        'attributes'=>[
                            'data-parent' => '.filter-form-asalrujukan'
                        ]
                    ],
                    'reset'=> [
                        'attributes'=>[
                            'data-parent' => '.filter-form-asalrujukan'
                        ]
                    ],
                    'add' => [
                        'attributes' => [
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'action' => 'asal-rujukan/create',
                        ]
                    ],
                    'edit' => [
                        'attributes' => [
                            'data-options' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'data-url' => 'asal-rujukan/update?id=',
                        ]
                    ],
                    'delete' => [
                        'attributes' => [
                            'id' => 'btn-delete',
                            'data-target' => '/master/asal-rujukan/delete?id=',
                            'action' => 'null_id'
                        ]
                    ],
                    'custom-excel' => [
                        'title' => Yii::t('fe', 'Unduh Excel'),
                        'icon' => 'fa fa-file-excel-o',
                        'attributes' => [
                            'id' => 'excel-asal-rujukan',
                            'data-options' => 'excel',
                        ],
                    ],
                    'pdf'=> [
                        'attributes'=>[
                            'data-parent' => '.filter-form-asalrujukan',
                            'data-target' => '/master/asal-rujukan/export-pdf?'
                        ]
                    ],  
                ],'#table-asalrujukan');?>    
        </div>
        <br>
        <div class="panel-body">
            <div class="col-md-12 filter-form-asalrujukan"></div>
            <table id="table-asalrujukan" class="table table-striped table-condensed table-hover" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th><?= Yii::t('fe', 'No') ?></th>
                        <th><?=Yii::t('fe', 'Asal Rujukan')?></th>
                        <th><?=Yii::t('fe', 'Asal Rujukan Kode')?></th>
                        <th><?=Yii::t('fe', 'Institusi Asal Rujukan')?></th>
                        <th><?=Yii::t('fe', 'Nama Lainnya')?></th>
                        <th><?= Yii::t('fe', 'Status') ?></th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
<div id="modal_asalrujukan" class="modal fade" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
        </div>
    </div>
</div>

<script>
    generateFilter("filter-form-asalrujukan", "filter-rujukan");
    var tableAsalRujukan= $('#table-asalrujukan').docoTabel({
        columnDefs: [ {
            orderable: false,
            className: 'select-checkbox',
            targets:   0,
            width: "10%"
        }],
        select: {
            style:    'os',
            selector: 'tr'
        },
        filter: true,
        sorting: [[1, "asc"]], 
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: baseUrl+"master/rujukan-perujuk-pasien/get-data-asal-rujukan",
        columns: [
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {title: "<?=Yii::t('fe', 'Asal Rujukan')?>",  data: "asalrujukan_nama"},
            {title: "<?=Yii::t('fe', 'Asal Rujukan Kode')?>",  data: "asalrujukan_kode"},
            {title: "<?=Yii::t('fe', 'Institusi Asal Rujukan')?>",  data: "asalrujukan_institusi"},
            {title: "<?=Yii::t('fe', 'Nama Lainnya')?>",  data: "asalrujukan_namalainnya"},
            {title: "Status",  data: "is_active"}
        ],
       scrollCollapse: true,
        fixedColumns: {
        leftColumns: 2,
        }
    });
     $(".dataTables_filter").hide();
     $(".filter-rujukan").datatableBootstrapFilter(tableAsalRujukan, 
            [
                [5, '<?=(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $status, ['class' => 'select2', 'prompt' => \Yii::t('fe', '--Pilih Status--')])));?>']
            ], {
                1:0,
                2:1,
                3:2,
                4:3,
                5:4,
        }, true);

     // On click tr
    $("#table-asalrujukan tbody").on("click", "tr", function(){
        try {
             var primaryKey = tableAsalRujukan.row(".selected").data().primary ? tableAsalRujukan.row(".selected").data().primary : null;
        } catch (e) {
            var primaryKey = false;
        }

        // Check primary
        if (primaryKey) {
            $("#btn-delete").attr("action", $("#btn-delete").data("target") + primaryKey);
        }
    });

   

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        tableAsalRujukan.draw();
    });

     // Event Reload
    $(document).on("switchChange.bootstrapSwitch", ".change-status", function (e, state) {

        var dataStatus = "0";
        var dataId = $(this).attr("data-id");

        if (e.target.checked == true)
            dataStatus = "1";
        
        $(this).docoForm("delete",{
            url: baseUrl+"master/asal-rujukan/change-status?id="+dataId+"&status="+dataStatus,
            confirmTitle : "<?=Yii::t('fe', 'Konfirmasi')?>",
            confirmMessage : "<?=Yii::t('fe', 'Apa anda yakin ingin mengubah status data ini ?')?>",
            success : function (data) {
                tableAsalRujukan.draw();
            }
        });
        tableAsalRujukan.draw();
    });

    // Print pdf
    $(document).on("click", "#print-asal-rujukan", function() {
        window.open("/master/asal-rujukan/export-pdf?"+$.param(tableAsalRujukan.ajax.params()));
    });

    // Excel
    $('#excel-asal-rujukan').click(function () {
        // var 
        window.open('/master/asal-rujukan/export-excel?'+$.param(tableAsalRujukan.ajax.params()));
    });
</script>
