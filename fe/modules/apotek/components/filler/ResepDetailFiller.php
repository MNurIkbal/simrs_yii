<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\components\filler;

use Exception;
use GuzzleHttp\Client;
use Yii;
use GuzzleHttp\Exception\RequestException;

class ResepDetailFiller {
    public static function getData($id = null) {
        $return = [
                'data_resep'      => [],
                'detail_resep'    => [],
                'data_dokter'     => [],
                'data_pasien'     => [],
                'data_alergi'     => [],
                'data_karyawan'   => [],
                'data_stok'       => [],
                'data_cara_bayar' => [],
                'data_penjamin'   => [],
                'data_pegawai'    => [],
                'data_signa'      => [],
                'konfig'          => [],
                'konfigsys'       => []
            ];
        try {
            $instalasi_id = Yii::$app->docoVars->workspace('instalasi_id');
            $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
            $request = Yii::$app->request;
            $response = Yii::$app->docoRest->apotek->request('GET', 'allow/ajax', [
                'query' => [
                    'instalasi_id' => $instalasi_id,
                    'ruangan_id' => $ruangan_id,
                    'reseptur_id' => $id
                ]
            ]);
            $row = [];
            $body = json_decode($response->getBody(),TRUE);
            $return = [
                'data_resep' => $body['response']['data-resep'],
                'detail_resep' => $body['response']['detail-resep'],
                'data_dokter' => $body['response']['data-dokter'],
                'data_pasien' => $body['response']['data-pasien'],
                'data_alergi' => $body['response']['data-alergi'],
                'data_karyawan' => $body['response']['data-karyawan'],
                'data_stok' => $body['response']['data-stok'],
                'data_cara_bayar' => $body['response']['data-cara-bayar'],
                'data_penjamin' => $body['response']['data-penjamin'],
                'data_pegawai' => $body['response']['data-pegawai'],
                'data_signa' => $body['response']['data-signa'],
                'konfig' => $body['response']['konfig'],
                'konfigsys' => $body['response']['konfigsys']
            ];

            return $return;
        } catch (RequestException $e) {
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public static function getInfoResepData(Client $client, $jenis, $param_ids = [])
    {
        $resep_id          = isset($param_ids['resep_id']) ? $param_ids['resep_id'] : null;
        $reseptur_id       = isset($param_ids['reseptur_id']) ? $param_ids['reseptur_id'] : null;
        $penjualanresep_id = isset($param_ids['penjualanresep_id']) ? $param_ids['penjualanresep_id'] : null;
        $pasien_id         = isset($param_ids['pasien_id']) ? $param_ids['pasien_id'] : null;
        $pendaftaran_id    = isset($param_ids['pendaftaran_id']) ? $param_ids['pendaftaran_id'] : null;

        $requests = EditResepturFiller::makeRequest([
            'info_resep_pasien' => [
                'url' => 'allow/get-info-resep-data?',
                'params' => [
                    'id_name' => $jenis == 'reseptur' ? 'reseptur_id' : 'resep_id',
                    'id_value' => $jenis == 'reseptur' ? $reseptur_id : $resep_id,
                    'has_reseptur' => !empty($reseptur_id),
                    'pasien_id' => $pasien_id,
                    'pendaftaran_id' => $pendaftaran_id,
                ]
            ],
            'detail_penjualan_data' => [
                'url' => 'inf-reseptur/get-penjualan-resep-data?',
                'params' => [
                    'reseptur_id' => $reseptur_id,
                    'penjualanresep_id' => $penjualanresep_id,
                ]
            ],
            'konfig_farmasi' => [
                'url' => 'transaksi-resep/get-konfig-farmasi?',
                'params' => [
                    'selected' => ['enable_split_kronis'],
                    'cache_duration' => 1800
                ]
            ],
        ]);
        try {
            $responses = EditResepturFiller::poolRequest($client, $requests);
            return self::responsesKeyParse($responses, ['info_resep_pasien', 'detail_penjualan_data'], ['konfig_farmasi']);
        } catch(RequestException $re) {
            Yii::error(['RequestException' => $re]);
            return [];
        } catch(Exception $e) {
            Yii::error(['Exception' => $e]);
            return [];
        }
    }

    protected static function responsesKeyParse($responses, $details = [], $keeps = [])
    {
        $result = [];
        foreach ($responses as $key => $response) {
            if (in_array($key, $keeps)) {
                $result[$key] = $response;
            }
            if (in_array($key, $details)) {
                foreach ($response as $k => $v) {
                    $result[$k] = $v;
                }
            }
        }
        return ['response' => $result];
    }
}