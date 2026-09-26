<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-06-05 12:40:23
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-11-02 16:58:27
 * @Last Modified by:   Lukman_Hakim (muhamad.lukman@sirs.co.id)
 * @Last Modified time: 2021-06-08 10:21:27
 */

use app\components\DocoHelpers;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use yii\web\View;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;

$this->title = \Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<?php 
    
    $disable = isset($id) ? true : false;

 ?>
<style type="text/css">
	.input-group-addon {
		background-color: #34bfa3 !important;
	}
</style>

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
                      <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
                      <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                        'back' => [
                            'attributes' => [
                                'href' => $homeUrl
                            ]
                        ],
                        'log-custom'=>[
                            'type'=>'button',
                            'title' => \Yii::t('fe', 'Log Perubahan'),
                            'icon' => 'fa fa-file-text-o',
                            'attributes' => [
                                'id'          => 'btn-log',
                                'data-width'  => '90%',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'disabled'    => isset($id) ? false : true,
                                'action'      => isset($id) ?'/master/barang/log-perubahan?id='.$id.'&modal=is_modal' : '',
                            ]
                        ],
                    ]);?>
            </div>
            <div class="panel-body">
                <?php
                $form = ActiveForm::begin([
                    'id' => 'barang-form',
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL],
                    'enableAjaxValidation'=>false,
                    'enableClientValidation'=>false,
                ]);
                ?>
                <div class="form-barang">
                    <fieldset title="1" class="stepy-step" onmouseover="this.title='';">
                        <legend class="stepy-legend"><?=Yii::t('fe', 'Data Dasar Barang')?></legend>
                        <div class="col-md-6">
                            <?=$form->field($model, 'barang_nama')?>
                            <?php 
                                if($autoGenerateKodeBarang) {
                                    echo $form->field($model, 'barang_kode', [
                                        'addon' => [
                                            'append' => [
                                                'content' => '<i class="fa fa-refresh btn-info"></i>',
                                                'options' => ['class' => 'btn-generate']
                                            ]
                                        ]
                                    ]);
                                } else {
                                    echo $form->field($model, 'barang_kode');
                                }
                            ?>
                            <?=$form->field($model, 'barang_merk')?>
                            <?=$form->field($model, 'golonganbarang_id')
                            ->radioList($golongan, ['inline' => true]) ?>
                            <?=$form->field($model, 'kelompokbarang_id')->dropDownList($kelompok, ['class'=>'select2','prompt'=> '', 'id' => 'kelompokbarang_id'])?>
                            <?= $form->field($model, 'subkelompokbarang_id')->widget(DepDrop::classname(), [
                                    'options' => [
                                        'id'=>'subkelompokbarang_id', 'class' => 'form-control select2'
                                    ],
                                    'pluginOptions'=>[
                                        'depends' => ['kelompokbarang_id'],
                                        'placeholder' => Yii::t('fe', 'Sub Kelompok'),
                                        'url'=> Url::to(['/master/barang/get-sub-kelompok?selected='.$model->subkelompokbarang_id]),
                                        'prompt' => Yii::t('fe', 'Pilih Sub Kelompok'),
                                        'initialize' => true,
                                    ]
                                ])->label(Yii::t('fe', 'Sub Kelompok'));
                            ?>
                            <?=$form->field($model, 'is_kadaluarsa')->radioList([1 => 'Ada', 0 => 'Tidak'], [
                                'inline' => true,
                            ])?>
                            <div id="error_barangform-is_kadaluarsa"></div>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'is_active')->checkbox([
                                'label' => 'Aktif',
                            ])
                            ->label(Yii::t('fe', 'Status'))
                            ?>
                            
                            <?= $form->field($model, 'barang_harganetto')->textInput([
                                'placeholder' => Yii::t('fe', 'Harga Netto'),
                                'class' => 'form-control doco-number',
                                'autocomplete' => "off",
                            ])->label(Yii::t('fe', 'Harga Netto (Rp.)')); ?>

                            <?=$form->field($model, 'satuankecil_id')->dropDownList($satuankecil, [
                                'class'=>'select2 satuan-kecil-id',
                                'id'=>'satuan-kecil-id',
                                'prompt'=>'',
                                'disabled' => $disabled
                                ])?>
                            <?= Html::hiddenInput('BarangForm[hidden_satuankecil_id]', $model->satuankecil_id); ?>
                            
                            <?=$form->field($model, 'satuan1_id')->dropDownList($satuanbesar, [
                                'class'=>'select2 satuan1-id',
                                'id'=>'satuan1-id',
                                'prompt'=>'',
                                'disabled' => $disabled
                            ])?>
                            <?= Html::hiddenInput('BarangForm[hidden_satuan1_id]', $model->satuan1_id); ?>
                            
                            <?=$form->field($model, 'satuan2_id')->dropDownList($satuansedang, [
                                'class'=>'select2 satuan2-id',
                                'id'=>'satuan2-id',
                                'prompt'=>'',
                                'disabled' => $disabled 
                            ])?>
                            <?= Html::hiddenInput('BarangForm[hidden_satuan2_id]', $model->satuan2_id); ?>
                            
                            <?=$form->field($model, 'isi_satuan1')->textInput([
                                'class' => 'doco-number isi-satuan1',
                                'disabled' => $disabled
                            ]) ?>
                            <?= Html::hiddenInput('BarangForm[hidden_isi_satuan1]', $model->isi_satuan1); ?>
                            
                            <?=$form->field($model, 'isi_satuan2')->textInput([
                                'class' => 'doco-number isi-satuan2',
                                'disabled' => $disabled 
                            ]) ?>
                            <?= Html::hiddenInput('BarangForm[hidden_isi_satuan2]', $model->isi_satuan2); ?>
                        </div>
                        <!-- inputs -->
                    </fieldset>
                    <!-- <fieldset title="2" class="stepy-step" onmouseover="this.title='';">
                        <legend class="stepy-legend"><?//=Yii::t('fe', 'Detail Barang')?></legend>
                        <div class="col-md-6">
                            <?//=$form->field($model, 'barang_thnperoleh')->textInput(['class' => 'pickadate']) ?>
                            <?//=$form->field($model, 'harga_perolehan')->textInput(['class'=>'doco-number'])?>
                            <div class="form-group">
                                <label class="control-label col-sm-4"><?=$model->attributeLabels()['n_ekonomis_thn']?></label>
                                <div class="col-sm-4">
                                    <?//= Html::activeTextInput($model, 'n_ekonomis_thn', [
                                       // 'class'=>'form-control doco-number', 'placeholder' => 'Tahun'])?>
                                </div>
                                <div class="col-sm-4">
                                    <?//= Html::activeTextInput($model, 'n_ekonomis_bln', [
                                       // 'class'=>'form-control doco-number', 'placeholder' => 'Bulan'])?>
                                </div>
                            </div>
                            <?//=$form->field($model, 'barang_image')->fileInput(); ?>
                        </div>
                        <div class="col-md-6">
                            <?//=$form->field($model, 'barang_harganetto')->textInput(['class'=>'doco-number'])?>
                            <?//=$form->field($model, 'barang_min')->textInput(['class'=>'doco-number'])->label('Harga Netto Minimum')?>
                            <?//=$form->field($model, 'barang_max')->textInput(['class'=>'doco-number'])->label('Harga Netto Maksimum')?>
                            <?//=$form->field($model, 'barang_average')->textInput(['class'=>'doco-number'])->label('Harga Netto Average')?>
                        </div>
                    </fieldset> -->
                    <fieldset title="2" class="stepy-step" onmouseover="this.title='';">
                        <legend class="stepy-legend"><?=Yii::t('fe', 'Pengorderan')?></legend>
                        <div class="col-md-6">
                            <?=$form->field($model, 'lead_time', [
                                'addon' => ['append' => ['content'=>'hari']],
                            ])->textInput(['class'=>'doco-number 
                            lead_time'])?>
                            <?=$form->field($model, 'stok_minimal')->textInput(['class'=>'doco-number minimalstok'])?>
                            <?=$form->field($model, 'avg_usage')->textInput(['class'=>'doco-number 
                            avg_usage']); ?>
                            <?=$form->field($model, 'min_order')->textInput(['class'=>'doco-number 
                            min_order'])?>
                        </div>
                        <div class="col-md-6">
                            <?=$form->field($model, 'max_order')->textInput(['class'=>'doco-number max_order'])?>
                            <?=$form->field($model, 'on_ro')->textInput(['class'=>'doco-number on_ro', 
                            'readonly' => true])?>
                            <?=$form->field($model, 'on_po')->textInput(['class'=>'doco-number on_po', 
                            'readonly' => true])?>
                        </div>
                    </fieldset>
                    <?=Html::submitButton("<b><i class='fa fa-floppy-o'></i></b> Simpan", ['class'=>'stepy-finish btn btn-xs btn-labeled btn-info'])?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php 

$this->registerJs("
    var autoGenerate = '". $autoGenerateKodeBarang ."';

    $('.form-barang').stepy({
          backLabel: '".Yii::t('fe', 'Kembali')." <b><i class=\'fa fa-chevron-left\'></i></b>',
          nextLabel: '".Yii::t('fe', 'Selanjutnya')." <b><i class=\'fa fa-chevron-right\'></i></b>',
        })
    $('.form-barang').find('.button-next').addClass('btn btn-xs btn-labeled btn-info');
    $('.form-barang').find('.button-back').addClass('btn btn-xs btn-labeled btn-info');
    $('.pickadate').pickadate({
        format: 'dd mmm, yyyy',
        selectMonths: true,
        selectYears: 99,
        formatSubmit: 'yyyy-mm-dd',
    });
    
    $('#barang-form').docoForm('submit',{
        success : function(data) {
            if (data.status == 201)
                this.formInput[0].reset();
            window.location = '".Url::to(['index'])."'
        },
        error: function(data){
            $('.form-barang').stepy('step', 1)
        }
    });

    if(autoGenerate) {
        $('#barangform-barang_kode').prop('readonly', true);

        $('#subkelompokbarang_id').on('select2:select', function(e) {
            _generateKode(e.params.data.id);
        });

        $('.btn-generate').on('click', function() {
            if($('#subkelompokbarang_id').val() != '') {
                _generateKode($('#subkelompokbarang_id').val());
            }
        });
    }

    var _generateKode = function(id) {
        $.ajax({
            url: '/master/barang/auto-generate-kode-barang?subkelompokbarang_id=' + id,
            type:'GET',
            success: function(response) {
                $('#barangform-barang_kode').val(response);
                
                return true;
            }
        });

        return true;
    }
    ", View::POS_END, 'sj');
$this->registerJs($this->render('js/form.js'), View::POS_END);
?>