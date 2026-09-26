<?php
    use app\components\DocoHelpers;
    use yii\helpers\ArrayHelper;
    use yii\helpers\Html;
    use yii\web\View;

    $explode = explode(' - ', $tgl_pulang);
                
    if (count($explode) == 2) {
        $start = date('Y-m-d', strtotime($explode[0])).' 00:00:00';
        $end = date('Y-m-d', strtotime($explode[1])).' 23:59:59';
    } else {
        $start = date('Y-m-d 00:00:01');
        $end = date('Y-m-d 23:59:59');
    }
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Detail <?= $title; ?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-2">Instalasi</div>
        <div class="col-md-1">:</div>
        <div class="col-md-9" style="margin-left:-60px;"><?= $title; ?></div>
    </div>
    <div class="row">
        <div class="col-md-2">Tanggal Pulang</div>
        <div class="col-md-1">:</div>
        <div class="col-md-9" style="margin-left:-60px;"><?= DocoHelpers::convDateTime($start, false, false).' - '.DocoHelpers::convDateTime($end, false, false); ?></div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel-body">
                <div class="advanced-filter"></div>
                <table id="tbl-detail" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th><?= \Yii::t("fe", "No Registrasi"); ?></th>
                            <th><?= \Yii::t("fe", "No Rekam Medik"); ?></th>
                            <th><?= \Yii::t("fe", "Nama"); ?></th>
                            <th><?= \Yii::t("fe", "Tanggal Kunjungan"); ?></th>
                            <th><?= \Yii::t("fe", "Alamat"); ?></th>
                            <th><?= \Yii::t("fe", "Ruangan"); ?></th>
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

<?php
$this->registerJs('
    var table_detail;
    var params = "?instalasi_id='.$instalasi_id.'&tgl_pulang='.$tgl_pulang.'";

    $(document).ready(function() {
        table_detail = $("#tbl-detail").docoTabel({
            filter: false,
            sorting: [[0, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"rm/lap-pasien-rujuk-ranap/get-data-detail"+params,
            columns: [
                {title: "'.(\Yii::t("fe", "No Registrasi")).'", data: "no_pendaftaran", searchable: false},
                {title: "'.(\Yii::t("fe", "No Rekam Medik")).'", data: "no_rekam_medik", searchable: false},
                {title: "'.(\Yii::t("fe", "Nama")).'", data: "nama_pasien", searchable: false},
                {title: "'.(\Yii::t("fe", "Tanggal Kunjungan")).'", data: "tgl_kunjungan", searchable: false},
                {title: "'.(\Yii::t("fe", "Alamat")).'", data: "alamat_pasien", searchable: false, orderable: false},
                {title: "'.(\Yii::t("fe", "Ruangan")).'", data: "ruangan_nama", searchable: false, orderable: false},
            ]
        });
    });
', View::POS_END, 'detail.js');
?>
