<?php
use yii\helpers\Html;
use kartik\datetime\DateTimePicker;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;

use yii\helpers\Url;
?>
<style media="screen">
  .sweet-alert, .sweet-overlay{
    z-index: 9999 !important;
  }
</style>

<?php
$form = ActiveForm::begin([
    'id' => 'jadwaldokter-form',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 2, 'deviceSize' => ActiveForm::SIZE_SMALL],
    'enableClientValidation'=>false,
    'enableAjaxValidation'=>false,
]);
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <!-- <div class="alert alert-danger alert-bordered alert-modal" style="display:none"> -->
        <button type="button" class="close" data-dismiss="alert"><span>×</span><span class="sr-only">Close</span></button>
        <span class="text-semibold alert-message"></span>
    <!-- </div> -->
    <?php
    $modelJadwalDokter->instalasi_id = 1;
    ?>
    <?= $form->field($modelJadwalDokter, 'jadwaldokter_id')->hiddenInput(['id'=>'jadwaldokter_id'])->label(false); ?>
    <?= $form->field($modelJadwalDokter, 'instalasi_id')->hiddenInput(['id'=>'instalasi_id'])->label(false); ?>

    <?= $form->field($modelJadwalDokter, 'ruangan_id')->dropDownList($listRuangan, [
        'class'=>'select2',
        'id'=>'ruangan-id',
        'prompt'=>'--Pilih--'
    ]); ?>

    <?= $form->field($modelJadwalDokter, 'pegawai_id')->widget(DepDrop::classname(), [
        'options'=>['id'=>'pegawai_id', 'class'=>'select2'],
        'pluginOptions'=>[
            'initialize'=>true,
            'depends'=>['ruangan-id'],
            'placeholder'=>'--Pilih--',
            'loadingText'=>'Tunggu',
            'url'=>Url::to(['/master/jadwal-dokter/dep-list-dokter-ruangan'])
        ],
        'data'=>[$modelJadwalDokter['pegawai_id']=>''],
    ]); ?>
    <div class="content-hari hidden">
        <?= $form->field($modelJadwalDokter, 'jadwaldokter_hari')->dropDownList($listHari, [
            'class'=>'select2',
            'prompt'=>'--Pilih--'
        ]); ?>
    </div>
    <div class="content-jadwal">
        <div class="form-group field-jadwalbukapoli_id required">
            <label class="control-label col-sm-2 jbp"><?=Yii::t('fe', 'Jadwal Buka Poli')?></label>
            <label class="control-label col-sm-2 jdh hidden"><?=Yii::t('fe', 'Jadwal Dokter Hari')?></label>
            <?= $form->field($modelJadwalDokter, 'jadwalbukapoli_id')->widget(DepDrop::classname(), [
                'options'=>['id'=>'jadwalbukapoli_id', 'class'=>'select2'],
                'pluginOptions'=>[
                    'initialize'=>true,
                    'depends'=>['ruangan-id','jadwaldokter_id'],
                    'placeholder'=>'--Pilih--',
                    'loadingText'=>'Tunggu',
                    'url'=>Url::to(['/master/jadwal-dokter/dep-list-hari-ruangan'])
                ],
                'pluginEvents'=>[
                    "depdrop:afterChange"=>"function(event, id, value) {
                                        // var data = $('#jadwalbukapoli_id').find(':selected').data();
                                        // var hari = data.hari;
                                        // var jam_mulai = data.jam_mulai;
                                        // var jam_selesai = data.jam_selesai;
                                        // var pegawai_id = $('#pegawai_id').val();
                                        // var ruangan_id = $('#ruangan_id').val();

                                        // getDisabledTime(hari, jam_mulai, jam_selesai, pegawai_id, ruangan-id, false);
                    }",
                ]
            ])->label(false); ?>
        </div>

    </div>
    <?php
    echo $form->field($modelJadwalDokter, 'jadwaldokter_mulai')->textInput([
        'class' => 'form-control input-sm jam_mulai',
        'data-mask' => '99:99'
    ]);
    ?>


    <?php
    echo $form->field($modelJadwalDokter, 'jadwaldokter_tutup')->textInput([
        'class' => 'form-control input-sm jam_selesai',
        'data-mask' => '99:99'
    ]);
    ?>

    <?php if($konfigSlotDokter == true) :?>
        <div class="form-group">
            <label class="col-lg-1 control-label">
                <?= Yii::t('fe', 'Slot Dokter'); ?>
            </label>
            <div class="col-lg-6">
                <?=$form->field($modelJadwalDokter, 'is_loaddokter')->checkbox()?>
            </div>
        </div>
    <?php endif; ?>

    <div id="div_jumlah_loaddokter">
    <?php
        echo $form->field($modelJadwalDokter, 'jumlah_loaddokter', ['addon' => ['append' => ['content' => 'Menit']]])
                  ->textInput(['class' => 'form-control input-sm doco-number doco-minute-only', 'id' => 'jumlah_loaddokter', 'onChange' => 'kalkulasi()' ]) 
    ?>
    </div>

    <?php if ($kuota_antrian == DocoConstants::VAR_ID_KUOTA_ANTRIAN_DOKTER): ?>
        <div class="content-maksantrian">
            <?php
            echo $form->field($modelJadwalDokter, 'kuota_total')
            ->textInput(
                [
                    'type'=>'number',
                    'min' => '0',
                    'value' => !empty($modelJadwalDokter->kuota_total)
                    ? $modelJadwalDokter->kuota_total
                    : 0,
                    'class' => 'form-control input-sm kuotaTotal docoNumberOnly kalkulasi',
                ]
            );
            ?>

            <?php if($konfigCaraBayar == true && $konfigReservasi == false) :?>
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-11">
                        <?=
                        $form->field($modelJadwalDokter, 'kuota_bpjs_total')
                        ->textInput(
                            [
                                'type'=>'number',
                                'min' => '0',
                                'value' => !empty($modelJadwalDokter->kuota_bpjs_total)
                                ? $modelJadwalDokter->kuota_bpjs_total
                                : 0,
                                'class' => 'form-control input-sm kuota_bpjs_total docoNumberOnly',
                                'onChange' => 'checkTotal(1)'
                            ]
                        );
                        ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-11">
                        <?=
                        $form->field($modelJadwalDokter, 'kuota_nonbpjs_total')
                        ->textInput(
                            [
                                'type'=>'number',
                                'min' => '0',
                                'value' => !empty($modelJadwalDokter->kuota_nonbpjs_total)
                                ? $modelJadwalDokter->kuota_nonbpjs_total
                                : 0,
                                'class' => 'form-control input-sm kuota_nonbpjs_total docoNumberOnly',
                                'onChange' => 'checkTotal(1)'
                            ]
                        );
                        ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="content-kuota">
                <?=
                $form->field($modelJadwalDokter, 'maximumantrian')
                ->textInput(
                    [
                        'type'=>'number',
                        'min' => '0',
                        'value' => !empty($modelJadwalDokter->maximumantrian)
                        ? $modelJadwalDokter->maximumantrian
                        : 0,
                        'class' => 'form-control input-sm maximumantrian docoNumberOnly kuotaAntrian',
                    ]
                );
                ?>

                <?php if($konfigCaraBayar == true) :?>
                    <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-md-11">
                            <?=
                            $form->field($modelJadwalDokter, 'kuota_bpjs_offline')
                            ->textInput(
                                [
                                    'type'=>'number',
                                    'min' => '0',
                                    'value' => !empty($modelJadwalDokter->kuota_bpjs_offline)
                                    ? $modelJadwalDokter->kuota_bpjs_offline
                                    : 0,
                                    'class' => 'form-control input-sm kuota_bpjs_offline docoNumberOnly',
                                    'onChange' => 'checkTotal(2)'
                                ]
                            );
                            ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-md-11">
                            <?=
                            $form->field($modelJadwalDokter, 'kuota_nonbpjs_offline')
                            ->textInput(
                                [
                                    'type'=>'number',
                                    'min' => '0',
                                    'value' => !empty($modelJadwalDokter->kuota_nonbpjs_offline)
                                    ? $modelJadwalDokter->kuota_nonbpjs_offline
                                    : 0,
                                    'class' => 'form-control input-sm kuota_nonbpjs_offline docoNumberOnly',
                                    'onChange' => 'checkTotal(2)'
                                ]
                            );
                            ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?=
                $form->field($modelJadwalDokter, 'kuota_online')
                ->textInput(
                    [
                        'type'=>'number',
                        'min' => '0',
                        'value' => !empty($modelJadwalDokter->kuota_online)
                        ? $modelJadwalDokter->kuota_online
                        : 0,
                        'class' => 'form-control input-sm kuota_online docoNumberOnly kuotaAntrian',
                    ]
                );
                ?>

                <?php if($konfigCaraBayar == true) :?>
                    <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-md-11">
                            <?=
                            $form->field($modelJadwalDokter, 'kuota_bpjs_online')
                            ->textInput(
                                [
                                    'type'=>'number',
                                    'min' => '0',
                                    'value' => !empty($modelJadwalDokter->kuota_bpjs_online)
                                    ? $modelJadwalDokter->kuota_bpjs_online
                                    : 0,
                                    'class' => 'form-control input-sm kuota_bpjs_online docoNumberOnly',
                                    'onChange' => 'checkTotal(3)'
                                ]
                            );
                            ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-md-11">
                            <?=
                            $form->field($modelJadwalDokter, 'kuota_nonbpjs_online')
                            ->textInput(
                                [
                                    'type'=>'number',
                                    'min' => '0',
                                    'value' => !empty($modelJadwalDokter->kuota_nonbpjs_online)
                                    ? $modelJadwalDokter->kuota_nonbpjs_online
                                    : 0,
                                    'class' => 'form-control input-sm kuota_nonbpjs_online docoNumberOnly',
                                    'onChange' => 'checkTotal(3)'
                                ]
                            );
                            ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <span id="messageKuota" style="padding-left: 95px;"><span>
        </div>
    <?php endif ?>

    <?php if($konfigSlotDokter == true) :?>
        <div class="form-group">
            <label for="is_active" class="col-lg-1 control-label">
                <?= Yii::t('fe', 'Bersedia Appointment'); ?>
            </label>
            <div class="col-lg-6">
                <?=$form->field($modelJadwalDokter, 'is_bersedia')->checkbox()?>
            </div>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <label for="is_active" class="col-lg-1 control-label">
            <?= Yii::t('fe', 'Status'); ?>
        </label>
        <div class="col-lg-6">
            <?=$form->field($modelJadwalDokter, 'is_active')->checkbox()?>
        </div>
    </div>

    <div class="form-group">
        <label for="is_active" class="col-lg-1 control-label">
            <?= Yii::t('fe', 'HFIS'); ?>
        </label>
        <div class="col-lg-6">
            <?=$form->field($modelJadwalDokter, 'is_skip_jkn')->checkbox()?>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?= Html::button('Simpan', ['class' => 'btn btn-success btn-md btn-simpan']) ?>
    <?= Html::button('Kembali',[
        'class' => 'btn btn-default btn-md',
        'data-dismiss' => 'modal'
    ]); ?>
