<?php
	/**
	 * @author Chacha Nurholis
	 * A product of PT Citra Raya Nusatama
	 * Powered by Sirs
	 */

    use app\components\DHtml;
	use yii\widgets\Breadcrumbs;
	use app\components\DocoHelpers;

	$this->title = DHtml::getTitleMenu();
	$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"), 'url' => ['index']];
	$this->params['breadcrumbs'][] = ['label' => 'Laporan', 'url' => ['index']];
	$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .select2-selection--multiple .select2-search--inline .select2-search__field {
        padding: 3px 7px;
    }

    .select2-selection--multiple .select2-search--inline .select2-search__field {
        margin-left: -3px !important;
    }

    .advancedFilter .select2 .select2-selection {
        padding: 0px !important;
        height: auto !important;
        border-color: #ced4da !important;
    }
    .select2-selection__rendered {
        max-height: 70px !important;
        overflow-y: auto !important;
    }

    .select2-results__option[aria-selected=true] {
        display: none;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">

			<!-- Panel Heading -->
            <div class="panel-heading">
              <div class="row">
                  <div class="column-1">
                      <img src="<?= Yii::$app->docoVars->workspace("modul_icon") ?>">
                  </div>
                  <div class="column-2">
                      <h3 class="panel-title"><b><?= $this->title ?></b></h3>
                      <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])) ?>
                  </div>
              </div>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>

			<!-- Panel Toolbar -->
            <div class="panel-toolbar clearfix">
                <?php
                    $btn_toolbar = [
                        'search',
                        'reset' => [
                            'attributes'=> [
                                'data-parent' => '.filter-form'
                            ]
                        ],
                        'excel' => [
                            'attributes' => [
                                'data-target' => $module.'export-excel?'
                            ]
                        ]
                    ]
                ?>
                <?= DocoHelpers::generateToolbar($btn_toolbar, '#laporan-po') ?>
            </div>

			<!-- Panel Body -->
            <div class="panel-body">
                <div class="filter-form"></div>
                <table id="laporan-po" class="table table-striped table-condensed table-hover" style="width: 100%;">
                    <thead>
                        <tr class="bg-inverse">
                            <th><?= \Yii::t('fe', 'No.') ?></th>
                            <th><?= \Yii::t("fe", "Kode Supplier") ?></th>
                            <th><?= \Yii::t("fe", "Nama Supplier") ?></th>
                            <th><?= \Yii::t("fe", "No. PO") ?></th>
                            <th><?= \Yii::t("fe", "Tanggal PO") ?></th>
                            <th><?= \Yii::t("fe", "Tanggal Validasi PO") ?></th>
                            <th><?= \Yii::t("fe", "Status PO") ?></th>
                            <th><?= \Yii::t("fe", "Tanggal Batal PO") ?></th>
                            <th><?= \Yii::t("fe", "Alasan Batal") ?></th>
                            <th><?= \Yii::t("fe", "Total Harga (Rp.)") ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="10"><?= \Yii::t("fe", "Data tidak ditemukan.") ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs('var _dataSuppliers = ' . json_encode($suppliers) . ';' . $this->render('js/index.js') . ';') ?>