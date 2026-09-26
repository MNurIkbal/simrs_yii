<?php 

/**
 * @author Randy Vianda Putra
 * @todo Input Hasil Radiologi
 * @copyright 27 Juli 2018 aweutist
 */

namespace Doco\radiologi\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use Doco\radiologi\models\UploadHasilForm;

class InputHasilController extends DocoController
{
    protected $_title = "Hasil Pemeriksaan Radiologi";
    protected $_module = '/radiologi/hasil-rad';
    protected $_restRad;
    protected $allowAction = [
        '*'
    ];

    public function init()
    {
        parent::init();
        $this->_restRad = Yii::$app->docoRest->radiologi;
    }

    private function getDataApi($id = null, $daftartindakan_id, $tindakan_id)
    {
        $id = DocoHelpers::decrypt($id);
        $daftartindakan_id = DocoHelpers::decrypt($daftartindakan_id);
        $tindakan_id = DocoHelpers::decrypt($tindakan_id);
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        try {
            $response = $this->_restRad
                ->request('GET', 'input-hasil/generate-api?id=' . $id . '&ruangan_id=' . $ruangan_id . '&tindakan_id=' . $tindakan_id . '&daftartindakan_id=' . $daftartindakan_id);
            $body = json_decode($response->getBody(),TRUE);

            $return = [
                'data_pasien' => $body['response']['data-pasien'],
                'data_hasil_rad' => $body['response']['data-hasil-rad'],
                // 'header_gol_umur' => $body['response']['header-gol-umur'],
                // 'detail_gol_umur' => $body['response']['detail-gol-umur'],
                'data_pegawai' => $body['response']['data-pegawai'],
                'data_hasil' => $body['response']['data-hasil'],
            ];

            return $return;
        } catch (RequestException $e) {
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionAmbilFoto($id, $tindakan_id)
    {
        $id = DocoHelpers::decrypt($id);
        $tindakan_id = DocoHelpers::decrypt($tindakan_id);
        $request = Yii::$app->request;
        $post = $request->post();
        $post['daftartindakan_id'] = $id;
        try {
            $post['pendaftaran_id'] = DocoHelpers::decrypt($post['pendaftaran_id']);
            $response = $this->_restRad->request('POST', 'input-hasil/ambil-foto', [
                'form_params' => $post
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::response($body);
        } catch (RequestException $e) {
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionUploadHasil($id, $tindakan_id, $penunjang_id, $status)
    {
        // id = daftartindakan_id
        // tindakan_id = tindakanpelayanan_id
        
        $maxUpload = DocoConstants::MAX_UPLOAD_RAD;
        $model = new UploadHasilForm;
        $data = $this->getDataApi($penunjang_id, $id, $tindakan_id);
        
        $tindakan = !empty($data['data_hasil']) 
            ? $data['data_hasil']['daftartindakan_nama']
            : '';
        $data_pegawai = !empty($data['data_pegawai']) 
            ? ArrayHelper::map($data['data_pegawai'], 'pegawai_id', 'nama_pegawai')
            : [];
        $title = $this->_title;
        $id = DocoHelpers::decrypt($id);
        $tindakan_id = DocoHelpers::decrypt($tindakan_id);
        $penunjang_id = DocoHelpers::decrypt($penunjang_id);
        $pasienpenunjang_id = DocoHelpers::encrypt($penunjang_id);
        $status = DocoHelpers::decrypt($status);
        $hasilpemeriksaanrad_id = !empty($data['data_hasil']) 
            ? $data['data_hasil']['hasilpemeriksaanrad_id']
            : '';
        $hasilpemeriksaan_id = DocoHelpers::encrypt($hasilpemeriksaanrad_id);
        $folderName = $hasilpemeriksaan_id . '-' . $pasienpenunjang_id;
        $penanggungjawab_id = !empty($data['data_hasil_rad'][0]) 
            ? $data['data_hasil_rad'][0]['petugasrad_id']
            : '';
        $data_upload = !empty($data['data_hasil_rad']) ? $data['data_hasil_rad'] : [];

        return $this->render('upload_hasil', get_defined_vars());
    }

    public function actionSaveUpload()
    {
        @ini_set( 'upload_max_size' , '256M' );
        @ini_set( 'post_max_size', '256M');
        @ini_set( 'max_execution_time', '300' );

        $model = new UploadHasilForm;
        $request = Yii::$app->request;
        $post = $request->post();
        $model->load($post);
        $file = UploadedFile::getInstances($model, "upload_file");
        Yii::error(["a;da" => $file]);
        if (!empty($file)) {
            $data = $post['UploadHasilForm'];
            $postData = [];
            for ($i=0; $i < count($file); $i++) {
                $path = \Yii::getAlias('@webroot');
                $size = $file[$i]->size;
                $ext = end(explode(".", $file[$i]->name));

                // if ($size > DocoConstants::MAX_UPLOAD_RAD) {
                //     $result['response']['title'] = Yii::t('fe', 'Proses gagal !');
                //     $result['response']['text'] = Yii::t('fe', 'File maksimal 250 mb !');

                //     return DocoHelpers::response($result, 422);
                // }

                if(($ext == 'mp4') OR ($ext == 'MP4') OR ($ext == '3gp') OR ($ext == 'mkv')) {
                    $result['response']['title'] = Yii::t('fe', 'Proses gagal !');
                    $result['response']['text'] = Yii::t('fe', 'File harus berbentuk jpg, png dengan maksimal 250 mb');

                    return DocoHelpers::response($result, 422);
                }
                // if (($ext == 'jpg') OR ($ext == 'png') OR ($ext == 'pdf') OR ($ext == 'docx')) {
                    
                // } else {
                //     var_dump($file[$i]);die;
                //     $result['response']['title'] = Yii::t('fe', 'Proses gagal !');
                //     $result['response']['text'] = Yii::t('fe', 'File harus berbentuk jpg, png dengan maksimal 250 mb');

                //     return DocoHelpers::response($result, 422);
                // }

                $postData[] = [
                    'upload_file' => $file[$i]->name,
                    'catatan' => $data['catatan'][$i],
                    'petugasrad_id' => $data['pegawairad_id'],
                    'hasilpemeriksaanrad_id' => $data['hasilpemeriksaanrad_id']
                ];


                $hasilpemeriksaanrad_id = DocoHelpers::encrypt($data['hasilpemeriksaanrad_id']);
                $penunjang_id = DocoHelpers::encrypt($data['penunjang_id']);
                $folderName = $hasilpemeriksaanrad_id . '-' . $penunjang_id .'/';
                if (!file_exists($path.'/media/input-hasil-rad/'.$folderName)) {
                    mkdir($path.'/media/input-hasil-rad/'.$folderName, 0777, true);
                }

                $file[$i]->saveAs($path.'/media/input-hasil-rad/'. $folderName . $file[$i]->name);
                
            }
            $response = $this->_restRad->request('POST', 'input-hasil/hasil-scan', [
                'form_params' => $postData
            ]);

            $response = json_decode($response->getBody(), true);
            // return DocoHelpers::response($response);
        } else {
            $result['response']['title'] = Yii::t('fe', 'Proses gagal !');
            $result['response']['text'] = Yii::t('fe', 'Tidak ada file yang di upload');

            return DocoHelpers::response($result, 422);
        }

        return DocoHelpers::response($response, 422, 'UploadHasilForm');
    }

    public function actionDeleteUpload($id,$parent)
    {
        try {
            $response = $this->_restRad->request('DELETE', 'input-hasil/delete-upload',[
                'query' => ['id' => $id ]
            ]);
            $response = json_decode($response->getBody(),true);
            $file = isset($response['response']['data']) ? $response['response']['data'] : null;
            $path = Yii::getAlias('@webroot/media/input-hasil-rad/'.$parent);
            if (file_exists($path.'/'.$file)) {
                unlink($path.'/'.$file);
            }
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionUpdateCatatan($id)
    {
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $response = $this->_restRad->request('POST', 'input-hasil/update-catatan?id='.$id, [
                'form_params' => $post
            ]);
            $response = json_decode($response->getBody(),true);
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil di update'
            ];
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionBatal(){
        $request = Yii::$app->request;
        $pasienmasukpenunjang_id =  DocoHelpers::decrypt($request->post('pasienmasukpenunjang_id', null));
        $hasilpemeriksaanrad_id =  DocoHelpers::decrypt($request->post('hasilpemeriksaanrad_id', null));
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
        $post = [
            'pegawai_id' => $pegawai_id,
            'pasienmasukpenunjang_id' => $pasienmasukpenunjang_id,
            'hasilpemeriksaanrad_id' => $hasilpemeriksaanrad_id,
        ];
        try {
            $response = $this->_restRad->request('POST', 'input-hasil/batal', [
                'form_params' => $post
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::response($body);
        } catch (RequestException $e) {
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}