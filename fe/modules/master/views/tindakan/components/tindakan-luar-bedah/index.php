<?php

/**
 * @author Ripan
 * @todo Master Mapping Tindakan dan Luar Bedah
 * @copyright 9 Mei 2022
 */

use yii\web\View;
use app\components\DocoHelpers;

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
                                'data-parent'=>'.filter-tindakan-luar-bedah'
                            ]
                        ],
                        'create' => [
                            'title' => \Yii::t('fe', 'Tambah'),
                            'icon' => 'fa fa-plus',
                            'attributes'=>[
                                'class' => 'spa',
                                'data-options' => 'click',
                                'data-render' => 'create-tindakan-luar-bedah',
                                'data-tab' => 'tab-tindakan-luar-bedah',
                                'data-target' => '#view-tindakan-luar-bedah',
                            ]
                        ],
                        'update' => [
                            'title' => \Yii::t('fe', 'Ubah'),
                            'icon' => 'fa fa-edit',
                            'attributes'=>[
                                'id' => 'btn-update-tindakan',
                                'class' => 'spa',
                                'data-options'=>'click',
                                'data-render' => 'update-tindakan-luar-bedah?id=',
                                'data-tab' => 'tab-tindakan-luar-bedah',
                                'data-target' => '#view-tindakan-luar-bedah',
                                'data-type' => 'wp'
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                                'id' => 'btn-delete-tindakan-luar-bedah',
                                'data-additional' => 'data-rm',
                                'data-target' => '/master/tindakan/delete-tindakan-luar-bedah?id='
                            ]
                        ],
                    ],'#table-tindakan-luar-bedah');?>    
            
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-tindakan-luar-bedah tab-tindakan-luar-bedah"></div>
                </div>
           
                <table id="table-tindakan-luar-bedah" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width=7%></th>
                            <th width=5%><?=\Yii::t("fe", "No");?></th>
                            <th width=50%><?=\Yii::t("fe", "Tindakan");?></th>
                            <th width=50%><?=\Yii::t("fe", "Tindakan Kode");?></th>
                            <th width=50%><?=\Yii::t("fe", "Detail Mapping");?></th>
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
var tableTindakanLuarBedah;

// Event Ready
$(document).ready(function() {
    // generateFilter("tab-tindakan-luar-bedah", "filter-tindakan-luar-bedah");
    // Generate Table
    tableTindakanLuarBedah = $("#table-tindakan-luar-bedah").docoTabel({
        filter: true,
        //add for handle checkbox
        columnDefs: [{
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
        ajax: "/master/tindakan/get-data-tindakan-luar-bedah",
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
            {
                title: "'.(\Yii::t("fe", "Tindakan")).'", 
                data: "daftartindakan_nama",
            },
            {
                title: "'.(\Yii::t("fe", "Tindakan Kode")).'", 
                data: "daftartindakan_kode",
                visible: false,
                searchable: false,
            },
            {
                title: "'.(\Yii::t("fe", "Detail Mapping")).'", 
                data: "detail", 
                searchable: false,
                orderable: false
            },
        ],
    });
    $(".dataTables_filter").hide();
    $(".filter-tindakan-luar-bedah").datatableBootstrapFilter(tableTindakanLuarBedah, [],
    {
        1:0,
    }, true);
});
    
',VIEW::POS_END);
?>