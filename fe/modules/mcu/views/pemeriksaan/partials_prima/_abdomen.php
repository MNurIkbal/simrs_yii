<?php
use yii\helpers\ArrayHelper;
use app\widgets\DHAnatomiWidget;
?>

<div class="row">
    <div class="col-md-6">
        <div class="row flex-detail">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <a data-toggle="collapse" href="#abdomen" role="button" aria-expanded="false" aria-controls="abdomen">
                        <div class="panel-heading flex-container">
                            <h6 class="panel-title"><b><?= Yii::t('fe', 'Abdomen'); ?></b></h6>
                            <div>
                                <ul class="icons-list"><li><i id="chevron" class="fa fa-chevron-down"></i></li></ul>
                            </div>
                        </div>
                    </a>
                    <div class="panel-body collapse in collapse multi-collapse" id="abdomen">
                        <?= $form->field($modelFisikNew, 'abdomen_umum_batas_normal')->checkbox() ?>
                        <?= $form->field($modelFisikNew, 'inspeksi')->radioList([0 => 'Datar', 1 => 'Distended'], ['inline' => true, 'class' => 'inspeksi'])->label($modelFisikNew->getAttributeLabel('inspeksi')); ?>
                        <?= $form->field($modelFisikNew, 'auskultasi_abdomen')->radioList([0 => 'Normal', 1 => 'Meningkat', 2 => 'Menurun'], ['inline' => true, 'class' => 'auskultasi_abdomen']); ?>
                        <?= $form->field($modelFisikNew, 'palpasi')->radioList([0 => 'Soepel', 1 => 'Defans Muscular'], ['inline' => true, 'class' => 'palpasi']); ?>
                        <?= $form->field($modelFisikNew, 'perkusi')->radioList([0 => 'Timpani', 1 => 'Shifting Dullnes'], ['inline' => true, 'class' => 'perkusi']); ?>
                        <?= $form->field($modelFisikNew, 'hati')->radioList([0 => 'Tidak Teraba Membesar', 1 => 'Teraba Membesar'], ['inline' => true, 'class' => 'hati']); ?>
                        <?= $form->field($modelFisikNew, 'limfa')->radioList([0 => 'Tidak Teraba', 1 => 'Teraba Schuffner'], ['inline' => true, 'class' => 'limfa']); ?>
                        <?= $form->field($modelFisikNew, 'nyeri_tekan')->radioList($option_adadantiada, ['inline' => true, 'class' => 'nyeri_tekan']); ?>
                        <?= DHAnatomiWidget::widget([
                            'name_id' => 'abdomen',
                            'image' => '@web/media/img/img-pemeriksaan/abdomen_anatomi.png',
                            'option' => ArrayHelper::map($optBagianTubuhabdomen,'bagiantubuhdetail_id','nama_bagiantubuh'),
                            'dataAnatomi' => $dataAnatomiAbdomen,
                            'counter_data' => $counterAbdomen,
                            'size_image' => 12,
                            'size_table' => 12
                        ])
                        ?><br>
                        <table class="table table-bordered table-striped table-hover dataTable no-footer table-framed" id="tabel-r">
                        <thead>
                            <tr class="bg-inverse">
                            <th>Ginjal</th>
                            <th>Kanan</th>
                            <th>Kiri</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            <td>Nyeri Ketok CVA</td>
                            <td><?= $form->field($modelFisikNew, 'nyeri_ketok_CVA_kanan')->radioList($option_adadantiada, ['inline' => true, 'class' => 'nyeri_ketok_CVA_kanan'])->label(false); ?></td>
                            <td><?= $form->field($modelFisikNew, 'nyeri_ketok_CVA_kiri')->radioList($option_adadantiada, ['inline' => true, 'class' => 'nyeri_ketok_CVA_kiri'])->label(false); ?></td>
                            </tr>
                        </tbody>
                        </table>
                        <?= $form->field($modelFisikNew, 'keterangan_abdomen', [])->textInput(['class' => 'form-control input-sm']); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="row flex-detail">
            <div class="col-md-12">
            <div class="panel panel-default">
                <a data-toggle="collapse" href="#extremitas_vertebrata" role="button" aria-expanded="false" aria-controls="extremitas_vertebrata">
                <div class="panel-heading flex-container">
                    <h6 class="panel-title"><b><?= Yii::t('fe', 'Ekstremitas dan Vertebrae'); ?></b></h6>
                    <div>
                    <ul class="icons-list"><li><i id="chevron" class="fa fa-chevron-down"></i></li></ul>
                    </div>
                </div>
                </a>
                <div class="panel-body collapse in collapse  multi-collapse" id="extremitas_vertebrata">
                <?= $form->field($modelFisikNew, 'extremitas_umum_batas_normal')->checkbox() ?>
                <?= $form->field($modelFisikNew, 'range_of_motion')->radioList($option_range_of_motion, ['inline' => true, 'class' => 'range_of_motion'])->label($modelFisikNew->getAttributeLabel('range_of_motion')); ?>
                <div class="row detail">
                    <div class="col-md-4 detail pemeriksaan-fisik">
                    <?= $form->field($modelFisikNew, 'cervical')->radioList($option_extremitas, [
                        'item' => function($index, $label, $name, $checked, $value) {
                        $return = '<label class="modal-radio">';
                        if($checked) {$return .= '<input checked  type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" id="opt_cervical' . $value . '">';}
                        else {$return .= '<input type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" id="opt_cervical' . $value . '">';}
                        $return .= '<i></i>';
                        $return .= '&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
                        $return .= '</label>';
                        return $return;
                        },'inline' => true,
                    ]); ?>
                    </div>
                    <div class="col-md-8 detail">
                    <label for=""></label>
                    <?= $form->field($modelFisikNew, 'note_cervical', [])->textInput(['class' => 'form-control input-sm','disabled' => 'disabled'])->label(false); ?>
                    </div>
                </div>
                <div class="row detail">
                    <div class="col-md-4 detail pemeriksaan-fisik">
                    <?= $form->field($modelFisikNew, 'extremitas_atas')->radioList($option_extremitas, [
                        'item' => function($index, $label, $name, $checked, $value) {
                        $return = '<label class="modal-radio">';
                        if($checked) {$return .= '<input checked  type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" id="extremitas_atas' . $value . '">';}
                        else {$return .= '<input type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" id="extremitas_atas' . $value . '">';}
                        $return .= '<i></i>';
                        $return .= '&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
                        $return .= '</label>';
                        return $return;
                        },'inline' => true,
                    ]); ?>
                    </div>
                    <div class="col-md-8 detail">
                    <label for=""></label>
                    <?= $form->field($modelFisikNew, 'note_extremitas_atas', [])->textInput(['class' => 'form-control input-sm','disabled' => 'disabled'])->label(false); ?>
                    </div>
                </div>
                <div class="row detail">
                    <div class="col-md-4 detail pemeriksaan-fisik">
                    <?= $form->field($modelFisikNew, 'lumbar')->radioList($option_extremitas, [
                        'item' => function($index, $label, $name, $checked, $value) {
                        $return = '<label class="modal-radio">';
                        if($checked) {$return .= '<input checked  type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" id="opt_lumbar' . $value . '">';}
                        else {$return .= '<input type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" id="opt_lumbar' . $value . '">';}
                        $return .= '<i></i>';
                        $return .= '&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
                        $return .= '</label>';
                        return $return;
                        },'inline' => true,
                    ]); ?>
                    </div>
                    <div class="col-md-8 detail">
                    <label for=""></label>
                    <?= $form->field($modelFisikNew, 'note_lumbar', [])->textInput(['class' => 'form-control input-sm','disabled' => 'disabled'])->label(false); ?>
                    </div>
                </div>
                <div class="row detail">
                    <div class="col-md-4 detail pemeriksaan-fisik">
                    <?= $form->field($modelFisikNew, 'extremitas_bawah')->radioList($option_extremitas, [
                        'item' => function($index, $label, $name, $checked, $value) {
                        $return = '<label class="modal-radio">';
                        if($checked) {$return .= '<input checked  type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" id="opt_extremitas_bawah' . $value . '">';}
                        else {$return .= '<input type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" id="opt_extremitas_bawah' . $value . '">';}
                        $return .= '<i></i>';
                        $return .= '&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
                        $return .= '</label>';
                        return $return;
                        },'inline' => true,
                    ]); ?>
                    </div>
                    <div class="col-md-8 detail">
                    <label for=""></label>
                    <?= $form->field($modelFisikNew, 'note_extremitas_bawah', [])->textInput(['class' => 'form-control input-sm','disabled' => 'disabled'])->label(false); ?>
                    </div>
                </div>
                <?= $form->field($modelFisikNew, 'manual_muscle_test')->radioList($option_range_of_motion, ['inline' => true, 'class' => 'manual_muscle_test'])->label($modelFisikNew->getAttributeLabel('manual_muscle_test')); ?>
                <div class="row detail">
                    <div class="col-md-4 detail pemeriksaan-fisik">
                    <?= $form->field($modelFisikNew, 'extremitas_atas_tes')->radioList($option_extremitas, [
                        'item' => function($index, $label, $name, $checked, $value) {
                        $return = '<label class="modal-radio">';
                        if($checked) {$return .= '<input checked  type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" id="opt_extremitas_atas_tes' . $value . '">';}
                        else {$return .= '<input type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" id="opt_extremitas_atas_tes' . $value . '">';}
                        $return .= '<i></i>';
                        $return .= '&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
                        $return .= '</label>';
                        return $return;
                        },'inline' => true,
                    ]); ?>
                    </div>
                    <div class="col-md-8 detail">
                    <label for=""></label>
                    <?= $form->field($modelFisikNew, 'note_extremitas_atas_tes', [])->textInput(['class' => 'form-control input-sm','disabled' => 'disabled'])->label(false); ?>
                    </div>
                </div>
                <div class="row detail">
                    <div class="col-md-4 detail pemeriksaan-fisik">
                    <?= $form->field($modelFisikNew, 'extremitas_bawah_tes')->radioList($option_extremitas, [
                        'item' => function($index, $label, $name, $checked, $value) {
                        $return = '<label class="modal-radio">';
                        if($checked) {$return .= '<input checked  type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" id="opt_extremitas_bawah_tes' . $value . '">';}
                        else {$return .= '<input type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" id="opt_extremitas_bawah_tes' . $value . '">';}
                        $return .= '<i></i>';
                        $return .= '&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
                        $return .= '</label>';
                        return $return;
                        },'inline' => true,
                    ]); ?>
                    </div>
                    <div class="col-md-8 detail">
                    <label for=""></label>
                    <?= $form->field($modelFisikNew, 'note_extremitas_bawah_tes', [])->textInput(['class' => 'form-control input-sm','disabled' => 'disabled'])->label(false); ?>
                    </div>
                </div>
                <?= $form->field($modelFisikNew, 'phallen')->radioList($option_leher, ['inline' => true, 'class' => 'phallen'])->label($modelFisikNew->getAttributeLabel('phallen')); ?>
                <?= $form->field($modelFisikNew, 'reverse_phallen')->radioList($option_leher, ['inline' => true, 'class' => 'reverse_phallen'])->label($modelFisikNew->getAttributeLabel('reverse_phallen')); ?>
                <?= $form->field($modelFisikNew, 'tinnel_sign')->radioList($option_adadantiada, ['inline' => true, 'class' => 'tinnel_sign'])->label($modelFisikNew->getAttributeLabel('tinnel_sign')); ?>
                <?= $form->field($modelFisikNew, 'edema_tungkai')->radioList($option_adadantiada, ['inline' => true, 'class' => 'edema_tungkai'])->label($modelFisikNew->getAttributeLabel('edema_tungkai')); ?>
                <?= $form->field($modelFisikNew, 'keterangan_extremitas', [])->textInput(['class' => 'form-control input-sm']); ?>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>
