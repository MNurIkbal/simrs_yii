<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

?>
<style type="text/css">
    table#table-vital-sign-anestesi .regenerateable > div.static-width {
        min-width: 50px;
        text-align: center;
        margin: auto;
    }
    .intra-operative-anestesi-color.fa-chevron-up,
    .intra-operative-anestesi-color.fa-chevron-down {
        color: #c9922f;
    }
    .intra-operative-anestesi-color.fa-circle {
        color: #14c014;
    }
    table#table-vital-sign-anestesi thead,
    table#table-vital-sign-anestesi tbody,
    table#table-vital-sign-anestesi tr,
    table#table-vital-sign-anestesi th,
    table#table-vital-sign-anestesi td,
    table#table-vital-sign-anestesi div.static-width {
        position: static;
    }
    /**
     * ref https://stackoverflow.com/questions/41214785/create-a-dashed-diagonal-line-with-css-linear-gradient
     */
    table#table-vital-sign-anestesi .line-horizontal-dash {
        position: absolute;
        height: 2px;
        background: url(data:image/svg+xml;utf8;base64,PHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiPjxsaW5lIHN0cm9rZS1kYXNoYXJyYXk9IjUsIDUiICB4MT0iMCIgeTE9IjAiIHgyPSIxMDAlIiB5Mj0iMCIgc3R5bGU9InN0cm9rZTpyZ2IoMTk5LDE3OCwxNDEpO3N0cm9rZS13aWR0aDo1Ii8+PC9zdmc+) no-repeat center center;
    }
    table#table-vital-sign-anestesi .line-topdown-dash {
        position: absolute;
        background: url(data:image/svg+xml;utf8;base64,PHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiPjxsaW5lIHN0cm9rZS1kYXNoYXJyYXk9IjUsIDUiICB4MT0iMCIgeTE9IjAiIHgyPSIxMDAlIiB5Mj0iMTAwJSIgc3R5bGU9InN0cm9rZTpyZ2IoMTk5LDE3OCwxNDEpO3N0cm9rZS13aWR0aDoyIi8+PC9zdmc+) no-repeat center center;
    }
    table#table-vital-sign-anestesi .line-bottomup-dash {
        position: absolute;
        background: url(data:image/svg+xml;utf8;base64,PHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiPjxsaW5lIHN0cm9rZS1kYXNoYXJyYXk9IjUsIDUiICB4MT0iMCIgeTE9IjEwMCUiIHgyPSIxMDAlIiB5Mj0iMCIgc3R5bGU9InN0cm9rZTpyZ2IoMTk5LDE3OCwxNDEpO3N0cm9rZS13aWR0aDoyIi8+PC9zdmc+) no-repeat center center;
    }
</style>

