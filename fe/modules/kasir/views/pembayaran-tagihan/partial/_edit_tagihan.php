<?php

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 *
 * Modal Form Tabel Multi Penjamin
 */


use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\select2\Select2;
use yii\web\JsExpression;
use app\components\DocoHelpers;

?>
<style type="text/css">
.modal-open .modal {
    overflow-y: hidden !important;
}
#table-edit-tagihan_info {
    display: none;
}
.dataTables_scroll {
    height: calc(40vh - 50px) !important;
    max-height: calc(40vh - 50px) !important;
    position: relative !important;
    margin-bottom: 10px;
}

td.bg-yellow {
    background-color: #e5e509;
}
td.bg-yellow:hover {
    background-color: #e5e509 !important;
}
.table-hover > tbody > tr:hover td.bg-yellow {
  background-color: #e5e509 !important;
}
.top-buffer{
  margin-top: 40px;
}

</style>

<div class="modal-header bg-inverse">
    <!-- <button type="button" class="close" data-dismiss="modal">&times;</button> -->
    <h5 class="modal-title"><strong><?= $title ?></strong></h5>
</div>

<?php
    $form = ActiveForm::begin([
        'id' => 'edit-tagihan-form',
        'enableAjaxValidation' => false,
        'enableClientValidation' => false,
        'formConfig' => [
            'labelSpan' => 3,
            'deviceSize' => ActiveForm::SIZE_SMALL
        ],
        'options' => [
            'role' => 'form',
        ]
    ]);
?>
<div class="modal-body">
    <div class="panel panel-white">
        <div class="panel-body" style="z-index: 10;">
            <table id="table-edit-tagihan" class="table table-striped table-condensed table-hover" style="width: 100%; height: 100%; max-height: 500px">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1%">No</th>
                        <th>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <?=\Yii::t("fe", "Tanggal");?>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </th>
                        <th>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <?=\Yii::t("fe", "Ruangan");?>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            </th>
                        <th>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <?=\Yii::t("fe", "Tindakan/Obat");?>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </th>
                        <th>
                            <?=\Yii::t("fe", "Qty");?></th>
                        <th>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <?=\Yii::t("fe", "Harga (Rp)");?>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </th>
                        <th>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <?=\Yii::t("fe", "Cito (Rp)");?></th>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <th>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <?=\Yii::t("fe", "Diskon  ");?>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </th>
                        <th><?=\Yii::t("fe", "Sub Total (Rp)");?></th>
                        <th>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <?=\Yii::t("fe", "Main Payer");?>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </th>
                        <th>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <?=\Yii::t("fe", "Main Payer(Rp)");?>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </th>
                        <th class = 'kolom-nama-subpayer'>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <?=\Yii::t("fe", "Sub Penjamin");?>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </th>
                        <th class = 'kolom-subpayer'>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <?=\Yii::t("fe", "Sub Payer(Rp)");?>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </th>
                        <th><?=\Yii::t("fe", "Harus Bayar (Rp)");?></th>
                        <th>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <?=\Yii::t("fe", "Keterangan");?>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>

            <div class="">
                <!-- <div class="form-group col-md-3">
                    <label><b></b></label>
                    < ?=Html::button(\Yii::t('fe', '<i class="fa fa-calculator"></i> Hitung Pro Rate'),[
                        'class' => 'form-control btn bg-teal btn-sm btn-hitung-prorate',
                        // 'data-toggle' => 'modal',
                        // 'data-target' => '#modal_backdrop_search',
                        // //'action' => 'kasir/pembayaran-tagihan/selisih-penjamin',
                        // 'data_width' => '30%',
                        ]); 
                    ? >
                </div>               -->
                <div class="form-group col-md-2">
                    <label><b>Total Biaya (Rp)</b></label>
                    <input type="text" 
                            class="form-control doco-number top-buffer" 
                            id="total-biaya"
                            readonly
                            style="text-align: right;font-size: 20px;font-weight: bold;background-color: #f5f5f5">
                </div>
                <div class="form-group col-md-2 excess_penjamin">
                    <label><b>Total Main Payer (Rp)</b></label>
                    </br>
                    <div class="row col-md-12" style="height:40px">
                    <label><h9 class="excess-label-main"></h9></label>
                    </div>
                    <div class="plafon-main-payer">
                    <input type="text" 
                            readonly
                            class="form-control doco-number" 
                            id="total-plafon-main-payer"
                            style="text-align: right;font-size: 20px;font-weight: bold;background-color: #f5f5f5">
                    </br>
                    <label><b>Plafon Main Payer (Rp)</b></label>
                    <input type="text" 
                            class="form-control doco-number" 
                            id="selisih-penjamin"
                            value = 0 
                            style="text-align: right;font-size: 20px;font-weight: bold;background-color: white">
                    </div>
                </div>
                <div class="form-group col-md-2 excess_penjamin_sub">
                    <label><b>Total Sub Payer (Rp)</b></label>
                    </br>
                    <div class="row col-md-12" style="height:40px">
                    <label><h9 class="excess-label-sub"></h9></label>
                    </div>
                    <div class="plafon-sub-payer">
                    <input type="text" 
                            readonly
                            class="form-control doco-number" 
                            id="total-plafon-sub-payer"
                            style="text-align: right;font-size: 20px;font-weight: bold;background-color: #f5f5f5">
                    </div>
                    </br>
                    <label><b>Plafon Sub Payer (Rp)</b></label>
                    <input type="text" 
                            class="form-control doco-number" 
                            id="selisih-penjamin-sub"
                            value = 0
                            style="text-align: right;font-size: 20px;font-weight: bold;background-color: white">                
                </div>
                <div class="form-group col-md-2">
                    <label><b>Total Dijamin (Rp)</b></label>
                    <input type="text" 
                            readonly 
                            class="form-control top-buffer" 
                            id="totaldijamin" 
                            style="text-align: right;font-size: 20px;font-weight: bold;background-color: #f5f5f5">
                </div>
                <?= Html::hiddenInput('totaldijamin_main', 0,['id' => 'totaldijamin_main']); ?>
                <?= Html::hiddenInput('totaldijamin_sub', 0,['id' => 'totaldijamin_sub']); ?>
                <div class="form-group col-md-2">
                    <label><b>Total Diskon (Rp)</b></label>
                    <input type="text" 
                            readonly 
                            class="form-control top-buffer" 
                            id="total-diskon" 
                            style="text-align: right;font-size: 20px;font-weight: bold;background-color: #f5f5f5">
                </div>
                <div class="form-group col-md-2">
                    <label><b>Total Harus Dibayar (Rp)</b></label>
                    <input type="text" 
                            readonly 
                            class="form-control top-buffer" 
                            id="totalharusbayar" 
                            style="text-align: right;font-size: 20px;font-weight: bold;background-color: #f5f5f5">
                            </br>
                    <div class="excess_pasien">
                    <label><b>Excess Pasien (Rp)</b></label>
                    <input type="text" 
                            class="form-control doco-number" 
                            id="excess-pasien"
                            value = 0
                            readonly
                            style="text-align: right;font-size: 20px;font-weight: bold;background-color: white">
                    </div>
                </div>
            </div>    
        </div>
