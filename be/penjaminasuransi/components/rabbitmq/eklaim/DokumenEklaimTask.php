<?php

namespace app\components\rabbitmq\eklaim;

use app\modules\v1\models\DokumenEklaimParamV;
use app\modules\v1\models\DokumenUploadT;
use app\modules\v1\models\LogDokumenEklaim;
use app\modules\v1\models\KonfigDokumenEklaimK;
use app\modules\v1\models\SyKunjunganPasien;
use Doco\components\DocoConstants;
use Doco\components\DocoConstansId;
use Doco\components\DocoHelpers;
use Doco\models\SuratKeteranganPasien;
use Doco\rabbitmq\task\IntegrasiTask;
use Exception;
use Yii;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use mikemadisonweb\rabbitmq\components\ConsumerInterface;

class DokumenEklaimTask extends IntegrasiTask
{
   /**
    * Main execute
    * 
    * @author Maulana Muhammad Rizky
    */
   public function prosesSync()
   {
      $kunjunganId = $this->kunjungan_id;
      $kunjunganId = [$kunjunganId];
      $params = Yii::$app->params['iniFile'];
      try {
         $dir = dirname(dirname(dirname(dirname(__DIR__))));
         $rootPath = 'uploads';
         $path = $dir . '/' . $rootPath;

         // Cari data kunjungan dulu.
         $dataKunjungan = SyKunjunganPasien::find()
            ->select([
               'no_pendaftaran'
            ])
            ->where(['IN', 'kunjungan_id', $kunjunganId])
            ->asArray()
            ->all();

         foreach ($dataKunjungan as $key => $value) {
            $pathNew = $path . '/' . $value['no_pendaftaran'];
            if (!file_exists($pathNew)) {
               mkdir($pathNew, 0777);
            }
            @chmod($pathNew, 0777);
         }

         $listDoc = KonfigDokumenEklaimK::find()->asArray()->all();
         $dataKunjungan = SyKunjunganPasien::find()
            ->select([
               'no_pendaftaran',
               'nama_pasien',
               'nosep',
               'no_rekammedik'
            ])
            ->where(['IN', 'kunjungan_id', $kunjunganId])
            ->asArray()->all();

         $tmpNoPendaftaran = [];
         $tmpKunjungan = [];
         if (empty($dataKunjungan)) {
            return false;
         }

         foreach ($dataKunjungan as $key => $value) {
            if (!in_array($value['no_pendaftaran'], $tmpNoPendaftaran)) {
               $tmpNoPendaftaran[] = $value['no_pendaftaran'];
               $tmpKunjungan[] = $value;
            }
         }

         $tmpReferencesId = [];
         $references = DokumenEklaimParamV::find()
            ->where(['IN', 'no_pendaftaran', $tmpNoPendaftaran])
            ->asArray()
            ->all();

         if (!empty($references)) {
            foreach ($references as $key => $referencesId) {
               if ($referencesId['ref_pendaftaran_id'] != null) {
                  if (!in_array($referencesId['ref_pendaftaran_id'], $tmpReferencesId)) {
                     $tmpReferencesId[$referencesId['no_pendaftaran']] = $referencesId['ref_pendaftaran_id'];
                  }
               }
            }
         }

         if (!empty($tmpReferencesId)) {
            $refrPendaftaran = DokumenEklaimParamV::find()
               ->where(['IN', 'pendaftaran_id', $tmpReferencesId])
               ->asArray()
               ->all();

            foreach ($refrPendaftaran as $key => $valRef) {
               foreach ($tmpReferencesId as $keyRef => $value) {
                  if ($value == $valRef['pendaftaran_id']) {
                     $tmpReferencesId[$keyRef] = $valRef['no_pendaftaran'];
                  }
               }
            }

            $valueParam = array_merge($references, $refrPendaftaran);
         } else {
            $valueParam = $references;
         }

         /**
          * Generate login and cookies.
          */
         $url_frontend = isset($params['server']['url']) ? $params['server']['url'] : 'http://web:8857/';
         $guzzle = new Client([
            'cookies' => true,
            'verify' => false,
            'headers' => [
               'Cache-Control' => 'no-cache',
               'Content-Type' => 'application/x-www-form-urlencoded'
            ]
         ]);
         $jar = new CookieJar();
         $guzzle->post($url_frontend . '/site/login', [
            'form_params' => [
               'LoginForm[username]' => isset($params['authusereklaim']) ? $params['authusereklaim']['username'] : null,
               'LoginForm[password]' => isset($params['authusereklaim']) ? $params['authusereklaim']['password'] : null,
            ],
            'cookies' => $jar
         ]);

         $_csrf = $jar->getCookieByName("_csrf")->toArray();
         $sirs = $jar->getCookieByName("development-sirs")->toArray();
         $cookieJar = null;
         $bearerToken = null;

         if (!empty($_csrf) && !empty($sirs)) {

            $cookieJar = CookieJar::fromArray([
               '_csrf' => $_csrf['Value'],
               'development-sirs' => $sirs['Value']
            ], $sirs['Domain']);

            $setModule = new Client([
               'verify' => false,
               'headers' => [
                  'Cache-Control' => 'no-cache',
                  'Content-Type' => 'application/json'
               ]
            ]);

            $setModule->post($url_frontend . '/site/set-method', [
               'form_params' => [
                  'moduleID' => DocoConstants::MODUL_EKLAIM,
                  'instalasiIndex' => 0,
                  'instalasiID' => DocoConstants::INSTALASI_EKLAIM,
                  'roomIndex' => 0,
                  'roomID' => DocoConstants::RUANGAN_EKLAIM
               ],
               'cookies' => $cookieJar
            ]);
            $bearerToken = $this->AuthLogin();
         }

         $tmpUrl = [];
         $tmpPendaftaranId = [];
         foreach ($valueParam as $key => $value) {
            if (!isset($tmpPendaftaranId[$value['pendaftaran_id']])) {

               $refGabil = array_keys($tmpReferencesId, $value['no_pendaftaran']);
               if (!empty($refGabil)) {
                  $tmpPendaftaranId[$value['pendaftaran_id']] = [
                     'pendaftaran_id' => $value['pendaftaran_id'],
                     'no_pendaftaran' => $refGabil[0],
                     'raw_pendaftaran' => $value['no_pendaftaran']
                  ];
               } else {
                  $tmpPendaftaranId[$value['pendaftaran_id']] = [
                     'pendaftaran_id' => $value['pendaftaran_id'],
                     'no_pendaftaran' => $value['no_pendaftaran'],
                     'raw_pendaftaran' => $value['no_pendaftaran']
                  ];
               }
            }
         }

         foreach ($tmpKunjungan as $key => $valJ) {
            foreach ($tmpPendaftaranId as $keyId => $valId) {
               $remote_file = isset($params['konfigftp']) ? $params['konfigftp']['path'] : null;
               $valId['remote_file'] = $remote_file . $valJ['no_rekammedik'] . '_' . $valJ['nama_pasien'] . '_' . $valJ['nosep'];
               $tmpPendaftaranId[$keyId] = $valId;
            }
         }

         $awal = $judul = $daftar_id = [];
         foreach ($tmpPendaftaranId as $key => $value) {
            if (!in_array($key, $awal)) {
               $awal[] = $key;
            }
         }

         LogDokumenEklaim::updateAll(['is_deleted' => true, 'is_active' => false], ['and',
            ['in', 'pendaftaran_id', $awal[0]],
            ['type_dokumen' => DocoConstants::KONFIG_DOKUMEN]
         ]);

         if (!empty($tmpPendaftaranId)) {
            $this->DokumenUploadFtp($tmpPendaftaranId, $cookieJar);
            $this->DokumenSuratKeterangan($tmpPendaftaranId, $path);
         }
         foreach ($listDoc as $docKey =>  $doc) {
            if ($doc['type'] == 'document') {
               foreach ($valueParam as $key => $val) { // data user
                  $paramsArray = [];
                  $valParam = null;
                  $skipDokumen = false;
                  foreach (json_decode($doc['params'], true) as $attr => $param) {
                     if (is_null($param['fixed_value'])) {
                        if (!empty($val[$param['column']])) {
                           // Kondisi ada.
                           if (isset($param['is_integer']) == true) {
                              $valParam = $param['encrypt'] ? DocoHelpers::encrypt($val[$param['column']]) : intval($val[$param['column']]);
                           } else {
                              $valParam = $param['encrypt'] ? DocoHelpers::encrypt($val[$param['column']]) : $val[$param['column']];
                           }
                           if (isset($param['change_doc_name'])) {
                              $doc['nama_dokumen'] = isset($val[$param['change_doc_name']]) ? preg_replace('/\//', ' ', $val[$param['change_doc_name']]) : $doc['nama_dokumen'];
                           }
                        } else {
                           $skipDokumen = true;
                           break;
                        }
                     } else {
                        $valParam = $param['fixed_value'];
                     }
                     $paramsArray[$attr] = $valParam;
                  }

                  if ($skipDokumen) continue;
                  if (!$doc['is_reportdesigner']) {
                     $url_doc = $doc['base_url'] . $doc['doc_url'] . http_build_query($paramsArray);
                  } else {
                     $url_doc = isset($params['report']) ? $params['report']['api_url'] : $doc['base_url'];
                  }
                  $validationData = json_decode($doc['validation'], true);

                  /**
                   * Kondisi untuk menyimpan folder dari gabung billing
                   */
                  if (in_array($val['no_pendaftaran'], $tmpReferencesId)) {
                     $data = array_keys($tmpReferencesId, $val['no_pendaftaran']);
                     if (!empty($data)) {
                        $folderPendaftaran = isset($data[0]) ? $data[0] : "Folderkosong";
                        if ($doc['is_multiple']) {
                           $pathUri = $path . '/' . $folderPendaftaran . '/' . $doc['nama_dokumen'] . ' - ' . $val['no_pendaftaran'] . '-' . $key . '.pdf';
                           $nama_doc = $doc['nama_dokumen'] . ' - ' . $val['no_pendaftaran'] . '-' . $key . '.pdf';
                        } else {
                           $pathUri = $path . '/' . $folderPendaftaran . '/' . $doc['nama_dokumen'] . ' - ' . $val['no_pendaftaran'] . '.pdf';
                           $nama_doc = $doc['nama_dokumen'] . ' - ' . $val['no_pendaftaran'] . '.pdf';
                        }
                     }
                  } else {
                     if ($doc['is_multiple']) {
                        $pathUri = $path . '/' . $val['no_pendaftaran'] . '/' . $doc['nama_dokumen'] . '-' . $key . '.pdf';
                        $nama_doc = $doc['nama_dokumen'] . '-' . $key . '.pdf';
                     } else {
                        $pathUri = $path . '/' . $val['no_pendaftaran'] . '/' . $doc['nama_dokumen'] . '.pdf';
                        $nama_doc = $doc['nama_dokumen'] . '.pdf';
                     }
                  }
                  
                  $judul[$nama_doc] = $doc['konfig_dokumen_id'];
                  $daftar_id[$nama_doc] = $awal[0];

                  /**
                   * Set Unique Code.
                   */
                  $uniqueValue = [
                     'pend' => $val['no_pendaftaran'],
                     'doc_id' => $doc['konfig_dokumen_id']
                  ];
                  $uniqueString = http_build_query(array_merge($uniqueValue, $paramsArray));

                  if (!empty($validationData) && is_array($validationData)) {
                     foreach ($validationData as $key => $dataValidasi) {
                        switch ($key) {
                           case 'instalasi_id':
                              if (in_array($val['instalasi_id'], $dataValidasi)) {
                                 $data = [
                                    'raw_url' => $doc['base_url'] . $doc['doc_url'],
                                    'url' => $url_doc,
                                    'is_report' => $doc['is_reportdesigner'],
                                    'is_bgprocess' => $doc['is_bgprocess'],
                                    'nama_dokumen' => $doc['nama_dokumen'],
                                    'kode_dokumen' => $doc['kode_report'],
                                    'unique_code' => $uniqueString,
                                    'query_param' => $paramsArray,
                                    'path' => $pathUri
                                 ];

                                 /**
                                  * Handle kondisi RJ.
                                  */
                                 if ($val['instalasi_id'] == DocoConstants::VAR_I_RJ) {
                                    $tmpUrl[] = $data;
                                 }

                                 /**
                                  * Handle kondisi IGD NON RUJUK.
                                  */
                                 if ($val['instalasi_id'] == DocoConstants::VAR_I_RD && empty($val['pasienadmisi_id'])) {
                                    $tmpUrl[] = $data;
                                 }

                                 /**
                                  * Handle kondisi RI.
                                  */
                                 if ($val['instalasi_id'] == DocoConstants::VAR_I_RANAP) {
                                    if (!empty($val['pasienadmisi_id'])) {
                                       $tmpUrl[] = $data;
                                    }
                                 }
                              }
                              break;
                           case 'admisi_igd':
                              /**
                               * Handle kondisi CETAKAN RD RUJUK RANAP.
                               */
                              if (!empty($val['pasienadmisi_id']) && $val['instalasi_id'] == DocoConstants::VAR_I_RD) {
                                 $tmpUrl[] = [
                                    'raw_url' => $doc['base_url'] . $doc['doc_url'],
                                    'url' => $url_doc,
                                    'is_report' => $doc['is_reportdesigner'],
                                    'is_bgprocess' => $doc['is_bgprocess'],
                                    'nama_dokumen' => $doc['nama_dokumen'],
                                    'kode_dokumen' => $doc['kode_report'],
                                    'unique_code' => $uniqueString,
                                    'query_param' => $paramsArray,
                                    'path' => $pathUri
                                 ];
                              }
                              break;
                           default:
                              break;
                        }
                     }
                  } else {
                     $payloadData = [
                        'raw_url' => $doc['base_url'] . $doc['doc_url'],
                        'url' => $url_doc,
                        'is_report' => $doc['is_reportdesigner'],
                        'is_bgprocess' => $doc['is_bgprocess'],
                        'nama_dokumen' => $doc['nama_dokumen'],
                        'kode_dokumen' => $doc['kode_report'],
                        'unique_code' => $uniqueString,
                        'query_param' => $paramsArray,
                        'path' => $pathUri
                     ];
                     if ($doc['is_multiple']) {
                        $payloadData['path'] = $pathUri;
                        $tmpUrl[] = $payloadData;
                     } else {
                        $tmpUrl[] = $payloadData;
                     }
                  }
               }
            }
         }

         $cleanUrlData = [];
         foreach ($tmpUrl as $key => $value) {
            if (!in_array($value['unique_code'], $cleanUrlData)) {
               $cleanUrlData[$value['unique_code']] = $value;
            }
         }

         if (!empty($cleanUrlData)) {
            foreach ($cleanUrlData as $key => $value) {
               $pathUrl = $value['path'];
               if ($value['is_report']) {
                  if (!file_exists($pathUrl)) {
                     $query_parameter = [
                        'api-key' => isset($params['report']) ? $params['report']['api_key'] : null,
                        'ds_access' => isset($params['report']) ? $params['report']['schema'] : null,
                        'ds_kode' => $value['kode_dokumen']
                     ];
                     $mergeParameter = array_merge($query_parameter, $value['query_param']);
                     $client = new Client([
                        'verify' => false,
                        'base_uri' => $value['url']
                     ]);

                     $client->get('api/preview-pdf', [
                        'query' => $mergeParameter,
                        'save_to' => $pathUrl,
                        'http_errors' => false
                     ]);

                     Yii::error(json_encode([
                        'query' => $mergeParameter,
                        'save_to' => $pathUrl,
                        "value" => $value
                     ]));
                  }
               } else {
                  if (!file_exists($pathUrl)) {
                     $guzzle = new Client([
                        'cookies' => true,
                        'verify' => false
                     ]);

                     if ($value['is_bgprocess']) {
                        $headers = [
                           'Authorization' => $bearerToken,
                           'X-Owner' => 'YmRnLXNpbXJzLWRvY28tZGV2ZWxvcG1lbnQ'
                        ];
                        $value['query_param']['nama_dokumen'] = $value['nama_dokumen'];
                        $value['query_param']['is_ftp'] = true;
                        $response = $guzzle->get($value['raw_url'], [
                           'http_errors' => false,
                           'headers' => $headers,
                           'query' => $value['query_param']
                        ]);
                        Yii::error(json_encode($response->getBody()));
                     } else {
                        $headers = [
                           'Cache-Control' => 'no-cache',
                           'Content-Type' => 'Content-Disposition: attachment; filename="dokumen"',
                           'Content-Transfer-Encoding' => 'binary'
                        ];
                        $guzzle->get($value['url'], [
                           'save_to' => $pathUrl,
                           'cookies' => $cookieJar,
                           'http_errors' => false,
                           'headers' => $headers
                        ]);
                     }
                  }
                  Yii::error(json_encode([
                     'save_to' => $pathUrl
                  ]));
               }
            }
         }

         // kode buat connect ke FTP. ====================================================
         $host = isset($params['konfigftp']) ? $params['konfigftp']['host'] : null;
         $user = isset($params['konfigftp']) ? $params['konfigftp']['username'] : null;
         $password = isset($params['konfigftp']) ? $params['konfigftp']['password'] : null;
         $ftpConn = ftp_connect($host);
         $login = ftp_login($ftpConn, $user, $password);
         $payload_log = [];
         ftp_pasv($ftpConn, true);

         if ((!$ftpConn) || (!$login)) {
            Yii::error('FTP connection has failed! Attempted to connect to ' . $host . ' for user ' . $user . '.');
         } else {
            if (!empty($tmpKunjungan)) {
               foreach ($tmpKunjungan as $value) {
                  $remote_file = isset($params['konfigftp']) ? $params['konfigftp']['path'] : null;
                  $remote_file = $remote_file . $value['no_rekammedik'] . '_' . $value['nama_pasien'] . '_' . $value['nosep'];
                  $dirExists = ftp_nlist($ftpConn, $remote_file);
                  if ($dirExists == false) {
                     @ftp_mkdir($ftpConn, $remote_file);
                     // ftp_chmod($ftpConn, 0777, $remote_file); Windows Server belum support
                  }

                  $totalFile = glob($path . '/' . $value['no_pendaftaran'] . "/*.*");
                  if (!empty($totalFile)) {
                     foreach ($totalFile as $sourceFile) {
                        $ext = pathinfo($sourceFile, PATHINFO_EXTENSION);
                        $status = null;
                        if (strtolower($ext) == 'pdf') {
                           $fp = fopen($sourceFile, 'r');
                           fseek($fp, 0);
                           $data = fread($fp, 5);
                           if (strcmp(trim($data), "%PDF") == 0 || strcmp($data, "%PDF-") == 0) {
                              $filename = pathinfo($sourceFile, PATHINFO_BASENAME);
                              if(ftp_put($ftpConn, $remote_file . '/' . $filename, $sourceFile, FTP_BINARY)){
                                 $status = true;
                              }else{
                                 $status = false;
                              }
                           }
                           fclose($fp);
                        } else {
                           $filename = pathinfo($sourceFile, PATHINFO_BASENAME);
                           if(ftp_put($ftpConn, $remote_file . '/' . $filename, $sourceFile, FTP_BINARY)){
                              $status = true;
                           }else{
                              $status = false;
                           }
                        }
                        if(isset($judul[pathinfo($sourceFile, PATHINFO_BASENAME)])){
                           $payload_log[] = [
                              'dokumen_id' => $judul[pathinfo($sourceFile, PATHINFO_BASENAME)],
                              'pendaftaran_id' => $daftar_id[pathinfo($sourceFile, PATHINFO_BASENAME)],
                              'status' => $status
                           ];
                        }
                        unlink($sourceFile);
                     }
                     rmdir($path . '/' . $value['no_pendaftaran']);
                  }
               }
               SyKunjunganPasien::updateAll(['status_unduh_dokumen' => DocoConstants::SELESAI_UNDUH_DOKUMN], ['in', 'kunjungan_id', $kunjunganId]);
            }
         }
         ftp_close($ftpConn);

         $pendId = [];
         foreach ($tmpPendaftaranId as $key => $value) {
            if (!in_array($key, $pendId)) {
               $pendId[] = $key;
            }
         }

         if(!empty($payload_log)){
            $send_data = [
               'log' => $payload_log,
               'type' => 'konfig_dokumen'
            ];
            $this->saveLogEklaim($send_data, $pendId[0]);
         }
         $this->logoutCookies($url_frontend, $cookieJar);

         Yii::error(json_encode([
            'service' => 'Sirs-IntegrasiDokumenEklaim',
            'payload' => $this,
            'response' => $cleanUrlData,
            'timestamp' => date('Y-m-d H:i:s'),
         ]));
      } catch (\Exception $th) {
         Yii::error(json_encode([
            'service' => 'Sirs-IntegrasiDokumenEklaim',
            'payload' => $this,
            'response' => $th->getMessage(),
            'line' => $th->getLine(),
            'timestamp' => date('Y-m-d H:i:s'),
         ]));
      }
   }

