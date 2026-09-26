<?php
// Author : Ardi Pratama

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = Yii::t('fe', 'Cara Masuk');
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
                <div class="btn-group pull-left">
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
                                'action' => '/master/identitas/create-cara-masuk',
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'id' => 'edit_cara_masuk',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_caramasuk',
                                'data-url' => '/master/identitas/update-cara-masuk?id=',
                                'action' => '/master/identitas/unknown-id'
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                                'id' => 'data-delete-caramasuk',
                                'data-target' => '/master/identitas/delete-cara-masuk?id=',
                                'action' => 'null_id'
                            ]
                        ],
                        'pdf' => [
                            'attributes' => [
                                'data-target' => '/master/identitas/cetak-cara-masuk?',
                            ]
                        ],
                        'excel' => ['attributes' => ['data-target'=>'/master/identitas/export-excel?type=1&']],
                    ], '#table-caramasuk');?>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form-caramasuk"></div>
                </div>
                <div class="form-group">
                </div>
                <table id="table-caramasuk" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th>No</th>
                            <th><?=Yii::t('fe', 'Cara Masuk')?></th>
                            <th><?=Yii::t('fe', 'Nama Lainnya')?></th>
                            <th>Status</th>
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

<div id="modal_caramasuk" class="modal fade" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
        </div>
    </div>
</div>

<script>
    var table3;
    // Event Ready
    $(document).ready(function() {
        table3 = $('#table-caramasuk').docoTabel({
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
                // stateSave: true,
                //scrollX: true,
                ajax: baseUrl+"master/identitas/get-data-masuk",
                columns: [
                    {
                        title: "No",
                        data: "rowNum",
                        searchable: false,
                        orderable: false
                    },
                    {title: "<?=Yii::t('fe', 'Cara Masuk')?>",  data: "caramasuk_nama"},
                    {title: "<?=Yii::t('fe', 'Nama Lainnya')?>",  data: "caramasuk_namalainnya"},
                    {title: "Status",  data: "is_active"}
                ]
            });
        $(".dataTables_filter").hide();
        $(".filter-form-caramasuk").datatableBootstrapFilter(table3, 
                [
                    [3, '<?=(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $status, ['class' => 'select2', 'prompt' => \Yii::t('fe', 'Pilih')])));?>']
                ]
        );

         var primaryKey;

        $("#table-caramasuk tbody").on("click", "tr", function(){
            try {
                primaryKey = table3.row(".selected").data().primary ? table3.row(".selected").data().primary : null;
            } catch (e) {
                primaryKey = false;
            }


            if (primaryKey) {
                $("#edit_cara_masuk").attr("action",$("#edit_cara_masuk").data("url")+primaryKey);
                $("#data-delete-caramasuk").attr("action",$("#data-delete-caramasuk").data("target")+primaryKey);
            } else {
                $("#edit_cara_masuk").attr("action",'/master/identitas/unknown-id');
                $("#data-delete-caramasuk").attr("action",'null_id');
            }
            
        });
    });
   

    // Event Reload
    $(document).on("click", ".data-reset", function() {
        table3.draw();
    });
</script>