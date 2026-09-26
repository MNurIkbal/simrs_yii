<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2019-01-22 14:09:24
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-02-08 10:43:40
 */

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\helpers\ArrayHelper;

?>
<div class="panel panel-default hidden" id="panel-jenazah">
    <div class="panel-heading">
        <div id="head_kesimpulan_jenazah" class="checkbox2 head_kesimpulan_jenazah form-group">
            <label class="control-label col-md-1">
                <?=Html::checkbox('checked_kesimpulan_jenazah',false,['class'=>'styled','id'=>'checked_kesimpulan_jenazah'])?>
                <span class="cr"><i class="cr-icon fa fa-check"></i></span>
            </label>
            <div class="col-md-11">
                <h5 class="panel-title"><?=Yii::t('fe','Pelayanan Jenazah')?></h5>
            </div>
        </div>
    </div>
    <div id="col_kesimpulan_jenazah" class="panel-collapse collapse">
        <div class="panel-body">
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#kondisipasien" role="tab" data-toggle="tab">Kondisi Pasien & Penanggung Jawab</a></li>
                <li role="presentation"><a href="#list-order" role="tab" data-toggle="tab">Obat & Tindakan</a></li>
                <li role="presentation"><a href="#list-linen" role="tab" data-toggle="tab">Linen & Alat yang masih melekat</a></li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="kondisipasien">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="control-label col-md-3"><?=\Yii::t('fe', 'Kondisi Pasien')?></label>
                                <div class="col-md-9">
                                    <?=Html::activeTextArea($modelJenazah, 'kondisi', ['class'=> 'form-control', 'rows' => 3])?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group required">
                                <label class="control-label col-md-4"><?=\Yii::t('fe', 'Nama Penanggung Jawab')?></label>
                                <div class="col-md-5">
                                    <?=Html::activeTextInput($modelJenazah, 'nama_pj', ['class'=> 'form-control'])?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group required">
                                <label class="control-label col-md-2"><?=\Yii::t('fe', 'Jenis Kelamin')?></label>
                                <div class="col-md-5">
                                    <?=Html::activeDropdownlist($modelJenazah,'jeniskelamin_id', $listJk, ['class'=> 'form-control select2', 'prompt' => \Yii::t('fe', '-- Pilih --')])?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group required">
                                <label class="control-label col-md-4"><?=\Yii::t('fe', 'Umur (Thn)')?></label>
                                <div class="col-md-5">
                                    <?=Html::activeTextInput($modelJenazah, 'umur', ['class'=> 'form-control'])?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group required">
                                <label class="control-label col-md-2"><?=\Yii::t('fe', 'No. Telp / Hp')?></label>
                                <div class="col-md-5">
                                    <?=Html::activeTextInput($modelJenazah, 'no_kontak', ['class'=> 'form-control'])?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group required">
                                <label class="control-label col-md-4"><?=\Yii::t('fe', 'Hubungan Keluarga')?></label>
                                <div class="col-md-5">
                                    <?=Html::activeDropdownlist($modelJenazah,'hubungan_keluarga', $listHubungan, ['class'=> 'form-control select2', 'prompt' => \Yii::t('fe', '-- Pilih --')])?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="control-label col-md-3"><?=\Yii::t('fe', 'Alamat')?></label>
                                <div class="col-md-9">
                                    <?=Html::activeTextArea($modelJenazah, 'alamat', ['class'=> 'form-control', 'rows' => 3])?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="list-order">
                    <div class="row" id="row-tindakan">
                        <div class="col-md-12">
                            <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group required">
                                            <label class="control-label col-md-3"><?=\Yii::t('fe', 'Tindakan')?></label>
                                            <div class="col-md-8">
                                                <?=Html::dropdownlist('daftartindakan_id', '', [], ['class'=> 'form-control select2', 'id' => 'tindakan-select'])?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group required">
                                            <label class="control-label col-md-3"><?=\Yii::t('fe', 'Qty')?></label>
                                            <div class="col-md-6">
                                                <?=Html::textInput('qty', '', ['class'=> 'tindakan-qty form-control doco-number'])?>
                                            </div>
                                            <div class="col-md-1">
                                                <button class="btn btn-success btn-sm" id="add-tindakan"><i class="fa fa-plus fa-sm"></i></button>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                            <div class="row">
                                <table class="table table-striped table-hover datatable-basic dataTable" style="width:100%;" id="tabel-tindakan">
                                    <thead>
                                        <tr class="bg-inverse">
                                            <th><?=Yii::t('fe', 'No')?></th>
                                            <th><?=Yii::t('fe', 'Nama Tindakan')?></th>
                                            <th><?=Yii::t('fe', 'Qty')?></th>
                                            <th><?=Yii::t('fe', 'Harga')?></th>
                                            <th><?=Yii::t('fe', 'Aksi')?></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div><br><hr>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row" id="row-obat">
                                    <div class="col-md-4">
                                        <div class="form-group required">
                                            <label class="control-label col-md-2"><?=\Yii::t('fe', 'Obat')?></label>
                                            <div class="col-md-8">
                                                <?=Html::dropdownlist('obatalkes_id', '', [], ['class'=> 'form-control select2', 'id' => 'obatalkes-select'])?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group required">
                                            <label class="control-label col-md-3"><?=\Yii::t('fe', 'Qty')?></label>
                                            <div class="col-md-6">
                                                <?=Html::textInput('qty', '', ['class'=> 'form-control doco-number qty-obat'])?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?=\Yii::t('fe', 'Satuan')?></label>
                                            <div class="col-md-6">
                                                <?=Html::textInput('satuankecil_nama', '', ['class' => 'form-control satuankecil-nama', 'readonly' => true])?>
                                            </div>
                                            <div class="col-md-1">
                                                <button id="add-obatalkes" class="btn btn-success btn-sm"><i class="fa fa-plus fa-sm"></i></button>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                            <div class="row">
                                <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="tabel-obat" width="100%">
                                    <thead>
                                        <tr class="bg-inverse">
                                            <th><?=Yii::t('fe', 'No')?></th>
                                            <th><?=Yii::t('fe', 'Nama Obat Alkes')?></th>
                                            <th><?=Yii::t('fe', 'Qty')?></th>
                                            <th><?=Yii::t('fe', 'Satuan')?></th>
                                            <th><?=Yii::t('fe', 'Harga')?></th>
                                            <th><?=Yii::t('fe', 'Aksi')?></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="list-linen">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row" id="row-linen">
                                    <div class="col-md-4">
                                        <div class="form-group required">
                                            <label class="control-label col-md-2"><?=\Yii::t('fe', 'Linen')?></label>
                                            <div class="col-md-8">
                                                <?=Html::dropdownlist('barang_id', '', [], ['class'=> 'form-control select2', 'id' => 'linen-select'])?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group required">
                                            <label class="control-label col-md-3"><?=\Yii::t('fe', 'Qty')?></label>
                                            <div class="col-md-6">
                                                <?=Html::hiddenInput('barang_nama', '', ['class' => 'barang-nama'])?>
                                                <?=Html::textInput('qty', '', ['class'=> 'form-control doco-number barang-qty'])?>
                                            </div>
                                            <div class="col-md-1">
                                                <button class="btn btn-success btn-sm" id="add-linen"><i class="fa fa-plus fa-sm"></i></button>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                            <div class="row">
                                <table class="table table-striped table-hover datatable-basic dataTable" style="width:100%;" id="tabel-linen">
                                    <thead>
                                        <tr class="bg-inverse">
                                            <th><?=Yii::t('fe', 'No')?></th>
                                            <th><?=Yii::t('fe', 'Nama Linen')?></th>
                                            <th><?=Yii::t('fe', 'Qty')?></th>
                                            <th><?=Yii::t('fe', 'Aksi')?></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div><br><hr>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row" id="row-alat">
                                    <div class="col-md-4">
                                        <div class="form-group required">
                                            <label class="control-label col-md-5"><?=\Yii::t('fe', 'Alat yang masih terpasang')?></label>
                                            <div class="col-md-7">
                                                <?=Html::dropdownlist('obatalkes_id', '', [], ['class'=> 'form-control select2', 'id' => 'alat-select'])?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group required">
                                            <label class="control-label col-md-3"><?=\Yii::t('fe', 'Qty')?></label>
                                            <div class="col-md-6">
                                                <?=Html::hiddenInput('obatalkes_nama', '',['class'=>'alat-nama'])?>
                                                <?=Html::textInput('qty', '', ['class'=> 'form-control doco-number alat-qty'])?>
                                            </div>
                                            <div class="col-md-1">
                                                <button class="btn btn-success btn-sm" id="add-alat"><i class="fa fa-plus fa-sm"></i></button>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                            <div class="row">
                                <table class="table table-striped table-hover datatable-basic dataTable" style="width:100%;" id="tabel-alat">
                                    <thead>
                                        <tr class="bg-inverse">
                                            <th><?=Yii::t('fe', 'No')?></th>
                                            <th><?=Yii::t('fe', 'Nama Alat')?></th>
                                            <th><?=Yii::t('fe', 'Qty')?></th>
                                            <th><?=Yii::t('fe', 'Aksi')?></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div><br><hr>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
$this->registerJs("
    var table_linen;
    var table_obat_kesimpulan;
    var table_tindakan;
    var table_alat;
    var pendaftaranId = '".$id."';
    ".$this->render('_js/formjenazah.js'), View::POS_END, 'js');
?>
