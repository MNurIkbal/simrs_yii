<?php

/**
 * @author Naufal Ziyad L
 */

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;

$this->title = Yii::t('fe', 'Reservasi Poliklinik');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Pendaftaran'), 'url' => ['index']];
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
                      <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                      <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
              </div>
              <!-- end -->
            </div>
            <div class="panel-body" style="padding:10px;">
            <?php
                $form = ActiveForm::begin([
                    'id' => 'form-reservasi-poli',
                    'class' => 'horizontal-form',
               /*     'role'=>'form',*/
                    'enableClientValidation'=>true,
                ]);
            ?>
            <fieldset class="content-group">
            <div class="row" >
                <div class="col-md-12 panel panel-flat">
                    <legend class="text-bold">Data Pasien</legend>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><?=Yii::t('fe','Cari No. Rekam Medik')?></label>
                            <div class="input-group">
                                <?php
                                echo Html::dropDownList('no_rekam_medik', '', array(),
                                        [
                                            'class' => 'form-control select2 ddl_no_rekam_medik',
                                            'prompt' => \Yii::t('fe', 'Pilih'),
                                        ]
                                    );
                                ?>
                                <span class="input-group-btn">
                                    <button class="btn btn-default" id="datashow" type="button"><?=Yii::t('fe','Tampilkan')?></button>
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label"><?= Yii::t('fe', 'Nama Pasien') ?></label>
                            <div class="input-group col-md-12">
                                <?php
                                echo Html::textInput('','',['class'=>'form-control selectPasien','readonly'=>'true','placeholder'=>Yii::t('fe', 'Nama pasien') ]);
                                ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label"><?= Yii::t('fe', 'Nama Panggilan') ?></label>
                            <div class="input-group col-md-12">
                                    <?php
                                    echo Html::textInput('','',['class'=>'form-control selectPanggilan','readonly'=>'true','placeholder'=>Yii::t('fe', 'Nama Panggilan') ]);
                                    ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label"><?= Yii::t('fe', 'Tempat Lahir') ?></label>
                            <div class="input-group col-md-12">
                                    <?php
                                    echo Html::textInput('','',['class'=>'form-control selectTempatLahir','readonly'=>'true','placeholder'=>Yii::t('fe', 'Tempat Lahir') ]);
                                    ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label"><?= Yii::t('fe', 'Tanggal Lahir') ?></label>
                            <div class="input-group col-md-12">
                                <?php
                                echo Html::textInput('','',['class'=>'form-control date selectTanggalLahir','readonly'=>'true','placeholder'=>Yii::t('fe', 'Tanggal Lahir') ]);
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label"><?= Yii::t('fe', 'Umur') ?></label>
                            <div class="input-group col-md-12">
                                <?php
                                    echo Html::textInput('','',['class'=>'form-control selectUmur','readonly'=>'true','placeholder'=>Yii::t('fe', 'Umur') ]);
                                ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label"><?= Yii::t('fe', 'Jenis Kelamin') ?></label>
                            <div class="input-group col-md-12">
                                <?php
                                    echo Html::textInput('','',['class'=>'form-control selectJenisKelamin','readonly'=>'true','placeholder'=>Yii::t('fe', 'Umur') ]);
                                ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label"><?= Yii::t('fe', 'Alamat Pasien') ?></label>
                            <div class="input-group col-md-12">
                                <?php
                                    echo Html::textArea('','',['class'=>'form-control selectAlamatPasien','rows' => 4,'readonly'=>'true','placeholder'=>Yii::t('fe', 'Alamat Pasien') ]);
                                ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="control-label"><?= Yii::t('fe', 'No Tlp/Hp') ?></label>
                            <div class="input-group col-md-12">
                                <?php
                                    echo Html::textInput('','',['class'=>'form-control selectNoTelp','readonly'=>'true','placeholder'=>Yii::t('fe', 'No Tlp/Hp') ]);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row" >
                <div class="col-md-11 panel panel-flat" style="margin-left:15px">
                        <legend class="text-bold">Data Pemesanan</legend>
                    <div class="col-md-4" style="margin-left:5%">
                            <div class="form-group">
                                <label class="control-label">
                                   <i class="fa fa-phone"></i> <?= Yii::t('fe', 'By Phone') ?>&nbsp;&nbsp;<input type="checkbox" name="byphone" id="by_phonecheck">
                                </label>
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?= Yii::t('fe', 'Ruangan') ?></label>
                                <div class="input-group col-md-12">
                                    <?= $form->field($model, 'ruangan_id')->dropDownList($ddlRuangan, ['id'=>'ruangan_id','prompt'=>'— PILIH —'])->label(false) ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?= Yii::t('fe', 'Dokter') ?></label>
                                <div class="input-group col-md-12">
                                    <?= $form->field($model, 'pegawai_id')->dropDownList($pegawai, ['id'=>'pegawai_id','prompt'=>'— PILIH —'])->label(false) ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label"><?= Yii::t('fe', 'Cara Bayar') ?></label>
                                <div class="input-group col-md-12">
                                    <?= $form->field($model, 'carabayar_id')->dropDownList($carabayar, ['id'=>'carabayar_id','prompt'=>'— pilih cara bayar —'])->label(false) ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label"><?= Yii::t('fe', 'Penjamin') ?></label>
                                <div class="input-group col-md-12">
                                    <?= $form->field($model, 'penjamin_id')->widget(DepDrop::classname(), [
                                        'options'=>['id'=>'penjamin_id'],
                                        'data'=>$penjaminList,
                                        'pluginOptions'=>[
                                            'depends'=>['carabayar_id'],
                                            'initialize' => true,
                                            'loadingText' => Yii::t('fe', 'Memuat...'),
                                            'placeholder'=>'--Pilih penjamin--',
                                            'url'=>Url::to(['/pendaftaran/reservasi-poliklinik/list-penjamin'])
                                        ]
                                    ])->label(false); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label"><?= Yii::t('fe', 'Tanggal Reservasi') ?></label>
                                <div class="input-group col-md-12">
                                    <?= $form->field($model, 'tgl_jadwal')->textInput(['class' => 'form-control pickadate'])->label(false) ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label"><?= Yii::t('fe', 'Keterangan') ?></label>
                                <div class="input-group col-md-12">
                                    <?= $form->field($model, 'keterangan_buatjanji')->textArea()->label(false); ?>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
            </fieldset>
            <div class="row" style="margin-left:15px">
            <?= Html::submitButton("<i class='fa fa-floppy-o'> Simpan</i>", ['class' => 'btn bg-teal']) ?>
            <?= Html::button("<i class='fa fa-refresh'> Ulang</i>",[
                                'class' => 'btn btn-lime-green',
                                'id' => 'tombol-reset',
                                ]); ?>
            <?= Html::button("<i class='fa fa-print'> Print Karcis</i>",[
                                'class' => 'btn btn-dodger-blue',
                                'data-dismiss' => 'modal'
                                ]); ?>
            </div>

            <span class="inputpemesan">
                <?= $form->field($model, 'pasien_id')->textInput(['class' => 'selectPasienId']) ?>
                <?= $form->field($model, 'status_janjipoli')->textInput(['class' => 'selectStatusJanji']) ?>
                <?= $form->field($model, 'tgl_buatjanji')->textInput(['class' => 'selectTanggalSekarang']) ?>
                <?= $form->field($model, 'hari_jadwal')->textInput(['class' => 'selectHari']) ?>
                <?= $form->field($model, 'antrian_id')->textInput(['class' => 'selectAntrian']) ?>
                <?= $form->field($model, 'by_phone')->textInput(['class' => 'ByPhoneText']) ?>
            </span>
            <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs($this->render('js/reservasi-poli.js')) ?>

