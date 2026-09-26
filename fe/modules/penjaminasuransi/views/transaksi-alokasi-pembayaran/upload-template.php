<?php
// use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use app\components\DocoConstants;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;

$this->title = Yii::t('fe', 'Unggah Template');
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

    #upload-template-table tbody tr td {
        color: #000 !important;
    }

    .hide-element {
        display: none;
    }

    .after-upload-wrapper {
        margin-top: -35px;
    }

    .after-upload-wrapper .dataTables_scrollBody {
        margin-top: -10px;
    }
    .after-upload-wrapper .dataTables_scroll {
        max-height: max-content !important;
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
                    'title' => 'Simpan',
                    'attributes' => [
                        'id' => 'save-data',
                        'disabled' => true
                    ],
                ],
                'reupload' => [
                    'title' => 'Refresh Modal (Upload Ulang)',
                    'icon' => 'fa fa-refresh',
                    'attributes' => [
                        'id' => 'btn-reupload',
                        'data-options' => 'click',
                        'disabled' => true
                    ]
                ],
            ], "#upload-template-table");
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
    <div class="before-upload-wrapper">
        <div class="form-group">
            <div class="col-md-2">
                <label class="control-label text-black l-label" style="padding-left:0px;">
                    Pilih File
                </label>
            </div>
            <div class="col-md-6">
                <input type="file" name="TransaksiAlokasiImportForm[upload_file]" id="file-upload" class="form-control inputfile inputfile-1" data-multiple-caption="{count} files selected">
                <label for="file-upload">
                    <i class="fa fa-upload"></i>
                    <span id="label-file">Pilih Berkas</span>
                </label>
                <p class="format-upload">format :xls, xlsx</p>
            </div>
        </div>
        <div class="progress-wrapper form-group">
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
    </div>

    <div class="after-upload-wrapper hide-element">
        <table id="upload-template-table" class="table table-striped table-condensed table-hover" style="width:100%; padding-top: 10px;">
            <thead>
                <tr class="bg-inverse">
                    <th width="1">No</th>
                    <th>Data Pasien</th>
                    <th>No Invoice</th>
                    <th>Instalasi / Ruangan</th>
                    <th>Tagihan (Rp.)</th>
                    <th>Jumlah Pasien Bayar (Rp.)</th>
                    <th>Piutang (Rp.)</th>
                    <th>Piutang (Telah Bayar) (Rp.)</th>
                    <th>Jumlah Bayar (Rp.)</th>
                    <th>Sisa Tagihan (Rp.)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center" colspan="9">Data Tidak Ditemukan</td>
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
                            <li><span style='background:#ffcccc;'>Jumlah Bayar Lebih Dari Sisa Tagihan</span></li>
                            <li><span style='background:#b37bb1;'>Jumlah Bayar Negatif</span></li>
                            <li><span style='background:#aeffef;'>Data lengkap dan siap untuk disimpan</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
    var uploadTemplateTable = null;
    var resultDatas = null;

    function checkIsCompletedAllDatas(datas) {
        let isCompleted = true;
        datas.forEach(element => {
            const isNotCompleted = element.status != 'complete';
            if (isNotCompleted) isCompleted = false;
        });
        return isCompleted;
    }

    function initButtonRefresh() {
        $(`#btn-reupload`).attr(`disabled`, true);
        $(`#btn-reupload`).on(`click`, function() {
            toggleUploadWrapper(false);
            clearDatatableUploadTemplate();
        });
    }

    function failedProgressBar() {
        $("#label-file").html('Pilih Berkas');
        $(".lihat_file").attr('data-file', 'Pilih Berkas');
        $(".myprogress").css("width", "0%");
        $(".myprogress").html("0%");
        $('.msg').text('');
        $(`.progress-wrapper`).addClass(`hide-element`);
    }

    function clearDatatableUploadTemplate() {
        if (uploadTemplateTable) {
            uploadTemplateTable.clear();
            uploadTemplateTable.destroy();
            initDatatableUploadTemplate({
                data: []
            });
        }
    }

    function initDatatableUploadTemplate({
        data
    }) {
        uploadTemplateTable = $('#upload-template-table').DataTable({
            data,
            columns: [{
                    title: "No",
                    orderable: false,
                    data: 'no',
                },
                {
                    title: "Data Pasien",
                    data: 'data_pasien',
                    orderable: false,
                    render: (data, type) => {
                        if(data) data = data.replace(/(?:\r\n|\r|\n)/g, '<br>')
                        return data;
                    }
                },
                {
                    title: "No Invoice",
                    data: 'no_invoice'
                },
                {
                    title: "Instalasi / Ruangan",
                    orderable: false,
                    data: 'instalasi_ruangan',
                    render: (data, type) => {
                        if(data) data = data.replace(/(?:\r\n|\r|\n)/g, '<br>')
                        return data;
                    }
                },
                {
                    title: "Tagihan (Rp.)",
                    data: 'tagihan'
                },
                {
                    title: "Jumlah Pasien Bayar (Rp.)",
                    data: 'pasien_bayar'
                },
                {
                    title: "Piutang (Rp.)",
                    data: 'piutang'
                },
                {
                    title: "Piutang (Telah Bayar) (Rp.)",
                    data: 'piutang_bayar'
                },
                {
                    title: "Jumlah Bayar (Rp.)",
                    data: 'jumlah_bayar'
                },
                {
                    title: "Sisa Tagihan (Rp.)",
                    data: 'sisa_tagihan'
                },
            ],
            scrollX: '100%',
            destroy: true,
            "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                if (aData.status == 'incomplete') {
                    $(nRow).css("background", "#fde4a8");
                } else if (aData.status == 'is_large_than') {
                    $(nRow).css("background-color", "#ffcccc");
                } else if (aData.status == 'is_negative') {
                    $(nRow).css("background-color", "#b37bb1");
                } else if (aData.status == 'complete') {
                    $(nRow).css("background-color", "#aeffef");
                }
            },
        });
        setTimeout(() => {
            $('#upload-template-table').DataTable().columns.adjust();
        }, 10);
    }

    function toggleUploadWrapper(isAfterUpload) {
        if (isAfterUpload) {
            $(`.before-upload-wrapper`).addClass(`hide-element`);
            $(`.after-upload-wrapper`).removeClass(`hide-element`);
        } else {
            $('.myprogress').css('width', '0');
            $('.msg').text('');
            $(`#save-data`).attr(`disabled`, true);
            $(`.before-upload-wrapper`).removeClass(`hide-element`);
            $(`.after-upload-wrapper`).addClass(`hide-element`);
        }
    }

    $("#btn-back").on("click", function(event) {
        $("#modal_backdrop").modal('toggle');
    });

    $("#save-data").on("click", function(event) {
        event.preventDefault();
        $(this).docoForm('click', {
            type: "post",
            dataType: 'JSON',
            url: '/penjamin-asuransi/transaksi-alokasi-pembayaran/upload-template-save',
            data: {
                details: JSON.stringify(resultDatas)
            },
            success: function(res) {
                _tabel.draw();
                $("#modal_backdrop").modal('toggle');
            },
            error: function(res) {}
        });

    });


    $(document).ready(function() {
        initButtonRefresh();
        initDatatableUploadTemplate({
            data: []
        });
        $(`#btn-reupload`).attr(`disabled`, true);
        $("#file-upload").change(function(event) {
            event.preventDefault();
            toggleUploadWrapper(false);
            $(`.progress-wrapper`).removeClass(`hide-element`);
            let button = this;
            let data = new FormData();
            let dataPost = $("#form-upload").serializeArray();
            let getLink = $('.lihat_file').attr('href');
            data.append("TransaksiAlokasiImportForm[upload_file]", $("#file-upload")[0].files[0]);
            event.target.value = null;
            $.ajax({
                type: "post",
                dataType: false, // what to expect back from the PHP script, if anything
                cache: false,
                contentType: false,
                processData: false,
                url: "/penjamin-asuransi/transaksi-alokasi-pembayaran/upload-template-process",
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
                    const currentResponse = res.response;
                    const tempData = currentResponse.data
                    const tempFileName = currentResponse.file
                    const tempStatus = currentResponse.status
                    resultDatas = tempData
                    initDatatableUploadTemplate({
                        data: tempData
                    });
                    toggleUploadWrapper(true);
                    $(`#btn-reupload`).attr(`disabled`, false);
                    const isCompleted = checkIsCompletedAllDatas(tempData);
                    if (isCompleted) {
                        $(`#save-data`).attr(`disabled`, false);
                    }
                },
                error: function(res) {
                    failedProgressBar();
                    docoNotification("warning", i18next.t("Proses Gagal"), i18next.t("File yang di upload tidak sesuai dengan format contoh, xls dan xlsx"));
                    clearDatatableUploadTemplate();
                    initDatatableUploadTemplate({
                        data: []
                    });
                    $(`#btn-reupload`).attr(`disabled`, false);
                    $(`#save-data`).attr(`disabled`, true);
                }
            });

        });
    });
</script>