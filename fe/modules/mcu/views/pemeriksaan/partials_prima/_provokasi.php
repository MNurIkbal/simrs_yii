<div class="row">
    <div class="col-md-6">
        <div class="row flex-detail">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <a data-toggle="collapse" href="#cervical" role="button" aria-expanded="false" aria-controls="cervical">
                        <div class="panel-heading flex-container">
                            <h6 class="panel-title"><b><?= Yii::t('fe', 'Tes Provokasi Radiks Cervical'); ?></b></h6>
                            <div>
                                <ul class="icons-list"><li><i id="chevron" class="fa fa-chevron-down"></i></li></ul>
                            </div>
                        </div>
                    </a>
                    <div class="panel-body collapse in collapse  multi-collapse" id="cervical">
                        <?= $form->field($modelFisikNew, 'cervical_umum_batas_normal')->checkbox(['class' => 'detail']) ?>
                        <?= $form->field($modelFisikNew, 'tes_spurling')->radioList($option_cervical, ['inline' => true, 'class' => 'tes_spurling'])->label($modelFisikNew->getAttributeLabel('tes_spurling')); ?>
                        <?= $form->field($modelFisikNew, 'tes_distraksi')->radioList($option_cervical, ['inline' => true, 'class' => 'tes_distraksi'])->label($modelFisikNew->getAttributeLabel('tes_distraksi')); ?>
                        <?= $form->field($modelFisikNew, 'keterangan_cervacal', [])->textInput(['class' => 'form-control input-sm']); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="row flex-detail">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <a data-toggle="collapse" href="#lumbar" role="button" aria-expanded="false" aria-controls="lumbar">
                        <div class="panel-heading flex-container">
                            <h6 class="panel-title"><b><?= Yii::t('fe', 'Tes Provokasi Radiks Lumbar'); ?></b></h6>
                            <div>
                                <ul class="icons-list"><li><i id="chevron" class="fa fa-chevron-down"></i></li></ul>
                            </div>
                        </div>
                    </a>
                    <div class="panel-body collapse in collapse  multi-collapse" id="lumbar">
                        <?= $form->field($modelFisikNew, 'lumbar_umum_batas_normal')->checkbox() ?>
                        <?= $form->field($modelFisikNew, 'tes_lasegue')->radioList($option_cervical, ['inline' => true, 'class' => 'tes_lasegue'])->label($modelFisikNew->getAttributeLabel('tes_lasegue')); ?>
                        <?= $form->field($modelFisikNew, 'tes_braggard')->radioList($option_cervical, ['inline' => true, 'class' => 'tes_braggard'])->label($modelFisikNew->getAttributeLabel('tes_braggard')); ?>
                        <?= $form->field($modelFisikNew, 'keterangan_lumbar', [])->textInput(['class' => 'form-control input-sm']); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