</div>
</div>

<div class="modal-footer">
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Kembali'),['class' => 'btn bg-slate btn-sm btn-popup-kembali']); ?>
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-save"></i> Simpan'),['class' => 'btn bg-teal btn-sm simpan-edit-tagihan']); ?>
</div>
<?php ActiveForm::end(); ?>

<?php
$this->registerJs('
    // Event Ready
    var groupKelTindakan = {}
    var dataGroupTindakan = {}
    _plafonPayer = _plafonSubPayer = totalBiayaBayarPlafon = _excessPasien = 0
    var isPlafon = false
    $(document).ready(function(){
        //Set Nama Penjamin Sub Payer dan Main Payer
        _listPenjamin.forEach(function (val, key) {
            if (val.selected == true){     
                if(typeof val.text != "undefined" && val.text != ""){
                    $(".excess-label-main").text(val.text)
                }
            }
            if (val.selected == false){
                if(typeof val.text != "undefined" && val.text != ""){
                    $(".excess-label-sub").text(val.text)
                }
            }
        })

        //Set apabila bukan multipayer dan set Plafon sesuai Konfig
        if (_listPenjamin.length < 2 && _isSetPlafon == 1){
            $(".excess_penjamin_sub").hide()
            $(".excess_pasien").hide()
        } else if(_isSetPlafon == 1){
            isMultiPayer = true
            $(".excess_penjamin").show()
        } else {
            $(".excess_penjamin").hide()
            $(".excess_penjamin_sub").hide()
            $(".excess_pasien").hide()
        }
        tmpTableTransaksi.forEach(function(val, key) {
            var _newVal = Object.assign({}, val)
            var _kelTindakan = _newVal.kelompoktindakan_nama.replace(/[^\w]/g, "")
            if(val.pelayanan_id != null) {
                _plafonPayer = val.plafon_payer
                _plafonSubPayer = val.plafon_subpayer
                _excessPasien = val.excess_pasien
            }
            
            if (typeof groupKelTindakan[_kelTindakan] == "undefined") {
                groupKelTindakan[_kelTindakan] = {}
                dataGroupTindakan[_kelTindakan] = {
                    total_diskon:0,
                    total_subtotal:0,
                    total_dijamin:0,
                    total_dijaminSubPayer:0,
                    total_bayar:0,
                }
            }
            groupKelTindakan[_kelTindakan][key] = _newVal
            dataGroupTindakan[_kelTindakan].total_diskon += _newVal.nominal_diskon;
            dataGroupTindakan[_kelTindakan].total_subtotal += parseFloat(_newVal.subtotal);
            dataGroupTindakan[_kelTindakan].total_dijamin += parseFloat(_newVal.dijamin);
            dataGroupTindakan[_kelTindakan].total_dijaminSubPayer += parseFloat(_newVal.dijamin_subpayer);
            dataGroupTindakan[_kelTindakan].total_bayar += parseFloat(_newVal.totalDibayar);
            dataGroupTindakan[_kelTindakan].origin_name = _newVal.kelompoktindakan_nama;
            if(_plafonPayer > 0){
                totalBiayaBayarPlafon += parseFloat(_newVal.totalDibayar)
            }
        })
        if(_plafonPayer > 0){
            isPlafon = true
            $("#selisih-penjamin").val(docoHelper.convertToRupiah(_plafonPayer))
            $("#selisih-penjamin-sub").val(docoHelper.convertToRupiah(_plafonSubPayer))
        }
        if(_excessPasien > 0) {
            $("#excess-pasien").val(docoHelper.convertToRupiah(_excessPasien))
        }
        generateTableEditTagihan()
    });
    
', View::POS_END);

$this->registerJs($this->render('../js/_edit_tagihan.js'), View::POS_END);
?>