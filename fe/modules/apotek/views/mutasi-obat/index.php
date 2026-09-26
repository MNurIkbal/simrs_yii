<?php

/**
 * @author Randy Vianda Putra
 * @copyright 15 January 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
// use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\widgets\DepDrop;
use kartik\widgets\ActiveForm;
use kartik\typeahead\Typeahead;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', 'Mutasi obat alkes');
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("instalasi_name"), 'url' => ['']];
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
                        <h3 class="panel-title"><b><?= $this->title ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'simpan'=>[
                        'title'=>Yii::t('fe', 'Simpan'),
                        'icon'=>'fa fa-floppy-o',
                        'attributes'=>[
                            'data-options'=>'click',
                            'id'=>'btn-simpan'
                        ]
                    ],
                    'muat-ulang'=>[
                        'title'=>Yii::t('fe', 'Muat Ulang'),
                        'icon'=>'fa fa-repeat',
                        'attributes'=>[
                            'data-options'=>'click',
                            'id'=>'btn-ulang'
                        ]
                    ],
                ])
                ?>
            </div>
            <div class="panel-body">
                <div class="col-md-5">
                    <div class="panel panel-default" style="">
                        <div class="panel-heading">
                            <h5 class="panel-title"><?= Yii::t('fe', 'Data Mutasi') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h5>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="panel-body">
                            <?php
                            $form = ActiveForm::begin([
                                'id' => 'ajax-form',
                                'action' => '/apotek/transaksi-pemesanan/save-cache',
                                'enableAjaxValidation' => false,
                                'enableClientValidation' => false,
                                'type' => ActiveForm::TYPE_HORIZONTAL,
                                'formConfig' => [
                                    'labelSpan' => 3,
                                    'deviceSize' => ActiveForm::SIZE_SMALL
                                ],
                            ]);
                            ?>
                            <?= $form->field($model, 'tanggal_kirim', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-8'
                                ],
                                ])->textInput([
                                    'placeholder' => $model->getAttributeLabel('tanggal_kirim'),
                                    'class' => 'form-control input-sm',
                                    'id' => 'tanggal-kirim',
                                    'autocomplete' => "off",
                                    'readonly' => true,
                                    'value' => date("d F, Y")
                                ]);
                            ?>

                            <?=
                                $form->field($model, 'instalasi_tujuan', [
                                    'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-8'
                                    ]
                                ])->dropDownList($instalasi, [
                                    'class' => 'select2 selectInstalasi',
                                    'id' => 'instalasi_select',
                                    'prompt' => Yii::t('fe', '-- Pilih --'),
                                    'tab-index' => 0
                                ]);
                            ?>


                            <?=
                            $form->field($model, 'ruangan_tujuan', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-8'
                                ]
                                ])->widget(DepDrop::classname(), [
                                    'name' => 'ruangan_nama',
                                    'options' => [
                                        'disabled' => false,
                                        'class' => 'form-control ruangan select2',
                                        'id' => 'ruangan_select',
                                        'tab-index' => 1
                                    ],
                                    'pluginOptions' => [
                                        'depends' => ['instalasi_select'],
                                        'placeholder' => '',
                                        'url' => '/apotek/transaksi-pemesanan/get-ruangan'
                                    ]
                                ]);
                            ?>

                            <?= $form->field($model, 'obat_alkes', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-8'
                                ]
                                ])->dropDownList([], [
                                    'class' => 'select2',
                                    'id' => 'obatalkes_id',
                                    'tab-index' => 2
                                ]);
                            ?>
                            <?php
                                echo Html::hiddenInput('TransaksiPemesananForm[kode_obat]',
                                    $model->kode_obat, [
                                    'class' => 'kode_obat'
                                ]);
                            ?>
                            <?= $form->field($model, 'satuan', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-8'
                                ],
                                ])->dropDownList([], [
                                    'class' => 'select2',
                                    'id' => 'list-satuan',
                                    'tab-index' => 3
                                ]);
                            ?>

                            <?= $form->field($model, 'stok', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-4'
                                ]
                                ])->textInput([
                                    'placeholder' => $model->getAttributeLabel('stok'),
                                    'class' => 'form-control input-sm',
                                    'autocomplete' => "off",
                                    'id' => 'pemesanan-obat-stok',
                                    'type' => 'number',
                                    'readonly' => true,
                                ]); ?>
                            <?= $form->field($model, 'qty', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-4'
                                ]
                                ])->textInput([
                                    'placeholder' => $model->getAttributeLabel('qty'),
                                    'class' => 'form-control input-sm ',
                                    'autocomplete' => "off",
                                    'id' => 'pemesanan-obat-qty',
                                    'type' => 'number',
                                    'tab-index' => 4
                                ]); ?>
                            <hr>
                            <div class="btn-group pull-right">
                                <?= Html::submitButton(
                                    '<b><i class="fa fa-plus"></i></b>' . Yii::t('fe', 'Tambah'),
                                    [
                                        'class' => 'btn btn-success btn-labeled btn-xs',
                                        'id' => 'simpan-pemakain-obat',
                                        'tab-index' => 5
                                    ]
                                ) ?>
                            </div>
                                <?php ActiveForm::end(); ?>
                            </div>
                        </div>
                </div>

                <div class="col-md-7">
                    <div class="panel panel-default" style="">
                        <div class="panel-heading">
                            <h5 class="panel-title"><?= Yii::t('fe', 'Tabel Mutasi') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h5>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="panel-body" style="max-height: 392px; overflow-y: scroll;">
                            <div class="row">
                                <table id="pemesanan-obat-alkes" class="table table-striped table-condensed table-hover" style="width:100%">
                                    <thead>
                                        <tr class="bg-inverse">
                                            <th width="1">No</th>
                                            <th><?= \Yii::t("fe", "Kode obat alkes"); ?></th>
                                            <th><?= \Yii::t("fe", "Nama obat alkes"); ?></th>
                                            <th><?= \Yii::t("fe", "Qty Mutasi"); ?></th>
                                            <th><?= \Yii::t("fe", "Qty Konversi"); ?></th>
                                            <th><?= \Yii::t("fe", "Aksi"); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center" colspan="9">
                                                <?= \Yii::t("fe", "Data tidak ditemukan."); ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label for=""><?= Yii::t('fe', 'Catatan') ?></label>
                        <textarea name="catatan" id="catatan" cols="30" rows="10" class="form-control"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerCss($this->render('../assets/css/apotek.css'));
$this->registerJs('
    var table;
    var _detailObat = {
        stok : {},
        satuankecil : {},
        satuan : {},
        currentStok : 0,
        currentSatuan : 0,
        item : {},
        cacheSatuan : ' . $cache . '
    };
    var attributes = {};

    var ruangan_id = '.$ruangan_gdf.';
    var current_ruangan_id = '.$ruangan_id.';
', View::POS_END, 'js');
$this->registerJs($this->render('mutasi-obat.js'), View::POS_END, 'js2');
?>



