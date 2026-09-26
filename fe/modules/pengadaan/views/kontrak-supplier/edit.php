<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use app\components\DHtml;
use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\DatePicker;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;

$this->title = $title;
$this->params['breadcrumbs'][] = [
    'label' => Yii::$app->docoVars->workspace("modul_alias"),
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;

?>

<style type="text/css" media="screen">
    #table-obat-pr thead input,
    #table-obat-pr thead textarea{
        padding: 6px 7px;
        /*margin-bottom: 5px;*/
    }

    .datepicker>div{
        display:block;
    }

    .select2-container .select2-selection--single {
        height: 35px !important;
    }

    .panel-body {
        padding: 15px!important;
    }

    .va-top {
        vertical-align: top!important;
    }

    .persen_to_rupiah {
        font-style: italic;
        color: #606060;
    }
    .not_active{
        background-color: #fcdacf !important;
    }
    #datatable-obat-kontrak-supplier_wrapper .dataTables_scroll{
        max-height: none !important;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= $title; ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <div class="col-md-12">
                    <?=
                        DocoHelpers::generateToolbar([
                            'back',
                            'custom-save' => [
                                'type' => 'button',
                                'title' => Yii::t('fe', 'Simpan'),
                                'icon' => 'fa fa-floppy-o',
                                'attributes' => [
                                    'data-options' => 'click',
                                    'id' => 'btn-edit'
                                ],
                            ],
                        ]);
                    ?>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-white">
                            <div class="panel-heading">
                                <h6 class="panel-title">Informasi Kontrak Supplier</h6>
                            </div>
                            <div class="panel-body">
                                <?php
                                    $form = ActiveForm::begin([
                                        'id' => 'update-kontrak-supplier-form',
                                        'enableAjaxValidation'=>false,
                                        'enableClientValidation'=>false,
                                        'type' => ActiveForm::TYPE_HORIZONTAL,
                                        'action' => "/pengadaan/kontrak-supplier/update?id={$kontraksupplier_id}",
                                        'formConfig' => [
                                            'labelSpan' => 3,
                                            'deviceSize' => ActiveForm::SIZE_SMALL
                                        ],
                                        'options' => [
                                            'skip-confirm' => "true"
                                        ]
                                    ]);
                                ?>
                                <div class="row">
                                    <div class="col-md-4">
                                        <?= $form->field($model, 'supplier_nama', [
                                                'horizontalCssClasses' => [
                                                    'label'   => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-8'
                                                ]
                                            ])->textInput([
                                                'tabindex' => 3,
                                                'value' => ArrayHelper::getValue($header,'supplier_nama','-'),
                                                'disabled' => true
                                            ]) ?>

                                        <?= $form->field($model, 'tgl_berlaku', [
                                            'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-8'
                                                ]
                                            ])->widget(DatePicker::classname(), [
                                                'name' => 'date_12',
                                                'readonly' => true,
                                                'language' => 'en',
                                                'options' => [
                                                    'tabindex' => 7,
                                                    'value' => date('d-M-Y',strtotime(ArrayHelper::getValue($header,'tgl_berlaku','-'))),
                                                ],
                                                'pluginOptions' => [
                                                    'autoclose' => true,
                                                    'format' => 'dd-M-yyyy',
                                                    'startDate' => "0d"
                                                ]
                                            ]) ?>

                                         <?= $form->field($model, 'payterm_id',[
                                            'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-8'
                                                ],
                                            ])->dropDownList(ArrayHelper::map($options['payterm'], 'payterm_id', 'payterm_nama'),[
                                                'class' => 'select2',
                                                'prompt' => '-- Pilih --',
                                                'tabindex' => 4,
                                                'value' => ArrayHelper::getValue($header,'payterm_id','-')
                                            ]) ?>

                                        <?= $form->field($model, 'pajak_id',[
                                                'horizontalCssClasses' => [
                                                        'label' => 'text-left control-label col-sm-4',
                                                        'wrapper' => 'col-md-8'
                                                    ],
                                                ])->dropDownList(ArrayHelper::map($options['ppn'], 'pajak_id', 'pajak_label'),[
                                                    'class' => '',
                                                    'prompt' => '-- Pilih --',
                                                    'id' => 'pajak_id',
                                                    'value' => ArrayHelper::getValue($header,'pajak_id','-')
                                                ]) ?>
                                    </div>
                                    <div class="col-md-4">
                                        <?= $form->field($model, 'kontraksupplier_no', [
                                                'horizontalCssClasses' => [
                                                    'label'   => 'text-left control-label col-sm-5',
                                                    'wrapper' => 'col-md-7'
                                                ]
                                            ])->textInput([
                                                'tabindex' => 4,
                                                'value' => ArrayHelper::getValue($header,'kontraksupplier_no','-'),
                                                'disabled' => true
                                            ]) ?>

                                        <?= $form->field($model, 'contact_person', [
                                            'horizontalCssClasses' => [
                                                'label'   => 'text-left control-label col-sm-5',
                                                'wrapper' => 'col-md-7'
                                            ]
                                        ])->textInput([
                                            'tabindex' => 8,
                                            'value' => ArrayHelper::getValue($header,'contact_person','-')
                                        ]) ?>
                                    </div>
                                    <div class="col-md-4">
                                        <?= $form->field($model, 'catatan', [
                                            'horizontalCssClasses' => [
                                                'label'   => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                        ])->textarea([
                                            'style' => 'resize: none', 
                                            'tabindex' => 6,
                                            'value' => ArrayHelper::getValue($header,'catatan','-')
                                        ]) ?>
                                    </div>
                                </div>
                                <?php ActiveForm::end(); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
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
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                </table>
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
    var list_detail = '. json_encode($detail) .';
    var kontraksupplier_id = "'. $kontraksupplier_id .'";
', View::POS_END);

$this->registerJs($this->render('../assets/js/kontrak-supplier/kontrak-supplier-edit.js'));
?>
