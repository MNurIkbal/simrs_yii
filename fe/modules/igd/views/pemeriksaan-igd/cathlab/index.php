<?php
use app\components\DHtml;
use yii\web\View;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use app\components\DocoHelpers;
use kartik\datetime\DateTimePicker;
use yii\helpers\Url;
?>
 
<div class="panel panel-body">
    <div class="panel panel-default panel-shadow">
        <div class="panel-toolbar clearfix">
            <?=DocoHelpers::generateToolbar([
                'custom-save' => [
                    'title' => \Yii::t('fe', 'Simpan'),
                    'icon' => 'fa fa-save',
                    'attributes'=>[
                        'id' => 'submit-cathlab',
                        'data-options'=>'click'
                    ]
                ],
                'print-implementasi' => [
                    'type' => 'link',
                    'title' => Yii::t('fe', 'Cetak'),
                    'icon' => 'fa fa-print',
                    'attributes' => [
                        'id' => 'btn-cetak-implementasi',
                        'url' => $linkcetak,
                        'data-options' => 'link',
                        'data-target' => $linkcetak,
                        'target' => '_blank'
                    ]
                ],
            ],'');?>
        </div>  
        <div class="panel-body panel-body-collapse-form-cathlab">
            <div class="row">
                <div class="form-cathlab" style="padding: 12px;">
                    <?php
                    $form = ActiveForm::begin([
                        'id' => 'form-cathlab',
                        'enableAjaxValidation'=>false,
                        'enableClientValidation'=>false,
                        'type' => ActiveForm::TYPE_HORIZONTAL,
                        'formConfig' => [
                            'labelSpan' => 3,
                            'deviceSize' => ActiveForm::SIZE_SMALL,
                        ],
                    ]);
                    ?>
                    
                    <div class="row form-row">
                        <div class="col-sm-10">
                            <?php $hidden = $tipe == 'koroangiografi' ? '' : ( $tipe == 'pci' ? '' : ($tipe == 'dsa' ? '' : '') ); ?>
                            <div class="row form-row <?=$hidden?>">
                                <div class="col-sm-3">
                                    <label for="control-label text-label"><b>INDIKASI</b></label>
                                </div>
                                <div class="col-sm-9 form-group">
                                    <?=
                                        Html::activeTextarea($model, 'indikasi', ['class' => 'form-control']);
                                    ?>
                                    <div class="help-block">
                                    </div>
                                </div>
                            </div>
                        
                            <?php $hidden = $tipe == 'koroangiografi' ? '' : ( $tipe == 'pci' ? '' : ($tipe == 'dsa' ? '' : '') ); ?>
                            <div class="row form-row <?=$hidden?>">
                                <div class="col-sm-3">
                                    <label for="control-label text-label"><b>APPROACH</b></label>
                                </div>
                                <div class="col-sm-9 form-group">
                                    <?=
                                        Html::activeTextarea($model, 'approach', ['class' => 'form-control']);
                                    ?>
                                    <div class="help-block">
                                    </div>
                                </div>
                            </div>
                        
                            <?php $hidden = $tipe == 'koroangiografi' ? '' : ( $tipe == 'pci' ? 'hidden' : ($tipe == 'dsa' ? 'hidden' : '') ); ?>
                            <div class="row form-row <?=$hidden?>">
                                <div class="col-sm-3">
                                    <label for="control-label text-label"><b>KORONER</b></label>
                                </div>
                                <div class="col-sm-9 form-group">
                                    <?=
                                        Html::activeTextarea($model, 'koroner', ['class' => 'form-control']);
                                    ?>
                                    <div class="help-block">
                                    </div>
                                </div>
                            </div>

                            <?php $hidden = $tipe == 'koroangiografi' ? 'hidden' : ( $tipe == 'pci' ? '' : ($tipe == 'dsa' ? 'hidden' : '') ); ?>
                            <div class="row form-row <?=$hidden?>">
                                <div class="col-sm-3">
                                    <label for="control-label text-label"><b>TARGET</b></label>
                                </div>
                                <div class="col-sm-9 form-group">
                                    <?=
                                        Html::activeTextarea($model, 'target', ['class' => 'form-control']);
                                    ?>
                                    <div class="help-block">
                                    </div>
                                </div>
                            </div>
                        
                            <?php $hidden = $tipe == 'koroangiografi' ? '' : ( $tipe == 'pci' ? '' : ($tipe == 'dsa' ? 'hidden' : '') ); ?>
                            <div class="row form-row <?=$hidden?>">
                                <div class="col-sm-3">
                                    <label for="control-label text-label"><b>LM</b></label>
                                </div>
                                <div class="col-sm-9 form-group">
                                    <?=
                                        Html::activeTextInput($model, 'lm',['class' => 'form-control']);
                                    ?>
                                    <div class="help-block">
                                    </div>
                                </div>
                            </div>
                        
                            <?php $hidden = $tipe == 'koroangiografi' ? '' : ( $tipe == 'pci' ? '' : ($tipe == 'dsa' ? 'hidden' : '') ); ?>
                            <div class="row form-row <?=$hidden?>">
                                <div class="col-sm-3">
                                    <label for="control-label text-label"><b>LAD</b></label>
                                </div>
                                <div class="col-sm-9 form-group">
                                    <?=
                                        Html::activeTextInput($model, 'lad', ['class' => 'form-control']);
                                    ?>
                                    <div class="help-block">
                                    </div>
                                </div>
                            </div>
                        
                            <?php $hidden = $tipe == 'koroangiografi' ? '' : ( $tipe == 'pci' ? '' : ($tipe == 'dsa' ? 'hidden' : '') ); ?>
                            <div class="row form-row <?=$hidden?>">
                                <div class="col-sm-3">
                                    <label for="control-label text-label"><b>LCX</b></label>
                                </div>
                                <div class="col-sm-9 form-group">
                                    <?=
                                        Html::activeTextInput($model, 'lcx', ['class' => 'form-control']);
                                    ?>
                                    <div class="help-block">
                                    </div>
                                </div>
                            </div>
                        
                            <?php $hidden = $tipe == 'koroangiografi' ? '' : ( $tipe == 'pci' ? '' : ($tipe == 'dsa' ? 'hidden' : '') ); ?>
                            <div class="row form-row <?=$hidden?>">
                                <div class="col-sm-3">
                                    <label for="control-label text-label"><b>RCA</b></label>
                                </div>
                                <div class="col-sm-9 form-group">
                                    <?=
                                        Html::activeTextInput($model, 'rca', ['class' => 'form-control']);
                                    ?>
                                    <div class="help-block">
                                    </div>
                                </div>
                            </div>
                        
                            <?php $hidden = $tipe == 'koroangiografi' ? '' : ( $tipe == 'pci' ? 'hidden' : ($tipe == 'dsa' ? 'hidden' : '') ); ?>
                            <div class="row form-row <?=$hidden?>">
                                <div class="col-sm-3">
                                    <label for="control-label text-label"><b>Lain-lain</b></label>
                                </div>
                                <div class="col-sm-9 form-group">
                                    <?=
                                        Html::activeTextarea($model, 'lain_lain', ['class' => 'form-control']);
                                    ?>
                                    <div class="help-block">
                                    </div>
                                </div>
                            </div>

                            <?php $hidden = $tipe == 'koroangiografi' ? 'hidden' : ( $tipe == 'pci' ? '' : ($tipe == 'dsa' ? 'hidden' : '') ); ?>
                            <div class="row form-row <?=$hidden?>">
                                <div class="col-sm-3">
                                    <label for="control-label text-label"><b>LAPORAN PCI</b></label>
                                </div>
                                <div class="col-sm-9 form-group">
                                    <?=
                                        Html::activeTextarea($model, 'laporan_pci', ['class' => 'form-control']);
                                    ?>
                                    <div class="help-block">
                                    </div>
                                </div>
                            </div>

                            <?php $hidden = $tipe == 'koroangiografi' ? 'hidden' : ( $tipe == 'pci' ? 'hidden' : ($tipe == 'dsa' ? '' : '') ); ?>
                            <div class="row form-row <?=$hidden?>">
                                <div class="col-sm-3">
                                    <label for="control-label text-label"><b>LAPORAN DSA</b></label>
                                </div>
                                <div class="col-sm-9 form-group">
                                    <?=
                                        Html::activeTextarea($model, 'laporan_dsa', ['class' => 'form-control']);
                                    ?>
                                    <div class="help-block">
                                    </div>
                                </div>
                            </div>
                            
                            <?php $hidden = $tipe == 'koroangiografi' ? '' : ( $tipe == 'pci' ? '' : ($tipe == 'dsa' ? '' : '') ); ?>
                            <div class="row form-row <?=$hidden?>">
                                <div class="col-sm-3">
                                    <label for="control-label text-label"><b>KESIMPULAN</b></label>
                                </div>
                                <div class="col-sm-9 form-group">
                                    <?=
                                        Html::activeTextarea($model, 'kesimpulan', ['class' => 'form-control']);
                                    ?>
                                    <div class="help-block">
                                    </div>
                                </div>
                            </div>
                        
                            <?php $hidden = $tipe == 'koroangiografi' ? '' : ( $tipe == 'pci' ? '' : ($tipe == 'dsa' ? '' : '') ); ?>
                            <div class="row form-row <?=$hidden?>">
                                <div class="col-sm-3">
                                    <label for="control-label text-label"><b>SARAN</b></label>
                                </div>
                                <div class="col-sm-9 form-group">
                                    <?=
                                        Html::activeTextarea($model, 'saran', ['class' => 'form-control']);
                                    ?>
                                    <div class="help-block">
                                    </div>
                                </div>
                            </div>
                        
                            <?php $hidden = $tipe == 'koroangiografi' ? '' : ( $tipe == 'pci' ? '' : ($tipe == 'dsa' ? 'hidden' : '') ); ?>
                            <div class="row form-row <?=$hidden?>">
                                <div class="col-sm-3">
                                    <label for="control-label text-label"><b>DOSIS</b></label>
                                </div>
                                <div class="col-sm-9 form-group">
                                    <div class="row form-row">
                                        <div class="col-sm-6">
                                            <?=$form->field($model, 'cum_air_kerma', ['addon' => ['append' => ['content'=>'MGy']]])->textInput(); ?>
                                            <?=$form->field($model, 'fluo_time', ['addon' => ['append' => ['content'=>'Menit']]])->textInput(); ?>
                                            <?=$form->field($model, 'procedure_time', ['addon' => ['append' => ['content'=>'Menit']]])->textInput(); ?>
                                        </div>
                                        <div class="col-sm-6">
                                            <?=$form->field($model, 'cum_dap', ['addon' => ['append' => ['content'=>'MGycm2']]])->textInput(); ?>
                                            <?=$form->field($model, 'kontras', ['addon' => ['append' => ['content'=>'CC']]])->textInput(); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <?php $hidden = $tipe == 'koroangiografi' ? '' : ( $tipe == 'pci' ? '' : ($tipe == 'dsa' ? '' : '') ); ?>
                            <div class="row form-row <?=$hidden?>">
                                <div class="col-sm-3">
                                    <label for="control-label text-label"><b>OPERATOR</b></label>
                                </div>
                                <div class="col-sm-9 form-group">
                                    <?= $form->field($model, 'operator_id')->dropdownList([], ['id' => 'operator_id-form'])->label(false); ?>
                                    <div class="help-block">
                                    </div>
                                </div>
                            </div>

                            <?php $hidden = $tipe == 'koroangiografi' ? '' : ( $tipe == 'pci' ? '' : ($tipe == 'dsa' ? '' : '') ); ?>
                            <div class="row form-row <?=$hidden?>">
                                <div class="col-sm-3">
                                    <label for="control-label text-label"><b>TGL PROSEDUR</b></label>
                                </div>
                                <div class="col-sm-9 form-group">
                                    <?=DateTimePicker::widget([
                                            'model' => $model,
                                            'attribute' => 'tgl_prosedure',
                                            'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                                            'readonly' => true,
                                            'convertFormat' => true,
                                            'value' => date('d-m-Y H:i:s'),
                                            'pluginOptions' => [
                                                'format' => 'dd-MM-yyyy HH:mm:ss',
                                                'showMeridian' => true,
                                                'autoclose' => true,
                                                'todayBtn' => true,
                                                'endDate' => date('d-m-Y H:i:s'),
                                                // 'startDate' => $tgl_pendaftaran
                                            ]
                                        ]);
                                    ?>
                                </div>
                            </div>
                        </div>

                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div> 
    </div>
</div>

<?php
$this->registerJs('
    var pendaftaranId = "' . $pendaftaranId . '"
    var tipe = "' . $tipe . '"
    var cathlabData    = ' . json_encode($cathlabData) . '
');
$this->registerJs($this->render('_index.js'), View::POS_END);
?>