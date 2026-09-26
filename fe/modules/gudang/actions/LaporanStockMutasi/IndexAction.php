<?php

namespace Doco\gudang\actions\LaporanStockMutasi;

use Yii;
use yii\base\Action;
use app\components\Traits\ControllerHelperTrait;
use app\components\DHtml;

class IndexAction extends Action
{
    use ControllerHelperTrait;
	public function run()
	{
        $module = $this->controller->_module;
        $columns = $this->getColumns();
        $titleMenu = DHtml::getTitleMenu();
        $title = !empty($titleMenu) ? $titleMenu : $this->controller->_title;
        $filters = $this->guzzleExec(Yii::$app->docoRest->gudang, [
            'url' => 'lap-stock-mutasi/filters',
            'payload' => [
                'query' => [
                    'types' => [
                        'instalasi_ruangan',
                        'jenis_obatalkes'
                    ]
                ]
            ]
        ]);

        $is_disabled = false;
		return $this->controller->render('index', get_defined_vars());
	}	

    private function getColumns()
    {
        return [
            Yii::t('fe', 'No'),
            Yii::t('fe', 'Tanggal'),
            Yii::t('fe', 'Kode Obat'),
            Yii::t('fe', 'Nama Obat Alkes'),
            Yii::t('fe', 'Jenis Obat Alkes'),
            Yii::t('fe', 'Manufaktur'),
            Yii::t('fe', 'Ruangan'),
            Yii::t('fe', 'UoM'),
            Yii::t('fe', 'HNA'),
            Yii::t('fe', 'Total Qty Awal'),
            Yii::t('fe', 'Total Value Awal'),
            Yii::t('fe', 'Total Qty Received'),
            Yii::t('fe', 'Total Nilai Received'),
            Yii::t('fe', 'Total Qty Usage'),
            Yii::t('fe', 'Total Nilai Usage'),
            Yii::t('fe', 'Total Qty Akhir'),
            Yii::t('fe', 'Total Value Akhir'),
            Yii::t('fe', 'Turn Over')
        ];
    }
}
