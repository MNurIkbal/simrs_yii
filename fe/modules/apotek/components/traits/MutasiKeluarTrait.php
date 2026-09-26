<?php

namespace Doco\apotek\components\traits;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use Doco\apotek\models\InformasiForm;

trait MutasiKeluarTrait
{
	public function actionObatAlkesKeluar()
	{
        $title = Yii::t("fe", "Informasi Mutasi Obat Alkes Keluar");
        $model = new InformasiForm;
        $response = $this->_restApotek->get('instalasi?advanced-filter[is_active]=1');
        $body = json_decode($response->getBody(), TRUE);
        $instalasi = $body['response']['data'];

        return $this->render('mutasi-keluar/obat-alkes-keluar', get_defined_vars());
	}

	public function actionGetDataKeluar()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $get = $request->get();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['instalasi_asal_id'] = Yii::$app->docoVars->workspace("instalasi_id");
        $yiiRestfulParams['advanced-filter']['ruangan_asal_id'] = Yii::$app->docoVars->workspace("ruangan_id");
        $draw = $request->get('draw',1);
        $data = [];
        try {
            $response = $this->_restApotek->get('inf-mutasi-obatalkes/index-keluar?'.http_build_query($yiiRestfulParams),['form_params'=>[]]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['mutasiobatruangan_id']);
                $value['primary'] = $primaryKey;
                unset($value['mutasiobatruangan_id']);
                $value['tglmutasioa'] = date("j M Y", strtotime($value['tglmutasioa']));
                $value['tgl_terima'] = !empty($value['tgl_terima']) ? date("j M Y", strtotime($value['tgl_terima'])) : "-";
                $value['reference'] = !empty($value['reference']) ? $value['reference'] : "-";
                $value['rowNum'] = $no;
                $value['type'] = DocoHelpers::encrypt('keluar');
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];

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