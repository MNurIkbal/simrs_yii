<?php

use yii\web\View;
use yii\helpers\Url;
use app\components\widgets\DocoTableWidget;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;

?>

<style type="text/css" media="screen">
    .not_active{
        background-color: #fcdacf !important;
    }
    #datatable-obat-kontrak-supplier_wrapper .dataTables_scroll{
        max-height: none !important;
    }
</style>

<!-- Modal Header -->
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Modal Detail Kontrak Supplier</h5>
</div>

<!-- Modal Body -->
<?php
$form = ActiveForm::begin([
    'id' => 'detail-kontrak-supplier-form',
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'action' => "#",
    'formConfig' => [
        'labelSpan' => 3,
        'deviceSize' => ActiveForm::SIZE_SMALL
    ],
    'options' => [
        'skip-confirm' => "true"
    ]
]);
$configField = [
    'horizontalCssClasses' => [
        'label'   => 'text-left control-label col-sm-4',
        'wrapper' => 'col-md-8'
    ]
];
?>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">

            <div class="panel panel-white">
                <div class="panel-heading">
                    <h6 class="panel-title">Informasi Kontrak Supplier</h6>
                </div>
                <div class="panel-body">
                    <div class="row mt-10">
                        <div class="col-md-6">
                            <?= $form->field($model, 'supplier_nama', $configField)->textInput([
                                'value' => ArrayHelper::getValue($header, 'supplier_nama', '-'),
                                'disabled' => true
                            ]) ?>
                            <?= $form->field($model, 'tgl_berlaku', $configField)->textInput([
                                'value' => date('d-M-Y', strtotime(ArrayHelper::getValue($header, 'tgl_berlaku'))),
                                'disabled' => true
                            ]) ?>
                            <?= $form->field($model, 'payterm_id', $configField)->textInput([
                                'value' => ArrayHelper::getValue($header, 'payterm_nama', '-'),
                                'disabled' => true
                            ]) ?>
                            <?= $form->field($model, 'pajak_id', $configField)->textInput([
                                'value' => ArrayHelper::getValue($header, 'pajak_nama', '-'),
                                'disabled' => true
                            ]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'kontraksupplier_no', $configField)->textInput([
                                'value' => ArrayHelper::getValue($header, 'kontraksupplier_no', '-'),
                                'disabled' => true
                            ]) ?>
                            <?= $form->field($model, 'contact_person', $configField)->textInput([
                                'value' => ArrayHelper::getValue($header, 'contact_person', '-'),
                                'disabled' => true
                            ]) ?>
                            <?= $form->field($model, 'catatan', $configField)->textarea([
                                'style' => 'resize: none',
                                'value' => ArrayHelper::getValue($header, 'catatan', '-'),
                                'disabled' => true
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-white">
                <div class="panel-heading">
                    <h6 class="panel-title">List Obat Kontrak Supplier</h6>
                </div>
                <div class="panel-body">
                    <div class="legend-index">
                        <div class="row mb-10">
                            <div class="col-md-12">
                                <div class="legend-header">Keterangan</div>
                                <div class="legend-wrapper">
                                    <div class="legend-information">
                                        <div class="legend-information__color" style="background-color: #fcdacf"></div>
                                        <div class="legend-information__text">Obat Tidak Aktif</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="table table-striped table-condensed table-hover" style="width:100%" id="datatable-obat-kontrak-supplier">
                        <thead>
                            <tr class="bg-inverse">
                                <th class="text-left" width="10%">Kode Obat Alkes</th>
                                <th class="text-left" width="10%">Nama Obat Alkes</th>
                                <th class="text-left" width="15%">Unit Of Measurement</th>
                                <th class="text-left" width="15%">Harga Order</th>
                                <th class="text-left" width="5%">Pengurang</th>
                                <th class="text-left" width="10.5%">Total Harga</th>
                                <th class="text-left" width="10%">Terakhir Update Pada</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$phpVars = [
    'details' => $details
];
$this->registerJsVar('phpVars', $phpVars, View::POS_READY);
$this->registerJs($this->render('js/detail.js'), View::POS_READY);
?>