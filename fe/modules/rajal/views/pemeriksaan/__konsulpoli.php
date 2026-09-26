<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-03-05 11:00:08
 * @Last Modified by:   afil
 * @Last Modified time: 2018-03-05 15:22:11
 * @Description:
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use kartik\widgets\DepDrop;
use app\components\DocoHelpers;
?>
<style type="text/css">
.select2-search--dropdown:after {
    content: ''
}
</style>
<div class="row">
    <!-- form start -->
    <div class="panel panel-flat">
        <div class="panel-heading">
            <h5 class="panel-title"><?=Yii::t('fe', 'Konsul poli')?> - <?= $infoPasien['nama_pasien']. ' / '. $infoPasien['penjamin_nama'] . ' / ' . $infoPasien['kelaspelayanan_nama'] ?></h5>
            <div class="heading-elements">
                <ul class="icons-list">
                    <li><a data-action="collapse"></a></li>
                </ul>
            </div>
        </div>
        <div class="panel-toolbar clearfix">
            <?=DocoHelpers::generateToolbar([
                'save' => ['attributes'=>['form_id'=>'form-konsulpoli']],
            ]);?>
        </div>
        <div class="panel-body">
            <?php
            $form = ActiveForm::begin([
                'id' => 'form-konsulpoli',
                'type' => ActiveForm::TYPE_HORIZONTAL,
                'enableAjaxValidation'=>false,
                'enableClientValidation'=>false,
                'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
            ]);
            ?>
            <div class="row" id="section-konsulpoli">
                <div class="row">
                    <div class="col-md-5">
                        <?=$form->field($modelKonsulpoli, 'jadwaldokter_id', ['labelOptions' => ['class' => 'text-right']])
                            ->dropDownList([], [
                                'class' => 'form-control input-sm selectPoli',
                                'id' => 'poli_ruangan_id',
                                'prompt' => Yii::t('fe', '--Pilih ruangan tujuan--'),
                            ]
                        );?>

                        <?= $form->field($modelKonsulpoli, 'pegawai_id', ['labelOptions' => ['class' => 'text-right']])->widget(DepDrop::classname(), [
                            'options'=>['id'=>'pegawai_id', 'class' => 'form-control input-sm select2'],
                            'pluginOptions'=>[
                                'depends'=>['poli_ruangan_id'],
                                'loadingText' => 'Loading Data ...',
                                'placeholder' => '--Pilih Dokter--',
                                'url'=>Url::to(['/rajal/allow/list-dokter-jadwal'])
                            ]
                        ]); ?>
                    </div>
                    <div class="col-md-5">
                        <?= $form->field($modelKonsulpoli, 'catatan_dokter_konsul')->textarea(['rows' => '4'],['class' => 'form-control', 'id' => 'catatan_dokter_konsul']); ?>
                    </div>

                </div>
                <div class="row">
                    <div class="col-sm-12"><hr></div>
                </div>
                <div class="row">
                    <div class="panel-body">
                        <table class="table datatable-basic table-striped table-hover dataTable no-footer table-framed tabel-konsul-poli">
                            <thead>
                                <tr class="bg-inverse">
                                    <th width="5%">No</th>
                                    <th><?=Yii::t('fe', 'Tanggal')?></th>
                                    <th><?=Yii::t('fe', 'Dikonsul Dari')?></th>
                                    <th><?=Yii::t('fe', 'Sudah Konsul')?></th>
                                    <th><?=Yii::t('fe', 'Dokter Konsul')?></th>
                                    <th><?=Yii::t('fe', 'Jawaban Dokter Konsul')?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <!-- form end -->
</div>

<?php
$this->registerJs('
    if(pasienpulang_id != ""){
        $("#btn-submit").prop("disabled", true);
        $("#poli_ruangan_id").prop("disabled", true);
    }
    var tabel_konsul_poli = $(".tabel-konsul-poli").docoTabel({
        filter: true,
        sorting: [[1, "asc"]],
        displayLength: 10,
        processing: true,
        serverSide: true,
        searching: false,
        ajax: baseUrl+"rajal/pemeriksaan/get-data-konsul-poli?id=' . $pendaftaran_id . '",
        columns:[
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {title: "' . (\Yii::t("fe", "Tanggal")) . '", data: "tgl_poli"},
            {title: "' . (\Yii::t("fe", "Dikonsul Dari")) . '", data: "ruangan_asal"},
            {title: "' . (\Yii::t("fe", "Sudah Konsul")) . '", data: "tgl_selesaikonsul"},
            {title: "' . (\Yii::t("fe", "Dokter Konsul")) . '", data: "nama_dokter"},
            {title: "' . (\Yii::t("fe", "Jawaban Dokter Konsul")) . '", data: "jawaban_konsul"},
        ]
    });

    $("#form-konsulpoli").docoForm("submit",{
        success : function(data) {
            tabel_konsul_poli.draw();
            $(".selectPoli").val(null).trigger("change");
            $("#pegawai_id").val(null).trigger("change");
            $("#catatan_dokter_konsul").val("");
            $("textarea").val("");
        }
    });

    $(document).ready(function(){
        // $("#field-dokter").hide()
        $(".selectPoli").docoPaginationSelec2(
            config = {
                // dropdownCssClass : "no-search",
                placeholder : "-- Pilih Ruangan Tujuan --",
                _api : "/rajal/master-api/get-jadwal-poli",
            }
        );


    });

    function cetakKonsul(konsulId) {
        window.open(`/rajal/inf-konsul-poli/export-pdf-konsul?id=${konsulId}`)
    }

    function cetakJawabanKonsul(konsulId) {
        window.open(`/rajal/inf-konsul-poli/export-pdf-konsul?id=${konsulId}&jenis=jawaban-konsul`)
    }

    function cetakRencanaKontrol(rencanaKontrolId) {
        window.open(`/pendaftaran/rencana-kontrol-inap/print-rencana?rencanakontrol_id=${rencanaKontrolId}`);
    }
');
?>
