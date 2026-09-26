<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\TimePicker;
use yii\web\View;
use app\components\DocoConstants;
?>

<?php 
$form = ActiveForm::begin([
    'id' => 'ajax-form', 
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]); 
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <div class="alert alert-danger alert-bordered alert-modal" style="display:none">
        <button type="button" class="close" data-dismiss="alert"><span>×</span><span class="sr-only">Close</span></button>
        <span class="text-semibold alert-message"></span>
    </div>

    <?=$form
        ->field($model, 'ruangan_id', ['labelOptions' => ['class' => 'text-left']])
        ->dropDownList($ruangan, [
            'class' => 'form-control input-sm select2 rid',
            'prompt' => \Yii::t('fe', 'Pilih'),
        ])->label(Yii::t('fe', 'Ruangan'));
    ?>
    <?=$form
        ->field($model, 'hari', ['labelOptions' => ['class' => 'text-left']])
        ->dropDownList($hari, [
            'class' => 'form-control input-sm select2 rday',
            'prompt' => \Yii::t('fe', 'Pilih'),
        ]);
    ?>

    <?=
        $form->field($model, 'shift_id', ['labelOptions' => ['class' => 'text-left']])->dropDownList($shift, [
            'options' => $shift_options,
            'class' => 'form-control input-sm select2 shift',
            'id' => 'shift',
            'prompt' => \Yii::t('fe', 'Pilih'),
        ]);
    ?>

    <?php
        echo $form->field($model, 'jam_mulai')->textInput([
            'id'=>'jamMulai',
            'class' => 'form-control input-sm jam_mulai',
            'data-mask' => '99:99'
        ]); 
    ?>
    <?php
        echo $form->field($model, 'jam_tutup')->textInput([
            'id'=>'jamTutup',
            'class' => 'form-control input-sm jam_mulai',
            'data-mask' => '99:99'
        ]); 
    ?>

    <?=$form->field($model, 'waktu_pelayanan', ['labelOptions' => ['class' => 'text-left']])->textInput(['class' => 'form-control input-sm', 'id' =>'jamBuka', 'readonly' => 'readonly']); ?>
    
    <?php if ($konfig == DocoConstants::VAR_ID_KUOTA_ANTRIAN_POLIKLINIK): ?>
    <?php
        echo $form->field($model, 'maxantrian_poli')->textInput([
            'class' => 'form-control input-sm docoNumberOnly',
        ]);
    ?>
    <?php
        echo $form->field($model, 'kuota_online')->textInput([
            'class' => 'form-control input-sm docoNumberOnly',
        ]); 
    ?>
    <?php endif ?>

    <?= $form->field($model, 'is_active', [
        'horizontalCssClasses' => [
            'label' => 'text-left control-label col-sm-4',
            'wrapper' => 'col-md-8'
        ]
    ])->checkbox(['label' => 'Aktif'])->label(Yii::t('fe', 'Status')) ?>
</div>
<div class="modal-footer">
            <?= Html::button("<i class='fa fa-floppy-o'> ". Yii::t('fe', 'Simpan')."</i>", ['class' => 'btn bg-teal','id'=>'btn-submit-jadwal']) ?>
            <?= Html::button("<i class='fa fa-arrow-left'> ". Yii::t('fe', 'Kembali')."</i>",[
                                'class' => 'btn bg-slate',
                                'data-dismiss' => 'modal'
                                ]); ?>
