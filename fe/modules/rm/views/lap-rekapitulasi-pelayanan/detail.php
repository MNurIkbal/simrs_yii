<?php
    use app\components\DocoHelpers;
    use yii\helpers\ArrayHelper;
    use yii\helpers\Html;
    use yii\web\View;

    $explode = explode('/', $tgl_pendaftaran);
                
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
    <h5 class="modal-title"><?= $title; ?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-1">Instalasi</div>
        <div class="col-md-1">:</div>
        <div class="col-md-9" style="margin-left:-60px;"><?= $instalasi_nama; ?></div>
    </div>
    <div class="row">
        <div class="col-md-1">Ruangan</div>
        <div class="col-md-1">:</div>
        <div class="col-md-9" style="margin-left:-60px;"><?= $ruangan_nama; ?></div>
    </div>
    <div class="row">
        <div class="col-md-1">Tanggal</div>
        <div class="col-md-1">:</div>
        <div class="col-md-9" style="margin-left:-60px;">
         <span id="startdate"></span> s/d <span id="enddate"></span>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel-body">
                <div class="advanced-filter"></div>
                <table id="tbl-pasien" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th><?= \Yii::t("fe", "No Registrasi"); ?></th>
                            <th><?= \Yii::t("fe", "No Rekam Medik"); ?></th>
                            <th><?= \Yii::t("fe", "Nama"); ?></th>
                            <th><?= \Yii::t("fe", "Tanggal Kunjungan"); ?></th>
                            <th><?= \Yii::t("fe", "Alamat"); ?></th>
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
    var table;

    function formatDate(userDate) {
        var parts = userDate.split("/");
        if (parts[0].length == 1) parts[0] = "0" + parts[0];
        if (parts[1].length == 1) parts[1] = "0" + parts[1];
        return parts[2] +"-" + parts[0] + "-" + parts[1];
      } 
    var startdate = convertDateByFormat($("#rangeDemoStart").val(),"Y-M-d");
    var endate = convertDateByFormat($("#rangeDemoFinish").val(),"Y-M-d");
     

    $("#startdate").html($("#rangeDemoStart").val());
    $("#enddate").html($("#rangeDemoFinish").val());
    var tgl_pendaftaran = startdate.concat("/", endate);
    var status_bayar = $("#status_bayar").val()
    var status_periksa = $("#status_periksa").val()
    var params = "?pegawai_id='.$pegawai_id.'&ruangan_id='.$ruangan_id.'&instalasi_id='.$instalasi_id.'&status_bayar="+status_bayar+"&status_periksa="+status_periksa+"&tgl_pendaftaran=" + tgl_pendaftaran;

    $(document).ready(function() {
        table = $("#tbl-pasien").docoTabel({
            filter: false,
            sorting: [[0, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: baseUrl+"rm/lap-rekapitulasi-pelayanan/get-data-pasien"+params,
            columns: [
                {title: "'.(\Yii::t("fe", "No Registrasi")).'", data: "no_pendaftaran", searchable: false},
                {title: "'.(\Yii::t("fe", "No Rekam Medik")).'", data: "no_rekam_medik", searchable: false},
                {title: "'.(\Yii::t("fe", "Nama")).'", data: "nama_pasien", searchable: false},
                {title: "'.(\Yii::t("fe", "Tanggal Kunjungan")).'", data: "tgl_pendaftaran", searchable: false},
                {title: "'.(\Yii::t("fe", "Alamat")).'", data: "alamat_pasien", searchable: false, orderable: false}
            ]
        });
    });
', View::POS_END, 'detail.js');
?>
