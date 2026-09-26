<?php

namespace Doco\penjaminasuransi\actions\TransaksiPengajuanKlaim;

use Yii;
use yii\validators\Validator;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class CetakPengajuanAction extends BaseCurrentAction
{
    protected $_validator;
    protected $_restPenjaminAsuransi;

    public function init()
    {
        $this->_validator = new Validator();
        $this->_restPenjaminAsuransi = Yii::$app->docoRest->penjaminasuransi;
    }

    public function run()
    {
        $request = Yii::$app->request;
        $pengajuanKlaimId = $request->get('pengajuanklaim_id', null);

        if ($this->_validator->isEmpty($pengajuanKlaimId)) {
            throw new \Exception("Pengajuan Klaim ID Tidak Boeleh Kosong!");
        }

        $pengajuanKlaimId = DocoHelpers::decrypt($pengajuanKlaimId);
        $path = Yii::getAlias("@download") . "/cetak-pengajuan.pdf";

        try {
            $this->cetakPengajuan($path, $pengajuanKlaimId);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            Yii::error($e->getMessage());
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            Yii::error($e->getMessage());
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }

    /**
     * @method cetakPengajuan
     * @param String $path
     * @param Integer $pengajuanKlaimId
     */
    private function cetakPengajuan($path, $pengajuanKlaimId)
    {
        return (new DocoHelpers)->guzzleExec($this->_restPenjaminAsuransi, [
            'method' => 'GET',
            'url' => 'transaksi-pengajuan-klaim/cetak-pengajuan',
            'save_to' => $path,
            'payload' => [
                'query' => [
                    'pengajuanklaim_id' => $pengajuanKlaimId
                ]
            ]
        ]);
    }
}
