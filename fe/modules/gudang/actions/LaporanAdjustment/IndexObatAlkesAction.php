<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\gudang\actions\LaporanAdjustment;

use Yii;
use yii\base\Action;
use app\components\DHtml;
use app\components\Traits\ControllerHelperTrait;

class IndexObatAlkesAction extends Action
{
    use ControllerHelperTrait;
    public function run()
    {
        $module = $this->controller->_module;
        $columns = $this->getColumns();
        $titleMenu = DHtml::getTitleMenu();
        $title = !empty($titleMenu) ? $titleMenu : $this->controller->_title."Obat Alkes";
        $filters = $this->guzzleExec(Yii::$app->docoRest->gudang, [
            'url' => 'laporan-adjustment/filters',
            'payload' => [
                'query' => [
                    'types' => [
                        'instalasi_ruangan',
                        'jenis_adjustment',
                        'jenis_obatalkes'
                    ]
                ]
            ]
        ]);

        return $this->controller->render('obat-alkes', get_defined_vars());
    }

    private function getColumns()
    {
        return [
            Yii::t('fe', 'No'),
            Yii::t('fe', 'Ruangan'),
            Yii::t('fe', 'No Transaksi'),
            Yii::t('fe', 'Tanggal Adjustment'),
            Yii::t('fe', 'Jenis Adjustment'),
            Yii::t('fe', 'Jenis Obat Alkes'),
            Yii::t('fe', 'Kode Obat Alkes'),
            Yii::t('fe', 'Nama Obat Alkes'),
            Yii::t('fe', 'Qty'),
            Yii::t('fe', 'Satuan'),
            Yii::t('fe', 'Qty Konversi'),
            Yii::t('fe', 'Satuan Terkecil'),
            Yii::t('fe', 'Nama Pegawai')
        ];
    }
}
