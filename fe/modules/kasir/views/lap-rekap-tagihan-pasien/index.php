<?php

use yii\web\View;
use yii\helpers\Html;
use app\components\DHtml;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;

$this->title = DHtml::getTitleMenu();

$this->params['breadcrumbs'][] = ['label' => 'Kasir', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<style>
    .dataTables_scroll {
        max-height: 100%;
        overflow: auto;
        position: relative;
    }
    .bg-yellow {
        background-color: #FCF3CF;
        color: #000000;
        font-weight: bold;
    }
    .bg-green {
        background-color: #32c949;
        color: #000000;
        font-weight: bold;
    }
    .bg-blue {
        background-color: #679beb;
        color: #000000;
        font-weight: bold;
    }
</style>

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
                <?php
                    $defaultBtn = [
                    'search' => [
                        'attributes' => [
                            'data-parent'=>'.filter-rekap',
                            'class' => 'btn btn-info btn-labeled btn-xs cari-rekap'
                        ]
                    ],
                    'reset' => [
                        'attributes' => [
                            'data-parent'=>'.filter-rekap',
                            'class' => 'btn btn-info btn-labeled btn-xs reset-rekap'
                        ]
                    ],
                    'excel-bgprocess' => [
                        'type' => 'button',
                        'title' => 'Unduh excel',
                        'icon' => 'fa fa-file-excel-o',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id'=>'excel-bgprocess',
                            'data-options' => 'excel-serconn',
                            'data-target' => '#modal_backdrop',
                            'data-width' => '50%',
                            'data-url' => '/kasir/lap-rekap-tagihan-pasien/show-popup',
                        ]
                    ],

                ];

                ?>
                <?=DocoHelpers::generateToolbar($defaultBtn,'#table-informasi');?>
            </div>

            <div class="panel-body">
                <div class="search-form">
                    <div class="col-md-2 filter_bulan_tahun">
                        <label>Pilih Bulan Awal: </label>
                        <?= Html::textInput('bulan_awal', date('Y').'-'.date('m'), [
                            'class'=>'form-control',
                            'id'=>'filter_bulan_awal',
                            'type' => 'month',
                            'min' => '2018-01',
                            'max' => date('Y').'-'.date('m'),
                        ]); ?>
                    </div>
                    
                    <div class="col-md-2 filter_bulan_tahun2">
                        <label>Pilih Bulan Akhir : </label>
                        <?= Html::textInput('bulan_akhir', date('Y').'-'.date('m'), [
                            'class'=>'form-control',
                            'id'=>'filter_bulan_akhir',
                            'type' => 'month',
                            'min' => '2018-01',
                            'max' => date('Y').'-'.date('m'),
                        ]); ?>
                    </div>

                    <div class="col-md-2">
                        <label>Pilih Penjamin : </label>
                        <?= Html::dropDownList('penjamin', '', [], [
                            'class'=>'form-control',
                            'id'=>'filter_penjamin',
                            'prompt'=>Yii::t('fe', '----Pilih Penjamin----'),
                        ]); ?>
                    </div>
                    <div class="col-md-2">
                        <label>Pilih Pelayanan : </label>
                        <?= Html::dropDownList('unit', '', [], [
                            'class'=>'form-control',
                            'id'=>'filter_unit',
                            'prompt'=>Yii::t('fe', '----Pilih Pelayanan----'),
                        ]); ?>
                    </div>
                    <div class="col-md-2">
                        <label>Pilih Ruangan : </label>
                        <?= Html::dropDownList('ruangan', '', [], [
                            'class'=>'form-control',
                            'id'=>'filter_ruangan',
                            'prompt'=>Yii::t('fe', '----Pilih Ruangan----'),
                        ]); ?>
                    </div>
                </div>
                <br>
                <br>
                <br>
                <br>
                <table id="table-informasi" class="table" style="width:100%">
                    <thead>
                    <tr class="bg-inverse">
                            <th rowspan="2" width="1">No</th>
                            <th rowspan="2"><?=\Yii::t("fe", "Unit Pelayanan");?></th>
                            <th rowspan="2"><?=\Yii::t("fe", "Penjamin");?></th>
                            <th rowspan="2"><?=\Yii::t("fe", "Ruangan");?></th>
                            <th colspan="2" class="text-center"><?=\Yii::t("fe", "JAN")." " ?><span class="current_year"></span></th>
                            <th colspan="2" class="text-center"><?=\Yii::t("fe", "FEB")." " ?><span class="current_year"></span></th>
                            <th colspan="2" class="text-center"><?=\Yii::t("fe", "MAR")." " ?><span class="current_year"></span></th>
                            <th colspan="2" class="text-center"><?=\Yii::t("fe", "APR")." " ?><span class="current_year"></span></th>
                            <th colspan="2" class="text-center"><?=\Yii::t("fe", "MEI")." " ?><span class="current_year"></span></th>
                            <th colspan="2" class="text-center"><?=\Yii::t("fe", "JUN")." " ?><span class="current_year"></span></th>
                            <th colspan="2" class="text-center"><?=\Yii::t("fe", "JUL")." " ?><span class="current_year"></span></th>
                            <th colspan="2" class="text-center"><?=\Yii::t("fe", "AUG")." " ?><span class="current_year"></span></th>
                            <th colspan="2" class="text-center"><?=\Yii::t("fe", "SEPT")." " ?><span class="current_year"></span></th>
                            <th colspan="2" class="text-center"><?=\Yii::t("fe", "OKT")." " ?><span class="current_year"></span></th>
                            <th colspan="2" class="text-center"><?=\Yii::t("fe", "NOV")." " ?><span class="current_year"></span></th>
                            <th colspan="2" class="text-center"><?=\Yii::t("fe", "DES")." " ?><span class="current_year"></span></th>
                            <th rowspan="2"><?=\Yii::t("fe", "Total Biaya");?></th>
                        </tr>
                        <tr class="bg-inverse">
                        <?php
                            for ($x = 0; $x <= 11; $x++) {
                        ?>
                            <th><?=\Yii::t("fe", "Lama");?></th>
                            <th><?=\Yii::t("fe", "Baru");?></th>
                        <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="29"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
// Global Var
var table;
var _listPenjamin = '.json_encode($listPenjamin).';
var _listRuangan = '.json_encode($listRuangan).';
var _listUnit = '.json_encode($listUnit).';
var _dataPenjamin = [];
var _dataRuangan = [];
var _dataUnit = [];

$.each(_listPenjamin, function( index, value ) {
    _dataPenjamin.push({
        id: index,
        text: value,
    });
});
$.each(_listRuangan, function( index, value ) {
    _dataRuangan.push({
        id: index,
        text: value,
    });
});
$.each(_listUnit, function( index, value ) {
    _dataUnit.push({
        id: index,
        text: value,
    });
});

$(document).ready(function() {
    //Initial Filter Set
    _thisYear = new Date().getFullYear()
    _thisMonth = new Date().getMonth()+1
    $("#filter_bulan_awal").val(_thisYear+"-01")
    $("#filter_bulan_akhir").val(_thisYear+"-0"+_thisMonth)
    $(".current_year").text(_thisYear)

    var column = [
        {
            title: "No",
            data: "rowNum",
            searchable: false,
            orderable: false
        },
        {
            title: "'.(\Yii::t("fe", "Unit Pelayanan")).'",
            data: "unit_pelayanan",
            name:"unit_pelayanan",
            searchable : false,
        },
        {
            title: "'.(\Yii::t("fe", "Penjamin")).'",
            data: "penjamin",
            name:"penjamin",
            searchable : false,
        },
        {
            title: "'.(\Yii::t("fe", "Ruangan")).'",
            data: "ruangan",
            name:"ruangan",
            searchable : false,
        },
    ];
    const month = ["January","February","March","April","May","June","July","August","September","October","November","December"];
    for(var i = 0; i <= 11; i++) {
        column.push({
            title: "Lama",
            data: "kunjungan_lama-"+ month[i],
            searchable: false,
            className: "text-right",
        });
        column.push({
            title: "Baru",
            data: "kunjungan_baru-"+ month[i],
            searchable: false,
            className: "text-right",
        });
    }
    column.push({
        title: "'.(\Yii::t("fe", "Total Biaya")).'",
        data: "total",
        name:"total",
        searchable: false,
        visible: true,
    },
    )
    table = $("#table-informasi").docoTabel({
        filter: true,
        ordering: false,
        processing: true,
        serverSide: true,
        scrollX: true,
        paging: false,
        info: false,
        ajax: baseUrl+"'.(Yii::$app->controller->module->id).'/lap-rekap-tagihan-pasien/get-data",
        columns: column,
        createdRow : function(row, data) {
            var is_penjamin = data.is_penjamin;
            var is_unit = data.is_unit;
            var is_total = data.is_total;
            if(is_penjamin) {
                $(row).addClass("bg-yellow");
            }
            if(is_unit) {
                $(row).addClass("bg-green");
            }
            if(is_total) {
                $(row).addClass("bg-blue");
            }
        },
    });

    $("#filter_bulan_awal").on("change", function(event){
        date_awal = $("#filter_bulan_awal").val()
        dateval_awal = new Date(date_awal).valueOf()
        date_akhir = $("#filter_bulan_akhir").val()
        dateval_akhir = new Date(date_akhir).valueOf()
        year_awal = new Date(date_awal).getFullYear()
        year_akhir = new Date(date_akhir).getFullYear()

        if(dateval_awal > dateval_akhir || year_awal != year_akhir){
            $("#filter_bulan_akhir").val(date_awal)
        }
    })
    $("#filter_bulan_akhir").on("change", function(event){
        date_awal = $("#filter_bulan_awal").val()
        dateval_awal = new Date(date_awal).valueOf()
        date_akhir = $("#filter_bulan_akhir").val()
        dateval_akhir = new Date(date_akhir).valueOf()
        year_awal = new Date(date_awal).getFullYear()
        year_akhir = new Date(date_akhir).getFullYear()

        if(dateval_awal > dateval_akhir || year_awal != year_akhir){
            $("#filter_bulan_awal").val(date_akhir)
        }
    })
    $(".dataTables_filter").hide();
    $("#filter_penjamin").select2({
        placeholder: "-- Pilih Penjamin --",
        allowClear: false,
        data: _dataPenjamin, 
    });
    $("#filter_ruangan").select2({
        placeholder: "-- Pilih Ruangan --",
        allowClear: false,
        data: _dataRuangan, 
    });
    $("#filter_unit").select2({
        placeholder: "-- Pilih Pelayanan --",
        allowClear: false,
        data: _dataUnit, 
    });
    $(".cari-rekap").on("click", function(event){
        event.preventDefault();
        var _penjamin = $("#filter_penjamin").val();
        var _ruangan = $("#filter_ruangan").val();
        var _unit = $("#filter_unit").val();
        var _tanggalMulai = $("#filter_bulan_awal").val();
        var _tanggalAkhir = $("#filter_bulan_akhir").val();
        year_awal = new Date(_tanggalMulai).getFullYear()
        $(".current_year").text(year_awal)
    
        _url = baseUrl+"kasir/lap-rekap-tagihan-pasien/get-data?tanggalMulai="+ _tanggalMulai + "&tanggalAkhir=" + _tanggalAkhir + "&penjamin=" + _penjamin + "&ruangan=" + _ruangan + "&unit=" + _unit
        table.ajax.url(_url).draw(false);
    });
    $(".reset-rekap").on("click", function (event) {
        event.preventDefault()

        $("#filter_penjamin").val("").trigger("change");
        $("#filter_ruangan").val("").trigger("change");
        $("#filter_unit").val("").trigger("change");
        _thisYear = new Date().getFullYear()
        _thisMonth = new Date().getMonth()+1
        $("#filter_bulan_awal").val(_thisYear+"-01")
        $("#filter_bulan_akhir").val(_thisYear+"-0"+_thisMonth)
        

        _url = baseUrl+"kasir/lap-rekap-tagihan-pasien/get-data"
        table.ajax.url(_url).draw(false);
    });

    $("#excel-bgprocess").unbind("click");
    $("#excel-bgprocess").on("click", function (event) {
        var _penjamin = $("#filter_penjamin").val();
        var _ruangan = $("#filter_ruangan").val();
        var _unit = $("#filter_unit").val();
        var _tanggalMulai = $("#filter_bulan_awal").val();
        var _tanggalAkhir = $("#filter_bulan_akhir").val();

        _url = encodeURI(baseUrl+"kasir/lap-rekap-tagihan-pasien/show-popup?tanggalMulai="+ _tanggalMulai + "&tanggalAkhir=" + _tanggalAkhir + "&penjamin=" + _penjamin + "&ruangan=" + _ruangan + "&unit=" + _unit +"&")
        $(this).attr("data-url",_url);
    })
});



',View::POS_END,'b-index');
