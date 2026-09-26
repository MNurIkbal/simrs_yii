<?php

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\web\View;
?>

<style type="text/css">

.zIndex { z-index: 1;  }
.fc th {
    padding: 12px 8px !important;
}
.fc-event {
    position : absolute !important;
}
.fc-resource-area {
    width : 200px;
}
.fc-head {
    background-color : #606060;
    border-color : #606060;
    color : #fff;
    font-size : 12px;
}
.fc-content {
    overflow: inherit !important;
    white-space: normal !important;
    font-size : 12px !important;
    font-weight: bold;
}
.datepicker>div{
    display:block;
}
</style>
<style>
    .square-sukses {
    height: 30px;
    width: 120px;
    background-color: #26A65B;
    color:#ffffff;
    padding: 5px 0 5px 10px;
    margin-right:20px;
    }
    .square-batal {
    height: 30px;
    width: 70px;
    background-color: #D24D57;
    color:#ffffff;
    padding: 5px 0 5px 10px;
    }
</style>

<?php $form = ActiveForm::begin([
    'id' => 'jadwal-operasi-form', 
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'action' => '/ranap/pemeriksaan-rawat-inap/set-jadwal-operasi',
    'enableClientValidation' => false,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]) ?>

<div class="modal-header bg-inverse">
    <button type="button" class="close close-modal-jadwal" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <div class='row'>
        <div class='col-md-6'>
            <?= $form->field($modelJadwalOperasi, 'tgl_kirimpasien', [
                    'labelOptions' => ['class' => 'text-right'],
                ])->textInput([
                    'class' => 'form-control input-sm pickadate',
                    'id' => 'operasi_tgl_kirimpasien',
                    'onchange' => 'getjadwaloperasi();'
                ]); 
            ?>

            <?= $form->field($modelJadwalOperasi, 'jam_mulai', [
                'labelOptions' => ['class' => 'text-right'],
            ])->textInput(['class' => 'form-control input-sm jam_mulai','readonly' => true]); 
            ?>

            <?= $form->field($modelJadwalOperasi, 'jam_selesai', [
                'labelOptions' => ['class' => 'text-right'],
            ])->textInput(['class' => 'form-control input-sm jam_selesai','readonly' => true]); 
            ?>
        </div>

        <div class='col-md-6'>
            <?= $form->field($modelJadwalOperasi, 'dr_operator_id', ['labelOptions' => ['class' => 'text-right']]) 
            ->dropDownList(
                ArrayHelper::map($list_dokter, 'pegawai_id', 'nama_pegawai'),
                [
                    'id' => 'operasi_dr_operator_id',
                    'class' => 'form-control input-sm', 
                    'prompt' => Yii::t('fe', '--Pilih--'),
                ]
            );
            ?>
            <?= $form->field($modelJadwalOperasi, 'dr_anestesi_id', ['labelOptions' => ['class' => 'text-right']]) 
            ->dropDownList(
                ArrayHelper::map($list_dokter, 'pegawai_id', 'nama_pegawai'),
                [
                    'id' => 'operasi_dr_anestesi_id',
                    'class' => 'form-control input-sm', 
                    'prompt' => Yii::t('fe', '--Pilih--'),
                ]
            );
            ?>

            <?= $form->field($modelJadwalOperasi, 'ruangan_id', [
                    'labelOptions' => ['class' => 'text-right']
                ])->hiddenInput(['id' => 'operasi_ruangan_id'])->label(false); ?>
        </div>
    </div>

    <br>
    <div class='row'>
        <div class="col-lg-12">
            <div class="pull-right">
                <button type="submit" id="operasi_set_jadwal" class="btn bg-teal">
                    <i class="fa fa-floppy-o"></i> <?= Yii::t('fe', 'Set Jadwal'); ?>
                </button>
            </div>
        </div>
    </div>

    <br>
    <div class='row'>
        <div class="text-center" id="loading-kalender">
            <h3>
                <i class="icon-spinner4 spinner position-center"></i>&nbsp;&nbsp;<b>Loading . . . </b>
            </h3>
        </div>
        <div class='col-md-12'>
            <div class='panel-list-jadwal' style="display: none;">
                
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end() ?>


