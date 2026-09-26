<?php

/**
 * @author Randy Vianda Putra
 * @copyright 15 January 2018 aweutist
 */

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;
use app\components\DocoHelpers;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Apotek'), 'url' => []];
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', $this->title), 'url' => ['bebas']];

?>
<style type="text/css">
    .text-right{
        text-align: right;
    }
    #tabel-obat{
        display: block;
        height: 300px;
        overflow-y: scroll;
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
                        <h3 class="panel-title"><b><?= $title ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'simpan-resep'=>[
                        'title'=>Yii::t('fe', 'Simpan'),
                        'icon'=>'fa fa-floppy-o',
                        'attributes'=>[
                            'id'=>'save-bebas',
                            'data-options'=>'click'
                        ]
                    ],
                    /*'ulang-resep'=>[
                        'title'=>Yii::t('fe', 'Ulang'),
                        'icon'=>'fa fa-refresh',
                        'attributes'=>[
                            'id'=>'ulang',
                            'data-options'=>'click'
                        ]
                    ]*/
                ])
                ?>
            </div>
            <div class="panel-body" style="">
                <!-- Data Pasien / Pembeli -->
                <div class="col-md-4">
                    <div class="panel panel-default" style="">
                        <div class="panel-heading">
                            <h5 class="panel-title"><?= Yii::t('fe', 'Data Pasien / Pembeli') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h5>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="panel-body">
                            <?php
                                $form = ActiveForm::begin([
                                    'id' => 'ajax-form2', 
                                    'options' => ['class' => 'form-horizontal'],
                                    // 'action' =>['obat-alkes-kasus/create']
                                ]); 
                            ?>

                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="col-lg-3 control-label"><?= Yii::t('fe', 'Tanggal penjualan') ?></label>
                                    <div class="col-lg-9">
                                        <div class="input-group">
                                            <?= Html::textInput('tanggal_penjualan',null,[
                                                    'class' => 'form-control is_pasien_rs pickadate tanggal_penjualan',
                                                    'placeholder' => 'Tanggal Penjualan'
                                                ]);
                                            ?>
                                            <span class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="col-lg-3 control-label"><?= Yii::t('fe', 'Nama pembeli') ?></label>
                                    <div class="col-lg-9">
                                        <?= Html::textInput('nama_pembeli', null, [
                                            'class' => 'form-control nama_pembeli',
                                            'placeholder' => 'Nama Pembeli'
                                        ]);
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <br>

                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="col-lg-3 control-label"><?= Yii::t('fe', 'Iter') ?></label>
                                    <div class="col-lg-9">
                                        <?= Html::textInput('iter',null,[
                                                'class' => 'form-control iter doco-number',
                                                'placeholder' => 'Iter'
                                            ]);
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="col-lg-3 control-label"><?= Yii::t('fe', 'Cara bayar') ?> <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <?= Html::activeDropDownList(
                                            $model,
                                            'cara_bayar',
                                            ArrayHelper::map($data_cara_bayar, 'carabayar_id', 'carabayar_nama'),
                                            [
                                                'class' => 'select2 cara_bayar',
                                                'id' => 'cara_bayar',
                                                'prompt' => Yii::t('fe', '— Pilih —')
                                            ]
                                        )
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="col-lg-3 control-label"><?= Yii::t('fe', 'Nama penjamin') ?> <span class="text-danger">*</span></label>
                                    <div class="col-lg-9">
                                        <?=
                                        $form->field($model, 'penjamin')->widget(
                                            DepDrop::classname(),
                                            [
                                                'name' => 'penjamin_id',
                                                'options' => [
                                                    'disabled' => false,
                                                    'class' => 'form-control penjamin select2',
                                                    'id' => 'penjamin',
                                                    'prompt' => Yii::t('fe', '— Pilih —')
                                                ],
                                                'pluginOptions' => [
                                                    'depends' => ['cara_bayar'],
                                                    'placeholder' => Yii::t('fe', '— Pilih —'),
                                                    'url' => '/apotek/transaksi-resep/get-penjamin'
                                                ]
                                            ]
                                        )->label(false);
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <label class="col-lg-3 control-label"><?= Yii::t('fe', 'Catatan') ?></label>
                                    <div class="col-lg-9">
                                       <?=Html::activeTextArea($model, 'catatan', ['class'=> 'form-control', 'row'=>4, 'col'=>7, 'id'=> 'catatan'])?>
                                    </div>
                                </div>
                            </div>
                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>

                <!-- Data Obat Alkes -->
                <div class="col-md-8">
                    <div class="panel panel-default" style="">
                        <div class="panel-heading">
                            <h5 class="panel-title"><?= Yii::t('fe', 'Data Obat Alkes') ?><a class="heading-elements-toggle"><i class="icon-more"></i></a></h5>
                            <div class="heading-elements">
                                <ul class="icons-list">
                                    <li><a data-action="collapse"></a></li>
                                </ul>
                            </div>
                        </div>

                        <div class="panel-body">
                            <?php
                                $form = ActiveForm::begin([
                                    'id' => 'form-obat',
                                    'options' => ['class' => 'form-horizontal'],
                                    // 'action' =>['obat-alkes-kasus/create']
                                ]);
                            ?>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="col-md-4">
                                        <label class="col-lg-3 control-label"><?= Yii::t('fe', 'R ke') ?> <span class="text-danger required_racikan">*</span></label>
                                        <div class="col-lg-9">
                                            <?= Html::textInput('r_ke',null,[
                                                    'class' => 'form-control r_ke',
                                                    'placeholder' => 'R ke'
                                                ]);
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="checkbox" class="styled">
                                            <input type="checkbox" name="racikan_id" class="racikan_id">
                                            <label>
                                                <?= Yii::t('fe', 'Racikan') ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="col-md-4">
                                        <label class=""><?= Yii::t('fe', 'Nama Obat Alkes') ?> <span class="text-danger">*</span></label>
                                        <div class="col-lg-12">
                                            <?= Html::activeDropDownList(
                                                $model,
                                                'obatalkes',
                                                ArrayHelper::map([], 'obatalkes_id', 'obatalkes_namalain'),
                                                [
                                                    'class' => 'select2 autoObat',
                                                    'id' => 'id_auto_obat',
                                                    'prompt' => Yii::t('fe', '— Pilih —')
                                                ]
                                            )
                                            ?>
                                            <?= Html::hiddenInput('obatalkes_id', '', ['class' => 'id_obat']); ?>
                                            <?= Html::hiddenInput('obat_nama', '', ['class' => 'obat_nama']); ?>
                                            <?= Html::hiddenInput('stok', '', ['class' => 'stok']); ?>
                                            <?= Html::hiddenInput('harga', '', ['class' => 'harga']); ?>
                                            <?= Html::hiddenInput('ppn', '', ['class' => 'ppn']); ?>
                                            <?= Html::hiddenInput('satuankecil_id', '', ['class' => 'satuankecil_id']); ?>
                                            <?= Html::hiddenInput('posisiNo', '', ['class' => 'posisi']); ?>
                                            <?= Html::hiddenInput('type', 1, ['class' => 'type']); ?>
                                            <?= Html::hiddenInput('harganetto', '', ['class' => 'harganetto']); ?>
                                            <?= Html::hiddenInput('jmlmargin', '', ['class' => 'hn_margin']); ?>
                                            <?= Html::hiddenInput('jmlppn', '', ['class' => 'hn_ppn']); ?>
                                            <?= Html::hiddenInput('jmldiscount', '', ['class' => 'hn_diskon']); ?>
                                            <?= Html::hiddenInput('persenppn', '', ['class' => 'persenppn']); ?>
                                            <?= Html::hiddenInput('persenmargin', '', ['class' => 'persenmargin']); ?>
                                            <?= Html::hiddenInput('persendiscount', '', ['class' => 'persendiscount']); ?>
                                            <?= Html::hiddenInput('hargajual', '', ['class' => 'hargajual']); ?>
                                            <?= Html::hiddenInput('signa_nama', '', ['class' => 'signanama']); ?>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label class=""><?= Yii::t('fe', 'Signa') ?></label>
                                        <div class="col-lg-12">
                                            <?= Html::dropDownList(
                                                'signa', 
                                                null, 
                                                ArrayHelper::map($data['data_signa'], 'signa_id', 'signa_nama'), 
                                                ['class'=>'form-control select2 signaid', 'prompt'=>'— Pilih —']
                                            ); ?>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <label class=""><?= Yii::t('fe', 'Qty') ?> <span class="text-danger">*</span></label>
                                        <div class="col-lg-12">
                                            <?= Html::textInput('qty',null,[
                                                    'class' => 'form-control qty-obat',
                                                    'type' => 'number',
                                                    'min' => 1,
                                                    'placeholder' => 'Qty'
                                                ]);
                                            ?>
                                        </div>
                                    </div>

                                    <div class="col-md-1 button-list">
                                        <label for=""></label>
                                        <?= Html::submitButton('<i class="fa fa-plus"></i> ' . Yii::t('fe', "Tambah"), ['class' => 'btn btn-success add',
                                            'id'=> 'tambah-obat'
                                            ]); ?>
                                        <input type="hidden" name="form" value="true">
                                    </div>
                                </div>
                            </div>
                            <?php ActiveForm::end(); ?>

                            <!-- Info Penjualan Resep Bebas -->
                            <div class="col-md-12">
                                <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="tabel-obat">
                                    <thead>
                                        <tr class="bg-inverse">
                                            <th width="1">No</th>
                                            <th width="10%"><?= Yii::t('fe', 'Racikan').' / '.Yii::t('fe', 'Non Racikan') ?></th>
                                            <th width="10%"><?= Yii::t('fe', 'R ke') ?></th>
                                            <th width="25%"><?= Yii::t('fe', 'Nama Obat Alkes') ?></th>
                                            <th align="right" width="30%"><?= Yii::t('fe', 'Harga Satuan (Rp.)') ?></th>
                                            <th><?= Yii::t('fe', 'Signa') ?></th>
                                            <th class="text-right"><?= Yii::t('fe', 'Qty') ?></th>
                                            <th align="right" width="20%"><?= Yii::t('fe', 'Sub Total (Rp.)') ?></th>
                                            <th><?= Yii::t('fe', 'Aksi') ?></th>
                                        </tr>
                                        </tr>
                                    </thead>
                                    <tbody id="list-obat">
                                    
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-12">
                        <?php
                            $form = ActiveForm::begin([
                                'id' => 'form-obat2',
                                'options' => ['class' => 'form-horizontal'],
                                            // 'action' =>['obat-alkes-kasus/create']
                            ]);
                            ?>
                        <div class="col-md-4">
                            <label class="col-lg-3 control-label"><?= Yii::t('fe', 'Total harga obat alkes') ?></label>
                            <div class="col-lg-9">
                                <div class="input-group">
                                <span class="input-group-addon">Rp. </span>
                                    <?= Html::textInput('total_harga',null,[
                                            'class' => 'form-control doco-number',
                                            'id' => 'subtotalItem',
                                            'readonly' => true,
                                            'placeholder' => Yii::t('fe', 'Total harga obat alkes')
                                        ]);
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="col-lg-3 control-label"><?= Yii::t('fe', 'Jasa racik') ?></label>
                            <div class="col-lg-9">
                                <div class="input-group">
                                    <span class="input-group-addon">Rp. </span>
                                    <?= Html::textInput('jasa_racik',null,[
                                            'class' => 'form-control doco-number',
                                            'id' => 'jasa_racik',
                                            'placeholder' => Yii::t('fe', 'Jasa racik')
                                        ]);
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="col-lg-3 control-label"><?= Yii::t('fe', 'Total tagihan pasien') ?></label>
                            <div class="col-lg-9">
                                <div class="input-group">
                                    <span class="input-group-addon">Rp. </span>
                                    <?= Html::textInput('total_tagihan',null,[
                                            'class' => 'form-control doco-number',
                                            'id' => 'total_tagihan',
                                            'readonly' => true,
                                            'placeholder' => Yii::t('fe', 'Total tagihan pasien')
                                        ]);
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="col-md-4">
                            <label class="col-lg-3 control-label"><?= Yii::t('fe', 'Biaya administrasi') ?></label>
                            <div class="col-lg-9">
                                <div class="input-group">
                                    <span class="input-group-addon">Rp. </span>
                                    <?= Html::textInput('biaya_admin',null,[
                                            'class' => 'form-control doco-number',
                                            'id' => 'biaya_admin',
                                            'placeholder' => 'Biaya Administrasi'
                                        ]);
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="col-lg-3 control-label"><?= Yii::t('fe', 'Pembulatan') ?></label>
                            <div class="col-lg-9">
                                <div class="input-group">
                                    <span class="input-group-addon">Rp. </span>
                                    <?= Html::textInput('pembulatan', null,[
                                            'readonly' => true,
                                            'id' => 'pembulatan',
                                            'class' => 'form-control doco-number',
                                            'placeholder' => 'Pembulatan'
                                        ]);
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="clear"></div>
                <br>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<!-- Untuk Kebutuhan Modal Global -->
<div id="modal_backdrop-lg" class="modal fade" style="z-index:1065;" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
<!-- End -->
<?php 
    $dateNow = date("d M Y");
    $this->registerCss($this->render('../assets/css/apotek.css'));
    $this->registerJs('
        var transObat = '. $transApotek . ';
        var isBackdate = '. $checkBackdate . ';
        var ispembulatan = "'.$isPembulatan.'";
        var satuanpembulatan = "'.$satuanPembulatan.'";
        var dateNow = "'.$dateNow.'";
    ');
    $this->registerJs($this->render('../assets/js/transaksi-bebas.js'));
?>