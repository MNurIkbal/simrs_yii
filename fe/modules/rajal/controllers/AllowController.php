<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-03-05 15:18:38
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-03-21 17:32:22
 * @Description:
 */

namespace Doco\rajal\controllers;

use Yii;
use yii\filters\AccessControl;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

use yii\base\Exception;
use function GuzzleHttp\json_encode;
use \DateTime;

class AllowController extends DocoController
{
    protected $_restRajal;
    protected $_restApotek;

    public function init()
    {
        parent::init();
        $this->_restRajal = Yii::$app->docoRest->rajal;
        $this->_restApotek = Yii::$app->docoRest->apotek;
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actions()
    {
        return [
            'get-medicine-details' => 'Doco\rajal\actions\GetMedicineDetailsAction'
        ];

    }

    /**
     *
     * Bypass dcms auth
     *
     */
    public function beforeAction($action)
    {
        return true;
    }

    /**
     *
     * get data depdrop list dokter berdasarkan ruangan
     *
     */
    public function actionListDokterJadwal()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $jadwalbukapoli_id = $post['depdrop_parents'][0];
        $tanggal = $post['depdrop_parents'][1];
        $out = [];
        $tgl_pendaftaran_pasien = !empty($request->post('tgl_pendaftaran')) ? date('Y-m-d', strtotime($request->post('tgl_pendaftaran'))) : null;
        $with_self_pegawai = !empty($tgl_pendaftaran_pasien) && (date('Y-m-d', strtotime($tanggal)) > $tgl_pendaftaran_pasien) ? true : false;

        $dokterRequest = $this->_restRajal->get('allow/get-dokter-jadwal?jadwalbukapoli_id='.$jadwalbukapoli_id.'&tanggal='.$tanggal.'&with_self_pegawai='.$with_self_pegawai);
        $body = json_decode($dokterRequest->getBody(),TRUE);
        $ddlDokter = $body['response']['data-dokter'];
        foreach($ddlDokter as $dok => $value) {
            $out[] = [
                'id' => $value['pegawai_id'],
                'name' => $value['nama_pegawai'],
            ];
        }


        return json_encode(['output'=>$out, 'selected'=>'', 'message' => 'success']);
    }

    /**
     *
     * get data depdrop list jadwal dokter
     *
     */
    public function actionListJadwal()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $jadwalbukapoli_id = $post['depdrop_parents'][0];
        $tanggal = $post['depdrop_parents'][1];
        $dokter_id = $post['depdrop_parents'][2];
        $data = [];
        $dateNow = date('Y-m-d H:i:s');
        $disabled = false;
        $tgl_pendaftaran_pasien = !empty($request->post('tgl_pendaftaran')) ? date('Y-m-d', strtotime($request->post('tgl_pendaftaran'))) : null;
        $with_self_pegawai = !empty($tgl_pendaftaran_pasien) && (date('Y-m-d', strtotime($tanggal)) > $tgl_pendaftaran_pasien) ? true : false;

        $res = $this->_restRajal->get('allow/get-jadwal?jadwalbukapoli_id='.$jadwalbukapoli_id.'&tanggal='.$tanggal.'&dokter_id='.$dokter_id.'&with_self_pegawai='.$with_self_pegawai);
        $body = json_decode($res->getBody(),TRUE);
        $listJadwal = $body['response']['data-jadwal'];
        foreach($listJadwal as $k => $value) {
            $jamTutup = new DateTime($value['jadwaldokter_tutup']);
            $merge = new DateTime($tanggal.' ' .$jamTutup->format('H:i:s'));
            $disabled = false;
            if($value['temp_kuota'] <= 0){
                // $disabled = true; // dicomment untuk kebutuhan RPP-775
            }else{
                if(strtotime($merge->format('Y-m-d H:i:s')) < strtotime($dateNow)){
                    $disabled = true;
                }
            }
            $data[] = [
                'id' => $value['jadwaldokter_id'],
                'name' => $value['jadwaldokter_mulai'].' - '.$value['jadwaldokter_tutup'].' ('.$value['temp_kuota'].')',
                'disabled' => $disabled,
            ];
        }

