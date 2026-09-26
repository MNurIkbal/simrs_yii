<?php

/**
 * @Author: Sigit
 * @Date:   2018-07-12 10:22:29
 */

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
?>

<?= Html::hiddenInput('isEditReseptur', isset($isEditReseptur) ? $isEditReseptur : false, ['id' => 'is-edit-reseptur']) ?>

<?= $this->render('_reseptur', [
    'diagnosa_nama' => $diagnosa_nama,
    'modelReseptur' => $modelReseptur,
    'pegawai' => $pegawai,
    'listDataApotek' => $listDataApotek,
    'isEditReseptur' => $isEditReseptur,
]); ?>

<div class="row">
    <hr>
</div>

<?= $this->render('_form_non_racikan', [
    'data_pasien' => $data_pasien,
    'modelResepturDetailNonRacikan' => $modelResepturDetailNonRacikan,
    'listDataSigna' => $listDataSigna,
    'isEditReseptur' => $isEditReseptur,
    'encryptedPendaftaranId' => $encryptedPendaftaranId,
    'cppt_id' => $cppt_id,
    'instruksi_id' => $instruksi_id,
]); ?>

<?= $this->render('_form_racikan', [
    'data_pasien' => $data_pasien,
    'modelResepturDetailRacikan' => $modelResepturDetailRacikan,
    'listDataSigna' => $listDataSigna,
    'isEditReseptur' => $isEditReseptur,
    'encryptedPendaftaranId' => $encryptedPendaftaranId,
    'cppt_id' => $cppt_id,
    'instruksi_id' => $instruksi_id,
]); ?>

<?= $this->render('_table_reseptur', [
    'isEditReseptur' => $isEditReseptur,
    'encryptedPendaftaranId' => $encryptedPendaftaranId,
    'cppt_id' => $cppt_id,
    'data_pasien' => $data_pasien,
    'modelReseptur' => $modelReseptur,
    'initObatAlkes' => $initObatAlkes,
    'isUbah' => $isUbah,
    'instruksi_id' => $instruksi_id,
]); ?>

<?php $this->registerJs($this->render('js/terapi_reseptur.js'), View::POS_END); ?>