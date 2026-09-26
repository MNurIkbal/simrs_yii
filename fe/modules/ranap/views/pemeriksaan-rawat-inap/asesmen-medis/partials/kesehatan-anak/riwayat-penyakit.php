<div class="row">
    <p style="margin-left:20px;margin-top:20px;margin-bottom:20px;font-weight:bold;">Riwayat Penyakit </p>
</div>
<div class="row" style="margin-bottom:10px;margin-left:15px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'pemeriksaanFisik')
            ->textArea(['class' => 'form-control']); ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'laboratorium')
            ->textArea(['class' => 'form-control']); ?>
    </div>
</div>
<div class="row" style="margin-bottom:10px;margin-left:15px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'diagnosaKerja')
            ->textInput(['class' => 'form-control']); ?>
    </div>
</div>
