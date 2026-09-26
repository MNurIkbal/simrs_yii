<?php

use yii\web\View;
use yii\helpers\Html;
use app\components\DocoHelpers;

$this->params['breadcrumbs'][] = ['label' => 'DCMS', 'url' => ['index']];
$this->params['breadcrumbs'][] = $title;
$this->title = $title;
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
                <?= DocoHelpers::generateToolbar([
                        'search',
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-log'
                            ]
                        ],
                    ],"#table-audit-log");?>

            </div>

            <div class="panel-body">
                <div class="tab-log">
                </div>
                <table id="table-audit-log" class="table table-bordered table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Detail");?></th>
                            <th><?= \Yii::t("fe", "Waktu") ?></th>
                            <th><?= \Yii::t("fe", "Service") ?></th>
                            <th><?= \Yii::t("fe", "Aksi") ?></th>
                            <th><?= \Yii::t("fe", "IP") ?></th>
                            <th><?= \Yii::t("fe", "User DB") ?></th>
                            <th><?= \Yii::t("fe", "Table") ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="8">Data tidak ditemukan.</td>
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
    var tableAuditLog;
    // Event Ready
    $(document).ready(function() {
        // Generate Table
        generateFilter("tab-log", "filter-log");
        tableAuditLog = $("#table-audit-log").docoTabel({
            filter: true,
            sorting: [[2, "desc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"dcms/audit/get-data?view=log",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Detail")).'",
                    data: "button",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Waktu")).'",
                    data: "action_tstamp_tx"
                },
                {
                    title: "'.(\Yii::t("fe", "Service")).'",
                    data: "application_name"
                },
                {
                    title: "'.(\Yii::t("fe", "Aksi")).'",
                    data: "action",
                    render: function(action) {
                        let v = action;
                        switch (action) {
                            case "I" :
                                v = "INSERT"; break;
                            case "U" :
                                v = "UPDATE"; break;
                            case "D" :
                                v = "DELETE"; break;
                            case "T" :
                                v = "TRUNCATE"; break;
                            default :
                                v = "DEFAULT"; break;
                        }
                        return v;
                    }
                },
                {
                    title: "'.(\Yii::t("fe", "IP")).'",
                    data: "client_addr"
                },
                {
                    title: "'.(\Yii::t("fe", "User DB")).'",
                    data: "session_user_name"
                },
                {
                    title: "'.(\Yii::t("fe", "Table")).'",
                    data: "table_name"
                },
            ]
        });

        $(".dataTables_filter").hide();
        $(".filter-log").datatableBootstrapFilter(tableAuditLog, [
            [
                2,
                \'<div class="input-group"><input type="text" id="rangeDemoStartTin" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinishTin" readonly="true" class="form-control endDate"/><input type="text" style="display:none" class="targetDate" col-index=3></div>\'
            ],
            [   
                4,
                \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('action', '', ['I' => 'INSERT', 'U' => 'UPDATE', 'D' => 'DELETE', 'T' => 'TRUNCATE'], 
                    ['class' => 'form-control select2', 
                    'prompt' => \Yii::t('fe', '— Pilih Tipe Aksi —'),
                    'name' => "action",
                    ]))).'\'
            ]
        ], {
            3:0,
            2:1,
            7:2,
            4:3
        }, true);
        dateRangeHelper(".startDate",".endDate",".targetDate");
    });

', View::POS_END, 'e-index');
?>
