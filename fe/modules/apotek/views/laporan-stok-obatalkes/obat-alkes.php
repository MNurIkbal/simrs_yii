<?php

/**
 * @author Randy Vianda Putra
 * @copyright 19 January 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Apotek', 'url' => []];
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
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'reset',
                    'pdf' => [
                        'title' => Yii::t('fe', 'Cetak'),
                        'attributes'=>[
                            'data-target'=>Url::home().'apotek/laporan-stok-obatalkes/export-pdf?jenis=apotek&'
                        ],
                    ],
                    'excel' => [
                        'title' => Yii::t('fe', 'Excel'),
                        'attributes'=>[
                            'data-target'=>Url::home().'apotek/laporan-stok-obatalkes/export-excel?jenis=apotek&'
                        ]
                    ],
                ]);?>
            </div>
            <div class="panel-body">
                <div class="col-md-12 filter-form"></div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Periode Stok");?></th>
                            <th><?=\Yii::t("fe", "Instalasi");?></th>
                            <th><?=\Yii::t("fe", "Ruangan");?></th>
                            <th><?=\Yii::t("fe", "Nama Obat Alkes");?></th>
                            <th><?=\Yii::t("fe", "Qty Masuk");?></th>
                            <th><?=\Yii::t("fe", "Qty Keluar");?></th>
                            <th><?=\Yii::t("fe", "Qty Dipesan");?></th>
                            <th><?=\Yii::t("fe", "Tersedia");?></th>
                            <th><?=\Yii::t("fe", "Stok");?></th>
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

$(document).ready(function(){
    table = $("#example").docoTabel({
        filter: true,
        sorting: [[1, "asc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl+"apotek/laporan-stok-obatalkes/get-data",
        columns: [
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {title: "'.(\Yii::t('fe', 'Periode stok')).'", data: "periodestok_nama"},
            {title: "'.(\Yii::t('fe', 'nama obat alkes')).'", data: "obatalkes_namalain"},
            {title: "'.(\Yii::t('fe', 'Instalasi')).'",  data: "instalasi_id", visible:false},
            {title: "'.(\Yii::t('fe', 'Ruangan')).'", data: "ruangan_nama"},
            {title: "'.(\Yii::t('fe', 'Qty masuk')).'", data: "qty_masuk", searchable: false, "class":"text-right"},
            {title: "'.(\Yii::t('fe', 'Qty keluar')).'", data: "qty_keluar", searchable: false, "class":"text-right"},
            {title: "'.(\Yii::t('fe', 'Qty dipesan')).'", data: "qty_dipesan", searchable: false, "class":"text-right"},
            {title: "'.(\Yii::t('fe', 'Tersedia')).'", data: "qty_tersedia", searchable: false, "class":"text-right"},
            {title: "'.(\Yii::t('fe', 'Stok')).'", data: "qty_stok", searchable: false, "class":"text-right"},
        ],
    });
    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table, [
        [
            1, 
            \'<div class="form-group">'.preg_replace('/[\n\t\r]/i', '', preg_replace("/[\']/i", '\'',
                    Html::dropDownList('periodestok_nama', '',
                        ArrayHelper::map($data_periode, 'periodestokobat_id', 'periodestok_nama'),
                        [
                            'id' => 'filter_periode',
                            'class' => 'form-control select2',
                            'prompt' => \Yii::t('fe', '-- Pilih --'),
                        ]
                    )
                )).'<div>\'
        ],
        [
            2, 
            \'<div class="form-group">'.preg_replace('/[\n\t\r]/i', '', preg_replace("/[\']/i", '\'',
                    Html::dropDownList('obatalkes_namalain', '', array(),
                        [
                            'class' => 'form-control select2 obatalkes_namalain',
                            'prompt' => '',
                            'col-index'=>3,
                            'style'=>'width:100%;'
                        ]
                    )
                )).'<div>\'
        ],
        [
            3, 
            \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '', 
                Html::dropDownList('instalasi_id', $instalasiId, 
                    ArrayHelper::map($instalasi, 'instalasi_id', 'instalasi_nama'), 
                    [
                        'id' => 'filter_instalasi', 
                        'class' => 'form-control select2', 
                        'prompt' => \Yii::t('fe', '-- Pilih --')
                    ]
                )
            )).'</div>\'
        ],
        [
            4, 
            \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '', 
                DepDrop::widget([
                    'name' => 'ruangan_nama',
                    'options' => [
                        'disabled' => false,
                        'class' => 'form-control select2 ruangan_nama'
                    ],
                    'pluginOptions' => [
                       'depends'  => ['filter_instalasi'],
                       'placeholder' => \Yii::t('fe', '-- Pilih --'),
                       'url' =>'laporan-stok-obatalkes/get-ruangan',
                    ]
                ])
            )).'</div>\'
        ],
    ], {
        1:0,3:1,4:2,
    },true);
    
    $(".obatalkes_namalain").select2({
        placeholder: "",
        minimumInputLength: 3,
        ajax: {
            url: "/apotek/laporan-stok-obatalkes/get-obat-alkes-nama",
            dataType: "json",
            quietMillis: 250,
            data: function(term, page){
                return{
                    q: term,
                    z: $(".ruangan_nama").val(),
                    page: page
                }
            },
            processResults: function (data) {
              return {
                results: data.result
              };
            }
        },
        dropdownCssClass: "bigdrop",
        escapeMarkup: function (m) { return m; },
    });

    $(".data-reset").on("click", function(){
        $(".ruangan_nama").prop("disabled",true);
    });

    setTimeout(function () {
        $(\'#filter_instalasi\').trigger(\'depdrop:change\');
    },1);
    
    });
', VIEW::POS_END, "js-kunings");
?>