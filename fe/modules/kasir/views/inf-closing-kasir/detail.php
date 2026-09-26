<?php
// Author : Ramdhan Nurrachman

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = \Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Kasir', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b>Closing <?=$title;?> </b></h3>
                <?=Breadcrumbs::widget([
                    'homeLink' => [ 
                        'label' => Yii::t('yii', 'Home'),
                        'url' => Yii::$app->homeUrl,
                    ],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]);?>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <div class="btn-group pull-left">
                    <?=DocoHelpers::generateToolbar([
                        'back',
                        'excel-detail' => [
                            'type'=>'button',
                            'title' => Yii::t('fe', 'Unduh Excel'),
                            'icon' => 'fa fa-file-excel-o',
                            'attributes' => [
                                'class' => 'excel',
                                'method' => 'json',
                                'data-options' => 'link'
                            ],
                        ],
                        'custom-print' => [
                            'type'=>'button',
                            'title' => Yii::t('fe', 'Cetak'),
                            'icon' => 'fa fa-print',
                            'attributes' => [
                                'class' => 'print',
                                'method' => 'json',
                                'data-options' => 'link'
                            ],
                        ],
                    ]);?>
                </div>
            </div>
            <div class="panel-body">
                <fieldset class="scheduler-border">
                    <center><legend class="scheduler-border">Closing Kasir <br> <?=$this->title;?></legend></center>
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <strong> Nomor Closing Kasir : <?= $response_header['no_closingkasir'] ?> </strong>
                        </div>
                        <div class="col-md-4 text-center"> 
                            <strong>Tanggal Closing Kasir : <?= date('d M Y H:i:s',strtotime($response_header['tgl_closingkasir'])) ?> </strong> 
                        </div>
                        <div class="col-md-4 text-center">
                            <strong> Shift Kasir : <?= $response_header['shift_nama'] ?> </strong>
                        </div>
                    </div>
                    <br>
                    <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th rowspan="2" width="1">No</th>
                                <th rowspan="2"><?=\Yii::t("fe", "Tanggal Pembayaran");?></th>
                                <th rowspan="2"><?=\Yii::t("fe", "Info Pasien");?></th>
                                <th rowspan="2"><?=\Yii::t("fe", "Cara Bayar");?></th>
                                <th rowspan="2"><?=\Yii::t("fe", "Metode Pembayaran");?></th>
                                <th colspan="4" class="text-center"><?=\Yii::t("fe", "Total");?></th>
                            </tr>
                            <tr class="bg-inverse">
                                <th class="text-center"><?=\Yii::t("fe", "Tagihan");?></th>
                                <th class="text-center"><?=\Yii::t("fe", "Tunai");?></th>
                                <th class="text-center"><?=\Yii::t("fe", "Non Tunai");?></th>
                                <th class="text-center"><?=\Yii::t("fe", "Dijamin");?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center" colspan="4"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th class="text-right"><strong><?= Yii::t('fe', 'Total') ?></strong></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table><br>
                    <table id="example2" class="table table-striped table-condensed table-hover" style="width:100%;display: none;">
                        <thead>
                            <tr class="bg-inverse">
                                <th width="1">No</th>
                                <th><?=\Yii::t("fe", "Uang Pecahan");?></th>
                                <th><?=\Yii::t("fe", "Qty");?></th>
                                <th><?=\Yii::t("fe", "Jumlah");?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center" colspan="3"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <th style="background-color: #EAFAF1"></th>
                            <th style="background-color: #EAFAF1"></th>
                            <th style="background-color: #EAFAF1" class="text-right"><strong><?= Yii::t('fe', 'Total') ?></strong></th>
                            <th style="background-color: #EAFAF1"></th>
                        </tfoot>
                    </table>
                </fieldset>
            </div>
        </div>
    </div>
</div>

