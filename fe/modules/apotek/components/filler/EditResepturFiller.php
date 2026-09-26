<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\components\filler;

use Exception;
use Yii;
use yii\base\ErrorException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use yii\helpers\ArrayHelper;

class EditResepturFiller {
    public static function getData($reseptur_id = null, $resep_id = null) {
        $return = [
                'data_resep'      => [],
                'detail_resep'    => [],
                'data_alergi'     => [],
                'data_signa'      => [],
                'konfig'          => [],
                'konfigsys'       => [],
                'satuan_unit'     => []
            ];

        try {
            $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
            $request = Yii::$app->request;
            $response = Yii::$app->docoRest->apotek->request('GET', 'transaksi-resep/filler-edit-reseptur', [
                'query' => [
                    'ruangan_id' => $ruangan_id,
                    'reseptur_id' => $reseptur_id,
                    'resep_id' => $resep_id
                ]
            ]);
            $row = [];
            $body = json_decode($response->getBody(),TRUE);
            $return = [
                'data_resep' => $body['response']['data']['resep'],
                'detail_resep' => $body['response']['data']['detailResep'],
                'data_racikan' => $body['response']['data']['obatRacikan'],
                'data_alergi' => $body['response']['data']['alergi'],
                'riwayat_personal' => $body['response']['data']['riwayat_personal'],
                'data_signa' => $body['response']['data']['signa'],
                'konfig' => $body['response']['data']['konfig'],
                'konfigsys' => $body['response']['data']['konfigSys'],
                'satuan_unit' => $body['response']['data']['satuan_unit'],
            ];

            return $return;
        } catch (RequestException $e) {
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public static function getDataReseptur($reseptur_id, $ruangan_id)
    {
        $apotek_api = Yii::$app->docoRest->apotek;
        $params = [
            'reseptur_id' => $reseptur_id,
            'ruangan_id' => $ruangan_id,
        ];
        try {
            $result = self::poolRequest($apotek_api, function() use ($params) {
                yield 'resep_data' => new Request('GET', 'transaksi-resep/get-reseptur-data?' . http_build_query($params));
                yield 'detail_resep' => new Request('GET', 'transaksi-resep/get-detail-resep?' . http_build_query($params));
                yield 'data_racikan' => new Request('GET', 'transaksi-resep/get-list-reseptur-racikan?' . http_build_query($params));
                yield 'data_signa' => new Request('GET', 'transaksi-resep/get-list-data-signa');
                yield 'satuan_unit' => new Request('GET', 'transaksi-resep/get-list-satuan-unit');
                yield 'konfig' => new Request('GET', 'transaksi-resep/get-konfig-farmasi');
                yield 'konfigsys' => new Request('GET', 'transaksi-resep/get-konfig-system');
                yield 'hargaobat' => new Request('GET', 'transaksi-resep/get-harga-obat?' . http_build_query($params));
            });
            $result['data_resep'] = $result['resep_data']['data_resep'];
            $result['data_alergi'] = $result['resep_data']['data_alergi'];
            $result['riwayat_personal'] = $result['resep_data']['riwayat_personal'];
            unset($result['resep_data']);
            return $result;
        } catch(RequestException $re) {
            Yii::error($re);
            return [];
        } catch(ErrorException $e) {
            Yii::error($e);
            return [];
        }
    }

    public static function getDataResep($resep_id, $ruangan_id)
    {
        $apotek_api = Yii::$app->docoRest->apotek;
        $params = [
            'resep_id' => $resep_id,
            'ruangan_id' => $ruangan_id,
        ];
        try {
            $result = self::poolRequest($apotek_api, function() use ($params) {
                yield 'resep_data' => new Request('GET', 'transaksi-resep/get-resep-data?' . http_build_query($params));
                yield 'detail_resep' => new Request('GET', 'transaksi-resep/get-detail-resep?' . http_build_query($params));
                yield 'data_signa' => new Request('GET', 'transaksi-resep/get-list-data-signa');
                yield 'satuan_unit' => new Request('GET', 'transaksi-resep/get-list-satuan-unit');
                yield 'konfig' => new Request('GET', 'transaksi-resep/get-konfig-farmasi');
                yield 'konfigsys' => new Request('GET', 'transaksi-resep/get-konfig-system');
                yield 'hargaobat' => new Request('GET', 'transaksi-resep/get-harga-obat?' . http_build_query($params));
            });

            $result['data_resep'] = $result['resep_data']['data_resep'];
            $result['data_alergi'] = $result['resep_data']['data_alergi'];
            $result['riwayat_personal'] = $result['resep_data']['riwayat_personal'];
            unset($result['resep_data']);
            return $result;
        } catch(RequestException $re) {
            Yii::error($re);
            return [];
        } catch(ErrorException $e) {
            Yii::error($e);
            return [];
        }
    }

    public static function makeRequest($requests = [])
    {
        if (!empty($requests)) {
            return function () use ($requests) {
                foreach($requests as $keyword => $request) {
                    yield $keyword => new Request('GET', ArrayHelper::getValue($request, 'url') . (isset($request['params']) ? http_build_query($request['params']) : ''));
                }
            };
        }
    }

    public static function poolRequest(Client $client, $requests)
    {
        $result = [];
        $pool = new Pool($client, $requests(), [
            'concurrency' => 10,
            'fulfilled' => function($response, $idx) use (&$result){
                $res = json_decode($response->getBody(), TRUE);
                $result[$idx] = $res['response'];
            },
            'rejected' => function($e, $idx) {
                $result[$idx] = [];
                $res = json_decode($e->getResponse()->getBody(), true);
                Yii::error($res);
            },
        ]);
        $promise = $pool->promise();
        $promise->wait();
        return $result;
    }
 }