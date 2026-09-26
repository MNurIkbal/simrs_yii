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

<div class="row" id="racikan">
	<div class="col-lg-12">
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
                'enableClientValidation'=>false,
                'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
            ]) ?>
            
            <?= Html::hiddenInput('ResepturDetailForm[satuaninput_id][0]', null, ['id' => 'satuaninput_id_0']); ?>
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
            <div class="panel-body">
                <div class="col-md-12" id="section-racikan">
                    <div class="row">
    					<div class="col-md-4">
                            <?=$form->field($modelResepturDetailRacikan, 'rke', ['labelOptions' => ['class' => 'text-right']])
                            ->textInput([
                                'class' => 'form-control input-sm docoNumberOnly r-ke cek-racikan',
                            ]); ?>
                        </div>
                        <div class="col-md-4">
                            <?=$form->field($modelResepturDetailRacikan, 'signa_reseptur', ['labelOptions' => ['class' => 'text-right']]); ?>
                        </div>
                    </div>
                    <div class="row">
    					<div class="col-md-4">
                            <?= $form->field($modelResepturDetailRacikan, 'obatalkes_id[0]', [
                                'labelOptions' => ['class' => 'text-right']
                            ])->dropDownList([], [
                                'class' => 'form-control racikan_append cek-racikan obatalkes',
                                'prompt' => '-',
                                'id' => 'obatalkes_id_0'
                            ]) ?>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?=$form->field($modelResepturDetailRacikan, 'stok', [
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
                        <div class="col-md-4">
                            <?=$form->field($modelResepturDetailRacikan, 'hargasatuan_reseptur[0]', ['labelOptions' => ['class' => 'text-right']])
                                ->textInput([
                                    'id' => 'hargasatuan_reseptur_0',
                                    'class' => 'form-control input-sm field-harga',
                                    'readonly' => 'readonly',
                                ]); ?>
                        </div>
                    </div>
                    <div class="row">
    					<div class="col-md-4">
                            <div class="form-group highlight-addon has-size-sm field-resepturdetailform-satuankecil_id_0 required">
                                <label class="text-right has-star">Satuan</label>
                                <?= DepDrop::widget([
                                    'name' => 'ResepturDetailForm[satuankecil_id]',
                                    'options' => [
                                        'id'=>'satuankecil_id_0',
                                        'class' => 'form-control select2 satuankecil_id cek-racikan'
                                    ],
                                    'pluginOptions' => [
                                       'depends' => ['obatalkes_id_0'],
                                       'placeholder' => \Yii::t('fe', '--Pilih Satuan--'),
                                       'url' => Url::to(['/rajal/allow/list-satuan-besar'])
                                    ],
                                    'pluginEvents' => [
                                        'depdrop:afterChange' => 'function(event, id, value, textStatus){
                                            var _id = $(this).val();
                                            var _response = $(\'#satuankecil_id_0\').depdrop(\'getAjaxResults\');

                                            _group = {};
                                            $.each(_response.output, function (x,y) {
                                              _group[y.id] = y.konversi;
                                            });
                                            
                                            var _nilai_konversi = _group[_id];
                                            
                                            $("#nilai_konversi_0").val(_nilai_konversi);
                                        }'
                                    ]
                                ]); ?>
                                <div class="help-block"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <?=$form->field($modelResepturDetailRacikan, 'qty_reseptur[0]', [
                                'labelOptions' => ['class' => 'text-right'],
                                'addon' => ['append' => ['content'=>'<span class="konversi_0"></span>']],
                            ])
                                ->textInput([
                                    'class' => 'form-control input-sm doco-decimal cek-racikan',
                                    'id' => 'qty_reseptur_0'
                                ])->label(Yii::t('fe', 'Qty')); ?>
                        </div>
                        <div class="col-md-4">
                            <?=$form->field($modelResepturDetailRacikan, 'etiket', ['labelOptions' => ['class' => 'text-right']])
                                ->textArea([
                                    'cols' => 5,
                                    'id' => 'etiket',
                                    'class' => 'form-control input-sm',
                                ])->label(Yii::t('fe', 'Catatan')); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-1">
                            <div class="form-group">
                                <label class="text-right col-sm-4"></label>
                                <div class="col-sm-8">
                                    <?= Html::button('<i class="fa fa-plus"></i>', ['class' => 'btn btn-success', 'id' => 'btnAppendRacikan', 'onclick' => 'appendRacikan()']); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
			<div class="panel-footer">
                <div class="pull-right" style="margin-right:5px">
                    <?= Html::button('<i class="fa fa-plus"></i> '.Yii::t('fe', 'Tambah'), ['class' => 'btn btn-success btn-md save-racikan', 'onclick' => 'saveracikan()']) ?>
                </div>
            </div>
                            
            <?php ActiveForm::end() ?>
        </div>
	</div>
</div>
<?php $this->registerJs('
var pendaftaran_id = "'.$pendaftaran_id.'";
var kelaspelayanan_id = "'.$kelaspelayanan_id.'";

', View::POS_END) ?>

<?php $this->registerJs($this->render('js/_racikan.js'), View::POS_END); ?>