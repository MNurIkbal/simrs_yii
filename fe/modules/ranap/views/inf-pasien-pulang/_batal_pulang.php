<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
?>
<style type="text/css">
    .modal-content {
        border-radius: 3px;
        -webkit-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);
        max-width: 827px;
        width: 950px;
        transform: translate(-10%, 0%);
    }

</style>

<div class="modal-header pulang bg-inverse">
    <button type="button" class="close" >&times;</button>   
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body" id="modalPasienPulang">
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
                            <label class="container-label"><?php echo date('d F Y H:i:s', strtotime($getDetailInfoPasienPulang['tgl_admisi'])); ?></label>
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
                            <label class="container-label kamar-ditempati"><?php echo $getDetailInfoPasienPulang['ruangan_nama'].' - '.$getDetailInfoPasienPulang['kamarruangan_nokamar'].' - '.$getDetailInfoPasienPulang['no_tempattidur'] ?></label>
                            <button type="button" id="btnCariKamar" class="btn btn-default">Kamar</button>
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
                            <label class="container-label"><?php echo $getDetailInfoPasienPulang['jns_kelamin'] ?></label>
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
                            <label class="control-label text-black l-label" style="padding-left:0px;"><?= Yii::t('fe', 'Cara Bayar - Penjamin'); ?></label>
                        </div>
                        <div class="col-md-7">
                            <label class="container-label"><?php echo $getDetailInfoPasienPulang['carabayar_nama'].' - '.$getDetailInfoPasienPulang['penjamin_nama'] ?></label>
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
                            <label class="control-label text-black l-label" style="padding-left:0px;"><?= Yii::t('fe', 'Cara - Kondisi pulang'); ?></label>
                        </div>
                        <div class="col-md-7">
                            <label class="container-label"><?php echo $getDetailInfoPasienPulang['carakeluar_nama'].' - '.$getDetailInfoPasienPulang['kondisikeluar_nama'] ?></label>
                        </div>
                    </div>
                </div>                
            </div>
        </div>
    </div>
    <hr>
    <?php 
        $form = ActiveForm::begin([
            'id' => 'batal-pulang-form', 
            'options' => [
                    'class' => 'form-horizontal', 
                    'enableAjaxValidation' => true,
                    'role' => 'form',
                    'style' => 'padding: 0 10px 0 10px;'
                ],
        ]); 
    ?>
    <?= Html::hiddenInput('PasienBatalPulangForm[pasienpulang_id]', $getDetailInfoPasienPulang['pasienpulang_id'] );?>
    <?= Html::hiddenInput('PasienBatalPulangForm[ruangan_id]', null,["id"=>"pasienbatalpulangform-ruangan_id"] );?>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-flat">
                <div class="panel panel-toolbar">
                    <h6 class="panel-title"><?= Yii::t('fe', 'Pembatalan Pulang Pasien Rawat Inap'); ?></h6>
                </div>
                <div class="clearfix"></div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label class="control-label" style="padding-left:0px;"><?= Yii::t('fe', 'Tanggal pembatalan'); ?></label>
                                </div>
                                <div class="col-md-9">
                                    <b><span id="tglpasienpulang"></span></b>
                                    <?= Html::hiddenInput('PasienBatalPulangForm[tgl_pembatalan]', $model->tgl_pembatalan);?>
                                </div>
                            </div>
                            <div class="form-group required">
                                <div class="col-md-3">
                                    <label class="control-label" style="padding-left:0px;"><?= Yii::t('fe', 'Alasan  pembatalan'); ?></label>
                                </div>
                                <div class="col-md-9">
                                    <?= $form->field($model, 'alasan_pembatalan')->textArea(['rows' => '6']); ?>
                                </div>
                            </div>                            
                        </div>                        
                    </div>
                </div>
                <?= $form->field($model, 'kamarruangan_id')->hiddenInput()->label(false); ?>
                <?= $form->field($model, 'kamartempattidur_id')->hiddenInput()->label(false); ?>
                <?= Html::dropDownList('hidden_data_jeniskasuspenyakit',null,$dataJenisKasusPenyakit,['style'=>'display:none','prompt'=>'Semua']) ?>
                <?= Html::dropDownList('hidden_data_kelaspelayanan',null,$dataKelasPelayanan,['style'=>'display:none','prompt'=>'Semua']) ?>
                <?= Html::dropDownList('hidden_data_ruangan',null,$dataRuangan,['style'=>'display:none','prompt'=>'Semua']) ?>
                <?= Html::dropDownList('hidden_data_kamar',null,$dataKamar,['style'=>'display:none','prompt'=>'Semua']) ?>
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

<div class="modal-body hide" id="modalTempatTidur" >
    <div class="panel-button">
        <div class="row" id="filterHeader">
        </div>
    </div>
    <hr>
    <div class="row table-responsive">
        <div id="tableKamarWrapper" class="table-scroll">
            <table class="table table-striped table-condensed table-hover table-pilih-kamar" style="width:100%" id="tableKamar">
                <thead>
                    <tr class="bg-inverse">
                        <th width="80">No</th>
                        <th><?=\Yii::t("fe", "Jenis Kasus Penyakit");?></th>
                        <th><?=\Yii::t("fe", "Ruangan");?></th>
                        <th><?=\Yii::t("fe", "Kamar");?></th>
                        <th><?=\Yii::t("fe", "Kelas");?></th>
                        <th><?=\Yii::t("fe", "No Tempat Tidur");?></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // const scrollWrapper = document.querySelector("#tableKamarWrapper");

    // scrollWrapper.addEventListener("scroll", function () {
    // if (scrollWrapper.scrollTop + scrollWrapper.clientHeight >= scrollWrapper.scrollHeight && isStillExistDataKamar.general) {
    //     // var jenisKamar = "semua";
    //     // initDatatable(jenisKamar);
    // }
    // });

    function clock() {
        var d = new Date();
        var date = d.getDate();
        var month = d.getMonth() + 1;
        var hour = checkTime(d.getHours());
        var min = checkTime(d.getMinutes());
        var sec = checkTime(d.getSeconds());
        var ampm = (hour >= 12) ? "PM" : "AM";
        var currentTime = hour +":"+ min +":"+ sec;
        var tglpasienpulang = document.getElementById("tglpasienpulang");
        if(tglpasienpulang != undefined){

            tglpasienpulang.innerHTML = getCurrentDate() + "  " + currentTime;
        }
        // set time
        $("input[name=\'PasienBatalPulangForm[tgl_pembatalan]\']").val(  d.getFullYear()+"-"+ month +"-"+d.getDate()+" "+hour+":"+min+":"+sec);
    }
    function checkTime(i) {
        if (i < 10) {i = "0" + i;}  // add zero in front of numbers < 10
        return i;
    }
    setInterval(clock, 1000);
</script>
