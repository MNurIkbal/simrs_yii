<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\components\DocoConstants;


$this->title = \Yii::t('fe', $this->context->_title);
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace('instalasi_name'), 'url' => []];
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
                        <h3 class="panel-title"><b>
                            <?=
                                $this->title;
                            ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                    <?= DocoHelpers::generateToolbar([
                        'search',
                        'reset',
                        'mutasi' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Mutasi'),
                            'icon' => 'fa fa-send',
                            'method' => '#',
                            'attributes' => [
                                'class' => 'data-lihat btn btn-info btn-labeled btn-xs',
                                'id' => 'btn-mutasi',
                                'disabled' => true,
                                'data-options' => 'click',
                                'data-target' => Url::home().('apotek/pemusnahan-obat/mutasi'),
                            ],
                        ],
                        'export-excel-bg' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Excel'),
                            'icon' => 'fa fa-file-excel-o',
                            'attributes' => [
                                'id' => 'data-export-excel-bg',
                                'data-options' => 'excel-serconn',
                                'data-target' => '#modal_backdrop',
                                'data-url' => Url::home() . 'apotek/pemusnahan-obat/export-excel-popup?',
                                'data-width' => '75%'
                            ]
                        ],
                        // 'back' => [
                        //     'type' => 'link',
                        //     'title' => \Yii::t('fe', 'Pemusnahan'),
                        //     'icon' => 'fa fa-trash',
                        //     'method' => '#',
                        //     'attributes' => [
                        //         'class' => 'data-lihat btn btn-info btn-labeled btn-xs',
                        //         'href' => '/apotek/pemusnahan-obat/view',
                        //         'id' => 'btn-pemusnahan',
                        //         'disabled' => true,
                        //         'data-target' => Url::home().('apotek/pemusnahan-obat/view'),
                        //     ]
                        // ],
                    ],'#table-informasi'); ?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="table-informasi" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th width="1">No</th>
                            <th>
                                <?= \Yii::t("fe", "Nama Obat Alkes"); ?>
                            </th>
                            <th>
                                <?=\Yii::t("fe", "Tanggal Expired");?>
                            </th>
                            <th>
                                <?=\Yii::t("fe", "Qty");?>
                            </th>
                            <th>
                                <?=\Yii::t("fe", "Instalasi");?>
                            </th>
                            <th>
                                <?=\Yii::t("fe", "Ruangan");?>
                            </th>
                            <th>
                                <?=\Yii::t("fe", "Total Cost (WA)");?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="9"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Untuk Kebutuhan Modal Global -->
<div id="modal_backdrop_search" class="modal fade" style="z-index:1065;" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
<!-- End -->

<?php
$_ruanganToJson = json_encode($ruangan);
$_instalasiToJson = json_encode($instalasi);
$_ruanganId = Yii::$app->docoVars->workspace("ruangan_id");
$_instalasiId = Yii::$app->docoVars->workspace("instalasi_id");
$_isGudang = DocoConstants::GUDANG_FARMASI;
$_isInstalasiGudang = DocoConstants::INSTALASI_GUDANG_FARMASI;

