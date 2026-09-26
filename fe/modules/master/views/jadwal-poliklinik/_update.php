<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use yii\web\View;
use app\components\DocoConstants;
?>

<?php 
$form = ActiveForm::begin([
    'id' => 'ajax-form', 
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]); 
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <?=
        $form->field($model,'jadwalbukapoli_id')->hiddenInput()->label(false);
    ?>
    <?=$form
        ->field($model, 'ruangan_id', ['labelOptions' => ['class' => 'text-left']])
        ->dropDownList($ruangan, [
            'class' => 'form-control input-sm select2',
            'prompt' => \Yii::t('fe', 'Pilih'),
        ]);
    ?>

    <?=$form
        ->field($model, 'hari', ['labelOptions' => ['class' => 'text-left']])
        ->dropDownList($hari, [
            'class' => 'form-control input-sm select2',
            'prompt' => \Yii::t('fe', 'Pilih'),
        ]);
    ?>
    
    <?=
    $form->field($model, 'shift_id', ['labelOptions' => ['class' => 'text-left']])->dropDownList($shift, [
            'options' => $shift_options,
            'class' => 'form-control input-sm select2 shift',
            'id' => 'shift',
            'prompt' => \Yii::t('fe', 'Pilih'),
            'value' => $model->shift_id
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
    
    <!-- <div class="form-group">
        <label for="is_active" class="col-lg-2 control-label">
            <?= Yii::t('fe', 'Status'); ?>
        </label>
        <div class="col-lg-6">
            <?=$form->field($model, 'is_active')->checkbox()?>
        </div>
    </div> -->
    
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
            <?= Html::button("<i class='fa fa-floppy-o'> ". Yii::t('fe', 'Update')."</i>", ['class' => 'btn bg-teal','id'=>'btn-submit-update-jadwal']) ?>
            <?= Html::button("<i class='fa fa-arrow-left'> ". Yii::t('fe', 'Kembali')."</i>",[
                                'class' => 'btn bg-slate',
                                'data-dismiss' => 'modal'
                                ]); ?>
</div>
<?php ActiveForm::end(); ?>
<script type="text/javascript">
 
    var v_max_antrian = null;
    var v_ruangan = null;
    var v_hari = null;
    $(document).on('click','#btn-submit-update-jadwal', function(e){
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
            //tableJadwalPoliklinik.draw();
            $('.data-filter').trigger('click');
        }
    });

    $(document).ready(function() {

        var hari = $('#jadwalpoliklinikform-hari').val();
        var ruangan_id = $('#jadwalpoliklinikform-ruangan_id').val();
        var data_shift = $('#shift option:selected').data();

        if(data_shift.data.id != '' && data_shift != undefined){
            $('#jamMulai').val(convToTime(data_shift.jam_awal));
            $('#jamTutup').val(convToTime(data_shift.jam_akhir));
            $('#jamTutup').trigger('change');
            $('#jamMulai').prop('readonly',true);
            $('#jamTutup').prop('readonly',true);
        }else{
            var jam_awal = convToTime($('#jamMulai').val());
            var jam_akhir = convToTime($('#jamTutup').val());
            $('#jamMulai').val(jam_awal);
            $('#jamTutup').val(jam_akhir);
            $('#jamTutup').trigger('change');
            $('#jamMulai').prop('readonly',false);
            $('#jamTutup').prop('readonly',false);
        }

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

        if($('#shift').val() != ''){
            $('#jamMulai').prop('readonly',true);
            $('#jamTutup').prop('readonly',true);
        }
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
        });
        // var f_jadwalbuka_id = $('#jadwalpoliklinikform-jadwalbukapoli_id').val();
        // if(f_jadwalbuka_id != ''){
        //     $.ajax({
        //         'type': "POST",
        //         'dataType': 'JSON',
        //         'url': baseUrl + "master/jadwal-poliklinik/check-jadwal-dokter",
        //         'data': {
        //             id: f_jadwalbuka_id
        //         },
        //         'success': function(res){
        //             console.log(res)
        //             if(res.mulai != null && res.selesai != null){
        //             }
        //         },
        //     });
        // }

        // v_max_antrian = $("#jadwalpoliklinikform-maxantiran_poli").val();

        // v_ruangan = $("#jadwalpoliklinikform-ruangan_id").val();
        // v_hari = $("#jadwalpoliklinikform-hari").val();

        // t_ruangan = $("#select2-jadwalpoliklinikform-ruangan_id-container").html();
        // t_hari = $("#select2-jadwalpoliklinikform-hari-container").html();

        function arraysEqual(arr1, arr2) {
            if (JSON.stringify(arr1) === JSON.stringify(arr2)) {
                return true;
            }
        }

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
    });

        

    // document.getElementById("reset").onclick = function(){
       
    //     $("#jadwalpoliklinikform-maxantiran_poli").val(v_max_antrian);
    //     $("#jadwalpoliklinikform-ruangan_id").val(v_ruangan);
    //     $("#jadwalpoliklinikform-hari").val(v_hari);

    //     $("#select2-jadwalpoliklinikform-ruangan_id-container").html(t_ruangan);
    //     $("#select2-jadwalpoliklinikform-hari-container").html(t_hari
    //         );
    // };

    function convToTime(time) {
        console.log(time);
        time = time.split(/:/);
        return time[0] + ":" + time[1];
    }

</script>