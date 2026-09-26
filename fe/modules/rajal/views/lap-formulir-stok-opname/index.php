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
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rawat jalan'), 'url' => ['/rajal/dashboard']];
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
                            <th><?=Yii::t('fe', 'Tanggal Formulir')?></th>
                            <th><?=Yii::t('fe', 'Periode Stok')?></th>
                            <th><?=Yii::t('fe', 'Nomor Formulir')?></th>
                            <th><?=Yii::t('fe', 'Harga Netto Sistem')?></th>
                            <th><?=Yii::t('fe', 'Instalasi')?></th>
                            <th><?=Yii::t('fe', 'Ruangan')?></th>
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
        ajax: baseUrl+"rajal/lap-formulir-stok-opname/get-data",
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
            {title: "'.(\Yii::t("fe", "Tanggal Formulir")).'", data: "tglformulir"},
            {title: "'.(\Yii::t("fe", "Periode Stok")).'", data: "periodestok_nama", searchable: false},
            {title: "'.(\Yii::t("fe", "Nomor Formulir")).'", data: "noformulir"},
            {title: "'.(\Yii::t("fe", "Harga Netto Sistem")).'", data: "harganetto_sistem", searchable: false},
            {title: "'.(\Yii::t("fe", "Instalasi")).'", data: "instalasi_nama", visible: false},
            {title: "'.(\Yii::t("fe", "Ruangan")).'", data: "ruangan_nama", visible: false},
        ],
    });

    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table, [
        [   2,
            \''.(preg_replace("/[\n\t\r]/i", '',
                  Html::textInput('tglformulir', '', ['class' => 'form-control daterange','placeholder'=>\Yii::t('fe', 'Tanggal Formulir')])
                  )).'\'],
        [   4,
            \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('noformulir', '', ArrayHelper::map($api['response']['formulir'], 'noformulir', 'noformulir'), ['class' => 'form-control select2', 'prompt' => Yii::t('fe', '--Pilih Nomor Formulir--') ]))).'\' ],

        [
            6,
            \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '',
                Html::dropDownList('instalasi_nama', '',
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
            7,
            \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                DepDrop::widget([
                    'name' => 'ruangan_nama',
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
});
', View::POS_END, 'b-index');

?>
