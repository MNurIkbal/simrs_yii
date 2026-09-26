<?php

/**
 * @author Yaya
 * @copyright 26 November 2018
 */

use yii\web\View;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

?>

<div class="col-md-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h6 class="panel-title"><b><?= Yii::t('fe','Informasi Pasien') ?></b></h6>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <br>
                <div class="col-md-4">
                    <label class="text-left control-label col-sm-5"><b>Instalasi Akhir</b></label>
                    <div class="col-sm-7">
                        <p><b>:</b>&nbsp;<?= $model->instalasi_nama ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="text-left control-label col-sm-5"><b>No pendaftaran</b></label>
                    <div class="col-sm-7">
                        <p><b>:</b>&nbsp;<?= $model->no_pendaftaran ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="text-left control-label col-sm-5"><b>Cara bayar</b></label>
                    <div class="col-sm-7">
                        <p><b>:</b>&nbsp;<?= $model->carabayar_nama ?></p>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-4">
                    <label class="text-left control-label col-sm-5"><b>Ruangan akhir</b></label>
                    <div class="col-sm-7">
                        <p><b>:</b>&nbsp;<?= $model->ruangan_nama ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="text-left control-label col-sm-5"><b>No rekam medis</b></label>
                    <div class="col-sm-7">
                        <p><b>:</b>&nbsp;<?= $model->no_rekam_medik ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="text-left control-label col-sm-5"><b>Penjamin</b></label>
                    <div class="col-sm-7">
                        <p><b>:</b>&nbsp;<?= $model->penjamin_nama ?></p>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-4">
                    <label class="text-left control-label col-sm-5"><b>Tanggal pendaftaran</b></label>
                    <div class="col-sm-7">
                        <p><b>:</b>&nbsp;<?= date('d-M-Y',strtotime($model->tgl_pendaftaran)) ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="text-left control-label col-sm-5"><b>Nama pasien</b></label>
                    <div class="col-sm-7">
                        <p><b>:</b>&nbsp;<?= $model->nama_pasien ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="text-left control-label col-sm-5"><b>Kelas pelayanan</b></label>
                    <div class="col-sm-7">
                        <p><b>:</b>&nbsp;<?= $model->kelaspelayanan_nama ?></p>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-4">
                    <label class="text-left control-label col-sm-5"><b>No telephon</b></label>
                    <div class="col-sm-7">
                        <p><b>:</b>&nbsp;<?= $model->no_mobile_pasien ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="text-left control-label col-sm-5"><b>Tanggal keluar</b></label>
                    <div class="col-sm-7">
                        <p><b>:</b>&nbsp;<?= $model->tglpasienpulang
                                ? date('d-M-Y',strtotime($model->tglpasienpulang))
                                : '-' ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-md-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h6 class="panel-title"><b><?= Yii::t('fe','Detail Transaksi') ?></b></h6>
        </div>
        <div class="panel-body">
            <br>
           <?php
                $form = ActiveForm::begin([
                    'id' => 'ajax-form',
                    'action' => '/kasir/pembayaran-tagihan/simpan?id='.$id_pendaftaran,
                    'enableAjaxValidation'=>false,
                    'enableClientValidation'=>false,
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => [
                        'labelSpan' => 3,
                        'deviceSize' => ActiveForm::SIZE_SMALL
                    ],
                    'options' => [
                        'skip-confirm' => "true"
                    ]
                ]);
            ?>
            <div class="form-group">
                <div class="col-md-4">
                    <label class="text-left control-label col-sm-5">
                        <b><?= $model->getAttributeLabel('tanggal_pembayaran') ?></b>
                    </label>
                    <div class="col-sm-7">
                    <?= $form->field($model, 'tanggal_pembayaran',[
                                    'addon' => ['append' => ['content'=>'<i class="fa fa-calendar"></i>']],
                                    'template' => '{input}',
                                    'options' => [
                                        'tag' => false
                                    ]
                                ])->textInput([
                                    'placeholder' => $model->getAttributeLabel('tanggal_pembayaran'),
                                    'class' => 'form-control input-sm date-picker'
                                ])->label(false); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="text-left control-label col-sm-5">
                        <b><?= $model->getAttributeLabel('jumlah_uangmuka') ?></b>
                    </label>
                    <div class="col-sm-7">
                        <?= $form->field($model, 'jumlah_uangmuka',[
                                        'addon' => ['prepend' => ['content'=>'Rp.']],
                                        'template' => '{input}',
                                        'options' => [
                                            'tag' => false
                                        ]
                                    ])->textInput([
                                        'placeholder' => $model->getAttributeLabel('jumlah_uangmuka'),
                                        'class' => 'form-control input-sm text-right doco-number',
                                        'autocomplete' => "off",
                                        'id' => 'jumlah_uangmuka',
                                        'readonly' => true
                                    ])->label(false); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="text-left control-label col-sm-5">
                        <b><?= $model->getAttributeLabel('pengguna_uang_muka') ?></b>
                    </label>
                    <div class="col-sm-7">
                    <?= $form->field($model, 'pengguna_uang_muka',[
                                    'addon' => ['prepend' => ['content'=>'Rp.']],
                                    'template' => '{input}',
                                    'options' => [
                                        'tag' => false
                                    ]
                                ])->textInput([
                                    'placeholder' => $model->getAttributeLabel('pengguna_uang_muka'),
                                    'class' => 'form-control input-sm text-right doco-number',
                                    'id' => 'pengguna_uang_muka',
                                    'autocomplete' => "off",
                                ])->label(false); ?>
                    </div>
                </div>
            </div>
            <hr>
            <div id="error_TagihanPasienFormdetail_tagihan"></div>
            <table id="tagihan-pelayanan"
            class="table table-striped table-condensed table-hover" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th><?=\Yii::t("fe", "No");?></th>
                        <th><?=\Yii::t("fe", "Tanggal Tindakan");?></th>
                        <th><?=\Yii::t("fe", "Instalasi");?></th>
                        <th><?=\Yii::t("fe", "Ruangan");?></th>
                        <th><?=\Yii::t("fe", "Nama Tindakan/Obat");?></th>
                        <th><?=\Yii::t("fe", "Cara Bayar");?></th>
                        <th><?=\Yii::t("fe", "Penjamin");?></th>
                        <th><?=\Yii::t("fe", "Harga Satuan");?>(Rp)</th>
                        <th><?=\Yii::t("fe", "Tarif Cyto");?>(Rp)</th>
                        <th><?=\Yii::t("fe", "Qty");?></th>
                        <th><?=\Yii::t("fe", "Sub Total");?>(Rp)</th>
                        <th><?=\Yii::t("fe", "Aksi");?></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
               <tr>
                    <td colspan="12" class="text-center row-default">
                        <?php
                            $id = DocoHelpers::encrypt($id_pendaftaran);
                        ?>
                        <button type="button"
                            class="addrow btn btn-info btn-labeled btn-xs"
                            data-toggle="modal"
                            data-original-title="Tambah"
                            data-popup="tooltip"
                            data-target="#modal_backdrop"
                            data-width="50%"
                            action=<?= "/kasir/transaksi-pelayanan-pasien/form-pelayanan?id={$id}&instalasi_terakhir={$model->instalasi_id}&ruangan_terakhir={$model->ruangan_id}" ?>>
                            <b><i class="fa fa-plus"></i></b>Tambah
                        </button>
                    </td>
                </tr>
                <tr>
                    <tr>
                        <td colspan="9" class="text-right"><h4>Total Tagihan</h4></td>
                        <td class="text-right" colspan="3">
                        <?= $form->field($model, 'total_tagihan',[
                                        'addon' => ['prepend' => ['content'=>'Rp.']],
                                        'template' => '{input}',
                                        'options' => [
                                            'tag' => false
                                        ]
                                    ])->textInput([
                                        'placeholder' => $model->getAttributeLabel('total_tagihan'),
                                        'class' => 'form-control input-sm text-right doco-number',
                                        'autocomplete' => "off",
                                        'readonly' => true
                                    ])->label(false); ?>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="4" class="text-right"><h4>Ditagihkan ke Pasien</h4></th>
                        <th class="text-right" colspan="2">
                            <h4>Rp. <nominal id="label-tagihan">0</nominal></h4>
                        <?= $form->field($model, 'tagihan_pasien')->textInput([
                                        'placeholder' => $model->getAttributeLabel('tagihan_pasien'),
                                        'class' => 'form-control input-sm text-right doco-number',
                                        'autocomplete' => "off",
                                        'id' => 'tagihan_pasien',
                                        'readonly' => true,
                                        'type' => 'hidden'
                                    ])->label(false); ?>
                        </th>
                        <th colspan="3" class="text-right"><h4>Subsidi Asuransi</h4></th>
                        <th class="text-right" colspan="3">
                        <?= $form->field($model, 'subsidi_asuransi',[
                                        'addon' => ['prepend' => ['content'=>'Rp.']],
                                        'template' => '{input}',
                                        'options' => [
                                            'tag' => false
                                        ]
                                    ])->textInput([
                                        'placeholder' => $model->getAttributeLabel('subsidi_asuransi'),
                                        'class' => 'form-control input-sm text-right doco-number',
                                        'autocomplete' => "off",
                                        'id' => 'subsidi-asuransi',
                                        'readonly' => true
                                    ])->label(false); ?>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="4" class="text-right"><h4>Pembulatan</h4></th>
                        <th class="text-right" colspan="2">
                            <h4>Rp. <nominal id="label-pembulatan"><?= isset($header['total_pembulatan'])
                                ? DocoHelpers::formatNumber($header['total_pembulatan']) : 0;
                            ?></nominal></h4>
                        <?= $form->field($model, 'pembulatan')->textInput([
                                        'placeholder' => $model->getAttributeLabel('pembulatan'),
                                        'class' => 'form-control input-sm text-right doco-number',
                                        'autocomplete' => "off",
                                        'id' => 'pembulatan',
                                        'readonly' => true,
                                        'type' => 'hidden'
                                    ])->label(false); ?>
                        </th>
                        <th colspan="3" class="text-right"><h4>Biaya Administrasi</h4></th>
                        <th class="text-right" colspan="3">
                        <?= $form->field($model, 'biaya_administrasi',[
                                        'addon' => ['prepend' => ['content'=>'Rp.']],
                                        'template' => '{input}',
                                        'options' => [
                                            'tag' => false
                                        ]
                                    ])->textInput([
                                        'placeholder' => $model->getAttributeLabel('biaya_administrasi'),
                                        'class' => 'form-control input-sm text-right doco-number',
                                        'id' => 'biaya_administrasi',
                                        'autocomplete' => "off",
                                    ])->label(false); ?>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="4" class="text-right"><h4>Uang Kembalian</h4></th>
                        <th class="text-right" colspan="2">
                            <h4>Rp. <nominal id="label-kembalian">0
                            </nominal></h4>
                        <?= $form->field($model, 'uang_kembalian')->textInput([
                                        'placeholder' => $model->getAttributeLabel('uang_kembalian'),
                                        'class' => 'form-control input-sm text-right doco-number',
                                        'autocomplete' => "off",
                                        'id' => "uang_kembalian",
                                        'readonly' => true,
                                        'type' => 'hidden'
                                    ])->label(false); ?>
                        </th>
                        <th colspan="3" class="text-right"><h4>Uang Diterima</h4></th>
                        <th class="text-right" colspan="3">
                        <?= $form->field($model, 'uang_diterima',[
                                        'addon' => ['prepend' => ['content'=>'Rp.']],
                                        'template' => '{input}',
                                        'options' => [
                                            'tag' => false
                                        ]
                                    ])->textInput([
                                        'placeholder' => $model->getAttributeLabel('uang_diterima'),
                                        'class' => 'form-control input-sm text-right doco-number',
                                        'autocomplete' => "off",
                                        'id' => 'uang-diterima'
                                    ])->label(false); ?>
                        </th>
                    </tr>
                </tr>
            </table>
            <hr>
        </div>
    </div>
