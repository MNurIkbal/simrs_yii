<?php

/**
 * @author Chacha Nurholis (chacha@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\fisioterapi\controllers;

use Yii;
use app\components\DocoController;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoHelpers;

class InformasiPasienFisioterapiRanapController extends DocoController
{
    protected $_module = '/fisioterapi/informasi-pasien-fisioterapi-ranap/';
    protected $_restFisioterapi;
    protected $allowAction = ['*'];

    public function init()
    {
        ini_set('memory_limit', '-1');

        parent::init();
        $this->_restFisioterapi = Yii::$app->docoRest->fisioterapi;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        $index = [
            'class' => 'app\components\actions\ExtensionAction',
            'key'   => 'informasi_pasien_fisioterapi_ranap_index'
        ];
        $newActions = [
            'index' => $index,
            'get-data-ranap' => 'Doco\fisioterapi\actions\InformasiPasienFisioterapi\GetDataRanapAction',
            'program-ranap' => 'Doco\fisioterapi\actions\InformasiPasienFisioterapi\ProgramRanapAction',
            'program' => 'Doco\fisioterapi\actions\InformasiPasienFisioterapi\ProgramAction',
            'pilih-program' => 'Doco\fisioterapi\actions\InformasiPasienFisioterapi\PilihProgramAction',
            'pilih-ranap' => 'Doco\fisioterapi\actions\InformasiPasienFisioterapi\PilihRanapAction',
            'kunjungan' => 'Doco\fisioterapi\actions\InformasiPasienFisioterapi\KunjunganAction',
        ];
        $actions = array_merge($actions, $newActions);
        return $actions;
    }

    public function actionIndexBak()
    {
        $title         = $this->_title;
        $dokterRujukan = [];
        $status        = [];
        try {
        $response      = $this->_restFisioterapi->get('informasi-pasien-fisioterapi/get-attributes');
        $response      = json_decode($response->getBody(), true);
        $dokterRujukan = $response['response']['dokterPerujuk'];
        $statusPeriksa = $response['response']['statusPeriksa'];
        if (is_array($dokterRujukan)) {
            foreach ($dokterRujukan as $dokter) {
                $list_dokter[] = [
                    'id'   => $dokter['pegawai_id'],
                    'text' => $dokter['nama_pegawai']
                ];
            }
        }
        if (is_array($statusPeriksa)) {
            $list_status[] = [
                'id'   => 'Semua',
                'text' => 'Semua'
            ];
            foreach ($statusPeriksa as $status) {
                $list_status[] = [
                    'id'   => $status['lookup_id'],
                    'text' => $status['lookup_name']
                ];
            }
        }
        } catch (RequestException $e) {
            return ['error' => $e->getMessage()];
        }
        return $this->render('kunjungan', get_defined_vars());
    }

    public function actionUpdateDpjp()
    {
        $payload = $this->validatePayload([
            'payloadKey' => [
                'pendaftaran_id' => 'required',
                'pegawai_id' => 'required',
            ]
        ]);
        if (isset($payload['errors'])) {
            return $this->responseJson(422, 'Silakan cek kembali input', ['errors' => $payload['errors']]);
        } else {
            if(isset($payload['pendaftaran_id'])) {
                $payload['pendaftaran_id'] = DocoHelpers::decrypt($payload['pendaftaran_id']);
            }
            return $this->guzzleExec($this->_restFisioterapi, [
                'url' => 'informasi-pasien-fisioterapi/update-dpjp',
                'method' => 'POST',
                'returnResponse' => true,
                'payload' => [
                    'form_params' => $payload
                ]
            ]);
        }
    }
}
