<?php

/**
 * @Author: Wahyu Saepuloh
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
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Gudang'), 'url' => []];
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
                    <div class="col-md-12 filter-form"></div>
                    <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th class="select-checkbox">
                                    <div class="header-checkbox checkbox" style="">
                                      <input type="checkbox" id="check-all" value="1">
                                    </div>
                                </th>
                                <th >No</th>
                                <th><?=Yii::t('fe','Nama Barang')?></th>
                                <th><?=Yii::t('fe','Kelompok Barang')?></th>
                                <th><?=Yii::t('fe','Sub Kelompok Barang')?></th>
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
        'action' => '/gudang/transaksi-formulir-barang/print-pdf' ,
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
var tmpDataSOBarang = {};
var notIn = {};
var checkAll = true;
var namaBarang = "";
var kelompokBarang = "";
var subKelompokBarang = "";
var tmpAllDataSO = {};
var list_barang_id = {};

// $(document).on("change","select[name=ruangan_nama]",function (event) {
//     event.preventDefault();
//     $(\'.data-filter\').trigger(\'click\');
// })
$(document).on("click", ".data-reset", function(e){
    setTimeout( function(){ $("#filter_periode").val("'.date('Y').'").trigger("change");
        $("#filter_instalasi").val("'.$instalasiId.'").trigger("change").trigger("depdrop:change");
     }, 100 )
})
$(document).on("click",".data-save", function (event) {
    event.preventDefault();
    var listBarangId = JSON.stringify(list_barang_id);

    $().docoForm("click",{
        url : "/gudang/transaksi-formulir-barang/save?" + $.param(table.ajax.params()),
        method : "POST",
        data : {
            instalasi_id: $("#filter_instalasi").val(), 
            ruangan_id: $("#filter_ruangan").val(),
            namaBarang:  $(`input[id="filter_barang_nama"]`).val(),
            kelompokBarang:  $("#kelompokbarang_nama").val(),
            subKelompokBarang:  $("#subkelompok_barang").val(),
            listBarangId: listBarangId
        },
        success : function (data) {
            tmpDataSOBarang = {};
            notIn = {};
            $(this).attr("disabled", "disabled");
            try{
                table.draw();
                var _action = "/gudang/transaksi-formulir-barang/print-pdf?id=" + data.data.data.id_parent + "&formulir="+data.data.data.no_formulir;
                (new PNotify({
                        title: "Berhasil",
                        text: "Transaksi Formulir Stok Opname dengan Nomor " + "<strong>" + data.data.data.no_formulir + "</strong>" + " berhasil disimpan, apakah Anda ingin melakukan cetak?",
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
                console.log(e);
            }
        }, error: function(res) {
            var data = res.responseJSON.data
            docoNotification("error", "Proses Gagal", data.text);
        }
    });
});

$(document).ready(function(){
    $("#check-all").trigger("click")
    table = $("#example").docoTabel({
        filter: true,
        columnDefs: [ {
            orderable: false,
            className: "select-checkbox",
            targets:   0
        }],
        select: {
            style:    "multi",
            selector: "tr"
        },
        sorting: [[2, "asc"]], 
        // displayLength: 10,
        paging: false,
        processing: true,
        serverSide: true,
        // stateSave: true,
        scrollX: true,
        ajax: baseUrl+"gudang/transaksi-formulir-barang/get-data",
        columns: [
            {
                data: null,
                defaultContent: "",
                searchable: false,
                orderable: false,
                width: "10%"
            },
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: "'.(\Yii::t('fe', 'Nama Barang')).'", 
                data: "barang_nama",
                searchable: false
            },
            {
                title: "'.(\Yii::t('fe', 'Kelompok Barang')).'", 
                data: "kelompok_barang",
                name: "kelompokbarang_id"
            },
            {
                title: "'.(\Yii::t('fe', 'Sub Kelompok Barang')).'", 
                data: "subkelompok_barang",
                name: "subkelompokbarang_id"
            },
            {
                title: "'.(\Yii::t('fe', 'Stok Sistem')).'", 
                data: "stok_sistem",
                searchable: false,
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
        ],
        drawCallback: function(setting){
            var api = this.api();
            var tmpData = api.rows( {page:\'current\'} ).data();
            if($("#check-all").prop("checked") == true){
                $.each(tmpData, function(key, value){
                    if(typeof notIn[value.barang_id] == "undefined"){
                        table.row(":eq("+key+")").select();
                    }
                })
                table.rows().select();
                trigger_tr_click();
            }else{
                $.each(tmpData, function(key, value){
                    if(typeof tmpDataSOBarang[value.barang_id] != "undefined"){
                        table.row(":eq("+key+")").select();
                    }
                })
            }
        }
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
                // DepDrop::widget([
                //     'name' => 'ruangan_nama',
                //     'options' => [
                //         'disabled' => false,
                //         'class' => 'form-control select2',
                //         'id'=>'filter_ruangan'
                //     ],
                //     'pluginOptions' => [
                //        'depends'  => ['filter_instalasi'],
                //        'placeholder' => \Yii::t('fe', '-- Pilih --'),
                //        'url' =>'transaksi-formulir-barang/get-ruangan',
                //     ],
                //     'pluginEvents' => [
                //         'depdrop:afterChange' => "function (event, id, value, jqXHR, textStatus) {
                //             var _data = textStatus.responseJSON;
                //             var _value = $('#filter_ruangan').attr('data-value');
                //             $('#filter_ruangan').val(_value).trigger('depdrop:change');
                //             table.draw()
                //             if (typeof _data != 'undefined') {
                //                 dataCollect = _data.data_collect;
                //             } else {
                //                 dataCollect = {};
                //             }
                //         }"
                //     ]
                
                // ])
                Html::dropDownList('ruangan_id', '', 
                    [], 
                    [
                        'id' => 'filter_ruangan', 
                        'class' => 'form-control select2', 
                        'prompt' => \Yii::t('fe', '-- Pilih --')
                    ]
                )
               
            )).'</div>\'
        ],
        [
            3,
            \'<div class="form-group">'.(preg_replace("/[\n\t\r]/i", '',
                Html::dropDownList('kelompok_barang', '',
                    ArrayHelper::map($getkelompok['kelompok'], 'kelompokbarang_id', 'kelompokbarang_nama'),
                    [
                        'id' => 'kelompokbarang_nama',
                        'class' => 'form-control select2 dep-to-parent',
                        'prompt' => \Yii::t('fe', '-- Pilih Kelompok Barang --'),
                    ]
                )
            )).'</div>\'
        ],
        [
            4,
            \'<div class="form-group">'.
                (preg_replace("/[\n\t\r]/i", '', DepDrop::widget([
                    'name' => 'subkelompok_barang',
                    'options' => [
                        'id' => 'subkelompok_barang',
                        'class' => 'form-control select2',
                    ],
                    'pluginOptions' => [
                       'depends'  => ['kelompokbarang_nama'],
                       'prompt' => '-- Pilih Sub Kelompok Barang --',
                       'placeholder' => '-- Pilih Sub Kelompok Barang --',
                       'url' => Url::to(['/gudang/transaksi-formulir-barang/get-sub-kelompok'])
                    ]
                ])
            )).'</div>\'
        ],
    ], {
        6:0,
        7:1,
        3:2,
        4:3,
        });
    setTimeout(function () {
        $(\'#filter_instalasi\').trigger(\'depdrop:change\');
    },1);
    $("#filter_ruangan").select2Ruangan("'.$instalasiId.'", {
        additionalPayload: {
            column: "ruangan_nama,ruangan_id",
            instalasi_id: "' . $instalasiId . '"
        }
    })
});

$(document).on("keydown", null, "alt+s", function (event) {
    $("#btn-submit").click();
});

$(document).on("click", "#check-all", function(){
    if (typeof table == "undefined") return true;
    if ($("#check-all").prop("checked") == true) {
        table.rows().select();
        notIn = {};
        checkAll = true;
    }else{
        tmpDataSOBarang = {};
        list_barang_id = {};
        table.rows().deselect();
        checkAll = false;
    }

    trigger_tr_click();
});

$("#check-all").uniform({
    radioClass: "choice",
    checkboxClass: "checker checker-inverse"
});

function trigger_tr_click(){
    $.each($("#example tbody tr"), function(){
        $(this).trigger("click");
    });
}

$(document).on("click", "#example tbody tr", function(event){
    event.preventDefault(); 
    var checkedTbl = $(this).hasClass("selected");
    
    try {
        var result = table.row(this).data();
        tmpAllDataSO[result.barang_id] = result
        delete list_barang_id;
        if (checkedTbl) {
            if($("#check-all").prop("checked") == true){
                if(typeof notIn[result.barang_id] !== undefined){
                    delete notIn[result.barang_id];
                }
            }else{
                tmpDataSOBarang[result.barang_id] = {
                    barang_id : result.barang_id,
                    stok : result.stok_sistem,
                    harganetto : result.barang_harganetto
                };
            }
            list_barang_id[result.barang_id] = result.barang_id;
        } else {
            if($("#check-all").prop("checked") == true){
                notIn[result.barang_id] = {
                    barang_id : result.barang_id,
                    stok : result.stok_sistem,
                    harganetto : result.barang_harganetto
                };
            }else{
                if(typeof tmpDataSOBarang[result.barang_id] !== undefined){
                    delete tmpDataSOBarang[result.barang_id];
                }
            }
            delete list_barang_id[result.barang_id];
        }
    } catch (e) { }
});

', VIEW::POS_END, "js-kunings");
?>