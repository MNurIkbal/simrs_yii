<?php

/**
 * @author Yaya
 * @copyright 23 March 2018 
 */

use yii\web\View;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\typeahead\Typeahead;
use app\components\DocoHelpers;
use kartik\datetime\DateTimePicker;

$this->title = $title;
$this->params['breadcrumbs'][] = [
    'label' => Yii::$app->docoVars->workspace("modul_alias"), 
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;
?>
<style type="text/css">
    .dataTables_scroll{
        min-height: 102px;
    }
    .kv-datetime-picker{
        display : none;
    }
    .kv-datetime-remove{
        border-right: 1px solid #ddd !important;
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
                    <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
            </div>
                <div class="panel-toolbar clearfix">
                    <?=DocoHelpers::generateToolbar([
                        'back',
                        'save' => [
                            'attributes' => [
                                'onClick' => ''
                            ]
                        ],
                        'pdf' => [
                            'attributes' => [
                                'data-target' => "/penjamin-asuransi/informasi-alokasi-pembayaran/cetak-transaksi?id=$id&"
                            ]
                        ],
                        // 'excel' => [
                        //     'attributes' => [
                        //         'data-target' => '/penjamin-asuransi/informasi-alokasi-pembayaran/export-excel?'
                        //     ]
                        // ],
                    ]);?>
                </div>
                <div class="panel-body">
                    <div class="col-md-12 info-pengajuan">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h6 class="panel-title"><b><?= Yii::t('fe', 'Informasi Pengajuan'); ?></b></h6>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-sm-5"><b>No Pengajuan </b></label>
                                        <div class="col-sm-7">:
                                            <?= isset($header['no_pengajuanklaim']) ? $header['no_pengajuanklaim'] : null ?>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-sm-5"><b>Cara Bayar </b></label>
                                        <div class="col-sm-7">:
                                            <?= isset($header['carabayar_nama']) ? $header['carabayar_nama'] : null ?>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-sm-5"><b>Penjamin </b></label>
                                        <div class="col-sm-7">:
                                            <?= isset($header['penjamin_nama']) ? $header['penjamin_nama'] : null ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-sm-5"><b>Instalasi :</b></label>
                                        <div class="col-sm-7">:
                                            <?= isset($header['instalasi_nama']) ? $header['instalasi_nama'] : null ?>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label class="control-label col-sm-5"><b>Ruangan </b></label>
                                        <div class="col-sm-7">:
                                            <?= isset($header['ruangan_nama']) ? $header['ruangan_nama'] : null ?>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                    <form id="form-penerimaan-pembayaran">
                                        <div class="row">
                                            <div class="form-group col-md-12">
                                                <label class="control-label col-md-2">
                                                    <b><?=$model->attributeLabels()['tanggal_pembayaran']?></b>
                                                </label>
                                                <div class="col-sm-3">
                                                <?=DateTimePicker::widget([
                                                        'model' => $header,
                                                        'attribute' => 'tanggal_pembayaran',
                                                        'type' => DateTimePicker::TYPE_COMPONENT_APPEND ,
                                                        'readonly' => true,
                                                        'value' => isset($header['tgl_pembayaranalokasi']) ? date('Y-m-d H:i:s',strtotime($header['tgl_pembayaranalokasi'])) : null,
                                                        'name' => "TransaksiAlokasiForm[tanggal_pembayaran]",
                                                        'id' => 'transaksialokasiform-tanggal_pembayaran',
                                                        'convertFormat' => true,
                                                        'pluginOptions' => [
                                                            'disabled' => true,
                                                            'format' => 'yyyy-MM-dd hh:mm:ss',
                                                            'timePicker24Hour'=>true,
                                                            'startDate' => date('Y-m-d H:i:s',strtotime('-2 minute')),
                                                            'autoclose' => true,
                                                            'minuteStep' => 1,
                                                            'endDate' => date('Y-m-d H:i:s')
                                                        ]
                                                    ]);
                                                    ?>
                                                    <div class="help-block"></div>
                                                </div>
                                                <label class="control-label col-md-2">
                                                    <b><?=$model->attributeLabels()['total_pengajuan']?></b>
                                                </label>
                                                <div class="col-sm-3">
                                                    <div class="input-group">
                                                        <span class="input-group-addon" title="Select date &amp; time">
                                                            <span>Rp.</span>
                                                        </span>
                                                        <?=
                                                            Html::activeTextInput($model, 'total_pengajuan',[
                                                                'class' => 'form-control doco-number text-right',
                                                                'readonly' => true
                                                            ])
                                                        ?>
                                                        <div class="help-block"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-12">
                                                <label class="control-label col-md-2 required">
                                                    <b><?=$model->attributeLabels()['no_pembayaran']?></b>
                                                </label>
                                                <div class="col-sm-3">
                                                    <?= Html::activeDropdownList($model, 'no_pembayaran', [
                                                            1 => isset($header['no_terimabayarklaim']) ? $header['no_terimabayarklaim'] : null
                                                        ], [
                                                            'class' => 'form-control select2 no_pembayaran', 
                                                            'prompt' => Yii::t('fe', 'Pilih'),
                                                            'disabled' => true,
                                                            'value' => 1,
                                                            'title' => isset($header['no_terimabayarklaim']) ? $header['no_terimabayarklaim'] : 'none'
                                                        ]) ?>
                                                    <div class="help-block"></div>
                                                </div>
                                                <label class="control-label col-md-2">
                                                    <b><?=$model->attributeLabels()['total_terbayar']?></b>
                                                </label>
                                                <div class="col-sm-3">
                                                    <div class="input-group">
                                                        <span class="input-group-addon" title="Select date &amp; time">
                                                            <span>Rp.</span>
                                                        </span>
                                                        <?=
                                                            Html::activeTextInput($model, 'total_terbayar',[
                                                                'class' => 'form-control doco-number text-right',
                                                                'readonly' => true
                                                            ])
                                                        ?>
                                                        <div class="help-block"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-12">
                                                <label class="control-label col-md-2 required">
                                                    <b><?=$model->attributeLabels()['jumlah_pembayaran']?></b>
                                                </label>
                                                <div class="col-sm-3">
                                                    <div class="input-group">
                                                        <span class="input-group-addon" title="Select date &amp; time">
                                                            <span>Rp.</span>
                                                        </span>
                                                        <?=
                                                            Html::activeTextInput($model, 'jumlah_pembayaran',[
                                                                'class' => 'form-control doco-number text-right',
                                                                'readonly' => true
                                                            ])
                                                        ?>
                                                        <div class="help-block"></div>
                                                    </div>
                                                </div>
                                                <label class="control-label col-md-2">
                                                    <b><?=$model->attributeLabels()['sisa_piutang']?></b>
                                                </label>
                                                <div class="col-sm-3">
                                                    <div class="input-group">
                                                        <span class="input-group-addon" title="Select date &amp; time">
                                                            <span>Rp.</span>
                                                        </span>
                                                        <?=
                                                            Html::activeTextInput($model, 'sisa_piutang',[
                                                                'class' => 'form-control doco-number text-right',
                                                                'readonly' => true
                                                            ])
                                                        ?>
                                                        <div class="help-block"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-12">
                                                <label class="control-label col-md-2">
                                                    <b><?=$model->attributeLabels()['catatan']?></b>
                                                </label>
                                                <div class="col-sm-3">
                                                    <?=
                                                        Html::activeTextarea($model, 'catatan',[
                                                            'class' => 'form-control',
                                                            'rows' => 6,
                                                            'value' => isset($header['catatan']) ? $header['catatan'] : null
                                                        ])
                                                    ?>
                                                    <div class="help-block"></div>
                                                </div>
                                                <div class="col-md-6 text-center">
                                                        <b>
                                                            <h3 style="margin-top : 49px !important;">Total Alokasi Pembayaran : Rp. 
                                                                <nominal id="total-bayar">0</nominal>
                                                            </h3>
                                                        </b>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <h6>Alokasi Pembayaran</h6>
                                    <hr>
                                    <table id="example" class="table table-striped table-condensed table-hover" style="width:100%">
                                        <thead>
                                            <tr class="bg-inverse">
                                                <th class="select-checkbox"></th>
                                                <th>No</th>
                                                <th>Data Pasien</th>
                                                <th><?=\Yii::t("fe", "No Invoice");?></th>
                                                <th><?=\Yii::t("fe", "No SEP");?></th>
                                                <th><?=\Yii::t("fe", "Tanggal Masuk");?></th>
                                                <th><?=\Yii::t("fe", "Tanggal Keluar");?></th>
                                                <th>Instalasi / Ruangan</th>
                                                <th><?=\Yii::t("fe", "Tagihan");?></th>
                                                <th><?=\Yii::t("fe", "Jumlah Pasien Bayar");?></th>
                                                <th><?=\Yii::t("fe", "Piutang");?></th>
                                                <th><?=\Yii::t("fe", "Piutang (Telah Bayar)");?></th>
                                                <th width="15%"><?=\Yii::t("fe", "Jumlah Bayar");?></th>
                                                <th><?=\Yii::t("fe", "Sisa Tagihan");?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-center" colspan="19"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>

<?php 
$this->registerJs("
    var _cacheAlokasi = {};
    var _totalPembayaran = 0;
    var _rawDetail = [];                                                    
    var date = new Date();

    var dateStr =
    date.getFullYear() + `-` +
    (`00` + (date.getMonth() + 1)).slice(-2) + `-` +
    (`00` + date.getDate()).slice(-2) + ` ` +
    (`00` + date.getHours()).slice(-2) + `:` +
    (`00` + date.getMinutes()).slice(-2) + `:` +
    (`00` + date.getTime()).slice(-2);

    var _countTotalbayar = function () {
        var _target = $('#total-bayar');
        var _total = 0;
        $.each(_cacheAlokasi, function (key, val) {
           _total += val.jumlah_bayar;
           _totalPembayaran = _total;
        });
        _target.html(docoHelper.convertToRupiah(_total));
    }

    $(document).on('click','.data-save', function (event) {
        event.preventDefault();
        var _data = $('#form-penerimaan-pembayaran').serializeArray();
        _data.push({
            name : 'data_alokasi',
            value : JSON.stringify(_cacheAlokasi)
        });
        _data.push({
            name : 'total_alokasi',
            value : JSON.stringify(_totalPembayaran)
        });
        $().docoForm('click',{
            url : '/penjamin-asuransi/informasi-alokasi-pembayaran/save?id=$id',
            data : _data,
            success : function (data) {
                $('.data-reset').trigger('click');
            }
        });
    });

    $(document).on('click', '#example tbody tr', function (event) {
        var _data = _tabel.row(this).data();
        var _idDetail = _data.pembayaranalokasidetail_id;
        var _sisaPiutang = _data.jumlah_sisapiutang;
        var _bayar_alokasi = parseInt(_data.bayar_alokasi);
        var _nominal = 0;
        if ($(this).hasClass('selected')) {
            if (typeof _cacheAlokasi[_idDetail] != 'undefined') {
                _bayar_alokasi = _cacheAlokasi[_idDetail].jumlah_bayar;
            }
            $(this).find('input').prop('disabled', false);
            $(this).find('input').val(_bayar_alokasi ? _bayar_alokasi : _sisaPiutang).trigger('keyup');
            return true;
        }
        $(this).find('input').prop('disabled', true);
        $(this).find('input').val(0).trigger('keyup');
    });

    $(document).on('keyup', '#example .total-alokasi', function(event){
        event.preventDefault();
        var _parent = $(this).closest('tr');
        var result = _tabel.row($(_parent)).data();
        var _nominal = docoHelper.convertToAngka($(this).val());
        var _idParent = result.pembayaranalokasidetail_id;
        if (_nominal > result.sisa_piutang) {
            _nominal = result.sisa_piutang;
            $(this).val(result.sisa_piutang).trigger('change');
        }
        if (typeof _cacheAlokasi[_idParent] == 'undefined') {
            _cacheAlokasi[_idParent] = {
                pendaftaran_id : result.pendaftaran_id,
                pasienadmisi_id : result.pasienadmisi_id,
                pasien_id : result.pasien_id,
                jumlah_piutang : result.piutang,
                jumlah_telahbayar : result.bayar,
                jumlah_bayar : 0,
                bayar_alokasi : result.bayar_alokasi,
            }
        }
        _cacheAlokasi[_idParent].jumlah_bayar = _nominal;
        _countTotalbayar();
    });
    showLoader();
    $(document).ready(function() {
        var _no_pembayaran = $('.no_pembayaran').attr('title');
        $.ajax({
            url: '/penjamin-asuransi/informasi-alokasi-pembayaran/get-pembayaran',
            data: {no_pembayaran : _no_pembayaran},
            success: function(data){
                $('#transaksialokasiform-tanggal_pembayaran').datetimepicker({
                    format : 'yyyy-mm-dd hh:ii:ss',
                    minuteStep : 1,
                    timePicker24Hour : true,
                    startDate :  data[0].tgl_terimabayarklaim,
                    autoclose : false,
                    endDate : dateStr,
                });
                var datepicker= document.getElementsByClassName('datetimepicker');
                if(datepicker.length > 1) {
                    document.getElementsByClassName('datetimepicker')[0].remove();
                }
                $(document).on('click', function(e) {
                    if (e.target.id !== 'transaksialokasiform-tanggal_pembayaran') {
                        document.getElementsByClassName('datetimepicker')[0].style.display = 'none';
                    } 
                })
                $('#transaksialokasiform-tanggal_pembayaran').on('click', function(){
                    document.getElementsByClassName('datetimepicker')[0].style.display = 'block';
                });

                _tabel = $('#example').docoTabel({
                    filter: true,
                    columnDefs: [
                        {
                            targets: 13,
                            width : '35%'
        
                        },
                        {
                            orderable: false,
                            className: 'select-checkbox',
                            targets: 0
                        }
                    ],
                    select: {
                        style:    'multiple',
                        selector: 'tr td:not(:nth-child(13))'
                    },
                    sorting: [[3, 'desc']], 
                    displayLength: 10,
                    processing: true,
                    serverSide: true,
                    scrollX: true,
                    scrollY: 550,
                    deferRender: true,
                    scrollCollapse: true,
                    scroller: true,
                    scroller: {
                        loadingIndicator: true
                    },
                    ajax: '/penjamin-asuransi/informasi-alokasi-pembayaran/get-detail-alokasi?id=$id',
                    columns: [
                        {
                            data: null,
                            searchable: false,
                            orderable: false,
                            defaultContent: '',
                        },
                        {
                            title: 'No',
                            data: 'rowNum',
                            searchable: false,
                            orderable: false
                        },
                        {
                            title: 'Data Pasien',
                            orderable: false,
                            render: (data, type, row, meta) => {
                                let namaPasien = row.nama_pasien;
                                namaPasien = `<b>` + namaPasien + `</b>`;
                                let noPendaftaran = row.no_pendaftaran;
                                const renderText = namaPasien + ` <br/>` + noPendaftaran;
                                return renderText;
                            }
                        },
                        {
                            title: 'No Invoice', 
                            data: 'no_pembayaran'
                        },
                        {
                            title: 'No SEP', 
                            data: 'nosep'
                        },
                        {
                            title: 'Tanggal Masuk', 
                            data: 'tgl_pendaftaran'
                        },
                        {
                            title: 'Tanggal Keluar', 
                            data: 'tglpasienpulang'
                        },
                        {
                            title: 'Instalasi / Ruangan', 
                            orderable: false,
                            render: (data, type, row, meta) => {
                                let instalasi = row.instalasi_nama;
                                instalasi = `<b>` + instalasi + `</b>`;
                                let ruangan = row.ruangan_nama;
                                const renderText = instalasi + ` / <br/>` + ruangan;
                                return renderText;
                            }
                        },
                        {
                            title: 'Tagihan (Rp.)', 
                            data: 'total_tagihan',
                            orderable: false,
                            className : 'text-right'
                        },
                        {
                            title: 'Jumlah Pasien Bayar (Rp.)', 
                            data: 'jumlah_telahbayar',
                            orderable: false,
                            className : 'text-right'
                        },
                        {
                            title: 'Piutang (Rp.)', 
                            data: 'jumlah_piutang',
                            orderable: false,
                            className : 'text-right'
                        },
                        {
                            title: 'Piutang (Telah Bayar) (Rp.)', 
                            data: 'jumlah_bayar',
                            orderable: false,
                            className : 'text-right'
                        },
                        {
                            title: '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jumlah Bayar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (Rp.)', 
                            data: 'label_bayar',
                            searchable: false,
                            orderable: false,
                            className : 'text-right'
                        },
                        {
                            title: 'Sisa Tagihan (Rp.)', 
                            data: 'jumlah_sisapiutang',
                            orderable: false,
                            className : 'text-right'
                        },
                    ],
                    drawCallback : function (setting) {
                        var api = this.api();
                        var dataRows = api.rows( {page:'current'} ).data();
                        var tr = $(this);
                        $.each(dataRows, function (key, val) {
                            var _primary = val.pembayaranalokasidetail_id;
                            var _bayar_alokasi = parseInt(val.bayar_alokasi);
                            var _cache_jum_bayar = 0;
                            if (typeof _cacheAlokasi[_primary] != 'undefined') {
                                _cache_jum_bayar = _cacheAlokasi[_primary].jumlah_bayar;
                                $($('tbody > tr:eq('+ key +')',tr))
                                        .find('.total-alokasi')
                                        .val(_cache_jum_bayar);
                            } else {
                                _cacheAlokasi[_primary] = {
                                    pendaftaran_id : val.pendaftaran_id,
                                    pasienadmisi_id : val.pasienadmisi_id,
                                    pasien_id : val.pasien_id,
                                    jumlah_piutang : val.piutang,
                                    jumlah_telahbayar : val.bayar,
                                    jumlah_bayar : _bayar_alokasi,
                                    bayar_alokasi : val.bayar_alokasi,
                                }
                            }
                            if (_bayar_alokasi > 0 || _cache_jum_bayar > 0) {
                               _tabel.row(':eq('+key+')').select();
                               $('tbody > tr:eq('+ key +')',tr).trigger('click');
                            }
                        })
                        $('.doco-number').trigger('change');
                        _countTotalbayar();
                        hideLoader();
                    }
                });
            }
        })
        
        $('.doco-number').trigger('change');
        $('.dataTables_filter').hide();
    });
",View::POS_END, 'b-index');
?>
