<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-03-09 11:00:02
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2019-01-08 13:39:55
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\form\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\DatePicker;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .datepicker>div{
        display:block;
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
                        <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= Html::button('<b><i class="fa fa-floppy-o"></i></b>'.Yii::t('fe', ' Simpan'), 
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'id' => 'simpan-proses',
                        'disabled' => ($data['status_periksa'] == 577) ? false : true,
                    ]);
                ?>
                <?=
                    DocoHelpers::generateToolbar([
                        'back',
                    ]);
                ?>
            </div>

            <div class="panel-body">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b><?= Yii::t('fe','Informasi Pasien') ?></b></h6>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-12">
                                <br>
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>Nama Pasien</b></label>
                                    <div class="col-sm-7">
                                        <p><b>:</b>&nbsp;<?= $model->nama_pasien ?></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>Tempat Lahir / Umur</b></label>
                                    <div class="col-sm-7">
                                        <p><b>:</b>&nbsp;<?= $model->umur ?></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>Alamat</b></label>
                                    <div class="col-sm-7">
                                        <p><b>:</b>&nbsp;<?= $model->alamat_pasien ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>Nama Perawat</b></label>
                                    <div class="col-sm-7">
                                        <p><b>:</b>&nbsp;<?= $model->pegawai_ruangan ?></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>Jabatan</b></label>
                                    <div class="col-sm-7">
                                        <p><b>:</b>&nbsp;<?= $model->jabatan_nama ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b><?= Yii::t('fe','Tabel Tindakan & Obat Alkes') ?></b></h6>
                        </div>
                        <div class="panel-body">
							<?php $form = ActiveForm::begin([
                                'id' => 'form', 
                                'action' => "/jenazah/informasi-pasien-meninggal/save-proses?id={$id}",
                                'enableAjaxValidation'=>false, 
                                'enableClientValidation'=>false,
                                'type' => ActiveForm::TYPE_HORIZONTAL,
                                'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL] 
                            ]); 
                            echo $form->field($model, 'pasien_id')->hiddenInput(['value' => $masukPenunjang['pasien_id']])->label(false);
                            echo $form->field($model, 'pegawai_id')->hiddenInput(['value' => $masukPenunjang['pegawai_id']])->label(false);
                            echo $form->field($model, 'tglserah_terima')->hiddenInput(['value' => $masukPenunjang['tglmasukpenunjang']])->label(false);
                            ?>
                            <table id="tindakan" class="table table-striped table-condensed table-hover" style="width:100%">
			                    <thead>
			                        <tr class="bg-inverse">
			                            <th>No</th>
			                            <th><?=\Yii::t("fe", "Nama Tindakan");?></th>
			                            <th><?=\Yii::t("fe", "Qty");?></th>
                                        <th><?=\Yii::t("fe", "Satuan");?></th>
			                            <th><?=\Yii::t("fe", "Dilakukan");?></th>
			                            <th><?=\Yii::t("fe", "Tanggal Tindakan");?></th>
			                        </tr>
			                    </thead>
			                    <tbody>
			                        <tr>
			                            <td class="text-center" colspan="9"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
			                        </tr>
			                    </tbody>
			                </table>

			                <div class="row">
                                <div class="col-md-12">
                                    <?= $form->field($model, 'catatan_proses', [
                                    'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-3',
                                            'wrapper' => 'col-md-12'
                                        ]
                                    ])->textArea([
                                        'placeholder' => Yii::t('fe', 'Catatan'),
                                        'class' => 'form-control input-sm',
                                        'rows' => 5,
                                    ])->label(Yii::t("fe", "Catatan")); ?>
                                </div>
                            </div>
			                <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
/*****
untuk nge get asset kartik
***/ 

echo DatePicker::widget([
    'name' => 'tgl_tindakan[]',
    'type' => DatePicker::TYPE_INPUT,
    'value' => '23-Feb-1982',
    'options' => [
    	'type' => 'hidden',
    ],
    'pluginOptions' => [
        'autoclose'=>true,
        'format' => 'dd-M-yyyy'
    ]
]);
?>

