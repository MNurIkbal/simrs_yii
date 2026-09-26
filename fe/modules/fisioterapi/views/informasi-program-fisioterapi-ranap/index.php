<?php
    /**
   * @author Chacha Nurholis (chacha@sirs.co.id)
   * A Product of PT Citraraya Nusatama
   * Powered by Sirs
   */

	use yii\web\View;
	use yii\widgets\Breadcrumbs;
	use app\components\DocoHelpers;
    use app\widgets\filters\DropdownKamarRanap\DHSelectKamarRanap;
    use app\widgets\filters\DropdownPemeriksaan\DHSelectPemeriksaan;
    use app\widgets\filters\DropdownRuanganRanap\DHSelectRuanganRanap;
    use app\widgets\filters\DropdownTempatTidurRanap\DHSelectTempatTidurRanap;

	$title = $dataView['title'];
	$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace('modul_alias'), 'url' => ['/']];
	$this->params['breadcrumbs'][] = ['label' => 'Informasi', 'url' => ['/']];
	$this->params['breadcrumbs'][] = $title;
?>
<style>
	.dataTables_scroll {
		max-height: 100% !important;
	}
	.filter-form .form-control {
		margin-left: 4px;
	}
	.filter-form 
	.form-group > select {
		margin-left: 5px;
	}
	.modal-content {
        margin-top: -2%;
		margin-left: -10%;
		width: 120% !important;
	}
	.img-icon {
		width: 20px !important;
	}
</style>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace('modul_icon') ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= $title ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])) ?>
                    </div>
                </div>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'search' => [
                        'title' => 'Cari',
                        'attributes' => ['id' => 'toolbar-cari']
                    ],
                    'reset' => [
                        'title' => Yii::t("fe", "Muat Ulang"),
                        'attributes' => ['data-parent' => '.filter-form']
                    ]
                ]) ?>
            </div>
        <div class="panel-body">
            <?= Yii::$app->controller->renderPartial('partials/tabs') ?>
            <?= Yii::$app->controller->renderPartial('partials/table') ?>
        </div>
        </div>
    </div>
</div>
<div id="modalProgramTerapi" style="overflow-y:auto" class="modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content"></div>
    </div>
</div>
<div id="modal-lab" class="modal fade" style="z-index: 1041 !important; overflow-y:auto" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            xxx
        </div>
    </div>
</div>
<div id="modal-order-pemeriksaan" style="z-index: 2041 !important" class="modal fade" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            xxx
        </div>
    </div>
</div>
<?php
	$dataFilter = [
    'form_filters' => [
        'tempatTidurRanap' => DHSelectTempatTidurRanap::widget([
            'id' => 'filterTempatTidur',
            'isDepToParent' => true,
            'idDepChild' => 'filterKamar',
            'prompt' => 'Semua',
        ]),
        'ruanganRanap' => DHSelectRuanganRanap::widget([
            'id' => 'filterRuangan',
            'isDepToChild' => true,
            'depUrl' => '/api/master/get-kamar-ranap-dep',
            'idDepChild' => 'filterKamar',
            'prompt' => 'Semua',
            'dataDependPrompt' => 'Semua',
            'dataStorage' => 'kamarruangan',
            'dataKey' => 'kamarruangan_id',
        ]),
        'kamarRanap' => DHSelectKamarRanap::widget([
            'id' => 'filterKamar',
            'isDepToChild' => true,
            'depUrl' => '/api/master/get-tempat-tidur-ranap-dep',
            'idDepChild' => 'filterTempatTidur',
            'prompt' => 'Semua',
            'dataDependPrompt' => 'Semua',
            'dataStorage'       => 'kamarruangan',
            'dataKey'           => 'kamarruangan_id',
        ]),
        'pemeriksaan' => DHSelectPemeriksaan::widget([
            'id' => 'filterNamaPemeriksaan',
            'prompt' => 'Semua',
            'independent' => true,
            'dataDependPrompt' => 'Semua'
        ]),
    ],
		'listDokter' => $dataView['listDokter'],
		'listStatus' => $dataView['listStatus'],
        'statusProgramOpenId' => $dataView['statusProgramOpenId']
	];
	$this->registerJsVar('dataFilter', $dataFilter);
	$this->registerJs($this->render('js/index.js'), View::POS_END, 'js');
?>
