<?php
/**
 * RujukanBantaranProcess
 * 
 * Unified process class for all Rujukan Bantaran actions
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\pendaftaran\processes;

use Yii;
use yii\base\Action;
use yii\base\View;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;
use app\modules\pendaftaran\models\VerifikasiBantaranForm;

class RujukanBantaranProcess extends \app\components\DocoBaseProcessExtension
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
            case 'show-attachment':
                return $this->processShowAttachment($controller);
            case 'approve-bantaran':
                return $this->processApproveBantaran($controller);
            case 'reject-bantaran':
                return $this->processRejectBantaran($controller);
            default:
                return $this->processIndex($controller);
        }
    }
    
    protected function processIndex($controller)
    {
        $title = "Informasi Reservasi Rujukan Pembantaran";

        try {
            // Get index data from backend API
            $restPendaftaran = Yii::$app->docoRest->pendaftaran;
            $response = $restPendaftaran->get('rujukan-bantaran/get-index-data', ['form_params' => []]);
            $body = json_decode($response->getBody(), true);

            $masterUpt = isset($body['response']['master_upt']) ? $body['response']['master_upt'] : [];
            $masterInstalasi = isset($body['response']['instalasi']) ? $body['response']['instalasi'] : [];

            return $controller->render('index', [
                'title' => $title,
                'masterUpt' => $masterUpt,
                'masterInstalasi' => $masterInstalasi,
            ]);
            
        } catch (RequestException $e) {
            Yii::error('RujukanBantaranProcess - RequestException: ' . $e->getMessage(), 'rujukan-bantaran');
            Yii::$app->session->setFlash('error', 'Gagal memuat data rujukan bantaran: ' . $e->getMessage());
            
            // Set empty data for graceful degradation
            $masterUpt = [];
            $masterInstalasi = [];
            
            return $controller->render('index', [
                'title' => $title,
                'masterUpt' => $masterUpt,
                'masterInstalasi' => $masterInstalasi,
            ]);
            
        } catch (\Exception $e) {
            Yii::error('RujukanBantaranProcess - Exception: ' . $e->getMessage(), 'rujukan-bantaran');
            Yii::$app->session->setFlash('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
            
            // Set empty data for graceful degradation
            $masterUpt = [];
            $masterInstalasi = [];
            
            return $controller->render('index', [
                'title' => $title,
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

                $noReservasi = !empty($value['no_pendaftaranol']) ? $value['no_pendaftaranol'] : '-';
                $noRm = !empty($value['no_rekam_medik']) ? $value['no_rekam_medik'] : '-';
                $namaPasien = $noReservasi . '<br>' . $noRm . '<br>' .$value['nama_pasien'];

                $namaDokter = !empty($value['nama_dokter']) ? $value['nama_dokter'] : '-';
                $jamPoli = !empty($value['jam_buka']) && !empty($value['jam_tutup']) ?  $value['jam_buka'] .' - '.$value['jam_tutup'] : '';
                $ruangan = ($value['status_verifikasi_bantaran_id'] == DocoConstants::BELUM_VERIFIKASI_BANTARAN) ? '-' : $value['ruangan_nama'] . '<br>' . $namaDokter . '<br>' . $jamPoli;

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
        
        if (empty($id)) {
            Yii::$app->session->setFlash('error', 'ID rujukan bantaran tidak ditemukan.');
            return $controller->redirect(['index']);
        }

        $bantaranId = DocoHelpers::decrypt($id);
        $title = "Pemeriksaan Rujukan Bantaran";

        try {
            $restPendaftaran = Yii::$app->docoRest->pendaftaran;
            
            // Get detail rujukan bantaran
            $response = $restPendaftaran->get('rujukan-bantaran/detail-rujukan?bantaran_id=' . $bantaranId, ['form_params' => []]);
            $body = json_decode($response->getBody(), true);

            $responseBantaran = isset($body['response']['bantaran']) ? $body['response']['bantaran'] : [];
            $responseDokumenBantaran = isset($body['response']['dokumen']) ? $body['response']['dokumen'] : [];

            if (empty($responseBantaran)) {
                Yii::$app->session->setFlash('error', 'Data rujukan bantaran tidak ditemukan.');
                return $controller->redirect(['index']);
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
                'tgl_kunjungan' => isset($responseBantaran['tgl_kunjungan']) ? $responseBantaran['tgl_kunjungan'] : '-',
                'instalasi_nama' => isset($responseBantaran['instalasi_nama']) ? $responseBantaran['instalasi_nama'] : '-',
                'nama_dokter' => isset($responseBantaran['nama_dokter']) ? $responseBantaran['nama_dokter'] : '-',
            ];

            return $controller->render('periksa', [
                'title' => $title,
                'dataPasien' => $dataPasien,
                'responseBantaran' => $responseBantaran,
                'responseDokumenBantaran' => $responseDokumenBantaran,
                'bantaranId' => $id,
                'ruanganId' => $ruanganId,
            ]);
            
        } catch (RequestException $e) {
            Yii::error('RujukanBantaranProcess - RequestException: ' . $e->getMessage(), 'rujukan-bantaran');
            Yii::$app->session->setFlash('error', 'Gagal memuat data pemeriksaan rujukan bantaran: ' . $e->getMessage());
            return $controller->redirect(['index']);
            
        } catch (\Exception $e) {
            Yii::error('RujukanBantaranProcess - Exception: ' . $e->getMessage(), 'rujukan-bantaran');
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

        $statusVerikasi = isset($responseBantaran['status_verifikasi_bantaran']) ? $responseBantaran['status_verifikasi_bantaran'] : 'Detail Rujukan';
        $title = "Detail Rujukan - ".$statusVerikasi;

        return $controller->renderPartial('popup-rujukan', [
            'responseBantaran' => $responseBantaran,
            'responseDokumenBantaran' => $responseDokumenBantaran,
            'statusVerikasi' => $statusVerikasi,
            'title' => $title,
        ]);
    }
    
    protected function processKirimDokumen($controller)
    {
        $request = Yii::$app->request;
        $encryptedId = $request->get('id');
        $bantaranId = DocoHelpers::decrypt($encryptedId);

        if (empty($bantaranId)) {
            throw new \yii\web\BadRequestHttpException('Data rujukan tidak ditemukan.');
        }

        $restPendaftaran = Yii::$app->docoRest->pendaftaran;
        $response = $restPendaftaran->get('rujukan-bantaran/detail-rujukan?bantaran_id=' . $bantaranId, ['form_params' => []]);
        $body = json_decode($response->getBody(), true);

        $responseBantaran = isset($body['response']['bantaran']) ? $body['response']['bantaran'] : [];
        $responseDokumenBantaran = isset($body['response']['dokumen']) ? $body['response']['dokumen'] : [];

        return $controller->renderPartial('modal-kirim-dokumen', [
            'responseBantaran' => $responseBantaran,
            'responseDokumenBantaran' => $responseDokumenBantaran,
            'encryptedId' => $encryptedId
        ]);
    }
    
    protected function processUploadDokumen($controller)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $bantaranId = $request->post('rujukanbantaran_id');
        $namaDokumen = trim($request->post('nama_dokumen'));
        $dokumenFile = UploadedFile::getInstanceByName('dokumen_file');

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
        FileHelper::createDirectory($savePath, 0775, true);

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
        $responseDokumenBantaran = isset($body['response']['dokumen']) ? $body['response']['dokumen'] : [];
        $listCarabayar = isset($body['response']['list_carabayar']) ? $body['response']['list_carabayar'] : [];
        $listInstalasi = isset($body['response']['list_instalasi']) ? $body['response']['list_instalasi'] : [];

        $statusVerikasi = isset($responseBantaran['status_verifikasi_bantaran']) ? $responseBantaran['status_verifikasi_bantaran'] : 'Verifikasi Rujukan';
        $title = "Verifikasi Rujukan Bantaran";

        $isVerifikasiRujukan = true;

        $model = new VerifikasiBantaranForm;

        return $controller->renderPartial('popup-rujukan', [
            'responseBantaran' => $responseBantaran,
            'responseDokumenBantaran' => $responseDokumenBantaran,
            'listCarabayar' => $listCarabayar,
            'listInstalasi' => $listInstalasi,
            'statusVerikasi' => $statusVerikasi,
            'title' => $title,
            'isVerifikasiRujukan' => $isVerifikasiRujukan,
            'model' => $model,
        ]);
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
    
    protected function processApproveBantaran($controller)
    {
        $request = Yii::$app->request;
        $post = $request->post();
        
        $restPendaftaran = Yii::$app->docoRest->pendaftaran;
        $requests = $restPendaftaran->post('rujukan-bantaran/approve-bantaran', ['form_params' => $post]);

        $response = json_decode($requests->getBody(), true);
        return DocoHelpers::response($response);
    }

    public function actionRejectBantaran() {
        $request = Yii::$app->request;
        $post = $request->post();
        
        $restPendaftaran = Yii::$app->docoRest->pendaftaran;
        $requests = $restPendaftaran->post('rujukan-bantaran/reject-bantaran', ['form_params' => $post]);

        $response = json_decode($requests->getBody(), true);
        return DocoHelpers::response($response);
    }
}
