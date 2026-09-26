<?php 
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
?>
<style type="text/css">
textarea {
   resize: none;
}
</style>
<div class="panel panel-default panel-bordered" style="height:auto;">

    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <label class="text-left control-label" style="font-size: 15px;font-weight: bold;">
                    <b>Catatan</b>
                </label>
                <div class="">
                    <?= $form->field($model, 'catatan', [
                    ])->textarea([
                        'class' => 'form-control input-sm',
                        'autocomplete' => "off",
                        'rows' => 3,
                        'id' => 'catatan_pembayaran',
                    ])->label(false); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs($this->render('js/kasir.js'), View::POS_END);
?>
