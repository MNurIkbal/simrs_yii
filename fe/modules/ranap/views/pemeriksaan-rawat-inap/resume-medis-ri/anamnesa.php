<?php
use yii\helpers\Html;
?>
<div class="form-group row" id="anamnesa-row">
    <h5 style="margin-left: 10px;">Anamnesis</h5>
    <div class="col-md-6">
        <?= $form->field($model, 'keluhan_utama')->textArea(['rows' => 5])->label($model->getAttributeLabel('keluhan_utama'),['class' => 'text-bold']); ?>
    </div>
    
    <!-- <div class="col-md-4">
        <div class="form-group">
            <label class="col-sm-3 control-label text-bold" for="">Keluhan Utama</label>
            <div class="col-sm-9">
                <?=Html::activeTextInput($model, 'keluhan_utama', ['class' => 'form-control input-tags','data-role' => 'tagsinput'])?>
            </div>
        </div>
        <div class="form-group" style="margin-top: 10px">
            <label class="col-sm-3 control-label text-bold" for="">Metode Nyeri</label>
            <div class="col-sm-9">
                <?=Html::activeTextInput($model, 'metod_asmennyeri', ['class' => 'form-control'])?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label class="col-sm-3 control-label text-bold" for="">Riwayat Alergi</label>
            <div class="col-sm-9">
                <div class="form-group">
                    <label class="col-sm-3 control-label text-bold" for="">Obat</label>
                    <div class="col-sm-9">
                        <?=Html::activeTextInput($model, 'alergi_obat', ['class' => 'form-control'])?>
                    </div>
                </div>
                <div class="form-group" style="margin-top: 10px">
                    <label class="col-sm-3 control-label text-bold" for="">Makanan</label>
                    <div class="col-sm-9">
                        <?=Html::activeTextInput($model, 'alergi_makanan', ['class' => 'form-control'])?>
                    </div>
                </div>
                <div class="form-group" style="margin-top: 10px">
                    <label class="col-sm-3 control-label text-bold" for="">Lainnya</label>
                    <div class="col-sm-9">
                        <?=Html::activeTextInput($model, 'alergi_lainnya', ['class' => 'form-control'])?>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group" style="margin-top: 10px">
            <label class="col-sm-3 control-label text-bold" for="">Skor Nyeri</label>
            <div class="col-sm-5">
                <?=Html::activeTextInput($model, 'skala', ['class' => 'form-control'])?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label class="col-sm-4 control-label text-bold" for="">Obat obatan yang dikonsumsi</label>
            <div class="col-sm-8">
                <?=Html::activeTextInput($model, 'obat_diberikan', ['class' => 'form-control'])?>
            </div>
        </div>
    </div> -->
</div>
<div class="row">
    <div class="col-md-6">
        <?= $form->field($model, 'riwayat_penyakit_dahulu')->textArea(['rows' => 5])->label($model->getAttributeLabel('riwayat_penyakit_dahulu'),['class' => 'text-bold']); ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'pemeriksaan_fisik')->textArea(['rows' => 5])->label($model->getAttributeLabel('pemeriksaan_fisik'),['class' => 'text-bold']); ?>
    </div>
</div>
<div class="form-group row">
    <div class="col-md-12">
        <?= $form->field($model, 'indikasi_pasien_dirawat')->textArea(['rows' => 5])->label($model->getAttributeLabel('indikasi_pasien_dirawat'), ['class' => 'text-bold']); ?>
    </div>
</div>