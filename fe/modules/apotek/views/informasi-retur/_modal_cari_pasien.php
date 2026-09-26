<?php

use yii\web\View;
use app\components\DocoHelpers;
use kartik\select2\Select2;
use kartik\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = $this->title;

?>

<style type="text/css">
.search-nama-pasien,
.informasi-resep {
    margin-top: 1%;
}

.data-pasien {
    display: none;
}

.tbl-header tr:nth-child(even) {
    font-weight: bold;
}

.tbl-header td {
    width: 16.5%;
}

.tbl-detail thead tr th {
    text-align: center;
}

.tbl-detail tbody tr td {
    text-align: center;
}

.tbl-proses tr td {
    text-align: center;
    width: 20%;
    vertical-align: top !important;
}

.tbl-proses tr:nth-child(1) td {
    font-weight: bold;
    font-size: 16pt;
}

label#pasien {
    margin-top: 0.5%;
}

.button-ok{
    background-color: #34bfa3;
    color: #ffffff;
    flex: 1;
}
.button-ok:hover{
    background-color: #1ca189;
    color: #ffffff;
}

.bold {
    font-weight: bold;
}

.detail-pasien {
    margin-bottom: 1%;
}

.cancel-receipt {
    display: none;
}

.panel-warning > .panel-heading {
    background: #ffc107;
    color: #343a40;
}
</style>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-body">
                
                <?php echo $this->render('_pencarian', ['model' => $model]); ?>
                <div class="data-pasien">
                    <div class="col-md-12">
                        <hr>
                    </div>
                    <div id="example" class="col-md-12 detail-pasien">
                        <table class="table table-bordered table-condensed tbl-detail">
                            <thead class="bg-inverse">
                                <tr>
                                    <th width="1" class="text-center">No</th>
                                    <th><?= Yii::t('fe', 'Tanggal Pendaftaran') ?></th>
                                    <th><?= Yii::t('fe', 'No. Pendaftaran') ?></th>
                                    <th><?= Yii::t('fe', 'Instalasi - Ruangan Akhir') ?></th>
                                    <th><?= Yii::t('fe', 'Cara Bayar - Penjamin') ?></th>
                                    <th><?= Yii::t('fe', 'Status Periksa') ?></th>
                                    <th><?= Yii::t('fe', 'Aksi') ?></th>
                                </tr>
                            </thead>
                            <tbody id="detail-pasien">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
</div>

<?php
    $this->registerJs($this->render('js/index.js'), View::POS_END);
?>
