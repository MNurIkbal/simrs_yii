<?php

namespace Integrasi\Service\Sirs;

use Yii;
use GuzzleHttp\Client;
use yii\helpers\ArrayHelper;
use Integrasi\Service\Sirs\Models\RujukanBantaran;
use Integrasi\Service\Sirs\Models\Pendaftaran;
use Integrasi\Service\Sirs\Models\PendaftaranOl;
use Integrasi\Components\DocoConstants;

class StatusUpdatePembantaran extends \Integrasi\Contracts\DocoImplement
{
    protected $urlBantaran;
    public $pengajuan_id;
    public $nomor_pengajuan;
    public $status_type; // 'response', 'pendaftaran', 'pulang'
    
    public function execute()
    {
        $this->urlBantaran = Yii::$app->params['pembantaran']['url'];

        if (empty($this->urlBantaran)) {
            return $this->setResponse([
                'success' => false,
                'message' => 'URL Bantaran tidak ditemukan di konfigurasi'
            ]);
        }

        $update_from = isset($this->result['update_from']) ? $this->result['update_from'] : $this->update_from;

        Yii::info([
            'update_from' => $update_from,
            'result' => $this->result,
            'this->update_from' => $this->update_from
        ], 'bantaran-integration-debug');

        $response = [];
        
        // Determine which API to call based on update_from
        switch ($update_from) {
            case 'save_pendaftaran':
                $response = $this->updatePendaftaran();
                break;
            case 'pemulangan_pasien':
                $response = $this->updatePulang();
                break;
            case 'response_pengajuan':
                $response = $this->updateResponse();
                break;
            case 'send_document':
                $response = $this->sendDocument();
                break;
            case 'batal_verifikasi':
                $response = $this->batalVerifikasi();
                break;
            default:
                $response = [
                    'success' => false,
                    'message' => 'Update type tidak valid'
                ];
                break;
        }

        return $this->setResponse($response);
    }

    /**
     * Update status pengajuan (approve/reject)
     * POST /api/post/pengajuan/response
     */
    protected function updateResponse()
    {
        $pengajuan_id = $this->result['pengajuan_id'];
        $nomor_pengajuan = $this->result['nomor_pengajuan'];
        $status_pengajuan_id = isset($this->result['status_pengajuan_id']) ? $this->result['status_pengajuan_id'] : 4; // default approved
        $alasan_ditolak = isset($this->result['alasan_ditolak']) ? $this->result['alasan_ditolak'] : '';
        $rencana_tindakan = isset($this->result['rencana_tindakan']) ? $this->result['rencana_tindakan'] : '';

        $payload = [
            'pengajuan_id' => $pengajuan_id,
            'nomor_pengajuan' => $nomor_pengajuan,
            'status_pengajuan_id' => $status_pengajuan_id,
            'alasan_ditolak' => $alasan_ditolak,
            'rencana_tindakan' => $rencana_tindakan
        ];

        $url = $this->urlBantaran . '/api/post/pengajuan/response';

        $sendMessage = $this->sendRequest($url, $payload, 'POST');
        
        return $sendMessage;
    }

    /**
     * Update status pendaftaran
     * POST /api/post/pembantaran/pendaftaran
     */
    protected function updatePendaftaran()
    {
        if(!empty($this->result['pendaftaranol_id'])) {
            $rujukanByReservasi = RujukanBantaran::find()
            ->select(['pengajuan_id'])
            ->where(['pendaftaranol_id' => $this->result['pendaftaranol_id']])
            ->one();

            $getPendaftaranId = PendaftaranOl::find()
                ->select(['pendaftaran_id'])
                ->where(['pendaftaranol_id' => $this->result['pendaftaranol_id']])
                ->one();

            $getTanggalPendaftaran = Pendaftaran::find()
                ->select(['tgl_pendaftaran'])
                ->where(['pendaftaran_id' => $getPendaftaranId->pendaftaran_id])
                ->one();
            
            $updateCondition = ['pendaftaranol_id' => $this->result['pendaftaranol_id']];
        }
        
        if(!empty($this->result['pendaftaran_id'])) {
            $rujukanByReservasi = RujukanBantaran::find()
            ->select(['pengajuan_id'])
            ->where(['pendaftaran_id' => $this->result['pendaftaran_id']])
            ->one();

            $getTanggalPendaftaran = Pendaftaran::find()
                ->select(['tgl_pendaftaran'])
                ->where(['pendaftaran_id' => $this->result['pendaftaran_id']])
                ->one();

            $updateCondition = ['pendaftaran_id' => $this->result['pendaftaran_id']];
        }
        
        if (empty($rujukanByReservasi)) {
            return [
                'success' => false,
                'message' => 'Rujukan bantaran tidak ditemukan untuk pendaftaran tersebut'
            ];
        }

        // update status_pelayanan_bantaran on RujukanBantaran
        RujukanBantaran::updateAll(
            ['status_pelayanan_bantaran' => DocoConstants::DALAM_PELAYANAN_BANTARAN],
            $updateCondition
        );
        
        $pengajuan_id = $rujukanByReservasi->pengajuan_id;
        $tanggal_pendaftaran = date('Y-m-d\TH:i:s', strtotime($getTanggalPendaftaran->tgl_pendaftaran));

        $payload = [
            'pengajuan_id' => $pengajuan_id,
            'status_pendaftaran_id' => 9, // Status registered
            'tanggal_pendaftaran' => $tanggal_pendaftaran
        ];

        $url = $this->urlBantaran . '/api/post/pembantaran/pendaftaran';
        
        $response = $this->sendRequest($url, $payload, 'POST');
        
        // Save tahanan_url from response if exists
        if ($response['success'] && isset($response['data']['data']['tahanan_url'])) {
            RujukanBantaran::updateAll(
                ['tahanan_url' => $response['data']['data']['tahanan_url']],
                $updateCondition
            );
        }
        
        return $response;
    }