<script type="text/javascript">

    var _events = [];
    var _resourse = [];

    $(".pickadate").pickadate({
        applyClass: "bg-slate-600",
        cancelClass: "btn-default",
        locale: {
            format: "DD-MMMM-YYYY"
        }
    });
    var $inputMulai = $(".jam_mulai").pickatime({
        format: "HH:i",
    });
    var pickerMulai = $inputMulai.pickatime('picker');
    var $inputSelesai = $(".jam_selesai").pickatime({
        format: "HH:i",
        onOpen: function () {
            var selectedTime = pickerMulai.get('select');
            if(selectedTime){
                this.set('min',
                    [selectedTime.hour,selectedTime.mins]
                );
                if(pickerMulai.get('disable').length>0){
                    var firstDis = this.get('disable')[0];
                    var lastDis = this.get('disable')[this.get('disable').length -1];
                    this.set('max',false);
                }
            }
        }
    });
    var pickerSelesai = $inputSelesai.pickatime('picker');

    function getjadwaloperasi(){
        event.preventDefault();
        $.ajax({
            // data : $('.advancedFilter').serializeArray(),
            data : {
                tanggal_operasi: $('#operasi_tgl_kirimpasien').val(),
                ruangan_id: $('#operasi_ruangan_id').val(),
            },
            url : '/ranap/pemeriksaan-rawat-inap/get-data-jadwal-operasi',
            dataType : 'json',
            type : 'POST',
            beforeSend: function() {
                $('#loading-kalender').show();
                $(".panel-list-jadwal").html('').hide();
            },
            success : function (data) {
                _resourse = data.ruangan;
                _events = data.data_jadwal;
                $('#loading-kalender').hide();
                var html = '';
                var disabledTimes = [];
                $(".panel-list-jadwal").html('');
                $.each(_events, function(index, el) {

                    if (el.status == 471) {
                        var startTime = el.start.split(':');
                        var endTime = el.end.split(':');
                        disabledTimes.push({from: [parseInt(startTime[0]), parseInt(startTime[1])], to: [parseInt(endTime[0]), parseInt(endTime[1])]});
                    }
                    html += "<div class='col-md-3'>"
                    var colorPanel = "panel-";
                    if (el.status == 470) { // belum disetujui
                        colorPanel += "default"
                    } else if (el.status == 471) { // sudah disetujui
                        colorPanel += "success"
                    } else if (el.status == 472) { // Batal
                        colorPanel += "warning"
                    } else if (el.status == 541) { // Ditolak
                        colorPanel += "danger"
                    }

                    html += "<div class='panel " + colorPanel + "'>"
                    html += "<div class='panel-heading'>" + el.label + "</div>"
                    html += "<div class='panel-body'>"
                    html += "<div>Jam Mulai : " + el.start + "</div>"
                    html += "<div>Jam Selesai : " + el.end + "</div>"
                    html += "<div>Dokter Operator : " + el.nama_dokter + "</div>"
                    html += "<div>Status : " + el.title.split(" - ")[2] + "</div>"
                    html += "</div></div></div>"
                });
                $(".panel-list-jadwal").html(html).show();

                pickerMulai.set('disable', false);
                pickerSelesai.set('disable', false);
                if (disabledTimes.length > 0) {
                    pickerMulai.set('disable', disabledTimes);
                    pickerSelesai.set('disable', disabledTimes);
                }
            },
            error : function (data) {
                return false;
            }
        }).done(function () {
            // $('#loading-kalender').hide();
            // $('#calendar').show();
        });
    }

    $(document).ready(function() {
        $('#operasi_ruangan_id').val($('#penunjang_ruangan_id').val());
        $('#calendar').fullCalendar({
            schedulerLicenseKey: 'nesimrs',
            defaultView: 'timelineDay',
            locale : 'id',
            defaultEventMinutes: 30,
            timeFormat : 'H:mm',
            height: 300,
            aspectRatio: 2,
            resourceLabelText: 'Ruangan / Jam',
            contentHeight: "300px",
            resources: function (callback) {
                callback(_resourse);
            },
            events: function (start, end, tz, callback) {
                callback(_events);
            },
            loading : function (isLoading, view) {

            }

        });
        

        // $('#calendar').fullCalendar({
        //     schedulerLicenseKey: 'nesimrs',
        //     refetchResourcesOnNavigate : true,
        //     locale : 'id',
        //     height: 650,
        //     defaultEventMinutes: 30,
        //     timeFormat : 'H:mm',
        //     editable: false, // enable draggable events
        //     droppable: false, // this allows things to be dropped onto the calendar
        //     aspectRatio: 2,
        //     scrollTime: '00:00', // undo default 6am scrollTime
        //     header: {
        //         right: 'timelineDay'
        //     },
        //     defaultView: 'timelineDay',
        //     resourceLabelText: 'Ruangan / Jam',
        //     eventClick: function(event) {
        //     },
        //     resources: function (callback) {
        //         callback(_resourse);
        //     },
        //     events: function (start, end, tz, callback) {
        //         callback(_events);
        //     },
        //     eventRender: function(event, element) {
        //         $(element).attr('data-dadang',event.dadang);
        //         // $(element).attr('style','z-index:9000;');
        //         $(element).attr('data-popup','popover');
        //         $(element).attr('data-trigger','focus');
        //         $(element).attr('tabindex','0');
        //         var _html = '<div class=\"\"><label class=\"\"><b>Jam Mulai : '+ event.jam_rencana_mulai +'</b></label></div>';
        //             _html += '<div class=\"\"><label class=\"\"><b>Jam Selesai : '+ event.jam_rencana_selesai +'</b></label></div>';
        //             _html += '<div class=\"\"><label class=\"\"><b>Dokter Operator : '+ event.nama_dokter +'</b></label></div>';
        //             if (event.status == 470) {
        //                 _html += '<hr><div class=\"text-center\">';
        //                 _html += '<a type=\"button\" class=\"btn btn-info btn-labeled btn-xs\" href=\"/ranap/pemeriksaan-rawat-inap/view-jadwal-operasi?id='+ event.id +'\">';
        //                 _html += '<b><i class=\"fa fa-eye\"></i></b>Lihat</a></div>';
        //             }
        //         $(element).attr('data-content',_html);
        //         $(element).attr('data-original-title',event.label);
        //         $(element).attr('data-placement','bottom');
        //         element.popover({
        //             trigger: 'focus',
        //             container:'body',
        //             title: '<div class=\"label label-lg label-primary col-xs-12\">Informasi Pasien</div>',
        //             html: true,
        //             template: '<div class=\"popover border-teal-400\"><div class=\"arrow\"></div><h3 class=\"popover-title bg-teal-400\"></h3><div class=\"popover-content\"></div></div>'

        //         });
        //     },
        //     loading : function (isLoading, view) {

        //     }

        // });
        $('.fc-toolbar, .fc-license-message, .fc-divider, #calendar, #loading-kalender').hide();
        $('.data-filter').trigger('click');
    });


    $('#jadwal-operasi-form').submit(function(event){
        event.preventDefault();
        var _value = $(this).serializeArray();
        // console.log(_value);
        $(this).docoForm("submit", { // /ranap/pemeriksaan-rawat-inap/set-jadwal-operasi
            data: _value,
            success: function (response) {
                // console.log(response);
                $('#penunjang_tgl_kirimpasien').val($('#operasi_tgl_kirimpasien').val());
                $('#tgl_permintaan_info').html($('#operasi_tgl_kirimpasien').val());
                $('#jam_mulai_info').html($('#jadwaloperasiform-jam_mulai').val());
                $('#jam_selesai_info').html($('#jadwaloperasiform-jam_selesai').val());
                $('#dr_operator_info').html($('#operasi_dr_operator_id option:selected').text());
                // $('#dr_anestesi_info').html($('#operasi_dr_anestesi_id option:selected').text());
                let anestesiInfo = $('#operasi_dr_anestesi_id option:selected').text();
                if(anestesiInfo === '--Pilih--'){
                    $('#dr_anestesi_info').html('-');
                }else{
                    $('#dr_anestesi_info').html(anestesiInfo);
                }

                $('#penunjang_has_jadwal').val('1');

                $('.close-modal-jadwal').click();
            }
        });
    });
</script>