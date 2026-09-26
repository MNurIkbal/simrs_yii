<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-07 15:18:06
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-08-16 18:13:28
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\DepDrop;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Bedah sentral', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Informasi pasien operasi', 'url' => ['informasi-pasien-operasi']];
$this->params['breadcrumbs'][] = $this->title;

?>
<style>
    .datepicker>div{
        display:block;
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
                                <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias", $this->title); ?></b></h3>
                                <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                        </div>
                </div>
                <!-- end -->
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    // 'search',
                    'save'=>[
                        'attributes'=>[
                            'form_id'=>'form-batal-operasi',
                            'data-confirm-message'=>'Apakah anda yakin akan membatalkan data ini?',
                        ]
                    ],
                    'back',
                    
                ]) ?>
            </div>
            <div class="panel-body">
               <!-- pannel detail pasien -->
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b><?= Yii::t('fe', 'Informasi Pasien'); ?></b></h6>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "No Rekam Medik") ?></b></label>
                                            <div class="col-sm-5">
                                                <p><b>:</b>&nbsp;<?= isset($data['no_rekam_medik']) ? $data['no_rekam_medik'] : '-' ?> </p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Tanggal Lahir") ?></b></label>
                                            <div class="col-sm-5">
                                                <p><b>:</b>&nbsp;<?= isset($data['tanggal_lahir']) ? date('d M Y', strtotime($data['tanggal_lahir'])) : '-' ?> </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Tanggal pendaftaran") ?></b></label>
                                            <div class="col-sm-5">
                                                <p><b>:</b>&nbsp;<?= isset($data['tgl_pendaftaran']) ? date('d M Y', strtotime($data['tgl_pendaftaran'])) : '-' ?>  </p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Umur") ?></b></label>
                                            <div class="col-sm-5">
                                                <p><b>:</b>&nbsp; <?= isset($data['umur']) ? $data['umur'] : '-' ?> </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "No Pendaftaran") ?></b></label>
                                            <div class="col-sm-5">
                                                <p><b>:</b>&nbsp;<?= isset($data['no_pendaftaran']) ? $data['no_pendaftaran'] : '-' ?>  </p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Dokter pemeriksa") ?></b></label>
                                            <div class="col-sm-5">
                                                <p><b>:</b>&nbsp; <?= isset($data['dokter_penunjang']) ? $data['dokter_penunjang'] : '-' ?> </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Nama pasien") ?></b></label>
                                            <div class="col-sm-5">
                                                <p><b>:</b>&nbsp;<?= isset($data['nama_pasien']) ? $data['nama_pasien'] : '-' ?>  </p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Kelas pelayanan") ?></b></label>
                                            <div class="col-sm-5">
                                                <p><b>:</b>&nbsp; <?= isset($data['kelaspelayanan_nama']) ? $data['kelaspelayanan_nama'] : '-' ?> </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Jenis kelamin") ?></b></label>
                                            <div class="col-sm-5">
                                                <p><b>:</b>&nbsp;<?= isset($data['j_kelamin']) ? $data['j_kelamin'] : '-' ?>  </p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Cara bayar") ?></b></label>
                                            <div class="col-sm-5">
                                                <p><b>:</b>&nbsp; <?= isset($data['carabayar_nama']) ? $data['carabayar_nama'] : '-' ?> </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Kasus penyakit") ?></b></label>
                                            <div class="col-sm-5">
                                                <p><b>:</b>&nbsp;<?= isset($data['jeniskasuspenyakit_nama']) ? $data['jeniskasuspenyakit_nama'] : '-' ?>  </p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Penjamin") ?></b></label>
                                            <div class="col-sm-5">
                                                <p><b>:</b>&nbsp; <?= isset($data['penjamin_nama']) ? $data['penjamin_nama'] : '-' ?> </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <?php 
                                    $filename = isset($data['photopasien']) ? !empty($data['photopasien']) ? '/media/img/pasien/'.$data['photopasien']: '/media/img/icon-app/default.jpg' : '/media/img/icon-app/default.jpg';
                                    ?>
                                    <?=Html::img($filename, ['style'=>'height: 150px;margin: 5px auto', 'class'=>'img-responsive'])?>
                                </div>
                            </div>
                            <div class="row">
                                <hr>
                                <div class="col-md-3">
                                    <label class="text-left control-label col-sm-6"><b><?= Yii::t("fe", "Tanggal Permintaan") ?></b></label>
                                    <div class="col-sm-6">
                                        <p><b>:</b>&nbsp;<?= isset($data['tgl_rujukan']) ? date('d M Y', strtotime($data['tgl_rujukan'])) : '-' ?> </p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="text-left control-label col-sm-6"><b><?= Yii::t("fe", "Dokter Perujuk") ?></b></label>
                                    <div class="col-sm-6">
                                        <p><b>:</b>&nbsp;<?= isset($data['dok_perujuk']) ? $data['dok_perujuk'] : '-' ?> </p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="text-left control-label col-sm-6"><b><?= Yii::t("fe", "Dokter Operator") ?></b></label>
                                    <div class="col-sm-6">
                                        <p><b>:</b>&nbsp;<?= isset($data['dok_operator']) ? $data['dok_operator'] : '-' ?> </p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="text-left control-label col-sm-6"><b><?= Yii::t("fe", "No Rujukan") ?></b></label>
                                    <div class="col-sm-6">
                                        <p><b>:</b>&nbsp;<?= isset($data['no_rujukan']) ? $data['no_rujukan'] : '-' ?> </p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="text-left control-label col-sm-6"><b><?= Yii::t("fe", "Jam Mulai") ?></b></label>
                                    <div class="col-sm-6">
                                        <p><b>:</b>&nbsp;<?= isset($data['jam_rencana_mulai']) ? $data['jam_rencana_mulai'] : '-' ?> </p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="text-left control-label col-sm-6"><b><?= Yii::t("fe", "Jam Selesai") ?></b></label>
                                    <div class="col-sm-6">
                                        <p><b>:</b>&nbsp;<?= isset($data['jam_rencana_selesai']) ? $data['jam_rencana_selesai'] : '-' ?> </p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="text-left control-label col-sm-6"><b><?= Yii::t("fe", "Dokter Anastesi") ?></b></label>
                                    <div class="col-sm-6">
                                        <p><b>:</b>&nbsp;<?= isset($data['dok_anastesi']) ? $data['dok_anastesi'] : '-' ?> </p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="text-left control-label col-sm-6"><b><?= Yii::t("fe", "Catatan") ?></b></label>
                                    <div class="col-sm-6">
                                        <p><b>:</b>&nbsp;<?= isset($data['catatan_dokterpengirim']) ? $data['catatan_dokterpengirim'] : '-' ?> </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
               <!-- pannel detail pasien -->

               <!-- pannel data pemeriksaan -->
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b><?= Yii::t('fe', 'Tabel Pemeriksaan Bedah Sentral').' - '.Yii::t('fe', 'Tarif pelayanan'); ?></b></h6>
                        </div>

                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12 filter-form"></div>
                            </div>
                            <!-- table -->
                            <table id="tb-rencana-pemeriksaan-rad" class="table table-striped table-hover" style="width:100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th><?= Yii::t('fe', 'No') ?></th>
                                        <th><?= Yii::t("fe", "Jenis Pemeriksaan") ?></th>
                                        <th><?= Yii::t("fe", "Pemeriksaan") ?></th>
                                        <th><?= Yii::t('fe', 'Cyto')?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if(count($detail)) :
                                        $no = 0;
                                        foreach($detail as $value):
                                            $no++;
                                            $jenispemeriksaan = !empty($value['daftartindakan_nama']) ? $value['daftartindakan_nama'] : !empty($value['tipepaket_nama']) ? $value['tipepaket_nama'] : '';
                                            ?>
                                            <tr>
                                                <td><?=$no?></td>
                                                <td><?=$value['daftartindakan_nama']?></td>
                                                <td><?=$value['kegiatanoperasi_nama']?></td>
                                                <td><?=($value['cyto_tindakan'] == true) ? Yii::t('fe', 'Ya') : Yii::t('fe', 'Tidak')?></td>
                                            </tr>
                                            <?php
                                        endforeach;
                                    else :
                                        ?>
                                        <tr>
                                            <td class="text-center" colspan="4"><?=Yii::t('fe', 'Data tidak tersedia')?></td>
                                        </tr>
                                        <?php
                                    endif;
                                    ?>
                                </tbody>
                            </table>
                            <!-- table -->
                        </div>
                    </div>
                </div>
               <!-- pannel data pemeriksaan -->

               <!-- panel form pembatalan -->
                
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b><?= Yii::t('fe', 'Form Pembatalan'); ?></b></h6>
                        </div>

                        <div class="panel-body">
                            <?php 
                                $form = ActiveForm::begin([
                                    'id' => 'form-batal-operasi', 
                                    'type' => ActiveForm::TYPE_HORIZONTAL,
                                    'formConfig' => ['labelSpan' => 5, 'deviceSize' => ActiveForm::SIZE_SMALL]
                                ]); 
                                ?>
                                <?=Html::activeHiddenInput($model, 'pasienmasukpenunjang_id', 
                                    [
                                        'value' => isset($data['pasienmasukpenunjang_id']) ? $data['pasienmasukpenunjang_id'] : '',
                                    ])?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-sm-4"><?=Yii::t('fe', 'Nomor pembatalan')?></label>
                                            <div class="col-sm-6">
                                                <?=Html::textInput('no_batal', '',['class'=>'form-control no-batal', 'readonly'=>'true'])?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-top: 10px">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label col-sm-4"><?=$model->attributeLabels()['tgl_batalperiksa']?></label>
                                            <div class="col-sm-6">
                                                <?=Html::activeTextInput($model, 'tgl_batalperiksa', ['class'=>'form-control', 'readonly'=>'true'])?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group required">
                                            <label class="control-label col-sm-3"><?=$model->attributeLabels()['peg_menyetujui_id']?></label>
                                            <div class="col-sm-7">
                                                <?=Html::activeDropdownlist($model, 'peg_menyetujui_id', [], ['class'=>'form-control select2 namakaryawan'])?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-top: 10px">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="col-md-2 control-label"><?=$model->attributeLabels()['alasan']?></label>
                                            <div class="col-md-9">
                                                <?=Html::activeTextArea($model, 'alasan', ['class'=>'form-control'])?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php 
                                ActiveForm::end();
                                ?>
                        </div>
                    </div>
                </div>

               <!-- panel form pembatalan -->
            </div>
        </div>
    </div>
</div>
<?php 

$this->registerJs("
    $('#form-batal-operasi').docoForm('submit',{
        success : function(data) {
            $('.no-batal').val(data.response.no_batalperiksa)
        }
    });
    $(document).ready(function(){
        $('.namakaryawan').select2({
            placeholder: '',
            minimumInputLength: 3,  
            ajax: {
                url: '/bedah/informasi-pasien-operasi/get-pegawai',
                dataType: 'json',
                quietMillis: 250,
                data: function(term, page){
                    return{
                        q: term,
                        page: page
                    }
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                }
            },
            dropdownCssClass: 'bigdrop',
            escapeMarkup: function (m) { return m; },
        });
    })

    ", View::POS_END, 'js');

?>
