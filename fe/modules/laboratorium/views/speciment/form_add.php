<?php

/**
 * @author Randy Vianda Putra
 * @todo Modal Input speciment laboratorium
 * @copyright 11 Juli 2018 aweutist
 */

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
use app\components\DocoHelpers;
use app\components\DocoConstants;

?>
<style type="text/css">
.fixed-panel {
  min-height: 10px;
  max-height: 120px;
  overflow-y: scroll;
},
</style>
<!-- Start avtive form -->
<?php $form = ActiveForm::begin([
    'id' => 'form',
    'action' => '/laboratorium/speciment/save-cache',
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]) ?>

<input type="hidden" id="instalasi_id" name="instalasi_id" value="<?= $instalasi_id ?>">
<!-- Modal header -->
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= Yii::t('fe', 'Pilih speciment') ?></h5>
</div>

<!-- Modal body -->
<div class="modal-body">
    <div class="form-group field-specimentform-tanggal required">
        <label for="specimentform-tanggal" class="col-lg-4 control-label text-left">
            <?= Yii::t('fe', 'Tanggal'); ?>
        </label>
        <div class="col-lg-6">
            <?php
                echo DateTimePicker::widget([
                    'name' => 'SpecimentForm[tanggal]',
                    'id' => 'specimentform-tanggal',
                    'class' => 'tanggal',
                    'type' => DateTimePicker::TYPE_COMPONENT_APPEND,
                    'readonly' => true,
                    'language' => 'id',
                    'value' => date('d M Y H:i:s'),
                    'pluginOptions' => [
                        'format' => 'dd M yyyy HH:ii:ss',
                        'locale' => 'en',
                        'language' => 'en',
                        'showMeridian' => true,
                        'autoclose' => true,
                        'todayBtn' => true,
                        'endDate' => date('Y-m-d H:i:s'),
                        'startDate' => $tanggal_masuk
                    ]
                ]);
            ?>
        </div>
    </div>
    <?= 
        $form->field($model, 'samplelab_id', [
            'labelOptions' => ['class' => 'text-left']
        ])->dropdownList($data_sample, [
            'class' => 'form-control select2 input-sm sample',
            'prompt' => \Yii::t('fe', '-- Pilih --')
        ]); 
    ?>
    <?= $form->field($model, 'jumlah', ['labelOptions' => ['class' => 'text-left']])->textInput(['class' => 'form-control input-sm docoNumberOnly']) ?>
    <?= 
        $form->field($model, 'satuanlab_id', [
            'labelOptions' => ['class' => 'text-left']
        ])->dropdownList($data_satuan, [
            'class' => 'form-control select2 input-sm satuan',
            'prompt' => \Yii::t('fe', '-- Pilih --')
        ]);
    ?>
    <?= $form->field($model, 'keterangan', ['labelOptions' => ['class' => 'text-left']])->textarea(['rows' => '1', 'class' => 'form-control'])?>
        
        <br>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h6 class="panel-title text-bold"><?= Yii::t('fe', 'List Pemeriksaan') ?>
                </h6>
            </div>
            <div class="panel-body fixed-panel">
                <div class="form-group">
                    <div class="col-md-6 <?= $required_tindakan ?>">
                    <label for="" class="control-label">List Tindakan</label>
                    <br>
                    <input type="hidden" id="specimentform-pemeriksaan" name="SpecimentForm[pemeriksaan]" value="">
                    <div class="col-md-12">
                        <div class="row" style="margin-left: 0px; font-weight: bold;">
                            <input type="checkbox" id="checkAllNonPaket"/> <label for="checkAllNonPaket">Check All</label>
                        </div>
                        <?php
                            \Yii::error($list_non_paket);
                            if (!empty($list_non_paket)) {
                                foreach ($list_non_paket as $key => $value) {
                                    echo '
                                        <input 
                                            class="checkboxPemeriksaan"
                                            type="checkbox"
                                            name="SpecimentForm[pemeriksaan][]"
                                            value="' . $value['daftartindakan_nama'] . '"
                                            data-val="' . $value['tindakanpelayanan_id'] . '"
                                            data-tindakan="' . $value['daftartindakan_id'] . '"
                                            data-group="'. $value['tindakanpelayanan_id'] . '-' . $value['daftartindakan_id'] .'"
                                            class = "daftartindakan_id"
                                        > '.$value['daftartindakan_nama'] . 
                                    '<br>';
                                }
                            }
                        ?>
                    </div>
                    </div>
                    <div class="col-md-6 <?= $required_paket ?>">
                        <label for="" class="control-label">List Paket</label><br>
                        <input type="hidden" id="specimentform-list-paket" name="SpecimentForm[list_paket]" value="">
                        <div class="col-md-12">
                        <ol class="topnav">
                        <?php
                        if (!empty($list_paket)) {
                            foreach ($list_paket as $header => $detail_header) {
                                echo '<li><a>'. $header .'</a>';
                                if(!empty($detail_header)) {
                                    foreach ($detail_header as $key => $detail2) {
                                        if(!empty($detail2) && is_array($detail2)) {
                                            if(isset($detail2[0])) {
                                                echo '<ul><li><a>'. $key .'</a><ul>';
                                                echo '<li style="list-style: none;">';
                                                foreach ($detail2 as $detail3) {
                                                    $jenispemeriksaanlab_nama = $detail3['jenispemeriksaanlab_nama'];
                                                    $disabled = ($jenispemeriksaanlab_nama != null) ? '' : 'disabled';
                                                    echo '<input 
                                                        type="checkbox"
                                                        name="SpecimentForm[pemeriksaan][]"
                                                        value="' . $detail3['daftartindakan_nama'] . '" 
                                                        '.$disabled.' 
                                                        data-val="' . $detail3['tindakanpelayanan_id'] . '"
                                                        data-tindakan="' . $detail3['daftartindakan_id'] . '"
                                                        data-group="'. $detail3['tindakanpelayanan_id'] . '-' . $detail3['daftartindakan_id'] .'"
                                                    > '.$detail3['daftartindakan_nama'] . 
                                                    '<br>';
                                                }
                                                echo '</li></ul></li></ul>';
                                            }
                                            else {
                                                $jenispemeriksaanlab_nama = $detail2['jenispemeriksaanlab_nama'];
                                                $disabled = ($jenispemeriksaanlab_nama != null) ? '' : 'disabled';

                                                echo '<br><input 
                                                        type="checkbox"
                                                        name="SpecimentForm[pemeriksaan][]"
                                                        value="' . $detail2['daftartindakan_nama'] . '" 
                                                        '.$disabled.' 
                                                        data-val="' . $detail2['tindakanpelayanan_id'] . '"
                                                        data-tindakan="' . $detail2['daftartindakan_id'] . '"
                                                        data-group="'. $detail2['tindakanpelayanan_id'] . '-' . $detail2['daftartindakan_id'] .'"
                                                    > '.$detail2['daftartindakan_nama'] . 
                                                '<br>';
                                            }
                                        }
                                    }
                                }
                                echo '</li>';
                            }
                        }
                        ?>
                        </li></ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?= Html::hiddenInput('pasienmasukpenunjang_id', $pasienmasukpenunjang_id, ['class' => 'pasienmasukpenunjang_id']); ?>
