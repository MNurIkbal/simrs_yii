<?php

/**
 * @Author: JohnDoe
 * @Date:   2018-03-22 22:06:03
 * @Description:
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;

$this->title = Yii::t('fe', 'Informasi Pemesanan Barang Keluar');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', Yii::$app->docoVars->workspace('instalasi_name')), 'url' => ['/rajal/dashboard']];
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
                    'print',
                    'excel',
                    'delete' => [
                        'attributes' => [
                            'data-additional' => 'data-rm'
                        ]
                    ]

                ]);?>
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
                            <th><?=Yii::t('fe', 'Tanggal Pemesanan')?></th>
                            <th><?=Yii::t('fe', 'Nomor Pemesanan')?></th>
                            <th><?=Yii::t('fe', 'Instalasi Tujuan')?></th>
                            <th><?=Yii::t('fe', 'Ruangan Tujuan')?></th>
                            <th><?=Yii::t('fe', 'Status')?></th>
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
        ajax: baseUrl+"rajal/inf-pemesanan-barang/get-data",
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
            {title: "'.(\Yii::t("fe", "Tanggal Pemesanan")).'", data: "tgl_pesanbarang"},
            {title: "'.(\Yii::t("fe", "Nomor Pemesanan")).'", data: "no_pemesanan"},
            {title: "'.(\Yii::t("fe", "Instalasi Tujuan")).'", data: "instalasi_tujuan"},
            {title: "'.(\Yii::t("fe", "Ruangan Tujuan")).'", data: "ruangan_tujuan"},
            {title: "'.(\Yii::t("fe", "Status")).'", data: "status_pesan", searchable: false},
        ],
    });

    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table, [
        [
            2,
            \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" class="form-control endDate"/><input type="text" style="display:none" class="targetDate" col-index=2></div>\'
        ],
        [   3,
            \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('no_pemesanan', '', ArrayHelper::map($api['response']['pemesanan'], 'no_pemesanan', 'no_pemesanan'), ['class' => 'form-control select2', 'prompt' => Yii::t('fe', '--Pilih Nomor Pemesanan--') ]))).'\' ],

        [
            4,
            \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                Html::dropDownList('instalasi_tujuan', '',
                    ArrayHelper::map($api['response']['instalasi'], 'instalasi_id', 'instalasi_nama'),
                    [
                        'id' => 'filter_instalasi',
                        'class' => 'form-control select2',
                        'prompt' => \Yii::t('fe', 'Pilih Instalasi')
                    ]
                )
            )).'</div>\'
        ],
        [
            5,
            \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                DepDrop::widget([
                    'name' => 'ruangan_tujuan',
                    'data' => ['' => 'Pilih'],
                    'options' => [
                        'disabled' => false,
                        'class' => 'form-control select2'
                    ],
                    'pluginOptions' => [
                       'depends'  => ['filter_instalasi'],
                       'placeholder' => \Yii::t('fe', 'Pilih Ruangan'),
                       'url' =>'/rajal/lap-pemesanan-barang/get-ruangan',
                    ]
                ])
            )).'</div>\'
        ],
    ]);
    dateRangeHelper(".startDate",".endDate",".targetDate");
    $(".daterange-basic").daterangepicker({
        startDate: "'.(date("01-M-Y")).'", autoUpdateInput: true,
        endDate: "'.(date("d-M-Y")).'",
        applyClass: "bg-slate-600",
        cancelClass: "btn-default",
        locale: {
            format: "DD-MMMM-YYYY"
        }
    });
});
', View::POS_END, 'b-index');

?>
