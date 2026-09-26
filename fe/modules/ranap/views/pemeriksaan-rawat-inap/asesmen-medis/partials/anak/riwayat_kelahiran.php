<?php

use yii\helpers\Html;
use yii\web\View;

$classForm = 'form-control';
$classFormNumber = 'form-control doco-number';
?>

<div class="col-md-12 col-header">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title">D. Riwayat Kelahiran</h5>
        </div>
        <div class="panel-body">
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'usia_kehamilan', ['addon' => ['append' => ['content' => 'Minggu']]])
                            ->textInput(['class' => $classFormNumber]); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'pb_lahir', ['addon' => ['append' => ['content' => 'cm']]])
                            ->textInput(['class' => $classFormNumber]); ?>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'bb_lahir', ['addon' => ['append' => ['content' => 'Gram']]])
                            ->textInput(['class' => $classFormNumber]); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'menangis')
                            ->radioList(['1' => 'Ya', '0' => 'Tidak'], ['inline' => true]); ?>
                    </div>
                </div>
            </div>
            <div class="row" style="margin-top:15px;">
                <div class="col-md-12 form-group">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'persalinan')
                            ->radioList(['Spontan' => 'Spontan', 'Vakum' => 'Vakum Ektraksi', 'Sectio' => 'Sectio Secarea', 'Forcef' => 'Forcef']); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= $form->field($model, 'riwayat_kuning')
                            ->radioList(['1' => 'Ya', '0' => 'Tidak'], ['inline' => true]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