</div>

<?php ActiveForm::end(); ?>
<script type="text/javascript">
var skipConfirmAjax = false;
var is_skip_jkn = false;

$(function() {
    let konfig = '<?= $konfigReservasi ?>';
    $('#jadwaldokterform-is_loaddokter').trigger('change')
    $('.kuotaAntrian').trigger('change')
    if(konfig == 1) {
        $("#jadwaldokterform-kuota_total").attr('disabled', true)
        $(".content-kuota").prop('hidden', false)
        $("#messageKuota").html('')
    } else {
        $(".content-kuota").prop('hidden', true)
        $("#jadwaldokterform-kuota_total").attr('disabled', false)
        $("#messageKuota").html('*kuota walkin digabung dengan kuota reservasi')
    }
})
    $(".date").pickadate({
    applyClass: "bg-slate-600",
    cancelClass: "btn-default",
    locale: {
        format: "DD-MMMM-YYYY"
    }
});

// $(document).on("change", "#jadwalbukapoli_id", function (event) {

// });
// $('.txt-timepicker').timepicker({
//     showMeridian: false,
//     minuteStep: 30,
//     defaultTime: false
// });

// var $inputMulai = $(".jam_mulai").pickatime({
//     format: "HH:i",

// });
// var pickerMulai = $inputMulai.pickatime('picker');


