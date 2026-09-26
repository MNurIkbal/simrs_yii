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
                                        <?= $form->field($model, 'tanggal_berlaku', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-5'
                                            ],
                                            'addon' => ['append' => [
                                                    'content' => '<i class="fa fa-calendar"></i>'
                                                ]
                                            ]
                                            ])->textInput([
                                                'placeholder' => $model->getAttributeLabel('tanggal_berlaku'),
                                                'class' => 'form-control input-sm pickadate opt-tgl',
                                                'id' => 'tanggal-kirim',
                                                'autocomplete' => "off",
                                                'readonly' => true
                                            ]); 
                                        ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-opt-tgl" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 required">
                                        <?= $form->field($model, 'formula_penjualan', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                            ])->textInput([
                                                'placeholder' => $model->getAttributeLabel('formula_penjualan'),
                                                'class' => 'form-control input-sm typeahead formula',
                                                'autocomplete' => "off",
                                                // 'id' => 'pemesanan-obat-qty',
                                            ]); 
                                        ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-formula" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 required">
                                        <?= $form->field($model, 'persen_ppn', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                            ])->textInput([
                                                'placeholder' => $model->getAttributeLabel('persen_ppn'),
                                                'class' => 'form-control input-sm typeahead ppn docoNumberOnly',
                                                'autocomplete' => "off",
                                                // 'id' => 'pemesanan-obat-qty',
                                            ]); 
                                        ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-ppn" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'persen_pph', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                            ])->textInput([
                                                'placeholder' => $model->getAttributeLabel('persen_pph'),
                                                'class' => 'form-control input-sm typeahead docoNumberOnly',
                                                'autocomplete' => "off",
                                                // 'id' => 'pemesanan-obat-qty',
                                            ]); 
                                        ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 required">
                                        <?= $form->field($model, 'persen_margin', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                            ])->textInput([
                                                'placeholder' => $model->getAttributeLabel('persen_margin'),
                                                'class' => 'form-control input-sm typeahead margin docoNumberOnly',
                                                'autocomplete' => "off",
                                                // 'id' => 'pemesanan-obat-qty',
                                            ]); 
                                        ?>
                                        <div class="form-group">
                                            <div class="col-md-4"></div>
                                            <div class="col-md-8">
                                                <span id="error-margin" align="center"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <?= $form->field($model, 'persen_diskon', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8'
                                            ]
                                            ])->textInput([
                                                'placeholder' => $model->getAttributeLabel('persen_diskon'),
                                                'class' => 'form-control input-sm typeahead docoNumberOnly',
                                                'autocomplete' => "off",
                                                // 'id' => 'pemesanan-obat-qty',
                                            ]); 
                                        ?>
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
                                        <div class="col-md-6 required">
                                            <?= $form->field($model, 'pembulatan_harga', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-8'
                                                ]
                                                ])->textInput([
                                                    'placeholder' => $model->getAttributeLabel('pembulatan_harga'),
                                                    'class' => 'form-control input-sm typeahead pembulatan docoNumberOnly',
                                                    'autocomplete' => "off",
                                                    // 'id' => 'pemesanan-obat-qty',
                                                ]); 
                                            ?>
                                            <div class="form-group">
                                                <div class="col-md-4"></div>
                                                <div class="col-md-8">
                                                    <span id="error-pembulatan" align="center"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 required">
                                            <?= $form->field($model, 'harga_digunakan', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-8'
                                                ]
                                                ])->dropDownList($harga, [
                                                    'class' => 'select2 harga',
                                                    'id' => 'harga',
                                                    'prompt' => Yii::t('fe', '-- Pilih --')
                                                ]);
                                            ?>
                                            <div class="form-group">
                                                <div class="col-md-4"></div>
                                                <div class="col-md-8">
                                                    <span id="error-harga" align="center"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-6">
                                            <?= $form->field($model, 'biaya_admin', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-8'
                                                ]
                                                ])->textInput([
                                                    'placeholder' => $model->getAttributeLabel('biaya_admin'),
                                                    'class' => 'form-control input-sm typeahead docoNumberOnly',
                                                    'autocomplete' => "off",
                                                    // 'id' => 'pemesanan-obat-qty',
                                                ]); 
                                            ?>
                                        </div>
                                        <div class="col-md-6 required">
                                            <?= $form->field($model, 'metode_antrian', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-8'
                                                ]
                                                ])->dropDownList($antrian_obat, [
                                                    'class' => 'select2 metode',
                                                    'id' => 'antrian_obat',
                                                    'prompt' => Yii::t('fe', '-- Pilih --')
                                                ]);
                                            ?>
                                            <div class="form-group">
                                                <div class="col-md-4"></div>
                                                <div class="col-md-8">
                                                    <span id="error-metode" align="center"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-6">
                                            <?= $form->field($model, 'pesan_etiket', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-8'
                                                ]
                                                ])->textArea([
                                                    'placeholder' => $model->getAttributeLabel('pesan_etiket'),
                                                    'class' => 'form-control input-sm typeahead',
                                                    'autocomplete' => "off",
                                                    // 'id' => 'pemesanan-obat-qty',
                                                ]); 
                                            ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?= $form->field($model, 'pesan_struk', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-4',
                                                    'wrapper' => 'col-md-8'
                                                ]
                                                ])->textArea([
                                                    'placeholder' => $model->getAttributeLabel('pesan_struk'),
                                                    'class' => 'form-control input-sm typeahead',
                                                    'autocomplete' => "off",
                                                    // 'id' => 'pemesanan-obat-qty',
                                                ]); 
                                            ?>
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
                    <button id="btn-save" type="submit" class="btn bg-success-600 btn-huge-finish stepy-finish">Simpan <i class="icon-check position-right"></i></button>
                    
                    <?php ActiveForm::end(); ?>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
    $this->registerCss($this->render('../assets/css/wizard.css'));
    $this->registerJs($this->render('../assets/js/konfig-farmasi.js'));
?>