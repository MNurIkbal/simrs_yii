<?php
use yii\helpers\Html;
?>
<div class="form-group row" id="periksafisik-row">
    <div class="col-md-12">
        <h6 class="text-label-size text-bold">Pemeriksaan Fisik:</h6>
    </div>
    <div class="form-group row" style="margin-top: 10px">
        <div class="col-md-12">
            <?= Html::activeTextArea($model, 'pemeriksaan_fisik', ['class' => 'form-control', 'rows' => 5]) ?>
        </div>
    </div>

    <!-- <div class="col-md-4">
        <div class="form-group">
            <label class="col-sm-3 control-label text-bold" for="">Berat Badan</label>
            <div class="col-sm-5">
                <div class="input-group">
                    <?=Html::activeTextInput($model, 'berat_badan', ['class' => 'form-control doco-decimal-wcomma'])?>
                    <span class="input-group-addon">Kg</span>
                </div>
            </div>
        </div>
        <div class="form-group" style="margin-top: 10px">
            <label class="col-sm-3 control-label text-bold" for="">Nadi</label>
            <div class="col-sm-5">
                <div class="input-group">
                    <?=Html::activeTextInput($model, 'nadi', ['class' => 'form-control doco-decimal'])?>
                    <span class="input-group-addon">x/Menit</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label class="col-sm-3 control-label text-bold" for="">Tinggi Badan</label>
            <div class="col-sm-5">
                <div class="input-group">
                    <?=Html::activeTextInput($model, 'tinggi_badan', ['class' => 'form-control doco-decimal-wcomma'])?>
                    <span class="input-group-addon">cm</span>
                </div>
            </div>
        </div>
        <div class="form-group" style="margin-top: 10px">
            <label class="col-sm-3 control-label text-bold" for="">RR</label>
            <div class="col-sm-5">
                <div class="input-group">
                    <?=Html::activeTextInput($model, 'rr', ['class' => 'form-control doco-decimal'])?>
                    <span class="input-group-addon">x/Menit</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label class="col-sm-3 control-label text-bold" for="">Tekanan Darah</label>
            <div class="col-sm-5">
                <div class="input-group">
                    <?=Html::activeTextInput($model, 'td', ['class' => 'form-control'])?>
                    <span class="input-group-addon">mmHg</span>
                </div>
            </div>
        </div>
        <div class="form-group" style="margin-top: 10px">
            <label class="col-sm-3 control-label text-bold" for="">Suhu</label>
            <div class="col-sm-5">
                <div class="input-group">
                    <?=Html::activeTextInput($model, 'suhu', ['class' => 'form-control doco-decimal-wcomma'])?>
                    <span class="input-group-addon">&deg;C</span>
                </div>
            </div>
        </div>
    </div> -->
</div>