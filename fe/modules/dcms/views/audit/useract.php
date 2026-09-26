<?php

use yii\web\View;
use yii\helpers\Html;
use app\components\DocoHelpers;

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
                                'data-parent'=>'.filter-useract'
                            ]
                        ],
                    ],"#table-audit-useract");?>

            </div>

            <div class="panel-body">
                <div class="tab-useract">
                </div>
                <table id="table-audit-useract" class="table table-bordered table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Detail");?></th>
                            <th><?= \Yii::t("fe", "Waktu") ?></th>
                            <th><?= \Yii::t("fe", "Service") ?></th>
                            <th><?= \Yii::t("fe", "Aksi") ?></th>
                            <th><?= \Yii::t("fe", "IP") ?></th>
                            <th><?= \Yii::t("fe", "Browser") ?></th>
                            <th><?= \Yii::t("fe", "Nama Pemakai") ?></th>
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
    var tableAuditUserAct;
    // Event Ready
    $(document).ready(function() {
        // Generate Table
        generateFilter("tab-useract", "filter-useract");
        tableAuditUserAct = $("#table-audit-useract").docoTabel({
            filter: true,
            sorting: [[2, "desc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"dcms/audit/get-data?view=useract",
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
                    data: "stamp"
                },
                {
                    title: "'.(\Yii::t("fe", "Service")).'",
                    data: "service_name"
                },
                {
                    title: "'.(\Yii::t("fe", "Aksi")).'",
                    data: "action"
                },
                {
                    title: "'.(\Yii::t("fe", "IP")).'",
                    data: "ip_address"
                },
                {
                    title: "'.(\Yii::t("fe", "Browser")).'",
                    data: "browser"
                },
                {
                    title: "'.(\Yii::t("fe", "Nama Pemakai")).'",
                    name: "loginpemakai.nama_pegawai",
                    data: "nama_pegawai"
                },
            ]
        });

        $(".dataTables_filter").hide();
        $(".filter-useract").datatableBootstrapFilter(tableAuditUserAct, [
            [
                2,
                \'<div class="input-group"><input type="text" id="rangeDemoStartTin" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinishTin" readonly="true" class="form-control endDate"/><input type="text" style="display:none" class="targetDate" col-index=3></div>\'
            ],
            [   
                4,
                \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('action', '', ['INSERT' => 'INSERT', 'UPDATE' => 'UPDATE', 'DELETE' => 'DELETE'], 
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
