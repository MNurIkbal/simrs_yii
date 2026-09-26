<?php

/**
 * @Author: Maulana Muhammad Rizky
 * @Date:   2018-08-07 11:03:58
 * @Last Modified by:   Maulana Muhammad Rizky
 * @Last Modified time: 2018-09-05 10:30:58
 */

use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\form\ActiveForm;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Bedah sentral', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php 
    $form = ActiveForm::begin([
        'id'         => 'input-preanestesi', 
        'type'       => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['showErrors' => true,'labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL]
    ]); 
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-body">
                <nav>
                    <div class="nav nav-tabs nav-tab-worklist" id="nav-tab" role="tablist">
                        <a class="nav-item nav-tab-type nav-link active" data-type="all" id="nav-all-tab" data-toggle="tab" href="javacsript:void(0)" role="tab" aria-controls="nav-home" aria-selected="true">Semua Pasien</a>
                        <a class="nav-item nav-tab-type nav-link"  id="nav-rajal-tab" data-toggle="tab" href="javacsript:void(0)" role="tab" aria-controls="nav-home" aria-selected="true">Pasien Rawat Jalan</a>
                        <a class="nav-item nav-tab-type nav-link"  id="nav-mcu-tab" data-toggle="tab" href="javacsript:void(0)" role="tab" aria-controls="nav-home" aria-selected="true">Pasien MCU</a>
                        <a class="nav-item nav-tab-type nav-link"  id="nav-appointment-tab" data-toggle="tab" href="javacsript:void(0)" role="tab" aria-controls="nav-home" aria-selected="true">Appointment</a>
                        <a class="nav-item nav-tab-type nav-link"  id="nav-igd-tab" data-toggle="tab" href="javacsript:void(0)" role="tab" aria-controls="nav-home" aria-selected="true">Pasien Emergency</a>
                        <a class="nav-item nav-tab-type nav-link"  id="nav-ranap-tab" data-toggle="tab" href="javacsript:void(0)" role="tab" aria-controls="nav-home" aria-selected="true">Pasien Rawat Inap</a>
                        <a class="nav-item nav-tab-type nav-link"  id="nav-bedah-tab" data-toggle="tab" href="javacsript:void(0)" role="tab" aria-controls="nav-home" aria-selected="true">Procedure / OT</a>
                    </div>
                </nav>
                <div class="row">
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="d-grid">
                                    <div>Tanggal Operasi</div>
                                    <div style="margin-top: 7px"><b>20-02-2022</b></div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <?=$form->field($model, 'ruangan', [
                                        'horizontalCssClasses' => [
                                            'label'   => 'text-left control-label col-sm-4',
                                        ]
                                    ])->dropDownList([], ['prompt' => '-- Pilih --', 'class' => 'select2 form-control', 'tabindex' => 5])->label(Yii::t('fe', 'Ruangan'));
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
                                        'labelOptions' => ['class' => 'text-left'],
                                    ])->dropDownList([], ['prompt' => '-- Pilih --', 'class' => 'select2 form-control', 'tabindex' => 5, 'style' => 'padding-left: 0px !important'])->label(false);
                                ?>
                            </div>
                        </div>
                        <hr>
                        <div class="row mt-2">
                            <div class="col-md-2">
                                <p>Tinggi Badan</p>
                                    <?=$form->field($model, 'tinggi_badan', [
                                            'addon' => ['append' => ['content'=>'cm', 'options' => ['style' => 'width: 100px']]],
                                        ])->textInput(['class' => 'form-control', 'aria-describedby'=> "basic-addon2", 'addAriaAttributes' => true, 'style' => "width: 50px !important"])->label(false)
                                    ?>             
                            </div>
                            <div class="col-md-2">
                                <p>Berat Badan</p>
                                <?=$form->field($model, 'tinggi_badan', [
                                        'labelOptions' => ['class' => 'text-left'],
                                        'addon' => ['append' => ['content'=>'kg', 'options' => ['style' => 'width: 100px !important']]],                                    
                                    ])->textInput(['class' => "form-control", 'style' => "width: 50px"])->label(false)
                                ?>
                            </div>
                            <div class="col-md-2">
                                <p>Suhu Tubuh</p>
                                <?=$form->field($model, 'suhu_tubuh', [
                                        'labelOptions' => ['class' => 'text-left'],
                                        'addon' => ['append' => ['content'=>'celcius', 'options' => ['style' => 'width: 100px']]],
                                    ])->textInput(['class' => "form-control", 'style' => 'width: 50px'])->label(false)
                                ?>
                            </div>
                            <div class="col-md-2">
                                <p>Pernafasan</p>
                                <?=$form->field($model, 'pernafasan', [
                                        'labelOptions' => ['class' => 'text-left'],
                                        'addon' => ['append' => ['content'=>'/menit']],
                                    ])->textInput(['class' => "form-control", 'style' => 'width: 50px'])->label(false)
                                ?>
                            </div>
                            <div class="col-md-2">
                                <p>Tekanan Darah Systolic</p>
                                <?=$form->field($model, 'tekanan_darah_systolic', [
                                        'labelOptions' => ['class' => 'text-left'],
                                        'addon' => ['append' => ['content'=>'mmHg']],
                                    ])->textInput(['class' => "form-control", 'style' => 'width: 50px'])->label(false)
                                ?>
                            </div>
                            <div class="col-md-2">
                                <p>Tekanan Darah Diastolic</p>
                                <?=$form->field($model, 'tekanan_darah_diastolic', [
                                        'labelOptions' => ['class' => 'text-left'],
                                        'addon' => ['append' => ['content'=>'mmHg']],
                                    ])->textInput(['class' => "form-control", 'style' => 'width: 50px'])->label(false)
                                ?>
                            </div>
                            <div class="col-md-4">
                                <p>Nevous System</p>
                                <div style="padding-left: 10px; padding-right: 10px;">
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
                                            
                                        ])->radioList(["I", "II", "III", "IV", "V", "E"], ['inline' => true])->label(false)
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <p>Medication Taken Within Last Week (Drug & Dose) </p>
                                <div style="padding-left: 10px; padding-right: 10px;">
                                    <?=$form->field($model, 'musculo_skeletal_system', [
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
                                            <th>2</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Central/Pheriperal Venus/Artial</td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="background-color: #F5F5DC;">Site of replacement</td>
                                        </tr>
                                        <tr>
                                            <td>Type</td>
                                            <td>
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-addon" style="border: none; background-color: tranparent;">T:</div>
                                                        <input type="text" class="form-control" id="exampleInputAmount">
                                                    </div>
                                                </div>
                                            </td><td>
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-addon" style="border: none; background-color: tranparent;">T:</div>
                                                        <input type="text" class="form-control" id="exampleInputAmount">
                                                    </div>
                                                </div>
                                            </td><td>
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-addon" style="border: none; background-color: tranparent;">T:</div>
                                                        <input type="text" class="form-control" id="exampleInputAmount">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">Size</td>
                                            <td>
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-addon" style="border: none; background-color: tranparent;">S:</div>
                                                        <input type="text" class="form-control" id="exampleInputAmount">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="4" style="background-color: #F5F5DC;">Time &amp; Place Replacement</td>
                                        </tr>
                                        <tr>
                                            <td>Fluite</td>
                                            <td>
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-addon" style="border: none; background-color: tranparent;">F:</div>
                                                        <input type="text" class="form-control" id="exampleInputAmount">
                                                    </div>
                                                </div>
                                            </td><td>
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-addon" style="border: none; background-color: tranparent;">F:</div>
                                                        <input type="text" class="form-control" id="exampleInputAmount">
                                                    </div>
                                                </div>
                                            </td><td>
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-addon" style="border: none; background-color: tranparent;">F:</div>
                                                        <input type="text" class="form-control" id="exampleInputAmount">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Rate of Influsion</td>
                                            <td>
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-addon" style="border: none; background-color: tranparent;">R:</div>
                                                        <input type="text" class="form-control" id="exampleInputAmount">
                                                        <div class="input-group-addon">ml/menit</div>
                                                    </div>
                                                </div>
                                            </td><td>
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <div class="input-group-addon" style="border: none; background-color: tranparent;">R:</div>
                                                        <input type="text" class="form-control" id="exampleInputAmount">
                                                        <div class="input-group-addon">ml/menit</div>
                                                    </div>
                                                </div>
                                            </td><td>
                                                <div class="input-group">
                                                    <div class="input-group-addon" style="border: none; background-color: tranparent;">R:</div>
                                                    <input type="text" class="form-control" id="exampleInputAmount">
                                                    <div class="input-group-addon">ml/menit</div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Fuild Left in the container/syringer</td>
                                            <td>
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="exampleInputAmount">
                                                        <div class="input-group-addon">ml</div>
                                                    </div>
                                                </div>
                                            </td><td>
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="exampleInputAmount">
                                                        <div class="input-group-addon">ml</div>
                                                    </div>
                                                </div>
                                            </td><td>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="exampleInputAmount">
                                                    <div class="input-group-addon">ml</div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end() ?>
