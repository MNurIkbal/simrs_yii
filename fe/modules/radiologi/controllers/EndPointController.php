<?php

namespace Doco\radiologi\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;

class EndPointController extends DocoController
{
    protected $_restLab;
    protected $_restApotek;
    protected $allowAction = [
        '*'
    ];
    public function init()
    {
        parent::init();
        $this->_restLab = Yii::$app->docoRest->radiologi;
        $this->_restApotek = Yii::$app->docoRest->apotek;
    }

    public function actionGetObatAlkes()
    {
        $response = [];
        try {
            $instalasi_id = Yii::$app->docoVars->workspace("instalasi_id");
            $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
            $request = Yii::$app->request;
            $kelas_id = $request->get("kelas_id");
            $penjamin_id = $request->get("penjamin_id");
            $result = $this->_restApotek->get('allow/get-list-stok-apotek',[
                            'query' => [
                                'instalasi_id' => $instalasi_id,
                                'ruangan_id' => $ruangan_id,
                                'kelaspelayanan_id' => $kelas_id,
                                'penjamin_id' => $penjamin_id,
                                'keyword' => $request->get('term')
                            ]
                        ]);
            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']['data']) ? $result['response']['data'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $satuan = [];
                if (isset($value['satuankecil_id'])) {
                    $satuan[$value['satuankecil_id']] = $value['satuankecil_nama'];
                }

                if (isset($value['satuanbesar_id'])) {
                    $satuan[$value['satuanbesar_id']] = $value['satuanbesar_nama'];
                }

                // var_dump($value);die;
                $response[] = [
                    'id' => $value['obatalkes_id'],
                    'text' => $value['obatalkes_kode'] . ' - ' . $value['obatalkes_namalain'] . ' - stok ' . $value['qty_tersedia'],
                    'stok' => $value['qty_tersedia'],
                    'satuankecil_id' => $value['satuankecil_id'],
                    'satuankecil_nama' => $value['satuankecil_nama'],
                    'satuanbesar_id' => $value['satuanbesar_id'],
                    'satuanbesar_nama' => $value['satuanbesar_nama'],
                    'satuan' => $satuan,
                    'harga_netto' => $value['hargaygdipakai']
                ];
            }
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['message'] = $e->getMessage();
        }
        
        return DocoHelpers::response([
            'result' => $response
        ]);
    }
}