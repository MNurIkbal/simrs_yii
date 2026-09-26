<?php

/**
 * @Author: Sigit
 * @Date:   2018-12-14 11:27:09
 */

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\helpers\ArrayHelper;
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= Yii::t('fe', 'Permintaan Makan Pasien') ?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="form-horizontal">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label col-sm-4">
                        <?= Yii::t('fe', 'No. Rekam Medik') ?>
                    </label>
                    <div class="col-sm-1">
                        <p class="form-control-static">:</p>
                    </div>
                    <div class="col-sm-7">
                        <p class="form-control-static"><?= $data['no_rekam_medik'] ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label col-sm-4">
                        <?= Yii::t('fe', 'Tanggal Lahir') ?>
                    </label>
                    <div class="col-sm-1">
                        <p class="form-control-static">:</p>
                    </div>
                    <div class="col-sm-7">
                        <p class="form-control-static"><?= $data['tanggal_lahir'] ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-horizontal">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label col-sm-4">
                        <?= Yii::t('fe', 'Tanggal Pendaftaran') ?>
                    </label>
                    <div class="col-sm-1">
                        <p class="form-control-static">:</p>
                    </div>
                    <div class="col-sm-7">
                        <p class="form-control-static"><?= $data['tgl_pendaftaran'] ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label col-sm-4">
                        <?= Yii::t('fe', 'Umur') ?>
                    </label>
                    <div class="col-sm-1">
                        <p class="form-control-static">:</p>
                    </div>
                    <div class="col-sm-7">
                        <p class="form-control-static"><?= $data['umur'] ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-horizontal">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label col-sm-4">
                        <?= Yii::t('fe', 'No. Pendaftaran') ?>
                    </label>
                    <div class="col-sm-1">
                        <p class="form-control-static">:</p>
                    </div>
                    <div class="col-sm-7">
                        <p class="form-control-static"><?= $data['no_pendaftaran'] ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label col-sm-4">
                        <?= Yii::t('fe', 'Dokter DPJP') ?>
                    </label>
                    <div class="col-sm-1">
                        <p class="form-control-static">:</p>
                    </div>
                    <div class="col-sm-7">
                        <p class="form-control-static"><?= $data['dok_dpjp'] ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-horizontal">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label col-sm-4">
                        <?= Yii::t('fe', 'Nama Pasien') ?>
                    </label>
                    <div class="col-sm-1">
                        <p class="form-control-static">:</p>
                    </div>
                    <div class="col-sm-7">
                        <p class="form-control-static"><?= $data['nama_pasien'] ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label col-sm-4">
                        <?= Yii::t('fe', 'Kelas Pelayanan') ?>
                    </label>
                    <div class="col-sm-1">
                        <p class="form-control-static">:</p>
                    </div>
                    <div class="col-sm-7">
                        <p class="form-control-static"><?= $data['kelaspelayanan_nama'] ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-horizontal">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label col-sm-4">
                        <?= Yii::t('fe', 'Jenis Kelamin') ?>
                    </label>
                    <div class="col-sm-1">
                        <p class="form-control-static">:</p>
                    </div>
                    <div class="col-sm-7">
                        <p class="form-control-static"><?= $data['jenis_kelamin'] ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label col-sm-4">
                        <?= Yii::t('fe', 'No. Kamar / No. Bed') ?>
                    </label>
                    <div class="col-sm-1">
                        <p class="form-control-static">:</p>
                    </div>
                    <div class="col-sm-7">
                        <p class="form-control-static"><?= $data['kamarruangan_nokamar'].'/'.$data['no_tempattidur'] ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-horizontal">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label col-sm-4">
                        <?= Yii::t('fe', 'Kasus Penyakit') ?>
                    </label>
                    <div class="col-sm-1">
                        <p class="form-control-static">:</p>
                    </div>
                    <div class="col-sm-7">
                        <p class="form-control-static"><?= $data['jeniskasuspenyakit_nama'] ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label col-sm-4">
                        <?= Yii::t('fe', 'Cara Bayar/Penjamin') ?>
                    </label>
                    <div class="col-sm-1">
                        <p class="form-control-static">:</p>
                    </div>
                    <div class="col-sm-7">
                        <p class="form-control-static"><?= $data['carabayar_nama'].'/'.$data['penjamin_nama'] ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-horizontal">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label col-sm-4">
                        <?= Yii::t('fe', 'Tanggal Permintaan') ?>
                    </label>
                    <div class="col-sm-1">
                        <p class="form-control-static">:</p>
                    </div>
                    <div class="col-sm-7">
                        <p class="form-control-static"><?= $data['tgl_permintaanmakan'] ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label col-sm-4">
                        <?= Yii::t('fe', 'No. Permintaan') ?>
                    </label>
                    <div class="col-sm-1">
                        <p class="form-control-static">:</p>
                    </div>
                    <div class="col-sm-7">
                        <p class="form-control-static"><?= $data['no_permintaanmakan'] ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if ($data['status_permintaanmakan'] != 1): ?>
    <div class="row">
        <div class="form-horizontal">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label col-sm-4">
                        <?= Yii::t('fe', 'Tanggal Pembatalan') ?>
                    </label>
                    <div class="col-sm-1">
                        <p class="form-control-static">:</p>
                    </div>
                    <div class="col-sm-7">
                        <p class="form-control-static"><?= $data['waktu_pembatalan'] ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label col-sm-4">
                        <?= Yii::t('fe', 'No. Pembatalan') ?>
                    </label>
                    <div class="col-sm-1">
                        <p class="form-control-static">:</p>
                    </div>
                    <div class="col-sm-7">
                        <p class="form-control-static"><?= $data['no_pembatalan'] ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif ?>
    <hr>
    <div class="row">
        <div class="table-responsive">
            <table id="tb-permintaan-makan-detail" class="table table-striped table-hover dataTable" style="width:100%;">
                <thead>
                    <tr class="bg-inverse">
                        <th><?=Yii::t('fe', 'No')?></th>
                        <th><?=Yii::t('fe', 'Jenis Diet')?></th>
                        <th><?=Yii::t('fe', 'Menu')?></th>
                        <th><?=Yii::t('fe', 'Waktu Diet')?></th>
                        <th><?=Yii::t('fe', 'Keterangan')?></th>
                        <th><?=Yii::t('fe', 'Jumlah')?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($detail)): ?>
                        <?php $no = 1; ?>
                        <?php foreach ($detail as $key => $value): ?>
                            <tr>
                                <td><?= $no ?></td>
                                <td><?= $value['jenisdiet_nama'] ?></td>
                                <td><?= $value['makanandiet_nama'] ?></td>
                                <td><?= $value['waktu'] ?></td>
                                <td><?= $value['keterangan'] ?></td>
                                <td><?= $value['jumlah'] ?></td>
                            </tr>
                        <?php $no++; ?>
                        <?php endforeach ?>
                    <?php endif ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($data['status_permintaanmakan'] == 1): ?>
    <hr>
    <?php
        $form = ActiveForm::begin([
            'id' => 'form',
            'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL],
            'enableAjaxValidation' => false,
            'enableClientValidation' => false,
        ]);
    ?>
    <?= Html::hiddenInput('id', $id, [
        'id' => 'id',
        'class' => 'form-control',
    ]) ?>
    <div class="hidden" id="div-pembatalan">
        <div class="row">
            <div class="form-group required col-sm-6">
                <?= Html::label(Yii::t('fe', 'Alasan Pembatalan'), 'alasan_pembatalan', [
                    'class' => 'control-label'
                ]) ?>
                <?= Html::textarea('alasan_pembatalan', '', [
                    'id' => 'alasan_pembatalan',
                    'class' => 'form-control',
                ]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 text-right">
                <?= Html::submitButton('<b><i class="fa fa-check"></i></b>'.Yii::t('fe', 'Simpan'), [
                    'id' => 'btn-batal',
                    'class' => 'btn btn-info btn-labeled btn-xs',
                ]) ?>
                <?= Html::button("<b><i class='fa fa-remove'></i></b>".Yii::t('fe', 'Batal'), [
                    'id' => 'btn-batal-simpan',
                    'class' => 'btn btn-danger btn-labeled btn-xs',
                ]) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    <br>
    <div class="row">
        <?= Html::button("<b><i class='fa fa-arrow-left'></i></b>".Yii::t('fe', 'Kembali'), [
            'id' => 'btn-kembali',
            'class' => 'btn bg-slate btn-labeled btn-xs',
            'data-dismiss' => 'modal',
        ]) ?>
        <?= Html::a('<b><i class="fa fa-file-pdf-o"></i></b>'.Yii::t('fe', 'Cetak'), '/gizi/inf-permintaan-makan/cetak-detail?id='.DocoHelpers::encrypt($data['permintaaanmakan_id']).'&status='.$data['status_permintaanmakan'], [
            'id' => 'btn-cetak-detail',
            'class' => 'btn btn-info btn-labeled btn-xs',
            'target' => '_blank',
        ]) ?>
        <?= Html::button("<b><i class='fa fa-remove'></i></b>".Yii::t('fe', 'Pembatalan'), [
            'id' => 'btn-pembatalan',
            'class' => 'btn btn-danger btn-labeled btn-xs',
        ]) ?>
    </div>
    <?php else: ?>
    <hr>
    <div class="row">
        <div class="form-group">
            <?= Html::label(Yii::t('fe', 'Alasan Pembatalan'), 'alasan_pembatalan', [
                'class' => 'control-label'
            ]) ?>
            <?= Html::textarea('alasan_pembatalan', $data['alasan_pembatalan'], [
                'id' => 'alasan_pembatalan',
                'class' => 'form-control',
                'disabled' => 'disabled',
            ]) ?>
        </div>
    </div>
    <br>
    <div class="row">
        <?= Html::button("<b><i class='fa fa-arrow-left'></i></b>".Yii::t('fe', 'Kembali'),[
            'id' => 'btn-kembali',
            'class' => 'btn bg-slate btn-labeled btn-xs',
            'data-dismiss' => 'modal',
        ]) ?>
        <?= Html::a('<b><i class="fa fa-file-pdf-o"></i></b>'.Yii::t('fe', 'Cetak'), '/gizi/inf-permintaan-makan/cetak-detail?id='.DocoHelpers::encrypt($data['permintaaanmakan_id']).'&status='.$data['status_permintaanmakan'], [
            'id' => 'btn-cetak-detail',
            'class' => 'btn btn-info btn-labeled btn-xs',
            'target' => '_blank',
        ]) ?>
    </div>
    <?php endif ?>
</div>

<?php
$this->registerJs($this->render('js/detail.js'), View::POS_END);
?>