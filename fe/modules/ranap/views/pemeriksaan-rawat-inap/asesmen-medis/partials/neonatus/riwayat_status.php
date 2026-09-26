<?php
use yii\web\View;
$classFormControl = 'form-control input-sm';
?>

<div class="col-md-12 col-header">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">D. Riwayat Status Psikososial dan Ekonomi</h5>
        </div>
        <div class="panel-body">
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'persepsi_ortu', [
                                'labelOptions' => ['class' => '']
                            ])->textInput([
                                'class' => $classFormControl,
                            ])->label(); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'harapan_ortu', [
                                'labelOptions' => ['class' => '']
                            ])->textInput([
                                'class' => $classFormControl,
                            ])->label(); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'riwayat_status')
                            ->label()
                            ->radioList(
                                [
                                    'Sendiri' => 'Ditanggung Sendiri',
                                    'Perusahaan' => 'Ditanggung Perusahaan',
                                    'Lainnya' => 'Lainnya',
                                ],
                                [
                                    'itemOptions' => [
                                        'class' => 'riwayat_status'
                                    ],
                                ]
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'riwayat_status_lainnya')->textInput([
                                'class' => $classFormControl,
                            ])->label(false); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'riwayat_status_anak')
                            ->label()
                            ->radioList(
                                [
                                    1 => 'Diharapkan',
                                    0 => 'Tidak Diharapkan',
                                ],
                                [
                                    'itemOptions' => [],
                                    'inline' => 'true',
                                ]
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <p style="font-weight:bold;margin-left:20px;margin-top:20px;margin-bottom:20px;">Status Orang Tua </p>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'riwayat_status_ortu_berkunjung')
                            ->label()
                            ->radioList(
                                [
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak',
                                ],
                                [
                                    'itemOptions' => [],
                                    'inline' => 'true',
                                ]
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'riwayat_status_ortu_kontak_mata')
                            ->label()
                            ->radioList(
                                [
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak',
                                ],
                                [
                                    'itemOptions' => [],
                                    'inline' => 'true',
                                ]
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'riwayat_status_ortu_menyentuh')
                            ->label()
                            ->radioList(
                                [
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak',
                                ],
                                [
                                    'itemOptions' => [],
                                    'inline' => 'true',
                                ]
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'riwayat_status_ortu_berbicara')
                            ->label()
                            ->radioList(
                                [
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak',
                                ],
                                [
                                    'itemOptions' => [],
                                    'inline' => 'true',
                                ]
                            ); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= $form->field($model, 'riwayat_status_ortu_menggendong')
                            ->label()
                            ->radioList(
                                [
                                    'Ya' => 'Ya',
                                    'Tidak' => 'Tidak',
                                ],
                                [
                                    'itemOptions' => [],
                                    'inline' => 'true',
                                ]
                            ); ?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'riwayat_status_ortu_perawat')
                            ->label()
                            ->radioList(
                                [
                                    'Ibu' => 'Ibu',
                                    'Nenek' => 'Nenek',
                                    'Pengasuh' => 'Pengasuh',
                                ],
                                [
                                    'itemOptions' => [],
                                    'inline' => 'true',
                                ]
                            ); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
var riwayat_status_lainnya = "'.$model->riwayat_status_lainnya.'"

$(document).ready(function(){
    const checkValueStatus = $(".riwayat_status");
    const otherStatus = $("#neonatusform-riwayat_status_lainnya");
    otherStatus.prop("readonly", true);

    if(asesmenMedisId) {
        if(riwayat_status_lainnya) {
            otherStatus.prop("readonly", false);
        }
    }
    
    $(document).on("change", ".riwayat_status", function(){
        if($(this).is(":checked")) {
            if($(this).val() == "Lainnya") {
                otherStatus.prop("readonly", false);
            }
            else {
                otherStatus.val("").prop("readonly", true);
            }
        }
    })
})

', View::POS_END);
?>
