<?php

/**
 * @Author: Budi
 * @Date:   2018-04-24 11:36:33
 */

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\web\View;
use kartik\widgets\DepDrop;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;

// Some variables
$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                        <div class="column-1">
                                <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                        </div>
                        <div class="column-2">
                                <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
                                <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                        </div>
                </div>
                <!-- end -->
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'search',
                    'reset'=> [
                        'attributes'=>[
                            'data-parent' => '.filter-form'
                        ]
                    ],
                    'add' => [
                        'attributes' => [
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'action' => '/master/pmi/create',
                        ]
                    ],
                    'detail' => [
                        'title' => Yii::t('fe', 'Ubah'),
                        'icon' => 'fa fa-pencil',
                        'attributes' => [
                            'data-options'=>'modal',
                            'data-target'=>'#modal_backdrop',
                            'data-url' => '/master/pmi/update?id=',
                        ]
                    ],
                    'delete'=>[
                        'attributes'=>[
                            'data-additional'=>'data-rm',
                        ]
                    ],
                    'excel' => [
                        'attributes' => [
                            'data-target' => '/master/pmi/export-excel?'
                        ]
                    ],
                ]) ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">&nbsp;</th>
                            <th><?= Yii::t('fe', 'No') ?></th>
                            <th><?= Yii::t("fe", "Nama PMI") ?></th>
                            <th><?= Yii::t("fe", "Alamat") ?></th>
                            <th><?= Yii::t("fe", "No Telepon") ?></th>
                            <th><?= Yii::t("fe", "Status") ?></th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
var table;
$(".switch").bootstrapSwitch();
$(document).on("switchChange.bootstrapSwitch", ".change-status", function (e, state) {
    var dataStatus = "0";
    var dataId = $(this).attr("data-id");
    if (e.target.checked == true)
        dataStatus = "1";

    $(this).docoForm("delete",{
        url: baseUrl+"master/pmi/change-status?id="+dataId+"&status="+dataStatus,
        confirmTitle : "'.(\Yii::t("fe", "Konfirmasi")).'",
        confirmMessage : "'.(\Yii::t("fe", "Apa anda yakin ingin mengubah status data?")).'",
        success : function (data) {
            table .draw();
        }
    });
    table .draw();
});
$(document).ready(function() {
    table = $("#example").docoTabel({
        filter: true,
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
        ajax: baseUrl+"master/pmi/get-data",
        columns: [
            {
                title: "", 
                data: null, 
                defaultContent: "", 
                searchable: false, 
                orderable: false,
                width: "10%"
            },
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: "'.(\Yii::t("fe", "Nama PMI")).'",  
                data: "supplier_nama",
            },
            {
                title: "'.(\Yii::t("fe", "Alamat")).'",  
                data: "supplier_alamat",
            },
            {
                title: "'.(\Yii::t("fe", "No Telepon")).'",  
                data: "no_tlp",
                searchable: false,
            },
            {title: "Status", data: "is_active", class: "text-center"},
        ],
    });

    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table, [
        [
            5, 
            \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $is_active, ['class' => 'form-control select2']))).'\'
        ],
    ]);
    
});


', View::POS_END, 'b-index');
