<?php


use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use yii\widgets\Breadcrumbs;
use app\components\DocoTableHelper;
use kartik\datetime\DateTimePicker;

$this->title = \Yii::t('fe', 'Riwayat Pasien');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('fe', 'Rawat Darurat'), 'url' => ['/igd']];
$this->params['breadcrumbs'][] = $title;
?>

<style lang="">
    .info-pasien {
        width: 70px;
        height: 30px;
        border-radius: 5px;
        border: 1px solid black;
        float: left;
        margin: 3px;
    }

    .btn-riwayat {
        margin-bottom: 5px;
        margin-right: 5px;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-body">
                <table id="table-riwayat" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead class="text-center">
                        <tr class="bg-inverse">
                            <th><?= Yii::t('fe', 'Tanggal kunjungan / No pendaftaran') ?></th>
                            <th><?= Yii::t('fe', 'Ruangan / Kamar') ?></th>
                            <th><?= Yii::t('fe', 'Dokter pemeriksa') ?></th>
                            <th><?= Yii::t('fe', 'Pelayanan pasien') ?></th>
                            <th><?= Yii::t('fe', 'Penunjang') ?></th>
                            <th><?= Yii::t('fe', 'Cara keluar') ?></th>
                        </tr>
                    </thead>
                    <tbody> 
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs("
    var table;

    $(document).ready(function(){
        table = $('#table-riwayat').docoTabel({
            filter: false,
            sorting: [[0, 'asc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: '/igd/pemeriksaan-igd/get-data-riwayat-pasien?id='+'$id'+'&norm='+'$norm',
            columns: [
                {title: '".(\Yii::t('fe', 'Tanggal kunjungan / No pendaftaran'))."',  data: 'tgl_pendaftaran', name: 'tgl_pendaftaran'},
                {title: '".(\Yii::t('fe', 'Ruangan / Kamar'))."',  data: 'ruangan_pend'},
                {title: '".(\Yii::t('fe', 'Dokter pemeriksa'))."',  data: 'dok_rjrd'},
                {title: '".(\Yii::t('fe', 'Pelayanan pasien'))."',  data: 'aksi_pelayanan'},
                {title: '".(\Yii::t('fe', 'Penunjang'))."',  data: 'aksi_penunjang'},
                {title: '".(\Yii::t('fe', 'Cara keluar'))."',  data: 'cara_keluar'},
            ],
        });
    });
");

?>