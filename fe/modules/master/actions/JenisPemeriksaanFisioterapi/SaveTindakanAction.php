<?php

/**
 * @author Andri Amirul (andri.amirul@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 */
namespace Doco\master\actions\JenisPemeriksaanFisioterapi;

use Yii;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use Doco\master\exceptions\BaseCurrentException;
use app\modules\master\models\DaftarTindakanForm;

class SaveTindakanAction extends BaseCurrentAction
{

    public function run()
    {
        $model = new DaftarTindakanForm();
        $request = Yii::$app->request;
        $post = $request->post();
        Yii::error($post, 'post');
        try {
            $daftarTindakan = ArrayHelper::getValue($post, 'DaftarTindakanForm');
            $model->load($daftarTindakan, '');
            $model->kelompoktindakan_id = 1; //dummy value untuk input required yang dihilangkan
            $model->is_fisio = true;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            if ($model->validate()) {
                $response = (new DocoHelpers)->guzzleExec(Yii::$app->docoRest->fisioterapi, [
                    'method' => 'POST',
                    'url' => "jenis-pemeriksaan-fisioterapi/save-tindakan",
                    'payload' => [
                        'form_params' => $daftarTindakan
                    ],
                    'with_metadata' => true
                ]);
                return DocoHelpers::response($response);
            } else {
                $response = $model->errors;
                return DocoHelpers::response($response, 422, $formName);
            }
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
