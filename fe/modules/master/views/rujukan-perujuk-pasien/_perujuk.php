<?php
// Modify : Naufal Ziyad L

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = Yii::t('fe', 'Perujuk');
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel-heading">
            <h3 class="panel-title"><b><?=$this->title;?></b></h3>
        </div>
        <div class="panel-toolbar clearfix">
            <?=DocoHelpers::generateToolbar([
                'search' => [
                    'attributes'=>[
                        'data-parent' => '.filter-form-perujuk'
                    ]
                ],
                'reset'=> [
                    'attributes'=>[
                        'data-parent' => '.filter-form-perujuk'
                    ]
                ],
                'add' => [
                    'attributes' => [
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_backdrop',
                        'action' => 'perujuk/create',
                    ]
                ],
                'edit' => [
                    'attributes' => [
                        'data-options' => 'modal',
                        'data-target' => '#modal_backdrop',
                        'data-url' => 'perujuk/update?id=',
                    ]
                ],
                'delete' => [
                    'attributes' => [
                        'id' => 'btn-delete',
                        'data-target' => '/master/perujuk/delete?id=',
                        'action' => 'null_id'
                    ]
                ],
                'custom-print' => [
                    'type'=>'button',
                    'title' => Yii::t('fe', 'Cetak PDF'),
                    'icon' => 'fa fa-print',
                    'attributes' => [
                        'class' => 'print',
                        'id' => 'print-perujuk',
                        'method' => 'json',
                        'data-options' => 'link'
                    ],
                ],
                'custom-excel' => [
                    'title' => Yii::t('fe', 'Unduh Excel'),
                    'icon' => 'fa fa-file-excel-o',
                    'attributes' => [
                        'id' => 'excel-perujuk',
                        'data-options' => 'excel',
                    ],
                ],
            ],'#table-perujuk');?>
        </div>
        <div class="panel-body">
            <div class="col-md-12 filter-form-perujuk"></div>
            <table id="table-perujuk" class="table table-striped table-condensed table-hover" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th >No</th>
                        <th><?=Yii::t('fe', 'asalrujukan_id')?></th>
                        <th><?=Yii::t('fe', 'Asal Rujukan')?></th>
                        <th><?=Yii::t('fe', 'Nama Perujuk')?></th>
                        <th><?=Yii::t('fe', 'Kode Rujukan')?></th>
                        <th><?=Yii::t('fe', 'Spesialis')?></th>
                        <th><?=Yii::t('fe', 'Alamat')?></th>
                        <th><?=Yii::t('fe', 'Handphone')?></th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
<div id="modal_perujuk" class="modal fade" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
        </div>
    </div>
</div>

<script>
    var table2= $('#table-perujuk').docoTabel({
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
            ajax: baseUrl+"master/rujukan-perujuk-pasien/get-data-perujuk",
            columns: [
                {
                    title: "<?=Yii::t('fe', 'No')?>",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "<?=Yii::t('fe', 'Asal Rujukan')?>",  data: "asalrujukan_id", visible:false},
                {title: "<?=Yii::t('fe', 'Asal Rujukan')?>",  data: "asalrujukan_m.asalrujukan_nama", searchable:false},
                {title: "<?=Yii::t('fe', 'Nama Perujuk')?>",   data: "namaperujuk"},
                {title: "<?=Yii::t('fe', 'Kode Perujuk')?>",   data: "perujuk_kode"},
                {title: "<?=Yii::t('fe', 'Spesialis')?>",  data: "spesialis"},
                {title: "<?=Yii::t('fe', 'Alamat Lengkap')?>",  data: "alamatlengkap"},
                {title: "<?=Yii::t('fe', 'No Telp')?>",  data: "notelp"},
                {title: "<?=Yii::t('fe', 'Status')?>",  data: "is_active"}
            ]
        });
     $(".dataTables_filter").hide();
     $(".filter-form-perujuk").datatableBootstrapFilter(table2,
            [
                [1, '<?=(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('asalrujukan_id', '', $asal, ['class' => 'select2', 'prompt' => \Yii::t('fe', '--Pilih Asal Rujukan--')])));?>'],
                [8, '<?=(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $status, ['class' => 'select2', 'prompt' => \Yii::t('fe', '--Pilih Status--')])));?>']
            ],
            {
                1:1,
                4:2,
            },
            true
        );

    // On click tr
    $("#table-perujuk tbody").on("click", "tr", function(){
        try {
             var primaryKey = table2.row(".selected").data().primary ? table2.row(".selected").data().primary : null;
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
        table2.draw();
    });

        // Event Reload
    $(document).on("switchChange.bootstrapSwitch", ".change-status-perujuk", function (e, state) {

        var dataStatus = "0";
        var dataId = $(this).attr("data-id");

        if (e.target.checked == true)
            dataStatus = "1";
        
        $(this).docoForm("delete",{
            url: baseUrl+"master/perujuk/change-status-perujuk?id="+dataId+"&status="+dataStatus,
            confirmTitle : "<?=Yii::t('fe', 'Konfirmasi')?>",
            confirmMessage : "<?=Yii::t('fe', 'Apa anda yakin ingin mengubah status data ini ?')?>",
            success : function (data) {
                table2.draw();
            }
        });
        table2.draw();
    });

    // Print pdf
    $(document).on("click", "#print-perujuk", function() {
        window.open("/master/perujuk/export-pdf?"+$.param(table2.ajax.params()));
    });

    // Excel
    $('#excel-perujuk').click(function () {
        window.open('/master/perujuk/export-excel?'+$.param(table2.ajax.params()));
    });
</script>