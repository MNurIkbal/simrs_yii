<?php
// Modify : Naufal Ziyad L

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = Yii::t('fe', 'Rujukan Keluar');
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
                'search'=> [
                    'attributes'=>[
                        'data-parent' => '.filter-form-rujukan-keluar'
                    ]
                ],
                'reset'=> [
                    'attributes'=>[
                        'data-parent' => '.filter-form-rujukan-keluar'
                    ]
                ],
                'add' => [
                    'attributes' => [
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_backdrop',
                        'action' => 'rujukan-keluar/create',
                    ]
                ],
                'edit' => [
                    'attributes' => [
                        'data-options' => 'modal',
                        'data-target' => '#modal_backdrop',
                        'data-url' => 'rujukan-keluar/update?id=',
                    ]
                ],
                'delete' => [
                    'attributes' => [
                        'id' => 'btn-delete',
                        'data-target' => '/master/rujukan-keluar/delete?id=',
                        'action' => 'null_id'
                    ]
                ],
                'pdf' => [
                    'attributes' => [
                        'url' => '/master/rujukan-keluar/export-pdf?',
                    ]
                ],
                'excel' => [
                    'attributes' => [
                        'url' => '/master/rujukan-keluar/export-excel?',
                    ]
                ],
            ],'#table-rujukan-keluar');?>    
        </div>
        <div class="panel-body">
            <div class="col-md-12 filter-form-rujukan-keluar"></div>
            <table id="table-rujukan-keluar" class="table table-striped table-condensed table-hover" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th><?=Yii::t('fe', 'No')?></th>
                        <th><?=Yii::t('fe', 'asalrujukan_id')?></th>
                        <th><?=Yii::t('fe', 'Asal Rujukan')?></th>
                        <th><?=Yii::t('fe', 'Rumah Sakit Rujukan')?></th>
                        <th><?=Yii::t('fe', 'Alamat RS Rujukan')?></th>
                        <th><?=Yii::t('fe', 'Telp')?></th>
                        <th><?=Yii::t('fe', 'Status')?></th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
<div id="modal_rujukankeluar" class="modal fade" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
        </div>
    </div>
</div>


<script>
    var tableRujukanKeluar= $('#table-rujukan-keluar').docoTabel({
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
            ajax: baseUrl+"master/rujukan-perujuk-pasien/get-data-rujukan-keluar",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "<?=Yii::t('fe', 'Asal Rujukan')?>",  data: "asalrujukan_id", visible:false},
                {title: "<?=Yii::t('fe', 'Asal Rujukan')?>",  data: "asalrujukan_m.asalrujukan_nama", searchable:false},
                {title: "<?=Yii::t('fe', 'Rumah Sakit Rujukan')?>",   data: "rumahsakit_rujukan"},
                {title: "<?=Yii::t('fe', 'Alamat Lengkap')?>",  data: "alamat_rsrujukan"},
                {title: "<?=Yii::t('fe', 'No Telp')?>",  data: "telp_fax"},
                {title: "<?= Yii::t('fe', 'Status') ?>",  data: "is_active"}
            ]
        });
     $(".dataTables_filter").hide();
     $(".filter-form-rujukan-keluar").datatableBootstrapFilter(tableRujukanKeluar, 
            [
                [1, '<?=(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('asalrujukan_id', '', $asal, ['class' => 'select2', 'prompt' => \Yii::t('fe', '--Pilih Asal Rujukan--')])));?>'],
                [6, '<?=(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $status, ['class' => 'select2', 'prompt' => \Yii::t('fe', '--Pilih Status--')])));?>']
            ],
            {
                1:0,
            },
            true
        );

    // On click tr
    $("#table-rujukan-keluar tbody").on("click", "tr", function(){
        try {
             var primaryKey = tableRujukanKeluar.row(".selected").data().primary ? tableRujukanKeluar.row(".selected").data().primary : null;
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
        tableRujukanKeluar.draw();
    });

    // Event Switch Status
    $(document).on("switchChange.bootstrapSwitch", ".change-status-rujukan-keluar", function (e, state) {

        var dataStatus = "0";
        var dataId = $(this).attr("data-id");

        if (e.target.checked == true)
            dataStatus = "1";
        
        $(this).docoForm("delete",{
            url: baseUrl+"master/rujukan-keluar/change-status-rujukan-keluar?id="+dataId+"&status="+dataStatus,
            confirmTitle : "<?=Yii::t('fe', 'Konfirmasi')?>",
            confirmMessage : "<?=Yii::t('fe', 'Apa anda yakin ingin mengubah status data ini ?')?>",
            success : function (data) {
                tableRujukanKeluar.draw();
            }
        });
        tableRujukanKeluar.draw();
    });
</script>