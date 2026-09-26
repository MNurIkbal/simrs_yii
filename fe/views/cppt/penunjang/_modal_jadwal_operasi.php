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
.select2-dropdown {
  z-index: 9001;
}
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
    .kotak{
        width:100%;
        height:300px;
        padding-left:9px;
        padding-right:9px;
        overflow-x:scroll;
    }
</style>

<?php $form = ActiveForm::begin([
    'id' => 'jadwal-operasi-form', 
    'action' => '/rajal/pemeriksaan/set-jadwal-operasi',
    'enableClientValidation' => false,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]) ?>

<div class="modal-header bg-inverse">
    <button type="button" class="close close-modal-jadwal" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($modelJadwalOperasi, 'tgl_kirimpasien', [
                    'labelOptions' => ['class' => 'text-right'],
                ])->textInput([
                    'class' => 'form-control input-sm',
                    'id' => 'operasi_tgl_kirimpasien',
                    'onchange' => 'getjadwaloperasi();'
                ]); 
            ?>
        </div>
        <div class="col-md-6 select2-md">
            <?= $form->field($modelJadwalOperasi, 'dr_operator_id', ['labelOptions' => ['class' => 'text-right']]) 
                ->dropDownList([],
                    [
                        'id' => 'operasi_dr_operator_id',
                        'class' => 'form-control input-sm', 
                        'prompt' => Yii::t('fe', '--Pilih--'),
                    ]
                );
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <?= $form->field($modelJadwalOperasi, 'jam_mulai', [
                'labelOptions' => ['class' => 'text-right'],
            ])->textInput(['class' => 'form-control input-sm jam_mulai','readonly' => true]); 
            ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($modelJadwalOperasi, 'jam_selesai', [
                'labelOptions' => ['class' => 'text-right'],
            ])->textInput(['class' => 'form-control input-sm jam_selesai','readonly' => true]); 
            ?>
        </div>
        <div class="col-md-6 select2-md">
            <?= $form->field($modelJadwalOperasi, 'dr_anestesi_id', ['labelOptions' => ['class' => 'text-right']]) 
                ->dropDownList([],
                    [
                        'id' => 'operasi_dr_anestesi_id',
                        'class' => 'form-control input-sm', 
                        'prompt' => Yii::t('fe', '--Pilih--'),
                    ]
                );
            ?>

            <?= $form->field($modelJadwalOperasi, 'ruangan_id', [
                'labelOptions' => ['class' => 'text-right']
                ])->hiddenInput(['id' => 'operasi_ruangan_id'])->label(false); 
            ?>
        </div>
    </div>
    <div class='row'>
        <div class="col-lg-12">
            <div class="pull-right">
                <button type="submit" id="operasi_set_jadwal" class="btn btn-xs btn-labeled btn-info">
                    <b>
                        <i class="fa fa-floppy-o"></i>
                    </b> <?= Yii::t('fe', 'Set Jadwal'); ?>
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
            <div class='panel'>
                <div class='panel-heading'>
                    <div style=font-size:17px id="judul-panel">Informasi Jadwal Operasi</div>
                </div>
                <div class='panel-body'>
                    <div class='panel-list-jadwal-temp'>
                        <div style=font-size:10px;text-align:center;padding-top:10px; id="isi-panel">Belum ada tanggal yang dipilih</div>
                    </div>
                    <div class='panel-list-jadwal' style="display: none;">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end() ?>

