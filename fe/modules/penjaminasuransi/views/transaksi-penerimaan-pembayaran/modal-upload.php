<?php
// use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use app\components\DocoConstants;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;

$this->title = Yii::t('fe', 'Upload File');
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
   <?php $form = ActiveForm::begin([
        'id' => 'form-upload',
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
               <span id="label-file">Pilih File</span>
            </label>
            <p class="format-upload">format :xls, xlsx</p>
         </div>
      </div>
      <center><span class="populate-data" style="font-size:16px;font-weight:bold;margin-bottom:10px;"></span></center>
      <div class="modal-body">
         <div class="progress">
            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"  aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
            <span class="label-persentase"></span>%</div>
         </div>
         <span class="help-block label-progress"></span>
      </div>
   </div>
    
   <?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
   function failedProgressBar() {
      $("#label-file").html('Pilih File');
      $(".lihat_file").attr('data-file', 'Pilih File');
      $(".progress").css("width", "0%");
      $(".progress").html("0%");
      $('.label-progress').text('');
   }

   function toggleUploadWrapper() {
      $('.progress').css('width', '0');
      $('.label-progress').text('');
   }

   function randomString(length) {
      var result           = '';
      var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
      var charactersLength = characters.length;
      for ( var i = 0; i < length; i++ ) {
         result += characters.charAt(Math.floor(Math.random() * charactersLength));
      }
      return result;
   }

   var progress = $(".progress");
   var progressBar = $(".progress .progress-bar");
   var labelProgress = $(".label-progress");
   var labelPercent = $(".label-persentase");
   progress.css("display", "none")

   var showInfo = () => {
      return new Promise((resolve) => {
         setTimeout(() => {
               resolve($(".populate-data").html(`mempersiapkan data ...`))
         }, 1000);
         setTimeout(() => {
               resolve($(".populate-data").css("display", "none"))
               resolve(progress.css("display", "block"))
               resolve($(".label-progress").html(`<p style="font-size:16px;font-weight:bold;"> menyiapkan data ... </p>`))
         }, 2000);
      })
   }

   var setPresentase = function(progress) {
      $(".progress .label-persentase").html(progress)
      $(".progress .progress-bar").css("width", progress +"%")
      .attr("aria-valuenow", progress)
      .attr("aria-volume", progress);
   }

   async function updateProgressBar(data) {
      let config = await $.getJSON("./../../json/setup.json")
      if (config.origin == "true") {
         var socket = io.connect(window.location.origin);
      } else {
         var socket = io.connect(config.ip+':'+config.port);
      }
      const channel = `invoice:`
      await showInfo()
      
      _randString = randomString(5)
      
      $.ajax({
         type: "post",
         dataType: false,
         cache: false,
         contentType: false,
         processData: false,
         data: data,
         url : '/penjamin-asuransi/transaksi-penerimaan-pembayaran/process-sync?randString=' + _randString,
         success : function (res) {
            var _response = typeof res.response != 'undefind' ? res.response : null;
            hideLoader();
            let startNum = 5
            let totalPerPage = 50
            setPresentase(startNum)
            let totalProgres = parseInt(startNum) + parseInt(totalPerPage) + 20;
            labelProgress.html(`<p style="font-size:16px;font-weight:bold;">Sedang memproses Data </p>`);
            socket.on(channel + _response.randString, (message) => {
               const _data = $.parseJSON(message);
               const { status , progress, messageProcess, data_pengajuan, total_pengajuan, total_disetujui} = _data
               if(status == 'finish') {
                  $(".label-progress").html(`<p style="font-size:16px;font-weight:bold;">${messageProcess}</p>`)
                  setPresentase(progress)
                  if (progress == 100) {
                     $("#modal_backdrop").modal("toggle")
                     initTableBpjs({
                        data: data_pengajuan
                     });
                     $(document).find("#total_terimabayar").val(total_disetujui).trigger('change');
                  }
               } else if (status == 'failed') {
                  docoNotification('error','Proses Gagal!', messageProcess)
                  $('.progress-bar').addClass('bg-danger');
               } else {
                  startNum++
                  setPresentase(Math.ceil((startNum/totalProgres) * 100))
                  var currentProcess = (startNum-5);
                  labelProgress.html(`<p style="font-size:16px;font-weight:bold;">Sedang memproses Data </p>`)
               }
            });
         }
      });
   }

   $(document).ready(function() {
      $("#file-upload").change(function(event) {
         event.preventDefault();
         let button = this;
         let data = new FormData();
         let dataPost = $("#form-upload").serializeArray();
         data.append("TransaksiAlokasiImportForm[upload_file]", $("#file-upload")[0].files[0]);
         $.ajax({
            type: "post",
            dataType: false,
            cache: false,
            contentType: false,
            processData: false,
            url: "/penjamin-asuransi/transaksi-penerimaan-pembayaran/upload-template-process",
            data: data,
            success: function(res) {
               updateProgressBar(data)
            },
            error: function(res) {
               var _msg = i18next.t("File yang di upload tidak sesuai dengan format contoh, xls dan xlsx")
               var _title = i18next.t("Proses Gagal")
               if(typeof res.responseJSON !== 'undefined') {
                  var response = res.responseJSON
                  if(typeof response.response !== 'undefined') {
                     var responseData = response.response
                     if(typeof responseData.text !== 'undefined') {
                        _msg = responseData.text
                        _title = responseData.title
                     }
                  }
               }
               hideLoader();
               docoNotification("warning", _title, _msg);
               $("#modal_backdrop").modal("toggle")
            }
         });
      });
   });
</script>