</div>

<!-- Modal footer -->
<div class="modal-footer">
    <button class="btn bg-teal btn-sm" id="btn-save">
      <i class='fa fa-floppy-o'></i>
      Simpan
    </button>
</div>
<?php ActiveForm::end(); ?>
<!-- Javascript -->
<script type="text/javascript">
$('.topnav > li a').click(function () {
  $(this).parent('li').siblings('li').children('ul').hide();
  $(this).siblings('ul').toggle().children().show();
});

$(()=>{
  $('#checkAllNonPaket').click(()=>{
    const checkAllValue = $('#checkAllNonPaket:checked').val() ? true : false;
    console.log(`Checked ${checkAllValue}`)
    $('.checkboxPemeriksaan').each(function (i) {
      $(this).prop('checked',checkAllValue)
    });
  })
})

$(document).on('click', '#btn-save', function () {
  let data = new FormData();
  let dataPost = $("#form").serializeArray();
  let invalid = false;
  let val_checked = [];
  let val_tindakan = [];
  let text_checked = [];
  let val_group = [];
  let pasienmasukpenunjang_id = $('.pasienmasukpenunjang_id').val();
  let samplelab_id = $('.sample').val();
  let id_sample_list = $('.list_sample').get();
  for (let i = 0; i < id_sample_list.length; i++) {
    let id_sample = $(id_sample_list[i]).attr('data-sample');
    if (samplelab_id == id_sample) {
      invalid = true;
    }
  }

  let sample_nama = $('.sample').find(':selected').text();
  let satuan_nama = $('.satuan').find(':selected').text();
  $(':checkbox:checked').each(function (i) {
    val_checked[i] = $(this).attr('data-val');
    val_tindakan[i] = $(this).attr('data-tindakan');
    val_group[i] = $(this).attr('data-group');
    text_checked[i] = $(this).val();
  });
  dataPost.push({ name: 'SpecimentForm[tindakanpelayanan_id]', value: val_checked });
  dataPost.push({ name: 'SpecimentForm[daftartindakan_id]', value: val_tindakan });
  dataPost.push({ name: 'SpecimentForm[group]', value: val_group });
  dataPost.push({ name: 'SpecimentForm[nama_pemeriksaan]', value: text_checked });
  dataPost.push({ name: 'SpecimentForm[nama_sample]', value: sample_nama });
  dataPost.push({ name: 'SpecimentForm[satuan_nama]', value: satuan_nama });
  dataPost.push({ name: 'SpecimentForm[pasienmasukpenunjang_id]', value: pasienmasukpenunjang_id });

  if (!invalid) {
    $("#form").docoForm("submit", {
      data: dataPost,
      success: function (data) {
        $("#form")[0].reset();
        let value = data.data;
        let response = {};
        response[value.posisi] = value;
        transLab = $.extend({}, transLab, response);;
        appendSample(transLab)
        $('#modal_backdrop').modal('toggle');
      }
    });
  } else {
    docoNotification("warning", i18next.t("Perhatian"), i18next.t("Nama sample telah di input"));
    return false;
  }
});
</script>