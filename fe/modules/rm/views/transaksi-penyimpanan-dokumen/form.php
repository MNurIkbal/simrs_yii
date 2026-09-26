<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-10 15:37:18
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-24 11:39:45
 * desc: form penyimpanan dokumen rekam medis
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\Select2;
use yii\web\JsExpression;


$this->title = Yii::t('fe','Penyimpanan Dokumen Rekam Medik');
$this->params['breadcrumbs'][] = ['label' => 'Rekam Medik', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="panel panel-white">
        <div class="panel-heading">
            <h3 class="panel-title"><?=$this->title?></h3>
            <?=Breadcrumbs::widget([
                    'homeLink' => [ 
                        'label' => Yii::t('yii', 'Home'),
                        'url' => Yii::$app->homeUrl,
                    ],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]);?>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <!-- <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li> -->
                    </ul>
                </div>
        </div>
        <div class="panel-body">	
            <div class="row">
                <div class="col-md-12">
                    <?php 
                    $form = ActiveForm::begin([
                        'id'=>'penyimpanandokumen-form',
                        'options'=>[
                            'class' => 'form-horizontal',
                            'role' => 'form',
                        ],
                    ]);
                    ?><br>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="col-md-10">
                                <div class="form-group required">
                                    <label class="col-md-3 control-label"><?=$model->getAttributeLabel('no_rekam_medik')?></label>
                                    <div class="col-md-9">
                                            <?php
                                            echo $form->field($model, 'no_rekam_medik')->widget(Select2::classname(), [
                                                'initValueText' => $model['no_rekam_medik'] != ''? $model['no_rekam_medik']:null,
                                                'options' => [
                                                    'id' => 'search_no_rm',
                                                    // 'class' => 'select2'
                                                ],
                                                'pluginOptions' => [
                                                    // 'allowClear' => true,
                                                    'minimumInputLength' => 3,
                                                    'language' => [
                                                        'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                                                    ],
                                                    'ajax' => [
                                                        'url' => \yii\helpers\Url::to(['/rm/transaksi-penyimpanan-dokumen/get-all-rm']),
                                                        'dataType' => 'json',
                                                        'data' => new JsExpression('
                                                            function(params) {
                                                                return {
                                                                    q:params.term,
                                                                    page:params.page || 1
                                                                }; 
                                                            }
                                                        ')
                                                    ],
                                                    'escapeMarkup' => new JsExpression ('function(markup){ return markup;}'),
                                                    'templateResult' => new JsExpression ('function(diagnosa){ return diagnosa.text;}'),
                                                    'templateSelection' => new JsExpression ( 'function (subject) { return subject.text; }' ) ,
                                                ],
                                            ])->label(false);
                                            ?>
                                            <?= Html::hiddenInput('TransaksiPenyimpananDokumenForm[no_rekam_medik]', null,
                                                [
                                                    'class' => 'noRm'
                                                ]);
                                            ?>
                                            <?= Html::hiddenInput('TransaksiPenyimpananDokumenForm[warnadokrm_id]', null,
                                                [
                                                    'class' => 'warnadok'
                                                ]);
                                            ?>
                                            <?= Html::hiddenInput('TransaksiPenyimpananDokumenForm[tgl_rekam_medik]', null,
                                                [
                                                    'class' => 'tglrekammedik'
                                                ]);
                                            ?>
                                    </div>
                                </div>
                                <div class="form-group required">
                                    <label class="col-md-3 control-label"><?=$model->getAttributeLabel('no_pengiriman')?></label>
                                    <div class="col-md-9">
                                        <?php
                                            echo $form->field($model, 'no_pengiriman')->widget(Select2::classname(), [
                                                'initValueText' => $model['no_pengiriman'] != ''? $model['no_pengiriman']:null,
                                                'options' => [
                                                    'id' => 'search_no_pengiriman',
                                                    // 'class' => 'select2'
                                                ],
                                                'pluginOptions' => [
                                                    // 'allowClear' => true,
                                                    'minimumInputLength' => 3,
                                                    'language' => [
                                                        'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                                                    ],
                                                    'ajax' => [
                                                        'url' => \yii\helpers\Url::to(['/rm/transaksi-penyimpanan-dokumen/get-all-pengiriman']),
                                                        'dataType' => 'json',
                                                        'data' => new JsExpression('
                                                            function(params) {
                                                                return {
                                                                    q:params.term,
                                                                    page:params.page || 1
                                                                }; 
                                                            }
                                                        ')
                                                    ],
                                                    'escapeMarkup' => new JsExpression ('function(markup){ return markup;}'),
                                                    'templateResult' => new JsExpression ('function(diagnosa){ return diagnosa.text;}'),
                                                    'templateSelection' => new JsExpression ( 'function (subject) { return subject.text; }' ) ,
                                                ],
                                            ])->label(false);
                                        ?>
                                        <?= Html::hiddenInput('TransaksiPenyimpananDokumenForm[no_pengiriman]', null,
                                                [
                                                    'class' => 'noPengiriman'
                                                ]);
                                            ?>
                                    </div>

                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label"><?=$model->getAttributeLabel('status_indexing')?></label>
                                    <div class="col-md-9" style="padding-top: 10px">
                                        <?= $form->field($model, 'status_indexing')->radioList([
                                            '0'=>Yii::t('fe','sudah'),
                                            '1'=>Yii::t('fe','belum')
                                            ]
                                        )->label(false); ?>	
                                    </div>
                                    
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label"><?=$model->getAttributeLabel('status_assembling')?></label>
                                    <div class="col-md-9" style="padding-top: 10px">
                                        <?= $form->field($model, 'status_assembling')->radioList([
                                            '0'=>Yii::t('fe','sudah'),
                                            '1'=>Yii::t('fe','belum')
                                        ])->label(false); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="col-md-10">
                                <div class="form-group required">
                                    <label class="col-md-4 control-label"><?=$model->getAttributeLabel('no_rak')?></label>	
                                    <div class="col-md-8">
                                        <?= Html::activeDropDownList($model, 'no_rak',
                                            ArrayHelper::map($data_rak, 'lokasirak_id', 'lokasirak_nama'), [
                                                'class' => 'select2 selectRak',
                                                'id' => 'no_rak',
                                                'prompt' => Yii::t('fe', '-- Pilih --')
                                            ]) 
                                        ?>
                                        <?= Html::hiddenInput('TransaksiPenyimpananDokumenForm[no_rak]', null,
                                            [
                                                'class' => 'noRak'
                                            ]);
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group required">
                                    <label class="col-md-4 control-label"><?=$model->getAttributeLabel('no_sub_rak')?></label>
                                    <div class="col-md-8">
                                        <?= Html::activeDropDownList($model, 'no_sub_rak',
                                            ArrayHelper::map([], 'subrak_id', 'subrak_nama'), [
                                                'class' => 'select2 selectSubRak',
                                                'id' => 'no_sub_rak',
                                                'prompt' => Yii::t('fe', '-- Pilih --')
                                            ]) 
                                        ?>
                                        <?= Html::hiddenInput('TransaksiPenyimpananDokumenForm[no_sub_rak]', null,
                                            [
                                                'class' => 'noSubRak'
                                            ]);
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group required">
                                    <label class="col-md-4 control-label"><?=$model->getAttributeLabel('tgl_akhir_masuk')?></label>
                                    <div class="col-md-8">
                                        <?=$form->field($model, 'tgl_akhir_masuk')->textInput(['class'=>'form-control input-sm pickadate','data-tgl'=>date('Y-m-d')])->label(false)?>	
                                    </div>
                                </div>
                                <?= Html::hiddenInput('TransaksiPenyimpananDokumenForm[dokrm_id]', null,
                                    [
                                        'class' => 'dokrm_id'
                                    ]);
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?= Html::submitButton(\Yii::t('fe','Simpan'), ['class' => 'btn btn-info']) ?>
                            <?= Html::resetButton(\Yii::t('fe','Ulang'), ['class' => 'btn btn-info']) ?>
                        </div>
                    </div>
                    
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Untuk Kebutuhan Modal Global -->
<div id="modal_backdrop_lg" class="modal fade" style="z-index: 1065;" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
<!-- End -->
<?php 
$this->registerJs($this->render('js/penyimpanan-dokumen.js'));
?>