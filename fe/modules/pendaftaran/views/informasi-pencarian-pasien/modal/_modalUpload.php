<?php 
    use yii\web\View;
    use yii\helpers\Html;
    use kartik\widgets\DatePicker;
    use kartik\widgets\DepDrop;
    use kartik\widgets\Select2;
?>

<style type="text/css">
    .datepicker > div {
        display:block;
    }
</style>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<hr>
<div class="modal-body">
<div class= "row">
        <div class="row">
            <div class="col-md-2">
            <label><?= Yii::t('fe', 'Tanggal Kunjungan'); ?></label>
            <?= DatePicker::widget([
            	'name' => 'tanggal_dok', 
            	'value' => date('d-M-Y'),
                'id' => 'tanggal_dok',
                'language' => 'en',
                'options' => [
                    'placeholder' => 'Tanggal Kunjungan...',
                    'class' => 'form-control',
                ],
            	'pluginOptions' => [
            		'format' => 'dd-M-yyyy',
            		'todayHighlight' => true,
                    'autoclose' => true,
                    'endDate' => "0d",
            	]
            ]); ?>
            </div>                                  
            <div class="col-md-2">
            <label><?= Yii::t('fe', 'Ruangan'); ?></label>
               <?= Html::dropDownList('Ruangan',null,$ruangan,[
                     'class' => 'select2',
                     'id' => 'ruangan_ids',
                     'prompt' => '— Pilih —',
                     'options' =>[
                        'label' =>Yii::t('fe', 'Pendaftaran'),   
                     ]
                  ]);
               ?>
            </div>            
            <div class="col-md-2">
            <label><?= Yii::t('fe', 'Dokter'); ?></label>
               <?= Html::dropDownList('Dokter',null,$dokter,[
                     'class' => 'select2',
                     'id' => 'pegawai_ids',
                     'prompt' => '— Pilih —',
                     'options' =>[
                        'label' =>Yii::t('fe', 'Pendaftaran'),   
                     ]
                  ]);
               ?>
            </div>            
        </div>
    </div>
    <div class="row">
        <div id='content-upload-dokumen'>
        </div>
    </div>
</div>

<script>
    pasien_id = '<?= $pasien_id ?>'
    $(document).ready(() => {
        $("#content-upload-dokumen").docoLoad({
          url:"/api/upload-dokumen/tab-upload-dokumen?id="+pasien_id
          +"&pasienadmisi_id=null"
          +"&parent_id=dokumen-tab-ranap"
          +"&is_pasienid=null",
          dataType:"html",
          success: function(data){},
        });
   });
</script>