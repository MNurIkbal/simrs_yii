<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-03-07 17:29:55
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-02-26 11:00:27
 * @Description:
 */

use yii\bootstrap\Modal;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use app\components\DocoConstants;

$this->title = Yii::t('fe', 'Informasi Pasien Rawat Jalan');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', Yii::$app->docoVars->workspace("instalasi_name")), 'url' => ['/rajal/dashboard']];
$this->params['breadcrumbs'][] = $this->title;

?>

<style>
    .my-legend .legend-title {
        text-align: left;
        margin-bottom: 8px;
        font-weight: bold;
        font-size: 90%;
    }

    .my-legend .legend-scale ul {
        margin: 0;
        padding: 0;
        float: left;
        list-style: none;
    }

    .my-legend .legend-scale ul li {
        display: block;
        float: left;
        width: 80px;
        margin-bottom: 6px;
        margin-right: 5px;
        text-align: center;
        font-size: 80%;
        list-style: none;
    }

    .my-legend ul.legend-labels li span {
        display: block;
        float: left;
        height: 15px;
        width: 80px;
        border: solid 0.2px;
    }

    .my-legend .legend-source {
        font-size: 70%;
        color: #999;
        clear: both;
    }

    .my-legend a {
        color: #777;
    }
</style>

<div class="row body">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= $this->title ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                    </div>
                </div>
                <!-- end -->
            </div>

            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'search',
                    'reset' => ['attributes' => ['data-parent' => '.filter-form']],
                    'pdf' => [
                        'title' => Yii::t('fe', 'Cetak'),
                        'attributes' => [
                            'data-target' => Url::home() . 'rajal/pemeriksaan/export-pdf?jenis=rajal&'
                        ],
                    ],
                    'rincian' => [
                        'title' => Yii::t('fe', 'Cetak Rincian'),
                        'icon' => 'fa fa-file-pdf-o',
                        'attributes' => [
                            'id' => 'cetak-rincian-tagihan',
                            'data-options' => 'link',
                            'class' => 'spa',
                            'data-target' => '/rajal/pemeriksaan/export-pdf-rincian-tagihan?pendaftaran_id=',
                            'disabled' => 'true',
                            'target' => '_blank'
                        ]
                    ],
                    'excel' => [
                        'title' => Yii::t('fe', 'Excel'),
                        'attributes' => [
                            'data-target' => Url::home() . 'rajal/pemeriksaan/export-excel?jenis=rajal&'
                        ]
                    ],
                    'periksa' => [
                        'title' => \Yii::t('fe', 'Periksa'),
                        'icon' => 'fa fa-stethoscope',
                        'attributes' => [
                            'id' => 'data-periksa',
                            'data-target' => Url::home() . 'rajal/pemeriksaan/periksa?id=',
                            'data-conditions' => 'ruanganId',
                        ]
                    ],
                    'print-gelang' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Cetak Gelang'),
                        'icon' => 'fa fa-print',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id' => 'btn-print-gelang',
                            'class' => 'btn-print-gelang',
                            'data-options' => 'click',
                            'data-target' => '/rajal/pemeriksaan/print-gelang?id=',
                            'style' => 'display:none'
                        ]
                    ],
                    // 'pulang'=>[
                    //   'title' => \Yii::t('fe', 'Pulang'),
                    //   'icon' => 'fa fa-home',
                    //   'attributes' => [
                    //         'id' => 'data-pulang',
                    //         'data-target'=> '/rajal/pemeriksaan/pemulangan-pasien?pendaftaran_id=',
                    //         'disabled'=>'true',
                    //   ]
                    // ],

                    'batal' => [
                        'title' => \Yii::t('fe', 'Batal Periksa'),
                        'icon' => 'fa fa-remove',
                        'attributes' => [
                            'id' => 'data-batal',
                            'data-options' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'data-url' => '/rajal/informasi/batal-periksa?pendaftaran_id=',
                            'disabled' => 'true',
                        ]
                    ],
                ], '#data-informasi'); ?>
            </div>

            <div class="panel-body">
                <!--filter-->
                <div class="advanced-filter">
                </div>
                <div class="col-md-12">
                    <div class='my-legend'>
                        <div class='legend-title'>Keterangan</div>
                        <div class='legend-scale'>
                            <ul class='legend-labels'>
                                <li><span style='background:#dec0f1;'></span>Pasien Pulang</li>
                                <li><span style='background:#ccff33;'></span>Sudah Isi SOAP</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php
                if ($list_cara_bayar) {
                ?>
                    <div class="col-md-12">
                        <div class='my-legend'>
                            <div class='legend-title'>Keterangan Cara Bayar</div>
                            <div class='legend-scale'>
                                <ul class='legend-labels'>
                                    <?php
                                    foreach ($list_cara_bayar as $key => $value) {
                                        echo "<li><span style='background:" . $value['carabayar_kode_warna'] . "'></span>" . $value['carabayar_nama'] . "</li>";
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
                <div class="form-group">
                    <div class="col-md-12">
                    </div>
                </div>

                <table class="table datatable-basic table-striped table-hover dataTable" id="data-informasi" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th>No</th>
                            <th><?= Yii::t('fe', 'No antrian') ?></th>
                            <th><?= Yii::t('fe', 'Tanggal pendaftaran') ?></th>
                            <th><?= Yii::t('fe', 'Asal ruangan') ?></th>
                            <th><?= Yii::t('fe', 'No pendaftaran') ?></th>
                            <th><?= Yii::t('fe', 'No RM') ?></th>
                            <th><?= Yii::t('fe', 'Nama pasien') ?></th>
                            <th><?= Yii::t('fe', 'L/P') ?></th>
                            <th><?= Yii::t('fe', 'Cara Bayar') ?></th>
                            <th><?= Yii::t('fe', 'Penjamin') ?></th>
                            <th><?= Yii::t('fe', 'Dokter') ?></th>
                            <th><?= Yii::t('fe', 'Status') ?></th>
                            <th><?= Yii::t('fe', 'Ruangan') ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <audio id="playerAudio" preload="auto" tabindex="0" controls="" type="audio/mpeg" hidden='true'></audio>
    </div>
</div>

<div class="clearfix">
    <!-- Modal -->
    <?php
    Modal::begin([
        'header' => '<h4>' . \Yii::t("fe", "Konfirmasi Dokter") . '</h4>',
        'id' => 'modal',
        'size' => 'modal-lg',
    ]);
    echo "
            <div id='modalContent'>
                <div class='row'>
                    <div class='col-md-12'>
                        " . Html::dropDownList(
        'nama_pegawai',
        '',
        ArrayHelper::map($data_pegawai, 'pegawai_id', 'nama_pegawai'),
        [
            'class' => 'form-control select2',
            'id' => 'dd-pegawai',
            'prompt' => \Yii::t('fe', '--pilih dokter--'),
        ]
    ) . "
                    </div>
                </div>
                <div class='row'>
                    <div class='col-md-12'>
                        " . Html::hiddenInput('temp-pendaftaran', null, ['id' => 'temp-pendaftaran', 'read-only' => 'read-only']) . "
                        " . Html::hiddenInput('temp-pegawai', null, ['id' => 'temp-pegawai', 'read-only' => 'read-only']) . "
                    </div>
                </div>
                <div class='row'>
                    <div class='col-md-12'>
                        " . Html::button(Yii::t('fe', 'Pilih Dokter'), ['class' => 'btn btn-primary btn-update-pegawai']) . "
                    </div>
                </div>
            </div>
        ";
    Modal::end();
    ?>
</div>
<?php
$tgl_pendaftaran = 'tgl_pendaftaran';
$this->registerJs('
    const buttonAntrianPoli    = "/rajal/informasi/confirm-periksa?pendaftaran_id=";
    const buttonDiperiksa      = "/rajal/pemeriksaan/periksa?id=";
    const buttonPulang         = "/rajal/pemeriksaan/pemulangan-pasien?pendaftaran_id=";
    const buttonRincianTagihan = "/rajal/pemeriksaan/export-pdf-rincian-tagihan?pendaftaran_id=";
    
    var status_batal_periksa  = "' . $status_batal_periksa . '";
    var status_antrian_poli   = "' . $status_antrian_poli . '";
    var status_diperiksa      = "' . $status_diperiksa . '";
    var status_periksa_pulang = "' . $status_periksa_pulang . '";
    var kelompok_medis        = "' . $kelompok_medis . '";
    var kelompokpegawai_id    = "' . $kelompokpegawai_id . '";

    var tabel;
    $(document).ready(function() {

        tabel = $("#data-informasi").docoTabel({
            filter: true,
            //add for handle checkbox
            columnDefs: [ {
                orderable: false,
                className: "select-checkbox",
                targets:   0
            }],
            select: {
                style: "os",
                selector: "tr"
            },
            sorting: [[2, "asc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            // fixedColumns: {
            //     leftColumns: 1
            // },
            ajax: baseUrl+"rajal/pemeriksaan/get-data-list-pasien",
            columns: [
                 {
                    title: "",
                    data: null,
                    defaultContent: "",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "' . (\Yii::t("fe", "Nomor Antrian")) . '", data: "no_antrian"},
                {title: "' . (\Yii::t("fe", "Tanggal Pendaftaran")) . '", data: "tgl_pendaftaran"},
                {title: "' . (\Yii::t("fe", "Pasien")) . '", data: "no_pendaftaran", searchable: false},
                {title: "' . (\Yii::t("fe", "Nomor Rekam Medik")) . '", data: "no_rekam_medik", className: "hidden"},
                {title: "' . (\Yii::t("fe", "Nama Pasien")) . '", data: "nama_pasien", className: "hidden"},
                {title: "' . (\Yii::t("fe", "Ruangan")) . '", data: "ruangan_nama"},
                {title: "' . (\Yii::t("fe", "Ruangan Asal")) . '", data: "ruanganasal_nama", searchable: false, className: "hidden"},
                {title: "' . (\Yii::t("fe", "Jenis Kelamin")) . '", data: "jenis_kelamin", className: "hidden"},
                {title: "' . (\Yii::t("fe", "Cara Bayar")) . '", data: "carabayar_nama"},
                {title: "' . (\Yii::t("fe", "Penjamin")) . '", data: "penjamin_nama", className: "hidden"},
                {title: "' . (\Yii::t("fe", "Nama Dokter")) . '", data: "nama_pegawai"},
                {title: "' . (\Yii::t("fe", "Status")) . '", data: "status_periksa1"},
                {title: "' . (\Yii::t("fe", "Lantai")) . '", data: "lantai_id", visible: false},
                {title: "' . (\Yii::t("fe", "No. Pendaftaran")) . '", data: "no_pendaftaran", name: "no_pendaftaran", searchable: true, visible: false},
            ],
            fnRowCallback: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                if(aData.status_periksa == status_periksa_pulang){
                    $("td", nRow).css("background-color", "#dec0f1");
                }
                else if(aData.is_soap == true){
                    $("td", nRow).css("background-color", "#ccff33");
                }
                var textColor = invertColor(aData.carabayar_kode_warna,aData.carabayar_kode_warna);
                $("td:eq(10)", nRow).css("background-color", aData.carabayar_kode_warna);
                $("td:eq(10)", nRow).css("color", textColor);
            },
            language: {
                lengthMenu: "' . (\Yii::t("fe", "Menampilkan")) . ' _MENU_ ' . (\Yii::t("fe", "data perhalaman")) . '",
            }
        });
        $(".dataTables_filter").hide();
        $(".filter-form").datatableBootstrapFilter(tabel, [
            [6, \'' . (preg_replace("/[\n\t\r]/i", '', Html::textInput('nama_pasien', '', ['class' => 'form-control', 'placeholder' => 'Nama Pasien']))) . '\'],
            [
                3,
                \'<div class="input-group"><input value=' . date("d-M-Y") . ' type="text" id="rangeDemoStart" class="form-control startDate" /><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input value=' . date("d-M-Y") . ' type="text" id="rangeDemoFinish" class="form-control endDate" /><input type="text" style="display:none" class="targetDate" col-index=2 readonly="true"></div>\'
            ],
            
            [15, \'' . (preg_replace("/[\n\t\r]/i", '', Html::textInput('no_pendaftaran', '', ['class' => 'form-control', 'placeholder' => 'No Pendaftaran']))) . '\'],
            [5, \'' . (preg_replace("/[\n\t\r]/i", '', Html::textInput('no_rekam_medik', '', ['class' => 'form-control', 'placeholder' => 'No Rekam Medik']))) . '\'],
            [2, \'' . (preg_replace("/[\n\t\r]/i", '', Html::textInput('no_antrian', '', ['class' => 'form-control', 'placeholder' => 'No Antrian']))) . '\'],
            [7, \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('ruangan_nama', '', ArrayHelper::map($data_ruangan, 'ruangan_nama', 'ruangan_nama'), ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', '--Pilih Ruangan--')]))) . '\'],
            [9, \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('jenis_kelamin', '', ArrayHelper::map($data_jenis_kelamin, 'jenis_kelamin', 'lookup_name'), ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', '--Pilih Jenis Kelamin--')]))) . '\'],
            [
                10,
                \'<div class="form-group">' . preg_replace('/[\n\t\r]/i', "", preg_replace(
    '/[\']/i',
    "\"",
    Html::dropDownList(
        "carabayar_nama",
        "",
        ArrayHelper::map($data_cara_bayar, "carabayar_nama", "carabayar_nama"),
        [
            "id" => "filter_carabayar",
            "class" => "form-control select2 dep-to-child",
            "style" => "width:100%;",
            "prompt" => \Yii::t("fe", "--Pilih Cara bayar--"),
            "data-url" =>  Url::home() . (Yii::$app->controller->module->id) . "/pemeriksaan/get-penjamin",
            "data-depend_id" => "filter_penjamin",
            "data-depend_prompt" => \Yii::t("fe", "--Pilih Penjamin--"),
            "data-storage" => "penjamin",
            "data-key" => "penjamin_nama",
        ]
    )
)) . '<div>\'
            ],
            [
                11,
                \'<div class="form-group">' . preg_replace('/[\n\t\r]/i', "", preg_replace(
    '/[\']/i',
    "\"",
    Html::dropDownList(
        "penjamin_nama",
        "",
        ArrayHelper::map($data_penjamin, "penjamin_nama", "penjamin_nama"),
        [
            "id" => "filter_penjamin",
            "class" => "form-control select2 dep-to-parent",
            "style" => "width:100%;",
            "prompt" => \Yii::t("fe", "--Pilih Penjamin--"),
            "data-url" =>  Url::home() . (Yii::$app->controller->module->id) . "/pemeriksaan/get-carabayar",
            "data-depend_id" => "filter_carabayar",
        ]
    )
)) . '<div>\'
            ],





            [12, \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('nama_pegawai', '', ArrayHelper::map($data_pegawai, 'nama_pegawai', 'nama_pegawai'), ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', '--Pilih Dokter--')]))) . '\'],
            [13, \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('status_periksa1', '', ArrayHelper::map($data_statusperiksa, 'status_periksa1', 'lookup_name'), ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', '--Pilih Status Periksa--')]))) . '\'],
            [14, \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('lantai_id', '', ArrayHelper::map($data_lantai, 'lantai_id', 'lantai_nama'), ['class' => 'form-control select2', 'prompt' => \Yii::t('fe', '--Pilih Lantai--')]))) . '\'],
        ],{
            6:0,
            3:1,
            13:2,
            14:3,
            5:15
        }, true);
        dateRangeHelper(".startDate",".endDate",".targetDate", true);

        $("#data-informasi tbody").on("click", "tr", function(){
            try {
                primaryKey = tabel.row(".selected").data().primary ? tabel.row(".selected").data().primary : null;
            } catch (e) {
                primaryKey = false;
            }
            $("#data-periksa").attr("data-target", "/rajal/pemeriksaan/periksa?id=");
            $("#data-periksa").removeAttr("data-toggle");
            if (primaryKey) {
                $("#cetak-rincian-tagihan").attr("data-target",buttonRincianTagihan+primaryKey);
                var status = tabel.row(".selected").data().status_periksa;
                if(kelompokpegawai_id == kelompok_medis){
                    if(status == status_diperiksa){
                        enabledCetakRincianTagihan();
                        enabledBatal();
                    }else if(status == status_antrian_poli) {
                        enabledBatal();
                        disabledCetakRincianTagihan();
                        disabledPulang();
                        enabledPeriksa();

                        $("#data-periksa").attr("data-target","#modal_backdrop");
                        $("#data-periksa").attr("data-toggle","modal");
                        $("#data-periksa").attr("action",buttonAntrianPoli+primaryKey+ "&ruangan_id=" + tabel.row(".selected").data().ruangan_id );
                    }
                    else if(status == status_batal_periksa) {
                        $("#data-periksa").prop("disabled", true);
                        $("#data-batal").prop("disabled", true);
                        disabledCetakRincianTagihan();
                    }
                    else{
                        allDisabled();
                        $("#data-periksa").attr("disabled", true);
                    }
                }else{
                    if(status == status_antrian_poli){
                        enabledBatal();
                        disabledCetakRincianTagihan();
                        disabledPulang();
                        enabledPeriksa();

                        $("#data-periksa").attr("data-target","#modal_backdrop");
                        $("#data-periksa").attr("data-toggle","modal");
                        $("#data-periksa").attr("action",buttonAntrianPoli+primaryKey+ "&ruangan_id=" + tabel.row(".selected").data().ruangan_id );

                    }else if(status == status_diperiksa){ 
                        enabledBatal();

                    }else if(status == status_batal_periksa) {
                        $("#data-periksa").prop("disabled", true);
                        $("#data-batal").prop("disabled", true);
                    }
                    else{  
                        allDisabled();
                    }

                }
            } else {
                allDisabled();
            }

        });

        var _afterSave = function (bool) {
            tabel.reload();
        }

        $(document).on("click",".data-delete", function(event) {
            event.preventDefault();
            $(this).docoForm("delete",{
                success : function (data) {
                    _afterSave()
                }
            });
        });

        $(document).on("click",".data-aktifasi", function(event) {
            $(this).docoForm("delete",{
                success : function (data) {
                    _afterSave()
                }
            });
        });

        // Tooltip
        $("table").tooltip({selector: "[data-tooltip=tooltip]"});

        // Modal
        $(document).on("click", ".antrian", function(event) {
            // Pendaftaran id
            // var id = $("#temp-pendaftaran").val($(this).data("id"));
            const id_pendaftaran = $(this).attr("data-id");
            const no_antrian = $(this).attr("data-antrian");
            const nama_pasien = $(this).attr("data-namapasien");
            const ruangan_id = $(this).attr("data-ruanganid");
            const pegawai_id = $(this).attr("data-pegawaiid");
            const dataPost = {
                no_antrian: no_antrian,
                nama_pasien: nama_pasien,
                ruangan_id: ruangan_id,
                pegawai_id: pegawai_id
            };
            $.ajax({
                url: "/rajal/pemeriksaan/panggil-antrian",
                data: dataPost,
                type: "post",
                success: function (res) {
                    if (typeof res.teks_panggil !== "undefined") {
                        let text = res.teks_panggil;
                        let player = $("#playerAudio");
                        let arrayText = text.split(" ");
                        arrayText.push("stop");
                        arrayText = arrayText.filter(Boolean);

                        let index = 0;

                        player[0].defaultPlaybackRate = 1;
                        player[0].src = `${window.location.origin}/media/sounds/${arrayText[index]}.wav`;
                        player[0].play();

                        player[0].addEventListener("ended", function () {
                            index = index + 1;

                            if (index < arrayText.length) {
                                player[0].defaultPlaybackRate = index == arrayText.length - 3 ? 1.5 : 1.2;
                                if (arrayText[index] == "stop") {
                                    // hapusAntrianAudio();
                                } else {
                                    player[0].src = window.location.origin + "/media/sounds/" + arrayText[index] + ".wav";
                                    player[0].play();
                                }
                            }
                        });
                    }
                }
            });

        });

        // When pegawai dropdown changed
        $(document).on("change", "#dd-pegawai", function(event) {
            $("#temp-pegawai").val($(this).val());
        });

        $(document).on("click", ".periksa, .view", function(){
            if($(this).data("type") == "' . DocoConstants::STATUS_PERIKSA . '" ){
                window.location = $(this).data("action");
            }

            if($(this).data("type") == "' . DocoConstants::STATUS_PULANG . '-' . DocoConstants::STATUS_RUJUK_RAWAT_INAP . '" ){
                window.location = $(this).data("action");
            }
        })

        // When button update pegawai clicked
        $(document).on("click", ".btn-update-pegawai", function(e) {
            // Prevent default
            e.preventDefault();

            // Assign pemeriksaan pegawai
            let pendaftaranId = $("#temp-pendaftaran").val();
            let pegawaiId = $("#temp-pegawai").val();

            // Confirmation
            let confirmation = confirm("' . Yii::t("fe", "Dokter tidak bisa diubah kembali, apakah Anda yakin?") . '");

            // Check confirm
            if(confirm) {
                // Ajax
                $.ajax({
                    url: baseUrl + "' . (Yii::$app->controller->module->id) . '/pemeriksaan/update-pegawai",
                    type: "POST",
                    data: {
                        pendaftaranId: pendaftaranId,
                        pegawaiId: pegawaiId
                    },
                    success: function(data) {
                        // console.log(data)
                    }
                });
            }
        });
    });

    function getPeriksaTagihanPulang(primaryKey){
        enabledCetakRincianTagihan();
        enabledPulang();
        enabledPeriksa();
        // disabledBatal();
        $("#data-periksa").attr("data-target",buttonDiperiksa);

        $("#data-pulang").attr("data-target","#modal_backdrop");
        $("#data-pulang").attr("data-toggle","modal");
        $("#data-pulang").attr("action",buttonPulang+primaryKey);

        $("#cetak-rincian-tagihan").attr("data-target",buttonRincianTagihan+primaryKey);

        $.ajax({
            url: "/rajal/pemeriksaan/check-transaction",
            data: { pendaftaran_id : primaryKey },
            type: "get",
            success: function (res) {
                if (res.status == 200) {
                   enabledBatal();
                }else{
                    disabledBatal();
                }
            }
        });
    }


    function allDisabled(){
        $("#data-pulang").attr("disabled", true);
        $("#data-batal").attr("disabled", true);
        $("#cetak-rincian-tagihan").attr("disabled", true);
        // $("#data-periksa").attr("disabled", true);
    }

    function allEnabled(){
        $("#data-pulang").attr("disabled", false);
        $("#data-batal").attr("disabled", false);
        $("#cetak-rincian-tagihan").attr("disabled", false);
        $("#data-periksa").attr("disabled", false);
    }

    function enabledPulang(){
        $("#data-pulang").attr("disabled", false);
    }

    function disabledPulang(){
        $("#data-pulang").attr("disabled", true);
    }

    function enabledBatal(){
        $("#data-batal").attr("disabled", false);
    }

    function disabledBatal(){
        $("#data-batal").attr("disabled", true);
    }

    function enabledCetakRincianTagihan(){
        $("#cetak-rincian-tagihan").attr("disabled", false);
    }

    function disabledCetakRincianTagihan(){
        $("#cetak-rincian-tagihan").attr("disabled", true);
    }


    function enabledPeriksa(){
        $("#data-periksa").attr("disabled", false);
    }

    function disabledPeriksa(){
        $("#data-periksa").attr("disabled", true);
    }

', View::POS_END, 'b-index');

$this->registerJs($this->render('js/_informasi.js'), View::POS_END);
?>
