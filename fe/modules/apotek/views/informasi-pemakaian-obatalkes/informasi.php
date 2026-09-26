<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-02-26 10:49:25
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-02-28 10:41:44
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = Yii::t('fe', 'Informasi Pemakaian Obat Alkes');
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("instalasi_name"), 'url' => ['index']];
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
                      <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                      <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
              </div>
              <!-- end -->
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'reset'=>['attributes'=>['data-parent'=>'.filter-form']],
                    'lihat' => [
                        'type' => 'link',
                        'title' => \Yii::t('fe', 'Lihat'),
                        'icon' => 'fa fa-eye',
                        'method' => '#',
                        'attributes' => [
                            'class' => 'data-lihat',
                            'data-target' => Url::home().('apotek/informasi-pemakaian-obatalkes/view?id='),
                        ]
                    ],
                    // 'delete',
                ],'#example');?>
            </div>
                <div class="panel-body">
                    <div class="col-md-12 filter-form">
                    </div>
                    <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th width="1"></th>
                                <th width="80">No</th>
                                <th><?=Yii::t('fe','Tanggal Pemakaian')?></th>
                                <th><?=Yii::t('fe','Nomor Pemakaian')?></th>
                                <th><?=Yii::t('fe','Nama Penginput')?></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
var table;
$(document).on("click", ".data-reload", function() {
    table.draw();
});

$(document).ready(function(){
    table = $("#example").docoTabel({
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
        fixedColumns: {
            leftColumns: 1
        },
        ajax: baseUrl+"apotek/informasi-pemakaian-obatalkes/get-data-pemakaian",
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
            {title: "'.(\Yii::t('fe', 'Tanggal Pemakaian Obat Alkes')).'", data: "tglpemakaianobat"},
            {title: "'.(\Yii::t('fe', 'Nomor Pemakaian')).'",  data: "nopemakaian_obat"},
            {title: "'.(\Yii::t('fe', 'Nama Penginput')).'", data: "nama_pegawai", searchable: false, orderable: false},
        ],
    });
    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table, [
        [
            2,
            \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate" placeholder="Periode Awal"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" class="form-control endDate" placeholder="Periode Akhir"/><input type="text" style="display:none" class="targetDate"></div>\'
        ],
    ], {
        2:0,
        4:1,
    }, true);
    dateRangeHelper(".startDate",".endDate",".targetDate");

    });
', VIEW::POS_END, "js-kunings");
?>