        return json_encode(['output'=>$data, 'selected'=>'', 'message' => 'success']);
    }

    /**
     *
     * get data depdrop pegawai
     *
     */
    public function actionListPegawai()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $jabatan_id = $post['depdrop_parents'][0];

        $request = $this->_restRajal->get('allow/list-pegawai?jabatan_id='. $jabatan_id);
        $body = json_decode($request->getBody(),TRUE);
        $ddl = $body['response'];

        $out = [];
        foreach($ddl as $key => $value) {
            $out[] = [
                    'id' => $key,
                    'name' => $value
                ];
        }

        echo json_encode(['output'=>$out, 'selected'=>'']);
        return;
    }

    /**
     *
     * get data depdrop pegawai
     * hargaygdipakai
     */
    public function actionListObatAlkesDepo()
    {
        $request = Yii::$app->request;
        $get = $request->get();
        $instalasi_id = Yii::$app->docoVars->workspace('instalasi_id');
        $pegawai_id = Yii::$app->docoVars->user('id_pegawai');
        $penjamin_id = isset($get['penjamin_id']) ? $get['penjamin_id'] : 1;
        $kelaspelayanan_id = isset($get['kelaspelayanan_id']) ? $get['kelaspelayanan_id'] : null;
        $kelastagihan_id = isset($get['kelastagihan_id']) ? $get['kelastagihan_id'] : null;
        $ruangan_id = isset($get['ruangan_id']) ? $get['ruangan_id'] : null;
        $is_others = isset($get['is_others']) ? $get['is_others'] : null;
        $non_racikan = isset($get['non_racikan']) ? $get['non_racikan'] : null;
        $keyword = isset($get['q']) ? $get['q'] : '';
        $page = isset($get['page']) ? $get['page'] : 1;
        $limit = empty($keyword) ? 5 : (isset($get['limit']) ? $get['limit'] : 10);
        $return = [];
        $data = [];

        $jenis = !empty($request->get('groupJenisobat')) ? json_decode($request->get('groupJenisobat'), true) : null;

        // try {
            // $request = $this->_restRajal->get('allow/list-obat-alkes?ruangan_id=' .$ruangan_id.'&keyword='. $keyword.'&page='. $page);
            if (
                !is_null($kelastagihan_id)
                && !empty($kelastagihan_id)
            ) {
                $kelaspelayanan_id = $kelastagihan_id;
            }

            $request = $this->_restApotek->get('allow/get-list-stok-apotek',[
                'query' => [
                    'instalasi_id' => $instalasi_id,
                    'penjamin_id'  => $penjamin_id,
                    'group_jenisobat' => $jenis,
                    'ruangan_id'   => $ruangan_id,
                    'kelaspelayanan_id' => $kelaspelayanan_id,
                    'keyword'      => $keyword,
                    'page'         => $page,
                    'get_konfig_stok' => true,
                    'limit' => $limit+1,
                    'additional_filters' => $this->getAdditionalFilters($request->get()),
                ]
            ]);
            $body = json_decode($request->getBody(),TRUE);
            if(!empty($body['response']['data'])) {
                $data = $body['response']['data'];
                $cacheLabelTrackStock = 'trackObatPasien' . $ruangan_id . '-' . $pegawai_id;
                $cacheTrackStok = Yii::$app->cache->get($cacheLabelTrackStock);
                $trackStock = json_decode($cacheTrackStok, true);

                foreach ($data as $index => $stok_obat) {
                    $obatalkes_id = $stok_obat['obatalkes_id'];
                    $stok_terpakai = isset($trackStock[$obatalkes_id]) ? $trackStock[$obatalkes_id] : 0;
                    $data[$index]['qty_tersedia'] -= $stok_terpakai;
                }
            }

            $others = [
                'instalasi_id' => $instalasi_id,
                'obatalkes_id' => '0',
                'obatalkes_namalain' => 'OTHERS',
                'obatalkes_nama' => 'OTHERS',
                'qty_tersedia' => 1,
                'ruangan_id' => $ruangan_id,
                'jenisobatalkes_id' => '0',
                'qty_reseptur' => 1,
                'qty_konversi' => 1,
            ];
            if($is_others && $non_racikan){
                array_push($data, $others);
            }

            $return = [
                'data_stok' => $data,
                'payload' => $get,
                'limit' => $limit,
                'time' => isset($body['response']['time']) ? $body['response']['time'] : null,
            ];


            return DocoHelpers::response($return);
        // } catch (\Exception $e) {
        //     echo json_encode(['data_stok'=>[], 'payload'=>$get]);
        //     return;
        // }
    }

    public function actionGetAllDiagnosa($q = null, $page = null, $is_valueWithText = 0, $id = null, $set_id_as_text = 0)
    {
        try {
            $limit = 10;
            $offset = ($page - 1) * 10;
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $out = ['results' => ['id' => '', 'text' => '']];
            $response = $this->_restRajal->get('allow/get-all-diagnosa', [
                'query' => ['keyword' => $q, 'page' => $page, 'offset' => $offset, 'limit' => $limit]
            ]);
            $response = json_decode($response->getBody(), true);
            $results = [];
            if ($response['metadata']['status'] == 200) {
                $list = $response['response'];
                foreach ($list as $key => $each) {
                    if ($set_id_as_text == 0) {
                        if ($is_valueWithText == 2) { // normal condition
                            $results[] = [
                                'id' => $each['diagnosa_id'],
                                'text' => $each['nama_diagnosa'],
                            ];
                        } else {
                            $results[] = [
                                'id' => $each['diagnosa_id'] . '_' . $each['nama_diagnosa'],
                                'text' => $each['nama_diagnosa'],
                            ];
                        }
                    } else {
                        $results[] = [
                            'id' => $each['diagnosa_nama'],
                            'text' => $each['diagnosa_nama'],
                        ];
                    }
                }
                $out['results'] = $results;
                $out['pagination'] = ['more' => !empty($list) ? true : false];
            }

            return $out;
        } catch (RequestException $e) {
            return ['results' => ['id' => '', 'text' => '']];
        } catch (\Exception $e) {
            return ['results' => ['id' => '', 'text' => '']];
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
            $request = $this->_restRajal->get('allow/list-satuan-besar?obatalkes_id='. $obatalkes_id);
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
            return json_encode(['output'=>$out, 'selected'=>$selected]);
        } catch (\Exception $e) {
            return json_encode(['output'=>[], 'selected'=>$selected]);;
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
            $request = $this->_restRajal->get('allow/get-data-obat?obatalkes_id='. $obatalkes_id.'&ruangan_id='. $ruangan_id);
            $body = json_decode($request->getBody(),TRUE);
            $response = $body['response'];
            return $response;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function actionGetNilaiKonversi()
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $get = $request->get();
        $satuanbesar_id = $get['satuanbesar_id'];
        $obatalkes_id = $get['obatalkes_id'];
        try {
            $request = $this->_restRajal->get('allow/get-konversi?obatalkes_id='. $obatalkes_id.'&satuanbesar_id='. $satuanbesar_id);
            $body = json_decode($request->getBody(),TRUE);
            $response = $body['response'];
            return $response;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function actionGetSatuan()
    {
        return $this->helper->guzzleExec($this->_restApotek, [
            'url' => 'transaksi-resep/list-ampuls',
            'payload' => [
                'query' => [
                    'obatalkes_id' => Yii::$app->request->get('obatalkes_id')
                ]
            ],
            'returnResponse' => true
        ]);
    }

    public function actionGetListSigna()
    {
        return $this->helper->guzzleExec($this->_restRajal, [
            'url' => 'allow/get-list-signa',
            'payload' => [
                'query' => Yii::$app->request->get()
            ],
            'returnResponse' => true
        ]);
    }

    public function actionGetMasterUnit()
    {
        return $this->helper->guzzleExec($this->_restRajal, [
            'url' => 'allow/get-master-unit',
            'payload' => [
                'query' => Yii::$app->request->get()
            ],
            'returnResponse' => true
        ]);
    }

    public function actionGetTemplateResep()
    {
        return $this->helper->guzzleExec($this->_restRajal, [
            'url' => 'allow/get-template-resep',
            'payload' => [
                'query' => Yii::$app->request->get()
            ],
            'returnResponse' => true
        ]);
    }

    public function actionGetTemplateResepDetail()
    {
        return $this->helper->guzzleExec($this->_restRajal, [
            'url' => 'allow/generate-template',
            'payload' => [
                'query' => Yii::$app->request->get()
            ],
            'returnResponse' => true
        ]);
    }

    public function actionGetListHistoryResep()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $draw = $request->get('draw', 1);
        $pasienId = $request->get('pasien_id', null);

        $getData = $this->helper->guzzleExec($this->_restRajal, [
            'url' => 'allow/list-history-resep',
            'payload' => [
                'query' => [
                    'pasien_id' => $pasienId,
                    'start' => $request->get('start', 0),
                    'length' => $request->get('length', 10)
                ]
            ]
        ]);

        return [
            'data' => $getData['data'],
            'draw' => $draw,
            'recordsTotal' => $getData['_meta']['totalCount'],
            'recordsFiltered' => $getData['_meta']['totalCount']
        ];
    }

    public function actionGetDataSelect2()
    {
        return $this->guzzleExec($this->_restMaster, [
            'url' => 'obat-alkes/get-data-select2',
            'payload' => [
                'query' => Yii::$app->request->get()
            ],
            'returnResponse' => true
        ]);
    }

    /**
     * transform and get additional filters (temporary)
     * this function can be edit
     */
    private function getAdditionalFilters($data = [])
    {
        if (isset($data['obatalkes_id']) && !empty($data['obatalkes_id'])) {
            return ['obatalkes_id' => $data['obatalkes_id']];
        }

        return [];
    }
}
