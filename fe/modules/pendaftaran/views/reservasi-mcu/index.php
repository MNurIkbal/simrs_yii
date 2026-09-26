<?php

/**
 * @Author: afil
 * @Date:   2018-02-26 07:58:44
 * @Last Modified by:   afil
 * @Last Modified time: 2018-02-26 07:58:44
 *
 * @Refactored by : Anggoro
 * @Refactored time : 2019-07-01
 *
 * @Description: Pendaftaran IGD / Rawat Darurat
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\select2\Select2;
use app\components\DocoHelpers;
use app\components\DocoConstants;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Pendaftaran'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$typeRegis = !empty(DocoConstants::PARAM_DFTR[$ruanganId]) ? DocoConstants::PARAM_DFTR[$ruanganId] : null;
$rajal = !empty(DocoConstants::PARAM_DFTR[DocoConstants::WS_RAJAL]) ? DocoConstants::PARAM_DFTR[DocoConstants::WS_RAJAL] : null;
?>
<style type="text/css">
    .sweet-alert {
        z-index: 1065 !important;
    }
    .btn-icon {
        padding-left: 8px !important;
        padding-top: 2px !important;
    }
    [class^="icon-"], [class*=" icon-"] {
        top: -2px!important;
    }
    .popover-content {
        width: 400px!important;
    }
    .border-radius-top {
        border-top-right-radius: 26px !important;
        border-top-left-radius: 26px  !important;
    }
    .media {
        border: 1px solid #dddddd !important;
    }
    .bg-indigo-300 {
        background-color: #49ce8e6b !important;
        border-color: #7986CB !important;
    }
</style>
<input type="hidden" name="" class="params-header" value="<?=isset($params) ? $params : '' ?>">
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <!-- breadcrumb -->
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title">
                            <b>
                                <?= Yii::$app->docoVars->workspace("modul_alias") . ($loket_nama 
                                        ? ' - ' . Yii::t('fe', 'Loket') . ' ' . $loket_nama : '') ?>
                            </b>
                        </h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>

                <div class="heading-elements">
                    <?php
                        // if($typeRegis !== $params) :
                        if ($typeRegis == $rajal) :
                    ?>
                    <ul class="icons-list">
                            <li><?= Yii::t('fe', 'Tekan enter untuk mencari') ?></li>
                            <li>
                                <div class='form-group'>
                                    <input type="text" class="form-control no-antrian" name="no_antrian" autocomplete="off" placeholder="Ketik antrian manual" readonly onfocus="this.removeAttribute('readonly');" >
                                </div>
                            </li>
                            <li>
                                <button
                                    type="button" class="btn btn-primary"
                                    id="btn-pilih-antrian"
                                    action="/pendaftaran/daftar/pilih-antrian"
                                    data-width='900px'
                                    data-toggle="modal" data-target="#modal_backdrop"
                                >
                                    Panggil Antrian
                                </button>
                            </li>
                            <li>
                                <?php
                                    echo Html::button(Yii::t('fe', 'Ubah Jenis Antrian'),[
                                        'class' => 'btn btn-info btn-md',
                                        'id'=>'btn-ubah-jenis-antrian',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal_backdrop',
                                        'action' => '/pendaftaran/daftar/ubah-jenis-antrian?loket_id='.$loket_id.'&jenisantrian_id='.$jenisantrian_id,
                                    ]);
                                ?>
                            </li>
                            <li>
                                <?= Html::hiddenInput('loket', $loket_nama, ['id'=>'loket']); ?>
                            </li>
                        <li class="hidden"><a data-action="collapse"></a></li>
                    </ul>
                    <?php
                        endif;
                    ?>
                    <?= Yii::$app->controller->renderPartial('/daftar/partial/component/info-shortcut'); ?>
                </div>
                <!-- end of breadcrumb -->
            </div>

            <div class="panel-body no-border">
            <!-- Data Pasien -->
            <?php echo Yii::$app->controller->renderPartial('partial/_pasien', $render_pasien_data);?>
            <!-- End of Data Pasien -->

            <div class="row">
                <?php
                    echo Yii::$app->controller->renderPartial(
                        'partial/_formkunjungan', $render_kunjungan_data
                    );
                 ?>
            </div>
            <!-- End of Data Kunjungan -->
        </div>
        </div>
    </div>
</div>

&nbsp;
<div class="clearfix"></div>

<div id="modal_pencarian_lanjutan" class="modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div>
    </div>
</div>

<button 
    class="btn btn-primary grid-button btn-sm btn-form-bpjs" 
    data-toggle="modal" 
    data-target="#modal_backdrop" 
    data-width="90%" 
    action="/pendaftaran/daftar-igd/get-form-bpjs?pendaftaran_id=364&no_rekam_medik=00005"
    style="display: none">Tampilkan</button>

<button class="btn btn-primary grid-button btn-sm btn-form-asuransi hidden" 
        data-toggle="modal" 
        data-target="#modal_backdrop" data-width="75%">Tampilkan</button>

<?php
    $_rujukanDari = json_encode($rujukan_dari);
    $_penOl = json_encode($penOl);
    $this->registerJs("
        var _rujukanDari = $_rujukanDari;
        var _penOl = $_penOl;
        var default_jenis_penyakit = '".$render_kunjungan_data['default_jenis_penyakit']."'
    "
        .$this->render('js/index.js')
        .$this->render('js/pasien.js')
    , View::POS_END, "js-index");

 ?>