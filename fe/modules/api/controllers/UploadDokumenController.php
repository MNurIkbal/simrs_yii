<?php

namespace Doco\api\controllers;

use Yii;
use app\components\DocoController;
use app\components\DocoHelpers;
use yii\web\UploadedFile;
use app\modules\api\models\DokumenPasienForm;
use app\components\Services\UplodDokumenService;
use app\components\DocoConstants;
use yii\web\Response;
use app\components\DocoDatatableHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

class UploadDokumenController extends DocoController
{
    protected $allowAction = ['*'];
    public $fileAction = [
        'preview' => [
            'pdf'
        ],
        'download' => [
            'doc',
            'docx',
            'xls',
            'xlsx',
            'txt',
            'csv'
        ],
        'detail' => [
            'jpg',
            'png',
            'jpeg'
        ]
    ];

    public function actionTabUploadDokumen($id, $pasienadmisi_id = null, $is_modal = null, $parent_id = 'dokumen-tab-ranap', $is_pasienid = null, $isHide = false)
    {

        $model = new DokumenPasienForm;
        $pasien_id = null;
        $idEncrypted = $id;
        if(empty($is_pasienid)){
            $id = $this->helper->decrypt($id);
        } else {
            $pasien_id = $this->helper->decrypt($id);
            $id = null;
        }

        if (Yii::$app->request->isPost) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $model->load(Yii::$app->request->post());
    
            $File = UploadedFile::getInstance($model, 'attachment');

            $req = Yii::$app->request->post();

            $model->pasienadmisi_id = $pasienadmisi_id;
            if($is_pasienid == null){
                $model->pendaftaran_id = $id;
            } else{
                $model->pasien_id = $id;
            }
            $model->dokumen_id = isset($req['DokumenPasienForm']['dokumen_id']) ? $req['DokumenPasienForm']['dokumen_id'] : null;
            $model->is_eklaim = isset($req['DokumenPasienForm']['is_eklaim']) ? $req['DokumenPasienForm']['is_eklaim'] : 0;
            $no_rekam_medik = isset($req['DokumenPasienForm']['no_rekam_medik']) ? $req['DokumenPasienForm']['no_rekam_medik'] : null;
            $no_pendaftaran = isset($req['DokumenPasienForm']['no_pendaftaran']) ? $req['DokumenPasienForm']['no_pendaftaran'] : null;
            $dokter_id = isset($req['DokumenPasienForm']['dokter_id']) ? $req['DokumenPasienForm']['dokter_id'] : null;
            $ruangan_id = isset($req['DokumenPasienForm']['ruangan_id']) ? $req['DokumenPasienForm']['ruangan_id'] : null;
            $doc_date = isset($req['DokumenPasienForm']['doc_date']) ? $req['DokumenPasienForm']['doc_date'] : null;
            $nama_dokumen_freetext = isset($req['DokumenPasienForm']['nama_dokumen_freetext']) ? $req['DokumenPasienForm']['nama_dokumen_freetext'] : null;

            if($is_pasienid == null){
                $path = '/uploads/dokumenpasien/' . $no_rekam_medik . '/' . $no_pendaftaran . '/';
            } else {
                $path = '/uploads/dokumenpasien/' . $no_rekam_medik . '/';
            }
            
            $ext = '';
            $fileName = '';
            
            if (!empty($File)) {
                $ext = end(explode(".", $File->name));
                if($is_pasienid == null){
                    $fileName = $no_pendaftaran . '-' . $model->dokumen_id . '-' . date('Ymdhis', strtotime('NOW')) . '.' . $ext;
                } else {
                    $fileName = $no_rekam_medik . '-' . $model->dokumen_id . '-' . date('Ymdhis', strtotime('NOW')) . '.' . $ext;
                }
                if (($ext == 'mp4') or ($ext == 'MP4') or ($ext == '3gp') or ($ext == 'mkv')) {
                    $result['response']['message'] = Yii::t('fe', 'Proses gagal !');
                    $result['response']['text'] = Yii::t('fe', 'File harus berbentuk jpg, png, pdf, doc, xls, txt dengan maksimal 20 mb');

                    return DocoHelpers::response($result, 422);
                }

                $model->filename = $fileName;
                $model->path = $path;
                $model->no_rekam_medik = $no_rekam_medik;
                $model->no_pendaftaran = $no_pendaftaran;
                $model->pasien_id = $pasien_id;
                $model->ruangan_id = $ruangan_id;
                $model->dokter_id = $dokter_id;
                $model->doc_date = $doc_date;
                $model->nama_dokumen_freetext = $nama_dokumen_freetext;
                $response = (new UplodDokumenService)->uploadDokumen($model->attributes);
            }

            if ($response['metadata']['status'] == 200) {
                $File = (new UplodDokumenService)->uploadFile($File, $path, $model->filename, $no_pendaftaran);
                if ($File['status'] == true) {
                    $model->filename = $File['file_name'];
                } else {
                    $result['response']['message'] = $File['title'];
                    $result['response']['text'] = $File['text'];
                    return DocoHelpers::response($result, 422);
                }
            }

            return $response;
        }
            $datas = Yii::$app->docoRest->rm->get('allow/data-dokumen?pasien_id='. $pasien_id .'&pendaftaran_id=' . $id);
            $datas = json_decode($datas->getBody(), true);
            $dokumen = [];
            $dokumenEklaim = [];
            if (!empty($datas['response']['dokumen'])) {
                $dokumen = $datas['response']['dokumen'];
            }
            if (!empty($datas['response']['dokumen_eklaim'])) {
                $dokumenEklaim = $datas['response']['dokumen_eklaim'];
            }
            $data = (!empty($datas['response']['items'])) ? $datas['response']['items'] : [];
            $model->no_pendaftaran = isset($datas['response']['pendaftaran']['no_pendaftaran']) ? $datas['response']['pendaftaran']['no_pendaftaran'] : null;
            $model->no_rekam_medik = isset($datas['response']['pendaftaran']['no_rekam_medik']) ? $datas['response']['pendaftaran']['no_rekam_medik'] : null;
            $groupCaraBayarId = isset($datas['response']['pendaftaran']['groupcarabayar_id']) ? $datas['response']['pendaftaran']['groupcarabayar_id'] : null;
            if(!empty($is_pasienid))
            {
                $data['is_pasien'] = 1;
                $data['pasien_id'] = $pasien_id;
            }
            
