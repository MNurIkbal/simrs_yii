<?php

use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\web\View;
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<div class="modal-body">
    <?php if (!empty($bundleData['data_askep'] || $bundleData['data_asmed'] || $bundleData['data_instruksi'])) : ?>
        <div class="alert alert-danger" role="alert">
            <strong>Pasien Tidak Dapat dilakukan Batal Periksa!</strong>
            Karena pasien sudah memiliki:
            <ul>
                <?= !empty($bundleData['data_askep']) ? '<li>Asesmen Keperawatan</li>' : '' ?>
                <?= !empty($bundleData['data_asmed']) ? '<li>Asesmen Medis</li>' : '' ?>
                <?php
                if (!empty($bundleData['data_instruksi'])) :
                    if ($bundleData['count_approved_instruksi'] == 0) :
                    foreach ($bundleData['data_instruksi'] as $key => $value) :
                ?>
                    <li><b><?= $value['tipe_instruksi'] ?></b> - <?= $value['instruksi'] ?></li>
                <?php
                    endforeach;
                    echo "<li>Harap lakukan pembatalan order terlebih dahulu.</li>";
                    else:
                ?>
                    <li>Terapi yang telah disetujui</li>
                <?php
                    endif;
                endif;
                ?>
            </ul>
        </div>
    <?php else : ?>
        <?php
        $form = ActiveForm::begin([
            'id' => 'batal-form',
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'enableAjaxValidation' => false,
            'enableClientValidation' => false,
            'action' => '/igd/inf-pasien-igd/batal',
        ]);
        ?>

        <?= $form->field($model, 'username')->textInput(['readonly' => true, 'value' => $username]) ?>

        <?= $form->field($model, 'password')->passwordInput() ?>

        <?= $form->field($model, 'alasan_batal')->textarea(['rows' => '4'], ['class' => 'form-control']); ?>

        <?= Html::hiddenInput('PasienBatalPeriksaForm[pendaftaran_id]', $pendaftaran_id); ?>

        <div class="modal-footer">
            <?= Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['class' => 'btn bg-teal btn-sm btn-simpan']) ?>
            <?= Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'), ['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
        </div>

        <?php ActiveForm::end(); ?>
    <?php endif; ?>
</div>

<?php
$this->registerJs($this->render('js/_batal.js'), View::POS_END);
?>
