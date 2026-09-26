<?php

/**
 * @Author: Ilham pramono
 * @Date:   2021-08-05 15:24:43
 */
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;

use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

?>

<style type="text/css">

.strikethrough {
    position: relative;
}
.strikethrough:before {
    position: absolute;
    content: "";
    left: 0;
    top: 50%;
    right: 0;
    border-top: 2px solid #666666!important;
    border-color: inherit;
}

</style>



<div class="modal-header">
    <button type="button" class="close close-modal-rujuk" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $editable ? "Edit" : "Detail" ?> Rujuk Balik</h5>
</div>
<div class="modal-body form-modal-rujuk-balik">
<?php $form = ActiveForm::begin([
    'id' => 'edit-rujuk-balik',
    // 'type' => ActiveForm::TYPE_HORIZONTAL,
    'action' => Url::to(['/rajal/pemeriksaan/save-rujuk-balik', 'id' => $pendaftaran_id, 'rujukbalik_id' => $rujukbalik_id]),
    'enableClientValidation' => false,
    'formConfig' => ['deviceSize' => ActiveForm::SIZE_SMALL]
]) ?>
<div class="row">
    <div class="col-sm-4">
        <div class="row">
            <?= $form->field($model, 'tgl_rujukbalik')->textInput([
                'class' => 'form-control',
                'disabled' => true,
            ])?> 
        </div>
        <div class="row">
            <?= $form->field($model, 'no_srb')->textInput([
                'class' => 'form-control',
                'disabled' => true,
            ])?> 
        </div>
        <div class="row">
            <?= $form->field($model, 'nosep')->textInput([
                'class' => 'form-control',
                'disabled' => true,
            ])?> 
        </div>
        <div class="row">
            <?= $form->field($model, 'no_rekam_medik')->textInput([
                'class' => 'form-control',
                'disabled' => true,
            ])?> 
        </div>
        <div class="row">
            <?= $form->field($model, 'nama_pasien')->textInput([
                'class' => 'form-control',
                'disabled' => true,
            ])?> 
        </div>
    </div>
    <div class="col-sm-4">
        <div class="row">
            <?= $form->field($model, 'alamat')->textArea([
                'class' => 'form-control',
                'disabled' => !$editable,
                'rows' => '4',
            ])?> 
        </div>
        <div class="row">
            <?= $form->field($model, 'email')->textInput([
                'class' => 'form-control',
                'disabled' => !$editable,
            ])?> 
        </div>
        <div class="row">
            <?= $form->field($model, 'kode_dpjp')->textInput([
                'id' => 'kode_dpjp',
                'class' => 'form-control',
                'disabled' => true,
            ])?> 
        </div>
        <div class="row">
            <?= $form->field($model, 'kode_dpjp')->dropDownList($listDpjp, [
                'prompt' => '-- Pilih --',
                'class' => 'form-control',
                'disabled' => !$editable,
            ])->label($model->getAttributeLabel('nama_dokter'))?>
            <?=Html::activeHiddenInput($model, 'nama_dokter')?>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="row">
            <?= $form->field($model, 'saran')->textArea([
                'class' => 'form-control',
                'disabled' => !$editable,
                'rows' => '4',
            ])?> 
        </div>
        <div class="row">
            <?= $form->field($model, 'diagnosa')->dropDownList($listDiagnosa, [
                'prompt' => '-- Pilih --',
                'class' => 'form-control',
                'disabled' => true,
            ])?> 
        </div>
    </div>
</div>
<div class="row">

            <div class="table-responsive">
                <table class="table table-hover" id="tbl-reseptur">
                    <thead>
                        <tr class="bg-inverse">
                            <th style="width: 8%">No</th>
                            <th style="width: 12%">Racikan/Non-Racikan</th>
                            <th style="width: 8%">R ke</th>
                            <th style="width: 30%">Nama Obat</th>
                            <th style="width: 30%">Signa</th>
                            <th style="width: 12%">Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        foreach ($listReseptur as $resep) :
                            $signa = json_decode($resep['signa'], true)?>
                        <tr>
                            <td><?= $no++?></td>
                            <td><?= $resep['racikan_nama'] ?></td>
                            <td><?= empty($resep['rke']) ? '-' : $resep['rke']  ?></td>
                            <td><?= $editable ? 
                                $form->field($model, 'data_reseptur['. $resep['resepturdetail_id'] . '][kode_bpjs]')->dropDownList([
                                    $resep['kode_bpjs'] => $resep['obatalkes_nama'],
                                ], [
                                    'prompt' => 'Pilih Obat',
                                    'class' => 'form-control nama_obat',
                                    'value' => $resep['kode_bpjs'],
                                ])->label(false) .
                                Html::activeHiddenInput($model, 'data_reseptur['. $resep['resepturdetail_id'] . '][obatalkes_nama]', [
                                    'value' => $resep['obatalkes_nama']
                                ]) : 
                                $resep['obatalkes_nama']  ?></td>
                            <td><?= $editable ? 
                                $form->field($model, 'data_reseptur['. $resep['resepturdetail_id'] . '][signa][id]')->dropDownList([
                                    $resep['signa_id'] => (isset($signa['kode']) ? $signa['kode'] . ' ' : '') . $resep['signa_nama'],
                                ], [
                                    'prompt' => 'Pilih Signa',
                                    'class' => 'form-control signa',
                                    'value' => $resep['signa_id'],
                                ])->label(false)  .
                                Html::activeHiddenInput($model, 'data_reseptur['. $resep['resepturdetail_id'] . '][signa][text]', [
                                    'value' => $resep['signa_nama']
                                ]) .
                                Html::activeHiddenInput($model, 'data_reseptur['. $resep['resepturdetail_id'] . '][signa][kode]', [
                                    'value' => isset($signa['kode']) ? $signa['kode'] : ''
                                ]) . 
                                Html::activeHiddenInput($model, 'data_reseptur['. $resep['resepturdetail_id'] . '][qty_signa]', [
                                    'value' => $resep['qty_signa']
                                ]) .
                                Html::activeHiddenInput($model, 'data_reseptur['. $resep['resepturdetail_id'] . '][iterasi_signa]', [
                                    'value' => $resep['iterasi_signa']
                                ]) :
                                (isset($signa['kode']) ? $signa['kode'] . ' ' : '') . $resep['signa_nama']  ?></td>
                            <td><?= $editable ?
                                $form->field($model, 'data_reseptur['. $resep['resepturdetail_id'] . '][qty_reseptur]')->textInput([
                                    'class' => 'form-control docoNumberOnly',
                                    'value' => $resep['qty_reseptur']
                                ])->label(false) : $resep['qty_reseptur']  ?></td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                    <tfoot style="display: none">
                        <tr>
                            <td>&nbsp;</td>
                            <td class="text-bold">TOTAL</td>
                            <td class="text-right text-bold order-summary">Rp. 0</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
    <?= $form->field($model, 'data_reseptur')->hiddenInput(['disabled' => true])->label(false); ?>

</div>
<?php ActiveForm::end() ?>
</div>
<?php if($editable) : ?>
<div class="modal-footer text-right">
    <button type='button' style='margin-right: 5px' id="btn-save-rujuk-balik" class='btn btn-labeled btn-info btn-xs'>
        <b><i class='fa fa-save'></i></b> Simpan
    </button>
</div>
<?php endif; ?>

<?php 
    $this->registerJs("".$this->render('edit.js'), View::POS_END, 'js');
?>