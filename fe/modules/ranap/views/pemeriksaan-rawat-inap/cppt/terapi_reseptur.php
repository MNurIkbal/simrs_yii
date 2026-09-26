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

<?= Html::hiddenInput('isEditReseptur', isset($isEditReseptur) ? $isEditReseptur : false, ['id' => 'is-edit-reseptur']) ?>
<div class="row">
        <hr>
</div>
<?php $form = ActiveForm::begin([
    'id' => 'form-reseptur', 
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'enableClientValidation' => false,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]) ?>
<?= Html::activeHiddenInput($modelReseptur, 'reseptur_id') ?>
<div class="row">
    <div class="col-lg-3">
        <?= $form->field($modelReseptur, 'is_hamil')->radioList(
            [1 => Yii::t('fe', 'Ya'), 0 => Yii::t('fe', 'Tidak')],
            ['inline'=>true]
        )->label(Yii::t('fe', 'Hamil')) ?>
    </div>
    <div class="col-lg-3">
        <!-- Berat badan -->
        <?= $form->field($modelReseptur, 'berat_badan', [
            'template' => '{label}<div class="input-group">{input}<span class="input-group-addon" id="basic-addon2">Kg</span></div>{error}{hint}'
        ])->textInput(['class' => 'form-control input-sm doco-decimal-wcomma bb_tb', 'id' => 'berat_badan']) ?>
    </div>
    <div class="col-lg-3">
        <!-- Tinggi badan -->
        <?= $form->field($modelReseptur, 'tinggi_badan', [
            'inputOptions' => ['class' => 'form-control input-sm doco-decimal-wcomma bb_tb'],
            'template' => '{label}<div class="input-group">{input}<span class="input-group-addon" id="basic-addon2">Cm</span></div>{error}{hint}'
        ])->textInput(['class' => 'form-control input-sm doco-decimal-wcomma bb_tb', 'id' => 'tinggi_badan']) ?>
    </div>
    <div class="col-lg-3">
        <!-- Luas tubuh -->
        <?= $form->field($modelReseptur, 'luas_tubuh')->textInput([
            'id' => 'luas_tubuh',
            'class' => 'form-control input-sm doco-decimal-wcomma',
        ])->label(Yii::t('fe', 'Luas Permukaan Tubuh')) ?>
    </div>
</div>
<div class="row">
    <div class="col-lg-3">
        <div class="form-group">
            <?=Html::label(Yii::t('fe', 'Diagnosa'), '', ['class' => 'control-label col-sm-4']);?>
            <div class=" col-sm-8">
                <?= isset($diagnosa_nama) ? $diagnosa_nama : '' ?>
                <?= Html::activeHiddenInput($modelReseptur, 'diagnosa_id') ?>
            </div>
        </div>
    </div>
</div>
<br>
<div class="row">
    <div class="col-lg-3">
        <div class="form-group required">
            <?=Html::label(Yii::t('fe', 'Dokter'), '', [
                'class' => 'control-label col-sm-4'
            ]);?>
            <div class=" col-sm-8">
                <?= Html::textInput(
                	'nama_dokter', 
                	isset($terapiobat_init['nama_dokter_cppt']) ? $terapiobat_init['nama_dokter_cppt'] : '', [
	                    'class' => 'form-control input-sm',
	                    'id' => 'nama_dokter_cppt',
	                    'readonly' => 'readonly',
	                ]) ?>
                <?= Html::activeHiddenInput($modelReseptur, 'pegawai_id') ?>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <!-- Tanggal resep -->
        <?= $form->field($modelReseptur, 'tglreseptur')->textInput([
            'class' => 'form-control input-sm date',
            'disabled' => 'disabled',
        ])->label(Yii::t('fe', 'Tanggal Resep')) ?>
    </div>
    <div class="col-lg-3">
        <?= $form->field($modelReseptur, 'ruangan_id')->widget(Select2::classname(), [
            'data' => $listDataApotek,
            'options' => [
                'id' => 'select_depo',
                'class' => 'form-control input-sm select2',
                'placeholder' => '-- Pilih Depo --',
                'value' => $defaultDepo,
                'disabled' => $isEditReseptur == true ? 'disabled' : false
            ],
        ])->label(Yii::t('fe', 'Depo Tujuan')) ?>
    </div>
    <div class="col-lg-3">
        <!-- Iter -->
        <?= $form->field($modelReseptur, 'iter')->textInput([
            'id' => 'reseptur_iter',
            'class' => 'form-control input-sm docoNumberOnly',
            'disabled' => $isEditReseptur == true ? 'disabled' : false,
        ])->label(Yii::t('fe', 'Iterasi')) ?>
    </div>
