<?php

/**
 * @author Randy Vianda Putra
 * @todo Konfig Farmasi
 * @copyright 23 April 2018 aweutist
 */


// use yii\web\View;
// use yii\helpers\Html;
// use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use yii\web\View;
use yii\helpers\Url;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Master'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .text-large {
        font-size: 14px;
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
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-body">				
                <?php
                    $form = ActiveForm::begin([
                        'id' => 'konfig-form', 
                        'type' => ActiveForm::TYPE_HORIZONTAL,
                        'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
                    ]); 
                ?>
                    <fieldset title="1">
                        <legend class="text-semibold">Dasar Perhitungan</legend>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6 required">
                                        <div class="form-group">
                                            <div class="col-md-4 text-large"><?= $model->getAttributeLabel('tanggal_berlaku') ?></div>
                                            <div class="col-md-8 text-large">
                                                <?= date('d-m-Y', strtotime($data['tglberlaku']))?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 required">
                                        <div class="form-group">
                                            <div class="col-md-4 text-large"><?= $model->getAttributeLabel('formula_penjualan') ?></div>
                                            <div class="col-md-8 text-large">
                                                <?= $data['formula_penjualan'] ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 required">
                                        <div class="form-group">
                                            <div class="col-md-4 text-large"><?= $model->getAttributeLabel('persen_ppn') ?></div>
                                            <div class="col-md-8 text-large">
                                                <?= $data['persenppn'] ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="col-md-4 text-large"><?= $model->getAttributeLabel('persen_pph') ?></div>
                                            <div class="col-md-8 text-large">
                                                <?= $data['persenpph'] ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 required">
                                        <div class="form-group">
                                            <div class="col-md-4 text-large"><?= $model->getAttributeLabel('persen_margin') ?></div>
                                            <div class="col-md-8 text-large">
                                                <?= $data['persenmargin'] ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="col-md-4 text-large"><?= $model->getAttributeLabel('persen_diskon') ?></div>
                                            <div class="col-md-8 text-large">
                                                <?= $data['persdiskpasien'] ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
							<div class="col-md-4 col-md-offset-4">
								<span id="error-opt-poly" align="center"></span>
							</div>
							<div class="col-md-4">&nbsp;</div>
							&nbsp;
						</div>
                    </fieldset>
                    <fieldset title="2">
                        <legend class="text-semibold">Setting Stok</legend>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="col-md-4 text-large"><?= $model->getAttributeLabel('pembulatan_harga') ?></div>
                                                <div class="col-md-8 text-large">
                                                    <?= $data['pembulatanharga'] ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="col-md-4 text-large"><?= $model->getAttributeLabel('harga_digunakan') ?></div>
                                                <div class="col-md-8 text-large">
                                                    <?= $data['hargaygdigunakan'] ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="col-md-4 text-large"><?= $model->getAttributeLabel('biaya_admin') ?></div>
                                                <div class="col-md-8 text-large">
                                                    <?= $data['administrasi'] ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="col-md-4 text-large"><?= $model->getAttributeLabel('metode_antrian') ?></div>
                                                <div class="col-md-8 text-large">
                                                    <?= $data['metodeantrian'] ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="col-md-4 text-large"><?=  $model->getAttributeLabel('pesan_etiket') ?></div>
                                                <div class="col-md-8 text-large">
                                                    <?= $data['pesan_etiket'] ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="col-md-4 text-large"><?=  $model->getAttributeLabel('pesan_struk') ?></div>
                                                <div class="col-md-8 text-large">
                                                    <?= $data['pesandistruk'] ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
							<div class="col-md-4 col-md-offset-4">
								<span id="error-opt-jaminan" align="center"></span>
							</div>
							<div class="col-md-4">&nbsp;</div>
							&nbsp;
						</div>
                    </fieldset>
                    <button id="btn-detail" disabled type="submit" class="btn bg-success-600 btn-huge-finish stepy-finish">Simpan <i class="icon-check position-right"></i></button>
                    
                    <?php ActiveForm::end(); ?>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
    $this->registerCss($this->render('../assets/css/wizard.css'));
    $this->registerJs($this->render('../assets/js/konfig-farmasi.js'));
    $this->registerJs("
        $('#btn-detail').hide()

	", VIEW::POS_END, 'js-kunings');
?>