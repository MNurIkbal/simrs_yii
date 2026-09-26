
<?php

use app\components\DocoConstants;
use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\web\View;
use kartik\widgets\ActiveForm;
?>
<style>
    .iconforalert {
        font-size: 22px;
    }

    .info-success, .info-success .close {
        color: #205823;
    }
    .info {
        display: flex;
        column-gap: 18px;
        position: relative;
        padding-left: 20px;
        padding-right: 20px;
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 3px;
    }
    .info-success {
        background-color: #E8F5E9;
        border-color: #4CAF50;
        color: 'white';
    }
</style>

<?php
$form = ActiveForm::begin([
    'id' => 'satusehat-sinkronisasi-form',
    'enableAjaxValidation' => false,
    'enableClientValidation' => false,
    'type' => ActiveForm::TYPE_VERTICAL,
]);
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<div class="modal-body">
    <div class='row'>
        <div>
            <div class="info info-success">
                <div class="iconforalert">
                    <i class="fa fa-info-circle"></i>
                </div>
                <div>
                    <div>
                        <strong>Jumlah Singkronisasi</strong>
                    </div>
                    <div>
                        <p class="m-0">
                            Jumlah maksimal data yang akan dikirimkan saat satu kali sikron data: 100 data
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <?= $form->field($model, 'jenis_sinkronisasi', [
                'labelOptions' => ['class' => 'jenis_sinkronisasi']
                ])->radioList($jenis_sinkronisasi, ['itemOptions' => [
                    'class' => 'pilih_sync'
                ]]); 
            ?>
        </div>
        <div class="col-sm-6">

        </div>
    </div><br>
    <div class="row">
        <center><span class="populate-data" style="font-size:16px;font-weight:bold;margin-bottom:10px;"></span></center>
        <div class="progress" style="margin-left: 12px;display:none;">
            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"  aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                <span class="label-persentase">0</span>%
            </div>
        </div>
        <span class="help-block label-progress" style="margin-left: 12px;"></span>
    </div><br>
    <div class="row">
        <div class="col-sm-8">
            <strong><p class="info-select-master font-weight-bold"></p></strong>
        </div>
        <?php
            echo "<div class='text-right'>";
            echo Html::button('<i class="fa fa-paper-plane"></i> ' . Yii::t('fe', 'Sync'), [
                'class'=>'btn btn-info btn-sync-satusehat']);
            echo '&nbsp;&nbsp;&nbsp;&nbsp;';
            echo Html::button('<i class="fa fa-arrow-left"></i> ' . Yii::t('fe', 'Kembali'), ['class'=>'btn bg-slate btn-kembali', 'data-dismiss' => 'modal']);
            echo "</div>";
        ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?php
$this->registerJs('

var list_jumlah_data = '.json_encode($list_jumlah_data).';
',View::POS_END,'b-index');
?>
<?php $this->registerJs($this->render('js/_modal_sinkronisasi.js'), View::POS_END); ?>