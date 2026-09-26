<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-08-14 13:58:13
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-09-07 11:01:49
 */
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use kartik\widgets\DatePicker;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Gudang Farmasi', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .datepicker>div{
        display:block;
    }

    #penerimaan_wrapper{
        height: 250px;
        overflow-y: scroll;
    }

    .consignment{
        background-color: #FDB7B7;
    }

    .checkbox{
        padding-top:1px!important;
    }

    body>.ui-pnotify {
        z-index: 1030 !important;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?=
                    DocoHelpers::generateToolbar([
                        'custom-save' => [
                            'type' => 'button',
                            'title' => Yii::t('fe', 'Simpan'),
                            'icon' => 'fa fa-floppy-o',
                            'attributes' => [
                                'data-options' => 'click',
                                'id' => 'simpan-penerimaan'
                            ],
                        ],
                    ]);
                ?>
            </div>
            <div class="panel-body">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h6 class="panel-title"><?= $this->title ?></h6>
                    </div>
                    <div class="panel-body">
                        <?php
                            $form = ActiveForm::begin([
                                'id' => 'penerimaan-form',
                                'enableAjaxValidation'=>false,
                                'enableClientValidation'=>false,
                                'action' => '/gudang/penerimaan-obat-supplier/set-list-item',
                                'type' => ActiveForm::TYPE_HORIZONTAL,
                                'formConfig' => [
                                    'labelSpan' => 4,
                                    'deviceSize' => ActiveForm::SIZE_SMALL
                                ],
                                'options' => [
                                    'skip-confirm' => "true"
                                ]
                        ]);
                        echo Html::hiddenInput('PenerimaanSupplierDetailForm[obatalkes_nama]',
                            $modelDetail->obatalkes_nama, [
                            'class' => 'obatalkes_nama'
                        ]);
                        echo Html::hiddenInput('PenerimaanSupplierDetailForm[obatalkes_kode]',
                            $modelDetail->obatalkes_kode, [
                            'class' => 'obatalkes_kode'
                        ]);
                        echo Html::hiddenInput('PenerimaanSupplierDetailForm[satuanunit_nama_besar]',
                            $modelDetail->satuanunit_nama_besar, [
                            'class' => 'satuanunit_nama_besar'
                        ]);

                        echo Html::hiddenInput('PenerimaanSupplierDetailForm[satuanunit_nama_kecil]',
                            $modelDetail->satuanunit_nama_kecil, [
                            'class' => 'satuanunit_nama_kecil'
                        ]);

                        echo Html::hiddenInput('PenerimaanSupplierDetailForm[nilai_konversi]',
                            $modelDetail->nilai_konversi, [
                            'class' => 'nilai_konversi'
                        ]);
                        echo Html::hiddenInput('countPenerimaan', $countPenerimaan, [
                            'class' => 'countPenerimaan'
                        ]);
                        echo Html::hiddenInput('isConsigmentHeader', false, [
                            'class' => 'isConsigmentHeader',
                        ]);
                        echo Html::hiddenInput('isDonasiHeader', false, [
                            'class' => 'isDonasiHeader'
                        ]);
                        echo Html::hiddenInput('PenerimaanSupplierDetailForm[is_consignment]', false, [
                            'class' => 'item_consignment'
                        ]);
                        echo Html::hiddenInput('PenerimaanSupplierDetailForm[is_donasi]', false, [
                            'class' => 'item_donasi'
                        ]);
                        ?>
                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="row">
                                    <div class="col-md-3">Jenis</div>
                                    <div class="col-md-3">
                                        <?= $form->field($model, 'is_consigment')->checkbox(); ?>
                                    </div>
                                    <div class="col-md-5">
                                        <?= $form->field($model, 'is_donasi')->checkbox(); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'tgl_penerimaan', [
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ]
                                ])->textInput([
                                    'placeholder' => Yii::t('fe', 'Tanggal Penerimaan'),
                                    'class' => 'form-control input-sm',
                                    'readOnly' => true,
                                    'value' => date('d-M-Y'),
                                    'tab-index' => 0,
                                ])->label(Yii::t('fe', 'Tanggal Penerimaan')); ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'peg_menyetujui',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ],
                                ])->dropDownList([],[
                                    'class' => '',
                                    'id' => 'peg_menyetujui',
                                ])->label(Yii::t('fe', 'Pegawai Menyetujui')); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <?= $form->field($model, 'supplier_id',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ],
                                ])->dropDownList([],[
                                    'class' => '',
                                    'id' => 'supplier_id',
                                ])->label(Yii::t('fe', 'Nama Supplier')); ?>
                            </div>
                            <div class="col-md-4">
                                <div class="nonconsignment" >
                                    <?= $form->field($model, 'no_faktur', [
                                    'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-4',
                                            'wrapper' => 'col-md-8'
                                        ]
                                    ])->textInput([
                                        'placeholder' => Yii::t('fe', 'Nomer Faktur'),
                                        'class' => 'form-control input-sm',
                                        'autocomplete' => "off",
                                    ])->label(Yii::t('fe', 'Nomor Faktur')); ?>
                                </div>
                                <div class="isconsignment" hidden>
                                    <?= $form->field($model, 'no_suratjalan', [
                                    'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-4',
                                            'wrapper' => 'col-md-8'
                                        ]
                                    ])->textInput([
                                        'placeholder' => Yii::t('fe', 'Nomer Surat Jalan'),
                                        'class' => 'form-control input-sm',
                                        'autocomplete' => "off",
                                    ])->label(Yii::t('fe', 'Nomor Surat Jalan')); ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'peg_mengetahui',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ],
                                ])->dropDownList([],[
                                    'class' => '',
                                    'id' => 'peg_mengetahui',
                                ])->label(Yii::t('fe', 'Pegawai Mengetahui')); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <?= $form->field($model, 'pajak_id',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ],
                                ])->dropDownList(ArrayHelper::map($options['ppn'],'pajak_id','pajak_label'),[
                                    'class' => 'select2',
                                    'prompt' => '-- Pilih --'
                                ])->label(Yii::t('fe', 'Tarif Pajak')); ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'payterm_id',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ],
                                ])->dropDownList(ArrayHelper::map($options['payterm'],'payterm_id','payterm_nama'),[
                                    'class' => 'select2',
                                    'prompt' => '-- Pilih --'
                                ])->label(Yii::t('fe', 'Payment Term')); ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'sumber_penerimaan',[
                                'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ],
                                ])->dropDownList(ArrayHelper::map($options['sumber_penerimaan'],'lookup_id','lookup_name'),[
                                    'class' => 'select2',
                                    'prompt' => '-- Pilih --'
                                ])->label(Yii::t('fe', 'Sumber Penerimaan')); ?>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="col-sm-4 ">
                                        <?= $form->field($modelDetail, 'obatalkes_id',[
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8',
                                            ],
                                        ])->dropDownList([],[
                                            'class' => '',
                                            'id' => 'obatalkes_id',
                                        ])->label(Yii::t('fe', 'Nama Obat')); ?>
                                    </div>
                                    <div class="col-sm-4">
                                        <?= $form->field($modelDetail, 'satuankonversi_id',[
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ],
                                        ])->widget(DepDrop::classname(), [
                                                'options' => ['id' => 'satuankonversi_id',
                                                'class' => 'form-control select-satuan'],
                                                'pluginOptions'=>[
                                                    'depends' => ['obatalkes_id'],
                                                    'placeholder' => Yii::t('fe', 'Satuan'),
                                                    'url'=> Url::to(['/api/master/get-satuan-konversi']),
                                                    'prompt' => Yii::t('fe', 'Pilih Satuan'),
                                                ]
                                            ])->label(Yii::t('fe', 'Satuan'));
                                        ?>
                                    </div>
                                    <div class="col-sm-4">
                                        <?= $form->field($modelDetail, 'qty_besar', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                            ]
                                        ])->textInput([
                                            'placeholder' => Yii::t('fe', 'Qty Penerimaan'),
                                            'class' => 'form-control input-sm doco-number text-right',
                                            'autocomplete' => "off",
                                        ])->label(Yii::t('fe', 'Qty Penerimaan')); ?>
                                        <span class="help-block text-info info-konversi text-semibold" style="
                                                margin-bottom: 0px!important;
                                                margin-top: 0px!important;
                                                margin-left: 145px!important;
                                            ">
                                            <i class="fa fa-info-circle" aria-hidden="true"></i>
                                            <u><i class="total_konversi"></i></u>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-sm-4">
                                        <?= $form->field($modelDetail, 'tgl_kadaluarsa', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                            ]
                                        ])->widget(DatePicker::classname(), [
                                            'name' => 'date_12',
                                            'value' => "",
                                            'readonly' => true,
                                            'language' => 'en',
                                            'pluginOptions' => [
                                                'autoclose' => true,
                                                'format' => 'dd-M-yyyy',
                                                // 'endDate' => "0d",
                                                'startDate' => "0d",
                                            ]
                                        ]); ?>
                                    </div>
                                    <div class="col-sm-4">
                                        <?= $form->field($modelDetail, 'harga_netto', [
                                            'addon' => ['prepend' => ['content'=>'Rp.']],
                                            'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-8'
                                                ]
                                        ])->textInput([
                                            'placeholder' => Yii::t('fe', 'Harga Netto (Satuan Besar)'),
                                            'class' => 'form-control total_harga input-sm text-right',
                                            'autocomplete' => "off",
                                        ])->label(Yii::t('fe', 'Total Harga Netto')); ?>
                                        <span class="help-block text-info info-harga text-semibold" style="
                                                margin-bottom: 10px!important;
                                                margin-top: 0px!important;
                                                margin-left: 145px!important;
                                            ">
                                            <i class="fa fa-info-circle" aria-hidden="true"></i>
                                            <u><i class="harga_netto"></i></u>
                                        </span>
                                    </div>
                                    <div class="col-sm-4">
                                        <?= $form->field($modelDetail, 'diskon', [
                                        'addon' => ['append' => ['content'=>'%']],
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->textInput([
                                            'placeholder' => Yii::t('fe', 'Diskon'),
                                            'class' => 'form-control input-sm text-right',
                                            'autocomplete' => "off",
                                            'value' => 0
                                        ]); ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="col-sm-4">
                                        <?= $form->field($modelDetail, 'no_batch', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->textInput([
                                            'placeholder' => Yii::t('fe', 'No. Batch'),
                                            'class' => 'form-control input-sm',
                                            'autocomplete' => "off",
                                        ]); ?>
                                    </div>
                                    <div class="col-sm-4">
                                        <?= $form->field($modelDetail, 'keterangan', [
                                        'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->textarea([
                                            'class' => 'form-control input-sm',
                                        ]); ?>
                                    </div>
                                    <div class="col-sm-4">
                                       <?= Html::submitButton(
                                            '<b><i class="fa fa-plus"></i></b>' . Yii::t('fe','Tambah'),
                                            [
                                                'class' => 'btn btn-success btn-labeled btn-xs',
                                                'id' => 'btn-tambah'
                                            ])
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="legend-index">
                                    <div class="legend-header">Keterangan</div>
                                    <div class="legend-wrapper">
                                        <div class="legend-information">
                                            <div class="legend-information__color consignment"></div>
                                            <div class="legend-information__text">Item Bukan Consignment</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <table id="penerimaan" class="table table-striped table-condensed table-hover" style="width:100%">
                                    <thead>
                                        <tr class="bg-inverse">
                                            <th width="1">No</th>
                                            <th><?=\Yii::t("fe", "Kode");?></th>
                                            <th><?=\Yii::t("fe", "Nama");?></th>
                                            <th><?=\Yii::t("fe", "Qty");?></th>
                                            <th><?=\Yii::t("fe", "Qty");?></th>
                                            <th><?=\Yii::t("fe", "Tanggal Kadaluarsa");?></th>
                                            <th class="text-right"><?=\Yii::t("fe", "Total Harga Netto (Rp.)");?></th>
                                            <th><?=\Yii::t("fe", "Diskon (%)");?></th>
                                            <th><?=\Yii::t("fe", "No. Batch");?></th>
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
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->render("/alert-perubahan-harga/_modal.php") ?>
<?php
$this->registerJs("
    var need_verif = '".$need_verif."';
    let harga_donasi = '".$harga_donasi."'

    ".$this->render('/assets/js/penerimaan.js').$this->render('/alert-perubahan-harga/alert-harga.js', ["url"=>"penerimaan-obat-supplier"]), View::POS_END);
?>