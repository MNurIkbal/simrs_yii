<?php


namespace Doco\pendaftaran\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use app\components\Pelayanan\PelayananHelpers;
use GuzzleHttp\Exception\RequestException;
use app\modules\pendaftaran\models\BpjsNewForm;

class EndPointController extends DocoController
{
    protected $_restPendaftaran;
    /**
     * @inheritdoc
     */

    public function init()
    {
        parent::init();
        $this->_restPendaftaran=Yii::$app->docoRest->pendaftaran;
    }
    public function beforeAction($action)
    {
        return true;
    }

    /**@change Perubahan default params isAps */
    public function actionNorm($q = null, $isRanap=false, $isAps = true)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        if ($isRanap) {
            $response = $this->_restPendaftaran->post('allow/get-pasien-ranap', [
                'form_params' => [
                    'keyword'=>$q,
                    'bblPasien' => Yii::$app->request->get('isBbl')
                ]
            ]);
        } else {
            $response = $this->_restPendaftaran->post('allow/get-pasien-rajal', [
                'form_params' => ['keyword'=>$q , 'is_aps'=>$isAps]
            ]);
        }

        $response = json_decode($response->getBody(), TRUE);
        $results = [];

        if ($response['metadata']['status'] == 200) {
            $list = $response['response'];
            $tmpRm = [];

            foreach ($list as $key => $each) {
                $tglLahir = date('d-m-Y', strtotime($each['tanggal_lahir']));
                $no_and_nama = $each['no_rekam_medik'] . " / " . $each['nama_pasien'] . " / " . $tglLahir;
                $tmpRm = $each['no_rekam_medik'];
                $eachData = [
                    'id' => $each['no_rekam_medik'],
                    'text' => $no_and_nama,
                    'pendaftaran_id' => $isRanap ? $each['pendaftaran_id'] : null,
                    'no_rekam_medik' => $each['no_rekam_medik'],
                    'nama_pasien' => $each['nama_pasien'],
                    'pasien_id' => $each['pasien_id'],
                ];
                if ($isRanap) {
                    $eachData['jeniskasuspenyakit_id'] = isset($each['jeniskasuspenyakit_id']) ? $each['jeniskasuspenyakit_id'] : '';
                    $eachData['kelaspelayanan_id'] = isset($each['kelaspelayanan_id']) ? $each['kelaspelayanan_id'] : '';
                }

                $results[] = $eachData;
            }
            if($isRanap && Yii::$app->request->get('isBbl') == true) {
                $tmpRes = $results;
                $results = $tmpArr = [];

                foreach($tmpRes as $k => $v) {
                    if(!isset($tmpArr[$v['id']])) {
                        $tmpArr[$v['id']] = $v;
                    }
                }

                foreach($tmpArr as $k => $v) {
                    $results[] = $v;
                }
            }
        }
        return ['results' => $results];
    }

    public function actionNormIbu($q = null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $response = $this->_restPendaftaran->post('allow/get-pasien-bayi', [
            'form_params' => ['keyword'=>$q]
        ]);

        $response = json_decode($response->getBody(), TRUE);

        $results = [];
        if ($response['metadata']['status'] == 200) {
            $list = $response['response'];
            foreach ($list as $key => $each) {
                $tglLahir = date('d-m-Y', strtotime($each['tanggal_lahir']));
                $no_and_nama = $each['no_rekam_medik'] . " / " . $each['nama_pasien'];
                $results[] = [
                    'id'=>$each['pendaftaran_id'],
                    'text'=>$no_and_nama,
                    // 'pendaftaran_id'=>$each['pendaftaranbaru_id'],
                ];
            }
        }

        return ['results' => $results];
    }

    public function actionGetBayi($pendaftaran_id)
    {
        $request = Yii::$app->request;
        $title = Yii::t('fe', 'Daftar Bayi Baru Lahir');

        return $this->renderAjax('_list_bayi_lahir', get_defined_vars());
    }

    public function actionPilihTempatTidur()
    {
        $request = Yii::$app->request;

        $title = Yii::t('fe', 'Pilih Tempat Tidur');
        $ruangan_id = $request->get('ruangan_id');
        $jenis_id = $request->get('jenis_id');
        $kelas_id = $request->get('kelas_id');

        $masterWarnaTempatTidur = $this->_restPendaftaran->get(
                'allow-antrian/get-warna-tempat-tidur',[
                    'query'=>[]
                ]);
        $body = json_decode($masterWarnaTempatTidur->getBody(),TRUE);
        $getWarnaTempatTidur = $body['response'];

        return $this->renderAjax('_pemilihan_tempat_tidur', get_defined_vars());
    }

    public function actionPilihTempatTidurBayi()
    {
        $request = Yii::$app->request;
        $title = Yii::t('fe', 'Pilih Tempat Tidur');
        $ruangan_id = $request->get('ruangan_id');
        $jenis_id = $request->get('jenis_id');
        $kelas_id = $request->get('kelas_id');

        $masterWarnaTempatTidur = $this->_restPendaftaran->get(
                'allow-antrian/get-warna-tempat-tidur',[
                    'query'=>[]
                ]);
        $body = json_decode($masterWarnaTempatTidur->getBody(),TRUE);
        $getWarnaTempatTidur = $body['response'];

        return $this->renderAjax('_pemilihan_tempat_tidur_bayi', get_defined_vars());
    }

    public function actionGetDataBayi($pendaftaran_id)
    {
         Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $draw = $request->get('draw',1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restPendaftaran->get('allow/get-data-bayi?pendaftaran_id='.$pendaftaran_id,['form_params'=>[],
            ]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['no_rekam_medik'] = $value['no_rekam_medik'].' / '.$value['nama_pasien'];
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

    public function actionGetDataKamar()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $jenis_id = $request->get('jenis_id');
        $kelas_id = $request->get('kelas_id');
        $ruangan_id = $request->get('ruangan_id');
        $draw = $request->get('draw',1);
        $data = [];
        try {
            $response = $this->_restPendaftaran->get('pemesanan-kamar/get-data-kamar',['form_params'=>[],
                'query' => ['jeniskasuspenyakit_id'=>$jenis_id,'kelaspelayanan_id'=>$kelas_id,'ruangan_id'=>$ruangan_id]
            ]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            $result['data'] = $this->listRuangan($body['response']['list-ruangan'],$body['response']['data']);
            $result['recordsTotal'] = '';
            $result['recordsFiltered'] = '';

            return $result;

        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDataKamarBayi()
    {
         Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $jenis_id = $request->get('jenis_id');
        $kelas_id = $request->get('kelas_id');
        $ruangan_id = $request->get('ruangan_id');
        $draw = $request->get('draw',1);
        $data = [];
        try {
            $response = $this->_restPendaftaran->get('pemesanan-kamar/get-data-kamar',['form_params'=>[],
                'query' => ['jeniskasuspenyakit_id'=>$jenis_id,'kelaspelayanan_id'=>$kelas_id,'ruangan_id'=>$ruangan_id]
            ]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            $result['data'] = $this->listRuanganBayi($body['response']['list-ruangan'],$body['response']['data']);
            $result['recordsTotal'] = '';
            $result['recordsFiltered'] = '';

            return $result;

        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function listRuangan($index,$data)
    {
        function listKamar($id,$no_kamar,$data){
            $buttonList = [];
            $i = 1;
            foreach ($data as $key => $value) {
                if($value['ruangan_id'] == $id && $value['kamarruangan_nokamar'] == $no_kamar){
                    $attributes = [
                        'style' => "margin-bottom:8px;background-color:{$value['kode_warna']}",
                        'data-kettempattidur_id' => $value['kettempattidur_id'],
                        'data-kamartempattidur_id' => $value['kamartempattidur_id'],
                        'data-kamarruangan_jenis' => $value['kamarruangan_jenis'],
                        'data-kamarruangan_id' => $value['kamarruangan_id'],
                        'data-no_tempattidur' => $value['no_tempattidur'],
                        'data-kamarruangan_nokamar' => $value['kamarruangan_nokamar'],
                        'class' => 'pilih-kamar btn btn-danger-custom btn-xs',
                        'onClick' =>'pilihKamar(this)'
                    ];
                    if (($value['kettempattidur_id'] == DocoConstants::ISI_PRMPN) OR ($value['kettempattidur_id'] == DocoConstants::ISI_LAKI) OR ($value['kettempattidur_id'] == DocoConstants::DIPESAN)) {
                        $attributes['disabled'] = 'disabled';
                    }
                    // $buttonLabel = $value['kamarruangan_nokamar'] . ' - ' . $value['no_tempattidur'];
                    $buttonLabel = $value['no_tempattidur'];
                    $buttonList[$value['no_tempattidur'].$value['kamartempattidur_id']] = Html::buttonInput($buttonLabel, $attributes);
                }
                $i++;
            }
            if(count($buttonList) == 0) {
                $buttonList[] = "<p>--Tempat Tidur Tidak Tersedia--</p>";
            }else{
                ksort($buttonList);
            }

            return implode(' ', $buttonList);
        }

        $data_kamar = [];
        $no = 1;
        foreach ($index as $key => $value) {
            $value['rowNum'] = $no;
            $value['datakamar'] = listKamar($value['ruangan_id'],$value['kamarruangan_nokamar'],$data);
            $data_kamar[$key] = $value;
            $no++;
        }
        return $data_kamar;
    }

    public function listRuanganBayi($index,$data)
    {
        function listKamar($id,$no_kamar,$data){
            $buttonList = [];
            $i = 1;
            foreach ($data as $key => $value) {
                if($value['ruangan_id'] == $id && $value['kamarruangan_nokamar'] == $no_kamar){
                    $attributes = [
                        'style' => "margin-bottom:8px;background-color:{$value['kode_warna']}",
                        'data-kettempattidur_id' => $value['kettempattidur_id'],
                        'data-kamartempattidur_id' => $value['kamartempattidur_id'],
                        'data-kamarruangan_jenis' => $value['kamarruangan_jenis'],
                        'data-kamarruangan_id' => $value['kamarruangan_id'],
                        'data-no_tempattidur' => $value['no_tempattidur'],
                        'data-kamarruangan_nokamar' => $value['kamarruangan_nokamar'],
                        'class' => 'pilih-kamar btn btn-danger-custom btn-xs',
                    ];
                    if (($value['kettempattidur_id'] == DocoConstants::ISI_PRMPN) OR ($value['kettempattidur_id'] == DocoConstants::ISI_LAKI) OR ($value['kettempattidur_id'] == DocoConstants::DIPESAN)) {
                        $attributes['disabled'] = 'disabled';
                    }
                    // $buttonLabel = $value['kamarruangan_nokamar'] . ' - ' . $value['no_tempattidur'];
                    $buttonLabel = $value['no_tempattidur'];
                    $buttonList[$value['no_tempattidur'].$value['kamartempattidur_id']] = Html::buttonInput($buttonLabel, $attributes);
                }
                $i++;
            }
            if(count($buttonList) == 0) {
                $buttonList[] = "<p>--Tempat Tidur Tidak Tersedia--</p>";
            }else{
                ksort($buttonList);
            }

            return implode(' ', $buttonList);
        }

        $data_kamar = [];
        $no = 1;
        foreach ($index as $key => $value) {
            $value['rowNum'] = $no;
            $value['datakamar'] = listKamar($value['ruangan_id'],$value['kamarruangan_nokamar'],$data);
            $data_kamar[$key] = $value;
            $no++;
        }
        return $data_kamar;
    }

    /**
    * @author Ardi Pratama
    * @since
    * @param
    * @return
    * @desc
    */
    public function actionListPenjamin() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $post = $request->post();
        $carabayar_id = $post['depdrop_parents'][0];

        $penjaminRequest = $this->_restPendaftaran->get('allow/list-penjamin?carabayar_id='.$carabayar_id);
        $body = json_decode($penjaminRequest->getBody(),TRUE);
        $responses = $body['response'];

        $out = [];
        foreach($responses as $key => $response) {
            $out[] = [
                'id' => $key,
                'name' => $response
            ];
        }

        return ['output'=>$out, 'selected'=>''];
        echo json_encode(['output'=>$out, 'selected'=>'']);
        return;
    }

    /*
    * author: Rizqi Febian
    * date: 25-04-2018
    * desc: get rujukan dari by asalrujukan_id
    * params needed: asalrujukan_idm
    */
    public function actionGetRujukanDari(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restPendaftaran->get('allow/get-rujukan-dari?id='.$parent_label);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $key => $value)
                $result['output'][] = [
                    'id' => $key,
                    'name' => $value
                ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return$result;
        }
    }

    public function actionCekKamarFleksibel($kamarruangan_id)
    {
        try {
            $response = $this->_restPendaftaran->request('GET', 'pemesanan-kamar/cek-kamar-fleksibel?kamarruangan_id=' . $kamarruangan_id);
            $body = json_decode($response->getBody(),TRUE);

            return DocoHelpers::response($body);
        } catch (RequestException $e) {
            // dump($e);exit;
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionCekKamarFleksibelBayi($kamarruangan_id)
    {
        try {
            $response = $this->_restPendaftaran->request('GET', 'pemesanan-kamar/cek-kamar-fleksibel?kamarruangan_id=' . $kamarruangan_id);
            $body = json_decode($response->getBody(),TRUE);

            return DocoHelpers::response($body);
        } catch (RequestException $e) {
            // dump($e);exit;
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionGetDataAsuransi()
    {
        try {
            $payload = Yii::$app->request->get();
            $groupedByNoAsuransi = isset($payload['groupedByNoAsuransi']) && !empty($payload['groupedByNoAsuransi']) && $payload['groupedByNoAsuransi'];
            $response = $this->_restPendaftaran->get('allow/get-data-asuransi', [
                'query'=>[
                    'pasien_id' => isset($payload['pasien_id']) ? $payload['pasien_id'] : '',
                    'no_asuransi' => $payload['no_asuransi'],
                    'groupedByNoAsuransi' => $groupedByNoAsuransi,
                    'onlyOneRecord' => isset($payload['onlyOneRecord']) && !empty($payload['onlyOneRecord']) && $payload['onlyOneRecord'],
                    'penjamin_id' => $payload['penjamin_id']
                ]
            ]);
            $body = json_decode($response->getBody(),TRUE);
            if ($groupedByNoAsuransi) {
                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                \Yii::$app->response->statusCode = 200;
                return $body['response'];
            } else {
                return DocoHelpers::response($body);
            }
        } catch (RequestException $e) {
            // dump($e);exit;
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionListKabupaten($selected = null) {
        $request = Yii::$app->request;
        $post = $request->post();
        if($post['depdrop_parents'][0] && $post['depdrop_parents'][0] != 'Loading ...') {
            $propinsi = $post['depdrop_parents'][0];
            $jenis = $no_identitas = '';
            if (isset($post['depdrop_parents'][1]) && isset($post['depdrop_parents'][2])) {
                $jenis = $post['depdrop_parents'][1];
                $no_identitas = substr($post['depdrop_parents'][2], 2, 2);
            }

            $PropinsiRequest = $this->_restPendaftaran->get('allow/list-kabupaten-new?propinsi='.$propinsi);
            $body = json_decode($PropinsiRequest->getBody(),TRUE);
            $ddlKabupaten = $body['response'];

            $out = [];
            $selected = null;
            foreach($ddlKabupaten as $kab => $value) {
                $out[] = [
                    'id' => $value['kabupaten_id'],
                    'name' => $value['kabupaten_nama'],
                ];
                if ($jenis && $jenis == DocoConstants::JENIS_KTP && $no_identitas && $no_identitas == $value['kode_kabupaten']) {
                    $selected = $value['kabupaten_id'];
                }
            }

            echo json_encode(['output'=>$out, 'selected'=>$selected]);
            return;
        }
        echo json_encode(['output'=>'', 'selected'=>'']);
    }

    public function actionListKecamatan() {
        $request = Yii::$app->request;
        $post = $request->post();
        if($post['depdrop_parents'][0] && $post['depdrop_parents'][0] != 'Loading ...') {
            $kabupaten = $post['depdrop_parents'][0];

            $jenis = $no_identitas = '';
            if (isset($post['depdrop_parents'][1]) && isset($post['depdrop_parents'][2])) {
                $jenis = $post['depdrop_parents'][1];
                $no_identitas = substr($post['depdrop_parents'][2], 4, 2);
            }

            $KecamatanRequest = $this->_restPendaftaran->get('allow/list-kecamatan-new?kabupaten='.$kabupaten);
            $body = json_decode($KecamatanRequest->getBody(),TRUE);
            $ddlKecamatan = $body['response'];

            $out = [];
            $selected = null;
            foreach($ddlKecamatan as $key => $value) {
                $out[] = [
                    'id' => $value['kecamatan_id'],
                    'name' => $value['kecamatan_nama']
                ];
                if ($jenis && $jenis == DocoConstants::JENIS_KTP && $no_identitas && $no_identitas == $value['kode_kecamatan']) {
                    $selected = $value['kecamatan_id'];
                }
            }

            echo json_encode(['output'=>$out, 'selected'=>$selected]);
            return;
        }
        echo json_encode(['output'=>'', 'selected'=>'']);
    }

    public function actionListKelurahan() {
        $request = Yii::$app->request;
        $post = $request->post();
        if(!empty($post['depdrop_parents'][0])) {

            $kecamatan = (int)$post['depdrop_parents'][0];

            $KelurahanRequest = $this->_restPendaftaran->get('allow/list-kelurahan-new?kecamatan='.$kecamatan);
            $body = json_decode($KelurahanRequest->getBody(),TRUE);
            $ddlKelurahan = $body['response'];

            $out = [];
            foreach($ddlKelurahan as $key => $value) {
                $out[] = [
                    'id' => $value['kelurahan_id'],
                    'name' => $value['kelurahan_nama']
                ];
            }

            // echo json_encode();
            echo json_encode(['output'=>$out, 'selected'=>'']);
            return;
        }
        echo json_encode(['output'=>'', 'selected'=>'']);
    }

    public function actionPrintSep()
    {
        $session = Yii::$app->session;
        $request = Yii::$app->request;
        $idRuanganWorkspace = $request->get('ruangan_id') != null ? $request->get('ruangan_id') : $session->get('active_workspace')['ruangan_id'];
        $path = Yii::getAlias("@download") . "/print-sep.pdf";

        try {
            $pendaftaran_id = $request->get('pendaftaran_id', null);
            $nosep = $request->get('nosep', null);
            $jenis_pendaftaran = $request->get('jenis', null);

            if (!is_numeric($pendaftaran_id)) {
                $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
            }

            $nosep = $request->get('nosep', null);

            if (is_null($nosep) || empty($nosep)) {
                $filterQuery = [
                    'pendaftaran_id' => $pendaftaran_id
                ];
            } else {
                if(!is_numeric($nosep)) {
                    $nosep = DocoHelpers::decrypt($nosep);
                }
                $filterQuery = [
                    'pendaftaran_id' => $pendaftaran_id,
                    'nosep' => $nosep
                ];
            }

            if ($jenis_pendaftaran != null) {
                $filterQuery['jenis_pendaftaran'] = $jenis_pendaftaran;
            }

            $filterQuery['ruangan_id'] = $idRuanganWorkspace;

            $response = $this->_restPendaftaran->get('allow-bpjs/print-sep', [
                'query' => $filterQuery,
                'save_to' => $path
            ]);

            $body = json_decode($response->getBody(), true);
            
            return DocoHelpers::previewPdf($path, $response);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionCreateSepNew()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $bpjs = new BpjsNewForm();
        $postBpjs = $post['BpjsNewForm'];
        $postBpjs['no_kartu'] = $postBpjs['no_asuransi'];
        $bpjs->attributes = $postBpjs;
        $bpjs->pasienadmisi_id = DocoHelpers::decrypt($bpjs->pasienadmisi_id);

        $bpjs->jenis_pelayanan = isset($postBpjs['jenis_pelayanan']) ? $postBpjs['jenis_pelayanan'] : 1;
        $bpjs->poli_tujuan = !empty($postBpjs['poli_tujuan']) ? $postBpjs['poli_tujuan'] : 'IGD';
        $bpjs->poli_eksekutif = isset($postBpjs['poli_eksekutif']) ? $postBpjs['poli_eksekutif'] : 0;
        $bpjs->no_kartu = $postBpjs['no_asuransi'];
        $bpjs->user = Yii::$app->docoVars->user("nama");
        if (isset($post['is_skdp'])) {
            if ($bpjs->kasus_kecelakaan) {
                if ($bpjs->status_suplesi == '1') {
                    $bpjs->scenario = 'skdpsuplesi';
                } else {
                    $bpjs->scenario = 'skdpkll';
                }
            } else {
                $bpjs->scenario = 'skdp';
            }
        } else {
            if ($bpjs->kasus_kecelakaan) {
                if ($bpjs->status_suplesi == '1') {
                    $bpjs->scenario = 'suplesi';
                } else {
                    $bpjs->scenario = 'kll';
                }
            }
        }

        // var_dump($bpjs->attributes);die;
        if ($bpjs->validate()) {
            $response = $this->_restPendaftaran->post('allow-bpjs/create-sep-new', [
                'form_params' => $bpjs->attributes
            ]);
            $body = json_decode($response->getBody(), TRUE);
            return $response->getBody();
        } else {
            $formName = substr(strrchr(get_class($bpjs), "\\"), 1);
            $response = $bpjs->getErrors();

            return DocoHelpers::response($response, 422, $formName);
        }

    }

    /**
     * get data bayi by noRekamMedik
     *
     * @param Integer $pasienId
     * @return JSON
     * @author Tsani Nashrullah
     **/
    public function actionBayiByPasien()
    {
        $noRekamMedik = Yii::$app->request->get('noRekamMedik');
        if (!empty($noRekamMedik)) {
            // hit service pendaftaran
            $response = $this->_restPendaftaran->get('allow/bayi-by-pasien',[
                'query' => [
                    'noRekamMedik' => $noRekamMedik
                ]
            ]);
            $response = json_decode($response->getBody(), TRUE);
            return $this->helper->macroResponseJson(200, 'Berhasil mendapatkan data bayi.', $response['response']);
        } else {
            return $this->helper->macroResponseJson(400, 'Isian pasien tidak boleh kosong');
        }
    }

    /**
     * this function return data of region
     *
     * @param String/Integer $type type of region requested
     * @param Integer $foreignId foreign id of region (if exist)
     * @return JSON
     * @author Tsani Nashrullah
     **/
    public function actionRegionList()
    {
        $payload = Yii::$app->request->get();
        if (isset($payload['type'])) {
            $response = $this->_restPendaftaran->get('allow/region-by-type',[
                'query' => $payload
            ]);
            $response = json_decode($response->getBody(), TRUE);
            return $this->helper->macroResponseJson(200, 'Berhasil mendapatkan data region.', $response['response']);
        } else {
            return $this->helper->macroResponseJson(400, 'Tipe Region tidak boleh kosong', $payload);
        }
    }

    public function actionCreateSepManual()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $bpjs = new BpjsNewForm();
        $postBpjs = $post['BpjsNewForm'];

        $pasienadmisi_id = ($postBpjs['pasienadmisi_id'] == "null" || $postBpjs['pasienadmisi_id'] == "") ? null : $postBpjs['pasienadmisi_id'];

        $workspace = Yii::$app->session->get('active_workspace');
        $ruangan_id = $workspace['ruangan_id'];
        $bpjs->attributes = $postBpjs;
        $bpjs->ruangan_id = $ruangan_id;
        $bpjs->no_kartu = $postBpjs['no_asuransi'];
        $bpjs->tanggal_rujukan = $postBpjs['tanggal_sep'];
        $bpjs->no_telp = '-';
        $bpjs->kasus_kecelakaan = 0;
        $bpjs->ppk_rujukan = '-';
        $bpjs->asal_rujukan = 0;
        $bpjs->nosep = $postBpjs['nosep'];
        $bpjs->poli_tujuan = $postBpjs['poli_tujuan'];
        $bpjs->pendaftaran_id = (new PelayananHelpers)->decryptId($postBpjs['pendaftaran_id']);
        $bpjs->pasienadmisi_id = $pasienadmisi_id;
        $bpjs->tanggal_lahir = $postBpjs['tanggal_lahir'];
        $bpjs->jenis_kelamin = $postBpjs['jenis_kelamin'];
        $bpjs->hak_kelas = $postBpjs['hak_kelas'];
        $bpjs->asuransi = $postBpjs['asuransi'];
        $bpjs->penjamin = $postBpjs['penjamin'];
        $bpjs->jenis_peserta = $postBpjs['jenis_peserta'];
        $bpjs->penjamin = $postBpjs['penjamin'];
        $bpjs->noMr = $postBpjs['noMr'];
        $bpjs->nama_pasien = $postBpjs['nama_pasien'];
        $no_kartu = $bpjs->no_kartu;
        if ($bpjs->validate()) {
            $response = $this->_restPendaftaran->post('allow-bpjs/create-sep-manual', [
                'form_params' => $bpjs->attributes
            ]);
            $body = json_decode($response->getBody(), TRUE);
            return DocoHelpers::response($body);
        } else {
            $formName = substr(strrchr(get_class($bpjs), "\\"), 1);
            $response = $bpjs->getErrors();

            return DocoHelpers::response($response, 422, $formName);
        }
    }

    public function actionGetNoAsuransi()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $no_rekam_medik = $request->get('no_rekam_medik', null);
        $carabayar_id = $request->get('carabayar_id', null);
        $penjamin_id = $request->get('penjamin_id', null);

        $response = $this->_restPendaftaran->get('allow/get-no-asuransi',[
                'query' => [
                    'no_rekam_medik' => $no_rekam_medik,
                    'carabayar_id' => $carabayar_id,
                    'penjamin_id' => $penjamin_id,
                ]
            ]);

        $body = json_decode($response->getBody(), TRUE);
        return DocoHelpers::response($body);
    }

    /**
     * @function : cek status kunjungan
     */
    public function actionValidate()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request->post();
        $noRm = $request['noRm'];
        $instalasi = $request['params'];
        $results = [];
        $results['instalasi'] = $instalasi;

        if($instalasi == DocoConstants::PARAM_DFTR[DocoConstants::WS_PENUNJANG]){
            return DocoHelpers::response($results, 200);
        }

        $response = $this->_restPendaftaran->post('allow/validate-kunjungan', [
            'form_params' => ['params'=>$noRm, 'instalasi' => $instalasi]
        ]);
        $response = json_decode($response->getBody(), TRUE);

        if ($response['metadata']['status'] == 200) {
            $kunjungan = $response['response'];
            if(!empty($kunjungan)) {
                $kunjunganGantung = [];
                foreach($kunjungan as $k => $v){
                    if($v['ins_id'] == DocoConstants::INSTALASI_ID_RJ && $v['pasienpulang_id'] == null) {
                        if(!isset($kunjunganGantung[$v['rua_id']])){
                            $kunjunganGantung[$v['rua_id']] = $v['rua_nama'];
                        }
                    }
                    if($v['ins_id'] == DocoConstants::INSTALASI_ID_RD) {
                        if($v['pasienpulang_id'] == null || $v['pasienpulangri_id'] == null) {
                            if(!isset($kunjunganGantung[$v['rua_id']])){
                                $kunjunganGantung[$v['rua_id']] = $v['rua_nama'];
                            }
                       }
                    }
                    if($v['ins_id'] == DocoConstants::INSTALASI_ID_RI && $v['pasienpulangri_id'] == null) {
                        if(!isset($kunjunganGantung[$v['rua_id']])){
                            $kunjunganGantung[$v['rua_id']] = $v['rua_nama'];
                        }
                    }
                }

                if(!empty($kunjunganGantung)) {
                    $kunjunganGantung = implode("<br>", $kunjunganGantung);
                    $results['text'] = 'Pasien masih dalam pelayanan di unit :<br><b>'.$kunjunganGantung;

                    if($instalasi == DocoConstants::PARAM_DFTR[DocoConstants::WS_RAJAL]) {
                        return DocoHelpers::response($results, 200);
                    } else {
                        return DocoHelpers::response($results, 500);
                    }
                } else {
                    return DocoHelpers::response($results, 200);
                }
            }
            return DocoHelpers::response($results, 200);
        }
    }

    public function actionGetPenanggungJawab()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request->get();
        $idPj = $request['idPj'];
        if($idPj == "null") {
            $idPj = null;
        }
        $pasienId = $request['pasienId'];
        if($pasienId == "null") {
            $pasienId = null;
        }
        if($idPj || $pasienId) {
            $response = $this->helper->guzzleExec($this->_restPendaftaran, [
                'url' => 'allow/get-penanggung-jawab',
                'method' => 'post',
                'payload' => [
                    'form_params' => [
                        'penanggungjawab_id' => $idPj,
                        'pasien_id' => $pasienId
                    ]
                ]
            ]);
            return DocoHelpers::response($response);
        } else {
            return $this->helper->macroResponseJson(422, 'Id Tidak Boleh Kosong', '');
        }
    }

    public function actionGetProfileRs()
    {
        $response = $this->_restPendaftaran->get('allow/get-profile-rs',[
            'query' => []
        ]);
        $response = json_decode($response->getBody(), TRUE);
        return DocoHelpers::response($response, 200);
    }

    public function actionListBagian($selected = null) {
        $request = Yii::$app->request;
        $post = $request->post();
        if($post['depdrop_parents'][0] && $post['depdrop_parents'][0] != 'Loading ...') {
            $carabayar_id = $post['depdrop_parents'][0];

            $response = $this->_restPendaftaran->get('allow/list-bagian?carabayar_id='.$carabayar_id);
            $body = json_decode($response->getBody(),TRUE);
            $ddlBagian = $body['response'];

            $out = [];
            $selected = null;
            foreach($ddlBagian as $bag => $value) {
                $out[] = [
                    'id' => $value['ruangancarabayar_id'],
                    'name' => $value['ruangcarabayar_nama'],
                ];
            }

            return json_encode(['output'=>$out, 'selected'=>$selected]);
        }
        return json_encode(['output'=>'', 'selected'=>'']);
    }

    /**
     * @function : getll list all dokter
     */
    public function actionSearchDokter($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];

        try {
            $response = $this->_restPendaftaran->get('allow/get-list-dokter?q=' . $q);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response'] as $value) {
                $result['results'][] = [
                    'id' => $value['pegawai_id'],
                    'text' => $value['nama_pegawai'],
                ];
            }

            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetLastPenanggung()
    {
        $request = Yii::$app->request;
        $pasien_id = $request->get('pasien_id', null);
        if(empty($pasien_id)) {
            return DocoHelpers::response([
                'response' => [
                    'title' => 'Proses Gagal!',
                    'text' => 'Pasien tidak ditemukan.'
                ]
            ],422);
        }

        $result = [];
        try {
            $response = $this->_restPendaftaran->get('allow/last-penanggung', [
                'query' => [
                    'id' => $pasien_id
                ]
            ]);
            $body = json_decode($response->getBody(),TRUE);
            $result = $body['response'];

            return DocoHelpers::response(['results' => $result]);
        } catch (RequestException $e) {
            $result['errorMessage'] = $e->getMessage();
            return $result;
        } catch (ErrorException $e) {
            $result['errorMessage'] = $e->getMessage();
            return $result;
        }
    }

    /**
     * @method : search regis before (styp)
     */
    public function actionSearchKunjunganSebelumnya()
    {
        $result = [];
        $request = Yii::$app->request->get();
        $pasienId = $request['pasienId'];

        if(!empty($pasienId)) {
            $result = $this->helper->guzzleExec($this->_restPendaftaran, [
                'url' => 'allow/get-kunjungan-sebelumnya',
                'payload' => [
                    'query' => [
                        'pasien_id' => $pasienId
                    ]
                ]
            ]);
        }

        return DocoHelpers::response(['results' => $result]);
    }

    public function actionGetGradePenjamin()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $term = $request->get('search');
        $results = [];
        $data = $this->helper->guzzleExec($this->_restPendaftaran, [
            'url' => 'allow/get-grade-penjamin',
            'payload' => [
                'query' => [
                    'penjamin_id' => $request->get('penjamin_id')
                ]
            ]
        ]);

        if(!empty($data)) {
            foreach($data as $key => $value) {
                if(!empty($term)) {
                    if (strpos(strtolower($value['grade']),strtolower($term)) !== FALSE) {
                        $results[] = [
                            'id' => $value['penjamingrade_id'],
                            'text' => $value['grade']
                        ];
                    }
                } else {
                    $results[] = [
                        'id' => $value['penjamingrade_id'],
                        'text' => $value['grade']
                    ];
                }
            }
        }

        return ['results' => $results];
    }

    /**
     * @function : cek status pembayaran pasien rs
     */
    public function actionValidatePembayaran()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;

        return $this->helper->guzzleExec($this->_restPendaftaran, [
            'url' => 'allow/validate-pembayaran',
            'payload' => [
                'query' => [
                    'pendaftaran_id' => $request->get('pendaftaran_id')
                ]
            ],
            'returnResponse' => true
        ]);
    }

    public function actionFilterRuangan()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return $this->helper->guzzleExec($this->_restPendaftaran, [
            'url' => 'allow/filter-ruangan',
            'method' => 'get',
            'payload' => [
                'query' => [
                    'instalasi_id' => Yii::$app->request->get('instalasi_id'),
                ]
            ],
            'returnResponse' => true
        ]);
    }
}
