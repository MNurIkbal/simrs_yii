<?php

/**
 * @author : Rizqi Fitrianto
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 * @edited by : Anggoro (tri.anggoro@docotel.com)
 */


use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\DepDrop;


$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Apotek', 'url' => ['index']];
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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias","Informasi Kartu Stok"); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    "search"=> [
                        'attributes'=>[
                            'id' => 'find-data',
                        ]
                    ],
                    'reset'=>['attributes'=>['data-parent'=>'.filter-form']],
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
                            'data-url' => '/apotek/informasi-kartu-stok/show-popup-excel?',
                        ]
                    ],
                ]);?>
            </div>
            <div class="panel-body">
                <div class="filter-form">
                </div>
                <div style="font-weight: bold">Jumlah Sisa Obat: <span id="total_sisa"></span></div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?= \Yii::t("fe", "Tanggal transaksi"); ?></th>
                            <th><?= \Yii::t("fe", "Kode obat alkes"); ?></th>
                            <th><?= \Yii::t("fe", "Nama obat alkes"); ?></th>
                            <th><?= \Yii::t("fe", "Tanggal kadaluarsa"); ?></th>
                            <th><?= \Yii::t("fe", "No Transakasi"); ?></th>
                            <th><?= \Yii::t("fe", "Keterangan"); ?></th>
                            <th><?= \Yii::t("fe", "Reference"); ?></th>
                            <th><?= \Yii::t("fe", "Qty masuk"); ?></th>
                            <th><?= \Yii::t("fe", "Qty keluar"); ?></th>
                            <th><?= \Yii::t("fe", "Stok"); ?></th>
                            <th><?= \Yii::t("fe", "Satuan"); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- <tr>
                            <td class="text-center" colspan="3"><?= \Yii::t("fe", "Data tidak ditemukan."); ?></td>
                        </tr> -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php

