<?php

namespace app\modules\v1\models;

use Yii;
use app\modules\v1\models\Lookup;
use Doco\components\DocoConstants;
use Doco\Services\ApiBPJSLZString;

/**
 * This is the model class for table "bpjs_t".
 *
 * @property int $bpjs_id
 * @property string $tglsep
 * @property string $nosep
 * @property string $nokartuasuransi
 * @property string $tglrujukan
 * @property string $norujukan
 * @property string $ppkrujukan
 * @property string $ppkpelayanan
 * @property int $jnspelayanan 0=Rawat Jalan, 1=Rawat Inap
 * @property string $catatansep
 * @property string $diagnosaawal
 * @property string $politujuan
 * @property int $klsrawat
 * @property string $tglpulang
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 * @property string $nama_peserta
 * @property int $lakalantas 0=Kasus Kecelakaan, 1=Bukan Kasus Kecelakaan
 * @property string $lokasilaka
 * @property string $no_rekam_medik
 */
class Bpjs extends \yii\db\ActiveRecord
{
    public $url;
    public $cons_id;
    public $secret_key;
    public $ppkPelayanan;

    // kebutuhan SEP
    public $arr_sep = [
        'noKartu',
        'tglSep',
        // 'ppkPelayanan',
        'jnsPelayanan',
        'klsRawat',
        'noMR',
        'asalRujukan',
        'tglRujukan',
        'noRujukan',
        'ppkRujukan',
        'catatan',
        'diagAwal',
        'tujuan',
        'eksekutif',
        'cob',
        'lakaLantas',
        'penjamin',
        'lokasiLaka',
        'noTelp',
        'user',
    ];
    public $t_sep;

    public $isktp;
    public $isrujukanrs;
    // public $user;
    // public $nomr;

    static protected $cons_ids;
    static protected $secret_keys;
    static protected $version = 1.0;
    static protected $timestamp;
    public $user_key;
    static protected $user_keys;

    public function init()
    {
        parent::init();
        // deprecated
        // $this->url = 'http://dvlp.bpjs-kesehatan.go.id:8081/devwslokalrest/';
        // $this->url = 'http://dvlp.bpjs-kesehatan.go.id:8081/VClaim-rest/';
        // $this->cons_id = '32226';
        // $this->secret_key = '4vK0F548FB';
        
        $cache = Yii::$app->cache;
        $cache_bpjs = $cache->get(DocoConstants::LOOKUP_BPJS);
        if (!$cache_bpjs) {
            $lookup = Lookup::find()->where(['lookup_type'=>DocoConstants::LOOKUP_BPJS])
                ->asArray()
                ->all();
            $cache->set(DocoConstants::LOOKUP_BPJS, $lookup);
            $cache_bpjs = $cache->get(DocoConstants::LOOKUP_BPJS);
        }
        
        if ($cache_bpjs) {
            foreach ($cache_bpjs as $each) {
                if ($each['lookup_name'] == 'secret_key') {
                    $this->secret_key = $each['lookup_value'];
                }
                if ($each['lookup_name'] == 'cons_id') {
                    $this->cons_id = $each['lookup_value'];
                }
                if ($each['lookup_name'] == 'url') {
                    $this->url = $each['lookup_value'];
                }
                if ($each['lookup_name'] == 'ppkPelayanan') {
                    $this->ppkPelayanan = $each['lookup_value'];
                }
                if ($each['lookup_name'] == 'version') {
                    self::$version = $each['lookup_value'];
                }
                if ($each['lookup_name'] == 'user_key_vclaim') {
                    $this->user_key = $each['lookup_value'];
                }
            }
        }

        self::$cons_ids = $this->cons_id;
        self::$secret_keys = $this->secret_key;
        self::$user_keys = $this->user_key;

    }

    public function attributes()
    {
        $list = parent::attributes();
        $list[] = 'nomr';
        $list[] = 'isktp';
    
        $list[] = 'arr_sep';
        $list[] = 't_sep';
        return $list;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bpjs_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nokartuasuransi'], 'required'],
            [['tglsep', 'tglrujukan', 'tglpulang', 'created_date', 'last_modified_date', 'deleted_date', 'nama_peserta', 'ppkpelayanan'], 'safe'],
            [['jnspelayanan', 'klsrawat', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'lakalantas'], 'default', 'value' => null],
            [['jnspelayanan', 'klsrawat', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'lakalantas'], 'integer'],
            [['catatansep', 'diagnosaawal', 'additional_data', 'lokasilaka'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['nosep', 'politujuan', 'nama_peserta'], 'string', 'max' => 100],
            [['nokartuasuransi', 'norujukan', 'ppkrujukan', 'ppkpelayanan'], 'string', 'max' => 50],
            [['no_rekam_medik'], 'string', 'max' => 25],
        ];
    }


