<?php

use kartik\form\ActiveForm;
use yii\helpers\Html;
use kartik\select2\Select2;

    $form = ActiveForm::begin([
        'id'         => 'input-preanestesi', 
        'type'       => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['showErrors' => true,'labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_MEDIUM],
    ]); 
?>

<style>
    .d-flex {
        display: flex;
    }

    .mr-10 {
        margin-right: 10px;
        margin-top: 7px;
    }
</style>

<div id="component-preanesthic">
    <div class="row">
        <div class="col-md-10">
            <div class="row">
                <?= $form->field($model, 'pasienmasukpenunjang_id')->hiddenInput()->label(false); ?>
                <div class="col-md-2">
                    <div class="d-grid">
                        <div>Tanggal Operasi</div>
                        <div style="margin-top: 7px"><b><?= date('d-m-Y', strtotime($tanggal_operasi)) ?></b></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <p>Ruangan</p>
                    <?=$form->field($model, 'ruangan_id', [
                            'horizontalCssClasses' => [
                                'label'   => 'text-left control-label col-sm-4',
                            ]
                        ])->dropDownList($ruangan, ['prompt' => '-- Pilih --', 'class' => 'select2 form-control', 'tabindex' => 5])->label(false);
                    ?>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-4">
                    <p>Surgical Procedure</p>
                    <?=$form->field($model, 'surgical_procedure')->label(false)->textInput(['placeholder' => $model->getAttributeLabel('surgical_procedure'),'class' => 'form-control input-sm p-0']); ?>
                </div>
                <div class="col-md-4">
                    <p>Surgical Status</p>
                    <?=$form->field($model, 'surgical_status', [
                            'horizontalCssClasses' => [
                                'label'   => 'text-left control-label col-sm-4',
                            ]
                        ])->dropDownList($statusAnesthetic['status_surgical'], ['prompt' => '-- Pilih --', 'class' => 'select2 form-control', 'tabindex' => 5])->label(false);
                    ?>
                </div>
            </div>
            <hr>
            <div class="row mt-2">
                <div class="col-md-2">
                    <p>Tinggi Badan</p>
                    <?=$form->field($model, 'tinggi_badan', [
                            'addon' => ['append' => ['content'=>'cm', 'options' => ['style' => 'width: 100px']]
                        ],
                        ])->textInput(['class' => 'form-control doco-number', 'aria-describedby'=> "basic-addon2", 'addAriaAttributes' => true, 'style' => "width: 50px !important"])->label(false)
                    ?>             
                </div>
                <div class="col-md-2">
                    <p>Berat Badan</p>
                    <?=$form->field($model, 'berat_badan', [
                            'labelOptions' => ['class' => 'text-left'],
                            'addon' => ['append' => ['content'=>'kg', 'options' => ['style' => 'width: 100px !important']]],                                    
                        ])->textInput(['class' => "form-control doco-number", 'style' => "width: 50px"])->label(false)
                    ?>
                </div>
                <div class="col-md-2">
                    <p>Suhu Tubuh</p>
                    <?=$form->field($model, 'suhu_tubuh', [
                            'labelOptions' => ['class' => 'text-left'],
                            'addon' => ['append' => ['content'=>'celcius', 'options' => ['style' => 'width: 100px']]],
                        ])->textInput(['class' => "form-control doco-number", 'style' => 'width: 50px'])->label(false)
                    ?>
                </div>
                <div class="col-md-2">
                    <p>Pernafasan</p>
                    <?=$form->field($model, 'pernafasan', [
                            'labelOptions' => ['class' => 'text-left'],
                            'addon' => ['append' => ['content'=>'/menit', 'options' => ['style' => 'width: 100px']]],
                        ])->textInput(['class' => "form-control doco-number", 'style' => 'width: 50px'])->label(false)
                    ?>
                </div>
                <div class="col-md-2">
                    <p>Tekanan Darah Systolic</p>
                    <?=$form->field($model, 'tekanan_darah_systolic', [
                            'labelOptions' => ['class' => 'text-left'],
                            'addon' => ['append' => ['content'=>'mmHg', 'options' => ['style' => 'width: 100px']]],
                        ])->textInput(['class' => "form-control doco-number", 'style' => 'width: 50px'])->label(false)
                    ?>
                </div>
                <div class="col-md-2">
                    <p>Tekanan Darah Diastolic</p>
                    <?=$form->field($model, 'tekanan_darah_diastolic', [
                            'labelOptions' => ['class' => 'text-left'],
                            'addon' => ['append' => ['content'=>'mmHg', 'options' => ['style' => 'width: 100px']]],
                        ])->textInput(['class' => "form-control doco-number", 'style' => 'width: 50px'])->label(false)
                    ?>
                </div>
                <div class="col-md-4">
                    <p>Nevous System</p>
                    <div style=" padding-left: 10px; padding-right: 10px;">
                        <?=$form->field($model, 'nevous_system', [
                                'addClass' => 'form-group',
                                'labelOptions' => ['class' => 'text-left'],
                                'template' => '<div>{label}</div>{input}{error}{hint}',
                            ])->textarea(['class' => "form-control", 'style' => 'width: 100%'])->label(false)
                        ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <p>Urinary Tracking System</p>
                    <div style="padding-left: 10px; padding-right: 10px;">
                        <?=$form->field($model, 'urinary_tracking_system', [
                                'labelOptions' => ['class' => 'text-left'],
                                'template' => '<div>{label}</div>{input}{error}{hint}',
                            ])->textarea()->label(false)
                        ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <p>Cardiovascular System</p>
                    <div style="padding-left: 10px; padding-right: 10px;">
                        <?=$form->field($model, 'cardiovascular_system', [
                                'labelOptions' => ['class' => 'text-left'],
                                'template' => '<div>{label}</div>{input}{error}{hint}',
                            ])->textarea()->label(false)
                        ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <p>Gastrointestinal System</p>
                    <div style="padding-left: 10px; padding-right: 10px;">
                        <?=$form->field($model, 'gastrointestinal_system', [
                                'labelOptions' => ['class' => 'text-left'],
                                'template' => '<div>{label}</div>{input}{error}{hint}',
                            ])->textarea()->label(false)
                        ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <p>Metabolic System</p>
                    <div style="padding-left: 10px; padding-right: 10px;">
                        <?=$form->field($model, 'metabolic_system', [
                                'labelOptions' => ['class' => 'text-left'],
                                'template' => '<div>{label}</div>{input}{error}{hint}',
                            ])->textarea()->label(false)
                        ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <p>Coexist Condition</p>
                    <div style="padding-left: 10px; padding-right: 10px;">
                        <?=$form->field($model, 'coexist_condition', [
                                'labelOptions' => ['class' => 'text-left'],
                                'template' => '<div>{label}</div>{input}{error}{hint}',
                            ])->textarea()->label(false)
                        ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <p>Respitory System</p>
                    <div style="padding-left: 10px; padding-right: 10px;">
                        <?=$form->field($model, 'respitory_system', [
                                'labelOptions' => ['class' => 'text-left'],
                                'template' => '<div>{label}</div>{input}{error}{hint}',
                            ])->textarea()->label(false)
                        ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <p>Musculo Skeletal System</p>
                    <div style="padding-left: 10px; padding-right: 10px;">
                        <?=$form->field($model, 'musculo_skeletal_system', [
                                'labelOptions' => ['class' => 'text-left'],
                                'template' => '<div>{label}</div>{input}{error}{hint}',
                            ])->textarea()->label(false)
                        ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <p>Status ASA</p>
                    <div style="display: inline;">
                        <?=$form->field($model, 'status_asa', [
                            ])->radioList($statusAnesthetic['status_asa'], ['inline' => true])->label(false)
                        ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <p>Medication Taken Within Last Week (Drug & Dose) </p>
                    <div style="padding-left: 10px;  padding-right: 10px;">
                        <?=$form->field($model, 'medication_taken', [
                                'labelOptions' => ['class' => 'text-left'],
                                'template' => '<div>{label}</div>{input}{error}{hint}',
                            ])->textarea()->label(false)
                        ?>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="example" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th>Intra Vascular Access</th>
                                <th>1</th>
                                <th>2</th>
                                <th>3</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div style="display: flex; justify-content: space-between; margin-top: 7px; margin-bottom: 7px">
                                        <div>Central/Pheriperal</div>
                                        <div> Venus/Artial</div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" style="background-color: #F5F5DC;">Site of replacement</td>
                            </tr>
                            <tr>
                                <td>Type</td>
                                <td>
                                    <div class="form-group d-flex">
                                        <span class="mr-10">T:</span>
                                        <?=$form->field($model, 'type_1', [
                                                'labelOptions' => ['class' => 'text-left'],
                                                'template' => '<div>{label}</div>{input}{error}{hint}',
                                            ])->textInput()->label(false)
                                        ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group d-flex">
                                        <span class="mr-10">T:</span>
                                        <?=$form->field($model, 'type_2', [
                                                'labelOptions' => ['class' => 'text-left'],
                                                'template' => '<div>{label}</div>{input}{error}{hint}',
                                            ])->textInput()->label(false)
                                        ?>
                                    </div>
                                </td><td>
                                    <div class="form-group d-flex">
                                        <span class="mr-10">T:</span>
                                        <?=$form->field($model, 'type_3', [
                                                'labelOptions' => ['class' => 'text-left'],
                                                'template' => '<div>{label}</div>{input}{error}{hint}',
                                            ])->textInput()->label(false)
                                        ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">Size</td>
                                <td>
                                    <div class="form-group d-flex">
                                        <span class="mr-10">S: </span>
                                        <?=$form->field($model, 'size', [
                                                'labelOptions' => ['class' => 'text-left'],
                                                'template' => '<div>{label}</div>{input}{error}{hint}',
                                            ])->textInput()->label(false)
                                        ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" style="background-color: #F5F5DC;">Time &amp; Place Replacement</td>
                            </tr>
                            <tr>
                                <td>Fluite</td>
                                <td>
                                    <div class="form-group d-flex">
                                        <span class="mr-10">F: </span>
                                        <?=$form->field($model, 'fluite_1', [
                                                'labelOptions' => ['class' => 'text-left'],
                                                'template' => '<div>{label}</div>{input}{error}{hint}',
                                            ])->textInput()->label(false)
                                        ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group d-flex">
                                        <span class="mr-10">F: </span>
                                        <?=$form->field($model, 'fluite_2', [
                                                'labelOptions' => ['class' => 'text-left'],
                                                'template' => '<div>{label}</div>{input}{error}{hint}',
                                            ])->textInput()->label(false)
                                        ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group d-flex">
                                        <span class="mr-10">F: </span>
                                        <?=$form->field($model, 'fluite_3', [
                                                'labelOptions' => ['class' => 'text-left'],
                                                'template' => '<div>{label}</div>{input}{error}{hint}',
                                            ])->textInput()->label(false)
                                        ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Rate of Influsion</td>
                                <td>
                                    <div class="form-group d-flex">
                                        <span class="mr-10">R: </span>
                                        <?=$form->field($model, 'rate_of_influsion_1', [
                                                'addon' => ['append' => ['content'=>'ml/menit']],
                                                'labelOptions' => ['class' => 'text-left'],
                                                'template' => '<div>{label}</div>{input}{error}{hint}',
                                            ])->textInput(['class' => "doco-number"])->label(false)
                                        ?>
                                    </div>
                                </td><td>
                                    <div class="form-group d-flex">
                                        <span class="mr-10">R: </span>
                                        <?=$form->field($model, 'rate_of_influsion_2', [
                                                'addon' => ['append' => ['content'=>'ml/menit']],
                                                'labelOptions' => ['class' => 'text-left'],
                                                'template' => '<div>{label}</div>{input}{error}{hint}',
                                            ])->textInput(['class' => "doco-number"])->label(false)
                                        ?>
                                    </div>
                                </td><td>
                                    <div class="form-group d-flex">
                                        <span class="mr-10">R: </span>
                                        <?=$form->field($model, 'rate_of_influsion_3', [
                                                'addon' => ['append' => ['content'=>'ml/menit']],
                                                'labelOptions' => ['class' => 'text-left'],
                                                'template' => '<div>{label}</div>{input}{error}{hint}',
                                            ])->textInput(['class' => "doco-number"])->label(false)
                                        ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Fuild Left in the container/syringer</td>
                                <td>
                                    <div class="form-group">
                                        <?=$form->field($model, 'fluid_left_1', [
                                                'addon' => ['append' => ['content'=>'/ml']],
                                                'labelOptions' => ['class' => 'text-left'],
                                                'template' => '<div>{label}</div>{input}{error}{hint}',
                                            ])->textInput(['class' => "doco-number"])->label(false)
                                        ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <?=$form->field($model, 'fluid_left_2', [
                                                'addon' => ['append' => ['content'=>'/ml']],
                                                'labelOptions' => ['class' => 'text-left'],
                                                'template' => '<div>{label}</div>{input}{error}{hint}',
                                            ])->textInput(['class' => "doco-number"])->label(false)
                                        ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <?=$form->field($model, 'fluid_left_3', [
                                                'addon' => ['append' => ['content'=>'/ml']],
                                                'labelOptions' => ['class' => 'text-left'],
                                                'template' => '<div>{label}</div>{input}{error}{hint}',
                                            ])->textInput(['class' => "doco-number"])->label(false)
                                        ?>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <hr class="m-2">
                </div>
                <div class="col-md-12">
                    <div class="d-flex" style="justify-content: end;">
                        <?= Html::submitButton('<b><i class="fa fa-floppy-o"></i></b>'.Yii::t('fe', ' Simpan'), 
                            [
                                'class' => 'btn btn-info btn-labeled btn-xs btn-simpan',
                                'id' => 'btn-save-preanesthetic',
                                'disabled' => false,
                            ]);
                        ?> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end() ?>
<?php $this->registerJs("

    $('.select2').select2({
        placeholder: '--Pilih--',
        dropdownCssClass: 'bigdrop',
        escapeMarkup: function (m) { return m; },
    });

    $('#input-preanestesi').docoForm('submit',{
        url : '/bedah/informasi-pasien-anestesi/save-pre-anesthetic',
        method : 'POST',
        type : 'json',
        data: $(this).serializeArray(),
        success : function(data) {
            $('#modal_backdrop').modal('hide');
        },
    });
") ?>