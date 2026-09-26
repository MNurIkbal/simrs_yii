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
use app\components\DocoHelpers;


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
                <?= DocoHelpers::generateToolbar([
                    'save' => [
                        'attributes' => [
                            'id' => 'save-pasien-karcis',
                            'onClick' => '',
                        ]
                    ],
                    'reset'=> [
                        'attributes'=>[
                            'data-parent' => '.filter-form'
                        ]
                    ]
                ]) ?>
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
                            <div id="error_TagihanPasienFormdetail_tagihan"></div>
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
                                        $_cache = [
                                            'tindakan' => [],
                                            'obat' => []
                                        ];
                                        if ($model->detail_tagihan) :
                                            $no = 1;
                                            foreach ($model->detail_tagihan as $value) :
                                                $row = [
                                                    'penjamin_pelayanan_id' => $value['penjamin_pelayanan_id'],
                                                    'carabayar_pelayanan_id' => $value['carabayar_pelayanan_id'],
                                                    'tindakan_obat_id' => $value['tindakan_obat_id'],
                                                    'kelaspelayanan_id' => $value['kelaspelayanan_id'],
                                                    'kelompoktindakan_id' => $value['kelompoktindakan_id'],
                                                    'pasien_id' => $value['pasien_id'],
                                                    'pendaftaran_id' => $value['pendaftaran_id'],
                                                    'penjamin_pendaftaran_id' => $value['penjamin_pendaftaran_id'],
                                                    'qty' => $value['qty'],
                                                    'ruangan_id' => $value['ruangan_id'],
                                                    'sub_total' => $value['sub_total'],
                                                    'tarif_satuan' => $value['tarif_satuan'],
                                                    'pelayanan_id' => $value['pelayanan_id'],
                                                ];
                                                $flag = 'tindakan';
                                                if (!empty($value['is_obat'])) :
                                                    $flag = 'obat';
                                                    $_cache['obat'][$value['pelayanan_id']] = $row;
                                                else :
                                                    $_cache['tindakan'][$value['pelayanan_id']] = $row;
                                                endif;
                                    ?>
                                                <tr data-id="<?= $value['pelayanan_id'] ?>">
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
                                                        <?= Html::dropDownList('carabayar_id', 
                                                            isset($value['carabayar_pelayanan_id']) 
                                                                    ? $value['carabayar_pelayanan_id'] 
                                                                    : '', 
                                                            $caraBayar, 
                                                            [
                                                                'class' => 'select2 event-karcis', 
                                                                'data-property' => 'cara_bayar',
                                                                'data-target' => $flag,
                                                                'prompt' => Yii::t('fe','--Pilih--')
                                                            ]) ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?= Html::dropDownList('penjamin_id', 
                                                            isset($value['penjamin_pelayanan_id']) 
                                                                ? $value['penjamin_pelayanan_id'] 
                                                                : '', 
                                                            $penjamin, 
                                                            [
                                                                'class' => 'select2 event-karcis',
                                                                'data-property' => 'penjamin',
                                                                'data-target' => $flag,
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
                                    'action' => '/kasir/pembayaran-tagihan/create',
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
                                                    'class' => 'form-control input-sm pickadate'
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
                                                    'class' => 'form-control input-sm text-right',
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
                                                    'class' => 'form-control input-sm text-right',
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
                                                    'class' => 'form-control input-sm text-right',
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
                                                    'class' => 'form-control input-sm text-right',
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
                                                    'class' => 'form-control input-sm text-right',
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
                                                    'class' => 'form-control input-sm text-right',
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
                                                    'class' => 'form-control input-sm text-right',
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
                                                    'class' => 'form-control input-sm text-right',
                                                    'autocomplete' => "off",
                                                    'type' => 'number'
                                                ])->label(false); ?>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        </div>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<?php
$_cache = json_encode($_cache);
$this->registerJs('
    $(document).on("click","#save-pasien-karcis", function (event) {
        event.preventDefault();
        var _form = $("#ajax-form").serializeArray();
        $.each(_cache, function (key, val) {
            $.each(val, function (id,item) {
                _form.push({name : "detail_tagihan[" + key + "]["+ id +"]", value : JSON.stringify(item)});
            })
        })
        $().docoForm("click",{
            url : "/kasir/inf-pasien-karcis/save",
            method : "POST",
            type : "json",
            data : _form,
            success : function (data) {

            }
        });
    });

    $(document).on("change",".event-karcis", function (event) {
        event.preventDefault();
        var _value = $(this).val();
        var _parent = $(this).closest("tr");
        var _property = $(this).attr("data-property");
        var _target = $(this).attr("data-target");
        var _id = _parent.attr("data-id");

        if (typeof _cache[_target][_id] != "undefined") {
            var _newValue = _cache[_target][_id];
            if (_property === "penjamin") {
                _newValue.penjamin_pelayanan_id = parseInt(_value);
            } else if (_property === "cara_bayar") {
                _newValue.carabayar_pelayanan_id = parseInt(_value);
            }
            _cache[_target][_id] = _newValue;
        }
        console.log(_cache);
    });

    var _cache = '. $_cache .';

    $(".pickadate").pickadate({
        format: "dd-mm-yyyy",
        formatSubmit: "yyyy-mm-dd",
        onStart: function() {
            var date = new Date();
            this.set("select", [[date.getFullYear(), date.getMonth() + 1, date.getDate()]]);
        }
    });
',View::POS_END,'pemakaian-barang');