    protected static function getSignature()
    {
        // Computes the signature by hashing the salt with the secret key as the key
        $signature = hash_hmac(
            'sha256', 
            self::$cons_ids . "&" . self::getTimestamp(), 
            self::$secret_keys, 
            true
        );
        return base64_encode($signature);
    }

    protected static function getTimestamp()
    {
        // Computes the timestamp
        date_default_timezone_set('UTC');
        if (empty(self::$timestamp)) {
            self::$timestamp = strval(time() - strtotime('1970-01-01 00:00:00'));
        }
        return self::$timestamp;
    }

    protected static function curl($url, $data, $header, $action = '')
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT ,0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);

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
                'metadata'=>[
                    "code"=>500,
                    "message"=>curl_error($curl) ? : "Problem with bridging",
                ]
            ];
            $res = json_encode($errorArray);
            // die('Error: "'.curl_error($curl).'" - Code: '.curl_errno($curl));
        }
        curl_close($curl);

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
            $keyEncrypt = self::$cons_ids . self::$secret_keys . self::getTimestamp();
            $outPut['response'] = (new ApiBPJSLZString)->decryptWithDecompress($keyEncrypt, $response);
        }
        return $outPut;
    }

    /**
     * header for API http://dvlp.bpjs-kesehatan.go.id:8081/devwslokalrest/
     * url baru untuk api https://dvlp.bpjs-kesehatan.go.id/vClaim-rest/
     * @param mixed $data;
     * @return HTTP Header
     */
    protected static function getHeader($form_url_encoded = false)
    {
        if ($form_url_encoded)
            $conten_type = "Content-Type: application/x-www-form-urlencoded";
        else
            $conten_type = "Content-Type: application/json";

        return [
            "X-cons-id:" . self::$cons_ids,
            "X-timestamp:" . self::getTimestamp(),
            "X-signature:" . self::getSignature(),
            "user_key:" . self::$user_keys,
            $conten_type
        ];
    }


    /**
     * Get Peserta by Nomor BPJS or Nomor KTP.
     * @param mixed $id param input from Nomor BPJS or Nomor KTP
     * @param mixed $is_ktp param using Nomor KTP
     * @return mixed response web services
     */
    public function peserta($nokartu, $tglSEP=null, $is_ktp = false)
    {
        // $tglSEP = $tglSEP ? : date('Y-m-d');
        $tglSEP = date('Y-m-d');
        $param = "Peserta/nokartu/{$nokartu}/tglSEP/{$tglSEP}";
        if ($is_ktp) {
            $param = "Peserta/nik/{$nokartu}/tglSEP/{$tglSEP}";
        }
        // $this->url = "https://dvlp.bpjs-kesehatan.go.id/vClaim-rest/";
        // $rtr[] = self::getHeader(true);
        // $rtr[] = $this->url . $param;
        // return $rtr;
        // echo $this->url . $param;
        // die();
        $data = self::curl($this->url . $param, false, self::getHeader(true));
        // return $data;
        return self::out($data);
    }

    /**
     * Pencarian Data Rujukan Dari PCare Berdasarkan Nomor Rujukan
     * Method : GET
     * @param mixed $no_rujukan, Nomor Rujukan
     * @param mixed $rs, Pcare atau Rumah Sakit
     * @return mixed response web services
     */
    public function rujukan($nomor, $asal_rujukan)
    {
        $returndummy = [
            "metaData" => [
                "code" => "200",
                "message" => "OK"
            ],
            "response" => [
                "rujukan" => [
                    "diagnosa" => [
                        "kode" => "N40",
                        "nama" => "Hyperplasia of prostate"
                    ],
                    "keluhan" => "kencing tidak puas",
                    "noKunjungan" => "030107010217Y001465",
                    "pelayanan" => [
                        "kode" => "1",
                        "nama" => "Rawat Inap"
                    ],
                    "peserta" => [
                        "cob" => [
                            "nmAsuransi" => null,
                            "noAsuransi" => null,
                            "tglTAT" => null,
                            "tglTMT" => null
                        ],
                        "hakKelas" => [
                        "keterangan" => "KELAS I",
                        "kode" => "1"
                        ],
                        "informasi" => [
                            "dinsos" => null,
                            "noSKTM" => null,
                            "prolanisPRB" => null
                        ],
                        "jenisPeserta" => [
                            "keterangan" => "PENERIMA PENSIUN PNS",
                            "kode" => "15"
                        ],
                        "mr" => [
                            "noMR" => "298036",
                            "noTelepon" => null
                        ],
                        "nama" => "MUSDIWAR,BA",
                        "nik" => null,
                        "noKartu" => "0000416382632",
                        "pisa" => "2",
                        "provUmum" => [
                            "kdProvider" => "03010701",
                            "nmProvider" => "SITEBA"
                        ],
                        "sex" => "L",
                        "statusPeserta" => [
                            "keterangan" => "AKTIF",
                            "kode" => "0"
                        ],
                        "tglCetakKartu" => "2017-11-13",
                        "tglLahir" => "1938-08-31",
                        "tglTAT" => "2038-08-31",
                        "tglTMT" => "1996-08-20",
                        "umur" => [
                            "umurSaatPelayanan" => "78 tahun ,6 bulan ,6 hari",
                            "umurSekarang" => "79 tahun ,3 bulan ,18 hari"
                        ]
                    ],
                    "poliRujukan" => [
                        "kode" => "URO",
                        "nama" => "UROLOGI"
                    ],
                    "provPerujuk" => [
                        "kode" => "03010701",
                        "nama" => "SITEBA"
                    ],
                    "tglKunjungan" => "2017-02-25"
                ]
            ]
        ];
        return $returndummy;

        $url_rujukan = "Rujukan/$nomor";
        if ($asal_rujukan == 1) {
            $url_rujukan = "Rujukan/RS/$nomor";
        }

        $full_url = $this->url . $url_rujukan;
        $data_rujukan = self::curl($full_url, false, self::getHeader(), 'GET');

        return self::out($data_rujukan);
    }

    /**
     * Pencarian Data Rujukan Dari PCare Berdasarkan Nomor Kartu
     * Method : GET
     * @param mixed $no_kartu, Nomor Rujukan
     * @param mixed $rs, Pcare atau Rumah Sakit
     * @return mixed response web services
     */
    public function rujukanPeserta($no_kartu, $rs = false)
    {
        $url_rujukan = "Rujukan/Peserta/$no_kartu";
        if (!empty($rs))
            $url_rujukan = "Rujukan/RS/Peserta/$no_kartu";

        $full_url = $this->url . $url_rujukan;
        $data_rujukan = self::curl($full_url, false, self::getHeader(), 'GET');

        return self::out($data_rujukan);
    }

    /**
     * Pencarian Data Poli
     * Method : GET
     * @return mixed response web services
     */
    public function referensiPoli($param)
    {
        $full_url = $this->url . "referensi/poli/" . $param;
        $data = self::curl($full_url, false, self::getHeader(), 'GET');

        return self::out($data);
    }

    /**
     * Pencarian Data Diagnosa
     * Method : GET
     * @return mixed response web services
     */
    public function referensiDiagnosa($param)
    {
        $full_url = $this->url . "referensi/diagnosa/" . $param;
        $data = self::curl($full_url, false, self::getHeader(), 'GET');

        return self::out($data);
    }
    /**
     * Pencarian Data Diagnosa
     * Method : GET
     * @return mixed response web services
     */
    public function referensiFaskes($kode, $jenis=1)
    {
        $encodedKode = rawurlencode($kode);
        $full_url = $this->url . "referensi/faskes/" . $encodedKode . '/' . $jenis;
        $data = self::curl($full_url, false, self::getHeader(), 'GET');

        return self::out($data);
    }

    /**
     * Insert SEP (POST) / Update SEP (PUT)
     * @param mixed $value
     * @param mixed $update; function for update SEP, if value is true;
     * @return mixed response web services
     */
    public function createSep()
    {
        $data = [
            'request'=>[
                't_sep'=>[
                    'noKartu' => $this->t_sep['noKartu'],
                    'tglSep' => date('Y-m-d', strtotime($this->t_sep['tglSep'])),
                    'ppkPelayanan' => $this->ppkPelayanan, // from db/cache
                    'jnsPelayanan' => $this->t_sep['jnsPelayanan'],
                    'klsRawat' => $this->t_sep['klsRawat'],
                    'noMR' => $this->t_sep['noMR'],
                    'rujukan' => [
                        'asalRujukan' => $this->t_sep['asalRujukan'],
                        'tglRujukan' => date('Y-m-d', strtotime($this->t_sep['tglRujukan'])),
                        'noRujukan' => $this->t_sep['noRujukan'],
                        'ppkRujukan' =>$this->t_sep['ppkRujukan']
                    ],
                    'catatan' => $this->t_sep['catatan'],
                    'diagAwal' => $this->t_sep['diagAwal'],
                    'poli' => [
                        'tujuan' => $this->t_sep['tujuan'],
                        'eksekutif' =>$this->t_sep['eksekutif']
                    ],
                    'cob' => [
                        'cob' =>$this->t_sep['cob']
                    ],
                    'jaminan' => [
                        'lakaLantas' => $this->t_sep['lakaLantas'],
                        'penjamin' => $this->t_sep['penjamin'],
                        'lokasiLaka' =>$this->t_sep['lokasiLaka']
                    ],
                    'noTelp' => $this->t_sep['noTelp'],
                    'user' => $this->t_sep['user']
                ]
            ]
        ];
        $json_data = is_array($data) ? json_encode($data) : json_encode(array());
        $param = 'SEP/insert';
        $action = 'POST';
        
        $full_url = $this->url . $param;

        $get_curl = self::curl($full_url, $json_data, self::getHeader(true), $action);

        return self::out($get_curl);
    }

    /**
     * Hapus Data SEP
     * Method : DELETE
     * @param mixed $value
     * @return mixed response web services
     */
    public function deleteSep()
    {
        $data = ['request' => ['t_sep' => $this->t_sep]];
        $json_data = is_array($data) ? json_encode($data) : json_encode(array());

        $param = 'SEP/Delete';
        $full_url = $this->url . $param;
        $get_curl = self::curl($full_url, $json_data, self::getHeader(true), 'DELETE');


        return self::out($get_curl);
    }

    /**
     *
     * Get data referensi
     * method : GET
     * @param array $params
     * @return mixed response web services
     *
     */
    public function referensi($params = [])
    {
        $full_url = $this->url . "/referensi/poli";
        // $data = self::curl($full_url, false, self::getHeader(), 'GET');

        // dummy response
        $data = '{"metaData": {"code": "200", "message": "Sukses"},
            "response": {
                "poli": [{"kode": "ICU","nama": "Intensive Care Unit"}],
                "diagnosa": [{"kode": "A04","nama": "A04 - Other bacterial intestinal infections"}]
            }
        }';
        $data = self::out($data);

        // list data return
        $list_return = [];

        // list poli
        $list_poli = isset($data['response']['poli']) ? $data['response']['poli'] : [];
        $list_return['poli'] = $list_poli;

        // list diagnosa
        $list_poli = isset($data['response']['diagnosa']) ? $data['response']['diagnosa'] : [];
        $list_return['diagnosa'] = $list_poli;


        return $list_return;
    }
    
    /**
    * @author Rizal
    * @since 2018-04-25 11:30:38 
    * @param $model
    * @return 
    * @desc 
    */
    public function setManualAttribute()
    {
        $arr = $this->t_sep;
        $this->nokartuasuransi = $arr['noKartu'];
        $this->tglsep = $arr['tglSep'];
        $this->jnspelayanan = $arr['jnsPelayanan'];
        $this->klsrawat = $arr['klsRawat'];
        $this->no_rekam_medik = $arr['noMR'];
        $this->tglrujukan = $arr['tglRujukan'];
        $this->norujukan = $arr['noRujukan'];
        $this->ppkrujukan = $arr['ppkRujukan'];
        $this->catatansep = $arr['catatan'];
        $this->diagnosaawal = $arr['diagAwal'];
        $this->politujuan = $arr['tujuan'];
        $this->lakalantas = $arr['lakaLantas'];
        $this->lokasilaka = $arr['lokasiLaka'];

        $this->additional_data = json_encode($this->t_sep);
    }

    /**
     * Update Tanggal Pulang SEP 
     * Method : PUT
     * @param mixed $value
     * @return mixed response web services
     */
    public function updateTanggalPulangSep()
    {
        $data = [
            'request' => [
                't_sep' => $this->t_sep
            ]
        ];
        $json_data = is_array($data) ? json_encode($data) : json_encode(array());
        // $param = self::$version != 1 ? 'SEP/2.0/updtglplg' : 'SEP/updtglplg';
        $param = 'SEP/2.0/updtglplg';
        $action = 'PUT';
        
        $full_url = $this->url . $param;

        $get_curl = self::curl($full_url, $json_data, self::getHeader(true), $action);

        return self::out($get_curl);
    }
}