   /**
    * Function untuk melakukan logout ketika sudah selesai.
    *
    * @author Maulana Muhammad Rizky
    */
   private function logoutCookies($url_frontend, $cookieJar)
   {
      $setModule = new Client([
         'verify' => false,
         'headers' => [
            'Cache-Control' => 'no-cache',
            'Content-Type' => 'application/json'
         ]
      ]);

      $setModule->post($url_frontend . '/site/logout', [
         'cookies' => $cookieJar
      ]);
   }

   /**
    * Auth login
    *
    * @author Maulana
    */
   private function AuthLogin()
   {
      $params = Yii::$app->params['iniFile'];
      $baseConfig = isset($params['authusereklaim']) ? $params['authusereklaim'] : [];
      $urlBackend = isset($params['rabbitMq']['url_backend']) ? $params['rabbitMq']['url_backend'] : 'http://web:8858/';
      $docoRest = new Client([
         'base_uri' => $urlBackend . 'dcms/v1/',
         'verify' => false,
         'headers' => [
            'user-agent' => 'cli',
         ]
      ]);

      if (!empty($baseConfig)) {
         $response = $docoRest->post('auth/get-token', [
            'form_params' => [
               'username' => $baseConfig['username'],
               'password' => $baseConfig['password']
            ]
         ]);
         $response = json_decode($response->getBody(), true);
         $token = isset($response['response']['access_token']) ? $response['response']['access_token'] : null;

         $bearer = 'Bearer ' . $token;
         Yii::error(json_encode([
            'token_user' => $bearer
         ]));
         return $bearer;
      }

      return null;
   }


