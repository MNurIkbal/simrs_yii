<?php

use yii\web\View;
use yii\web\JsExpression;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\DatePicker;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;

?>

<style type="text/css">
    .datepicker>div {
        display: block;
    }

    .kv-date-remove {
        display: none;
    }
</style>

<div class="panel panel-flat">
  <div class="panel-heading">
      <div class="row">
          <div class="col-md-7">
              <h5 class="panel-title"><?= $title ?></h5>   
          </div>    
          <div class="col-md-4">                   
              <?= Html::dropDownList('template_id', '', array(),
                  [
                      'id'  => 'list-phr',
                      'class' => 'form-control select2  input-sm ',
                      'prompt' => Yii::t('fe', '-- Pilih Template --'),
                  ]
              ); ?>
          </div>   
          <div class="col-md-1">  
            <button type="button" class="btn btn-sm btn-info" id="pilih-phr">Pilih</button>
          </div>   
      </div>
  </div>
    <div class="panel-toolbar clearfix">
        <div class="col-md-8">
            <?= DocoHelpers::generateToolbar([
                'save' => [
                    'attributes' => [
                        'form_id' => 'form-hasil-phr',
                        'id' => 'submit-hasil-phr',
                    ]
                ],
                'cetak-report' => [
                    'type' => 'button',
                    'title' => \Yii::t('fe', 'Cetak Rahasia Medis'),
                    'icon' => 'fa fa-print',
                    'method' => 'not-exist',
                    'attributes' => [
                        'id' => 'btn-print-rahasia-medik',
                        'class' => 'btn-print-cetak-report',
                        'data-options' => 'link',
                        'target' => '_blank',
                    ]
                ],
                'cetak-report-public' => [
                    'type' => 'button',
                    'title' => \Yii::t('fe', 'Cetak Release Publik'),
                    'icon' => 'fa fa-print',
                    'method' => 'not-exist',
                    'attributes' => [
                        'id' => 'btn-print-rilis-mudik',
                        'class' => 'btn-print-cetak-report',
                        'data-options' => 'link',
                        'target' => '_blank',
                    ]
                ]
            ], '');
            ?>
        </div>
        <div class="col-md-4 template" align="right">
            <button  type="button" class="btn btn-sm btn-danger" id="hapus-template-phr" disabled><li class="fa fa-trash"></li> Hapus Template</button>
            <?= Html::button('<i class="fa fa-floppy-o"></i> '. Yii::t('fe', 'Simpan Template'), [
                'class' => 'btn btn-primary save-template btn-sm',
                'id' => 'save-template-phr',
                'data-toggle' => 'modal',
                'data-target' => '#modal_backdrop',
                'action'      => '/mcu/pemeriksaan/modal-template?id='.$pendaftaran_id.'&type=hasil_pemeriksaan&modal=is_modal',true,
                'disabled'    => 'disabled'
            ]); ?>
        </div>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <?php
                $form = ActiveForm::begin([
                    'id' => 'form-hasil-phr',
                    'enableAjaxValidation' => false,
                    'enableClientValidation' => false,
                    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
                ]);
                ?>
                <!-- Section Identitas Kerja -->
                <div class="row">
                    <div class="col-md-12 ml-3 mt-3">
                        <div class="panel panel-default">
                            <a data-toggle="collapse" href="#identitaspasien" role="button" aria-expanded="true" aria-controls="identitaspasien">
                                <div class="panel-heading flex-container">
                                    <h6 class="panel-title"><b><?= Yii::t('fe', 'Identitas Pekerja'); ?></b></h6>
                                    <div>
                                        <ul class="icons-list">
                                            <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                                        </ul>
                                    </div>
                                </div>
                            </a>
                            <div class="panel-body collapse multi-collapse collapse in" id="identitaspasien">
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <?= $form->field($model, 'nama_perusahaan')->textInput(); ?>
                                        </div>
                                        <div class="form-group">
                                            <?= $form->field($model, 'lokasi_kerja'); ?>
                                        </div>
                                        <div class="form-group">
                                            <?= $form->field($model, 'tipe_pekerja')->radioList([0 => "Field Worker", 1 => "Office Worker "], [
                                                'inline' => true
                                            ]); ?>
                                        </div>
                                        <div class="form-group">
                                            <?= $form->field($model, 'jabatan')->textInput()->label("Pekerjaan/Jabatan"); ?>
                                        </div>
                                        <div class="form-group">
                                            <?php $model->tgl_periksa = !empty($model->tgl_periksa) ? date('d-m-Y', strtotime($model->tgl_periksa)) : date('d-m-Y'); ?>
                                            <?= $form->field($model, 'tgl_periksa', [
                                                'horizontalCssClasses' => [
                                                    'label' => 'text-left control-label col-sm-2',
                                                    'wrapper' => 'col-md-5'
                                                ]
                                            ])->widget(DatePicker::classname(), [
                                                'name' => 'date_12',
                                                'readonly' => true,
                                                'pluginOptions' => [
                                                    'autoclose' => true,
                                                    'format' => 'dd-mm-yyyy',
                                                    'endDate' => "0d",
                                                ]
                                            ]);
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <?= $form->field($model, 'matriks_pemeriksaan')->radioList([0 => "Matriks Lama", 1 => "Matriks Baru "], [
                                                'inline' => true
                                            ]); ?>
                                        </div>
                                        <div class="form-group">
                                            <?= $form->field($model, 'prosedur_pemeriksaan')->radioList(
                                                [
                                                    0 => "Sebelum Bekerja (Pre Employment)",
                                                    1 => "Pemeriksaan Berkala (Periodic)",
                                                    2 => "Pemeriksaan Spesific: Surveillance",
                                                    3 => "Pemeriksaan Khusus: Job Transfer, For Cause Return to Work",
                                                    4 => "Lainnya",
                                                ],
                                                [
                                                    'inline' => false,
                                                    'item' => function ($index, $label, $name, $checked, $value) use ($model) {
                                                        $lainnyaValue = '';
                                                        $check = null;
                                                        if ($model->prosedur_pemeriksaan != null) {
                                                            if ($model->prosedur_pemeriksaan == $value) {
                                                                $check = 'checked="checked"';
                                                            }
                                                        }
                                                        if ($index == 4) {
                                                            $lainnyaValue .= '
                                                                <span class="ml-3">
                                                                    <input type="text" name="HasilPemeriksaanPhrForm[prosedur_pemeriksaan_text]" class="form-control" style="width:50%; display:inline-flex !important" value=' . $model->prosedur_pemeriksaan_text . '>
                                                                </span>
                                                            ';
                                                        }
                                                        $return = '<div class="radio-' . $value . '">';
                                                        $return .= '<input name="HasilPemeriksaanPhrForm[prosedur_pemeriksaan]"' . $check . 'class="haislpemeriksaanphrform-prosedur_pemeriksaan" type="radio" name="' . $name . '" value="' . $value . '" tabindex="3"' . ' id="pemeriksaan-' . $value . '">';
                                                        $return .= ' <i></i>';
                                                        $return .= '<span>' . ucwords($label) . $lainnyaValue . '</span>';
                                                        $return .= '</div>';

                                                        return $return;
                                                    }
                                                ]
                                            ); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Section Identitas Kerja -->

                <!-- Section Rekomendasi Status Derajat Kesehatan -->
                <div class="row">
                    <div class="col-md-12 ml-3 mt-3">
                        <div class="panel panel-default">
                            <a data-toggle="collapse" href="#statusderajatkesehatan" role="button" aria-expanded="true" aria-controls="statusderajatkesehatan">
                                <div class="panel-heading flex-container">
                                    <h6 class="panel-title"><b><?= Yii::t('fe', 'Rekomendasi Status Derajat Kesehatan'); ?></b></h6>
                                    <div>
                                        <ul class="icons-list">
                                            <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                                        </ul>
                                    </div>
                                </div>
                            </a>
                            <div class="panel-body collapse multi-collapse collapse in" id="statusderajatkesehatan">
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <?= $form->field($model, 'status_derajat_kesehatan')->radioList(
                                                [
                                                    0 => "<b>P1. Fit</b> - Tidak ditemukan kelainan medis",
                                                    1 => "<b>P2. Fit dengan catatan</b> - Ditemukan kelainan medis yang tidak serius",
                                                    2 => "<b>P3. Fit dengan catatan</b> - Ditemukan kelainan medis, risiko kesehatan rendah",
                                                    3 => "<b>P4. Fit dengan catatan</b> - Ditemukan kelainan medis, risiko kesehatan sedang",
                                                    4 => "<b>P5. Fit dengan catatan</b> - Ditemukan kelainan medis, risiko kesehatan tinggi",
                                                    5 => "<b>P6. Fit dengan pembatasan</b> - Ditemukan kelainan medis yang menyebabkan keterbatasan fisik maupun psikis untuk melakukan pekerjaan sesuai jabatan/posisinya",
                                                    6 => "<b>P7. UNFIT</b> - Tidak dapat bekerja untuk melakukan pekerjaan sesuai jabatan/posisinya dan / atau posisi apapun, dalam perawatan di rumah sakit, atau dalam status ijin sakit",
                                                ],
                                                [
                                                    'inline' => false
                                                ]
                                            )->label(false); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Section Rekomendasi Status Derajat Kesehatan -->

                <!-- Section Kelaikan -->
                <div class="row">
                    <div class="col-md-12 ml-3 mt-3">
                        <div class="panel panel-default">
                            <a data-toggle="collapse" href="#kelaikan" role="button" aria-expanded="true" aria-controls="kelaikan">
                                <div class="panel-heading flex-container">
                                    <h6 class="panel-title"><b><?= Yii::t('fe', 'Rekomendasi Status Kelaikan Kerja'); ?></b></h6>
                                    <div>
                                        <ul class="icons-list">
                                            <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                                        </ul>
                                    </div>
                                </div>
                            </a>
                            <div class="panel-body collapse multi-collapse collapse in" id="kelaikan">
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <?= $form->field($model, 'laik_satutahun')->radioList(
                                                [
                                                    0 => "Laik Bekerja, berlaku 1 tahun",
                                                    1 => "Laik Bekerja, dengan catatan",
                                                    2 => "Laik Bekerja, dengan penyesuaian dan/atau pembatasan pekerjaan",
                                                    3 => "Tidak Laik Bekerja",
                                                ],
                                                [
                                                    'inline' => false,
                                                    'item' => function ($index, $label, $name, $checked, $value) use ($form, $model) {
                                                        $lainnyaValue = '';
                                                        $check = null;
                                                        $masaBerlaku = [
                                                            0 => '3 Bulan',
                                                            1 => '6 Bulan',
                                                            2 => '1 Tahun',
                                                        ];

                                                        if ($model->laik_satutahun != null) {
                                                            if ($model->laik_satutahun == $value) {
                                                                $check = 'checked="checked"';
                                                            }
                                                        }

                                                        if ($index != 0) {
                                                            $content = '';

                                                            if ($index == 1) {
                                                                $return = '<div class="radio-' . $value . ' mt-2">';
                                                                $return .= '<input name="HasilPemeriksaanPhrForm[laik_satutahun]"' . $check . ' type="radio" name="' . $name . '" value="' . $value . '" tabindex="3"' . ' id="pemeriksaan-' . $value . '">';
                                                                $return .= ' <i></i>';
                                                                $return .= '<span>' . ucwords($label) . $lainnyaValue . '</span>';

                                                                $content .= '
                                                                    <div style="margin-left:30px !important; margin-top:7px">
                                                                        <span>Masa Berlaku</span>
                                                                        <div class="mt-2">
                                                                            ' . $form->field($model, 'laik_catatan_masaberlaku')->radioList($masaBerlaku, ['inline' => true])->label(false) . '
                                                                        </div>
                                                                        <span>Catatan</span>
                                                                        <div class="mt-2">
                                                                            ' . $form->field($model, 'laik_catatan_keterangan')->textarea()->label(false) . '
                                                                        </div>
                                                                    </div>
                                                                ';

                                                                $return .= $content;
                                                                $return .= '</div>';
                                                                return $return;
                                                            }

                                                            if ($index == 2) {
                                                                $return = '<div class="radio-' . $value . ' mt-2">';
                                                                $return .= '<input name="HasilPemeriksaanPhrForm[laik_satutahun]"' . $check . ' type="radio" name="' . $name . '" value="' . $value . '" tabindex="3"' . ' id="pemeriksaan-' . $value . '">';
                                                                $return .= ' <i></i>';
                                                                $return .= '<span>' . ucwords($label) . $lainnyaValue . '</span>';

                                                                $content .= '
                                                                    <div style="margin-left:30px !important; margin-top:7px">
                                                                        <span>Masa Berlaku</span>
                                                                        <div class="mt-2">
                                                                            ' . $form->field($model, 'laik_penyesuaian_masaberlaku')->radioList($masaBerlaku, ['inline' => true])->label(false) . '
                                                                        </div>
                                                                        <span>Jenis Batasan Pekerjaan</span>
                                                                        <div class="mt-2">
                                                                            ' . $form->field($model, 'laik_penyesuaian_keterangan')->textarea()->label(false) . '
                                                                        </div>
                                                                    </div>
                                                                ';

                                                                $return .= $content;
                                                                $return .= '</div>';
                                                                return $return;
                                                            }


                                                            if ($index == 3) {
                                                                $return = '<div class="radio-' . $value . ' mt-2">';
                                                                $return .= '<input name="HasilPemeriksaanPhrForm[laik_satutahun]"' . $check . ' type="radio" name="' . $name . '" value="' . $value . '" tabindex="3"' . ' id="pemeriksaan-' . $value . '">';
                                                                $return .= ' <i></i>';
                                                                $return .= '<span>' . ucwords($label) . $lainnyaValue . '</span>';

                                                                $content .= '
                                                                    <div style="margin-left:30px !important; margin-top:7px">
                                                                        <div class="mt-2">
                                                                            ' . $form->field($model, 'tidak_laik_pilihan')->radioList(["Permanen", "Sementara, dievaluasi setelah"], ['inline' => true])->label(false) . '
                                                                        </div>
                                                                        <div class="mt-2">
                                                                            ' . $form->field($model, 'tidak_laik_keterangan')->textInput()->label(false) . '
                                                                        </div>
                                                                    </div>
                                                                ';

                                                                $return .= $content;
                                                                $return .= '</div>';
                                                                return $return;
                                                            }
                                                        } else {
                                                            $return = '<div class="radio-' . $value . '">';
                                                            $return .= '<input name="HasilPemeriksaanPhrForm[laik_satutahun]"' . $check . 'type="radio" name="' . $name . '" value="' . $value . '" tabindex="3"' . ' id="pemeriksaan-' . $value . '">';
                                                            $return .= ' <i></i>';
                                                            $return .= '<span>' . ucwords($label) . $lainnyaValue . '</span>';
                                                            $return .= '</div>';

                                                            return $return;
                                                        }
                                                    }
                                                ]
                                            )->label(false); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Section Kelaikan -->

                <!-- Section Catatan Dokter Pemeriksa -->
                <div class="row">
                    <div class="col-md-12 ml-3 mt-3">
                        <div class="panel panel-default">
                            <a data-toggle="collapse" href="#catatandokterperiksa" role="button" aria-expanded="true" aria-controls="catatandokterperiksa">
                                <div class="panel-heading flex-container">
                                    <h6 class="panel-title"><b><?= Yii::t('fe', 'Catatan Dokter Pemeriksa'); ?></b></h6>
                                    <div>
                                        <ul class="icons-list">
                                            <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                                        </ul>
                                    </div>
                                </div>
                            </a>
                            <div class="panel-body collapse multi-collapse collapse in" id="catatandokterperiksa">
                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <?= $form->field($model, 'catatan_wajib_kacamata')->checkbox()->label("Harus Menggunakan Kacamata"); ?>
                                        </div>
                                        <div class="form-group">
                                            <?= $form->field($model, 'catatan_wajib_alatdengar')->checkbox()->label("Harus Menggunakan Alat Bantu Dengar"); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <?= $form->field($model, 'catatan_rekomendasi')->textarea()->label("Rekomendasi Lainnya (Jika perlu tindak lanjut)"); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Section -->
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('.select2').select2();
    $(document).ready(function() {
        $("#form-hasil-phr").docoForm('submit', {
            skipErrorNotif: false,
            success: function(data) {

            }
        });

        $("input[name='HasilPemeriksaanPhrForm[laik_satutahun]']").on('change', function () {
            let val = $("input[name='HasilPemeriksaanPhrForm[laik_satutahun]']:checked").val()
            let laik_catatan_masaberlaku = $("input[name='HasilPemeriksaanPhrForm[laik_catatan_masaberlaku]']")
            let laik_catatan_keterangan = $("textarea[name='HasilPemeriksaanPhrForm[laik_catatan_keterangan]']")
            let laik_penyesuaian_masaberlaku = $("input[name='HasilPemeriksaanPhrForm[laik_penyesuaian_masaberlaku]']")
            let laik_penyesuaian_keterangan = $("textarea[name='HasilPemeriksaanPhrForm[laik_penyesuaian_keterangan]']")
            let tidak_laik_pilihan = $("input[name='HasilPemeriksaanPhrForm[tidak_laik_pilihan]']")
            let tidak_laik_keterangan = $("input[name='HasilPemeriksaanPhrForm[tidak_laik_keterangan]']")

            if (val == 1) {
                laik_catatan_masaberlaku.prop("disabled", false)
                laik_catatan_keterangan.prop("disabled", false)
            } else {
                laik_catatan_masaberlaku.prop({"disabled": true, "checked": false})
                laik_catatan_keterangan.prop("disabled", true).val(null)
            }
        
            if (val == 2) {
                laik_penyesuaian_masaberlaku.prop("disabled", false)
                laik_penyesuaian_keterangan.prop("disabled", false)
            } else {
                laik_penyesuaian_masaberlaku.prop({"disabled": true, "checked": false})
                laik_penyesuaian_keterangan.prop("disabled", true).val(null)
            }
        
            if (val == 3) {
                tidak_laik_pilihan.prop("disabled", false)
                tidak_laik_keterangan.prop("disabled", false)
            } else {
                tidak_laik_pilihan.prop({"disabled": true, "checked": false})
                tidak_laik_keterangan.prop("disabled", true).val(null)
            }
        }).trigger('change')
    });

    var pendaftaran_cetakan= "<?= DocoHelpers::decrypt($pendaftaran_id) ?>"
    $("#btn-print-rilis-mudik").click(function(e) {
        e.preventDefault();

        var url = `/reports/viewer/hasilpemeriksaanmcuphr?pendaftaran_id=${pendaftaran_cetakan}&kategori_surat=2`;
        $(this).attr("data-target", url);
    });

    $("#btn-print-rahasia-medik").click(function(e) {
        e.preventDefault();

        var url = `/reports/viewer/hasilpemeriksaanmcuphr?pendaftaran_id=${pendaftaran_cetakan}&kategori_surat=1`;
        $(this).attr("data-target", url);
    });
</script>

<?php
$this->registerJs("
    var type = 'phr';
    var id_form = 'form-hasil-phr';
    var detail_type = 'hasil_pemeriksaan';
    var tab = 'tab-hasil-pemeriksaan-kesehatan';
", View::POS_END);
$this->registerJs($this->render('../js/_template.js'), View::POS_END);
?>
