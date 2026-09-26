<?php
use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;

$pendaftaranId = ArrayHelper::getValue($dataView, 'pendaftaranId');
$noRekamMedik = DocoHelpers::encrypt(ArrayHelper::getValue($dataView, 'dataPasien.no_rekam_medik'));
$pasienId = DocoHelpers::encrypt(ArrayHelper::getValue($dataView, 'dataPasien.pasien_id'));;
$pasienAdmisiId = DocoHelpers::encrypt($dataView['pasienadmisiId']);
$programTerapiIds = ArrayHelper::getValue($dataView, 'programTerapiIds');
?>
<style type="text/css">
    #modal_backdrop {
        z-index: 1041 !important;
        max-height: calc(100vh);
        overflow-y: auto;
    }
</style>
<div class="panel panel-white">
    <div class="panel-heading">
        <h5 class="panel-title">CPPT</h5>
    </div>
    <div class="panel-heading clearfix">
        <?= DocoHelpers::generateToolbar([
            'print' => [
                'title' => 'Cetak Semua CPPT',
                'attributes' => [
                    'url' => "/fisioterapi/pemeriksaan/cetak-cppt?id=$pendaftaranId&program_terapi_id=$programTerapiIds&"
                ]
            ],
            'hasil-radiologi' => [
                'type' => 'button',
                'title' => Yii::t('fe', 'Hasil Radiologi'),
                'icon' => 'fa fa-history',
                'attributes' => [
                    'id' => 'btn-hasil-radiologi',
                    'data-width' => '80%',
                    'data-toggle' => 'modal',
                    'data-target' => '#modalProgramTerapi',
                    // 'action' => '/igd/riwayat-pasien/list-penunjang-radiologi?id='.$pendaftaranId.'&norm='.$noRekamMedik
                    'action' => '/api/igd/list-penunjang-radiologi/index?id='.$pendaftaranId.'&norm='.$noRekamMedik.'&pasienadmisi_id='.$pasienAdmisiId.'&'
                ]
            ],
            'riwayat-order-bedah' => [
                'type' => 'button',
                'title' => Yii::t('fe', 'Riwayat Order Bedah'),
                'icon' => 'fa fa-history',
                'attributes' => [
                    'id' => 'btn-riwayat-order-bedah',
                    'data-width' => '80%',
                    'data-toggle' => 'modal',
                    'data-target' => '#modalProgramTerapi',
                    'action' => '/api/ranap/history-surgery-orders?id='.$pendaftaranId.'&norm='.$noRekamMedik.'&'
                ]
            ],
            'tindak-lanjut' => [
                'type' => 'button',
                'title' => Yii::t('fe', 'Pulang'),
                'icon' => 'fa fa-sign-out fa-md',
                'attributes' => [
                    'id' => 'pemulangan-pasien',
                    'class' => 'btn btn-info btn-labeled btn-xs btn-flat-warning',
                    'data-width' => '60%',
                    'data-toggle' => 'modal',
                    'data-target' => '#modal_backdrop',
                    'action' => '/fisioterapi/pemeriksaan/pemulangan-pasien?id='.$pendaftaranId.''
                ]
            ],
        ], '#tb-cppt') ?>
    </div>
    <div class="panel-body">
        <table class="table table-bordered datatable-basic dataTable" id="tb-cppt" style="width:100%;">
            <thead>
                <tr class="bg-inverse">
                    <th colspan="5" class="text-center">SOAP / Verbal Order</th>
                </tr>
                <tr class="bg-inverse">
                    <th width="8px"></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <!-- nullable -->
            </tbody>
        </table>
    </div>
</div>