// var $inputSelesai = $(".jam_selesai").pickatime({
//     format: "HH:i",
//     onOpen: function () {
//         var selectedTime = pickerMulai.get('select');
//         if(selectedTime){
//             this.set('min',
//                 [selectedTime.hour,selectedTime.mins+30]
//             );
//             if(pickerMulai.get('disable').length>0){
//                 var firstDis = this.get('disable')[0];
//                 var lastDis = this.get('disable')[this.get('disable').length -1];
//                 this.set('max',false);
//                 if(selectedTime.hour <= firstDis[0]){
//                     this.set('max',firstDis);
//                 }else{
//                     var selesai = $('#jadwalbukapoli_id').find(":selected").data('jam_selesai');
//                     this.set('max',selesai);
//                 }
//             }
//         }
//     }
// });
// var pickerSelesai = $inputSelesai.pickatime('picker');

// $(document).on("change", "#jadwalbukapoli_id", function (event) {
//     let selected = $(this).find(":selected");

//     let hari = selected.data("hari");
//     let jam_mulai = roundTime(selected.data("jam_mulai"),15);
//     let jam_selesai = roundTime(selected.data("jam_selesai"),15);
//     let pegawai_id = $('#pegawai_id').val();
//     let ruangan_id = $('#ruangan_id').val();

