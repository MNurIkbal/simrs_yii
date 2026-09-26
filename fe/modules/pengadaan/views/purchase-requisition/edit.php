<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\Html;
use app\components\DHtml;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use kartik\widgets\ActiveForm;

$this->title = $title;
$this->params['breadcrumbs'][] = [
    'label' => Yii::$app->docoVars->workspace("modul_alias"),
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;

?>
<style>
    table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }

    #detail-pr thead tr th {
        text-align: center;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
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
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?=
                    DocoHelpers::generateToolbar([
                        'back' => [
                            'attributes' => [
                                'href' => '/pengadaan/info-purchase-requisition'
                            ]
                        ],
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
            <div class="panel-body">
                <?php
                    $form = ActiveForm::begin([
                        'id' => 'pr-edit-form',
                        'enableAjaxValidation'=>false,
                        'enableClientValidation'=>false,
                        'type' => ActiveForm::TYPE_HORIZONTAL,
                        'action' => "/pengadaan/purchase-requisition/update?id={$id}",
                        'formConfig' => [
                            'labelSpan' => 3,
                            'deviceSize' => ActiveForm::SIZE_SMALL
                        ],
                        'options' => [
                            'skip-confirm' => "true"
                        ]
                ]);
                ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h6 class="panel-title">Edit Purchase Request</h6>
                    </div>

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="text-left control-label col-sm-4">
                                    <b><?= Yii::t("fe", "Tanggal PR") ?></b>
                                </label>
                                <div class="col-sm-5">
                                    <p>
                                        <b>:</b>&nbsp;<?= ArrayHelper::getValue($header, 'created_date', '-') ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-left control-label col-sm-4">
                                    <b><?= Yii::t("fe", "Nama Pegawai") ?></b>
                                </label>
                                <div class="col-sm-5">
                                    <p><b>:</b>&nbsp;<?= ArrayHelper::getValue($header, 'pegawai', '-') ?> </p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="text-left control-label col-sm-4">
                                    <b><?= Yii::t("fe", "No. PR") ?></b>
                                </label>
                                <div class="col-sm-5">
                                    <p><b>:</b>&nbsp;<?= ArrayHelper::getValue($header, 'no_pr', '-') ?> </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-left control-label col-sm-4">
                                    <b><?= Yii::t("fe", "Ruangan") ?></b>
                                </label>
                                <div class="col-sm-5">
                                    <p><b>:</b>&nbsp;<?= ArrayHelper::getValue($header, 'ruangan', '-') ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="text-left control-label col-sm-4">
                                    <b><?= Yii::t("fe", "Status PR") ?></b>
                                </label>
                                <div class="col-sm-5">
                                    <p><b>:</b>&nbsp;<?= ArrayHelper::getValue($header, 'status_pr', '-') ?> </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-left control-label col-sm-4">
                                    <b><?= Yii::t("fe", "Reference") ?></b>
                                </label>
                                <div class="col-sm-5">
                                    <p><b>:</b>&nbsp;<?= ArrayHelper::getValue($header, 'reference', '-') ?> </p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="text-left control-label col-sm-4">
                                    <b><?= Yii::t("fe", "Cito ") ?></b>
                                </label>
                                <div class="col-sm-5">
                                    <p><b>:</b>&nbsp;<?= ArrayHelper::getValue($header, 'pr_cyto', '-') ?> </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-left control-label col-sm-4">
                                    <b><?= Yii::t("fe", "Tanggal Approval") ?></b>
                                </label>
                                <div class="col-sm-5">
                                    <p>
                                        <b>:</b>&nbsp;<?= ArrayHelper::getValue($header, 'tgl_approve', '-') ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="text-left control-label col-sm-4">
                                    <b><?= Yii::t("fe", "Admin") ?></b>
                                </label>
                                <div class="col-sm-5">
                                    <p><b>:</b>&nbsp;<?= ArrayHelper::getValue($header, 'pr_admin', '-') ?> </p>
                                </div>
                            </div>
                            <?php if (strtolower($type) != DocoConstants::JENIS_BARANG) { ?>
                            <div class="col-md-6">
                                <label class="text-left control-label col-sm-4">
                                    <b><?= Yii::t("fe", "Consignment") ?></b>
                                </label>
                                <div class="col-sm-5">
                                    <p>
                                        <b>:</b>&nbsp;<?= ArrayHelper::getValue($header, 'pr_consignment', '-') ?>
                                    </p>
                                </div>
                            </div>
                            <?php } ?>
                            <div class="col-md-6 col-md-offset-6">
                                <label class="text-left control-label col-sm-4">
                                    <b><?= Yii::t("fe", "Reference") ?></b>
                                </label>
                                <div class="col-sm-8">
                                    <textarea
                                        name="reference"
                                        class="form-control"
                                        placeholder="Reference"><?= $header['reference'] ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h6 class="panel-title">Detail Obat</h6>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <table id="detail-pr" class="table table-condensed">
                                    <thead>
                                        <tr class="bg-inverse">
                                            <th class="" width="1%">
                                                <?=\Yii::t("fe", "No");?>
                                            </th>
                                            <th width="8%">
                                                <?=\Yii::t("fe", "Kode Item");?>
                                            </th>
                                            <th width="18%">
                                                <?=\Yii::t("fe", "Nama Item");?>
                                            </th>
                                            <th width="5%">
                                                <?=\Yii::t("fe", "DOI");?>
                                            </th>
                                            <th width="5%">
                                                <?=\Yii::t("fe", "SSmin");?>
                                            </th>
                                            <th width="8%">
                                                <?=\Yii::t("fe", "Stok");?>
                                            </th>
                                            <th width="8%">
                                                <?=\Yii::t("fe", "Stok Farmasi");?>
                                            </th>
                                            <th width="8%">
                                                <?=\Yii::t("fe", "Stok Gudang");?>
                                            </th>
                                            <th width="8%">
                                                <?=\Yii::t("fe", "Stok Ruangan Lain");?>
                                            </th>                                            
                                            <th width="5%">
                                                <?=\Yii::t("fe", "Qty Suggestion");?>
                                            </th>
                                            <th width="8%">
                                                <?=\Yii::t("fe", "Qty");?>
                                            </th>
                                            <th width="12%">
                                                <?=\Yii::t("fe", "Satuan");?>
                                            </th>
                                            <th width="15%">
                                                <?=\Yii::t("fe", "Catatan");?>
                                            </th>
                                            <th width="8%">
                                                <?=\Yii::t("fe", "Status");?>
                                            </th>
                                            <th>
                                                <?=\Yii::t("fe", "Aksi");?>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $no=1;
                                            $_listIncremnt = [];
                                            foreach ($detail as $_detail):
                                                $idPrimary = $_detail['obatalkes_id'];
                                                $list = isset($konversi[$_detail['obatalkes_id']])
                                                        ? $konversi[$_detail['obatalkes_id']] : [];
                                                $default = isset($nilaiDefault[$_detail['obatalkes_id']])
                                                        ? $nilaiDefault[$_detail['obatalkes_id']] : null;

                                                $_listIncremnt[$_detail['obatalkes_id']] = [
                                                    'id' => $no,
                                                    'purchasereqdetail_id' => $_detail['purchasereqdetail_id'],
                                                    'obatalkes_id' => $_detail['obatalkes_id'],
                                                    'stok' => $_detail['stok'],
                                                    'qty' => (int) $_detail['qty_input'],
                                                    'satuan_id' => $_detail['satuan_id'],
                                                    'catatan' => $_detail['catatan'],
                                                    'ext_st_farmasi' => $_detail['ext_st_farmasi'],
                                                    'ext_st_gudang' => $_detail['ext_st_gudang'],
                                                    'ext_st_lain' => $_detail['ext_st_lain'],
                                                    'ext_qty_sugesstion' => $_detail['ext_qty_sugesstion'],
                                                ];

                                                if($_detail['status_id'] == DocoConstants::VAR_CANCEL_PR || !$is_large_unit_pr) {
                                                    $disabled_satuan = 'disabled';
                                                } else {
                                                    $disabled_satuan = '';
                                                }

                                                $disabled_status = $_detail['status_id'] == DocoConstants::VAR_CANCEL_PR ? 'disabled' : '';
                                        ?>
                                            <tr id="<?= $idPrimary ?>">
                                                <td><?= $no ?></td>
                                                <td><?= ArrayHelper::getValue($_detail,'kode_obat','-') ?></td>
                                                <td><?= ArrayHelper::getValue($_detail,'obatalkes_nama','-') ?></td>
                                                <td><?= ArrayHelper::getValue($_detail,'doi','-') ?></td>
                                                <td><?= ArrayHelper::getValue($_detail,'ssmin','-') ?></td>
                                                <td>
                                                    <span id="<?= "existing_stok_".$idPrimary ?>">
                                                        <?= ArrayHelper::getValue($_detail,'stok_saatini','-') ?>
                                                    </span>
                                                </td>
                                                <td class="<?= "ext_st_farmasi_".$idPrimary ?>"><?= ArrayHelper::getValue($_detail,'stok_farmasi','-') ?></td>
                                                <td class="<?= "ext_st_gudang_".$idPrimary ?>"><?= ArrayHelper::getValue($_detail,'stok_gudang','-') ?></td>
                                                <td class="<?= "ext_st_lain_".$idPrimary ?>"><?= ArrayHelper::getValue($_detail,'stok_ruanganlain','-') ?></td>
                                                <td class="<?= "ext_qty_sugesstion_".$idPrimary ?>"><?= ArrayHelper::getValue($_detail,'stok_sugesstion','-') ?></td>
                                                <td>
                                                    <input type='text'
                                                        name='InfoPrForm[qty][<?= $idPrimary ?>]'
                                                        class='form-control qty input-sm text-right doco-number'
                                                        value="<?= number_format((int)$_detail['qty_input'], 0, ",", ".") ?>" <?= $disabled_status ?>
                                                        style="width: 65px">
                                                </td>
                                                <td>
                                                    <select name="InfoPrForm[satuan_id][<?= $idPrimary ?>]" class="form-control input-sm satuan_id selectSatuan select2" data-konversi="1" id="existing_satuan_id_<?= $idPrimary ?>" <?= $disabled_satuan ?>>
                                                        <?php
                                                            $selected = "";
                                                            foreach ($konversi[$idPrimary] as $key => $value) {
                                                                if($_detail['satuan_id'] == $key) {
                                                                    $selected = "selected";
                                                                } else {
                                                                    $selected = "";
                                                                }
                                                                echo "<option value='".$key."' data-konversi='".$hasil_konversi[$idPrimary][$key]."' data-satuan='".$satuan[$idPrimary][$key]."' ".$selected.">".$value."</option>";
                                                            }
                                                        ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input 
                                                        type='text'
                                                        maxlength="250"
                                                        style="width: 200px;"
                                                        name='InfoPrForm[catatan][<?= $idPrimary ?>]'
                                                        class='form-control catatan input-sm'
                                                        value="<?= $_detail['catatan'] ?>" <?= $disabled_status ?>
                                                    >
                                                </td>
                                                <td><?= $_detail['status'] ?></td>
                                                <td>
                                                    <button type="button" class="deleteRow btn btn-danger btn-custom" <?= $disabled_status ?>>
                                                        <span class="fa fa-trash"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php
                                                $no++;
                                            endforeach;
                                        ?>
                                            <tr id="tr-default" data-row="<?= count($detail) ?>" data-last="<?= count($detail) ?>">
                                                <td colspan="14" class="text-center">
                                                </td>
                                                <td style="height: 50px!important;">
                                                    <button type="button" class="addrow btn btn-info btn-custom">
                                                        <span class="fa fa-plus"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    var _listIncremnt = '. json_encode($_listIncremnt) .';
    var isLargeUnit = '. $is_large_unit_pr .';
    var isConsignment = `'. ArrayHelper::getValue($header, 'is_consignment', '-') .'`;
', View::POS_END);
$this->registerJs($this->render('../assets/js/purchase-requisition/edit.js'));
?>
