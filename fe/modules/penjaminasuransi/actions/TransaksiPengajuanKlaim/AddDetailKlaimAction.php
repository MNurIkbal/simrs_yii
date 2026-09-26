<?php

namespace Doco\penjaminasuransi\actions\TransaksiPengajuanKlaim;

use Yii;
use yii\validators\Validator;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\modules\penjaminasuransi\models\PengajuanKlaimForm;

class AddDetailKlaimAction extends BaseCurrentAction
{
    protected $_validator;

    public function init()
    {
        parent::init();
        $this->_validator = new Validator();
    }

    public function run()
    {
        $request = Yii::$app->request;
        $title = 'Proses Pengajuan Klaim';
        $model = new PengajuanKlaimForm();
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $cachePenjamin = Yii::$app->cache->get("pengajuan_klaim_penjamin");

        // METHOD POST (Simpan Pengajuan Klaim)
        if ($model->load($request->post())) {
            if ($model->validate()) {
                $model->instalasi_id = $request->post('instalasi_id');
                $model->ruangan_id = $request->post('ruangan_id');
                $model->pembayaranpelayanan_id = $request->post('pembayaranpelayanan_id');

                try {
                    $payload = $model->attributes;
                    $response = $this->simpanPengajuan($payload);

                    return DocoHelpers::response($response);
                } catch (RequestException $e) {
                    Yii::error($e, 'errors');
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                } catch (\Exception $e) {
                    Yii::error($e, 'errors');
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);

                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        }

        return Yii::$app->controller->render('_form_detail_klaim', get_defined_vars());
    }

    /**
     * @method simpanPengajuan
     * @param Array $payload
     * @return 
     */
    private function simpanPengajuan($payload)
    {
        return (new DocoHelpers)->guzzleExec($this->_restPenjaminAsuransi, [
            'method' => 'POST',
            'url' => 'transaksi-pengajuan-klaim/save',
            'payload' => [
                'form_params' => $payload
            ],
            'with_metadata' => true
        ]);
    }
}