//     getDisabledTime(hari, jam_mulai, jam_selesai, pegawai_id, ruangan_id);
// });

// function getDisabledTime(hari, jam_mulai, jam_selesai, pegawai_id, ruangan_id, setdisable=true) {
//     // console.log(hari, jam_mulai, jam_selesai, pegawai_id, ruangan_id, setdisable);
//     if (typeof hari == 'undefined') {return;}

//     $.ajax({
//         'type': "POST",
//         'dataType': 'JSON',
//         'url': baseUrl + "master/jadwal-dokter/check-jadwal",
//         'data': {
//             hari: hari,
//             pegawai_id: pegawai_id
//         },
//         'beforeSend': function () {

//         },
//         'success': function (res) {
//             if (res != '[]') {
//                 var listDisableHours = [];
//                 $.each(res, function (index, value) {

//                     var times = getTimes(res[index]['jam_mulai'], res[index]['jam_tutup']);
//                     $.each(times, function (i, item){
//                         listDisableHours.push(item);
//                     });

//                 });

//                 if (setdisable) {

//                     // console.log(listDisableHours); return;
//                     if(listDisableHours.length > 0){
//                         // console.log(listDisableHours);
//                         pickerMulai.set('disable',listDisableHours);
//                         pickerSelesai.set('disable',listDisableHours);
//                     }else{
//                         pickerMulai.set('enable',true);
//                         pickerSelesai.set('enable',true);
//                     }
//                 }
//                 if(jam_mulai != ''){
//                     pickerMulai.set('min',jam_mulai);
//                 }
//                 if(jam_selesai != ''){
//                     pickerMulai.set('max',jam_selesai);
//                     pickerSelesai.set('max',jam_selesai);
//                 }
//             }

//         },
//         'error': function (res) {

//         }
//     });
// }


