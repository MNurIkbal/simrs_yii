<?php

/**
 * @author Randy Vianda Putra
 * @todo View Input Hasil laboratorium
 * @copyright 13 Juli 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use yii\widgets\Breadcrumbs;
use app\components\DocoTableHelper;
use kartik\datetime\DateTimePicker;
use kartik\widgets\ActiveForm;
use kartik\widgets\FileInput;
$max_uploads = DocoConstants::MAX_UPLOAD_LAB;
?>

<style lang="">
   .info-pasien {
      width: 70px;
      height: 30px;
      border-radius: 5px;
      border: 1px solid black;
      float: left;
      margin: 3px;
   }
   .js .inputfile {
      width: 0.1px;
      height: 0.1px;
      opacity: 0;
      overflow: hidden;
      position: absolute;
      z-index: -1;
   }
   #file-1 {
      display:none;
      margin: 10px;
   }
   .inputfile + label {
      max-width: 100%;
      font-size: 1.25rem;
      /* 20px */
      font-weight: 700;
      text-overflow: ellipsis;
      white-space: nowrap;
      cursor: pointer;
      display: inline-block;
      overflow: hidden;
      /* padding: 0.625rem 1.25rem; */
      padding: 7px 25px;
      width: auto;
      /* 10px 20px */
   }

   .no-js .inputfile + label {
      display: none;
   }

   .inputfile:focus + label,
   .inputfile.has-focus + label {
      outline: 1px dotted #000;
      outline: -webkit-focus-ring-color auto 5px;
   }

   .inputfile + label * {
      /* pointer-events: none; */
      /* in case of FastClick lib use */
   }

   .inputfile + label svg {
      width: 1em;
      height: 1em;
      vertical-align: middle;
      fill: currentColor;
      margin-top: -0.25em;
      /* 4px */
      margin-right: 0.25em;
      /* 4px */
   }


   .inputfile-1 + label {
      color: #f1e5e6;
      background-color: #d3394c;

   }

   .inputfile-1:focus + label,
   .inputfile-1.has-focus + label,
   .inputfile-1 + label:hover {
      background-color: #722040;
   }

   .lurus {
      float: left;
      margin-left: 20px;
   }