</div>
<?= Html::activeHiddenInput($modelReseptur, 'pasienadmisi_id') ?>
<?= Html::hiddenInput('ruangan_id', $modelReseptur->ruangan_id, ['readonly' => 'readonly']) ?>
<?php ActiveForm::end() ?>
<div class="row">
    <hr>
</div>
<div class="row">
    <!-- Panel non racikan -->
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
                'type' => ActiveForm::TYPE_HORIZONTAL,
                'enableClientValidation' => false,
                'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL],
                'action' => '/ranap/pemeriksaan-rawat-inap/reseptur?id='.DocoHelpers::encrypt($data_pasien['pendaftaran_id']).'&pasien_id='.DocoHelpers::encrypt($data_pasien['pasien_id'])
            ]) ?>
            <?= Html::activeHiddenInput($modelResepturDetailNonRacikan, 'cppt_id') ?>
            <div class="panel-body">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-4">
                        <?= $form->field($modelResepturDetailNonRacikan, 'obatalkes_id', [
                                'labelOptions' => ['class' => 'text-right']
                            ])->dropDownList([], [
                                'class' => 'form-control reseptur-reset newselect2 autoObat obatreseptur',
                                'id' => 'depdrop_reseptur_nr',
                                'prompt' => '-- Pilih Nama Obat --'
                            ]) ?>
                        </div>
                        <div class="col-md-4">
                            <?=$form->field($modelResepturDetailNonRacikan, 'hargasatuan_reseptur', ['labelOptions' => ['class' => 'text-right']])
                                ->textInput([
                                    'id' => 'harga_reseptur_nr',
                                    'class' => 'form-control input-sm field-harga', 
                                    'readonly' => 'readonly', 
                                ])->label(Yii::t('fe', 'Harga Per <p id="penyimpanan"></p>')); ?>
                        </div>
                        <?= Html::activeHiddenInput($modelResepturDetailNonRacikan, 'tmp_hargasatuan', ['id' => 'tmp_hargasatuan']); ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="text-right col-sm-4">Stok Tersedia per <p class="lb_stok"></p></label>
                                <div class="col-sm-8">
                                <b><span class="val_qty"></span></b>
                                </div>
                            </div>
                        </div>
                        <?= Html::activeHiddenInput($modelResepturDetailNonRacikan, 'stok_sisa', ['id' => 'stok_sisa_nr']); ?>
                        <?= Html::activeHiddenInput($modelResepturDetailNonRacikan, 'tmp_stok_sisa', ['id' => 'tmp_stok_sisa_nr']); ?>
                        <?= Html::activeHiddenInput($modelResepturDetailNonRacikan, 'stok_konversi', ['id' => 'stok_konversi_nr']); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-4">
                        <?= $form->field($modelResepturDetailNonRacikan, 'satuankecil_nama', [
                                'labelOptions' => ['class' => 'text-right']
                            ])->widget(DepDrop::classname(), [
                                'type' => DepDrop::TYPE_SELECT2,
                                'options' => [
                                    'id' => 'satuankecil_nama',
                                    'class' => 'form-control select2 input-sm'
                                ],
                                'pluginOptions' => [
                                    'depends' => ['depdrop_reseptur_nr'],
                                    'placeholder' => \Yii::t('fe', '-- Pilih Satuan --'),
                                    'url' => Url::to(['/ranap/pemeriksaan-rawat-inap/list-satuan-besar'])
                                ],
                                'pluginEvents' => [
                                    'depdrop:afterChange' => 'function(event, id, value, textStatus){
                                        var _response = $(\'#satuankecil_nama\').depdrop(\'getAjaxResults\');
                                        _group = {};
                                        $.each(_response.output, function (x,y) {
                                          _group[y.id] = y.konversi;
                                        });
                                    }'
                                ]
                            ])->label(Yii::t('fe', 'Satuan')); ?>
                            <?= Html::activeHiddenInput($modelResepturDetailNonRacikan, 'nilai_konversi', ['id' => 'nilai_konversi']); ?>
                            <?= Html::activeHiddenInput($modelResepturDetailNonRacikan, 'satuankecil_nama', ['id' => 'satuan_default']); ?>
                            <?= Html::activeHiddenInput($modelResepturDetailNonRacikan, 'satuankecil', ['id' => 'satuankecil']); ?>
                        </div>
                        <div class="col-md-4">
                            <?=$form->field($modelResepturDetailNonRacikan, 'jml_konversi', ['labelOptions' => ['class' => 'text-right']])
                                ->textInput([
                                    'id' => 'qty_nonracikan_id',
                                    'class' => 'form-control input-sm doco-decimal-wcomma',
                                    'maxlength' => '8',
                                ]); ?>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="text-right col-sm-4">Jumlah Konversi</label>
                                <div class="col-sm-8">
                                <b><span class="konversi"></span></b>
                                </div>
                            </div>
                        </div>
                        <?= Html::activeHiddenInput($modelResepturDetailNonRacikan, 'satuan_penyimpanan', ['id' => 'satuan_penyimpanan']); ?>
                        <?= Html::activeHiddenInput($modelResepturDetailNonRacikan, 'qty_reseptur', ['id' => 'jml_konversi']); ?>
                        <?= Html::activeHiddenInput($modelResepturDetailNonRacikan, 'qty_konversi', ['id' => 'qty_konversi']); ?>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-4">
                            <?= $form->field($modelResepturDetailNonRacikan, 'signa_reseptur', [
                                    'labelOptions' => ['class' => 'text-right']
                                ]) ?>
                            <?= Html::activeHiddenInput($modelResepturDetailNonRacikan, 'satuankecil_id', ['id' => 'satuan_id_reseptur_nr']); ?>
                            <?= Html::activeHiddenInput($modelResepturDetailNonRacikan, 'satuanbesar_id', ['id' => 'satuanbesar_id_reseptur_nr']); ?>
                        </div>
                        <div class="col-md-4">
                            <?=$form->field($modelResepturDetailNonRacikan, 'etiket', ['labelOptions' => ['class' => 'text-right']])
                                ->textarea([
                                    'id' => 'catatan_nr',
                                    'class' => 'form-control input-sm',
                                ]); ?>
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

    <!-- Panel racikan -->
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
                'type' => ActiveForm::TYPE_HORIZONTAL,
                'enableClientValidation' => false,
                'formConfig' => ['labelSpan' => 5, 'deviceSize' => ActiveForm::SIZE_SMALL],
                'action' => '/ranap/pemeriksaan-rawat-inap/reseptur?id='.DocoHelpers::encrypt($data_pasien['pendaftaran_id']).'&pasien_id='.DocoHelpers::encrypt($data_pasien['pasien_id'])
            ]) ?>
            <?= Html::activeHiddenInput($modelResepturDetailRacikan, 'cppt_id') ?>
            <div class="panel-body">
                <div class="col-md-12" id="section-racikan">
                    <div class="row">
                        <div class="col-md-4">
                            <?=$form->field($modelResepturDetailRacikan, 'rke', ['labelOptions' => ['class' => 'text-right']])
                            ->textInput([
                                'class' => 'form-control cek_r input-sm docoNumberOnly r-ke cek-racikan', 
                            ])->label(Yii::t('fe', 'R ke /')) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($modelResepturDetailRacikan, 'signa_reseptur', [
                                    'labelOptions' => ['class' => 'text-right']
                                ]) ?>
                            <?=Html::activeHiddenInput($modelResepturDetailRacikan, 'satuankecil_id[0]', ['id' => 'satuan_id_reseptur_r']); ?>
                            <?=Html::activeHiddenInput($modelResepturDetailRacikan, 'satuanbesar_id[0]', ['id' => 'satuanbesar_id_reseptur_r']); ?>
                        </div>
                        <div class="col-md-4">
                            <?=$form->field($modelResepturDetailRacikan, 'etiket', ['labelOptions' => ['class' => 'text-right']])
                                ->textarea([
                                    'id' => 'catatan_r',
                                    'class' => 'form-control input-sm',
                                ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <?= $form->field($modelResepturDetailRacikan, 'obatalkes_id[0]', [
                                'labelOptions' => ['class' => 'text-right']
                            ])->dropDownList([], [
                                'class' => 'form-control obatreseptur racikan_apend',
                                'prompt' => '-- Pilih Nama Obat --',
                                'id' => 'depdrop_reseptur_r'
                            ]) ?>
                            
                        </div>
                        <div class="col-md-4">
                            <?=$form->field($modelResepturDetailRacikan, 'hargasatuan_reseptur[0]', ['labelOptions' => ['class' => 'text-right']])
                                ->textInput([
                                    'id' => 'harga_reseptur_r',
                                    'class' => 'form-control input-sm field-harga', 
                                    'readonly' => 'readonly', 
                                ])->label(Yii::t('fe', 'Harga Per <p id="penyimpanan_r"></p>')); ?>
                        </div>
                        <?= Html::activeHiddenInput($modelResepturDetailRacikan, 'tmp_hargasatuan[0]', ['id' => 'tmp_hargasatuan_r']); ?>
                        <div class="col-md-4">
                            <div class="text-left">
                                <label class="text-right col-sm-4">Stok Tersedia per <p class="lb_stok_r"></p></label>
                                <div class="col-sm-8">
                                <b><span class="val_qty_r"></span></b>
                                </div>
                            </div>
                        </div>
                        <?= Html::activeHiddenInput($modelResepturDetailRacikan, 'stok_sisa[0]', ['id' => 'stok_sisa_r']); ?>
                        <?= Html::activeHiddenInput($modelResepturDetailRacikan, 'tmp_stok_sisa[0]', ['id' => 'tmp_stok_sisa_r']); ?>
                        <?= Html::activeHiddenInput($modelResepturDetailRacikan, 'stok_konversi[0]', ['id' => 'stok_konversi_r']); ?>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                        <?= $form->field($modelResepturDetailRacikan, 'satuankecil_nama[0]', [
                                'labelOptions' => ['class' => 'text-right']
                            ])->widget(DepDrop::classname(), [
                                'type' => DepDrop::TYPE_SELECT2,
                                'options' => [
                                    'id' => 'satuankecil_nama_r',
                                    'class' => 'form-control select2 satuan_append field-satuankecil'
                                ],
                                'pluginOptions' => [
                                    'depends' => ['depdrop_reseptur_r'],
                                    'placeholder' => \Yii::t('fe', '-- Pilih Satuan --'),
                                    'url' => Url::to(['/ranap/pemeriksaan-rawat-inap/list-satuan-besar'])
                                ],
                                'pluginEvents' => [
                                    'depdrop:afterChange' => 'function(event, id, value, textStatus){
                                        var _response = $(\'#satuankecil_nama_r\').depdrop(\'getAjaxResults\');
                                        _group = {};
                                        $.each(_response.output, function (x,y) {
                                          _group[y.id] = y.konversi;
                                        });
                                    }'
                                ]
                            ])->label(Yii::t('fe', 'Satuan')); ?>
                            <?= Html::activeHiddenInput($modelResepturDetailRacikan, 'nilai_konversi[0]', ['id' => 'nilai_konversi_r']); ?>
                            <?= Html::activeHiddenInput($modelResepturDetailRacikan, 'satuankecil_nama[0]', ['id' => 'satuan_default_r']); ?>
                            <?= Html::activeHiddenInput($modelResepturDetailRacikan, 'satuankecil[0]', ['id' => 'satuankecil_r']); ?>
                        </div>
                        <div class="col-md-4">
                            <?=$form->field($modelResepturDetailRacikan, 'jml_konversi[0]', ['labelOptions' => ['class' => 'text-right']])
                                ->textInput([
                                    'id' => 'qty_racikan_id',
                                    'class' => 'form-control input-sm qty-reseptur cek-racikan doco-decimal-wcomma',
                                    'maxlength' => '8',
                                ]); ?>
                        </div>
                        <div class="col-md-3">
                            <div class="text-left">
                                <label class="text-right col-sm-4">Jumlah Konversi</label>
                                <div class="col-sm-8">
                                <b><span class="konversi_r"></span></b>
                                </div>
                            </div>
                        </div>
                        <?= Html::activeHiddenInput($modelResepturDetailRacikan, 'satuan_penyimpanan[0]', ['id' => 'satuan_penyimpanan_r']); ?>
                        <?= Html::activeHiddenInput($modelResepturDetailRacikan, 'qty_reseptur[0]', ['id' => 'jml_konversi_r']); ?>
                        <?= Html::activeHiddenInput($modelResepturDetailRacikan, 'qty_konversi[0]', ['id' => 'qty_konversi_r']); ?>
                        <div class="col-md-1">
                            <?= Html::button('<i class="fa fa-plus"></i>', ['class' => 'btn btn-success', 'id' => 'btnAppendRacikan', 'onclick' => 'appendRacikan()']); ?>
                        </div>
                    </div>
                    <div class="row">
                    </div>
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
</div>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title"><?=Yii::t('fe', 'Tabel Reseptur')?></h5>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="tabel-reseptur" class="table table-striped table-hover datatable-basic dataTable" style="width:100%;">
                    <thead>
                        <tr class="bg-inverse">
                            <th>No</th>
                            <th><?=Yii::t('fe', 'Racikan / non racikan')?></th>
                            <th><?=Yii::t('fe', 'R ke-')?></th>
                            <th><?=Yii::t('fe', 'Nama obat')?></th>
                            <th><?=Yii::t('fe', 'Satuan')?></th>
                            <th><?=Yii::t('fe', 'Signa')?></th>
                            <th><?=Yii::t('fe', 'Stok Tersedia')?></th>
                            <th><?=Yii::t('fe', 'Qty')?></th>
                            <th><?=Yii::t('fe', 'Qty Konversi')?></th>
                            <th><?=Yii::t('fe', 'Harga satuan (Rp.)')?></th>
                            <th><?=Yii::t('fe', 'Jumlah harga (Rp.)')?></th>
                            <th><?=Yii::t('fe', 'Catatan')?></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th><?=Yii::t('fe', 'Aksi')?></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="panel-footer">
                <div class="col-md-6 text-left">
                    <?php if (isset($isEditReseptur) && $isEditReseptur == true): ?>
                        <?= Html::button('<b><i class="fa fa-pencil"></i></b> '.Yii::t('fe', 'Ubah'), [
                            'class' => 'btn btn-info btn-labeled btn-xs',
                            'id' => 'btn-save-reseptur',
                            'action' => '/ranap/pemeriksaan-rawat-inap/save-session-reseptur?id='.$encryptedPendaftaranId.'&cppt_id='.$cppt_id.'&update=true',
                            'onclick' => 'saveSessionReseptur()',
                        ]) ?>
                    <?php else: ?>
                        <?= Html::button('<b><i class="fa fa-floppy-o"></i></b> '.Yii::t('fe', 'Simpan'), [
                            'class' => 'btn btn-info btn-labeled btn-xs',
                            'id' => 'btn-save-reseptur',
                            'action' => '/ranap/pemeriksaan-rawat-inap/save-session-reseptur?id='.$encryptedPendaftaranId.'&cppt_id='.$cppt_id.'&update=false',
                            'onclick' => 'saveSessionReseptur()',
                        ]) ?>
                    <?php endif ?>
                </div>
                <div class="col-md-6 text-right">
                    <?php if (isset($isEditReseptur) && $isEditReseptur == true): ?>
                        <?= Html::button('<b><i class="fa fa-print"></i></b> '. Yii::t('fe', 'Cetak'), [
                            'class' => 'btn btn-success btn-labeled btn-xs', 
                            'id' => 'btn-cetak-reseptur',
                        ]) ?>
                    <?php endif ?>
                    <?php 
                    // echo Html::button('<b><i class="fa fa-refresh"></i></b> '. Yii::t('fe', 'Muat ulang'), [
                    //     'class' => 'btn btn-danger btn-labeled btn-xs', 
                    //     'id' => 'btn-reset-reseptur',
                    //     'data-url' => '/ranap/pemeriksaan-rawat-inap/reset-reseptur-session?id='.$encryptedPendaftaranId.'&cppt_id='.$cppt_id
                    // ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JS -->
<?php
$this->registerJs('
    // Global var
    var pendaftaran_id = "'.DocoHelpers::encrypt($data_pasien['pendaftaran_id']).'";
    var berat_badan = "'.$modelReseptur['berat_badan'].'";
    var tinggi_badan = "'.$modelReseptur['tinggi_badan'].'";
    var luas_tubuh = "'.$modelReseptur['luas_tubuh'].'";
    var isEditReseptur = "'.$isEditReseptur.'";
    var initObatAlkes = '.json_encode($initObatAlkes).';
    var cppt_id = "'.$cppt_id.'";
    var isUbah = "'.$isUbah.'";
    var tabel_reseptur = "";
    var tmp_isEdit = "'.$isEditReseptur.'";
    var ruangan = "'.$model['ruangan_id'].'";
    var pegawaId = "'.$model['pegawai_id'].'";
    var penjaminId = "'. $data_pasien['penjamin_id'] .'";
    var kelaspelayananId = "'. $data_pasien['kelaspelayanan_id'] .'";

    $(function(){
        if (tmp_isEdit){
            console.log(tmp_isEdit)
            $("#select_depo").trigger("change");
            $("#depdrop_reseptur_r").val(null).trigger("change");
        }
    });

    // Tabel reseptur
    var tabel_reseptur = $("#tabel-reseptur").docoTabel({
        "columnDefs": [{
            "searchable": false,
            "orderable": false,
            "targets": 0
        }],
        filter: false,
        sorting: [[1, "asc"]], 
        processing: true,
        serverSide: true,
        paging: false,
        ajax: baseUrl+"ranap/pemeriksaan-rawat-inap/get-data-reseptur-session?id="+pendaftaran_id+"&cppt_id="+cppt_id+"&ruangan_id="+$("#select_depo").val(),
        columns: [
            {
                title: "No",
                data: "no",
                searchable: false,
                orderable: false, 
            },
            {title: "Racikan/ non racikan", data: "nama_racikan"},
            {title: "R ke-", data: "rke"},
            {title: "Nama obat", data: "obatalkes_nama"},
            {title: "Satuan", data: "satuankecil_nama"},
            {title: "Signa", data: "signa_edit"},
            {title: "Stok Tersedia", data: "stok_konversi", className:"stok_tersedia_konv hidden"},
            {title: "Qty", data: "qty_edit"},
            {title: "Qty Konversi", data: "qty_konv", className:"qty_konv"},
            {title: "Harga satuan (Rp.)", data: "hargasatuan_reseptur"},
            {title: "Jumlah harga (Rp.)", data: "jumlah_harga"},
            {title: "Catatan", data: "etiket"},
            {title: "", data: "stok_tersedia", className:"stok_tersedia hidden"},
            {title: "", data: "nilai_konversi", className:"val_konv hidden"},
            {title: "", data: "satuan_penyimpanan", className:"satuan_konv hidden"},
            {title: "", data: "obatalkes_id", "visible" : false},
            {
                title: "Aksi",
                data: "aksi",
                searchable: false,
                orderable: false, 
            },
        ],
        rowCallback: function (row, data) {
            var qty_element = $(row).find(".qty");

            $("td:eq(6)", row).addClass("stok_tersedia_k"+data.obatalkes_id);
            $("td:eq(8)", row).addClass("qty_konv_"+data.obatalkes_id);
            $("td:eq(12)", row).addClass("stok_tersedia_"+data.obatalkes_id);

            qty_element.on("focusin", function () {
                $(this).data("last-value", $(this).val());
            }).on("change", function () {
                var val_konv = parseFloat($(row).find(".val_konv").text());
                var qty = parseFloat($(this).val());
                var qty_k = qty * val_konv;
                var last_qty = parseFloat($(this).data("last-value"));
                var last_qty_k = last_qty * val_konv;
                var stok_tersedia = parseFloat($(row).find(".stok_tersedia").text());
                var stok_tersedia_konv = parseFloat($(row).find(".stok_tersedia_konv").text());
                var satuan_konv = $(row).find(".satuan_konv").text();
                var last_stok_tersedia = stok_tersedia + last_qty;
                var last_stok_tersedia_konv = stok_tersedia_konv + last_qty;
                var harga_satuan = $(row).find(".harga_satuan").text();
                harga_satuan = docoHelper.convertToAngka(harga_satuan);

                resetAll(false);

                if (last_stok_tersedia_konv >= qty) {
                    $.ajax({
                        url: "/ranap/pemeriksaan-rawat-inap/set-qty-reseptur?id="+pendaftaran_id+"&cppt_id="+cppt_id+"&obatalkes_id="+data.obatalkes_id+"&jenis_racikan="+data.jenis_racikan+"&qty="+$(this).val(),
                        success: function(response) {
                            
                            var sisa_stok = stok_tersedia + last_qty - qty;
                            var sisa_stok_k = stok_tersedia + last_qty_k - qty_k;
                            var tmp_stok_js = sisa_stok_k / val_konv;

                            $(".stok_tersedia_k"+data.obatalkes_id).text(tmp_stok_js.toFixed(2));
                            $(".stok_tersedia_"+data.obatalkes_id).text(sisa_stok_k.toFixed(2));
                            $(".qty_konv_"+data.obatalkes_id).text(qty_k.toFixed(2) + " " + satuan_konv);
                            $(row).find(".jumlah_harga").text(docoHelper.numberFormat((harga_satuan * qty), 2, ",", "."));
                        }
                    });
                } else {
                    docoNotification("error", "Tidak bisa ubah qty!", "Qty tidak boleh lebih dari stok tersedia!");

                    $(this).val(last_qty);
                }
            });
        }
    });
', View::POS_END) ?>

<?php // File
// $this->registerJs('
// // Global var
// ', View::POS_END);
$this->registerJs($this->render('js/terapi_reseptur.js'), View::POS_END);
?>