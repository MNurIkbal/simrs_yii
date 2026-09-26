<?php
//Author: Ardi Pratama

// Using
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\datetime\DateTimePicker;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
?>
<div class="row body">
    <div class="col-md-12">
        <div id="div-cppt">
            <div class="panel panel-white">
                <div class="panel-toolbar clearfix">
                    <?=DocoHelpers::generateToolbar([
                        'kembali-implementasi' => [
                            'type' => 'button',
                            'title' => Yii::t('fe', 'Kembali'),
                            'icon' => 'fa fa-arrow-left',
                            'attributes' => [
                                'id' => 'btn-kembali-transaksi-implementasi',
                                'data-options' => 'click',
                            ],
                        ],
                        'implementasi' => [
                            'type' => 'button',
                            'title' => Yii::t('fe', 'Simpan'),
                            'icon' => 'fa fa-floppy-o',
                            'attributes' => [
                                'id' => 'btn-simpan-transaksi-implementasi',
                                'data-options' => 'click',
                            ],
                        ]
                    ], '#tb-implementasi');?>
                </div>
                <div class="panel-body">
                    <?php 
                        $form = ActiveForm::begin([
                            'id' => 'form-implementasi',
                            'type' => ActiveForm::TYPE_HORIZONTAL,
                            'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
                        ]); 
                    ?>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="col-lg-6">
                            
                            <?= $form->field($modelImplementasi, 'tgl_implementasi')->textInput([
                                'class' => 'form-control input-sm date',
                                'readonly' => 'readonly'
                            ]) ?>
                            </div>
                            <div class="col-lg-6">
                            <?=$form->field($modelImplementasi, 'catatan_implementasi')
                                    ->textArea() ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="col-lg-6">
                            <?=$form->field($modelImplementasi, 'catatan')
                                    ->textArea([
                                        'class' => 'form-control',
                                        'readonly'=> true , 
                                        ]); ?>
                            </div>
                        </div>
                    </div>
                        <?=$form->field($modelImplementasi, 'instruksi_id')
                                    ->hiddenInput()->label(false); ?>
                    <?php ActiveForm::end(); ?>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-white">
                                <div class="panel-heading">
                                    <h6 class="panel-title">
                                    <?=Yii::t("fe", "Tindakan Medis")?></h6>
                                    <div class="heading-elements">
                                        <ul class="icons-list">
                                            <li><a data-action="collapse"></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <?php 
                                        $form = ActiveForm::begin([
                                            'id' => 'form-implementasi-tindakan',
                                            'type' => ActiveForm::TYPE_HORIZONTAL,
                                            'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
                                        ]); 
                                    ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="tb-implementasi-tindakan-medis" style="width:100%">
                                            <thead>
                                                <tr class="bg-inverse">
                                                    <th>No</th>
                                                    <th><?= Yii::t('fe', 'Tanggal Implementasi') ?></th>
                                                    <th><?= Yii::t('fe', 'Nama Tindakan/Paket') ?></th>
                                                    <th><?= Yii::t('fe', 'Dokter Pemeriksa') ?></th>
                                                    <th><?= Yii::t('fe', 'Perawat 1')?></th>
                                                    <th><?= Yii::t('fe', 'Perawat 2')?></th>
                                                    <th><?= Yii::t('fe', 'Jumlah Instruksi')?></th>
                                                    <th><?= Yii::t('fe', 'Cyto')?></th>
                                                    <th><?= Yii::t('fe', 'Belum Implementasi')?></th>
                                                    <th><?= Yii::t('fe', 'Jumlah Implementasi')?></th>
                                                    <th><?= Yii::t('fe', 'Tarif Satuan (Rp.)')?></th>
                                                    <th><?= Yii::t('fe', 'Tarif Cyto (Rp.)')?></th>
                                                    <th><?= Yii::t('fe', 'Jumlah Tarif (Rp.)')?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                        <?php
                                                        $countRowTindakan = 0;
                                                    foreach ($datasetTindakan as $row) { 
                                                        if(isset($row['tindakan_deleted']) && $row['tindakan_deleted'] == true){
                                                            continue;
                                                        } ?>
                                                        <tr>
                                                            <td><?=$countRowTindakan+1?></td>
                                                            <td class="tgl_implementasi"></td>
                                                            <?php if($row['tipe'] == "PAKET"){?>
                                                            <td>
                                                                <?=@$row['tindakan_paket_obat']?>
                                                                <ul>
                                                                    <?php 
                                                                    if(isset($row['paketDetail'])){
                                                                        foreach ($row['paketDetail'] as $valPaketDetail) {
                                                                    ?>
                                                                    <li><?=@$valPaketDetail['daftartindakan_nama']?></li>
                                                                    <?php 
                                                                        }
                                                                    }
                                                                    ?>
                                                                </ul>
                                                            </td>
                                                            <?php }else{?>
                                                            <td><?=@$row['tindakan_paket_obat']?></td>
                                                            <?php }?>
                                                            <td><?=@$row['dokter_periksa']?></td>
                                                            <td class="perawat1_id">
                                                                <?= Html::dropDownList('ImplementasiTindakanForm['.$countRowTindakan.'][perawat1_id]', isset($row['perawat1_id'])?$row['perawat1_id']:'', ArrayHelper::map($data_perawat, 'pegawai_id', 'nama_pegawai'),['class'=>'select2','prompt'=> '--Pilih Perawat--']) ?>
                                                                    
                                                                </td>
                                                            <td class="perawat2_id">
                                                                <?= Html::dropDownList('ImplementasiTindakanForm['.$countRowTindakan.'][perawat2_id]', isset($row['perawat2_id'])?$row['perawat2_id']:'', ArrayHelper::map($data_perawat, 'pegawai_id', 'nama_pegawai'),['class'=>'select2','prompt'=> '--Pilih Perawat--']) ?>
                                                                    
                                                                </td>
                                                            <td><?=@$row['qty']?></td>
                                                            <?php
                                                                $rowCyto = '&#10006;';
                                                                $dataCyto = 0;
                                                                if(isset($row['is_cyto']) && $row['is_cyto'] == true){
                                                                    $dataCyto = 1;
                                                                    $rowCyto = '&#10004;';
                                                                }
                                                            ?>
                                                            <td class="is_cyto" data-cyto="<?=$dataCyto?>"><?=@$rowCyto?></td>
                                                            <td class="qty_belum"><?=@$row['qty_sisa']?></td>
                                                            <td class="jml_diimplementasi">
                                                                <?= Html::input('text', 'ImplementasiTindakanForm['.$countRowTindakan.'][jml_diimplementasi]', '', 
                                                                ['class' => 'form-control jml_implemen docoNumberOnly','data-counter'=>$countRowTindakan]) ?>
                                                            </td>
                                                            <?php
                                                                $tarifsatuan_tindakan = 0;
                                                                if(isset($row['tarif_satuan']) && $row['tarif_satuan'] != 0){
                                                                    $tarifsatuan_tindakan = $row['tarif_satuan'];
                                                                }
                                                            ?>
                                                            <td class="tarif_satuan" data-tarif="<?=@$tarifsatuan_tindakan?>"><?= DocoHelpers::formatNumber($tarifsatuan_tindakan) ?></td>
                                                            <?php
                                                                $tarifCyto = 0;;
                                                                if(isset($row['tarif_cyto']) && $row['tarif_cyto'] != 0){
                                                                    $tarifCyto = $row['tarif_cyto'];
                                                                }
                                                            ?>
                                                            <td class="tarif_cyto" data-tarif="<?=@$tarifCyto?>"><?= DocoHelpers::formatNumber($tarifCyto)?></td>
                                                            <td class="subtotalTindakan">0</td>
                                                            <?= Html::hiddenInput('ImplementasiTindakanForm['.$countRowTindakan.'][tarif_satuan]', @$row['tarif_satuan'], ['class'=>'tarif_satuan','readonly' => 'readonly']) ?>
                                                            <?= Html::hiddenInput('ImplementasiTindakanForm['.$countRowTindakan.'][tarif_cyto]', @$row['tarif_cyto'], ['class'=>'tarif_cyto','readonly' => 'readonly']) ?>
                                                            <?= Html::hiddenInput('ImplementasiTindakanForm['.$countRowTindakan.'][tgl_implementasi]', @$row['tgl_implementasi'], ['class'=>'tgl_implementasi','readonly' => 'readonly']) ?>
                                                            <?= Html::hiddenInput('ImplementasiTindakanForm['.$countRowTindakan.'][instruksitindakan_id]', @$row['instruksitindakan_id'], ['class'=>'instruksitindakan_id','readonly' => 'readonly']) ?>
                                                        </tr>
                                                        <?php
                                                        $countRowTindakan++;
                                                    } ?>
                                            </tbody>
                                            <tfoot>
                                                <td colspan="12">Total (Rp.)</td>
                                                <td id="totalTindakan">0</td>
                                            </tfoot>
                                        </table>
                                    </div>
                                
                                    <?php ActiveForm::end(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-white">
                                <div class="panel-heading">
                                    <h6 class="panel-title">
                                    <?=Yii::t("fe", "Pemakaian BMHP/Alkes")?></h6>
                                    <div class="heading-elements">
                                        <ul class="icons-list">
                                            <li><a data-action="collapse"></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <?php 
                                        $form = ActiveForm::begin([
                                            'id' => 'form-implementasi-bmhp',
                                            'type' => ActiveForm::TYPE_HORIZONTAL,
                                            'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
                                        ]); 
                                    ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered datatable-basic dataTable" id="tb-implementasi-pemakaian-bmhp" style="width:100%">
                                            <thead>
                                                <tr class="bg-inverse">
                                                    <th>No</th>
                                                    <th><?= Yii::t('fe', 'Tanggal Implementasi') ?></th>
                                                    <th><?= Yii::t('fe', 'Nama Tindakan') ?></th>
                                                    <th><?= Yii::t('fe', 'Obat/Alkes') ?></th>
                                                    <th><?= Yii::t('fe', 'Perawat 1')?></th>
                                                    <th><?= Yii::t('fe', 'Perawat 2')?></th>
                                                    <th><?= Yii::t('fe', 'Ditagihkan')?></th>
                                                    <th><?= Yii::t('fe', 'Jumlah Instruksi')?></th>
                                                    <th><?= Yii::t('fe', 'Belum Implementasi')?></th>
                                                    <th><?= Yii::t('fe', 'Implementasi')?></th>
                                                    <th><?= Yii::t('fe', 'Tarif Satuan (Rp.)')?></th>
                                                    <th><?= Yii::t('fe', 'Jumlah Tarif (Rp.)')?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                        <?php
                                                        $countRowBmhp = 0;
                                                    foreach ($datasetBmhp as $row) {
                                                        if(isset($row['tindakan_deleted']) && $row['tindakan_deleted'] == true){
                                                            continue;
                                                        } 
                                                        ?>
                                                        <tr>
                                                            <td><?=$countRowBmhp+1?></td>
                                                            <td class="tgl_implementasi"></td>
                                                            <td><?=@$row['bmhp_namainstruksi']?></td>
                                                            <td><?=@$row['tindakan_paket_obat']?></td>
                                                            <td class="perawat3_id">
                                                                <?= Html::dropDownList('ImplementasiBmhpForm['.$countRowBmhp.'][perawat1_id]', isset($row['perawat1_id'])?$row['perawat1_id']:'', ArrayHelper::map($data_perawat, 'pegawai_id', 'nama_pegawai'),['class'=>'select2','prompt'=> '--Pilih Perawat--']) ?>
                                                                    
                                                                </td>
                                                            <td class="perawat4_id">
                                                                <?= Html::dropDownList('ImplementasiBmhpForm['.$countRowBmhp.'][perawat2_id]', isset($row['perawat2_id'])?$row['perawat2_id']:'', ArrayHelper::map($data_perawat, 'pegawai_id', 'nama_pegawai'),['class'=>'select2','prompt'=> '--Pilih Perawat--']) ?>
                                                                    
                                                                </td>
                                                            <?php
                                                                $rowDitagihkan = '&#10006;';
                                                                if(isset($row['ditagihkan']) && $row['ditagihkan'] == true){
                                                                    $rowDitagihkan = '&#10004;';
                                                                }
                                                            ?>
                                                            <td><?=@$rowDitagihkan?></td>
                                                            <td><?=@$row['qty']?></td>
                                                            <td class="qty_belum_bmhp"><?=@$row['qty_sisa']?></td>
                                                            <td class="jml_diimplementasi">
                                                                <?= Html::input('text', 'ImplementasiBmhpForm['.$countRowBmhp.'][jml_diimplementasi]', '', 
                                                                ['class' => 'form-control jml_implemen_bmhp docoNumberOnly','data-counter'=>$countRowBmhp]) ?>
                                                            </td>
                                                            <?php
                                                                $tarifsatuan_bmhp = 0;
                                                                if(isset($row['tarif_satuan']) && $row['tarif_satuan'] != 0){
                                                                    $tarifsatuan_bmhp = $row['tarif_satuan'];
                                                                }
                                                            ?>
                                                            <td class="tarif_satuan" data-tarif="<?=$tarifsatuan_bmhp?>"><?=DocoHelpers::formatNumber($row['tarif_satuan'])?></td>
                                                            <td class="subtotalBmhp">0</td>
                                                            <?= Html::hiddenInput('ImplementasiBmhpForm['.$countRowBmhp.'][tgl_implementasi]', @$row['tgl_implementasi'], ['class'=>'tgl_implementasi','readonly' => 'readonly']) ?>
                                                            <?= Html::hiddenInput('ImplementasiBmhpForm['.$countRowBmhp.'][instruksitindakanbmhp_id]', @$row['instruksitindakan_id'], ['class'=>'instruksitindakan_id','readonly' => 'readonly']) ?>
                                                            <?= Html::hiddenInput('ImplementasiBmhpForm['.$countRowBmhp.'][obatalkes_id]', @$row['tindakan_paket_obat_id'], ['class'=>'obatalkes_id','readonly' => 'readonly']) ?>
                                                            <?= Html::hiddenInput('ImplementasiBmhpForm['.$countRowBmhp.'][is_ditagihkan]', @$row['ditagihkan'], ['class'=>'is_ditagihkan','readonly' => 'readonly']) ?>
                                                        </tr>
                                                        <?php
                                                        $countRowBmhp++;
                                                    } ?>
                                                
                                            </tbody>
                                            <tfoot>
                                                <td colspan="11">Total (Rp.)</td>
                                                <td id="totalBmhp">0</td>
                                            </tfoot>
                                        </table>
                                    </div>
                                
                                    <?php ActiveForm::end(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
    $form = ActiveForm::begin([
        'id' => 'form-implementasi-additional',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
    ]); 
    echo Html::hiddenInput('ruangan_id', @$dataAdditional['ruangan_id'], ['readonly' => 'readonly']);
    echo Html::hiddenInput('ImplementasiAdditionalForm[pendaftaran_id]', @$dataAdditional['pendaftaran_id'], ['readonly' => 'readonly']);
    echo Html::hiddenInput('ImplementasiAdditionalForm[kelaspelayanan_id]', @$dataAdditional['kelaspelayanan_id'], ['readonly' => 'readonly']);
    echo Html::hiddenInput('ImplementasiAdditionalForm[jeniskasuspenyakit_id]', @$dataAdditional['jeniskasuspenyakit_id'], ['readonly' => 'readonly']);
    echo Html::hiddenInput('ImplementasiAdditionalForm[pasien_id]', @$dataAdditional['pasien_id'], ['readonly' => 'readonly']);
    echo Html::hiddenInput('ImplementasiAdditionalForm[carabayar_id]', @$dataAdditional['carabayar_id'], ['readonly' => 'readonly']); 
    echo Html::hiddenInput('ImplementasiAdditionalForm[penjamin_id]', @$dataAdditional['penjamin_id'], ['readonly' => 'readonly']); 
    echo Html::hiddenInput('ImplementasiAdditionalForm[pasienadmisi_id]', @$dataAdditional['pasienadmisi_id'], ['readonly' => 'readonly']); 
    echo Html::hiddenInput('ImplementasiAdditionalForm[instalasi_id]', @$instalasi_id, ['readonly' => 'readonly']); 
    echo Html::hiddenInput('ImplementasiAdditionalForm[ruangan_id]', @$ruangan_id, ['readonly' => 'readonly']); 

    ActiveForm::end(); ?>
<?php

// Script
$this->registerJs('
    // Global vars
    var cppt_id = "'.$decryptedCppt_id.'";
    var instruksi_id = "'.$decryptedInstruksi_id.'";
    var tanggalPukul = "'.(\Yii::t("fe", "Tanggal/Pukul")).'";
    var instruksiDokter = "'.(\Yii::t("fe", "Instruksi Dokter")).'";
    var dokter = "'.(\Yii::t("fe", "Dokter")).'";
    var status = "'.(\Yii::t("fe", "Status")).'";
    var implementasi = "'.(\Yii::t("fe", "Implementasi")).'";
    var petugas1 = "'.(\Yii::t("fe", "Petugas 1")).'";
    var petugas2 = "'.(\Yii::t("fe", "Petugas 2")).'";

    // Datatable language
    var emptyTable = "'.(\Yii::t("fe", "Tidak ada data yang tersedia")).'";
    var info = "'.(\Yii::t("fe", "Menampilkan _START_ sampai _END_ dari _TOTAL_ data")).'";
    var infoEmpty = "'.(\Yii::t("fe", "Menampilkan 0 sampai 0 dari 0 data")).'";
    var infoFiltered = "'.(\Yii::t("fe", "(disaring dari _MAX_ total data)")).'";
    var lengthMenu = "'.(\Yii::t("fe", "Menampilkan _MENU_ data")).'";
    var loadingRecords = "'.(\Yii::t("fe", "Memuat...")).'";
    var processing = "'.(\Yii::t("fe", "Memproses...")).'";
    var search = "'.(\Yii::t("fe", "Cari:")).'";
    var zeroRecords = "'.(\Yii::t("fe", "Tidak ada data yang ditemukan")).'";
    var sortAscending = "'.(\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terkecil ke yang terbesar")).'";
    var sortDescending = "'.(\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terbesar ke yang terkecil")).'";
', View::POS_END, 'implementasi-transaksi');
$this->registerJs($this->render('js/transaksi.js'), View::POS_END);
?>