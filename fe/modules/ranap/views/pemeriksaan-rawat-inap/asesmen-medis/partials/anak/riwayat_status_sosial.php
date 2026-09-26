<?php
use yii\web\View;
$classFormControl = 'form-control input-sm';
?>

<div class="col-md-12 col-header">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">H. Riwayat Status Sosial, Ekonomi, Kultural dan Spiritual</h5>
        </div>
        <div class="panel-body">
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-bottom:20px;">Status Sosial & Ekonomi </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'hubungan_pasien')
                            ->label(Yii::t('fe', 'Hubungan Pasien Dengan Anggota Keluarga'))
                            ->radioList(
                                [
                                    '1' => 'Baik',
                                    '0' => 'Tidak Baik',
                                ],
                                [
                                    'inline' => true,
                                ]
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <p style="margin-left:20px;margin-bottom:20px;">Kerabat Terdekat Yang Dapat Dihubungi </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'nama_kerabat')->textInput([
                                'class' => $classFormControl,
                            ])->label(Yii::t('fe', 'Nama')); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'hubungan_kerabat')->textInput([
                                'class' => $classFormControl,
                            ])->label(Yii::t('fe', 'Hubungan')); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'telepon_kerabat')->textInput([
                                'class' => $classFormControl,
                            ])->label(Yii::t('fe', 'Telepon')); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'kebiasaan_beribadah')
                            ->label(Yii::t('fe', 'Kebiasaan Beribadah Teratur'))
                            ->radioList(
                                [
                                    '1' => 'Ya',
                                    '0' => 'Tidak',
                                ],
                                [
                                    'inline' => true,
                                ]
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pengambil_keputusan')->textInput([
                                'class' => $classFormControl,
                            ])->label(Yii::t('fe', 'Pengambilan Keputusan Dalam Keluarga')); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pekerjaan')->textInput([
                                'class' => $classFormControl,
                            ]); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'tempat_tinggal')->textInput([
                                'class' => $classFormControl,
                            ])->label(Yii::t('fe', 'Tempat Tinggal : Rumah/Panti/Lainnya')); ?>
                        </div>
                    </div>
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-bottom:20px;">Status Kultural & Spiritual </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'kesulitan_komunikasi')
                            ->label(Yii::t('fe', 'a. Kesulitan Dalam Melakukan Komunikasi'))
                            ->radioList(
                                [
                                    '1' => 'Ya',
                                    '0' => 'Tidak',
                                ],
                                [
                                    'inline' => true,
                                ]
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'kebiasaan_diet')
                            ->label(Yii::t('fe', 'b. Kebiasaan-Kebiasaan Diet & Berbusana'))
                            ->radioList(
                                [
                                    '1' => 'Ya',
                                    '0' => 'Tidak',
                                ],
                                [
                                    'inline' => true,
                                ]
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'kebiasaan_diet_lainnya')
                            ->label(Yii::t('fe', 'Jika Ya, Sebutkan'))
                            ->textInput([
                                'class' => $classFormControl,
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'kebiasaan_beribadah_kultural')
                            ->label(Yii::t('fe', 'c. Kebiasaan Beribadah Teratur'))
                            ->radioList(
                                [
                                    '1' => 'Ya',
                                    '0' => 'Tidak',
                                ],
                                [
                                    'inline' => true,
                                ]
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'kepercayaan')
                            ->label(Yii::t('fe', 'd. Kepercayaan & Nilai-Nilai Keagamaan'))
                            ->radioList(
                                [
                                    '1' => 'Ya',
                                    '0' => 'Tidak',
                                ],
                                [
                                    'inline' => true,
                                ]
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'kepercayaan_lainnya')
                            ->label(Yii::t('fe', 'Jika Ya, Sebutkan'))
                            ->textInput([
                                'class' => $classFormControl,
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'status_demografi')
                            ->label(Yii::t('fe', 'e. Status Demografi'))
                            ->radioList(
                                [
                                    '1' => 'Dataran Tinggi',
                                    '2' => 'Dataran Rendah',
                                    '3' => 'Pegunungan',
                                    '4' => 'Pantai',
                                    '5' => 'Lembah'
                                ]
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pantangan_berobat')
                            ->label(Yii::t('fe', 'f. Pantangan Berobat Terhadap Nilai-Nilai Kepercayaan'))
                            ->radioList(
                                [
                                    '1' => 'Ya',
                                    '0' => 'Tidak',
                                ],
                                [
                                    'inline' => true,
                                ]
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'pantangan_berobat_lainnya')
                            ->label(Yii::t('fe', 'Jika Ya, Sebutkan'))
                            ->textInput([
                                'class' => $classFormControl,
                            ]); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
$(document).ready(function(){
    const checkedDiet = $("input[name=\'AnakForm[kebiasaan_diet]\']");
    const checkedKepercayaan = $("input[name=\'AnakForm[kepercayaan]\']");
    const checkedObat = $("input[name=\'AnakForm[pantangan_berobat]\']");

    const inputDiet = $("#anakform-kebiasaan_diet_lainnya");
    const inputKepercayaan = $("#anakform-kepercayaan_lainnya");
    const inputObat = $("#anakform-pantangan_berobat_lainnya");

    inputDiet.prop("readonly", true);
    inputKepercayaan.prop("readonly", true);
    inputObat.prop("readonly", true);

    if(checkedDiet.is(":checked")) {
        if($("input[name=\'AnakForm[kebiasaan_diet]\']:checked").val() == "1") {
            inputDiet.prop("readonly", false);
        }
        else {
            inputDiet.val("").prop("readonly", true);
        }
    }
    if(checkedKepercayaan.is(":checked")) {
        if($("input[name=\'AnakForm[kepercayaan]\']:checked").val() == "1") {
            inputKepercayaan.prop("readonly", false);
        }
        else {
            inputKepercayaan.val("").prop("readonly", true);
        }
    }
    if(checkedObat.is(":checked")) {
        if($("input[name=\'AnakForm[pantangan_berobat]\']:checked").val() == "1") {
            inputObat.prop("readonly", false);
        }
        else {
            inputObat.val("").prop("readonly", true);
        }
    }
    checkedDiet.on("change", function(){
        if ($(this).is(":checked")) {
            if($(this).val() == "1") {
                inputDiet.prop("readonly", false);
            }
            else {
                inputDiet.val("").prop("readonly", true);
            }
        }
    })
    checkedKepercayaan.on("change", function(){
        if ($(this).is(":checked")) {
            if($(this).val() == "1") {
                inputKepercayaan.prop("readonly", false);
            }
            else {
                inputKepercayaan.val("").prop("readonly", true);
            }
        }
    })
    checkedObat.on("change", function(){
        if ($(this).is(":checked")) {
            if($(this).val() == "1") {
                inputObat.prop("readonly", false);
            }
            else {
                inputObat.val("").prop("readonly", true);
            }
        }
    })
})

', View::POS_END);
?>
