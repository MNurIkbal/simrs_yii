<?php

namespace app\components\rabbitmq\autoRegister;

use Yii;
use Doco\rabbitmq\task\IntegrasiTask;
use GuzzleHttp\Client;
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use app\modules\v1\cache\Cache;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\BpjsRabbitMq as Bpjs;
use app\modules\v1\models\InfoPendaftaranOlView;
use app\modules\v1\models\Diagnosa;
use Doco\models\bpjs\BpjsRujukanKhususT;


class BulkRegisterReservasiTask extends IntegrasiTask 
{
  protected $reservationItem;
  protected $processKey;
  private $responseMessage = 'Silahkan lakukan pendaftaran secara manual melalui tombol Setujui.';
  private $guzzleClient;
  protected $keyConfig = 'auth_mcare';

  public function prosesSync()
  {
    $params = Yii::$app->params;
    $urlBackend = isset($params['url_backend']) ? $params['url_backend'] : 'http://web:8858/';
    $isBpjs = (int) ArrayHelper::getValue($this->reservationItem, 'carabayar_id', null) === DocoConstants::VAR_ID_CARABAYAR_BPJS;
    $konfigTarif = Cache::getKonfigTarif();
    $konfigSystem = Cache::getKonfigSystem();
    $kelasDefault = ArrayHelper::getValue($konfigSystem, 'kelas_pelayanan', null);
    $decodeKelasPelayanan =  json_decode($kelasDefault,true);
    $kelasPelayananId = ArrayHelper::getValue($konfigTarif, 'default_kelas', null);
    if (isset($decodeKelasPelayanan[0]) && !empty($decodeKelasPelayanan[0])) {
      $kelasPelayananId = $decodeKelasPelayanan[0];
    }
    $pendaftaranOlID = ArrayHelper::getValue($this->reservationItem, 'pendaftaranol_id', null);
    if ($pendaftaranOlID !== null) {
      $pendaftaranOlID = DocoHelpers::decrypt($pendaftaranOlID);
    }

    Yii::error([
      'reservationItems' => $this->reservationItem,
      'execution-time' => date('Y-m-d H:i:00')
    ]);

    $bpjsPayload = [];
    if ($isBpjs) {
      $bpjsPayload = $this->generateBpjsPayload();
    } 

    /**
     * Check token for reservasi token Mcare.
     */
    $carabayarId = ArrayHelper::getValue($this->reservationItem, 'carabayar_id', null);
    $jenisReservasi = ArrayHelper::getValue($this->reservationItem, 'jenis_reservasi', null);

    if ($carabayarId == DocoConstants::VAR_ID_CARABAYAR_ASURANSI && $jenisReservasi == DocoConstants::JENIS_RESERVASI_APPOINMENT) {
      $this->eligibleCheck($this->reservationItem, $pendaftaranOlID);
      $this->token = $this->getAuthToken();
    }

    if (empty(substr($this->token, 7))) {
      $this->token = $this->getAuthToken();
    }

    Yii::error(json_encode([
      'token' => $this->token,
      'xOwner' => $this->xOwner,
      'urlBackend' => $urlBackend,
      'execution-time' => date('Y-m-d H:i:00'),
    ]));

    // init guzzle client
    $this->guzzleClient = new Client([
      'base_uri' => $urlBackend . 'pendaftaran/v1/',
      'headers' => [
        'user_agent' => 'cli',
        'Authorization' => $this->token,
        'X-Owner' =>  $this->xOwner,
      ]
    ]);

    // create registration data
    $registrationStatus = false;
    $pendaftaranId = null;
    try {
      if ($isBpjs && is_null($bpjsPayload)) {
        throw new \Exception("BPJS Data Is Empty!");
      }

      $getTarifKarcis = $this->getDefaultTarifKarcis([
        'ruangan_id' => ArrayHelper::getValue($this->reservationItem, 'ruangan_id', null),
        'kelaspelayanan_id' => $kelasPelayananId,
        'penjamin_id' => ArrayHelper::getValue($this->reservationItem, 'penjamin_id', null),
        'dokter_id' => ArrayHelper::getValue($this->reservationItem, 'dokter_id', null),
      ]);

      $result = $this->guzzleClient->post('pendaftaran-rajal/save-pendaftaran', [
          'form_params' => [
              'tipe_pasien' => [
                'carabayar_id' => ArrayHelper::getValue($this->reservationItem, 'carabayar_id', null),
                'penjamin_id' => ArrayHelper::getValue($this->reservationItem, 'penjamin_id', null),
                'asalrujukan_id' => ArrayHelper::getValue($this->reservationItem, 'asalrujukan_id', null),
                'no_rekam_medik' => ArrayHelper::getValue($this->reservationItem, 'no_rekam_medik', null),
                'is_kolektif' => ArrayHelper::getValue($this->reservationItem, 'is_kolektif', null),
                'no_asuransi' => ArrayHelper::getValue($this->reservationItem, 'no_asuransi', null),
                'pendaftaranol_id' => $pendaftaranOlID,
                'antrian_id' => ArrayHelper::getValue($this->reservationItem, 'antrian_id', null),
              ],
              'kunjungan' => [
                'tgl_pendaftaran' => ArrayHelper::getValue($this->reservationItem, 'tgl_pendaftaran', null),
                'ruangan_id' => ArrayHelper::getValue($this->reservationItem, 'ruangan_id', null),
                'jeniskasuspenyakit_id' => ArrayHelper::getValue($this->reservationItem, 'jeniskasuspenyakit_id', null),
                'kelaspelayanan_id' => $kelasPelayananId,
                'dokter_id' => ArrayHelper::getValue($this->reservationItem, 'dokter_id', null),
                'pegawai_id' => ArrayHelper::getValue($this->reservationItem, 'dokter_id', null),
                'keadaan_masuk' => '159',
                'jadwaldokter_id' => ArrayHelper::getValue($this->reservationItem, 'jadwaldokter_id', null),
                'tindakan_karcis' => json_encode($getTarifKarcis),
              ],
              'pj_pasien' => [
                'pj_pengantar' => ArrayHelper::getValue($this->reservationItem, 'pj_pengantar', null),
                'pj_nama' => ArrayHelper::getValue($this->reservationItem, 'nama_pasien', null),
                'pj_jk' => ArrayHelper::getValue($this->reservationItem, 'jeniskelamin', null),
              ],
              'bpjs' => $bpjsPayload,
          ]
      ]);

      $result = json_decode($result->getBody(), true);
      $statusCode = (int) ArrayHelper::getValue($result, 'metadata.status', 200);

      // handle error when there is any validation failed on save-pendaftaran
      if ($statusCode !== 200) {
        Yii::error(json_encode($result));
        $errorMsg = ArrayHelper::getValue($result, 'response.text', "Pasien gagal Didaftarkan");
        $this->responseMessage = $errorMsg;
        throw new \Exception($errorMsg);
      } else {
        if ($carabayarId == DocoConstants::VAR_ID_CARABAYAR_ASURANSI && $jenisReservasi == DocoConstants::JENIS_RESERVASI_APPOINMENT) {
          $this->responseMessage = "Pasien Berhasil Didaftarkan";
        }
      }

      $registrationStatus = true;
      $pendaftaranId = ArrayHelper::getValue($result, 'response.id', null);
    } catch (\GuzzleHttp\Exception\ClientException $e) {
      Yii::error(json_encode(['msg' => 'There is an Error on this request Dude!', 'error' => $e->getMessage()]));
    } catch (\GuzzleHttp\Exception\ConnectException $e) {
      Yii::error(json_encode(['msg' => 'We failed to Connect on this request Dude!', 'error' => $e->getMessage()]));
    } catch (\GuzzleHttp\Exception\ServerException $e) {
      Yii::error(json_encode(['msg' => 'There is an Error on this request Dude!', 'error' => $e->getMessage()]));
    } catch (\Exception $e) {
      Yii::error(json_encode(['msg' => 'There is an Error on this function Dude!', 'error' => $e->getMessage()]));
    }

    Yii::$app->redis->executeCommand('PUBLISH', [
        'channel' => 'bulk-register:'.$this->processKey,
        'data' => json_encode([
          'timestamp' => ArrayHelper::getValue($this->reservationItem, 'tgl_pendaftaran', '-'),
          'no_pendaftaranol' => ArrayHelper::getValue($this->reservationItem, 'no_pendaftaranol', '-'),
          'registration_status' => $registrationStatus,
          'message' => $this->responseMessage,
          'pendaftaran_id' => $pendaftaranId
        ]),
    ]);
  }