<?php
$this->registerJs("
    $('.pickadate').pickadate({
            format: 'yyyy/mm/dd',
        });

    $(function(){
        $('.ddl_no_rekam_medik').select2({
            minimumInputLength: 3,
            ajax: {
                url: '/pendaftaran/pemesanan-kamar/get-pasien-rekam-medik',
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

        $('#datashow').click(function(){
            no_rm = $('.ddl_no_rekam_medik').val();

            $.ajax({
                url: '/pendaftaran/pemesanan-kamar/get-info-pasien',
                type: 'GET',
                async: true,
                dataType: 'json',
                data: {
                    no_rm: no_rm
                },
                beforeSend : function () {
                    console.log('Mohon Tunggu');
                },
                success: function(data){
                    $('.selectPasien').val(data['nama_pasien']);
                    $('.selectPanggilan').val(data['nama_bin']);
                    $('.selectTempatLahir').val(data['tempat_lahir']);
                    $('.selectTanggalLahir').val(data['tanggal_lahir']);

                    dob = new Date(data['tanggal_lahir']);
                    var today = new Date();
                    var age = Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000));

                    $('.selectUmur').val(age + 'Tahun');
                    $('.selectJenisKelamin').val(data['jenis_kelamin']);
                    $('.selectAlamatPasien').val(data['alamat_sekarang'] + ' RT/RW ' + data['rt'] + '/' + data['rw']);
                    $('.selectNoTelp').val(data['no_telepon_pasien']);
                },
                error : function (data) {
                    console.log('ERROR');
                },
            })
        });

    })
", View::POS_END, 'jkun');
?>
