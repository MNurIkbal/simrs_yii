<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = \Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"), 'url' => ['index']];
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
                        <h3 class="panel-title"><b><?= $this->title ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                    <?=DocoHelpers::generateToolbar($btn_toolbar,'#table-informasi');?>
            </div>

            <div class="panel-body">
                <div class="advanced-filter"></div>
                <table id="table-informasi" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Tanggal Pemesanan");?></th>
                            <th><?=\Yii::t("fe", "Nomor Pemesanan");?></th>
                            <th><?=\Yii::t("fe", "Instalasi-Ruangan Tujuan");?></th>
                            <th><?=\Yii::t("fe", "Status");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Kirim");?></th>
                            <th><?=\Yii::t("fe", "Tanggal Terima");?></th>
                            <th><?=\Yii::t("fe", "Reference");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="7"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
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
var table;
var dataCollect;
$(".data-edit").hide();
$(document).on("click","#table-informasi tr",function (event) {
    event.preventDefault();
    var _data = table.row(\'.selected\').data();
    if (typeof _data != "undefined") {
        if(_data.statusdistribusiobat_id == 666 && _data.statuspesan == 398){
            $(".data-edit").show();
        }else{
            $(".data-edit").hide();
        }
        
        if (_data.statuspesan == 398) {
            $(".data-delete").show();
            return false;
        }
        else {
            $(".data-delete").hide();
        }

    }
});
$(function(){
    table = $("#table-informasi").docoTabel({
        filter: true,
        columnDefs: [{
            orderable: false,
            className: "select-checkbox",
            targets:   0
        }],
        select: {
            style:    "os",
            selector: "tr"
        },
        sorting: [[2, "desc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl+"apotek/informasi-obat-alkes-keluar/get-data",
        columns: [
            {
                data : null,
                render : function ( data, type, full, meta ) {
                    return null;
                },
                searchable: false,
                orderable: false
            },
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: "'.(\Yii::t("fe", "Tanggal Pemesanan")).'",
                data: "tglpemesanan"
            },
            {
                title: "'.(\Yii::t("fe", "Nomor Pemesanan")).'",
                data: "nopemesanan"
            },
            {
                title: "'.(\Yii::t("fe", "Instalasi-Ruangan Tujuan")).'",
                data: "instalasi_ruangan",
            },
            {
                title: "'.(\Yii::t("fe", "Status")).'",
                data: "statusdistribusiobat",
            },
            {
                title: "'.(\Yii::t("fe", "Tanggal Kirim")).'",
                data: "tglmutasioa",
                searchable: false
            },
            {
                title: "'.(\Yii::t("fe", "Tanggal Terima")).'",
                data: "tglterima",
                searchable: false
            },
            {
                title: "'.(\Yii::t("fe", "Reference")).'",
                data: "reference",
            }
        ],
    });
    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table,
        [
            [
                2,
                \'<div class="input-group"><input type="text" id="rangeDemoStart" value="'.date("d-M-Y").'" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" readonly="true" value="'.date("d-M-Y").'" class="form-control endDate"/><input type="text" style="display:none"  class="targetDate" col-index=2></div>\'
            ],
            [
                4,
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('instalasi_ruangan', '',
                        $ruangan,
                        [
                            'id' => 'filter_instalasi',
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', 'ALL')
                        ]
                    )
                )).'</div>\'
            ],
            [
                5,
                \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                    Html::dropDownList('status', '',
                        $status,
                        [
                            'id' => 'filter_status',
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', 'ALL')
                        ]
                    )
                )).'</div>\'
            ]

        ], {
            2:0,
            3:1,
            4:2,
            5:3,
            8:4
        }, true
    );
    dateRangeHelper(".startDate",".endDate",".targetDate");
    $(".instalasi").select2({
        placeholder: "",
    })
});

',View::POS_END,'b-index');