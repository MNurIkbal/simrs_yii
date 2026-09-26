<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

?>

<div class="panel panel-flat">
  <div class="panel-heading">
      <div class="row">
          <div class="col-md-7">
              <h5 class="panel-title"><?= $title ?></h5>   
          </div>    
          <div class="col-md-4">                   
              <?= Html::dropDownList('template_id', '', array(),
                  [
                      'id' => 'list-riwayat-penyakit',
                      'class' => 'form-control select2  input-sm ',
                      'prompt' => Yii::t('fe', '-- Pilih Template --'),
                  ]
              ); ?>
          </div>   
          <div class="col-md-1">  
            <button type="button" class="btn btn-sm btn-info" id="pilih-riwayat-penyakit">Pilih</button>
          </div>   
      </div>
  </div>
    <div class="panel-toolbar clearfix">
        <div class="col-md-8">
            <?= DocoHelpers::generateToolbar([
                'save' => [
                    'attributes' => [
                        'form_id' => 'form-riwayat-penyakit',
                        'id' => 'submit-riwayat-penyakit',
                    ]
                ],
            ], ''); ?>
        </div>
        <div class="col-md-4 template" align="right">
            <button  type="button" class="btn btn-sm btn-danger" id="hapus-template-riwayat-penyakit" disabled><li class="fa fa-trash"></li> Hapus Template</button>
           <?= Html::button('<i class="fa fa-floppy-o"></i> '. Yii::t('fe', 'Simpan Template'), [
                'class' => 'btn btn-primary save-template btn-sm',
                'id' => 'save-template-riwayat-penyakit',
                'data-toggle' => 'modal',
                'data-target' => '#modal_backdrop',
                'action'      => '/mcu/pemeriksaan/modal-template?id='.$pendaftaran_id.'&type=riwayat_penyakit&modal=is_modal',true,
                'disabled'    => 'disabled'
            ]); ?>
        </div>
    </div>
    <div class="panel-body">
        <div class="row">
            <?php
            $form = ActiveForm::begin([
                'id' => 'form-riwayat-penyakit',
                'enableAjaxValidation' => false,
                'enableClientValidation' => false,
                'type' => ActiveForm::TYPE_VERTICAL,
                'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
            ]);
            ?>

            <!-- Section Pasien Khusus PHR -->
            <?= Yii::$app->controller->renderPartial('partials_prima/_pasien_phr.php', [
                'form' => $form,
                'model' => $model
            ]); ?>
            <!-- End Section Pasien Khusus PHR -->

            <!-- Section Riwayat Pekerjaan-->
            <?= Yii::$app->controller->renderPartial('partials_prima/_riwayat_pekerjaan.php', [
                'form' => $form,
                'model' => $model,
                'riwayatRekap' => $riwayatRekap,
                'currentSelectYear' => $currentSelectYear,
                'currentSelectEndYear' => $currentSelectEndYear
            ]); ?>
            <!-- End Section Riwayat Pekerjaan -->

            <!-- Section Tanda Tanda Vital-->
            <?= Yii::$app->controller->renderPartial('partials_prima/_tanda_vital.php', [
                'form' => $form,
                'model' => $model
            ]); ?>
            <!-- End Section Tanda Tanda Vital -->

            <!-- Section Keluhaan Saat Ini-->
            <?= Yii::$app->controller->renderPartial('partials_prima/_keluhansaat_ini.php', [
                'form' => $form,
                'model' => $model
            ]); ?>
            <!-- End Section Keluhaan Saat Ini -->

            <!-- Section Tingkat kebiasaan olahraga-->
            <?= Yii::$app->controller->renderPartial('partials_prima/_kebiasaan_olahraga.php', [
                'form' => $form,
                'model' => $model
            ]); ?>
            <!-- End Section Tingkat kebiasaan olahraga -->

            <!-- Section Penyakit Dalam Keluarga-->
            <?= Yii::$app->controller->renderPartial('partials_prima/_penyakit_keluarga.php', [
                'form' => $form,
                'model' => $model
            ]); ?>
            <!-- End Section Penyakit Dalam Keluarga -->

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php
$this->registerJs(" 
    var detail_type = 'riwayat_penyakit';
    var id_form = 'form-riwayat-penyakit';
    var type = 'riwayat-penyakit';
    var id = ".$pendaftaran_id.";
    var tab = 'tab-riwayat-penyakit';
", View::POS_END);
$this->registerJs($this->render('js/_template.js'), View::POS_END);
?>

<script type="text/javascript">
    $(document).ready(function() {
        setRadio('riwayat_diderita', 'riwayat_diderita_catatan');
        setRadio('riwayat_alergi', 'riwayat_alergi_catatan');
        setRadio('riwayat_dirawat_rs', 'riwayat_dirawat_rs_catatan');
        setRadio('riwayat_operasi', 'riwayat_operasi_catatan');
        setRadio('riwayat_imunisasi', 'riwayat_imunisasi_catatan');

        function setRadio(name, id) {
            var attributeName = document.getElementsByName('RiwayatPenyakitForm[' + name + ']');
            for (var i = 0, length = attributeName.length; i < length; i++) {
                if (attributeName[i].checked) {
                    if (attributeName[i].value == 0) {
                        $('#riwayatpenyakitform-' + id + '').prop("readonly", true);
                    }
                    break;
                }
            }
        }

        $(function() {
            $('input:radio[name="RiwayatPenyakitForm[riwayat_diderita]"]').change(function() {
                if ($(this).val() == 0) {
                    $('#riwayatpenyakitform-riwayat_diderita_catatan').prop("readonly", true);
                } else {
                    $('#riwayatpenyakitform-riwayat_diderita_catatan').prop("readonly", false);
                }
            });
            $('input:radio[name="RiwayatPenyakitForm[riwayat_alergi]"]').change(function() {
                if ($(this).val() == 0) {
                    $('#riwayatpenyakitform-riwayat_alergi_catatan').prop("readonly", true);
                } else {
                    $('#riwayatpenyakitform-riwayat_alergi_catatan').prop("readonly", false);
                }
            });
            $('input:radio[name="RiwayatPenyakitForm[riwayat_dirawat_rs]"]').change(function() {
                if ($(this).val() == 0) {
                    $('#riwayatpenyakitform-riwayat_dirawat_rs_catatan').prop("readonly", true);
                } else {
                    $('#riwayatpenyakitform-riwayat_dirawat_rs_catatan').prop("readonly", false);
                }
            });
            $('input:radio[name="RiwayatPenyakitForm[riwayat_operasi]"]').change(function() {
                if ($(this).val() == 0) {
                    $('#riwayatpenyakitform-riwayat_operasi_catatan').prop("readonly", true);
                } else {
                    $('#riwayatpenyakitform-riwayat_operasi_catatan').prop("readonly", false);
                }
            });
            $('input:radio[name="RiwayatPenyakitForm[riwayat_imunisasi]"]').change(function() {
                if ($(this).val() == 0) {
                    $('#riwayatpenyakitform-riwayat_imunisasi_catatan').prop("readonly", true);
                } else {
                    $('#riwayatpenyakitform-riwayat_imunisasi_catatan').prop("readonly", false);
                }
            });
        });
        $("#form-riwayat-penyakit").docoForm('submit', {
            success: function(data) {
                $('.print').attr('disabled', false);
            }
        });
        
    });
</script>