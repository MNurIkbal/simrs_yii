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
            <h5 class="panel-title"><?=Yii::t('fe', 'Non racikan')?></h5>
            <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse"></a></li>
                </ul>
            </div>
        </div>
        <?php $form = ActiveForm::begin([
            'id' => 'form-nonracikan', 
            'type' => ActiveForm::TYPE_VERTICAL,
            'enableClientValidation' => false,
            'formConfig' => ['labelSpan' => 5, 'deviceSize' => ActiveForm::SIZE_SMALL],
            'action' => '/igd/pemeriksaan-igd/reseptur?id='.DocoHelpers::encrypt($data_pasien['pendaftaran_id']).'&pasien_id='.DocoHelpers::encrypt($data_pasien['pasien_id'])
        ]) ?>
        <?= Html::activeHiddenInput($modelResepturDetailNonRacikan, 'jenis_racikan'); ?>
        <?= Html::activeHiddenInput($modelResepturDetailNonRacikan, 'cppt_id') ?>
        <?= Html::hiddenInput('ResepturNrDetailForm[satuandefault_id]', null, ['id' => 'satuandefault_id']); ?>
        <?= Html::hiddenInput('ResepturNrDetailForm[satuandefault_nama]', null, ['id' => 'satuandefault_nama']); ?>
        <?= Html::hiddenInput('ResepturNrDetailForm[satuankecil_nama]', null, ['id' => 'satuankecil_nama']); ?>
        <?= Html::hiddenInput('ResepturNrDetailForm[nilai_konversi] ', null, ['id' => 'nilai_konversi']); ?>
        <?= Html::hiddenInput('ResepturNrDetailForm[harga]', null, ['id' => 'harga']); ?>
        <?= Html::hiddenInput('ResepturNrDetailForm[harganetto]', null, ['id' => 'harganetto']); ?>
        <?= Html::hiddenInput('ResepturNrDetailForm[harga_konversi]', null, ['id' => 'harga_konversi']); ?>
        <?= Html::hiddenInput('ResepturNrDetailForm[harga_satuan]', null, ['id' => 'harga_satuan']); ?>
        <?= Html::hiddenInput('ResepturNrDetailForm[harga_jual]', null, ['id' => 'harga_jual']); ?>
        <?= Html::hiddenInput('ResepturNrDetailForm[stok_tersedia]', '', ['id' => 'qty_tersedia']) ?>
        <?= Html::hiddenInput('ResepturNrDetailForm[isEditReseptur]', isset($isEditReseptur) 
                ? $isEditReseptur : false, ['id' => 'is-edit-reseptur']) ?>
        <?= Html::hiddenInput('ResepturNrDetailForm[instruksi_id]', isset($instruksi_id) ? $instruksi_id : 0, ['id' => 'instruksi_id']) ?>
        <?= Html::hiddenInput('ResepturNrDetailForm[is_submit]', 0, ['id' => 'is_submit']) ?>
        <?= Html::hiddenInput('penjamin_id', $data_pasien['penjamin_id'], ['id' => 'penjamin_id']); ?>
        <div class="panel-body">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-3">
                        <?= $form->field($modelResepturDetailNonRacikan, 'obatalkes_id', [
                                'labelOptions' => ['class' => 'text-right']
                            ])->dropDownList([], [
                                'class' => 'form-control autoObat',
                                'id' => 'obatalkes_id',
                                'prompt' => '-'
                        ]) ?>
                    </div>
                    <div class="col-md-3">
                        <?=$form->field($modelResepturDetailNonRacikan, 'satuankecil_id', [
                                'labelOptions' => ['class' => 'text-right']
                            ])->widget(DepDrop::classname(), [
                            'options'=>[
                                'id' => 'satuankecil_id',
                                'class' => 'form-control select2'
                            ],
                            'pluginOptions'=>[
                                'depends'=>['obatalkes_id'],
                                'placeholder'=> \Yii::t('fe', '--Pilih Satuan--'),
                                'url'=>Url::to(['/igd/end-point/list-satuan-besar'])
                            ],
                            'pluginEvents' => [
                                'depdrop:afterChange' => 'function(event, id, value, textStatus){
                                    var _id = $(this).val();
                                    var _response = $(\'#satuankecil_id\').depdrop(\'getAjaxResults\');
                                    _group = {};

                                    $.each(_response.output, function (x,y) {
                                      _group[y.id] = y.konversi;
                                    });
                                    
                                    var _harga = $("#harga_reseptur_nr").val();
                                    var _nilai_konversi = _group[_id];
                                    var _stok = $("#stok_sisa_nr").val();
                                    var _harga_satuan = $("#harga").val();
                                    var _konversi_stok = _stok/_nilai_konversi;
                                    var _konversi_harga = _harga_satuan*_nilai_konversi;
                                    
                                    $("#nilai_konversi").val(_nilai_konversi);
                                    $("#harga_konversi").val(_konversi_harga.toFixed(2));
                                    $("#stok_sisa_nr").val(docoHelper.convertToRupiah(_konversi_stok.toFixed(2)));
                                    $("#harga_satuan").val(_konversi_harga.toFixed(2));
                                    $("#harga_reseptur_nr").val(docoHelper.convertToRupiah(_konversi_harga));
                                }'
                            ]
                        ])->label(Yii::t('fe', 'Satuan')); ?>
                    </div>
                    <div class="col-md-3">
                        <?=$form->field($modelResepturDetailNonRacikan, 'hargasatuan_reseptur', [
                                'labelOptions' => ['class' => 'text-right'],
                                'addon' =>  ['prepend' => ['content'=>'<i class="satuandefault_nama_nr"></i>']],
                            ])
                            ->textInput([
                                'id' => 'harga_reseptur_nr',
                                'class' => 'form-control input-sm', 
                                'readonly' => 'readonly', 
                            ]); ?>
                    </div>
                    <div class="col-md-3">
                        <?=$form->field($modelResepturDetailNonRacikan, 'stok_sisa', [
                            'labelOptions' => ['class' => 'text-right'],
                            'addon' =>  ['prepend' => ['content'=>'<i class="satuandefault_nama_nr"></i>']],
                        ])
                        ->textInput([
                            'id' => 'stok_sisa_nr',
                            'class' => 'form-control input-sm',
                            'readonly' => true,
                        ])->label(Yii::t('fe', 'Stok')); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <?= $form->field($modelResepturDetailNonRacikan, 'signa_reseptur', ['labelOptions' => ['class' => 'text-right']]) ?>
                    </div>
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-md-6">
                                <?=$form->field($modelResepturDetailNonRacikan, 'qty_reseptur', ['labelOptions' => ['class' => 'text-right']])
                                    ->textInput([
                                        'id' => 'qty_nonracikan_id',
                                        'class' => 'form-control input-sm doco-decimal-wcomma',
                                    ])->label(Yii::t('fe', 'Qty')); ?>
                            </div>
                            <div class="col-md-6">
                                <?=$form->field($modelResepturDetailNonRacikan, 'qty_konversi', [
                                    'labelOptions' => ['class' => 'text-right'],
                                    'addon' =>  ['append' => ['content'=>'<i class="satuankecil_nama_nr"></i>']],
                                ])
                                ->textInput([
                                    'class' => 'form-control input-sm konversi',
                                    'readonly' => true,
                                ]); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <?=$form->field($modelResepturDetailNonRacikan, 'etiket', ['labelOptions' => ['class' => 'text-right']])
                        ->textArea([
                            'id' => 'etiket',
                            'class' => 'form-control input-sm',
                        ]); ?>
                    </div>
                    <div class="col-md-3">
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="pull-right" style="margin-right:5px">
                <?= Html::button('<i class="fa fa-plus"></i> '.Yii::t('fe', 'Tambah'), [
                    'class' => 'btn btn-success btn-md save-non-racikan',
                    'value' => 'submit-nonracikan',
                    'onclick' => 'submitNonracikan()',
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

<?php $this->registerJs($this->render('js/_non_racikan.js'), View::POS_END); ?>