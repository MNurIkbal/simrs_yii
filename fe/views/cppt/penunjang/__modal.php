<?php

use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\Pelayanan\PelayananHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\web\View;
use yii\web\JsExpression;
use yii\helpers\Html;
?>
<style>
  .field-diagnosa_penyerta .bootstrap-tagsinput{
      background-color: #fafafa;
      overflow: auto;
      /* set manual background disabled */
  }
  #diagnosa_utama_text, #instruksipenunjangform-catatan_dokterpengirim{
      max-width:: 100%;
      max-height: 100%;
      resize: vertical;
  }
</style>

<?=Html::hiddenInput('tmp_ruangan_id', null, [
    'id' => 'tmp-ruangan-id'
])?>
<?php $form = ActiveForm::begin([
    'id' => 'order-penunjang-form',
    // 'type' => ActiveForm::TYPE_HORIZONTAL,
    'action' => $url['form-action'],
    'enableClientValidation' => false,
    'formConfig' => ['deviceSize' => ActiveForm::SIZE_SMALL]
]) ?>
<?=Html::hiddenInput('penjamin_id', $penjaminId, [
    'id' => 'instruksipenunjang-penjamin_id'
])?>
<?=Html::hiddenInput('kelaspelayanan_id', $kelaspelayananId, [
    'id' => 'instruksipenunjang-kelaspelayanan_id'
])?>
<?=Html::activeHiddenInput($model, 'pendaftaran_id')?>
<?=Html::activeHiddenInput($model, 'instalasi_id', [
    'id' => 'instruksipenunjang-instalasi_id'
])?>
<?=Html::activeHiddenInput($model, 'pasienadmisi_id')?>
<?php if($model->instalasi_id == DocoConstants::INSTALASI_ID_REHAB){
    echo Html::activeHiddenInput($model, 'pegawai_id');
}?>
<?=Html::activeHiddenInput($model, 'cppt_id')?>
<?=Html::activeHiddenInput($model, 'ruangan')?>
<div class="modal-header">
    <button type="button" class="close close-modal-jadwal" data-dismiss-confirmation="modal">&times;</button>
    <?php
        if($model->instalasi_id == DocoConstants::INSTALASI_ID_BEDAH){
    ?>
        <h5 class="modal-title">Form Penjadwalan Prosedur / Operasi - <?= $infoPasien['nama_pasien']. ' / '. $infoPasien['penjamin_nama'] . ' / ' . $infoPasien['kelaspelayanan_nama'] ?></h5>
    <?php
        }
        else{
    ?>
        <h5 class="modal-title">Order Pemeriksaan <?= ucwords($type); ?> - <?= $infoPasien['nama_pasien']. ' / '. $infoPasien['penjamin_nama'] . ' / ' . $infoPasien['kelaspelayanan_nama'] ?></h5>
    <?php
        }
    ?>
