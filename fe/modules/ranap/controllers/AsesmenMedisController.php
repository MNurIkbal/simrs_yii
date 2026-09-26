<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-06-11 10:11:45
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-07-10 15:55:30
 */

namespace Doco\ranap\controllers;

use Yii;
use app\components\DocoController;
use app\components\DocoConstants;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;
use app\modules\ranap\models\AsesmenMedisForm;

class AsesmenMedisController extends DocoController
{
    protected $_title = "Asesmen Medis";
    protected $_module = 'ranap/asesmen-medis/';
    protected $_restRanap;

    public function init()
    {
        parent::init();
        $this->_restRanap = Yii::$app->docoRest->ranap;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actionIndex($pendaftaran_id){
        $model = new AsesmenMedisForm;
        $title = $this->_title;
        $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
        if(Yii::$app->request->post()){
            $data = Yii::$app->request->post();
            $data['AsesmenMedisForm']['r_penyakitdahulu'] = json_encode($data['riwayat_penyakit']);
            // untuk kebutuhan change field alergi menjadi free text - issue 1699
            // $data['AsesmenMedisForm']['r_alergiobat'] = json_encode($data['AsesmenMedisForm']['r_alergiobat']);
            $data['AsesmenMedisForm']['r_imunisasi'] = json_encode($data['AsesmenMedisForm']['r_imunisasi']);
            $data['AsesmenMedisForm']['r_penyakitkeluarga'] = ($data['AsesmenMedisForm']['r_penyakitkeluarga']);
            unset($data['riwayat_penyakit']);
            $post = $this->_restRanap->post('asesmen-medis/save-asesmen', ['form_params'=>$data]);
            $rest = json_decode($post->getBody(), true );
            return DocoHelpers::response($rest['response']);
        }
        $model->pendaftaran_id = $pendaftaran_id;
        
        $response = $this->_restRanap->post('allow/list-pack-asesmen', ['form_params'=>['pendaftaran_id'=>$pendaftaran_id]]);
        $body = json_decode($response->getBody(), true);
        $body = $body['response'];

        // Inisialisasi temp
        $tempRiwayatPenyakitKeluarga = [];
        $tempRiwayatImunisasi = [];
        $riwayatPenyakitKeluarga = [];
        $riwayatImunisasi = [];

        // Cek data
        if (!empty($body['data_riwayat'])) {
            // Loop
            foreach ($body['data_riwayat'] as $value) {
                // Cek data
                if (!empty($value['r_penyakitkeluarga'])) {
                    // Assign riwayat
                    $tempRiwayatPenyakitKeluarga[] = json_decode($value['r_penyakitkeluarga']);
                }

                // Cek data
                if (!empty($value['r_imunisasi'])) {
                    // Assign riwayat
                    $tempRiwayatImunisasi[] = json_decode($value['r_imunisasi']);
                }
            }
        }

        // Cek temp
        if (!empty($tempRiwayatPenyakitKeluarga)) {
            // Loop
            foreach ($tempRiwayatPenyakitKeluarga as $value) {
                // Cek data
                if (!empty($value)) {
                    // Looping
                    foreach ($value as $value) {
                        // Assign riwayat
                        $riwayatPenyakitKeluarga[] = $value;
                    }
                }
            }
        }

        // Cek temp
        if (!empty($tempRiwayatImunisasi)) {
            // Loop
            foreach ($tempRiwayatImunisasi as $value) {
                // Cek data
                if (!empty($value)) {
                    // Looping
                    foreach ($value as $value) {
                        // Assign riwayat
                        $riwayatImunisasi[] = $value;
                    }
                }
            }
        }

        // Cek asesmen
        if(count($body['data_asesmen']['asesmen']) > 0){
            unset($body['data_asesmen']['asesmen']['additional_data']);
            unset($body['data_asesmen']['asesmen']['created_date']);
            unset($body['data_asesmen']['asesmen']['created_by']);
            unset($body['data_asesmen']['asesmen']['modified_count']);
            unset($body['data_asesmen']['asesmen']['last_modified_date']);
            unset($body['data_asesmen']['asesmen']['last_modified_by']);
            unset($body['data_asesmen']['asesmen']['is_deleted']);
            unset($body['data_asesmen']['asesmen']['is_active']);
            unset($body['data_asesmen']['asesmen']['deleted_by']);
            unset($body['data_asesmen']['asesmen']['deleted_date']);
            $body['data_asesmen']['asesmen']['r_penyakitkeluarga'] = $riwayatPenyakitKeluarga;
            $body['data_asesmen']['asesmen']['r_imunisasi'] = $riwayatImunisasi;
            $body['data_asesmen']['asesmen']['r_penyakitdahulu'] = json_decode($body['data_asesmen']['asesmen']['r_penyakitdahulu']);
            // untuk kebutuhan change field alergi menjadi free text - issue 1699
            // $body['data_asesmen']['asesmen']['r_alergiobat'] = json_decode($body['data_asesmen']['asesmen']['r_alergiobat']);
            $body['data_asesmen']['asesmen']['tgl_asesmenmedis'] = date('d M, Y', strtotime($body['data_asesmen']['asesmen']['tgl_asesmenmedis']));
            $model->attributes = $body['data_asesmen']['asesmen'];
        }
        
        //define data
        $tahun = [];
        $start_year = date('Y', strtotime('-10 year'));
        for ( $start_year; $start_year <= date('Y') ; $start_year++) { 
            $tahun[] =  ['tahun'=>$start_year];
        }
        $data_kunjungan = isset($body['data_kunjungan']) ? $body['data_kunjungan'] : '';
        $data_kontak = [ '1'=>Yii::t('fe','Adekuat'), '0'=>Yii::t('fe','Tidak adekuat')];
        $data_discharge = [ '0'=>Yii::t('fe','Tidak'),  '1'=>Yii::t('fe','Ya').'('.Yii::t('fe','Lanjut ke halaman discharge planning').')'];
        $data_isMerokok = [ '1'=>Yii::t('fe','Ya'), '0'=>Yii::t('fe','Tidak')];
        $data_isTerintubasi = [ '1'=>Yii::t('fe','Terintubasi'), '0'=>Yii::t('fe','Tidak terintubasi')];
        $data_kakududuk = [ '1'=>Yii::t('fe','Ya'), '0'=>Yii::t('fe','Tidak')];
        $data_sumberInfo = ['1'=>'Pasien', '0'=>'Orang lain, Hubungan dengan pasien '];
        
        $data_diagnosa = ( isset($body['data_diagnosa']) && !empty($body['data_diagnosa']) ) ? $body['data_diagnosa'] : [];
        $data_imunisasi = ( isset($body['data_imunisasi']) && !empty($body['data_imunisasi']) ) ? $body['data_imunisasi'] : [];
        $data_obatalkes = ( isset($body['data_obatalkes']) && !empty($body['data_obatalkes']) ) ? $body['data_obatalkes'] : [];
        $data_gcs = ( isset($body['data-gcs']) && !empty($body['data-gcs']) ) ? $body['data-gcs'] : [];
        $data_metodegcs = ( isset($body['data-metodegcs']) && !empty($body['data-metodegcs']) ) ? $body['data-metodegcs'] : [];
        $data_listgcs = ( isset($body['data-listgcs']) && !empty($body['data-listgcs']) ) ? $body['data-listgcs'] : [];
        $data_bmi = ( isset($body['data-bmi']) && !empty($body['data-bmi']) ) ? $body['data-bmi'] : [];
        $data_tekanandarah = ( isset($body['data-tekanandarah']) && !empty($body['data-tekanandarah']) ) ? $body['data-tekanandarah'] : [];
        $data_bagiantubuh = ( isset($body['data-bagiantubuh']) && !empty($body['data-bagiantubuh']) ) ? $body['data-bagiantubuh'] : [];
        $data_detailbagiantubuh = ( isset($body['data-detailbagiantubuh']) && !empty($body['data-detailbagiantubuh']) ) ? $body['data-detailbagiantubuh'] : [];
        $data_denyutJantung = ( isset($body['data_denyutjantung']) && !empty($body['data_denyutjantung']) ) ? $body['data_denyutjantung'] : [];
        $data_gcsEye = ( isset($data_listgcs['eye']) && !empty($data_listgcs['eye']) ) ? $data_listgcs['eye'] : [];
        $data_gcsVerbal = ( isset($data_listgcs['verbal']) && !empty($data_listgcs['verbal']) ) ? $data_listgcs['verbal'] : [];
        $data_gcsMotorik = ( isset($data_listgcs['motorik']) && !empty($data_listgcs['motorik']) ) ? $data_listgcs['motorik'] : [];
        $gcsEyeOptions = [];
        $gcsVerbalOptions = [];
        $gcsMotorikOptions = [];
        if(count($data_gcsEye) > 0){
            foreach ($data_gcsEye as $keyEye => $valueEye) {
                $gcsEyeOptions[$valueEye['metodegcs_id']]['data-nilai'] = $valueEye['metodegcs_nilai'];
            }
        }
        if(count($data_gcsVerbal) > 0){
            foreach ($data_gcsVerbal as $keyVerbal => $valueVerbal) {
                $gcsVerbalOptions[$valueVerbal['metodegcs_id']]['data-nilai'] = $valueVerbal['metodegcs_nilai'];
            }
        }
        if(count($data_gcsMotorik) > 0){
            foreach ($data_gcsMotorik as $keyMotorik => $valueMotorik) {
                $gcsMotorikOptions[$valueMotorik['metodegcs_id']]['data-nilai'] = $valueMotorik['metodegcs_nilai'];
            }
        }
        $data_metodAsesmen = ( isset($body['data_asesmennyeri']) && !empty($body['data_asesmennyeri']) ) ? $body['data_asesmennyeri'] : [];
        $data_anatomiPasien = isset($body['data_asesmen']['anatomi']) ? $body['data_asesmen']['anatomi'] : [];
        $jsonAnatomi = json_encode($data_anatomiPasien,JSON_FORCE_OBJECT);
        $counter = count($data_anatomiPasien) + 1;
        $model->dokter_id = isset($data_kunjungan['pegawai_id']) ? $data_kunjungan['pegawai_id'] : '';
        $dokterNama = isset($data_kunjungan['nama_pegawai']) ? $data_kunjungan['nama_pegawai'] : '';
        $urlCetak = '';
        return $this->render('index', get_defined_vars());
    }

    public function actionGetHasilTd($pendaftaran_id, $nilai = '0/0', $golongan_umur)
    {
        $request = Yii::$app->request;
        $data_td = DocoConstants::TD_HASIL;
        if(count($data_td) < 1){
            $response = $this->_restRanap->get('allow/get-data-td');
            $body = json_decode($response->getBody(), true);
            foreach ($body['response'] as $key => $value) {
                $data_td[ $value['golonganumur_id'] ][] = $value; 
            }
        }
        $nilai = explode('/', $nilai);
        $nilai_systolic = $nilai[0];
        $nilai_diastolic = $nilai[1];
        $hasil = '';

        foreach ($data_td[$golongan_umur] as $key => $value) {
            if( ( ($nilai_systolic >= $value['systolic_min'] && $nilai_systolic <= $value['systolic_max']) && ($nilai_diastolic >= $value['diastolic_min'] && $nilai_diastolic <= $value['diastolic_max']) ) || ($nilai_systolic >= $value['systolic_min'] && $nilai_systolic <= $value['systolic_max']) ) {
                $hasil = $value['sysdia_nama']; break;
            }
        }
        

        $result = ['hasil'=>$hasil];
        return DocoHelpers::response($result);
    }

    public function actionSaveAnatomi()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        try {
            $data = $request->post('data');
            $pendaftaran_id = $request->post('pendaftaran_id');
            $pasien_id = $request->post('pasien_id');
            $pemeriksaanfisik_id = $request->post('pemeriksaanfisik_id');

            $send_data = [
                'data' => $data,
                'pendaftaran_id' => $pendaftaran_id,
                'pasien_id' => $pasien_id,
                'pemeriksaanfisik_id' => $pemeriksaanfisik_id,
            ];

            $response = $this->_restRanap->post('asesmen-medis/save-periksatubuh', [
                'form_params' => $send_data
            ]);
            $response = json_decode($response->getBody(), true);

            return [
                'status' => 200,
                'message' => $response,
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function actionGetDiagnosa()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            $response = $this->_restRanap->request('POST', 'allow/data-diagnosa',[
                            'form_params'=>['term'=>$_GET['q']['term']],
                        ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = ['id'=>$value['diagnosa_id'],'text'=>$value['diagnosa_nama']];
            }
            $total = count($body['response']);
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
            return DocoHelpers::response($return);
        }
    }
    public function actionCetakAsesmen($id)
    {
        $id = DocoHelpers::decrypt($id);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/cetak-asesmen.pdf";
        try {
            $response = $this->_restRanap->get('asesmen-medis/cetak-asesmen',[
                'save_to' => $path,
                'query' => [
                        'id'=>$id,
                    ],
            ]);
            $body = json_decode($response->getBody(), true);

            return DocoHelpers::downloadPdf($response,$path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    // Hapus asesmen
    public function actionHapusAsesmenMedis($id)
    {
        // Try catch
        try {
            // Check post
            if ($id != '') {
                // Send to backend
                $request = $this->_restRanap->delete('asesmen-medis/delete?id='.$id);
                $response = json_decode($request->getBody(), true);

                // Response
                return DocoHelpers::response($response['response']);
            }
        } catch (\Exception $e) {
            // Exception
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch (RequestException $e) {
            // Exception
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }
}
