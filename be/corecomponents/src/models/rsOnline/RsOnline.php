<?php 
namespace Doco\models\rsOnline;

use Yii;
use app\modules\v1\models\Lookup;
use Doco\components\DocoConstants;
use Doco\Services\ApiBPJSLZString;
use Doco\components\RsonlineLog;
use Doco\models\KlasifikasiKamarV;
use Doco\models\KamarTempatTidur;
use Doco\models\TempatTidurView; 
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstansId;
use Doco\models\KetersediaanKamarFnDet; 
use Doco\models\KamarRuangan;

class RsOnline extends \yii\db\ActiveRecord
{
    public $rs_id;
    public $url;
    public $pass;

    static protected $x_rs_ids;
    static protected $x_pass;
    static protected $timestamp;

    static protected $version = 1.0;


    public function init() 
    {
        parent::init();

        $cache = Yii::$app->cache;
        $cache_rsonline = $cache->get(DocoConstants::LOOKUP_RS_ONLINE);
        if (!$cache_rsonline) {
            $lookup = Lookup::find()->where(['lookup_type'=>DocoConstants::LOOKUP_RS_ONLINE])->asArray()->all();
            $cache->set(DocoConstants::LOOKUP_RS_ONLINE, $lookup);
            $cache_rsonline = $cache->get(DocoConstants::LOOKUP_RS_ONLINE);
        }

        if ($cache_rsonline) {
            foreach($cache_rsonline as $each) 
            {
                if ($each['lookup_name'] == 'rs_id') {
                    $this->rs_id = $each['lookup_value'];
                }
                if ($each['lookup_name'] == 'pass') {
                    $this->pass = $each['lookup_value'];
                }
                if ($each['lookup_name'] == 'url') {
                    $this->url = $each['lookup_value'];
                }
            }
        }

        self::$x_rs_ids = $this->rs_id;
        self::$x_pass = $this->pass;
    }

