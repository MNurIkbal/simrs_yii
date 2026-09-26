<?php

/**
 * @Author: rizal
 * @Date:   2018-05-28 15:47:03
 * @Description:
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = $title;
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><?= $title; ?></h4>
</div>
<div class="modal-body">
    <div class="panel panel-white">
        <div class="panel-toolbar clearfix">
            <?=DocoHelpers::generateToolbar([
                'search',
            ]);?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 filter-form">
        </div>
    </div>

    <table id="list-pemesanan-kamar" class="table table-striped table-condensed table-hover" style="width:100%">
        <thead>
            <tr class="bg-inverse">
                <th width="20">No</th>
                <th><?=\Yii::t("fe", "Tanggal pemesanan");?></th>
                <th><?=\Yii::t("fe", "No pemesanan");?></th>
                <th><?=\Yii::t("fe", "Pemesan");?></th>
                <th><?=\Yii::t("fe", "No rekam medik");?></th>
                <th><?=\Yii::t("fe", "Nama pasien");?></th>
                <th><?=\Yii::t("fe", "No telepon pasien");?></th>
                <th><?=\Yii::t("fe", "Ruangan");?></th>
                <th><?=\Yii::t("fe", "Kamar");?></th>
                <th><?=\Yii::t("fe", "Kelas");?></th>
                <th><?=\Yii::t("fe", "Status konfirmasi");?></th>
                <th><?=\Yii::t("fe", "Aksi");?></th>
            </tr>
        </thead>
        <tbody>
            <?php /* list data */ ?>
        </tbody>
    </table>
</div>

<?php
$this->registerJs('
    // Event Ready
    $(document).ready(function() {
        $(function(){
            // $(".pickadate").pickadate({
            //     format: "dd mmm yyyy",
            //     formatSubmit: "yyyy-mm-dd",
            // });
            $(".daterange").daterangepicker({
                applyClass: "bg-slate-600",
                cancelClass: "btn-default",
                locale: {
                    format: "DD MMM YYYY"
                }
            });
        })

        // Generate Table
        table = $("#list-pemesanan-kamar").docoTabel({
            filter: true,
            sorting: [[1, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            stateSave: false,
            scrollX: true,
            ajax: baseUrl+"pendaftaran/daftar/get-data-bookingkamar",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "'.(\Yii::t("fe", "Tanggal Pemesanan")).'", data: "tgl_pesan", searchable: false},
                {title: "'.(\Yii::t("fe", "No Pemesanan")).'",  data: "no_pemesanan"},
                {title: "'.(\Yii::t("fe", "Pemesan")).'",  data: "nama_pemesan"},
                {title: "'.(\Yii::t("fe", "No Rekam Medik")).'", data: "no_rekam_medik", searchable: false},
                {title: "'.(\Yii::t("fe", "Nama Pasien")).'", data: "nama_pasien"},
                {title: "'.(\Yii::t("fe", "No Hp/Tlp Pasien")).'", data: "no_telepon_pasien", searchable: false},
                {title: "'.(\Yii::t("fe", "Ruangan")).'", data: "ruangan_nama", searchable: false},
                {title: "'.(\Yii::t("fe", "Kamar")).'", data: "kamarruangan_nokamar", searchable: false},
                {title: "'.(\Yii::t("fe", "Kelas")).'", data: "kelaspelayanan_nama", searchable: false},
                {title: "'.(\Yii::t("fe", "Status Konfirmasi")).'", data: "status_booking", searchable: false},
                {title: "'.(\Yii::t("fe", "Aksi")).'", data: "aksi", searchable: false, orderable: false, class: "text-center"},
            ],
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(table, []);
    });


    // set for pendaftaran ranap
    $(document).on("click", ".pilih-bookingkamar", function() {
        $("#bookingkamar_no").val($(this).data("bookingkamar_no"));
        $("#bookingkamar_id").val($(this).data("bookingkamar_id")).trigger("change");
        $("#jeniskasuspenyakit_id").val($(this).data("jeniskasuspenyakit_id")).trigger("change");
        $("#kelaspelayanan_id").val($(this).data("kelaspelayanan_id")).trigger("change").trigger("depdrop:change");
        $("#temp_ruangan_id").val($(this).data("ruangan_id"));
        $("#kamarruangan_id").val($(this).data("kamarruangan_id"));
        $("#kamartempattidur_id").val($(this).data("kamartempattidur_id"));
        $("#nokamar").val($(this).data("kamarruangan_nokamar"));


        $(".close").trigger("click");
    });

');

?>

