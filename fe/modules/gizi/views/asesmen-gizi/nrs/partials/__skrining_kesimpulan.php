<?php
use app\components\DHtml;
use yii\helpers\Html;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use yii\web\View;
use kartik\datetime\DateTimePicker;
use yii\helpers\Url;
?>
<div class="skrining-lanjut hidden">
    <div class="row form-row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title"><b>Kesimpulan</b></h5>
            </div>
            <div class="panel-body">
                <?php foreach ($skriningLanjut as $keterangan) : ?>
                <div class="col-sm-2">
                    <?= $form->field($model, 'skor[dewasa]['.$keterangan['id'].']')->textInput([
                        'class' => 'form-control input-sm skor-skrining-lanjut',
                        'rows' => '2',
                        'readonly' => true,
                    ])->label('Skor ' . $keterangan['nama'] ) ?>
                </div>
                <?php endforeach; ?>
                <div class="col-sm-2">
                    <?= $form->field($model, 'skor[dewasa][total]')->textInput([
                        'class' => 'form-control input-sm',
                        'rows' => '2',
                        'readonly' => true,
                    ])->label('Total Skor' ) ?>
                </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'skor[dewasa][keterangan]')->textInput([
                        'class' => 'form-control input-sm',
                        'rows' => '2',
                        'readonly' => true,
                    ])->label('Keterangan' ) ?>
                </div>
            </div>
        </div>
    </div>
</div>