<script type="text/javascript">
    var operator =  sessionStorage.getItem("operator") == null ? _dokterPerujukNama : sessionStorage.getItem("operator");
    var anestesi =  sessionStorage.getItem("anestesi");
    var mulai =  sessionStorage.getItem("mulai");
    var selesai =  sessionStorage.getItem("selesai");
    var tgl =  sessionStorage.getItem("tgl");
    var _events = [];
    var _resourse = [];

    $("#operasi_tgl_kirimpasien").pickadate({
        format: 'dd-mm-yyyy',
        disable: [
            {from: [0,0,0], to: new Date((new Date()).valueOf()-1000*60*60*24)}
        ]
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
        if(tgl == null){
            event.preventDefault();
        }
        if( $('#order-penunjang-form').attr('action').includes('rajal') ){
            var urlto = '/igd/pemeriksaan-igd/get-data-jadwal-operasi'
        } else if($('#order-penunjang-form').attr('action').includes('ranap')){
            var urlto = '/ranap/pemeriksaan-rawat-inap/get-data-jadwal-operasi'
        } else {
            var urlto = '/igd/pemeriksaan-igd/get-data-jadwal-operasi'
        }
        $.ajax({
            // data : $('.advancedFilter').serializeArray(),
            data : {
                tanggal_operasi: $('#operasi_tgl_kirimpasien').val(),
                ruangan_id: $('#operasi_ruangan_id').val(),
            },
            url : urlto,
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
                
                var disabledTimes = [];
                $(".panel-list-jadwal").html('');
                var count = 0;
                var tanggal = $('#operasi_tgl_kirimpasien').val()
                $('#judul-panel').text('Informasi Jadwal Operasi - Tanggal ' + tanggal + '')
                if(_events.length == 0){
                    $('.panel-list-jadwal-temp').show();
                    $('#isi-panel').text('Tidak ada jadwal operasi')
                }
                else{
                    var html = '';
                    $('.panel-list-jadwal-temp').hide();
                    html += "<div class=kotak>" 
                    $.each(_events, function(index, el) {
                        if(count % 3 == 0){
                            html += "<div class='row'>"  
                        }
                        html += "<div class='col-md-4'>"
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

                        if (typeof el.jam_rencana == 'undefined') {
                            el.jam_rencana = '-';
                        }
                        if (typeof el.nama_dokter == 'undefined') {
                            el.nama_dokter = '-';
                        }
                        if (typeof el.kegiatanoperasi_nama == 'undefined') {
                            el.kegiatanoperasi_nama = '-';
                        }
                        if (typeof el.ruangan_nama == 'undefined') {
                            el.ruangan_nama = '-';
                        }
                        if (typeof el.pegawai_approve == 'undefined') {
                            el.pegawai_approve = '-';
                        }
                        if (typeof el.tgl_approve == 'undefined') {
                            el.tgl_approve = '-';
                        }

                        html += "<div class='panel " + colorPanel + "'>"
                        html += "<div class='panel-heading'>" + el.label + "</div>"
                        html += "<div class='panel-body'>"
                        html += "<div>Jam : " + el.jam_rencana + "</div>"
                        html += "<div>Dokter Operator : " + el.nama_dokter + "</div>"
                        html += "<div>Jenis Operasi : " + el.kegiatanoperasi_nama + "</div>"
                        html += "<div>Ruangan Operasi : " + el.ruangan_nama + "</div>"
                        html += "<div>Status : " + el.title.split(" - ")[2] + "</div>"
                        html += "<div>Pegawai Approve : " + el.pegawai_approve + "</div>"
                        html += "<div>Tanggal Approve : " + el.tgl_approve + "</div>"
                        html += "</div></div></div>"
                        if(count % 3 == 2){
                            html += "</div>"  
                        }
                        count +=1;
                    });
                    html += "</div>" 
                }
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
        $("#operasi_dr_operator_id").docoPaginationSelec2(
            config = {
                placeholder : "Pilih",  
                _api : "/rajal/master-api/list-dokter",
            }
        );
        $("#operasi_dr_anestesi_id").docoPaginationSelec2(
            config = {
                placeholder : "Pilih",  
                _api : "/rajal/master-api/list-dokter",
            }
        );
        $('#operasi_ruangan_id').val($('#instruksipenunjangform-ruangan_id').val());

        $('.fc-toolbar, .fc-license-message, .fc-divider, #calendar, #loading-kalender').hide();
        $('.data-filter').trigger('click');
     
        $('#operasi_dr_operator_id').append(new Option(operator, _dokterPerujukId, true, true)).trigger('change')
        if(anestesi != null){
        $('#operasi_dr_anestesi_id').append(new Option(anestesi, _dokterPerujukId, true, true)).trigger('change')
        }
        $('.jam_mulai').val(mulai).trigger('change')
        $('.jam_selesai').val(selesai).trigger('change')
        $('#operasi_tgl_kirimpasien').val(tgl).trigger('change')
      
    });

    $('#jadwal-operasi-form').submit(function(event){
        event.preventDefault();
        if( $('#operasi_tgl_kirimpasien').val() == '' ){
            $('#operasi_tgl_kirimpasien').parent('.form-group').find('.help-block').html(`<p class="text-error" style="color: red">Tanggal Operasi Tidak Boleh Kosong!</p>`)
        }

        if( $('.jam_mulai').val() == '' ){
            $('.jam_mulai').parent('.form-group').find('.help-block').html(`<p class="text-error" style="color: red">Jam Mulai Tidak Boleh Kosong!</p>`)
        }

        if( $('.jam_selesai').val() == '' ){
            $('.jam_selesai').parent('.form-group').find('.help-block').html(`<p class="text-error" style="color: red">Jam Selesai Tidak Boleh Kosong!</p>`)
        }

        if( $('#operasi_dr_operator_id').val() == '' ){
            $('#operasi_dr_operator_id').parent('.form-group').find('.help-block').html(`<p class="text-error" style="color: red">Dokter Operator Tidak Boleh Kosong!</p>`)
        }

        if($('.jam_mulai').val() >= $('.jam_selesai').val()){
            $('.jam_mulai').parent('.form-group').find('.help-block').html(`<p class="text-error" style="color: red">Jam Mulai Tidak Boleh Lebih Atau Sama Dengan Jam Selesai!</p>`)
        }

        if( $('.text-error').length ){
            return false
        }
        $.each($('#jadwal-operasi-form').serializeArray(), (k,v) => {
            if(v.name != '_csrf'){
                let _name = v.name.replace('JadwalOperasiForm', '').replace('[','').replace(']', '')
                _jadwalOperasi[_name] = v.value
            }
        })

        $('#modal-lab .modal-content').find('#tgl_permintaan_info').text( ': ' + $('#operasi_tgl_kirimpasien').val() )
        $('#modal-lab .modal-content').find('#jam_mulai_info').text(  ': ' + $('.jam_mulai').val() )
        $('#modal-lab .modal-content').find('#jam_selesai_info').text(  ': ' + $('.jam_selesai').val() )
        $('#modal-lab .modal-content').find('#jam_selesai_info').text(  ': ' + $('.jam_selesai').val() )
        $('#modal-lab .modal-content').find('#dr_operator_info').text(  ': ' + $('#operasi_dr_operator_id :selected').text() )

        if( $('#operasi_dr_anestesi_id').val() != ''){
            $('#modal-lab .modal-content').find('#dr_anestesi_info').text(  ': ' + $('#operasi_dr_anestesi_id :selected').text() )
        }
        sessionStorage.setItem("tgl",$('#operasi_tgl_kirimpasien').val());
        sessionStorage.setItem("mulai",$('.jam_mulai').val());
        sessionStorage.setItem("selesai",$('.jam_selesai').val());
        sessionStorage.setItem("operator",$('#operasi_dr_operator_id :selected').text());
        sessionStorage.setItem("anestesi",$('#operasi_dr_anestesi_id :selected').text());
        $('#modal-jadwal-dokter').find('.modal-header').find('.close-modal-jadwal').click()

    });
</script>