<?php
// use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use app\components\DocoConstants;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;

$this->title = Yii::t('fe', 'Verifikasi Data');
?>
<style type="text/css">
    .modal-dialog {
        width: 65% !important;
        margin: 30px auto;
    }

    button#button-back {
        height: 30px;
        padding-top: 5px;
    }

    #file-upload {
        display: none;
        margin: 10px;
    }

    .inputfile+label {
        max-width: 100%;
        font-size: 1.25rem;
        /* 20px */
        font-weight: 700;
        text-overflow: ellipsis;
        white-space: nowrap;
        cursor: pointer;
        display: inline-block;
        overflow: hidden;
        /* padding: 0.625rem 1.25rem; */
        padding: 7px 25px;
        width: auto;
        /* 10px 20px */
    }

    .no-js .inputfile+label {
        display: none;
    }

    .inputfile:focus+label,
    .inputfile.has-focus+label {
        outline: 1px dotted #000;
        outline: -webkit-focus-ring-color auto 5px;
    }

    .inputfile+label * {
        /* pointer-events: none; */
        /* in case of FastClick lib use */
    }

    .inputfile+label svg {
        width: 1em;
        height: 1em;
        vertical-align: middle;
        fill: currentColor;
        margin-top: -0.25em;
        /* 4px */
        margin-right: 0.25em;
        /* 4px */
    }


    .inputfile-1+label {
        color: #f1e5e6;
        background-color: #54be8b;

    }

    .inputfile-1:focus+label,
    .inputfile-1.has-focus+label,
    .inputfile-1+label:hover {
        background-color: #078448;
    }

    .lurus {
        float: left;
        margin-left: 20px;
    }

    .highlight {
        background-color: #FF9;
    }

    /*td:last-child {  
      background-color: #FF9;    
    }*/
    .dataTables_wrapper {
        position: relative;
        clear: both;
        padding-top: 20px;
    }

    .format-upload {
        font-size: 12px;
        text-align: left;
        width: 100%;
        font-style: italic;
    }

    .my-legend .legend-title {
        margin-bottom: 8px;
        font-weight: bold;
        font-size: 11px;
    }

    .my-legend .legend-scale ul {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .my-legend .legend-scale ul li {
        display: block;
        float: left;
        margin-bottom: 6px;
        margin-right: 5px;
        text-align: center;
        font-size: 10px;
        list-style: none;
    }

    .my-legend ul.legend-labels li span {
        display: block;
        float: left;
        border: 1px solid #616161;
        padding: 4px 10px;
        color: #191919;
    }

    .my-legend .legend-source {
        font-size: 70%;
        color: #999;
        clear: both;
    }

    .my-legend a {
        color: #777;
    }

    #label-file,
    .fa {
        color: #fff !important;
    }

    #verifikasi-klaim-table tbody tr td {
        color: #000 !important;
    }
