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

class CreateAction extends Action {
    protected $_title = "Margin Harga";

    public function run() {
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Tambah').' '.\Yii::t('fe', $this->_title);
        $model = new KonfigMarginForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = null;

        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                $detail_margin = json_decode($request->post('detail_margin'), true);
                if($detail_margin['row-0']['harga_max'] <= $detail_margin['row-0']['harga_min']) {
                    $response['response'] = [
                        'title' => 'Proses Gagal !',
                        'text' => 'Harga Max tidak boleh kecil dari Harga Min.'
                    ];
                    return DocoHelpers::response($response, 422);
                } else {

                    $postDetailMargin = $request->post('detail_margin');
                    $post = [
                        'header' => $model->attributes,
                        'detail' => $postDetailMargin,
                    ];

                    try {
                        $response = Yii::$app->docoRest->master->post('margin-harga/create', [
                            'form_params' => $post
                        ]);
                        $body = json_decode($response->getBody(), True);
                        return DocoHelpers::response($body);
                    } catch (RequestException $e) {
                       return DocoHelpers::response(['message' => $e->getMessage()],500);
                    } catch (\Exception $e) {
                        return DocoHelpers::response(['message' => $e->getMessage()],500);
                    }
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {
            $groupmargin_request = Yii::$app->docoRest->master->get('margin-harga/list-group-margin');
            $body = json_decode($groupmargin_request->getBody(),TRUE);
            $group_margin = isset($body['response']['group_margin']) ? $body['response']['group_margin'] : [];
            $kelas_pelayanan = isset($body['response']['kelas_pelayanan']) ? $body['response']['kelas_pelayanan'] : [];
            $jenis_obat = isset($body['response']['jenis_obat']) ? $body['response']['jenis_obat'] : [];
            return $this->controller->renderAjax('form_edit', get_defined_vars());
        }
    }
}