   /**
    * Get data from upload dokumen. 
    *
    * @author Maulana Muhammad Rizky.
    */
   private function GetDokumenUpload($pendaftaranId)
   {
      $dokumen = DokumenUploadT::find()
         ->select([
            'dokumenupload_t.pendaftaran_id',
            'dokumenupload_t.path',
            'dokumenupload_t.filename',
            'pendaftaran_t.no_pendaftaran'
         ])
         ->leftJoin('pendaftaran_t', 'pendaftaran_t.pendaftaran_id = dokumenupload_t.pendaftaran_id')
         ->where(['IN', 'dokumenupload_t.pendaftaran_id', $pendaftaranId])
         ->andWhere(['dokumenupload_t.is_eklaim' => true])
         ->asArray()->all();

      if (!empty($dokumen)) {
         foreach ($dokumen as $key => $value) {
            $dir = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
            $backend = dirname(dirname(dirname(dirname(__DIR__))));
            $pathUrl = $backend . '/uploads/' . $value['no_pendaftaran'];
            $pathDokumen = $dir . '/backend/' . $value['path'] . '/' . $value['filename'];
            if (!file_exists($pathUrl)) {
               mkdir($pathUrl, 0777);
            }

            if (file_exists($pathDokumen)) {
               copy($pathDokumen, $backend . '/uploads/' . $value['no_pendaftaran'] . '/' . $value['filename']);
            }
         };
      }
   }