// function getTimes(from, until) {
//     var until = Date.parse("01/01/2001 " + until);
//     var from = Date.parse("01/01/2001 " + from);
//     var max = (Math.abs(until - from) / (60 * 60 * 1000)) * 2;
//     var time = new Date(from);
//     var hours = [];
//     for (var i = 0; i <= max; i++) {
//         var hour = time.getHours();
//         var minute = time.getMinutes();
//         hours.push([hour, minute]);
//         time.setMinutes(time.getMinutes() + 30);
//     }
//     return hours;
// }

$('.btn-simpan').on('click', function(event, skipConfirmAjax=false, isSkipJkn=false){
  event.preventDefault();

  var bukapoli_mulai = $('#jadwalbukapoli_id option:selected').data('jam_mulai');
  var bukapoli_selesai = $('#jadwalbukapoli_id option:selected').data('jam_selesai');
  if(bukapoli_mulai == undefined || bukapoli_mulai == ''){
      if($('.alert-danger').length <= 1){
          new PNotify({
              title: "Simpan Gagal",
              text: "Jadwal Poli Belum Dipilih",
              addclass: "alert alert-danger alert-arrow-right alert-styled-right",
              type: "error",
          });
      }
      //docoNotification("error", "Simpan Gagal", "Jadwal Poli Belum Dipilih");
      return false;
  }
  if(bukapoli_selesai == undefined || bukapoli_selesai == ''){
      if($('.alert-danger').length <= 1){
          new PNotify({
              title: "Simpan Gagal",
              text: "Jadwal Poli Belum Dipilih",
              addclass: "alert alert-danger alert-arrow-right alert-styled-right",
              type: "error",
          });
      }
      //docoNotification("error", "Simpan Gagal", "Jadwal Poli Belum Dipilih");
      return false;
  }
  var regexhour = /^([01]\d|2[0-3]):?([0-5]\d)$/g;
  var val_jammulai = $('#jadwaldokterform-jadwaldokter_mulai').val();
  var val_jamtutup = $('#jadwaldokterform-jadwaldokter_tutup').val();
  if(val_jammulai.match(regexhour) && val_jamtutup.match(regexhour) && bukapoli_mulai.match(regexhour) && bukapoli_mulai.match(regexhour)){
      var parse_val_jammulai = Date.parse("01/01/2001 " + val_jammulai);
      var parse_val_jamtutup = Date.parse("01/01/2001 " + val_jamtutup);
      var time_val_jammulai = new Date(parse_val_jammulai);
      var time_val_jamtutup = new Date(parse_val_jamtutup);
      var val_jammulai_ms = time_val_jammulai.getTime();
      var val_jamtutup_ms = time_val_jamtutup.getTime();
      var diff_times = val_jamtutup_ms - val_jammulai_ms;
      var minutes_difference = diff_times / 1000 / 60;
      if(minutes_difference <=0){
          if($('.alert-danger').length <= 1){
              new PNotify({
                  title: "Simpan Gagal",
                  text: "Jam Tutup Tidak Boleh Kurang dari Sama Dengan Jam Mulai",
                  addclass: "alert alert-danger alert-arrow-right alert-styled-right",
                  type: "error",
              });
          }
          //docoNotification("error", "Simpan Gagal", "Jam Tutup Tidak Boleh Kurang dari Sama Dengan Jam Mulai");
          return false;
      }
      // else if(minutes_difference < 30){
      //     docoNotification("error", "Simpan Gagal", "Jadwal Dokter Tidak Boleh Kurang dari 30 Menit");
      //     return false;
      // }

      split_val_jammulai = val_jammulai.split(':');
      split_val_jamselesai = val_jamtutup.split(':');
      split_bukapoli_mulai = bukapoli_mulai.split(':');
      split_bukapoli_selesai = bukapoli_selesai.split(':');

      jam_mulai_minuteformat = (split_val_jammulai[0] * 60) + split_val_jammulai[1];
      jam_selesai_minuteformat = (split_val_jamselesai[0] * 60) + split_val_jamselesai[1];
      bukapoli_mulai_minuteformat = (split_bukapoli_mulai[0] * 60) + split_bukapoli_mulai[1];
      bukapoli_selesai_minuteformat = (split_bukapoli_selesai[0] * 60) + split_bukapoli_selesai[1];
      if(
          parseInt(split_val_jammulai[0]) >= parseInt(split_bukapoli_mulai[0]) &&
          parseInt(jam_mulai_minuteformat) >= parseInt(bukapoli_mulai_minuteformat) &&
          parseInt(split_val_jamselesai[0]) <= parseInt(split_bukapoli_selesai[0]) &&
          parseInt(jam_selesai_minuteformat) <= parseInt(bukapoli_selesai_minuteformat)
      ){
          if (isSkipJkn == true) {
              $('[name="JadwalDokterForm[is_skip_jkn]"]').val(1).change();
          }
          var _dataPost = $('#jadwaldokter-form').serializeArray();
          let total_kuota = {
              name: "JadwalDokterForm[kuota_total]",
              value: $('#jadwaldokterform-kuota_total').val()
          }
          _dataPost.push(total_kuota)


          $('.help-block').empty();

          $('.form-group').removeClass('has-error');
          $('#jadwaldokter-form').find('.has-error').removeClass('has-error');
          $().docoForm('click',{
              url     : $('#jadwaldokter-form').attr('action'),
              data    : _dataPost,
              skipConfirm : skipConfirmAjax,
              success : function(data) {
                  $('#modal_backdrop').modal('toggle');
                  $('.data-filter').trigger('click');
              },
              error : function(data) {
                  if (data.status == 422 && data.responseJSON?.response.data?.is_bpjs_error == true) {
                      swal({
                          title:"Proses BPJS Gagal!",
                          text:"Anda dapat melanjutkan simpan jadwal dokter tanpa update jadwal hfis",
                          type:"warning",
                          showConfirmButton: true,
                          confirmButtonText: 'Lanjutkan',
                          showCancelButton: true,
                          cancelButtonText: 'Batal'
                      }, function (i) {
                          if (i) {
                              $('.btn-simpan').trigger('click', [true, true]);
                          }
                      });
                  }
              }
          });
      }else{
          if($('.alert-danger').length <= 1){
              new PNotify({
                  title: "Simpan Gagal",
                  text: "Jadwal Dokter Tidak Dapat Diluar Jam Buka Poliklinik",
                  addclass: "alert alert-danger alert-arrow-right alert-styled-right",
                  type: "error",
              });
          }
          //docoNotification("error", "Simpan Gagal", "Jadwal Dokter Tidak Dapat Diluar Jam Buka Poliklinik");
          return false;
      }
  }else{
      if($('.alert-danger').length <= 1){
          new PNotify({
              title: "Simpan Gagal",
              text: "Format Jam Salah",
              addclass: "alert alert-danger alert-arrow-right alert-styled-right",
              type: "error",
          });
      }
      //docoNotification("error", "Simpan Gagal", "Format Jam Salah");
      return false;
  }
});