    protected static function curl($url, $data, $header, $action = '')
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            if (!empty($action)) {
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $action);
            }
        }

        $res = curl_exec($curl);

        $curl_get_info = curl_getinfo($curl);
        $get_response_time = !empty($curl_get_info['total_time']) ? $curl_get_info['total_time'] : 0;

        if (!$res) {
            $errorArray = [
                'metaData'=>[
                    "code"=>500,
                    "message"=>curl_error($curl) ? : "Problem with bridging",
                ]
            ];
            $res = json_encode($errorArray);
        }
        curl_close($curl);
        RsonlineLog::saveLogRsOnline($url, $header, $data, $res);
        return $res;
    }

        /**
     * Output from CURL
     * @param mixed $data;
     * @return string JSON
     */
    protected static function out($data)
    {
        $outPut = $data && is_string($data) && json_decode($data) ? json_decode($data, true) : [];
        
        if (!empty($outPut['response']) && self::$version != 1) {
            $response = $outPut['response'];
            $keyEncrypt = self::$rs_id . self::getTimestamp();
            if(is_string($response)){
                $outPut['response'] = (new ApiBPJSLZString)->decryptWithDecompress($keyEncrypt,$response);
            }else{
                $outPut['response'] = $response;
            }
        }
        return $outPut;

    }

    /**
     * url baru untuk api http://sirs.kemkes.go.id/fo/index.php/Fasyankes
     * @param mixed $data;
     * @return HTTP Header
     */
    protected static function getHeader($form_url_encoded = false)
    {
        if ($form_url_encoded) {
            $conten_type = "Content-Type: application/x-www-form-urlencoded";
        } else {
            $conten_type = "Content-Type: application/json";
        }

        return [
            "X-rs-id:" . self::$x_rs_ids,
            "X-timestamp:" . self::getTimestamp(),
            "X-pass:" . self::$x_pass,
            $conten_type
        ];
    }

    protected static function getTimestamp()
    {
        $date_utc = new \DateTime("now", new \DateTimeZone("Asia/Jakarta"));
        $time_utc = $date_utc->format('Y-m-d H:i:s');
        if (empty(self::$timestamp)) {
            self::$timestamp = strtotime($time_utc);
        }
        return self::$timestamp;
    }

    public function getFasyankes()
    {
        $param = 'Fasyankes';
        $full_url = $this->url.$param;
        $get_curl = self::curl($full_url, false, self::getHeader(false), 'GET');
        return self::out($get_curl);
    }

    public function getTempatTidurRsonline()
    {
        $param = 'Referensi/tempat_tidur';
        $full_url = $this->url.$param;
        $get_curl = self::curl($full_url, false, self::getHeader(false), 'GET');
        return self::out($get_curl);
    }

    public function postFasyankes($id, $type, $ruangan_id, $klasifikasikamar_id)
    {
        $payload = $this->setPayload($id, $ruangan_id, $klasifikasikamar_id);
        if (ArrayHelper::getValue($payload, 'id_tt')) {
            $json_data = is_array($payload) ? json_encode($payload) : json_encode(array());
            $action = $type == DocoConstants::TYPE_CREATE_APLICARE ? "POST" : "PUT";
            $param = 'Fasyankes';
            $full_url = $this->url.$param;
            
            $get_curl = self::curl($full_url, $json_data, self::getHeader(false), $action);
            return self::out($get_curl);
        }
    }

    protected function getKamartempatTidur($ruangan_id, $listKamarRuangan = [])
    {
        return KamarTempatTidur::find()
        ->join('join', 'kamarruangan_m', 'kamarruangan_m.kamarruangan_id = kamartempattidur_m.kamarruangan_id')
        ->where(['ruangan_id' => $ruangan_id])
        ->andWhere(['IN', 'kamartempattidur_m.kamarruangan_id',  $listKamarRuangan])
        ->andWhere([
            'kamartempattidur_m.is_rekapkinerjaprofesi' => true,
            'kamartempattidur_m.is_active' => true
        ])
        ->andWhere([
            'kamarruangan_m.is_active' => true,
            'kamarruangan_m.is_deleted' => false
        ]);
    }

    /** payload
     * $id = kamarruangan_id
     * $ruangan_id = ruangan_id
     * $klasifikasikamar_id bisa klasifikasi baru / sebelumnya 
     * payload untuk create data baru / update ke Rs ONline
     */
    protected function setPayload($id, $ruangan_id, $klasifikasikamar_id)
    {
        $klasifikasiKamar = KlasifikasiKamarV::find()->where([
            'ruangan_id' => $ruangan_id,
            'klasifikasikamar_id' => $klasifikasikamar_id,
        ])->asArray()->all();
        $dataKlasifikasiKamar = ArrayHelper::getValue($klasifikasiKamar, 0, []);
        $listKamarRuangan = ArrayHelper::getColumn($klasifikasiKamar, 'kamarruangan_id');

        /** berdasarkan klasifikasi rs online */
        $jumlahRuangan = KlasifikasiKamarV::find()
            ->where(['ruangan_id' => $ruangan_id])
            // ->andWhere(['not', ['kodett_rsonline' => null]])
            ->andWhere(['kodett_rsonline' => ArrayHelper::getValue($dataKlasifikasiKamar, 'kodett_rsonline')])
            ->count();

        $tempatTidur = $this->getKamartempatTidur($ruangan_id, $listKamarRuangan);
        $terpakai = $this->getKamartempatTidur($ruangan_id, $listKamarRuangan);

        /*
        $KetersediaanKamarFnDet = KetersediaanKamarFnDet::find()
        ->where(['ruangan_id' => $ruangan_id])
        ->andWhere(['IN','kamarruangan_id', $listKamarRuangan]);
        */

        $jmlTempatTidur = $tempatTidur->count();
        $prepare = $tempatTidur->andWhere([
            'kamartempattidur_m.kettempattidur_id' => DocoConstants::KET_TT_ALTD_NOT_OCCUPIED_ID,
            'kamartempattidur_m.is_terisi' => false
        ])->count();
        
        $ketBed = (new DocoConstansId)->actionGetAdditional('set_keterangan_bed', true);
        if (isset($ketBed['occupied']) && !empty($ketBed['occupied']) && in_array(DocoConstants::KET_TT_OCC, $ketBed['occupied'])) {
            $terpakai = $terpakai->andWhere(['OR',
                ['IN', 'kettempattidur_id', $ketBed['occupied']],
                ['kamartempattidur_m.is_terisi' => true]
            ])->count();
        } else {
            $terpakai = $terpakai->andWhere(['OR',
                ['kettempattidur_id' => DocoConstants::KET_TT_OCC],
                ['kamartempattidur_m.is_terisi' => true]
            ])->count();
        }

        return [
            'id_tt' => (string) ArrayHelper::getValue($dataKlasifikasiKamar, 'kodett_rsonline'),
            'ruang' =>  ArrayHelper::getValue($dataKlasifikasiKamar, 'ruangan_nama'), //: nama ruangan yang telah di mapping pada tempat tidur yang telah di pilih 
            'jumlah_ruang' => (string) $jumlahRuangan, //: jumlah kamar pada ruangan tempat tidur tersebut,
            'jumlah' => (string) $jmlTempatTidur, //: jumlah tempat tidur pada ruangan tersebut,
            'terpakai' => (string) $terpakai, //: jumlah tempat tidur yang berstatus occupied ,
            'terpakai_suspek' => (string) 0,
            'terpakai_konfirmasi' => (string) $terpakai, //: jumlah tempat tidur yang berstatus occupied ,
            'antrian' => (string) 0,
            'prepare' => (string) $prepare, //: jumlah tempat tidur yang berstatus alloted but not ocupied ,
            'prepare_plan' => (string) 0,
            'covid' => (string) 0
        ];
    }

    /**
     * delete data by id_t_tt
     */
    public function deleteFasyankes($id)
    {   
        $klasifikasiKamar = KlasifikasiKamarV::find()->select([
            'kamarruangan_id', 'kodett_rsonline', 'namatt_rsonline','ruangan_nama', 'id_t_tt_rsonline'
        ])->where(['kamarruangan_id' => $id])->asArray()->one();

        if (ArrayHelper::getValue($klasifikasiKamar, 'id_t_tt_rsonline')) {
            $payload = [
                "id_t_tt" => (string) ArrayHelper::getValue($klasifikasiKamar, 'id_t_tt_rsonline')
            ];
            $json_data = is_array($payload) ? json_encode($payload) : json_encode(array());
            
            $param = 'Fasyankes';
            $full_url = $this->url.$param;
            $get_curl = self::curl($full_url, $json_data, self::getHeader(false), "DELETE");
            return self::out($get_curl);
        }
    }

    /**
     * function cek data di RS ONLINE
     */
    protected function existingData($id)
    {
        $existingData = false;
        $klasifikasiKamar = KlasifikasiKamarV::find()->select([
            'kamarruangan_id', 'kodett_rsonline', 'namatt_rsonline','ruangan_nama', 'id_t_tt_rsonline'
        ])->where(['kamarruangan_id' => $id])->asArray()->one();
        $kodett_rsonline = ArrayHelper::getValue($klasifikasiKamar, 'kodett_rsonline');
        $ruangan_nama = ArrayHelper::getValue($klasifikasiKamar, 'ruangan_nama');

        $getFasyankes = $this->getFasyankes();
        $getFasyankes = ArrayHelper::getValue($getFasyankes, 'fasyankes', []);
        foreach($getFasyankes as $key => $value) {
            $id_tt = ArrayHelper::getValue($value, 'id_tt');
            $ruang = ArrayHelper::getValue($value, 'ruang');
            if ($id_tt == $kodett_rsonline && $ruang == $ruangan_nama) {
                $existingData = true;
            }
        }
        return $existingData;
    }

    /** 
     * function cek data setelah create / update ke RS ONLINE
     * return id_t_tt untuk di simpan ke internal
     */
    protected function existingDataAndGetIdTTt($id)
    {
        $klasifikasiKamar = KlasifikasiKamarV::find()->select([
            'kamarruangan_id', 'kodett_rsonline', 'namatt_rsonline','ruangan_nama', 'id_t_tt_rsonline'
        ])->where(['kamarruangan_id' => $id])->asArray()->one();
        $kodett_rsonline = ArrayHelper::getValue($klasifikasiKamar, 'kodett_rsonline');
        $ruangan_nama = ArrayHelper::getValue($klasifikasiKamar, 'ruangan_nama');

        $id_t_tt = null;
        $getFasyankes = $this->getFasyankes();
        $getFasyankes = ArrayHelper::getValue($getFasyankes, 'fasyankes', []);
        foreach($getFasyankes as $key => $value) {
            $id_tt = ArrayHelper::getValue($value, 'id_tt');
            $ruang = ArrayHelper::getValue($value, 'ruang');
            $id_t_tt_rso = ArrayHelper::getValue($value, 'id_t_tt');
            if ($id_tt == $kodett_rsonline && $ruang == $ruangan_nama) {
                if ($id_t_tt_rso != ArrayHelper::getValue($klasifikasiKamar, 'id_t_tt_rsonline')) {
                    $id_t_tt = $id_t_tt_rso;
                }
            }
        }
        return $id_t_tt;
    }

    /**
     * mapping untuk ws ke rs online 
     * oldKlasifikasikamarId klasifikasi lama harus di update juga
     * klasifikasikamar_id perubahan klasifikasi baru
     * existingData() cek apakah ruangan sudah ada di RS ONLINE 
     */
    public function rsOnlinePostOrPutFasyankes($id, $params = [])
    {
        $ruangan_id = ArrayHelper::getValue($params, 'ruangan_id');
        $klasifikasikamar_id = ArrayHelper::getValue($params, 'klasifikasikamar_id');
        $oldKlasifikasikamarId = ArrayHelper::getValue($params, 'oldKlasifikasikamarId');
        /** sudah pernah di mapping dan ada perubahan klasifikasi */
        if (!empty($oldKlasifikasikamarId) && $oldKlasifikasikamarId != $klasifikasikamar_id) {
            /** update dengan old klasifikasi  */
            $klasifikasiKamar = KlasifikasiKamarV::find()->where([
                'ruangan_id' => $ruangan_id, 'klasifikasikamar_id' => $oldKlasifikasikamarId,
            ])->asArray()->all();
            
            /** delete jika kodeKlass rs sudah tidak ada di mappingan */
            if (empty($klasifikasiKamar)) {
                $this->deleteFasyankes($id);
            } else {
                $updateRs = $this->postFasyankes($id, DocoConstants::TYPE_UPDATE_APLICARE, $ruangan_id, $oldKlasifikasikamarId);
            }
        }
        $existingData = $this->existingData($id);
        $type = $existingData ? DocoConstants::TYPE_UPDATE_APLICARE : DocoConstants::TYPE_CREATE_APLICARE;
        $post = $this->postFasyankes($id, $type, $ruangan_id, $klasifikasikamar_id);
        $id_t_tt = $this->existingDataAndGetIdTTt($id);
        if ($id_t_tt) {
            KamarRuangan::updateAll(['id_t_tt_rsonline' => $id_t_tt],
                ['and', 
                    ['ruangan_id' => $ruangan_id],
                    ['klasifikasikamar_id' =>  $klasifikasikamar_id]
                ]
            );
        }
    }

    /** delete or update ke rs online
     * jika sudah tidak ada mapping klasifikasi delete
     * jika masih ada mapping klasifikasi update 
     */
    public function rsOnlineDeleteOrPutFasyankes($id, $params = [])
    {
        $ruangan_id = ArrayHelper::getValue($params, 'ruangan_id');
        $oldKlasifikasikamarId = ArrayHelper::getValue($params, 'oldKlasifikasikamarId');
        $klasifikasiKamar = KlasifikasiKamarV::find()->where([
            'ruangan_id' => $ruangan_id, 'klasifikasikamar_id' => $oldKlasifikasikamarId,
        ])->asArray()->all();

        /** delete jika kodeKlass rs sudah tidak ada di mappingan */
        if (empty($klasifikasiKamar)) {
            $this->deleteFasyankes($id);
            KamarRuangan::updateAll(['id_t_tt_rsonline' => null], ['kamarruangan_id' => $id]);
        } else {
            $updateRs = $this->postFasyankes($id, DocoConstants::TYPE_UPDATE_APLICARE, $ruangan_id, $oldKlasifikasikamarId);
        }
    }

    public function rsOnlineCheckUpdate($kamarruangan_id) {
        $kamarRuangan = KamarRuangan::find()->where(['kamarruangan_id' => $kamarruangan_id])->one();
        if (!empty($kamarRuangan) && !empty($kamarRuangan->klasifikasikamar_id)) {
            $rsOnline = (new RsOnline)->rsOnlinePostOrPutFasyankes($kamarruangan_id, [
                'ruangan_id' => $kamarRuangan->ruangan_id,
                'klasifikasikamar_id' => $kamarRuangan->klasifikasikamar_id,
                'oldKlasifikasikamarId' => null
            ]);
        }
    }
}