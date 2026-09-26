<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-08-10 11:33:35
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2019-03-19 16:04:57
 */
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
?>

<div class="row">
    <div class="col-md-12">
        <?php
        $form = ActiveForm::begin([
            'id' => 'keluar-form',
            'enableAjaxValidation'=>false,
            'enableClientValidation'=>false,
            'action' => '/gudang/adjustment-obat-alkes/set-list-item-keluar',
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'formConfig' => [
                'labelSpan' => 3,
                'deviceSize' => ActiveForm::SIZE_SMALL
            ],
            'options' => [
                'skip-confirm' => "true"
            ]
        ]);
        echo Html::hiddenInput('AdjusmenObatKeluarForm[obatalkes_kode]', $model->obatalkes_kode, [
            'class' => 'obatalkes_kode'
        ]);

        echo Html::hiddenInput('AdjusmenObatKeluarForm[obatalkes_nama]', $model->obatalkes_nama, [
            'class' => 'obatalkes_nama'
        ]);

        echo Html::hiddenInput('AdjusmenObatKeluarForm[satuanunit_nama_besar]',
            $model->satuanunit_nama_besar, [
            'class' => 'satuanunit_nama_besar_keluar'
        ]);

        echo Html::hiddenInput('AdjusmenObatKeluarForm[satuanunit_nama_kecil]',
            $model->satuanunit_nama_kecil, [
            'class' => 'satuanunit_nama_kecil_keluar'
        ]);

        echo Html::hiddenInput('AdjusmenObatKeluarForm[nilai_konversi]',
            $model->nilai_konversi, [
            'class' => 'nilai_konversi_keluar'
        ]);

        echo Html::hiddenInput('countKeluar', $countKeluar, [
            'class' => 'countKeluar'
        ]);
    ?>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'obatalkes_id',[
            'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-8'
                ],
            ])->dropDownList([],[
                'class' => 'select2',
                'id' => 'obatalkes_id_keluar',
                'tabindex' => 1
            ])->label(Yii::t('fe', 'Obat Alkes')); ?>

            <?= $form->field($model, 'satuankonversi_id',[
            'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-8'
                ],
            ])->widget(DepDrop::classname(), [
                    'options' => [
                        'id'=>'satuankonversi_id_keluar',
                        'class' => 'form-control select-satuan',
                        'tabindex' => 2,
                    ],
                    'pluginOptions'=>[
                        'depends' => ['obatalkes_id_keluar'],
                        'placeholder' => Yii::t('fe', 'Satuan'),
                        'url'=> Url::to(['/gudang/adjustment-obat-alkes/get-satuan-konversi']),
                        'prompt' => Yii::t('fe', 'Pilih Satuan'),
                    ]
                ])->label(Yii::t('fe', 'Satuan'));
            ?>

            <?= $form->field($model, 'qty', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-8'
                    ]
                ])->textInput([
                    'placeholder' => Yii::t('fe', 'Qty Pengeluaran'),
                    'class' => 'form-control input-sm doco-number',
                    'autocomplete' => "off",
                    'tabindex' => 3
                ])->label(Yii::t('fe', 'Qty Pengeluaran')); ?>
            
            <?= $form->field($model, 'alasan', [
            'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-8'
                ]
            ])->textArea([
                'placeholder' => Yii::t('fe', 'Alasan Pengeluaran'),
                'class' => 'form-control input-sm',
                'rows' => 5,
                'tabindex' => 4
            ])->label(Yii::t('fe', 'Alasan Pengeluaran')); ?>
            
            <?= $form->field($model, 'no_batch', [
                'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-4',
                        'wrapper' => 'col-md-8'
                    ]
                ])->textInput([
                    'placeholder' => Yii::t('fe', 'No. Batch'),
                    'class' => 'form-control input-sm',
                    'autocomplete' => "off",
                    'tabindex' => 5
                ]); ?>

            <?= $form->field($model, "keterangan", [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-8'
                ]
            ])->textarea([
                'placeholder' => Yii::t('fe', 'Keterangan'),
                'class' => 'form-control input-sm',
                'rows' => 5,
                'tabindex' => 6
            ]); ?>

            <div class="form-group">
                <label class="control-label text-left col-sm-4"></label>
                <div class="col-md-8">
                    <?= Html::submitButton(
                        '<b><i class="fa fa-plus"></i></b>' . Yii::t('fe','Tambah'),
                        [
                            'class' => 'btn btn-success btn-labeled btn-xs',
                            'tabindex' => 7
                        ])
                    ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="alert alert-info alert-styled-left alert-bordered info-obat">
                  <span class="text-semibold">
                        <p class="stok"></p>
                        <p class="total_konversi_keluar"></p>
                  </span>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    </div>
</div><br><br>
<div class="row">
    <div class="col-md-12">
        <table id="adjus-keluar" class="table table-striped table-condensed table-hover" style="width:100%">
            <thead>
                <tr class="bg-inverse">
                    <th width="1">No</th>
                    <th><?=\Yii::t("fe", "Kode Obat Alkes");?></th>
                    <th><?=\Yii::t("fe", "Nama Obat Alkes");?></th>
                    <th><?=\Yii::t("fe", "Qty Pengeluaran");?></th>
                    <th><?=\Yii::t("fe", "Qty Konversi ");?></th>
                    <th><?=\Yii::t("fe", "Alasan Pengeluaran");?></th>
                    <th><?=\Yii::t("fe", "No Batch");?></th>
                    <th><?=\Yii::t("fe", "Keterangan");?></th>
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
        countKeluar = parseInt($('.countKeluar').val());
        $('.select-satuan').select2();
        $('.info-obat').hide();
        $('.total_konversi').hide();
        if(countKeluar == 0) {
            $("#simpan-adjustment").prop('disabled', true);
        }
        else {
            $("#simpan-adjustment").prop('disabled', false);
        }
    });
</script>