$this->registerJs('
var table;
var _cachePemusnahan = {};
var _instalasi = '.$_instalasiToJson.';
var _ruangan = '.$_ruanganToJson.';
var _ruanganId = '.$_ruanganId.';
var _instalasiId = '.$_instalasiId.';
var _isGudang = '.$_isGudang.';
var _isInstalasiGudang = '.$_isInstalasiGudang.';
var _mapp = [];
var _counter = 0;

// if (localStorage.getItem("pemusnahan-obat")) {
//     var _cachePemusnahan = JSON.parse(localStorage.getItem("pemusnahan-obat"));
// }

$(document).on("click",".data-reset", function (event) {
    event.preventDefault();
     _cachePemusnahan = {};
})

var _checkBtn = function () {
    var _isDisable = false;
    $.each(_cachePemusnahan, function (key, val) {
        if(val.ruangan_id == _ruanganId){
            _isDisable = false;
            return true;
        }
    });

    if(_counter){
        if(_isDisable){
            $("#btn-mutasi").attr("disabled", true);
        }else{
            if (Object.keys(_cachePemusnahan).length) {
                $("#btn-mutasi").attr("disabled",false);
                return true;
            } else {
                $("#btn-mutasi").attr("disabled",true);
            }
        }
    }else{
        $("#btn-pemusnahan").attr("disabled", true);
        $("#btn-mutasi").attr("disabled",true);
    }
}


$(function(){
    _checkBtn();
    $.each(_ruangan, function (key,val) {
        _mapp[val.ruangan_id] = val.instalasi_id;
    })
    table = $("#table-informasi").docoTabel({
        filter: true,
        columnDefs: [{
            orderable: false,
            className: "select-checkbox",
            targets: 0,
        }],
        searchCols: [
          null,
          null,
          null,
          null,
          null,
          {"search": _instalasiId},
          {"search": _ruanganId},
          null
        ],
        select: {
            info: false,
            style: "multi",
            selector: "tr:not(.no-select)"
        },
        sorting: [[2, "asc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        stateSave: false,
        scrollX: true,
        ajax: baseUrl+"apotek/pemusnahan-obat/get-data",
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
                title: "'.(\Yii::t("fe", "Nama Obat Alkes")).'",
                data: "obatalkes_nama",
            },
            {
                title: "'.(\Yii::t("fe", "Tanggal Expired")).'",
                data: "tglkadaluarsa"
            },
            {
                title: "'.(\Yii::t("fe", "Qty")).'",
                data: "stok_display",
                name:"stok_display",
                searchable: false,
                orderable: false
            },
            {
                title: "'.(\Yii::t("fe", "Instalasi")).'",
                data: "instalasi_nama",
                name:"instalasi_id"
            },
            {
                title: "'.(\Yii::t("fe", "Ruangan")).'",
                data: "ruangan_nama",
                name:"ruangan_id"
            },
            {
                title: "'.(\Yii::t("fe", "Total Cost (WA)")).'",
                data: "cost_wa_display",
                class: "text-right",
                searchable: false,
            },
        ],
        rowCallback : function (row, data) {
            if(data.ruangan_id != _ruanganId){
                $(row).find("td").removeClass("select-checkbox");
            }
            var api = this.api();
            var dataRows = api.rows( {page:"current"} ).data();
            var tr = $(this);
            $.each(dataRows, function (key, val) {
                _counter++;
            })
            _checkBtn();
        }
    });
    $(".dataTables_filter").hide();
    $(".filter-form").datatableBootstrapFilter(table,
        [
            [
                3,
                \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('tglkadaluarsa', '',
                ArrayHelper::map($date_range, 'lookup_id', 'lookup_name'), ['class' => 'form-control select2',]))).'\'
            ],
            [
                5,
                \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('instalasi-list', $_instalasiId,
                    ArrayHelper::map($instalasi, 'instalasi_id', 'instalasi_nama'), [
                        'class' => 'form-control select2',
                        'prompt' => \Yii::t('fe', '--Pilih--')
                    ]))).'\'
            ],
            [
                6, \''.(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('ruangan-list', $_ruanganId,
                    ArrayHelper::map($ruangan, 'ruangan_id', 'ruangan_nama'), [
                        'class' => 'form-control select2',
                        'prompt' => \Yii::t('fe', '--Pilih--')
                    ]))).'\'
            ],

        ], {
            3:0,
            2:1,
            5:2,
            6:3
        }, true
    );

    $(document).on("change","select[name=instalasi-list]", function (event) {
        event.preventDefault();
        var _value = $(this).val();
        var _child = $("select[name=ruangan-list]");
        var _valChild = _child.val();
        _child.empty();
        var promptOpt = new Option("--Pilih--", "", false, false);
        _child.append(promptOpt);
        if (_value != "") {
            $.each(_ruangan, function (key, val) {
                var _selected = _valChild != val.ruangan_id ? false : true;
                if (val.instalasi_id == _value) {
                    var newOption = new Option(val.ruangan_nama, val.ruangan_id, false, _selected);
                    _child.append(newOption);
                }
            });
        } else {
            $.each(_ruangan, function (key, val) {
                var newOption = new Option(val.ruangan_nama, val.ruangan_id, false, false);
                _child.append(newOption);
            });
        }
        _checkBtn();
    });

    $(document).on("change","select[name=ruangan-list]", function (event) {
        event.preventDefault();
        var _value = $(this).val();
        var _parent = $("select[name=instalasi-list]").val();
        if (typeof _mapp[_value] != "undefined") {
            if (_parent == "") {
                $("select[name=instalasi-list]").val(_mapp[_value]).trigger("change");
            }
        }
        _checkBtn();
    });

    if(_instalasiId != _isInstalasiGudang) {
        $("select[name=instalasi-list]").prop("disabled", "disabled");
    }

    if(_ruanganId != _isGudang) {
        $("select[name=ruangan-list]").prop("disabled", "disabled");
    }

    $(".daterange-basic").daterangepicker({
        startDate: "'.(date("01-M-Y")).'", autoUpdateInput: true,
        endDate: "'.(date("d-M-Y")).'",
        applyClass: "bg-slate-600",
        cancelClass: "btn-default",
        locale: {
            format: "DD-MMMM-YYYY"
        }
    });

    $(document).on("click", "#table-informasi tr", function(event){
        event.preventDefault();
        var tbl = $(this).hasClass("selected");
        var checked = $(this).find("td").hasClass("select-checkbox");
        var result = table.row(this).data();
        if (tbl && checked) {
            _cachePemusnahan[result.primary] = result;
        } else {
            $(this).removeClass("selected");
            delete _cachePemusnahan[result.primary];
        }
        _checkBtn();
        localStorage.setItem("pemusnahan-obat",JSON.stringify(_cachePemusnahan))
    });

    $(document).on("click","#btn-pemusnahan", function (event) {
        event.preventDefault();
        var _href = $(this).attr("href");
        if ($(this).attr("disabled") != "disabled") {
            $.ajax({
                type : "json",
                method : "POST",
                data : _cachePemusnahan,
                url : "/apotek/pemusnahan-obat/set-storage",
                success : function (data) {
                    window.open(_href, \'_self\');
                },
                error : function (data) {

                }
            });
        }
    });
    $(document).on("click","#btn-mutasi", function (event) {
        event.preventDefault();
        var _href = $(this).attr("data-target");
        if ($(this).attr("disabled") != "disabled") {
            let key = "-mutasi-pemusnahan";
            $.ajax({
                type : "json",
                method : "POST",
                data : _cachePemusnahan,
                url : "/apotek/pemusnahan-obat/set-storage?key="+key,
                success : function (data) {
                    window.open(_href, \'_self\');
                },
                error : function (data) {

                }
            });
        }
    });

     $(".instalasi").select2({
        placeholder: "",
    })

    $(document).on("click",".data-reset", function (event) {
        event.preventDefault();
         _cachePemusnahan = {};
         $("select[name=instalasi-list]").val(_instalasiId).trigger("change");
         $("select[name=ruangan-list]").val(_ruanganId).trigger("change");
         $("select[name=tglkadaluarsa]").val(703).trigger("change");
         table.column(3).search(703).draw();
         table.column(5).search(_instalasiId).draw();
         table.column(6).search(_ruanganId).draw();
    });
});

',View::POS_END,'b-index');