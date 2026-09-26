<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;


$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = [
    'label' => Yii::$app->docoVars->workspace("modul_alias"),
    'url' => ['index']
];
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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias","Informasi Retur Obat"); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'search',
                    'reset',
                    'tagihan' => [
                        'title' => \Yii::t('fe', 'Cetak Transaksi'),
                        'icon' => 'fa fa-print',
                        'attributes' => [
                            'id'=>'cetak-tagihan',
                            'data-target' => '/gudang/informasi-penerimaan-obat/retur-pdf?no_retur=',
                            'data-pages' => '_blank'
                        ]
                    ],
                    'pdf',
                    'excel'
                ]); ?>
            </div>
            <div class="panel-body">
                <div class="advanced-filter">
                </div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"></th>
                            <th><?= \Yii::t("fe", "No"); ?></th>
                            <th><?= \Yii::t("fe", "Tanggal Retur"); ?></th>
                            <th><?= \Yii::t("fe", "No Retur"); ?></th>
                            <th><?= \Yii::t("fe", "No Penerimaan"); ?></th>
                            <th><?= \Yii::t("fe", "No Faktur"); ?></th>
                            <th><?= \Yii::t("fe", "Supplier"); ?></th>
                            <th><?= \Yii::t("fe", "Nama Obat"); ?></th>
                            <th><?= \Yii::t("fe", "Qty Obat"); ?></th>
                            <th><?= \Yii::t("fe", "Alasan Retur"); ?></th>
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

$this->registerJs("
    var table;
    $(document).ready(function(){
        table = $('#example').docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets:   0
            }],
            select: {
                style:    'os',
                selector: 'tr'
            },
            sorting: [[2, 'asc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+'gudang/informasi-retur-obat-supplier/get-data',
            columns: [
                {
                    data: null,
                    searchable: false,
                    orderable: false,
                    width: '50px',
                    defaultContent: ''
                },
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {
                    title: '".(\Yii::t('fe', 'Tanggal Retur'))."',
                    data: 'tgl_retur'
                },
                {
                    title: '".(\Yii::t('fe', 'No Retur'))."',
                    data: 'no_returpenerimaanobat'
                },
                {
                    title: '".(\Yii::t('fe', 'No Penerimaan'))."',
                    data: 'no_penerimaan',
                },
                {
                    title: '".(\Yii::t('fe', 'No Faktur'))."',
                    data: 'no_faktur'
                },
                {
                    title: '".(\Yii::t('fe', 'Supplier'))."',
                    data: 'supplier_nama'
                },
                {
                    title: '".(\Yii::t('fe', 'Nama Obat'))."',
                    data: 'obatalkes_nama'
                },
                {
                    title: '".(\Yii::t('fe', 'Qty Retur'))."',
                    data: 'qty_retur',
                    searchable: false,
                },
                {
                    title: '".(\Yii::t('fe', 'Alasan Retur'))."',
                    data: 'alasan_retur',
                    searchable: false,
                    orderable: false,
                },
            ],
        });
        $('.dataTables_filter').hide();
        $('.filter-form').datatableBootstrapFilter(table, [
            [
                2,
                \"<div class='input-group'><input type='text' id='rangeDemoStart' class='form-control startDate' value='".date('d-M-Y')."' readonly/><span class='input-group-addon' style='border-left: 0; border-right: 0;'>-</span><input type='text' id='rangeDemoFinish' class='form-control endDate' value='".date('d-M-Y')."' readonly/><input type='text' style='display:none' class='targetDate' col-index=2></div>\"
            ],
        ], {2:0},true);
        dateRangeHelper('.startDate','.endDate','.targetDate');
    });
", VIEW::POS_END, 'js-kunings');
?>