<?php

use yii\web\View;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use yii\helpers\Url;
use app\modules\components\helpers\DynamicFormHelpers;
use app\modules\ranap\components\widget\DynamicFormWidget;
use yii\web\JsExpression;
use app\components\DocoConstants;
?>

<div class='panel panel-flat' onload="startTime()">
    <div class="panel-body">
        <!-- <div class="row"> -->
            <?php 
                    $form = ActiveForm::begin([
                        'id' => 'list-form',
                        // 'type' => ActiveForm::TYPE_HORIZONTAL,
                        // 'enableAjaxValidation' => false,
                        // 'enableClientValidation'=>false,
                        // 'action' => '/ranap/pemeriksaan-rawat-inap/save-rekon-cache',
                        // 'formConfig' => ['labelSpan' => 4,'showErrors'=>false, 'deviceSize' => ActiveForm::SIZE_SMALL]
                    ]); 
                ?>
                <label>
                    <h6 class="panel-title"><?= Yii::t('fe', 'Daftar permintaan konsultasi'); ?> - <?= $infoPasien['nama_pasien']. ' / '. $infoPasien['penjamin_nama'] . ' / ' . $infoPasien['kelaspelayanan_nama'] ?></h6> 
                </label>
                <div class="clearfix"><br></div>
                <table class="table datatable-basic table-striped table-hover dataTable" id="tb_permintaan_konsultasi_view" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th>No</th>
                            <th><?= Yii::t('fe', 'Waktu permintaan') ?></th>
                            <th><?= Yii::t('fe', 'Dokter DPJP') ?></th>
                            <th><?= Yii::t('fe', 'Dokter yang dikonsul') ?></th>
                            <th><?= Yii::t('fe', 'Jenis konsul') ?></th>
                            <th><?= Yii::t('fe', 'Permintaan konsultasi') ?></th>
                            <th><?= Yii::t('fe', 'Aksi')?></th>
                          </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <?php ActiveForm::end(); ?>
        <!-- </div> -->
    </div>
</div>


<?php
	$pen_id = !empty($getPendaftaranId) ? $getPendaftaranId : $pendaftaran_id;
	
    $this->registerJs('
    	var table;
        var is_disabled = "'.$disabled.'";
        var status_disabled = "'.$status_disabled.'";
        var is_valid = false;
        if(is_disabled || status_disabled) {
            is_valid = false;
        } else {
            is_valid = true;
        }

        $(document).ready(function(){
            $("#permintaan-konsul-form :input").prop("disabled", is_valid);
            $("#btn-save-permintaan-konsul").attr("disabled", is_valid);
        });

        var pasienId = "'. $pasienId .'";
        var list_dokter_available = ' . json_encode($listfilter['listDokter']) . ';


        table = $("#tb_permintaan_konsultasi_view").docoTabel({
            filter: false,
            pageLength: 20,
            // lengthMenu: [5, 10, 25, 100],
            bLengthChange: false,
            serverSide: true,
            stateSave: true,
            processing: true,
            scrollX: true,
            sorting:[[1,\'asc\']],
            ajax: baseUrl + "ranap/pemeriksaan-rawat-inap/get-list-permintaan-konsul?id='.$pen_id.'&preview_only='.$preview_only.'",
            columns: [
                {
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    data: "waktu_permintaan",
                    searchable: false,
                    orderable: false
                },
                {
                    data: "dokterdpjpasal_nama",
                    searchable: false,
                    orderable: false
                },
                {
                    data: "dok_konsul",
                    searchable: false,
                    orderable: false
                },
                {
                    data: "jenis_konsul_nama",
                    searchable: false,
                    orderable: false,
                    // class: "text-center"
                },
                {
                    data: "ket_konsul",
                    searchable: false,
                    orderable: false,
                    // class: "text-center"
                },
                {
                    data: "aksi",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                },
            ],
        });

    ', View::POS_END);
    //$this->registerJs($this->render('_permintaankonsul.js',['pasienId'=>$pasienId]), View::POS_END);
?>