</div>
<?php ActiveForm::end(); ?>
<script type="text/javascript">
    $(document).on('click','#btn-submit-jadwal', function(e){
        e.preventDefault();

        var regexhour = /^([01]\d|2[0-3]):?([0-5]\d)$/g;
        var val_jammulai = $('#jamMulai').val();
        var val_jamtutup = $('#jamTutup').val();

        if(val_jammulai.match(regexhour) && val_jamtutup.match(regexhour)){
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
                return false;
            }
            // else if(minutes_difference < 30){
            //     if($('.alert-danger').length <= 1){
            //         new PNotify({
            //             title: "Simpan Gagal", 
            //             text: "Jadwal Buka Poli Tidak Boleh Kurang dari 30 Menit",
            //             addclass: "alert alert-danger alert-arrow-right alert-styled-right",
            //             type: "error",
            //         });
            //     }
            //     return false;
            // }

            $('#ajax-form').submit();
        }else{
            if($('.alert-danger').length <= 1){
                new PNotify({
                    title: "Simpan Gagal", 
                    text: "Format Jam Salah",
                    addclass: "alert alert-danger alert-arrow-right alert-styled-right",
                    type: "error",
                });
            }
            return false;
        }
    });
    $("#ajax-form").docoForm("submit",{
        success : function(data) {
            $('#modal_backdrop').modal('toggle');
            // tableJadwalPoliklinik.draw();
            $('.data-filter').trigger('click');
        }
    });
    $(document).ready(function() {
        $('#jamMulai').val('');
        $('#jamTutup').val('');
        // function cekJadwalPoli(hari,ruangan_id,callback){
        //     return $.ajax({
        //         'type': "POST",
        //         'dataType': 'JSON',
        //         'url': baseUrl + "master/jadwal-poliklinik/check-jadwal-poli",
        //         'data':{
        //             hari:hari,
        //             ruangan_id:ruangan_id
        //         },
        //         'success':callback
        //     });
        // }
        $(document).on('change','#jadwalpoliklinikform-ruangan_id',function(){
            if($('#jadwalpoliklinikform-hari').val() != ''){
                var hari = $('#jadwalpoliklinikform-hari').val();
                var ruangan_id = $('#jadwalpoliklinikform-ruangan_id').val();
                // cekJadwalPoli(hari,ruangan_id,function (data){
                //     if(data.list_disable){
                //         $('#jamMulai').val('');
                //         $('#jamTutup').val('');
                //         $("#jamBuka").val('')
                //     } else {
                //         if ($('shift').val() != '') {
                //             $('.shift').trigger('change')
                //         }
                //     }
                // });
            }
        });
        $(document).on('change','#jadwalpoliklinikform-hari',function(){
            if($('#jadwalpoliklinikform-ruangan_id').val() != ''){
                var hari = $('#jadwalpoliklinikform-hari').val();
                var ruangan_id = $('#jadwalpoliklinikform-ruangan_id').val();
                // cekJadwalPoli(hari,ruangan_id,function (data){
                //     if(data.list_disable){
                //         $('#jamMulai').val('');
                //         $('#jamTutup').val('');
                //         $("#jamBuka").val('')
                //     } else {
                //         if ($('shift').val() != '') {
                //             $('.shift').trigger('change')
                //         }
                //     }
                // });
            }
        });

        $( "#reset" ).click(function() {
          clear();
        });

        $(document).on('change','#jamMulai',function(){
            var val_mulai = $(this).val();
            var val_tutup = $('#jamTutup').val();

            if($(this).val() == ''){
                $('#jamBuka').val('');
            }else{
                $('#jamBuka').val(val_mulai+' - '+val_tutup);
            }
        });

        $(document).on('change','#jamTutup',function(){
            var val_mulai = $('#jamMulai').val();
            var val_tutup = $(this).val();

            if($(this).val() == ''){
                $('#jamBuka').val('');
            }else{
                $('#jamBuka').val(val_mulai+' - '+val_tutup);
            }
        });

        $(document).on('change', '#shift', function() {
            var hari = $('#jadwalpoliklinikform-hari').val();
            var ruangan_id = $('#jadwalpoliklinikform-ruangan_id').val();
            var data_shift = $('#shift option:selected').data();

            if($(this).val() != '' && data_shift != undefined){
                $('#jamMulai').val(convToTime(data_shift.jam_awal));
                $('#jamTutup').val(convToTime(data_shift.jam_akhir));
                $('#jamTutup').trigger('change');
                $('#jamMulai').prop('readonly',true);
                $('#jamTutup').prop('readonly',true);
            }else{
                $('#jamMulai').val('');
                $('#jamTutup').val('');
                $('#jamBuka').val('');
                $('#jamMulai').prop('readonly',false);
                $('#jamTutup').prop('readonly',false);
            }
            // if ($('#jadwalpoliklinikform-ruangan_id').val() != '' && $('#jadwalpoliklinikform-hari').val() != '' && $(this).val() != '') {
            //     $.ajax({
            //         type: "POST",
            //         dataType: 'JSON',
            //         url: baseUrl + "master/jadwal-poliklinik/get-data-shift",
            //         data: {
            //             shift_id: $(this).val(),
            //         },
            //         success: function(res) {
            //             cekJadwalPoli(hari,ruangan_id,function (data){
            //                 let list_disable = data.list_disable;
            //                 let check, checkJamAwal, checkJamAkhir, countAwal, countAkhir;
            //                 if (data.list_disable){
            //                     check = list_disable.includes(res.j_awal);
            //                     $.each(list_disable, function(key, val) {
            //                         checkJamAwal = arraysEqual(val, res.j_awal);
            //                         if (checkJamAwal) {
            //                             countAwal = true;
            //                         }
            //                         checkJamAkhir = arraysEqual(val, res.j_akhir);
            //                         if (checkJamAkhir) {
            //                             countAkhir = true;
            //                         }
                                    
            //                     });

            //                     if (!countAwal && !countAkhir) {
            //                         $('#jamMulai').val(res.shift_jamawal);
            //                         $('#jamTutup').val(res.shift_jamakhir);
            //                         $("#jamBuka").val(res.shift_jamawal+' - '+res.shift_jamakhir)
            //                     } else {
            //                         $('#jamMulai').val('');
            //                         $('#jamTutup').val('');
            //                         $("#jamBuka").val('')
            //                     }
            //                 } else {
            //                     $('#jamMulai').val(res.shift_jamawal);
            //                     $('#jamTutup').val(res.shift_jamakhir);
            //                     $("#jamBuka").val(res.shift_jamawal+' - '+res.shift_jamakhir)
            //                 }
            //             });
            //         }
            //     });
            // }
        });

    })

    function clear() {
            $("#jamMulai").val('');
            $("#jamTutup").val('');
            $("#jamBuka").val('');
            $("#jadwalpoliklinikform-maxantiran_poli").val('');
            $("#select2-jadwalpoliklinikform-ruangan_id-container").html('-');
            $("#select2-jadwalpoliklinikform-hari-container").html('-');
        }
    
    function arraysEqual(arr1, arr2) {
        if (JSON.stringify(arr1) === JSON.stringify(arr2)) {
            return true;
        }
    }

    function convToTime(time) {
        time = time.split(/:/);
        return time[0] + ":" + time[1];
    }
</script>