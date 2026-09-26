<?php

namespace app\components\rabbitmq\eklaim;

use app\modules\v1\models\LogDokumenResepKronis;
use Doco\components\DocoHelpers;
use Doco\rabbitmq\task\IntegrasiTask;
use Yii;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

class DokumenResepKronisTask extends IntegrasiTask
{
    public function prosesSync()
    {
        $data = $this->data;
        $params = Yii::$app->params['iniFile'];
        $url_frontend = isset($params['server']['url']) ? $params['server']['url'] : 'http://web:8857/';
        $cookieJar = self::getCookieJar($url_frontend);
        $payload_logs = [];
        $row_delete = [];
        try {
            if (!empty($data) && is_array($data)) {
                $ids = array_column($data, 'penjualanresep_id');
                if (!empty($ids)) {
                    LogDokumenResepKronis::deleteAll(['penjualanresep_id' => $ids]);
                }
                foreach ($data as $value) {
                    try{
                        $folderFtp = $value['no_rekam_medik'] . '_' . $value['nama_pasien'] . '_' . $value['noresep'] . '/';
                        $paramsCetakRsp = [
                            'id' => $value['penjualanresep_id'],
                            'nomor' => DocoHelpers::encrypt($value['noresep']),
                            'type' => 'resep'
                        ];
        
                        // Cetakan Resep
                        $urlRsp = $url_frontend . '/apotek/informasi-reseptur/print-resep-detail';
                        $guzzle = new \GuzzleHttp\Client(['verify' => false]);
                        $headers = [
                            'Cache-Control' => 'no-cache',
                            'Content-Type' => 'Content-Disposition: attachment; filename="dokumen"',
                            'Content-Transfer-Encoding' => 'binary'
                        ];
                        $tmpName = $paramsCetakRsp['id'] . '_' . DocoHelpers::generateRandomString();
                        $tmpPath = Yii::getAlias('@runtime/'.$tmpName.'.pdf');
                        $guzzle->get($urlRsp, [
                            'query' => $paramsCetakRsp,
                            'save_to' => $tmpPath,
                            'cookies' => $cookieJar,
                            'http_errors' => false,
                            'headers' => $headers,
                            'verify' => false
                        ]);
                        DocoHelpers::uploadFileFtp(self::konfigFtp(), $folderFtp, (object)['tempName' => $tmpPath], 'Resep Dokter.pdf');
                        unlink($tmpPath);
        
                        // Cetakan Invoice
                        $urlReport = isset($params['report']) ? $params['report']['api_url'] : null;
                        $url_param = [
                            'invoice_id' => $value['pembayaran_id'],
                            'jenis_invoice' => 1,
                            'id_pegawai' => 1
                        ];
                        $query_parameter = [
                            'api-key' => isset($params['report']) ? $params['report']['api_key'] : null,
                            'ds_access' => isset($params['report']) ? $params['report']['schema'] : null,
                            'ds_kode' => 'new-invoice-detail'
                        ];
                        $mergeParameter = array_merge($query_parameter, $url_param);
                        $client = new Client([
                            'verify' => false,
                            'base_uri' => $urlReport
                        ]);
        
                        $tmpReportName = $paramsCetakRsp['id'] . '_' . DocoHelpers::generateRandomString();
                        $tmpPathReport = Yii::getAlias('@runtime/'.$tmpReportName.'.pdf');
                        $client->get('api/preview-pdf', [
                            'query' => $mergeParameter,
                            'save_to' => $tmpPathReport,
                            'http_errors' => false
                        ]);
                        DocoHelpers::uploadFileFtp(self::konfigFtp(), $folderFtp, (object)['tempName' => $tmpPathReport], 'Billing Detail.pdf');
                        unlink($tmpPathReport);
        
                        $payload_logs[] = [
                            'penjualanresep_id' => $value['penjualanresep_id'],
                            'response' => is_array($value) ? json_encode($value) : null,
                            'is_sent' => true
                        ];
                        
                        Yii::error(json_encode([
                            'service' => 'Sirs-DokumenResepKronis',
                            'status' => 'success',
                            'response' => $value,
                            'timestamp' => date('Y-m-d H:i:s'),
                        ]));
                    } catch (\Exception $th) {
                        $payload_logs[] = [
                            'penjualanresep_id' => $value['penjualanresep_id'],
                            'response' => is_array($value) ? json_encode($value) : null,
                            'is_sent' => false
                        ];
                        Yii::error(json_encode([
                            'service' => 'Sirs-DokumenResepKronis',
                            'status' => 'failed',
                            'response' => $th->getMessage(),
                            'line' => $th->getLine(),
                            'timestamp' => date('Y-m-d H:i:s'),
                        ]));
                    }
                }
                LogDokumenResepKronis::batchInsert($payload_logs);
            }
        } catch (\Exception $th) {
            Yii::error(json_encode([
                'service' => 'Sirs-DokumenResepKronis',
                'status' => 'failed',
                'response' => $th->getMessage(),
                'line' => $th->getLine(),
                'timestamp' => date('Y-m-d H:i:s'),
            ]));
        }
    }

    private static function konfigFtp()
    {
        $env = Yii::$app->params['iniFile'];
        return [
            'host' => isset($env['konfigftp']) ? $env['konfigftp']['host'] : null,
            'user' => isset($env['konfigftp']) ? $env['konfigftp']['username'] : null,
            'password' => isset($env['konfigftp']) ? $env['konfigftp']['password'] : null,
            'remotePath' => isset($env['konfigftp']) ? $env['konfigftp']['path_apotekonline'] : null
        ];
    }

    private static function getCookieJar($url_frontend) {
        $params = Yii::$app->params['iniFile'];
        $guzzle = new Client([
            'cookies' => true,
            'verify' => false,
            'headers' => [
                'Cache-Control' => 'no-cache',
                'Content-Type' => 'application/x-www-form-urlencoded'
            ]
        ]);
        $jar = new CookieJar();
        $guzzle->post(trim($url_frontend,'/') . '/site/login', [
            'form_params' => [
                'LoginForm[username]' => isset($params['authusereklaim']) ? $params['authusereklaim']['username'] : null,
                'LoginForm[password]' => isset($params['authusereklaim']) ? $params['authusereklaim']['password'] : null,
            ],
            'cookies' => $jar
        ]);

        $_csrf = $jar->getCookieByName("_csrf")->toArray();
        $sirs = $jar->getCookieByName("development-sirs")->toArray();
        return CookieJar::fromArray([
            '_csrf' => $_csrf['Value'],
            'development-sirs' => $sirs['Value']
        ], $sirs['Domain']);
    }
}