            $isHide = $isHide === "true";
        
        return $this->renderAjax('index', [
            'model' => $model,
            'data' => $data,
            'dokumen' => $dokumen,
            'dokumen_eklaim' => $dokumenEklaim,
            'pendaftaran_id' => $id,
            'pendaftaran_id_encrypted' => $idEncrypted,
            'is_modal' => $is_modal,
            'parent_id' => $parent_id,
            'is_pasienid' => $is_pasienid,
            'groupCaraBayarId' => $groupCaraBayarId,
            'isHide' => $isHide
        ]);
    }

    public function actionGetDokumenList()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $payload = array_merge(DocoDatatableHelper::advancedFilterParam(), [
            'pendaftaran_id' => $this->helper->decrypt(Yii::$app->request->get('id')),
            'pasien_id' => $this->helper->decrypt(Yii::$app->request->get('pasien_id')),
            'norm' => Yii::$app->request->get('norm'),
            'fileAction' => $this->fileAction
        ]);

        $result = (new UplodDokumenService)->getDokumenList($payload);
        return $result;
    }

    public function actionDeleteUpload($pendaftaran_id, $parent)
    {
        try {
            $result = (new UplodDokumenService)->deleteUpload($pendaftaran_id, $parent);
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionPreviewDokumen($dokumenupload_id)
    {
        $detailDokumen = (new UplodDokumenService)->getDetailDokumen($dokumenupload_id);
        $konfigFtp = (new UplodDokumenService)->konfigFtp();

        if (in_array(Yii::$app->request->get('filetype', null), $this->fileAction['preview'])) {
            return DocoHelpers::previewPdfFtp($konfigFtp, $detailDokumen['filePath']) ? DocoHelpers::previewPdfFtp($konfigFtp, $detailDokumen['filePath']) : DocoHelpers::previewPdf($detailDokumen['filePathOld']);
        } elseif (in_array(Yii::$app->request->get('filetype', null), $this->fileAction['download'])) {
            $namefile = ArrayHelper::getValue($detailDokumen,'dataDokumen.filename','-');
            $ext = end(explode('.', $detailDokumen['dataDokumen']['filename']));
            if (!DocoHelpers::downloadFileFtp($konfigFtp, $detailDokumen['filePath'], $namefile, $ext)) {
                if($ext == 'xlsx'){
                    ob_clean();
                    header("Pragma: public");
                    header("Expires: 0");
                    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                    header("Cache-Control: private",false);
                    header("Content-Description: File Transfer");
                    header("Content-Disposition: attachment; filename=\"$namefile\"");
                    header("Content-Type:  application/vnd.ms-excel");
                    header("Content-Transfer-Encoding: binary");
                    header("Content-Length: ".filesize($detailDokumen['filePathOld']));
                    readfile($detailDokumen['filePathOld']);
                    exit;
                    return Yii::$app->response->sendFile($detailDokumen['filePathOld'],$namefile,['mimeType'=>'xlsx']);
                }
                return Yii::$app->response->sendFile($detailDokumen['filePathOld'],$namefile);
            }
            return DocoHelpers::downloadFileFtp($konfigFtp, $detailDokumen['filePath'], $namefile, $ext);
        } else {
            return $this->renderAjax('_detail_dokumen', [
                'data' => $detailDokumen,
                'type' => end(explode('.', $detailDokumen['dataDokumen']['filename'])),
                'title' => 'Dokumen - ' . ucwords(strtolower($detailDokumen['dataDokumen']['nama_dokumen'])),
                'path' => $detailDokumen['filePath'],
                'pathOld' => $detailDokumen['filePathOld'],
                'konfigFtp' => $konfigFtp,
                'fileAction' => $this->fileAction
            ]);
        }
    }
}
