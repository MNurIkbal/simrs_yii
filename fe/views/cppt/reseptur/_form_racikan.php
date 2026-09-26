<?php
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use yii\web\View;
?>

<div class="row racikan-container">
    <div class="col-sm-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title"><?=Yii::t('fe', 'Racikan')?></h5>
                <div class="heading-elements">
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 item-container-racikan">
                        <div class="col-md-1">Racikan  <i class="fa fa-info-circle" data-placement="right" data-toggle="tooltip" data-html="true" title="Field ini juga bisa dijadikan sebagai catatan untuk <br/> petugas farmasi jika ada obat yang tidak tersedia." aria-hidden="true"></i></div>
                        <div class="col-md-11"><textarea id="racikan" class="form-control input-sm" name="racikan" rows="5" cols="5"></textarea></div>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type='button' id="btn-tambah-racikan" class='btn btn-labeled btn-info btn-xs' disabled="true">
                            <b><i class='fa fa-plus'></i></b> Tambah
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs($this->render('js/_racikan.js'), View::POS_END); ?>
