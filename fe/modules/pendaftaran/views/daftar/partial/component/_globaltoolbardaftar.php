<?php
    use app\components\DocoHelpers;
    use yii\helpers\Url;
    use yii\web\View;
?>

<style>
.dropdown-menu li a[disabled],
.dropdown-menu li a[disabled]:hover,
.dropdown-menu li a[disabled]:focus {
    pointer-events: none !important;
    opacity: 0.5 !important;
    color: #999 !important;
    background-color: transparent !important;
    cursor: not-allowed !important;
}
</style>

<div class="panel-toolbar clearfix">
    <?php
        if ($param == 'igd') {
            $createsep = 'hidden';
        }else {
            $createsep = '';
        }

        $printAntrianPoli = ($param == 'rajal') ? true : false;

        $tableID = 'table-daftar-terakhir';
    ?>

    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"> 
            Print <span class="caret"></span> 
        </button>
        <ul class="dropdown-menu">
            <li>
                <a href="#"
                id="btn-print-karcis"
                class="btn-print-pasien-terakhir"
                data-options="click"
                data-target="<?= Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-karcis' ?>"
                >
                    <span class="glyphicon glyphicon-print"></span> Karcis
                </a>
            </li>
            <li>
                <a href="#"
                id="btn-print-status-pasien"
                class="btn-print-pasien-terakhir"
                data-options="click"
                data-target="<?= Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-status-pasien' ?>"
                >
                    <span class="glyphicon glyphicon-print"></span> Tracer
                </a>
            </li>
            <li>
                <a href="#"
                id="btn-print-kartu-pasien"
                class="btn-print-pasien-terakhir"
                data-options="click"
                data-target="<?= Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-kartu-pasien' ?>"
                >
                    <span class="glyphicon glyphicon-print"></span> Kartu Pasien
                </a>
            </li>
            <li>
                <a href="#"
                id="btn-print-gelang-pasien-dewasa"
                class="btn-print-pasien-terakhir"
                data-options="click"
                data-target="<?= Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-gelang-pasien' ?>"
                >
                    <span class="glyphicon glyphicon-print"></span> Gelang Dewasa
                </a>
            </li>
            <li>
                <a href="#"
                id="btn-print-gelang-pasien-anak"
                class="btn-print-pasien-terakhir"
                data-options="click"
                data-target="<?= Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-gelang-pasien-anak' ?>"
                >
                    <span class="glyphicon glyphicon-print"></span> Gelang Anak
                </a>
            </li>
            <li>
                <a href="#"
                id="btn-print-label-pasien"
                class="btn-print-pasien-terakhir"
                data-options="click"
                data-target="<?= Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-label-pasien' ?>"
                >
                    <span class="glyphicon glyphicon-print"></span> Gelang Pasien
                </a>
            </li>
            <li>
                <a href="#"
                id="btn-print-label-pasien-multiple"
                data-options="modal"
                data-target="#modal_backdrop"
                data-width="30%"
                data-url="<?= Url::home().Yii::$app->controller->module->id.'/informasi-pasien/pilih-jumlah-cetakan?jenis='.$param.'&primary=' ?>"
                data-conditions="pendaftaran_id,pasien_id,no_pendaftaran"
                >
                    <span class="glyphicon glyphicon-print"></span> Label Pasien Multiple
                </a>
            </li>
            <li>
                <a href="#"
                id="btn-print-sep"
                data-options="click"
                data-target="<?= Url::home().Yii::$app->controller->module->id.'/end-point/print-sep?jenis='.$param.'&pendaftaran_id=' ?>"
                disabled
                <?= ($param != 'rajal' && $param != 'ranap') ? 'disabled' : '' ?>
                >
                    <span class="glyphicon glyphicon-print"></span> Print SEP
                </a>
            </li>
            <li>
                <a href="#"
                id="btn-print-r2k"
                class="btn-print-pasien-terakhir"
                data-options="click"
                data-target="<?= Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-r2k' ?>"
                <?= ($param != 'rajal') ? 'disabled' : '' ?>
                >
                    <span class="glyphicon glyphicon-print"></span> R2K
                </a>
            </li>
            <li>
                <a href="#"
                id="btn-print-r2mk"
                class="btn-print-pasien-terakhir"
                data-options="click"
                data-target="<?= Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-r2mk' ?>"
                <?= ($param != 'ranap') ? 'disabled' : '' ?>
                >
                    <span class="glyphicon glyphicon-print"></span> R2MK
                </a>
            </li>
            <li>
                <a href="#"
                id="btn-print-label-bed"
                class="btn-print-pasien-terakhir"
                data-options="click"
                data-target="<?= Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-label-bed' ?>"
                <?= ($param != 'ranap') ? 'disabled' : '' ?>
                >
                    <span class="glyphicon glyphicon-print"></span> Label Bed
                </a>
            </li>
            <li>
                <a href="#"
                id="btn-print-antrian-poli"
                class="<?= ($printAntrianPoli) ? '' : 'hidden' ?>"
                data-options="click"
                data-target="<?= Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/print-antrian-poli' ?>"
                >
                    <span class="glyphicon glyphicon-print"></span> Antrian Poli
                </a>
            </li>
        </ul>
    </div>

    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"> 
            SEP <span class="caret"></span> 
        </button>
        <ul class="dropdown-menu">
            <li>
                <a href="#"
                id="btn-create-sep"
                class="<?= $createsep ?>"
                data-options="modal"
                data-target="#modal_backdrop"
                data-width="90%"
                data-url="<?= $module.'/get-form-bpjs?params='.$param.'-' ?>"
                disabled
                >
                    <span class="glyphicon glyphicon-pencil"></span> Create SEP
                </a>
            </li>
            <li>
                <a href="#"
                id="btn-create-sep-manual"
                data-options="modal"
                data-target="#modal_backdrop"
                data-width="90%"
                data-url="<?= $module.'/get-form-bpjs-manual?params='.$param ?>"
                disabled
                >
                    <span class="glyphicon glyphicon-pencil"></span> Input SEP
                </a>
            </li>
            <li>
                <a href="#"
                id="pengajuan-sep"
                data-options="modal"
                data-target="#modal_backdrop"
                data-url="<?= Url::home().Yii::$app->controller->module->id.'/informasi-pasien/pengajuan-sep' ?>"
                disabled
                >
                    <span class="glyphicon glyphicon-pencil"></span> Pengajuan SEP
                </a>
            </li>

        </ul>
    </div>
</div>
