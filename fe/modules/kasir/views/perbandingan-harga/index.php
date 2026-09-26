<?php

use yii\web\View;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\typeahead\Typeahead;
use yii\web\JsExpression;
use app\components\DHtml;

$this->title = DHtml::getTitleMenu();
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', $this->title), 'url' => ['index']];

?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
                            <?php echo Breadcrumbs::widget([
                                  'homeLink' => [ 
                                        'label' => Yii::t('fe', 'Home'),
                                        'url' => Yii::$app->homeUrl,
                                    ],
                                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                               ]); 
                            ?>
                    </div>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-3">
                            <?= DHtml::dropDownList('list-pendaftaran', null, [], [
                                'id' => 'list-pendaftaran',
                                'class' => 'selectCategory',
                                'prompt' => '-- Pilih Pendaftaran --'
                            ]) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="col-md-12" id="informasi" style="display: none">
                    <div class="panel panel-default">
                        <a id="info-heading" data-toggle="collapse" href="#infopasien" role="button" aria-expanded="false" aria-controls="infopasien" >
                            <div class="panel-heading flex-container">
                                <h6 class="panel-title">Informasi Pasien</h6>
                                <p class="p-data" id="data-pasien"></p>
                                <ul class="icons-list">
                                    <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                                </ul>
                            </div>
                        </a>

                        <div class="panel-body collapse multi-collapse info-card" id="infopasien">
                            <div class="col-xs-2">
                                <div class="border-img">
                                    <img 
                                        class="img-responsive" 
                                        src="/media/img/icon-app/default.jpg" 
                                        alt="" 
                                        style="width: 100%;height: auto;max-width: 114px;">
                                </div>
                            </div>

                            <div class="col-xs-9">
                                <div class="row">
                                    <div class="col-xs-4">
                                        <b class="text-left control-label font-design">Nama Pasien</b>
                                        <br>
                                        <p class="nama_pasien"></p>
                                    </div>
                                    <div class="col-xs-4">
                                        <b class="text-left control-label font-design">No Rekam Medik</b>
                                        <br>
                                        <p class="no_rekam_medik"></p>
                                    </div>
                                    <div class="col-xs-4">
                                        <b class="text-left control-label font-design">Tanggal Lahir</b>
                                        <br>
                                        <p class="tanggal_lahir"></p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-4">
                                        <b class="text-left control-label font-design">Tanggal Pendaftaran</b>
                                        <br>
                                        <p class="tgl_pendaftaran"></p>
                                    </div>
                                    <div class="col-xs-4">
                                        <b class="text-left control-label font-design">No Pendaftaran</b>
                                        <br>
                                        <p class="no_pendaftaran"></p>
                                    </div>
                                    <div class="col-xs-4">
                                        <b class="text-left control-label font-design">Instalasi Akhir</b>
                                        <br>
                                        <p class="instalasi_nama"></p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-4">
                                        <b class="text-left control-label font-design">Ruangan Akhir</b>
                                        <br>
                                        <p class="ruangan_nama"></p>
                                    </div>
                                    <div class="col-xs-4">
                                        <b class="text-left control-label font-design">Cara Bayar</b>
                                        <br>
                                        <p class="cara_bayar"></p>
                                    </div>
                                    <div class="col-xs-4">
                                        <b class="text-left control-label font-design">Penjamin</b>
                                        <br>
                                        <p class="penjamin_nama"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-offset-4 col-md-4" id="pembandingan-tagihan" style="display: none">
                    <div class="panel panel-default">
                        <div class="panel-heading flex-container">
                            <h6 class="panel-title">PERBANDINGAN TAGIHAN</h6>
                        </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-6">
                                <b class="text-left control-label">Kelas Pelayanan</b>
                                <br>
                                <p class="kelas_pelayanan_nama font-design"></p>
                            </div>
                            <div class="col-xs-6 required">
                                <label class="control-label has-star"><strong>Kelas Pembanding</strong></label>
                                <br>
                                <?= DHtml::dropDownList('kelas-pembanding', null, 
                                        ArrayHelper::map($kelasPelayanan, 'kelaspelayanan_id', 'kelaspelayanan_nama'), 
                                        [
                                            'id' => 'kelas-pembanding',
                                            'class' => 'select2',
                                            'prompt' => '-- Pilih Kelas --'
                                        ])
                                ?>
                            </div>
                        </div>
                        <hr style="margin-top:4px!important;margin-bottom:4px!important;">
                        <div class="row text-center">
                                <button type="button" 
                                    id="btn-pembanding" 
                                    class="btn btn-info btn-labeled btn-xs"
                                    data-toggle="modal"
                                    data-target="#modal_backdrop"
                                    data-width="75%"
                                    disabled 
                                    >
                                <b><i class="fa fa-balance-scale"></i></b>Bandingkan</button>
                        </div>
                    </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs($this->render('js/index.js'), View::POS_END); 
?>