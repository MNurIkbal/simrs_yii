<?php

/**
 * @author : Anggoro (tri.anggoro@docotel.com)
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
use kartik\widgets\DateTimePicker;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Informasi Reseptur'), 'url' => ['/apotek/informasi-reseptur']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', $this->title)];
?>

<style type="text/css">
    textarea.form-control {
        min-height: initial !important;
    }

    .text-right {
        text-align: right;
    }

    #example tr td,
    #example tr th {
        padding: 10px
    }

    #example tr td .btn-xs,
    .temp-racikan tr td .btn-xs {
        min-width: initial;
        min-height: initial;
        padding: 5px 10px !important;
    }

    tr.out-stock.resep {
        color: red !important;
        font-weight: bold;
    }

    .temp-racikan tr td .btn-xs {
        padding: 1px 2px !important;
    }

    .form-informasi .form-group {
        margin-bottom: 6px !important;
        display: flow-root;
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

    .row.small-gutter>[class*='col-'] {
        padding-right: 2px;
        padding-left: 2px;
    }

    table.temp-racikan,
    table.temp-racikan th {
        font-size: 10px !important;
    }

    table.temp-racikan th,
    table.temp-racikan td {
        padding: 4px 5px !important;
    }

    #form-racikan hr {
        margin-top: 10px;
        margin-bottom: 10px;
    }

    .biayaAdmin {
        margin-bottom: 7px;
    }
    
    .ig-pasien-bebas {
        display: flex !important;
        width: 100% !important;
    }

    .ig-pasien-bebas .select2-container {
        flex: 1 1 auto !important;
        width: 100% !important;
    }

    .ig-pasien-bebas .input-group-addon {
        flex: 0 0 auto !important;
    }
        .ig-pasien-bebas .input-group-addon {
        display: none !important;
    }

</style>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= $this->title ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                    </div>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'save' => [
                        'attributes' => [
                            'id' => 'save-pasien',
                            'data-options' => 'click'
                        ]
                    ],
                    'back' => ['attributes' => ['href' => '/apotek/informasi-reseptur/#']],
                ])
                ?>
            </div>

            <div class="panel-body" style="padding: 10px">
                <div class="row">
                    <div class="col-md-3" style="padding: 0 !important">
                        <!-- Informasi -->
                        <div class="panel panel-default" id="informasi" style="margin: 10px">
                            <div class="panel-heading">
                                <h5 class="panel-title"><?= Yii::t('fe', 'Informasi') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h5>
                                <div class="heading-elements">
                                    <ul class="icons-list">
                                        <li><a data-action="collapse"></a></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="panel-body" style="padding: 10px">
                                <form id="form-pasien">
                                    <div class="form-informasi">
                                        <div class="form-group">
                                            <label class="control-label">Jenis Penjualan</label><br>
                                            <div id="jenis_penjualan">
                                                <label class="label-jenis-penjualan">
                                                    <input type="radio" value="pasien" name="jenis_penjualan" checked>
                                                    <span>Pasien RS</span>
                                                </label>
                                                <label class="label-jenis-penjualan">
                                                    <input type="radio" value="karyawan" name="jenis_penjualan">
                                                    <span>Karyawan</span>
                                                </label>
                                                <label class="label-jenis-penjualan">
                                                    <input type="radio" value="bebas" name="jenis_penjualan">
                                                    <span>Bebas</span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label">Tanggal Penjualan</label>
                                            <?= DateTimePicker::widget([
                                                'name' => 'tglpenjualan',
                                                'id' => 'tglresep',
                                                'language' => 'en',
                                                'type' => DateTimePicker::TYPE_INPUT,
                                                'value' => date('d-M-Y H:i:s'),
                                                'readonly' => true,
                                                'pluginOptions' => [
                                                    'startDate' => date('d-M-Y H:i:s', strtotime('1970-01-01 23:59:59')),
                                                    'endDate' => date('d-M-Y 23:59:59'),
                                                    'autoclose' => true,
                                                    'todayBtn' => true,
                                                    'format' => 'dd-M-yyyy hh:ii:ss',
                                                ]
                                            ]); ?>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label label-no-pendaftaran">No. Pendaftaran</label><span class="text-danger req-nopendaftaran" style="display: inline;"> *</span>
                                            <div class="input-group ig-pasien-bebas">
                                                <select name="no_pendaftaran" class="form-control" id="src_no_pendaftaran"></select>
                                                <input type="hidden" name="pendaftaran_id" class="form-control">
                                                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                            </div>

                                            <div class="input-group ig-karyawan">
                                                <select name="nip_karyawan" class="form-control" id="src_nip_karyawan"></select>
                                                <input type="hidden" name="nip" class="form-control">
                                                <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                            </div>
                                        </div>

                                        <div class="form-group pasien-form-group">
                                            <label for="" class="control-label label-nama-pasien">No. RM / Pasien</label> <span class="text-danger req-namapasien" style="display: none;">*</span>
                                            <select name="pembeli" class="form-control" id="pembeli" disabled></select>
                                            <input type="hidden" name="pasien">
                                            <input type="hidden" name="pasien_id">
                                            <input type="hidden" name="pasienadmisi_id">
                                        </div>

                                        <div class="form-group">
                                            <label for="" class="control-label">Tanggal Lahir</label>
                                            <input type="text" class="form-control tgllahir" readonly name="tgl_lahir">
                                        </div>

                                        <div class="form-group">
                                            <label for="" class="control-label">Dokter Resep</label>
                                            <select class="form-control" id="select_dokter" name="dokter_id"></select>

                                        </div>

                                        <div class="form-group">
                                            <label class="control-label"><?= Yii::t('fe', 'Iter') ?></label>
                                            <?= Html::textInput('iter', null, [
                                                'class'       => 'form-control iter',
                                                'placeholder' => 'Jumlah Iter',
                                            ]);
                                            ?>
                                        </div>

                                        <div class="form-group">
                                            <label for="" class="control-label">Cara Bayar</label> <span class="text-danger req-namapasien" style="display: none;">*</span>
                                            <div class="input-group ig-carabayar">
                                                <?php
                                                echo Select2::widget([
                                                    'name' => 'bebas_carabayar_id',
                                                    'data' => $data_cara_bayar,
                                                    'options' => [
                                                        'placeholder' => '— Pilih —',
                                                        'id' => 'selectcarabayar_id',
                                                        'class' => 'form-control'
                                                    ]
                                                ]);
                                                ?>
                                            </div>

                                            <div class="input-group default-carabayar">
                                                <p class="form-info"><span id="val_carabayar">-</span></p>
                                                <input type="hidden" class="carabayar_id" name="carabayar_id">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="" class="control-label">Penjamin</label> <span class="text-danger req-namapasien" style="display: none;">*</span>
                                            <div class="input-group ig-penjamin">
                                                <?php
                                                echo DepDrop::widget(
                                                    [
                                                        'name' => 'bebas_penjamin_id',
                                                        'options' => [
                                                            'disabled' => false,
                                                            'id'    => 'selectpenjamin_id',
                                                            'class' => 'select2 bebas_penjamin_id',
                                                        ],
                                                        'pluginOptions' => [
                                                            'depends'     => ['selectcarabayar_id'],
                                                            'class'       => 'bebas_penjamin_id',
                                                            'placeholder' => \Yii::t('fe', '--Pilih--'),
                                                            'url'         => Url::to(['/apotek/transaksi-resep/get-penjamin'])
                                                        ],
                                                        'pluginEvents' => [
                                                            'depdrop:afterChange' => "function (event, id, value) {
                                                                $('.bebas_penjamin_id').trigger('change');
                                                            }"
                                                        ]
                                                    ]
                                                );
                                                ?>
                                            </div>

                                            <div class="input-group default-penjamin">
                                                <p class="form-info"><span id="val_penjamin">-</span></p>
                                                <input type="hidden" id="penjamin_id" class="form-control" name="penjamin_id">
                                            </div>
                                        </div>

                                            <div class="form-group">
                                                <label for="" class="control-label">Instalasi - Ruangan</label>
                                                <p class="form-info"><span id="val_instalasi_ruangan">-</span></p>
                                            </div>

                                            <div class="form-group">
                                                <label for="" class="control-label">Catatan</label>
                                                <textarea name="catatan" id="" cols="30" rows="3" class="form-control"></textarea>
                                            </div>


                                            <input type="hidden" id="kelaspelayanan_id" class="form-control" name="kelaspelayanan_id_hidden">
                                            <input type="hidden" id="kelastitipan_id" class="form-control" name="kelastitipan_id_hidden">
                                        </div>
                                </form>
                                <!-- End Form -->
                            </div>
                        </div>
                    </div> <!-- End of Informasi -->

                    <div class="col-md-9" style="padding: 0 !important">
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
                                <table id="example" class="table datatable-basic table-striped table-hover dataTable no-footer" style="margin: 10px 0">
                                    <thead>
                                        <tr class="bg-inverse" style="font-size: 12px">
                                            <th style="padding: 10px;">No.</th>
                                            <th colspan="2"><?= Yii::t('fe', 'R ke') ?></th>
                                            <th><?= Yii::t('fe', 'Nama Obat Alkes') ?></th>
                                            <th title="Harga setelah ditambah embalase"><?= Yii::t('fe', 'Harga') ?><sup>*</sup> (Rp.)</th>
                                            <th><?= Yii::t('fe', 'Signa') ?></th>
                                            <th><?= Yii::t('fe', 'Qty') ?></th>
                                            <th><?= Yii::t('fe', 'Satuan') ?></th>
                                            <th><?= Yii::t('fe', 'Catatan') ?></th>
                                            <th><?= Yii::t('fe', 'Sub Total (Rp.)') ?></th>
                                            <th><?= Yii::t('fe', 'Aksi') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody id="list-obat">
                                    </tbody>
                                    <tfoot>
                                        <?= Html::hiddenInput('subtotal', '', ['class' => 'subTotalItem']) ?>
                                        <tr>
                                            <td class="text-right" colspan="9">Sub Total (Rp.)</td>
                                            <td class="subtotal text-right"></td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td class="text-right" colspan="9">Biaya Admin (Rp.)</td>
                                            <td class="biaya-admin text-right">
                                                <input type="text" name="biaya_admin" style="text-align: right;" class="doco-number form-control biayaAdmin" placeholder="Biaya Admin" value="0" size="7" />
                                            </td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <?= Html::hiddenInput('total', '', ['class' => 'totalItem']) ?>
                                        <tr>
                                            <td class="text-right" colspan="9">Total (Rp.)</td>
                                            <td class="total text-right"></td>
                                            <td>&nbsp;</td>
                                        </tr>
                                    </tfoot>
                                </table>
                                
                                <div id="form-header">
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
                                    <?= Html::hiddenInput('obatalkes_id', ''); ?> <?= Html::hiddenInput('obat_nama', ''); ?>
                                    <?= Html::hiddenInput('stok', ''); ?> <?= Html::hiddenInput('harga', ''); ?>
                                    <?= Html::hiddenInput('ppn', ''); ?> <?= Html::hiddenInput('satuankecil_id', '', ['class' => 'racikan_satuankecil_id']); ?>
                                    <?= Html::hiddenInput('posisiNo', ''); ?> <?= Html::hiddenInput('type', 4); ?>
                                    <?= Html::hiddenInput('harganetto', ''); ?> <?= Html::hiddenInput('jmlmargin', ''); ?>
                                    <?= Html::hiddenInput('jmlppn', ''); ?> <?= Html::hiddenInput('jmldiscount', ''); ?>
                                    <?= Html::hiddenInput('persenppn', ''); ?> <?= Html::hiddenInput('persenmargin', ''); ?>
                                    <?= Html::hiddenInput('persendiscount', ''); ?> <?= Html::hiddenInput('hargajual', ''); ?>
                                    <?= Html::hiddenInput('signa_nama', ''); ?> <?= Html::hiddenInput('id', ''); ?>
                                    <?= Html::hiddenInput('nilai_konversi', ''); ?>

                                    <?= Html::hiddenInput('satuaninput_id', null) ?> <?= Html::hiddenInput('satuan_input', null) ?>
                                    <?= Html::hiddenInput('satuankonversi_id', null) ?> <?= Html::hiddenInput('satuan_konversi', null) ?>
                                    <?= Html::hiddenInput('harga_kecil', null) ?>
                                    <?= Html::hiddenInput('satuan_racikan_obat_nama', ''); ?>

                                    <div class="row" style="margin-top: 15px;">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <!-- R ke Racikan -->
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="control-label mandatory"><?= Yii::t('fe', 'R ke') ?> <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control r_ke" name="r_ke">
                                                    </div>
                                                </div>

                                                <!-- Nama Racikan -->
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label class="control-label">Nama Racikan</label>
                                                        <input type="text" class="form-control nama_racikan" name="nama_racikan">
                                                    </div>
                                                </div>

                                                <!-- Signa Racikan -->
                                                <div class="col-md-4">
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

                                                <!-- Qty Jumlah Racikan -->
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label class="control-label"><?= Yii::t('fe', 'Qty Racikan') ?> <span class="text-danger">*</span></label>
                                                        <?= Html::textInput('qty_racikan', null, [
                                                            'class'       => 'form-control qty_racikan',
                                                            'placeholder' => 'Qty Racikan',
                                                        ]);
                                                        ?>
                                                    </div>
                                                </div>

                                                <!-- satuan racikan -->
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="control-label mandatory">Satuan Racikan <span class="text-danger">*</span></label>
                                                        <?php
                                                        echo Select2::widget([
                                                            'name' => 'satuan_racikan_id',
                                                            'data' => $satuan_unit,
                                                            'options' => [
                                                                'placeholder' => '— Pilih —',
                                                                'id' => 'satuan_racikan_id',
                                                                'class' => 'form-control'
                                                            ],
                                                        ]);
                                                        ?>
                                                        <?= Html::hiddenInput('satuan_racikan_hidden', null) ?>
                                                    </div>
                                                </div>

                                                <!-- Catatan Racikan -->
                                                <div class="col-md-4">
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
                                                        <select name="obatalkes_id" id="select_obat_racikan" class="form-control select2" style="width: 100%"></select>
                                                    </div>
                                                </div>

                                                <!-- Satuan Racikan -->
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="control-label"><?= Yii::t('fe', 'Satuan') ?><span class="text-danger">*</span></label>
                                                        <?php
                                                        echo DepDrop::widget(
                                                            [
                                                                'name' => 'satuan',
                                                                'options' => [
                                                                    'id'    => 'satuan_racikan',
                                                                    'class' => 'select2 satuan_racikan',
                                                                ],
                                                                'pluginOptions' => [
                                                                    'depends'     => ['select_obat_racikan'],
                                                                    'class'       => 'satuan_racikan',
                                                                    'placeholder' => \Yii::t('fe', '--Pilih--'),
                                                                    'url'         => Url::to(['/apotek/transaksi-resep/dep-list-ampuls'])
                                                                ],
                                                                'pluginEvents' => [
                                                                    'depdrop:afterChange' => 'function(event, id, value, textStatus) {
                                                                           var _response = $(\'#satuan_racikan\').depdrop(\'getAjaxResults\');
                                                                            _group = {};
                                                                            $.each(_response.output, function (x,y) {
                                                                                _group[y.id] = y.konversi;
                                                                                if(y.konversi == 1) {
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
                                                        <?= Html::textInput('harga_konversi', null, [
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
                                                        <?= Html::textInput('qty', null, [
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
                                                        <?= Html::textInput('qty_konversi', null, [
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
                                                <div class="col-md-6 col-md-offset-6 text-right">
                                                    <button class="btn btn-danger btn-sm btn-ulang"><i class="fa fa-times"></i> Ulang</button>
                                                    <button type="button" class="btn btn-sm btn-success" id="btn-racikan"><i class="fa fa-plus"></i> Tambah ke Racikan</button>
                                                </div>
                                            </div>

                                            <div id="disabled_button_info_racikan" style="display: none;">
                                                <br>
                                                <p style="color: red;"><i>
                                                        *Obat/Alkes tidak bisa ditambahkan karena stok tidak mencukupi
                                                    </i></p>
                                            </div>
                                        </div>

                                        <div class="col-md-6" style=" border-left: 2px solid rgb(221, 221, 221); min-height: 200px;">
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
                                            <select name="obatalkes_id" id="select_obat_non_racikan" class="form-control select2" style="width: 100%"></select>

                                            <?= Html::hiddenInput('obatalkes_id', '', ['class' => 'id_obat']); ?>
                                            <?= Html::hiddenInput('obat_nama', '', ['class' => 'obat_nama']); ?>
                                            <?= Html::hiddenInput('stok', '', ['class' => 'stok']); ?>
                                            <?= Html::hiddenInput('harga', '', ['class' => 'harga']); ?>
                                            <?= Html::hiddenInput('ppn', '', ['class' => 'ppn']); ?>
                                            <?= Html::hiddenInput('satuankecil_id', '', ['class' => 'satuankecil_id']); ?>
                                            <?= Html::hiddenInput('posisiNo', '', ['class' => 'posisi']); ?>
                                            <?= Html::hiddenInput('type', 4, ['class' => 'type']); ?>
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
                                                'name' => 'ampuls_id',
                                                'options' => [
                                                    'id'    => 'ampuls_id',
                                                    'class' => 'select2 satuan_select',
                                                ],
                                                'pluginOptions' => [
                                                    'depends'     => ['select_obat_non_racikan'],
                                                    'placeholder' => \Yii::t('fe', '--Pilih--'),
                                                    'class' => 'satuan_select',
                                                    'url'         => Url::to(['/apotek/transaksi-resep/dep-list-ampuls'])
                                                ],
                                                'pluginEvents' => [
                                                    'depdrop:afterChange' => 'function(event, id, value, textStatus) {
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
                                            <?= Html::textInput('qty', null, [
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
                                            <?= Html::textInput('harga_konversi', null, [
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
                                            <?= Html::textInput('qty_konversi', null, [
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
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="control-label"><?= Yii::t('fe', 'Catatan') ?></label>
                                            <?= Html::textInput('catatan', null, [
                                                'class' => 'form-control catatan',
                                                'id'    => 'catatan',
                                            ]);
                                            ?>
                                        </div>
                                    </div>

                                    <?= Html::hiddenInput('satuaninput_id', null, [
                                        'class' => 'form-control satuaninput_id',
                                        'id'    => 'satuaninput_id',
                                    ]);
                                    ?>
                                    <?= Html::hiddenInput('satuan_input', null, [
                                        'class' => 'form-control satuan_input',
                                        'id'    => 'satuan_input',
                                    ]);
                                    ?>
                                    <?= Html::hiddenInput('satuankonversi_id', null, [
                                        'class' => 'form-control satuankonversi_id',
                                        'id'    => 'satuankonversi_id',
                                    ]);
                                    ?>
                                    <?= Html::hiddenInput('satuan_konversi', null, [
                                        'class' => 'form-control satuan_konversi',
                                        'id'    => 'satuan_konversi',
                                    ]);
                                    ?>
                                    <?= Html::hiddenInput('harga_kecil', null, [
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
                                <div id="disabled_button_info" style="display: none;">
                                    <br>
                                    <p style="color: red;"><i>
                                            *Obat/Alkes tidak bisa ditambahkan karena stok tidak mencukupi
                                        </i></p>
                                </div>
                                <?php ActiveForm::end(); ?>
                            </div>
                        </div>
                    </div> <!-- End of Resep Obat -->
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerCss($this->render('../assets/css/apotek.css'));
?>

<?php
$this->registerJs("
        let instalasi_id = null;
        var ruangan_id = '" . $ruangan_id . "'
        const dateNow = () => {
            const date = new Date()
            this.set('select', [date.getFullYear(), date.getMonth(), date.getDate()]);
        }
        $('.ig-karyawan').prop('style', 'display: none');
        $(document).ready(function(){
            $('.form-nonracikan input').attr('disabled', true);
            $('#form-racikan input').attr('disabled', true);
            $('.ig-carabayar').prop('style', 'display: none');
            $('.ig-penjamin').prop('style', 'display: none');
            $('.default-carabayar').prop('style', 'display: inline');
            $('.default-penjamin').prop('style', 'display: inline');

            $('#select_dokter').select2({
                ajax: {
                    url: '/apotek/transaksi-resep/list-dokter',
                    delay: 300,
                    data: function(params) {
                        var query = {
                            search: {
                                value: params.term
                            },
                            'advanced-filter[nama_pegawai]': params.term
                        }
                        return query;
                    },
                    processResults: function(result) {
                        return {
                            results: result
                        }
                    }
                },
                placeholder: 'Pilih Dokter',
                minimumInputLength: 3
            });

            $(document).on('click' ,'input[name=\"jenis_penjualan\"]', function(e) {
                var _val = $(this).val();
                if(_val == 'bebas') {
                    $('.req-namapasien').prop('style', 'display: inline');
                    $('.req-nopendaftaran').prop('style', 'display: none');
                    $('#src_no_pendaftaran').prop('disabled', true);
                    $('input[name=\"pasien\"]').prop('readonly', false);
                    $('input[name=\"pasien\"]').prop('placeholder', 'Nama Pasien');
                    $('#pembeli').prop('disabled', false);
                    $('#val_carabayar').prop('style', 'display: none');
                    $('#val_penjamin').prop('style', 'display: none');
                    $('.ig-carabayar').prop('style', 'display: inline');
                    $('.ig-penjamin').prop('style', 'display: inline');
                    $('#selectcarabayar_id').val(null).trigger('change');
                    $('#selectpenjamin_id').val(null).trigger('change');
                    $('.default-carabayar').prop('style', 'display: none');
                    $('.default-penjamin').prop('style', 'display: none');
                    // $('#val_carabayar').text('Umum');
                    // $('.carabayar_id').val(5);
                    // $('#val_penjamin').text('Perseorangan');
                    // $('#penjamin_id').val(1);
                    $('input[name=\"tgl_lahir\"]').val(null);

                    $('.ig-karyawan').prop('style', 'display: none');
                    $('.ig-pasien-bebas').prop('style', 'display: table');
                    $('.label-no-pendaftaran').text('No. Pendaftaran');
                    $('.label-nama-pasien').text('Nama Pembeli');
                    $('#kelaspelayanan_id').val(0);
                } else if(_val == 'karyawan') {
                    $('.label-no-pendaftaran').text('Karyawan');
                    $('.label-nama-pasien').text('Nama Karyawan');
                    $('.ig-karyawan').prop('style', 'display: table');
                    $('.ig-pasien-bebas').prop('style', 'display: none');
                    $('.ig-carabayar').prop('style', 'display: none');
                    $('.ig-penjamin').prop('style', 'display: none');
                    $('input[name=\"pasien\"]').prop('readonly', true);
                    $('input[name=\"pasien\"]').prop('placeholder', 'Nama Karyawan');
                    $('#pembeli').prop('disabled', true);
                    $('.req-namapasien').prop('style', 'display: none');
                    $('.req-nopendaftaran').prop('style', 'display: inline');
                    $('input[name=\"tgl_lahir\"]').val(null);

                    $('#val_carabayar').prop('style', 'display: inline');
                    $('#val_penjamin').prop('style', 'display: inline');
                    $('#val_carabayar').text(carabayarkaryawan_nama);
                    $('.carabayar_id').val(carabayarkaryawan_id);
                    $('#val_penjamin').text(penjaminkaryawan_nama);
                    $('#penjamin_id').val(penjaminkaryawan_id);
                    $('#kelaspelayanan_id').val(0);
                    $('.default-carabayar').prop('style', 'display: inline');
                    $('.default-penjamin').prop('style', 'display: inline');
                } else {
                    $('.req-nopendaftaran').prop('style', 'display: inline');
                    $('.req-namapasien').prop('style', 'display: none');
                    $('#src_no_pendaftaran').prop('disabled', false);
                    $('input[name=\"pasien\"]').prop('readonly', true);
                    $('input[name=\"pasien\"]').prop('placeholder', '');
                    $('#pembeli').prop('disabled', true);
                    $('#val_carabayar').prop('style', 'display: inline');
                    $('#val_penjamin').prop('style', 'display: inline');
                    $('#val_carabayar').text('-');
                    $('.carabayar_id').val(null);
                    $('#val_penjamin').text('-');
                    $('#penjamin_id').val(null);
                    $('#kelaspelayanan_id').val(0);
                    $('#kelastitipan_id').val(0);
                    $('input[name=\"tgl_lahir\"]').val(null);

                    $('.ig-carabayar').prop('style', 'display: none');
                    $('.ig-penjamin').prop('style', 'display: none');
                    $('.ig-karyawan').prop('style', 'display: none');
                    $('.ig-pasien-bebas').prop('style', 'display: table');
                    $('.label-no-pendaftaran').text('No. Pendaftaran');
                    $('.label-nama-pasien').text('Nama Pembeli');
                    $('.label-nama-pasien').text('No. RM / Pasien');
                    $('.default-carabayar').prop('style', 'display: inline');
                    $('.default-penjamin').prop('style', 'display: inline');
                }

                $('#src_no_pendaftaran').val(null).trigger('change');
                $('#src_nip_karyawan').val(null).trigger('change');
                $('#pembeli').val(null).trigger('change');
                $('input[name=\"tgl_lahir\"]').val(null);
                $('input[name=\"pasien\"]').val(null);
                $('input[name=\"pendaftaran_id\"]').val(null);
                $('input[name=\"pasien_id\"]').val(null);
                $('input[name=\"pasienadmisi_id\"]').val(null);
                $('#select_dokter').val(null).trigger('change');
                $('.iter').val(null);
                $('input[name=\"dokter_id\"]').val(null);
                $('#val_instalasi_ruangan').text('-');
                $('textarea[name=\"catatan\"]').val(null);
                $('.form-tb-bb').remove();

                // clear min date tanggal resep
                $('#tglresep').datetimepicker('setStartDate', '1970-01-01');
                $('#tglresep').datetimepicker('update', new Date());
            });

            let _pasien = {};
            let _selected_pasien = {};
            function formatListPasien(src){
                if(src.loading){
                    return src.text;
                }

                if(src.is_pasien_aktif != undefined && src.is_pasien_aktif){
                    container = $('<p style=\"font-style:italic;\" class=\"text-bold\">'+src.text+'</p>');
                }else{
                    container = $('<p style=\"opacity:0.75;\">'+src.text+'</p>');
                }

                return container;
            }
            $('#src_no_pendaftaran').select2({
                ajax: {
                    url: '/apotek/transaksi-resep/search-pasien-select2',
                    delay: 300,
                    data: function(params) {
                        return {
                            no_pendaftaran: params.term,
                            page: params.page || 1
                        }
                    },
                    processResults: function(result, params) {
                        params.page = params.page || 1;
                        _pasien = result.data;
                        return {
                            results: result.list,
                            pagination: {
                                more: result.more
                            }
                        }
                    },
                },
                placeholder: 'Cari No. Pendaftaran / No. RM / Nama',
                minimumInputLength: 3,
                templateResult:formatListPasien
            });

            $('#pembeli').select2({
                ajax: {
                    url: '/apotek/transaksi-resep/search-master-pasien-select2',
                    delay: 300,
                    data: function(params) {
                        return {
                            nama_pasien: params.term,
                            page: params.page || 1
                        }
                    },
                    processResults: function(result, params) {
                        params.page = params.page || 1;
                        _pasien = result.data;
                        return {
                            results: result.list,
                            pagination: {
                                more: result.more
                            }
                        }
                    },
                },
                tags: true,
                createTag: function (params) {
                    return {
                        id: params.term,
                        text: params.term,
                        newOption: true
                    }
                },
                placeholder: 'Nama',
                minimumInputLength: 3,
                templateResult:formatListPasien
            });

            let _karyawan = {};
            $('#src_nip_karyawan').select2({
                ajax: {
                    url: '/apotek/transaksi-resep/search-karyawan-select2',
                    delay: 300,
                    data: function(params) {
                        return {
                            nip: params.term,
                            page: params.page || 1
                        }
                    },
                    processResults: function(result, params) {
                        params.page = params.page || 1;
                        _karyawan = result.data;
                        return {
                            results: result.list,
                            pagination: {
                                more: result.more
                            }
                        }
                    },
                },
                placeholder: 'Cari NIP / Nama Karyawan',
                minimumInputLength: 3
            });

            $('#selectpenjamin_id').on('change', function (ev) {
                $('#penjamin_id').val($(this).val());
            });

            $('#selectcarabayar_id').on('change', function (ev) {
                $('.carabayar_id').val($(this).val());
            });

            $(document).on('select2:select', '#src_nip_karyawan', function(e) {
                _data = e.params.data.data;
                var _select = $(this);

                var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                var tanggal_lahir = new Date(_data.tgl_lahirpegawai);
                var string_tanggal_lahir = tanggal_lahir.getDate() + ' ' + months[tanggal_lahir.getMonth()] + ' ' + tanggal_lahir.getFullYear();

                $('input[name=\"nip\"]').val(_data.nomorindukpegawai);
                $('input[name=\"pasien\"]').val(_data.nama_pegawai);
                $('input[name=\"pasien_id\"]').val(_data.pegawai_id);
                $('input[name=\"tgl_lahir\"]').val(string_tanggal_lahir);
            });

            $(document).on('select2:select', '#src_no_pendaftaran', function(e) {
                _data = e.params.data.data;
                var _select = $(this);
                $('.form-tb-bb').remove();

                changeDataPasienAfterSelected(_data, _select);
            });

            $('#pembeli').on('select2:close', function(e) {
                var me = $(this);
                var tag = me.find('option[data-select2-tag]');

                if (tag && tag.length && me.find('option').length === 1) {
                    me.val(tag.attr('value'));
                    me.trigger('change');
                    $('input[name=\"pasien\"]').val(tag.attr('value'));
                }
            });

            $('#pembeli').on('select2:select', (e) => {
                _data = e.params.data.data;
                var _select = $(this);
                $('.form-tb-bb').remove();

                if(_data != undefined) {
                    changeDataPasienAfterSelected(_data, _select);
                } else {
                    _data = e.params.data;
                    $('input[name=\"pendaftaran_id\"]').val='';
                    $('input[name=\"pasien\"]').val(_data.text);
                    $('input[name=\"pasien_id\"]').val();
                    $('input[name=\"pasienadmisi_id\"]').val='';
                    $('input[name=\"carabayar_id\"]').val='';
                    $('#penjamin_id').val('');
                    $('#kelastitipan_id').val('');
                    $('#val_instalasi_ruangan').text('');
                }
            });

            function changeDataPasienAfterSelected(_data, _select) {
                var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                var tanggal_lahir = new Date(_data.tanggal_lahir);
                var string_tanggal_lahir = tanggal_lahir.getDate() + ' ' + months[tanggal_lahir.getMonth()] + ' ' + tanggal_lahir.getFullYear();

                $('input[name=\"pendaftaran_id\"]').val(_data.pendaftaran_id);
                $('input[name=\"pasien\"]').val(_data.no_rekam_medik + ' / ' + _data.nama_pasien);
                $('input[name=\"pasien_id\"]').val(_data.pasien_id);
                $('input[name=\"pasienadmisi_id\"]').val(_data.pasienadmisi_id);
                $('input[name=\"carabayar_id\"]').val(_data.carabayar_id);
                $('input[name=\"tgl_lahir\"]').val(string_tanggal_lahir);

                var carabayar_id = _data.carabayar_id;
                var carabayar_nama = _data.carabayar_nama;
                var carabayar_option = {
                    id: carabayar_id,
                    text: carabayar_nama
                }
                var newOptionCarabayar = new Option(carabayar_option.text, carabayar_option.id, true, false);
                $('#selectcarabayar_id').append(newOptionCarabayar).trigger('change');
                $('#selectcarabayar_id').val(carabayar_option.id).trigger('change');

                var penjamin_id = _data.penjamin_id;
                var penjamin_nama = _data.penjamin_nama;
                var penjamin_option = {
                    id: penjamin_id,
                    text: penjamin_nama
                }
                var newOptionPenjamin = new Option(penjamin_option.text, penjamin_option.id, true, false);
                $('#selectpenjamin_id').append(newOptionPenjamin).trigger('change');
                $('#selectpenjamin_id').val(penjamin_option.id).trigger('change');

                var dokter_id = _data.dokter_id;
                var dokter_nama = _data.dokter_nama;
                $('input[name=\"dokter_id\"]').val(dokter_id);

                var data = {
                    id: dokter_id,
                    text: dokter_nama
                }
                var newOption = new Option(data.text, data.id, true, false);

                _select[0].options = null;

                $('#select_dokter').append(newOption).trigger('change');
                $('#select_dokter').val(data.id).trigger('change');
                $('#val_carabayar').text(_data.carabayar_nama);
                $('#val_penjamin').text(_data.penjamin_nama);
                $('#penjamin_id').val(_data.penjamin_id);
                if(_data.kelaspelayanan_id !== undefined) {
                    $('#kelaspelayanan_id').val(_data.kelaspelayanan_id);
                }

                $('#kelastitipan_id').val(_data.kelas_titipan);
                
                $('#val_instalasi_ruangan').text(_data.instalasi_nama + ' - ' + _data.ruangan_nama);
                if(_data.instalasi_nama === undefined) {
                    $('#val_instalasi_ruangan').text(' - ');
                }
                
                var bb = (_data.berat_badan ? parseInt(_data.berat_badan).toFixed(2).concat(' kg') : '-');
                var tb = (_data.tinggi_badan ? parseInt(_data.tinggi_badan).toFixed(2).concat(' cm') : '-');
                $('<div class=\"form-group form-tb-bb\"><label class=\"control-label\"> Berat Badan / Tinggi Badan</label><input class=\"form-control\" type=\"text\" readonly value=\"'+bb+' / '+tb+' \" /></div>').insertAfter('.pasien-form-group');
                $('#tglresep').datetimepicker('update', new Date());
                $('#tglresep').datetimepicker('setStartDate', _data.tgl_pendaftaran);
                instalasi_id = _data.instalasi_id               
                if (_data.instalasi_id != null && _data.instalasi_id == 4 || _data.instalasi_id == 5 || _data.instalasi_id == 7) {
                    instalasi_id = 1; // Set Into Rajal if prev penjamin is penunjang.
                }
            }

            $(document).on('click' ,'input[name=\"jenis_racikan\"]', function(e) {
                var _pasien = $('input[name=\"pasien\"]').val();
                var _carabayar = $('.carabayar_id').val();
                var _penjamin = $('#penjamin_id').val();

                if(_pasien == ''){
                    docoNotification('warning', i18next.t('Perhatian'),
                        i18next.t('Pasien Masih Kosong'));
                    return false;
                }

                if(_carabayar == ''){
                    docoNotification('warning', i18next.t('Perhatian'),
                        i18next.t('Cara Bayar Kosong'));
                    return false;
                }

                if(_penjamin == ''){
                    docoNotification('warning', i18next.t('Perhatian'),
                        i18next.t('Penjamin Kosong'));
                    return false;
                }

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

        var transObat;
        var urutObatRs;
        var display = 'block';
        var sum_subtotalNetto = 0;
        var tempObatRacikan = [];
        var carabayarkaryawan_id = '$carabayarkaryawan_id';
        var carabayarkaryawan_nama = '$carabayarkaryawan_nama';
        var penjaminkaryawan_id = $penjaminkaryawan_id;
        var penjaminkaryawan_nama = '$penjaminkaryawan_nama';
        var cache_label_track_pasien = '$cacheLabelTrackPasien';
        var list_signa = '$data_signa'
        list_signa = JSON.parse(list_signa)
    ");

$this->registerJs($this->render('../assets/js/penjualan-resep-pasien.js'));
?>
