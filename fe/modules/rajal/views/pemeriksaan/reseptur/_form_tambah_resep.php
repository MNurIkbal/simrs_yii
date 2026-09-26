<?php 
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

?>
<?php $form = ActiveForm::begin([
    'id' => 'form-header',
    'type' => ActiveForm::TYPE_VERTICAL,
    'enableClientValidation'=>false,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]) ?>
<?= Html::hiddenInput('pendaftaran_id', $pendaftaran_id, ['id' => 'pendaftaran_id']); ?>
<?= Html::hiddenInput('penjamin_id', $penjamin_id, ['id' => 'penjamin_id']); ?>
<?= Html::hiddenInput('diagnosa_id', $idDiagnosa, ['id' => 'diagnosa_id']); ?>
<div class="row">
    <div class="col-lg-3">
        <?= $form->field($modelReseptur, 'depo_id', [
            'labelOptions' => ['class' => 'text-right']
        ])->dropDownList(ArrayHelper::map($list_data_apotek, 'ruangan_id', 'ruangan_nama'), [
            'class' => 'form-control select2 input-sm',
            'id' => 'select_ruangan',
            'prompt' => Yii::t('fe', '--Pilih depo--')
        ])->label(Yii::t('fe', 'Depo Tujuan')); ?>
    </div>
    <div class="col-lg-5">
        <?= $form->field($modelReseptur, 'pilih_template', [
            'labelOptions' => ['class' => 'text-right']
        ])->dropDownList(ArrayHelper::map($data_template, 'reseptemp_id', 'reseptemp_nama'), [
            'class' => 'form-control select2 input-sm',
            'id' => 'select_template',
            'prompt' => Yii::t('fe', '--Pilih Template--')
        ])->label(Yii::t('fe', 'Pilih Template')); ?>
    </div>
    <div class="col-lg-1" style="margin-top:12px;">
        <button type="button" class="btn btn-md btn-info pilih-template">Pilih</button>
    </div>

    <div class="col-lg-3">
        <?=$form->field($modelReseptur, 'iter', ['labelOptions' => ['class' => 'text-right']])
        ->textInput([
            'id' => 'reseptur_iter',
            'class' => 'form-control input-sm docoNumberOnly',
        ])->label(Yii::t('fe', 'Iter')); ?>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-4">
        <?=$form->field($modelReseptur, 'jenis_racikan', ['labelOptions' => ['class' => 'text-right']])
        ->radioList([0 => 'Racikan', 1 => 'Non Racikan'],
        [
            'item' => function($index, $label, $name, $checked, $value) {
                $return = '<label class="modal-radio">';
                $return .= '<input type="radio" class="jenis_racikan" name="' . $name . '" value="' . $value . '" tabindex="3">';
                $return .= '&nbsp;&nbsp;';
                $return .= '<span>' . ucwords($label) . '</span>';
                $return .= '</label>';

                return $return;
            },
            'inline' => true,
        ])->label(Yii::t('fe', 'Jenis Racikan')); ?>
    </div>
</div>
<br>

<?php ActiveForm::end() ?>