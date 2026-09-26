<?php

namespace Doco\penjaminasuransi\actions\TransaksiAlokasiPembayaran;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class BaseCurrentAction extends Action
{
    const DETAILS_SESSION_KEY = 'transaksi_alokasi_pembayaran.details';

    protected $_title;
    protected $_restPenjamin;
    protected $_module = '/penjaminasuransi/transaksi-alokasi-pembayaran/';

    protected function beforeRun()
    {
        $this->_title = Yii::t('fe', 'Transaksi Alokasi Pembayaran');
        $this->_restPenjamin = Yii::$app->docoRest->penjaminasuransi;
        return true;
    }

    protected function setDetailsToSession($details)
    {
        $key = self::DETAILS_SESSION_KEY;
        $session = Yii::$app->session;
        $session->set($key, $details);
    }

    protected function getDetailsSession()
    {
        $key = self::DETAILS_SESSION_KEY;
        $session = Yii::$app->session;
        $details = $session->get($key);
        return $details;
    }

    protected function clearDetailsSession()
    {
        $key = self::DETAILS_SESSION_KEY;
        $session = Yii::$app->session;
        $session->remove($key);
    }
}
