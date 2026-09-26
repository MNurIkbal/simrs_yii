<?php

namespace Doco\penjaminasuransi\actions\TransaksiPengajuanKlaim;

use Yii;
use yii\helpers\ArrayHelper;
use yii\validators\Validator;
use app\components\DocoHelpers;
use app\components\Services\Penjamin\GetInitFilterService;
use app\modules\penjaminasuransi\models\PengajuanKlaimForm;

class IndexAction extends BaseCurrentAction
{
    protected $_validator;

    public function init()
    {
        parent::init();
        $this->_validator = new Validator();
    }

    public function run()
    {
        $title = $this->_title;
        $model = new PengajuanKlaimForm();
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $request = Yii::$app->request;
        $response = (new GetInitFilterService)->execute();
        $requestCaraBayar = ArrayHelper::getValue($response, 'cara_bayar');
        if ($model->load($request->post())) {
            $post = $request->post('PengajuanKlaimForm', []);

            $model->no_pengajuanklaim = 'dummy';
            $model->tgl_jatuhtempo = 'dummy';
            $model->attributes = $post;

            $penjamin_nama = '';

            $penjaminId = ArrayHelper::getValue($post, 'penjamin_id');
            $caraBayarNama = ArrayHelper::getValue($post, 'carabayar_nama');

            if (!$this->_validator->isEmpty($penjaminId)) {
                $penjamin = $this->getNamaPenjamin($penjaminId);
                $penjamin_nama = ArrayHelper::getValue($penjamin, 'penjamin_nama');
            }

            if ($model->validate()) {
                $cachePenjamin = Yii::$app->cache->get("pengajuan_klaim_penjamin");

                if ($cachePenjamin == false) {
                    Yii::$app->cache->set("pengajuan_klaim_penjamin", []);
                    $cachePenjamin = [];
                }

                $setCache = [
                    'carabayar_id' => $model->carabayar_id,
                    'penjamin_id' => $model->penjamin_id,
                    'carabayar_nama' => $caraBayarNama,
                    'penjamin_nama' => $penjamin_nama,
                ];
                $cachePenjamin = $setCache;

                $this->setCache('pengajuan_klaim_penjamin', $cachePenjamin, 3600);

                $response['response'] = [
                    'title' => 'Proses Berhasil!',
                    'text' => 'Data berhasil diset'
                ];

                return DocoHelpers::response($response);
            } else {
                $response = $model->errors;
                Yii::error($response);
                return DocoHelpers::response($response, 422, $formName);
            }
        }

        $cacheCaraBayar = Yii::$app->cache->get("pengajuan_klaim_penjamin");

        if ($cacheCaraBayar) {
            $caraBayarId = ArrayHelper::getValue($cacheCaraBayar, 'carabayar_id');
            $penjaminId = ArrayHelper::getValue($cacheCaraBayar, 'penjamin_id');

            $model->carabayar_id = isset($caraBayarId) ? $caraBayarId : null;
            $model->penjamin_id = isset($penjaminId) ? $penjaminId : null;
        }

        return Yii::$app->controller->render('index', get_defined_vars());
    }
}
