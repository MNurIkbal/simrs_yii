<?php

use yii\web\View;
use yii\helpers\Html;
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><b><?=$title;?></b></h5>
</div>
<div class="modal-body">
    <div class="panel-body">
        <table id="table-log-activity" class="table nowrap table-striped table-hover table-framed" style="width:100%">
            <thead>
                <tr class="bg-inverse">
                    <th><?=\Yii::t("fe", "Waktu");?></th>
                    <th><?=\Yii::t("fe", "Keterangan");?></th>
                    <th><?=\Yii::t("fe", "Tipe");?></th>
                    <th><?=\Yii::t("fe", "Alasan");?></th>
                    <th><?=\Yii::t("fe", "Oleh");?></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <div class="modal-footer" style="padding:0px !important;">
        <!-- <hr> -->
        <?= Html::button("<b><i class='fa fa-arrow-left'></i></b>&nbsp;Kembali",[
            'class' => 'btn btn-info btn-labeled btn-xs',
            'data-dismiss' => 'modal'
        ]); ?>
    </div>
</div>
<?php 
$this->registerJs("
    var pendaftaranId = '".$pendaftaran_id."'
    var isKasir = '".$isKasir."'

    var tableLogActivity = $('#table-log-activity').docoTabel({
        filter: false,
        sorting: [[0, 'desc']],
        displayLength: 10,
        processing: true,
        serverSide: true,
        cache: false,
        ajax: {
            url: '/penatajasa/inf-tagihan-pasien/get-data-log-activity?pendaftaran_id=' + pendaftaranId + '&is_kasir=' + isKasir,
         },
         columns: [
            {
                title: 'Waktu',
                data: 'waktu',
                render: (data) => {
                    return data == '' || data == null ? '-' : data
                }
            },
            {
                title: 'Keterangan',
                data: 'keterangan',
                render: (data) => {
                    return data == '' || data == null ? '-' : data
                }
            },
            {
                title: 'Tipe',
                data: 'tipe',
                render: (data) => {
                    return data == '' || data == null ? '-' : data
                }
            },
            {
                title: 'Alasan',
                data: 'alasan',
                render: (data) => {
                    return data == '' || data == null ? '-' : data
                }
            },
            {
                title: 'Oleh',
                data: 'nama_pemakai',
                render: (data) => {
                    return data == '' || data == null ? '-' : data
                }
            },
         ],
    })
", View::POS_END);
?>
