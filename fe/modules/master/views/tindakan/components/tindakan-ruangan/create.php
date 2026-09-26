<?php

/**
 * @author Randy Vianda Putra
 * @todo Master Tindakan Ruangan
 * @copyright 26 April 2018 aweutist
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\modules\master\models\CaraBayarForm;
use Doco\master\controllers\CaraBayarController;
use kartik\widgets\ActiveForm;


// $this->title = $title;

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
                                'data-render' => 'tindakan-ruangan',
                                'data-tab' => 'tab-tindakan-ruangan',
                                'data-target' => '#view-tindakan-ruangan',
                            ]
                        ],
                    ],'#table-tindakan-ruangan');
                ?>
            </div>
            <div class="panel-body">
                <?php
                    $form = ActiveForm::begin([
                        'id' => 'antrian-form',
                        'enableAjaxValidation' => false,
                        'enableClientValidation' => false,
                        // 'type' => ActiveForm::TYPE_INLINE,
                        'type' => ActiveForm::TYPE_HORIZONTAL,
                        'formConfig' => [
                            'labelSpan' => 3,
                            'deviceSize' => ActiveForm::SIZE_SMALL
                        ],
                        'options' => [
                            'role' => 'form',
                            'enctype'=>'multipart/form-data'
                        ]
                    ]);
                    ?>
                    <?=Html::activeHiddenInput($model, 'ruangan_id', ['value'=>$id])?>
                    <h5 class="panel-title"><b>
                        <br>
                        <?=Yii::t('fe', "Mapping").' '.Yii::t('fe', "Tindakan").' '.Yii::t('fe', "Ruangan").' '.$ruangan_nama ?></b>
                    </h5>
                    <div class="row">
                        <div class="col-md-12">
                            <hr>
                            <div class="form-group">
                                <label class="control-label col-sm-2"><?=Yii::t('fe', 'Nama tindakan')?></label>
                                <div class="col-md-3">
                                    <?=Html::activeDropdownList($model, 'daftartindakan_id', [], ['class'=>'form-control select-daftartindakan select2'])?>
                                </div>
                            </div>
                            <br>
                            <div class="col-md-12">
                                <table class="table table-striped table-condensed table-hover table-responsive" id="table-create-tr-<?=$id?>">
                                    <thead>
                                        <tr class="bg-inverse">
                                            <th width="1">No</th>
                                            <th><?=\Yii::t("fe", "Kode tindakan");?></th>
                                            <th><?=\Yii::t("fe", "Nama tindakan");?></th>
                                            <th><?=\Yii::t("fe", "Kelompok tindakan");?></th>
                                            <th><?=\Yii::t("fe", "Default");?></th>
                                            <th><?=\Yii::t("fe", "Aksi")?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center" colspan="6"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
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
            var id = '".$id."'
            var tableCreateTr
        ".$this->render('js/tindakan-ruangan.js'), View::POS_END);
?>