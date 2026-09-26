<?php
/**
 * RujukanBantaranSirsOnly
 * 
 * Extension class for Rujukan Bantaran SIRS Only (handles both index and getData)
 * @author : Sirs Developer
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\extensions\pendaftaran\RujukanBantaran;

use Yii;
use yii\base\Action;
use yii\base\View;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;
use yii\web\NotFoundHttpException;
use yii\web\HttpException;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use app\modules\pendaftaran\processes\RujukanBantaranIndexProcess;
use app\modules\pendaftaran\processes\RujukanBantaranGetDataProcess;

class RujukanBantaranSirsOnly extends \app\components\DocoBaseProcessExtension
{
    protected function processFlow($controller)
    {
        $actionId = Yii::$app->controller->action->id;
        
        // Route to appropriate method based on action
        switch ($actionId) {
            case 'get-data':
                return $this->processGetData($controller);
            case 'periksa':
                return $this->processPeriksa($controller);
            case 'detail-rujukan':
                return $this->processDetailRujukan($controller);
            case 'kirim-dokumen':
                return $this->processKirimDokumen($controller);
            case 'upload-dokumen':
                return $this->processUploadDokumen($controller);
            case 'verifikasi-rujukan':
                return $this->processVerifikasiRujukan($controller);
            case 'batal-verifikasi':
                return $this->processBatalVerifikasi($controller);
            case 'tolak-rujukan':
                return $this->processTolakRujukan($controller);
            case 'reject-bantaran':
                return $this->processRejectBantaran($controller);
            case 'show-attachment':
                return $this->processShowAttachment($controller);
            case 'approve-bantaran':
                return $this->processApproveBantaran($controller);
            case 'simpan-soap':
                return $this->processSimpanSoap($controller);
            case 'get-soap-data':
                return $this->processGetSoapData($controller);
            case 'hapus-soap':
                return $this->processHapusSoap($controller);
            case 'simpan-ttv':
                return $this->processSimpanTtv($controller);
            case 'get-ttv-data':
                return $this->processGetTtvData($controller);
            case 'hapus-ttv':
                return $this->processHapusTtv($controller);
            case 'pemulangan-tahanan':
                return $this->processPemulanganTahanan($controller);
            case 'print-qr':
                return $this->processPrintQr($controller);
            default:
                return $this->processIndex($controller);
        }
    }
    
    protected function processIndex($controller)
    {
        $title = "Informasi Reservasi Rujukan Pembantaran";

        try {
            $restPendaftaran = Yii::$app->docoRest->pendaftaran;
            $response = $restPendaftaran->get('rujukan-bantaran/get-index-data', ['form_params' => []]);
            $body = json_decode($response->getBody(), true);

            $masterUpt = isset($body['response']['master_upt']) ? $body['response']['master_upt'] : [];
            $masterInstalasi = isset($body['response']['instalasi']) ? $body['response']['instalasi'] : [];
	    $notifications = empty(Yii::$app->session->get('notifications')) ? ['records' => [], 'totalUnread' => 0, 'users' => null] : Yii::$app->session->get('notifications');

            return $controller->render('@app/extensions/pendaftaran/views/rujukan-bantaran/index', [
                'title' => 'Extension Rujukan Bantaran SIRS Only',
                'masterUpt' => $masterUpt,
                'masterInstalasi' => $masterInstalasi,
		'notifications' => $notifications
            ]);
            
        } catch (RequestException $e) {
            Yii::error('RujukanBantaranSirsOnly - RequestException: ' . $e->getMessage(), 'rujukan-bantaran-sirs');
            Yii::$app->session->setFlash('error', 'Gagal memuat data rujukan bantaran SIRS: ' . $e->getMessage());
            
            $masterUpt = [];
            $masterInstalasi = [];
            
            return $controller->render('@app/extensions/pendaftaran/views/rujukan-bantaran/index', [
                'title' => 'Extension Rujukan Bantaran SIRS Only',
                'masterUpt' => $masterUpt,
                'masterInstalasi' => $masterInstalasi,
            ]);
            
        } catch (\Exception $e) {
            Yii::error('RujukanBantaranSirsOnly - Exception: ' . $e->getMessage(), 'rujukan-bantaran-sirs');
            Yii::$app->session->setFlash('error', 'Terjadi kesalahan sistem SIRS: ' . $e->getMessage());
            
            $masterUpt = [];
            $masterInstalasi = [];
            
            return $controller->render('@app/extensions/pendaftaran/views/rujukan-bantaran/index', [
                'title' => 'Extension Rujukan Bantaran SIRS Only',
                'masterUpt' => $masterUpt,
                'masterInstalasi' => $masterInstalasi,
            ]);
        }
    }
    
    protected function processGetData($controller)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        try {
            $restPendaftaran = Yii::$app->docoRest->pendaftaran;
            $response = $restPendaftaran->get('rujukan-bantaran/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;

                $noTahanan = !empty($value['no_tahanan']) ? $value['no_tahanan'] : '-';
                $noRujukanBantaran = !empty($value['no_rujukanbantaran']) ? $value['no_rujukanbantaran'] : '-';
                $namaPasien = '<b>' . $value['nama_pasien'] . '</b><br/>' . 'No. Tahanan: ' . $noTahanan . '<br/>' . 'No. Pembantaran: ' . $noRujukanBantaran;

                $namaDokter = !empty($value['dokter_nama']) ? $value['dokter_nama'] : '-';
                $ruangan = ($value['status_verifikasi_bantaran_id'] == DocoConstants::BELUM_VERIFIKASI_BANTARAN) ? '-' : $value['ruangan_nama'] . '<br>' . $namaDokter;

                $primaryKey = DocoHelpers::encrypt($value['rujukanbantaran_id']);
                $value['primary'] = $primaryKey;

                $value['ruangan_nama'] = $ruangan;
                $value['tgl_kunjungan'] = !empty($value['tgl_kunjungan']) ? date('d M Y', strtotime($value['tgl_kunjungan'])) : null;
                $value['nama_pasien'] = $namaPasien;

                $tglVerif = !empty($value['tgl_verifikasi']) ? date('d M Y H:i:s', strtotime($value['tgl_verifikasi'])) : '';
                $value['pegawaiverifikasi_id'] = $value['nama_pegawaiverifikasi'] .'<br>'. $tglVerif;

                $value['rowNum'] = $no;

                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
    
    protected function processPeriksa($controller)
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $ruanganId = $request->get('ruanganId');
        $restRajal = Yii::$app->docoRest->rajal;
        
        if (empty($id)) {
            Yii::$app->session->setFlash('error', 'ID rujukan bantaran tidak ditemukan.');
            return $controller->redirect(['index']);
        }

        $bantaranId = DocoHelpers::decrypt($id);
        $title = "Pemeriksaan Rujukan Bantaran SIRS";

        try {
            $restPendaftaran = Yii::$app->docoRest->pendaftaran;
            
            // Get detail rujukan bantaran
            $response = $restPendaftaran->get('rujukan-bantaran/detail-rujukan?bantaran_id=' . $bantaranId, ['form_params' => []]);
            $body = json_decode($response->getBody(), true);

            $responseBantaran = isset($body['response']['bantaran']) ? $body['response']['bantaran'] : [];
            $responseDokumenBantaran = isset($body['response']['dokumen_bantaran']) ? $body['response']['dokumen_bantaran'] : [];
            $responseDokumenMedis = isset($body['response']['dokumen_medis']) ? $body['response']['dokumen_medis'] : [];
            Yii::error($responseDokumenMedis, 'rujukan-bantaran-sirs-dokumen-medis');
            if (empty($responseBantaran)) {
                Yii::$app->session->setFlash('error', 'Data rujukan bantaran tidak ditemukan.');
                return $controller->redirect(['index']);
            }

            if($responseBantaran['status_pelayanan_bantaran_id'] == DocoConstants::MENUNGGU_DIDAFTARKAN_BANTARAN) {
                $updateStatusPendaftaran = $restPendaftaran->post('rujukan-bantaran/periksa', ['form_params' => ['bantaran_id' => $bantaranId]]);    
            }
            

            // Get pasien data
            $dataPasien = [
                'no_rujukanbantaran' => isset($responseBantaran['no_rujukanbantaran']) ? $responseBantaran['no_rujukanbantaran'] : '-',
                'no_tahanan' => isset($responseBantaran['no_tahanan']) ? $responseBantaran['no_tahanan'] : '-',
                'nama_pasien' => isset($responseBantaran['nama_pasien']) ? $responseBantaran['nama_pasien'] : '-',
                'no_identitas_pasien' => isset($responseBantaran['no_identitas_pasien']) ? $responseBantaran['no_identitas_pasien'] : '-',
                'jenis_kelamin' => isset($responseBantaran['jenis_kelamin']) ? $responseBantaran['jenis_kelamin'] : '-',
                'tempat_lahir' => isset($responseBantaran['tempat_lahir']) ? $responseBantaran['tempat_lahir'] : '-',
                'tgl_lahir' => isset($responseBantaran['tgl_lahir']) ? $responseBantaran['tgl_lahir'] : '-',
                'uptasal_nama' => isset($responseBantaran['uptasal_nama']) ? $responseBantaran['uptasal_nama'] : '-',
                'keterangan_rujukan' => isset($responseBantaran['keterangan_rujukan']) ? $responseBantaran['keterangan_rujukan'] : '-',
                'tujuan_pemeriksaan' => isset($responseBantaran['tujuan_pemeriksaan']) ? $responseBantaran['tujuan_pemeriksaan'] : '-',
                'tgl_kunjungan' => isset($responseBantaran['tgl_kunjungan']) ? $responseBantaran['tgl_kunjungan'] : '-',
                'instalasi_nama' => isset($responseBantaran['instalasi_nama']) ? $responseBantaran['instalasi_nama'] : '-',
                'ruangan_nama' => isset($responseBantaran['ruangan_nama']) ? $responseBantaran['ruangan_nama'] : '-',
                'dokter_nama' => isset($responseBantaran['dokter_nama']) ? $responseBantaran['dokter_nama'] : '-',
                'rencana_tindakan' => isset($responseBantaran['rencana_tindakan']) ? $responseBantaran['rencana_tindakan'] : '-',
                'status_pelayanan_bantaran' => isset($responseBantaran['status_pelayanan_bantaran']) ? $responseBantaran['status_pelayanan_bantaran'] : '-',
            ];
            
            // Get pendaftaran_id for MonitoringTtvTrait
            $pendaftaranId = isset($responseBantaran['pendaftaran_id']) ? $responseBantaran['pendaftaran_id'] : null;
            $pendaftaranIdEncrypted = null;
            $sumber = [];
            $jenisTtv = [];
            $tingkatKesadaran = [];
            
            // Only fetch TTV data if pendaftaran_id exists (after approval)
            if (!empty($pendaftaranId)) {
                $pendaftaranIdEncrypted = DocoHelpers::encrypt($pendaftaranId);
                
                // Get sumber TTV filter
                try {
                    $filterResponse = $restRajal->get('monitoring-ttv/get-filter?pendaftaran_id=' . $pendaftaranId, ['form_params' => []]);
                    $filterBody = json_decode($filterResponse->getBody(), true);
                    Yii::error($filterBody, 'rujukan-bantaran-sirs-ttv-filter');
                    $sumber = isset($filterBody['response']['sumberTtv']) ? $filterBody['response']['sumberTtv'] : [];
                    $jenisTtv = isset($filterBody['response']['jenisTtv']) ? $filterBody['response']['jenisTtv'] : [];
                    $tingkatKesadaran = isset($filterBody['response']['tingkatKesadaran']) ? $filterBody['response']['tingkatKesadaran'] : [];
                } catch (\Exception $e) {
                    Yii::error('Failed to get TTV filters: ' . $e->getMessage(), 'rujukan-bantaran-sirs');
                }
            }

            return $controller->render('@app/extensions/pendaftaran/views/rujukan-bantaran/periksa', [
                'title' => $title,
                'dataPasien' => $dataPasien,
                'responseBantaran' => $responseBantaran,
                'responseDokumenBantaran' => $responseDokumenBantaran,
                'responseDokumenMedis' => $responseDokumenMedis,
                'bantaranId' => $id,
                'ruanganId' => $ruanganId,
                'pendaftaranId' => $pendaftaranIdEncrypted,
                'pendaftaranIdDecrypt' => $pendaftaranId,
                'sumber' => $sumber,
                'jenisTtv' => $jenisTtv,
                'tingkatKesadaran' => $tingkatKesadaran,
                'statusPelayananBantaranId' => isset($responseBantaran['status_pelayanan_bantaran_id']) ? $responseBantaran['status_pelayanan_bantaran_id'] : null,
            ]);
            
        } catch (RequestException $e) {
            Yii::error('RujukanBantaranSirsOnly - RequestException: ' . $e->getMessage(), 'rujukan-bantaran-sirs');
            Yii::$app->session->setFlash('error', 'Gagal memuat data pemeriksaan rujukan bantaran: ' . $e->getMessage());
            return $controller->redirect(['index']);
            
        } catch (\Exception $e) {
            Yii::error('RujukanBantaranSirsOnly - Exception: ' . $e->getMessage(), 'rujukan-bantaran-sirs');
            Yii::$app->session->setFlash('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
            return $controller->redirect(['index']);
        }
    }
    
    protected function processDetailRujukan($controller)
    {
        $request = Yii::$app->request;
        $bantaranId = $request->get('id');
        $bantaranId = DocoHelpers::decrypt($bantaranId);

        $restPendaftaran = Yii::$app->docoRest->pendaftaran;
        $response = $restPendaftaran->get('rujukan-bantaran/detail-rujukan?bantaran_id=' . $bantaranId, ['form_params' => []]);
        $body = json_decode($response->getBody(), true);

        $responseBantaran = isset($body['response']['bantaran']) ? $body['response']['bantaran'] : [];
        $responseDokumenBantaran = isset($body['response']['dokumen']) ? $body['response']['dokumen'] : [];
        $responseDokumenMedis = isset($body['response']['dokumen_medis']) ? $body['response']['dokumen_medis'] : [];

        $statusVerikasi = isset($responseBantaran['status_verifikasi_bantaran']) ? $responseBantaran['status_verifikasi_bantaran'] : 'Detail Rujukan';
        $title = "Detail Rujukan - ".$statusVerikasi;

        return $controller->renderPartial('@app/extensions/pendaftaran/views/rujukan-bantaran/popup-rujukan', [
            'responseBantaran' => $responseBantaran,
            'responseDokumenBantaran' => $responseDokumenBantaran,
            'responseDokumenMedis' => $responseDokumenMedis,
            'statusVerikasi' => $statusVerikasi,
            'title' => $title,
        ]);
    }
    
    protected function processUploadDokumen($controller)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $bantaranId = $request->post('rujukanbantaran_id');
        $namaDokumen = trim($request->post('nama_dokumen'));
        $dokumenFile = \yii\web\UploadedFile::getInstanceByName('dokumen_file');

        if (empty($bantaranId) || empty($dokumenFile)) {
            return DocoHelpers::response([
                'message' => 'Dokumen atau data rujukan tidak valid.',
                'text' => 'Pastikan file dan data rujukan terisi.'
            ], 422);
        }

        $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
        if (!in_array(strtolower($dokumenFile->extension), $allowedExtensions)) {
            return DocoHelpers::response([
                'message' => 'Format dokumen tidak didukung.',
                'text' => 'File harus berformat PDF atau gambar (JPG/PNG).'
            ], 422);
        }

        if (empty($namaDokumen)) {
            $namaDokumen = $dokumenFile->baseName;
        }

        $relativePath = '/media/input-hasil-pembantaran/' . $bantaranId . '/';
        $savePath = Yii::getAlias('@webroot' . $relativePath);
        \yii\helpers\FileHelper::createDirectory($savePath, 0775, true);

        $safeBaseName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $dokumenFile->baseName);
        $fileName = $safeBaseName . '_' . time() . '.' . $dokumenFile->extension;
        $fullPath = $savePath . $fileName;

        if (!$dokumenFile->saveAs($fullPath)) {
            return DocoHelpers::response([
                'message' => 'Dokumen gagal disimpan.',
                'text' => 'Silakan coba kembali.'
            ], 500);
        }

        $publicUrl = Yii::$app->request->hostInfo . Yii::$app->request->baseUrl . $relativePath . $fileName;

        try {
            $restPendaftaran = Yii::$app->docoRest->pendaftaran;
            $response = $restPendaftaran->post('rujukan-bantaran/upload-dokumen', [
                'form_params' => [
                    'rujukanbantaran_id' => $bantaranId,
                    'nama_dokumen' => $namaDokumen,
                    'url_dokumen' => $publicUrl
                ]
            ]);

            $body = json_decode($response->getBody(), true);
            if (isset($body['metadata']['message'])) {
                $body['message'] = $body['metadata']['message'];
            }
            return DocoHelpers::response($body);
        } catch (RequestException $e) {
            @unlink($fullPath);
            return DocoHelpers::response([
                'message' => 'Gagal menyimpan dokumen.',
                'text' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            @unlink($fullPath);
            return DocoHelpers::response([
                'message' => 'Gagal menyimpan dokumen.',
                'text' => $e->getMessage()
            ], 500);
        }
    }
    
    protected function processVerifikasiRujukan($controller)
    {
        $request = Yii::$app->request;
        $bantaranId = $request->get('id');
        $bantaranId = DocoHelpers::decrypt($bantaranId);

        $restPendaftaran = Yii::$app->docoRest->pendaftaran;
        $response = $restPendaftaran->get('rujukan-bantaran/detail-rujukan?bantaran_id=' . $bantaranId, ['form_params' => []]);
        $body = json_decode($response->getBody(), true);

        $responseBantaran = isset($body['response']['bantaran']) ? $body['response']['bantaran'] : [];
        $listCarabayar = isset($body['response']['list_carabayar']) ? $body['response']['list_carabayar'] : [];
        $listInstalasi = isset($body['response']['list_instalasi']) ? $body['response']['list_instalasi'] : [];
        $responseDokumenBantaran = isset($body['response']['dokumen_bantaran']) ? $body['response']['dokumen_bantaran'] : [];

        $statusVerikasi = isset($responseBantaran['status_verifikasi_bantaran']) ? $responseBantaran['status_verifikasi_bantaran'] : 'Verifikasi Rujukan';
        $title = "Verifikasi Rujukan Bantaran";

        $isVerifikasiRujukan = true;

        $model = new \app\modules\pendaftaran\models\VerifikasiBantaranForm;

        return $controller->renderPartial('@app/extensions/pendaftaran/views/rujukan-bantaran/popup-rujukan', [
            'responseBantaran' => $responseBantaran,
            'listCarabayar' => $listCarabayar,
            'listInstalasi' => $listInstalasi,
            'statusVerikasi' => $statusVerikasi,
            'responseDokumenBantaran' => $responseDokumenBantaran,
            'title' => $title,
            'isVerifikasiRujukan' => $isVerifikasiRujukan,
            'model' => $model,
        ]);
    }
    
    protected function processTolakRujukan($controller)
    {
        $request = Yii::$app->request;
        $bantaranId = $request->get('id');
        $bantaranId = DocoHelpers::decrypt($bantaranId);

        $restPendaftaran = Yii::$app->docoRest->pendaftaran;
        $response = $restPendaftaran->get('rujukan-bantaran/detail-rujukan?bantaran_id=' . $bantaranId, ['form_params' => []]);
        $body = json_decode($response->getBody(), true);

        $responseBantaran = isset($body['response']['bantaran']) ? $body['response']['bantaran'] : [];
        $responseDokumenBantaran = isset($body['response']['dokumen_bantaran']) ? $body['response']['dokumen_bantaran'] : [];

        return $controller->renderPartial('@app/extensions/pendaftaran/views/rujukan-bantaran/popup-tolak-bantaran', [
            'responseBantaran' => $responseBantaran,
            'responseDokumenBantaran' => $responseDokumenBantaran,
        ]);
    }
    
    protected function processRejectBantaran($controller)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $request = Yii::$app->request;
        $bantaranId = $request->post('bantaran_id');
        $keteranganTolakRujukan = $request->post('keterangan_tolak_rujukan');

        try {
            $restPendaftaran = Yii::$app->docoRest->pendaftaran;
            $response = $restPendaftaran->post('rujukan-bantaran/reject-bantaran', [
                'form_params' => [
                    'bantaran_id' => $bantaranId,
                    'keterangan_tolak_rujukan' => $keteranganTolakRujukan
                ]
            ]);
            
            $body = json_decode($response->getBody(), true);

            if (isset($body['metadata']['status']) && $body['metadata']['status'] == 200) {
                return [
                    'success' => true,
                    'message' => isset($body['response']['message']) ? $body['response']['message'] : 'Rujukan bantaran berhasil ditolak.',
                    'pengajuan_id' => isset($body['response']['pengajuan_id']) ? $body['response']['pengajuan_id'] : null,
                    'nomor_pengajuan' => isset($body['response']['nomor_pengajuan']) ? $body['response']['nomor_pengajuan'] : null,
                    'status_pengajuan_id' => isset($body['response']['status_pengajuan_id']) ? $body['response']['status_pengajuan_id'] : null,
                    'alasan_ditolak' => isset($body['response']['alasan_ditolak']) ? $body['response']['alasan_ditolak'] : null
                ];
            } else {
                return [
                    'success' => false,
                    'message' => isset($body['response']['message']) ? $body['response']['message'] : 'Gagal menolak rujukan bantaran.'
                ];
            }
            
        } catch (RequestException $e) {
            $errorBody = $e->hasResponse() ? json_decode($e->getResponse()->getBody(), true) : [];
            $errorMessage = isset($errorBody['response']['message']) ? $errorBody['response']['message'] : $e->getMessage();
            
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $errorMessage
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }
    
    protected function processShowAttachment($controller)
    {
        $request = Yii::$app->request;
        $dokumenbantaran_id = $request->get('id');
        
        $restPendaftaran = Yii::$app->docoRest->pendaftaran;
        $response = $restPendaftaran->get('rujukan-bantaran/show-dokumen?dokumenbantaran_id=' . $dokumenbantaran_id, ['form_params' => []]);
        $body = json_decode($response->getBody(), true);

        var_dump($body);
    }
    
    protected function processBatalVerifikasi($controller)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $request = Yii::$app->request;
        $bantaranId = $request->post('bantaran_id');
        $bantaranId = DocoHelpers::decrypt($bantaranId);
        
        try {
            $restPendaftaran = Yii::$app->docoRest->pendaftaran;
            $response = $restPendaftaran->post('rujukan-bantaran/batal-verifikasi', [
                'form_params' => [
                    'bantaran_id' => $bantaranId,
                ]
            ]);
            
            $body = json_decode($response->getBody(), true);
            
            if (isset($body['metadata']['status']) && $body['metadata']['status'] == 200) {
                return [
                    'success' => true,
                    'message' => isset($body['response']['message']) ? $body['response']['message'] : 'Verifikasi bantaran berhasil dibatalkan.',
                ];
            } else {
                return [
                    'success' => false,
                    'message' => isset($body['response']['message']) ? $body['response']['message'] : 'Gagal membatalkan verifikasi bantaran.'
                ];
            }
            
        } catch (RequestException $e) {
            $errorBody = $e->hasResponse() ? json_decode($e->getResponse()->getBody(), true) : [];
            $errorMessage = isset($errorBody['response']['message']) ? $errorBody['response']['message'] : $e->getMessage();
            
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $errorMessage
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }
    
    protected function processApproveBantaran($controller)
    {
        $request = Yii::$app->request;
        $post = $request->post();
        
        $restPendaftaran = Yii::$app->docoRest->pendaftaran;
        $requests = $restPendaftaran->post('rujukan-bantaran/verification', ['form_params' => $post]);

        $response = json_decode($requests->getBody(), true);
        return DocoHelpers::response($response);
    }
    
    protected function processSimpanSoap($controller)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $post = $request->post();
        
        try {
            $restPendaftaran = Yii::$app->docoRest->pendaftaran;
            $response = $restPendaftaran->post('rujukan-bantaran/insert-cppt', ['form_params' => $post]);
            $body = json_decode($response->getBody(), true);
            
            return $body['response'];
            
        } catch (RequestException $e) {
            Yii::error('RujukanBantaranSirsOnly - processSimpanSoap Error: ' . $e->getMessage(), 'rujukan-bantaran-sirs');
            Yii::$app->response->statusCode = 500;
            return ['message' => 'Gagal menyimpan data SOAP: ' . $e->getMessage()];
        } catch (\Exception $e) {
            Yii::error('RujukanBantaranSirsOnly - processSimpanSoap Error: ' . $e->getMessage(), 'rujukan-bantaran-sirs');
            Yii::$app->response->statusCode = 500;
            return ['message' => 'Terjadi kesalahan: ' . $e->getMessage()];
        }
    }
    
    protected function processGetSoapData($controller)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $rujukanbantaran_id = $request->get('rujukanbantaran_id');
        
        try {
            $restPendaftaran = Yii::$app->docoRest->pendaftaran;
            $response = $restPendaftaran->get('rujukan-bantaran/get-cppt-data?rujukanbantaran_id=' . $rujukanbantaran_id);
            $body = json_decode($response->getBody(), true);
            
            return $body['response'];
            
        } catch (RequestException $e) {
            Yii::error('RujukanBantaranSirsOnly - processGetSoapData Error: ' . $e->getMessage(), 'rujukan-bantaran-sirs');
            return ['data' => [], 'message' => 'Gagal mengambil data SOAP.'];
        } catch (\Exception $e) {
            Yii::error('RujukanBantaranSirsOnly - processGetSoapData Error: ' . $e->getMessage(), 'rujukan-bantaran-sirs');
            return ['data' => [], 'message' => 'Terjadi kesalahan.'];
        }
    }
    
    protected function processHapusSoap($controller)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $post = $request->post();
        
        try {
            $restPendaftaran = Yii::$app->docoRest->pendaftaran;
            $response = $restPendaftaran->post('rujukan-bantaran/remove-cppt', ['form_params' => $post]);
            $body = json_decode($response->getBody(), true);
            
            return $body['response'];
            
        } catch (RequestException $e) {
            Yii::error('RujukanBantaranSirsOnly - processHapusSoap Error: ' . $e->getMessage(), 'rujukan-bantaran-sirs');
            Yii::$app->response->statusCode = 500;
            return ['message' => 'Gagal menghapus data SOAP: ' . $e->getMessage()];
        } catch (\Exception $e) {
            Yii::error('RujukanBantaranSirsOnly - processHapusSoap Error: ' . $e->getMessage(), 'rujukan-bantaran-sirs');
            Yii::$app->response->statusCode = 500;
            return ['message' => 'Terjadi kesalahan: ' . $e->getMessage()];
        }
    }

    protected function processSimpanTtv($controller)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            // Get filter data for LOV (jenis, kesadaran, sumber)
            $pendaftaranId = ArrayHelper::getValue($post, 'pendaftaran_id');
            
            if (empty($pendaftaranId)) {
                Yii::$app->response->statusCode = 422;
                return DocoHelpers::response([
                    'message' => 'Pendaftaran ID tidak ditemukan'
                ], 422);
            }
            
            $restRajal = Yii::$app->docoRest->rajal;
            
            // Get filter data
            $filterResponse = $restRajal->get('monitoring-ttv/get-filter', [
                'query' => ['pendaftaran_id' => $pendaftaranId]
            ]);
            $filterBody = json_decode($filterResponse->getBody(), true);
            $lov = ArrayHelper::getValue($filterBody, 'response', []);

            // Process and transform data
            $formData = [];
            $formData['pendaftaran_id'] = $pendaftaranId;
            
            // Date/Time conversion
            $formData['tanggal_ttv'] = !empty($post['tgl_ttv']) 
                ? date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $post['tgl_ttv']))) 
                : null;
            
            // Sumber TTV - default to Monitoring if not provided
            $formData['sumberttv_id'] = (int)ArrayHelper::getValue($post, 'sumberttv_id', DocoConstants::SUMBER_TTV_MONITORING_ID);
            $formData['sumberttv'] = ArrayHelper::getValue($lov['sumberTtv'], $formData['sumberttv_id'], '');
            
            // Jenis TTV - form sends text value, need to find ID from LOV
            $jenisttv_text = trim((string)ArrayHelper::getValue($post, 'jenis_ttv'));
            $jenisTtvLov = ArrayHelper::getValue($lov, 'jenisTtv', []);
            // Search for the key (ID) by value (text) in the LOV array
            $jenisttv_key = array_search($jenisttv_text, $jenisTtvLov);
            $formData['jenisttv_id'] = ($jenisttv_key !== false) ? (int)$jenisttv_key : null;
            $formData['jenisttv'] = $jenisttv_text;
            
            // Tingkat Kesadaran - form sends text value, need to find ID from LOV
            $tingkatkesadaran_text = trim((string)ArrayHelper::getValue($post, 'tingkat_kesadaran'));
            $tingkatKesadaranLov = ArrayHelper::getValue($lov, 'tingkatKesadaran', []);
            // Search for the key (ID) by value (text) in the LOV array
            $tingkatkesadaran_key = array_search($tingkatkesadaran_text, $tingkatKesadaranLov);
            
            $formData['tingkatkesadaran_id'] = ($tingkatkesadaran_key !== false) ? (int)$tingkatkesadaran_key : null;
            $formData['tingkatkesadaran'] = $tingkatkesadaran_text;
            
            // TTV Values - map from form fields to API fields, convert to proper types
            $formData['sistol'] = !empty($post['sistol']) ? (int)$post['sistol'] : null;
            $formData['diastol'] = !empty($post['diastol']) ? (int)$post['diastol'] : null;
            $formData['nadi'] = !empty($post['nadi']) ? (int)$post['nadi'] : null;
            $formData['suhu'] = !empty($post['suhu']) ? (float)$post['suhu'] : null;
            $formData['respirasi'] = !empty($post['pernapasan']) ? (int)$post['pernapasan'] : null;
            $formData['spo2'] = !empty($post['spo2']) ? (int)$post['spo2'] : null;
            $formData['tinggi_badan'] = !empty($post['tinggi_badan']) ? (float)$post['tinggi_badan'] : null;
            $formData['berat_badan'] = !empty($post['berat_badan']) ? (float)$post['berat_badan'] : null;
            
            // GCS Values - convert to integers
            $formData['gcs_e'] = !empty($post['gcs_e']) ? (int)$post['gcs_e'] : null;
            $formData['gcs_m'] = !empty($post['gcs_m']) ? (int)$post['gcs_m'] : null;
            $formData['gcs_v'] = !empty($post['gcs_v']) ? (int)$post['gcs_v'] : null;

            // Insert TTV data
            $response = $restRajal->post('monitoring-ttv/insert', ['form_params' => $formData]);
            $body = json_decode($response->getBody(), true);
            
            return DocoHelpers::response($body);
            
        } catch (RequestException $e) {
            // Log the full error response for debugging
            $errorBody = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'No response body';
            Yii::error('RujukanBantaranSirsOnly - processSimpanTtv Error: ' . $e->getMessage() . ' | Response: ' . $errorBody, 'rujukan-bantaran-sirs');
            
            // Try to parse error response
            $errorData = json_decode($errorBody, true);
            $message = isset($errorData['response']['message']) ? $errorData['response']['message'] : 'Gagal menyimpan data Monitoring TTV';
            $errors = isset($errorData['response']['errors']) ? $errorData['response']['errors'] : [];
            
            Yii::$app->response->statusCode = $e->getCode();
            return DocoHelpers::response([
                'message' => $message,
                'errors' => $errors,
                'formData' => $formData // Debug: show what was sent
            ], $e->getCode());
        } catch (\Exception $e) {
            Yii::error('RujukanBantaranSirsOnly - processSimpanTtv Error: ' . $e->getMessage(), 'rujukan-bantaran-sirs');
            Yii::$app->response->statusCode = 500;
            return DocoHelpers::response([
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    protected function processGetTtvData($controller)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id');
        
        try {
            $restRajal = Yii::$app->docoRest->rajal;
            $response = $restRajal->get(
                'monitoring-ttv/get-data', 
                ['query' => ['pendaftaran_id' => $pendaftaran_id, 'is_filter_date' => false]]
            );
            $body = json_decode($response->getBody(), true);
            
            return $body['response'];
            
        } catch (RequestException $e) {
            Yii::error('RujukanBantaranSirsOnly - processGetTtvData Error: ' . $e->getMessage(), 'rujukan-bantaran-sirs');
            return ['data' => [], 'message' => 'Gagal memuat data Monitoring TTV.'];
        } catch (\Exception $e) {
            Yii::error('RujukanBantaranSirsOnly - processGetTtvData Error: ' . $e->getMessage(), 'rujukan-bantaran-sirs');
            return ['data' => [], 'message' => 'Terjadi kesalahan.'];
        }
    }
    
    protected function processHapusTtv($controller)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $post = $request->post();
        
        try {
            $restRajal = Yii::$app->docoRest->rajal;
            $response = $restRajal->post('monitoring-ttv/remove-ttv', ['form_params' => $post]);
            $body = json_decode($response->getBody(), true);
            
            return $body['response'];
            
        } catch (RequestException $e) {
            Yii::error('RujukanBantaranSirsOnly - processHapusTtv Error: ' . $e->getMessage(), 'rujukan-bantaran-sirs');
            Yii::$app->response->statusCode = 500;
            return ['message' => 'Gagal menghapus data Monitoring TTV: ' . $e->getMessage()];
        } catch (\Exception $e) {
            Yii::error('RujukanBantaranSirsOnly - processHapusTtv Error: ' . $e->getMessage(), 'rujukan-bantaran-sirs');
            Yii::$app->response->statusCode = 500;
            return ['message' => 'Terjadi kesalahan: ' . $e->getMessage()];
        }
    }
    
    protected function processPemulanganTahanan($controller)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $post = $request->post();
        
        $rujukanbantaran_id = ArrayHelper::getValue($post, 'rujukanbantaran_id');
        $catatan_pemulangan = trim(ArrayHelper::getValue($post, 'catatan_pemulangan', ''));
        
        // Validation
        if (empty($rujukanbantaran_id)) {
            Yii::$app->response->statusCode = 422;
            return DocoHelpers::response([
                'success' => false,
                'message' => 'ID rujukan bantaran tidak ditemukan'
            ], 422);
        }
        
        if (empty($catatan_pemulangan)) {
            Yii::$app->response->statusCode = 422;
            return DocoHelpers::response([
                'success' => false,
                'message' => 'Catatan pemulangan harus diisi'
            ], 422);
        }
        
        try {
            $restPendaftaran = Yii::$app->docoRest->pendaftaran;
            
            $pegawaiId = null;
            if (Yii::$app->user->identity && isset(Yii::$app->user->identity->pegawai_id)) {
                $pegawaiId = Yii::$app->user->identity->pegawai_id;
            }
            
            $response = $restPendaftaran->post('rujukan-bantaran/pemulangan-tahanan', [
                'form_params' => [
                    'rujukanbantaran_id' => $rujukanbantaran_id,
                    'catatan_pemulangan' => $catatan_pemulangan,
                    'tgl_pemulangan' => date('Y-m-d H:i:s'),
                    'pegawai_id' => $pegawaiId,
                ]
            ]);
            
            $body = json_decode($response->getBody(), true);
            
            // Check if response has metadata structure
            $message = 'Pemulangan tahanan berhasil diproses';
            if (isset($body['metadata']['message'])) {
                $message = $body['metadata']['message'];
            } elseif (isset($body['message'])) {
                $message = $body['message'];
            }
            
            return DocoHelpers::response([
                'success' => true,
                'message' => $message
            ]);
            
        } catch (RequestException $e) {
            $errorBody = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'No response body';
            Yii::error('RujukanBantaranSirsOnly - processPemulanganTahanan Error: ' . $e->getMessage() . ' | Response: ' . $errorBody, 'rujukan-bantaran-sirs');
            
            // Try to parse error response
            $errorData = json_decode($errorBody, true);
            $message = isset($errorData['metadata']['message']) ? $errorData['metadata']['message'] : 
                      (isset($errorData['message']) ? $errorData['message'] : 'Gagal memproses pemulangan tahanan');
            
            Yii::$app->response->statusCode = $e->getCode();
            return DocoHelpers::response([
                'success' => false,
                'message' => $message
            ], $e->getCode());
            
        } catch (\Exception $e) {
            Yii::error('RujukanBantaranSirsOnly - processPemulanganTahanan Error: ' . $e->getMessage(), 'rujukan-bantaran-sirs');
            Yii::$app->response->statusCode = 500;
            return DocoHelpers::response([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function processPrintQr($controller)
    {
        $request = Yii::$app->request;
        $encryptedId = $request->get('id');

        if (empty($encryptedId)) {
            throw new NotFoundHttpException('ID rujukan bantaran tidak ditemukan.');
        }

        $bantaranId = DocoHelpers::decrypt($encryptedId);
        if (empty($bantaranId)) {
            throw new NotFoundHttpException('ID rujukan bantaran tidak valid.');
        }

        $filePath = Yii::getAlias('@download') . '/bantaran-qr-' . $bantaranId . '.pdf';
        $tempDir = Yii::getAlias('@runtime/mpdf');
        FileHelper::createDirectory($tempDir);

        try {
            $restPendaftaran = Yii::$app->docoRest->pendaftaran;
            $response = $restPendaftaran->get('rujukan-bantaran/print-qr-data?bantaran_id=' . $bantaranId, ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $data = isset($body['response']) ? $body['response'] : $body;

            $bantaran = isset($data['bantaran']) ? $data['bantaran'] : [];
            if (empty($bantaran)) {
                throw new NotFoundHttpException('Data rujukan bantaran tidak ditemukan.');
            }

            $qrImage = isset($data['qr_image']) ? $data['qr_image'] : '';
            $hospitalName = isset($data['hospital_name']) ? $data['hospital_name'] : Yii::$app->name;
            $hospitalAddress = isset($data['hospital_address']) ? $data['hospital_address'] : '';

            $html = $controller->renderPartial('@app/extensions/pendaftaran/views/rujukan-bantaran/print-qr', [
                'bantaran' => $bantaran,
                'qrImage' => $qrImage,
                'hospitalName' => $hospitalName,
                'hospitalAddress' => $hospitalAddress,
            ]);

            $mpdf = new Mpdf([
                'format' => [50, 100],
                'orientation' => 'P',
                'margin_left' => 3,
                'margin_right' => 3,
                'margin_top' => 3,
                'margin_bottom' => 3,
                'tempDir' => $tempDir,
            ]);
            $mpdf->WriteHTML($html);
            $mpdf->Output($filePath, Destination::FILE);

            return DocoHelpers::previewPdf($filePath, null, true);
        } catch (RequestException $e) {
            Yii::error('RujukanBantaranSirsOnly - processPrintQr RequestException: ' . $e->getMessage(), 'rujukan-bantaran-sirs');
            throw new HttpException(500, 'Gagal menyiapkan dokumen QR pembantaran.');
        } catch (NotFoundHttpException $e) {
            throw $e;
        } catch (\Exception $e) {
            Yii::error('RujukanBantaranSirsOnly - processPrintQr Error: ' . $e->getMessage(), 'rujukan-bantaran-sirs');
            throw new HttpException(500, 'Gagal menyiapkan dokumen QR pembantaran.');
        }
    }
}
