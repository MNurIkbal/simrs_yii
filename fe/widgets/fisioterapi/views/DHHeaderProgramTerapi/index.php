<?php 
    use yii\helpers\Html;
    use app\components\DocoHelpers;
?>
<style>
    .no-pad-t {
        padding-top: 0 !important;
        margin-top: 0 !important;
    }
    .no-pad-b {
        padding-bottom: 0 !important;
        margin-bottom: 0 !important;
    }
</style>
<div class="row">
    <div class="form-horizontal">
        <div class="col-md-4">
            <div class="row form-group">
                <div class="col-sm-4">
                    <p class="form-control-static"><?= Yii::t('fe', 'Patient Name') ?></p>
                </div>
                <label class="control-label text-bold col-sm-8">
                    : <?= !empty($data['no_rekam_medik']) ? $data['no_rekam_medik'] : '-' ?> - <?= !empty($data['nama_pasien']) ? $data['nama_pasien'] : '-' ?> (<?= !empty($data['jeniskelamin_nama']) ? $data['jeniskelamin_nama'] : '-' ?>)
                </label>
                <?= Html::hiddenInput('id', $id, ['id' => 'programterapi_id']) ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="row form-group">
                <div class="col-sm-5">
                    <p class="form-control-static"><?= Yii::t('fe', 'Request Date'); ?></p>
                </div>
                <label class="control-label text-bold col-sm-7">
                    : <?= (!is_null($data['tgl_permintaan'])) ? DocoHelpers::convertDate($data['tgl_permintaan'], 'd-M-Y H:i:s') : '-' ?>
                </label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="row form-group">
                <div class="col-sm-4">
                    <p class="form-control-static"><?= Yii::t('fe', 'Therapy Frequency') ?></p>
                </div>
                <label class="control-label text-bold col-sm-8">
                    : <?= !empty($data['frekuensi']) ? htmlspecialchars($data['frekuensi']) : '-' ?>
                </label>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="form-horizontal">
        <div class="col-md-4">
            <div class="row form-group">
                <div class="col-sm-4">
                    <p class="form-control-static"><?= Yii::t('fe', 'Referring Doctor') ?></p>
                </div>
                <label class="control-label text-bold col-sm-8">
                    : <?= !empty($scheduledetailDoctor['dokter_perujuk']) ? htmlspecialchars($scheduledetailDoctor['dokter_perujuk']) : '-' ?>
                </label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="row form-group">
                <div class="col-sm-5">
                    <p class="form-control-static no-pad-b"><?= Yii::t('fe', 'Diagnosis') ?></p>
                </div>
                <label class="control-label text-bold col-sm-7 no-pad-b">
                    : <?= !empty($data['diagnosa']) ? htmlspecialchars($data['diagnosa']) : '-' ?>
                </label>
                <br/>
                <br/>
                <div class="col-sm-5">
                    <p class="form-control-static no-pad-t"><?= Yii::t('fe', 'Diagnosa Penyerta') ?></p>
                </div>
                <label class="control-label text-bold col-sm-7 no-pad-t">
                    : 
                    <?php
                    if (!empty($data['a_diag_penyerta'])) {
                        foreach ($data['a_diag_penyerta'] as $key => $value) {
                    ?>                        
                        <?= htmlspecialchars($value['text']) ?>
                        <br/>
                    <?php
                        }
                    } else { echo "-"; }
                    ?>
                </label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="row form-group">
                <div class="col-sm-4">
                    <p class="form-control-static"><?= Yii::t('fe', 'Notes') ?></p>
                </div>
                <label class="control-label text-bold col-sm-8">
                    : <?= !empty($data['catatan']) ? htmlspecialchars($data['catatan']) : '-' ?>
                </label>
            </div>
        </div>
    </div>
</div>
    