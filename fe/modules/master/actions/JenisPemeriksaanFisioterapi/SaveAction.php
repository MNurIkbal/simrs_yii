<?php

/**
 * @author Andri Amirul (andri.amirul@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 */
namespace Doco\master\actions\JenisPemeriksaanFisioterapi;

use Yii;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use Doco\master\exceptions\BaseCurrentException;

class SaveAction extends BaseCurrentAction
{

    public function run()
    {
        $data = Yii::$app->request->post('data');
        try {
            $response = (new DocoHelpers)->guzzleExec(Yii::$app->docoRest->fisioterapi, [
                'method' => 'POST',
                'url' => "jenis-pemeriksaan-fisioterapi/save",
                'payload' => [
                    'form_params' => $data
                ],
                'with_metadata' => true
            ]);
            Yii::error($response);
            $response = DocoHelpers::response($response);
            return $response;

        } catch (BaseCurrentException $e) {
            (new DocoHelpers)->logError($e);
            return (new DocoHelpers)->response($e->getMessage(), 422);
        } catch (RequestException $e) {
            (new DocoHelpers)->logError($e);
            return ['error' => $e->getMessage()];
        } catch (\Exception $e) {
            (new DocoHelpers)->logError($e);
            return ['error' => $e->getMessage()];
        }
    }
}
