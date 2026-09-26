<?php
/**
 * @Author: Sigit
 * @Date:   2019-02-06 14:28:23
 */

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use kartik\datetime\DateTimePicker;

$persalinanId = $model['persalinan_id'];
if ($model['persalinan_id'] == '' || $model['persalinan_id'] == null) {
    $persalinanId = -1;
}
?>

<?php $form = ActiveForm::begin([
    'id' => 'form-keadaan-umum',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => [
        'labelSpan' => 4,
        'deviceSize' => ActiveForm::SIZE_SMALL,
        'enableClientValidation' => false
    ]
]) ?>
<div class="row">
    <div class="col-lg-6">
        <?= Html::activeHiddenInput($model, 'persalinan_id')?>
        <?= Html::activeHiddenInput($model, 'pendaftaran_id')?>
        <?= Html::activeHiddenInput($model, 'pasienadmisi_id')?>
        <div class="form-group highlight-addon field-persalinanform-tgl_persalinan">
            <?= Html::label(Yii::t('fe', 'Tanggal Persalinan'), null, ['class' => 'control-label col-sm-4']) ?>
            <div class="col-sm-8">
                <?= $form->field($model, 'tgl_persalinan', ['template'=>"{input}\n{hint}\n{error}"])->widget(DateTimePicker::classname(), [
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format' => 'dd-m-yyyy HH:ii',
                                'startDate' => date('d-m-Y H:i', strtotime($tglPendaftaran)),
                                'endDate' => date('d-m-Y H:i'),
                            ]
                        ]); ?>
                <div id="error_PersalinanFormtgl_persalinan"></div>
            </div>
        </div>
        <div class="form-group highlight-addon field-persalinanform-penolong">
            <?= Html::label(Yii::t('fe', 'Penolong'), null, ['class' => 'control-label col-sm-4']) ?>
            <div class="col-sm-8 select2-md">
                <?= $form->field($model, 'penolong', ['template'=>"{input}\n{hint}\n{error}"])->dropDownList([], [
                    'class' => 'form-control',
                    'prompt' => 'Pilih Pegawai',
                ]) ?>
                <div id="error_PersalinanFormpenolong"></div>
            </div>
        </div>
        <div class="form-group highlight-addon field-persalinanform-tempat_persalinan">
            <?= Html::label(Yii::t('fe', 'Tempat Persalinan'), null, ['class' => 'control-label col-sm-4']) ?>
            <div class="col-sm-8">
                <?= $form->field($model, 'tempat_persalinan', ['template'=>"{input}\n{hint}\n{error}"]) ?>
                <div id="error_PersalinanFormtempat_persalinan"></div>
            </div>
        </div>
        <div class="form-group highlight-addon field-persalinanform-tempat_persalinan">
            <?= Html::label(Yii::t('fe', 'Jenis Persalinan'), null, ['class' => 'control-label col-sm-4']) ?>
            <div class="col-sm-8 select2-md">
                <?= $form->field($model, 'jenis_persalinan', ['template'=>"{input}\n{hint}\n{error}"])->dropDownList(ArrayHelper::map($jenisPersalinan, 'lookupkeperawatan_id', 'lookup_value'), [
                    'class' => 'form-control select2',
                    'prompt' => 'Pilih Jenis Persalinan'
                ]) ?>
                <div id="error_PersalinanFormtempat_persalinan"></div>
            </div>
        </div>
        <div class="form-group highlight-addon field-persalinanform-rujuk_kala">
            <?= Html::label(Yii::t('fe', 'Catatan'), null, ['class' => 'control-label col-sm-4']) ?>
            <div class="col-sm-8">
                <?= $form->field($model, 'rujuk_kala', ['template'=>"{input}\n{hint}\n{error}"])->radioList(ArrayHelper::map($rujukKala, 'lookupkeperawatan_id', 'lookup_name')) ?>
                <div id="error_PersalinanFormrujuk_kala"></div>
            </div>
        </div>
        <br>
        <div id="rujuk_kala" hidden>
            <div class="form-group highlight-addon field-persalinanform-alasan_merujuk">
                <?= Html::label(Yii::t('fe', 'Alasan Merujuk'), null, ['class' => 'control-label col-sm-4']) ?>
                <div class="col-sm-8">
                    <?= $form->field($model, 'alasan_merujuk', ['template'=>"{input}\n{hint}\n{error}"]) ?>
                    <div id="error_PersalinanFormalasan_merujuk"></div>
                </div>
            </div>
            <div class="form-group highlight-addon field-persalinanform-tempat_rujukan">
                <?= Html::label(Yii::t('fe', 'Tempat Rujukan'), null, ['class' => 'control-label col-sm-4']) ?>
                <div class="col-sm-8">
                    <?= $form->field($model, 'tempat_rujukan', ['template'=>"{input}\n{hint}\n{error}"]) ?>
                    <div id="error_PersalinanFormtempat_rujukan"></div>
                </div>
            </div>
            <div class="form-group highlight-addon field-persalinanform-pendamping">
                <?= Html::label(Yii::t('fe', 'Pendamping'), null, ['class' => 'control-label col-sm-4']) ?>
                <div class="col-sm-8">
                    <?= $form->field($model, 'pendamping', ['template'=>"{input}\n{hint}\n{error}"])->radioList(
                        ArrayHelper::map($pendamping, 'lookupkeperawatan_id', 'lookup_name'),
                        [
                            'item' => function($index, $label, $name, $checked, $value) {
                                $return = '<label class="modal-radio">';
                                $return .= $checked ? '<input type="radio" name="'.$name.'" value="'.$value.'" tabindex="3" checked>' : '<input type="radio" name="'.$name.'" value="'.$value.'" tabindex="3">';
                                $return .= '<i></i>';
                                $return .= '<span>'.ucwords($label).'</span>';
                                $return .= '</label>';
                                return $return;
                            }
                        ]
                    ) ?>
                    <div id="error_PersalinanFormpendamping"></div>
                </div>
            </div>
            <div class="form-group highlight-addon field-persalinanform-masalah_persalinan">
                <?= Html::label(Yii::t('fe', 'Masalah Persalinan'), null, ['class' => 'control-label col-sm-4']) ?>
                <div class="col-sm-8">
                    <?= $form->field($model, 'masalah_persalinan', ['template'=>"{input}\n{hint}\n{error}"])->radioList(
                        ArrayHelper::map($masalahPersalinan, 'lookupkeperawatan_id', 'lookup_name'),
                        [
                            'item' => function($index, $label, $name, $checked, $value) {
                                $return = '<label class="modal-radio">';
                                $return .= $checked ? '<input type="radio" name="'.$name.'" value="'.$value.'" tabindex="3" checked>' : '<input type="radio" name="'.$name.'" value="'.$value.'" tabindex="3">';;
                                $return .= '<i></i>';
                                $return .= '<span>'.ucwords($label).'</span>';
                                $return .= '</label>';
                                return $return;
                            }
                        ]
                    ) ?>
                    <div id="error_PersalinanFormmasalah_persalinan"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 text-right">
        <?= Html::submitButton('<b><i class="fa fa-floppy-o"></i></b> Simpan', [
            'class' => 'btn btn-xs btn-labeled btn-info'
        ]) ?>
        <?= Html::button("<b><i class='fa fa-arrow-right'></i></b> ".Yii::t('fe', 'Selanjutnya'), [
            'class' => 'btn btn-xs btn-labeled btn-info btn-selanjutnya hidden',
            'data-index' => 0
        ]) ?>
    </div>
</div>
<?php ActiveForm::end() ?>
<?php $this->registerJs('
    var persalinanId = '.$persalinanId.';
    var _penolongId = '.( !is_null($model->penolong) ? $model->penolong : "null" ).';
    if (persalinanId != -1) {
        $("#btn-cetak-keadaan-umum").removeAttr("disabled");
    }
', View::POS_END, 'register-keadaanumum') ?>
<?php $this->registerJs($this->render('js/_keadaanumum.js'), View::POS_END, 'keadaanumum') ?>