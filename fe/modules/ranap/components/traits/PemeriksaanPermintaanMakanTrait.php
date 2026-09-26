<?php

/**
 * @Author: Ardi Pratama
 */

// Namespace
namespace app\modules\ranap\components\traits;

// Using Yii
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;

// Using Guzzles
use function GuzzleHttp\json_encode;
use GuzzleHttp\Exception\RequestException;

// Using components
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

// Using model
use app\modules\ranap\models\PermintaanMakanForm;

// Trait
trait PemeriksaanPermintaanMakanTrait 
{
    public function actionMintaMakan()
    {
        $result = $this->_restRanap->get('tra-minta-makan/bundle-data-permintaan-makan',[]);
        $result = json_decode($result->getBody(),true);
        $dataWaktuDiet = $dataJenisDiet = $dataKeterangan = [];
        if(isset($result['response']['datamaster']['waktu_diet'])){
            $dataWaktuDiet = $result['response']['datamaster']['waktu_diet'];
        }
        if(isset($result['response']['datamaster']['jenis_diet'])){
            $dataJenisDiet = $result['response']['datamaster']['jenis_diet'];
        }
        if(isset($result['response']['datamaster']['keterangan'])){
            $dataKeterangan = $result['response']['datamaster']['keterangan'];
        }
        $dropdownWaktuDiet= [];
        $dropdownWaktuDiet[] = [
            'id' => '-1',
            'text' => 'Pilih Waktu Diet'
        ];
        $dropdownKeterangan= [];
        $dropdownKeterangan[] = [
            'id' => '-1',
            'text' => 'Pilih Keterangan'
        ];
        $modelMakan = new PermintaanMakanForm;
        if(is_array($dataWaktuDiet) && count($dataWaktuDiet)>0){
            foreach ($dataWaktuDiet as $val_waktu_diet) {
                $dropdownWaktuDiet[] = [
                    'id' => $val_waktu_diet['lookup_id'],
                    'text' => $val_waktu_diet['lookup_name']
                ];
            }
        }
        if(is_array($dataKeterangan) && count($dataKeterangan)>0){
            foreach ($dataKeterangan as $val_keterangan) {
                $dropdownKeterangan[] = [
                    'id' => $val_keterangan['lookup_name'],
                    'text' => $val_keterangan['lookup_name']
                ];
            }
        }
        $dropdownWaktuDiet = json_encode($dropdownWaktuDiet);
        $disabled = (!empty($this->_data_pasien['pasienpulang_id']) || $this->_data_pasien['is_stopakomodasi'] == true) ? true : false;
        return $this->renderAjax('permintaan-makan/index', [
            'modelMakan' => $modelMakan,
            'dropdownWaktuDiet' => $dropdownWaktuDiet,
            'jenisDiet' => json_encode($dataJenisDiet),
            'disabled' => $disabled,
            'dataKeterangan' => json_encode($dropdownKeterangan),
        ]);
    }

    public function actionCariJenisDiet()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restRanap->get('tra-minta-makan/cari-jenis-diet',[
                'query' => [
                    'term' => $request->get('term')
                ]
            ]);

            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $response[] = [
                    'id' => $value['jenisdiet_id'],
                    'text' => $value['jenisdiet_kode'].' - '.$value['jenisdiet_nama'],
                ];
            }
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['message'] = $e->getMessage();
        }
        
        return DocoHelpers::response([
            'result' => $response
        ]);
    }

    public function actionCariMenuDietByJenis()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = [];
        $result = [];
        $result['output'] = [];
        $result['selected'] = '';
        try {
            $request = Yii::$app->request;
            $parents = $request->post('depdrop_parents',[]);
            $result = $this->_restRanap->get('tra-minta-makan/cari-menu-diet-by-jenis',[
                'query' => [
                    'term' => $request->get('term'),
                    'jenis_id' => @$parents[0]
                ]
            ]);

            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $result['output'][] = [
                    'id' => $value['makanandiet_id'],
                    'name' => $value['makanandiet_nama'],
                ];
            }

            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            $result['output'] = [];
            $result['selected'] = '';
            return $result;
        }
    }

    public function actionSimpanMintaMakan()
    {
        $request = Yii::$app->request;
        if(Yii::$app->request->post()) {
            $pendaftaran_id = DocoHelpers::decrypt($request->get('id','MQ'));
            $post = $request->post();
            
            $data_minta_makan = $request->post('PermintaanMakanForm',[]);
            $detailMintaMakan = [];
            if(is_array($data_minta_makan) && count($data_minta_makan)>0){
                foreach ($data_minta_makan as $val_minta_makan) {
                    $modelDetailMakan = new PermintaanMakanForm;
                    $modelDetailMakan->attributes = $val_minta_makan;
                    if(!$modelDetailMakan->validate()){
                        return DocoHelpers::response($modelDetailMakan->errors,422,'PermintaanMakanForm');
                    }

                    $detailMintaMakan[] = $val_minta_makan;
                }
                try{
                    $result = $this->_restRanap->post('tra-minta-makan/simpan-permintaan-makan',[
                        'form_params'=>[
                            'pendaftaran_id' => $pendaftaran_id,
                            'pasienadmisi_id' => $this->_pasienadmisi_id,
                            'pegawai_pemesan' => Yii::$app->session->get('user_identity')['id_pegawai'],
                            'detailMintaMakan' => $detailMintaMakan
                        ]
                    ]);
                    $result = json_decode($result->getBody(),TRUE);
                    return DocoHelpers::response($result, false);
                }catch(RequestException $e){
                    throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
                }
            }
            
        }
    }

    public function actionCetakPermintaanMakan()
    {
        // Path
        $path = Yii::getAlias("@download") . "/permintaan-makan-pasien.pdf";
        $params = Yii::$app->request;
        $pendaftaran_id = DocoHelpers::decrypt($params->get('id','MQ'));
        $no_permintaanmakan = $params->get('no_permintaanmakan',0);
        $nama_usercetak = Yii::$app->session->get('user_identity')['nama'];
        $id_usercetak = Yii::$app->session->get('user_identity')['id_pegawai'];
        // Try catch
        try {
            // Request
            $request = $this->_restGizi->get('transaksi-permintaan-makan/cetak-permintaan-makan', [
                'query' => [
                    'pendaftaran_id' => $pendaftaran_id,
                    'no_permintaanmakan' => $no_permintaanmakan,
                    'nama_usercetak' => $nama_usercetak,
                    'id_usercetak' => $id_usercetak
                ],
                'save_to' => $path,
            ]);
            // Download pdf
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            var_dump(json_decode($e->getResponse()->getBody(),TRUE));exit;
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}