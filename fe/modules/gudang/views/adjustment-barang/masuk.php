<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-08-10 11:33:35
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-12-11 16:29:29
 */
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use kartik\widgets\DatePicker;
?>
<style>
    .datepicker>div{
        display:block;
    }
</style>
<div class="row">
    <div class="col-md-12">
     <?php
        $form = ActiveForm::begin([
            'id' => 'masuk-form',
            'enableAjaxValidation'=>false,
            'enableClientValidation'=>false,
            'action' => '/gudang/adjustment-barang/set-list-item-masuk',
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'formConfig' => [
                'labelSpan' => 3,
                'deviceSize' => ActiveForm::SIZE_SMALL
            ],
            'options' => [
                'skip-confirm' => "true"
            ]
        ]);

        echo Html::hiddenInput('AdjusmenBarangMasukForm[barang_nama]', $model->barang_nama, [
            'class' => 'barang_nama'
        ]);

        echo Html::hiddenInput('AdjusmenBarangMasukForm[satuanunit_nama_besar]',
            $model->satuanunit_nama_besar, [
            'class' => 'satuanunit_nama_besar'
        ]);

        echo Html::hiddenInput('AdjusmenBarangMasukForm[satuanunit_nama_kecil]',
            $model->satuanunit_nama_kecil, [
            'class' => 'satuanunit_nama_kecil'
        ]);

        echo Html::hiddenInput('AdjusmenBarangMasukForm[nilai_konversi]',
            $model->nilai_konversi, [
            'class' => 'nilai_konversi'
        ]);

        echo Html::hiddenInput('countMasuk', $countMasuk, [
            'class' => 'countMasuk'
        ]);
    ?>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'barang_id',[
            'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-8'
                ],
            ])->dropDownList([],[
                'class' => '',
                'id' => 'barang_id',
                'autofocus' => true,
                'tabindex' => 1
            ])->label(Yii::t('fe', 'Nama Barang')); ?>

            <?= $form->field($model, 'satuankonversi_id',[
            'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-8'
                ],
            ])->widget(DepDrop::classname(), [
                    'options' => [
                        'id'=>'satuankonversi_id', 
                        'class' => 'form-control select-satuan',
                        'tabindex' => 2
                    ],
                    'pluginOptions'=>[
                        'depends' => ['barang_id'],
                        'placeholder' => Yii::t('fe', 'Satuan'),
                        'url'=> Url::to(['/gudang/adjustment-barang/get-satuan-konversi']),
                        'prompt' => Yii::t('fe', 'Pilih Satuan')
                    ]
                ])->label(Yii::t('fe', 'Satuan'));
            ?>

            <?= $form->field($model, 'qty', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-8'
                    ]
                ])->textInput([
                    'placeholder' => Yii::t('fe', 'Qty Penerimaan'),
                    'class' => 'form-control input-sm doco-number',
                    'autocomplete' => "off",
                    'tabindex' => 3
                ])->label(Yii::t('fe', 'Qty Penerimaan')); ?>

            <?= $form->field($model, 'harga_netto', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-8'
                    ]
                ])->textInput([
                    'placeholder' => Yii::t('fe', 'Harga Netto'),
                    'class' => 'form-control input-sm doco-number',
                    'autocomplete' => "off",
                    'tabindex' => 4
                ])->label(Yii::t('fe', 'Harga Netto')); ?>

             <?= $form->field($model, 'tgl_kadaluarsa', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-8'
                    ]
                ])->widget(DatePicker::classname(), [
                'name' => 'date_12',
                'value' => "",
                'readonly' => true,
                'language' => 'en',
                'options' => [
                    'tabindex' => 5,
                ],
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-M-yyyy',
                    'startDate' => "0d"

                ]
            ])->label(Yii::t("fe", "Tanggal Kadaluarsa")); ?>

            <?= $form->field($model, 'no_batch', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-8'
                    ]
                ])->textInput([
                    'placeholder' => Yii::t('fe', 'No. Batch'),
                    'class' => 'form-control input-sm',
                    'autocomplete' => "off",
                    'tabindex' => 6
            ]); ?>

            <div class="form-group">
                <label class="control-label text-left col-sm-4"></label>
                <div class="col-md-8">
                    <?= Html::submitButton(
                        '<b><i class="fa fa-plus"></i></b>' . Yii::t('fe','Tambah'),
                        [
                            'class' => 'btn btn-success btn-labeled btn-xs',
                        ])
                    ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="alert alert-info alert-styled-left alert-bordered info-konversi-masuk">
                  <span class="text-semibold">
                      <p class="total_konversi_masuk"></p>
                  </span>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    </div>
</div><br><br>
<div class="row">
    <div class="col-md-12">
        <table id="adjus-masuk" class="table table-striped table-condensed table-hover" style="width:100%">
            <thead>
                <tr class="bg-inverse">
                    <th width="1">No</th>
                    <th><?=\Yii::t("fe", "Nama Barang");?></th>
                    <th><?=\Yii::t("fe", "Qty Penerimaan");?></th>
                    <th><?=\Yii::t("fe", "Qty Konversi ");?></th>
                    <th><?=\Yii::t("fe", "Tanggal Kadaluarsa");?></th>
                    <th><?=\Yii::t("fe", "Harga Netto");?></th>
                    <th><?=\Yii::t("fe", "No. Batch");?></th>
                    <th width="12"><?=\Yii::t("fe", "Aksi");?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center" colspan="8">
                        <?=\Yii::t("fe", "Data tidak ditemukan.");?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function($) {
        $('.select-satuan').select2();
        $('.info-konversi-masuk').hide();
        countMasuk = parseInt($('.countMasuk').val());
            $("#barang_id").select2({
                placeholder: "Pilih Nama Barang",
                minimumInputLength: 3,
                ajax : {
                    url: "/gudang/adjustment-barang/search-barang?tipe=0",
                    dataType: 'json',
                    quietMillis: 250,
                    data: function (params) {
                      var query = {
                        search: params,
                      }
                      return params;
                    },
                    processResults: function (data) {
                      return {
                        results: data.result
                      };
                    },
                    dropdownCssClass: 'bigdrop',
                    escapeMarkup: function (m) { return m; },
                },
            }).on('select2:select', function(e){
                var data = e.params.data;
                $(".barang_nama").val(data.text);
                $(".satuan_awal_obat").val(data.satuankecil_id);
                if(data.is_kadaluarsa == false) {
                    $(".div-tgl_kadaluarsa").hide();
                    $("#adjusmenbarangmasukform-tgl_kadaluarsa").prop("disabled", true);
                }
                else {
                    $(".div-tgl_kadaluarsa").show();
                    $("#adjusmenbarangmasukform-tgl_kadaluarsa").prop("disabled", false);
                }
                $("#barang_id").focus();
            });
        if(countMasuk == 0) {
            $("#simpan-adjustment").prop('disabled', true);
        }
        else {
            $("#simpan-adjustment").prop('disabled', false);
        }
    });
</script>
