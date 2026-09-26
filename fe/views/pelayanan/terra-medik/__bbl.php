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
        <table class="table table-bordered datatable-basic dataTable tb-terra-medik" id="tb-bbl" style="width:100%;">
            <thead>
                <tr class="bg-inverse">
                    <th width="8px">No</th>
                    <th>Nama Bayi</th>
                    <th>Hari/Tanggal/Jam Lahir</th>
                    <th>Panjang</th>
                    <th>Berat</th>
                    <th>Dokter</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<?php

$this->registerJs("
    var pasien_terra = '$pasien_terra';

    var url          = '$url';

    // Tabel
    var tableBbl = null;

    // Initiate page
    $('#tab-bbl').on('click', function () {

        if (tableBbl == null) {
            let formWrapper = $(`.body-history-terra .filter-forms`);

            // Generate Table
            tableBbl = $('#tb-bbl').docoTabel({
                filter: false,
                stateSave: false,
                displayLength: 10,
                processing: true,
                serverSide: true,
                destroy : true,
                cacheFilter: false,
                paging: true,
                ajax: {
                    url : url +'/get-data-terra-bbl?pasien_terra=' + pasien_terra,
                    data : {
                        advancedFilter : serializeArrayToJson(formWrapper),
                    }
                },
                columns: [
                    {
                        title: 'No',
                        data: 'no',
                        searchable: false,
                        orderable: false,
                        width:'5px'
                    },
                    {
                        title: 'Nama Bayi',
                        data: 'nama_bayi',
                        searchable: false,
                        orderable: false
                    },
                    {
                        title: 'Hari/Tanggal/Jam Lahir',
                        data: 'hari_tanggal_jam',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: 'Panjang',
                        data: 'panjang',
                        searchable: false,
                        orderable: false
                    },
                    {
                        title: 'Berat',
                        data: 'berat',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: 'Dokter',
                        data: 'dokter',
                        searchable: false,
                        orderable: false,
                    },
                ],
            });
        }
    });

", View::POS_END, 'js-bbl');

?>
