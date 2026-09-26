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
use kartik\widgets\TimePicker;
use kartik\widgets\DatePicker;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"), 'url' => []];
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', $title)];

?>
<style lang="">
    .datepicker>div{
        display:block;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
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
                <?= Html::button('<b><i class="fa fa-floppy-o"></i></b>'.Yii::t('fe', ' Simpan'), 
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'id' => 'simpan'
                    ]);
                ?>
            </div>
            <div class="panel-body">
                <?php 
                    $form = ActiveForm::begin([
                        'id' => 'ajax-form', 
                        'action' => '/bankdarah/inf-pemesanan-darah/set-list-item-penerimaan',
                        'enableAjaxValidation'=>false, 
                        'enableClientValidation'=>false,
                        'action' => "/bankdarah/inf-pemesanan-darah/save-penerimaan",
                        'type' => ActiveForm::TYPE_HORIZONTAL,
                        'formConfig' => [
                            'labelSpan' => 3, 
                            'deviceSize' => ActiveForm::SIZE_SMALL
                        ],
                        'options' => [
                            'skip-confirm' => "true"
                        ]
                    ]); 

                    echo Html::hiddenInput('TerimaDarahPmiForm[total_harga]', null, [
                        'class' => 'total_harga'
                    ]);

                    echo Html::hiddenInput('TerimaDarahPmiForm[pesandarahpmi_id]', $id, [
                        'class' => 'pesandarahpmi_id'
                    ]);

                    echo Html::hiddenInput('TerimaDarahPmiForm[total_kantongdarah]', null, [
                        'class' => 'total_kantongdarah'
                    ]);
                ?>
                <br>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-4">
                            <?= $form->field($model, 'no_pesandarahpmi', [
                            'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-3 text-bold',
                                    'wrapper' => 'col-md-5'
                                ]
                            ])->staticInput(['class' => 'no_pesandarahpmi'])
                            ->label(Yii::t('fe', 'No Pemesanan')); ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'nama_pmi', [
                            'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-3 text-bold',
                                    'wrapper' => 'col-md-5'
                                ]
                            ])->staticInput(['class' => 'nama_pmi'])
                            ->label(Yii::t('fe', 'Nama PMI')); ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'no_tlp', [
                            'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-3 text-bold',
                                    'wrapper' => 'col-md-5'
                                ]
                            ])->staticInput(['class' => 'no_tlp'])
                            ->label(Yii::t('fe', 'No Telepon')); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <?= $form->field($model, 'supplier_alamat', [
                            'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-3 text-bold',
                                    'wrapper' => 'col-md-5'
                                ]
                            ])->staticInput(['class' => 'supplier_alamat'])
                            ->label(Yii::t('fe', 'Alamat')); ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'penerima_id', [
                            'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-3 text-bold',
                                'wrapper' => 'col-md-8'
                            ]
                            ])->dropDownList([],[
                                'class' => '',
                                'id' => 'penerima_id',
                                'prompt' => Yii::t('fe', 'Petugas Penerima')
                            ]); ?>
                        </div>
                    </div>
                    <br><br><hr>
                    <div class="row">
                    	<table id="penerimaan" class="table table-striped table-condensed table-hover" style="width:100%">
		                    <thead>
		                        <tr class="bg-inverse">
		                            <th width="1">No</th>
		                            <th><?=\Yii::t("fe", "Jenis Darah");?></th>
		                            <th><?=\Yii::t("fe", "Golongan Darah & Rhesus");?></th>
		                            <th><?=\Yii::t("fe", "Tanggal Permintaan di Kirim");?></th>
		                            <th><?=\Yii::t("fe", "Jumlah Pemesanan");?></th>
		                            <th><?=\Yii::t("fe", "Telah Diterima Sebelumnya");?></th>
		                            <th><?=\Yii::t("fe", "Nomor Kantong Yang Diterima");?></th>
		                            <th width="20%"><?=\Yii::t("fe", "Tanggal Pengambilan Darah");?></th>
		                            <th><?=\Yii::t("fe", "Harga (Rp.)");?></th>
		                            <th><?=\Yii::t("fe", "Aksi");?></th>
		                        </tr>
		                    </thead>
		                    <tbody>
		                        <?php 
		                        $listCache = [];
		                        if(!empty($detail)) : 
		                        	$no = 1;
		                        	foreach ($detail as $key => $value) : 
                                        if($value['qty_pesan'] == $value['qty_diterima']) continue;
		                        		$listCache[$value['jenisdarah_id']]["row-{$no}"] = [
                                            'pesandarahpmi_id' => $value['pesandarahpmi_id'],
                                            'pesandarahpmidetail_id' => $value['pesandarahpmidetail_id'],
                                            'jenisdarah_id' => $value['jenisdarah_id'],
                                            'golongandarah_id' => $value['golongandarah_id'],
                                            'jenisdarah_nama' => $value['jenisdarah_nama'],
                                            'golongandarah_nama' => $value['golongandarah_nama'],
                                            'rhesus' => $value['rhesus'],
                                            'tgl_mintakirim' => $value['tgl_mintakirim'],
                                            'qty_pesan' => $value['qty_pesan'],
                                            'qty_diterima' => $value['qty_diterima'],
                                            'harga_satuan' => $value['harga_satuan'],
                                            'no_kantongdarah' => null,
                                            'tgl_pengambilan' => date('d-M-Y'),
                                        ];

		                        ?>
								<tr data-id="<?= $value['jenisdarah_id'] ?>" data-key=<?= $no?> data-last="0">
									<td><?= $no ?></td>
									<td><?= $value['jenisdarah_nama'] ?></td>
									<td><?= $value['golongandarah_nama'].' / '.$value['rhesus'] ?></td>
									<td><?= date("j M Y", strtotime($value['tgl_mintakirim'])) ?></td>
									<td style="text-align: right;"><?= $value['qty_pesan'] ?></td>
									<td style="text-align: right;"><?= is_null($value['qty_diterima']) ? 0 : $value['qty_diterima']  ?></td>
									<td>
										<?=
                                            Html::textInput('TerimaDarahPmiDetailForm[no_kantongdarah-row-'.$no.']',null,[
                                                'class' => 'form-control no_kantongdarah',
                                            ])
                                        ?>
									</td>
									<td>
										<?=
                                            DatePicker::widget([
                                                'name' => 'tgl_pengambilan',
                                                'value' => date('d-M-Y'),
                                                'type' => DatePicker::TYPE_INPUT,
                                                'language' => 'en',
                                                'options' => [
                                                    'class' => 'form-control date-kartik',
                                                    'readonly' => true,
                                                ],
                                                'pluginOptions' => [
                                                    'autoclose'=>true,
                                                    'format' => 'dd-M-yyyy',
                                                    'endDate' => "0d",
                                                ]
                                            ]);
                                        ?>
                                        <div id="error_TerimaDarahPmiForm<?= $value['jenisdarah_id'] ?>date-row-0"
                                            class="error-parent"></div>
									</td>
									<td><?= DocoHelpers::formatNumber($value['harga_satuan']) ?>
									</td>
									<td>
										<button type="button" class="addrow btn btn-info btn-sm btn-custom" style="padding-left:8px !important;">
                                            <span class="fa fa-plus"></span>
                                        </button>
									</td>
								</tr>
		                        <?php $no++; endforeach; endif; ?>
		                    </tbody>
		                </table>
                    </div>
                </div>
                
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php
$listCache = json_encode($listCache);
$dateNow = date('d-M-Y');
$this->registerJs('
    var table;
    var attributes = {};
    var pesandarahpmi_id = "'.$id.'";
	var _cache = '. $listCache .';
    var dateNow = "'.$dateNow.'";
    
    $("#ajax-form").submit(function(event){
        event.preventDefault();
        var _value = $(this).serializeArray();
        if (Object.keys(attributes).length) {
            $.each(attributes, function (key, val) {
                _value.push({
                    name : key,
                    value : val
                });
            });
        } 
        $(this).docoForm("submit",{
            data : _value,
            success : function (data) {
                $("#jenisdarah_id").val("").trigger("change");
                $("#jumlah").val(1).trigger("change");
                $("#pesandarahpmidetailform-wkt_mintakirim").val("").trigger("change");
                $(".wkt_mintakirim").val("").trigger("change");
                table.draw();
            }
        });
    });
    
    $(document).on(\'click\',\'.delete\', function(event) {
        event.preventDefault();
        $(this).docoForm(\'delete\',{
            skipConfirm: true,
            success : function (data) {
                table.draw();
            }
        });
    });
    
    $(document).ready(function(){
		$("#penerima_id").select2({
	        placeholder: "Pilih Petugas Penerima",
	        minimumInputLength: 3, 
	        ajax : {
	            url: "/bankdarah/inf-pemesanan-darah/search-petugas",
	            dataType: "json",
	            quietMillis: 250,
	            data: function (params) {
	              var query = {
	                search: params,
	              }
	              return params; 
	            },
	            processResults: function (data) {
	              return {
	                results: data.result
	              };
	            },
	            dropdownCssClass: "bigdrop",
	            escapeMarkup: function (m) { return m; },
	        },
	    });
    })
    
    $("#simpan").on("click",function (event) {
        event.preventDefault();
        var _penerima_id = $("#penerima_id").val();
        var dataPost = $("#ajax-form").serializeArray();
        dataPost.push({
            name : "data_detail",
            value : JSON.stringify(_cache)
        });
        dataPost.push({
            name : "penerima_id",
            value : _penerima_id
        });
		
        $(this).docoForm("click",{
        	url : $("#ajax-form").attr("action"),
        	data : dataPost,
            success : function (data) {
                var no_terimadarahpmi = data.response.no_terimadarahpmi;
                var urlCetak = "/bankdarah/inf-pemesanan-darah/cetak-penerimaan?no_terimadarahpmi="+no_terimadarahpmi;

                $("#penerima_id").val("").trigger("change");
                $(".no_pesandarahpmi").text("");
                $(".supplier_alamat").text("");
                $(".nama_pmi").text("");
                $(".no_tlp").text("");

                (new PNotify({
                    title: "Berhasil",
                    text: "Data Berhasil di Simpan dengan Nomor Penerimaan Darah PMI " + "<strong>" + no_terimadarahpmi + "</strong>" + " , Apakah Anda Ingin Mencetak Bukti Penerimaan Darah PMI?",
                    addclass: "alert alert-success alert-arrow-right alert-styled-right",
                    type: "success",
                    buttons: {
                        closer: false,
                        sticker: false
                    },
                    hide: false,
                    confirm: {
                        confirm: true,
                        buttons: [
                            {
                                text: "Ya",
                                addClass: "btn btn-xs btn-success",
                            },
                            {
                                text: "Tidak",
                                addClass: "btn btn-xs btn-danger",
                            }
                        ]
                    },
                    history: {
                        history: false
                    }
                })).get().on("pnotify.confirm", function() {
                    window.open(urlCetak);
                }).on("pnotify.cancel", function() {

                });
            }
        });
    });
    
    $(".addrow").on("click", function(e) {
        e.preventDefault();
        var _parent = $(this).closest("tr");
        var _id = parseInt(_parent.attr("data-id"));
        var _key = parseInt(_parent.attr("data-key"));
        var _last = parseInt(_parent.attr("data-last"));
        var _clone = _parent.clone();
        var _tr = $("<tr></tr>");
        _length = 0;
        
        if (typeof  _cache[_id]["row-"+_key] != "undefined") {
            _last++;
            var _dP = _cache[_id]["row-"+_key];
            var _data = {
                pesandarahpmi_id : _dP.pesandarahpmi_id,
                golongandarah_nama : _dP.golongandarah_nama,
                jenisdarah_nama : _dP.jenisdarah_nama,
                harga_satuan : _dP.harga_satuan,
                no_kantongdarah : _dP.no_kantongdarah,
                qty_diterima : _dP.qty_diterima,
                qty_pesan : _dP.qty_pesan,
                rhesus : _dP.rhesus,
                tgl_mintakirim : _dP.tgl_mintakirim,
                tgl_pengambilan : _dP.tgl_pengambilan,
            };

            _cache[_id]["row-"+_last] = _data;
            var _length = Object.keys(_cache[_id]).length;

        }

        _clone.filter(function () {
            var _tr = $(this).find("tr");
            $.each(_tr, function (key, val) {
                var _div = $("<div></div>");
                
            })
            return _tr;
        });

        _clone.attr("data-key",_last);
        var _button = "<button type=\"button\" class=\"del-row btn btn-danger btn-sm btn-custom\" style=\"padding-left:8px !important;\"><span class=\"fa fa-trash\"></span></button>";
        
        _clone.find("td:last-child").html(_button);
        _clone.find("input").val(null);
        _clone.find("input.date-kartik").val(dateNow);
        _parent.attr("data-last",_last);
        _parent.after(_clone);
        _parent.filter(function () {
            var _tr = $(this).find("tr");
            $.each(_tr, function (key, val) {
                if (key < 5) {
                    _tr.eq(key).attr("rowspan",_length);
                }
            })
            return _tr;
        });

        $(".date-kartik").kvDatepicker({
            autoclose : true,
            format : "dd-M-yyyy",
            lang : "en",
            startDate : "0d",
        });
    });

    $(document).on("click",".del-row", function (e) {
        e.preventDefault();
        var _parent = $(this).closest("tr");
        var _id = parseInt(_parent.attr("data-id"));
        var _key = parseInt(_parent.attr("data-key"));
        var _last = parseInt(_parent.attr("data-last"));
        var _grandPa = $("tr[data-id="+_id+"][data-key=0]");
        _length = 0;
        if (typeof  _cache[_id]["row-"+_key] != "undefined") {
            delete _cache[_id]["row-"+_key];
            var _length = Object.keys(_cache[_id]).length;
        }

        _grandPa.filter(function () {
            var _td = $(this).find("td");
            $.each(_td, function (key, val) {
                if (key < 5) {
                    _td.eq(key).attr("rowspan",_length);
                }
            })
            return _td;
        });
        _parent.remove();
    });

    $(document).on("keyup", ".no_kantongdarah", function (e) {
        e.preventDefault();
        var _parent = $(this).closest("tr");
        var _id = parseInt(_parent.attr("data-id"));
        var _key = parseInt(_parent.attr("data-key"));
        var _last = parseInt(_parent.attr("data-last"));
        if (typeof  _cache[_id]["row-"+_key] != "undefined") {
            var _data = _cache[_id]["row-"+_key];
            _data.no_kantongdarah = $(this).val();
            var _dataEx = $.extend({},_data, _cache[_id]["row-"+_key]);
            _cache[_id]["row-"+_key] = _dataEx;
        }
    });

    $(document).on("change",".date-kartik", function (e) {
        e.preventDefault();
        var _parent = $(this).closest("tr");
        var _id = parseInt(_parent.attr("data-id"));
        var _key = parseInt(_parent.attr("data-key"));
        var _last = parseInt(_parent.attr("data-last"));
        if (typeof  _cache[_id]["row-"+_key] != "undefined") {
            var _data = _cache[_id]["row-"+_key];
            _data.tgl_pengambilan = $(this).val();
            var _dataEx = $.extend({},_data, _cache[_id]["row-"+_key]);
            _cache[_id]["row-"+_key] = _dataEx;
        }
    });

',View::POS_END,'penerimaan-darah');