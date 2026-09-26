<?php

/**
 * @author Randy Vianda Putra
 * @copyright 5 April 2018 aweutist
 * @modify : ali.padilah@docotel.com 1/8/2018
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

<div class="site-index">
    <div class="data-module">
        <!-- List styles -->
        <h2 class="content-group text-semibold">
            <?= strtoupper($this->title)?>
        </h2>

        <div class="row pd-20">
            <?php foreach ($list_layar as $key => $value) { ?>
                 <div class="col-md-3 plan">
                    <a class="text-default pilih_antrian" data-id-decrypt="<?= $decryptJenisId ?>" data-id="<?= $jenis_id ?>">
                        <label class="lbl-on-cb">
                            <h4><b>
                            <?= \Yii::t('fe', 'Antrian') ?> <?= $value['fungsi_antrian'] ?>
                            </b></h4>
                        </label>
                    </a>
                </div>
            <?php }?>
        </div>
    </div>
</div>


<div class="modal fade modal-step-antrian" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            ...
        </div>
    </div>
</div>

<?php
    $this->registerCss($this->render('../assets/css/antrian-kasir.css'));
    $this->registerJs($this->render('../assets/js/antrian-kasir.js'));
    $this->registerCss($this->render('../assets/css/antrian.css'));
?>
