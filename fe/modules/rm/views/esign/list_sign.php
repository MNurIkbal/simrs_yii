<?php
use app\components\DocoHelpers;
use yii\web\View;
use yii\widgets\Breadcrumbs;

$this->title = Yii::t('fe', 'List Penandatanganan Dokumen');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rekam Medik'), 'url' => ['/rm']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row body">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= $this->title ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                        'sign' => [
                            'title' => \Yii::t('fe', 'Tanda Tangan'),
                            'icon' => 'fa fa-file-o',
                            'attributes' => [
                                'data-options' => 'click',
                                'id' => 'btn-sign',
                            ]
                        ],
                        'sign-all' => [
                            'title' => \Yii::t('fe', 'Tanda Tangani Semua'),
                            'icon' => 'fa fa-files-o',
                            'attributes' => [
                                'data-options' => 'click',
                                'id' => 'btn-sign-all',
                            ]
                        ],
                    ]);
                ?>
            </div>
            <div class="panel-body">
                <!--filter-->
                <table class="table table-striped table-condensed table-hover" id="table-esign" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="1"><?=\Yii::t("fe", "Rownum");?></th>
                            <th><?=\Yii::t("fe", "Nama Dokumen");?></th>
                            <th><?=\Yii::t("fe", "Pasien");?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
$this->registerJs($this->render('js/table.js'), View::POS_END);