  protected function getDefaultTarifKarcis($payload = [])
  {
    $tindakanKarcis = [];
    $isRequestFailed = true;

    try {
      $getTindakanKarcis = $this->guzzleClient->get('pendaftaran/get-tarif-karcis-pendaftaran', [
        'query' => [
          'ruangan_id' => ArrayHelper::getValue($payload, 'ruangan_id', null),
          'kelaspelayanan_id' => ArrayHelper::getValue($payload, 'kelaspelayanan_id', null),
          'penjamin_id' => ArrayHelper::getValue($payload, 'penjamin_id', null),
          'kelompoktindakan_id' => 17,
          'dokter_id' => ArrayHelper::getValue($payload, 'dokter_id', null),
        ]
      ]);
      $result = json_decode($getTindakanKarcis->getBody(), true);
      $statusCode = (int) ArrayHelper::getValue($result, 'metadata.status', 200);

      // handle error when there is any validation failed on save-pendaftaran
      if ($statusCode !== 200) {
        $errorMsg = ArrayHelper::getValue($result, 'response.text', "There is an Error on when try to get Tindakan Karcis!");
        throw new \Exception($errorMsg);
      }

      $isRequestFailed = false;
      $tindakanKarcis = ArrayHelper::getValue($result, 'response.data', []);
    } catch (\GuzzleHttp\Exception\ClientException $e) {
      Yii::error(json_encode(['msg' => 'There is an Error on when try to get Tindakan Karcis!', 'error' => $e->getMessage()]));
    } catch (\GuzzleHttp\Exception\ConnectException $e) {
      Yii::error(json_encode(['msg' => 'We failed to Connect on this get Tindakan Karcis request Dude!', 'error' => $e->getMessage()]));
    } catch (\GuzzleHttp\Exception\ServerException $e) {
      Yii::error(json_encode(['msg' => 'There is an Error on when try to get Tindakan Karcis!', 'error' => $e->getMessage()]));
    } catch (\Exception $e) {
      Yii::error(json_encode(['msg' => 'There is an Error on when try to get Tindakan Karcis!', 'error' => $e->getMessage()]));
    }

    if ($isRequestFailed) {
      throw new \Exception("Failed get Tindakan Karcis Data");
    }

    if ($tindakanKarcis) {
      $filteredDefaultKarcis = [];
      foreach($tindakanKarcis as $karcisItem) {
        $isDefault = ArrayHelper::getValue($karcisItem, 'is_default', false);
        if (!$isDefault) {
          continue;
        }

        $filteredDefaultKarcis[] = $karcisItem['daftartindakan_id'];
      }
      return $filteredDefaultKarcis;
    }

    return [];
  }

