<div class="row">
    <div class="col-md-6">
    <div class="row flex-detail">
        <div class="col-md-12">
            <div class="panel panel-default">
                <a data-toggle="collapse" href="#hidung" role="button" aria-expanded="false" aria-controls="hidung">
                    <div class="panel-heading flex-container">
                        <h6 class="panel-title"><b><?= Yii::t('fe', 'Hidung'); ?></b></h6>
                        <div>
                            <ul class="icons-list"><li><i id="chevron" class="fa fa-chevron-down"></i></li></ul>
                        </div>
                    </div>
                </a>
                <div class="panel-body collapse in collapse  multi-collapse" id="hidung">
                    <?= $form->field($modelFisikNew, 'hidung_umum_batas_normal')->checkbox() ?>
                    <?= $form->field($modelFisikNew, 'metus_nasi')->radioList($option_leher, ['inline' => true, 'class' => 'metus_nasi'])->label($modelFisikNew->getAttributeLabel('metus_nasi')); ?>
                    <?= $form->field($modelFisikNew, 'septum_nasi')->radioList([0 => 'Normal', 1 => 'Deviasi'], ['inline' => true, 'class' => 'septum_nasi'])->label($modelFisikNew->getAttributeLabel('septum_nasi')); ?>
                    <?= $form->field($modelFisikNew, 'konka_nasal')->radioList($option_leher, ['inline' => true, 'class' => 'konka_nasal'])->label($modelFisikNew->getAttributeLabel('konka_nasal')); ?>
                    <?= $form->field($modelFisikNew, 'nyeri_tekan_sinus')->radioList($option_adadantiada, ['inline' => true, 'class' => 'nyeri_tekan_sinus'])->label($modelFisikNew->getAttributeLabel('nyeri_tekan_sinus')); ?>
                    <?= $form->field($modelFisikNew, 'penciuman')->radioList($option_leher, ['inline' => true, 'class' => 'penciuman'])->label($modelFisikNew->getAttributeLabel('penciuman')); ?>
                    <?= $form->field($modelFisikNew, 'keterangan_hidung', [])->textInput(['class' => 'form-control input-sm']); ?>
                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="col-md-6">
    <div class="row flex-detail">
        <div class="col-md-12">
            <div class="panel panel-default">
                <a data-toggle="collapse" href="#tenggorokan" role="button" aria-expanded="false" aria-controls="tenggorokan">
                    <div class="panel-heading flex-container">
                        <h6 class="panel-title"><b><?= Yii::t('fe', 'Tenggorokan'); ?></b></h6>
                        <div>
                            <ul class="icons-list"><li><i id="chevron" class="fa fa-chevron-down"></i></li></ul>
                        </div>
                    </div>
                </a>
                <div class="panel-body collapse in collapse  multi-collapse" id="tenggorokan">
                    <?= $form->field($modelFisikNew, 'tenggorokan_umum_batas_normal')->checkbox() ?>
                    <?= $form->field($modelFisikNew, 'pharinx')->radioList([0 => 'Normal', 1 => 'Hiperemis'], ['inline' => true, 'class' => 'pharinx'])->label($modelFisikNew->getAttributeLabel('pharinx')); ?>
                    <?= $form->field($modelFisikNew, 'tonsil')->radioList([0 => 'Normal', 1 => 'Hiperemis', 2 => 'Crypta Melebar'], ['inline' => true, 'class' => 'tonsil'])->label($modelFisikNew->getAttributeLabel('tonsil')); ?>
                    <?= $form->field($modelFisikNew, 'ukuran', [])->textInput(['class' => 'form-control input-sm']); ?>
                    <?= $form->field($modelFisikNew, 'palatum')->radioList($option_leher, ['inline' => true, 'class' => 'palatum'])->label($modelFisikNew->getAttributeLabel('palatum')); ?>
                    <?= $form->field($modelFisikNew, 'keterangan_tenggorokan', [])->textInput(['class' => 'form-control input-sm']); ?>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
