<?php

/**
 * @author Andri Amirul (andri.amirul@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 * 
 * PemeriksaanDepService digunakan untuk kebutuhan 
 * mengambil data tempat tidur yang dependent ke pemeriksaan
 */

namespace app\components\Services\Master;

use Yii;
use yii\web\Response;
use app\components\Traits\ControllerHelperTrait;

class PemeriksaanDepService
{
    use ControllerHelperTrait;

    protected $service;

    public function __construct()
    {
        $this->service = Yii::$app->docoRest->master;
    }

    public function execute()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $params = '';
        if ($request->post()) {
            $depdrop_parents = $request->post('depdrop_parents');
            $parent_label = $depdrop_parents[0];
            $params = '?jenispemeriksaanfisio_id='.$parent_label;
        }
        $result = [];
        $result['output'] = [];
        $result['selected'] = '';
        try {
            $response = $this->service->get('pemeriksaan-fisioterapi/get-pemeriksaan'. $params);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $value)
                $result['output'][] = [
                    'id' => !is_null($value['daftartindakandet_id']) ? $value['daftartindakandet_id'] : $value['daftartindakan_id'],
                    'name' => !is_null($value['daftartindakandet_nama']) ? $value['daftartindakandet_nama']  : $value['daftartindakan_nama']
                ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
}