   /**
    * Process dokumen upload FTP
    *
    * @author Maulana Muhammad Rizky.
    * @return void
    */
   private function DokumenUploadFtp($bulkArray, $cookieJar)
   {
      $params = Yii::$app->params['iniFile'];
      $host = isset($params['server']) ? $params['server']['url'] : null;

      $guzzle = new Client([
         'cookies' => true,
         'verify' => false
      ]);

      $pendId = [];
      foreach ($bulkArray as $key => $value) {
         if (!in_array($key, $pendId)) {
            $pendId[] = $key;
         }
      }

      $response = $guzzle->get($host . '/penjamin-asuransi/informasi-pasien-ranap-bpjs/upload-dokumen-eklaim', [
         'cookies' => $cookieJar,
         'http_errors' => false,
         'query' => [
            'pendaftaran_id' => $pendId,
            'remote_url' => $bulkArray
         ]
      ]);
      $body = json_decode($response->getBody(), true);
      if(isset($body['data']['send_data'])) {
         $this->saveLogEklaim($body['data']['send_data'], $pendId[0]);
      }
      Yii::error(json_encode([
         'service' => 'Sirs-Upload Dokumen',
         'url' => $host . '/penjamin-asuransi/informasi-pasien-ranap-bpjs/upload-dokumen-eklaim',
         'message' => $body
      ]));
   }

