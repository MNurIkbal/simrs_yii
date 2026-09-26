<?php
// author : rizal@docotel.com

namespace Doco\igd\controllers;

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
use GuzzleHttp\Exception\RequestException;

class EndPointController extends DocoController
{


    protected $_restIgd;
	protected $_restKasir;
    protected $allowAction = ['*'];
    /**
     * @inheritdoc
     */

    public function init()
    {
        parent::init();
        $this->_restIgd = Yii::$app->docoRest->igd;
    	$this->_restKasir = Yii::$app->docoRest->kasir;
    }

    public function beforeAction($action)
    {
    	return true;
    }

    public function actionGetListDokterJaga($q="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restIgd->get('allow/get-list-dokter?q='.$q);
            $body = json_decode($response->getBody(), True);
            $result['results'][] = [
                'id' => 'null',
                'text' => '--Piih Dokter Jaga--'
            ];
            foreach ($body['response'] as $value)
            $result['results'][] = [
                'id' => $value['pegawai_id'],
                'text' => $value['nama_pegawai']
            ];

            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetListDokterPenanggungJawab($q="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restIgd->get('allow/get-list-dokter?q='.$q);
            $body = json_decode($response->getBody(), True);
            $result['results'][] = [
                'id' => 'null',
                'text' => '--Piih Dokter Penanggung Jawab--'
            ];
            foreach ($body['response'] as $value)
            $result['results'][] = [
                'id' => $value['nama_pegawai'],
                'text' => $value['nama_pegawai']
            ];

            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetListDokter()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post();
        $ruangan_id = null;
        $tgl_lanjut_rawat = null;
        $params = '?';

        if (isset($post['depdrop_all_params']['poliklinik_id'])) {
            $params = $params.'ruangan_id='.$post['depdrop_all_params']['poliklinik_id'];
        }

        if (isset($post['depdrop_all_params']['tgl_lanjut_rawat'])) {
            if ($params == '?') {
                $params = $params.'tgl_lanjut_rawat='.date('Y-m-d', strtotime($post['depdrop_all_params']['tgl_lanjut_rawat']));
            } else {
                $params = $params.'&tgl_lanjut_rawat='.date('Y-m-d', strtotime($post['depdrop_all_params']['tgl_lanjut_rawat']));
            }
        }

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';
        $listJadwalDokter = '&list=jadwal_dokter';
        try {
            $response = $this->_restIgd->get('allow/get-jadwal-dokter'.$params.$listJadwalDokter);
            $body = json_decode($response->getBody(), true);

            if (!empty($body['response'])) {
                foreach ($body['response'] as $value) {
                    $result['output'][] = [
                        'id' => $value['pegawai_id'],
                        'name' => $value['nama_pegawai']
                    ];
                }
            }

            // json_encode($result);
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetAllDiagnosa($q = null, $page = null,$is_valueWithText= 0, $id = null)
    {
        try{
            $limit = 10;
            $offset = ($page-1)*10;
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $out = ['results' => ['id'=>'','text'=>'']];
            $response = $this->_restIgd->get('allow/get-all-diagnosa',[
                'query' => ['keyword'=>$q,'page'=>$page,'offset'=>$offset,'limit'=>$limit]
            ]);
            $response = json_decode($response->getBody(), TRUE);
            $results = [];
            if ($response['metadata']['status'] == 200) {
                $list = $response['response'];
                foreach ($list as $key => $each) {
                    $results[] = [
                        'id'=>$each['diagnosa_id'].'_'.$each['nama_diagnosa'],
                        'text'=>$each['nama_diagnosa'],
                    ];
                }
                $out['results'] = $results;
                $out['pagination'] = [ 'more' => !empty($list)?true:false ];
            }

            return $out;
        } catch (RequestException $e) {
            return ['results' => ['id'=>'','text'=>'']];
        } catch (\Exception $e) {
            return ['results' => ['id'=>'','text'=>'']];
        }
    }



    public function actionGetListRuangan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_instalasi = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restIgd->get('allow/get-list-ruangan?instalasi_id='.$parent_instalasi);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $value)
                $result['output'][] = [
                    'id' => $value['ruangan_id'],
                    'name' => $value['ruangan_nama']
                ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    // penunjang CLONE FROM DAFTARCONTROLLER
    // Rizal Faidin
    public function actionGetTarifPaket()
    {
        $result = [];
        $request = Yii::$app->request;
        $get = $request->get();
        try {
            $params = [
                'ruangan_id' => isset($get['ruangan_id']) ? $get['ruangan_id'] : '',
                'penjamin_id' => isset($get['penjamin_id']) ? $get['penjamin_id'] : '',
                'kelaspelayanan_id' => isset($get['kelaspelayanan_id']) ? $get['kelaspelayanan_id'] : '',
                'instalasi_id' => isset($get['instalasi_id']) ? $get['instalasi_id'] : '',
            ];
            $data = [];
            $draw = $request->get('draw', 1);
            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;
            $result['recordsTotal'] = 0;

            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($get);
            $params['page'] = $yiiRestfulParams['page'];
            $params['per-page'] = $yiiRestfulParams['per-page'];

            if(isset($yiiRestfulParams['advanced-filter']['tipepaket_nama'])){
                $params['tipepaket_nama'] = strtolower($yiiRestfulParams['advanced-filter']['tipepaket_nama']);
            }

            if(isset($yiiRestfulParams['advanced-filter']['jenispemeriksaanlab_nama'])){
                $params['jenispemeriksaanlab_nama'] = strtolower($yiiRestfulParams['advanced-filter']['jenispemeriksaanlab_nama']);
            }

            $url = 'allow/get-tarif-paket';
            $response = $this->_restKasir->get($url, [
                'query' => $params
            ]);
            $body = json_decode($response->getBody(), true);
            $body = isset($body['response']) ? $body['response'] : [];
            $no = 0;
            foreach ($body['data'] as $key => $value) :
                $no++;
                $value['rowNum'] = $no;

                if(isset($value['paketDetailView'])){
                    if(count($value['paketDetailView']) > 0){
                        foreach ($value['paketDetailView'] as $k => $v) :
                            $value['nama_tindakan_paket'][] =$v['daftartindakan_nama'];
                        endforeach;
                        $value['nama_tindakan_paket'] = implode(',', $value['nama_tindakan_paket']);
                    }
                }
                $data[$key] = $value;
            endforeach;
            $result['data'] = $data;
            $result['recordsTotal'] = $body['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['_meta']['totalCount'];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }
    public function actionGetTarifTindakan()
    {
        $result = [];
        $request = Yii::$app->request;
        $get = $request->get();
        try {
            $params = [
                'ruangan_id' => isset($get['ruangan_id']) ? $get['ruangan_id'] : '',
                'penjamin_id' => isset($get['penjamin_id']) ? $get['penjamin_id'] : '',
                'kelaspelayanan_id' => isset($get['kelaspelayanan_id']) ? $get['kelaspelayanan_id'] : '',
                'instalasi_id' => isset($get['instalasi_id']) ? $get['instalasi_id'] : '',
            ];
            $data = [];
            $draw = $request->get('draw', 1);
            $result = [];
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = 0;
            $result['recordsTotal'] = 0;

            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($get);
            $params['page'] = $yiiRestfulParams['page'];
            $params['per-page'] = $yiiRestfulParams['per-page'];

            if(isset($yiiRestfulParams['advanced-filter']['jenispemeriksaanlab_nama'])){
                $params['jenispemeriksaanlab_nama'] = strtolower($yiiRestfulParams['advanced-filter']['jenispemeriksaanlab_nama']);
            }

            if (isset($yiiRestfulParams['advanced-filter']['daftartindakan_nama'])){
                $params['daftartindakan_nama'] = strtolower($yiiRestfulParams['advanced-filter']['daftartindakan_nama']);
            }
            $url = 'allow/get-tarif-tindakan';
            $response = $this->_restKasir->get($url, ['query'=>$params]);
            $body = json_decode($response->getBody(), true);
            $body = isset($body['response']) ? $body['response'] : [];
            $no = 0;
            foreach ($body['data'] as $key => $value) :
                $no++;
                $value['rowNum'] = $no;
                $data[$key] = $value;
            endforeach;
            $result['data'] = $data;
            $result['recordsTotal'] = $body['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['_meta']['totalCount'];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionGetListKondisikeluar()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_carakeluar = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restIgd->get('allow/get-list-kondisikeluar?carakeluar_id='.$parent_carakeluar);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $value)
                $result['output'][] = [
                    'id' => $value['kondisikeluar_id'],
                    'name' => $value['kondisikeluar_nama']
                ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    /**
    * @author Rizal F. <rizal@docotel.com>
    * @since
    * @param date tanggal
    * @return array list poli jadwal aktif
    */
    public function actionGetJadwalPoli()
    {
        $request = Yii::$app->request;
        $post = $request->post('tanggal');
        $instalasi_id = $request->post('instalasi_id');

        try {
            if ($instalasi_id != '') {
                $response = $this->_restIgd->get('allow/get-jadwal-poli', ['query'=>['tanggal'=>$post, 'instalasi_id'=>$instalasi_id]]);
            } else {
                $response = $this->_restIgd->get('allow/get-jadwal-poli', ['query'=>['tanggal'=>$post]]);
            }

            $body = json_decode($response->getBody(), True);

            return DocoHelpers::response($body['response']);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDataKondisiKeluar($carakeluar_id)
    {
        try{
            $data = [];
            if(isset($carakeluar_id)){
                $response = $this->_restIgd->get('allow/data-kondisi-keluar?carakeluar_id='.$carakeluar_id);
                $body = json_decode($response->getBody(), True);
                foreach ($body['response'] as $key => $value) {
                    $data[] = [
                        'id' => $value['kondisikeluar_id'],
                        'text' => $value['kondisikeluar_nama'],
                        'parent' => $value['carakeluar_id']
                    ];
                }
                $total = count($body['response']);
                $return = ['result'=>$data];
                return DocoHelpers::response($return);
            }
        }catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    /**
     * @todo Method untuk mendapatkan data diagnosa berdasarkan versi tabular list
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetNewDiagnosa($q = '', $type = 'diagnosa_masuk', $all_text = 0, $id_with_text = 0, $is_perawat = 0,  $page = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $limit = 10;
            $offset = ($page-1)*10;
            $result = [];
            $result['results'] = [];

            if ($type == 'diagnosa_masuk') {
                $type = DocoConstants::VAR_KELOMPOK_DIAGNOSA_MASUK;
            } else if ($type == 'diagnosa_utama') {
                $type = DocoConstants::VAR_KELOMPOK_DIAGNOSA_UTAMA;
            } else if ($type == 'diagnosa_penyerta') {
                $type = DocoConstants::VAR_KELOMPOK_DIAGNOSA_PENYERTA;
            } else if ($type == 'diagnosa_operasi') {
                $type = DocoConstants::VAR_KELOMPOK_DIAGNOSA_OPERASI;
            } else if ($type == 'diagnosa_keluarga') {
                $type = DocoConstants::VAR_KELOMPOK_DIAGNOSA_KELUARGA;
            } else if ($type == 'diagnosa_terapi') {
                $type = DocoConstants::VAR_KELOMPOK_DIAGNOSA_TERAPI;
            } else {
                $type = DocoConstants::VAR_KELOMPOK_DIAGNOSA_AWALAN;
            }

            $request = $this->_restIgd->get('allow/get-new-diagnosa', [
                'query' => compact('q', 'type', 'is_perawat', 'limit', 'offset', 'page')
            ]);
            $response = json_decode($request->getBody(), true);

            $list = $response['response'];
            if ($all_text == 1) {
                foreach ($response['response'] as $value) {
                    $result['results'][] = [
                        'id' => $value['diagnosa_kode'] . ' - ' . $value['diagnosa_nama'],
                        'text' => $value['diagnosa_kode'] . ' - ' . $value['diagnosa_nama'],
                    ];
                }
            } else {
                if ($id_with_text == 1) {
                    foreach ($response['response'] as $value) {
                        $result['results'][] = [
                            'id' => $value['diagnosa_id'] . '_' . $value['diagnosa_kode'] . ' - ' . $value['diagnosa_nama'],
                            'text' => $value['diagnosa_kode'] . ' - ' . $value['diagnosa_nama'],
                        ];
                    }
                } else {
                    foreach ($response['response'] as $value) {
                        $result['results'][] = [
                            'id' => $value['diagnosa_id'],
                            'text' => $value['diagnosa_kode'] . ' - ' . $value['diagnosa_nama'],
                        ];
                    }
                }
            }

            $result['pagination'] = [ 'more' => !empty($list)?true:false ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDefaultSatuan()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $get = $request->get();
        $ruangan_id = $get['ruangan_id'];
        $obatalkes_id = $get['obatalkes_id'];
        try {
            $request = $this->_restIgd->get('allow/get-data-obat?obatalkes_id='. $obatalkes_id.'&ruangan_id='. $ruangan_id);
            $body = json_decode($request->getBody(),TRUE);
            $response = $body['response'];
            return $response;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * @Author: Budi (budi@docotel.com)
     * @Date:   2019-09-10 16:53
     * get data depdrop satuan besar
     */
    public function actionListSatuanBesar()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $obatalkes_id = $post['depdrop_parents'][0];
        $selected = null;
        try {
            $request = $this->_restIgd->get('allow/list-satuan-besar?obatalkes_id='. $obatalkes_id);
            $body = json_decode($request->getBody(),TRUE);
            $response = $body['response'];
            $selected = $response[0]['satuankecil_id'];
            $out = [];
            foreach($response as $key => $value) {
                $out[] = [
                        'id' => $value['satuanbesar_id'],
                        'name' => $value['satuan_besar'],
                        'konversi' => $value['nilai_konversi'],
                    ];
            }
            return json_encode(['output'=>$out, 'selected'=> $selected]);
        } catch (\Exception $e) {
            return json_encode(['output'=>[], 'selected'=> $selected]);;
        }
    }

    /**
     * @Author: Budi (budi@docotel.com)
     * @Date:   2019-09-12 10:37
     * get data konversi satuan
     */
    public function actionGetNilaiKonversi()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $get = $request->get();
        $satuanbesar_id = $get['satuanbesar_id'];
        $obatalkes_id = $get['obatalkes_id'];
        $ruangan_id = $get['ruangan_id'];
        try {
            $request = $this->_restIgd->get('allow/get-konversi?obatalkes_id='. $obatalkes_id.'&satuanbesar_id='. $satuanbesar_id.'&ruangan_id='. $ruangan_id);
            $body = json_decode($request->getBody(),TRUE);
            $response = $body['response'];
            return $response;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function actionDoctorList()
    {
        $request = $this->_restIgd->get('allow/doctor-list', [
            'query' => Yii::$app->request->get('payload', [])
        ]);
        $body = json_decode($request->getBody(),TRUE);
        return $this->responseJson(200, 'Data Berhasil didapat!', $body['response']);
    }

    public function actionGetKamarTempatTidur()
     {
        $request = $this->_restIgd->get('allow/get-kamar-tempat-tidur', [
            'query' => Yii::$app->request->get('payload', [])
        ]);
        $body = json_decode($request->getBody(),TRUE);
        return $this->responseJson(200, 'Data Berhasil didapat!', $body['response']);
     }

     public function actionGetJenisKamar()
     {
        $request = $this->_restIgd->get('allow/get-jenis-kamar', [
            'query' => Yii::$app->request->get('payload', [])
        ]);
        $body = json_decode($request->getBody(),TRUE);
        return $this->responseJson(200, 'Data Berhasil didapat!', $body['response']);
    }

    public function actionGetDokterSpesialis()
    {
        $request = $this->_restIgd->get('allow/get-list-dokter-spesialis', [
            'query' => Yii::$app->request->get('payload', [])
        ]);
        $body = json_decode($request->getBody(),TRUE);
        return $this->responseJson(200, 'Data Berhasil didapat!', $body['response']);
    }

    public function actionGetPegawaiData($idKelompok = null)
     {
         $options = [
            'method' => 'get',
            'url' => 'allow/list-pegawai',
            'returnResponse' => true
         ];
         if ($idKelompok) {
            $options['payload'] = [
                'query' => [
                    'kelompokpegawai_id' => json_decode($idKelompok)
                ]
            ];
         }
         
         return $this->guzzleExec($this->_restIgd, $options);
     }

    public function actionListDataTindakan()
    {
         $title = 'Laporan Terapi';
         $type = Yii::$app->request->get('type', DocoConstants::TYPE_RJ);
         $pendaftaran_id = Yii::$app->request->get('id');
         $pasienadmisi_id = Yii::$app->request->get('pasienadmisi_id');
         $pasien_id = Yii::$app->request->get('pasien_id', null); // kegunaannya untuk ri dan rd
         switch ($type) {
             case DocoConstants::TYPE_IGD:
             case 'igdLaporanTerapi':
                 $url = [
                     'datatable' => Url::to(['/igd/end-point/get-list-tindakan', 'id' => Yii::$app->request->get('id'), 'pasien_id'=> $pasien_id,'type' => $type])
                 ];
                 break;
             case DocoConstants::TYPE_IGD:
                     $url = [
                         'datatable' => Url::to(['/igd/end-point/get-list-tindakan', 'id' => Yii::$app->request->get('id'), 'type' => $type])
                     ];
                 break;
         }
         return $this->renderAjax('//cppt/laporan_tindakan', compact('title', 'url'));
     }

    public function actionGetListTindakan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $type = Yii::$app->request->get('type', DocoConstants::TYPE_IGD);
        $payload = DocoDatatableHelper::advancedFilterParam();
        $pasien_id = '';
        $urlDelete = '';
        $instalasiPenunjangArr = [DocoConstants::INSTALASI_ID_RAD, DocoConstants::INSTALASI_ID_LAB, DocoConstants::INSTALASI_ID_BEDAH];
        switch (strtolower($type)) {
            case DocoConstants::TYPE_IGD:
            case 'igdlaporanterapi':
                $url = 'riwayat-pasien/fetch-list-tindakan';
                $urlDelete = '/igd/riwayat-pasien/batal-instruksi-form';
                $pasien_id = Yii::$app->request->get('pasien_id', null) ? DocoHelpers::decrypt(Yii::$app->request->get('pasien_id', null)) : null;
                break;
            case DocoConstants::TYPE_IGD:
                    $url = 'riwayat-pasien/fetch-list-tindakan';
                    $urlDelete = '/igd/riwayat-pasien/batal-instruksi-form';
                break;
        }
        $queryParams = array_merge($payload, [
            'pendaftaran_id' => DocoHelpers::decrypt(Yii::$app->request->get('id')),
            'pasien_id' => $pasien_id,
            'pasienadmisi_id' => Yii::$app->request->get('pasienadmisi_id', null),
            'start' => Yii::$app->request->get('start', 0),
            'length' => Yii::$app->request->get('length', 10),
            'type' => $type,
            'instruksi' => Yii::$app->request->get('instruksi'),
            'jenis' => Yii::$app->request->get('jenis'),
            'startDate' => Yii::$app->request->get('startDate'),
            'endDate' => Yii::$app->request->get('endDate'),
        ]);
        $getData = $this->guzzleExec($this->_restIgd, [
            'url' => $url,
            'payload' => [
                'query' => $queryParams
            ]
        ]);
        $statusImplementasiFarmasi = isset($getData['status_implementasi_farmasi']) ? $getData['status_implementasi_farmasi'] : DocoConstants::STATUS_RESEPTUR_BELUM_DIPROSES;
        foreach ($getData['data'] as $key => $value) {
            $getData['data'][$key]['instruksi'] = str_replace('Cyto', 'CITO', $value['instruksi']);
            $buttonAction = [];
            $is_resep_lunas = strtolower($type) == DocoConstants::RJ_LAP_TERAPI ? $value['status_bayar'] != DocoConstants::STAT_BAYAR_LUNAS ? false : true : $value['is_bayar'];

            /**
             * Penyesuaian show alasan, tanggal & pegawai ketika order bedah ditolak
             * 541 = Ditolak
             */

            if (!$value['deleted'] && (int) $value['status_implementasi'] != 541) {
                if (!$value['is_bayar']) {
                    if (strtolower($value['grouping_tipe']) == 'tindakanbmhp' && !$value['is_pulang']
                         && $value['status_bmhp_id'] == DocoConstants::BMHP_BELUM_VERIFIKASI) {
                        $buttonAction = [
                            'title' => '<i class="fa fa-trash"></i>',
                            'attr' => [
                                'class' => 'btn btn-danger btn-sm btn-delete-instruksi',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal-batal-instruksi',
                                'href' => Url::to([
                                    $urlDelete,
                                    'id' => $this->helper->encrypt($value['pendaftaran_id']),
                                    'pasienadmisi_id' => Yii::$app->request->get('pasienadmisi_id', null),
                                    'instruksitindakan_id' => $value['instruksitindakan_id'],
                                    'jenis' => strtolower($value['jenis']),
                                    'type' => $type,
                                    'group' => strtolower($value['grouping_tipe'])
                                ]),
                                'data-instruksitindakan_id' => $value['instruksitindakan_id']
                            ]
                        ];
                    } else if (Yii::$app->docoVars->workspace('instalasi_id') == DocoConstants::INSTALASI_ID_RD && strtolower($value['grouping_tipe']) == 'tindakanbmhp' && !$value['is_pulang'] && empty($value['status_bmhp_id'])) {
                        $buttonAction = [
                            'title' => '<i class="fa fa-trash"></i>',
                            'attr' => [
                                'class' => 'btn btn-danger btn-sm btn-delete-instruksi',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal-batal-instruksi',
                                'href' => Url::to([
                                    
                                    $urlDelete,
                                    'id' => $this->helper->encrypt($value['pendaftaran_id']),
                                    'instruksitindakan_id' => $value['instruksitindakan_id'],
                                    'instruksi_id' => $value['instruksi_id'],
                                    'jenis' => strtolower($value['jenis']),
                                    'type' => $type,
                                    'group' => strtolower($value['grouping_tipe']),
                                    'instalasi_id' => $value['instalasi_penunjang_id']
                                ])
                            ]
                        ];
                    }

                    // Batal Penunjang
                    // aksi hanya dimunculkan jika tindakan terkait belum dibayar dan status tindakan tersebut tidak termasuk dalam list status yg dijadikan kondisi
                    /**
                     * Reseptur
                     * 347 = Sudah Diproses
                     *
                     * Implementasi
                     * 455 = Sudah Implementasi
                     *
                     * Penunjang
                     * 477 = Sudah disetujui/belum periksa penunjang
                     * 471 = Sudah disetujui Bedah
                     * 472 = Batal
                     * 473 = Periksa
                     * 475 = Selesai
                     * 476 = Batal
                     * 482 = Sedang operasi
                     * 483 = Sudah operasi
                     * 488 = Belum operasi
                     * 692 = Reschedule
                     */

                    if (in_array($value['instalasi_penunjang_id'], $instalasiPenunjangArr) && !in_array((int) $value['status_implementasi'], [347, 455, 477, 471, 472, 473, 475, 476, 482, 483, 488, 692]) ||  in_array($value['instalasi_penunjang_id'], $instalasiPenunjangArr) && $value['is_telah_implementasi']) {
                        // pengecekan kondisi untuk menyesuaikan parameter yg dikirim disesuaikan dengan instalasi di pendaftaran

                        if (Yii::$app->docoVars->workspace('instalasi_id') ==  DocoConstants::INSTALASI_ID_RD && !$value['is_pulang']) {
                            if (count(array_intersect([ArrayHelper::getValue($value, 'instalasi_penunjang_id')], DocoConstants::INSTALASI_ID_PENUNJANG)) && !ArrayHelper::getValue($value, 'is_telah_implementasi', false) && ArrayHelper::getValue($value, 'status_implementasi') != DocoConstants::LAB_ST_PEN_BELUMPERIKSA) {
                                $buttonAction = [
                                    'title' => '<i class="fa fa-trash"></i>',
                                    'attr' => [
                                        'class' => 'btn btn-danger btn-sm btn-delete-instruksi',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-batal-instruksi',
                                        'href' => Url::to([
                                            $urlDelete,
                                            'id' => $this->helper->encrypt($value['pendaftaran_id']),
                                            'instruksitindakan_id' => $value['instruksitindakan_id'],
                                            'jenis' => strtolower($value['jenis']),
                                            'type' => $type,
                                            'group' => strtolower($value['grouping_tipe']),
                                            'instalasi_id' => $value['instalasi_penunjang_id']
                                        ]),
                                    ]
                                ];
                            }
                        }
                    }

                    if (strtolower($value['grouping_tipe'])  == 'reseptur') {
                        if($value['status_implementasi'] != DocoConstants::STATUS_RESEPTUR_DISERAHKAN && $value['status_implementasi'] != DocoConstants::STATUS_RESEPTUR_BATAL &&   !$value['is_pulang'] && !$is_resep_lunas) {
                            $buttonAction = [
                                'title' => '<i class="fa fa-trash"></i>',
                                'attr' => [
                                    'class' => 'btn btn-danger btn-sm btn-delete-instruksi',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-batal-instruksi',
                                    'href' => Url::to([
                                        $urlDelete,
                                        'id' => $this->helper->encrypt($value['pendaftaran_id']),
                                        'noresep' => $value['noresep'],
                                        'type' => $type,
                                        'group' => strtolower($value['grouping_tipe']),
                                        'instalasi_id' => $value['instalasi_penunjang_id']
                                    ]),
                                ]
                            ];
                        } else if ($value['status_implementasi'] == DocoConstants::STATUS_RESEPTUR_BATAL) {
                            if (strtolower($type) == DocoConstants::TYPE_IGD | strtolower($type) == DocoConstants::RD_LAP_TERAPI) {
                                $buttonAction = [
                                    'title' => '<i class="fa fa-eye"></i>',
                                    'attr' => [
                                        'class' => 'btn btn-info btn-sm btn-view-instruksi',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modal-batal-instruksi',
                                        'data-width' => '50%',
                                        'href' => Url::to([
                                            '/igd/riwayat-pasien/view-pembatalan-instruksi',
                                            'id' => $this->helper->encrypt($value['pendaftaran_id']),
                                            'noresep' => $value['noresep'],
                                            'group' => $value['grouping_tipe'],
                                        ]),
                                    ]
                                ];
                            }
                        }
                    }
                }
            } else {
                if ((strtolower($type) == DocoConstants::TYPE_IGD || strtolower($type) == DocoConstants::RD_LAP_TERAPI) && in_array(strtolower($value['grouping_tipe']), ['tindakanbmhp', 'penunjang', 'reseptur'])) {
                    $buttonAction = [
                        'title' => '<i class="fa fa-eye"></i>',
                        'attr' => [
                            'class' => 'btn btn-info btn-sm btn-view-instruksi',
                            'data-toggle' => 'modal',
                            'data-target' => '#modal-batal-instruksi',
                            'data-width' => '50%',
                            'href' => Url::to([
                                '/igd/riwayat-pasien/view-pembatalan-instruksi',
                                'id' => $this->helper->encrypt($value['pendaftaran_id']),
                                'instruksitindakan_id' => $value['instruksitindakan_id'],
                                'group' => $value['grouping_tipe'],
                                'noresep' => $value['noresep']
                            ]),
                        ]
                    ];
                }
            }
            $getData['data'][$key]['aksi'] = !empty($buttonAction) ? Html::button($buttonAction['title'], $buttonAction['attr']) : '';
        }
        return [
            'draw' => Yii::$app->request->get('draw'),
            'data' => $getData['data'],
            'load_more' => $getData['load_more']
        ];
    }
}