</div>
<?php
    $data = [];
    if (!empty($model->detail_tagihan)) :
        $no = 1;
        foreach ($model->detail_tagihan as $value) :
            $is_paket = ($value['kelompoktindakan_nama'] == 'kelompok_paket')
                            ? true : false;
            $data[] = [
                'no' => $no,
                'penjamin_pelayanan_id' => $value['penjamin_pelayanan_id'],
                'carabayar_pelayanan_id' => $value['carabayar_pelayanan_id'],
                'tindakan_obat_id' => $value['tindakan_obat_id'],
                'tgl_pelayanan' => date('d M Y',strtotime($value['tgl_pelayanan'])),
                'instalasi_pelayanan' => $value['instalasi_pelayanan'],
                'ruangan_pelayanan' => $value['ruangan_pelayanan'],
                'tindakan_obat_nama' => $value['tindakan_obat_nama'],
                'carabayar_pelayanan' => $value['carabayar_pelayanan'],
                'penjamin_pelayanan' => $value['penjamin_pelayanan'],
                'action' => '<i class="fa fa-lock"></i>',
                'is_obat' => $value['is_obat'],
                'is_paket' => $is_paket,
                'pelayanan_id' => $value['pelayanan_id'],
                'tarif_satuan_label' => DocoHelpers::formatNumber($value['tarif_satuan']),
                'qty_label' => DocoHelpers::formatNumber($value['qty']),
                'tarif_cyto_label' => DocoHelpers::formatNumber($value['tarif_cyto']),
                'sub_total_label' => DocoHelpers::formatNumber($value['sub_total']),
                'tarif_satuan' => $value['tarif_satuan'],
                'qty' => $value['qty'],
                'tarif_cyto' => $value['tarif_cyto'],
                'sub_total' => $value['sub_total'],
                'is_flag' => false

            ];
            $no++;
        endforeach;
    endif;
    $_data = json_encode($data);
    $_info = json_encode($model->attributes);
    $curentUrl = Yii::$app->request->url;
    $isPembulatan = isset($konfigSistem['is_pembulatankeatas'])
            ? $konfigSistem['is_pembulatankeatas'] : null;
    $satuan = isset($konfigSistem['satuanpembulatan'])
            ? $konfigSistem['satuanpembulatan'] : 0;
