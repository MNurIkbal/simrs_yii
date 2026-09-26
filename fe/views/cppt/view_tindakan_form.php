<?php

use yii\web\View;
use kartik\form\ActiveForm;
use yii\helpers\Html;

$this->title = $title;
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-<?=$size?> text-center">
            <label><strong> Tanggal Pembatalan : </strong></label>
            <p><?= !empty($data['tgl_batal']) ? date('d/m/Y H:i:s', strtotime($data['tgl_batal'])) : '-' ?></p>
        </div>
        <div class="col-md-<?=$size?> text-center">
            <label><strong>Pegawai : </strong></label>
            <p><?= !empty($data['pegawai_hapus_nama']) ? $data['pegawai_hapus_nama'] : '-' ?></p>
        </div>
        <div class="col-md-<?=$size?> text-center">
            <label><strong>Alasan Pembatalan : </strong></label>
            <p><?= !empty($data['alasan_batal']) ? $data['alasan_batal'] : '-' ?></p>
        </div>
        <?php  if($is_bmhp):  ?>
            <div class="col-md-3 text-center">
                <label><strong>Qty : </strong></label>
                <p><?= $qty ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>
