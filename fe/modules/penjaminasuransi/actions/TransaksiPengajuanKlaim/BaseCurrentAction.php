<?php

/**
 * @author Alphabet Team
 * Powered by Sirs (PT Citraraya Nusatama)
 */

namespace Doco\penjaminasuransi\actions\TransaksiPengajuanKlaim;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;

class BaseCurrentAction extends Action
{
    protected $_title;
    protected $_restMaster;
    protected $_restPenjaminAsuransi;

    public function init()
    {
        parent::init();
        $this->_title = 'Pengajuan Klaim';
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_restPenjaminAsuransi = Yii::$app->docoRest->penjaminasuransi;
    }

    /**
     * @method getListCaraBayar
     * @return Object
     */
    protected function getListCaraBayar()
    {
        return (new DocoHelpers)->guzzleExec($this->_restMaster, [
            'method' => 'GET',
            'url' => 'allow/get-list-cara-bayar-penjamin'
        ]);
    }

    /**
     * @method getNamaPenjamin
     * @param Integer $penjaminId (penjamin_id)
     * @return Object
     */
    protected function getNamaPenjamin($penjaminId)
    {
        return (new DocoHelpers)->guzzleExec($this->_restPenjaminAsuransi, [
            'method' => 'GET',
            'url' => 'transaksi-pengajuan-klaim/get-penjamin',
            'payload' => [
                'query' => [
                    'penjamin_id' => $penjaminId
                ]
            ]
        ]);
    }

    /**
     * @method setCache (Set Cache)
     * @param String $idCache
     * @param Mix $dataCache
     * @param Integer $expireTime
     * @return Bool
     */
    protected function setCache($idCache, $dataCache, $expireTime)
    {
        Yii::$app->cache->set($idCache, $dataCache, $expireTime);
    }
}
