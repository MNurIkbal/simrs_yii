<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;


?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<hr>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12 filter-form-pegawai"></div>
    </div>
    <br>
    <table id="data-user" class="table table-striped table-condensed table-hover" style="width:100%">
        <thead>
            <tr class="bg-inverse">
                <th><?= Yii::t('fe', 'No') ?></th>
                <th><?= Yii::t('fe', 'Nama pemakai') ?></th>
                <th width="1"><?= Yii::t('fe', 'NIK Pegawai') ?></th>
                <th><?= Yii::t('fe', 'Nama Pegawai') ?></th>
                <th><?= Yii::t('fe', 'Alamat Pegawai') ?></th>
                <th width="1">Aksi</th>
            </tr>
        </thead>
        <tbody> 
            <tr>
                <td class="text-center" colspan="9"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
            </tr>
        </tbody>
    </table>
</div>

<script type="text/javascript">
    // Global Var
    var tablePegawai;

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        tablePegawai.draw();
    });

    // Event Delete
    $(document).on("click", ".data-delete", function(e) {
        e.preventDefault();
        $(this).docoForm("delete",{
            success : function (data) {
                tablePegawai.draw()
            }
        });
        return false;
    });

    // Event Ready
    $(document).ready(function() {
        // Generate Table
        tablePegawai = $("#data-user").docoTabel({
            filter: true,
            sorting: [[1, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            stateSave: true,
            ajax: baseUrl+"dcms/akses-pengguna/get-user",
            columns: [
                {
                    data: "rowNum",
                    name : "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    data: "nama_pemakai",
                    title : "<?= Yii::t('fe', 'Nama pemakai') ?>" , 
                    name: "nama_pemakai"
                },
                {
                    data: "nomorindukpegawai",
                    title : "<?= Yii::t('fe', 'NIK Pegawai') ?>" , 
                    name: "pegawai_m.nomorindukpegawai"
                },
                {
                    data: "nama_pegawai", 
                    title : "<?= Yii::t('fe', 'Nama Pegawai') ?>", 
                    name: "pegawai_m.nama_pegawai"
                },
                {
                    data: "alamat_pegawai", 
                    title : "<?= Yii::t('fe', 'Alamat Pegawai') ?>",  
                    name: "pegawai_m.alamat_pegawai"
                },
                {
                    data: "aksi",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                }
            ],
            scrollCollapse: false,
        });
        $("#data-user_filter").hide();
        $(".filter-form-pegawai").datatableBootstrapFilter(tablePegawai);
    });
</script>

