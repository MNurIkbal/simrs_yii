<?php

/**
 * @author : Sulthan Zaidan Fauzi (sulthanzaidan1026@gmail.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;
use kartik\widgets\ActiveForm;
use app\components\DocoHelpers;

$this->title = $title;
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
                        <img alt="modul-icon" src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= $this->title ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?php
                    $buttons = [
                        'simpan'=>[
                            'title'=>Yii::t('fe', 'Simpan'),
                            'icon'=>'fa fa-floppy-o',
                            'attributes'=>[
                                'data-options'=>'click',
                                'id'=>'btn-simpan'
                            ],
                        ],
                        'muat-ulang'=>[
                            'title'=>Yii::t('fe', 'Muat Ulang'),
                            'icon'=>'fa fa-repeat',
                            'attributes'=>[
                                'data-options'=>'click',
                                'id'=>'btn-ulang'
                            ],
                        ],
                    ];

                    if (!is_null($model->pemesananproduksiobat_id)) {
                        $buttons['back'] = [
                            'title'=>Yii::t('fe', 'Batal'),
                            'icon'=>'fa fa-close',
                            'attributes' => [
                                'href' => '/apotek/inf-produksi-obat'
                            ],
                        ];
                    }
                ?>
                <?=DocoHelpers::generateToolbar($buttons)?>
            </div>
            <div class="panel-body">
                <div class="col-md-5">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h5 class="panel-title"><?= Yii::t('fe', 'Data pemesanan produksi') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h5>
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
                                'action' => '/apotek/transaksi-pemesanan-produksi/save-cache',
                                'enableAjaxValidation' => false,
                                'enableClientValidation' => false,
                                'type' => ActiveForm::TYPE_HORIZONTAL,
                                'formConfig' => [
                                    'labelSpan' => 3,
                                    'deviceSize' => ActiveForm::SIZE_SMALL
                                ],
                            ]);
                            ?>

                            <?php
                            if (!is_null($model->pemesananproduksiobat_id)) {
                                echo  $form->field($model, 'nopemesanan', [
                                    'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-4'
                                    ]
                                    ])->textInput([
                                        'placeholder' => $model->getAttributeLabel('nopemesanan'),
                                        'class' => 'form-control input-sm',
                                        'autocomplete' => "off",
                                        'id' => 'nopemesanan',
                                        'readonly' => true,
                                    ]);
                            }
                            ?>

                            <?= $form->field($model, 'tglpemesanan', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-8'
                                ],
                                ])->textInput([
                                    'placeholder' => $model->getAttributeLabel('tanggal_pesan'),
                                    'class' => 'form-control input-sm',
                                    'id' => 'tanggal_pesan',
                                    'autocomplete' => "off",
                                    'readonly' => true,
                                    'value' => $model->pemesananproduksiobat_id ? date("j M Y", strtotime($model->tglpemesanan)) : date("j M Y")
                                ]);
                            ?>

                            <?=
                                $form->field($model, 'instalasi_id', [
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

                            <?= Html::hiddenInput('init_ruangan_id', $model->ruangan_id, ['id'=>'ruangan_id']); ?>
                            <?= $form->field($model, 'ruangan_id', [
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
                                        'url' => '/apotek/transaksi-pemesanan-produksi/get-ruangan',
                                        'initialize' => true,
                                        'params'=> ['ruangan_id'],
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

                            <?= $form->field($model, 'qty', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-4'
                                ]
                                ])->textInput([
                                    'placeholder' => $model->getAttributeLabel('qty'),
                                    'class' => 'form-control input-sm ',
                                    'autocomplete' => "off",
                                    'id' => 'qty-pemesanan',
                                    'type' => 'number',
                                    'tab-index' => 4
                                ]); ?>

                            <?php
                            if (!is_null($model->pemesananproduksiobat_id)) {
                                echo  $form->field($model, 'pegawai_pemesanan', [
                                    'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-4',
                                        'wrapper' => 'col-md-4'
                                    ]
                                    ])->textInput([
                                        'placeholder' => $model->getAttributeLabel('pegawai_pemesanan'),
                                        'class' => 'form-control input-sm',
                                        'autocomplete' => "off",
                                        'id' => 'pegawai_pemesanan',
                                        'readonly' => true,
                                    ]);
                            }
                            ?>

                            <?= Html::hiddenInput('pemesanan_id', $model->pemesananproduksiobat_id, ['id'=>'pemesanan_id']); ?>

                            <hr>
                            <div class="btn-group pull-right">
                                <?= Html::submitButton(
                                    '<b><i class="fa fa-plus"></i></b>' . Yii::t('fe', 'Tambah'),
                                    [
                                        'class' => 'btn btn-success btn-labeled btn-xs',
                                        'id' => 'simpan-pemakaian-obat',
                                        'tab-index' => 5
                                    ]
                                ) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h5 class="panel-title"><?= Yii::t('fe', 'Tabel pemesanan produksi') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h5>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="panel-body" style="max-height: 392px; overflow-y: scroll;">
                            <div class="row">
                                <table id="pemesanan-produksi-obat-alkes" class="table table-striped table-condensed table-hover" style="width:100%">
                                    <thead>
                                        <tr class="bg-inverse">
                                            <th width="1">No</th>
                                            <th><?= \Yii::t("fe", "Nama obat alkes"); ?></th>
                                            <th><?= \Yii::t("fe", "Qty Pemesanan"); ?></th>
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
                        <?= Html::label('Catatan', 'catatan', ['class' => 'control-label']) ?>
                        <?= Html::textarea('TransaksiPemesananProduksiForm[catatan_bahanbaku]', $model->catatan_bahanbaku, [
                            'cols' => 30,
                            'rows' => 10,
                            'maxlength' => 1000,
                            'placeholder' => $model->getAttributeLabel('catatan_bahanbaku'),
                            'class' => 'form-control',
                            'id' => 'catatan',
                            'tab-index' => 6
                        ]) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
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
        satuankecil : {},
        satuan : {},
        item : {},
    };
    var attributes = {};

    var ruangan_id = '.$ruangan_gdf.';
', View::POS_END, 'js');
$this->registerJs($this->render('../assets/js/transaksi-pemesanan-produksi-obat-alkes.js'), View::POS_END, 'js2');
?>