  protected function generateBpjsPayload()
  {
    $noKartuBpjs = ArrayHelper::getValue($this->reservationItem, 'no_kartu_bpjs', null);
    $noRujukanBpjs = ArrayHelper::getValue($this->reservationItem, 'no_rujukan_bpjs', null);
    $pegawaiId = ArrayHelper::getValue($this->reservationItem, 'dokter_id', null);
    $ruanganId = ArrayHelper::getValue($this->reservationItem, 'ruangan_id', null);
    $jenisKunjunganBpjs = ArrayHelper::getValue($this->reservationItem, 'jenis_kunjungan_bpjs', 4);
    $noSuratKontrol = ArrayHelper::getValue($this->reservationItem, 'no_surat_kontrol', null);

    $getPegawaiBpjsInfo = Pegawai::find()->select([
      'kode_dokter_bpjs',
      'nama_dokter_bpjs'
    ])->where(['pegawai_id' => $pegawaiId])->asArray()->one();

    $getRuanganBpjs = Ruangan::find()->select(['ruangan_id', 'kode_ruangan_bpjs'])->where(['ruangan_id' => $ruanganId])->asArray()->one();

    // Improve disini
    // Kalau jenis kunjungan kontrol akan baca 2 kondisi pertama baca api kontrol lanjut baca api rujukan
    // kalau kondisi FKTP,FKTL dan Rujuk Internal baca salah satu kondisi cariRujukanPeserta
    $getBpjsData = null;
	$isPostRanap = false;
	$getNoSep = null;
    if (in_array($jenisKunjunganBpjs, [DocoConstants::JNS_KUNJUNGAN_JKN[DocoConstants::RUJUK_KONTROL]])) {
		$getSurkon = (new Bpjs)->cariRencanaKontrolByNoRencanaKontrol($noSuratKontrol);
		$getNoRujukan = ArrayHelper::getValue($getSurkon, 'response.sep.provPerujuk.noRujukan', null);
		$getNoSep = ArrayHelper::getValue($getSurkon, 'response.sep.noSep', null);
		$getAsalRujukan = ArrayHelper::getValue($getSurkon, 'response.sep.provPerujuk.asalRujukan', null);
		$sepAsalKontrol = ArrayHelper::getValue($getSurkon, 'response.sep.jnsPelayanan', null);
		if ($sepAsalKontrol == "Rawat Inap") {
			$isPostRanap = true;
		}
		$getBpjsData = (new Bpjs)->cariRujukan($getNoRujukan);
      // double prevent
		if (isset($getBpjsData['metaData']['code']) && !empty($getBpjsData['metaData']['code']) && $getBpjsData['metaData']['code'] != 200) {
			$rujukan = (new Bpjs)->referensiCariSep($getNoSep);
			if($rujukan['metaData']['code'] != 200) { // error code 200 berhasil
				$bpjsMessage = isset($rujukan['metaData']['message']) && !empty($rujukan['metaData']['message']) ? $rujukan['metaData']['message'] : 'Rujukan Tidak Ada';
				$this->responseMessage = $bpjsMessage. ', ' . $this->responseMessage;
				return null;
			}
			$rkSep = (new Bpjs)->rencanaKontrolCariSep($getNoSep);
			if($rkSep['metaData']['code'] != 200) {
				$bpjsMessage = isset($rkSep['metaData']['message']) && !empty($rkSep['metaData']['message']) ? $rkSep['metaData']['message'] : 'Rujukan Tidak Ada';
				$this->responseMessage = $bpjsMessage. ', ' . $this->responseMessage;
				return null;
			}
			$diagnosa = explode("-", ArrayHelper::getValue($rkSep, 'response.diagnosa'));
			$getBpjsData = [
					'response' => [
					'asalFaskes' => ArrayHelper::getValue($getSurkon, 'response.sep.provPerujuk.asalRujukan', null),
					'rujukan' => [
						'tglKunjungan' => ArrayHelper::getValue($getSurkon, 'response.tglRencanaKontrol', null),
						'provPerujuk' => [
							'kode' => ArrayHelper::getValue($rkSep, 'response.provPerujuk.kdProviderPerujuk', null),
							'nama' => ArrayHelper::getValue($rkSep, 'response.provPerujuk.nmProviderPerujuk', null),
						],
						'diagnosa' => [
							'kode' => isset($diagnosa[1]) ? rtrim($diagnosa[0]) : "",
							'nama' => isset($diagnosa[1]) ? $diagnosa[1] : $diagnosa[0],
						],
						'peserta' => [
						'hakKelas' => [
							'kode' => ArrayHelper::getValue($getSurkon, 'response.sep.peserta.hakKelas', null)
						]
						]
					]
				]
			];
		}
    } else {
		$getBpjsData = (new Bpjs)->cariRujukanPeserta($noKartuBpjs, false, true);
		if (isset($getBpjsData['metaData']['code']) && !empty($getBpjsData['metaData']['code']) && $getBpjsData['metaData']['code'] != 200) {
			$getBpjsData = (new Bpjs)->cariRujukanPeserta($noKartuBpjs, false, false);
		}
    }
     
    $getRujukanKhusus = $this->getRujukanKhususByNomor($noRujukanBpjs);

    if (isset($getBpjsData['metaData']['code']) && !empty($getBpjsData['metaData']['code']) && $getBpjsData['metaData']['code'] != 200) {
        $bpjsMessage = isset($getBpjsData['metaData']['message']) && !empty($getBpjsData['metaData']['message']) ? $getBpjsData['metaData']['message'] : 'Rujukan Tidak Ada';
        $this->responseMessage = $bpjsMessage. ', ' . $this->responseMessage;
        return null;
    }

    if (ArrayHelper::getValue($getBpjsData, 'response', null) === null) {
        $this->responseMessage = 'Terjadi kesalahan saat komunikasi dengan server BPJS';
        return null;
    }

    $jenisRujukan = ArrayHelper::getValue($getBpjsData, 'response.asalFaskes', 2);

    $bpjsPayload = [
      'no_rekam_medik' => ArrayHelper::getValue($this->reservationItem, 'no_rekam_medik', null),
      'jenis_rujukan' => $jenisRujukan,
      'tanggal_sep' => date('d-m-Y'),
      'jenis_pelayanan' => 2, // (1. ranap, 2. rajal) Akan selalu 2 karena auto daftar akan selalu rajal
      'jenis_kartu' => '1',
      'no_kartu' => $noKartuBpjs,
      'poli_tujuan' => (new DocoHelpers)->coalesce(ArrayHelper::getValue($getRuanganBpjs, 'kode_ruangan_bpjs', null), ArrayHelper::getValue($getBpjsData, 'response.rujukan.poliRujukan.kode', null)),
      'asal_rujukan' => ArrayHelper::getValue($getBpjsData, 'response.asalFaskes', null),
      'ppk_rujukan' => ArrayHelper::getValue($getBpjsData, 'response.rujukan.provPerujuk.kode', null),
      'no_rujukan' => $noRujukanBpjs,
      'no_rujukan_f' => $noRujukanBpjs,
      'tanggal_rujukan' => !empty($getRujukanKhusus) && !empty($getRujukanKhusus['tglrujukan_berakhir']) ? $getRujukanKhusus['tglrujukan_berakhir'] : ArrayHelper::getValue($getBpjsData, 'response.rujukan.tglKunjungan', null),
      'kelas_rawat' => ArrayHelper::getValue($getBpjsData, 'response.rujukan.peserta.hakKelas.kode', '1'),
      'no_telp' => ArrayHelper::getValue($this->reservationItem, 'no_telepon_pasien', null),
      'katarak' => '0',
      'kasus_kecelakaan' => '0',
      'tanggal_kejadian' => date('d-m-Y'),
      'status_suplesi' => '0',
      'kode_dpjp' => ArrayHelper::getValue($getPegawaiBpjsInfo, 'kode_dokter_bpjs', null), // ambil dari pegawai_m berdasarkan pegawai_id ambil dari kolom kode_dokter_bpjs
      'kode_dpjp_melayani' => ArrayHelper::getValue($getPegawaiBpjsInfo, 'kode_dokter_bpjs', null), // ambil dari pegawai_m berdasarkan pegawai_id ambil dari kolom kode_dokter_bpjs
      'nama_dpjp_melayani' => ArrayHelper::getValue($getPegawaiBpjsInfo, 'nama_dokter_bpjs', null), // ambil dari pegawai_m berdasarkan pegawai_id ambil dari kolom nama_dokter_bpjs
      'kode_ppk_perujuk' => ArrayHelper::getValue($getBpjsData, 'response.rujukan.provPerujuk.kode', null), // ambil dari api get kunjungan by no rujukan
      'nama_ppk_perujuk' => ArrayHelper::getValue($getBpjsData, 'response.rujukan.provPerujuk.nama', null), // ambil dari api get kunjungan by no rujukan
      'is_tujuan_kunj' => '0',
      'diagnosa_awal' => !empty($getRujukanKhusus) && !empty($getRujukanKhusus['diagppk']) ? $getRujukanKhusus['diagppk'] : ArrayHelper::getValue($getBpjsData, 'response.rujukan.diagnosa.kode', null),
      'catatan_sep' => '',
      'info_response' => json_encode(ArrayHelper::getValue($getBpjsData, 'response', [])),
    ];

    if (in_array($jenisKunjunganBpjs, [DocoConstants::JNS_KUNJUNGAN_JKN[DocoConstants::RUJUK_KONTROL]])) {
        // $checkPostRanap = $this->getLastSepHistoryRanap($noKartuBpjs);

        if ($isPostRanap) {
            $bpjsPayload['no_rujukan'] = $getNoSep;
            $bpjsPayload['no_rujukan_f'] = $getNoSep;
            $bpjsPayload['tujuanKunj'] = 0;
            $bpjsPayload['is_tujuan_kunj'] = "";
            $bpjsPayload['assesmentPel'] = "";
        } else {
            $bpjsPayload['tujuanKunj'] = 2;
            $bpjsPayload['is_tujuan_kunj'] = 2;
            $bpjsPayload['assesmentPel'] = $jenisKunjunganBpjs == DocoConstants::JNS_KUNJUNGAN_JKN[DocoConstants::RUJUK_KONTROL] ? 5 : '';
        }
		$bpjsPayload['no_surat_kontrol'] = $noSuratKontrol;
		$bpjsPayload['flagProcedure'] = "";

    } elseif (in_array($jenisKunjunganBpjs, [DocoConstants::JNS_KUNJUNGAN_JKN[DocoConstants::RUJUK_INTERNAL]])) {
        $lastHistoryPelayanan = $this->getHistoryPelayanan($noKartuBpjs, $noRujukanBpjs);
        if (ArrayHelper::getValue($lastHistoryPelayanan, 'tglSep', '') != date('Y-m-d')) {
             $bpjsPayload['assesmentPel'] = 2;
        } else {
             $bpjsPayload['assesmentPel'] = "";
        }
        $bpjsPayload['tujuanKunj'] = 0;
        $bpjsPayload['is_tujuan_kunj'] = 0;
    } else {
        $bpjsPayload['tujuanKunj'] = 0;
        $bpjsPayload['is_tujuan_kunj'] = 0;
    }

    return $bpjsPayload;
  }

