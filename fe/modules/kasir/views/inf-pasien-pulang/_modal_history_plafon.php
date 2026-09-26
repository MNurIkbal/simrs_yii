<?php
/**
 * @author Budi
 * Powered by Sirs
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
?>

<div class="modal-header bg-inverse">
	<button type="button" class="close" data-dismiss="modal">&times;</button>
	<h5 class="modal-title"><?= $title ?></h5>
</div>

<div class="modal-body">
	<div class="col-md-12">
        <div class="panel-toolbar clearfix">
            <div class="btn-group pull-left">
            </div>
        </div>
        <br>
		<div class="row">
            <div class="col-md-12">
                <table class="table datatable-basic table-hover dataTable no-footer" id="table-history" style="width: 100%;margin-bottom:20px;">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1">No</th>
                            <th><?=Yii::t('fe', 'Waktu'); ?></th>
                            <th><?=Yii::t('fe', 'petugas'); ?></th>
                            <th><?=Yii::t('fe', 'Nominal Plafon Awal'); ?></th>
                            <th><?=Yii::t('fe', 'Nominal Plafon Baru'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="5"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                        </tr>
                    </tbody>
                </table>
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
    var tableHistory
    var pendaftaranId = "<?= $pendaftaranId ?>";
    $(document).ready(function(){
        tableHistory = $("#table-history").docoTabel({
            filter: false,
            sorting: [[1, "desc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            paging: false,
            info: false,
            ajax: {
            url: "/kasir/inf-pasien-pulang/get-data-history-plafon?pendaftaran_id=" + pendaftaranId,
            },
            columns: [
            {
                data: null,
                searchable: false,
                orderable: false,
                render: (data, rowElement, rowData, rowAdditionalData) => {
                var tableInfo = table.page.info();
                return tableInfo.start + rowAdditionalData.row + 1;
                },
            },
            {
                title: "Waktu",
                data: "created_date",
                render: (data) => {
                    return data == null ? '-' : moment(data).format('DD/MM/YYYY H:mm')
                },
            },
            {
                title: "Petugas",
                data: "nama_pegawai",
                render: (data) => {
                return data == "" || data == null ? "-" : data;
                },
            },
            {
                title: "Nominal Plafon Awal",
                data: "plafon_lama",
                searchable: false,
                orderable: false,
                className: "text-right",
                render: $.fn.dataTable.render.number(".", ",", 0, ""),
            },
            {
                title: "Nominal Plafon Baru",
                data: "plafon_baru",
                searchable: false,
                orderable: false,
                className: "text-right",
                render: $.fn.dataTable.render.number(".", ",", 0, ""),
            },
            ],
        });
    })
</script>
