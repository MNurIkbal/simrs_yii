<?php
// Author : Ardi Pratama
use app\components\DocoConstants;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\web\View;
?>
<style type="text/css">
    .select2-selection__rendered {
        width: 200px;
    }
</style>
<div class="col-lg-12">
    <div class="row">
    <?php $form = ActiveForm::begin([
        'id' => 'form-tindakan', 
        'enableClientValidation'=> false,
        'type' => ActiveForm::TYPE_VERTICAL,
        'formConfig' => [
            'labelSpan' => 4, 
            'deviceSize' => ActiveForm::SIZE_SMALL,
            'showErrors' => true
        ]
    ]) ?>
        <div class="col-lg-6">
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h5 class="panel-title"><?=Yii::t('fe', 'Tindakan')?></h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                        </ul>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($modelInstruksiTindakan, 'tgl_tindakan')->textInput([
                                'class' => 'form-control input-sm date',
                                'readonly' => 'readonly'
                            ]) ?>
                        </div>
                        <div class="col-md-6">
                            <?=$form->field($modelInstruksiTindakan, 'dokterdpjp_id')->widget(Select2::className(),[
                                'data' => ArrayHelper::map($data_dokter, 'pegawai_id', 'nama_pegawai'),
                                'disabled' => isset($modelInstruksiTindakan->dokterdpjp_id)?true:false,
                                'options' => [
                                    'prompt' => Yii::t('fe', '-- Pilih dokter--'),
                                    'id' => 'dokter_pemeriksa'
                                ],
                            ]);
                                ?>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <?php
                            echo $form->field($modelInstruksiTindakan, 'daftartindakan_id',[
                                'addon' => [
                                    'prepend' => [
                                        'content' => Html::checkbox('paket', false, ['id' => 'paket', 'label' => Yii::t('fe', 'Paket')])
                                    ],
                                ]
                            ])->dropDownList(
                                ArrayHelper::map($data_tindakanruangan, 'daftartindakan_id', 'daftartindakan_nama'),
                                [
                                    'class' => 'select-2',
                                    'id' => 'tindakan',
                                    'prompt' => Yii::t('fe', '-- Pilih tindakan--')
                                ]
                            )->label(Yii::t('fe', 'Nama Tindakan / Paket')) 
                            ?>
                            <?= Html::hiddenInput('pasienadmisi_id', @$data_pasien['pasienadmisi_id'], [
                                    'id' => 'pasienadmisi_id', 
                                    'readonly' => 'readonly'
                            ]) ?>
                            <?= Html::hiddenInput('penjamin_id', isset($data_pasien['penjamin_id'])?$data_pasien['penjamin_id']:'', [
                                    'id' => 'penjamin_id', 
                                    'readonly' => 'readonly'
                            ]) ?>
                            <?= Html::hiddenInput('kelaspelayanan_id', isset($data_pasien['kelaspelayanan_id'])?$data_pasien['kelaspelayanan_id']:'', [
                                    'id' => 'kelaspelayanan_id', 
                                    'readonly' => 'readonly'
                            ]) ?>
                        </div>
                        <div class="col-md-6">
                            <?=$form->field($modelInstruksiTindakan, 'perawat1_id')->widget(Select2::className(),[
                                'data' => ArrayHelper::map($data_perawat, 'pegawai_id', 'nama_pegawai'),
                                'disabled' => isset($modelInstruksiTindakan->perawat1_id)?true:false,
                                'options' => [
                                    'prompt' => Yii::t('fe', '-- Pilih perawat--'),
                                    'id' => 'perawat1_id'
                                ],
                            ]);
                                ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?=$form->field($modelInstruksiTindakan, 'tarif_satuan')
                                ->textInput([
                                    'class' => 'form-control input-sm doco-number', 
                                    'readonly' => 'readonly', 
                                ]); ?>
                        </div>
                        <div class="col-md-6">
                            <?=$form->field($modelInstruksiTindakan, 'perawat2_id', ['labelOptions' => ['class' => 'text-right']])
                                ->dropDownList(ArrayHelper::map($data_perawat, 'pegawai_id', 'nama_pegawai'), [
                                    'class' => 'form-control input-sm select-2', 
                                    'id' => 'perawat2_id',
                                    'prompt' => Yii::t('fe', '-- Pilih perawat--')
                                ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?=$form->field($modelInstruksiTindakan, 'tarif_cyto',[
                                    'addon' => [
                                        'prepend' => [
                                            'content' => Html::checkbox('cyto', false, [
                                                    'id' => 'instruksitindakanform-cyto_tindakan', 
                                                    'label' => Yii::t('fe', 'Cyto')
                                            ]) 
                                        ]
                                    ]
                                ])->textInput([
                                    'class' => 'form-control input-sm doco-number', 
                                    'readonly' => 'readonly', 
                                    'placeholder' => Yii::t('fe', 'Tarif cyto'), 
                                ])->label(false); ?>
                        </div>
                        <div class="col-md-4 detail-paket"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?=$form->field($modelInstruksiTindakan, 'qty')->textInput([
                                'class' => 'form-control input-sm docoNumberOnly'
                            ]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?=$form->field($modelInstruksiTindakan, 'tarif_tindakan')
                                ->textInput([
                                    'class' => 'form-control input-sm  doco-number', 
                                    'readonly' => 'readonly', 
                                ]); ?>
                        </div>
                        <div class="col-md-6"></div>
                    </div>
                    <div class="row">
                        <div class="pull-right">
                            <?= Html::button('<i class="fa fa-plus"></i> '.Yii::t('fe', 'Tambah'), [
                                'class' => 'btn btn-success btn-sm', 
                                'id' => 'tambah-tindakan'
                            ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <hr>
                        </div>
                    </div>
                    <div class="row">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-framed tabel-tindakan">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th class="col-sm-1">No</th>
                                        <th class="col-sm-1"><?= Yii::t('fe', 'Tanggal tindakan') ?></th>
                                        <th class="col-sm-2"><?= Yii::t('fe', 'Nama tindakan / paket') ?></th>
                                        <th class="col-sm-1"><?= Yii::t('fe', 'Dokter Pemeriksa') ?></th>
                                        <th class="col-sm-1"><?= Yii::t('fe', 'Perawat 1') ?></th>
                                        <th class="col-sm-1"><?= Yii::t('fe', 'Perawat 2') ?></th>
                                        <th class="col-sm-1"><?= Yii::t('fe', 'Jumlah Tindakan') ?></th>
                                        <th class="col-sm-1"><?= Yii::t('fe', 'Implementasi') ?></th>
                                        <th class="col-sm-1"><?= Yii::t('fe', 'Cyto') ?></th>
                                        <th class="col-sm-1"><?= Yii::t('fe', 'Hapus') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        $total = 0; 
                                        $countTindakan = 0;
                                        $no = 1;
                                    ?>
                                    <?php 
                                    if (!empty($data_tindakanbmhp) && is_array($data_tindakanbmhp) && count($data_tindakanbmhp)>0):
                                        foreach ($data_tindakanbmhp as $key => $value):
                                            if ($value['tipe'] == 'TINDAKAN' || $value['tipe'] == 'PAKET'): 
                                                if ($value['tindakan_deleted'] == true) {
                                                    continue;
                                                }
                                    ?>
                                                <?php
                                                    $idgenerated_tindakan = isset($value['instruksitindakan_id']) ? $value['instruksitindakan_id'] : 0;
                                                ?>
                                                <tr class='is_tbl_tindakan' data-id='<?= $value['tindakan_paket_obat_id'] ?>' data-count='<?=$countTindakan?>' data-id_instruksi_tindakan='<?=$idgenerated_tindakan?>'>
                                                    <?php 
                                                    if(isset($value['is_cyto']) && $value['is_cyto'] == TRUE){
                                                        $value['is_cyto'] = 1;
                                                    }else{
                                                        $value['is_cyto'] = 0;
                                                    }
                                                    if(isset($value['paketDetail']) && is_array($value['paketDetail'])){
                                                        $countDetail = count($value['paketDetail']) + 1;
                                                        ?>
                                                    <td rowspan="<?=@$countDetail?>" class='td-no'><?= $no ?></td>
                                                    <?php
                                                    }else{
                                                    ?>
                                                    <td class='td-no'><?= $no ?></td>
                                                    <?php }?>
                                                    <td><?= date('j F Y h:i:s', strtotime($value['tgl_tindakan'])) ?></td>
                                                    <?php
                                                    $tindakan_nama = $value['tindakan_paket_obat'] != '' ? $value['tindakan_paket_obat'] : '';
                                                    
                                                        ?>
                                                    <td class='daftartindakan-nama'><?=$tindakan_nama?></td>
                                                    <td class='dokterdpjp-nama'><?= $value['dokter_periksa'] ?></td>
                                                    <td><?= @$value['perawat_1'] ?></td>
                                                    <td><?= @$value['perawat_2'] ?></td>
                                                    <td class="qty"><?= @$value['qty'] ?></td>
                                                    <td><?= @$value['nama_status_implementasi'] ?></td>
                                                    <td><?= $value['is_cyto'] == TRUE ? '✓' : '-' ?></td>
                                                    <?php
                                                        if($value['status_implementasi'] == '454'){
                                                    ?>

                                                        <?php if($isUbah == "1"){ ?>
                                                        <td class="td-ganti-tindakan">
                                                            <button type='button' class='btn btn-danger btn-ganti-tindakan'><i class='fa fa-remove'></i></button>
                                                        </td>
                                                        <?php }else{?>
                                                        <td class='td-hapus'><button type='button' class='btn btn-danger btn-remove-tindakan'><i class='fa fa-remove'></i></button></td>
                                                        <?php }?>

                                                    <?php } else{?>
                                                        <td>&nbsp;</td>
                                                    <?php }?>

                                                    <?= Html::hiddenInput('InstruksiTindakanForm['.$countTindakan.'][tgl_tindakan]', $value['tgl_tindakan'], ['class'=>'tgl_tindakan','readonly' => 'readonly']) ?>
                                                    <?= Html::hiddenInput('InstruksiTindakanForm['.$countTindakan.'][daftartindakan_id]',  $value['tindakan_paket_obat_id'], ['class' => 'daftartindakan_id', 'readonly' => 'readonly']) ?>
                                                    <?= Html::hiddenInput('InstruksiTindakanForm['.$countTindakan.'][dokterdpjp_id]', @$value['dokterdpjp_id'], ['class' => 'dokterdpjp_id', 'readonly' => 'readonly']) ?>
                                                    <?= Html::hiddenInput('InstruksiTindakanForm['.$countTindakan.'][perawat1_id]', @$value['perawat1_id'], ['class'=>'perawat1_id','readonly' => 'readonly']) ?>
                                                    <?= Html::hiddenInput('InstruksiTindakanForm['.$countTindakan.'][perawat2_id]', @$value['perawat2_id'], ['class'=>'perawat2_id','readonly' => 'readonly']) ?>
                                                    <?= Html::hiddenInput('InstruksiTindakanForm['.$countTindakan.'][qty]', $value['qty'], ['class'=>'qty','readonly' => 'readonly']) ?>
                                                    <?= Html::hiddenInput('InstruksiTindakanForm['.$countTindakan.'][status_implementasi]', @$value['status_implementasi'], ['readonly' => 'readonly']) ?>
                                                    <?= Html::hiddenInput('InstruksiTindakanForm['.$countTindakan.'][pasien_id]', @$value['pasien_id'], ['readonly' => 'readonly']) ?>
                                                    <?= Html::hiddenInput('InstruksiTindakanForm['.$countTindakan.'][carabayar_id]', @$value['carabayar_id'], ['readonly' => 'readonly']) ?>
                                                    <?= Html::hiddenInput('InstruksiTindakanForm['.$countTindakan.'][pendaftaran_id]', @$value['pendaftaran_id'], ['readonly' => 'readonly']) ?>
                                                    <?= Html::hiddenInput('InstruksiTindakanForm['.$countTindakan.'][jeniskasuspenyakit_id]', @$value['jeniskasuspenyakit_id'], ['readonly' => 'readonly']) ?>
                                                    <?= Html::hiddenInput('InstruksiTindakanForm['.$countTindakan.'][penjamin_id]', @$value['penjamin_id'], ['readonly' => 'readonly']) ?>
                                                    <?= Html::hiddenInput('InstruksiTindakanForm['.$countTindakan.'][ruangan_id]', @$value['ruangan_rawat_id'], ['readonly' => 'readonly']) ?>
                                                    <?= Html::hiddenInput('InstruksiTindakanForm['.$countTindakan.'][instalasi_id]', @$value['instalasi_id'], ['readonly' => 'readonly']) ?>
                                                    <?= Html::hiddenInput('InstruksiTindakanForm['.$countTindakan.'][kelaspelayanan_id]', @$value['kelaspelayanan_id'], ['readonly' => 'readonly']) ?>
                                                    <?= Html::hiddenInput('InstruksiTindakanForm['.$countTindakan.'][komponentarif_id]', isset($value['komponentarif_id']) ? $value['komponentarif_id'] : null, ['readonly' => 'readonly']) ?>
                                                    <?= Html::hiddenInput('InstruksiTindakanForm['.$countTindakan.'][tarif_satuan]', @$value['tarif_satuan'], ['readonly' => 'readonly']) ?>
                                                    <?= Html::hiddenInput('InstruksiTindakanForm['.$countTindakan.'][tarif_cyto]', @$value['tarif_cyto'], ['readonly' => 'readonly']) ?>
                                                    <?= Html::hiddenInput('InstruksiTindakanForm['.$countTindakan.'][is_cyto]', @$value['is_cyto'], ['class'=>'is_cyto','readonly' => 'readonly']) ?>
                                                    <?= Html::hiddenInput('InstruksiTindakanForm['.$countTindakan.'][jumlah_tarif]', @$value['jumlah_tarif'], ['readonly' => 'readonly']) ?>
                                                    <?= Html::hiddenInput('InstruksiTindakanForm['.$countTindakan.'][tipepaket_id]', $value['tipe'] == "PAKET" ? @$value['tindakan_paket_obat_id'] : '', ['readonly' => 'readonly']) ?>
                                                    <?= Html::hiddenInput('InstruksiTindakanForm['.$countTindakan.'][id_instruksi_tindakan]', @$idgenerated_tindakan, ['readonly' => 'readonly']) ?>
                                                    <?php
                                                    if($isUbah == "1"){
                                                        echo Html::hiddenInput('InstruksiTindakanForm['.$countTindakan.'][instruksitindakan_id]', @$value['instruksitindakan_id'], ['readonly' => 'readonly']); 
                                                    }
                                                    ?>
                                                </tr>
                                                <?php
                                                if(isset($value['paketDetail']) && is_array($value['paketDetail'])){
                                                        $listpaket = '';
                                                        foreach ($value['paketDetail'] as $detail) {
                                                            $listpaket .=  "<tr data-id='".@$value['tindakan_paket_obat_id']."' data-count='".@$countTindakan."' class='detailed-paket'><td></td><td></td><td colspan='8'>";
                                                            $listpaket .= @$detail;
                                                            $listpaket .= "</td></tr>";
                                                        }
                                                        echo $listpaket;
                                                    }
                                                ?>
                                            <?php $countTindakan = $countTindakan + 1; 
                                                $no = $no + 1;
                                                $total = $total + @$value['jumlah_tarif'];
                                            endif;
                                        endforeach;
                                    else:
                                ?>
                                    <tr class="first-class">
                                        <th colspan="10" class="text-center">Data Kosong</th>
                                    </tr>
                                <?php
                                    endif; 
                                ?>
                                </tbody>
                            </table>
                            <?= Html::hiddenInput('pasien_id', @$data_pasien['pasien_id'], ['id' => 'pasien_id']) ?>
                            <?= Html::hiddenInput('carabayar_id', @$data_pasien['carabayar_id'], ['id' => 'carabayar_id']) ?>
                            <?= Html::hiddenInput('InstruksiTindakanForm[pendaftaran_id]', @$data_pasien['pendaftaran_id'], ['id' => 'pendaftaran_id']) ?>
                            <?= Html::hiddenInput('jeniskasuspenyakit_id', @$data_pasien['jeniskasuspenyakit_id'], ['id' => 'jeniskasuspenyakit_id']) ?>
                            <?= Html::hiddenInput('penjamin_id', @$data_pasien['penjamin_id'], ['id' => 'new_penjamin_id']) ?>
                            <?= Html::hiddenInput('ruangan_id', $id_ruangan, ['id' => 'ruangan_id']) ?>
                            <?= Html::hiddenInput('instalasi_id', $id_instalasi, ['id' => 'instalasi_id']) ?>
                            <?= Html::hiddenInput('komponentarif_id', '', ['id' => 'komponentarif_id']) ?>
                            <?= Html::hiddenInput('tipepaket_id', '', ['id' => 'tipepaket_id']) ?>
                            <?= Html::hiddenInput('tipe', '', ['id' => 'tipe']) ?>
                            <?= Html::hiddenInput('countTindakan', $countTindakan, ['id' => 'countTindakan']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="panel panel-flat">
                <div class="panel-heading">
                <h5 class="panel-title"><?=Yii::t('fe', 'Pemakaian Obat / Alkes Non - Reseptur')?></h5>
                    <div class="heading-elements">
                        <ul class="icons-list">
                            <li><a data-action="collapse"></a></li>
                        </ul>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="row">
                    <div class="col-md-6" 
                        <?php if($kelompokpegawai_id == DocoConstants::KELOMPOK_KEPERAWATAN){ echo "hidden";};?>
                    >
                            <?= $form->field($modelInstruksiBmhp, 'obat')->radioList(
                                $listJenisPemakaian,
                                [
                                    'item' => function($index, $label, $name, $checked, $value) {
                                        // Item
                                        $return = '<label class="radio-inline">';
                                        $return .= '<input type="radio" name="'.$name.'" class="jenis_pemakaian" value="'.$value.'" tabindex="3">';
                                        $return .= '<i></i>';
                                        $return .= '<span>'.ucwords($label).'</span>';
                                        $return .= '</label>';

                                        // Return
                                        return $return;
                                    }
                                ]
                            ) ?>
                            <?= Html::hiddenInput('temp_obat', null, ['id' => 'temp_obat', 'readonly' => 'readonly']) ?>
                            
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                                <?= $form->field($modelInstruksiBmhp, 'depo_id')->widget(Select2::classname(), [
                                    'data' => $listDataApotek,
                                    'options' => [
                                        'id' => 'select_depo_bmhp',
                                        'class' => 'form-control input-sm select2',
                                        'placeholder' => '-- Pilih Depo --',
                                        //MHBG Perawat bisa order hanya alkes saja
                                        //'disabled' => isset($modelInstruksiBmhp->perawat1_id)?true:false,
                                    ],
                                ])->label(Yii::t('fe', 'Depo Tujuan')) ?>
                            </div>
                        <div class="col-md-6">
                            <?=$form->field($modelInstruksiBmhp, 'harga_jumlah',[
                                'addon' => [
                                    'prepend' => [
                                        'content' => Html::checkbox('tagih_pasien', false, [
                                                'id' => 'tagih_pasien', 
                                                'label' => Yii::t('fe', 'Tagihkan')
                                            ])
                                        ]
                                    ]
                            ])->textInput([
                                    'class' => 'form-control input-sm jml_tarif doco-number', 
                                    'readonly' => 'readonly', 
                                ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?=$form->field($modelInstruksiBmhp, 'daftartindakan_id')
                                ->dropDownList([], [
                                    'class' => 'form-control input-sm select-2', 
                                    'id' => 'tindakan_obatalkes',
                                    'prompt' => Yii::t('fe', '-- Pilih nama tindakan--')
                                ]); ?>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="control-label col-sm-4"><?=Yii::t('fe', 'Stok Tersedia')?></label>
                                    <?= Html::textInput('obat_alkes_tersedia',0, [
                                        'class' => 'form-control',
                                        'readonly' => true,
                                        'id' => 'obat_alkes_tersedia'
                                    ]); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?=$form->field($modelInstruksiBmhp, 'obatalkes_id')
                                ->dropDownList([], [
                                    'class' => 'form-control input-sm select-2', 
                                    'id' => 'obatalkes_select2',
                                    'prompt' => Yii::t('fe', '-- Pilih obat/alkes --')
                                ]); ?>
                        </div>
                        <div class="col-md-6">
                            <?=$form->field($modelInstruksiBmhp, 'perawat1_id')->widget(Select2::className(),[
                                'data' => ArrayHelper::map($data_perawat, 'pegawai_id', 'nama_pegawai'),
                                'disabled' => isset($modelInstruksiBmhp->perawat1_id)?true:false,
                                'options' => [
                                    'placeholder' => Yii::t('fe', '-- Pilih perawat--'),
                                    'id' => 'perawat3_id'
                                ],
                            ]);
                                ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($modelInstruksiBmhp, 'qty')->textInput([
                                    'class' => 'form-control input-sm docoNumberOnly jml', 
                            ])->label(Yii::t('fe', 'Jumlah')) ?>
                        </div>
                        <div class="col-md-6">
                            <?=$form->field($modelInstruksiBmhp, 'perawat2_id')
                                ->dropDownList(ArrayHelper::map($data_perawat, 'pegawai_id', 'nama_pegawai'), [
                                    'class' => 'form-control input-sm select-2', 
                                    'id' => 'perawat4_id',
                                    'prompt' => Yii::t('fe', '-- Pilih perawat--')
                                ]); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="pull-right">
                            <?=Html::button('<i class="fa fa-plus"></i> '.Yii::t('fe', 'Tambah'), ['class' => 'btn btn-success btn-sm', 'id' => 'tambah-obatalkes']); ?>
                        </div>
                    </div>
                    <?=Html::activeHiddenInput($modelInstruksiBmhp, 'obatalkes_nama'); ?>
                    <?=Html::activeHiddenInput($modelInstruksiBmhp, 'hargasatuan_oa', ['class' => 'harga_digunakan', 'value' => isset($konfig['hargaygdigunakan']) ? $konfig['hargaygdigunakan'] : '']); ?>
                    <?=Html::activeHiddenInput($modelInstruksiBmhp, 'harga_netto'); ?>
                    <div style="display:none;" id="count_riwayat" data-id="<?= @$count ?>"></div>
                    <div class="row">
                        <div class="col-md-12">
                            <hr>
                        </div>
                    </div>
                    <div class="row">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover table-framed tabel-bmhp">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th><?=Yii::t('fe', 'No')?></th>
                                        <th><?=Yii::t('fe', 'Tanggal tindakan')?></th>
                                        <th><?=Yii::t('fe', 'Nama tindakan')?></th>
                                        <th><?=Yii::t('fe', 'Obat/alkes')?></th>
                                        <th><?=Yii::t('fe', 'Nama perawat 1')?></th>
                                        <th><?=Yii::t('fe', 'Nama perawat 2')?></th>
                                        <th><?=Yii::t('fe', 'Jumlah Tindakan')?></th>
                                        <th><?=Yii::t('fe', 'Ditagihkan')?></th>
                                        <th><?=Yii::t('fe', 'Implementasi')?></th>
                                        <th><?=Yii::t('fe', 'Hapus')?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        $total = 0; 
                                        $countBmhp = 0;
                                        $no = 1;
                                    ?>
                                    <?php if (!empty($data_tindakanbmhp)):
                                        foreach ($data_tindakanbmhp as $key => $value):
                                            if ($value['tipe'] == 'BMHP'): 
                                                if($value['tindakan_deleted'] == true){
                                                    continue;
                                                }
                                            ?>
                                                <?php
                                                    $idgenerated_bmhp = isset($value['bmhp_instruksitindakan_id']) && $value['bmhp_instruksitindakan_id'] != '' ? $value['bmhp_instruksitindakan_id'] : 0;
                                                ?>
                                                <tr class='is_tbl_bmhp' data-count='<?=$countBmhp?>' data-daftartindakan_id='<?= $idgenerated_bmhp ?>' data-id_instruksi_tindakan='<?=$idgenerated_bmhp?>'>
                                                    <td class='td-no-bmhp'><?= $no ?></td>
                                                    <td><?= date('j F Y h:i:s', strtotime($value['tgl_tindakan'])) ?></td>
                                                    <td class="namatindakan"><?= $value['bmhp_namainstruksi'] ?></td>
                                                    <td class='daftartindakan-nama'><?= $value['tindakan_paket_obat'] != '' ? $value['tindakan_paket_obat'] : $value['tindakan'] ?></td>
                                                    <td><?= $value['perawat_1'] != '' ? $value['perawat_1'] : '-' ?></td>
                                                    <td><?= $value['perawat_2'] != '' ? $value['perawat_2'] : '-' ?></td>
                                                    <td class="qty"><?= $value['qty'] ?></td>
                                                    <td><?= $value['ditagihkan'] != null ? '✓' : '-' ?></td>
                                                    <td><?= @$value['nama_status_implementasi'] ?></td>

                                                    <?php
                                                        if($value['status_implementasi'] == '454'){
                                                    ?>

                                                        <?php if($isUbah == "1"){ ?>
                                                        <td class="td-ganti-bmhp">
                                                            <button type='button' class='btn btn-danger btn-ganti-bmhp'><i class='fa fa-remove'></i></button>
                                                        </td>
                                                        <?php }else{?>
                                                        <td class='td-hapus'><button type='button' class='btn btn-danger btn-remove-bmhp'><i class='fa fa-remove'></i></button></td>
                                                        <?php }?>


                                                    <?php } else{?>
                                                        <td>&nbsp;</td>
                                                    <?php }?>

                                                    <?= Html::hiddenInput('InstruksiTindakanBmhpForm['.$countBmhp.'][tgl_pelayanan]', $value['tgl_tindakan'], ['readonly' => 'readonly']) ?>
                                                    <?= Html::hiddenInput('InstruksiTindakanBmhpForm['.$countBmhp.'][daftartindakan_id]',  @$value['daftartindakan_id'], ['readonly' => 'readonly']) ?>
                                                    <?= Html::hiddenInput('InstruksiTindakanBmhpForm['.$countBmhp.'][obatalkes_id]', $value['tindakan_paket_obat_id'], ['class' => 'obatalkes_id', 'readonly' => 'readonly']) ?>
                                                    <?= Html::hiddenInput('InstruksiTindakanBmhpForm['.$countBmhp.'][dokterdelegasi_id]', $value['dokterdelegasi_id'], ['readonly' => 'readonly']) ?>
                                                    <?= Html::hiddenInput('InstruksiTindakanBmhpForm['.$countBmhp.'][perawat1_id]', $value['perawat1_id'], ['class'=>'perawat1_id','readonly' => 'readonly']) ?>
                                                    <?= Html::hiddenInput('InstruksiTindakanBmhpForm['.$countBmhp.'][perawat2_id]', $value['perawat2_id'], ['class'=>'perawat2_id','readonly' => 'readonly']) ?>
                                                    <?= Html::hiddenInput('InstruksiTindakanBmhpForm['.$countBmhp.'][qty]', $value['qty'], ['class' => 'qty', 'readonly' => 'readonly']) ?>
                                                    <?= Html::hiddenInput('InstruksiTindakanBmhpForm['.$countBmhp.'][hargajual_oa]', $value['ditagihkan'] != null ? $value['ditagihkan'] : 0, ['class' => 'tarif-bmhp', 'readonly' => 'readonly']) ?>
                                                    <?= Html::hiddenInput('InstruksiTindakanBmhpForm['.$countBmhp.'][pasien_id]', $value['pasien_id'], ['readonly' => 'readonly']) ?>
                                                    <?= Html::hiddenInput('InstruksiTindakanBmhpForm['.$countBmhp.'][carabayar_id]', $value['carabayar_id'], ['readonly' => 'readonly']) ?>
                                                    <?= Html::hiddenInput('InstruksiTindakanBmhpForm['.$countBmhp.'][pendaftaran_id]', $value['pendaftaran_id'], ['readonly' => 'readonly']) ?>
                                                    <?= Html::hiddenInput('InstruksiTindakanBmhpForm['.$countBmhp.'][penjamin_id]', $value['penjamin_id'], ['readonly' => 'readonly']) ?>
                                                    <?= Html::hiddenInput('InstruksiTindakanBmhpForm['.$countBmhp.'][ruangan_id]', $value['ruangan_rawat_id'], ['readonly' => 'readonly']) ?>
                                                    <?= Html::hiddenInput('InstruksiTindakanBmhpForm['.$countBmhp.'][dokter_id]', $value['dokterdpjp_id'], ['readonly' => 'readonly']) ?>
                                                    <?= Html::hiddenInput('InstruksiTindakanBmhpForm['.$countBmhp.'][is_ditagihkan]', @$value['ditagihkan'], ['class'=>'is_ditagihkan','readonly' => 'readonly']) ?>
                                                    <?= Html::hiddenInput('InstruksiTindakanBmhpForm['.$countBmhp.'][id_instruksi_tindakan]', @$idgenerated_bmhp, ['class'=>'id_instruksi_tindakan','readonly' => 'readonly']) ?>

                                                    <?php
                                                    if($isUbah == "1"){
                                                        echo Html::hiddenInput('InstruksiTindakanBmhpForm['.$countBmhp.'][instruksitindakanbmhp_id]', @$value['instruksitindakan_id'], ['readonly' => 'readonly']); 
                                                    }
                                                    ?>
                                                </tr>
                                                <?php $countBmhp = $countBmhp + 1; 
                                                $no = $no + 1;
                                                $total = $total + @$value['jumlah_tarif'];
                                            endif;
                                        endforeach;
                                    else :
                                ?>
                                    <tr class="first-class">
                                        <th colspan="10" class="text-center">Data Kosong</th>
                                    </tr>
                                <?php
                                    endif; ?>
                                <?= Html::hiddenInput('count_bmhp', $countBmhp, ['id' => 'count-bmhp']) ?>
                                <?= Html::hiddenInput('daftartindakan_id', '', ['id' => 'daftartindakan-id']) ?>
                                <?= Html::hiddenInput('pegawai_id', '', ['id' => 'pegawai-id']) ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    <?php ActiveForm::end(); ?>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="pull-left">
                <button type="button" id="save-terapi-tindakan" class="btn bg-teal"><i class="fa fa-floppy-o"></i> Simpan</button>
                <button type="button" id="reset-terapi-tindakan" class="btn bg-teal"><i class="fa fa-refresh"></i> Muat Ulang</button>
            </div>
        </div>
    </div>
</div>

<?php // File
$this->registerJs('
var ruanganNama = "'.$ruangan_nama.'";
// Global var
var ruangan = "'.$model['ruangan_id'].'";
var kelaspelayananId = "'. $data_pasien['kelaspelayanan_id'] .'";
', View::POS_END);
$this->registerJs($this->render('js/terapi_tindakan.js'), View::POS_END);
?>

