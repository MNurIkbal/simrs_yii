<?php
$classForm = 'form-control input-sm';
?>

<div class="row" style="margin-top:20px;margin-left:10px;margin-right:10px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'suhu')->textInput(['class' => $classForm]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'kulit')->textInput(['class' => $classForm]) ?>
    </div>
</div>

<div class="row" style="margin-left:10px;margin-right:10px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'nadi')->textInput(['class' => $classForm]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'gigiAtas')->textInput(['class' => $classForm]) ?>
    </div>
</div>

<div class="row" style="margin-left:10px;margin-right:10px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'pucat')->textInput(['class' => $classForm]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'gigiBawah')->textInput(['class' => $classForm]) ?>
    </div>
</div>

<div class="row" style="margin-left:10px;margin-right:10px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'sianosis')->textInput(['class' => $classForm]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'gigiKiri')->textInput(['class' => $classForm]) ?>
    </div>
</div>

<div class="row" style="margin-left:10px;margin-right:10px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'tonus')->textInput(['class' => $classForm]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'gigiKanan')->textInput(['class' => $classForm]) ?>
    </div>
</div>

<div class="row" style="margin-left:10px;margin-right:10px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'tugor')->textInput(['class' => $classForm]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'caries')->textInput(['class' => $classForm]) ?>
    </div>
</div>

<div class="row" style="margin-left:10px;margin-right:10px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'edema')->textInput(['class' => $classForm]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'tenggorokan')->textInput(['class' => $classForm]) ?>
    </div>
</div>

<div class="row" style="margin-left:10px;margin-right:10px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'ikterus')->textInput(['class' => $classForm]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'tonsil')->textInput(['class' => $classForm]) ?>
    </div>
</div>

<div class="row" style="margin-left:10px;margin-right:10px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'kepala')->textInput(['class' => $classForm]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'perut')->textInput(['class' => $classForm]) ?>
    </div>
</div>

<div class="row" style="margin-left:10px;margin-right:10px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'muka')->textInput(['class' => $classForm]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'ppKanan')->textInput(['class' => $classForm]) ?>
    </div>
</div>

<div class="row" style="margin-left:10px;margin-right:10px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'rambut')->textInput(['class' => $classForm]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'pkKanan')->textInput(['class' => $classForm]) ?>
    </div>
</div>

<div class="row" style="margin-left:10px;margin-right:10px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'ubunBesar')->textInput(['class' => $classForm]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'pdKanan')->textInput(['class' => $classForm]) ?>
    </div>
</div>

<div class="row" style="margin-left:10px;margin-right:10px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'telinga')->textInput(['class' => $classForm]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'prKanan')->textInput(['class' => $classForm]) ?>
    </div>
</div>

<div class="row" style="margin-left:10px;margin-right:10px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'mata')->textInput(['class' => $classForm]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'limpa')->textInput(['class' => $classForm]) ?>
    </div>
</div>

<div class="row" style="margin-left:10px;margin-right:10px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'hidung')->textInput(['class' => $classForm]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'hati')->textInput(['class' => $classForm]) ?>
    </div>
</div>

<div class="row" style="margin-left:10px;margin-right:10px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'bibir')->textInput(['class' => $classForm]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'konsistensi')->textInput(['class' => $classForm]) ?>
    </div>
</div>

<div class="row" style="margin-left:10px;margin-right:10px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'lidah')->textInput(['class' => $classForm]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'permikaan')->textInput(['class' => $classForm]) ?>
    </div>
</div>

<div class="row" style="margin-left:10px;margin-right:10px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'selMulut')->textInput(['class' => $classForm]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'kelLimfa')->textInput(['class' => $classForm]) ?>
    </div>
</div>

<div class="row" style="margin-left:10px;margin-right:10px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'leher')->textInput(['class' => $classForm]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'anggotaGerak')->textInput(['class' => $classForm]) ?>
    </div>
</div>

<div class="row" style="margin-left:10px;margin-right:10px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'bentukDada')->textInput(['class' => $classForm]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'tasbeh')->textInput(['class' => $classForm]) ?>
    </div>
</div>

<div class="row" style="margin-left:10px;margin-right:10px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'jantung')->textInput(['class' => $classForm]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'colVertebralis')->textInput(['class' => $classForm]) ?>
    </div>
</div>

<div class="row" style="margin-left:10px;margin-right:10px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'ictus')->textInput(['class' => $classForm]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'pinggir')->textInput(['class' => $classForm]) ?>
    </div>
</div>

<div class="row" style="margin-left:10px;margin-right:10px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'batasKiri')->textInput(['class' => $classForm]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'nyeriTekan')->textInput(['class' => $classForm]) ?>
    </div>
</div>

<div class="row" style="margin-left:10px;margin-right:10px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'batasKanan')->textInput(['class' => $classForm]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'bprTprAtas')->textInput(['class' => $classForm]) ?>
    </div>
</div>

<div class="row" style="margin-left:10px;margin-right:10px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'batasAtas')->textInput(['class' => $classForm]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'bprTprBawah')->textInput(['class' => $classForm]) ?>
    </div>
</div>

<div class="row" style="margin-left:10px;margin-right:10px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'irama')->textInput(['class' => $classForm]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'bprTprKiri')->textInput(['class' => $classForm]) ?>
    </div>
</div>

<div class="row" style="margin-left:10px;margin-right:10px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'soufle')->textInput(['class' => $classForm]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'bprTprKanan')->textInput(['class' => $classForm]) ?>
    </div>
</div>

<div class="row" style="margin-left:10px;margin-right:10px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'thrill')->textInput(['class' => $classForm]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'kprAprAtas')->textInput(['class' => $classForm]) ?>
    </div>
</div>

<div class="row" style="margin-left:10px;margin-right:10px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'paruParu')->textInput(['class' => $classForm]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'kprAprBawah')->textInput(['class' => $classForm]) ?>
    </div>
</div>

<div class="row" style="margin-left:10px;margin-right:10px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'pp')->textInput(['class' => $classForm]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'kprAprKiri')->textInput(['class' => $classForm]) ?>
    </div>
</div>

<div class="row" style="margin-left:10px;margin-right:10px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'pr')->textInput(['class' => $classForm]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'kprAprKanan')->textInput(['class' => $classForm]) ?>
    </div>
</div>

<div class="row" style="margin-left:10px;margin-right:10px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'pk')->textInput(['class' => $classForm]) ?>
    </div>
</div>

<div class="row" style="margin-left:10px;margin-right:10px;">
    <div class="col-sm-6">
        <?= $form->field($model, 'pd')->textInput(['class' => $classForm]) ?>
    </div>
</div>
