<?php

    /**
     * @author : Anggoro (tri.anggoro@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */

    use yii\web\View;
    use yii\widgets\Breadcrumbs;
    use app\components\DocoHelpers;
    use kartik\widgets\ActiveForm;

    $this->title = $title;
    $this->params['breadcrumbs'][] = ['label' => $moduleAlias, 'url' => [$modulePath]];
    $this->params['breadcrumbs'][] = $this->title;
?>

<style type="text/css" media="screen">
    table {
        border-collapse: collapse; width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    #table-obat-pr thead input,
    #table-obat-pr thead textarea{
        padding: 6px 7px;
        margin-bottom: 5px;
    }

    #table-obat-pr textarea{
        padding: 6px 7px;
        margin-bottom: 5px;
    }

    #table-obat-pr thead tr th {
        text-align: center;
    }

    .number-align {
        text-align: right;
        max-width: 15px;
    }

    #qty {
        text-align:  right;
    }

    tr {
        margin-bottom: 6px;
    }

    .small {
        font-size: 10px;
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

    /*.tableFixHead { 
        overflow: auto; max-height: 300px; min-height: 150px;
    }
    
    .tableFixHead thead {
        position: sticky; top: 0; z-index: 1;
    }*/
</style>
<style>
  .my-legend .legend-title {
    text-align: left;
    margin-bottom: 8px;
    font-weight: bold;
    font-size: 90%;
    }
  .my-legend .legend-scale ul {
    margin: 0;
    padding: 0;
    float: left;
    list-style: none;
    }
  .my-legend .legend-scale ul li {
    display: block;
    float: left;
    width: 50px;
    margin-bottom: 6px;
    margin-right: 5px;
    text-align: center;
    font-size: 80%;
    list-style: none;
    }
  .my-legend ul.legend-labels li span {
    display: block;
    float: left;
    height: 15px;
    width: 50px;
    border: solid 0.2px;
    }
  .my-legend .legend-source {
    font-size: 70%;
    color: #999;
    clear: both;
    }
  .my-legend a {
    color: #777;
    }

  .square-sukses {
    height: 30px;
    width: 120px;
    background-color: #26A65B;
    color:#ffffff;
    padding: 5px 0 5px 10px;
    margin-right:20px;
    }
  .square-batal {
    height: 30px;
    width: 70px;
    background-color: #D24D57;
    color:#ffffff;
    padding: 5px 0 5px 10px;
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
                            'reset',
                            'custom-generate' => [
                                'type' => 'button',
                                'title' => Yii::t('fe', 'Rekomendasi Order'),
                                'icon' => 'fa fa-gears',
                                'attributes' => [
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal_ro',
                                    'action' => '/pengadaan/purchase-requisition/recommendation-order',
                                ]
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
                                            ])->textInput(['readonly'=> true])  ?>

                                            <?= $form->field($model, 'reference', [
                                                'horizontalCssClasses' => [
                                                    'label'   => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-8'
                                                ]
                                            ])->textarea(['style' => 'resize:none']) ?>
                                        </div>
                                        <div class="col-md-4">

                                            <div class="form-group highlight-addon field-purchaserequisitionform-is_cyto">
                                                <div class="text-left control-label col-sm-2">
                                                    Jenis PR
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="checkbox">
                                                        <label class="has-star text-left col-sm-4" for="purchaserequisitionform-is_cyto">
                                                            <input type="hidden" name="PurchaseRequisitionForm[is_cyto]" value="0"><input type="checkbox" id="purchaserequisitionform-is_cyto" name="PurchaseRequisitionForm[is_cyto]">
                                                        Cito
                                                        </label>
                                                        <div class="help-block"></div>
                                                    </div>
                                                    <div class="checkbox">
                                                        <label class="has-star text-left col-sm-4" for="purchaserequisitionform-is_consignment">
                                                            <input type="hidden" name="PurchaseRequisitionForm[is_consignment]" value="0"><input type="checkbox" id="purchaserequisitionform-is_consignment" name="PurchaseRequisitionForm[is_consignment]">
                                                        Consignment
                                                        </label>
                                                        <div class="help-block"></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="checkbox">
                                                        <label class="has-star text-left col-sm-4" for="purchaserequisitionform-is_admin">
                                                            <input type="hidden" name="PurchaseRequisitionForm[is_admin]" value="0"><input type="checkbox" id="purchaserequisitionform-is_admin" name="PurchaseRequisitionForm[is_consignment]">
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
                                <h6 class="panel-title">List Obat Purchase Requisition</h6>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class='my-legend'>
                                            <div class='legend-title'>Keterangan</div>
                                            <div class='legend-scale'>
                                            <ul class='legend-labels'>
                                                <li><span style='background:antiquewhite;'></span>Reorder <p>Tidak</p></li>
                                                <li><span style='background:#fff;'></span>Reorder <p>Ya</p></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="dataTables_scroll">
                                    <table class="table table-striped table-condensed table-hover _scroll" style="width:100%;" id="table-obat-pr">
                                        <thead>
                                            <tr class="bg-inverse small">
                                                <th width="1" rowspan="2">No</th>
                                                <th style="width: 10%" rowspan="2">Kode Obat</th>
                                                <th style="width: 10%" rowspan="2">Nama Obat</th>
                                                <th colspan="3">Pemakaian</th>
                                                <th rowspan="2">DOI</th>
                                                <th rowspan="2">SS Min</th>
                                                <th rowspan="2">Kriteria</th>
                                                <th colspan="3">Stok</th>
                                                <th colspan="3">Qty</th>
                                                <th style="width: 10%" rowspan="2">Satuan</th>
                                                <th style="width: 10%" rowspan="2">Konversi</th>
                                                <th style="width: 15%" rowspan="2">Catatan</th>
                                                <th width="1" rowspan="2">Aksi</th>
                                            </tr>
                                            <tr class="bg-inverse small">
                                                <th>7 Hari</th>
                                                <th>14 Hari</th>
                                                <th>30 Hari</th>
                                                <th>Gudang</th>
                                                <th>Farmasi</th>
                                                <th>R. Lain</th>
                                                <th>Outs. PO</th>
                                                <th title="Suggestion">Suggest</th>
                                                <th style="width: 10%">PR</th>
                                            </tr>
                                            <tr class="small" style="background: #ffffff;">
                                                <td>&nbsp;</td>
                                                <td colspan="2" style="max-width: 10%;">
                                                    <div class="select2-md" style="max-width: 200px;">
                                                        <select name="nama_obat" class="form-control small" style="width: 100%"></select>
                                                    </div>
                                                </td>
                                                <td id="last_7" class="number-align">0</td>
                                                <td id="last_14" class="number-align">0</td>
                                                <td id="last_30" class="number-align">0</td>
                                                <td id="doi" class="number-align">0</td>
                                                <td id="ss_min" class="number-align">0</td>
                                                <td id="kriteria"></td>
                                                <td id="stok_gudang" class="number-align">0</td>
                                                <td id="stok_farmasi" class="number-align">0</td>
                                                <td id="stok_lain" class="number-align">0</td>
                                                <td id="qty_outstanding" class="number-align">0</td>
                                                <td id="qty_suggestion" class="number-align">0</td>
                                                <td><input type="text" id="qty" class="form-control doco-number small" style="width: 70px" name="qty"></td>
                                                <td>
                                                    <div class="select2-md">
                                                        <select name="satuan_obat" class="form-control small" style="width: 100%" disabled></select>
                                                    </div>
                                                    <input type="hidden" name="nilai_konversi">
                                                </td>
                                                <td id="konversi">-</td>
                                                <td><textarea maxlength="250" name="catatan" cols="1" rows="1" class="form-control small" style="width: 70px; resize: none;"></textarea></td>
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

<div id="modal_ro" class="modal fade">
    <div class="modal-dialog modal">
        <div class="modal-content">
        </div>
    </div>
</div>

<div id="modal_list_jenis_obat" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>

<?php
    $this->registerJs('
        var dataUser = '.json_encode($dataUser).';
    ');
    $this->registerJs('var isLargeUnit = '. $is_large_unit_pr .';', View::POS_END);
    $this->registerJs($this->render('../assets/js/purchase-requisition/create.js'));
?>
