<?php

namespace Extensions\pelayanan;

use Yii;
use Doco\models\kasir\PembayaranPelayanan;
use GuzzleHttp\Exception\RequestException;
use Doco\exceptions\ValidationException;

class DisableByPayment extends \Doco\processes\ImplementationButtonProcess
{
    protected function getPaymentStatus()
    {
        // Disable implementation button if payment exist
        $pendaftaran_id = Yii::$app->request->get('pendaftaran_id', null);
        $pasienadmisi_id = Yii::$app->request->get('pasienadmisi_id', null);

        // Validation
        if (empty($pendaftaran_id)) {
            \Yii::$app->response->statusCode = 422;
            throw new ValidationException(422, $this->_error, [
                'text' => 'Pendaftaran ID tidak boleh kosong'
            ]);
        }

        if (empty($pasienadmisi_id)) {
            \Yii::$app->response->statusCode = 422;
            throw new ValidationException(422, $this->_error, [
                'text' => 'Pasien admisi ID tidak boleh kosong'
            ]);
        }

        $model = PembayaranPelayanan::find()->where(compact('pendaftaran_id', 'pasienadmisi_id'))->one();
        $cekStatus = empty($model) ? false : true;
        return $cekStatus;
    }

    public function execute()
    {
        try {
            return $this->getPaymentStatus();
        } catch (RequestException $e) {
            \Yii::$app->response->statusCode = 500;
            $response = json_decode($e->getResponse()->getBody(), true);
            return $response['response'];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            $response = json_decode($e->getResponse()->getBody(), true);
            return $response['response'];
        }
    }
}
