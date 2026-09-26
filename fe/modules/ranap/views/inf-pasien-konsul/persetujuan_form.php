<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <div class="clear"></div>
    <div class="row">
            <?php 
                $form = ActiveForm::begin([
                    'id' => 'persetujuan-form', 
                    'options' => [
                            'class' => 'form-horizontal', 
                            'enableAjaxValidation' => true,
                            'role' => 'form',
                            'style' => 'padding: 0 10px 0 10px;'
                        ],
                ]); 
            ?>
           
            <div class="form-group">
                <div class="col-md-4">
                    <label class="control-label text-black" style="padding-left:0px;"><?= Yii::t('fe', 'No pendaftaran'); ?></label>
                </div>
                <div class="col-md-8">
                    <label class="container-label"><?php echo $getDataPermintaan['no_pendaftaran'] ?></label>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-4">
                    <label class="control-label text-black"  style="padding-left:0px;"><?= Yii::t('fe', 'No rekam medik'); ?></label>
                </div>
                <div class="col-md-8">
                    <label class="container-label"><?php echo $getDataPermintaan['no_rekam_medik'] ?></label>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-4">
                    <label class="control-label text-black"  style="padding-left:0px;"><?= Yii::t('fe', 'Nama pasien'); ?></label>
                </div>
                <div class="col-md-8">
                    <label class="container-label"><?php echo $getDataPermintaan['nama_pasien'] ?></label>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-4">
                    <label class="control-label text-black"  style="padding-left:0px;"><?= Yii::t('fe', 'Waktu permintaan'); ?></label>
                </div>
                <div class="col-md-8">
                    <label class="container-label"><?php echo date('d F Y H:i:s', strtotime($getDataPermintaan['waktu_permintaan'])); ?></label>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-4">
                    <label class="control-label text-black"  style="padding-left:0px;"><?= Yii::t('fe', 'Dokter yang dikonsul'); ?></label>
                </div>
                <div class="col-md-8">
                    <label class="container-label"><?php echo $getDataPermintaan['dok_konsul'] ?></label>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-4">
                    <label class="control-label text-black"  style="padding-left:0px;"><?= Yii::t('fe', 'Jenis konsul'); ?></label>
                </div>
                <div class="col-md-8">
                    <label class="container-label"><?php echo $getDataPermintaan['jenis_konsul_nama'] ?></label>
                </div>
            </div>
             <div class="form-group">
                <div class="col-md-4">
                    <label class="control-label text-black"  style="padding-left:0px;"><?= Yii::t('fe', 'Permintaan konsultasi'); ?></label>
                </div>
                <div class="col-md-8">
                    <label class="container-label"><?php echo $getDataPermintaan['ket_konsul'] ?></label>
                </div>
            </div>
             <div class="form-group">
                    <div class="col-md-4">
                        <label class="control-label text-black" style="padding-left:0px;"><?= Yii::t('fe', 'Waktu persetujuan'); ?></label>
                    </div>
                    <div class="col-md-8">
                        <b><span id="waktu-permintaan"></span></b>
                        <?= Html::hiddenInput('PermintaanKonsulForm[waktu_persetujuan]', $model->waktu_persetujuan);?>
                    </div>
                </div>
            <div class="form-group">
                <div class="col-md-4 required">
                    <label class="control-label text-black"  style="padding-left:0px;"><?= Yii::t('fe', 'Persetujuan'); ?></label>
                </div>
                <div class="col-md-8">
                    <?=$form->field($model, 'status_konsul')->dropDownList($persetujuan, 
                        [ 'class' => 'form-control input-sm select2', 
                            'prompt' => Yii::t('fe', '--Pilih--'),
                             'id' => 'persetujuan',
                    ])?>

                </div>
            </div>
            <?= Html::hiddenInput('PermintaanKonsulForm[permintaankonsul_id]', $getDataPermintaan['permintaankonsul_id'] );?>
    </div>
    <hr>
    <div class="modal-footer">
        <?=Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['class' => 'btn btn bg-teal btn-sm','id'=>'btn-setuju']); ?>
    </div>
    <?php
        ActiveForm::end();
    ?>
    <br>
</div>
<script>
    $(document).ready(function () {
       $('label[for="persetujuan"]').hide();
        // $("#persetujuan").select2 ('container').find ('.select2-search').addClass ('hidden') ; 
    });
    $("#btn-setuju").on("click", function(event) {
        event.preventDefault();
        var data = $("#persetujuan-form").serializeArray();
        $(this).docoForm('click',{
            url: '/ranap/inf-pasien-konsul/setujui',
            data: data,
            success : function(res) {
                var form = $("#persetujuan-form");
                form[0].reset();
                table.draw();
                $("#persetujuan").val(0);           
                $("#modal_backdrop").modal('toggle');
                //_afterSave()           
            }
        });
    });

    // date & time
    function getCurrentDate(){
        var d = new Date();
        var date = d.getDate(); 
        var month = d.getMonth(); 
        var montharr = ["Jan","Feb","Mar","April","May","June","July","Aug","Sep","Oct","Nov","Dec"]; 

        month = montharr[month];

        var year = d.getFullYear(); 
        var day = d.getDay();

        var dayarr =["Sun","Mon","Tues","Wed","Thurs","Fri","Sat"];
         
        day = dayarr[day];
        return date +" "+ month +" "+ year;
    }
    function clock() {
        var d = new Date();
        var hour = checkTime(d.getHours());
        var min = checkTime(d.getMinutes());
        var sec = checkTime(d.getSeconds());
        var ampm = (hour >= 12) ? 'PM' : 'AM';
        var currentTime = hour +":"+ min +":"+ sec;

        // set time
        document.getElementById ("waktu-permintaan").innerHTML = getCurrentDate() + '  ' + currentTime;
        $('input[name="PermintaanKonsulForm[waktu_permintaan]"]').val(  d.getFullYear()+"-"+d.getMonth()+"-"+d.getDate()+" "+hour+":"+min+":"+sec);
    }
    function checkTime(i) {
        if (i < 10) {i = "0" + i;}  // add zero in front of numbers < 10
        return i;
    }

    setInterval(clock, 1000);
    // document.getElementById ("waktu-permintaan").innerHTML = getCurrentDate() + ' ' + clock();

    function startTime() {
        alert('asdas');
        var today = new Date();
        var h = today.getHours();
        var m = today.getMinutes();
        var s = today.getSeconds();
        m = checkTime(m);
        s = checkTime(s);

        $('#waktu-permintaan').innerHTML =  h + ":" + m + ":" + s;
        var t = setTimeout(startTime, 500);
    }

</script>