<?php 
$this->registerJs('
    var table;
    // var table_pecahan;
    var id = '.$id.';

    $(document).on("click", ".data-reload", function() {
        table.draw();
        // table_pecahan.draw();
    });

    $(document).ready(function() {
        table = $("#example").docoTabel({
            filter: false,
            sorting: [[1, "desc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"'.(Yii::$app->controller->module->id).'/inf-closing-kasir/get-data-header?id=" + id,
            columns: [
                {title: "No", data: "rowNum", searchable: false, sortable: false},
                {title: "'.(\Yii::t("fe", "Tanggal Pembayaran")).'", data: "tgl_pembayaran"},
                {title: "'.(\Yii::t("fe", "Info Pasien")).'", data: "no_pendaftaran"},
                {title: "'.(\Yii::t("fe", "Cara Bayar/Penjamin")).'", data: "carabayar_nama"},
                {title: "'.(\Yii::t("fe", "Metode Pembayaran Non Tunai")).'", data: "metode_pembayaran_nama"},
                {title: "'.(\Yii::t("fe", "Tagihan (Rp.)")).'",  data: "total_tagihan", class:"text-right"},
                {title: "'.(\Yii::t("fe", "Tunai (Rp.)")).'",  data: "total_tunai", class:"text-right"},
                {title: "'.(\Yii::t("fe", "Non Tunai (Rp.)")).'",  data: "total_non_tunai", class:"text-right"},
                {title: "'.(\Yii::t("fe", "Dijamin (Rp.)")).'",  data: "total_dijamin", class:"text-right"},
            ],
            footerCallback: function(rowNum, data, start, end, display) {
                var api = this.api(), data;
                var intVal = function (i) {
                  return typeof i === "string" ? i.replace(/[\Rp.]/g, "")*1 : typeof i === "number" ? i : 0;
                };

                pageTotalTagihan = api.column(5, {page: "current"}).data().reduce(function (a, b) {
                  return intVal(a) + intVal(b);
                }, 0 );

                pageTotalTunai = api.column(6, {page: "current"}).data().reduce(function (a, b) {
                  return intVal(a) + intVal(b);
                }, 0 );

                pageTotalNonTunai = api.column(7, {page: "current"}).data().reduce(function (a, b) {
                  return intVal(a) + intVal(b);
                }, 0 );

                pageTotalDijamin = api.column(8, {page: "current"}).data().reduce(function (a, b) {
                  return intVal(a) + intVal(b);
                }, 0 );
                $(api.column(5).footer()).html(
                  "Rp. "+docoHelper.convertToRupiah(pageTotalTagihan)
                );
                $(api.column(6).footer()).html(
                  "Rp. "+docoHelper.convertToRupiah(pageTotalTunai)
                );
                $(api.column(7).footer()).html(
                  "Rp. "+docoHelper.convertToRupiah(pageTotalNonTunai)
                );
                $(api.column(8).footer()).html(
                  "Rp. "+docoHelper.convertToRupiah(pageTotalDijamin)
                );
                widthTableBawah = $(".dataTables_scrollHead").css("overflow")
                $(".dataTables_scrollFoot").css({"overflow" :widthTableBawah})
            }
        });
       
        // table_pecahan = $("#example2").docoTabel({
        //     filter: false,
        //     sorting: [[1, "desc"]], 
        //     displayLength: 10,
        //     processing: true,
        //     serverSide: true,
        //     scrollX: true,
        //     ajax: baseUrl+"'.(Yii::$app->controller->module->id).'/inf-closing-kasir/get-data-detail?id=" + id,
        //     columns: [
        //         {title: "No", data: "rowNum", searchable: false, sortable: false},
        //         {title: "'.(\Yii::t("fe", "Uang Pecahan")).'", data: "nilaiuang"},
        //         {title: "'.(\Yii::t("fe", "Qty")).'", data: "banyakuang", class:"text-right"},
        //         {title: "'.(\Yii::t("fe", "Jumlah (Rp.)")).'", data: "jumlahuang", class:"text-right"},
        //     ],
        //     footerCallback: function(rowNum, data, start, end, display) {
        //         var api = this.api(), data;
        //         var intVal = function (i) {
        //           return typeof i === "string" ? i.replace(/[\Rp.]/g, "")*1 : typeof i === "number" ? i : 0;
        //         };
        //         total = api.column(3).data().reduce(function (a, b) {
        //           return intVal(a) + intVal(b);
        //         }, 0 );

        //         pageTotal = api.column(3, {page: "current"}).data().reduce(function (a, b) {
        //           return intVal(a) + intVal(b);
        //         }, 0 );

        //         $(api.column(3).footer()).html(
        //           "Rp. "+docoHelper.convertToRupiah(pageTotal)
        //         );
        //     }
        // });
        // $(".dataTables_filter").hide();
        // $(".filter-form").datatableBootstrapFilter(table);
    });

$(document).on("click", ".print", function() {
    window.open("/kasir/inf-closing-kasir/export-pdf?id="+id);
});
$(document).on("click", ".excel", function() {
    window.open("/kasir/inf-closing-kasir/export-excel?id="+id);
});
', View::POS_END, 'b-index');
?>
