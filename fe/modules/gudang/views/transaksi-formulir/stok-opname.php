<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\DepDrop;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Gudang', 'url' => []];
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
                    'save' => [
                        'attributes' => [
                            'onClick' => ''
                        ]
                    ],
                    'reset'=>['attributes'=>['data-parent'=>'.filter-form']]
                ],'#example');?>
            </div>
                <div class="panel-body">
                    <div class="col-md-12 filter-form">
                    </div>
                    <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th >No</th>
                                <th><?=Yii::t('fe','Nama Obat Alkes')?></th>
                                <th><?=Yii::t('fe','Tanggal Kadaluarsa')?></th>
                                <th><?=Yii::t('fe','Stok Sistem')?></th>
                                <th><?=Yii::t('fe','Stok Fisik')?></th>
                                <th><?=Yii::t('fe','Kondisi')?></th>
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
<?= 
Html::button('<b><i class="fa fa-file-pdf-o"></i></b>'.Yii::t('fe', ' Cetak PDF'), 
    [
        'class' => 'btn btn-info btn-labeled btn-xs',
        'action' => '/gudang/transaksi-formulir/before-print' ,
        'id' => 'show-cetak',
        'data-width' => '800px',
        'data-target' => '#modal_backdrop',
        'data-toggle' => 'modal',
        'style' => 'display:none;',
    ]);
?>
<?php 
$this->registerJs('
var table;

$(document).on("change","select[name=ruangan_nama]",function (event) {
    event.preventDefault();
    $(\'.data-filter\').trigger(\'click\');
})

$(document).on("click",".data-save", function (event) {
    event.preventDefault();
    $().docoForm("click",{
        url : "/gudang/transaksi-formulir/save?" + $.param(table.ajax.params()),
        method : "GET",
        data : {},
        success : function (data) {
            var _action = $("#show-cetak").attr("action");
            _action += "?id=" + data.response.id_parent;
            $("#show-cetak").attr("action",_action);
            $("#show-cetak").trigger("click");
            table.draw();
        }

    });
});

$(document).ready(function(){
    table = $("#example").docoTabel({
        filter: true,
        sorting: [[2, "asc"]], 
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,     
        fixedColumns: {
            leftColumns: 1
        },       
        ajax: baseUrl+"gudang/transaksi-formulir/get-data",
        columns: [
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: "'.(\Yii::t('fe', 'Nama Obat Alkes')).'", 
                data: "barang_nama",
                searchable: false
            },
            {
                title: "'.(\Yii::t('fe', 'Tanggal Kadaluarsa')).'", 
                data: "tglkadaluarsa",
                searchable: false
            },
            {
                title: "'.(\Yii::t('fe', 'Stok Sistem')).'", 
                data: "stok_sistem",
                searchable: false,
            },
            {
                title: "'.(\Yii::t('fe', 'Stok Fisik')).'", 
                data: null,
                searchable: false,
                orderable: false,
                render : function () {
                    return null
                }
            },
            {
                title: "'.(\Yii::t('fe', 'Kondisi')).'", 
                data: null,
                searchable: false,
                orderable: false,
                render : function () {
                    return null
                }
            },
            {
                title: "'.(\Yii::t('fe', 'Instansi')).'", 
                data: "instalasi_id",
                visible : false
            },
            {
                title: "'.(\Yii::t('fe', 'Ruangan')).'", 
                data: "ruangan_id",
                visible : false
            },
            {
                title: "'.(\Yii::t('fe', 'Periode Stok')).'", 
                data: "periodestokbarang_id",
                visible : false
            },
        ],
    });
    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table, [
        [
            8, 
            \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate" placeholder="Periode Awal"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" class="form-control endDate" placeholder="Periode Akhir"/><input type="text" style="display:none" class="targetDate"></div>\'
        ],
        [
            6, 
            \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '', 
                Html::dropDownList('instalasi_id', $instalasiId, 
                    ArrayHelper::map($instalasi, 'instalasi_id', 'instalasi_nama'), 
                    [
                        'id' => 'filter_instalasi', 
                        'class' => 'form-control select2', 
                        'prompt' => \Yii::t('fe', '--Pilih Semua--')
                    ]
                )
            )).'</div>\'
        ],
        [
            7, 
            \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '', 
                DepDrop::widget([
                    'name' => 'ruangan_nama',
                    'options' => [
                        'disabled' => false,
                        'class' => 'form-control select2'
                    ],
                    'pluginOptions' => [
                       'depends'  => ['filter_instalasi'],
                       'placeholder' => \Yii::t('fe', '--Pilih Semua--'),
                       'url' =>'transaksi-formulir/get-ruangan',
                    ],
                    'pluginEvents' => [
                        'depdrop:afterChange' => "function (event, id, value) {
                            $('.data-filter').trigger('click');
                       }"
                    ]
                ])
            )).'</div>\'
        ],
    ], {
        8:0,
    }, true);
    
    setTimeout(function () {
        $(\'#filter_instalasi\').trigger(\'depdrop:change\');
    },1);

    dateRangeHelper(".startDate",".endDate",".targetDate");

    });
', VIEW::POS_END, "js-kunings");
?>

