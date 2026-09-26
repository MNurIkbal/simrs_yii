<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\file\FileInput;
use app\components\DocoHelpers;
?>
<style type="text/css">
   .classMin {
      margin-left: -30px;
   }

   .bodyMin {
      margin-top: 0px;
   }
</style>
<div class="modal-header bg-inverse">
   <button type="button" class="close" data-dismiss="modal">&times;</button>
   <h5 class="modal-title"><?= $title ?></h5>
</div>

<div class="modal-body bodyMin">
   <div class="col-md-12">
      <div class="row">
         <div class="progress" style="display: none;">
            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
               <span class="label-persentase"></span>%
            </div>
         </div>
         <span class="help-block label-progress"></span>
         <div class="col-12" style="margin-top: 50px;">
            <h6>Data Sinkronisasi gagal</h6>
            <table class="table table-striped" id="syncGagal" style="width: 100%;">
               <thead>
                  <tr>
                     <th>Nomer Pendaftaran</th>
                     <th>Status Sinkronisasi</th>
                  </tr>
               </thead>
               <tbody id="tbody-table">
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div>
<div class="modal-footer"></div>

<script type="text/javascript">
   $(document).ready(function() {
      const progressSingle = $(".progress");
      var randString = '<?= $randString ?>';
      const showInfo = () => {
         return new Promise((resolve) => {
            setTimeout(() => {
               resolve($(".populate-data").html(`mempersiapkan data ...`))
            }, 1000);
            setTimeout(() => {
               resolve($(".populate-data").css("display", "none"))
               resolve(progressSingle.css("display", "block"))
               resolve($(".label-progress").html(`<p style="font-size:16px;font-weight:bold;"> menyiapkan data ... </p>`))
            }, 2000);
         })
      }

      const setPresentase = function(progress) {
         $(".progress .label-persentase").html(progress)
         $(".progress .progress-bar").css("width", progress + "%")
            .attr("aria-valuenow", progress)
            .attr("aria-volume", progress);
      }

      async function updateProgressBar() {
         let config = await $.getJSON("./../../json/setup.json")
         if (config.origin == "true") {
            var socket = io.connect(window.location.origin);
         } else {
            var socket = io.connect(config.ip + ':' + config.port);
         }

         const channel = `sync-eklaim:`
         await showInfo()
         $.ajax({
            url: '/penjamin-asuransi/informasi-pasien-ranap-bpjs/sync-all-save?randString=' + randString,
            success: function(data) {
               let startNum = 5
               setPresentase(startNum)
               let totalProgres = parseInt(startNum) + parseInt(data.totalPerPage) + 20;
               $(".label-progress").html(`<p style="font-size:16px;font-weight:bold;">Sinkronisasi sedang berjalan</p>`);
               socket.on(channel + randString, (message) => {
                  const _data = $.parseJSON(message);
                  const {
                     status,
                     messageProcess,
                     filename,
                     progress,
                     no_pendaftaran
                  } = _data;
                  if (status == 'finish') {
                     $(".label-progress").html(`<p style="font-size:16px;font-weight:bold;">${messageProcess}</p>`)
                     setPresentase(progress)
                  } else if (status == 'finish') {
                     docoNotification('error', 'Proses Gagal!', messageProcess)
                  }

                  if (status == 'failed') {
                     startNum++
                     setPresentase(progress)
                     var currentProcess = (startNum - 5);
                     $(".label-progress")
                        .html(`<p style="font-size:16px;font-weight:bold;">${messageProcess}</p>`)
                     if(no_pendaftaran != undefined) {
                        appendGagalSync(no_pendaftaran)
                     }
                  }
               });
            }
         });
      }

      let table = $('#syncGagal').DataTable({
         "searching": false,
         "displayLength": 10,
         "columns": [{
               "data": "no_pendaftaran"
            },
            {
               "data": "status"
            }
         ]
      })

      function appendGagalSync(no_pendaftaran) {
         if(no_pendaftaran != undefined) {
            table.row.add( {
               "no_pendaftaran": no_pendaftaran,
               "status": 'Gagal'
            }).draw();
         }
      }

      updateProgressBar()
   })
</script>