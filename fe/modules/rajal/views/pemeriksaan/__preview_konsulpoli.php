<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-03-05 11:00:08
 * @Last Modified by:   afil
 * @Last Modified time: 2018-03-05 15:22:11
 * @Description:
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use kartik\widgets\DepDrop;
use app\components\DocoHelpers;
?>

<!-- form start -->
<div class="panel panel-flat">
    <div class="panel-body">
        <div class="row">
            <div class="panel-body">
                <table class="table datatable-basic table-striped table-hover dataTable no-footer table-framed tabel-konsul-poli-view">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="5%">No</th>
                            <th><?=Yii::t('fe', 'Tanggal')?></th>
                            <th><?=Yii::t('fe', 'Dikonsul Dari')?></th>
                            <th><?=Yii::t('fe', 'Sudah Konsul')?></th>
                            <th><?=Yii::t('fe', 'Dokter Konsul')?></th>
                            <th><?=Yii::t('fe', 'Jawaban Dokter Konsul')?></th>
                            <th><?= Yii::t('fe', 'Aksi') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- form end -->

<?php
$this->registerJs('
    var tabel_konsul_poli = $(".tabel-konsul-poli-view").docoTabel({
        filter: true,
        sorting: [[1, "asc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        searching: false,
        ajax: "/rajal/pemeriksaan/get-data-konsul-poli?id=' . $pendaftaran_id . '&preview_only='.$preview_only.'",
        columns:[
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {title: "' . (\Yii::t("fe", "Tanggal")) . '", data: "tgl_poli"},
            {title: "' . (\Yii::t("fe", "Dikonsul Dari")) . '", data: "ruangan_asal"},
            {title: "' . (\Yii::t("fe", "Sudah Konsul")) . '", data: "tgl_selesaikonsul"},
            {title: "' . (\Yii::t("fe", "Dokter Konsul")) . '", data: "nama_dokter"},
            {title: "' . (\Yii::t("fe", "Jawaban Dokter Konsul")) . '", data: "jawaban_konsul"},
            {title: "' . (\Yii::t("fe", "Aksi")) . '", data: "action"},
        ]
    });

    function cetakKonsul(konsulId) {
        window.open(`/rajal/inf-konsul-poli/export-pdf-konsul?id=${konsulId}`)
    }

    function cetakJawabanKonsul(konsulId) {
        window.open(`/rajal/inf-konsul-poli/export-pdf-konsul?id=${konsulId}&jenis=jawaban-konsul`)
    }

    function cetakRencanaKontrol(rencanaKontrolId) {
        window.open(`/pendaftaran/rencana-kontrol-inap/print-rencana?rencanakontrol_id=${rencanaKontrolId}`);
    }
');
?>
