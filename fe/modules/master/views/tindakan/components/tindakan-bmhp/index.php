<?php

/**
 * @author Wahyu Saepuloh
 * @todo Master Mapping Tindakan dan BMHP
 * @copyright 5 November 2019
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

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
                                'data-parent'=>'.filter-tindakan-bmhp'
                            ]
                        ],
                        'create' => [
                            'title' => \Yii::t('fe', 'Tambah'),
                            'icon' => 'fa fa-plus',
                            'attributes'=>[
                                'class' => 'spa',
                                'data-options' => 'click',
                                'data-render' => 'create-tindakan-bmhp',
                                'data-tab' => 'tab-tindakan-bmhp',
                                'data-target' => '#view-tindakan-bmhp',
                            ]
                        ],
                        'update' => [
                            'title' => \Yii::t('fe', 'Ubah'),
                            'icon' => 'fa fa-edit',
                            'attributes'=>[
                                'id' => 'btn-update-tindakan',
                                'class' => 'spa',
                                'data-options'=>'click',
                                'data-render' => 'update-tindakan-bmhp?id=',
                                'data-tab' => 'tab-tindakan-bmhp',
                                'data-target' => '#view-tindakan-bmhp',
                                'data-type' => 'wp'
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                                'id' => 'btn-delete-tindakan-bmhp',
                                'data-additional' => 'data-rm',
                                'data-target' => '/master/tindakan/delete-tindakan-bmhp?id='
                            ]
                        ],
                        // 'pdf' => [
                        //     'attributes' => [
                        //         'data-target' => '/master/tindakan/export-pdf-tindakan?'
                        //     ]
                        // ],
                        // 'excel' => [
                        //     'attributes' => [
                        //         'data-target' => '/master/tindakan/export-excel-tindakan?'
                        //     ]
                        // ],
                    ],'#table-tindakan-bmhp');?>    
            
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-tindakan-bmhp tab-tindakan-bmhp"></div>
                    <!-- <div class="tab-tindakan-bmhp"></div> -->
                </div>
           
                <table id="table-tindakan-bmhp" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width=7%></th>
                            <th width=5%><?=\Yii::t("fe", "No");?></th>
                            <th width=50%><?=\Yii::t("fe", "Tindakan");?></th>
                            <th width=50%><?=\Yii::t("fe", "Detail Mapping");?></th>
                            <!-- <th width=10%><?=\Yii::t("fe", "Aksi");?></th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="10"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php 
$this->registerJs('
// Global Var
var tableTindakanBmhp;

// Event Ready
$(document).ready(function() {
    // generateFilter("tab-tindakan-bmhp", "filter-tindakan-bmhp");
    // Generate Table
    tableTindakanBmhp = $("#table-tindakan-bmhp").docoTabel({
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
        sorting: [[2, "asc"]], 
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: "/master/tindakan/get-data-tindakan-bmhp",
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                defaultContent: "",
            },
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {title: "'.(\Yii::t("fe", "Tindakan")).'", data: "daftartindakan_nama"},
            // {title: "'.(\Yii::t("fe", "Mapping")).'", data: "obatalkes_id", searchable: false},
            {
                title: "'.(\Yii::t("fe", "Detail Mapping")).'", 
                data: "detail", 
                searchable: false,
                orderable: false
            },
            // {
            //     title: "'.(\Yii::t("fe", "Aksi")).'", 
            //     data: "aksi", 
            //     searchable: false,
            //     orderable: false
            // },
        ],
    });
    $(".dataTables_filter").hide();
    $(".filter-tindakan-bmhp").datatableBootstrapFilter(tableTindakanBmhp, [],
    {
        1:0,
    }, true);
});
    
',VIEW::POS_END);
?>