$this->registerJs("
    var table;
    // Event Reload
    $(document).on('click', '.data-reload', function() {
        table.draw();
    });

    $(document).on('change', '#obatalkes_nama', function(){
        $('#find-data').click();
    });

    $(document).on('change', '#rangeDemoFinish', function(){
        $('#find-data').click();
    });

    $(document).ready(function(){
        var jumlah_stok = null;
        var obatalkes_nama = null;
        table = $('#example').docoTabel({
            sorting: [2,11],
            stateSave: false,
            ajax:  baseUrl+'apotek/informasi-kartu-stok/get-data',
            drawCallback: function ( settings ) {
                var api = this.api();
                var rows = api.rows( {page:'current'} ).nodes();
                var number = api.column(0, {page:'current'} ).nodes();
                var last = null;
                var rownum = 1;
                var _num = 0;
                var satuan = '';
                api.column(3, {page:'current'} ).data().each( function ( group, i ) {
                    rownum++;
                    if ( last !== group ) {
                        if(obatalkes_nama == null){
                            obatalkes_nama = api.column(3, {page:'current'} ).data()[i]
                        }

                        if(obatalkes_nama !== api.column(3, {page:'current'} ).data()[i] || jumlah_stok == null){
                            jumlah_stok = api.column(10, {page:'current'} ).data()[i];
                            satuan = api.column(11, {page:'current'} ).data()[i];
                            stok_out = api.column(9, {page:'current'} ).data()[i];
                            stok_in = api.column(8, {page:'current'} ).data()[i];
                            jumlah_stok = parseFloat(jumlah_stok) + parseFloat(stok_out - stok_in);
                            jumlah_stok = Math.round(jumlah_stok * Math.pow(10, 2))/Math.pow(10, 2);
                        }
                        $(rows).eq(i).before(
                            '<tr>'+
                                '<td></td>'+
                                '<td></td>'+
                                '<td></td>'+
                                '<td></td>'+
                                '<td></td>'+
                                '<td></td>'+
                                '<td><b>Stok Awal<b></td>'+
                                '<td></td>'+
                                '<td></td>'+
                                '<td></td>'+
                                '<td><b>'+jumlah_stok+'<b></td>'+
                                '<td><b>'+satuan+'<b></td>'+
                            '</tr>'
                        );
                        obatalkes_nama = api.column(3, {page:'current'} ).data()[i]
                        last = group;
                    }
                });
                last = null;
                jumlah_stok = null;
            },
            columns: [
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {
                    title: '" . (\Yii::t('fe', 'Tanggal transaksi')) . "',
                    data: 'tanggal_transaksi',
                    orderable: false
                },
                {
                    title: '" . (\Yii::t('fe', 'Kode obat alkes')) . "',
                    data: 'obatalkes_kode',
                    name: 'obatalkes_kode',
                    orderable: false,
                    searchable: false
                },
                {
                    title: '" . (\Yii::t('fe', 'Nama obat alkes')) . "',
                    data: 'obatalkes_nama',
                    name: 'obatalkes_id',
                    orderable: false
                },
                {
                    title: '" . (\Yii::t('fe', 'Tanggal kadaluarsa')) . "',
                    data: 'tglkadaluarsa',
                    orderable: false,
                    searchable: false
                },
                {
                    title: '" . (\Yii::t('fe', 'No Transaksi')) . "',
                    data: 'no_transaksi',
                    orderable: false,
                    searchable: false
                },
                {
                    title: '" . (\Yii::t('fe', 'Keterangan')) . "',
                    data: 'keterangan',
                    searchable: false,
                    orderable: false
                },
                {
                    title: '" . (\Yii::t('fe', 'Reference')) . "',
                    data: 'reference',
                    searchable: false,
                    orderable: false
                },
                {
                    title: '" . (\Yii::t('fe', 'Qty masuk')) . "',
                    data: 'qtystok_in',
                    searchable: false,
                    orderable: false
                },
                {
                    title: '" . (\Yii::t('fe', 'Qty keluar')) . "',
                    data: 'qtystok_out',
                    searchable: false,
                    orderable: false
                },
                {
                    title: '" . (\Yii::t('fe', 'Stok')) . "',
                    data: 'total',
                    searchable: false,
                    orderable: false
                },
                {
                    title: '" . (\Yii::t('fe', 'Satuan')) . "',
                    data: 'satuanunit_nama',
                    searchable: false,
                    orderable: false
                },
                // {
                //     title: '" . (\Yii::t('fe', 'Resep Id')) . "',
                //     data: 'stokobatalkes_id',
                //     searchable: false,
                //     visible: false
                // },
            ],
        });
        $('.dataTables_filter').hide();
        $('.filter-form').datatableBootstrapFilter(table, [
            [
                1,
                \"<div class='input-group'><input type='text' id='rangeDemoStart' value=".date('d-M-Y')." class='form-control startDate'/><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' value=".date('d-M-Y')." id='rangeDemoFinish' class='form-control endDate'/><input type='text' style='display:none' class='targetDate' col-index=2></div>\"
            ],
            [
                3,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('nama_obat', '', array(),
                            [
                                'class' => 'form-control select2 nama_obat',
                                'prompt' => '',
                                'id'=>'obatalkes_nama',
                                'col-index' => 3
                            ]
                        )
                        )
                ). "<div>\"
            ],
        ]);
        dateRangeHelper('.startDate','.endDate','.targetDate');
        dateRangeHelper('.startDate1','.endDate2','.targetDate1');
        $('.nama_obat').select2({
            placeholder: '',
            minimumInputLength: 3,
            ajax: {
                url: '/apotek/informasi-kartu-stok/get-obat',
                dataType: 'json',
                quietMillis: 250,
                data: function(term, page){
                    return{
                        q: term,
                        page: page
                    }
                },
                processResults: function (data) {
                    return {
                        results: data.result
                    };
                }
            },
            dropdownCssClass: 'bigdrop',
            escapeMarkup: function (m) { return m; },
        });

        $(document).on('draw.dt', '#example', function(e) {
            getStokObat()
        });

        function getStokObat() {
            var obatalkes_id = $('.nama_obat').val();
            var tgl_transaksi = $('.startDate').val() + ' - ' + $('.endDate').val();
            var stok = 0;
            if(obatalkes_id != '') {
                var string_url = baseUrl + 'apotek/informasi-kartu-stok/get-stok-obat?'
                    + 'obatalkes_id=' + obatalkes_id
                    + '&tgl_transaksi=' + tgl_transaksi;
                $.ajax({
                    url: string_url,
                    success: function(data) {
                        $('#total_sisa').html(data);
                    }
                });
            } else {
                $('#total_sisa').html('-');
            }
        }
    });


    ", VIEW::POS_END, 'js-kunings');
?>