</style>
<div class="row">
   <div class="col-md-12">
      <div class="panel panel-white">
         <div class="panel-toolbar clearfix">
            <?= DocoHelpers::generateToolbar([
            'back' => [
               'title' => \Yii::t('fe', 'Kembali'),
               'icon' => 'fa fa-arrow-left',
               'attributes' => [
                  'class' => 'btn btn-info btn-labeled btn-xs spa',
                  'data-options' => 'click',
                  'data-render' => 'hasil-lab?id='.$penunjang_id,
                  'data-tab' => 'tab-non-rujukan',
                  'data-target' => '#view-non-rujukan',
                  'id' => 'btn-kembali-input-hasil',
               ]
            ],
            'save' => [
               'title' => \Yii::t('fe', 'Simpan'),
               'icon' => 'fa fa-check-square-o',
               'attributes' => [
                  'class' => 'btn btn-info btn-labeled btn-xs spa',
                  'id' => 'btn-simpan-input-hasil',
                  'data-options' => 'click',
               ]
            ],
            ]) ?>
         </div>
         <div class="panel-body">
            <div class="row row-eq-height " style="margin-top:10px;">
               <div class="col-md-8" id="informasi">
                  <div class="panel panel-default">
                     <a id="info-heading" data-toggle="collapse" href="#infopasien" role="button" aria-expanded="false" aria-controls="infopasien" >
                        <div class="panel-heading flex-container">
                           <h6 class="panel-title"><?= Yii::t('fe', 'Informasi Pasien') ?></h6>
                           <p class="p-data" id="data-pasien">
                              <?= isset($data_pasien['no_rekam_medik']) ? $data_pasien['no_rekam_medik'] : '-' ?> -
                              <b class="font" ><?= isset($data_pasien['nama_pasien']) ? $data_pasien['nama_pasien'] : '-' ?></b>
                           </p>
                           <ul class="icons-list">
                              <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                           </ul>
                        </div>
                     </a>
                     <div class="panel-body collapse multi-collapse info-card" id="infopasien">
                        <div class="col-xs-2">
                           <div class="border-img">
                              <?php
                                 $filename = isset($data_pasien['photopasien']) ? !empty($data_pasien['photopasien']) ? '/media/img/pasien/'.$data_pasien['photopasien']: '/media/img/icon-app/default.jpg' : '/media/img/icon-app/default.jpg';
                              ?>
                              <?=Html::img($filename, [ 'style'=>'width: 100%;height: auto;max-width: 114px;', 'class'=>'img-responsive'])?>
                           </div>
                        </div>
                        <div class="col-xs-9">
                           <div class="row">
                              <br>
                              <div class="col-xs-6">
                                 <b class="text-left control-label font-design"><?= Yii::t("fe", "Pasien") ?></b>
                                 <br>
                                 <p>
                                    <?= isset($data_pasien['no_rekam_medik']) ? $data_pasien['no_rekam_medik'] : '-' ?> -
                                    <?= isset($data_pasien['nama_pasien']) ? $data_pasien['nama_pasien'] : '-' ?>
                                 </p>
                                 <b class="text-left control-label font-design"><?= Yii::t("fe", "No Telepon") ?></b>
                                 <p>
                                    <?= isset($data_pasien['no_telepon_pasien']) ? $data_pasien['no_telepon_pasien'] : '-' ?>
                                 </p>
                              </div>
                              <div class="col-xs-6">
                                 <b class="text-left control-label font-design"><?= Yii::t("fe", "Pendaftaran") ?></b>
                                 <p>
                                    <?= isset($data_pasien['no_pendaftaran']) ? $data_pasien['no_pendaftaran'] : '-' ?> -
                                    (<?= isset($data_pasien['tglmasukpenunjang']) ? date('d-M-Y', strtotime($data_pasien['tglmasukpenunjang'])) : '-' ?>)
                                 </p>
                                 <b class="text-left control-label font-design"><?= Yii::t("fe", "Kelas pelayanan") ?></b>
                                 <p>
                                    <?= isset($data_pasien['kelaspelayanan_nama']) ? $data_pasien['kelaspelayanan_nama'] : '-' ?> -
                                    <?= isset($data_pasien['carabayar_nama']) ? $data_pasien['carabayar_nama'] : '-' ?> -
                                    <?= isset($data_pasien['penjamin_nama']) ? $data_pasien['penjamin_nama'] : '-' ?>
                                 </p>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="panel panel-default">
                     <a id="info-heading" data-toggle="collapse" href="#infodetail" role="button" aria-expanded="false" aria-controls="infopasien" >
                        <div class="panel-heading flex-container">
                           <h6 class="panel-title"><b><?= Yii::t('fe', 'Detail Informasi Pasien'); ?></b></h6>
                           <ul class="icons-list">
                              <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                           </ul>
                        </div>
                     </a>
                     <div class="panel-body column-info collapse multi-collapse info-card" id="infodetail">
                        <div class="row row-eq-height">
                           <br>
                           <div class="col-xs-6">
                                 <b class="text-left control-label font-design"><?= Yii::t("fe", " Instalasi Akhir") ?></b>
                                 <p>
                                    <?= isset($data_pasien['asalrujukan_nama']) ? $data_pasien['asalrujukan_nama'] : '-' ?>
                                 </p>
                           </div>
                           <div class="col-xs-6">
                                 <b class="text-left control-label font-design"><?= Yii::t("fe", "Ruangan akhir") ?></b>
                                 <p>
                                    <?= !empty($data_pasien['ruangan_nama']) ? $data_pasien['ruangan_nama'] : null ?>
                                 </p>
                           </div>
                           <div class="col-md-12">
                                 <?php
                                    $kuning = !empty($data_pasien['kuning']) ? 'block' : 'none';
                                    $warna_kuning = !empty($data_pasien['kuning']) ? $data_pasien['kuning'] : '';
                                    $ungu = !empty($data_pasien['ungu']) ? 'block' : 'none';
                                    $warna_ungu = !empty($data_pasien['ungu']) ? $data_pasien['ungu'] : '';
                                    $merah = !empty($data_pasien['merah']) ? 'block' : 'none';
                                    $warna_merah = !empty($data_pasien['merah']) ? $data_pasien['merah'] : '';
                                    $coklat = !empty($data_pasien['coklat']) ? 'block' : 'none';
                                    $warna_coklat = !empty($data_pasien['coklat']) ? $data_pasien['coklat'] : '';
                                 ?>
                                 <div class="info-pasien" style="background-color:<?= $warna_kuning; ?>; display:<?= $kuning; ?>;"></div>
                                 <div class="info-pasien" style="background-color:<?= $warna_ungu; ?>; display:<?= $ungu; ?>;"></div>
                                 <div class="info-pasien" style="background-color:<?= $warna_merah; ?>; display:<?= $merah; ?>;"></div>
                                 <div class="info-pasien" style="background-color:<?= $warna_coklat; ?>; display:<?= $coklat; ?>;"></div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="col-md-12 panel panel-default" id="informasi" style="margin-top:10px;">
               <div class="panel-heading">
                  <h6 class="panel-title text-bold"><?= Yii::t('fe', 'Hasil Pemeriksaan Laboratorium') ?>
                     <a class="heading-elements-toggle"><i class="icon-more"></i></a>
                  </h6>
               </div>
               <div class="panel-body">
                  <div class="col-md-12">
                     <?php $form = ActiveForm::begin([
                        'id' => 'form',
                        'action' => '/laboratorium/speciment/save-cache',
                        'enableAjaxValidation' => false,
                        'enableClientValidation' => false,
                        'type' => ActiveForm::TYPE_VERTICAL,
                        // 'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
                     ]) ?>
                     <?= Html::hiddenInput('pasien_id', $pasien_id, ['class' => 'pasien_id']); ?>
                     <?= Html::hiddenInput('pendaftaran_id', $pendaftaran_id, ['class' => 'pendaftaran_id']); ?>
                     <?= Html::hiddenInput('pasienadmisi_id', $pasienadmisi_id, ['class' => 'pasienadmisi_id']); ?>
                     <?= Html::hiddenInput('pasienpenunjang_id', $pasienpenunjang_id, ['class' => 'pasienpenunjang_id']); ?>
                     <?= Html::hiddenInput('penunjang_id', $penunjang_id, ['class' => 'penunjang_id']); ?>
                     <?= Html::hiddenInput('sample_id', $sample_id, ['class' => 'sample_id']); ?>
                     <?= Html::hiddenInput('hasilpemeriksaanlab_id', $hasilpemeriksaanlab_id, ['class' => 'hasilpemeriksaanlab_id']); ?>

                     <div class="row">
                        <div class="col-md-3">
                           <?=
                              $form->field($model, 'no_hasil', [
                                 'labelOptions' => ['class' => 'text-left']
                              ])->textInput([
                                 'class' => 'form-control input-sm sample',
                                 'readonly' => true,
                                 'value' => $no_lab
                              ])->label(Yii::t('fe', 'No Hasil'));
                           ?>
                        </div>
                        <div class="col-md-3">
                           <?= $form->field($model, 'tanggal')->widget(DateTimePicker::classname(), [
                              'language' => 'en',
                              'options' => [
                                 'id' => 'inputhasilform-tanggal',
                                 'class' => 'tanggal',
                                 'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                                 'value' => $tgl_hasilpemeriksaanlab,
                                 'readonly' => true,
                              ],
                              'pluginOptions' => [
                                 'format' => 'dd MM yyyy HH:ii:ss',
                                 'showMeridian' => true,
                                 'autoclose' => true,
                                 'todayBtn' => true,
                                 'endDate' => date('Y-m-d H:i:s'),
                                 'startDate' => $tgl_ambilsample
                              ]
                           ])->label(Yii::t('fe', 'Tanggal Hasil')); ?>
                        </div>
                        <div class="col-md-3">
                           <?= $form->field($model, 'penanggungjawab', [
                                 'labelOptions' => ['class' => 'text-left']
                              ])->dropdownList($data_dokter, [
                                 'class' => 'form-control select2 input-sm penanggungjawab',
                                 'prompt' => \Yii::t('fe', '-- Pilih --'),
                                 'value' => $penanggungjawab_id
                              ])->label(Yii::t('fe', 'Dokter Penanggung Jawab'));
                           ?>
                        </div>
                        <div class="col-md-3">
                           <?=
                              $form->field($model, 'petugas', [
                                 'labelOptions' => ['class' => 'text-left']
                              ])->dropdownList($data_dokter, [
                                 'class' => 'form-control select2 input-sm petugas',
                                 'prompt' => \Yii::t('fe', '-- Pilih --'),
                                 'id' =>'generatepetugas',
                                 'value' => $petugas_id
                              ]);
                           ?>
                        </div>
                     </div>
                     <?php ActiveForm::end(); ?>
                  </div>
                  <hr>
                  <div></div><h6><?= Yii::t('fe', 'Speciment') ?> : <?= $nama_sample ?></h6></div>
                  <table class="table datatable-basic table-striped table-hover dataTable no-footer">
                     <thead>
                        <tr class="bg-inverse">
                           <th><?= Yii::t('fe', 'No') ?></th>
                           <th><?= Yii::t('fe', 'Nama pemeriksaan') ?></th>
                           <th><?= Yii::t('fe', 'Hasil') ?></th>
                           <th><?= Yii::t('fe', 'Satuan') ?></th>
                           <th><?= Yii::t('fe', 'Nilai rujukan') ?></th>
                           <th><?= Yii::t('fe', 'Keterangan') ?></th>
                           <th><?= Yii::t('fe', 'Verifikasi') ?></th>
                           <th><?= Yii::t('fe', 'Petugas') ?></th>
                        </tr>
                     </thead>
                     <tbody>
                     <?php $form = ActiveForm::begin([
                        'id' => 'form-hasil',
                        'action' => '/laboratorium/input-hasil/save',
                        'enableAjaxValidation' => false,
                        'enableClientValidation' => false,
                        'type' => ActiveForm::TYPE_HORIZONTAL,
                        'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
                     ]) ?>
                     <?php
                        $no = 1;
                        $uniq_id = 0;
                        if (!empty($detail_gol_umur)) :
                           foreach ($detail_gol_umur as $pemeriksaan => $detail) :
                                 $count = count($detail);
                                 if ($count > 1) :
                                    ?>
                           <tr>
                                 <td><?= $no ?></td>
                                 <td colspan="6"><?= str_replace(' ', '&nbsp;', $pemeriksaan) ?></td>
                           </tr>
                           <?php
                                 foreach ($detail as $value) :
                                    if (!empty($value['nama_rujukan'])) :
                           ?>
                                    <tr class="legend<?= $no ?>">
                                       <td></td>
                                       <td><?= $value['nama_rujukan']; ?></td>
                                       <td width="20%">
                                          <input
                                             value="<?= $value['hasil'] ?>"
                                             type="text"
                                             name="InputHasilForm[hasil][]"
                                             onblur="onHasilLegend(<?= $no ?>, this.value, '<?= $value['nilai_rujukan']; ?>')"
                                             id ="hasil<?= $no ?>"
                                             class="form-control hasil"
                                             data-id="<?= $value['daftartindakan_id']; ?>"
                                             data-tindakan="<?= $value['tipepaket_id']; ?>"
                                             data-rujukan="<?= $value['nilairujukan_id']; ?>"
                                             data-pemeriksaan="<?= $value['pemeriksaanlab_id'] ?>"
                                             data-nilai="<?= $value['nilairujukan_id'] ?>"
                                             data-satuan="<?= $value['satuan_hasillab'] ?>"
                                             data-keterangan="<?= $value['keterangan'] ?>"
                                          >
                                       </td>
                                       <td><?= $value['satuanlab_nama']; ?></td>
                                       <td><?= $value['nilai_rujukan']; ?></td>
                                       <td><?= $value['is_verifikasi']; ?></td>
                                       <td><?= $value['keterangan']; ?></td>
                                       <td width="20%">
                                          <?=
                                             Html::activeDropDownList($model, 'pegawai_id[]', $data_dokter, [
                                                'value' => $value['petugaslab_id'],
                                                'class' => 'form-control select2 pegawai_id',
                                                'id' => 'pegawai_id'. $uniq_id,
                                                'prompt' => Yii::t('fe', '-- Pilih --')
                                             ])
                                          ?>
                                       </td>
                                    </tr>
                           <?php
                                 endif;
                                 $uniq_id++;
                                 endforeach;
                                 else :
                           ?>
                                 <tr class="legend<?= $no ?>">
                                    <td><?= $no ?></td>
                                    <td><?= str_replace(' ', '&nbsp;', $pemeriksaan) ?></td>
                                    <?php
                                       $uniq_id = $uniq_id;
                                       foreach ($detail as $value) :
                                    ?>
                                       <td width="20%" >
                                             <input
                                                value="<?= $value['hasil'] ?>"
                                                type="text"
                                                name="InputHasilForm[hasil][]"
                                                onblur="onHasilLegend(<?= $no ?>, this.value, '<?= $value['nilai_min']; ?>', '<?= $value['nilai_max']; ?>')"
                                                id ="hasil<?= $no ?>"
                                                class="form-control hasil"
                                                data-id="<?= $value['daftartindakan_id']; ?>"
                                                data-tindakan="<?= $value['tipepaket_id']; ?>"
                                                data-rujukan="<?= $value['nilairujukan_id']; ?>"
                                                data-pemeriksaan="<?= $value['pemeriksaanlab_id'] ?>"
                                                data-nilai="<?= $value['nilairujukan_id'] ?>"
                                                data-satuan="<?= $value['satuan_hasillab'] ?>"
                                                data-keterangan="<?= $value['keterangan'] ?>"
                                             >
                                       </td>
                                       <td><?= $value['satuanlab_nama']; ?></td>
                                       <td><?= $value['nilai_rujukan']; ?></td>
                                       <td><?= $value['keterangan']; ?></td>
                                       <td>
                                             <?=
                                             Html::activeCheckbox($model, 'is_verifikasi['.$no.']', [
                                                'checked'=>$value['is_verifikasi'],
                                                'id' => "verif-". $value['nilairujukan_id'],
                                                'label'=>null
                                             ]);
                                             ?>
                                       </td>
                                       <td width="20%">
                                             <?= Html::activeDropDownList($model, 'pegawai_id[]', $data_dokter, [
                                                'value' => $value['petugaslab_id'],
                                                'class' => 'form-control select2 pegawai_id',
                                                'id' => 'pegawai_id'. $uniq_id,
                                                'prompt' => Yii::t('fe', '-- Pilih --')
                                             ]) ?>
                                       </td>
                                    <?php
                                       $uniq_id++;
                                       endforeach;
                                    ?>
                                 </tr>
                        <?php
                           endif;
                           $no++;
                           endforeach;
                        else :
                        ?>
                           <tr>
                                 <td colspan="7" class="text-center"><?= Yii::t('fe', 'Data tidak ditemukan'); ?></td>
                           </tr>
                        <?php endif; ?>
                     <?php ActiveForm::end(); ?>
                     </tbody>
                  </table>
                  <br><br>
                  <?php $form = ActiveForm::begin([
                     'id' => 'form-upload',
                     'enableAjaxValidation' => false,
                     'enableClientValidation' => false,
                     'type' => ActiveForm::TYPE_VERTICAL,
                     // 'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL],
                     'options' => [
                        'role' => 'form',
                        'enctype'=>'multipart/form-data'
                     ]
                  ]) ?>
                  <div class="col-md-12">
                     <div class="col-md-10">
                        <div class="lurus">
                           <label for="file-1" class="control-label">
                                 Upload File
                           </label>
                        </div>
                        <div class="lurus">
                           <input type="file" name="UploadForm[upload_file]" id="file-1" class="form-control inputfile inputfile-1" data-multiple-caption="{count} files selected" >
                           <label for="file-1">
                                 <i class="fa fa-upload"></i>
                                 <span id="label-file">Pilih Berkas</span>
                           </label>
                        </div>
                        <div class="lurus">
                        <?= Html::a(''.Yii::t('fe', ' <span id="lihat"> Lihat File</span>'), '@web/media/input-hasil-lab/',
                           [
                              'class' => 'btn btn-info btn-sm lihat_file',
                              'target' => 'blank',
                              'data-file' => $file,
                              'data-id' => $pendaftaran_id,
                              ]);
                        ?>
                        </div>
                     </div>
                     <div class="col-md-2">
                     </div>
                     <div class="col-md-10 col-md-offset-1">
                        * <b><?= Yii::t('fe', 'maksimal 100 mb'); ?></b>
                     </div>
                     <div class="col-md-10 col-md-offset-1">
                        <div class="error-upload"></div>
                     </div>
                  </div>
                  <?php ActiveForm::end(); ?>
                  <br>
                  <div class="col-md-12">
                     <input type="checkbox" class="is_kritis" <?= $is_kritis ?>>
                     <label style="font-size:13px;"><?= Yii::t('fe', 'Kondisi kritis') ?></label>
                  </div>
                  <br>
                  <div class="col-md-12">
                     <label style="font-size:14px;"><?= Yii::t('fe', 'Expertise') ?> :</label>
                     <textarea class="form-control expertise" id="" cols="20" rows="5"><?= $expertise ?></textarea>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>

<?php
   $this->registerJs("
      var MAX_UPLOAD = {$max_uploads};
   ", VIEW::POS_END, 'js-kunings');
   $this->registerJs($this->render('js/save-hasil.js'), View::POS_END);
?>
