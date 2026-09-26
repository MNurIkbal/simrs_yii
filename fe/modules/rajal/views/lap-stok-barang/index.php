<?php

/**
 * @Author: JohnDoe
 * @Date:   2018-03-22 10:06:03
 * @Description:
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title ='Laporan Stok Barang';
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("ruangan_name")];
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
                    </ul>
                </div>
            </div>

            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'reset',
                    // 'print',
                    'pdf',
                    'excel',
                ],'#example');?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                        <hr>
                    </div>
                </div>
                <table class="table datatable-basic table-striped table-hover dataTable no-footer"
                    id="example" style="width:100%" data-filter=".form-filter">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">&nbsp;</th>
                            <th width="20">No</th>
                            <th><?=Yii::t('fe', 'Periode Stok')?></th>
                            <th><?=Yii::t('fe', 'Ruangan')?></th>
                            <th><?=Yii::t('fe', 'Nama Barang')?></th>
                            <th><?=Yii::t('fe', 'Qty Masuk')?></th>
                            <th><?=Yii::t('fe', 'Qty Keluar')?></th>
                            <th><?=Yii::t('fe', 'Qty Dipesan')?></th>
                            <th><?=Yii::t('fe', 'Tersedia')?></th>
                            <th><?=Yii::t('fe', 'Stok')?></th>
                            <th><?=Yii::t('fe', 'Instalasi')?></th>
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
$(document).on("click", ".data-reload", function() {
    table.draw();
});
$(document).ready(function() {
    $(function(){
        $(".daterange").daterangepicker({
            applyClass: "bg-slate-600",
            cancelClass: "btn-default",
            locale: {
                format: "DD MMM YYYY"
            }
        });
    });

    table = $("#example").docoTabel({
        filter: false,
        columnDefs: [
            {
                orderable: false,
                className: "select-checkbox",
                targets:   0
            },
        ],
        select: {
            style:    "os",
            selector: "td:first-child"
        },
        sorting: [[2, "asc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl+"rajal/lap-stok-barang/get-data",
        columns: [

            {
                title: "",
                data: null,
                defaultContent: "",
                searchable: false,
                orderable: false
            },
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {title: "'.(\Yii::t("fe", "Periode Stok")).'", data: "periodestok_nama"},
            {title: "'.(\Yii::t("fe", "Ruangan")).'", data: "ruangan_nama"},
            {title: "'.(\Yii::t("fe", "Nama Barang")).'", data: "barang_nama"},
            {title: "'.(\Yii::t("fe", "Qty Masuk")).'", data: "qty_masuk", searchable: false},
            {title: "'.(\Yii::t("fe", "Qty Keluar")).'", data: "qty_keluar", searchable: false},
            {title: "'.(\Yii::t("fe", "Qty Dipesan")).'", data: "qty_dipesan", searchable: false},
            {title: "'.(\Yii::t("fe", "Tersedia")).'", data: "qty_tersedia", searchable: false},
            {title: "'.(\Yii::t("fe", "Stok")).'", data: "qty_stok", searchable: false},
            {title: "'.(\Yii::t("fe", "Instalasi")).'", data: "instalasi_nama", "visible": false},
        ],
    });

    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table, [
        [   2,
            \''.(preg_replace("/[\n\t\r]/i", '',
                  Html::textInput('periodestok_nama', '', ['class' => 'form-control daterange','placeholder'=>\Yii::t('fe', 'Periode Stok')])
                  )).'\'],
        [   4,
            \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('barang_nama', '', ArrayHelper::map($api['response']['barang'], 'barang_id', 'barang_nama'), ['class' => 'form-control select2', 'prompt' => Yii::t('fe', '--Pilih Barang--') ]))).'\' ],
        [   3,
            \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('ruangan_nama', '', ArrayHelper::map($api['response']['ruangan'], 'ruangan_id', 'ruangan_nama'), ['class' => 'form-control select2', 'prompt' => Yii::t('fe', '--Pilih Ruangan--') ]))).'\' ],

        [   10,
            \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('instalasi_nama', '', ArrayHelper::map($api['response']['instalasi'], 'instalasi_id', 'instalasi_nama'), ['class' => 'form-control select2', 'prompt' => Yii::t('fe', '--Pilih Instalasi--') ]))).'\' ],


    ]);
});
', View::POS_END, 'b-index');

?>
