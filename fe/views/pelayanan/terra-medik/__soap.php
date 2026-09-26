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
        <table class="table table-bordered datatable-basic dataTable tb-terra-medik" id="tb-terra" style="width:100%;">
            <thead>
                <tr class="bg-inverse">
                    <th colspan="3" class="text-center"><?= Yii::t('fe', 'SOAP / Verbal Order') ?></th>
                    <th colspan="1" class="text-center"><?= Yii::t('fe', 'Terapi') ?></th>
                </tr>
                <tr class="bg-inverse">
                    <th width="8px">No</th>
                    <th><?= Yii::t('fe', 'Ruang / Tanggal dan Jam / Profesi') ?></th>
                    <th><?= Yii::t('fe', 'Hasil Asesmen Penatalaksanaan Pasien') ?></th>
                    <th><?= Yii::t('fe', 'Instruksi DPJP Termasuk Pasca Bedah') ?></th>
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
    var tabel;

    // Initiate page
    $(document).ready(function () {
        let formWrapper = $(`.body-history-terra .filter-forms`);

        // Generate Table & Filternya manual
        tabel = $('#tb-terra').docoTabel({
            filter: false,
            displayLength: 10,
            stateSave: false,
            processing: true,
            serverSide: true,
            destroy : true,
            cacheFilter: false,
            paging: true,
            ajax: {
                url : url +'/get-data-terra-soap?pasien_terra=' + pasien_terra,
                data : {
                    advancedFilter : serializeArrayToJson(formWrapper),
                },
            },
            columns: [
                {
                    title: 'No',
                    data: 'no',
                    width: '10%',
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'Ruang / Tanggal dan Jam / Profesi',
                    data: 'ruang',
                    width: '15%',
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'Hasil Asesmen Penatalaksanaan Pasien',
                    data: 'soap',
                    width: '55%',
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'Instruksi DPJP Termasuk Pasca Bedah',
                    data: 'resep',
                    width: '20%',
                    searchable: false,
                    orderable: false
                },
            ],
            createdRow: (rowElement, data) => {
                if (data.is_dokter) {
                    for (var i = 0; i < rowElement.childNodes.length; ++i) {
                      if (i == 1) {
                        $(rowElement.childNodes[i]).css('background-color', '#1FA345')
                        $(rowElement.childNodes[i]).css('color', 'white')
                      }
                    }
                }
            },
        });
    });

", View::POS_END, 'index');

?>
