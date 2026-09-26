<?php 
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use yii\web\View;

?>

<div class="row" id="non_racikan">
	<div class="col-lg-12">
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
                'enableClientValidation'=>false,
                'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
            ]) ?>
			<?= Html::hiddenInput('ResepturNrDetailForm[stok_tersedia]', '', ['id' => 'stok_tersedia']) ?>
            <?= Html::hiddenInput('ResepturNrDetailForm[satuandefault_id]', null, ['id' => 'satuandefault_id']); ?>
            <?= Html::hiddenInput('ResepturNrDetailForm[satuandefault_nama]', null, ['id' => 'satuandefault_nama']); ?>
            <?= Html::hiddenInput('ResepturNrDetailForm[satuankecil_nama]', null, ['id' => 'satuankecil_nama']); ?>
            <?= Html::hiddenInput('ResepturNrDetailForm[nilai_konversi]', null, ['id' => 'nilai_konversi']); ?>
            <?= Html::hiddenInput('ResepturNrDetailForm[harga]', null, ['id' => 'harga']); ?>
            <?= Html::hiddenInput('ResepturNrDetailForm[harga_konversi]', null, ['id' => 'harga_konversi']); ?>
            <?= Html::hiddenInput('ResepturNrDetailForm[harganetto_reseptur]', null, ['id' => 'harganetto_reseptur']); ?>

            <div class="panel-body">
                <div class="row">
					<div class="col-md-4">
	                    <?= $form->field($modelResepturDetailNonRacikan, 'obatalkes_id', [
	                        'labelOptions' => ['class' => 'text-right'],

	                    ])->dropDownList([], [
	                        'class' => 'form-control autoObat obatalkes',
	                        'id' => 'obatalkes_id',
	                        'prompt' => '-'
	                    ]) ?>
                    </div>
                    <div class="col-md-4">
                        <?=$form->field($modelResepturDetailNonRacikan, 'stok', [
                            'labelOptions' => ['class' => 'text-right'],
                            'addon' =>  ['prepend' => ['content'=>'<i class="satuandefault_nama"></i>']]
                        ])
                            ->textInput([
                                'id' => 'stok_sisa',
                                'class' => 'form-control input-sm',
                                'disabled' => true,
                        ])->label(Yii::t('fe', 'Stok')); ?>
                    </div>
                    <div class="col-md-4">
                        <?=$form->field($modelResepturDetailNonRacikan, 'hargasatuan_reseptur', ['labelOptions' => ['class' => 'text-right']])
                            ->textInput([
                                'id' => 'harga_reseptur',
                                'class' => 'form-control input-sm',
                                'readonly' => 'readonly',
                            ]); ?>
                    </div>
                    
                </div>
                <div class="row">
					<div class="col-md-4">
                        <div class="form-group highlight-addon has-size-sm field-satuankecil_id required">
                            <label class="text-right has-star">Satuan</label>
                            <?= DepDrop::widget([
                                'name' => 'ResepturNrDetailForm[satuankecil_id]',
                                'options' => [
                                    'id'=>'satuankecil_id',
                                    'class' => 'form-control select2'
                                ],
                                'pluginOptions' => [
                                   'depends' => ['obatalkes_id'],
                                   'placeholder' => \Yii::t('fe', '--Pilih Satuan--'),
                                   'url' => Url::to(['/rajal/allow/list-satuan-besar'])
                                ],
                                'pluginEvents' => [
                                    'depdrop:afterChange' => 'function(event, id, value, textStatus){
                                        var _id = $(this).val();
                                        var _response = $(\'#satuankecil_id\').depdrop(\'getAjaxResults\');

                                        _group = {};
                                        $.each(_response.output, function (x,y) {
                                          _group[y.id] = y.konversi;
                                        });
                                        
                                        var _nilai_konversi = _group[_id];
                                        $("#nilai_konversi").val(_nilai_konversi);
                                    }'
                                ]
                            ]); ?>
                            <div class="help-block"></div>
                        </div>
                        
                    </div>
                    <div class="col-md-4">
                        <?=$form->field($modelResepturDetailNonRacikan, 'qty_reseptur', [
                            'labelOptions' => ['class' => 'text-right'],
                            'addon' => ['append' => ['content'=>'<span class="konversi"></span>']],
                        ])
                            ->textInput([
                                'id' => 'qty_reseptur',
                                'class' => 'form-control input-sm doco-decimal',
                            ])->label(Yii::t('fe', 'Qty')); ?>
                    </div>
                    <div class="col-md-4">
                        <?=$form->field($modelResepturDetailNonRacikan, 'signa_reseptur', ['labelOptions' => ['class' => 'text-right']]); ?>
                    </div>
                    
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <?=$form->field($modelResepturDetailNonRacikan, 'etiket', ['labelOptions' => ['class' => 'text-right']])
                            ->textArea([
                                'cols' => 5,
                                'id' => 'etiket',
                                'class' => 'form-control input-sm',
                            ])->label(Yii::t('fe', 'Catatan')); ?>
                    </div>
					
                </div>
            </div>
			<div class="panel-footer">
                <div class="pull-right" style="margin-right:5px">
                    <?= Html::button('<i class="fa fa-plus"></i> '.Yii::t('fe', 'Tambah'), ['class' => 'btn btn-success btn-md save-non-racikan', 'onclick' => 'addTemp()']) ?>
                </div>
            </div>
                            
            <?php ActiveForm::end() ?>
        </div>
	</div>
</div>

<?php $this->registerJs($this->render('js/_non_racikan.js'), View::POS_END); ?>