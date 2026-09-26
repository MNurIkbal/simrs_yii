<?php
/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\DatePicker;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => $moduleAlias, 'url' => [$modulePath]];
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
                                    'id' => 'btn-simpan'
                                ],
                            ]
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
                                        'id' => 'create-kontrak-supplier-form',
                                        'enableAjaxValidation'=>false,
                                        'enableClientValidation'=>false,
                                        'type' => ActiveForm::TYPE_HORIZONTAL,
                                        'action' => '/pengadaan/kontrak-supplier/save',
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
                                            <?= $form->field($model, 'supplier_id',[
                                                'horizontalCssClasses' => [
                                                        'label' => 'text-left control-label col-sm-4',
                                                        'wrapper' => 'col-md-8'
                                                    ],
                                                ])->dropDownList([],[
                                                    'class' => '',
                                                    'id' => 'supplier_id',
                                                    'tabindex' => 1
                                                ]) ?>

                                            <?= $form->field($model, 'tgl_berlaku', [
                                                'horizontalCssClasses' => [
                                                        'label' => 'text-left control-label col-sm-4',
                                                        'wrapper' => 'col-md-8'
                                                    ]
                                                ])->widget(DatePicker::classname(), [
                                                    'name' => 'date_12',
                                                    'value' => "",
                                                    'readonly' => true,
                                                    'language' => 'en',
                                                    'options' => [
                                                        'tabindex' => 7,
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
                                                    'tabindex' => 4
                                                ]) ?>

                                            <?= $form->field($model, 'pajak_id',[
                                                'horizontalCssClasses' => [
                                                        'label' => 'text-left control-label col-sm-4',
                                                        'wrapper' => 'col-md-8'
                                                    ],
                                                ])->dropDownList(ArrayHelper::map($options['ppn'], 'pajak_id', 'pajak_label'),[
                                                    'class' => '',
                                                    'prompt' => '-- Pilih --',
                                                    'id' => 'pajak_id'
                                                ]) ?>
                                        </div>
                                        <div class="col-md-4">
                                            <?= $form->field($model, 'kontraksupplier_no', [
                                                'horizontalCssClasses' => [
                                                    'label'   => 'text-left control-label col-sm-5',
                                                    'wrapper' => 'col-md-7'
                                                ]
                                            ])->textInput(['tabindex' => 2]) ?>

                                            <?= $form->field($model, 'contact_person', [
                                                'horizontalCssClasses' => [
                                                    'label'   => 'text-left control-label col-sm-5',
                                                    'wrapper' => 'col-md-7'
                                                ]
                                            ])->textInput(['tabindex' => 8]) ?>
                                        </div>
                                        <div class="col-md-4">
                                            <?= $form->field($model, 'catatan', [
                                                'horizontalCssClasses' => [
                                                    'label'   => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-8'
                                                ]
                                            ])->textarea(['style' => 'resize: none', 'tabindex' => 6]) ?>
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
                                <table class="table table-striped table-condensed table-hover" style="width:100%" id="table-obat-kontrak-supplier">
                                    <thead>
                                        <tr class="bg-inverse">
                                            <th class="text-center" width="25%">Nama Obat Alkes</th>
                                            <th class="text-center" width="15%">Unit Of Measurement</th>
                                            <th class="text-center" width="15%">Harga Order</th>
                                            <th class="text-center" width="10.5%">Pengurang</th>
                                            <th class="text-center" width="10%">Total Harga</th>
                                            <th class="text-center" width="1%">Aksi</th>
                                        </tr>
                                        <tr>
                                            <td class="va-top">
                                                <select
                                                    name="nama_obat"
                                                    id="nama_obat"
                                                    class="form-control select2"
                                                    tabindex="9"
                                                    style="width: 100%">
                                                </select>
                                            </td>
                                            <td class="va-top">
                                                <select
                                                    name="uom"
                                                    id="uom"
                                                    class="form-control"
                                                    tabindex="10"
                                                    style="width: 100%"
                                                    disabled="true">
                                                </select>
                                            </td>
                                            <!-- <td class="va-top">
                                                <input
                                                    type="text"
                                                    name="qty_minimum"
                                                    class="form-control qty_minimum text-right"
                                                    id="qty_minimum"
                                                    value="1"
                                                    tabindex="11"
                                                    autocomplete="off"
                                                    style="width: 100%">
                                            </td> -->
                                            <td class="va-top">
                                                <div class="input-group">
                                                    <span class="input-group-addon">Rp</span>
                                                    <input
                                                        type="text"
                                                        name="harga_order"
                                                        class="form-control harga_order text-right"
                                                        id="harga_order"
                                                        autocomplete="off"
                                                        value="0"
                                                        tabindex="12"
                                                        style="width: 100%">
                                                </div>
                                            </td>
                                            <td class="text-right" style="padding-bottom: 0.5%!important;">
                                                <div class="input-group">
                                                    <input
                                                        type="text"
                                                        name="pengurang"
                                                        class="form-control input-sm text-right pengurang"
                                                        id="pengurang"
                                                        autocomplete="off"
                                                        value="0"
                                                        tabindex="13"
                                                        style="width: 100%">
                                                    <span class="input-group-addon">%</span>
                                                </div>
                                                <span class="persen_to_rupiah">
                                                    Rp <span id="pengurang_rp">0</span>
                                                </span>
                                            </td>
                                            <!-- <td class="text-right" style="padding-bottom: 0.5%!important;">
                                                <div class="input-group">
                                                    <input
                                                        type="text"
                                                        name="penambah"
                                                        class="form-control input-sm text-right penambah"
                                                        id="penambah"
                                                        autocomplete="off"
                                                        value="0"
                                                        tabindex="14"
                                                        style="width: 100%">
                                                    <span class="input-group-addon">%</span>
                                                </div>
                                                <span class="persen_to_rupiah">
                                                    Rp <span id="penambah_rp">0</span>
                                                </span>
                                            </td> -->
                                            <td class="text-right">
                                                Rp <span id="total_harga">0</span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-success" tabindex="15" id="btn-tambah">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
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
    var list_no_kontraksupplier = '. json_encode($list_no_kontraksupplier) .';
    var list_detail = {};
', View::POS_END);

$this->registerJs($this->render('../assets/js/kontrak-supplier/kontrak-supplier-create.js'));
?>
