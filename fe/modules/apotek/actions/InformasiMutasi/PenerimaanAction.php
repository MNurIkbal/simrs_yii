<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\InformasiMutasi;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use Doco\apotek\models\PenerimaanObatForm;
use yii\helpers\Url;
use Doco\apotek\services\InfMutasiObatalkesService;

class PenerimaanAction extends Action {
    protected $_title = "";

    public function run($nomutasioa)
    {
        try {
            $title = 'Penerimaan Obat Alkes';
            $request = Yii::$app->request;
            $model = new PenerimaanObatForm;
            if ($request->post()) {
                $post = $request->post();
                $post['PenerimaanObatForm']['pegawai_mengetahui'] = DocoHelpers::decrypt($post['PenerimaanObatForm']['pegawai_mengetahui']);
                // $post['PenerimaanObatForm']['pegawai_menyetujui'] = DocoHelpers::decrypt($post['PenerimaanObatForm']['pegawai_menyetujui']);
                $body = InfMutasiObatalkesService::postTerima($nomutasioa,$post);
                $body['response']['id'] = DocoHelpers::encrypt($body['response']['id']);
                // delete cache
                \yii\caching\TagDependency::invalidate(Yii::$app->cache, 'obat');
                return DocoHelpers::response($body,false,true);
            }else {
                $response = InfMutasiObatalkesService::getDetail([
                    'nomutasioa' => $nomutasioa
                ]);

                $model->loadFromMutasi($response['header']);

                $optMengetahui = $optMenyetujui=[];
                $optMengetahui = [
                    $model->pegawai_mengetahui = $model->nama_pegawai_mengetahui
                ];
                $btn_toolbar = [
                    'save-penerimaan' => [
                        'type' => 'button',
                        'method' => 'ss',
                        'icon' => 'fa fa-floppy-o',
                        'title' => \Yii::t('fe', 'Simpan'),
                        'attributes' => [
                            'id' => 'btn-simpan-penerimaan',
                            'form_id' => 'penerimaan-form',
                            'class' => 'btn btn-success btn-labeled btn-xs',
                            'disabled' => (in_array($model->status_mutasi, [null, DocoConstants::STATUS_TERIMA]))
                        ]
                    ],
                    'print-rincian' => [
                        'type' => 'button',
                        'title' => 'Print',
                        'icon' => 'fa fa-print',
                        'attributes' => [
                            'class' => 'data-lihat print-tagihan',
                            'id' => 'print-tagihan',
                            'method' => 'json',
                            'data-options' => 'link',
                            'disabled' =>  (@$model->status_mutasi== DocoConstants::STATUS_TERIMA ? false : true)
                        ]
                    ],
                    'kembali'=>[
                        'type'=>'link',
                        'title' => \Yii::t('fe', 'Kembali'),
                        'icon' => 'fa fa-arrow-left',
                        'method' => 'not-exist',
                        'attributes' => [
                            'class'=>'btn btn-success btn-labeled btn-xs btn btn-info data-kembali',
                            // 'href' => Url::home().Yii::$app->controller->module->id.'/informasi-obat-alkes-keluar',
                            'href'=> Url::home().Yii::$app->controller->module->id.'/'.Yii::$app->controller->id.'/obat-alkes'
                        ]
                    ],
                ];
                return $this->controller->render('penerimaan_mutasi', get_defined_vars());
            }
        } catch (RequestException $e) {
            $error = json_decode($e->getResponse()->getBody(),true);
            $message = isset($error['response']['message']) ? $error['response']['message'] : $e->getMessage();
            return DocoHelpers::response(['message' => $message,'response'=>['message'=>$message]],$e->getResponse()->getStatusCode());
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }
}