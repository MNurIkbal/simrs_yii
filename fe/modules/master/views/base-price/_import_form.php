<?php
    // use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use yii\web\View;
    use app\components\DocoConstants;
    use app\components\DocoHelpers;
    use kartik\widgets\ActiveForm;

    $this->title = Yii::t('fe', 'Import Data Base Price Obat');
?>
<style type="text/css">
    .modal-dialog {
        /*w*/idth: 65% !important;
        margin: 30px auto;
    }
    button#button-back {
        height: 30px;
        padding-top: 5px;
    }

    #file-upload {
        display:none;
        margin: 10px;
    }
    .inputfile + label {
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
    .no-js .inputfile + label {
        display: none;
    }

    .inputfile:focus + label,
    .inputfile.has-focus + label {
        outline: 1px dotted #000;
        outline: -webkit-focus-ring-color auto 5px;
    }

    .inputfile + label * {
        /* pointer-events: none; */
        /* in case of FastClick lib use */
    }

    .inputfile + label svg {
        width: 1em;
        height: 1em;
        vertical-align: middle;
        fill: currentColor;
        margin-top: -0.25em;
        /* 4px */
        margin-right: 0.25em;
        /* 4px */
    }


    .inputfile-1 + label {
        color: #f1e5e6;
        background-color: #54be8b;
        
    }

    .inputfile-1:focus + label,
    .inputfile-1.has-focus + label,
    .inputfile-1 + label:hover {
        background-color: #722040;
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
    .format-upload{
        font-size: 12px;
        text-align: left;
        width: 14%;
        font-style: italic;
    }
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
    width: 100%;
    }
  .my-legend .legend-scale ul li {
    display: inline-grid;
    float: left;
    width: 50px;
    margin-bottom: 6px;
    margin-right: 5px;
    text-align: left;
    font-size: 98%;
    list-style: none;
    width: 40%;
    font-style: italic;
    }
  .my-legend ul.legend-labels li span {
    display: block;
    float: left;
    height: 15px;
    width: 50px;
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
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $this->title;?></h5>
</div>
<div class="modal-body">
    <div class="panel">
        <div class="panel-toolbar clearfix">
             <?=
             DocoHelpers::generateToolbar([
                "back"=> [
                        'attributes'=>[
                            'id' => 'btn-back',
                        ]
                    ],
                'save' => [
                    'title' => \Yii::t('fe', 'Upload'),
                    'attributes' => [
                        // 'style' => $roleUbahBtn,
                        'id' => 'upload-baseprice',
                        // 'data-options' => 'modal',
                        // 'data-target' => '#modal_backdrop',
                        // 'data-url' => '/master/base-price/edit?id=',
                    ]
                ],
            ], "#history-obat");
            ?>
        </div>
    </div>
    <?php $form = ActiveForm::begin([
            'id' => 'pasien-luar-form',
            'enableAjaxValidation'=>false, 
            'enableClientValidation'=>false, 
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'formConfig' => [
                'labelSpan' => 4, 
                'deviceSize' => ActiveForm::SIZE_MEDIUM
            ],
            'options' => [
                'skip-confirm' => "true"
            ]
        ]);
    ?>
    <div class="clear"><br></div>
    <div class="form-group">
        <div class="col-md-2">
            <label class="control-label text-black l-label" style="padding-left:0px;"><?= Yii::t('fe', 'Pilih File'); ?></label>
        </div>
        <div class="col-md-10">
            <input type="file" name="UploadForm[upload_file]" id="file-upload" class="form-control inputfile inputfile-1" data-multiple-caption="{count} files selected" >
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
    <table id="history-obat" class="table table-striped table-condensed table-hover" style="width:100%;padding-top: 10px;">
        <thead>
            <tr class="bg-inverse">
                <th width="1">No</th>
                <th><?=\Yii::t("fe", "Nama Obat");?></th>
                <th align="right"><?=\Yii::t("fe", "Harga Netto Terakhir").' (Rp.) ';?></th>
                <th align="right"><?=\Yii::t("fe", "Suggestion System").' (Rp.) ';?></th>
                <th align="right"><?=\Yii::t("fe", "Harga Dasar Saat ini").' (Rp.) ';?></th>
                <th align="right" ><?=\Yii::t("fe", "Harga Dasar Yang Akan Digunakan").' (Rp.) ';?></th>
                <th ><?=\Yii::t("fe", "Obat Alkes ID");?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" colspan="5"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
            </tr>
        </tbody>
    </table>
    <div class="row">
        <div class="col-md-12">
            <div class='my-legend'>
                <div class='legend-title'>Peringatan :</div>
                <div class='legend-scale'>
                    <ul class='legend-labels'>
                        <li><span style='background:#F6C1C1;'></span>Data tidak valid dan tidak akan tersimpan</li>
                    </ul>
                </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?php 
$this->registerJs('
    // Global Var
    var _tableObat;
    var no_urut = 0;
    

', View::POS_END, 'b-index');
?>
<script>
    $("#btn-back").on("click", function(event) {
       $("#modal_backdrop").modal('toggle');
    });
    $(document).ready(function () {
        _tableObat = $('#history-obat').DataTable( {
                data: {},
                columns: [
                    {
                        title: "No",
                        data: "obatalkes_nama",
                        orderable: false
                    },
                    {
                        title: "Nama Obat", 
                        data: "obatalkes_nama",
                    },
                    {
                        title: "Harga Netto Terakhir (Rp.)", 
                        data: "hn_last",
                        class : "text-right",
                    },
                    {
                        title: "Suggestion System (Rp.)", 
                        data: "harga_sugesstion",
                        class : "text-right",
                    },
                    {
                        title: "Harga Dasar Saat ini (Rp.)", 
                        data: "last_harganetto",
                        class : "text-right",
                        // searchable: false
                    },
                    {
                        title: "Harga Dasar Yang Akan Digunakan (Rp.)", 
                        data: "harganetto",
                        class : "text-right",
                        // searchable: false
                    },
                    {
                        title: "Obat Alkes ID", 
                        data: "obatalkes_id",
                        searchable: false,
                        visible: false
                    },
                ],
                "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                    console.log(aData.obatalkes_id)
                    if (aData.obatalkes_id) {
                        $(nRow).css("background", "#F6C1C1");
                    } else {
                        $(nRow).css("background-color", "fff");
                    }
                   // var index = iDisplayIndex +1;
                   // $('td:eq(0)',nRow).html(index);
                   // return nRow;
                }
            });
    });


        $("#file-upload").change(function(event) {
        event.preventDefault();
        let button = this;
        let data = new FormData();
        let dataPost = $("#form-upload").serializeArray();
        let getLink = $('.lihat_file').attr('href');
        
        data.append("UploadForm[upload_file]", $("#file-upload")[0].files[0]);
        $('.myprogress').css('width', '0');
        $('.msg').text('');

        $.ajax({
            type: "post",
            dataType: false, // what to expect back from the PHP script, if anything
            cache: false,
            contentType: false,
            processData: false,
            url: "/master/base-price/upload",
            data: data,
            beforeSend: function (request, res) {
            },
            xhr: function (res) {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function (evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = evt.loaded / evt.total;
                        percentComplete = parseInt(percentComplete * 100);
                        $('.myprogress').text(percentComplete + '%');
                        $('.myprogress').css('width', percentComplete + '%');
                    }
                }, false);
                // Download progress
                /*xhr.addEventListener("progress", function(evt){
                  if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    console.log(percentComplete);
                  }
                }, false);*/
                return xhr;
            },
            success: function (res) {
                $("#file-upload").val('');
                // $('.msg').text(res.response.file);
                $("#file-upload").removeAttr("disabled");
                var data = res.response.data;
                if (res.response.status == 422) {
                    failedProgressBar();
                    docoNotification("warning", i18next.t("Proses Gagal"), i18next.t("File gagal di upload, data inputan Harga Dasar Yang Akan Digunakan (Rp.) tidak boleh kosong."));
                    tableBlank();
                } else {
                    _tableObat.destroy();
                    _tableObat = $('#history-obat').DataTable( {
                                data: data,
                                columns: [
                                            {
                                                title: "No",
                                                data: "nomor",
                                                orderable: false
                                            },
                                            {
                                                title: "Nama Obat", 
                                                data: "obatalkes_nama",
                                            },
                                            {
                                                title: "Harga Netto Terakhir (Rp.)", 
                                                data: "hn_last",
                                                class : "text-right",
                                            },
                                            {
                                                title: "Suggestion System (Rp.)", 
                                                data: "harga_sugesstion",
                                                class : "text-right",
                                            },
                                            {
                                                title: "Harga Dasar Saat ini (Rp.)", 
                                                data: "last_harganetto",
                                                class : "text-right",
                                            },
                                            {
                                                title: "Harga Dasar Yang Akan Digunakan (Rp.)", 
                                                data: "harganetto",
                                                class : "text-right",
                                            },
                                            {
                                                title: "Obat Alkes ID", 
                                                data: "obatalkes_id",
                                                searchable: false,
                                                visible: false
                                            },
                                        ],
                                        "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                                            if ((aData.obatalkes_id == null) || (aData.harganetto == null)  ) {
                                                $(nRow).css("background", "#F6C1C1");
                                            } else {
                                                $(nRow).css("background-color", "fff");
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
    })

    function failedProgressBar() {
        $("#label-file").html('Pilih Berkas');
        $(".lihat_file").attr('data-file', 'Pilih Berkas');
        $(".myprogress").css("width","0%");
        $(".myprogress").html("0%")
    }


    $("#upload-baseprice").on("click", function(event) {
        event.preventDefault();
        var data = $("#upload-baseprice-form").serializeArray();
        $(this).docoForm('click',{
            url: '/master/base-price/upload-base-price',
            data: data,
            success : function(res) {
                // var form = $("#table-base-price");
                // form[0].reset();
                table.draw();
                $("#modal_backdrop").modal('toggle');
            },
            error: function(res) {
                return true;
                // docoNotification("danger", i18next.t("Proses Gagal"), i18next.t("Data Gagal di simpan"));
            }
        });
    });

    function tableBlank() {
        _tableObat.clear();
        _tableObat.destroy();
        _tableObat = $('#history-obat').DataTable( {
                    data: [],
                    columns: [
                {
                    title: "No",
                    data: "nomor",
                    orderable: false
                },
                {
                    title: "Nama Obat", 
                    data: "obatalkes_nama",
                },
                {
                    title: "Harga Netto Terakhir (Rp.)", 
                    data: "hn_last",
                    class : "text-right",
                },
                {
                    title: "Suggestion System (Rp.)", 
                    data: "harga_sugesstion",
                    class : "text-right",
                },
                {
                    title: "Harga Dasar Saat ini (Rp.)", 
                    data: "last_harganetto",
                    class : "text-right",
                },
                {
                    title: "Harga Dasar Yang Akan Digunakan (Rp.)", 
                    data: "harganetto",
                    class : "text-right",
                },
                {
                    title: "Obat Alkes ID", 
                    data: "obatalkes_id",
                    searchable: false,
                    visible: false
                },
            ],
        });
    }

</script>
