<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-10-02 15:40:21
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-10-25 14:56:10
 */
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"), 'url' => ['/']];
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                            <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias", $this->title); ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                    </div>
                </div>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'kembali' => [
                        'title' => 'Kembali',
                        'icon' => 'fa fa-arrow-left',
                        'attributes' => [
                            'id' => 'btn-back',
                            'data-options' => 'click',
                        ]
                    ],
                    'buat-po' => [
                        'title' => 'Buat PO',
                        'icon' => 'fa fa-floppy-o',
                        'attributes' => [
                            'id' => 'btn-simpan',
                            'data-options' => 'click',
                        ]
                    ],
                    'tolak' => [
                        'title' => 'Tolak',
                        'icon' => 'fa fa-times',
                        'attributes' => [
                            'id' => 'btn-tolak',
                            'data-options' => 'click',
                        ]
                    ],
                    'add-supplier' => [
                        'title' => ' Supplier',
                        'icon' => 'fa fa-plus',
                        'attributes' => [
                            'id' => 'btn-add-supplier',
                            'data-options' => 'click',
                        ]
                    ]
                ]) ?>
                </div>

            <div class="panel-body">
               <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b><?= Yii::t('fe', 'Data Transaksi'); ?></b></h6>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="text-left control-label col-sm-7"><b>
                                                <?= Yii::t("fe", "Nomor Transaksi") ?></b></label>
                                            <div class="col-sm-5">
                                                <p><b>:&nbsp;<?= isset($header['no_rekomendasibarang']) ? $header['no_rekomendasibarang'] : '' ?> 
                                                </b></p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="text-left control-label col-sm-7"><b>
                                                <?= Yii::t("fe", "Ruangan") ?></b></label>
                                            <div class="col-sm-5">
                                                <p><b>:&nbsp;<?= isset($header['ruangan_nama']) ? $header['ruangan_nama'] : '' ?></b></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="text-left control-label col-sm-7"><b><?= Yii::t("fe", "Tanggal Transaksi") ?></b></label>
                                            <div class="col-sm-5">
                                                <p><b>:&nbsp;<?= isset($header['tgl_rekomendasibarang']) ? date('d M Y', strtotime($header['tgl_rekomendasibarang']))  : '' ?> </b></p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="text-left control-label col-sm-7"><b><?= Yii::t("fe", "Nama Pegawai") ?></b></label>
                                            <div class="col-sm-5">
                                                <p><b>:&nbsp;<?= isset($header['nama_pegawai']) ? $header['nama_pegawai'] : '' ?> </b></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <?php
                            $form = ActiveForm::begin([
                                'id' => 'form',
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

                            echo $form->field($model, 'ruangan_id')->hiddenInput([
                                'value' => $header['ruangan_id']
                            ])->label(false);

                            echo $form->field($model, 'pegawai_id')->hiddenInput([
                                'value' => $header['pegawai_id']
                            ])->label(false);
                            ?>
                            <table id="po" class="table table-striped table-condensed table-hover" 
                            style="width:100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1">No</th>
                                        <th><?=\Yii::t("fe", "Nama Barang");?></th>
                                        <th><?=\Yii::t("fe", "Reorder Point");?></th>
                                        <th><?=\Yii::t("fe", "Stok");?></th>
                                        <th><?=\Yii::t("fe", "Rekomendasi");?></th>
                                        <th><?=\Yii::t("fe", "Qty PO");?></th>
                                        <th><?=\Yii::t("fe", "Supplier");?></th>
                                        <th><?=\Yii::t("fe", "Aksi");?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 0; 
                                        foreach ($detail as $key => $value) : $no++; ?>
                                        <tr class="tr_<?= $value['barang_id'] ?> parent"
                                            data-id="<?= $value['barang_id'] ?>"
                                            data-row="0"
                                            data-last="0">
                                            <td><?= $no ?></td>
                                            <td><?= $value['barang_nama'] ?></td>   
                                            <td><?= $value['nilai_ro'] ?></td>
                                            <td><?= $value['qty_tersedia'] ?></td>
                                            <td><?= $value['rekomendasi'] ?></td>
                                            <td style="width:200px;">
                                                <?= $form->field($modelDetail, 'qty_po['.$value['barang_id'].'0]')
                                                ->textInput(['class' => 'form-control input-sm doco-number qty_po'])->label(false);
                                                ?>
                                            </td>
                                            <td style="width:400px;">
                                                <?= $form->field($modelDetail, 'supplier_id['.$value['barang_id'].'0]')
                                                ->dropDownList([], ['class' => 'form-control input-sm select2 selectSupplier', 'prompt' => 'Pilih Supplier'])
                                                ->label(false);
                                                ?>
                                            </td>
                                            <td><button type="button" 
                                                class="add_supplier btn btn-info btn-xs"><span class="fa fa-plus">
                                            </span></button></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
               </div>
            </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
var _listIncremnt = {};
var _listPo = {};
var id = "'.$id.'";

$(document).ready(function(){
    $(".qty_po").trigger("keyup");
    $(".selectSupplier").select2({
        placeholder: "Pilih Supplier",
        minimumInputLength: 3, 
        ajax : {
            url: baseUrl+"pengadaan/info-recomended-order/search-supplier",
            dataType: "json",
            quietMillis: 250,
            data: function (params) {
              var query = {
                search: params,
              }
              return params; 
            },
            processResults: function (data) {
                $.each(data.result, function (key,val) {
                });
              return {
                results: data.result
              };
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; },
        },
    });
})

$(".add_supplier").click(function(){
    var no = 0;
    var parent = $(this).closest("tr");
    // ini id obat
    var _id = parent.attr("data-id");
    var _row = parseInt(parent.attr("data-last"));
    var str = parent[0].className;
    var barang_id = parseInt(str.split("tr_").pop());
    
    var _inc = parseInt(_row) + 1;
    if (typeof _listIncremnt[_id + "-" + _inc] == "undefined") {
        _listIncremnt[_id + "-" + _inc] = {
            id : _row + 1,
            id_supplier : null,
            qty : 0,
            barang_id: _id
        };
    }

    parent.attr("data-last", _inc);
    
    var supplier = "<div class=\'form-group\'><div class=\'col-sm-9\'><select name=\'ValidasiPoBarangDetailForm[supplier_id]["+barang_id+_inc+"]\' class=\'form-control input-sm select2 selectSupplier\'></select></div></div>";

    var button_delete = "'.preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'', 
                        Html::button('<span class="fa fa-times"></span>',   
                                [
                                    'class' => 'delete_supplier btn btn-danger btn-xs', 
                                ]
                            )
                        )
                    ).'";

    var newRow = "<tr data-row=\'"+ _inc +"\' data-id=\'"+ _id +"\'><td></td><td><input type=\'hidden\' name=\'ValidasiPoBarangDetailForm[barang_id]["+barang_id+"]\' value="+barang_id+" class=\'form-control input-sm \'></td><td></td><td></td><td></td>" +
        "<td style=\'width:200px;\'><div class=\'form-group\'><div class=\'col-sm-9\'><input type=\'text\' name=\'ValidasiPoBarangDetailForm[qty_po]["+barang_id+_inc+"]\' class=\'form-control input-sm  doco-number qty_po\'></div></div></td><td style=\'width:400px;\'>"+supplier+"</td>" +
        "<td>"+button_delete+"</td></tr>";

    $(newRow).insertAfter(parent);
        
    $(".select2").select2();
    $(".selectSupplier").select2({
        placeholder: "Pilih Supplier",
        minimumInputLength: 3, 
        ajax : {
            url: baseUrl+"pengadaan/info-recomended-order/search-supplier",
            dataType: "json",
            quietMillis: 250,
            data: function (params) {
              var query = {
                search: params,
              }
              return params; 
            },
            processResults: function (data) {
                $.each(data.result, function (key,val) {
                    // $(".satuanunit_nama").val(val.text);
                });
              return {
                results: data.result
              };
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; },
        },
    });
});

