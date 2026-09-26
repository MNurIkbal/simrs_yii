<?php
    use yii\helpers\Html;
    use kartik\widgets\ActiveForm;
    use yii\web\View;

?>
<?php $form = ActiveForm::begin([
    'id' => 'form-rujukan-khusus',
    'enableClientValidation' => false,
    'enableAjaxValidation' => false
]) ?>
<div class="panel panel-white">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <h6><b><?= Yii::t('fe', 'Create Rencana Kontrol/Inap') ?></b></h6>
                <div class="col ml-5 form-wrapper">
                    <div  class="row justify-content-center" style="" id="form-diagnosa">
                        <div class="col-md-3" >
                        <?=  $form->field($data, 'type_diagnosa')
                            ->radioList(
                                [
                                    'P'=> Yii::t('fe', 'Primer'),
                                    'S'=> Yii::t('fe', 'Skunder'),
                                ],
                                ['id'=>'type_diagnosa', 'name'=>'type_diagnosa', 'inline'=>true, '']
                            ); ?>
                        </div>
                        <div class="col-md-3">
                        <?php
                        echo $form->field($data, 'diagnosa_rujukan')
                            ->dropDownList([],
                                [
                                    'id'=>'diagnosa_rujukan',
                                    'name'=>'diagnosa_rujukan',
                                    'class'=>'select2Diagnosa',
                                    'prompt'=>'— PILIH —',
                                ]
                            )->label(Yii::t('fe', 'Diagnosa'));
                        ?>
                        </div>
                       
                        <div class="col-md-3">
                            <button type="button" class="btn btn-info btn-sm btn-add-form-diagnosa mr-10 mt-10" style=""><b><i class="fa fa-plus"></i></b></button>
                        </div>
                    </div>

                    <div class="row mt-10">
                        <table id="table-diagnosa" class="table table-striped table-condensed table-hover" style="width:65%">
                            <thead>
                                <tr>
                                    <th>Type Diagnosa</th>
                                    <th>Diagnosa</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-diagnosa">
                            </tbody>
                        </table>
                        <div class="help-block" id="error-diagnosa" style="color: red; display:none;"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> &nbsp;Diagnosa cannot be blank.</div>
                    </div>

                    <div class="row mt-10 pt-10 ">
                    </div>
                    <div  class="row mt-10" style="" id="form procedure">
                        <div class="col-md-3">
                        <?php
                        echo $form->field($data, 'procedure')
                            ->dropDownList([],
                                [
                                    'id'=>'procedure',
                                    'name'=>'procedure',
                                    'class'=>'select2Procedure',
                                    'prompt'=>'— PILIH —',
                                ]
                            )->label(Yii::t('fe', 'Procedure'));
                        ?>
                        </div>
                        <div class="col-md-3">

                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-info btn-sm btn-add-form-procedure mr-10 mt-10" style=""><b><i class="fa fa-plus"></i></b></button>
                        </div>
                    </div>
                    
                    <div class="row mt-10">
                        <table id="table-procedure" class="table table-striped table-condensed table-hover" style="width:65%">
                            <thead>
                                <tr>
                                    <th>Procedure</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-procedure">
                            </tbody>
                        </table>
                        <div class="help-block" id="error-procedure" style="color: red; display:none;"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> &nbsp;Procedure cannot be blank.</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-10 pt-10 ">
                    </div>
        <div class="row mt-10 mb-10">
            <div class="col-md-6">
            <button type="button" class="btn btn-secondary btn-sm btn-hapus mr-10" style="width: 20%;"><b><?=Yii::t('fe','Batal')?></b></button>
            <button type="submit" class="btn btn-info btn-sm btn-simpan mr-10" style="width: 20%;"><b><?=Yii::t('fe','Simpan')?></b></button>
            </div>
            
        </div>
</div>
<?php ActiveForm::end(); ?>

<div id="modal_pencarian_spesialis" class="modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div>
    </div>
</div>
<?php
$this->registerJs('
', View::POS_END, 'form');