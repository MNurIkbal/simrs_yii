<?php

namespace Doco\models\bpjs;

use Yii;
use app\modules\v1\models\Lookup;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\Services\ApiBPJSLZString;
use Doco\models\KlasifikasiKamarV;
use Doco\models\KamarTempatTidur;
use Doco\models\KamarRuangan;
use yii\helpers\ArrayHelper;

class BpjsAplicare extends Bpjs
{
    public $url_aplicare;

    public function init()
    {
        parent::init();
        
        $cache = Yii::$app->cache;
        $cache_bpjs = $cache->get(DocoConstants::LOOKUP_BPJS);
        if ($cache_bpjs) {
            foreach ($cache_bpjs as $each) {
                if ($each['lookup_name'] == 'url_aplicare') {
                    $this->url_aplicare = $each['lookup_value'];
                }
            }
        }
    }

    /**
     * Output from CURL
     * @param mixed $data;
     * @return string JSON
     */
    protected static function out($data)
    {
        $outPut = $data && is_string($data) && json_decode($data) ? json_decode($data, true) : [];
        if (!empty($outPut['response']) && self::$version >= 1.1) {
            $response = $outPut['response'];
            $keyEncrypt = self::$cons_ids . self::$secret_keys . self::getTimestamp();
            if(is_string($response)){
                $outPut['response'] = (new ApiBPJSLZString)->decryptWithDecompress($keyEncrypt,$response);
            }else{
                $outPut['response'] = $response;
            }
        }
        return $outPut;
    }

    public function referensiKamarAplicare()
    {
        $param = 'ref/kelas';
        $full_url = $this->url_aplicare.$param;
        $get_curl = self::curl($full_url, false, self::getHeader(true), 'GET');
        return self::out($get_curl);
    }

    public function createOrUpdateAplicare($id, $type)
    {
        $payload = $this->setPayloadBpjsAplicare($id);
        if (ArrayHelper::getValue($payload, 'kodekelas') && !is_null(ArrayHelper::getValue($payload, 'koderuang'))) {
            $json_data = is_array($payload) ? json_encode($payload) : json_encode(array());
            $param = $type == DocoConstants::TYPE_CREATE_APLICARE ? 'bed/create/' : 'bed/update/';
            $param = $param. rawurlencode($this->ppkPelayanan);
            $full_url = $this->url_aplicare.$param;
            $get_curl = self::curl($full_url, $json_data, self::getHeader(false), 'POST');
            return self::out($get_curl);
        }
    }

    public function deleteAplicare($id)
    {
        $dataKlasifikasiKamar = KlasifikasiKamarV::find()->where(['kamarruangan_id' => $id])->asArray()->one();
        $payload = [
            'kodekelas' => ArrayHelper::getValue($dataKlasifikasiKamar, 'kodekelas_aplicare', NULL),
            'koderuang' => ArrayHelper::getValue($dataKlasifikasiKamar, 'kamarruangan_kode', NULL),
        ];

        $param = 'bed/delete/';
        $json_data = is_array($payload) ? json_encode($payload) : json_encode(array());
        
        $param = $param. rawurlencode($this->ppkPelayanan);
        $full_url = $this->url_aplicare.$param;
        
        $get_curl = self::curl($full_url, $json_data, self::getHeader(false), 'POST');
        return self::out($get_curl);
    }

    public function deleteKamarNonAktifAplicare($dataKlasifikasiKamar)
    {
        $payload = [
            'kodekelas' => ArrayHelper::getValue($dataKlasifikasiKamar, 'kodekelas_aplicare', NULL),
            'koderuang' => ArrayHelper::getValue($dataKlasifikasiKamar, 'kamarruangan_kode', NULL),
        ];

        $param = 'bed/delete/';
        $json_data = is_array($payload) ? json_encode($payload) : json_encode(array());
        
        $param = $param. rawurlencode($this->ppkPelayanan);
        $full_url = $this->url_aplicare.$param;
        
        $get_curl = self::curl($full_url, $json_data, self::getHeader(false), 'POST');
        return self::out($get_curl);
    }

   /** payload integrasi bpjs */
   private function setPayloadBpjsAplicare($id)
   {
       $kapasitas = $tersedia = 0;
       $dataKlasifikasiKamar = KlasifikasiKamarV::find()->select([
           'kamarruangan_id', 'kodekelas_aplicare', 'kamarruangan_nokamar', 'ruangan_nama', 'kamarruangan_kode'
       ])->where(['kamarruangan_id' => $id])->asArray()->one();
       
       $kamarTempatTidur = KamarTempatTidur::find()
       ->join('join', 'kamarruangan_m', 'kamarruangan_m.kamarruangan_id = kamartempattidur_m.kamarruangan_id')
       ->where([
            'kamartempattidur_m.kamarruangan_id' => $id,
            'kamartempattidur_m.is_rekapkinerjaprofesi' => true,
            'kamartempattidur_m.is_active' => true
        ])
        ->andWhere([
            'kamarruangan_m.is_active' => true,
            'kamarruangan_m.is_deleted' => false
        ]);

       $kapasitas = $kamarTempatTidur->count();
       $tersedia = $kamarTempatTidur->andWhere([
            'kamartempattidur_m.is_terisi' => false,
            'kamartempattidur_m.kettempattidur_id' => DocoConstants::KET_TT_KSG_CMPR
        ])->count();

       $payload = [
           'kodekelas' => ArrayHelper::getValue($dataKlasifikasiKamar, 'kodekelas_aplicare', NULL),
           'koderuang' => ArrayHelper::getValue($dataKlasifikasiKamar, 'kamarruangan_kode', NULL),
           'namaruang' => ArrayHelper::getValue($dataKlasifikasiKamar, 'kamarruangan_nokamar', NULL),
           'kapasitas' => (string)$kapasitas,
           'tersedia' => (string)$tersedia,
           'tersediapria' => (string)0,
           'tersediawanita' => (string)0, 
           'tersediapriawanita' => (string)0
       ];
       return $payload;
   }

   public function readAplicare($start = 1, $limit = 5)
    {
        $param = 'bed/read/';
        $param = $param. rawurlencode($this->ppkPelayanan).'/'.$start.'/'.$limit;
        $full_url = $this->url_aplicare.$param;
        $get_curl = self::curl($full_url, false, self::getHeader(true), 'GET');
        return self::out($get_curl);
    }
}
