<?php

/**
 * @author Randy Vianda Putra
 * @modify ali.padilah@docotel.com
 * @copyright 9 April 2018 aweutist
 */

use app\components\DocoHelpers;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use yii\web\View;
use yii\helpers\Url;

$this->title = $judulLayarAntrian;
// $this->context->layout = 'antrian';
?>

<div class="col-sm-12 text-right" style="font-size: 20px;">
    <a class="fa fa-chevron-up mr-2 hd-up" onclick="hideHeader()" ></a>
    <a class="fa fa-chevron-down mr-2 hd-down" onclick="showHeader()" style="display:none"></a>
</div>

<?php
$form = ActiveForm::begin([
    'id' => 'ambil-antrian-form-penunjang',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]);
?>

<div class="site-index">
    <div class="data-module">
        <!-- List styles -->
        <h2 class="content-group text-semibold">
            <?= strtoupper($this->title)?>
        </h2>

        <div class="row pd-20">
            <?php foreach ($list_layar as $key => $value) { ?>
                 <div class="col-md-3 plan">
                    <a class="text-default pilih_antrian" data-id-decrypt="<?= $decryptJenisId ?>" data-id="<?= $jenis_id ?>" data-fungsiantrian="<?= $value['fungsiantrian_id'] ?>" data-ruangan = "<?= $value['ruangan_id'] ?>" data-instalasi = "<?= $value['instalasi_id'] ?>" >
                        <label class="lbl-on-cb">
                            <h4><b>
                            <?= \Yii::t('fe', 'Antrian') ?> <?= $value['instalasi_nama'] ?>
                            </b></h4>
                        </label>
                    </a>
                </div>
            <?php 
        } ?>
        </div>
        
    </div>
</div>
<?php ActiveForm::end(); ?>


<div class="modal fade modal-step-antrian" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            ...
        </div>
    </div>
</div>

<script type="text/javascript">
    function setCheck (index) {
        $(':checkbox', index).trigger('click');
    }

    function clearFind () {
        console.log('clear');
        $('#pasien_info').html('');
        $("#type_norm").val('');
    }

    function cetakFind () {
        console.log('cetak');
        $('input[name="cetak-antrian"]:checked').each(function() {
           console.log(this.value);
        });
    }
</script>

<?php
    $this->registerCss($this->render('../assets/css/antrian.css'));
    $this->registerJs($this->render('../assets/js/antrian-farmasi.js'));
?>
