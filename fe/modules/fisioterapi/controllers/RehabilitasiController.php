<?php

namespace Doco\fisioterapi\controllers;

use app\components\DocoController;
use app\components\DocoHelpers;
use GuzzleHttp\Client;
use Yii;
use yii\helpers\ArrayHelper;

class RehabilitasiController extends DocoController
{
    protected $allowAction = ['*'];

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $pasienId = $request->get('pasien_id');
        $pendaftaranId = DocoHelpers::decrypt($request->get('id'));
        $pendaftaranIdEnc = $request->get('id');
        $randString = DocoHelpers::generateRandomString();
        $title = 'Program Rehabilitasi';

        $response = $this->guzzleExec(Yii::$app->docoRest->fisioterapi, [
            'url' => 'informasi-pasien-fisioterapi/get-last-rehab',
            'method' => 'GET',
            'payload' => [
                'query' => [
                    'pendaftaran_id' => $pendaftaranId
                ]
            ]
        ]);

        $body = ArrayHelper::getValue($response, 'data');
        $ftpUrl = ArrayHelper::getValue($body, 'ftp_url');
        $isHide = !isset($ftpUrl) ? true : false;
        
        return $this->renderAjax('index', compact('title', 'pasienId', 'pendaftaranId', 'randString', 'isHide', 'pendaftaranIdEnc'));
    }

    public function actionGetRehabilitasi()
    {
        try {
            $request = Yii::$app->request;
            $pasien_id = DocoHelpers::decrypt($request->get('pasien_id'));

            /**
             * Fetch API.
             */
            $response = Yii::$app->docoRest->fisioterapi->get('informasi-pasien-fisioterapi/get-history-fisioterapi', [
                'query' => [
                    'pasien_id' => $pasien_id
                ]
            ]);

            $body = json_decode($response->getBody(), true);
            $data = ArrayHelper::getValue($body, 'response.data');
            $row = [];

            foreach ($data as $key => $value) {
                $row[$key]['rowNum'] = $key + 1;
                $row[$key]['pendaftaran_id'] = $value['pendaftaran_id'];
                $row[$key]['tgl_permintaan'] = date('d-m-Y H:i:s', strtotime($value['tgl_permintaan']));
                $row[$key]['dokter_perujuk'] = $value['nama_pegawai'];
                $row[$key]['frekuensi'] = $value['frekuensi'];
                $row[$key]['aksi'] = '
                    <button type="button" class="btn btn-info btn-labeled btn-xs btn-cetak-history"
                        data-pendaftaran_id="' . $value['pendaftaran_id'] . '"
                        data-tgl_permintaan="' . date('Y-m-d', strtotime($value['tgl_permintaan'])) . '"
                        data-jam_permintaan="' . date('H:i:s', strtotime($value['tgl_permintaan'])) . '"
                    >
                        <b><i class="fa fa-print"></i></b>
                        Cetak History Terapi
                    </button>
                ';
            }

            $return = [
                'data' => $row,
                'draw' => $request->get('draw'),
                'recordsTotal' => $body['response']['_meta']['totalCount'],
                'recordsFiltered' => $body['response']['_meta']['totalCount'],
            ];

            return DocoHelpers::response($return);
        } catch (\Exception $th) {
            return DocoHelpers::response($th->getMessage(), 500);
        }
    }

    public function actionGenerateProgramTerapi()
    {
        $request = Yii::$app->request;
        try {
            $pendaftaranId = $request->post('pendaftaranId');
            $randString = $request->post('randString');
            $data = json_decode($request->post('params'), true);

            return $this->guzzleExec(Yii::$app->docoRest->fisioterapi, [
                'url' => 'informasi-pasien-fisioterapi/generate-file-program-terapi',
                'method' => 'POST',
                'payload' => [
                    'form_params' => [
                        'pendaftaran_id' => $pendaftaranId,
                        'randString' => $randString,
                        'params' => $data
                    ]
                ],
                'returnResponse' => true
            ]);
        } catch (\Exception $th) {
            return DocoHelpers::response($th->getMessage(), 500);
        }
    }

    public function actionCetakProgramRehabilitasi()
    {
        $request = Yii::$app->request;
        $pendaftaranId = DocoHelpers::decrypt($request->get('pendaftaran_id'));

        $restFisio = Yii::$app->docoRest->fisioterapi;
        $path = Yii::getAlias("@download") . "/cetak-program-terapi.pdf";

        $restFisio->get('informasi-pasien-fisioterapi/get-cetakan-ftp', [
            'form_params' => [
                'pendaftaran_id' => $pendaftaranId
            ],
            'save_to' => $path
        ]);

        return DocoHelpers::previewPdf($path);
    }
}
