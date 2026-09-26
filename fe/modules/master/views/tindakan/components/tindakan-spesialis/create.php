<?php

use yii\web\View;
use yii\helpers\Html;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-toolbar clearfix">
                <?=
                DocoHelpers::generateToolbar([
                    'kembali' => [
                        'title' => \Yii::t('fe', 'Kembali'),
                        'icon' => 'fa fa-arrow-left',
                        'attributes' => [
                            'class' => 'spa',
                            'data-options' => 'click',
                            'data-render' => 'tindakan-spesialis',
                            'data-tab' => 'tab-tindakan-spesialis',
                            'data-target' => '#view-tindakan-spesialis',
                        ]
                    ],
                ], '#table-tindakan-spesialis');
                ?>
            </div>
            <div class="panel-body">
                <?php
                $form = ActiveForm::begin([
                    'enableAjaxValidation' => false,
                    'enableClientValidation' => false,
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => [
                        'labelSpan' => 3,
                        'deviceSize' => ActiveForm::SIZE_SMALL
                    ],
                    'options' => [
                        'role' => 'form',
                        'enctype' => 'multipart/form-data'
                    ]
                ]);
                ?>
                <?= Html::activeHiddenInput($model, 'spesialis_id', ['value' => $id]) ?>
                <h5 class="panel-title"><b>
                        <br>
                        <?= Yii::t('fe', "Mapping") . ' ' . Yii::t('fe', "Tindakan") . ' ' . Yii::t('fe', "Spesialis") . ' ' . $spesialis_nama ?></b>
                </h5>
                <div class="row">
                    <div class="col-md-12">
                        <hr>
                        <div class="form-group">
                            <label class="control-label col-sm-2"><?= Yii::t('fe', 'Nama tindakan') ?></label>
                            <div class="col-md-3">
                                <?= Html::activeDropdownList($model, 'daftartindakan_id', [], ['class' => 'form-control select-daftartindakan select2']) ?>
                            </div>
                        </div>
                        <br>
                        <div class="col-md-12">
                            <table class="table table-striped table-condensed table-hover table-responsive" id="table-create-ts-<?= $id ?>">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th width="1">No</th>
                                        <th><?= \Yii::t("fe", "Kode tindakan"); ?></th>
                                        <th><?= \Yii::t("fe", "Nama tindakan"); ?></th>
                                        <th><?= \Yii::t("fe", "Kelompok tindakan"); ?></th>
                                        <th><?= \Yii::t("fe", "Aksi") ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center" colspan="5"><?= \Yii::t("fe", "Data tidak ditemukan."); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs("
            var id = '" . $id . "'
            var tableCreateTs
        " . $this->render('js/tindakan-spesialis.js'), View::POS_END);
?>
