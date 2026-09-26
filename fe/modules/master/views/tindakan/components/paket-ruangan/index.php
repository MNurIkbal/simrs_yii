<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-04-27 11:35:09
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-08-07 14:24:48
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\modules\master\models\CaraBayarForm;
use Doco\master\controllers\CaraBayarController;

$this->title = $title;

?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-paket-ruangan'
                            ]
                        ],
                        // 'tambah' => [
                        //     'title'=>'Tambah',
                        //     'icon' => 'fa fa-plus',
                        //     'attributes' => [
                        //         'class' => 'spa',
                        //         'data-options' => 'click',
                        //         'data-render' => 'buat-paket-ruangan',
                        //         'data-tab' => 'tab-paket-ruangan',
                        //         'data-target' => '#view-paket-ruangan',
                        //     ]
                        // ],
                        'update' => [
                            'title' => 'Mapping',
                            'icon' => 'fa fa-map-pin',
                            'attributes' => [
                                // 'data-toggle' => 'modal',
                                // 'data-target' => '#modal_backdrop',
                                // 'data-url' => '/master/cara-bayar/update?id=',
                                'class' => 'spa btn btn-info btn-labeled btn-xs btn-toolbar',
                                'data-options' => 'click',
                                'data-render' => 'ubah-paket-ruangan?id=',
                                'data-tab' => 'tab-paket-ruangan',
                                'data-target' => '#view-paket-ruangan',
                                'data-type' => 'wp'

                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                                'data-target' => '/master/tindakan/hapus-paket-ruangan?id='
                            ]
                        ],
                        'pdf' => [
                            'attributes' => [
                                'data-target' => '/master/tindakan/export-pdf-paket-ruangan?'
                            ]
                        ],
                        'excel' => [
                            'attributes' => [
                                'data-target' => '/master/tindakan/export-excel-paket-ruangan?'
                            ]
                        ],
                        'copy' => [
                            'title' => 'Salin Ruangan',
                            'icon' => 'fa fa-copy',
                            'attributes' => [
                                    'id'=>'btn-copy',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal_backdrop',
                                    'action' => '/master/tindakan/copy-paket-ruangan?id=',
                                    'disabled'=>true
                                // 'class' => 'spa btn btn-info btn-labeled btn-xs btn-toolbar',
                                
                                // 'data-options' => 'click',
                                // 'data-render' => 'ubah-paket-ruangan?id=',
                                // 'data-tab' => 'tab-paket-ruangan',
                                // 'data-target' => '#view-paket-ruangan',
                                // 'data-type' => 'wp'

                            ]
                        ],
                    ],'#table-paket-ruangan');?>    
            
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-paket-ruangan"></div>
                </div>
                <table id="table-paket-ruangan" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="1"><?= \Yii::t("fe", "No"); ?></th>
                            <th width="80px"><?= \Yii::t("fe", "Detail"); ?></th>
                            <th><?=\Yii::t("fe", "Nama Ruangan");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="4"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    
</script>
<?php 
$this->registerJs('
    // Global Var
    var tablePaket;
    var url_copy ="/master/tindakan/copy-paket-ruangan?id=";

    // Event Ready
    $(document).ready(function() {
        // Generate Table
        tablePaket = $("#table-paket-ruangan").docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox",
                targets:   0
            }],
            select: {
                style:    "os",
                selector: "tr"
            },
            sorting: [[3, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: "/master/tindakan/get-data-paket-ruangan",
            columns: [
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    defaultContent: "",
                    width: "7%",
                },
                 {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    data:"detail",
                    searchable: false,
                    orderable: false,
                    
                },
                
                {title: "'.(\Yii::t("fe", "Nama Ruangan")). '", data: "ruangan_nama"},
            ],

        });
        $(".dataTables_filter").hide();
        $(".filter-paket-ruangan").datatableBootstrapFilter(tablePaket, [
            [
                3, 
                \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $data_ruangan, ['class' => 'form-control select2', 'prompt' => ""]))) . '\'
            ]
        ]
    
        );
    });


     $(document).on("click", "#table-paket-ruangan tbody tr", function() {
        // Try catch
        try {
            // Get primary
            primaryKey = tablePaket.row(".selected").data().primary ? tablePaket.row(".selected").data().primary : null;
            
        } catch (e) {
            // Make it false
            primaryKey = false;
        }

        // Assign to ubah
        $("#btn-copy").attr("action", url_copy + primaryKey);

        // Check class selected
        if ($("#table-paket-ruangan tr.selected").length == 0) {
            // Disable edit button
            $("#btn-copy").prop("disabled", true);
        }
        else {
            // Disable edit button
            $("#btn-copy").prop("disabled", false);
        }
    });

    ',VIEW::POS_END);
?>