<?php

/**
 * @Author: Rizal Faidin
 */

namespace app\modules\gizi\components\traits;

use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;

use function GuzzleHttp\json_encode;
use GuzzleHttp\Exception\RequestException;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

use app\modules\gizi\models\AsesmenAwalGiziForm;

trait SgaTrait 
{
    public function actionSga()
    {
        try{
            $request = Yii::$app->request;
            $id = $request->get('id');
            $pendaftaran_id = DocoHelpers::decrypt($id);
            $req = $this->_restGizi->get('allow/get-bundle-sga?id=' . $pendaftaran_id);
            $response = json_decode($req->getBody(), true);
            $response = $response["response"];
            $lookupPerawat = $response['lookupKeperawatan'];

            $model = new AsesmenAwalGiziForm;
            $preview = false;
            $data = $response['data_model'];
            if ($data) {
                $preview = true;
                $model->attributes = $data;

                if(strpos($model->bb_biasanya, '.') !== false) {
                    $model->bb_biasanya = str_replace('.', ',', $model->bb_biasanya);
                } else {
                    $model->bb_biasanya = $model->bb_biasanya.',00';
                }

                if(strpos($model->bb_saatini, '.') !== false) {
                    $model->bb_saatini = str_replace('.', ',', $model->bb_saatini);
                } else {
                    $model->bb_saatini = $model->bb_saatini.',00';
                }

                if(strpos($model->perubahan_kg, '.') !== false) {
                    $model->perubahan_kg = str_replace('.', ',', $model->perubahan_kg);
                } else {
                    $model->perubahan_kg = $model->perubahan_kg.',00';
                }

                if(strpos($model->perubahan_persen, '.') !== false) {
                    $model->perubahan_persen = str_replace('.', ',', $model->perubahan_persen);
                } else {
                    $model->perubahan_persen = $model->perubahan_persen.',00';
                }

                foreach ($lookupPerawat['gizi_perubahan'] as $each) {
                    if ($each['lookupkeperawatan_id'] == $model->perubahan_hasil) {
                        $model->perubahan_hasil_nama = $each['lookup_name'];
                        break;
                    }
                }

                $model->diagnosa_medis = json_decode($data['diagnosa_medis'], true)['text'];
                $model->perubahan_kg_data = $model->perubahan_kg;
                $model->perubahan_persen_data = $model->perubahan_persen;
            }
            return $this->renderAjax('sga/index', get_defined_vars());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        }
    }

    public function actionSimpanSga()
    {
        try {
            $request = Yii::$app->request;
            $model = new AsesmenAwalGiziForm;
            $model->load($request->post());
            $model->bb_biasanya = str_replace(',', '.', $model->bb_biasanya);
            $model->bb_saatini  = str_replace(',', '.', $model->bb_saatini);
            if ($model->validate()) {
                $response = $this->_restGizi->post('asesmen-awal-gizi/simpan-sga', [
                    'form_params' => $model->attributes
                ]);
                $response = json_decode($response->getBody(), true);
                // reset cache data pasien
                $data_pasien = $this->_data_pasien;
                $data_pasien['stat_asesmen_gizi'] = DocoConstants::SUDAH_ASESMEN;
                Yii::$app->cache->set('gizi-pendaftaran-id-'. $request->get('id'), $data_pasien, 3600);
                // end 
                return DocoHelpers::response($response);
            } else {
                $formName = substr(strrchr(get_class($model), "\\"), 1);
                $response = $model->errors;
                return DocoHelpers::response($response, 422, $formName);
            }
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        }
    }

    public function actionCetakSga()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/Subjective Global Assesment (SGA).pdf";
        try {
            $pendaftaran_id = DocoHelpers::decrypt($request->get('id'));
            $response = $this->_restGizi->get('asesmen-awal-gizi/cetak-sga?',[
                'query' => ['pendaftaran_id'=>$pendaftaran_id],
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}