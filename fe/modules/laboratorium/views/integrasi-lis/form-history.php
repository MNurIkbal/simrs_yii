<?php

/**
 * @author Aris Munandar
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\DatePicker;
?>

<style>
    .modal-dialog{
        width:  90%; 
    }
</style>
<!-- Modal header -->
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Perubahan Hasil Pemeriksaan</h5>
</div>

<!-- Modal body -->
<div class="modal-body">
    <div class="col-md-12">
        <div class="panel panel-default panel-bordered">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <input type="hidden" id="pasienmasukpenunjang_id" value="<?=$pasienmasukpenunjang_id?>"></input>
                        <input type="hidden" id="no_masukpenunjang" value="<?=$no_masukpenunjang?>"></input>
                        <table id="tb-history" class="table table-striped table-condensed table-hover" style="width:100%">
                            <thead>
                                <tr class="bg-inverse">
                                    <th><?= Yii::t('fe', 'No') ?></th>
                                    <th><?= Yii::t("fe", "Tanggal Perubahan") ?></th>
                                    <th><?= Yii::t("fe", "Hasil Sebelumnya") ?></th>
                                    <th><?= Yii::t("fe", "Hasil Update") ?></th>
                                    <th><?= Yii::t("fe", "Dirubah Oleh") ?></th>
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

<!-- Modal footer -->
<div class="modal-footer">
    <?= Html::button("<i class='fa fa-arrow-left'></i> ". Yii::t('fe', 'Kembali'),[
        'class' => 'btn bg-slate btn-sm',
        'data-dismiss' => 'modal'
    ]) ?>
</div>

<!-- Javascript -->
<script type="text/javascript">
$(document).ready(function() {
    let id = $('#pasienmasukpenunjang_id').val();
    let no = $('#no_masukpenunjang').val();
    console.log(id)
    tablePenunajang = $("#tb-history").docoTabel({
        select: {
            style: "os",
            selector: "tr"
        },
        filter: true,
        sorting: [[1, "asc"]],
        displayLength: 10,
        lengthChange: false,
        processing: true,
        serverSide: true,
        scrollX: true,
        bPaginate: false,
        ajax: baseUrl + "laboratorium/informasi-pasien-lab-wynacom/get-data-history?id="+id+"&no="+no,
        columns: [
            {
                title: "No", 
                data: "rowNum", 
                searchable: false, 
                orderable: false
            },
            {
                title: "Tanggal Perubahan", 
                data: "tgl_pemeriksaan", 
                searchable: false 
            },
            {
                title: "Hasil Sebelumnya", 
                data: "prev_hasil", 
                searchable: false 
            },
            {
                title: "Hasil Update", 
                data: "cur_hasil", 
                searchable: false
            },
            {
                title: "Dirubah Oleh", 
                data: "authorization_user", 
                searchable: false 
            },
        ],
        scrollCollapse: true,
        language: {
            emptyTable: emptyTable,
            info: info,
            infoEmpty: infoEmpty,
            infoFiltered: infoFiltered,
            lengthMenu: lengthMenu,
            loadingRecords: loadingRecords,
            processing: processing,
            search: search,
            zeroRecords: zeroRecords,
            paginate: {
                first: first,
                last: last,
                next: next,
                previous: previous
            },
            aria: {
                sortAscending: sortAscending,
                sortDescending: sortDescending
            }
        }
    });

    // Hide datatables filter form
    $(".dataTables_filter").hide();
})

</script>