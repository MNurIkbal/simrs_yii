<?php

/**
 * @Author: Sigit
 * @Date:   2018-07-12 10:22:29
 */

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
?>

<div class="col-md-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5 class="panel-title"><?=Yii::t('fe', 'Racikan')?></h5>
            <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse"></a></li>
                </ul>
            </div>
        </div>
        <?php $form = ActiveForm::begin([
            'id' => 'form-racikan', 
            'type' => ActiveForm::TYPE_VERTICAL,
            'enableClientValidation' => false,
            'formConfig' => ['labelSpan' => 5, 'deviceSize' => ActiveForm::SIZE_SMALL],
            'action' => '/igd/pemeriksaan-igd/reseptur?id='.DocoHelpers::encrypt($data_pasien['pendaftaran_id']).'&pasien_id='.DocoHelpers::encrypt($data_pasien['pasien_id'])
        ]) ?>
        <?= Html::activeHiddenInput($modelResepturDetailRacikan, 'jenis_racikan'); ?>
        <?= Html::activeHiddenInput($modelResepturDetailRacikan, 'cppt_id') ?>
        <?= Html::hiddenInput('ResepturDetailForm[satuandefault_id][0]', null, ['id' => 'satuandefault_id_0']); ?>
        <?= Html::hiddenInput('ResepturDetailForm[satuandefault_nama][0]', null, ['id' => 'satuandefault_nama_0']); ?>
        <?= Html::hiddenInput('ResepturDetailForm[satuankecil_nama][0]', null, ['id' => 'satuankecil_nama_0']); ?>
        <?= Html::hiddenInput('ResepturDetailForm[nilai_konversi][0]', null, ['id' => 'nilai_konversi_0']); ?>
        <?= Html::hiddenInput('ResepturDetailForm[harga][0]', null, ['id' => 'harga_0']); ?>
        <?= Html::hiddenInput('ResepturDetailForm[harga_konversi][0]', null, ['id' => 'harga_konversi_0']); ?>
        <?= Html::hiddenInput('ResepturDetailForm[stok_tersedia][0]', '', ['id' => 'qty_tersedia_0']) ?>
        <?= Html::hiddenInput('ResepturDetailForm[harganetto][0]', '', ['id' => 'harganetto_reseptur_0']) ?>
        <?= Html::hiddenInput('ResepturDetailForm[harga_satuan][0]', '', ['id' => 'harga_satuan_0']) ?>
        <?= Html::hiddenInput('ResepturDetailForm[harga_jual][0]', '', ['id' => 'harga_jual_0']) ?>
        <?= Html::hiddenInput('ResepturDetailForm[isEditReseptur]', isset($isEditReseptur) ? $isEditReseptur : false, ['id' => 'is-edit-reseptur-racikan']) ?>
        <?= Html::hiddenInput('ResepturDetailForm[instruksi_id]', isset($instruksi_id) ? $instruksi_id : 0, ['id' => 'instruksi_id_racikan']) ?>
        <?= Html::hiddenInput('penjamin_id', $data_pasien['penjamin_id'], ['id' => 'penjamin_id']); ?>
        <div class="panel-body">
            <div class="col-md-12" id="section-racikan">
                <div class="row">
                    <div class="col-md-3">
                        <?=$form->field($modelResepturDetailRacikan, 'rke', ['labelOptions' => ['class' => 'text-right']])
                        ->textInput([
                            'class' => 'form-control input-sm docoNumberOnly r-ke cek-racikan', 
                        ])->label(Yii::t('fe', 'R ke /')) ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($modelResepturDetailRacikan, 'signa_reseptur', ['labelOptions' => ['class' => 'text-right']]) ?>
                    </div>
                    <div class="col-md-3">
                        <?=$form->field($modelResepturDetailRacikan, 'etiket', ['labelOptions' => ['class' => 'text-right']])
                        ->textArea([
                            'id' => 'etiket_racikan',
                            'class' => 'form-control input-sm',
                        ]); ?>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-3">
                        <?= $form->field($modelResepturDetailRacikan, 'obatalkes_id[0]', [
                            'labelOptions' => ['class' => 'text-right']
                        ])->dropDownList([], [
                            'class' => 'form-control racikan_append newselect2',
                            'prompt' => '-',
                            'id' => 'obatalkes_id_0'
                        ]) ?>
                    </div>
                    <div class="col-md-3">
                        <?=$form->field($modelResepturDetailRacikan, 'satuankecil_id[0]', [
                                'labelOptions' => [
                                    'class' => 'text-right'
                                ]
                            ])->widget(DepDrop::classname(), [
                            'options'=>[
                                'id' => 'satuankecil_id_0',
                                'class' => 'form-control select2'
                            ],
                            'pluginOptions'=>[
                                'depends'=>['obatalkes_id_0'],
                                'placeholder'=> \Yii::t('fe', '--Pilih Satuan--'),
                                'url'=>Url::to(['/igd/end-point/list-satuan-besar'])
                            ],
                            'pluginEvents' => [
                                'depdrop:afterChange' => 'function(event, id, value, textStatus){
                                    var _id = $(this).val();
                                    var _response = $(\'#satuankecil_id_0\').depdrop(\'getAjaxResults\');

                                    _group = {};
                                    $.each(_response.output, function (x,y) {
                                      _group[y.id] = y.konversi;
                                    });
                                    
                                    var _harga = $("#hargasatuan_reseptur_0").val();
                                    var _nilai_konversi = _group[_id];
                                    var _stok = $("#stok_sisa_0").val();
                                    var _harga_satuan = $("#harga_0").val();
                                    var _konversi_stok = _stok/_nilai_konversi;
                                    var _konversi_harga = _harga_satuan*_nilai_konversi;

                                    $("#nilai_konversi_0").val(_nilai_konversi);
                                    $("#harga_konversi_0").val(_konversi_harga.toFixed(2));
                                    $("#stok_sisa_0").val(docoHelper.convertToRupiah(_konversi_stok.toFixed(2)));
                                    $("#harga_satuan_0").val(_konversi_harga.toFixed(2));
                                    $("#hargasatuan_reseptur_0").val(docoHelper.convertToRupiah(_konversi_harga));

                                }'
                            ]
                        ])->label(Yii::t('fe', 'Satuan')); ?>
                    </div>
                    <div class="col-md-3">
                        <?=$form->field($modelResepturDetailRacikan, 'hargasatuan_reseptur[0]', [
                                'labelOptions' => ['class' => 'text-right'],
                                'addon' =>  ['prepend' => ['content'=>'<i class="satuandefault_nama_0"></i>']],
                            ])
                            ->textInput([
                                'id' => 'hargasatuan_reseptur_0',
                                'class' => 'form-control input-sm', 
                                'readonly' => 'readonly', 
                            ]); ?>
                    </div>
                    <div class="col-md-3">
                        <?=$form->field($modelResepturDetailRacikan, 'stok_sisa[0]', [
                            'labelOptions' => ['class' => 'text-right'],
                            'addon' =>  ['prepend' => ['content'=>'<i class="satuandefault_nama_0"></i>']],
                        ])
                        ->textInput([
                            'id' => 'stok_sisa_0',
                            'class' => 'form-control input-sm',
                            'readonly' => true,
                        ])->label(Yii::t('fe', 'Stok')); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-md-6">
                                <?=$form->field($modelResepturDetailRacikan, 'qty_reseptur[0]', ['labelOptions' => ['class' => 'text-right']])
                                    ->textInput([
                                        'id' => 'qty_reseptur_0',
                                        'class' => 'form-control input-sm qty-reseptur cek-racikan doco-decimal-wcomma',
                                        'maxlength' => '8',
                                    ])->label(Yii::t('fe', 'Qty')); ?>
                            </div>
                            <div class="col-md-6">
                                <?=$form->field($modelResepturDetailRacikan, 'qty_konversi', [
                                    'labelOptions' => ['class' => 'text-right'],
                                    'addon' =>  ['append' => ['content'=>'<i class="satuankecil_nama_nr_0"></i>']],
                                ])
                                ->textInput([
                                    'class' => 'form-control input-sm',
                                    'readonly' => true,
                                ]); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group highlight-addon has-size-sm" style="
                            margin-top: 16px;
                            margin-right: 300px;">
                            <label class="text-right has-star col-sm-5"></label>
                            <?= Html::button('<i class="fa fa-plus"></i>', ['class' => 'btn btn-success', 'id' => 'btnAppendRacikan', 'onclick' => 'appendRacikan()']); ?>
                        </div>
                    </div>
                </div>

                <!-- WIP -->

            </div>
        </div>
        <div class="panel-footer">
            <div class="pull-right" style="margin-right:5px">
                <?= Html::button('<i class="fa fa-plus"></i> '.Yii::t('fe', 'Tambah'), [
                    'class' => 'btn btn-success btn-md save-racikan',
                    'value' => 'submit-racikan',
                    'onclick' => 'submitRacikan()',
                ]) ?>
            </div>
        </div>
        <?php ActiveForm::end() ?>
    </div>
</div>

<?php $this->registerJs('
var isEditReseptur = "'.$isEditReseptur.'";
var pendaftaran_id = "'.DocoHelpers::encrypt($data_pasien['pendaftaran_id']).'";
var cppt_id = "'.DocoHelpers::encrypt($cppt_id).'";

', View::POS_END) ?>
<?php $this->registerJs($this->render('js/_racikan.js'), View::POS_END); ?>