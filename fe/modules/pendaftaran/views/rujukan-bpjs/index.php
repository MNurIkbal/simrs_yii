<?php
/**
 * @Author: Sigit
 * @Date:   2018-09-26 10:37:09
 */

use app\components\DocoHelpers;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Pendaftaran'), 'url' => ['/']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title">
                            <b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title) ?></b>
                        </h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params["breadcrumbs"])) ?>
                    </div>
                </div>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'search',
                    'reset',
                    'print-rujukan' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Print Rujukan'),
                        'icon' => 'fa fa-print',
                        'method' => 'not-exist',
                        'attributes' => [
                            'id' => 'btn-print-rujukan',
                            'class' => 'btn-print-rujukan',
                            'data-options' => 'click',
                            'data-target' => Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-rujukan'
                        ]
                    ],
                    'add',
                    'update' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Edit'),
                        'icon' => 'fa fa-pencil',
                        'method' => '',
                        'attributes' => [
                            'data-target' => Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/update?id=',
                        ] 
                    ],
                    'batal' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Hapus'),
                        'icon' => 'fa fa-ban',
                        'attributes' => [
                            'id' => 'data-hapus',
                            'data-options' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'data-url' => Url::home() . 'pendaftaran/rujukan-bpjs/confirm-hapus?rujukanbpjs_id=',
                            'data-params' => 'rujukanbpjs_id'
                        ]
                    ],
                ], '#tb-rujukan-bpjs') ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="advanced-filter"></div>
                    </div>
                </div>
                <table id="tb-rujukan-bpjs" class="table table-striped" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th>&nbsp;</th>
                            <th><?= Yii::t("fe", "No") ?></th>
                            <th><?= Yii::t("fe", "No. Rujukan") ?></th>
                            <th><?= Yii::t("fe", "Tanggal Rujukan") ?></th>
                            <th><?= Yii::t("fe", "RI/RJ") ?></th>
                            <th><?= Yii::t("fe", "No. SEP") ?></th>
                            <th><?= Yii::t("fe", "No. Kartu") ?></th>
                            <th><?= Yii::t("fe", "Nama") ?></th>
                            <th><?= Yii::t("fe", "PPK Rujuk") ?></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs('
    var no = "'.(\Yii::t("fe", "No")).'";
    var noRujukan = "'.(\Yii::t("fe", "No. Rujukan")).'";
    var tanggalRujukan = "'.(\Yii::t("fe", "Tanggal Rujukan")).'";
    var riRj = "'.(\Yii::t("fe", "RI/RJ")).'";
    var noSep = "'.(\Yii::t("fe", "No. SEP")).'";
    var noKartu = "'.(\Yii::t("fe", "No. Kartu")).'";
    var nama = "'.(\Yii::t("fe", "Nama")).'";
    var ppkRujuk = "'.(\Yii::t("fe", "PPK Rujuk")).'";

    var emptyTable = "'.(\Yii::t("fe", "Tidak ada data yang tersedia")).'";
    var info = "'.(\Yii::t("fe", "Menampilkan _START_ sampai _END_ dari _TOTAL_ data")).'";
    var infoEmpty = "'.(\Yii::t("fe", "Menampilkan 0 sampai 0 dari 0 data")).'";
    var infoFiltered = "'.(\Yii::t("fe", "(disaring dari _MAX_ total data)")).'";
    var lengthMenu = "'.(\Yii::t("fe", "Menampilkan _MENU_ data")).'";
    var loadingRecords = "'.(\Yii::t("fe", "Memuat...")).'";
    var processing = "'.(\Yii::t("fe", "Memproses...")).'";
    var search = "'.(\Yii::t("fe", "Cari:")).'";
    var zeroRecords = "'.(\Yii::t("fe", "Tidak ada data yang ditemukan")).'";
    var sortAscending = "'.(\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terkecil ke yang terbesar")).'";
    var sortDescending = "'.(\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terbesar ke yang terkecil")).'";

    var date = "'.date('d-M-Y', strtotime('NOW')).'";
    var errorTitle = "'.Yii::t('fe', 'Proses Gagal.').'";
    var errorMessage = "'.Yii::t('fe', 'Data pendaftaran online tidak ditemukan/sudah diproses.').'";

    var inputTanggalRujukan = \''.(preg_replace("/[\n\t\r]/i", '', Html::textInput('tanggal_rujukan', '', ['class' => 'form-control daterange', 'id' => 'tanggal_rujukan']))).'\';
', View::POS_END, 'index');

$this->registerJs($this->render('js/index.js'), View::POS_END);
?>