<?php
use yii\helpers\ArrayHelper;
use app\widgets\DHAnatomiWidget;
?>

<div class="row">
    <div class="col-md-12">
        <div class="row flex-detail">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <a data-toggle="collapse" href="#kulit" role="button" aria-expanded="false" aria-controls="kulit">
                        <div class="panel-heading flex-container">
                            <h6 class="panel-title"><b><?= Yii::t('fe', 'Kulit'); ?></b></h6>
                            <div>
                                <ul class="icons-list"><li><i id="chevron" class="fa fa-chevron-down"></i></li></ul>
                            </div>
                        </div>
                    </a>
                    <div class="panel-body collapse in collapse multi-collapse" id="kulit">
                        <?= $form->field($modelFisikNew, 'kulit_umum_batas_normal')->checkbox() ?>
                        <?= DHAnatomiWidget::widget([
                            'name_id' => 'kulit',
                            'image' => '@web/media/img/img-pemeriksaan/bagian_tubuh.png',
                            'option' => ArrayHelper::map($optBagianTubuh,'bagiantubuh_id','namabagtubuh'),
                            'dataAnatomi' => $dataAnatomi,
                            'counter_data' => $counter,
                            'size_image' => 12,
                            'size_table' => 12
                        ])
                        ?>
                        <div class="col-md-6">
                        <?= $form->field($modelFisikNew, 'distribusi', [])->textInput(['class' => 'form-control input-sm']); ?>
                        <?= $form->field($modelFisikNew, 'karakteristik', [])->textInput(['class' => 'form-control input-sm']); ?>
                        </div>
                        <div class="col-md-6">
                        <?= $form->field($modelFisikNew, 'lesi', [])->textInput(['class' => 'form-control input-sm']); ?>
                        <?= $form->field($modelFisikNew, 'efloresensi', [])->textInput(['class' => 'form-control input-sm']); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
