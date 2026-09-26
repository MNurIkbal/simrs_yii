<?php

use yii\helpers\Html;
use yii\web\View;

$classForm = 'form-control';
$classFormNumber = 'form-control doco-number';
?>

<div class="col-md-12 col-header">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">E. Riwayat Imunisasi Dasar</h5>
        </div>
        <div class="panel-body">
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'riwayat_imunisasi')
                            ->radioList([
                                '1' => 'Lengkap (BCG, DPT, Hepatitis B, Polio, Campak)',
                                '0' => 'Tidak Lengkap',
                                '2' => 'Tidak Pernah'
                            ]); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'riwayat_imunisasi_lainnya')
                            ->label(Yii::t('fe', 'Sebutkan yang belum'))
                            ->textInput(['class' => $classForm]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs(
    '
    $(document).ready(function () {
        const checkedImunisasi = $("input[name=\'AnakForm[riwayat_imunisasi]\']");
        const inputImunisasi = $("#anakform-riwayat_imunisasi_lainnya");
        inputImunisasi.prop("readonly", true);

        if(checkedImunisasi.is(":checked")) {
            if($("input[name=\'AnakForm[riwayat_imunisasi]\']:checked").val() == "0") {
                inputImunisasi.prop("readonly", false);
            }
            else {
                inputImunisasi.val("").prop("readonly", true);
            }
        }

        checkedImunisasi.on("change", function(){
            if ($(this).is(":checked")) {
                if($(this).val() == "0") {
                    inputImunisasi.prop("readonly", false);
                }
                else {
                    inputImunisasi.val("").prop("readonly", true);
                }
            }
        })
    });'
);
?>
