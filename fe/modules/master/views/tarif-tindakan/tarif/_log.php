<?php
/**
 * @author Budi
 * Powered by Sirs
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use app\components\DocoHelpers;
use yii\helpers\Url;
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Log Tarif</h5>
</div>

<div class="modal-body">
    <div class="col-md-12">
        <div class="panel panel-default panel-bordered">
            <div class="panel-heading">
                <h6 class="panel-title">Log Tarif</h6>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12">
                                <table 
                                    class="table datatable-basic table-hover dataTable no-footer"
                                    id="table-log"
                                    style="width: 100%;"
                                >
                                    <thead>
                                        <tr class="bg-inverse">
                                            <th><?=Yii::t('fe', 'No'); ?></th>
                                            <th><?=Yii::t('fe', 'Aksi'); ?></th>
                                            <th><?=Yii::t('fe', 'Nama Tindakan/Paket'); ?></th>
                                            <th><?=Yii::t('fe', 'Keterangan'); ?></th>
                                            <th><?=Yii::t('fe', 'Tarif'); ?></th>
                                            <th><?=Yii::t('fe', 'Tanggal'); ?></th>
                                            <th><?=Yii::t('fe', 'User'); ?></th>
                                        </tr>    
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <?= Html::button("<i class='fa fa-arrow-left'></i> ". Yii::t('fe', 'Kembali'),[
        'class' => 'btn bg-slate btn-sm',
        'data-dismiss' => 'modal'
    ]) ?>
</div>

<script type="text/javascript">
$(function () {
    var tableLog;
    var urlLog = "<?=$urlLog?>";
    tableLog = $("#table-log").docoTabel({
        filter: true,
        sorting: [[5, "desc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        scrollCollapse: true,
        ajax: baseUrl + "master/tarif-tindakan/get-data-log?"+urlLog,
        columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                render: (data, rowElement, rowData, rowAdditionalData) => {
                    var tableInfo = tableLog.page.info()
                    return tableInfo.start + rowAdditionalData.row + 1
                }
            },
            {
                data: "aksi", 
                searchable: false 
            },
            {
                data: "tindakan_paket", 
                orderable: false,
                searchable: false
            },
            {
                data: "keterangan", 
                orderable: false,
                searchable: false
            },
            {
                data: "harga_tariftindakan", 
                orderable: false,
                searchable: false,
                className: "text-right",
                render: $.fn.dataTable.render.number(".", ",", 0, "")
            },
            {
                data: "tgl_proses",
                render: (data) => {
                    return data == "" || data == null ? "-" : moment(data).format("DD MMM YYYY HH:mm:ss")
                }
            },
            {
                data: "nama_user", 
                searchable: false 
            },
        ],
    });
    $(".dataTables_filter").hide();
})
</script>