$('#modal_backdrop').on('shown.bs.modal', () => {
    var pegawai_id = $('#jadwaldokter_id').val();
    if(pegawai_id != ""){
        setTimeout(() => {
            $('#ruangan-id').trigger('depdrop:change')
        }, 0)
    }
})

// $('#jadwaldokter-form').docoForm('submit',{
//     success : function(data) {
//         this.formInput[0].reset();
//         window.location.href = $('.data-back').attr('href')
//     }
// });

// $("#jadwaldokter-form").docoForm("submit",{
//         success : function(data) {
//             $('#modal_backdrop').modal('toggle');
//             $('.data-filter').trigger('click');
//         }
//     });

$('#instalasi-id').on('change', function(){
    if($(this).val() != 1){
        if(!$('.content-maksantrian').hasClass('hidden')){
            $('.content-maksantrian').addClass('hidden')
        }
        if(!$('.jbp').hasClass('hidden')){
            $('.jbp').addClass('hidden')
        }
        if($('.jdh').hasClass('hidden')){
            $('.jdh').removeClass('hidden')
        }
    }else{
        if($('.content-maksantrian').hasClass('hidden')){
            $('.content-maksantrian').removeClass('hidden')
        }
        if($('.jbp').hasClass('hidden')){
            $('.jbp').removeClass('hidden')
        }
        if(!$('.jdh').hasClass('hidden')){
            $('.jdh').addClass('hidden')
        }
    }
});
// $(document).on('click', '.data-simpan', function () {
//     $('.btn-simpan').click();
// });
// $('#jadwaldokter-form').docoForm('submit', {
//     success: function (response) {
//         this.formInput[0].reset();
//         $('#modal_backdrop').modal('hide');
//         $('.data-filter').click();
//     }
// });

