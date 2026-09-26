<?php

/**
 * @author Randy Vianda Putra
 * @todo Master Tindakan Ruangan
 * @copyright 26 April 2018 aweutist
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
                        'reset'=>[
                            'data-target'=>'.filter-tindakan-ruangan-new'
                        ],
                        'mapping' => [
                            // 'type' => 'link',
                            'title' => \Yii::t('fe', 'Mapping'),
                            'icon' => 'fa fa-map-pin',
                            'attributes' => [
                                'class' => 'spa',
                                'data-options' => 'click',
                                'data-render' => 'map-tindakan-ruangan?id=',
                                'data-tab' => 'tab-tindakan-ruangan',
                                'data-target' => '#view-tindakan-ruangan',
                                'data-type' => 'wp'
                            ]
                        ],
                        'pdf' => [
                            'attributes' => [
                                'data-target' => '/master/tindakan/pdf-tindakan-ruangan?',
                            ]
                        ],
                        'excel' => [
                            'attributes' => [
                                'data-target' => '/master/tindakan/excel-tindakan-ruangan?',
                            ]
                        ],
                        'salin-ruangan' => [
                            // 'type' => 'link',
                            'title' => \Yii::t('fe', 'Salin ruangan'),
                            'icon' => 'fa fa-copy',
                            'attributes' => [
                                'data-options'=>'modal',
                                'data-target'=>'#modal_backdrop',
                                'data-width' => '45%',
                                'data-url' => '/master/tindakan/salin-tindakan-ruangan?id=',
                                'data-conditions'=>'ruangan_nama'
                            ]
                        ],
                    ],'#table-tindakan-ruangan-new');?>    
            
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-tindakan-ruangan-new"></div>
                </div>
                <table id="table-tindakan-ruangan-new" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th width="1"><?=\Yii::t("fe", "No");?></th>
                            <th><?=\Yii::t("fe", "Detail");?></th>
                            <th><?=\Yii::t("fe", "Nama ruangan");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="3"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    var table;
    // Event Reload
    $(document).on("click", ".data-reload", function() {
        table.draw();
    });

    $(document).ready(function(){
        table = $("#table-tindakan-ruangan-new").docoTabel({
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
            scrollX: true,
            ajax: baseUrl+"master/tindakan/get-data-tindakan-ruangan",
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
                {title: "'.(\Yii::t("fe", "Detail")).'", data: "detail", searchable: false},
                {title: "'.(\Yii::t("fe", "Nama ruangan")).'", data: "ruangan_nama", name: "ruangan_id"},
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-tindakan-ruangan-new").datatableBootstrapFilter(table, [
            [
                3, 
                \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $data_ruangan, ['class' => 'form-control select2', 'prompt' => ""]))).'\'
            ]
        ]);
    });
    

    ', VIEW::POS_END, 'js-kunings');
?>