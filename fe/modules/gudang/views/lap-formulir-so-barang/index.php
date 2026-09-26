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
                    'reset'=>['attributes'=>['data-parent'=>'.filter-form']],
                    'pdf',
                    'excel'
                ],'#example');?>
            </div>
                <div class="panel-body">
                    <div class="col-md-12 filter-form">
                    </div>
                    <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th >No</th>
                                <th><?=Yii::t('fe','Tanggal Formulir')?></th>
                                <th><?=Yii::t('fe','Periode Stok')?></th>
                                <th><?=Yii::t('fe','Nomer Formulir')?></th>
                                <th><?=Yii::t('fe','Harga Netto Sistem')?></th>
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

var _checkRuangan = function () {
    var _value = $("select[name=ruangan_nama]").val();
    if (_value != "") {
        $(".data-pdf").show();
        $(".data-excel").show();
    } else {
        $(".data-pdf").hide();
        $(".data-excel").hide();
    }
}

$(document).on("click",".data-reset", function (event) {
    event.preventDefault();
    _checkRuangan();
})

$(document).on("change","select[name=ruangan_nama]",function (event) {
    event.preventDefault();
    $(\'.data-filter\').trigger(\'click\');
    _checkRuangan();
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
        sorting: [[1, "asc"]], 
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,     
        fixedColumns: {
            leftColumns: 1
        },       
        ajax: baseUrl+"gudang/lap-formulir-so-barang/get-data",
        columns: [
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: "'.(\Yii::t('fe', 'Tanggal Formulir')).'", 
                data: "tglformulir",
            },
            {
                title: "'.(\Yii::t('fe', 'Periode Stok')).'", 
                data: "periode_stok",
                searchable: false,
                orderable: false
            },
            {
                title: "'.(\Yii::t('fe', 'Nomer Formulir')).'", 
                data: "noformulir",
            },
            {
                title: "'.(\Yii::t('fe', 'Harga Netto Sistem')).'", 
                data: "total_harganetto",
                class : "text-right",
                searchable: false,
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
        ],
    });
    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table, [
        [
            1, 
            \'<div class="input-group"><input type="text" id="rangeDemoStart" class="form-control startDate" placeholder="Periode Awal"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinish" class="form-control endDate" placeholder="Periode Akhir"/><input type="text" style="display:none" class="targetDate"></div>\'
        ],
        [
            5, 
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
            6, 
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
                       'url' =>'lap-formulir-so-barang/get-ruangan',
                    ],
                    'pluginEvents' => [
                        'depdrop:afterChange' => "function (event, id, value) {
                            $('.data-filter').trigger('click');
                            _checkRuangan();
                       }"
                    ]
                ])
            )).'</div>\'
        ],
    ], {
        1:0,
    }, true);
    
    setTimeout(function () {
        $(\'#filter_instalasi\').trigger(\'depdrop:change\');
    },1);

    dateRangeHelper(".startDate",".endDate",".targetDate");

    });
', VIEW::POS_END, "js-kunings");
?>

