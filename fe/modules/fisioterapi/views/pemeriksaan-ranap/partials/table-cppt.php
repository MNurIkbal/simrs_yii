<?php
    use app\components\DocoHelpers;
    use yii\helpers\ArrayHelper;

    $pendaftaranId  = ArrayHelper::getValue($dataView, 'pendaftaranId');
    $pendaftaranIdEnc  = ArrayHelper::getValue($dataView, 'pendaftaranIdEnc');
    $noRekamMedik = ArrayHelper::getValue($dataView, 'dataPasien.no_rekam_medik');
    $noRekamMedikEnc = DocoHelpers::encrypt($noRekamMedik);
    $pasienId = ArrayHelper::getValue($dataView, 'dataPasien.pasien_id');
    $pasienIdEnc = DocoHelpers::encrypt($pasienId);
    $pasienAdmisiId = ArrayHelper::getValue($dataView, 'pasienadmisiId');
    $pasienAdmisiIdEnc = DocoHelpers::encrypt($pasienAdmisiId);
?>

<div class="panel panel-white">
    <div class="panel-heading">
        <h5 class="panel-title"><?= Yii::t('fe', 'CPPT') ?></h5>
    </div>
    <div class="panel-heading clearfix">
        <?=
            DocoHelpers::generateToolbar([
                'print' => [
                    'attributes' => [
                        'url' => '/fisioterapi/pemeriksaan-ranap/cetak-cppt?id='.$pendaftaranIdEnc.'&pasienadmisi_id='.$pasienAdmisiIdEnc.'&'
                    ]
                ],
                'hasil-radiologi' => [
                    'type'  => 'button',
                    'title' => Yii::t('fe', 'Hasil Radiologi'),
                    'icon'  => 'fa fa-history',
                    'attributes' => [
                        'id'          => 'btn-hasil-radiologi',
                        'data-width'  => '80%',
                        'data-toggle' => 'modal',
                        'data-target' => '#modalProgramTerapi',
                        'action'      => '/api/igd/list-penunjang-radiologi/index?id='.$pendaftaranIdEnc.'&norm='.$noRekamMedikEnc.'&pasienadmisi_id='.$pasienAdmisiIdEnc.'&'
                    ]
                ],
                'riwayat-order-bedah' => [
                    'type'  => 'button',
                    'title' => Yii::t('fe', 'Riwayat Order Bedah'),
                    'icon'  => 'fa fa-history',
                    'attributes' => [
                        'id'          => 'btn-riwayat-order-bedah',
                        'data-width'  => '80%',
                        'data-toggle' => 'modal',
                        'data-target' => '#modalProgramTerapi',
                        'action'      => '/api/ranap/history-surgery-orders/index?id='.$pendaftaranIdEnc.'&norm='.$noRekamMedikEnc.'&'
                    ]
                ]
            ], '#tb-cppt')
        ?>
    </div>

    <div class="panel-body">
        <table class="table table-bordered datatable-basic dataTable" id="tb-cppt" style="width:100%;">
            <thead>
                <tr class="bg-inverse">
                    <th colspan="5" class="text-center"><?= Yii::t('fe', 'SOAP / Verbal Order') ?></th>
                </tr>
                <tr class="bg-inverse">
                    <th width="8px"></th>
                    <th></th>
                    <th></th>
                    <th width="20%"></th>
                    <th width="20%"></th>
                </tr>
            </thead>
            <tbody>
                <!--  -->
            </tbody>
        </table>
    </div>
</div>