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
        <div class="row panel" id="form-racikan-detail">
            <a id="info-heading" data-toggle="collapse" href="#racikan-detail-collapse" role="button" aria-expanded="true" aria-controls="racikan-detail-collapse" class="">
                <div class="panel-heading flex-container">
                    <h5 class="panel-title"><?= Yii::t('fe', 'Racikan') ?></h5>
                    <ul class="icons-list" style="padding-top: 3px">
                        <li><i id="chevron" class="fa fa-chevron-up"></i></li>
                    </ul>
                </div>
            </a>
            <div class="panel-body multi-collapse label-information collapse" id="racikan-detail-collapse" aria-expanded="true" style="">
                  <div class="row">
                    <div class="col-md-5" style="border-right: 1px solid #f0f0f0">
                        <div class="row">
                            <div class="col-md-2">
                                <label for="" class=" required-reseptur">R</label>
                                <div class="form-group">
                                    <?=Html::textInput('rke', null, [
                                        'class' => 'form-control doco-number',
                                        'id' => 'rke',
                                        'placeholder' => 'R'
                                    ])?>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <label for="" class=" required-reseptur">Signa</label>
                                <div class="form-group select2-md">
                                    <?=Html::dropdownList('signa_id', null, [], [
                                        'prompt' => 'Pilih',
                                        'class' => 'form-control select2-selffocus docoSelect2SignaFormatOnly',
                                        'id' => 'r_signa_id'
                                    ])?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                          <div class="col-md-3 select2-md">
                              <label for="" class="required-reseptur">Hari</label>
                              <div class="form-group">
                                  <?=Html::dropdownList('r_hari', null,  array_combine(range(1, 31), range(1, 31)), [
                                      'class' => 'form-control doco-decimal',
                                      'id' => 'r_hari',
                                      'placeholder' => 'Qty',
                                      'readonly' => true,
                                      'text' => 'Pilih Hari'
                                  ])?>
                              </div>
                          </div>
                          <div class="col-md-3">
                              <label for="" class="required-reseptur">Qty Racikan</label>
                              <div class="form-group">
                                  <?=Html::textInput('qty_racikan', null, [
                                      'class' => 'form-control select2-selffocus',
                                      'id' => 'qty_racikan',
                                      'placeholder' => 'Qty',
                                      'readonly' => true,
                                  ])?>
                              </div>
                          </div>
                          <div class="col-md-3 select2-md">
                              <label for="" class="required-reseptur">Satuan Racikan</label>
                              <div class="form-group">
                                  <?=Html::dropDownList('satuan_racikan', null, [], [
                                      'class' => 'form-control select2-selffocus',
                                      'id' => 'satuan_racikan',
                                  ])?>
                              </div>
                          </div>
                          <div class="col-md-3 is-kronis-racikan">
                              <label for="is_kronis_racikan">Obat Kronis</label>
                              <div class="form-group">
                                  <?=Html::checkbox('is_kronis', null, [
                                      'id' => 'is_kronis_racikan',
                                  ], [
                                      'class' => 'form-control'
                                  ])?>
                              </div>
                          </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label for="">Nama Racikan</label>
                                <div class="form-group">
                                    <?=Html::textInput('racikan_nama', null, [
                                        'class' => 'form-control',
                                        'id' => 'racikan_nama',
                                        'placeholder' => 'Nama Racikan'
                                    ])?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label>Catatan Racikan</label>
                                <div class="form-group">
                                    <?=Html::textarea('catatan', null, [
                                        'class' => 'form-control',
                                        'rows' => 3,
                                        'placeholder' => 'Catatan Racikan',
                                        'id' => 'r_catatan'
                                    ])?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="row">
                            <div class="col-md-4 select2-md">
                                <div class="form-group">
                                    <label for="" class=" required-reseptur">Obat Alkes</label>
                                    <?=Html::dropDownList('r_obatalkes_id', null, [], [
                                        'prompt' => 'Pilih Obat Alkes',
                                        'class' => 'form-control select2-selffocus',
                                        'id' => 'r_obatalkes_id'
                                    ])?>
                                </div>
                            </div>
                            <div class="col-md-3" id="div_stok_tersedia_racikan">
                                <div class="row">
                                    <div class="col-md-5">
                                        <p>
                                            Stok Tersedia
                                        </p>
                                    </div>
                                    <div class="col-md-7">
                                        <strong><span id="text_stok_tersedia_racikan"></span></strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="">Kebutuhan</label>
                                    <?=Html::textInput(null, null, [
                                        'class' => 'form-control doco-decimal',
                                        'placeholder' => 'Kebutuhan',
                                        'id' => 'kebutuhan',
                                        'disabled' => 'disabled',
                                    ])?>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="">
                                        Qty <span class="text-danger">*</span>
                                        &nbsp;&nbsp;&nbsp;
                                        <i class="fa fa-info-circle" data-placement="right" data-toggle="tooltip" data-html="true" title="" aria-hidden="true" data-original-title="Isi terlebih dahulu QTY <br> Racikan untuk mengisi field ini " aria-describedby="tooltip476297"></i>
                                    </label>
                                    <?=Html::textInput('r_qty', null, [
                                        'class' => 'form-control doco-decimal',
                                        'placeholder' => 'Qty',
                                        'id' => 'r_qty',
                                        'disabled' => 'disabled',
                                    ])?>
                                </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="">Satuan</label>
                                    <?=Html::textInput('r_satuan_text', null, [
                                        'class' => 'form-control',
                                        'id' => 'r_satuan_text',
                                        'readonly' => true
                                    ])?>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <br>
                                <button type="button" class="btn btn-labeled btn-info btn-xs" id="btn-add-detail-racikan" style="margin-top: 5px">
                                    <b>
                                        <i class="fa fa-plus"></i>
                                    </b>
                                    Tambah
                                </button>
                            </div>
                            <?=Html::hiddenInput('r_stok_tersedia', null, [
                                'id' => 'r_stok_tersedia'
                            ])?>
                            <?=Html::hiddenInput('r_satuan_id', null, [
                                'id' => 'r_satuan_id'
                            ])?>
                            <?=Html::hiddenInput('r_stok_sisa', null, [
                                'id' => 'r_stok_sisa'
                            ])?>
                            <?=Html::hiddenInput('r_satuandefault_id', null, [
                                'id' => 'r_satuandefault_id'
                            ])?>
                            <?=Html::hiddenInput('r_satuankecil_id', null, [
                                'id' => 'r_satuankecil_id'
                            ])?>
                            <?=Html::hiddenInput('r_harga', null, [
                                'id' => 'r_harga'
                            ])?>
                            <?=Html::hiddenInput('r_harga_konversi', null, [
                                'id' => 'r_harga_konversi'
                            ])?>
                            <?=Html::hiddenInput('r_harganetto_reseptur', null, [
                                'id' => 'r_harganetto_reseptur'
                            ])?>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <div style="height: 50px; overflow: visible">
                                    <table class="table table-bordered" id="racikan-tbl">
                                        <thead>
                                            <tr class="bg-inverse">
                                                <th>Nama Obat Alkes</th>
                                                <th>Qty</th>
                                                <th>Stok Tersedia</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="3" class="no-row-data text-center">Belum ada data obat racikan yang ditambahkan</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button class="btn btn-sm btn-info btn-labeled btn-xs" disabled type="button" id="btn-add-racikan">
                            <b>
                                <i class="fa fa-plus"></i>
                            </b>
                            Tambah
                        </button>
                    </div>
                </div>
            </div>
        </div>

<?php
$this->registerJs($this->render('js/_racikan_detail.js'), View::POS_END);
?>