<?php 
$tgl_pendaftaran = date('d-m-Y', strtotime($data['tgl_pendaftaran']));
$this->registerJs('
    var table;
    var tableObat;
    var pendaftaran_id = "'.$id.'";
    var tgl_pendaftaran = "'.$tgl_pendaftaran.'";
    $(document).ready(function(){
        table = $("#tindakan").docoTabel({
            filter: false,
            sorting: [[2, "desc"]], 
            displayLength: 10,
            lengthChange: false,
            paging: false,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"jenazah/informasi-pasien-meninggal/get-data-tindakan-obat?pendaftaran_id=" + pendaftaran_id,
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "'.(\Yii::t("fe", "Nama Tindakan")).'", data: "tindakan_obat"},
                {title: "'.(\Yii::t("fe", "Qty")).'", data: "qty"},
                {title: "'.(\Yii::t("fe", "Satuan")).'", data: "satuan"},
                {title: "'.(\Yii::t("fe", "Dilakukan")).'", data: "dilakukan", className: "text-center"},
                {title: "'.(\Yii::t("fe", "Tanggal Tindakan")).'", data: "tgl_tindakan"},
            ],
            drawCallback:function(){
				$(".tgl_tindakan").kvDatepicker({
					autoclose: true,
					format: "dd-M-yyyy",
					language: "ID",
                    startDate: tgl_pendaftaran,
                    endDate: "0d",
				});
                checkTanggalTindakan();
                $(".chk_tindakan, .multiselect-container input").uniform({
                    radioClass: \'choice\'
                });
            }
        });

        var checkTanggalTindakan = function() {
            $.each($(".chk_tindakan"), function(){
                var parent = $(this).closest("tr");
                if(this.checked) {
                    parent.find(".tgl_tindakan").prop("disabled", false);
                    parent.find(".tgl_tindakan").prop("readonly", true);
                }
                else {
                    parent.find(".tgl_tindakan").prop("disabled", true);
                    parent.find(".tgl_tindakan").prop("readonly", true);
                }
            })
        }

        var checkTanggalObat = function() {
            $.each($(".chk_obat"), function(){
                var parent = $(this).closest("tr");
                if(this.checked) {
                    parent.find(".tgl_obat").prop("disabled", false);
                    parent.find(".tgl_obat").prop("readonly", true);
                }
                else {
                    parent.find(".tgl_obat").prop("disabled", true);
                    parent.find(".tgl_obat").prop("readonly", true);
                }
            })
        }

        $(".tgl_tindakan").prop("disabled", true);
        $(".tgl_obat").prop("disabled", true);
        $(document).on("change", ".chk_tindakan", function() {
            var parent = $(this).closest("tr");
            if(this.checked){
                parent.find(".tgl_tindakan").prop("disabled", false);
                parent.find(".tgl_tindakan").prop("readonly", true);
            }
            else {
                parent.find(".tgl_tindakan").prop("disabled", true);
                parent.find(".tgl_tindakan").prop("readonly", true);
            }
        });
        $(document).on("change", ".chk_obat", function() {
            var parent = $(this).closest("tr");
            if(this.checked){
                parent.find(".tgl_obat").prop("disabled", false);
                parent.find(".tgl_obat").prop("readonly", true);
            }
            else {
                parent.find(".tgl_obat").prop("disabled", true);
                parent.find(".tgl_obat").prop("readonly", true);
            }
        });
    });
    
    $("#simpan-proses").on("click", function (event) {
        event.preventDefault();
        var _data = $("#form").serializeArray();
        var validasi = 0;
        $.each($(".tgl_tindakan"), function(){
            var parent = $(this).closest("tr");
            if(this.checked) {
                var val_tgl_tindakan = parent.find(".tgl_tindakan").val();
                if(val_tgl_tindakan == "") {
                    validasi++;
                }
            }
        });
        if(validasi != 0) {
            docoNotification("error", "Proses gagal", "Tanggal Tindakan harus di isi.");
            return false;
        }
        $("#form").docoForm("submit",{
            data : _data,
            success : function (data) {
                var pendaftaran_id = data.response.pendaftaran_id;
                setTimeout(function(){
                    $("#simpan-proses").prop("disabled", true);
                }, 100);
                (new PNotify({
                    title: "&nbsp;Proses Berhasil !",
                    text: "Pemrosesan Jenazah Berhasil disimpan, apakah Anda ingin mencetak bukti pemrosesan jenazah?",
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
                    window.open("/jenazah/informasi-pasien-meninggal/cetak-proses2?pendaftaran_id="+pendaftaran_id);
                }).on("pnotify.cancel", function() {

                });
            }
        });
        $("#form").trigger("submit");
    })
', View::POS_END, 'b-index');
?>