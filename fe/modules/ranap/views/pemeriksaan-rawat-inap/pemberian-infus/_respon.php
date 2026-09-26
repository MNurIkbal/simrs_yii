<?php
use app\components\DocoHelpers;

use kartik\widgets\ActiveForm;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

?>

<div class="modal-header bg-inverse">
    <button type="button" class="close close-modal-respon" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Tambah Respon Terapi</h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <?php $form = ActiveForm::begin([
                'id' => 'respon-infus-form',
                'action' => Url::to(['/ranap/pemeriksaan-rawat-inap/save-respon-infus', 'id' => $id, 'pemberianinfus_id' => $pemberianinfus_id,]),
                'enableClientValidation' => false,
                'formConfig' => ['deviceSize' => ActiveForm::SIZE_SMALL]
            ]);?>
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group highlight-addon field-tgl_respon required">
                        <label class="control-label has-star" for="tgl_respon">Tanggal Pengecekan</label>
                        <?= Html::textInput('tgl_respon_temp', $now, [
                            'id' => 'tgl_respon',
                            'class' => 'form-control',
                            'disabled' => true,
                        ])?>
                        <div class="help-block"></div>
                    </div>
                    <?= Html::hiddenInput('tgl_respon', $tgl_pengecekan)?> 
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group highlight-addon field-respon-respon required">
                        <label class="control-label has-star" for="respon">Respon Terapi</label>
                        <?= Html::textarea('respon', null, [
                            'id' => 'respon',
                            'class' => 'form-control',
                        ])?> 
                        <div class="help-block"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-right">
                    <button type="submit" id="save-respon-infus" class="btn btn-success btn-xs btn-labeled"><b><i class="fa fa-save"></i></b> Simpan</button>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<?php
    $this->registerJs($this->render('_respon.js'), View::POS_END);
?>