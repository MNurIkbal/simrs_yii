<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\components\DocoHelpers;

?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<hr>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12 filter-form-pegawai"></div>
        <div class="col-md-12" style="margin-top:-15px;">
            <button type="button" class="btn btn-info btn-labeled btn-xs data-filter" data-parent=".filter-form-pegawai"><b><i class="fa fa-search"></i></b>Cari</button>
        </div>
    </div>
    <br>
    <table id="data-pegawai" class="table table-striped table-condensed table-hover" style="width:100%">
        <thead>
            <tr class="bg-inverse">
                <th><?= Yii::t('fe', 'No') ?></th>
                <th width="1"><?= Yii::t('fe', 'NIK Pegawai') ?></th>
                <th><?= Yii::t('fe', 'Nama Pegawai') ?></th>
                <th><?= Yii::t('fe', 'Tanggal lahir') ?></th>
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
        tablePegawai = $("#data-pegawai").docoTabel({
            filter: true,
            sorting: [[1, "asc"]], 
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"dcms/login-pemakai/get-pegawai",
            columns: [
                {
                    data: "rowNum",
                    name : "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    data: "nomorindukpegawai",
                    title : "<?= Yii::t('fe', 'NIK Pegawai') ?>" ,
                    name: "nomorindukpegawai"
                },
                {
                    data: "nama_pegawai",
                    title : "<?= Yii::t('fe', 'Nama Pegawai') ?>",
                    name: "nama_pegawai"
                },
                {
                    data: "tgl_lahirpegawai", 
                    title : "<?= Yii::t('fe', 'Tanggal lahir') ?>", 
                    name: "tgl_lahirpegawai",
                    searchable: false,
                },
                {
                    data: "alamat_pegawai", 
                    title : "<?= Yii::t('fe', 'Alamat Pegawai') ?>",  
                    name: "alamat_pegawai"
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
        $("#data-pegawai_filter").hide();
        $(".filter-form-pegawai").datatableBootstrapFilter(tablePegawai);
    });
</script>