$(document).on('change', '#pegawai_id', function(){
    // $("#jadwalbukapoli_id").change();
});

// $(document).ready(function () {
//     $("#jadwalbukapoli_id").val($('#hide-jadwalbukapoli_id').val()).change();
// });
// $('#jadwalbukapoli_id').change(function() {
//     alert('tes');
// });

// var roundTime = (time, minutesToRound) => {

//     // Convert hours and minutes to time in minutes
//     time = (hours * 60) + minutes;

//     let rounded = Math.round(time / minutesToRound) * minutesToRound;
//     let rHr = ''+Math.floor(rounded / 60)
//     let rMin = ''+ rounded % 60

//     return rHr.padStart(2, '0')+':'+rMin.padStart(2, '0')
// }

$("#div_jumlah_loaddokter").hide()
$("#jadwaldokterform-is_loaddokter").change(function(e) {
    let konfig = '<?= $konfigReservasi ?>';
    if ($(this).prop('checked')){
        if($("#jadwaldokterform-jadwaldokter_mulai").val() !== '' && $("#jadwaldokterform-jadwaldokter_tutup").val() !== ''){
            PNotify.removeAll();
            $("#div_jumlah_loaddokter").show()
            kalkulasi();
        } else {
            docoNotification('warning', i18next.t('Perhatian'), i18next.t('Jam Mulai / Jam Tutup masih belum di input'));
        }
    }else{
        $(".kuotaAntrian").trigger('change');
        $("#div_jumlah_loaddokter").hide()
        // $("#jadwaldokterform-kuota_online").attr('disabled', false)
        // $("#jadwaldokterform-kuota_total").attr('disabled', false)
        if(konfig == 1) {
            $("#messageKuota").html('')
        }
    }
});
function kalkulasi(){
    var time_start = new Date();
    var time_end = new Date();
    var jam_mulai = $("#jadwaldokterform-jadwaldokter_mulai").val()+":00"
    var jam_tutup = $("#jadwaldokterform-jadwaldokter_tutup").val()+":00"
    var value_start = jam_mulai.split(':');
    var value_end = jam_tutup.split(':');
    let konfig = '<?= $konfigReservasi ?>';
    time_start.setHours(value_start[0], value_start[1], value_start[2], 0)
    time_end.setHours(value_end[0], value_end[1], value_end[2],0)
    var diff = (time_end - time_start) / 1000
    diff /= 60;

    var jmlmenit_isi = $("#jumlah_loaddokter").val()
    var hasil = Math.abs(Math.round(diff)) / parseInt(jmlmenit_isi)
    if(konfig == 1) {
        // $("#jadwaldokterform-kuota_online").val(hasil)
    } else {
        // $("#jadwaldokterform-kuota_total").val(hasil)
    }
    $("#jadwaldokterform-kuota_total").val(Math.floor(hasil))
}

