<?php
use app\components\DocoConstants;
use yii\bootstrap\Modal;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\DateTimePicker;
?>

<div class="panel-body">
    <div class="row">
        <table class="table table-bordered datatable-basic dataTable tb-terra-medik" id="tb-terra-lab" style="width:100%;">
            <thead>
                <tr class="bg-inverse">
                    <th width="8px">No</th>
                    <th><?= Yii::t('fe', 'No Rekam Medik') ?></th>
                    <th><?= Yii::t('fe', 'Order Pemeriksa') ?></th>
                    <th><?= Yii::t('fe', 'Detail') ?></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<?php

$this->registerJs("
    var pasien_terra = '$pasien_terra';

    var detail_lab = [];

    var lab_columns = ['No', 'Dokter', 'Grup Pemeriksaan', 'Nama Pemeriksaan', 'Result', 'Nilai Normal', 'Satuan Pemeriksa'];

    // Tabel
    var tabel_lab = null;

    // Initiate page
    $('#tab-lab').on('click', function () {
        let formWrapper = $(`.body-history-terra .filter-forms`);

        if (tabel_lab == null) {
            // Generate Table
            tabel_lab = $('#tb-terra-lab').docoTabel({
                filter: false,
                displayLength: 10,
                processing: true,
                stateSave: false,
                serverSide: true,
                destroy : true,
                cacheFilter: false,
                paging: true,
                ajax: {
                    url : url +'/get-data-terra-laboratorium?pasien_terra=' + pasien_terra,
                    data : {
                        advancedFilter: serializeArrayToJson(formWrapper),
                    }
                },
                columns: [
                    {
                        title: 'No',
                        data: 'no',
                        searchable: false,
                        orderable: false
                    },
                    {
                        title: 'No Rekam Medik',
                        data: 'no_rm',
                        searchable: false,
                        orderable: false
                    },
                    {
                        title: 'Order Pemeriksa',
                        data: 'order_pemeriksa',
                        searchable: false,
                        orderable: false
                    },
                    {
                        title: 'Detail',
                        data: 'detail',
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row, meta) {
                            detail_lab[meta.row] = data;
                            html = '<button type=\"button\" class=\"btn btn-sm btn-success show-detail\"><i class=\"fa fa-plus-square-o\"></i></button>';
                            return html;
                        }
                    },
                ],
                initComplete: function( settings, json ) {

                }
            });


            $('#tb-terra-lab').on('click', '.show-detail', ({currentTarget}) => {
                if ($('#tb-terra-lab tbody tr').length > 0) {
                    let _this = $(currentTarget);
                    let data = {data : detail_lab, columns : lab_columns};
                    processExpandDetail(_this, data);
                }
            });
        }

    });

", View::POS_END, 'js-labs');

?>