  private function getHistoryPelayanan($no_peserta, $no_rujukan) 
  {
      $historiPelayanan = (new Bpjs)->historyPelayananPasien($no_peserta);
      return ArrayHelper::getValue(
          array_values(
            array_filter(
              ArrayHelper::getValue($historiPelayanan, 'response.histori', []), function($val) use ($no_rujukan){
                  return $no_rujukan == $val['noRujukan'];
              }
            )
          ), '0', []);
  }

  private function eligibleCheck($reservationItem, $pendaftaranOlID)
  {
    $noAsuransi = ArrayHelper::getValue($reservationItem, 'no_asuransi', null);
    $penjaminId = ArrayHelper::getValue($reservationItem, 'penjamin_id', null);

    /**
     * Check Integration Penjamin.
     */
    $penjaminTerintegrasi = $this->guzzleClient->get('allow/cek-penjamin-terintegrasi', [
      'query' => [
        'penjamin_id' => $penjaminId
      ],
      'http_errors' => false
    ]);

    $result = json_decode($penjaminTerintegrasi->getBody(), true);
    $data = ArrayHelper::getValue($result, 'response.data', []);

    if (empty($data)) {
      return;
    }

    $statusCode = (int) ArrayHelper::getValue($result, 'metadata.status', 200);
    if ($statusCode !== 200) {
      $message = 'Penjamin ' . $penjaminId . ' tidak terintegrasi';
      $this->publishMessage($message, false);
      throw new \Exception($message);
    }

    /**
     * Check Online Registration Data.
     */
    $this->checkOnlineRegistration($pendaftaranOlID);

    /**
     * Check Eligible User.
     */
    $this->checkEligibleUser($noAsuransi, $penjaminId);
  }

