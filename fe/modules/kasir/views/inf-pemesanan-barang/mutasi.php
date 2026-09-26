<?php

/**
 * @author Johndoe
 * @copyright 23 January 2018
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Kasir', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><?=$this->title?></h3>
                <?= Breadcrumbs::widget([
                        'homeLink' => [ 
                            'label' => Yii::t('yii', 'Home'),
                            'url' => Yii::$app->homeUrl,
                        ],
                        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    ]);
                ?>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-body">                
                <div class="row">                   
                    <?php 
                    $form = ActiveForm::begin([
                        'id' => 'ajax-form',
                        // 'action' => '/kasir/inf-pemesanan-barang/save',
                        'options' => [
                            'class' => 'form-horizontal',
                            'enableAjaxValidation' => true,                           
                            'role' => 'form',
                        ],
                    ]);

                    ?>
                    <div class="form-group">
                        <div class="col-md-6">
                            <?= Html::submitButton(Yii::t('fe', ' Simpan'), 
                                [
                                    'class' => 'btn bg-teal fa fa-floppy-o',
                                ]);
                            ?>
                            <?= Html::button('<i class="fa fa-print"></i> '. \Yii::t('fe','Print'), ['class' => 'btn btn-dodger-blue']) ?>
                        </div>
                    </div>
                    <div class="col-md-12 panel panel-flat">
                        <div class="panel-heading">
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="panel-body">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="col-md-4">
                                        <label class="col-lg-3 control-label"><?= Yii::t('fe', 'Tanggal Dikirim') ?></label>
                                        <div class="col-lg-9">
                                            <?= $form->field($model, 'tgl_mutasibarang')->input('', [
                                                'placeholder' => Yii::t('fe', 'Tanggal Dikirim'), 
                                                'class' => 'form-control pickadate'])->label(false);
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-lg-5 control-label"><?= Yii::t('fe', 'Instalasi Tujuan') ?></label>
                                        <div class="col-lg-7">
                                            <p class="form-control-static"><?= $response['instalasi_tujuan'] ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="col-lg-5 control-label"><?= Yii::t('fe', 'Pegawai Mengetahui') ?></label>
                                        <div class="col-lg-7">
                                            <div class="input-group">
                                            <?= Html::activeDropDownList($model, 'pegawaimengetahui_id',
                                                ArrayHelper::map([], 'pegawai_id', 'nama_pegawai'), [
                                                    'class' => 'select2 autoPegawai',
                                                    'prompt' => Yii::t('fe', '-- Pilih --')
                                                ]) 
                                            ?>
                                            <?= Html::hiddenInput('MutasiBarangForm[pegawaimengetahui_id]', '', ['class' => 'pegawaimengetahui_id']); ?>
                                            <span class="input-group-addon">
                                                <?php
                                                    echo Html::a('<i class="fa fa-list-ul"></i>
                                                        <i class="fa fa-search"></i>',
                                                        Url::to([$url_search_popup]), [
                                                        'data-toggle' => 'modal',
                                                        'data-target' => '#modal_backdrop'
                                                    ]);
                                                ?>
                                            </span>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="col-md-4">
                                        <label class="col-lg-5 control-label"><?= Yii::t('fe', 'Nomor Pemesanan') ?></label>
                                        <div class="col-lg-7">
                                            <p class="form-control-static"><?= $response['no_pemesanan'] ?></p>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="col-lg-5 control-label"><?= Yii::t('fe', 'Ruangan Tujuan') ?></label>
                                        <div class="col-lg-7">
                                            <p class="form-control-static"><?= $response['ruangan_tujuan'] ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php 
                            echo $form->field($model, 'pegawaipengirim_id')->hiddenInput(['value'=> Yii::$app->user->identity->id])
                            ->label(false);
                            echo $form->field($model, 'ruangantujuan_id')->hiddenInput(['value'=> $response['ruangan_id']])->label(false);
                            echo $form->field($model, 'pesanbarang_id')->hiddenInput(['value'=> $response['pesanbarang_id']])->label(false);
                            echo $form->field($model, 'id')->hiddenInput(['value'=> $id, 'id' => 'id'])->label(false);
                            ?>
                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>
                <div class="row">                                       
                    <div class="col-md-12 panel panel-flat">
                        <div class="panel-heading">
                            <h4 class="panel-title"><?= Yii::t('fe', 'Tabel Pemesanan') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h4>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="panel-body">
                            <table id="example" class="table datatable-basic table-striped table-hover" data-filter=".form-filter">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1">No</th>
                                        <th><?=\Yii::t("fe", "Nama Barang");?></th>
                                        <th><?=\Yii::t("fe", "Qty");?></th>
                                        <th><?=\Yii::t("fe", "Satuan Besar");?></th>
                                        <th><?=\Yii::t("fe", "Qty");?></th>
                                        <th><?=\Yii::t("fe", "Satuan Kecil");?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>

<?php 
$this->registerJs('


let autoPegawai = $(".autoPegawai");
let pegawai_id = $(".pegawaimengetahui_id");
let pegawai = { list_pegawai: {}};

$.ajax({
    url: "/rm/pegawai-ruangan/get-data-ajax",
    type: "json",
    success: function(res) {
        let data = [];
        let response = res.data_pegawai;
        for (var i in response) {
            data.push({ id: response[i].pegawai_id, text: response[i].nama_pegawai});
            pegawai.list_pegawai[response[i].pegawai_id] = response[i];
        }

        autoPegawai.select2({
            data: data,
            type: "GET",
            quietMillis: 50,
            minimumInputLength: 2,
        })
        
        autoPegawai.change(function (e) {
            var id = $(this).val();
            var selected = pegawai.list_pegawai[id];
            if (typeof selected !== "undefined") {
                pegawai_id.val(selected.pegawai_id);
            }
        });

        var _pegawai_id = pegawai_id.val();
        $(".autoPegawai").val(_pegawai_id).trigger("change");
    }
})

$("#ajax-form").docoForm("submit",{
    success : function(data) {
        console.log(data);
        document.location = "index"; 
    }
});

$(".pickadate").pickadate({
    format: "dd mmm yyyy",
});

var table;
var id = $("#id").val();

$(document).ready(function() {
    table = $("#example").docoTabel({
        filter: false,
        sorting: [[2, "asc"]], 
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: baseUrl+"kasir/inf-pemesanan-barang/get-data-detail?id=" + id,
        columns: [                
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {title: "'.(\Yii::t("fe", "Nama Barang")).'", data: "barang_nama"},
            {title: "'.(\Yii::t("fe", "Qty Pemesanan")).'", data: "qty_pesan"},
            {title: "'.(\Yii::t("fe", "Satuan Besar")).'", data: "satuan_besar"},
            {title: "'.(\Yii::t("fe", "Qty Pemesanan")).'", data: "qty_konversi"},
            {title: "'.(\Yii::t("fe", "Satuan Kecil")).'", data: "satuan_kecil"},
        ],            
    });
});


', View::POS_END, 'detail-pemesanan-obat'); ?>
