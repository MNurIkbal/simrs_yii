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
    <?php
    $form = ActiveForm::begin([
        'id' => 'sync-form',
        'enableAjaxValidation' => false,
        'enableClientValidation' => false,
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => [
            'labelSpan' => 3,
            'deviceSize' => ActiveForm::SIZE_SMALL
        ],
        'options' => [
            'role' => 'form',
        ]
    ]);
    ?>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>

<div class="modal-body bodyMin">
   <div class="col-md-12">
      <div class="row">
         <label class="text-left control-label col-sm-2 has-star"><?= Yii::t("fe", "Instalasi") ?></label>
         <div class="col-md-3 classMin">
            <?= Html::activeDropDownList($model, 'instalasi', $instalasi, ['class' => 'form-control select2 ']) ?>
         </div>
         <?=
               $form->field($model, 'no_pendaftaran', [
                  'horizontalCssClasses' => [
                     'label' => 'text-left control-label col-sm-3',
                     'wrapper' => 'col-md-3 classMin'
                  ]
               ])->textInput()->label(Yii::t('fe', 'No Pendaftaraan'))
         ?>
         <button type="button" class="btn btn-success btn-xs btn-labeled" id="btn-sync-pasien"><b><i class="fa fa-refresh"></i></b> <?= Yii::t('fe', 'Sinkron') ?></button>
         <?php ActiveForm::end(); ?>
      </div>
      <br>
      <div class="row">
         <div class="progress" style="display: none;">
            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"  aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
            <span class="label-persentase"></span>%</div>
         </div>
         <span class="help-block label-progress"></span>
      </div>
   </div>
</div>
<div class="modal-footer"></div>

<script type="text/javascript">
   $(document).ready(function() {
      const progressSingle = $(".progress");
      
      
      const showInfoSingle = () => {
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

      const setPresentaseSingle = function(progress) {
         $(".progress .label-persentase").html(progress)
         $(".progress .progress-bar").css("width", progress +"%")
         .attr("aria-valuenow", progress)
         .attr("aria-volume", progress);
      }

      async function updateProgressBarSingle(randString,instalasi,no) {
         let config = await $.getJSON("./../../json/setup.json")
         if (config.origin == "true") {
            var socket = io.connect(window.location.origin);
         } else {
            var socket = io.connect(config.ip+':'+config.port);
         }

         const channel = `sync-eklaim:`
         await showInfoSingle()
         $.ajax({
            url : '/penjamin-asuransi/informasi-pasien-ranap-bpjs/single-sync-save?randString=' + randString + '&instalasi=' + instalasi + '&no=' + no,
            beforeSend: function() {
               socket.on(channel + randString, (message) => {
                  const _data = $.parseJSON(message);
                  const { status , messageProcess , progress, hide} = _data
                  if(status == 'finish') {
                     $(".label-progress").html(`<p style="font-size:16px;font-weight:bold;">${messageProcess}</p>`)
                     setPresentaseSingle(progress)
                     if (progress == 100) {
                        $('#btn-sync-pasien').prop("disabled", false);
                        table.draw();
                        if(hide == false) {  
                           docoNotification("success", "Proses Berhasil", "Data Berhasil Tersinkronisasi");
                        }
                        $('#modal_backdrop').modal('hide');
                     }
                  } else if (status == 'failed') {
                     $('#btn-sync-pasien').prop("disabled", false);
                     docoNotification('error','Proses Gagal!', messageProcess)
                     $('#modal_backdrop').modal('hide');
                  } else {
                     setPresentaseSingle(100)
                     $(".label-progress").html(`<p style="font-size:16px;font-weight:bold;">Sinkronisasi sedang berjalan  </p>`)
                  }
               });
            },
            success : function (data) {
               let startNum = 5
               setPresentaseSingle(startNum)
               let totalProgres = parseInt(startNum) + parseInt(data.totalPerPage) + 20;
               $(".label-progress").html(`<p style="font-size:16px;font-weight:bold;">Sinkronisasi sedang berjalan </p>`);
            }
         });
      }

      $('#btn-sync-pasien').on('click', function(e) {
         e.preventDefault()
         let instalasi = $('#syncpasienform-instalasi').val()
         let no = $('#syncpasienform-no_pendaftaran').val()
         var header = "Perhatian!";
         var message = "Lakukan Sinkronisasi Pasien?";
         var label = {
            buttons: {
               "Yes": "button-yes",
               "No": "button-no"
            },
         };

         $.showQuestionDialog(header, message, label, function (reaction) {
            if (reaction == "Yes") {
               $.ajax({
                  url : '/penjamin-asuransi/informasi-pasien-ranap-bpjs/get-random-string',
                  success : function (data) {
                     var randString = data
                     if(randString !== null || randString != '') {
                        progressSingle.css("display", "block")
                        $('#btn-sync-pasien').prop("disabled", true);
                        updateProgressBarSingle(randString,instalasi,no)
                     }
                  }
               });
            } 
         });
      })
   })
</script>