  private function checkOnlineRegistration($pendaftaranOlID)
  {
    $pendaftaranOnline = InfoPendaftaranOlView::find()->where([
      'pendaftaranol_id' => $pendaftaranOlID
    ])->one();

    if (isset($pendaftaranOnline)) {
      if (is_null($pendaftaranOnline->benefit_code) && is_null($pendaftaranOnline->transaction_id)) {
        $message = 'Benefit code atau Transaction ID masih kosong';
        $this->publishMessage($message, false);
        throw new \Exception($message);
      }
    }
  }

  private function checkEligibleUser($noAsuransi, $penjaminId)
  {
    $checkEligible = $this->guzzleClient->get('allow/cek-eligible-peserta', [
      'query' => [
        'no_kartu' => $noAsuransi,
        'penjamin_id' => $penjaminId
      ]
    ]);

    $resultEligible = json_decode($checkEligible->getBody(), true);
    $insuranceEligible = ArrayHelper::getValue($resultEligible, 'response.response');

    if (! empty($insuranceEligible)) {
      $codeStatus = ArrayHelper::getValue($insuranceEligible, 'status');
      if ($codeStatus !== 0) {
        $message = ArrayHelper::getValue($insuranceEligible, 'message');
        $this->publishMessage($message, false);
        throw new \Exception($message);
      }
    }
  }

