<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-13 11:30:26
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-10-16 13:05:31
 */

namespace app\modules\bedah\components\traits;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\components\DocoHelpers;
use Doco\bedah\models\IntraItemOperasiForm;

trait ApiTrait
{
    public function actionGetPegawai()
    {
        $term = !empty($_GET['q']['term']) ? $_GET['q']['term'] : '';
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        $ruangan_id = !empty($_GET['ruangan_id']) ? $_GET['ruangan_id'] : $ruangan_id;
        $response = $this->_restBedah->request('POST', 'allow/get-pegawai', [
            'form_params' => ['term' => $term , 'ruangan_id' => $ruangan_id],
        ]);
        $body = json_decode($response->getBody(), true);
        $data = [];
        foreach ($body['response'] as $key => $value) {
            $data[] = ['id' => $value['pegawai_id'], 'text' => $value['nama_pegawai']];
        }
        $total = count($body['response']);
        $return = ['result' => $data, 'total_count' => $total, 'incomplete_results' => false];
        return DocoHelpers::response($return);
    }
    public function actionGetTindakan()
    {
        if (isset($_GET['q']['term']) && !empty($_GET['q']['term'])) {
            $penjamin_id = $_GET['penjamin'];
            $kelaspelayanan_id = $_GET['kelaspelayanan'];
            $typeReq = Yii::$app->request->get('typeReq', null);
            $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
            $response = $this->_restBedah->request('POST', 'allow/get-tindakan', [
                'form_params' => ['term' => $_GET['q']['term'], 'ruangan_id' => $ruangan_id, 'penjamin_id' => $penjamin_id, 'kelaspelayanan_id' => $kelaspelayanan_id, 'typeReq' => $typeReq],
            ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $value) {
                $data[] = [
                    'id' => $value['daftartindakan_id'],
                    'text' => $value['daftartindakan_nama'],
                    'harga_tariftindakan' => $value['harga_tariftindakan'],
                    'persencyto_tindakan' => $value['persencyto_tindakan'],
                    'golonganoperasi_id' => $value['kelompokpemeriksaanlab_id'],
                    'operasi_id' => $value['pemeriksaanlab_id'],
                    'operasi_nama' => $value['pemeriksaanlab_nama'],
                    'golonganoperasi_nama' => $value['nama_kelompok'],
                    'daftartindakan_id' => $value['daftartindakan_id'],
                    'daftartindakan_nama' => $value['daftartindakan_nama'],
                ];
            }
            $total = count($body['response']);
            $return = ['result' => $data, 'total_count' => $total, 'incomplete_results' => false];
            return DocoHelpers::response($return);
        }
    }
    public function actionGetJenisOperasi()
    {
        // Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restBedah->get('allow/list-jenis-operasi', ['query' => ['daftartindakan_id' => $parent_label]]);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $key => $value)
                $result['output'][] = [
                    'id' => $value['golonganoperasi_id'],
                    'name' => $value['golonganoperasi_nama'],
                    'operasi_id' => $value['operasi_id']
                ];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }
    public function actionGetJenisAlat()
    {
        if (isset($_GET['q']['term']) && !empty($_GET['q']['term'])) {
            $response = $this->_restBedah->post(
                'allow/get-jenis-alat',
                [
                    'form_params' => [
                        'obatalkes_nama' => $_GET['q']['term']
                    ]
                ]
            );
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = [
                    'id' => $value['obatalkes_id'],
                    'text' => $value['obatalkes_nama'],
                ];
            }
            $total = count($body['response']);
            $return = ['result' => $data, 'total_count' => $total, 'incomplete_results' => false];
            return DocoHelpers::response($return);
        }
    }
    public function actionGetDaftarTindakan()
    {
        if (isset($_GET['q']['term']) && !empty($_GET['q']['term'])) {
            $response = $this->_restBedah->request('POST', 'allow/get-daftar-tindakan', [
                'form_params' => ['daftartindakan_nama' => $_GET['q']['term']],
            ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = [
                    'id' => $value['daftartindakan_id'],
                    'text' => $value['daftartindakan_nama'],
                ];
            }
            $total = count($body['response']);
            $return = ['result' => $data, 'total_count' => $total, 'incomplete_results' => false];
            return DocoHelpers::response($return);
        }
    }
    public function actionGetTindakanTarif()
    {
        $request = Yii::$app->request;
        $get = $request->get(); // parsing multiple data custom
        $api = 'allow/get-data-tindakan';     // api get data
        $data_id = 'tariftindakan_id';         // get id data - fungsi untuk label id
        $data_name = [                    // get value name untuk dropdown - fungsi untuk label nama (MAX 4 DATA)
            'daftartindakan_nama'
        ];
        $getRest = $this->_restBedah;     // init master api
        return DocoHelpers::paginationSelec2($api, $data_id, $data_name, $getRest, $get);
    }

    public function actionGetDokter()
    {
        if (isset($_GET['q']['term']) && !empty($_GET['q']['term'])) {
            $response = $this->_restBedah->request('POST', 'allow/get-dokter', [
                'form_params' => ['nama_pegawai' => $_GET['q']['term']],
            ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = [
                    'id' => $value['pegawai_id'],
                    'text' => $value['nama_pegawai'],
                ];
            }
            $total = count($body['response']);
            $return = ['result' => $data, 'total_count' => $total, 'incomplete_results' => false];
            return DocoHelpers::response($return);
        }
    }
    public function actionGetBmhp($ruangan_id, $instalasi_id)
    {
        if (isset($_GET['q']['term']) && !empty($_GET['q']['term'])) {
            $response = $this->_restBedah->request('POST', 'allow/get-bmhp', [
                'form_params' => ['obatalkes_nama' => $_GET['q']['term'], 'ruangan_id' => $ruangan_id, 'instalasi_id' => $instalasi_id]
            ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = [
                    'id' => $value['obatalkes_id'],
                    'text' => $value['obatalkes_nama'],
                    'qty_tersedia' => $value['qty_tersedia'],
                ];
            }
            $total = count($body['response']);
            $return = ['result' => $data, 'total_count' => $total, 'incomplete_results' => false];
            return DocoHelpers::response($return);
        }
    }
    public function saveToCache($cacheName, $data)
    {
        $cache = Yii::$app->session;
        $cacheData = $cache->get($cacheName);
        if (count($data) < 1) {
            return true;
        }
        if ($cacheData == false) {
            if (!isset($data[0])) {
                $data = [$data];
            }
            $cache->set($cacheName, $data);
        } else {
            $arr = $cacheData;
            if (isset($data[0])) {
                $arr = array_merge($arr, $data);
            } else {
                array_push($arr, $data);
            }
            $cache->set($cacheName, $arr);
        }
        return true;
    }
    public function updateCache($cacheName, $data, $key)
    {
        $cache = Yii::$app->session;
        $cacheData = $cache->get($cacheName);
        $explode = explode('-', $cacheName);

        if ($cacheData) {
            $cacheData[$key] = $data;
            $arr = $cacheData;
            $newArr = [];
            foreach ($arr as $value) {
                $newArr[] = $value;
            }
            $cache->set($cacheName, $newArr);

            if(isset($explode[0]) && $explode[0] == 'penggunaanbmhp'){
                $obatAlkesId = isset($cacheData[$key]['obatalkes_id']) ? $cacheData[$key]['obatalkes_id'] : 0;
                $listPersediaan = [];
                foreach($cacheData as $v){
                    $tmpObatAlkesId = isset($v['obatalkes_id']) ? $v['obatalkes_id'] : 0;
                    if($obatAlkesId == $tmpObatAlkesId){
                        $listPersediaan[] = isset($v['persediaan']) ? $v['persediaan'] : 0;
                    }
                }
                if(!empty($listPersediaan)){
                    $maxPersediaan = max($listPersediaan);
                    $this->resetSisaStokBmhp($obatAlkesId, $cacheName, $maxPersediaan);
                }
            }
            
            return true;
        }
        return true;
    }
    
    public function actionGetCache($cacheName, $ruangan_id = null)
    {
        return Yii::$app->docoPlugin->execute($this,'get_cache');
    }

    public function actionEditTindakanOperasi($cacheName= null, $ruangan_id = null)
    {
        return Yii::$app->docoPlugin->execute($this,'edit_tindakan_operasi');
    }

    public function actionUnsetCache($key, $cacheName)
    {
        $cache = Yii::$app->session;
        $cacheData = $cache->get($cacheName);
        $explode = explode('-', $cacheName);
        
        if ($cacheData) {
            if (isset($explode[0]) && $explode[0] == 'pegawaioperasi') {
                $explodeKey = explode('-', $key);
                $key = $explodeKey[0];
                $keyPo = !empty($explodeKey[1]) ? $explodeKey[1] : null;
                $this->unsetCachePegawaiOperasi($key, $cacheName, $keyPo); 
            }else{
                if (isset($explode[0]) && $explode[0]  == 'itemoperasi') {
                    $daftartindakan_id = !empty($cacheData[$key]['daftartindakan_id']) ? $cacheData[$key]['daftartindakan_id'] : null;
                    $this->unsetCacheBmhp($daftartindakan_id, 'penggunaanbmhp-' . $explode[1] . '-' . $explode[2]);

                    $cacheNamePo = 'pegawaioperasi-'. $explode[1] . '-' . $explode[2];
                    $keyPo = $daftartindakan_id;
                    $cacheNamePo = 'pegawaioperasi-'. $explode[1] . '-' . $explode[2];
                    $this->unsetCachePegawaiOperasi(null, $cacheNamePo, $keyPo); 
                }
                $arr = $cacheData;
                
                $deletedCacheName = 'deleted-' . $cacheName;
                $this->saveToCache($deletedCacheName,  $arr[$key]);
                unset($arr[$key]);
                $newArr = [];
                foreach ($arr as $value) {
                    $newArr[] = $value;
                }
                $cache->set($cacheName, $newArr);

                if(isset($explode[0]) && $explode[0]  == 'penggunaanbmhp'){
                    $obatAlkesId = isset($cacheData[$key]['obatalkes_id']) ? $cacheData[$key]['obatalkes_id'] : 0;
                    $listPersediaan = [];
                    foreach($cacheData as $v){
                        $tmpObatAlkesId = isset($v['obatalkes_id']) ? $v['obatalkes_id'] : 0;
                        if($obatAlkesId == $tmpObatAlkesId){
                            $listPersediaan[] = isset($v['persediaan']) ? $v['persediaan'] : 0;
                        }
                    }
                    if(!empty($listPersediaan)){
                        $maxPersediaan = max($listPersediaan);
                        $this->resetSisaStokBmhp($obatAlkesId, $cacheName, $maxPersediaan);
                    }
                }
                
            }
            return DocoHelpers::response(['response' => ['title' => 'Proses berhasil!', 'text' => 'Data berhasil dihapus']]);
        }
        return DocoHelpers::response(['response' => ['title' => 'Proses berhasil!', 'text' => 'Data berhasil dihapus']]);
    }

    public function actionUnsetBmhpTindakan($key, $cacheName){
        return $this->unsetCacheBmhp($key, $cacheName);
    }
    
    public function unsetCacheBmhp($daftartindakan_id, $cacheName)
    {
        $cache = Yii::$app->session;
        $cacheData = $cache->get($cacheName);
        if ($cacheData) {
            $listPersediaan = [];
            foreach ($cacheData as $k => $v) {
                $obatAlkesId = isset($v['obatalkes_id']) ? $v['obatalkes_id'] : 0;
                if(!empty($obatAlkesId)){
                    $listPersediaan[$obatAlkesId][]=  isset($v['persediaan']) ? $v['persediaan'] : 0;
                }
                if (isset($v['daftartindakan_id']) && $v['daftartindakan_id'] == $daftartindakan_id) {
                    $deletedCacheName = 'deleted-' . $cacheName;
                    $this->saveToCache($deletedCacheName, $cacheData[$k]);
                    unset($cacheData[$k]);
                }
            }
            
            $newArr = [];
            foreach ($cacheData as $key => $value) {
                $newArr[] = $value;
            }
            $cache->set($cacheName, $newArr);
            /**blok reset persediaan dan stok */
            foreach($listPersediaan as $k => $v){
                $maxPersediaan = max($v);
                
                $this->resetSisaStokBmhp($k, $cacheName, $maxPersediaan);
            }
            return true;
        }
        return true;
    }


    public function unsetCachePegawaiOperasi($key, $cacheName, $keyPo){
        $cache = Yii::$app->session;
        $cacheData = $cache->get($cacheName);

        if ($cacheData && !empty([$keyPo])) {
            /** Untuk menghapus seluruh pegawai yang nempel di tindakan */
            if(is_null($key)){
                if(!empty($cacheData[$keyPo])){
                    unset($cacheData[$keyPo]);
                }
            }else if ($key >= 0 && !is_null($key)){
                /** Untuk menghapus pegawai item */
                if(!empty($cacheData[$keyPo][$key])){
                    $deletedCacheName = 'deleted-' . $cacheName;
                    $this->saveToCache($deletedCacheName, $cacheData[$keyPo][$key]);
                    unset($cacheData[$keyPo][$key]);
                    $newArr = [];
                    foreach ($cacheData[$keyPo] as $keys => $value) {
                        $newArr[$keyPo][] = $value;
                    }
                    
                    $cacheData[$keyPo] = !empty($newArr[$keyPo]) ? $newArr[$keyPo] : [];
                }
            }
            $cache->set($cacheName, $cacheData);

            $cacheData = $cache->get($cacheName);
            return true;
        }
        return true;

    }

    public function updateCachePegawaiOperasi($cacheName, $data, $key, $keyPo){
        $cache = Yii::$app->session;
        $cacheData = $cache->get($cacheName);

        if ($cacheData && !empty([$keyPo])) {
            if(!empty($cacheData[$keyPo][$key])){
                $cacheData[$keyPo][$key] = $data;
            }
            $cache->set($cacheName, $cacheData);
            return true;
        }
        return false;

    }

    public function actionGetView($type, $id)
    {
        $request = Yii::$app->request;
        $result = [];
        $data = [];
        $draw = $request->get('draw', 1);
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        $pegawaiOperasi = [];
        try {
            $response = $this->helper->guzzleExec($this->_restBedah, [
                'method' => 'GET',
                'url' => 'intra-operasi/get-cache-data',
                'payload' => [
                    'query' => [
                        'name' => $type,
                        'penunjangid' => $id
                    ],
                ]
            ]);
            
            if (!isset($response['data']) || empty($response['data'])) {
                return $this->helper->response($result);
            }
            if($type == "itemoperasi"){
                $pegawaiOperasi = $this->helper->guzzleExec($this->_restBedah, [
                    'method' => 'GET',
                    'url' => 'intra-operasi/get-cache-data',
                    'payload' => [
                        'query' => [
                            'name' => 'pegawaioperasi',
                            'penunjangid' => $id,
                        ],
                    ]
                ]);
                
                $pegawaiOperasi = !empty($pegawaiOperasi['data']) ? $pegawaiOperasi['data'] : [];
            }
            $no = 0;
            $groupTmp = [];
            foreach ($response['data'] as $key => $value) {
                $daftartindakan_id = !empty($value['daftartindakan_id']) ? $value['daftartindakan_id'] : null;
                if(!empty($pegawaiOperasi)) {       
                    $value['tim_operasi'] = json_encode($pegawaiOperasi[$daftartindakan_id]);
                    $value['str_null'] = "";
                    $daftartindakan_nama = isset($value['daftartindakan_nama']) ? $value['daftartindakan_nama'] : null;
                    $kegiatanoperasi_nama = isset($value['kegiatanoperasi_nama']) ? $value['kegiatanoperasi_nama'] : "-";
                    $is_cyto = isset($value['cyto']) ? $value['cyto'] : false;
                    $is_penyulit = isset($value['penyulit']) ? $value['penyulit'] : false;
                    $strCyto = $is_cyto ? " - CITO : <span>&#10003;</span>" : "";
                    $strPenyulit = $is_penyulit  ? " - PENYULIT : <span>&#10003;</span>" : "";
                    $value['kegiatan_operasi_header'] = "TINDAKAN OPERASI : <b>" . $daftartindakan_nama . $strCyto . $strPenyulit."</b>";
                }
                $no++;
                $value['rowNum'] = $no;
                if ($type == 'pelayanan-operasi-view') {
                    $value['cyto'] = ($value['cyto_tindakan']) ? '✓' : '';
                } else if ($type == 'bmhp-operasi-view') {
                    $value['ditagihkan'] = ($value['is_ditagihkan']) ? '✓' : '';
                } else if ($type == 'pasang-infus-view') {
                    $value['tgl_pemasangan'] = date('d M Y H:i:s', strtotime($value['tgl_pemasangan']));
                }
                if (!isset($groupTmp[$daftartindakan_id]) && $type != 'penggunaanbmhp') {
                    $groupTmp[$daftartindakan_id] = true;
                    $data[] = $value;
                } else if (in_array($type, ['penggunaanbmhp', 'tindakanluarbedah'])) {
                    $data[] = $value;
                }
            }
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = count($data);
            $result['recordsTotal'] = count($data);
            return DocoHelpers::response($result);
        } catch (Exception $e) {
            return DocoHelpers::response($result);
        }
    }

    /**
     * API get tindakan di luar bedah
     * 
     * @return JSON
     * @author : Tsani Nashrullah (tsani@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionTindakan()
    {
        return $this->guzzleExec($this->_restBedah, [
            'url' => 'allow/tindakan',
            'payload' => [
                'query' => Yii::$app->request->get('payload', [])
            ],
            'returnResponse' => true
        ]);
    }

    /**
     * get instrumen list
     * 
     * @return Array
     * @author : Rizqi Fitrianto (rizqi@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionGetInstrumen()
    {
        return $this->helper->guzzleExec(
            $this->_restBedah,
            [
                'url' => 'allow/get-instrumen',
                'method' => 'GET',
                'returnResponse' => true
            ],
            [
                'query' => Yii::$app->request->get('payload', [])
            ]
        );
    }

    public function actionUnsetPage()
    {
        $getCache = Yii::$app->cache->get('bedah-used-page');
        $url = parse_url(Yii::$app->request->referrer);
        if (!isset($url['path']) || !isset($url['query'])) {
            return true;
        }
        $path = $url['path'] . '?' . $url['query'];
        $getCache = Yii::$app->cache->get('bedah-used-page');
        if (isset($getCache[$path])) {
            unset($getCache[$path]);
            Yii::$app->cache->set('bedah-used-page', $getCache);
        }
        Yii::error([
            'message' => 'ur cache is here',
            'cachedata' => Yii::$app->cache->get('bedah-used-page')
        ]);
        return true;
    }

    /**
     * API get tindakan di luar bedah
     * 
     * @return JSON
     * @author : Budi (budi@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionKegiatan()
    {
        return $this->guzzleExec(
            $this->_restBedah,
            [
                'url' => 'allow/kegiatan',
                'method' => 'GET',
                'returnResponse' => true,
            ],
            [
                'query' => Yii::$app->request->get('payload', [])
            ]
        );
    }

    public function actionTindakanOperasi()
    {
        return $this->guzzleExec($this->_restBedah, [
            'url' => 'allow/tindakan-kegiatan-operasi',
            'payload' => [
                'query' => Yii::$app->request->get('payload', [])
            ],
            'returnResponse' => true
        ]);
    }

    public function actionGolongan()
    {
        return $this->guzzleExec(
            $this->_restBedah,
            [
                'url' => 'allow/golongan',
                'method' => 'GET',
                'returnResponse' => true,
            ],
            [
                'query' => Yii::$app->request->get('payload', [])
            ]
        );
    }

    public function actionCheckCacheItemOperasi($cacheName, $id){
        $result['is_exist'] = $this->checkExistCache($cacheName, $id, 'daftartindakan_id');
        $customError = [
            'daftartindakan_id' => [
                'Tindakan Sudah diinputkan.',
            ],
        ];
        if($result['is_exist']){
            $customFormName = substr(strrchr(get_class(new IntraItemOperasiForm), "\\"), 1);;
            $errors = DocoHelpers::parseError($customError, $customFormName);
            return DocoHelpers::responseTemplate(422, 'Error', $errors);
        }
        return DocoHelpers::response($result);
    }

    public function checkExistCache($cacheName, $id, $uniqueField){
        $cache = Yii::$app->session;
        $cacheData = $cache->get($cacheName);
        $is_exist = false;
        if(!empty($cacheData)){
            foreach($cacheData as $val){
                $idCache = !empty($val[$uniqueField]) ? $val[$uniqueField] : '';
                if($idCache == $id){
                    $is_exist = true;
                    break;
                }
            }
        }

        return $is_exist;
    }

    public function actionResetCache(){
        $request = Yii::$app->request;
        $cache = Yii::$app->session;
        $payload = $request->post('payload', []);
        $payload = json_decode($payload, true);
        if(!empty($payload)){
            foreach($payload as $val){
                $cacheName = !empty($val['cacheName']) ? $val['cacheName'] : '';
                $cacheData = !empty($val['cacheData']) ? $val['cacheData'] : [];
                if(!empty($cacheData) && !empty($cacheData)){
                    $cache->set($cacheName, $cacheData);
                }
            }
        }
        return true;
    }

    private function resetSisaStokBmhp($obatalkes_id, $cacheNameBmhp, $newStokUtama){
		$cache = Yii::$app->session;
		$cacheBmhp = $cache->get($cacheNameBmhp);
		$sisa = [];
		if(!empty($obatalkes_id) && !empty($cacheBmhp)){
			$qty_tersedia = $newStokUtama;
			foreach ($cacheBmhp as $key => $val){
				$tmp_obatalkes_id = !empty($val['obatalkes_id']) ? $val['obatalkes_id'] : null;
				$tmp_terpakai = !empty($val['terpakai']) ? $val['terpakai'] : 0;
				if($obatalkes_id == $tmp_obatalkes_id){
					$cacheBmhp[$key]['persediaan'] = $qty_tersedia;
					$tmp_sisa = $qty_tersedia - $tmp_terpakai;
					$qty_tersedia = $tmp_sisa;
					$cacheBmhp[$key]['sisa'] = $tmp_sisa;
					$sisa[] = $tmp_sisa;
				}
			}
			$cache->set($cacheNameBmhp, $cacheBmhp);
		}
		return $sisa;
	}

    public function actionListBmhp()
    {
        $request = Yii::$app->request;
        $ruangan_id = $request->get('ruangan_id', null);
        $kelaspelayanan_id = $request->get('kelaspelayanan_id', null);
        $penjamin_id = $request->get('penjamin_id', null);
        $jenis = $request->get('jenis', null);
        $keyword = $request->get('term', null);
        $page = $request->get('page', 1);
        $params = [
            'instalasi_id' => $request->get('instalasi_id', null),
            'ruangan_id' => $ruangan_id,
            'penjamin_id' => $penjamin_id,
            'kelaspelayanan_id' => $kelaspelayanan_id,
            'group_jenisobat' => $jenis,
            'page' => $page,
            'keyword' => $keyword,
            'get_konfig_stok' => false,
        ];

        $response= $this->guzzleExec(Yii::$app->docoRest->apotek, [
            'url' => 'allow/get-list-stok-apotek',
            'payload' => [
                'query' => $params
            ]
        ]);
        return $this->responseJson(200, 'Data berhasil diambil', $response['data']);
    }
}