</div>
<div class="modal-body form-modal-<?=$type?>">
<?php
    if( $model->instalasi_id != DocoConstants::INSTALASI_FISIOTERAPI ){
        ?>
    <div class="row">
        <div class="col-sm-1">
            <div class="form-group">
                <label class="control-label">Jenis</label>
                <?php
                    if($model->instalasi_id == DocoConstants::INSTALASI_ID_BEDAH){
                ?>
                    <p class="label-value-form">Prosedur / Operasi</p>
                <?php
                    }
                    else{
                ?>
                    <p class="label-value-form">Penunjang</p>
                <?php
                    }
                ?>
            </div>
        </div>
        <div class="col-sm-3">
            <?php
                if($model->instalasi_id == DocoConstants::INSTALASI_ID_BEDAH){
                    echo $form->field($model, 'catatan_dokterpengirim')->textarea([
                        'class' => 'form-control input-sm',
                        'rows' => '3'
                    ])->label("Kondisi Klinis / Diagnosis");
                } else if($model->instalasi_id != DocoConstants::INSTALASI_FISIOTERAPI) {
                    echo $form->field($model, 'catatan_dokterpengirim')->textarea([
                        'class' => 'form-control input-sm',
                        'rows' => '3',
                        'placeholder' =>  'Catatan klinis / pemeriksaan yang belum tersedia.'
                    ]);
                }
            ?>
            <div class="form-group <?=$inst_id == DocoConstants::INSTALASI_ID_RJ ? 'hidden' : ''?> <?=$inst_id == DocoConstants::INSTALASI_ID_RD ? 'hidden' : ''?>">
                <label>
                <?=Html::activeCheckbox($model, 'is_puasa')?>
                </label>
            </div>
            <?php
                if($model->instalasi_id != DocoConstants::INSTALASI_ID_BEDAH){
            ?>
            <div class="form-group">
                <label>
                    <?=Html::activeCheckbox($model, 'is_rujukan')?>
                </label>
            </div>
            <?php
                }
            ?>
        </div>
        <?php
            if($model->instalasi_id == DocoConstants::INSTALASI_ID_BEDAH){
        ?>
            <div class="col-sm-2">
                <?= $form->field($model, 'pemakaian_implant')->textarea([
                    'class' => 'form-control input-sm',
                    'rows' => '3'
                ]) ?>
            </div>
            <div class="col-sm-2">
                <?= $form->field($model, 'sewa_vendor')->textarea([
                    'class' => 'form-control input-sm',
                    'rows' => '3'
                ]) ?>
            </div>
            <div class="col-sm-2">
                <?= $form->field($model, 'sewa_alat_rs')->textarea([
                    'class' => 'form-control input-sm',
                    'rows' => '3'
                ]) ?>
            </div>
            <div class="col-sm-2">
                <br>
                <div class="form-group">
                    <label>
                    <?=Html::activeCheckbox($model, 'jenis_operasi_cito')?>
                    </label>
                </div>
                <div class="form-group">
                    <label>
                    <?=Html::activeCheckbox($model, 'jenis_operasi_elektif')?>
                    </label>
                </div>
                <div class="form-group">
                    <label>
                    <?=Html::activeCheckbox($model, 'jenis_operasi_odc')?>
                    </label>
                </div>
            </div>
        <?php
            }
        ?>

        <?php
            if(in_array($model->instalasi_id, [DocoConstants::INSTALASI_ID_RAD, DocoConstants::INSTALASI_ID_LAB, DocoConstants::INSTALASI_ID_BEDAH])){
                echo '<div class="col-md-3 '.($model->instalasi_id == DocoConstants::INSTALASI_ID_BEDAH ? "col-md-offset-1" : "").'">';
                echo $form->field($model, 'diagnosa_utama_text')->textArea([
                        'class' => 'form-control',
                        'id' => 'diagnosa_utama_text',
                        'readonly' => true
                    ]);
                echo '</div>';

                echo '<div class="col-md-3">';
                echo $form->field($model, 'diagnosa_penyerta', [
                      'labelOptions' => [
                          // 'class' => 'text-right'
                      ]
                    ])->textArea([
                        'class' => 'form-control',
                        'id' => 'diagnosa_penyerta',
                        'disabled' => true,
                    ]);
                echo '</div>';
            }
        ?>

    </div>
    <div class="row">
        <div class="col-sm-12">
            <hr>
        </div>
    </div>
<?php } ?>
    <div class="row">
        <?php
            if( $model->instalasi_id == DocoConstants::INSTALASI_FISIOTERAPI ){
        ?>
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label">Tanggal Permintaan</label>
                <?=Html::activeTextInput($model, 'tgl_kirimpasien', [
                    'class' => 'form-control pickadate',
                    'readonly' => true,
                    'value' => date('d/m/Y')
                ])?>
            </div>
        </div>
        <div class="col-sm-5"><?php
            echo $form->field($model, 'diagnosis')->textarea([
                'class' => 'form-control input-sm',
                'rows' => '3'
            ])->label("Diagnosis");
            echo $form->field($model, 'ruangan_id')->hiddenInput([])->label(false);

        ?></div>
        <?php
            } else {
        ?>
        <div class="col-md-6">
            <?php
            if($model->instalasi_id == DocoConstants::INSTALASI_ID_BEDAH){
                echo "<br>";
            }
            ?>
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label">Spesialisasi</label>
                        <p class="label-value-form"><?=ucfirst($type)?></p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label">Dokter Perujuk</label>
                        <?= $form->field($model, 'pegawai_id')->dropDownList($dokterList, [
                            'data-api' => $dokter_url
                        ])->label(false) ?>
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label">Tanggal Permintaan</label>
                        <?=Html::activeTextInput($model, 'tgl_kirimpasien', [
                            'class' => 'form-control pickadate',
                            'readonly' => true,
                            'value' => date('d/m/Y')
                        ])?>
                    </div>
                </div>
                <div class="col-sm-6 select2-md">
                    <?php
                        if($model->instalasi_id == DocoConstants::INSTALASI_ID_BEDAH){
                            echo $form->field($model, 'ruangan_id')->dropDownList($wards, [
                                'prompt' => 'Pilih'
                            ])->label("Ruangan");
                        } else {
                            echo $form->field($model, 'ruangan_id')->dropDownList($wards, [
                                'prompt' => 'Pilih'
                            ]);
                        }
                    ?>
                </div>
            </div>
        </div>
        <div class="col-md-6 panel-jadwal-operasi <?=$model->instalasi_id != DocoConstants::INSTALASI_ID_BEDAH ? 'hidden' : ''?>">
            <div class="row">
                <div class="col-md-12">
                    <div class='penunjang-bedah'>
                        <div class="panel panel-default" style="margin-bottom: 0 !important">
                            <div class="panel-heading">
                                <h6 class="panel-title">
                                    <div class="row">
                                        <div class="col-md-6" style="margin-top: 5px">
                                            <b><?= Yii::t('fe', 'Jadwal Prosedur / Operasi'); ?></b>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <button disabled="true" type="button" class="btn btn-primary btn-xs btn-info btn-labeled btn-buka-jadwal" data-href="<?=$url['jadwal-operasi']?>" data-width="60%">
                                                <b><i class="fa fa-calendar"></i></b> Set Jadwal Operasi
                                            </button>
                                        </div>
                                    </div>
                                </h6>
                            </div>
                            <div class="panel-body">
                                <div class="row" style="margin-top: 5px">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label for="" class="col-sm-6"><?= Yii::t("fe", "Tanggal Rencana Prosedur / Operasi") ?></label>
                                            <div class="col-sm-6">
                                                <b><span id='tgl_permintaan_info'>-</span></b>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="" class="col-sm-6"><?= Yii::t("fe", "Jam Mulai (Estimasi)") ?></label>
                                            <div class="col-sm-6">
                                                <b><span id='jam_mulai_info'>-</span></b>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="" class="col-sm-6"><?= Yii::t("fe", "Jam Selesai (Estimasi)") ?></label>
                                            <div class="col-sm-6">
                                                <b><span id='jam_selesai_info'>-</span></b>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label for="" class="col-sm-5"><?= Yii::t("fe", "Dokter Operator") ?></label>
                                            <div class="col-sm-7">
                                                <b><span id='dr_operator_info'>: <?= $user['nama_pegawai'] ?></span></b>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="" class="col-sm-5"><?= Yii::t("fe", "Dokter Anestesi") ?></label>
                                            <div class="col-sm-7">
                                                <b><span id='dr_anestesi_info'>-</span></b>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?=Html::activeHiddenInput($model, 'has_jadwal', [
                        'id' => 'penunjang_has_jadwal'
                    ])?>
                </div>
            </div>
        </div>
        <?php
                }
        ?>
    </div>
    <div class="row">
        <div class="col-sm-12" style="margin-bottom: 5px">
            <hr>
            <?php
                if($model->instalasi_id == DocoConstants::INSTALASI_ID_BEDAH){
            ?>
                <p class="text-bold">Nama Prosedur / Operasi</p>
            <?php
                }
                else{
            ?>
                <p class="text-bold">Tabel Pemeriksaan unit penunjang</p>
            <?php
                }
            ?>
            <button type='button' disabled="" id="btn-tambah-pemeriksaan" style='margin-right: 5px' class='btn btn-labeled btn-info btn-xs' data-width="80%" data-href="<?=$url['modal-pemeriksaan']?>"><b><i class='fa fa-plus'></i></b> Tambah</button>
            <button type='button' id="btn-reset-pemeriksaan" style='margin-right: 5px' class='btn btn-labeled btn-danger btn-xs'><b><i class='fa fa-trash'></i></b> Kosongkan</button>
        </div>
        <div class="col-sm-12">
            <div class="table-responsive">
                <table class="table table-hover" id="tbl-order-penunjang">
                    <thead>
                        <tr class="bg-inverse">
                            <th>No</th>
                            <?php
                                if($model->instalasi_id == DocoConstants::INSTALASI_ID_BEDAH){
                            ?>
                                <th>Kategori Prosedur</th>
                                <th>Nama Prosedur / Tindakan</th>
                            <?php
                                }
                                else{
                            ?>
                                <th>Jenis Pemeriksaan</th>
                                <th>Nama Pemeriksaan</th>
                            <?php
                                }
                            ?>
                            <?php
                                if( $model->instalasi_id != DocoConstants::INSTALASI_ID_BEDAH ){
                                    ?>
                                        <th>Catatan</th>
                                    <?php
                                }
                            ?>
                            <th class="text-right" style="display: none">Tarif Satuan</th>

                            <?php
                                if( $model->instalasi_id == DocoConstants::INSTALASI_FISIOTERAPI ){
                                    ?>
                                        <th class="text-center" style="display: none">CITO</th>
                                    <?php
                                } else {
                                    ?>
                                        <th class="text-center">CITO</th>
                                    <?php
                                }
                            ?>
                            <th class="text-right" style="display: none">Tarif Satuan CITO</th>
                            <th class="text-right" style="display: none">Harga</th>
                            <th>&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="nd-row">
                            <?php
                                if($model->instalasi_id == DocoConstants::INSTALASI_ID_BEDAH){
                            ?>
                                <td colspan="<?= $model->instalasi_id != DocoConstants::INSTALASI_ID_BEDAH ? '7' : '6'?>" class="text-center no-data-row">Belum ada Prosedur / Tindakan yang dipilih</td>
                            <?php
                                }
                                else{
                            ?>
                                <td colspan="<?= $model->instalasi_id != DocoConstants::INSTALASI_ID_BEDAH ? '7' : '6'?>" class="text-center no-data-row">Belum Ada Data yang Diinputkan</td>
                            <?php
                                }
                            ?>
                        </tr>
                    </tbody>
                    <tfoot style="display: none">
                        <tr>
                            <td>&nbsp;</td>
                            <td class="text-bold">TOTAL</td>
                            <td class="text-right text-bold order-summary">Rp. 0</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
    <?php
    if( $model->instalasi_id == DocoConstants::INSTALASI_FISIOTERAPI ){
    ?>
    <hr>
    <br>
    <div class="row">
        <div class="col-md-3 ">
                <?php
                    echo $form->field($model, 'frekuensi_terapi', ['template' => '{label}<div class="input-group">{input}<span class="input-group-addon">kali</span></div>{error}', 'options' => ['class' => 'form-group required']])->textInput([
                            'class' => 'form-control doco-decimal',
                        ])->label("Frekwensi terapi");
                ?>
        </div>
        <div class="col-md-5">
    <?php
            echo $form->field($model, 'catatan_dokterpengirim')->textarea([
                'class' => 'form-control input-sm',
                'rows' => '3'
            ])->label("Catatan");
    ?>
        </div>

    </div>
    <?php
    }
    ?>

</div>
<div class="modal-footer text-right">
    <button type='button' style='margin-right: 25px' id="btn-save-penunjang" class='btn btn-labeled btn-info btn-xs'><b><i class='fa fa-save'></i></b> Simpan</button>
</div>

<?php ActiveForm::end() ?>

<?php
$instalasiBedah = DocoConstants::INSTALASI_ID_BEDAH;
$this->registerJs("
    var _jadwalOperasi = {}
    var _dokterPerujukId = '".$drperujukId."'
    var _dokterPerujukNama = '".$user['nama_pegawai']."'
    var _diagnosaUtama = ".json_encode($opt_diagnosa_utama).";
    var _diagnosaPenyerta = ".json_encode($opt_diagnosa_penyerta).";
    var _orderBedahTanpaTindakan = '$orderBedahTanpaTindakan'
    var _instalasiId = '$model->instalasi_id'
    var _instalasiBedah = '$instalasiBedah'
".$this->render('__modal.js'), View::POS_END);
?>