    /**
     * Update status pulang
     * POST /api/post/pembantaran/pulang
     */
    protected function updatePulang()
    {
        $getPendaftaranOLId = PendaftaranOl::find()
            ->select(['pendaftaranol_id'])
            ->where(['pendaftaran_id' => $this->result['pendaftaran_id']])
            ->one();

        if (empty($getPendaftaranOLId->updatePendaftaran)) {
            $condition = ['pendaftaran_id' => $this->result['pendaftaran_id']];
        } else {
            $condition = ['pendaftaranol_id' => $getPendaftaranOLId->pendaftaranol_id];
        }
        
        $rujukanByReservasi = RujukanBantaran::find()
            ->select(['pengajuan_id'])
            ->where($condition)
            ->one();

        $catatan = isset($this->result['catatan']) ? $this->result['catatan'] : 'Pasien telah selesai mendapatkan pelayanan dan kembali.';
        $tgl_kembali = isset($this->result['tgl_kembali']) ? $this->result['tgl_kembali'] : date('Y-m-d\TH:i:s');

        // update status_pelayanan_bantaran on RujukanBantaran
        RujukanBantaran::updateAll(
            ['status_pelayanan_bantaran' => DocoConstants::SELESAI_PELAYANAN_BANTARAN],
            $condition
        );

        $payload = [
            'pengajuan_id' => $rujukanByReservasi->pengajuan_id,
            'catatan' => $catatan,
            'tgl_kembali' => $tgl_kembali
        ];

        $url = $this->urlBantaran . '/api/post/pembantaran/pulang';
        
        return $this->sendRequest($url, $payload, 'POST');
    }

    /**
     * Send document to Bantaran
     * POST /api/post/pembantaran/documents
     */
    protected function sendDocument()
    {
        $pengajuan_id = isset($this->result['pengajuan_id']) ? $this->result['pengajuan_id'] : null;
        $nama_dokumen = isset($this->result['nama_dokumen']) ? $this->result['nama_dokumen'] : null;
        $url_dokumen = isset($this->result['url_dokumen']) ? $this->result['url_dokumen'] : null;

        if (empty($pengajuan_id) || empty($nama_dokumen) || empty($url_dokumen)) {
            return [
                'success' => false,
                'message' => 'Parameter dokumen tidak lengkap (pengajuan_id, nama_dokumen, url_dokumen)'
            ];
        }

        $payload = [
            'pengajuan_id' => $pengajuan_id,
            'nama_dokumen' => $nama_dokumen,
            'url_dokumen' => $url_dokumen
        ];

        $url = $this->urlBantaran . '/api/post/pembantaran/documents';
        
        return $this->sendRequest($url, $payload, 'POST');
    }

    /**
     * Batal verifikasi bantaran
     * POST /api/post/pembantaran/batal-verifikasi
     */
    protected function batalVerifikasi()
    {
        $pengajuan_id = isset($this->result['pengajuan_id']) ? $this->result['pengajuan_id'] : null;

        if (empty($pengajuan_id)) {
            return [
                'success' => false,
                'message' => 'Parameter pengajuan_id tidak ditemukan'
            ];
        }

        $payload = [
            'pengajuan_id' => $pengajuan_id
        ];

        $url = $this->urlBantaran . '/api/post/pembantaran/batal-verifikasi';
        
        return $this->sendRequest($url, $payload, 'POST');
    }

    /**
     * Send HTTP request to Bantaran API
     */
    protected function sendRequest($url, $payload, $method = 'POST')
    {
        try {
            $client = new Client([
                'timeout' => 30,
                'verify' => false
            ]);

            $options = [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => $payload
            ];

            $response = $client->request($method, $url, $options);
            $statusCode = $response->getStatusCode();
            $body = $response->getBody()->getContents();
            $result = json_decode($body, true);

            $this->logRequest($url, $payload, $result, $statusCode);

            return [
                'success' => $statusCode >= 200 && $statusCode < 300,
                'status_code' => $statusCode,
                'data' => $result,
                'message' => $statusCode >= 200 && $statusCode < 300 ? 'Berhasil update status bantaran' : 'Gagal update status bantaran'
            ];

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $errorMessage = $e->getMessage();
            $statusCode = $e->hasResponse() ? $e->getResponse()->getStatusCode() : 500;
            $responseBody = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null;

            $this->logRequest($url, $payload, [
                'error' => $errorMessage,
                'response' => $responseBody
            ], $statusCode);

            return [
                'success' => false,
                'status_code' => $statusCode,
                'message' => 'Error: ' . $errorMessage,
                'error_detail' => $responseBody
            ];
        } catch (\Exception $e) {
            $this->logRequest($url, $payload, [
                'error' => $e->getMessage()
            ], 500);

            return [
                'success' => false,
                'status_code' => 500,
                'message' => 'Exception: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Set response format
     */
    public function setResponse($response)
    {
        return json_encode([
            'service' => 'Sirs-UpdateStatusPembantaran',
            'timestamp' => date('Y-m-d H:i:s'),
            'response' => $response
        ]);
    }

    /**
     * Log request to database or file
     */
    protected function logRequest($url, $payload, $response, $statusCode)
    {
        Yii::info([
            'service' => 'StatusUpdatePembantaran',
            'url' => $url,
            'payload' => $payload,
            'response' => $response,
            'status_code' => $statusCode,
            'timestamp' => date('Y-m-d H:i:s')
        ], 'bantaran-integration');
    }
}
