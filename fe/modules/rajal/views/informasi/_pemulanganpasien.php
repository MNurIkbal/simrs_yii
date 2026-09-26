<?php

/**
 * @Author: sunarko
 * @Date:   2018-05-22 09:54:19
 * @Last Modified by:
 * @Last Modified time:
 * @Description:
 */

use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\datetime\DateTimePicker;
use yii\helpers\ArrayHelper;

$form = ActiveForm::begin([
    'id' => 'form-pemulanganpasien',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]);
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <div class="form-group required">
               <label for="Tanggal Pulang" class="col-lg-3 control-label">
            <?= Yii::t('fe', 'Tanggal Pulang'); ?>
        </label>
        <div class="col-lg-6" style="margin-left: 10px;">
            <b><span id="tglpasienpulang"></span></b>
            <?= Html::hiddenInput('PasienPulangForm[tglpasienpulang]', $modelpulangpasien->tglpasienpulang);?>
            <?php
            // Use appended addon and display meridian format and today button
              /*echo DateTimePicker::widget([
                'name' => 'tglpasienpulang',
                'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                'pluginOptions' => [
                    'format' => 'dd MM yyyy HH:ii:ss',
                    'showMeridian' => true,
                    'autoclose' => true,
                    'todayBtn' => true
                ]
              ]);*/
            ?>
        </div>

    </div>
    <div class="form-group required">
        <label for="carakeluar_id" class="col-lg-3 control-label">
            <?= Yii::t('fe', 'Cara Pulang'); ?>
        </label>
        <div class="col-lg-9">
            <?= $form->field($modelpulangpasien, 'carakeluar_id')
                ->dropDownList(
                    $listCaraKeluar,
                    [
                        'class' => 'select2 autoJenisKasusPenyakit',
                        'prompt' => Yii::t('fe', '-- Pilih --'),
                    ]
                )->label(false);
            ?>
        </div>
    </div>
    <?=Html::hiddenInput('PasienPulangForm[pasien_id]', $data_pasien["pasien_id"]);?>
    <?=Html::hiddenInput('PasienPulangForm[pendaftaran_id]', $data_pasien["pendaftaran_id"]);?>
    <?=Html::hiddenInput('PasienPulangForm[ruanganakhir_id]', $data_pasien["ruangan_id"]);?>
</div>
<div class="modal-footer">
    <?=Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['class' => 'btn btn bg-teal btn-sm btn-save']); ?>

    <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
</div>
<?php ActiveForm::end(); ?>

<script type="text/javascript">
    $("#form-pemulanganpasien").docoForm("submit",{
        success : function(data) {
            tabel.draw();
            $("#modal_backdrop").modal("toggle");
        }
    });
</script>

<?php
$this->registerJs($this->render('js/_informasi.js'));
?>