   /**
    * Function for send Surat Keterangan.
    *
    * @author Maulana Muhammad Rizky.
    */
   protected function DokumenSuratKeterangan($bulkArray, $path)
   {
      $params = Yii::$app->params['iniFile'];
      $pendId = [];
      $pendaRef = [];
      foreach ($bulkArray as $key => $value) {
         $pendaRef["referensi"] = [
            'no_pendaftaran' => $value['no_pendaftaran'],
            'remote_file' => $value['remote_file']
         ];
         if (!in_array($key, $pendId)) {
            $pendId[] = $key;
         }
      }

      $suratKeterangan = SuratKeteranganPasien::find()
         ->select([
            'surat_m.code_report',
            'surat_m.judul_surat',
            'surat_keterangan_pasien_t.pendaftaran_id',
            'surat_keterangan_pasien_t.surat_keterangan_id'
         ])
         ->join('JOIN', 'surat_keterangan_m surat_m', 'surat_m.surat_keterangan_id = surat_keterangan_pasien_t.surat_keterangan_id')
         ->where(['pendaftaran_id' => $pendId, 'surat_keterangan_pasien_t.is_eklaim' => true])
         ->asArray()
         ->all();

      $remote_file = ArrayHelper::getValue($pendaRef['referensi'], 'remote_file');
      $no_pendaftaran = ArrayHelper::getValue($pendaRef['referensi'], 'no_pendaftaran');
      $judul = $daftar_id = [];
      if (!empty($suratKeterangan)) {
         foreach ($suratKeterangan as $key => $value) {
            $query_parameter = [
               'api-key' => isset($params['report']) ? $params['report']['api_key'] : null,
               'ds_access' => isset($params['report']) ? $params['report']['schema'] : null,
               'ds_kode' => $value['code_report']
            ];

            $queryParam = [
               'pendaftaran_id' => $value['pendaftaran_id'],
               'surat_keterangan_id' => $value['surat_keterangan_id']
            ];
            $mergeParameter = array_merge($query_parameter, $queryParam);

            $client = new Client([
               'verify' => false,
               'base_uri' => isset($params['report']) ? $params['report']['api_url'] : null
            ]);

            $rawPendaftaran =  isset($bulkArray[$value['pendaftaran_id']]) ? $bulkArray[$value['pendaftaran_id']]['raw_pendaftaran'] : $no_pendaftaran;
            $judulPurify = preg_replace('/\//', ' ', $value['judul_surat']);
            $client->get('api/preview-pdf', [
               'query' => $mergeParameter,
               'save_to' => $path . '/' . $no_pendaftaran . '/' . $judulPurify . '-' . $rawPendaftaran . '.pdf',
               'http_errors' => false
            ]);
            $judul[$judulPurify . '-' . $rawPendaftaran . '.pdf'] = $value['surat_keterangan_id'];
            $daftar_id[$judulPurify . '-' . $rawPendaftaran . '.pdf'] = $pendId[0];
         }
         $payload_log = [];
         $ftpConn = $this->FtpConnection($params); // Connect to FTP.

         $totalFile = glob($path . '/' .  $no_pendaftaran . "/*.*");
         $dirExists = ftp_nlist($ftpConn, $remote_file);
         if ($dirExists == false) {
            @ftp_mkdir($ftpConn, $remote_file);
         }

         if ($totalFile != false || !empty($totalFile)) {
            foreach ($totalFile as $key => $sourceFile) {
               $ext = pathinfo($sourceFile, PATHINFO_EXTENSION);
               $status = false;
               if (strtolower($ext) == 'pdf') {
                  $fp = fopen($sourceFile, 'r');
                  fseek($fp, 0);
                  $data = fread($fp, 5);
                  if (strcmp(trim($data), "%PDF") == 0 || strcmp($data, "%PDF-") == 0) {
                     $filename = pathinfo($sourceFile, PATHINFO_BASENAME);
                     if(ftp_put($ftpConn, $remote_file . '/' . $filename, $sourceFile, FTP_BINARY)){
                        $status = true;
                     }else{
                        $status = false;
                     }
                  }
                  fclose($fp);
               } else {
                  $filename = pathinfo($sourceFile, PATHINFO_BASENAME);
                  if(ftp_put($ftpConn, $remote_file . '/' . $filename, $sourceFile, FTP_BINARY)){
                     $status = true;
                  }else{
                     $status = false;
                  }
               }

               if(isset($judul[pathinfo($sourceFile, PATHINFO_BASENAME)])){
                  $payload_log[] = [
                     'dokumen_id' => $judul[pathinfo($sourceFile, PATHINFO_BASENAME)],
                     'pendaftaran_id' => $daftar_id[pathinfo($sourceFile, PATHINFO_BASENAME)],
                     'status' => $status
                  ];
               }
               unlink($sourceFile);
            }
         }
         ftp_close($ftpConn);

         $send_data = [
            'log' => $payload_log,
            'type' => 'surat_keterangan'
         ];
         $this->saveLogEklaim($send_data, $pendId[0]);

         Yii::error(json_encode([
            'service' => 'Sirs-Surat-Keterangan',
            'data' => $totalFile
         ]));
      }
   }

