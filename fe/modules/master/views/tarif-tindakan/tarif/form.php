<?php
    use yii\web\View;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use app\components\DocoHelpers;
    use kartik\widgets\DepDrop;
    use kartik\widgets\ActiveForm;
    $this->title = $title;
?>
<style type="text/css">
    .radio label, .checkbox label {
        padding-left: 0px;
    }
    input[type='checkbox']:hover {
         box-shadow: 0 0 5px 0px #fff; 
    }
    input[type='checkbox']:focus {
        box-shadow: 0 0 5px 0px #fff;
    }
    /*label.required label.control-label:after {
      content: " *";
      color: red; 
    }
    .required:after {
        color: #e32;
        content: ' *';
        display:inline;
    }*/
    .checkbox label {
        padding-left: 30px !important;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?= $this->title ;?></b></h3>
            </div>
            <div class="panel-toolbar clearfix">
                <?=
                    DocoHelpers::generateToolbar([
                        'save' =>[
                            'attributes' => [
                                'id' => 'btn-submit-tarif'
                            ]
                        ],
                        'log-tarif' => [
                            'title' => \Yii::t('fe', 'Log Tarif'),
                            'icon' => 'fa fa-eye',
                            'attributes' => [
                                'id' => 'log-tarif',
                                'data-popup'=>'tooltip',
                                'data-toggle'=>'modal',
                                'data-target'=>'#modal_backdrop',
                                'action' => '/master/tarif-tindakan/view-log?'.$urlLog,
                                'data-width' => "75%",
                            ]
                         ],
                        'reset'=>[
                            'attributes'=>[
                                'id' => 'btn-reset-tarif',
                                'data-parent'=>'#tarif-form',
                            ]
                        ],
                        'kembali' => [
                            'title' => \Yii::t('fe', 'Kembali'),
                            'icon' => 'fa fa-arrow-left',
                            'attributes' => [
                                'id' => 'btn-back-tarif',
                                'class' => 'spa',
                                'data-options' => 'click',
                                'data-content'=>'content-tarif',
                                'data-url' => '/master/tarif-tindakan/tarif',
                            ]
                        ],
                    ]);
                ?>
            </div>
            <div class="panel-body">
                <?php $form = ActiveForm::begin([
                    'id' => 'tarif-form', 
                    'enableAjaxValidation' => false,
                    'enableClientValidation' => false,
                    'validateOnSubmit' => false, 
                    'formConfig' => [
                            'labelSpan' => 4,
                            'deviceSize' => ActiveForm::SIZE_MEDIUM
                        ],
                    'options' => [
                            'class' => 'form-horizontal',
                            'role' => 'form',
                        ]
                    ]); 
                ?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="col-md-4">
                            <div class="panel panel-flat">
                                <div class="panel-heading">
                                    <h5 class="panel-title"><?=Yii::t('fe', 'Form Tambah Tarif Paket Tindakan')?></h5>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                        <?= Html::label('Jenis Tindakan/Paket', '_tindakanpaket', ['class' => '', 'id' => 'label_paket']) ?>
                                        <?= $form->field($model, 'tindakanpaket')->radioList($additional_data['jenis_tindakan_paket'],[
                                                'item'=> function($index, $label, $name, $checked, $value) use ($model, $additional_data) {
                                                    $return = '<div class="col-md-offset-0"><div class="form-group" style="display: grid;"><label class="control-label col-md-8"><input type="radio" id="RADIO-'.$value.'" name="'.$name.'" value="'.$value.'"> '.'Nama '.strtolower($label).'</label>';
                                                    $return .= '<div class="col-md-12">';
                                                    $return .= Html::activeDropDownList($model, ($value == 'TINDAKAN') ? 'daftartindakan_id' : 'tipepaket_id', [], ['class'=>($value == 'TINDAKAN') ? 'form-control select2 select-tindakan col-md-8' : 'form-control select2 select-paket col-md-8', 'disabled'=>'true']);
                                                    $return .= '</div></div></div>';
                                                    return $return;
                                                }
                                            ],[
                                                // 'inline'=>true,
                                                'class'=>'col-md-8'
                                            ]
                                        )->label(false); ?>

                                        <?php if ($status_edit == 1) : ?>
                                        <div id="edit-tindakan">
                                            <?=Html::activeHiddenInput($model, 'daftartindakan_id', ['class'=>'satuan-text', 'id'=>'val-tindakan'])?>
                                        </div>
                                        <div id="edit-paket">
                                            <?=Html::activeHiddenInput($model, 'tipepaket_id', ['class'=>'satuan-text', 'id'=>'val-paket'])?>
                                        </div>
                                        <?php endif; ?>
                                        <?= $form->field($model, 'kelaspelayanan_id')->dropDownList([],[
                                            'class' => 'form-control select2 selectKelas col-md-8',
                                            'prompt' => Yii::t('fe', '— Pilih Kelas Pelayanan —'),
                                            'disabled' => ($status_edit == 1) ? true : false,
                                        ])->label(Yii::t('fe', 'Kelas Pelayanan'),[
                                            'class' =>' text-left control-label col-md-4',
                                            ]
                                        ); ?>

                                        <?= $form->field($model, 'kamar_ruangan_id')->dropDownList([],[
                                            'class' => 'form-control select2 col-md-8',
                                            'id' => 'kamar_ruangan_id',
                                            'prompt' => Yii::t('fe', '— Pilih Kamar —'),
                                            'disabled' => ($status_edit == 1) ? true : false,
                                        ])->label(Yii::t('fe', 'Kamar'),[
                                            'class'=>'text-left control-label col-md-4'
                                            ]
                                        ); ?>

                                        <?= $form->field($model, 'carabayar_id')->dropDownList([],[
                                            'class' => 'form-control select2 col-md-8',
                                            'id'=>'carabayar_id',
                                            'prompt' => Yii::t('fe', '— Pilih Cara bayar —'),
                                            'disabled' => ($status_edit == 1) ? true : false,
                                        ])->label(Yii::t('fe', 'Cara bayar'),[
                                            'class'=>'text-left control-label col-md-4'
                                            ]
                                        ); ?>

                                        <?= $form->field($model, 'penjamin_id')->dropDownList([],[
                                            'class' => 'form-control select2 col-md-8',
                                            'id'=>'penjamin_id',
                                            'prompt' => Yii::t('fe', '— Pilih Penjamin —'),
                                            'disabled' => ($status_edit == 1) ? true : false,
                                        ])->label(Yii::t('fe', 'Penjamin'),[
                                            'class'=>'text-left control-label col-md-4'
                                            ]
                                        ); ?>

                                        <?= $form->field($model, 'dokter_id')->dropDownList([],[
                                            'class' => 'form-control select2 col-md-8',
                                            'id'=>'dokter_id',
                                            'prompt' => Yii::t('fe', '— Pilih Dokter —'),
                                            'disabled' => ($status_edit == 1) ? true : false,
                                        ])->label(Yii::t('fe', 'Dokter'),[
                                            'class'=>'text-left control-label col-md-4'
                                            ]
                                        ); ?>

                                        <?= $form->field($model, 'perdatarif_id')->dropDownList(@$additional_data['perdatarif'],[
                                            'id'=>'perdatarif_id',
                                            'class' => 'form-control select2 col-md-8',
                                            'prompt' => Yii::t('fe', '— Pilih Perda —'),
                                            'disabled' => ($status_edit == 1) ? true : false,
                                        ])->label(Yii::t('fe', 'Perda / SK'),[
                                            'class'=>'text-left control-label col-md-4'
                                            ]
                                        ); ?>
                                        
                                        <?= $form->field($model, "persencyto_tindakan", [
                                            'addon' => [
                                                'append' => [
                                                    'content'=>'%'
                                                ],
                                                'prepend' => [
                                                    'content'=>'Persen Cito'
                                                ]
                                            ],
                                        ])->textInput([
                                                "class" => "text-right persencyto_tindakan col-md-8",
                                                'id' => 'persencyto_tindakan',
                                                "placeholder" => '0,00'
                                        ])->label(Yii::t('fe', 'Persen Cito')); ?>
                                        <?= $form->field($model, "persen_penyulit", [
                                            'addon' => [
                                                'append' => [
                                                    'content'=>'%'
                                                ],
                                                'prepend' => [
                                                    'content'=>'Persen Penyulit'
                                                ]
                                            ],
                                        ])->textInput([
                                                "class" => "text-right persen_penyulit col-md-8",
                                                'id' => 'persen_penyulit',
                                                "placeholder" => '0,00'
                                        ])->label(Yii::t('fe', 'Persen Penyulit')); ?>
                                        <?=
                                            $form->field($model, 'is_active', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-right control-label col-sm-4',
                                                    'wrapper' => 'col-md-7',
                                                ],
                                            ])->checkbox([
                                                'label' => 'Aktif',
                                                'id' => 'is_active',
                                                'class'=>'text-left control-label col-md-4'
                                            ])
                                        ?>
                                        
                                        <div style="visibility: hidden">
                                        <?= $form->field($model, "persendiskon_tindakan", [
                                            'addon' => [
                                                'append' => [
                                                    'content'=>'%'
                                                ]
                                            ],
                                        ])->textInput([
                                                "class" => "text-right persendiskon_tindakan col-md-8",
                                                'id' => 'persendiskon_tindakan',
                                                "placeholder" => '0,00'
                                        ])->label(Yii::t('fe', 'Persen Diskon'),[
                                            'class'=>'text-left control-label col-md-4'
                                            ]
                                        ); ?>
                                        </div>
                                        
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8" id="status-tindakan" style="display:none">
                            <div class="panel panel-flat">
                                <div class="panel-heading">
                                    <h5 class="panel-title"><?=Yii::t('fe', 'Detail Komponen Tindakan')?></h5>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group required">
                                                <label class="col-md-2 control-label"><?=Yii::t('fe', 'Total Harga Tindakan')?></label>
                                                <div class="col-md-3">
                                                    <?= Html::input('text', 'TarifTindakanForm[total_harga_tindakan]', $model->total_harga_tindakan, [
                                                            'class' => "form-control text-right doco-number total_harga_tindakan",
                                                            'id' => 'total_harga_tindakan',
                                                            "placeholder" => '0,00'
                                                    ]) ?>
                                                </div>
                                            </div>
                                            <div class="form-group required">
                                                <label class="col-md-2 control-label"><?=Yii::t('fe', 'Hitung Berdasarkan')?></label>
                                                <div class="col-md-8">
                                                    <?= Html::radioList('TarifTindakanForm[is_persentase]', $model->is_persentase, [0 => 'Persentase', 1 => 'Nominal'],[
                                                            'encode' => false,
                                                            'id' => 'is_persentase_tindakan',
                                                    ]) ?>
                                                </div>
                                            </div>
                                            <div class="form-group required">
                                                <label class="col-md-2 control-label"><?=Yii::t('fe', 'Komponen')?></label>
                                                <div class="col-md-8">
                                                <?= 
                                                Html::dropDownList('komponentarif_id', '', [],[
                                                        'class' => 'form-control select2 selectKomponen',
                                                        'id' => 'komponentarif_id',
                                                        'prompt' => Yii::t('fe', '-- Pilih Komponen --'),
                                                    ]) ?>
                                                </div>
                                                <div class="col-lg-1">
                                                    <button class="btn btn-success btn-sm add-row"><i class="fa fa-plus"></i></button>
                                                </div>
                                            </div>
                                            <table class="table datatable-basic table-striped table-hover dataTable no-footer table-framed tbl-komponen">
                                                <thead>
                                                    <tr class="bg-inverse">
                                                        <!-- <th>No</th> -->
                                                        <th>Komponen</th>
                                                        <th align="right" style="text-align: right;">Persentase (%)</th>
                                                        <th align="right" style="text-align: right;">Nominal Harga (Rp.)</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="tr-default">
                                                        <td class="text-center" colspan="4"><?=Yii::t('fe', 'Data tidak tersedia')?></td>
                                                    </tr>
                                                </tbody>
                                                <tfoot></tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8" id="status-paket" style="display:none">
                            <div class="panel panel-flat">
                                <div class="panel-heading">
                                    <h5 class="panel-title"><?=Yii::t('fe', 'Detail Komponen Paket')?></h5>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table class="table datatable-basic table-striped table-hover dataTable no-footer table-framed tbl-detail-tindakan">
                                                <thead>
                                                    <tr class="bg-inverse">
                                                        <th>No</th>
                                                        <th>Tindakan</th>
                                                        <th>Kelompok</th>
                                                        <th>Instalasi - Ruangan</th>
                                                        <th align="right" style="text-align: right;">Harga (Rp.)</th>
                                                        <th width='3%'>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="tr-detail-default">
                                                        <td class="text-center" colspan="6">
                                                            <?=\Yii::t("fe", "No data available in table.");?>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td class="text-center" colspan="3"></td>
                                                        <td align="right"><p><b>Total Harga : </b></p></td>
                                                        <td colspan="2">
                                                        <?= $form->field($model, 'total_harga', [
                                                            'labelOptions' => ['class' => 'text-left'],
                                                            'horizontalCssClasses' => [
                                                                    'label' => 'text-left control-label col-sm-qw',
                                                                    'wrapper' => 'col-md-12'
                                                            ],
                                                            'addon'=>[
                                                                'prepend' => [
                                                                    'content'=>'Rp. '
                                                                ]
                                                            ],
                                                            ])->textInput([
                                                                'placeholder' => Yii::t('fe', 'Total Harga'),
                                                                'class' => 'form-control input-sm doco-number',
                                                                'autocomplete' => "off",
                                                                'id' => 'total_harga',
                                                                'readonly' => 'true'
                                                            ])->label(false); ?>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if(!$status_edit || !empty($model->tipepaket_id)) : ?>
                        <div class="col-md-8" id="status-paket-mcu" style="display:none">
                            <div class="panel panel-flat">
                                <div class="panel-heading">
                                    <h5 class="panel-title"><?=Yii::t('fe', 'Detail Komponen Paket')?></h5>
                                </div>
                                <div class="form-group required">
                                    <label class="ml-6 col-md-4 control-label"><?=Yii::t('fe', 'Penyesuaian Berdasarkan')?></label>
                                    <div class=" ml-6 col-md-8">
                                        <?= Html::radioList('TarifTindakanForm[is_persentase]', $model->is_persentase, [0 => 'Persentase', 1 => 'Nominal'],[
                                                'encode' => false,
                                                'id' => 'is_persentase',
                                        ]) ?>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <button type="button" style="display:none" data-options="click" class = 'btn btn-info' id= 'btn-show-detail-mcu'>Tampilkan detail Paket</button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table class="table datatable-basic table-striped table-hover dataTable no-footer table-framed tbl-detail-tindakan">
                                                <thead>
                                                    <tr class="bg-inverse">
                                                        <th>No</th>
                                                        <th>Tindakan</th>
                                                        <th>Kelompok</th>
                                                        <th>Instalasi - Ruangan</th>
                                                        <th align="right" style="text-align: right;">Harga Tarif (Rp.)</th>
                                                        <th>Penyesuaian (%)</th>
                                                        <th>Penyesuaian (Rp.)</th>
                                                        <th width='3%'>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="tr-detail-default-mcu">
                                                        <td class="text-center" colspan="6">
                                                            <?=\Yii::t("fe", "No data available in table.");?>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td class="text-center" colspan="3"></td>
                                                        <td align="right"><p><b>Total Harga : </b></p></td>
                                                        <td colspan="2">
                                                        <?= $form->field($model, 'total_harga', [
                                                            'labelOptions' => ['class' => 'text-left'],
                                                            'horizontalCssClasses' => [
                                                                    'label' => 'text-left control-label col-sm-qw',
                                                                    'wrapper' => 'col-md-12'
                                                            ],
                                                            'addon'=>[
                                                                'prepend' => [
                                                                    'content'=>'Rp. '
                                                                ]
                                                            ],
                                                            ])->textInput([
                                                                'placeholder' => Yii::t('fe', 'Total Harga'),
                                                                'class' => 'form-control input-sm doco-number',
                                                                'autocomplete' => "off",
                                                                'id' => 'total_harga_mcu',
                                                                'readonly' => 'true'
                                                            ])->label(false); ?>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif?>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php 
