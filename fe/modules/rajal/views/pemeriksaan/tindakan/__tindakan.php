<?php

/**
 * @Author: afil
 * @Date:   2018-01-16 15:08:25
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-03-21 17:23:28
 * @Description: 
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
?>

<div class="row">
    <!-- form tindakan start -->
        <div class="panel panel-flat">
            <div class="panel-heading" data-toggle="collapse" href="#collapse-riwayat-tindakanbmhp">
                <h5 class="panel-title"><?=Yii::t('fe', 'Transaksi Tindakan dan BMHP')?></h5>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse" data-toggle="collapse" href="#collapse-riwayat-tindakanbmhp"></a></li>
                    </ul>
                </div>
            </div>
            <div id="collapse-riwayat-tindakanbmhp" class="panel-collapse collapse in">
                <div class="panel-toolbar clearfix">
                    <?=DocoHelpers::generateToolbar([
                        'cppt' => [
                            'title' => 'CPPT',
                            'icon' => 'fa fa-arrow-left',
                            'attributes' => [
                                'href' => !empty($statepulang) ? '/rajal/inf-pasien-pulang' : '/rajal/pemeriksaan/',
                                'data-options' => 'click',
                                'id' => 'btn-tindakan-back'
                            ]
                        ],
                        'save' => ['attributes' => ['id' => 'btn-save']],
                        ]) ?>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-flat">
                                <?php $form = ActiveForm::begin([
                                    'id' => 'form-tindakanrajal-pegawai', 
                                    'type' => ActiveForm::TYPE_VERTICAL,
                                    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
                                ]) ?>
                                <div class="panel-body form-tindakan-bmhp">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group required">
                                                <label >Tanggal Tindakan</label>
                                                <p id="time_tgl_tindakan" class="form-control-static" style="margin-left:5px"></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <?=$form->field($modelTindakanBmhp, 'nama_dokterpj', ['labelOptions' => ['class' => '']])
                                            ->textInput([
                                                'class' => 'form-control input-sm', 
                                                'value' => $data_pasien['nama_pegawai'], 
                                                'id' => 'dokter_pemeriksa',
                                                'readonly' => 'readonly', 
                                            ]); ?>
                                            <?= Html::hiddenInput('TindakanBmhpForm[dokterpenanggungjawab_id]', $data_pasien['pegawai_id'], ['id' => 'tindakanbmhpform-dokterpenanggungjawab_id']) ?>
                                        </div>
                                        <div class="col-lg-6">
                                            <?=$form->field($modelTindakanBmhp, 'dokterdelegasi_id', ['labelOptions' => ['class' => 'text-right']])
                                                ->dropDownList(ArrayHelper::map($data_dokter, 'pegawai_id', 'nama_pegawai'), [
                                                    'class' => 'select2', 
                                                    'id' => 'dokter_delegasi',
                                                    'prompt' => Yii::t('fe', '-- Pilih dokter--')
                                                ]); ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <?=$form->field($modelTindakanBmhp, 'perawat1_id')
                                                ->dropDownList(ArrayHelper::map($data_perawat, 'pegawai_id', 'nama_pegawai'), [
                                                    'class' => 'select2', 
                                                    'id' => 'perawat1_id',
                                                    'prompt' => Yii::t('fe', '-- Pilih perawat--')
                                                ]); ?>
                                        </div>
                                        <div class="col-lg-6">
                                            <?=$form->field($modelTindakanBmhp, 'perawat2_id', ['labelOptions' => ['class' => 'text-right']])
                                                ->dropDownList(ArrayHelper::map($data_perawat, 'pegawai_id', 'nama_pegawai'), [
                                                    'class' => 'select2', 
                                                    'id' => 'perawat2_id',
                                                    'prompt' => Yii::t('fe', '-- Pilih perawat--')
                                                ]); ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <?=$form->field($modelTindakanBmhp, 'depo_id')
                                                ->dropDownList([], [
                                                    'class' => 'select2', 
                                                    'id' => 'depo_id',
                                                    'prompt' => Yii::t('fe', '-- Pilih Depo--')
                                                ]); ?>
                                        </div>
                                    </div>
                                </div>

                                <?php ActiveForm::end(); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="panel panel-flat">
                                <div class="panel-heading">
                                    <h5 class="panel-title"><?=Yii::t('fe', 'Tindakan')?></h5>
                                </div>
                                <div class="panel-body">

                                    <?php $form = ActiveForm::begin([
                                        'id' => 'form-tindakanrajal-tindakan', 
                                        'type' => ActiveForm::TYPE_VERTICAL,
                                        'enableClientValidation' => false,
                                        'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
                                    ]) ?>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <?= $form->field($modelTindakanPelayanan, 'daftartindakan_id',[
                                                'addon' => [
                                                    'prepend' => [
                                                        'content' => Html::checkbox('paket', false, [
                                                            'id' => 'paket', 'label' => Yii::t('fe', 'Paket')
                                                        ])
                                                    ],
                                                ]
                                            ])->dropDownList(
                                                ArrayHelper::map($data_tindakanruangan, 'daftartindakan_id', 'daftartindakan_nama'),
                                                [
                                                    'class' => 'form-control input-sm select2tindakan',
                                                    'id' => 'tindakan',
                                                    'prompt' => Yii::t('fe', '-- Pilih tindakan--')
                                                ]
                                            )->label(Yii::t('fe', 'Nama Tindakan / Paket')) ?>
                                            <?= Html::hiddenInput('penjamin_id', $data_pasien['penjamin_id'], ['id' => 'penjamin_id', 'readonly' => 'readonly']) ?>
                                            <?= Html::hiddenInput('kelaspelayanan_id', $data_pasien['kelaspelayanan_id'], ['id' => 'kelaspelayanan_id', 'readonly' => 'readonly']) ?>
                                        </div>
                                        <div class="col-md-4 detail-paket">
                                            
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <?=$form->field($modelTindakanPelayanan, 'tarif_satuan')
                                                ->textInput([
                                                    'class' => 'form-control input-sm', 
                                                    'readonly' => 'readonly', 
                                                ]); ?>
                                            <?= Html::hiddenInput('tarifsatuan_hidden', '', ['id' => 'tarifsatuan_hidden']) ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <?=$form->field($modelTindakanPelayanan, 'qty_tindakan')->textInput([
                                                'class' => 'form-control input-sm docoNumberOnly'
                                            ]) ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <?=$form->field($modelTindakanPelayanan, 'text_cyto_tindakan',[
                                                'addon' => [
                                                    'prepend' => [
                                                        'content' => Html::checkbox('cyto', false, [
                                                            'id' => 'tindakanpelayananform-cyto_tindakan',
                                                        ])
                                                    ],
                                                ]
                                            ])->textInput([
                                                    'class' => 'form-control input-sm', 
                                                    'readonly' => 'readonly', 
                                                    'placeholder' => Yii::t('fe', 'Tarif cyto'), 
                                                ])->label(); ?>
                                                <?=$form->field($modelTindakanPelayanan, 'tarifcyto_tindakan')->hiddenInput()->label(false)?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <?=$form->field($modelTindakanPelayanan, 'tarif_tindakan')
                                                ->textInput([
                                                    'class' => 'form-control input-sm date', 
                                                    'readonly' => 'readonly', 
                                                ]); ?>
                                            <?= Html::hiddenInput('tariftotal_hidden', '', ['id' => 'tariftotal_hidden']) ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="pull-right">
                                            <?=Html::button('<i class="fa fa-plus"></i> '.Yii::t('fe', 'Tambah'), ['class' => 'btn btn-success btn-sm', 'id' => 'tambah-tindakan']); ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <hr>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="table-responsive">
                                            <table id="table-trx-tindakan" class="table table-striped table-hover table-framed tabel-tindakan">
                                                <thead>
                                                    <tr class="bg-inverse">
                                                        <th>No</th>
                                                        <th><?= Yii::t('fe', 'Nama tindakan / paket') ?></th>
                                                        <th><?= Yii::t('fe', 'Qty') ?></th>
                                                        <th><?= Yii::t('fe', 'Tarif Satuan') ?></th>
                                                        <th><?= Yii::t('fe', 'Tarif Cyto') ?></th>
                                                        <th><?= Yii::t('fe', 'Jumlah Tarif') ?></th>
                                                        <th><?= Yii::t('fe', 'Hapus') ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="first-class">
                                                        <th colspan="7" class="text-center">Data Kosong</th>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <?php ActiveForm::end(); ?>
                                </div>
                            </div>
                        </div>
                        <!-- form tindakan end -->
                        <!-- form bmhp start -->
                        <div class="col-lg-6">
                            <div class="panel panel-flat">
                                <div class="panel-heading">
                                    <h5 class="panel-title"><?=Yii::t('fe', 'BMHP Pemakaian Obat / Alkes Non - Reseptur')?></h5>
                                </div>
                                <div class="panel-body">

                                <?php $form = ActiveForm::begin([
                                    'id' => 'form-tindakanrajal-bmhp', 
                                    'type' => ActiveForm::TYPE_VERTICAL,
                                    'enableClientValidation' => false,
                                    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
                                ]) ?>
                                    <div class="col-md-6" 
                                        <?php if($kelompokpegawai_id == DocoConstants::KELOMPOK_KEPERAWATAN){ echo "hidden";};?>
                                    >
                                        <div class="col-md-6">
                                            <?= $form->field($modelBmhp, 'obat')->radioList(
                                                $data_group_obat,
                                                [
                                                    'item' => function($index, $label, $name, $checked, $value) {
                                                        $return = '<label class="radio-inline">';
                                                        $return .= '<input type="radio" name="'.$name.'" class="jenis_pemakaian" value="'.$value.'" tabindex="3">';
                                                        $return .= '<i></i>';
                                                        $return .= '<span>'.ucwords($label).'</span>';
                                                        $return .= '</label>';
                                                        return $return;
                                                    }
                                                ]
                                            ) ?>
                                            <?= Html::hiddenInput('temp_obat', null, ['id' => 'temp_obat', 'readonly' => 'readonly']) ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?= $form->field($modelBmhp, 'hargasatuan_oa')->textInput([
                                                    'class' => 'form-control input-sm docoNumberOnly jml',
                                                    'readonly'=>true 
                                            ])->label(Yii::t('fe', 'Harga Satuan')) ?>
                                            <?= Html::hiddenInput('harga_obat_satuan_hidden', '', ['id' => 'harga_obat_satuan_hidden']) ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?=$form->field($modelBmhp, 'daftartindakan_id')
                                                ->dropDownList([], [
                                                    'class' => 'form-control input-sm select2', 
                                                    'id' => 'tindakan_obatalkes',
                                                    'prompt' => Yii::t('fe', '-- Pilih nama tindakan--')
                                                ]); ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?=$form->field($modelBmhp, 'jumlah_tarif',[
                                                    'addon' => [
                                                        'prepend' => [
                                                            'content' => Html::checkbox('tagih_pasien', false, [
                                                                'id' => 'tagih_pasien', 
                                                                'label' => Yii::t('fe', 'Tagihkan')
                                                            ])
                                                        ],
                                                    ]
                                                ])
                                                ->textInput([
                                                    'class' => 'form-control input-sm jml_tarif', 
                                                    'readonly' => 'readonly', 
                                                ]); ?>
                                            <?= Html::hiddenInput('jumlahtarifobat_hidden', '', ['id' => 'jumlahtarifobat_hidden']) ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?=$form->field($modelBmhp, 'obatalkes_id')
                                                ->dropDownList([], [
                                                    'class' => 'form-control input-sm', 
                                                    'id' => 'obatalkes_select2',
                                                    'prompt' => Yii::t('fe', '-- Pilih obat/alkes --')
                                                ]); ?>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label">Stok Tersedia</label>
                                                <?=Html::textInput('stok_obat_tersedia',0,[
                                                    'id' => 'stok_obat_tersedia',
                                                    'class' => 'form-control',
                                                    'readonly' => true
                                                ])?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?= $form->field($modelBmhp, 'qty_oa')->textInput([
                                                'class' => 'form-control input-sm doco-decimal jml', 
                                            ])->label(Yii::t('fe', 'Jumlah')) ?>
                                        </div>
                                        <div class="col-md-6">
                                            <div id="errorJumlah" class="help-block"></div>
                                        </div>
                                    </div>
                                    <div class="row">

                                    </div>
                                    <div class="row">

                                        <div class="col-md-6">
                                            <label for="" class="control-label col-sm-4"></label>
                                            <div class="col-md-8">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="pull-right">
                                            <?=Html::button('<i class="fa fa-plus"></i> '.Yii::t('fe', 'Tambah'), ['class' => 'btn btn-success btn-sm', 'id' => 'tambah-obatalkes']); ?>
                                        </div>
                                    </div>
                                    <?=Html::activeHiddenInput($modelBmhp, 'obatalkes_nama'); ?>
                                    <?=Html::activeHiddenInput($modelBmhp, 'harganetto_oa'); ?>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <hr>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="table-responsive">
                                            <table id="table-trx-bmhp" class="table table-striped table-hover table-framed tabel-bmhp">
                                                <thead>
                                                    <tr class="bg-inverse">
                                                        <th><?=Yii::t('fe', 'No')?></th>
                                                        <th><?=Yii::t('fe', 'Nama tindakan')?></th>
                                                        <th><?=Yii::t('fe', 'Obat/alkes')?></th>
                                                        <th><?=Yii::t('fe', 'Qty')?></th>
                                                        <th><?=Yii::t('fe', 'Ditagihkan')?></th>
                                                        <th><?=Yii::t('fe', 'Harga Satuan')?></th>
                                                        <th><?=Yii::t('fe', 'Jumlah tarif')?></th>
                                                        <th><?=Yii::t('fe', 'Hapus')?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="first-class">
                                                        <th colspan="8" class="text-center">Data Kosong</th>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <?php ActiveForm::end(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    <!-- form bmhp end -->
</div>

<div class="row">
    <div class="panel panel-flat">
        <div class="panel-heading">
            <h5 class="panel-title"><?=Yii::t('fe', 'Riwayat Tindakan dan BMHP')?></h5>
        </div>

        <div class="panel-toolbar clearfix">
            <?= Html::button('<b><i class="fa fa-print "></i></b> '.Yii::t('fe', 'Print'), ['class' => 'btn btn-info btn-labeled btn-xs', 'id' => 'btn-print', 'data-id' => $pendaftaran_id]) ?>
        </div>
        <div class="panel-body">
            <h4><?=Yii::t('fe', 'Tindakan')?></h4>
            <div class="table-responsive">
                <table class="table datatable-basic table-striped table-hover dataTable" 
                    id="data-riwayat-tindakan" 
                    style="width:100%"
                    >
                    <thead>
                        <tr class="bg-inverse">
                            <th>No</th>
                            <th><?= Yii::t('fe', 'Tanggal tindakan') ?></th>
                            <th><?= Yii::t('fe', 'Nama tindakan / paket') ?></th>
                            <th><?= Yii::t('fe', 'Dokter Pemeriksa') ?></th>
                            <th><?= Yii::t('fe', 'Dokter Delegasi') ?></th>
                            <th><?= Yii::t('fe', 'Perawat 1') ?></th>
                            <th><?= Yii::t('fe', 'Perawat 2') ?></th>
                            <th><?= Yii::t('fe', 'Qty') ?></th>
                            <th><?= Yii::t('fe', 'Tarif Satuan') ?></th>
                            <th><?= Yii::t('fe', 'Tarif Cyto') ?></th>
                            <th><?= Yii::t('fe', 'Jumlah Tarif') ?></th>
                            <th><?=Yii::t('fe', 'Batal')?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $total = 0; ?>
                        <?php if (!empty($data_tindakanbmhp)):
                            $no = 1;
                            $count = 0;
                            foreach ($data_tindakanbmhp as $v_tindakan):
                                $qty = isset($v_tindakan['tindakanbmhp_is_deleted']) && $v_tindakan['tindakanbmhp_is_deleted'] == TRUE ? '<del>'.$v_tindakan['qty'].'</del>' : $v_tindakan['qty'];

                                $tarif_satuan = isset($v_tindakan['tindakanbmhp_is_deleted']) && $v_tindakan['tindakanbmhp_is_deleted'] == TRUE ? '<del>'.DocoHelpers::rupiahDisplay($v_tindakan['tarif_satuan']).'</del>' : DocoHelpers::rupiahDisplay($v_tindakan['tarif_satuan']);

                                $tarifcyto_tindakan = isset($v_tindakan['tindakanbmhp_is_deleted']) && $v_tindakan['tindakanbmhp_is_deleted'] == TRUE ? '<del>'.DocoHelpers::rupiahDisplay($v_tindakan['tarifcyto_tindakan']).'</del>' : DocoHelpers::rupiahDisplay($v_tindakan['tarifcyto_tindakan']);

                                $jumlah_tarif = isset($v_tindakan['tindakanbmhp_is_deleted']) && $v_tindakan['tindakanbmhp_is_deleted'] == TRUE ? '<del>'.DocoHelpers::rupiahDisplay($v_tindakan['jumlah_tarif']).'</del>' : DocoHelpers::rupiahDisplay($v_tindakan['jumlah_tarif']);

                                if ($v_tindakan['tipe_pelayanan'] == 'TINDAKAN' || $v_tindakan['tipe_pelayanan'] == 'PAKET'): ?>
                                    <tr data-id='<?= $v_tindakan['tindakan_obat_id'] ?>'>
                                        <td class='td-no'><?= $no ?></td>
                                        <td><?= date('j F Y H:i:s', strtotime($v_tindakan['tgl_tindakan'])) ?></td>
                                        <td class='daftartindakan-nama'>
                                            <?= $v_tindakan['tipepaket_nama'] != '' 
                                                        ? $v_tindakan['tipepaket_nama'] 
                                                        : $v_tindakan['tindakan_obat'] ?>
                                        </td>
                                        <td class='dokterpenanggungjawab-nama'>
                                            <?= $v_tindakan['dokter_pemeriksa'] ?>
                                        </td>
                                        <td><?= $v_tindakan['dokter_delegasi'] ?></td>
                                        <td><?= $v_tindakan['perawat_1'] ?></td>
                                        <td><?= $v_tindakan['perawat_2'] ?></td>
                                        <td><?= $qty ?></td>

                                        <!-- <td><?//= $v_tindakan['qty'] ?></td> -->
                                        <td nowrap style="text-align: right">
                                            <?= $tarif_satuan ?>
                                        </td>
                                        <td nowrap style="text-align: right">
                                            <?= $tarifcyto_tindakan ?>
                                        </td>
                                        <td nowrap style="text-align: right">
                                            <?= $jumlah_tarif ?>
                                        </td>
                                        <?php if(isset($v_tindakan['tindakanbmhp_is_deleted']) 
                                                        && $v_tindakan['tindakanbmhp_is_deleted'] == TRUE) { 
                                        ?>
                                        <td><span class="label label-danger">Dibatalkan</span></td>
                                        <?php } else if (isset($v_tindakan['tindakansudahbayar_id']) 
                                                            || isset($v_tindakan['pasienpulang_id'])) {
                                        ?>
                                        <td><span class="label label-default">Sudah Dibayar/Pulang</span></td>
                                        <?php } else {?>
                                        <td class='td-hapus'><button type='button' data-id="<?=$v_tindakan['id']?>" data-tipe="<?=$v_tindakan['tipe_pelayanan']?>" class='btn btn-danger btn-batal-tindakan'><i class='fa fa-remove'></i></button></td>
                                        <?php }?>
                                    </tr>
                                <?php $count = $count + 1; 
                                    $no = $no + 1;
                                    if(isset($v_tindakan['tindakanbmhp_is_deleted']) && $v_tindakan['tindakanbmhp_is_deleted'] == FALSE) {
                                        $total = $total + $v_tindakan['jumlah_tarif'];
                                    }
                                endif;
                            endforeach;
                        endif; ?> 
                    </tbody>
                    <tfoot id="foot-tindakan">
                        <tr class='tr-foot'>
                            <th colspan='10' class='text-center'>Total</th>
                            <th nowrap style="text-align: right"><?=  DocoHelpers::rupiahDisplay($total) ?></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <br>
            <h4><?=Yii::t('fe', 'BMHP')?></h4>
            <div class="table-responsive">
                <table class="table datatable-basic table-striped table-hover dataTable" 
                    id="data-riwayat-bmhp" 
                    style="width:100%"
                    >
                    <thead>
                        <tr class="bg-inverse">
                            <th><?=Yii::t('fe', 'No')?></th>
                            <th><?=Yii::t('fe', 'Tanggal tindakan')?></th>
                            <th><?=Yii::t('fe', 'Nama tindakan')?></th>
                            <th><?=Yii::t('fe', 'Obat/alkes')?></th>
                            <th><?=Yii::t('fe', 'Depo')?></th>
                            <th><?=Yii::t('fe', 'Nama perawat 1')?></th>
                            <th><?=Yii::t('fe', 'Nama perawat 2')?></th>
                            <th><?=Yii::t('fe', 'Qty')?></th>
                            <th><?=Yii::t('fe', 'Ditagihkan')?></th>
                            <th><?=Yii::t('fe', 'Jumlah tarif')?></th>
                            <th><?=Yii::t('fe', 'Batal')?></th>
                        </tr>
                    </thead>
                    <tbody> 
                        <?php $total = 0; ?>
                        <?php if (!empty($data_tindakanbmhp)):
                            $no = 1;
                            $count = 0;
                            foreach ($data_tindakanbmhp as $key => $v_bmhp):
                                if ($v_bmhp['tipe_pelayanan'] == 'BMHP'): ?>
                                    <tr data-id='<?= $v_bmhp['tindakan_obat_id'] ?>'>
                                        <td class='td-no-bmhp'><?= $no ?></td>
                                        <td><?= date('j F Y H:i:s', strtotime($v_bmhp['tgl_tindakan'])) ?></td>
                                        <td class='daftartindakan-nama'><?= $v_bmhp['tipepaket_nama'] != '' ? $v_bmhp['tipepaket_nama'] : $v_bmhp['tindakan'] ?></td>
                                        <td><?= $v_bmhp['tindakan_obat'] ?></td>
                                        <td><?= $v_bmhp['ruangan_pelayanan'] ?></td>
                                        <td><?= $v_bmhp['perawat_1'] != '' ? $v_bmhp['perawat_1'] : '-' ?></td>
                                        <td><?= $v_bmhp['perawat_2'] != '' ? $v_bmhp['perawat_2'] : '-' ?></td>
                                        <td><?= $v_bmhp['qty'] ?></td>
                                        <td><?= $v_bmhp['ditagihkan'] != null ? '✓' : '-' ?></td>
                                        <td nowrap style="text-align: right"><?=DocoHelpers::rupiahDisplay($v_bmhp['jumlah_tarif']) ?></td>
                                        <?php if(isset($v_bmhp['tindakanbmhp_is_deleted']) && $v_bmhp['tindakanbmhp_is_deleted'] == TRUE){ ?>
                                        <td><span class="label label-danger">Dibatalkan</span></td>

                                        <?php }else if(isset($v_bmhp['tindakansudahbayar_id']) || isset($v_bmhp['pasienpulang_id'])){?>
                                        <td><span class="label label-default">Sudah Dibayar/Pulang</span></td>
                                        <?php }else{?>
                                        <td class='td-hapus'><button type='button' data-id="<?=$v_bmhp['id']?>" data-tipe="<?=$v_bmhp['tipe_pelayanan']?>" class='btn btn-danger btn-batal-bmhp'><i class='fa fa-remove'></i></button></td>
                                        <?php }?>
                                    </tr>
                                    <?php $count = $count + 1; 
                                    $no = $no + 1;
                                    $total = $total + $v_bmhp['jumlah_tarif'];
                                endif;
                            endforeach;
                        endif; ?>
                    </tbody>
                    <tfoot id="foot-bmhp">
                        <tr class='tr-foot'>
                            <th colspan='8' class='text-center'>Total</th>
                            <th nowrap style="text-align: right"><?= DocoHelpers::rupiahDisplay($total) ?></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
        $(document).ready(function(){
            $(".select2").select2()
            $("#form-tindakanrajal :input").prop("disabled", '.$status_update.');
            $("#btn-reset, #btn-save").prop("disabled", '.$status_update.');
            var _ruanganId = '.$ruangan_id.';
            var _ruanganNama = "'.$ruangan_nama.'";
            var _ruanganDepoId = '.$default_depo.';
            var _ruanganDepoNama = "'.$ruangan_depo.'";
            var _instalasi_id = '.$instalasi_id.';
            var _new = 
            {
                "id": _ruanganId,
                "text": _ruanganNama,
                "datavalue": {
                    "ruangan_id": _ruanganId,
                    "ruangan_nama": _ruanganNama
                }
            }
            $("#depo_id").docoPaginationSelec2(
                config = {
                    ajax: {
                        processResults: function (data, params) {
                            var _results = data.result;
                            _results.push(_new);
                            params.page = params.page || 1;
                            return {
                            results: _results,
                                pagination: {
                                    more: data.pagination.more
                                }
                            }
                        }
                    },
                    placeholder : "-- Cari Depo --",     
                    _api : "/rajal/master-api/list-depo",
                }
            );

            var data = {
                id: _ruanganDepoId,
                text: _ruanganDepoNama
            };

            if ($("#depo_id").find("option[value=" + data.id + "]").length) {
                $("#depo_id").val(data.id).trigger("change");
            } else { 
                var newOption = new Option(data.text, data.id, true, true);
                $("#depo_id").append(newOption).trigger("change");
            } 
        });
    ');
$this->registerJs($this->render('js/__tindakan.js'));
?>