<form id="component-intraoperative" autocomplete="off" data-action="/bedah/informasi-pasien-anestesi/intra-operative?id=<?= $id; ?>">
    <div class="col-md-12">
        <div class="row" id="root-model-intra-operative-anestesi">
            <div class="form-group col-md-2">
                <label class="">Start in induction</label>
                <div class="input-group">
                    <?= Html::activeTextInput($model, 'start_induction', ['class' => 'form-control usetimepicker timeinduction', 'readonly' => !empty($model->anestesiintraopr_id)]); ?>
                    <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                </div>
            </div>
            <div class="form-group col-md-2">
                <label class="">End in induction</label>
                <div class="input-group">
                    <?= Html::activeTextInput($model, 'end_induction', ['class' => 'form-control usetimepicker timeinduction', 'readonly' => !empty($model->anestesiintraopr_id)]); ?>
                    <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                </div>
            </div>
            <div class="form-group col-md-1">
                <label class="">Length Of Anesthesia</label>
                <?= Html::activeHiddenInput($model, 'length_anesthesia', ['class' => 'form-control', 'readonly' => true]); ?>
                <input id="length_anesthesia" class="form-control" value="<?= $model->displayLengthOfInduction(); ?>" title="<?= $model->displayLengthOfInduction(); ?>" readonly/>
                <div id="error_IntraOperativeAnestesiFormlength_anesthesia" class="help-block error"></div>
            </div>
            <div class="form-group col-md-2">
                <label class="">Start Of Surgery</label>
                <div class="input-group">
                    <?= Html::activeTextInput($model, 'start_surgery', ['class' => 'form-control usetimepicker timesurgery']); ?>
                    <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                </div>
            </div>
            <div class="form-group col-md-2">
                <label class="">End Of Surgery</label>
                <div class="input-group">
                    <?= Html::activeTextInput($model, 'end_surgery', ['class' => 'form-control usetimepicker timesurgery']); ?>
                    <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                </div>
            </div>
            <div class="form-group col-md-1">
                <label class="">Length Of Surgery</label>
                <?= Html::activeHiddenInput($model, 'length_surgery', ['class' => 'form-control', 'readonly' => true]); ?>
                <input id="length_surgery" class="form-control" value="<?= $model->displayLengthOfSurgery(); ?>" title="<?= $model->displayLengthOfSurgery(); ?>" readonly/>
                <div id="error_IntraOperativeAnestesiFormlength_surgery" class="help-block error"></div>
            </div>
            <div class="form-group col-md-2">
                <label class="">Patient Exit at</label>
                <div class="input-group">
                    <?= Html::activeTextInput($model, 'patient_exit', ['class' => 'form-control usetimepicker']); ?>
                    <?= Html::activeHiddenInput($model, 'anestesiintraopr_id'); ?>
                    <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                </div>
            </div>
        </div>
        <div class="row" style="margin-top: 15px;">
            <div class="col-sm-12">
                <div class="table-responsive" style="margin-bottom: 20px; position: relative;">
                    <table class="table datatable-basic table-bordered dataTable no-footer" style="width:100%; position: relative;" id="table-vital-sign-anestesi">
                        <thead>
                            <tr class="bg-inverse">
                                <th colspan="3" style="width: 200px !important;"><?=Yii::t('fe', 'Vital Sign'); ?></th>
                                <th class="fullcol" colspan="<?= $generatedTableColumn + 1; ?>"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="bg-inverse" id="generated-time-container">
                                <td style="text-align: center;">RR</td>
                                <td style="text-align: center;">HR</td>
                                <td style="text-align: center;">BP</td>
                                <?php for ($i = 0; $i <= $generatedTableColumn; $i++) : ?>
                                    <td class="regenerateable">
                                        <div class="static-width">
                                            <?= date('H:i', strtotime(sprintf('%s %d minute', $startColumnDateTime, $i * 5))); ?>
                                        </div>
                                    </td>
                                <?php endfor; ?>
                            </tr>
                            <tr id="generated-button-container">
                                <td style="text-align: center;">
                                    <i class="fa fa-circle-o"></i>
                                </td>
                                <td style="text-align: center; color: #14c014;">
                                    <i class="fa fa-circle"></i>
                                </td>
                                <td style="text-align: center; color: #c9922f;">
                                    <div style="width: 50px;">
                                        S <i class="fa fa-chevron-down"></i>
                                        D <i class="fa fa-chevron-up"></i>
                                    </div>
                                </td>
                                <?php for ($i = 0; $i <= $generatedTableColumn; $i++) : ?>
                                    <td class="regenerateable">
                                        <?php $time = date('H:i', strtotime(sprintf('%s %d minute', $startColumnDateTime, $i * 5))); ?>
                                        <div class="static-width" rel="data-<?= $time; ?>">
                                            <button data-target="#modal_backdrop" type="button" data-toggle="modal" action="/bedah/informasi-pasien-anestesi/modal-intra-operative?time=<?= rawurlencode($time); ?>&hr=<?= rawurlencode(ArrayHelper::getValue($vitalSignValues, "{$time}.hr")); ?>&rr=<?= rawurlencode(ArrayHelper::getValue($vitalSignValues, "{$time}.rr")); ?>&systolic=<?= rawurlencode(ArrayHelper::getValue($vitalSignValues, "{$time}.systolic")); ?>&diastolic=<?= rawurlencode(ArrayHelper::getValue($vitalSignValues, "{$time}.diastolic")); ?>" data-options="modal" class="btn btn-success"><i class="fa fa-plus"></i></button>
                                            <input type="hidden" class="intra-operative-anestesi-sign-time" value="<?= $time; ?>">
                                            <input type="hidden" class="intra-operative-anestesi-sign-rr" value="<?= ArrayHelper::getValue($vitalSignValues, "{$time}.rr"); ?>">
                                            <input type="hidden" class="intra-operative-anestesi-sign-hr" value="<?= ArrayHelper::getValue($vitalSignValues, "{$time}.hr"); ?>">
                                            <input type="hidden" class="intra-operative-anestesi-sign-systolic" value="<?= ArrayHelper::getValue($vitalSignValues, "{$time}.systolic"); ?>">
                                            <input type="hidden" class="intra-operative-anestesi-sign-diastolic" value="<?= ArrayHelper::getValue($vitalSignValues, "{$time}.diastolic"); ?>">
                                        </div>
                                    </td>
                                <?php endfor; ?>
                            </tr>
                            <?php foreach ($lookup['vitalSign'] as $inc => $vitalSign) : ?>
                                <tr class="generated-empty-container">
                                    <td style="text-align: center;"><?= $vitalSign['rr']; ?></td>
                                    <td style="text-align: center;"><?= $vitalSign['hr']; ?></td>
                                    <td style="text-align: center;"><?= $vitalSign['bp']; ?></td>
                                    <?php for ($i = 0; $i <= $generatedTableColumn; $i++) : ?>
                                        <td class="regenerateable">
                                            <?php $time = date('H:i', strtotime(sprintf('%s %d minute', $startColumnDateTime, $i * 5))); ?>
                                            <div class="static-width" rel="draw-<?= $time; ?>">
                                                <?php
                                                    echo ArrayHelper::getValue($vitalSignValues, "{$time}.hr") === $vitalSign['hr'] ? '<i class="intra-operative-anestesi-color fa fa-circle"></i>' : '';
                                                    echo ArrayHelper::getValue($vitalSignValues, "{$time}.rr") === $vitalSign['rr'] ? '<i class="intra-operative-anestesi-color fa fa-circle-o"></i>' : '';
                                                    echo ArrayHelper::getValue($vitalSignValues, "{$time}.systolic") === $vitalSign['bp'] ? '<i class="intra-operative-anestesi-color fa fa-chevron-down"></i>' : '';
                                                    echo ArrayHelper::getValue($vitalSignValues, "{$time}.diastolic") === $vitalSign['bp'] ? '<i class="intra-operative-anestesi-color fa fa-chevron-up"></i>' : '';
                                                ?>
                                            </div>
                                        </td>
                                    <?php endfor; ?>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="bg-inverse">
                                <th colspan="3" style="width: 200px !important;"><?=Yii::t('fe', 'Other monitoring'); ?></th>
                                <th class="fullcol" colspan="<?= $generatedTableColumn + 1; ?>"></th>
                            </tr>
                            <?php foreach ($lookup['patientMonitoring'] as $key => $patientMonitoring) : ?>
                                <tr class="generated-input-container">
                                    <td colspan="3" style="width: 200px !important;" rel="<?= $patientMonitoring['lookup_id']; ?>"><?= $patientMonitoring['lookup_name']; ?></td>
                                    <?php for ($i = 0; $i <= $generatedTableColumn; $i++) : ?>
                                        <td class="regenerateable">
                                            <div class="static-width">
                                                <?php
                                                    $time = date('H:i', strtotime(sprintf('%s %d minute', $startColumnDateTime, $i * 5)));
                                                ?>
                                                <input type="text" class="form-control doco-number intra-operative-anestesi-monitoring-vinput" value="<?= ArrayHelper::getValue($monitoringValues, "{$time}.{$patientMonitoring['lookup_id']}.vinput"); ?>" autocomplete="off">
                                                <input type="hidden" class="intra-operative-anestesi-monitoring-time" value="<?= $time; ?>">
                                                <input type="hidden" class="intra-operative-anestesi-monitoring-monitoring_id" value="<?= $patientMonitoring['lookup_id'];?>">
                                            </div>
                                        </td>
                                    <?php endfor; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>

<?php
$this->registerJs($this->render('../js/intraoperative.js'), View::POS_END);
?>