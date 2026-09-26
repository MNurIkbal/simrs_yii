<?php
    use yii\web\View;
    use app\components\DHtml;
    use yii\widgets\Breadcrumbs;
    use app\components\DocoHelpers;
    use app\components\DocoConstants;

    $this->title = DHtml::getTitleMenu();
    $this->params['breadcrumbs'][] = ['label' => 'Fisioterapi', 'url' => ['/fisioterapi']];
    $this->params['breadcrumbs'][] = ['label' => 'Informasi', 'url' => ['/fisioterapi']];
    $this->params['breadcrumbs'][] = $title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon") ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= $title ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])) ?>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <?= Yii::$app->controller->renderPartial('partials/tabs') ?>
                <?= Yii::$app->controller->renderPartial('partials/table', ['verifyButton' => $verifyButton]) ?>
            </div>
        </div>
    </div>
</div>

<?php
    $dataFilter = [
        'listDokter' => $listDokter,
        'listStatusPeriksa' => $listStatusPeriksa,
        'listStatusRajal' => $listStatusPeriksaRajal,
        'listCaraBayar' => $listCaraBayar,
        'listStatusBayar' => $listStatusBayar,
        'listjenisTerapi' => $listjenisTerapi,
        'statusPulang' => DocoConstants::STATUS_PULANG
    ];
    $this->registerJsVar('dataFilter', $dataFilter);
    $this->registerJs($this->render("js/index.js"), View::POS_END, 'js');
?>
