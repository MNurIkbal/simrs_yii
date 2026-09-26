<div class="row">
    <div class="col-md-6">
        <div class="row flex-detail">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <a data-toggle="collapse" href="#kepala" role="button" aria-expanded="false" aria-controls="kepala">
                        <div class="panel-heading flex-container">
                            <h6 class="panel-title"><b><?= Yii::t('fe', 'Kepala'); ?></b></h6>
                            <div>
                                <ul class="icons-list"><li><i id="chevron" class="fa fa-chevron-down"></i></li></ul>
                            </div>
                        </div>
                    </a>
                    <div class="panel-body collapse in collapse  multi-collapse" id="kepala">
                        <?= $form->field($modelFisikNew, 'kepala_umum_batas_normal')->checkbox() ?>
                        <?= $form->field($modelFisikNew, 'bentuk_wajah')->radioList($option_leher, ['inline' => true, 'class' => 'bentuk_wajah'])->label($modelFisikNew->getAttributeLabel('bentuk_wajah')); ?>
                        <?= $form->field($modelFisikNew, 'kulit_kepala')->radioList($option_leher, ['inline' => true, 'class' => 'kulit_kepala'])->label('Kulit Kepala'); ?>
                        <?= $form->field($modelFisikNew, 'rambut')->radioList($option_leher, ['inline' => true, 'class' => 'rambut'])->label($modelFisikNew->getAttributeLabel('rambut')); ?>
                        <?= $form->field($modelFisikNew, 'keterangan_kepala', [])->textInput(['class' => 'form-control input-sm']); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="row flex-detail">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <a data-toggle="collapse" href="#saraf" role="button" aria-expanded="false" aria-controls="saraf">
                        <div class="panel-heading flex-container">
                            <h6 class="panel-title"><b><?= Yii::t('fe', 'Saraf'); ?></b></h6>
                            <div>
                                <ul class="icons-list"><li><i id="chevron" class="fa fa-chevron-down"></i></li></ul>
                            </div>
                        </div>
                    </a>
                    <div class="panel-body collapse in collapse  multi-collapse" id="saraf">
                      <?= $form->field($modelFisikNew, 'saraf_umum_batas_normal')->checkbox() ?>
                      <?= $form->field($modelFisikNew, 'motorik')->radioList($option_leher, ['inline' => true, 'class' => 'motorik'])->label($modelFisikNew->getAttributeLabel('motorik')); ?>
                      <?= $form->field($modelFisikNew, 'sensorik')->radioList($option_leher, ['inline' => true, 'class' => 'sensorik'])->label($modelFisikNew->getAttributeLabel('sensorik')); ?>
                      <?= $form->field($modelFisikNew, 'ref_fisologis')->radioList($option_leher, ['inline' => true, 'class' => 'ref_fisologis'])->label($modelFisikNew->getAttributeLabel('ref_fisologis')); ?>
                      <?= $form->field($modelFisikNew, 'ref_patologis')->radioList($option_adadantiada, ['inline' => true, 'class' => 'ref_patologis'])->label($modelFisikNew->getAttributeLabel('ref_patologis')); ?>
                      <?= $form->field($modelFisikNew, 'n_facialis')->radioList([0 => 'Normal', 1 => 'Parese'], ['inline' => true, 'class' => 'n_facialis'])->label($modelFisikNew->getAttributeLabel('n_facialis')); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
