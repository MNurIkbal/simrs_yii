<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-06-05 12:40:23
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-11-21 10:50:39
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
                        ]
                    ]);?>
            </div>
            <div class="panel-body">
                <?php
                $form = ActiveForm::begin([
                    'id' => 'obatalkes-form',
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL],
                    'enableAjaxValidation'=>false,
                    'enableClientValidation'=>false,
                ]);
                ?>
                <div class="form-obatalkes">
                    <fieldset title="1" class="stepy-step" onmouseover="">
                        <legend class="stepy-legend"><?=Yii::t('fe', 'Data dasar obat')?></legend>
                        <div class="col-md-6">
                            <?=$form->field($model, 'obatalkes_nama')?>
                            <?=$form->field($model, 'obatalkes_namalain')?>
                            <div class="form-group">
                                <label class="control-label col-sm-4"><?=$model->attributeLabels()['obatalkes_kode']?></label>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                      <?=Html::activeTextInput($model, 'obatalkes_kode', ['class'=>'form-control kode-oa'])?>
                                      <span class="input-group-btn">
                                        <?=Html::button('<i class="fa fa-refresh"></i>', ['class'=>'btn btn-sm btn-info btn-kode', 'title'=>'Buat ulang kode'])?>
                                      </span>
                                    </div><!-- /input-group -->
                                </div>
                                <div class="col-sm-4">

                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4"><?=$model->attributeLabels()['kekuatan_obat']?></label>
                                <div class="col-sm-4">
                                    <?=Html::activeTextInput($model, 'kekuatan_obat', ['class'=>'form-control'])?>
                                </div>
                                <div class="col-sm-4">
                                    <?php
                                        echo $form->field($model, 'satuankekuatan', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-12'
                                            ]
                                        ])->dropDownList($satuan_kekuatan, [
                                            'class' => 'select2 selectsatuankekuatan',
                                            'id'=>'satuankekuatan',
                                            'prompt' => '— Pilih —'
                                        ])->label(false);
                                    ?>
                                </div>
                            </div>
                            <?=
                                $form->field($model, 'jenisobatalkes_id', [
                                    'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ]
                                ])->dropDownList($jenis_oa, [
                                    'class' => 'select2',
                                    'prompt' => '— Pilih —'
                                ]);
                            ?>
                        </div>
                        <div class="col-md-6">
                            <?php
                                $kecilSatuan = !empty($model->satuankecil_id)
                                    ? $model->satuankecil_id : null;
                            ?>
                            <?=
                                $form->field($model, 'satuankecil_id', [
                                    'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ]
                                ])->dropDownList($satuankecil, [
                                    'class' => 'select2 satuankecil_id',
                                    'id'=>'satuankecil_id',
                                    'prompt' => '— Pilih —',
                                    'disabled' => $disabled
                                ]);
                            ?>
                            <?=$form->field($model, 'satuanbesar_id')->dropDownList($satuanbesar, [
                                'class' => 'select2',
                                'prompt'=> '— Pilih —',
                                'disabled' => $disabled
                            ])?>
                            <?=$form->field($model, 'kemasan_besar')->textInput([
                                'disabled' => $disabled
                            ])?>
                        </div>
                        <input type="hidden" name="satuan_disable" value="<?= $kecilSatuan ?>">
                        <!-- inputs -->
                    </fieldset>
                    <fieldset title="2" class="stepy-step" onmouseover="">
                        <legend class="stepy-legend"><?=Yii::t('fe', 'Penggolongan obat')?></legend>
                        <div class="col-md-4">
                            <?=$form->field($model, 'is_generik')->radioList(['1'=>'Ya', '0'=>'Tidak'])?>
                            <div id="error_ObatAlkesFormis_generik" class="col-md-offset-4"></div>
                            <?=$form->field($model, 'ven')->dropDownList($dataven, ['class'=>'select2','prompt'=>'— Pilih —',])?>
                            <?=$form->field($model, 'ruteobat_id')->dropDownList($dataRuteObat, ['class'=>'select2','prompt'=>'— Pilih —',])?>
                            <div id="error_ObatAlkesFormis_oral" class="col-md-offset-4"></div>
                            <?=$form->field($model, 'is_consigment')->radioList(['1'=>'Ya', '0'=>'Tidak'])?>
                            <div id="error_ObatAlkesFormis_consigment" class="col-md-offset-4"></div>
                            <?=$form->field($model, 'is_produksi')->radioList(['1'=>'Ya', '0'=>'Tidak'])?>
                            <div id="error_ObatAlkesFormis_is_produksi" class="col-md-offset-4"></div>
                            <?= $kategoriobat ?>
                        </div>
                        <div class="col-md-4">
                            <?=$form->field($model, 'obatalkes_kategori')->dropDownList($kategorioa, ['class'=>'select2','prompt'=>'— Pilih —',])?>
                            <?=$form->field($model, 'groupinacbg_id')->dropDownList($groupinacbg, ['class'=>'select2','prompt'=>'— Pilih —',])?>
                            <?=$form->field($model,'zataktif_id')->dropDownList($dataZatAktif, ['class'=>'select2','prompt'=>'— Pilih —',])?>
                            <?=$form->field($model,'atccode_id')->dropDownList($dataATCCode, ['class'=>'select2','prompt'=>'— Pilih —',])?>
                            <?=$form->field($model,'mims_id')->dropDownList($dataMIMS, ['class'=>'select2','prompt'=>'— Pilih —',])?>
                            <?=$form->field($model,'obatalkesmims_id')->dropDownList($dataSubMIMS, ['class'=>'select2','prompt'=>'— Pilih —',])?>
                        </div>
                        <div class="col-md-4">
                            <?=$form->field($model, 'is_formularium')->radioList(['1'=>'Ya', '0'=>'Tidak'])?>
                            <div id="error_ObatAlkesFormis_formularium" class="col-md-offset-4"></div>
                            <?= $form->field($model, 'is_antibiotic')->radioList(['1' => 'Ya', '0' => 'Tidak']) ?>
                            <div id="error_ObatAlkesFormis_antibiotic" class="col-md-offset-4"></div>
                            <?= $form->field($model, 'is_psycothropica')->radioList(['1' => 'Ya', '0' => 'Tidak']) ?>
                            <div id="error_ObatAlkesFormis_psycothropica" class="col-md-offset-4"></div>
                            <?= $form->field($model, 'is_narcotic')->radioList(['1' => 'Ya', '0' => 'Tidak']) ?>
                            <div id="error_ObatAlkesFormis_narcotic" class="col-md-offset-4"></div>
                        </div>
                        <!-- inputs -->
                    </fieldset>
                    <fieldset title="3" class="stepy-step" onmouseover="">
                        <legend class="stepy-legend"><?=Yii::t('fe', 'Harga obat alkes')?></legend>
                        <div class="col-md-6">
                            <?=$form->field($model, 'harganetto')
                                    ->textInput([
                                        'class'=>'harga-netto doco-number',
                                        'readonly'=>'true'
                                    ])?>
                            <?=$form->field($model, 'hargamaksimum')->textInput(['class'=>'doco-number hargamaksimum', 'readonly'=>'true'])
                            ->label(Yii::t('fe', 'Harga Netto Maksimum')); ?>
                            <?=$form->field($model, 'hargaterakhir')->textInput(['class'=>'doco-number hargaterakhir', 'readonly'=>'true'])
                            ->label(Yii::t('fe', 'Harga Terakhir')); ?>
                        </div>
                        <div class="col-md-6">
                            <?=$form->field($model, 'hargaminimum')->textInput(['class'=>'harga-minimum doco-number hargaminimum', 'readonly'=>'true'])
                            ->label(Yii::t('fe', 'Harga Netto Minimum'))?>
                            <?=$form->field($model, 'hargaratarata')->textInput(['class'=>'doco-number hargaratarata', 'readonly'=>'true'])
                            ->label(Yii::t('fe', 'Harga Netto Rata Rata'))?>
                            <?=$form->field($model, 'discount')->textInput(['class'=>'doco-number discount', 'readonly'=>'true'])
                            ->label(Yii::t('fe', 'discount'))?>
                        </div>
                        <!-- inputs -->
                    </fieldset>
                    <fieldset title="4" class="stepy-step" onmouseover="">
                        <legend class="stepy-legend"><?=Yii::t('fe', 'Indikasi obat')?></legend>
                        <div class="col-md-6">
                            <?=$form->field($model, 'indikasi')->textArea()?>
                            <?=$form->field($model, 'interaksi')->textArea()?>
                        </div>
                        <div class="col-md-6">
                            <?=$form->field($model, 'kontradiksi')->textArea()?>
                            <?=$form->field($model, 'efek_samping')->textArea()?>
                        </div>
                        <!-- inputs -->
                    </fieldset>
                    <fieldset title="5" class="stepy-step" onmouseover="">
                        <legend class="stepy-legend"><?=Yii::t('fe', 'Pengorderan')?></legend>
                        <div class="col-md-6">
                            <?=$form->field($model,'manufaktur_id')->dropDownList($dataManufaktur, ['class'=>'select2','prompt'=>'— Pilih —',])?>
                            <?php if($konfig_manufaktur == true):?>
                                <?=$form->field($model, 'manufacture_ids[]',[
                                    'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-md-4',
                                        'wrapper' => 'col-md-8'
                                    ]
                                ])->dropDownList($dataManufakturs, [
                                    'class' => 'select2 word-wrapper',
                                    'id' => 'manufacture_ids',
                                    'multiple' => 'multiple'
                                ]) ?>
                            <?php endif;?>
                            <?=$form->field($model, 'supplier_id')->dropDownList($dataSupplier, ['class'=>'select2','prompt'=>'— Pilih —',])?>
                            <?php if($konfig_supplier == true):?>
                                <?=$form->field($model, 'supplier_ids[]',[
                                    'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-md-4',
                                        'wrapper' => 'col-md-8'
                                    ]
                                ])->dropDownList($dataSuppliers, [
                                    'class' => 'select2 word-wrapper',
                                    'id' => 'supplier_ids',
                                    'multiple' => 'multiple'
                                ]) ?>
                            <?php endif;?>
                            <?=$form->field($model, 'reorder')->radioList(['1'=>'Ya', '0'=>'Tidak'], [
                                'inline' => true,
                                'separator' => ' &nbsp;&nbsp;'
                            ])?>
                            <div id="error_ObatAlkesFormreorder" class="col-md-offset-4"></div>

                            <?=$form->field($model, 'lead_time', [
                                'addon' => ['append' => ['content'=>'hari']],
                            ])->textInput(['class'=>'doco-number
                            lead_time'])?>
                            <?=$form->field($model, 'minimalstok')->textInput(['class'=>'doco-number minimalstok'])?>
                        </div>
                        <div class="col-md-6">
                            <?=$form->field($model, 'avg_usage')->textInput(['class'=>'doco-number
                            avg_usage']); ?>
                            <?=$form->field($model, 'min_order')->textInput(['class'=>'doco-number
                            min_order'])?>
                            <?=$form->field($model, 'max_order')->textInput(['class'=>'doco-number max_order'])?>
                            <?=$form->field($model, 'on_ro')->textInput(['class'=>'doco-number on_ro',
                            'readonly' => true])?>
                            <?=$form->field($model, 'on_po')->textInput(['class'=>'doco-number on_po',
                            'readonly' => true])?>
                        </div>
                    </fieldset>
                    <?=Html::submitButton("<b><i class='fa fa-floppy-o'></i></b> Simpan", [
                            'class'=>'stepy-finish btn btn-xs btn-labeled btn-info',
                            'id' => 'btn-submit'
                        ])?>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs("
    var callbackSupplier = ".json_encode($callbackSupplier).";
    var callbackManufaktur = ".json_encode($callbackManufaktur).";
    $.each(callbackSupplier, function(k,v){
        var option = new Option(v.text, v.id+'_'+v.text, true, true);
        $('#supplier_ids').append(option).trigger('change');
    });

    $.each(callbackManufaktur, function(k,v){
        var option = new Option(v.text, v.id+'_'+v.text, true, true);
        $('#manufacture_ids').append(option).trigger('change');
    });

    var mims = ".json_encode($masterMIMS).";
    var _satuanKecil = '{$kecilSatuan}';
    $('.form-obatalkes').stepy({
      titleClick: true,
      backLabel: '".Yii::t('fe', 'Kembali')." <b><i class=\'fa fa-chevron-left\'></i></b>',
      nextLabel: '".Yii::t('fe', 'Selanjutnya')." <b><i class=\'fa fa-chevron-right\'></i></b>',
    })
    $('.form-obatalkes').find('.button-next').addClass('btn btn-xs btn-labeled btn-info');
    $('.form-obatalkes').find('.button-back').addClass('btn btn-xs btn-labeled btn-info');
    $('.pickadate').pickadate({
        format: 'dd mmm, yyyy',
        selectMonths: true,
          selectYears: 99,
        formatSubmit: 'yyyy-mm-dd',
    });
    $(document).on('keyup', '.harga-netto', function(){
        $('.hargaminimum').val($(this).val());
        $('.hargamaksimum').val($(this).val());
        $('.hargaratarata').val($(this).val());
    })
    $('.harga-beli').on('keyup', function(){
        let ppn_hasil = docoHelper.convertToAngka( $(this).val() ) * '".$ppn_persen."'
        // $('.ppn').val(docoHelper.convertToRupiah( ppn_hasil ) )
        // $('.hargamaksimum').val(docoHelper.convertToRupiah(ppn_hasil));
    })
    $(document).ready(function(){
        $('.selectSupplier').select2({
            placeholder: '-',
            minimumInputLength: 3,
            ajax: {
                url: '/master/obat-alkes/get-supplier',
                dataType: 'json',
                quietMillis: 250,
                data: function(term, page){
                    return{
                        q: term,
                        page: page
                    }
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                }
            },
            dropdownCssClass: 'bigdrop',
            escapeMarkup: function (m) { return m; },
        });
    })
    $('#obatalkes-form').docoForm('submit',{
        success : function(data) {
            if (data.status == 201)
                this.formInput[0].reset();
            // table.draw();
            window.location = '".Url::to(['index'])."'
        },
        error: function(response){
            let formName = Object.keys(response.responseJSON.response.data)[0];
            let stepVal = $('[name=\"'+formName+'\"]').closest('fieldset').attr('title')
            $('.form-obatalkes').stepy('step', stepVal)
            $('[name=\"'+formName+'\"]').focus();
        }
    });
    $('.btn-kode').on('click', function(){
        $.ajax({
            url: '".Url::to(['get-kode'])."',
            beforeSend: function(){
                $('.kode-oa').val('Harap tunggu....')
                $('.kode-oa').attr('readonly', true)
                $(this).attr('disabled', true)
            },
            success: function(data){
                $('.kode-oa').val(data)
                $('.kode-oa').attr('readonly', false)
                $(this).attr('disabled', false)
            }
        })
    })

    $('#obatalkesform-mims_id').on('change', function(){
        let mims_id = $(this).val();

        $('#obatalkesform-obatalkesmims_id').val('').trigger('change');
        $('#obatalkesform-obatalkesmims_id')
            .find('option')
            .remove()
            .end()
            .append(`<option value=''>-- Pilih --</option>`)
            .val();

        $.each(mims[mims_id], function(key, value) {
            $('#obatalkesform-obatalkesmims_id').append($(`<option></option>`)
                .attr('value', value.obatalkesmims_id)
                .text(value.obatalkesmims_nama)); 
       });
    })

");
    
$this->registerJs(""
    .$this->render('js/obat.js')
, View::POS_END, "js-index");
?>