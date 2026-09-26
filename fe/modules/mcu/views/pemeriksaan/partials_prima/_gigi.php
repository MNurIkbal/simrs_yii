<?php
use yii\helpers\ArrayHelper;
use app\widgets\DHAnatomiWidget;

?>

<div class="row">
    <div class="col-md-12">
        <div class="row flex-detail">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <a data-toggle="collapse" href="#gigi" role="button" aria-expanded="false" aria-controls="gigi">
                        <div class="panel-heading flex-container">
                            <h6 class="panel-title"><b><?= Yii::t('fe', 'Gigi'); ?></b></h6>
                            <div>
                                <ul class="icons-list"><li><i id="chevron" class="fa fa-chevron-down"></i></li></ul>
                            </div>
                        </div>
                    </a>
                    <div class="panel-body collapse in collapse multi-collapse" id="gigi">
                        <?= DHAnatomiWidget::widget([
                            'name_id' => 'gigi',
                            'image' => '@web/media/img/img-pemeriksaan/odontogram.png',
                            'option' => ArrayHelper::map($optBagianTubuhGigi,'bagiantubuhdetail_id','nama_bagiantubuh'),
                            'dataAnatomi' => $dataAnatomiGigi,
                            'counter_data' => $counterGigi,
                            'size_image' => 12,
                            'size_table' => 12
                        ])
                        ?>
                        <br><br>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
