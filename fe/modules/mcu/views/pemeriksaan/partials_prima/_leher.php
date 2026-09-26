<div class="row">
    <div class="col-md-6">
    <div class="row flex-detail">
        <div class="col-md-12">
            <div class="panel panel-default">
                <a data-toggle="collapse" href="#leher" role="button" aria-expanded="false" aria-controls="leher">
                    <div class="panel-heading flex-container">
                        <h6 class="panel-title"><b><?= Yii::t('fe', 'Leher'); ?></b></h6>
                        <div>
                            <ul class="icons-list"><li><i id="chevron" class="fa fa-chevron-down"></i></li></ul>
                        </div>
                    </div>
                </a>
                <div class="panel-body collapse in collapse  multi-collapse" id="leher">
                    <?= $form->field($modelFisikNew, 'leher_umum_batas_normal')->checkbox() ?>
                    <?= $form->field($modelFisikNew, 'otot_leher')->radioList([0 => 'Negatif', 1 => 'Spasme'], ['inline' => true, 'class' => 'otot_leher'])->label($modelFisikNew->getAttributeLabel('otot_leher')); ?>
                    <?= $form->field($modelFisikNew, 'kelenjar_thyroid')->radioList($option_leher, ['inline' => true, 'class' => 'kelenjar_thyroid'])->label($modelFisikNew->getAttributeLabel('kelenjar_thyroid')); ?>
                    <?= $form->field($modelFisikNew, 'jegular_vein_pressure')->radioList($option_leher, ['inline' => true, 'class' => 'jegular_vein_pressure'])->label($modelFisikNew->getAttributeLabel('jegular_vein_pressure')); ?>
                    <?= $form->field($modelFisikNew, 'trakea')->radioList([0 => 'Sentral', 1 => 'Deviasi'], ['inline' => true, 'class' => 'trakea'])->label($modelFisikNew->getAttributeLabel('trakea')); ?>
                    <?= $form->field($modelFisikNew, 'keterangan_leher', [])->textInput(['class' => 'form-control input-sm']); ?>
                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="col-md-6">
    <div class="row flex-detail">
        <div class="col-md-12">
            <div class="panel panel-default">
                <a data-toggle="collapse" href="#thorax" role="button" aria-expanded="false" aria-controls="thorax">
                    <div class="panel-heading flex-container">
                        <h6 class="panel-title"><b><?= Yii::t('fe', 'Thorax'); ?></b></h6>
                        <div>
                            <ul class="icons-list"><li><i id="chevron" class="fa fa-chevron-down"></i></li></ul>
                        </div>
                    </div>
                </a>
                <div class="panel-body collapse in collapse  multi-collapse" id="thorax">
                    <?= $form->field($modelFisikNew, 'torax_umum_batas_normal')->checkbox() ?>
                    <div class="row detail">
                    <div class="col-md-7 detail pemeriksaan-fisik">
                        <?= $form->field($modelFisikNew, 'bentuk_torax')->radioList([0 => 'Simetris', 1 => 'Asimetris', 2 => 'Deformitas'], [
                        'item' => function($index, $label, $name, $checked, $value) {
                            $return = '<label class="modal-radio">';
                            if($checked) {$return .= '<input checked  type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" id="opt_bentuk_torax' . $value . '">';}
                            else {$return .= '<input type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" id="opt_bentuk_torax' . $value . '">';}
                            $return .= '<i></i>';
                            $return .= '&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
                            $return .= '</label>';
                            return $return;
                        },'inline' => true,
                        ]); ?>
                    </div>
                    <div class="col-md-5 detail">
                        <label for=""></label>
                        <?= $form->field($modelFisikNew, 'note_bentuk_torax', [])->textInput(['class' => 'form-control input-sm','disabled' => 'disabled'])->label(false); ?>
                    </div>
                    </div>
                    <?= $form->field($modelFisikNew, 'mamae')->radioList($option_leher, ['inline' => true, 'class' => 'mamae'])->label($modelFisikNew->getAttributeLabel('mamae')); ?>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
