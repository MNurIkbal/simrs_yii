<?php

/**
 * @author : zen
 * Powered by Sirs
 */

namespace app\extensions\bedah;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use yii\base\DynamicModel;

use Doco\bedah\models\IntraOperasiForm;
use Doco\bedah\models\IntraPegawaiOperasiForm;
use Doco\bedah\models\IntraItemOperasiForm;
use Doco\bedah\models\IntraPenggunaanCairanForm;
use Doco\bedah\models\IntraAlatDitubuhForm;
use Doco\bedah\models\IntraPemeriksaanPelengkapForm;
use Doco\bedah\models\IntraKonsulTindakanForm;
use Doco\bedah\models\IntraPenggunaanBmhpForm;
use Doco\bedah\models\IntraTindakanLuarBedahForm;
use app\modules\bedah\components\traits\ApiTrait;

class GetCacheKramat extends \app\components\DocoBaseProcessExtension
{
    use ApiTrait;

    protected function processFlow($controller, $key = null, $unique = null)
    {
        $this->_restBedah = Yii::$app->docoRest->bedahsentral;
        $cacheName = Yii::$app->request->get('cacheName', null);
        $cache = Yii::$app->session;
        $cacheData = $cache->get($cacheName);
        $request = Yii::$app->request;
        $daftarTindakanId = Yii::$app->request->get('id', null);
        $ruangan_id = $request->get('ruangan_id', null);
        $result = [];
        $data = [];
        $draw = $request->get('draw', 1);
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        $explodeCache = explode('-', $cacheName);
        if (!$cacheData && (isset($explodeCache[0]) && ($explodeCache[0] != '' || $explodeCache[0] != null)) && (isset($explodeCache[2]) && ($explodeCache[2] != '' || $explodeCache[2] != null)) && $draw <= 1) {
            /** Penyesuaian pegawai operasi supaya:
             * Tidak manggil ke db lagi ketika di halaman tambah tindakan operasi
             * karena jika hasil array cache nya kosong bisa jadi pegawainya sudah dihapus, kalo manggil dari db maka cache nya akan kebentuk lagi.
             */
            if($explodeCache[0] != 'pegawaioperasi'){
                $getDataCache = DocoHelpers::guzzleExec($this->_restBedah, [
                    'url' => 'intra-operasi/get-cache-data',
                    'method' => 'GET'
                ], [
                    'query' => [
                        'name' => $explodeCache[0],
                        'penunjangid' => DocoHelpers::decrypt($explodeCache[2])
                    ]
                ]);
                if (!isset($getDataCache['data']) || !$getDataCache['data']) {
                    return DocoHelpers::response($result);
                }
                $cache->set($cacheName, $getDataCache['data']);
                $cacheData = $getDataCache['data'];
            }
        }
        if (empty($cacheData)) {
            return DocoHelpers::response($result);
        }
        $no = 0;

        if ($explodeCache[0] == 'pegawaioperasi') {
            if(isset($cacheData[$daftarTindakanId])){
                if(empty($cacheData[$daftarTindakanId])){
                    return DocoHelpers::response($result);
                }else{
                    $cacheData = $cacheData[$daftarTindakanId];
                }
            }else{
                return DocoHelpers::response($result);
            }
        }

        $listKegiatanData = [];
        $cachePegawaiOperasi =[];
        if ($explodeCache[0] == 'itemoperasi') {
            //*kebutuhan mapping ke item Operasi
            $cachePegawaiOperasiName = 'pegawaioperasi-' . $explodeCache[1] . '-' . $explodeCache[2];  
            $cachePegawaiOperasi = $cache->get($cachePegawaiOperasiName);
            
            if(!empty($cacheData)) {
                foreach ($cacheData as $key => $value) {
                    if(!empty($value['daftartindakan_id'])){
                        $listKegiatanData[$value['daftartindakan_id']] = [
                            'kegiatanoperasi_nama' => isset($value['kegiatanoperasi_nama']) ? $value['kegiatanoperasi_nama'] : ''
                        ];
                    }
                }
            }
        }

        $listKegiatan = [];
        if(!empty($cachePegawaiOperasi)) {
            foreach ($cachePegawaiOperasi as $key => $value) {
                if(is_array($value)) {
                    foreach ($value as $k => $val) {
                        $kegiatanoperasi_nama = null;
                        if(isset($val['daftartindakan_id'])) {
                            if(!empty($val['kegiatanoperasi_nama'])) {
                                $kegiatanoperasi_nama = $val['kegiatanoperasi_nama'];
                            }
                            elseif(!empty($listKegiatanData[$val['daftartindakan_id']]['kegiatanoperasi_nama'])) {
                                $kegiatanoperasi_nama = $listKegiatanData[$val['daftartindakan_id']]['kegiatanoperasi_nama'];
                            }
                        }

                        $listKegiatan[$val['daftartindakan_id']] = [
                            'kegiatanoperasi_nama' => $kegiatanoperasi_nama
                        ];
                    }
                }
            }
        }
        
        foreach ($cacheData as $key => $value) {
            $no++;
            $value['rowNum'] = $no;
            $value['aksi'] = '';
            $daftartindakan_id = isset($value['daftartindakan_id']) ? $value['daftartindakan_id'] : null;
            if ($explodeCache[0] == 'penggunaanbmhp') {
                // if (isset($value['msg']) && $value['msg'] == '') {
                    $value['aksi'] .= '<a href="' . Url::to(['intra-tambah-penggunaan-bmhp', 'key' => $key, 'unique' => $cacheName, 'ruangan_id' => $ruangan_id]) . '" class="btn btn-info btn-xs" data-toggle="modal" data-target="#modal_backdrop"><i class="fa fa-edit"></i></a>';
                // }
                $value['aksi'] .= ' <button class="btn btn-danger btn-xs delete-item" data-key="' . $key . '" data-cache="' . $cacheName . '"><i class="fa fa-trash"></i></button>';
            } else if ($explodeCache[0] == 'itemoperasi') {
                $value['rowNum'] = "";
                $daftartindakan_nama = isset($value['daftartindakan_nama']) ? $value['daftartindakan_nama'] : null;
                $value['kegiatanoperasi_nama'] = isset($listKegiatan[$daftartindakan_id]['kegiatanoperasi_nama']) ? $listKegiatan[$daftartindakan_id]['kegiatanoperasi_nama'] : '-';
                $is_cyto = isset($value['is_cyto']) ? $value['is_cyto'] : false;
                $is_penyulit = isset($value['penyulit']) ? $value['penyulit'] : false;
                $strCyto = $is_cyto ? " - CITO : <span>&#10003;</span>" : "";
                $strPenyulit = $is_penyulit  ? " - PENYULIT : <span>&#10003;</span>" : "";
                $pegawaiOperasi = !empty($cachePegawaiOperasi[$daftartindakan_id]) ? $cachePegawaiOperasi[$daftartindakan_id] : [];
                $value['tim_operasi'] = json_encode($pegawaiOperasi, true);
                $value['kegiatan_operasi_header'] = "TINDAKAN OPERASI : <b>" . $daftartindakan_nama . $strCyto . $strPenyulit . "</b>";
                $value['str_null'] = "";
                if (!isset($value['default'])) {
                    $value['default'] = 0;
                }
                if (!isset($value['pegawai_input'])) {
                    $value['pegawai_input'] = "";
                }
                if ($value['default'] == 0) {
                    $value['aksi'] .= ' <button class="btn btn-danger btn-sm delete-item" data-key="' . $key . '" data-cache="' . $cacheName . '"><i class="fa fa-trash" data-toggle="modal" data-target="#modal_backdrop"></i></button>';
                } else {
                    // $value['aksi'] .= '<a href="' . Url::to(['intra-tambah-item-operasi', 'key' => $key, 'unique' => $cacheName]) . '" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modal_backdrop"><i class="fa fa-edit"></i></a>';
                    $value['aksi'] .= ' <button class="btn btn-danger btn-sm delete-item" data-key="' . $key . '" data-cache="' . $cacheName . '"><i class="fa fa-trash" data-toggle="modal" data-target="#modal_backdrop"></i></button>';
                }
            } else if ($explodeCache[0] == 'pegawaioperasi') {
                $value['aksi'] .= ' <button class="btn btn-danger btn-sm delete-item" data-key="' . $key . '-' . $daftartindakan_id . '" data-cache="' . $cacheName . '"><i class="fa fa-trash"></i></button>';
                $value['aksi'] .= ' <button class="btn btn-primary btn-sm" data-key="' . $key . '-' . $daftartindakan_id . '" data-cache="' . $cacheName . '"><i class="fa fa-pencil"></i></button>';
            } else {
                $value['aksi'] .= ' <button class="btn btn-danger btn-sm delete-item" data-key="' . $key . '" data-cache="' . $cacheName . '"><i class="fa fa-trash"></i></button>';
                $value['aksi'] .= ' <button class="btn btn-primary btn-sm" data-key="' . $key . '" data-cache="' . $cacheName . '"><i class="fa fa-pencil"></i></button>';
            }

            $data[$key] = $value;
        }
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = count($data);
        $result['recordsTotal'] = count($data);
        return DocoHelpers::response($result);
    }    
}