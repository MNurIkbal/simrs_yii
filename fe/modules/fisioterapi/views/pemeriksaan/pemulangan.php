<?php

use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use kartik\datetime\DateTimePicker;
use app\components\DocoHelpers;
use yii\web\View;

?>
<style type="text/css">
    .select2-selection__clear::after {
        content: '';
    }
</style>
<div class="modal-header">
    <button type="button" class="close close-modal-jadwal" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Tindak Lanjut</h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <?php
            $form = ActiveForm::begin([
                'id' => 'form-pemulanganpasien',
                'type' => ActiveForm::TYPE_HORIZONTAL,
                'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
            ]);
            ?>
            <?=Html::hiddenInput('PasienPulangForm[pendaftaran_id]', $data_pendaftaran["pendaftaran_id"]);?>
            <div class="form-group required">
                    <label for="Tanggal Pulang" class="col-lg-4 control-label">
                            <?= Yii::t('fe', 'Tanggal'); ?>
                    </label>
                    <div class="col-lg-8">
                        <?=DateTimePicker::widget([
                                'model' => $modelpulangpasien,
                                'attribute' => 'tglpasienpulang',
                                'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                                'readonly' => true,
                                'convertFormat' => true,
                                'pluginOptions' => [
                                    'format' => 'dd/MM/yyyy HH:mm:ss',
                                    'startDate' => $tglpendaftaran,
                                    'endDate' => date('Y-m-d H:i:s'),
                                    'autoclose' => true,
                                    'todayBtn' => true,
                                ]
                            ]);
                        ?>
                    </div>
                </div>
                <div class="form-group required">
                    <label for="carakeluar_id" class="col-lg-4 control-label">
                        <?= Yii::t('fe', 'Cara Pulang'); ?>
                    </label>
                    <div class="col-lg-8">
                        <select name="PasienPulangForm[carakeluar_id]" id="pasienpulangform-carakeluar_id" class="select2 autoJenisKasusPenyakit" disabled>
                            <option selected value="<?= $caraKeluar['carakeluar_id'] ?>" data-freetext="<?= $caraKeluar['is_freetext'] ?>"><?= $caraKeluar['carakeluar_nama'] ?></option>
                        </select>
                        <div class="error_PelayananJenazahFormcarakeluar_id"></div>
                    </div>

                </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<div class="modal-footer text-right">
    <?= DocoHelpers::generateToolbar([
        'save' => [
            'attributes' => [
                'id' => 'btn-save-pulang',
                'data-options' => 'click',
                'onClick' => false
            ]
        ]
    ]);
    ?>
</div>
<?php
$this->registerJs('
    var pendaftaranId = "' . $pendaftaranId . '";
    var defaultDate = "'.date('d/m/Y H:i:s').'"
    ' . $this->render("pemulangan.js"), View::POS_END, 'js');
?>