  private function publishMessage($message, $status)
  {
    Yii::$app->redis->executeCommand('PUBLISH', [
        'channel' => 'bulk-register:'.$this->processKey,
        'data' => json_encode([
          'timestamp' => ArrayHelper::getValue($this->reservationItem, 'tgl_pendaftaran', '-'),
          'no_pendaftaranol' => ArrayHelper::getValue($this->reservationItem, 'no_pendaftaranol', '-'),
          'registration_status' => $status,
          'message' => $message,
        ]),
    ]);
  }

  protected function getAuthToken()
  {
    $params = Yii::$app->params;
    $cache = Yii::$app->cache;

    $urlBackend = isset($params['url_backend']) ? $params['url_backend'] : 'http://web:8858/';
    $envKonfig = isset($params['iniFile']) ? $params['iniFile'] : 'dev';
    $baseConfig = isset($envKonfig[$this->keyConfig]) ? $envKonfig[$this->keyConfig] : [];

    $tokenCache = $cache->get('token:pendaftaranmandiri');
    if ($tokenCache) {
      return $tokenCache;
    }

    $guzzleClient = new Client([
      'base_uri' => $urlBackend . 'dcms/v1/',
      'headers' => [
        'user_agent' => 'cli',
        'X-Owner' =>  $this->xOwner,
      ]
    ]);

    $response = $guzzleClient->post('auth/get-token', [
      'form_params' => [
        'username' => $baseConfig['username'],
        'password' => $baseConfig['password'],
      ]
    ]);

    $response = json_decode($response->getBody(), true);
    $token = isset($response['response']['access_token']) ? $response['response']['access_token'] : null;
    if (empty($token)) {
      return null;
    }
    
    $bearer = 'Bearer ' . $token; 
    $cache->set('token:pendaftaranmandiri', $bearer, 3600);

    return $bearer;
  }

