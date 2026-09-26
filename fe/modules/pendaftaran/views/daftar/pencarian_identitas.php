<?php

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use kartik\widgets\ActiveForm;
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=  Yii::t('fe', 'Pencarian Identitas') ?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="panel-body">
            <?php $form = ActiveForm::begin([
                'id' => 'form',
                'type' => ActiveForm::TYPE_VERTICAL,
                'formConfig' => [
                    'labelSpan' => 5,
                    'deviceSize' => ActiveForm::SIZE_SMALL
                ]
            ]) ?>
            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($model, 'jenis_pencarian')
                        ->radioList(
                            [
                                '1'=> Yii::t('fe', 'No. Kartu'),
                                '2'=> Yii::t('fe', 'NIK'),
                            ],
                            ['id'=>'jenis_pencarian', 'name'=>'jenis_pencarian', 'inline'=>true]
                        ); 
                    ?>
                    <div class="no_kartu">
                        <?= $form->field($model, 'no_kartu', [
                                'inputOptions' => [
                                    'id' => 'kartu_bpjs',
                                    'class' => 'form-control input-sm'
                                ]
                            ])->textInput(['class' => 'no_kartu']) ?>
                    </div>
                    
                    <div class="nik">
                        <?= $form->field($model, 'nik', [
                                'inputOptions' => [
                                    'id' => 'nik',
                                    'class' => 'form-control input-sm'
                                ]
                            ])->textInput(['class' => 'nik']) ?>
                    </div>
                    
                </div>
            </div>
            
            <button type="button" class="btn btn-info btn-sm btn-cari"><?=Yii::t('fe','Cari')?></button>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <table id="tb-pencarian-identitas" class="table table-striped table-condensed table-hover" style="width:100%">
        <thead>
            <tr class="bg-inverse">
                <th><?= Yii::t("fe", "No") ?></th>
                <th><?= Yii::t("fe", "No. Rujukan") ?></th>
                <th><?= Yii::t("fe", "Tanggal Rujukan") ?></th>
                <th><?= Yii::t("fe", "No. Kartu") ?></th>
                <th><?= Yii::t("fe", "Nama") ?></th>
                <th><?= Yii::t("fe", "PPK Perujuk") ?></th>
                <th><?= Yii::t("fe", "Subspesialis") ?></th>
                <th><?= Yii::t("fe", "Aksi") ?></th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<?php $this->registerJs($this->render('js/pencarian_identitas.js'));?>