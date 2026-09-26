<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-02-26 10:49:25
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2019-03-15 13:21:47
 */

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
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Apotek'), 'url' => []];
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
                        <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
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
                            'onClick' => '',
                            'id' => 'btn-submit'
                        ]
                    ],
                    'reset'
                ],'#example');?>
            </div>
                <div class="panel-body">
                    <div class="col-md-12 advanced-filter">
                    </div>
                    <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th >No</th>
                                <th><?=Yii::t('fe','Rak Obat')?></th>
                                <th><?=Yii::t('fe','Laci Obat')?></th>
                                <th><?=Yii::t('fe','Kode Obat Alkes')?></th>
                                <th><?=Yii::t('fe','Nama Obat Alkes')?></th>
                                <th><?=Yii::t('fe','Stok Sistem')?></th>
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
        'action' => '/apotek/transaksi-formulir/print-pdf' ,
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

$(document).on("click", ".data-reset", function(e){
    setTimeout( function(){ $("#filter_periode").val("'.date('Y').'").trigger("change");
        $("#filter_instalasi").val("'.$instalasiId.'").trigger("change").trigger("depdrop:change");
     }, 100 )
})
$(document).on("click",".data-save", function (event) {
    event.preventDefault();
    $().docoForm("click",{
        url : "/apotek/transaksi-formulir/save?" + $.param(table.ajax.params()),
        method : "POST",
        data : {
            periodestokobat_id: $("#filter_periode").val(),
            instalasi_id: $("#filter_instalasi").val(),
            ruangan_id: $("#filter_ruangan").val(),
            totaldata: table.page.info().recordsTotal
        },
        success : function (data) {
            $(this).attr("disabled", "disabled");
            try{
                table.draw();
                var _action = "/apotek/transaksi-formulir/print-pdf?id=" + data.response.id_parent + "&formulir="+data.response.no_formulir;
                (new PNotify({
                        title: "Berhasil",
                        text: "Transaksi Formulir Stok Opname dengan Nomor " + "<strong>" + data.response.no_formulir + "</strong>" + " berhasil disimpan, apakah Anda ingin melakukan cetak?",
                        addclass: "alert alert-success alert-arrow-right alert-styled-right",
                        type: "success",
                        buttons: {
                            closer: false,
                            sticker: false
                        },
                        hide: false,
                        confirm: {
                            confirm: true,
                            buttons: [
                                {
                                    text: "Ya",
                                    addClass: "btn btn-xs btn-success",
                                },
                                {
                                    text: "Tidak",
                                    addClass: "btn btn-xs btn-danger",
                                }
                            ]
                        },
                        history: {
                            history: false
                        }
                    })).get().on("pnotify.confirm", function() {
                        // Print
                        window.open(_action);
                    }).on("pnotify.cancel", function() {
        
                    });
            } catch(e){
                console.log(e)
            }
        }

    });
});

$(document).ready(function(){
    table = $("#example").docoTabel({
        filter: true,
        displayLength: 10,
        order: [[1, "asc"], [2, "asc"], [4, "asc"]],
        processing: true,
        serverSide: true,
        ajax: baseUrl+"apotek/transaksi-formulir/get-data",
        columns: [
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: "'.(\Yii::t('fe', 'Rak Obat')).'",
                data: "rakobat_nama",
                searchable: false,
                visible: false,
            },
            {
                title: "'.(\Yii::t('fe', 'Laci Obat')).'",
                data: "laci",
                searchable: false,
                orderable: false,
            },
            {
                title: "'.(\Yii::t('fe', 'Kode Obat Alkes')).'",
                data: "obatalkes_kode",
                searchable: false,
                orderable: false,
            },
            {
                title: "'.(\Yii::t('fe', 'Nama Obat Alkes')).'",
                data: "obatalkes_nama",
                searchable: false,
                orderable: false,
            },
            {
                title: "'.(\Yii::t('fe', 'Stok Sistem')).'",
                data: "stok_sistem",
                searchable: false,
                orderable: false,
            },
            {
                title: "'.(\Yii::t('fe', 'Instalasi')).'", 
                data: "instalasi_id",
                visible : false
            },
            {
                title: "'.(\Yii::t('fe', 'Ruangan')).'",
                data: "ruangan_id",
                visible : false
            },
            {
                title: "'.(\Yii::t('fe', 'Rak')).'",
                data: "rakobat_id",
                visible : false
            },
            {
                title: "'.(\Yii::t('fe', 'Consignment')).'", 
                data: "is_consigment",
                visible : false
            },
            {
                title: "'.(\Yii::t('fe', 'Jenis Obat Alkes')).'", 
                data: "jenisobatalkes_nama",
                visible : false,
            },
        ],
    });
    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table, [
        [
            6, 
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
            7, 
            \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '', 
                DepDrop::widget([
                    'name' => 'ruangan_nama',
                    'options' => [
                        'disabled' => false,
                        'class' => 'form-control select2',
                        'id'=>'filter_ruangan'
                    ],
                    'pluginOptions' => [
                       'depends'  => ['filter_instalasi'],
                       'placeholder' => \Yii::t('fe', '-- Pilih --'),
                       'url' =>'transaksi-formulir/get-ruangan',
                    ]
                ])
            )).'</div>\'
        ],
        [
            8, 
            \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '', 
                DepDrop::widget([
                    'name' => 'filter_rakobat',
                    'options' => [
                        'disabled' => false,
                        'class' => 'form-control select2',
                        'id'=>'filter_rakobat'
                    ],
                    'pluginOptions' => [
                       'depends'  => ['filter_ruangan'],
                       'placeholder' => \Yii::t('fe', '-- Pilih --'),
                       'url' =>'transaksi-formulir/get-rakobat',
                    ]
                ])
            )).'</div>\'
        ],
      
        [
            9,
            \'<div class=\"form-group\">'.(preg_replace("/[\n\t\r]/i", '', 
                Html::checkbox('is_consigment', false, 
                    [
                        'id' => 'filter_consigment'
                    ]
                )
            )).'</div>\'
        ],
        [
            10, 
            \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '', 
                DepDrop::widget([
                    'name' => 'filter_jenisobat',
                    'options' => [
                        'disabled' => false,
                        'class' => 'form-control select2',
                        'value'=>'jenisobatalkes_nama'
                    ],
                    'pluginOptions' => [
                       'depends'  => ['filter_ruangan'],
                       'placeholder' => \Yii::t('fe', '-- Pilih --'),
                       'url' =>'transaksi-formulir/get-jenis-obat',
                    ]
                ])
            )).'</div>\'
        ],
        
    ],{
        0:6,
        1:7,
        2:8,
        3:9,
        4:10,
    },true);
    setTimeout(function () {
        $(\'#filter_instalasi\').trigger(\'depdrop:change\');
    },1);
    // dateRangeHelper(".startDate",".endDate",".targetDate",true);
    });

    $(document).on("keydown", null, "alt+s", function (event) {
        $("#btn-submit").click();
    });

    $(document).on("change", "input[name=\'is_consigment\']", function () {
        if (this.checked) {
            this.value = "1";
            table.columns(9).search("1").draw();
        } else {
            this.value = "";
            table.columns(9).search("").draw();
        }
    });

', VIEW::POS_END, "js-kunings");
?>

