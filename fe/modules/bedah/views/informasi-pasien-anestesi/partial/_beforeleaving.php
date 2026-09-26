<?php

?>


<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

?>

<div id="component-beforeleaving" data-action="/bedah/informasi-pasien-anestesi/before-leaving?id=<?= $id; ?>">
    <div class="col-md-12">
        <!-- first segment -->
        <div class="row">
            <div class="col-md-2">
                <div class="form-group">
                    <label class="control-label">Observation Taken at</label>
                    <?= Html::activeTextInput($model, 'observation_taken', ['type' => 'text', 'class' => 'form-control beforeleavingusetimepicker']) ?>
                </div>
            </div>
        </div>

        <hr>

        <!-- second segment -->
        <div class="row">
            <div>
                <label for="table-1">
                    <strong>Patient Condition at the Time of Arrival</strong>
                </label>
            </div>

            <br>

            <div class="col-md-2">
                <div class="form-group">
                    <label class="control-label">Consciousness</label><br>
                    <div id="error_beforeleavingAnestesiFormconsciousness"></div>
                    <?= Html::activeRadioList($model, 'consciousness', [1 => 'Fully Awake', 2 => 'Sedated'],
                        [
                            'item' => function ($index, $label, $name, $checked, $value) {
                                $html = '<div class="radio">';
                                $html .= '<label>';
                                $html .= '<input type="radio" name="' . $name . '" value="' . $value . '" ' . ($checked ? 'checked' : '') . '>';
                                $html .= '<span>' . ucwords($label) . '</span>';
                                $html .= '</label>';
                                $html .= '</div>';
                                return $html;
                            },
                        ]) ?>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label class="control-label">Respiration</label><br>
                    <div id="error_beforeleavingAnestesiFormrespiration"></div>
                    <?= Html::activeRadioList($model, 'respiration', [1 => 'Controlled', 2 => 'Assisted'],
                        [
                            'item' => function ($index, $label, $name, $checked, $value) {
                                $html = '<div class="radio">';
                                $html .= '<label>';
                                $html .= '<input type="radio" name="' . $name . '" value="' . $value . '" ' . ($checked ? 'checked' : '') . '>';
                                $html .= '<span>' . ucwords($label) . '</span>';
                                $html .= '</label>';
                                $html .= '</div>';
                                return $html;
                            },
                        ]) ?>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label class="control-label">TV</label><br>
                    <div id="error_beforeleavingAnestesiFormtv"></div>
                    <?= Html::activeRadioList($model, 'tv', [1 => 'Normal', 2 => 'Low'],
                        [
                            'item' => function ($index, $label, $name, $checked, $value) {
                                $html = '<div class="radio">';
                                $html .= '<label>';
                                $html .= '<input type="radio" name="' . $name . '" value="' . $value . '" ' . ($checked ? 'checked' : '') . '>';
                                $html .= '<span>' . ucwords($label) . '</span>';
                                $html .= '</label>';
                                $html .= '</div>';
                                return $html;
                            },
                        ]) ?>
                </div>
            </div>
        </div>

        <br>

        <div class="row">
            <div class="col-md-3">
                <div>
                    <label>Hemodinamic</label>
                </div>

                <div class="form-inline form-group">
                    <label>BP:</label>
                    <div class="input-group">
                        <?= Html::activeTextInput($model, 'hemodinamic_bp', ['type' => 'text', 'class' => 'form-control doco-number']) ?>
                        <div class="input-group-addon">mm/HG</div>
                    </div>
                </div>

                <div class="form-inline form-group">
                    <label>HR:</label>
                    <div class="input-group">
                        <?= Html::activeTextInput($model, 'hemodinamic_hr', ['type' => 'text', 'class' => 'form-control doco-number']) ?>
                        <div class="input-group-addon">mm</div>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div>
                    <label>Spontaneous</label>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <?= Html::activeTextInput($model, 'spontaneous', ['type' => 'text', 'class' => 'form-control doco-number']) ?>
                        <div class="input-group-addon">Breath/minute</div>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div>
                    <label>Fio</label>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <?= Html::activeTextInput($model, 'fio', ['type' => 'text', 'class' => 'form-control doco-number']) ?>
                        <div class="input-group-addon">%</div>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div>
                    <label>SpO2</label>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <?= Html::activeTextInput($model, 'spo2', ['type' => 'text', 'class' => 'form-control doco-number']) ?>
                        <div class="input-group-addon">%</div>
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-6">
                <label class="control-label">Drug Support</label>
                <br>
                <button action="/bedah/informasi-pasien-anestesi/modal-before-condition" class="btn btn-xs btn-labeled btn-info" data-toggle="modal" data-target="#modal_backdrop"><b><i class="fa fa-sm fa-plus"></i></b> Tambah</button>
                <br>
                <table id="table-beforeleavingdrugsupport" class="table datatable-basic table-hover dataTable no-footer" style="width: 100%;">
                    <thead>
                        <tr class="bg-inverse">
                            <th><?= Yii::t('fe', 'Drug'); ?></th>
                            <th><?= Yii::t('fe', 'Dose'); ?></th>
                            <th><?= Yii::t('fe', 'Time Delivery'); ?></th>
                            <th width="50"><?= Yii::t('fe', 'Aksi'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($model->drugsupports) : ?>
                            <?php foreach ($model->drugsupports as $index => $drugsupport) : ?>
                                <tr>
                                    <td><?= ArrayHelper::getValue($drugsupport, 'obatalkes_nama'); ?></td>
                                    <td><?= ArrayHelper::getValue($drugsupport, 'dose'); ?></td>
                                    <td><?= date('H:i', strtotime(ArrayHelper::getValue($drugsupport, 'time_delivery'))); ?></td>
                                    <td width="50">
                                        <a class="btn btn-danger btn-sm btn-remove"><i class="fa fa-trash"></i></a>
                                        <input type="hidden" name="BeforeLeavingForm[drugsupports][<?= $index; ?>][obatalkes_id]" value="<?= ArrayHelper::getValue($drugsupport, 'obatalkes_id'); ?>">
                                        <input type="hidden" name="BeforeLeavingForm[drugsupports][<?= $index; ?>][obatalkes_nama]" value="<?= ArrayHelper::getValue($drugsupport, 'obatalkes_nama'); ?>">
                                        <input type="hidden" name="BeforeLeavingForm[drugsupports][<?= $index; ?>][dose]" value="<?= ArrayHelper::getValue($drugsupport, 'dose'); ?>">
                                        <input type="hidden" name="BeforeLeavingForm[drugsupports][<?= $index; ?>][time_delivery]" value="<?= ArrayHelper::getValue($drugsupport, 'time_delivery'); ?>">
                                        <input type="hidden" name="BeforeLeavingForm[drugsupports][<?= $index; ?>][anestesipostoprdrugsupport_id]" value="<?= ArrayHelper::getValue($drugsupport, 'anestesipostoprdrugsupport_id'); ?>">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="col-md-1">
                <div class="form-group">
                    <label class="control-label">Skin Color</label><br>
                    <div id="error_beforeleavingAnestesiFormskin_color"></div>
                    <?= Html::activeRadioList($model, 'skin_color', [1 => 'Normal', 2 => 'Pale'],
                        [
                            'item' => function ($index, $label, $name, $checked, $value) {
                                $html = '<div class="radio">';
                                $html .= '<label>';
                                $html .= '<input type="radio" name="' . $name . '" value="' . $value . '" ' . ($checked ? 'checked' : '') . '>';
                                $html .= '<span>' . ucwords($label) . '</span>';
                                $html .= '</label>';
                                $html .= '</div>';
                                return $html;
                            },
                        ]) ?>
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <label class="control-label">Skin Temperature</label><br>
                    <div id="error_beforeleavingAnestesiFormskin_temperature"></div>
                    <?= Html::activeRadioList($model, 'skin_temperature', [1 => 'Warn', 2 => 'Cold'],
                        [
                            'item' => function ($index, $label, $name, $checked, $value) {
                                $html = '<div class="radio">';
                                $html .= '<label>';
                                $html .= '<input type="radio" name="' . $name . '" value="' . $value . '" ' . ($checked ? 'checked' : '') . '>';
                                $html .= '<span>' . ucwords($label) . '</span>';
                                $html .= '</label>';
                                $html .= '</div>';
                                return $html;
                            },
                        ]) ?>
                </div>
            </div>
        </div>
        <div class="row" style="margin-bottom: 20px; margin-top: 30px; margin-right: -40px">
            <div class="col-sm-9 ml-left">
                <?= Html::activeHiddenInput($model, 'anestesikondisipasien_id'); ?>
                <button style="margin-left: 90%;" type="button" id="btn-save-beforeleaving" class="btn btn-info btn-labeled btn-xs btn-custom-save save-button-patient-condition"><b><i class="fa fa-floppy-o"></i></b>Simpan</button>
            </div>
        </div>
    </div>
</div>

<?php

$this->registerJs($this->render('../js/beforeleaving.js'), View::POS_END);


?>