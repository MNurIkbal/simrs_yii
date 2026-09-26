<?php

/**
 * @Author: Sigit
 * @Date:   2018-07-24 16:26:57
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-11-06 16:56:05
 */

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\web\View;
?>

<div class="row">
    <div class="col-md-12">
        <div id="div-pemberian-obat">
            <?= Yii::$app->controller->renderAjax('pemberian-obat/form_pemberian_obat', [
                'pendaftaran_id' => $pendaftaran_id,
                'pegawai_id' => $pegawai_id,
                'pemberianObatForm' => $pemberianObatForm,
                'dataPasien' => $dataPasien,
                'listDiagnosa' => $listDiagnosa,
                'listEfek' => $listEfek,
                'listKeterangan' => $listKeterangan,
                'listPegawai' => $listPegawai,
                'listStokObatPasien' => $listStokObatPasien,
                'listJenisObatRiwayat' => $listJenisObatRiwayat,
                'jenisObat' => $jenisObat,
                'status_disabled' => $status_disabled,
                'hide' => $hide,
                'text_diagnosa' => $text_diagnosa,
                'tmpDiag' => $tmpDiag,
                'disabled' => $disabled
            ]) ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div id="div-retur-obat">

        </div>
    </div>
</div>

<?php
$this->registerJs('
    var pendaftaran_id = "'.$pendaftaran_id.'";
', View::POS_END, 'index');
$this->registerJs($this->render('js/index.js'), View::POS_END);
?>