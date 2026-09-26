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
        <table class="table table-bordered datatable-basic dataTable tb-terra-medik" id="tb-fisioterapi" style="width:100%;">
            <thead>
                <tr class="bg-inverse">
                    <th width="8px">No</th>
                    <th>No Pendaftaran</th>
                    <th>Tgl Order - Selesai</th>
                    <th>Nama Pemeriksa</th>
                    <th>Dokter Perujuk</th>
                    <th>Diagnosa</th>
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
    var tableFisioterapi = null;

    // Initiate page
    $('#tab-fisioterapi').on('click', function () {

        if (tableFisioterapi == null) {
            let formWrapper = $(`.body-history-terra .filter-forms`);

            // Generate Table
            tableFisioterapi = $('#tb-fisioterapi').docoTabel({
                filter: false,
                displayLength: 10,
                stateSave: false,
                processing: true,
                serverSide: true,
                destroy : true,
                cacheFilter: false,
                paging: true,
                ajax: {
                    url : url +'/get-data-terra-fisioterapi?pasien_terra=' + pasien_terra,
                    data : {
                        advancedFilter: serializeArrayToJson(formWrapper),
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
                        title: 'No Pendaftaran',
                        data: 'no_pendaftaran',
                        searchable: false,
                        orderable: false
                    },
                    {
                        title: 'Tgl Order - Selesai',
                        data: 'tgl_order_selesai',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: 'Nama Pemeriksa',
                        data: 'nama_pemeriksa',
                        searchable: false,
                        orderable: false
                    },
                    {
                        title: 'Dokter Perujuk',
                        data: 'dokter_perujuk',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        title: 'Diagnosa',
                        data: 'diagnosa',
                        searchable: false,
                        orderable: false,
                    },

                ],
            });
        }

    });

", View::POS_END, 'js-fisioterapi');

?>