?>
<script type="text/javascript">
    var _jsonData = <?= $_data ?>;
    var _id = '<?= $id ?>';
    var _info = <?= $_info ?>;
    var _tabel;
    var _listTindakan = {
        'obat' : {},
        'paket' : {},
        'tindakan' : {}
    };
    var _jumUM = _totalTagihan = _subsidiAsuran = _oldPembayaran = 0;

    $(document).ready(function() {
        docoHelper.is_pembulatankeatas = '<?= $isPembulatan ?>';
        docoHelper.satuanpembulatan = '<?= $satuan ?>';

        _jumUM = docoHelper.convertToAngka($("#jumlah_uangmuka").val());
        _totalTagihan = docoHelper.convertToAngka($("#transaksipelayananform-total_tagihan").val());
        $('.doco-number').trigger('change');
        _tagihanPasien();

        _tabel = $('#tagihan-pelayanan').dataTable({
            data: _jsonData,
            ordering : false,
            paging: false,
            searching: false,
            columns: [
                {data: 'no'},
                {data: 'tgl_pelayanan'},
                {data: 'instalasi_pelayanan'},
                {data: 'ruangan_pelayanan'},
                {data: 'tindakan_obat_nama'},
                {data: 'carabayar_pelayanan'},
                {data: 'penjamin_pelayanan'},
                {data: 'tarif_satuan_label', className : 'text-right'},
                {data: 'tarif_cyto_label',className : 'text-right'},
                {data: 'qty_label', className : 'text-right'},
                {data: 'sub_total_label', className : 'text-right'},
                {data: 'action', className : 'text-center'},
            ],
            fnRowCallback : function (nRow, aData, index) {
                $('td:eq(0)',nRow).html(index + 1);
                return nRow;
            }
        });

        var d = new Date();
        $(".date-picker").pickadate({
            format: "dd-mmm-yyyy",
            formatSubmit: "yyyy-mm-dd",
            clear: false,
            min: [d.getFullYear(),d.getMonth(),d.getDate()],
            max: [d.getFullYear(),d.getMonth(),d.getDate()],
            onStart: function() {
                var date = new Date();
                this.set("select", [[date.getFullYear(), date.getMonth() + 1, date.getDate()]]);
            }
        });

    });

    /** Khusus untuk function **/

    var _tagihanPasien = function () {

        var _admin = docoHelper.convertToAngka($("#biaya_administrasi").val());
        var _uangMuka = docoHelper.convertToAngka($("#pengguna_uang_muka").val());
        var _angsulan = docoHelper.convertToAngka($("#uang_kembalian").val());
        var _asuransi = docoHelper.convertToAngka($("#subsidi-asuransi").val());
        var _uangTerima = docoHelper.convertToAngka($("#uang-diterima").val());
        var _jumUangMuka = docoHelper.convertToAngka($("#jumlah_uangmuka").val());

        var _total = _totalAsuranasi = 0;
        var _caraUmum = 0;
        _admin = parseInt(_admin);
        _uangMuka = parseInt(_uangMuka);

        $.each(_jsonData, function (x, y) {
            if (y.carabayar_pelayanan_id == 5) {
                _caraUmum++;
            } else {
                _totalAsuranasi += y.sub_total;
            }
            _total += y.sub_total;
        });
        _subsidiAsuran = _totalAsuranasi;
        $('#transaksipelayananform-total_tagihan').val(docoHelper.convertToRupiah(_total)).trigger('change');
        $('#subsidi-asuransi').val(docoHelper.convertToRupiah(_totalAsuranasi)).trigger('change');

        _totalTagihan = (_total + _admin) - _uangMuka;
        _oldPembayaran = _total;

        if (_caraUmum == 0) {
            $("#biaya_administrasi").attr("readonly",true);
            $("#uang-diterima").attr("readonly",true);
        } else {
            $("#biaya_administrasi").attr("readonly",false);
            $("#uang-diterima").attr("readonly",false);
        }
        _kembalian();
    }

    var _kembalian = function () {
        var _angsulan = docoHelper.convertToAngka($("#uang_kembalian").val());
        var _admin = docoHelper.convertToAngka($("#biaya_administrasi").val());
        var _asuransi = docoHelper.convertToAngka($("#subsidi-asuransi").val());
        var _uangTerima = docoHelper.convertToAngka($("#uang-diterima").val());
        var _uangMuka = docoHelper.convertToAngka($("#pengguna_uang_muka").val());
        var _jumUangMuka = docoHelper.convertToAngka($("#jumlah_uangmuka").val());
        var _tagAwal = docoHelper.convertToAngka($("#transaksipelayananform-total_tagihan").val());

        _uangMuka = isNaN(_uangMuka) ? 0 : parseInt(_uangMuka);
        _admin = isNaN(_admin) ? 0 : parseInt(_admin);
        _asuransi = isNaN(_asuransi) ? 0 : parseFloat(_asuransi);

        total = _jumUM > _uangMuka ? (_jumUM - _uangMuka) : 0;
        $("#jumlah_uangmuka").val(docoHelper.convertToRupiah(total));

        _totalTagihan = ((parseFloat(_tagAwal) + _admin) - _asuransi);

        if (_uangMuka > _oldPembayaran) {
            var _total = (_uangMuka - _totalTagihan > 0 ? _uangMuka - _totalTagihan : 0)
            $("#uang_kembalian").val(docoHelper.convertToRupiah(_total));
            $("#label-kembalian").html(docoHelper.convertToRupiah(_total));
            var _new = _totalTagihan - _uangMuka;
            $("#tagihan_pasien").val(docoHelper.convertToRupiah(_new > 0 ? _new : 0)).trigger("change");
            var _pembulatan = parseFloat($("#pembulatan").val());
            $("#uang-diterima").val(docoHelper.convertToRupiah(_new > 0 ? _new + _pembulatan : 0)).trigger("change");
            $("#label-tagihan").html(docoHelper.convertToRupiah(_new > 0 ? _new : 0)).trigger("change");

        } else {
            var _total = (_totalTagihan - _uangMuka > 0 ? _totalTagihan - _uangMuka : 0)
            $("#tagihan_pasien").val(docoHelper.convertToRupiah(_totalTagihan - _uangMuka)).trigger("change");
            var _pembulatan = parseFloat($("#pembulatan").val());
            $("#uang-diterima").val(docoHelper.convertToRupiah(_totalTagihan - _uangMuka + _pembulatan)).trigger("change");
            $("#label-tagihan").html(docoHelper.convertToRupiah(_totalTagihan - _uangMuka + _pembulatan)).trigger("change");
            $("#uang_kembalian").val(0);
            $("#label-kembalian").html(0);
        }
    }

    var _reOrderJson = function () {
        var _tmpJson = _jsonData;
        _jsonData = [];
        var _no = 0;
        $.each(_tmpJson, function (x, y) {
            if (typeof y != 'undefined') {
                _jsonData[_no] = y;
                _no++;
            }
        });
        _resetTabel();
    }

    var _resetTabel = function () {
        _tabel.dataTable().fnDestroy();
        _tabel.dataTable({
            data: _jsonData,
            ordering : false,
            paging: false,
            searching: false,
            columns: [
                {data: 'no'},
                {data: 'tgl_pelayanan'},
                {data: 'instalasi_pelayanan'},
                {data: 'ruangan_pelayanan'},
                {data: 'tindakan_obat_nama'},
                {data: 'carabayar_pelayanan'},
                {data: 'penjamin_pelayanan'},
                {data: 'tarif_satuan_label', className : 'text-right'},
                {data: 'tarif_cyto_label',className : 'text-right'},
                {data: 'qty_label', className : 'text-right'},
                {data: 'sub_total_label', className : 'text-right'},
                {data: 'action', className : 'text-center'},
            ],
            fnRowCallback : function (nRow, aData, index) {
                $('td:eq(0)',nRow).html(index + 1);
                return nRow;
            }
        });
    }



    $(document).on('click','.deleteRow',function (e) {
        e.preventDefault();
        var _typeBtn = $(this).attr('data-type');
        var _idBtn = $(this).attr('data-id');
        var _tr = $(this).closest('tr');
        var _rowIndex = _tr.index();
        switch (_typeBtn) {
            case 'tindakan':
                if (typeof _listTindakan.tindakan[_idBtn] != 'undefined') {
                    delete _listTindakan.tindakan[_idBtn];
                }
            break;
            case 'obat':
                if (typeof _listTindakan.obat[_idBtn] != 'undefined') {
                    delete _listTindakan.obat[_idBtn];
                }
            break;
            case 'paket':
                if (typeof _listTindakan.paket[_idBtn] != 'undefined') {
                    delete _listTindakan.paket[_idBtn];
                }
            break;
            default:
            break;
        }

        if (typeof _jsonData[_rowIndex] != 'undefined') {
            delete _jsonData[_rowIndex];
        }
        _reOrderJson();
        _tagihanPasien();
    })

    $('#biaya_administrasi').on('keyup',_tagihanPasien);

    $("#pengguna_uang_muka").on("keyup", function (event) {
        event.preventDefault();
        var _uangMuka = docoHelper.convertToAngka($("#jumlah_uangmuka").val());
        var _value = docoHelper.convertToAngka($(this).val());
        _jumUM = isNaN(_jumUM) ? 0 : _jumUM;
        if (_value > _jumUM) {
            $(this).val(docoHelper.convertToRupiah(_jumUM));
        } else {
            $(this).val(docoHelper.convertToRupiah(_value));
        }
        _kembalian();
    });

    $('#tagihan_pasien').on("change", function (event) {
        event.preventDefault();
        var _value = docoHelper.convertToAngka($(this).val());
        docoHelper.pembulatan(_value, $("#pembulatan"),$(this));
        $("#label-pembulatan").html(docoHelper.convertToRupiah($("#pembulatan").val()));
    });

    $('#uang-diterima').on("keyup", function (event) {
        event.preventDefault();
        var _value = docoHelper.convertToAngka($(this).val());
        var _totalTagihan = docoHelper.convertToAngka($("#tagihan_pasien").val());
        var _kembalian = 0;
        if (_value > _totalTagihan) {
            _kembalian = _value - _totalTagihan;
        }
        $("#uang_kembalian").val(_kembalian).trigger("change");
        $("#label-kembalian").html($("#uang_kembalian").val());
    });


</script>