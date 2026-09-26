<?php
// Author : Ardi Pratama

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = Yii::t('fe', 'Golongan Umur');
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
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
                        'search',
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-form'
                            ]
                        ],
                        'add' => [
                            'attributes' => [
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'action' => '/master/identitas/create-gol-umur',
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'id' => 'edit_gol_umur',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_golonganumur',
                                'data-url' => '/master/identitas/update-gol-umur?id=',
                                'action' => '/master/identitas/unknown-id'
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                                'id' => 'data-delete-golonganumur',
                                'data-target' => '/master/identitas/delete-gol-umur?id=',
                                'action' => 'null_id'
                            ]
                        ],
                        'pdf' => [
                            'attributes' => [
                                'data-target' => '/master/identitas/cetak-golongan?',
                            ]
                        ],
                        'excel' => ['attributes' => ['data-target'=>'/master/identitas/export-excel?type=2&']],
                    ], '#table-golonganumur');?>    
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form-golonganumur"></div>
                </div>
                <table id="table-golonganumur" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th><?=Yii::t('fe', 'Golongan Umur')?></th>
                            <th><?=Yii::t('fe', 'Usia')?></th>
                            <th><?=Yii::t('fe', 'Umur Minimal')?></th>
                            <th><?=Yii::t('fe', 'Umur Maksimal')?></th>
                            <th>Status</th>
                            <th><?=Yii::t('fe', 'Aksi')?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="5">Data tidak ditemukan.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
    </div>
</div>

<div id="modal_golonganumur" class="modal fade" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
        </div>
    </div>
</div>

<script>
    var table;
    $(document).ready(function() {
        table= $('#table-golonganumur').docoTabel({
                filter: true,
                columnDefs: [ {
                    orderable: false,
                    className: "select-checkbox",
                    targets:   0
                }],
                select: {
                    style:    "os",
                    selector: "td:first-child"
                },
                sorting: [[1, "asc"]], 
                displayLength: 10,
                processing: true,
                serverSide: true,
                ajax: baseUrl+"master/identitas/get-data-umur",
                columns: [
                    {
                        title: "No",
                        data: "rowNum",
                        searchable: false,
                        orderable: false
                    },
                    {title: "<?=Yii::t('fe', 'Golongan Umur')?>",   data: "golonganumur_nama", name: "golonganumur_m.golonganumur_nama"},
                    {title: "<?=Yii::t('fe', 'Usia')?>",  data: "golonganumur_namalainnya", name: "golonganumur_m.golonganumur_namalainnya"},
                    {title: "<?=Yii::t('fe', 'Umur Minimal')?>",  data: "golonganumur_minimal", name: "golonganumur_m.golonganumur_minimal"},
                    {title: "<?=Yii::t('fe', 'Umur Maksimal')?>",   data: "golonganumur_maksimal", name: "golonganumur_m.golonganumur_maksimal"},
                    {title: "Status",  data: "is_active"}
                ]
            });
         $(".dataTables_filter").hide();
         $(".filter-form-golonganumur").datatableBootstrapFilter(table, 
                [
                    [5, '<?=(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $status, ['class' => 'select2', 'prompt' => \Yii::t('fe', 'Pilih')])));?>']
                ]
            );
          var primaryKey;

        $("#table-golonganumur tbody").on("click", "tr", function(){
            try {
                primaryKey = table.row(".selected").data().primary ? table.row(".selected").data().primary : null;
            } catch (e) {
                primaryKey = false;
            }


            if (primaryKey) {
                $("#edit_gol_umur").attr("action",$("#edit_gol_umur").data("url")+primaryKey);
                $("#data-delete-golonganumur").attr("action",$("#data-delete-golonganumur").data("target")+primaryKey);
            } else {
                $("#edit_gol_umur").attr("action",'/master/identitas/unknown-id');
                $("#data-delete-golonganumur").attr("action",'null_id');
            }
            
        });
    });
    
    // Event Reload
    $(document).on("click", ".data-reset", function() {
        table.draw();
    });
</script>