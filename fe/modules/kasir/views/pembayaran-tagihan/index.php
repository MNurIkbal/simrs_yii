<?php

/**
 * @author Yaya
 * @copyright 26 April 2018 
 */

use yii\web\View;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\typeahead\Typeahead;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', $title), 'url' => ['index']];

?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
                <?php echo Breadcrumbs::widget([
                      'homeLink' => [ 
                                      'label' => Yii::t('fe', 'Home'),
                                      'url' => Yii::$app->homeUrl,
                                 ],
                      'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                   ]); 
                ?>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= Html::button('<b><i class="fa fa-floppy-o"></i></b>'.Yii::t('fe', ' Simpan'), 
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'id' => 'simpan-pemakaian-alkes'
                    ]);
                ?>
                <?= Html::button('<b><i class="fa fa fa-refresh"></i></b>'.Yii::t('fe', ' Ulang'), 
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                    ]);
                ?>

            </div>
            <div class="panel-body">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b><?= Yii::t('fe','Informasi Pasien') ?></b></h6>
                        </div>

                        <div class="panel-body">
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>Instalasi Akhir</b></label>
                                    <div class="col-sm-7">
                                        <p><?= $model->instalasi_nama ?></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>No pendaftaran</b></label>
                                    <div class="col-sm-7">
                                        <p><?= $model->no_pendaftaran ?></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>Cara bayar</b></label>
                                    <div class="col-sm-7">
                                        <p><?= $model->carabayar_nama ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>Ruangan akhir</b></label>
                                    <div class="col-sm-7">
                                        <p><?= $model->ruangan_nama ?></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>No rekam medis</b></label>
                                    <div class="col-sm-7">
                                        <p><?= $model->no_rekam_medik ?></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>Penjamin</b></label>
                                    <div class="col-sm-7">
                                        <p><?= $model->penjamin_nama ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>Tanggal pendaftaran</b></label>
                                    <div class="col-sm-7">
                                        <p><?= date('d-M-Y',strtotime($model->tgl_pendaftaran)) ?></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>Nama pasien</b></label>
                                    <div class="col-sm-7">
                                        <p><?= $model->nama_pasien ?></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>Kelas pelayanan</b></label>
                                    <div class="col-sm-7">
                                        <p><?= $model->kelaspelayanan_nama ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>No telephon</b></label>
                                    <div class="col-sm-7">
                                        <p><?= $model->no_mobile_pasien ?></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5"><b>Tanggal keluar</b></label>
                                    <div class="col-sm-7">
                                        <p><?= $model->tglpasienpulang 
                                                ? date('d-M-Y',strtotime($model->tglpasienpulang))
                                                : '-' ?></p>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b><?= Yii::t('fe','Detail Transaksi') ?></b></h6>
                        </div>

                        <div class="panel-body">
                            <table id="pemakaian-obat-alkes" 
                            class="table table-striped table-condensed table-hover" style="width:100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th><?=\Yii::t("fe", "No");?></th>
                                        <th><?=\Yii::t("fe", "Tanggal Tindakan");?></th>
                                        <th><?=\Yii::t("fe", "Instalasi");?></th>
                                        <th><?=\Yii::t("fe", "Ruangan");?></th>
                                        <th><?=\Yii::t("fe", "Nama Tindakan");?></th>
                                        <th><?=\Yii::t("fe", "Harga Satuan");?></th>
                                        <th><?=\Yii::t("fe", "Qty");?></th>
                                        <th><?=\Yii::t("fe", "Sub Total");?></th>
                                        <th><?=\Yii::t("fe", "Cara Bayar");?></th>
                                        <th><?=\Yii::t("fe", "Penjamin");?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $_cache = '';
                                        if ($model->detail_tagihan) :
                                            $no = 1;
                                            foreach ($model->detail_tagihan as $value) :
                                                $_cache = $value;
                                    ?>
                                                <tr>
                                                    <td class="text-center">
                                                        <?= $no ?>
                                                    </td>
                                                    <td>
                                                        <?= isset($value['tgl_pelayanan']) 
                                                                ? date('d-M-Y',strtotime($value['tgl_pelayanan'])) 
                                                                : '' ?>
                                                    </td>
                                                    <td>
                                                        <?= isset($value['instalasi_pelayanan']) 
                                                                ? $value['instalasi_pelayanan'] 
                                                                : '' ?>
                                                    </td>
                                                    <td>
                                                        <?= isset($value['ruangan_pelayanan']) 
                                                                ? $value['ruangan_pelayanan'] 
                                                                : '' ?>
                                                    </td>
                                                    <td>
                                                        <?= isset($value['tindakan_obat_nama']) 
                                                                ? $value['tindakan_obat_nama'] 
                                                                : '' ?>
                                                    </td>
                                                    <td class="text-right">
                                                        <?= isset($value['tarif_satuan']) 
                                                                ? $value['tarif_satuan'] 
                                                                : '' ?>
                                                    </td>
                                                    <td class="text-right">
                                                        <?= isset($value['qty']) 
                                                                ? $value['qty'] 
                                                                : '' ?>
                                                    </td>
                                                    <td class="text-right">
                                                        <?= isset($value['sub_total']) 
                                                                ? $value['sub_total'] 
                                                                : '' ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?= Html::dropDownList('kondisibarang',
                                                                null,
                                                                [],
                                                                [
                                                                    'class' => 'select2 event-so',
                                                                    'prompt' => Yii::t('fe','--Pilih--')
                                                                ])
                                                        ?>
                                                    </td>
                                                    <td class="text-center">
                                                    <?= Html::dropDownList('kondisibarang',
                                                            null,
                                                            [],
                                                            [
                                                                'class' => 'select2 event-so',
                                                                'prompt' => Yii::t('fe','--Pilih--')
                                                            ])
                                                    ?>
                                                    </td>
                                                </tr>
                                    <?php
                                                $no++;
                                            endforeach;
                                        else :
                                    ?>
                                        <tr>
                                            <td class="text-center" colspan="10">
                                                <?=\Yii::t("fe", "Data tidak ditemukan.");?>
                                            </td>
                                        </tr>
                                    <?php
                                        endif;
                                    ?>
                                </tbody>
                            </table>
                            <hr>
                            <?php 
                                $form = ActiveForm::begin([
                                    'id' => 'ajax-form', 
                                    'action' => '/gudang/pemakaian-barang/set-list-item',
                                    'enableAjaxValidation'=>false, 
                                    'enableClientValidation'=>false,
                                    'type' => ActiveForm::TYPE_HORIZONTAL,
                                    'formConfig' => [
                                        'labelSpan' => 3, 
                                        'deviceSize' => ActiveForm::SIZE_SMALL
                                    ],
                                    'options' => [
                                        'skip-confirm' => "true"
                                    ]
                                ]); 
                            ?>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5">
                                        <b><?= $model->getAttributeLabel('tanggal_pembayaran') ?></b>
                                    </label>
                                    <div class="col-sm-7">
                                    <?= $form->field($model, 'tanggal_pembayaran',[
                                                    'template' => '{input}',
                                                    'options' => [
                                                        'tag' => false
                                                    ]
                                                ])->textInput([
                                                    'placeholder' => $model->getAttributeLabel('tanggal_pembayaran'),
                                                    'class' => 'form-control input-sm typeahead',
                                                    'autocomplete' => "off",
                                                    'type' => 'number'
                                                ])->label(false); ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5">
                                        <b><?= $model->getAttributeLabel('pengguna_uang_muka') ?></b>
                                    </label>
                                    <div class="col-sm-7">
                                    <?= $form->field($model, 'pengguna_uang_muka',[
                                                    'template' => '{input}',
                                                    'options' => [
                                                        'tag' => false
                                                    ]
                                                ])->textInput([
                                                    'placeholder' => $model->getAttributeLabel('pengguna_uang_muka'),
                                                    'class' => 'form-control input-sm typeahead',
                                                    'autocomplete' => "off",
                                                    'type' => 'number'
                                                ])->label(false); ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5">
                                        <b><?= $model->getAttributeLabel('subsidi_asuransi') ?></b>
                                    </label>
                                    <div class="col-sm-7">
                                    <?= $form->field($model, 'subsidi_asuransi',[
                                                    'template' => '{input}',
                                                    'options' => [
                                                        'tag' => false
                                                    ]
                                                ])->textInput([
                                                    'placeholder' => $model->getAttributeLabel('subsidi_asuransi'),
                                                    'class' => 'form-control input-sm typeahead',
                                                    'autocomplete' => "off",
                                                    'type' => 'number'
                                                ])->label(false); ?>
                                    </div>
                                </div>
                            </div>
                           <div class="form-group">
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5">
                                        <b><?= $model->getAttributeLabel('total_tagihan') ?></b>
                                    </label>
                                    <div class="col-sm-7">
                                    <?= $form->field($model, 'total_tagihan',[
                                                    'template' => '{input}',
                                                    'options' => [
                                                        'tag' => false
                                                    ]
                                                ])->textInput([
                                                    'placeholder' => $model->getAttributeLabel('total_tagihan'),
                                                    'class' => 'form-control input-sm typeahead',
                                                    'autocomplete' => "off",
                                                    'type' => 'number'
                                                ])->label(false); ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5">
                                        <b><?= $model->getAttributeLabel('biaya_administrasi') ?></b>
                                    </label>
                                    <div class="col-sm-7">
                                    <?= $form->field($model, 'biaya_administrasi',[
                                                    'template' => '{input}',
                                                    'options' => [
                                                        'tag' => false
                                                    ]
                                                ])->textInput([
                                                    'placeholder' => $model->getAttributeLabel('biaya_administrasi'),
                                                    'class' => 'form-control input-sm typeahead',
                                                    'autocomplete' => "off",
                                                    'type' => 'number'
                                                ])->label(false); ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5">
                                        <b><?= $model->getAttributeLabel('uang_diterima') ?></b>
                                    </label>
                                    <div class="col-sm-7">
                                    <?= $form->field($model, 'uang_diterima',[
                                                    'template' => '{input}',
                                                    'options' => [
                                                        'tag' => false
                                                    ]
                                                ])->textInput([
                                                    'placeholder' => $model->getAttributeLabel('uang_diterima'),
                                                    'class' => 'form-control input-sm typeahead',
                                                    'autocomplete' => "off",
                                                    'type' => 'number'
                                                ])->label(false); ?>
                                    </div>
                                </div>
                            </div>
                           <div class="form-group">
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5">
                                        <b><?= $model->getAttributeLabel('uang_muka') ?></b>
                                    </label>
                                    <div class="col-sm-7">
                                    <?= $form->field($model, 'uang_muka',[
                                                    'template' => '{input}',
                                                    'options' => [
                                                        'tag' => false
                                                    ]
                                                ])->textInput([
                                                    'placeholder' => $model->getAttributeLabel('uang_muka'),
                                                    'class' => 'form-control input-sm typeahead',
                                                    'autocomplete' => "off",
                                                    'type' => 'number'
                                                ])->label(false); ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5">
                                        <b><?= $model->getAttributeLabel('pembulatan') ?></b>
                                    </label>
                                    <div class="col-sm-7">
                                    <?= $form->field($model, 'pembulatan',[
                                                    'template' => '{input}',
                                                    'options' => [
                                                        'tag' => false
                                                    ]
                                                ])->textInput([
                                                    'placeholder' => $model->getAttributeLabel('pembulatan'),
                                                    'class' => 'form-control input-sm typeahead',
                                                    'autocomplete' => "off",
                                                    'type' => 'number'
                                                ])->label(false); ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="text-left control-label col-sm-5">
                                        <b><?= $model->getAttributeLabel('uang_kembalian') ?></b>
                                    </label>
                                    <div class="col-sm-7">
                                    <?= $form->field($model, 'uang_kembalian',[
                                                    'template' => '{input}',
                                                    'options' => [
                                                        'tag' => false
                                                    ]
                                                ])->textInput([
                                                    'placeholder' => $model->getAttributeLabel('uang_kembalian'),
                                                    'class' => 'form-control input-sm typeahead',
                                                    'autocomplete' => "off",
                                                    'type' => 'number'
                                                ])->label(false); ?>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>