  private function getRujukanKhususByNomor($nomor)
  {
      $rujukan_khusus = BpjsRujukanKhususT::find()->where(['norujukan' => $nomor])->orderBy('id', 'desc')->asArray()->one();
      if ($rujukan_khusus && !empty($rujukan_khusus['diagppk'])) {
          $rujukan_khusus['diagnosa'] = Diagnosa::find()->select(['diagnosa_id', 'diagnosa_kode', 'diagnosa_nama', 'diagnosa_namalainnya'])->where(['diagnosa_kode' => $rujukan_khusus['diagppk']])->asArray()->one();
      }
      return $rujukan_khusus;
  }

  private function getNoSuratKontrol($nokartubpjs, $is_skdp = true)
  {
      // Pengambilan nomor surat kontrol dengan range H-14 sampai H+14 hari
      $list_surat_kontrol = (new Bpjs)->dataNomorSuratKontrol(date('Y-m-d', strtotime('-14 days')), date('Y-m-d', strtotime('+14 days')), 2);
      $jnsKontrol = $is_skdp ? 2 : 1;

      $list_surat_kontrol = array_values(array_filter(ArrayHelper::getValue($list_surat_kontrol, 'response.list', []), function ($var) use ($jnsKontrol, $nokartubpjs) {
                      return ($var['jnsKontrol'] == $jnsKontrol && $var['noKartu'] == $nokartubpjs);
                  }));
      // sorting surat kontrol by tglRencanaKontrol Desc
      usort($list_surat_kontrol, function ($element1, $element2) {
              $datetime1 = strtotime($element1['tglRencanaKontrol']);
              $datetime2 = strtotime($element2['tglRencanaKontrol']);
              return $datetime2 - $datetime1;
          });

      return ArrayHelper::getValue($list_surat_kontrol, '0.noSuratKontrol', null);
  }

  private function getLastSepHistoryRanap($nokartubpjs)
  {
      $history = (new Bpjs)->historyPelayananPasien($nokartubpjs);
      $ppkPelayanan = Cache::getPpkPelayanan();

      $ppkPelayanan_nama = ArrayHelper::getValue($ppkPelayanan, 'nama', null);
      list($namaPpk, $kotaPpk) = explode(" - ", $ppkPelayanan_nama);

      $history = ArrayHelper::getValue($history, 'response.histori', []);

      // // sorting histori pelayanan by tglPlgSep Desc
      usort($history, function ($element1, $element2) {
              $datetime1 = strtotime($element1['tglPlgSep']);
              $datetime2 = strtotime($element2['tglPlgSep']);
              return $datetime2 - $datetime1;
      });

      $lastHistorySep = isset($history[0]) ? $history[0] : [];

      return (!empty($lastHistorySep) && $lastHistorySep['ppkPelayanan'] == $namaPpk && $lastHistorySep['jnsPelayanan'] == 1) ?
              $lastHistorySep
              : [];
  }
}