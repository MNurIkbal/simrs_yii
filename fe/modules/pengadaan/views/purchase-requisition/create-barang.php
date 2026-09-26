<?php

    /**
     * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
     * Powered by Sirs
     */

    use yii\web\View;
    use yii\widgets\Breadcrumbs;
    use app\components\DocoHelpers;
    use kartik\widgets\ActiveForm;
    use kartik\widgets\Select2;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use kartik\widgets\DepDrop;
    use kartik\widgets\DatePicker;


    $this->title = $title;
    $this->params['breadcrumbs'][] = ['label' => $moduleAlias, 'url' => [$modulePath]];
    $this->params['breadcrumbs'][] = $this->title;
?>

<style type="text/css" media="screen">
    #table-barang-pr thead input,
    #table-barang-pr thead textarea{
        padding: 6px 7px;
        margin-bottom: 5px;
    }
    #table-barang-pr thead tr th {
        text-align: center;
    }
    table {
        border-collapse: collapse; width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    tr {
        margin-bottom: 6px;
    }
    .number-align {
        text-align: right;
        max-width: 15px;
    }
    .small {
        font-size: 10px;
    }
    #qty {
        text-align:  right;
    }

    .dataTables_scroll {
        max-height: 550px;
        overflow: auto;
        position: relative;
    }

    ._scroll thead {
        overflow: visible !important;
        position: sticky !important;
        top: 0;
        border: 0px;
        width: 100%;
        z-index: 2;
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
                            'save',
                            'reset'
                        ]);
                    ?>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-white">
                            <div class="panel-heading">
                                <h6 class="panel-title">Informasi Purchase Requisition</h6>
                            </div>
                            <div class="panel-body">
                                <?php
                                    $form = ActiveForm::begin([
                                        'id' => 'pr-form',
                                        'enableAjaxValidation'=>false,
                                        'enableClientValidation'=>false,
                                        'type' => ActiveForm::TYPE_HORIZONTAL,
                                        'formConfig' => [
                                            'labelSpan' => 3,
                                            'deviceSize' => ActiveForm::SIZE_SMALL
                                        ],
                                    ]);
                                    ?>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <?= $form->field($model, 'tgl_pr', [
                                                'horizontalCssClasses' => [
                                                    'label'   => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-8'
                                                ]
                                            ])->textInput(['readonly'=> true]) ?>

                                            <?= $form->field($model, 'instalasi_ruangan', [
                                                'horizontalCssClasses' => [
                                                    'label'   => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-8'
                                                ]
                                            ])->textInput(['readonly'=> true]) ?>
                                        </div>
                                        <div class="col-md-4">
                                            <?= $form->field($model, 'nama_pegawai', [
                                                'horizontalCssClasses' => [
                                                    'label'   => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-8'
                                                ]
                                            ])->textInput(['readonly'=> true]) ?>

                                            <?= $form->field($model, 'reference', [
                                                'horizontalCssClasses' => [
                                                    'label'   => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-8'
                                                ]
                                            ])->textarea(['style' => 'resize:none']) ?>
                                        </div>
                                        <div class="col-md-4">

                                            <div class="form-group highlight-addon field-purchaserequisitionform-is_cyto">
                                                <div class="text-left control-label col-sm-4">
                                                    Jenis PR
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="checkbox">
                                                        <label class="has-star text-left col-sm-4" for="purchaserequisitionform-is_cyto">
                                                            <input type="hidden" name="PurchaseRequisitionForm[is_cyto]" value="0"><input type="checkbox" id="purchaserequisitionform-is_cyto" name="PurchaseRequisitionForm[is_cyto]" value="1">
                                                        Cito
                                                        </label>
                                                        <div class="help-block"></div>
                                                    </div>
                                                    <div class="checkbox">
                                                        <label class="has-star text-left col-sm-4" for="purchaserequisitionform-is_admin">
                                                            <input type="hidden" name="PurchaseRequisitionForm[is_admin]" value="0"><input type="checkbox" id="purchaserequisitionform-is_admin" name="PurchaseRequisitionForm[is_admin]">
                                                            Admin
                                                        </label>
                                                        <div class="help-block"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php ActiveForm::end(); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="panel panel-white">
                            <div class="panel-heading">
                                <h6 class="panel-title">List Barang Purchase Requisition</h6>
                            </div>
                            <div class="panel-body">
                                <div class="dataTables_scroll">
                                    <table class="table table-striped table-condensed table-hover _scroll" style="width:100%" id="table-barang-pr">
                                        <thead>
                                            <tr class="bg-inverse">
                                                <th rowspan="2" class="text-center" width="1">No</th>
                                                <th rowspan="2" class="text-center" style="width: 10%">Kode Barang</th>
                                                <th rowspan="2" class="text-center" style="width: 10%">Nama Barang</th>
                                                <th colspan="3" class="text-center">Pemakaian</th>
                                                <th rowspan="2" class="text-center">DOI</th>
                                                <th rowspan="2" class="text-center">SS Min</th>
                                                <th colspan="2" class="text-center">Stok</th>
                                                <th colspan="3" class="text-center">Qty</th>
                                                <th rowspan="2" class="text-center" style="width: 15%">Satuan</th>
                                                <th rowspan="2" style="width: 10%">Konversi</th>
                                                <th rowspan="2" class="text-center" style="width: 15%">Catatan</th>
                                                <th rowspan="2" class="text-center" width="1">Aksi</th>
                                            </tr>
                                            <tr class="bg-inverse">
                                                <th>7 Hari</th>
                                                <th>14 Hari</th>
                                                <th>30 Hari</th>
                                                <th>Gudang</th>
                                                <th>R. Lain</th>
                                                <th>Outs. PO</th>
                                                <th title="Suggestion">Suggest</th>
                                                <th style="width: 10%">PR</th>
                                            </tr>
                                            <tr style="background: #ffffff;">
                                                <td>&nbsp;</td>
                                                <td colspan="2" style="max-width: 10%;">
                                                    <div class="select2-md" style="max-width: 200px;">
                                                        <select name="nama_barang" class="form-control" style="width: 100%"></select>
                                                    </div>
                                                </td>
                                                <td id="last_7" class="number-align">0</td>
                                                <td id="last_14" class="number-align">0</td>
                                                <td id="last_30" class="number-align">0</td>
                                                <td id="doi" class="number-align">0</td>
                                                <td id="ss_min" class="number-align">0</td>
                                                <td id="stok_gudang" class="number-align">0</td>
                                                <td id="stok_lain" class="number-align">0</td>
                                                <td id="qty_outstanding" class="number-align">0</td>
                                                <td id="qty_suggestion" class="number-align">0</td>
                                                <td>
                                                    <input type="text" id="qty" class="form-control doco-number" style="width: 70px" name="qty">
                                                </td>
                                                <td>
                                                    <div class="select2-md" style="min-width: 100px">
                                                        <select name="satuan_barang" class="form-control small" disabled></select>
                                                    </div>
                                                    <input type="hidden" name="nilai_konversi">
                                                </td>
                                                <td id="konversi">-</td>
                                                <td><textarea name="catatan" cols="1" rows="1" class="form-control small" style="min-width: 100px; resize: none;"></textarea></td>
                                                <td><button class="btn btn-sm btn-success" id="btn-tambah"><i class="fa fa-plus"></i></button></td>
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
</div>

<?php
$this->registerJs('
    var dataUser = '.json_encode($dataUser).';
');
$this->registerJs('var isLargeUnit = '. $is_large_unit_pr .';', View::POS_END);
$this->registerJs($this->render('../assets/js/purchase-requisition/create-barang.js'));
?>
