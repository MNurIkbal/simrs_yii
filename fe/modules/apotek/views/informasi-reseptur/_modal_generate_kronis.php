<?php


use yii\web\View;
use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$this->params['breadcrumbs'][] = $title;
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <table id="manage-obat-edit-reseptur" class="table datatable-basic dataTable no-footer" style="margin: 10px 0">
                <thead>
                    <tr class="bg-inverse" style="font-size: 12px">
                        <th style="display: none;"></th>
                        <th style="padding: 10px;">No.</th>
                        <th colspan="2"><?= Yii::t('fe', 'Racikan') ?></th>
                        <th><?= Yii::t('fe', 'Nama Obat Alkes') ?></th>
                        <th title="Harga setelah ditambah embalase"><?= Yii::t('fe', 'Harga') ?><sup>*</sup> (Rp.)</th>
                        <th style="max-width: 200px;"><?= Yii::t('fe', 'Hari') ?></th>
                        <th style="max-width: 200px;"><?= Yii::t('fe', 'Signa') ?></th>
                        <th style="text-align: right;"><?= Yii::t('fe', 'Qty') ?></th>
                        <th><?= Yii::t('fe', 'Satuan') ?></th>
                        <th><?= Yii::t('fe', 'Catatan') ?></th>
                        <th><?= Yii::t('fe', 'Sub Total (Rp.)') ?></th>
                        <th style="width: 5%"><?= Yii::t('fe', 'Aksi') ?></th>
                    </tr>
                </thead>
                <tbody id="list-obat-kronis">
                </tbody>
            <tfoot>
                <?= Html::hiddenInput('penjamin_id', $penjamin_id, ['class' => 'penjaminId']) ?>
                <?= Html::hiddenInput('carabayar_id', $carabayar_id, ['class' => 'carabayarId']) ?>
                <?= Html::hiddenInput('subtotal', '', ['class' => 'subTotalItem']) ?>
                <?= Html::hiddenInput('totalharga_netto', '', ['class' => 'totalharga_netto']) ?>
                <tr>
                    <td class="text-right" colspan="10">Sub Total (Rp.)</td>
                    <td class="subtotal text-right"></td>
                    <td>&nbsp;</td>
                </tr>
                <?= Html::hiddenInput('total', '', ['class' => 'totalItem']) ?>
                <tr>
                    <td class="text-right" colspan="10">Total (Rp.)</td>
                    <td class="total text-right"></td>
                    <td>&nbsp;</td>
                </tr>
            </tfoot>
            </table>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?= Html::submitButton('<i class="fa fa-plus"></i> ' . Yii::t('fe', "Split Resep"), [
        'class'    => 'btn btn-success add',
        'id'       => 'generate-resep-kronis',
        'disabled' => false,
        'style'    => 'margin-top: 18px;'
    ]); ?>
</div>

<?php
$_data_resep = json_encode($data_resep);
$this->registerJs("
    var tempObatRacikan = [];
    var transObat = ".$transApotek.";
    var pasien = \"".$pasien."\";
    var dokter = ".$dokter.";
    var biayaAdmin = ".$biayaadministrasi.";
    var reseptur_id = ".$id_reseptur.";
    var antrian = ".$antrian.";
    var penjualanresep_id = ".$penjualanresep_id.";
    var sep = '".$bpjs."';
    var id = ".$decId.";
    var masterSigna = ".$master_signa.";
    var _data_resep = $_data_resep;
");

$this->registerJs($this->render("js/_modal_generate_kronis.js"));
?>