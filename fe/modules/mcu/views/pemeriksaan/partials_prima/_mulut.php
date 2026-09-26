<div class="row">
    <div class="col-md-12">
        <div class="row flex-detail">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <a data-toggle="collapse" href="#mulut" role="button" aria-expanded="false" aria-controls="mulut">
                        <div class="panel-heading flex-container">
                            <h6 class="panel-title"><b><?= Yii::t('fe', 'Mulut'); ?></b></h6>
                            <div>
                                <ul class="icons-list"><li><i id="chevron" class="fa fa-chevron-down"></i></li></ul>
                            </div>
                        </div>
                    </a>
                    <div class="panel-body collapse in collapse  multi-collapse" id="mulut">
                        <div class="col-md-12"><?= $form->field($modelFisikNew, 'mulut_umum_batas_normal')->checkbox() ?></div>
                        <div class="col-md-6">
                        <?= $form->field($modelFisikNew, 'bibir')->radioList($option_leher, ['inline' => true, 'class' => 'bibir'])->label($modelFisikNew->getAttributeLabel('bibir')); ?>
                        <?= $form->field($modelFisikNew, 'gusi')->radioList($option_leher, ['inline' => true, 'class' => 'gusi'])->label($modelFisikNew->getAttributeLabel('gusi')); ?>
                        </div>
                        <div class="col-md-6">
                        <?= $form->field($modelFisikNew, 'lidah')->radioList($option_leher, ['inline' => true, 'class' => 'lidah'])->label($modelFisikNew->getAttributeLabel('lidah')); ?>
                        <?= $form->field($modelFisikNew, 'mukosa')->radioList([0 => 'Basah', 1 => 'Kering'], ['inline' => true, 'class' => 'mukosa'])->label($modelFisikNew->getAttributeLabel('mukosa')); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