   /**
    * Connect to FTP.
    * 
    * @author Maulana Muhammad Rizky.
    */
   protected function FtpConnection($params)
   {
      $host = isset($params['konfigftp']) ? $params['konfigftp']['host'] : null;
      $user = isset($params['konfigftp']) ? $params['konfigftp']['username'] : null;
      $password = isset($params['konfigftp']) ? $params['konfigftp']['password'] : null;
      $ftpConn = ftp_connect($host);
      $login = ftp_login($ftpConn, $user, $password);
      ftp_pasv($ftpConn, true);

      return $ftpConn;
   }

   /**
    * Function for save log.
    *
    * @author Asri Nurul M.
    */
   protected function saveLogEklaim($logData, $pendaftaranId){
      $log = $logData['log'];
      $type = $logData['type'];
      $insert_log = [];
      foreach ($log as $key => $value) {
            $insert_log_temp['type_dokumen'] = $type;
            $insert_log_temp['dokumen_id'] = $value['dokumen_id'];
            $insert_log_temp['pendaftaran_id'] = $value['pendaftaran_id'];
            $insert_log_temp['status'] = $value['status'];
            $insert_log[] = $insert_log_temp;
      }

      if ($insert_log) {
            $list_columns = [
               'type_dokumen',
               'dokumen_id',
               'pendaftaran_id',
               'status',
            ];
            
            if($type != DocoConstants::KONFIG_DOKUMEN){
               LogDokumenEklaim::updateAll(['is_deleted' => true, 'is_active' => false], ['and',
                  ['in', 'pendaftaran_id', $pendaftaranId],
                  ['type_dokumen' => $type]
               ]);
            }

            LogDokumenEklaim::batchInsert($insert_log);

            return [
               'message' => 'Data Berhasil di simpan',
               'status' => 200
            ];
      } else {
            return [
               'message' => 'Data Gagal disimpan',
               'status' => 422
            ];
      }
   }
}