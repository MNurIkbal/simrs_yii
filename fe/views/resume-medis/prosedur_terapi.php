<?php
use yii\helpers\Html;
use dosamigos\ckeditor\CKEditor;
?>
<div class="row">
    <div class="col-md-12" id="terapi-row">
        <h6 class="text-label-size text-bold">Lain Lain:</h6>
        <div class="form-group row" style="margin-top: 10px">
            <div class="col-md-12">
                <?= Html::activeTextArea($model, 'lain_lainnya', ['class' => 'form-control', 'rows' => 5]) ?>
            </div>
        </div>

        <h6 class="text-label-size text-bold">Tindakan Medis & Obat-Obatan/Terapi Selama di Rumah Sakit :</h6>
        <button type="button" data-type="tindakan_prosedur" id="search-prosedur-btn" class="btn btn-info btn-labeled btn-xs search-penunjang" style="margin-top:10px;margin-bottom:10px;"><b><i class="fa fa-search"></i></b> Tindakan Medis & Obat-Obatan</button>
        <div class="form-group row" style="margin-top: 10px">
            <div class="col-md-12">

            <?= $form->field($model, 'prosedur',[
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-2',
                                    'wrapper' => 'col-md-8'
                                ],
                            ])->widget(CKEditor::className(), [
                                'options' => ['rows' => 5],
                                'preset' => 'custom',
                                'clientOptions'=>[
                                    'toolbarGroups'=>[
                                        ['name' => 'basicstyles', 'groups' => ['basicstyles', 'cleanup']],
                                        ['name' => 'colors'],
                                    ]
                                ]
                            ])->label(false) ?>
            </div>
        </div>
        <h6 style="margin-left: 3px;">Prosedur Terapi</h5>
        <div class="form-group row">
            <div class="col-md-12" id="terapi-row">
                <?= $form->field($model, 'instruksi_tindakanbmhp')->textArea(['rows' => 5])->label(false); ?>
            </div>
        </div>
    </div>
</div>