$("#po").on("click", ".delete_supplier",function(){
    var parent = $(this).closest("tr");
    // ini id obat
    var _value = $(this).val();
    var _id = parent.attr("data-id");
    var _row = parseInt(parent.attr("data-row"));
    if(typeof _listIncremnt[_id + "-" + _row] != "undefined") {
        delete _listIncremnt[_id + "-" + _row];
    }
    $(this).parent().parent().remove();
});

$(document).on("change", ".selectSupplier ", function (e) {
    var parent = $(this).closest("tr");
    // ini id obat
    var _value = $(this).val();
    var _id = parent.attr("data-id");
    var _row = parseInt(parent.attr("data-row"));

    if (typeof _listIncremnt[_id + "-" + _row] != "undefined") {
        _listIncremnt[_id + "-" + _row].id_supplier = _value
    } else {
        _listIncremnt[_id + "-" + _row] = {
            id : _row,
            id_supplier : _value,
            qty : 0,
            barang_id: _id
        };
    }
})

$(document).on("keyup", ".qty_po ", function (e) {
    var parent = $(this).closest("tr");
    // ini id obat
    var _value = docoHelper.convertToAngka($(this).val());
    var _id = parent.attr("data-id");
    var _row = parseInt(parent.attr("data-row"));
    if (typeof _listIncremnt[_id + "-" + _row] != "undefined") {
        _listIncremnt[_id + "-" + _row].qty = _value
    } else {
        _listIncremnt[_id + "-" + _row] = {
            id : _row,
            id_supplier : null,
            qty : _value,
            barang_id: _id
        };
    }
})

$("#btn-simpan").on("click", function(){
    var ruangan_id = $("#validasipobarangform-ruangan_id").val();
    var pegawai_id = $("#validasipobarangform-pegawai_id").val();
    
    $(this).docoForm("click", {
        url : baseUrl+"pengadaan/info-recomended-order/save-barang?id=" + id,
        method : "POST",
        type : "json",
        data: {
            data : _listIncremnt,
            ruangan_id: ruangan_id,
            pegawai_id: pegawai_id,
        },
        success : function (data) {
            window.location.href = baseUrl+"pengadaan/info-recomended-order/barang";
        }
    })
});

$("#btn-add-supplier").on("click", function(){
    window.open(baseUrl+"master/supplier/create");
});

$("#btn-tolak").on("click", function(){
    $(this).docoForm("delete",{
        additional: "data-rm",
        url: baseUrl+"pengadaan/info-recomended-order/batal-barang?id="+id,
        confirmTitle : "'.(\Yii::t("fe", "Konfirmasi")).'",
        confirmMessage : "'.(\Yii::t("fe", "Apa anda yakin ingin membatalkan Pengadaan Barang?")).'",
        success : function (data) {
            window.location.href = baseUrl+"pengadaan/info-recomended-order/barang";
        }
    });
});

$("#btn-back").on("click", function(){
    window.location.href = baseUrl+"pengadaan/info-recomended-order/barang";
});

', View::POS_END, 'b-index');
?>