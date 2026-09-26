<?php
    /**
     * @author Andri Amirul Sonjaya (andri.amirul@sirs.co.id)
     * A Product of PT Citraraya Nusatama
     * Powered by Sirs
     */

    use yii\web\View;
    use app\components\DHtml;
    use yii\widgets\Breadcrumbs;
    use app\components\DocoHelpers;
    use yii\helpers\ArrayHelper;
    use app\widgets\filters\DropdownCaraBayar\DHSelectCaraBayar;
    use app\widgets\filters\DropdownInstalasi\DHSelectInstalasi;
    use app\widgets\filters\DropdownPenjamin\DHSelectPenjamin;
    use app\widgets\filters\DropdownRuanganRanap\DHSelectRuanganRanap;

    $title = $dataView['title'];
    $this->params['breadcrumbs'][] = ['label' => 'Fisioterapi', 'url' => ['/fisioterapi']];
    $this->params['breadcrumbs'][] = ['label' => 'Laporan', 'url' => ['/fisioterapi']];
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
                <?= Yii::$app->controller->renderPartial('partials/table') ?>
            </div>
        </div>
    </div>
</div>

<?php
    $formFilter = [
        'caraBayar' => DHSelectCaraBayar::widget([
            'id' => 'filterCaraBayar',
            'prompt' => 'Semua',
            'isDepToChild' => true,
            'depUrl' => '/api/master/get-penjamin-dep-drop',
            'idDepChild' => 'filterPenjamin',
            'dataDependPrompt' => 'Semua'
        ]),
        'penjamin' => DHSelectPenjamin::widget([
            'id' => 'filterPenjamin',
            'isDepToParent' => true,
            'idDepChild' => 'filterCaraBayar',
            'prompt' => 'Semua',
        ]),
        'instalasi' => DHSelectInstalasi::widget([
            'id' => 'filterInstalasi',
            'prompt' => 'Semua',
            'isDepToChild' => true,
            'depUrl' => '/api/master/get-ruangan-by-instalasi-dep',
            'idDepChild' => 'filterRuangan',
            'instalasiPilihan' => ['ranap', 'fisioterapi'],
            'dataDependPrompt' => 'Semua'
        ]),
        'ruangan' => DHSelectRuanganRanap::widget([
            'id' => 'filterRuangan',
            'isDepToParent' => true,
            'idDepChild' => 'filterInstalasi',
            'prompt' => 'Semua',
        ]),
    ];
    $dataFilter = [
        'listDokter' => ArrayHelper::getValue($dataView, 'listDokter'),
        'listStatusPeriksa' => ArrayHelper::getValue($dataView, 'listStatusPeriksa'),
    ];
    $this->registerJsVar('dataFilter', $dataFilter);
    $this->registerJsVar('formFilter', $formFilter);
    $this->registerJs($this->render("js/index.js"), View::POS_END, 'js');
?>
