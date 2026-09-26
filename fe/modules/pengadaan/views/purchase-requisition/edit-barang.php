<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
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
                                'href' => '/pengadaan/info-purchase-requisition/barang'
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
                                <div class="col-sm-8 control-label">
                                    <p>
                                        &nbsp;<?= date('d-m-Y',strtotime(ArrayHelper::getValue($header,'tgl_pr','-'))) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-left control-label col-sm-4">
                                    <b><?= Yii::t("fe", "Nama Pegawai") ?></b>
                                </label>
                                <div class="col-sm-8 control-label">
                                    <p>&nbsp;<?= ArrayHelper::getValue($header,'pegawai','-') ?> </p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="text-left control-label col-sm-4">
                                    <b><?= Yii::t("fe", "No. PR") ?></b>
                                </label>
                                <div class="col-sm-8 control-label">
                                    <p>&nbsp;<?= ArrayHelper::getValue($header,'no_pr','-') ?> </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-left control-label col-sm-4">
                                    <b><?= Yii::t("fe", "Ruangan") ?></b>
                                </label>
                                <div class="col-sm-8 control-label">
                                    <p>&nbsp;<?= ArrayHelper::getValue($header,'ruangan','-') ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label class="text-left control-label col-sm-4">
                                    <b><?= Yii::t("fe", "Status PR") ?></b>
                                </label>
                                <div class="col-sm-8 control-label">
                                    <p>&nbsp;<?= ArrayHelper::getValue($header,'status_pr','-') ?> </p>
                                </div>
                            </div>
                            <div class="col-md-6">
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
                                            <th width="24%">
                                                <?=\Yii::t("fe", "Nama");?>
                                            </th>
                                            <th width="15%">
                                                <?=\Yii::t("fe", "Satuan");?>
                                            </th>
                                            <th width="15%">
                                                <?=\Yii::t("fe", "Stok");?>
                                            </th>
                                            <th width="15%">
                                                <?=\Yii::t("fe", "Qty");?>
                                            </th>
                                            <th width="30%">
                                                <?=\Yii::t("fe", "Catatan");?>
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
                                                $idPrimary = $_detail['barang_id'];
                                                $list = isset($konversi[$_detail['barang_id']])
                                                        ? $konversi[$_detail['barang_id']] : [];
                                                $default = isset($nilaiDefault[$_detail['barang_id']])
                                                        ? $nilaiDefault[$_detail['barang_id']] : null;

                                                $_listIncremnt[$_detail['barang_id']] = [
                                                    'id' => $no,
                                                    'purchasereqbrgdetail_id' => $_detail['purchasereqbrgdetail_id'],
                                                    'barang_id' => $_detail['barang_id'],
                                                    'stok' => $_detail['stok'],
                                                    'qty' => (int) $_detail['qty_input'],
                                                    'satuan_id' => $_detail['satuan_id'],
                                                    'catatan' => $_detail['catatan']
                                                ];
                                        ?>
                                            <tr id="<?= $idPrimary ?>">
                                                <td><?= $no ?></td>
                                                <td><?= ArrayHelper::getValue($_detail,'barang_nama','-') ?></td>
                                                <td>
                                                    <select name="InfoPrForm[satuan_id][<?= $idPrimary ?>]" class="form-control input-sm satuan_id selectSatuan select2" data-konversi="1" id="existing_satuan_id_<?= $idPrimary ?>">
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
                                                    <span id="<?= "existing_stok_".$idPrimary ?>">
                                                        <?= ArrayHelper::getValue($_detail,'stok_saatini','-') ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <input type='text'
                                                        name='InfoPrForm[qty][<?= $idPrimary ?>]'
                                                        class='form-control qty input-sm text-right doco-number'
                                                        value="<?= number_format((int)$_detail['qty_input'], 0, ",", ".") ?>">
                                                </td>
                                                <td>
                                                    <input type='text'
                                                        name='InfoPrForm[catatan][<?= $idPrimary ?>]'
                                                        class='form-control catatan input-sm'
                                                        value="<?= $_detail['catatan'] ?>">
                                                </td>
                                                <td>
                                                    <button type="button" class="deleteRow btn btn-danger btn-custom">
                                                        <span class="fa fa-trash"></span>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php
                                                $no++;
                                            endforeach;
                                        ?>
                                            <tr id="tr-default" data-row="<?= count($detail) ?>" data-last="<?= count($detail) ?>">
                                                <td colspan="6" class="text-center">
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
', View::POS_END);
$this->registerJs($this->render('../assets/js/purchase-requisition/edit-barang.js'));
?>
