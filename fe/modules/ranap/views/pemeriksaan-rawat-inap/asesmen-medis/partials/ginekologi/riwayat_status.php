<?php

use yii\web\View;

$statusMental = $model->status_mental;
$statusMental1 = $statusMental2 = $statusMental3 = false;
$classFormControl = 'form-control input-sm';
$statusName = 'status_mental[]';
$labelSebutkan = 'Jika Ya, Sebutkan';

if($statusMental && is_array($statusMental)) {
    if(in_array('1', $statusMental)) {
        $statusMental1 = true;
    }
    if(in_array('2', $statusMental)) {
        $statusMental2 = true;
    }
    if(in_array('3', $statusMental)) {
        $statusMental3 = true;
    }
}

?>

<div class="col-md-12 col-header">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">C. Riwayat Status Psikologis, Sosial, Ekonomi, Kultural & Spiritual</h5>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'status_psikologi')
                            ->label(Yii::t('fe', 'Status Psikologis'))
                            ->checkboxList(
                            [
                                '1' => 'Cemas',
                                '2' => 'Sedih',
                                '3' => 'Tenang',
                                '4' => 'Takut',
                                '5' => 'Gelisah',
                                '6' => 'Marah',
                                '7' => 'Acuh Tak Acuh',
                                '8' => 'Lainnya',
                            ]
                        );
                        ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'status_psikologi_lainnya')->label(false)->textInput(
                            [
                                'class' => $classFormControl,
                            ]
                        ); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <p style="margin-left:20px;margin-bottom:20px;">Status Mental </p>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, $statusName)
                            ->label(Yii::t('fe', 'Status Mental'))
                            ->checkbox(
                            [
                                'label' => Yii::t('fe', 'Sadar & Orientasi Baik'),
                                'value' => '1',
                                'checked' => $statusMental1,
                            ]
                        );
                        ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, $statusName)
                            ->label(false)
                            ->checkbox(
                                [
                                    'label' => Yii::t('fe', 'Ada Masalah Perilaku'),
                                    'value' => '2',
                                    'checked' => $statusMental2,
                                    'id' => 'sm2'
                                ]
                            ); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'masalah_lainnya')->label(false)->textInput(
                            [
                                'class' => $classFormControl,
                                'readonly' => (!$statusMental2) ? true : false
                            ]
                        ); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, $statusName)
                            ->label(false)
                            ->checkbox(
                                [
                                    'label' => Yii::t('fe', 'Perilaku Kekerasan yang di Alami Pasien Sebelumnya'),
                                    'value' => '3',
                                    'checked' => $statusMental3,
                                    'id' => 'sm3'
                                ]
                            ); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'perilaku_lainnya')->label(false)->textInput(
                            [
                                'class' => $classFormControl,
                                'readonly' => (!$statusMental3) ? true : false
                            ]
                        ); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <p style="margin-left:20px;margin-bottom:20px;">Status Sosial & Ekonomi </p>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <p style="margin-left:20px;">a. Hubungan Pasien Dengan Anggota Keluarga </p>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'hubungan_pasien')
                    ->label(false)
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
                <div class="col-sm-6">
                    <p style="margin-left:20px;margin-bottom:10px;">b. Kerabat Terdekat Yang Dapat Dihubungi </p>
                </div>
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
                    <?= $form->field($model, 'pengambil_keputusan')->textInput([
                        'class' => $classFormControl,
                    ])->label(Yii::t('fe', 'c. Pengambilan Keputusan Dalam Keluarga :')); ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'pekerjaan')->textInput([
                        'class' => $classFormControl,
                    ])->label(Yii::t('fe', 'd. Pekerjaan :')); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'tempat_tinggal')->checkboxList([
                        '1' => 'Rumah Sendiri',
                        '2' => 'Rumah Keluarga',
                        '3' => 'Lainnya',
                    ],['inline' => true])->label(Yii::t('fe', 'e. Tempat Tinggal :')); ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'tempat_tinggal_lainnya')->textInput([
                        'class' => $classFormControl,
                    ])->label(false); ?>
                </div>
            </div>
            <div class="row">
                <p style="margin-left:20px;margin-bottom:10px;margin-top:10px;">Status Kultural & Spiritual </p>
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
                    ->label(Yii::t('fe', $labelSebutkan))
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
                    ->label(Yii::t('fe', $labelSebutkan))
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
                    ->label(Yii::t('fe', $labelSebutkan))
                    ->textInput([
                        'class' => $classFormControl,
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
var masalah_lainnya = "'.$model->masalah_lainnya.'"
var perilaku_lainnya = "'.$model->perilaku_lainnya.'"
var status_psikologi_lainnya = "'.$model->status_psikologi_lainnya.'"
var tempat_tinggal_lainnya = "'.$model->tempat_tinggal_lainnya.'"
var kebiasaan_diet_lainnya = "'.$model->kebiasaan_diet_lainnya.'"
var kepercayaan_lainnya = "'.$model->kepercayaan_lainnya.'"
var pantangan_berobat_lainnya = "'.$model->pantangan_berobat_lainnya.'"

$(document).ready(function(){
    const checkboxPsikologi = $("input[name=\'GinekologiForm[status_psikologi][]\'][value=\'8\']");
    const checkboxMasalah = $("input[name=\'GinekologiForm[status_mental][]\'][value=\'2\']");
    const checkboxPerilaku = $("input[name=\'GinekologiForm[status_mental][]\'][value=\'3\']");
    const checkedDiet = $("input[name=\'GinekologiForm[kebiasaan_diet]\']");
    const checkedKepercayaan = $("input[name=\'GinekologiForm[kepercayaan]\']");
    const checkedObat = $("input[name=\'GinekologiForm[pantangan_berobat]\']");
    const checkboxTempatTinggal = $("input[name=\'GinekologiForm[tempat_tinggal][]\'][value=\'3\']");

    const inputDiet = $(`#${_modelIdForm}-kebiasaan_diet_lainnya`);
    const inputKepercayaan = $(`#${_modelIdForm}-kepercayaan_lainnya`);
    const inputObat = $(`#${_modelIdForm}-pantangan_berobat_lainnya`);
    const masalahLainnya = $(`#${_modelIdForm}-masalah_lainnya`);
    const perilakuLainnya = $(`#${_modelIdForm}-perilaku_lainnya`);
    const psikologiLainnya = $(`#${_modelIdForm}-status_psikologi_lainnya`);
    const tempatTinggaliLainnya = $(`#${_modelIdForm}-tempat_tinggal_lainnya`);

    masalahLainnya.prop("readonly", true);
    perilakuLainnya.prop("readonly", true);
    psikologiLainnya.prop("readonly", true);
    inputDiet.prop("readonly", true);
    inputKepercayaan.prop("readonly", true);
    inputObat.prop("readonly", true);
    tempatTinggaliLainnya.prop("readonly", true);

    if(asesmenMedisId) {
        if(masalah_lainnya) {
            masalahLainnya.prop("readonly", false);
        }
        if(perilaku_lainnya) {
            perilakuLainnya.prop("readonly", false);
        }
        if(status_psikologi_lainnya) {
            psikologiLainnya.prop("readonly", false);
        }
        if(tempat_tinggal_lainnya) {
            tempatTinggaliLainnya.prop("readonly", false);
        }
    }

    checkboxMasalah.change(function () {
        if(checkboxMasalah.is(":checked")) {
            masalahLainnya.prop("readonly", false);
        }
        else {
            masalahLainnya.val("").prop("readonly", true);
        }
    });
    checkboxPerilaku.change(function () {
        if(checkboxPerilaku.is(":checked")) {
            perilakuLainnya.prop("readonly", false);
        }
        else {
            perilakuLainnya.val("").prop("readonly", true);
        }
    });
    checkboxPsikologi.change(function () {
        if(checkboxPsikologi.is(":checked")) {
            psikologiLainnya.prop("readonly", false);
        }
        else {
            psikologiLainnya.val("").prop("readonly", true);
        }
    });

    if(checkedDiet.is(":checked")) {
        if($("input[name=\'GinekologiForm[kebiasaan_diet]\']:checked").val() == "1") {
            inputDiet.prop("readonly", false);
        }
        else {
            inputDiet.val("").prop("readonly", true);
        }
    }
    if(checkedKepercayaan.is(":checked")) {
        if($("input[name=\'GinekologiForm[kepercayaan]\']:checked").val() == "1") {
            inputKepercayaan.prop("readonly", false);
        }
        else {
            inputKepercayaan.val("").prop("readonly", true);
        }
    }
    if(checkedObat.is(":checked")) {
        if($("input[name=\'GinekologiForm[pantangan_berobat]\']:checked").val() == "1") {
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
    checkboxTempatTinggal.change(function () {
        if(checkboxTempatTinggal.is(":checked")) {
            tempatTinggaliLainnya.prop("readonly", false);
        }
        else {
            tempatTinggaliLainnya.val("").prop("readonly", true);
        }
    });
})

', View::POS_END);
?>
