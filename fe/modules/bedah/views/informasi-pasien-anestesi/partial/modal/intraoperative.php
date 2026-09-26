<?php

use yii\web\View;
use yii\helpers\Html;

?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Vital Sign</h5>
</div>
<div class="modal-body">
    <div id="modal-anestesi-intra-operative">
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="control-label" for="">Time</label>
                    <div class="input-group">
                        <input id="modal-intra-operative-anestesi-time" type="text" class="form-control" value="<?= $time; ?>" readonly>
                        <div class="input-group-addon"><i class="fa fa-clock-o"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="control-label" for="">RR</label>
                    <?= Html::dropDownList('rr', $rr, $ddrr, ['class' => 'form-control select2', 'prompt' => 'Select...', 'id' => 'modal-intra-operative-anestesi-rr']); ?>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="control-label" for="">HR</label>
                    <?= Html::dropDownList('hr', $hr, $ddhr, ['class' => 'form-control select2', 'prompt' => 'Select...', 'id' => 'modal-intra-operative-anestesi-hr']); ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="control-label" for="">Systolic</label>
                    <?= Html::dropDownList('systolic', $systolic, $ddbp, ['class' => 'form-control select2', 'prompt' => 'Select...', 'id' => 'modal-intra-operative-anestesi-systolic']); ?>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="control-label" for="">Diastolic</label>
                    <?= Html::dropDownList('diastolic', $diastolic, $ddbp, ['class' => 'form-control select2', 'prompt' => 'Select...', 'id' => 'modal-intra-operative-anestesi-diastolic']); ?>
                </div>
            </div>
        </div>
        <div class="row" style=" margin-top: 20px;">
            <div class="col-sm-12" style="text-align: right;">
                <button type="button" id="save-button-intra-operative-anestesi" class="btn btn-info btn-labeled btn-xs btn-custom-save">
                    <b><i class="fa fa-floppy-o"></i></b> Simpan
                </button>
            </div>
        </div>
    </div>
</div>
