<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\master\actions\MarginHarga;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\modules\master\models\KonfigMarginForm;

class UpdateAction extends Action {
    protected $_title = "Margin Harga";

    public function run($id) {
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Ubah').' '.\Yii::t('fe', $this->_title);
        $model = new KonfigMarginForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);

        if ($request->post()) {
            $model->load($request->post());
            $postDetailMargin = $request->post('detail_margin');
            $model->konfigmargin_id = $id;
            try {
                $response = Yii::$app->docoRest->master->post('margin-harga/update-data?id='.$id, [
                    'form_params' => [
                        'header' => $model->attributes,
                        'detail' => $postDetailMargin,
                        'id' => $id
                    ]
                ]);
                $body = json_decode($response->getBody(), True);
                return DocoHelpers::response($body, false, 'KonfigMarginForm');
            } catch (RequestException $e) {
               return DocoHelpers::response(['message' => $e->getMessage()],500);
            } catch (\Exception $e) {
                return DocoHelpers::response(['message' => $e->getMessage()],500);
            }
        } else {
            $response = Yii::$app->docoRest->master->get('margin-harga/list-group-margin',[
                'query' => [
                    'id' => $id
                ]
            ]);
            $body = json_decode($response->getBody(), true);
            $attributes = isset($body['response']['header']) ? $body['response']['header'] : [];
            $data = isset($body['response']['detail']) ? $body['response']['detail'] : [];
            $group_margin = isset($body['response']['group_margin']) ? $body['response']['group_margin'] : [];
            $kelas_pelayanan = isset($body['response']['kelas_pelayanan']) ? $body['response']['kelas_pelayanan'] : [];
            $jenis_obat = isset($body['response']['jenis_obat']) ? $body['response']['jenis_obat'] : [];
            $model->konfigmargin_id = $attributes['konfigmargin_id'];
            $model->attributes = $attributes;
            return $this->controller->renderAjax('form_edit', get_defined_vars());
        }

    }
}