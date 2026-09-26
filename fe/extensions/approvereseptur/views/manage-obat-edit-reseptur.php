<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;

?>

 <style type="text/css">
    textarea.form-control {
        min-height: initial !important;
    }

    .text-right{
        text-align: right;
    }

    #manage-obat-edit-reseptur tr td, #manage-obat-edit-reseptur tr th{
        padding: 10px
    }

    #manage-obat-edit-reseptur tr td .btn-xs, .temp-racikan tr td .btn-xs{
        min-width: initial;
        min-height: initial;
        padding: 5px 10px !important;
    }

    tr.out-stock.resep{
        color: red !important;
        font-weight: bold;
    }

    tr.strikeout td:before {
        content: " ";
        position: absolute;
        top: 50%;
        left: 0;
        border-bottom: 1px solid #111;
        width: 100%;
    }

    .temp-racikan tr td .btn-xs {
        padding: 1px 2px !important;
    }

    .form-informasi .form-group{
        margin-bottom: 6px !important;
    }

    .form-informasi p.form-info {
        padding-top: 5px;
        padding-bottom: 10px;
        padding-left: 3px;
    }

    p.form-info {
        padding: 16px 0 0px 6px;
        margin-bottom: initial;
        font-weight: bold;
    }

    .row.small-gutter {
      margin-right: 2px;
      margin-left: 2px;
    }
    .row.small-gutter > [class*='col-'] {
      padding-right: 2px;
      padding-left: 2px;
    }

    table.temp-racikan, table.temp-racikan th {
        font-size: 10px !important;
    }
    table.temp-racikan th, table.temp-racikan td {
        padding: 4px 5px !important;
    }

    #form-racikan hr{
        margin-top: 10px;
        margin-bottom: 10px;
    }

    .biayaAdmin {
        margin-bottom: 7px;
    }
</style>

