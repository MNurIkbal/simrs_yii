<?php
/**
 * @author : Asri
 * Powered by Sirs
 */
use app\components\DocoConstants;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\web\View;
use yii\web\JsExpression;
use kartik\widgets\DateTimePicker;
use kartik\widgets\DatePicker;
?>
<style>
    .m-10 {
        margin: 10px 0px !important;
    }
    .ml-10{
        margin-left: -10px !important;
    }
    .panel-toolbar-skrining {
        padding: 6px 10px 6px 14px;
        border-bottom: 1px solid #cccccc;
        background-color: #f5f5f5;
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title"><?= Yii::t('fe', 'ASSESMENT INFORMASI PASIEN RAWAT JALAN') ?></h5>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapsed" href="#skrining-assesment"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel panel-toolbar-skrining clearfix">
                <?= DocoHelpers::generateToolbar([
                    'custom-save' => [
                        'title' => \Yii::t('fe', 'Simpan'),
                        'icon' => 'fa fa-save',
                        'attributes' => [
                            'id' => 'submit-assesment-pasien',
                            'data-options' => 'click'
                        ]
                    ],
                    'custom-print' => [
                        'title' => Yii::t('fe', 'Cetak'),
                        'icon' => 'fa fa-print',
                        'attributes' => [
                            'data-options' => 'click',
                            'id' => 'btn-print-skrining-assesment',
                            'data' => $pendaftaran_id,
                            'disabled' => !empty($skriningAssesment) ? false : true,
                        ],
                    ],
                ], ''); ?>
            </div>
            <div class="card">
              <div class="card-body">
                <?php
                    $form = ActiveForm::begin([
                        'id' => 'form-assesment-pasien',
                        'type' => ActiveForm::TYPE_HORIZONTAL,
                        'enableAjaxValidation'=>false, 
                        'enableClientValidation'=>false,
                    ]);
                ?>
                <table class="table table-bordered table-hover">
                    <tr>
                        <td rowspan="2" colspan=4""><?= $form->field($model, 'alamat_pasien', [
                              'horizontalCssClasses' => [
                                  'label' => 'text-left control-label col-sm-4',
                                  'wrapper' => 'col-md-8'
                              ]
                          ])->textarea(['rows' => '4','value'=> $alamat],['class' => 'form-control']); ?></td>
                        <td colspan="3">
                          <?= $form->field($model, 'kelurahan_desa', [
                              'horizontalCssClasses' => [
                                  'label' => 'text-left control-label col-sm-4',
                                  'wrapper' => 'col-md-8'
                              ]
                          ])->textInput([
                              'class' => 'form-control input-sm',
                              'value' => $kelurahan
                          ]) ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                          <?= $form->field($model, 'kecamatan', [
                              'horizontalCssClasses' => [
                                  'label' => 'text-left control-label col-sm-4',
                                  'wrapper' => 'col-md-8'
                              ]
                          ])->textInput([
                              'class' => 'form-control input-sm',
                              'value' => $kecamatan
                          ]) ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                          <?= $form->field($model, 'no_hp', [
                              'horizontalCssClasses' => [
                                  'label' => 'text-left control-label col-sm-4',
                                  'wrapper' => 'col-md-8'
                              ]
                          ])->textInput([
                              'class' => 'form-control input-sm',
                              'value' => $no_tlp
                          ]) ?>
                        </td>
                        <td colspan="3"><?= $form->field($model, 'kabupaten', [
                              'horizontalCssClasses' => [
                                  'label' => 'text-left control-label col-sm-4',
                                  'wrapper' => 'col-md-8'
                              ]
                          ])->textInput(['value'=> $kabupaten]) ?></td>
                    </tr>
                    <tr>
                      <td><?= $form->field($model, 'tempat_lahir', [
                              'horizontalCssClasses' => [
                                  'label' => 'text-center control-label col-sm-12',
                                  'wrapper' => 'col-md-12'
                              ]
                          ])->textInput(['value'=> $tempat_lahir]) ?></td>
                      <td><?= $form->field($model, 'tgl_lahir', [
                              'horizontalCssClasses' => [
                                  'label' => 'text-center control-label col-sm-12',
                                  'wrapper' => 'col-md-12'
                              ]
                          ])->textInput(['value'=> $tanggal_lahir]) ?></td>
                      <td><?= $form->field($model, 'jenis_kelamin')->radioList(['L' => 'L', 'P' => 'P'], ['inline' => true, 'class' => 'jenis_kelamin'])->label($model->getAttributeLabel('jenis_kelamin')); ?></td>
                      <td><?= $form->field($model, 'status_pernikahan', [
                              'horizontalCssClasses' => [
                                  'label' => 'text-center control-label col-sm-12',
                                  'wrapper' => 'col-md-12'
                              ]
                          ])->textInput() ?></td>
                      <td><?= $form->field($model, 'agama', [
                              'horizontalCssClasses' => [
                                  'label' => 'text-center control-label col-sm-12',
                                  'wrapper' => 'col-md-12'
                              ]
                          ])->textInput() ?></td>
                      <td><?= $form->field($model, 'pendidikan', [
                              'horizontalCssClasses' => [
                                  'label' => 'text-center control-label col-sm-12',
                                  'wrapper' => 'col-md-12'
                              ]
                          ])->textInput() ?></td>
                      <td><?= $form->field($model, 'pekerjaan', [
                              'horizontalCssClasses' => [
                                  'label' => 'text-center control-label col-sm-12',
                                  'wrapper' => 'col-md-12'
                              ]
                          ])->textInput(['value'=> $pekerjaan]) ?></td>
                    </tr>
                    <tr>
                      <td  colspan="4">
                        <div class="panel-body">
                            <div class="form-group">
                              <?= $form->field($model, 'nama_ayah_atau_ibu', [
                                  'horizontalCssClasses' => [
                                      'label' => 'text-left control-label col-sm-4',
                                      'wrapper' => 'col-md-8'
                                  ]
                              ])->textInput([
                                  'class' => 'form-control input-sm',
                                  'value' => $nama_ayah
                              ]) ?>
                            </div>
                            <div class="form-group">
                              <?= $form->field($model, 'nama_suami_atau_istri', [
                                  'horizontalCssClasses' => [
                                      'label' => 'text-left control-label col-sm-4',
                                      'wrapper' => 'col-md-8'
                                  ]
                              ])->textInput([
                                  'class' => 'form-control input-sm'
                              ]) ?>
                            </div>
                        </div>
                      </td>
                      <td colspan="3">
                        <div class="panel-body">
                            <div class="form-group">
                              <?= $form->field($model, 'pekerjaan_ayah_atau_ibu', [
                                  'horizontalCssClasses' => [
                                      'label' => 'text-left control-label col-sm-4',
                                      'wrapper' => 'col-md-8'
                                  ]
                              ])->textInput([
                                  'class' => 'form-control input-sm',
                              ]) ?>
                            </div>
                            <div class="form-group">
                              <?= $form->field($model, 'pekerjaan_suami_atau_istri', [
                                  'horizontalCssClasses' => [
                                      'label' => 'text-left control-label col-sm-4',
                                      'wrapper' => 'col-md-8'
                                  ]
                              ])->textInput([
                                  'class' => 'form-control input-sm',
                              ]) ?>
                            </div>
                        </div>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="7"><?= $form->field($model, 'cara_berkunjung')->textInput() ?></td>
                    </tr>
                    <tr>
                        <td colspan="4">
                          <?= $form->field($model, 'cara_bayar', [
                              'horizontalCssClasses' => [
                                  'label' => 'text-left control-label col-sm-4',
                                  'wrapper' => 'col-md-8'
                              ]
                          ])->textInput([
                              'class' => 'form-control input-sm',
                              'value' => $carabayar
                          ]) ?>
                        </td>
                        <td colspan="3">
                          <?= $form->field($model, 'no_peserta_bpjs_kis', [
                              'horizontalCssClasses' => [
                                  'label' => 'text-left control-label col-sm-4',
                                  'wrapper' => 'col-md-8'
                              ]
                          ])->textInput([
                              'class' => 'form-control input-sm',
                              'value' => $nokartuasuransi
                          ]) ?>
                        </td>
                    </tr>
                    <tr>
                      <td colspan="7"><?= $form->field($model, 'status_kepesertaan')->radioList([1 => 'Anak (1,2,3)', 2 => 'Istri', 3 => 'Suami', 4 => 'Peserta'], ['inline' => true, 'class' => 'status_kepesertaan'])->label($model->getAttributeLabel('status_kepesertaan')); ?></td>
                    </tr>
                    <tr>
                      <td colspan="7"><?= $form->field($model, 'tgl_kunjungan')->textInput(['type' => 'datetime-local']) ?></td>
                    </tr>
                    <tr>
                      <td colspan="7"><?= $form->field($model, 'cara_masuk')->textInput(['value'=> $caramasuk]) ?></td>
                    </tr>
                    <tr>
                        <td colspan="4">
                          <?= $form->field($model, 'petugas_loket_pendaftaran', [
                              'horizontalCssClasses' => [
                                  'label' => 'text-left control-label col-sm-4',
                                  'wrapper' => 'col-md-8'
                              ]
                          ])->textInput([
                              'class' => 'form-control input-sm'
                          ]) ?>
                        </td>
                        <td colspan="3">
                          <?= $form->field($model, 'pasien_keluarga_pasien', [
                              'horizontalCssClasses' => [
                                  'label' => 'text-left control-label col-sm-4',
                                  'wrapper' => 'col-md-8'
                              ]
                          ])->textInput([
                              'class' => 'form-control input-sm',
                          ]) ?>
                        </td>
                    </tr>
                </table>
                <?=  Html::activeHiddenInput($model, 'pendaftaran_id', ['value' => $pendaftaran_id]) ?>
                <?=  Html::activeHiddenInput($model, 'is_riwayat', ['value' => $is_riwayat]) ?>
                <?php ActiveForm::end(); ?>
              </div>
            </div>
        </div>
    </div>
</div>
<script>
    var is_riwayat = $('#is_riwayat').val()
    if(is_riwayat == 'true' || is_riwayat == true){
        $(':input[type="radio"]').attr("disabled", 'disabled');
        $(':input[type="text"]').attr("disabled", 'disabled');
        $(':input[type="number"]').attr("disabled", 'disabled');
        $(':input[type="checkbox"]').attr("disabled", 'disabled');
        $(':input[type="datetime-local"]').attr("disabled", 'disabled');
        $('#submit-assesment-pasien').attr("disabled", 'disabled');
        $('textarea').attr("disabled", 'disabled');
        $('#btn-print-skrining-assesment').attr('disabled',false);
    }
    $(document).ready(function () {
        $("#submit-assesment-pasien").click(function (e) { 
            e.preventDefault();
            var _data = $('#form-assesment-pasien').serializeArray();
            $().docoForm('click', {
                method: "POST",
                url: 'rajal/informasi/save-assesment-pasien',
                data: _data,
                success: function (data) {
                    $('#btn-print-skrining-assesment').attr('disabled',false);
                    $('#tab-assesment-pasien').trigger('click')
                }
            });
        });
    });

    $('#btn-print-skrining-assesment').on('click', function(){
        let data = $(this).attr('data');
        let url = '/rajal/informasi/print-skrining-assesment?pendaftaran_id='+data
        console.log(data)
        window.open(url, '_blank');
    })
</script>