$(".kuotaAntrian").change(function(e) {
    let hasil
    let kuotaOffline = $('#jadwaldokterform-maximumantrian').val()
    let kuotaOnline = $('#jadwaldokterform-kuota_online').val()
    let jd_kuota_total = $('#jadwaldokterform-kuota_total').val();

    if(kuotaOffline == null || kuotaOffline == 'NaN'){
        kuotaOffline = 0;
    }
    if(kuotaOnline == null || kuotaOnline == 'NaN'){
        kuotaOnline = 0;
    }
    hasil = parseInt(kuotaOffline) + parseInt(kuotaOnline);
    // if (typeof jd_kuota_total == 'undefined' || jd_kuota_total == '' || jd_kuota_total == null) {
    //     hasil = parseInt(kuotaOffline) + parseInt(kuotaOnline)
    // } else {
    //     hasil = jd_kuota_total
    // }

    let totalKuota = $("#jadwaldokterform-kuota_total")

    if($("#jadwaldokterform-is_loaddokter").is(':checked')) {
        if(hasil > parseInt(totalKuota.val())) {
            docoNotification('warning', i18next.t('Perhatian'), i18next.t('Jumlah Antrian Walkin + Antrian Reservasi lebih dari total kuota !'));
            // $('.btn-simpan').prop('disabled', true)
        } else {
            // $('.btn-simpan').prop('disabled', false)
            PNotify.removeAll();
        }
    } else {
        // $('.btn-simpan').prop('disabled', false)
        totalKuota.val(hasil)
    }
});

function checkTotal(x) {
    let konfigGabungKuota = '<?= $konfigReservasi ?>';
    let konfigPisahCabar = '<?= $konfigCaraBayar ?>';
    let kuotaBpjs, kuotaNonBpjs, totalKuota, info;

    if(parseInt(x) == 1) {
            kuotaBpjs = $('#jadwaldokterform-kuota_bpjs_total').val();
            kuotaNonBpjs = $('#jadwaldokterform-kuota_nonbpjs_total').val();
            totalKuota = $("#jadwaldokterform-kuota_total");
            info = 'Jumlah inputan melebihi total kuota!';
    } else if (parseInt(x) == 2) {
            kuotaBpjs = $('#jadwaldokterform-kuota_bpjs_offline').val();
            kuotaNonBpjs = $('#jadwaldokterform-kuota_nonbpjs_offline').val();
            totalKuota = $("#jadwaldokterform-maximumantrian");
            info = 'Jumlah inputan melebihi total kuota <?= $modelJadwalDokter->getAttributeLabel('maximumantrian') ?>';
    } else if (parseInt(x) == 3) {
            kuotaBpjs = $('#jadwaldokterform-kuota_bpjs_online').val();
            kuotaNonBpjs = $('#jadwaldokterform-kuota_nonbpjs_online').val();
            totalKuota = $("#jadwaldokterform-kuota_online");
            info = 'Jumlah inputan melebihi total kuota <?= $modelJadwalDokter->getAttributeLabel('kuota_online') ?>';
    }
    let hasil = parseInt(kuotaBpjs) + parseInt(kuotaNonBpjs);
    if(parseInt(konfigPisahCabar) == 1 || konfigPisahCabar == true) {
        totalKuota = totalKuota.val();
        if(totalKuota == null || totalKuota == 'NaN'){
            totalKuota = 0;
        }
        if(hasil > parseInt(totalKuota)) {
            docoNotification('warning', i18next.t('Perhatian'), i18next.t(`${info}`));
        }else{
            PNotify.removeAll();
        }
    }
}

$('#jadwaldokterform-is_bersedia').on('change', function(){
    if ($('#jadwaldokterform-is_bersedia').is(':checked')) {
        // $('#jadwaldokterform-jadwaldokter_mulai').val('00:01');
        // $('#jadwaldokterform-jadwaldokter_tutup').val('23:59');
        $('#jadwaldokterform-is_loaddokter').prop('checked', true);
        $('#jadwaldokterform-is_loaddokter').trigger('change');
    }
})
</script>



<?php
// $this->registerJs($this->render('_form.js'));
?>
