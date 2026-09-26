<?php

namespace Doco\gudang\actions\LaporanPemakaianBarang;

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
            'url' => 'lap-pemakaian-barang/filters',
            'payload' => [
                'query' => [
                    'types' => [
                        'ruangan_nama',
                        'nama_barang',
                        'kode_barang',
                        'kelompok_barang'
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
            Yii::t('fe', 'Nama Ruangan'),
            Yii::t('fe', 'Tanggal Transaksi'),
            Yii::t('fe', 'No Transaksi'),
            Yii::t('fe', 'Kelompok Barang'),
            Yii::t('fe', 'Kode Barang'),
            Yii::t('fe', 'Nama Barang'),
            Yii::t('fe', 'Qty'),
            Yii::t('fe', 'Satuan'),
            Yii::t('fe', 'Harga Satuan (Rp)'),
            Yii::t('fe', 'Total Harga (Rp)'),
            Yii::t('fe', 'User'),
            Yii::t('fe', 'Catatan')
        ];
    }
}