<!-- Resep Obat -->
<div class="panel panel-default" style="margin: 10px">
    <div class="panel-heading">
        <h5 class="panel-title"><?= Yii::t('fe', 'Resep Obat') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h5>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>
    </div>

    <div class="panel-body" id="resep_obat_body" style="margin: 10px 0;">
        <div id="form-header">
            <?php if(count($data_racikan) > 0) { ?>
                <?= $this->render('../assets/racikan-freetext', [
                    'data_racikan' => $data_racikan
                ]) ?>
            <?php } ?>

            <div class="form-group">
                <label class="control-label">Jenis Racikan</label> <br>
                <div class="radio-inline">
                    <label>
                        <input type="radio" value="racikan" name="jenis_racikan">
                        Racikan
                    </label>
                </div>
                <div class="radio-inline">
                    <label>
                        <input type="radio" value="nonracikan" name="jenis_racikan">
                        Non Racikan
                    </label>
                </div>
            </div>
        </div>

        <!-- Form Racikan -->
        <form id="form-racikan" style="display: none;">
            <?= Html::hiddenInput('obatalkes_id', ''); ?>       <?= Html::hiddenInput('obat_nama', ''); ?>
            <?= Html::hiddenInput('stok', ''); ?>               <?= Html::hiddenInput('harga', ''); ?>
            <?= Html::hiddenInput('ppn', ''); ?>                <?= Html::hiddenInput('satuankecil_id', ''); ?>
            <?= Html::hiddenInput('posisiNo', ''); ?>           <?= Html::hiddenInput('type', 3); //need condition if rs/pasien ?>
            <?= Html::hiddenInput('harganetto', ''); ?>         <?= Html::hiddenInput('jmlmargin', ''); ?>
            <?= Html::hiddenInput('jmlppn', ''); ?>             <?= Html::hiddenInput('jmldiscount', ''); ?>
            <?= Html::hiddenInput('persenppn', ''); ?>          <?= Html::hiddenInput('persenmargin', ''); ?>
            <?= Html::hiddenInput('persendiscount', ''); ?>     <?= Html::hiddenInput('hargajual', ''); ?>
            <?= Html::hiddenInput('signa_nama', ''); ?>         <?= Html::hiddenInput('id', ''); ?>
            <?= Html::hiddenInput('nilai_konversi', ''); ?>

            <?= Html::hiddenInput('satuaninput_id', null) ?>    <?= Html::hiddenInput('satuan_input',null) ?>
            <?= Html::hiddenInput('satuankonversi_id',null) ?>  <?= Html::hiddenInput('satuan_konversi',null) ?>
            <?= Html::hiddenInput('harga_kecil',null) ?>

            <div class="row" style="margin-top: 15px;">
                <div class="col-md-6" style="border-right: 2px solid rgb(221, 221, 221); min-height: 200px;">
                    <div class="row">
                        <!-- R ke Racikan -->
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label mandatory"><?= Yii::t('fe', 'R ke') ?> <span class="text-danger">*</span></label>
                                <input type="text" class="form-control r_ke" name="r_ke">
                            </div>
                        </div>

                        <!-- Signa Racikan -->
                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="control-label mandatory">Signa <span class="text-danger">*</span></label>
                                <?php

                                echo Select2::widget([
                                    'name' => 'signa',
                                    'data' => $list_signa,
                                    'options' => [
                                        'placeholder' => '— Pilih —',
                                        'id' => 'signa_racikan',
                                        'class' => 'form-control'
                                    ],
                                    'pluginOptions' => [
                                        'tags' => true,
                                        'tokenSeparators' => [',', '_'],
                                        'maximumInputLength' => 50
                                    ],
                                ]);

                                // Html::dropDownList(
                                //     'signa',
                                //     null,
                                //     $list_signa,
                                //     [
                                //         'class'     => 'form-control select2',
                                //         'prompt'    => '— Pilih —',
                                //         'id'        => 'signa_racikan'
                                //     ]
                                // );

                                ?>
                                <?= Html::hiddenInput('signa_hidden', null) ?>
                            </div>
                        </div>

                        <!-- Catatan Racikan -->
                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="control-label">Catatan</label>
                                <input type="text" class="form-control catatan" name="catatan">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <!-- Obat Alkes Racikan -->
                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="control-label"><?= Yii::t('fe', 'Nama Obat Alkes') ?><span class="text-danger">*</span></label>
                                <?= Html::activeDropDownList(
                                    $model,
                                    'obatalkes',
                                    $list_obat_ruangan,
                                    [
                                        // 'class'  => 'select2 select_obat',
                                        // 'id' => 'select_obat_racikan',
                                        'class'=>'select2',
                                        'id'=>'select_list_obat_racikan',
                                        'prompt' => Yii::t('fe', '-- Pilih --'),
                                        'options' => $list_obat_ruangan_options
                                    ]
                                )
                                ?>
                            </div>
                        </div>

                        <!-- Satuan Racikan -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label"><?= Yii::t('fe', 'Satuan') ?><span class="text-danger">*</span></label>
                                <?php
                                   echo DepDrop::widget(
                                       [
                                           'name'=>'satuan',
                                           'options'=>[
                                               'id'    =>'satuan_racikan',
                                               'class' =>'select2 satuan_racikan',
                                           ],
                                           'pluginOptions'=>[
                                               'depends'     =>['select_list_obat_racikan'],
                                               'class'       => 'satuan_racikan',
                                               'placeholder' =>\Yii::t('fe', '--Pilih--'),
                                               'url'         =>Url::to(['/apotek/transaksi-resep/dep-list-ampuls'])
                                           ],
                                           'pluginEvents'=> [
                                                'depdrop:afterChange'=>'function(event, id, value, textStatus) {
                                                    var _response = $(\'#satuan_racikan\').depdrop(\'getAjaxResults\');
                                                    _group = {};
                                                    $.each(_response.output, function (x,y) {
                                                        _group[y.id] = y.konversi;
                                                        if (y.konversi == 1) {
                                                            $("input[name=\'satuankonversi_id\']").val(y.id);
                                                            $("input[name=\'satuan_konversi\']").val(y.name);
                                                        }
                                                    });
                                                    $("#satuan_racikan").trigger("change");

                                                    var _nilai_konversi = $("#nilai_konversi").val();
                                                    var _stok           = $(".stok").val();
                                                    var _koversi_stok   = parseFloat(_stok)/parseFloat(_nilai_konversi);
                                                    _koversi_stok = _koversi_stok.toFixed(2);
                                                    $(".qty_tersedia").val(docoHelper.convertToRupiah(_koversi_stok));

                                                    var _hargajual      = $(".hargajual").val();
                                                    var _konversi_harga = _hargajual*_nilai_konversi;
                                                    _konversi_harga = _konversi_harga.toFixed(2);
                                                    $(".harga_konversi").val(docoHelper.convertToRupiah(_konversi_harga));

                                                    var _val_nama = $("#satuan_racikan").find("option:selected").text();
                                                    var _val_value = $("#satuan_racikan").val();
                                                    if ( _val_nama == "--Pilih--"){
                                                        _val_nama = "";
                                                        _val_value = "";
                                                    };
                                                    $(".satuan_kecil_text").html(_val_nama);
                                                    $("#satuaninput_id").val(_val_value);
                                                    $("#satuan_input").val(_val_nama);
                                                    $("#harga_kecil").val(_hargajual);
                                                 }',
                                           ]
                                       ]
                                   );
                                   ?>
                            </div>
                        </div>

                        <!-- Harga Racikan -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"><?= Yii::t('fe', 'Harga') ?> <span class="satuan"></span></label>
                                <?= Html::textInput('harga_konversi',null,[
                                        'class'       => 'form-control',
                                        // 'id'          => '',
                                        'readonly'    => true,
                                        'placeholder' => '0'
                                    ]);
                                ?>
                            </div>
                        </div>

                        <!-- Qty Input Racikan -->
                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="control-label"><?= Yii::t('fe', 'Qty') ?> <span class="text-danger">*</span></label>
                                <?= Html::textInput('qty',null,[
                                        'class'       => 'form-control qty',
                                        'placeholder' => 'Qty',
                                    ]);
                                ?>
                            </div>
                        </div>

                        <!-- Stok Racikan -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label"><?= Yii::t('fe', 'Stok') ?> &nbsp;<span class="stok_racikan_text"></span></label>
                                <p class="form-info">
                                    <span class="qty_tersedia">0</span>
                                </p>
                            </div>
                        </div>

                        <!-- Qty Konversi Racikan -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label"><?= Yii::t('fe', 'Qty Konversi') ?> <span class="satuan_kecil_racikan_text"></span></label>
                                <?= Html::textInput('qty_konversi',null,[
                                        'class'       => 'form-control qty_konversi',
                                        'id'          => 'qty_konversi_racikan',
                                        'placeholder' => '0',
                                        'readonly'    => true
                                    ]);
                                ?>
                            </div>
                        </div>
                    </div>

                    <!-- Btn Tambah ke temp racikan -->
                    <div class="row" style="margin-top: 10px">
                        <div class="col-md-12 text-right">
                            <button class="btn btn-danger btn-sm btn-ulang"><i class="fa fa-times"></i> Ulang</button>
                            <button type="button" class="btn btn-sm btn-success" id="btn-racikan"><i class="fa fa-plus"></i> Tambah ke Racikan</button>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <p><b>Table Obat Racikan ke - <span id="rke_display"></span></b></p>
                    <table class="table datatable-basic table-striped table-hover dataTable no-footer temp-racikan" style="margin: 10px 0">
                        <thead>
                            <tr class="bg-inverse">
                                <th>No</th>
                                <th>Nama Obat Alkes</th>
                                <th>Harga (Rp.)</th>
                                <th>Qty</th>
                                <th>Satuan</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="list-racikan-temp">
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada Data</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- btn simpan racikan -->
            <div class="row" style="margin-bottom: 10px">
                <div class="col-md-3 col-md-offset-9 text-right">
                    <button class="btn btn-success btn-sm" id="btn-tambah-racikan"><i class="fa fa-plus"></i> Tambahkan Racikan</button>
                </div>
            </div>
        </form>

        <!-- Form Non Racikan -->
        <?php
        $form = ActiveForm::begin([
            'id'        => 'form-obat',
            'options'   => ['class' => 'form-nonracikan', 'style' => 'display: none'],

        ]);
        ?>
            <div class="row" style="margin-top: 5px">
                <!-- Obat Alkes -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label"><?= Yii::t('fe', 'Nama Obat Alkes') ?> <span class="text-danger">*</span></label>
                        <?= Html::activeDropDownList(
                            $model,
                            'obatalkes',
                            $list_obat_ruangan,
                            [
                                // 'class'  => 'select2 autoObat',
                                // 'id'     => 'id_auto_obat',
                                'class' => 'select2',
                                'id' => 'select_list_obat_nonracikan',
                                'prompt' => Yii::t('fe', '-- Pilih --'),
                                'options' => $list_obat_ruangan_options
                            ]
                        )
                        ?>

                        <?= Html::hiddenInput('obatalkes_id', '', ['class' => 'id_obat']); ?>
                        <?= Html::hiddenInput('obat_nama', '', ['class' => 'obat_nama']); ?>
                        <?= Html::hiddenInput('stok', '', ['class' => 'stok']); ?>
                        <?= Html::hiddenInput('harga', '', ['class' => 'harga']); ?>
                        <?= Html::hiddenInput('ppn', '', ['class' => 'ppn']); ?>
                        <?= Html::hiddenInput('satuankecil_id', '', ['class' => 'satuankecil_id']); ?>
                        <?= Html::hiddenInput('posisiNo', '', ['class' => 'posisi']); ?>
                        <?= Html::hiddenInput('type', 5, ['class' => 'type']); //need condition ?>
                        <?= Html::hiddenInput('harganetto', '', ['class' => 'harganetto']); ?>
                        <?= Html::hiddenInput('jmlmargin', '', ['class' => 'hn_margin']); ?>
                        <?= Html::hiddenInput('jmlppn', '', ['class' => 'hn_ppn']); ?>
                        <?= Html::hiddenInput('jmldiscount', '', ['class' => 'hn_diskon']); ?>
                        <?= Html::hiddenInput('persenppn', '', ['class' => 'persenppn']); ?>
                        <?= Html::hiddenInput('persenmargin', '', ['class' => 'persenmargin']); ?>
                        <?= Html::hiddenInput('persendiscount', '', ['class' => 'persendiscount']); ?>
                        <?= Html::hiddenInput('hargajual', '', ['class' => 'hargajual']); ?>
                        <?= Html::hiddenInput('signa_nama', '', ['class' => 'signanama']); ?>
                        <?= Html::hiddenInput('id', null, ['class' => 'id']); ?>
                    </div>
                </div>

                <!-- Satuan -->
                <div class="col-md-2">
                    <label class="control-label"><?= Yii::t('fe', 'Satuan') ?></label>
                     <?php
                        echo DepDrop::widget(
                            [
                                'name'=>'ampuls_id',
                                'options'=>[
                                    'id'    =>'ampuls_id',
                                    'class' =>'select2 satuan_select',
                                ],
                                'pluginOptions'=>[
                                    'depends'     =>['select_list_obat_nonracikan'],
                                    'placeholder' =>\Yii::t('fe', '--Pilih--'),
                                    'class' => 'satuan_select',
                                    'url'         =>Url::to(['/apotek/transaksi-resep/dep-list-ampuls'])
                                ],
                                'pluginEvents'=> [
                                     'depdrop:afterChange'=>'function(event, id, value, textStatus) {
                                        var _response = $(\'#ampuls_id\').depdrop(\'getAjaxResults\');
                                        _group = {};
                                        $.each(_response.output, function (x,y) {
                                            _group[y.id] = y.konversi;
                                            if(y.konversi == 1) {
                                                $("input[name=\'satuankonversi_id\']").val(y.id);
                                                $("input[name=\'satuan_konversi\']").val(y.name);
                                            }
                                        });
                                        $("#ampuls_id").trigger("change");

                                        var _nilai_konversi = $("#nilai_konversi").val();
                                        var _stok           = $(".stok").val();
                                        var _koversi_stok   = parseFloat(_stok)/parseFloat(_nilai_konversi);
                                        _koversi_stok = _koversi_stok.toFixed(2);
                                        $(".qty_tersedia").val(docoHelper.convertToRupiah(_koversi_stok));

                                        var _hargajual      = $(".hargajual").val();
                                        var _konversi_harga = _hargajual*_nilai_konversi;
                                        // _konversi_harga = docoHelper.numberFormat(_konversi_harga, 2, ",", ".");
                                        _konversi_harga = _konversi_harga.toFixed(2);
                                        $(".harga_konversi").val(docoHelper.convertToRupiah(_konversi_harga));

                                        var _val_nama = $("#ampuls_id").find("option:selected").text();
                                        var _val_value = $("#ampuls_id").val();
                                        if ( _val_nama == "--Pilih--"){
                                            _val_nama = "";
                                            _val_value = "";
                                        };
                                        $(".satuan_kecil_text").html(_val_nama);

                                        $("#satuaninput_id").val(_val_value);
                                        $("#satuan_input").val(_val_nama);
                                        $("#harga_kecil").val(_hargajual);
                                     }',
                                ]
                            ]
                        );
                        ?>
                </div>

                <!-- Signa -->
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label"><?= Yii::t('fe', 'Signa') ?><span class="text-danger">*</span></label>
                        <?php
                        echo Select2::widget([
                            'name' => 'signa',
                            'data' => $list_signa,
                            'options' => [
                                'placeholder' => '— Pilih —',
                                'class' => 'signaid'
                            ],
                            'pluginOptions' => [
                                'tags' => true,
                                'tokenSeparators' => [',', '_'],
                                'maximumInputLength' => 50
                            ],
                        ]);
                        //  Html::dropDownList(
                        //     'signa',
                        //     null,
                        //     $list_signa,
                        //     ['class'=>'form-control select2 signaid', 'prompt'=>'— Pilih —']
                        // );
                        ?>
                    </div>
                </div>
            </div>

            <div class="row" style="margin-top: 5px">
                <!-- Qty Input -->
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label"><?= Yii::t('fe', 'Qty Input') ?> <span class="text-danger">*</span></label>
                        <?= Html::textInput('qty',null,[
                                'class'       => 'form-control qty',
                                'placeholder' => 'Qty Input',
                            ]);
                        ?>
                    </div>
                </div>

                <!-- Harga -->
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label"><?= Yii::t('fe', 'Harga') ?> &nbsp<span class="harga_text"></span></label>
                        <?= Html::textInput('harga_konversi',null,[
                                'class'       => 'form-control harga_konversi',
                                'id'          => 'harga_konversi',
                                'readonly'    => true,
                                'placeholder' => '0'
                            ]);
                        ?>
                    </div>
                </div>

                <!-- Stok -->
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label">Stok &nbsp;<span class="stok_text"></span></label>
                        <p class="form-info">
                            <span class="qty_tersedia">0</span>
                        </p>
                    </div>
                </div>


                <!-- Qty Konversi -->
                <div class="col-md-2">
                    <div class="form-group">
                        <label class=""><?= Yii::t('fe', 'Qty Konversi') ?> &nbsp<span class="satuan_kecil_text"></span></label>
                        <?= Html::textInput('qty_konversi',null,[
                                'class'       => 'form-control qty_konversi',
                                'id'          => 'qty_konversi',
                                'placeholder' => '0',
                                'readonly'    => True
                            ]);
                        ?>
                        <?= Html::hiddenInput('nilai_konversi', '', ['class' => 'nilai_konversi', 'id' => 'nilai_konversi']); ?>
                    </div>
                </div>
            </div>

            <div class="row" style="margin-top: 5px">
                <!-- Catatan -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label"><?= Yii::t('fe', 'Catatan') ?></label>
                        <?= Html::textInput('catatan',null,[
                                'class' => 'form-control catatan',
                                'id'    => 'catatan',
                            ]);
                        ?>
                    </div>
                </div>

                <?= Html::hiddenInput('satuaninput_id',null,[
                        'class' => 'form-control satuaninput_id',
                        'id'    => 'satuaninput_id',
                    ]);
                ?>
                <?= Html::hiddenInput('satuan_input',null,[
                        'class' => 'form-control satuan_input',
                        'id'    => 'satuan_input',
                    ]);
                ?>
                <?= Html::hiddenInput('satuankonversi_id',null,[
                        'class' => 'form-control satuankonversi_id',
                        'id'    => 'satuankonversi_id',
                    ]);
                ?>
                <?= Html::hiddenInput('satuan_konversi',null,[
                        'class' => 'form-control satuan_konversi',
                        'id'    => 'satuan_konversi',
                    ]);
                ?>
                <?= Html::hiddenInput('harga_kecil',null,[
                        'class' => 'form-control harga_kecil',
                        'id'    => 'harga_kecil',
                    ]);
                ?>

                <!-- Btn Simpan non racikan -->
                <div class="col-md-2">
                    <?= Html::submitButton('<i class="fa fa-plus"></i> ' . Yii::t('fe', "Tambahkan Obat"), [
                        'class'    => 'btn btn-success add',
                        'id'       => 'tambah-obat',
                        'disabled' => false,
                        'style'    => 'margin-top: 18px;'
                    ]); ?>
                </div>
            </div>
        <?php ActiveForm::end(); ?>

            <table id="manage-obat-edit-reseptur" class="table datatable-basic table-striped table-hover dataTable no-footer" style="margin: 10px 0">
                <thead>
                    <tr class="bg-inverse" style="font-size: 12px">
                        <th style="display: none;"></th>
                        <th style="padding: 10px;">No.</th>
                        <th><?= Yii::t('fe', 'Racikan') ?></th>
                        <th><?= Yii::t('fe', 'R ke') ?></th>
                        <th><?= Yii::t('fe', 'Nama Obat Alkes') ?></th>
                        <th title="Harga setelah ditambah embalase"><?= Yii::t('fe', 'Harga') ?><sup>*</sup> (Rp.)</th>
                        <th><?= Yii::t('fe', 'Signa') ?></th>
                        <th style="text-align: right;"><?= Yii::t('fe', 'Qty') ?></th>
                        <th style="text-align: right; width: 8%;"><?= Yii::t('fe', 'DET') ?></th>
                        <th><?= Yii::t('fe', 'Satuan') ?></th>
                        <th><?= Yii::t('fe', 'Catatan') ?></th>
                        <th><?= Yii::t('fe', 'Sub Total (Rp.)') ?></th>
                        <th style="width: 5%"><?= Yii::t('fe', 'Aksi') ?></th>
                    </tr>
                </thead>
                <tbody id="list-obat">
                </tbody>
                <tfoot>
                    <?=Html::hiddenInput('subtotal', '', ['class'=>'subTotalItem'])?>
                    <tr>
                        <td class="text-right" colspan="10">Sub Total (Rp.)</td>
                        <td class="subtotal text-right"></td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td class="text-right" colspan="10">Biaya Admin (Rp.)</td>
                        <td class="biaya-admin text-right">
                            <input type="text" name="biaya_admin" style="text-align: right;" class="doco-number form-control biayaAdmin" placeholder="Biaya Admin" value="<?= $biayaadministrasi ?>" size="7" />
                        </td>
                        <td>&nbsp;</td>
                    </tr>
                    <?=Html::hiddenInput('total', '', ['class'=>'totalItem'])?>
                    <tr>
                        <td class="text-right" colspan="10">Total (Rp.)</td>
                        <td class="total text-right"></td>
                        <td>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
    </div>
</div>

<?php
    $this->registerCss($this->render('../assets/css/apotek.css'));
?>

<?php
    $this->registerJs("
        var tempObatRacikan = [];

        $(document).ready(function(){
            $('.form-nonracikan input').attr('disabled', true);
            $('#form-racikan input').attr('disabled', true);

            $(document).on('click' ,'input[name=\"jenis_racikan\"]', function(e) {
                var _val = $(this).val();
                if(_val == 'racikan') {
                    $('#form-racikan').css('display', 'initial');
                    $('.form-nonracikan').css('display', 'none');
                    $('#form-racikan input').attr('disabled', false);
                    $('.form-nonracikan input').attr('disabled', true);
                } else {
                    $('#form-racikan').css('display', 'none');
                    $('.form-nonracikan').css('display', 'initial');
                    $('#form-racikan input').attr('disabled', true);
                    $('.form-nonracikan input').attr('disabled', false);
                }
            });
        });
    ");

    $this->registerJs($this->render('../assets/js/edit-reseptur.js'));
 ?>