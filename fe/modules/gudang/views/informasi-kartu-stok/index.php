<?php

/**
 * @author Randy Vianda Putra
 * @todo Informasi Kartu Stok Barang
 * @copyright 17 April 2018 aweutist
 */

use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;


$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Informasi'), 'url' => ['index']];
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
                        <h3 class="panel-title"><b><?= $this->title ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'search',
                    'reset',
                    'excel' => [
                        'attributes' => [
                            'data-target' => $module.'unduh-excel?'
                        ]
                    ]
                ]); ?>
            </div>
            <div class="panel-body">
                <div class="panel-body filter-form">

                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?= \Yii::t("fe", "Tanggal transaksi"); ?></th>
                            <th><?= \Yii::t("fe", "Nama barang"); ?></th>
                            <th><?= \Yii::t("fe", "Tanggal Kadaluarsa"); ?></th>
                            <th><?= \Yii::t("fe", "No. Transaksi"); ?></th>
                            <th><?= \Yii::t("fe", "Keterangan"); ?></th>
                            <th><?= \Yii::t("fe", "Ruangan Asal"); ?></th>
                            <th><?= \Yii::t("fe", "Ruangan Tujuan"); ?></th>
                            <th><?= \Yii::t("fe", "Qty Masuk"); ?></th>
                            <th><?= \Yii::t("fe", "Qty Keluar"); ?></th>
                            <th><?= \Yii::t("fe", "Stok"); ?></th>
                            <th><?= \Yii::t("fe", "Satuan"); ?></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<?php

$this->registerJs("
    var table;
    $(document).on('click', '.data-reload', function() {
        table.draw();
    });

    $(document).ready(function(){
        var barang_nama = null;
        var stok = null;
        table = $('#example').docoTabel({
            sorting: [2,11],
            ajax:  baseUrl+'gudang/informasi-kartu-stok/get-data',
            drawCallback: function ( settings ) {
                var api = this.api();
                var rows = api.rows( {page:'current'} ).nodes();
                var number = api.column(0, {page:'current'} ).nodes();
                var last = null;
                var rownum = 1;
                var _num = 0;
                api.column(2, {page:'current'} ).data().each( function ( group, i ) {
                    rownum++;
                    if ( last !== group ) {
                        if(barang_nama == null){
                            barang_nama = api.column(2, {page:'current'} ).data()[i]
                        }

                        if(barang_nama !== api.column(2, {page:'current'} ).data()[i] || stok == null){
                            stok = api.column(10, {page:'current'} ).data()[i];
                            stok_out = api.column(9, {page:'current'} ).data()[i];
                            stok_in = api.column(8, {page:'current'} ).data()[i];
                            stok = parseInt(stok) + (stok_out - stok_in);
                            stok = stok < 0 ? 0 : stok;
                        }
                        $(rows).eq(i).before(
                            '<tr>'+
                                '<td></td>'+
                                '<td></td>'+
                                '<td></td>'+
                                '<td></td>'+
                                '<td></td>'+
                                '<td><b>Stok Awal<b></td>'+
                                '<td></td>'+
                                '<td></td>'+
                                '<td></td>'+
                                '<td></td>'+
                                '<td><b>'+stok+'<b></td>'+
                                '<td></td>'+
                            '</tr>'
                        );
                        barang_nama = api.column(2, {page:'current'} ).data()[i]
                        last = group;
                    }
                });
                last = null;
                stok = null;
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
                    title: '" . (\Yii::t('fe', 'Nama Barang')) . "',
                    data: 'barang_nama',
                    name: 'barang_id',
                    orderable: false
                },
                {
                    title: '" . (\Yii::t('fe', 'Tanggal kadaluarsa')) . "',
                    data: 'tglkadaluarsa',
                    orderable: false,
                    searchable: false
                },
                {
                    title: '" . (\Yii::t('fe', 'No. Transaksi')) . "',
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
                    title: '" . (\Yii::t('fe', 'Ruangan asal')) . "',
                    data: 'ruangan_asal_nama',
                    searchable: false,
                    orderable: false
                },
                {
                    title: '" . (\Yii::t('fe', 'Ruangan tujuan')) . "',
                    data: 'ruangan_tujuan_nama',
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
            ],
        });
        $('.dataTables_filter').hide();
        $('.filter-form').datatableBootstrapFilter(table, [
            [
                1,
                \"<div class='input-group'><input type='text' id='rangeDemoStart' value=".date('d-M-Y')." class='form-control startDate'/><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' value=".date('d-M-Y')." id='rangeDemoFinish' class='form-control endDate'/><input type='text' style='display:none' class='targetDate' col-index=2></div>\"
            ],
            [
                2,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('nama_barang', '', array(),
                            [
                                'class' => 'form-control select2 nama_barang',
                                'prompt' => '',
                                'id'=>'barang_nama',
                                'col-index' => 3
                            ]
                        )
                        )
                )."<div>\"
            ],
        ]);
        dateRangeHelper('.startDate','.endDate','.targetDate');
        dateRangeHelper('.startDate1','.endDate2','.targetDate1');
        $('.nama_barang').select2({
            language: {
                errorLoading: function () { return 'Searching...' }
            },
            placeholder: '',
            minimumInputLength: 3,
            ajax: {
                url: '/gudang/informasi-kartu-stok/get-barang',
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
    });
  ", VIEW::POS_END, 'js-kunings');

?>