$tmpPenjaminId = isset($tmpPenjaminId) ? $tmpPenjaminId : '';
$tmpKelasPelayanan = isset($tmpKelasPelayanan) ? $tmpKelasPelayanan : '';
$this->registerJs("
    var komponen = []
    var komponenjson = ".$jsonKomponen."
    var status_edit = ".$status_edit."
    var total_harga = '".$model->total_harga."'
    var komponentotal = {}
    var opsiPerda = ".json_encode($opsiPerda)."
    var counter = 0
    var tipepaket = '".$tipepaket."'
    var tindakanpaket = '".$model->tindakanpaket."'
    var opsiKelas = ".json_encode($opsiKelas)."
    var opsiCaraBayar = ".json_encode($opsiCaraBayar)."
    var opsiPenjamin = ".json_encode($opsiPenjamin)."
    var opsiDokter = ".json_encode($opsiDokter)."
    var is_akomodasi = ".$is_akomodasi."
    var kamarruangan_id = '".$kamarruangan_id."'
    var kamar = '".$kamar."'
    var _id = '".$id."';
    var komponenRs = ".$komponenRs.";
    var is_mcu = false;
    var tipepaket_id = '';
    var tmpPenjaminId = '".$tmpPenjaminId."';
    var tmpKelasPelayanan = '".$tmpKelasPelayanan."';
    var opsitindakanpaket = ".json_encode($opsitindakan), View::POS_END);
    
    $this->registerJs($this->render('../js/tarif.js'), View::POS_END);
?>