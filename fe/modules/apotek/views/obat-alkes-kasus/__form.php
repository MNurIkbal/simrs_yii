<?php

/**
 * @author Randy Vianda Putra
 * @copyright 3 January 2018 aweutist
 */

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Obat Alkes Jenis Kasus Penyakit'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
                <?php echo Breadcrumbs::widget([
                      'homeLink' => [ 
                                      'label' => Yii::t('fe', 'Home'),
                                      'url' => Yii::$app->homeUrl,
                                 ],
                      'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                   ]); 
                ?>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-body" style="padding:10px;">
                <div class="col-md-12 panel panel-default" style="margin-top:10px;">
                    <div class="panel-heading">
                        <div class="panel-title">
                            <h3>Data</h3>
                        </div>
                    </div>
                    <div class="panel-body">
                        <?php
                            $form = ActiveForm::begin([
                                'id' => 'update-form', 
                                'options' => ['class' => 'form-horizontal'],
                                // 'action' =>['obat-alkes-kasus/update']
                            ]); 
                        ?>
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= Yii::t('fe', 'Obat alkes') ?></label>
                            <div class="col-lg-8">
                                <div class="input-group">
                                    <?= Html::activeDropDownList($model, 'obatalkes_id',
                                        ArrayHelper::map([], 'obatalkes_id', 'name'), [
                                            'class' => 'select2 autoObat',
                                            'prompt' => Yii::t('fe', '-- Pilih --')
                                        ]) 
                                    ?>
                                    <?= Html::hiddenInput('ObatAlkesKasusForm[obatalkes_id]', $id2,
                                        [
                                            'class' => 'id_obat'
                                        ]);
                                    ?>
                                    <span class="input-group-addon">
                                        <?php
                                            echo Html::a('<i class="fa fa-list-ul"></i>
                                                <i class="fa fa-search"></i>',
                                                Url::home().'apotek/obat-alkes-kasus/list-obat',[
                                                'data-toggle' => 'modal',
                                                'data-target' => '#modal_backdrop'
                                            ]);
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-lg-3 control-label"><?= Yii::t('fe', 'Jenis kasus penyakit') ?></label>
                            <div class="col-lg-8">
                                <div class="input-group">
                                    <?= Html::activeDropDownList($model, 'jeniskasuspenyakit_id',
                                        ArrayHelper::map([], 'jeniskasuspenyakit_id', 'name'), [
                                            'class' => 'select2 autoKasus',
                                            'prompt' => Yii::t('fe', '-- Pilih --')
                                        ])
                                    ?>
                                    <?= Html::hiddenInput('ObatAlkesKasusForm[jeniskasuspenyakit_id]', $id,
                                        [
                                            'class' => 'id_kasus'
                                        ]); 
                                    ?>
                                    <span class="input-group-addon">
                                        <?php
                                            echo Html::a('<i class="fa fa-list-ul"></i>
                                                <i class="fa fa-search"></i>',
                                                Url::home().'apotek/obat-alkes-kasus/list-penyakit',[
                                                'data-toggle' => 'modal',
                                                'data-target' => '#modal_backdrop'
                                            ]);
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 col-md-offset-3">
                            <?= Html::submitButton('<i class="fa fa-floppy-o"></i> '. Yii::t('fe', "Simpan"), ['class' => 'btn bg-teal']); ?>
                            <?= Html::resetButton('<i class="fa fa-refresh"></i> '. Yii::t('fe', "Ulang"),['class' => 'btn btn-lime-green reset']); ?>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
    $this->registerCss($this->render('../assets/css/apotek.css'));
    $this->registerJs($this->render('../assets/js/obat_alkes_kasus.js'));
?>