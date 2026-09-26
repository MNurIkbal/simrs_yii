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
    width: 50px;
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

    #modal_backdrop {
    z-index: 1051 !important;
}
</style>

<?php $form = ActiveForm::begin([
    'id' => 'jadwal-operasi-form', 
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'action' => '/igd/pemeriksaan-igd/set-jadwal-operasi',
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
            ->dropDownList([],
                [
                    'id' => 'operasi_dr_operator_id',
                    'class' => 'form-control input-sm select2', 
                    'prompt' => Yii::t('fe', '--Pilih--'),
                ]
            );
            ?>
            <?= $form->field($modelJadwalOperasi, 'dr_anestesi_id', ['labelOptions' => ['class' => 'text-right']]) 
            ->dropDownList([],
                [
                    'id' => 'operasi_dr_anestesi_id',
                    'class' => 'form-control input-sm select2', 
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
        <!-- <div class="text-center" id="loading-kalender">
            <h3>
                <i class="icon-spinner4 spinner position-center"></i>&nbsp;&nbsp;<b>Loading . . . </b>
            </h3>
        </div> -->
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

    $(document).ready(function() {
        $('#operasi_ruangan_id').val($('#penunjang_ruangan_id').val());

        $("#operasi_dr_operator_id").docoPaginationSelec2(
            // dapat disesuaikan dengan kebutuhan data / customize
            config = {
                placeholder : '-- Pilih --',      // custom placeholder (optional) default null
                _api : '/igd/master-api/list-all-new-dokter',   // get data
            }
        )

        $("#operasi_dr_anestesi_id").docoPaginationSelec2(
            // dapat disesuaikan dengan kebutuhan data / customize
            config = {
                placeholder : '-- Pilih --',      // custom placeholder (optional) default null
                _api : '/igd/master-api/list-all-new-dokter',   // get data
            }
        )
    });

    // $(document).on('change','#operasi_tgl_kirimpasien',function (event) {
    function getjadwaloperasi() {
        // event.preventDefault();
        // alert($(this).val()); return;
        $.ajax({
            // data : $('.advancedFilter').serializeArray(),
            data : {
                tanggal_operasi: $('#operasi_tgl_kirimpasien').val(),
                ruangan_id: $('#operasi_ruangan_id').val(),
            },
            url : '/igd/pemeriksaan-igd/get-data-jadwal-operasi',
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
        });
    }

    $('#jadwal-operasi-form').submit(function(event){
        event.preventDefault();
        var _value = $(this).serializeArray();
        // console.log(_value);
        $(this).docoForm("submit", { // /ranap/pemeriksaan-rawat-inap/set-jadwal-operasi
            data: _value,
            success: function (response) {
                // console.log(response);
                var cek_dok_anestesi = $('#operasi_dr_anestesi_id option:selected').val();
                $('#penunjang_tgl_kirimpasien').val($('#operasi_tgl_kirimpasien').val());
                $('#tgl_permintaan_info').html($('#operasi_tgl_kirimpasien').val());
                $('#jam_mulai_info').html($('#jadwaloperasiform-jam_mulai').val());
                $('#jam_selesai_info').html($('#jadwaloperasiform-jam_selesai').val());
                $('#dr_operator_info').html($('#operasi_dr_operator_id option:selected').text());
                $('#dr_anestesi_info').html(cek_dok_anestesi == "" ? "-" : $('#operasi_dr_anestesi_id option:selected').text());

                $('#penunjang_has_jadwal').val('1');

                $('.close-modal-jadwal').click();
            }
        });
    });
</script>