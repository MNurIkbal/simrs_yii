<div class="row">
    <div class="col-md-12">
        <div class="row flex-detail">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <a data-toggle="collapse" href="#jantung" role="button" aria-expanded="false" aria-controls="jantung">
                        <div class="panel-heading flex-container">
                            <h6 class="panel-title"><b><?= Yii::t('fe', 'Jantung'); ?></b></h6>
                            <div>
                                <ul class="icons-list"><li><i id="chevron" class="fa fa-chevron-down"></i></li></ul>
                            </div>
                        </div>
                    </a>
                    <div class="panel-body collapse in collapse  multi-collapse"  id="jantung">
                        <?= $form->field($modelFisikNew, 'jantung_umum_batas_normal')->checkbox() ?>
                        <div class="row detail">
                        <div class="col-md-4 detail pemeriksaan-fisik">
                            <?= $form->field($modelFisikNew, 'bunyi_jantung')->radioList([0 => 'Murni', 1 => 'Reguler', 2 => 'Irreguler', 3 => 'Suara Tambahan'], [
                            'item' => function($index, $label, $name, $checked, $value) {
                                $return = '<label class="modal-radio">';
                                if($checked) {$return .= '<input checked  type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" id="opt_bunyi_jantung' . $value . '">';}
                                else {$return .= '<input type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" id="opt_bunyi_jantung' . $value . '">';}
                                $return .= '<i></i>';
                                $return .= '&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
                                $return .= '</label>';
                                return $return;
                            },'inline' => true,
                            ]); ?>
                        </div>
                        <div class="col-md-8 detail detail-ui">
                            <label for=""></label>
                            <?= $form->field($modelFisikNew, 'note_bunyi_jantung', [])->textInput(['class' => 'form-control input-sm','disabled' => 'disabled'])->label(false); ?>
                        </div>
                        </div>
                        <?= $form->field($modelFisikNew, 'ictus_cordis')->radioList([0 => 'Teraba', 1 => 'Tidak Teraba'], ['inline' => true, 'class' => 'ictus_cordis'])->label($modelFisikNew->getAttributeLabel('ictus_cordis')); ?>
                        <div class="row detail">
                        <div class="col-md-2 detail pemeriksaan-fisik">
                            <?= $form->field($modelFisikNew, 'batas_kiri_jantung')->radioList([0 => 'lateral', 1 => 'Medial'], [
                                'item' => function($index, $label, $name, $checked, $value) {
                                $return = '<label class="modal-radio">';
                                if($checked) {$return .= '<input checked  type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" id="opt_batas_kiri_jantung' . $value . '">';}
                                else {$return .= '<input type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" id="opt_batas_kiri_jantung' . $value . '">';}
                                $return .= '<i></i>';
                                $return .= '&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
                                $return .= '</label>';
                                return $return;
                                },'inline' => true,
                            ]); ?>
                        </div>
                        <div class="col-md-10 detail detail-ui">
                            <label for=""></label>
                            <?= $form->field($modelFisikNew, 'note_batas_kiri_jantung', [
                            'addon' => ['append' => ['content' => 'cm LMCS']],
                            ])->textInput([
                            'class' => 'form-control input-sm doco-number',
                            'tabindex' => '2','disabled' => 'disabled'
                            ])->label(false); ?>
                        </div>
                        </div>
                        <?= $form->field($modelFisikNew, 'keterangan_jantung')->textArea([
                        'class' => 'form-control input-sm',
                        'rows' => 3
                        ]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
