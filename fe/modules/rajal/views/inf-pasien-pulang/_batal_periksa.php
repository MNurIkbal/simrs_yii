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
        <div class="col-md-12">
            <div class="col-md-6">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-5">
                            <label class="control-label text-black l-label" style="padding-left:0px;"><?= Yii::t('fe', 'Tgl pendaftaran'); ?></label>
                        </div>
                        <div class="col-md-7">
                            <label class="container-label"><?php echo date('d F Y H:i:s', strtotime($getDetailInfoPasienPulang['tgl_pendaftaran'])); ?></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5">
                            <label class="control-label text-black l-label" style="padding-left:0px;"><?= Yii::t('fe', 'Tgl pulang'); ?></label>
                        </div>
                        <div class="col-md-7">
                            <label class="container-label"><?php echo date('d F Y H:i:s', strtotime($getDetailInfoPasienPulang['tglpasienpulang'])); ?></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5">
                            <label class="control-label text-black l-label" style="padding-left:0px;"><?= Yii::t('fe', 'No pendaftaran'); ?></label>
                        </div>
                        <div class="col-md-7">
                            <label class="container-label"><?php echo $getDetailInfoPasienPulang['no_pendaftaran'] ?></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5">
                            <label class="control-label text-black l-label" style="padding-left:0px;"><?= Yii::t('fe', 'No rekam medik'); ?></label>
                        </div>
                        <div class="col-md-7">
                            <label class="container-label"><?php echo $getDetailInfoPasienPulang['no_rekam_medik'] ?></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5">
                            <label class="control-label text-black l-label" style="padding-left:0px;"><?= Yii::t('fe', 'Nama pasien'); ?></label>
                        </div>
                        <div class="col-md-7">
                            <label class="container-label"><?php echo $getDetailInfoPasienPulang['nama_pasien'] ?></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5">
                            <label class="control-label text-black l-label" style="padding-left:0px;"><?= Yii::t('fe', 'Ruangan'); ?></label>
                        </div>
                        <div class="col-md-7">
                            <label class="container-label"><?php echo $getDetailInfoPasienPulang['ruangan_nama'] ?></label>
                        </div>
                    </div>
                </div>                
            </div>
             <div class="col-md-6">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-5">
                            <label class="control-label text-black l-label" style="padding-left:0px;"><?= Yii::t('fe', 'Jenis kelamin'); ?></label>
                        </div>
                        <div class="col-md-7">
                            <label class="container-label"><?php echo $getDetailInfoPasienPulang['jenis_kelamin'] ?></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5">
                            <label class="control-label text-black l-label" style="padding-left:0px;"><?= Yii::t('fe', 'Tgl lahir'); ?></label>
                        </div>
                        <div class="col-md-7">
                            <label class="container-label"><?php echo date('d F Y', strtotime($getDetailInfoPasienPulang['tanggal_lahir'])); ?></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5">
                            <label class="control-label text-black l-label" style="padding-left:0px;"><?= Yii::t('fe', 'Umur'); ?></label>
                        </div>
                        <div class="col-md-7">
                            <label class="container-label"><?php echo $getDetailInfoPasienPulang['umur'] ?></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5">
                            <label class="control-label text-black l-label" style="padding-left:0px;"><?= Yii::t('fe', 'Penjamin'); ?></label>
                        </div>
                        <div class="col-md-7">
                            <label class="container-label"><?php echo $getDetailInfoPasienPulang['penjamin_nama'] ?></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5">
                            <label class="control-label text-black l-label" style="padding-left:0px;"><?= Yii::t('fe', 'Dokter'); ?></label>
                        </div>
                        <div class="col-md-7">
                            <label class="container-label"><?php echo $getDetailInfoPasienPulang['dokter'] ?></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-5">
                            <label class="control-label text-black l-label" style="padding-left:0px;"><?= Yii::t('fe', 'Status pulang'); ?></label>
                        </div>
                        <div class="col-md-7">
                            <label class="container-label"><?php echo $getDetailInfoPasienPulang['carakeluar_nama'] ?></label>
                        </div>
                    </div>
                </div>                
            </div>
        </div>
    </div>
    <hr>
    <?php 
        $form = ActiveForm::begin([
            'id' => 'batal-periksa-form', 
            'options' => [
                    'class' => 'form-horizontal', 
                    'enableAjaxValidation' => true,
                    'role' => 'form',
                    'style' => 'padding: 0 10px 0 10px;'
                ],
        ]); 
    ?>
    <?= Html::hiddenInput('PasienBatalPulangForm[pasienpulang_id]', $getDetailInfoPasienPulang['pasienpulang_id'] );?>
    <?= Html::activeHiddenInput($model, 'pendaftaran_id');?>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-flat">
                <div class="panel panel-toolbar">
                    <h6 class="panel-title"><?= Yii::t('fe', 'Pembatalan pulang pasien rawat jalan'); ?></h6>
                </div>
                <div class="clearfix"></div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label class="control-label" style="padding-left:0px;"><?= Yii::t('fe', 'Tanggal pembatalan'); ?></label>
                                </div>
                                <div class="col-md-8">
                                    <b><span id="tglpasienpulang"></span></b>
                                    <?= Html::hiddenInput('PasienBatalPulangForm[tgl_pembatalan]', $model->tgl_pembatalan);?>
                                </div>
                            </div>
                            <div class="form-group required">
                                <div class="col-md-4">
                                    <label class="control-label" style="padding-left:0px;"><?= Yii::t('fe', 'Alasan  pembatalan'); ?></label>
                                </div>
                                <div class="col-md-8">
                                    <?= $form->field($model, 'alasan_pembatalan')->textArea(['rows' => '6']); ?>
                                </div>
                            </div>                            
                        </div>                        
                    </div>
                </div>
            </div> 
        </div>        
    </div>


    <div class="modal-footer">
        <?=Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['class' => 'btn btn bg-teal btn-sm','id'=>'btn-simpan']); ?>
    </div>
    <?php
        ActiveForm::end();
    ?>
    <br>
</div>
<script>
    $(document).ready(function () {
        $('label[for="pasienbatalpulangform-alasan_pembatalan"]').hide();
    });
    $("#btn-simpan").on("click", function(event) {
        event.preventDefault();
        var data = $("#batal-periksa-form").serializeArray();
        $(this).docoForm('click',{
            url: '/rajal/inf-pasien-pulang/save-batal-periksa',
            data: data,
            success : function(res) {
                var form = $("#batal-periksa-form");
                form[0].reset();
                table.draw();
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
        var date = d.getDate();
        var month = d.getMonth() + 1;
        var hour = checkTime(d.getHours());
        var min = checkTime(d.getMinutes());
        var sec = checkTime(d.getSeconds());
        var ampm = (hour >= 12) ? 'PM' : 'AM';
        var currentTime = hour +":"+ min +":"+ sec;

        // set time
        document.getElementById("tglpasienpulang").innerHTML = getCurrentDate() + '  ' + currentTime;
        $('input[name="PasienBatalPulangForm[tgl_pembatalan]"]').val(  d.getFullYear()+"-"+ month +"-"+d.getDate()+" "+hour+":"+min+":"+sec);
    }
    function checkTime(i) {
        if (i < 10) {i = "0" + i;}  // add zero in front of numbers < 10
        return i;
    }
    setInterval(clock, 1000);

</script>