</style>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $this->title; ?></h5>
</div>
<div class="modal-body">
    <div class="panel">
        <div class="panel-toolbar clearfix">
            <?=
            DocoHelpers::generateToolbar([
                "back" => [
                    'attributes' => [
                        'id' => 'btn-back',
                    ]
                ],
                'save' => [
                    'title' => \Yii::t('fe', 'Simpan'),
                    'attributes' => [
                        'id' => 'save-data',
                        'disabled' => true
                    ],
                ],
            ], "#verifikasi-klaim-table");
            ?>
        </div>
    </div>
    <?php $form = ActiveForm::begin([
        'id' => 'validasi-klaim-form',
        'enableAjaxValidation' => false,
        'enableClientValidation' => false,
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => [
            'labelSpan' => 4,
            'deviceSize' => ActiveForm::SIZE_MEDIUM
        ],
        'options' => [
            'enctype' => 'multipart/form-data',
            'skip-confirm' => "true"
        ]
    ]);
    ?>
    <div class="clear"><br></div>
    <div class="form-group">
        <div class="col-md-2">
            <label class="control-label text-black l-label" style="padding-left:0px;"><?= Yii::t('fe', 'Pilih File'); ?></label>
        </div>
        <div class="col-md-6">
            <input type="file" name="VerifikasiKlaimForm[upload_file]" id="file-upload" class="form-control inputfile inputfile-1" data-multiple-caption="{count} files selected">
            <label for="file-upload">
                <i class="fa fa-upload"></i>
                <span id="label-file">Pilih Berkas</span>
            </label>
            <p class="format-upload">format :xls, xlsx</p>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-12">
            <div class="progress">
                <div class="progress-bar progress-bar-success myprogress" role="progressbar" style="width:0%">0%</div>
            </div>
            <div class="msg"></div>
        </div>
    </div>
    <div class="row">
        <div class="row">
            <div class="col-md-12 filter-form"></div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 filter-form"></div>
    </div>
    <table id="verifikasi-klaim-table" class="table table-striped table-condensed table-hover" style="width:100%;padding-top: 10px;">
        <thead>
            <tr class="bg-inverse">
                <th width="1" rowspan="2">No</th>
                <th rowspan="2"><?= \Yii::t("fe", "Nomor Sep"); ?></th>
                <th align="right" rowspan="2"><?= \Yii::t("fe", "Tanggal Verifikasi"); ?></th>
                <th colspan="3" class="text-center">Biaya</th>
            </tr>
            <tr class="bg-inverse">
                <th align="right"><?= \Yii::t("fe", "Rill RS"); ?></th>
                <th align="right"><?= \Yii::t("fe", "Diajukan"); ?></th>
                <th align="right"><?= \Yii::t("fe", "Disetujui"); ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" colspan="6"><?= \Yii::t("fe", "Data tidak ditemukan."); ?></td>
            </tr>
        </tbody>
    </table>
    <div class="row">
        <div class="col-md-12">
            <div class='my-legend'>
                <div class='legend-title'>Keterangan</div>
                <div class='legend-scale'>
                    <ul class='legend-labels'>
                        <li><span style='background:#fde4a8;'>Data Tidak Lengkap</span></li>
                        <li><span style='background:#ffcccc;'>Nomor SEP Tidak ditemukan di sistem</span></li>
                        <li><span style='background:#f7bf96;'>Nomor SEP digunakan oleh lebih dari 1 User</span></li>
                        <li><span style='background:#aeffef;'>Data lengkap dan siap untuk disimpan</span></li>

                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<script type="text/javascript">
    $("#btn-back").on("click", function(event) {
        $("#modal_backdrop").modal('toggle');
    });

    $(document).ready(function() {
        var verifikasiData = [];

        _tableVerifikasi = $('#verifikasi-klaim-table').DataTable({
            data: {},
            columns: [{
                    title: "No",
                    data: "nomor",
                    orderable: false
                },
                {
                    title: "No Sep",
                    data: "no_sep",
                },
                {
                    title: "Tanggal Verifikasi",
                    data: "tgl_verifikasi",
                    class: "text-right",
                },
                {
                    title: "Rill RS",
                    data: "rill_rs",
                    class: "text-right",
                },
                {
                    title: "Diajukan",
                    data: "diajukan",
                    class: "text-right",
                    // searchable: false
                },
                {
                    title: "Disetujui",
                    data: "disetujui",
                    class: "text-right",
                    // searchable: false
                },
            ],
            "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                if (aData.status == 'incomplete') {
                    $(nRow).css("background", "#fde4a8");
                } else if (aData.status == 'exist') {
                    $(nRow).css("background-color", "#aeffef");
                } else if (aData.status == 'duplikat') {
                    $(nRow).css("background-color", "#f7bf96");
                } else {
                    $(nRow).css("background-color", "#ffcccc");
                }
            }
        });

        $("#file-upload").change(function(event) {
            event.preventDefault();
            let button = this;
            let data = new FormData();
            let dataPost = $("#form-upload").serializeArray();
            let getLink = $('.lihat_file').attr('href');
            data.append("VerifikasiKlaimForm[upload_file]", $("#file-upload")[0].files[0]);
            $('.myprogress').css('width', '0');
            $('.msg').text('');
            $.ajax({
                type: "post",
                dataType: false, // what to expect back from the PHP script, if anything
                cache: false,
                contentType: false,
                processData: false,
                url: "/penjamin-asuransi/laporan-perbandingan-klaim/proses-upload",
                data: data,
                beforeSend: function(request, res) {},
                xhr: function(res) {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = evt.loaded / evt.total;
                            percentComplete = parseInt(percentComplete * 100);
                            $('.myprogress').text(percentComplete + '%');
                            $('.myprogress').css('width', percentComplete + '%');
                        }
                    }, false);
                    return xhr;
                },
                success: function(res) {
                    $("#file-upload").val('');
                    $('.msg').text(res.response.file);
                    $("#file-upload").removeAttr("disabled");
                    var data = res.response.data;
                    if (res.response.status == 422) {
                        failedProgressBar();
                        docoNotification("warning", i18next.t("Proses Gagal"), i18next.t("File gagal di upload, Nomor Sep Tidak boleh kosong."));
                        tableBlank();
                    } else {
                        if (res.response.verifikasi.length > 0) {
                            verifikasiData = res.response.verifikasi;
                            $('#save-data').attr('disabled', false);

                        }
                        _tableVerifikasi.destroy();
                        _tableVerifikasi = $('#verifikasi-klaim-table').DataTable({
                            data: data,
                            columns: [{
                                    title: "No",
                                    data: "nomor",
                                    orderable: false
                                },
                                {
                                    title: "No SEP",
                                    data: "no_sep",
                                },
                                {
                                    title: "Tanggal Verifikasi",
                                    data: "tgl_verifikasi",
                                    class: "text-right",
                                },
                                {
                                    title: "Rill RS",
                                    data: "rill_rs",
                                    class: "text-right",
                                },
                                {
                                    title: "Diajukan",
                                    data: "diajukan",
                                    class: "text-right",
                                },
                                {
                                    title: "Disetujui",
                                    data: "disetujui",
                                    class: "text-right",
                                }
                            ],
                            "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                                if (aData.status == 'incomplete') {
                                    $(nRow).css("background", "#fde4a8");
                                } else if (aData.status == 'exist') {
                                    $(nRow).css("background-color", "#aeffef");
                                } else if (aData.status == 'duplikat') {
                                    $(nRow).css("background-color", "#f7bf96");
                                } else {
                                    $(nRow).css("background-color", "#ffcccc");
                                }
                            }
                        });
                        let nama_file = res.response.file;
                        $("#label-file").html(nama_file);
                        $(".lihat_file").attr('data-file', nama_file);
                        docoNotification("success", i18next.t("Proses Berhasil"), i18next.t("File Berhasil di upload."));
                    }
                },
                error: function(res) {
                    failedProgressBar();
                    // $(".progress-bar-success").css("background-color","#eeeeee");
                    docoNotification("warning", i18next.t("Proses Gagal"), i18next.t("File yang di upload tidak sesuai dengan format contoh, xls dan xlsx"));
                    tableBlank();
                }
            });
        });

        $("#save-data").on("click", function(event) {
            event.preventDefault();
            if (verifikasiData.length > 0) {
                $(this).docoForm('click', {
                    type: "post",
                    dataType: 'JSON',
                    url: '/penjamin-asuransi/laporan-perbandingan-klaim/save-data',
                    data: {
                        'verifikasi_data': JSON.stringify(verifikasiData)
                    },
                    success: function(res) {
                        table.draw();
                        $("#modal_backdrop").modal('toggle');
                    },
                    error: function(res) {
                        // return true;
                        // docoNotification("danger", i18next.t("Proses Gagal"), i18next.t("Data Gagal di simpan"));
                    }
                });
            }
        });

    });

    function failedProgressBar() {
        $("#label-file").html('Pilih Berkas');
        $(".lihat_file").attr('data-file', 'Pilih Berkas');
        $(".myprogress").css("width", "0%");
        $(".myprogress").html("0%")
    }

    function tableBlank() {
        _tableVerifikasi.clear();
        _tableVerifikasi.destroy();
        _tableVerifikasi = $('#verifikasi-klaim-table').DataTable({
            data: [],
            columns: [{
                    title: "No",
                    data: "nomor",
                    orderable: false
                },
                {
                    title: "No SEP",
                    data: "no_sep",
                },
                {
                    title: "Tanggal Verifikasi",
                    data: "tgl_verifikasi",
                    class: "text-right",
                },
                {
                    title: "Rill RS",
                    data: "rill_rs",
                    class: "text-right",
                },
                {
                    title: "Diajukan",
                    data: "diajukan",
                    class: "text-right",
                },
                {
                    title: "Disetujui",
                    data: "disetujui",
                    class: "text-right",
                }
            ],
        });
    }
</script>