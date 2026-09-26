<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\web\View;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
<meta charset="<?= Yii::$app->charset ?>">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?=Html::csrfMetaTags() ?>
<title><?= Html::encode($this->title) ?></title>
<!--<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">-->
<script>
var baseUrl = "<?=Url::home();?>";
</script>
<!--<script src="https://cdn.socket.io/socket.io-1.3.5.js"></script>-->
<?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<!-- Page container -->
<div class="page-container">
    <!-- Page content -->
    <div class="page-content">
        <!-- Main content -->
        <div class="content-wrapper">
            <?= $content ?>
        </div>
        <!-- End Main Content -->
    </div>
    <!-- End page Content -->
</div>
<!-- End Page Container -->

<!-- Untuk Kebutuhan Modal Global -->
<div id="modal_backdrop" class="modal fade" style="z-index:1065;" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
        </div>
    </div>
</div>

    <div id="modal_riwayat" class="modal">
        <div class="modal-dialog modal-xl" style="width: 90%;">
            <div class="modal-content">
            </div>
        </div>
    </div>

    <div id="modal-preview" class="modal fade" data-backdrop="static">
        <div class="modal-dialog modal-lg" style="width: 90%;">
            <div class="modal-header bg-inverse" style="z-index: 1050">
                <button type="button" id="dismiss-preview-btn" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">Preview</h5>
            </div>
            <div class="modal-content">
                <div class="preview-wrapper" style="position: relative;" id="preview-wrapper">
                    <!-- <div class="overlay-preview"></div> -->
                    <iframe frameborder="0" id="preview-content" style="width:100%;height:85vh"></iframe>
                </div>
            </div>
        </div>
    </div>
    <div id="modal-preview-img" class="modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

            </div>
        </div>
    </div>

<!-- End -->

<!-- Untuk Kebutuhan Hapus Data dengan konfirmasi -->
<!-- edited by : rizqi febian, 16-01-2018, penambahan form konfirmasi sebelum hapus data -->
<!-- edited by : ramdhan nur, 1-2-2018, penambahan form konfirmasi sebelum hapus data -->
<audio id="audio-player" preload="auto" tabindex="0" controls="" type="audio/mpeg" hidden='true'></audio>
<div class="hidden" id="confirm-form">
    <div class="row">
    <div class="col-md-6 col-md-offset-3 delete-confirm-custom" id="clone">
        <!-- <div class="form-inline"> -->
          <div class="form-group">
            <input type="text" class="form-control input-pemakai" readonly="" value="<?= !empty(Yii::$app->user->identity->nama)
            ?  Yii::$app->user->identity->nama : null?>">
          </div>
          <div class="form-group">
            <input type="password" class="form-control input-sandi" placeholder="Password">
          </div>
        <!-- </div> -->
    </div>
    </div>
</div>

<?php echo Yii::$app->controller->renderPartial('//layouts/footer'); ?>
<?php
$moduleName = Yii::$app->controller->moduleName;
$url = Yii::$app->docoVars->workspace('url');
if(!empty($url) && $url !== '-'){
    $moduleName = preg_replace('/\W/m','', $url);
}

if ($moduleName == 'laboratorium') :
?>

<div id="modal-history-lab" class="modal fade" style="z-index:1065;" data-backdrop="static">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-content">
            <!-- Modal header -->
            <div class="modal-header bg-inverse">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h5 class="modal-title">Perubahan Hasil Pemeriksaan</h5>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <div class="col-md-12">
                    <table id="tb-history-lab" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th><?= Yii::t('fe', 'No') ?></th>
                                <th><?= Yii::t("fe", "Tanggal Perubahan") ?></th>
                                <th><?= Yii::t("fe", "Hasil Sebelumnya") ?></th>
                                <th><?= Yii::t("fe", "Hasil Update") ?></th>
                                <th><?= Yii::t("fe", "Dirubah Oleh") ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<?php
endif;
?>
<?php
if ($moduleName == 'ambulan') {
    $this->registerJsFile(
        '/js/app/ambulance-listener.js',
        [
            'depends' => [
                'app\assets\AppAsset',
            ]
        ]
    );
} else if($moduleName == 'apotek') {
    $this->registerJsFile(
        '/js/app/farmasi-listener.js',
        [
            'depends' => [
                'app\assets\AppAsset',
            ]
        ]
    );
}
if ($moduleName == 'laboratorium') {
    $this->registerJsFile(
        '/js/app/laboratorium-listener.js',
        [
            'depends' => [
                'app\assets\AppAsset',
            ]
        ]
    );
}
if ($moduleName == 'radiologi') {
    $this->registerJsFile(
        '/js/app/radiologi-listener.js',
        [
            'depends' => [
                'app\assets\AppAsset',
            ]
        ]
    );
}
$this->registerJs(Yii::$app->session->hasFlash('errorMsg') ? '
    docoNotification("error", "Terjadi Kesalahan", "' . Yii::$app->session->getFlash('errorMsg') . '")
    ' : '
', View::POS_END, 'main-js-inline');
$this->registerJsFile(
    '/js/app/notification-listener.js',
    [
        'depends' => [
            'app\assets\AppAsset',
        ]
    ]
);
?>
<?php $this->endBody() ?>

<?php
    if (Yii::$app->docoVars->user('nama') == 'superadmin') :
?>
<script>
    $("#flush-cache-btn").bind('click', () => {
        $.ajax({
            url: '/dcms/utility/flush-cache',
            method: 'POST'
        })
    })
</script>
<?php
    endif;
?>
</body>
</html>
<?php $this->endPage() ?>
