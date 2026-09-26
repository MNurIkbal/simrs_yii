<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\View;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
?>

<style type="text/css">
    .table > tbody > tr > td {
        padding: 12px 9px;
    }
</style>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <div class="panel-body">
        <div class="row">
            <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="log_activity" style="width: 100%">
                <thead>
                    <tr class="bg-inverse">
                        <th class="text-center" width="5%">No</th>
                        <th><?= Yii::t('fe', 'Tanggal') ?></th>
                        <th><?= Yii::t('fe', 'Aksi') ?></th>
                        <th><?= Yii::t('fe', 'Oleh') ?></th>
                        <th><?= Yii::t('fe', 'Alasan') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center" colspan="11"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <hr>
    <div class="modal-footer">
    </div>
</div>

<?php
    $this->registerJs("
        var table;
        $(document).ready(function(){
            table = $('#log_activity').docoTabel({
                filter: true,
                sorting: [[3, 'asc']],
                displayLength: 10,
                processing: true,
                serverSide: true,
                ajax: baseUrl+'apotek/informasi-retur/get-log-activity?id=".$transaksi_id."',
                columns: [
                    {
                        title: 'No.',
                        data: 'rowNum',
                        searchable: false,
                        orderable: false,
                        class: 'text-center'
                    },
                    {
                        title: '".(\Yii::t('fe', 'Tanggal'))."',
                        data: 'tgl'
                    },
                    {
                        title: '".(\Yii::t('fe', 'Aksi'))."',
                        data: 'aksi'
                    },
                    {
                        title: '".(\Yii::t('fe', 'Oleh'))."',
                        data: 'keterangan'
                    },
                    {
                        title: '".(\Yii::t('fe', 'Alasan'))."',
                        data: 'alasan'
                    },
                ]
            });
            $('.dataTables_filter').hide();
        });

    ", VIEW::POS_END, 'js-kuning');
?>
