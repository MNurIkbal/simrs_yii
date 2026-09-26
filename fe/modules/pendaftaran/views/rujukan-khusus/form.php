<?php

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;


$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rencana Kontrol/Rencana Inap'), 'url' => ['/pendaftaran/rencana-kontrol-inap']];
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
            <div class="panel-body">
                <?php $form = ActiveForm::begin([
                    'id' => 'form',
                    'type' => ActiveForm::TYPE_VERTICAL,
                    'formConfig' => [
                        'labelSpan' => 5,
                        'deviceSize' => ActiveForm::SIZE_SMALL
                    ]
                ]) ?>
                <div class="row">
                    <div class="col-md-3" id="cari_no_rujukan">
                        <?= $form->field($data, 'no_rujukan', [
                                'inputOptions' => [
                                    'id' => 'no_rujukan',
                                    'class' => 'form-control input-sm'
                                ]
                            ])->textInput(['class' => 'no_rujukan']) ?>
                    </div>
                    <div class="col-md-3 ">
                        <div class="d-flex align-items-center my-10">
                        <?= Html::button('<b><i class="fa fa-search"></i></b>'.Yii::t('fe', ' Cari'), 
                                [
                                    'class' => 'btn btn-info btn-labeled btn-xs btn-cari',
                                    'id' => 'cari'
                                ]);
                            ?>
                            <?= Html::button('<b><i class="fa fa fa-refresh"></i></b>'.Yii::t('fe', ' Batal'), 
                                [
                                    'class' => 'btn btn-danger btn-labeled btn-xs',
                                    'id' => 'reset'
                                ]);
                        ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <center><span class="populate-data" style="font-size:16px;font-weight:bold;margin-bottom:10px;"></span></center>
                </div>
                <div class="row" id="form-informasi_create" style="display:none;">
                    <div class="col-9">
                        <div id="form-rujukan-khusus"  style="display:none;">
                        <?php
                            echo Yii::$app->controller->renderPartial('partial/_form-rujukan-khusus', [
                                'data' => $data,
                            ]) 
                        ?>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<?php 
$this->registerJs("
var rencanaKontrol  = {};
// rencanaKontrol.jnsKontrol = $('input[name=\"jenis_rencana\"]:checked').val();
var noKartu = '';

function pilihDpjp(identifier) {
    const kode_poli = $(identifier).data('kode_poli');
    const nama_spesialis = $(identifier).data('nama_spesialis');
    const dokterdpjp_kode = $(identifier).data('dokterdpjp_kode');
    const dokterdpjp_nama = $(identifier).data('dokterdpjp_nama');

    $('#kode_poli').val(kode_poli);
    $('#nama_spesialis').val(nama_spesialis);
    $('#dokterdpjp_kode').val(dokterdpjp_kode);
    $('#dokterdpjp_nama').val(dokterdpjp_nama);

    $('#modal_pencarian_spesialis').modal('toggle');
}
", View::POS_END, 'index');
    $this->registerJs($this->render('partial/js/create.js'));
?>