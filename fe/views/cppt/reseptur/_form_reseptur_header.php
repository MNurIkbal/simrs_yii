<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use yii\web\View;
?>

<div class="row">
    <div class="col-sm-1">
        <div class="form-group">
            <label class="control-label">Jenis</label>
            <p class="label-value-form">Obat</p>
        </div>
    </div>

    <div class="col-sm-3">
        <label for="catatan_reseptur">Catatan</label>
        <textarea class="form-control input-sm" id="catatan_reseptur" name="catatan_reseptur" rows="3"></textarea>
    </div>

    <?php if ($is_ranap) { ?>
        <div class="col-sm-3">
            <input type="checkbox" id="reminder_puasa" name="reminder_puasa" value="1">
            <label for="reminder_puasa" style="vertical-align: bottom;">Reminder Puasa</label>
        </div>
    <?php } ?>
</div>

<div class="row">
    <div class="col-md-12">
        <hr>
    </div>
</div>

<div class="row">
    <div class="col-sm-3">
        <?= Html::hiddenInput('penjamin_id', $penjamin_id, ['id' => 'penjamin_id']); ?>
        <?= $form->field($model, 'diagnosa_id', [
            'labelOptions' => [
                'style' => 'display: none'
            ]
        ])->hiddenInput([]);
        ?>
    </div>
</div>

<div class="row">
    <div class="col-sm-3">
        <?= $form->field($model, 'diagnosa_nama', [
            'labelOptions' => [
                // 'class' => 'text-right'
            ]
        ])->textInput([
            'class' => 'form-control',
            'id' => 'diagnosa_nama',
            'readonly' => true
        ]);
        ?>
    </div>
    <div class="col-sm-3">
        <?= $form->field($model, 'pegawai_id')
            ->dropDownList($dokterList, [
                'data-api' => $dokter_url,
                'class' => 'select2-selffocus'
            ])->label('Dokter') ?>

    </div>
    <div class="col-sm-3">
        <?= $form->field($model, 'tglreseptur', [])->textInput([
            'class' => 'form-control',
            'id' => 'tglreseptur',
            'readonly' => true,
            'value' => date('d/m/Y')
        ]);
        ?>
    </div>
</div>

<div class="row">
    <div class="col-sm-3">
        <?= $form->field($model, 'is_hamil')->radioList([
            1 => 'Ya', 0 => 'Tidak'
        ], [
            'class' => 'form-inline'
        ]);
        ?>
    </div>
    <div class="col-sm-3 select2-md">
        <?= $form->field($model, 'berat_badan', [
            'addon' => [
                'append' => ['content' => 'kg']
            ],
        ])->textInput([
            'id' => 'berat_badan',
            'class' => 'form-control input-sm doco-decimal bb_tb',
        ])->label(Yii::t('fe', 'Berat Badan')); ?>
    </div>
    <div class="col-sm-3 select2-md">
        <?= $form->field($model, 'tinggi_badan', [
            'addon' => ['append' => ['content' => 'cm']],
        ])->textInput([
            'id' => 'tinggi_badan',
            'class' => 'form-control input-sm doco-decimal bb_tb',
        ])->label(Yii::t('fe', 'Tinggi Badan')); ?>
    </div>
</div>

<div class="row">
    <div class="col-sm-3">
        <?= $form->field($model, 'luas_tubuh', [])->textInput([
            'class' => 'form-control',
            'id' => 'luas_tubuh',
            'readonly' => true
        ]);
        ?>
    </div>
    <div class="col-sm-3 select2-md">
        <?= $form->field($model, 'depo_id', [
            'labelOptions' => ['class' => 'text-right']
        ])->dropDownList(ArrayHelper::map($list_data_apotek, 'ruangan_id', 'ruangan_nama'), [
            'class' => 'form-control input-sm select2-selffocus',
            'id' => 'select_ruangan',
            'prompt' => Yii::t('fe', '--Pilih depo--')
        ])->label(Yii::t('fe', 'Depo Tujuan'));
        ?>
    </div>
    <div class="col-sm-3">
        <?= $form->field($model, 'iter', [])->textInput([
            'class' => 'form-control',
            'id' => 'iter'
        ]);
        ?>
    </div>
</div>

<?php
$this->registerJs($this->render('js/_header.js'), View::POS_END);
?>
