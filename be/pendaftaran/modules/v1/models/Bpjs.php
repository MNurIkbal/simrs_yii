<?php

namespace app\modules\v1\models;

use Yii;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\Lookup;
use Doco\components\DocoConstants;
use Doco\components\BpjsLog;
use Doco\Services\ApiBPJSLZString;

class Bpjs extends \Doco\components\DocoActiveRecord
{
    const SCENARIO_CARI_KARTU = 'cari_kartu';

    protected $xssProtected = [
        'no_rujukan_f',
        'no_kartu',
    ];

    public $no_kartu;
    public $no_rujukan_f;

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
        'pendaftaran_id',
        'pasienadmisi_id',
    ];
    public $t_sep;
    public $t_sep_new;
    public $isktp;
    public $isrujukanrs;
    public $t_rujukan;
    public $t_rujukan_khusus;
    public $t_rencanakontrol;
    // public $user;
    // public $nomr;

    static protected $cons_ids;
    static protected $secret_keys;
    static protected $version = 1.0;
    static protected $timestamp;
    public $user_key;
    static protected $user_keys;

    public $antrian_jkn;

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

        // foreach ($cache_bpjs as $key=>$each) {
        //     if ($this->hasProperty($each['lookup_name'])) {
        //         $this->test = $each['lookup_name'];
        //         $this->$each['lookup_name'] = 'test';
        //         // break;
        //         // $this->$each['lookup_name'] = $each['lookup_value'];
        //     }
        // }
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
            [['nosep'], 'required', 'on' => 'edit-pendaftaran'],
            [['nokartuasuransi'], 'required'],
            [['tglsep', 'tglrujukan', 'tglpulang', 'created_date', 'last_modified_date', 'deleted_date', 'nama_peserta', 'ppkpelayanan', 'asal_rujukan', 'no_kartu', 'no_rujukan_f', 'kode_dpjp_melayani', 'nama_dpjp_melayani', 'nama_ppk_perujuk', 'kode_ppk_perujuk',  'no_perusahaan', 'nama_perusahaan', 'pembiayaan', 'penanggung_jawab', 'tujuan_kunj', 'flag_procedure', 'kd_penunjang', 'assesment_pel', 'klsrawatnaik', 'kode_dpjp_spri', 'nama_dpjp_spri', 'status_pulang', 'no_surat_meninggal', 'tgl_meninggal_bpjs', 'no_lp_manual'], 'safe'],
            [['jnspelayanan', 'klsrawat', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'lakalantas', 'klsrawatnaik'], 'default', 'value' => null],
            [['jnspelayanan', 'klsrawat', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'lakalantas', 'klsrawatnaik'], 'integer'],
            [['catatansep', 'diagnosaawal', 'additional_data', 'lokasilaka'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['nosep', 'politujuan', 'nama_peserta'], 'string', 'max' => 100],
            [['nokartuasuransi', 'norujukan', 'ppkrujukan', 'ppkpelayanan'], 'string', 'max' => 50],
            [['no_rekam_medik'], 'string', 'max' => 25],
            [['no_kartu', 'jnspelayanan', 'tglsep'], 'required', 'on' => self::SCENARIO_CARI_KARTU],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CARI_KARTU] = ['no_kartu', 'tglsep', 'jnspelayanan'];
        return $scenarios;
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
        // date_default_timezone_set('UTC');
        // return strval(time()-strtotime('1970-01-01 00:00:00'));

        /*
        date_default_timezone_set('UTC');
        if (empty(self::$timestamp)) {
            self::$timestamp = strval(time()-strtotime('1970-01-01 00:00:00'));
        }
        return self::$timestamp;
        */

        $date_utc = new \DateTime("now", new \DateTimeZone("UTC"));
        $time_utc = $date_utc->format('Y-m-d H:i:s');
        if (empty(self::$timestamp)) {
            self::$timestamp = strval(strtotime($time_utc)-strtotime('1970-01-01 00:00:00'));
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
        curl_setopt($curl, CURLOPT_TIMEOUT_MS, 20000);

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
            // die('Error: "'.curl_error($curl).'" - Code: '.curl_errno($curl));
        }
        curl_close($curl);
        BpjsLog::saveLogBpjs($url, $header, $data, $res);
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
            $outPut['response'] = (new ApiBPJSLZString)->decryptWithDecompress($keyEncrypt,$response);
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
        $tglSEP = !empty($tglSEP) ? date('Y-m-d',strtotime($tglSEP)) : date('Y-m-d');
        $param = "peserta/nokartu/{$nokartu}/tglSEP/{$tglSEP}";
        if ($is_ktp) {
            $param = "peserta/nik/{$nokartu}/tglSEP/{$tglSEP}";
        }
        $data = self::curl($this->url . $param, false, self::getHeader(true));
        return self::out($data);
    }

    public function pencarianFingerprint($noKartu,$tglPelayanan)
    {
        $tglPelayanan = !empty($tglPelayanan) ? date('Y-m-d',strtotime($tglPelayanan)) : date('Y-m-d');
        $param = "SEP/Fingerprint/Peserta/{$noKartu}/TglPelayanan/{$tglPelayanan}";
        $data = self::curl($this->url . $param, false, self::getHeader(true));
        return self::out($data);
    }

    public function listPesertaFingerprint($tglPelayanan)
    {
        $tglPelayanan = !empty($tglPelayanan) ? date('Y-m-d',strtotime($tglPelayanan)) : date('Y-m-d');
        $param = "SEP/Fingerprint/List/Peserta/TglPelayanan/{$tglPelayanan}";
        $data = self::curl($this->url . $param, false, self::getHeader(true));
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
        $nomor = trim($nomor);
        $url_rujukan = "Rujukan/$nomor";
        if ($asal_rujukan == 2) {
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
        $full_url = $this->url . "referensi/poli/" . rawurlencode($param);
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
        $full_url = $this->url . "referensi/diagnosa/" . rawurlencode($param);
        $data = self::curl($full_url, false, self::getHeader(), 'GET');

        return self::out($data);
    }
    /**
     * Pencarian Data Diagnosa
     * Method : GET
     * @return mixed response web services
     */
    public function referensiFaskes($kode, $jenis)
    {
        $encodedKode = rawurlencode($kode);
        $full_url = $this->url . "referensi/faskes/" . $encodedKode . '/' . $jenis;
        $data = self::curl($full_url, false, self::getHeader(), 'GET');

        return self::out($data);
    }

    /**
     * Pencarian Data dokter
     * Method : GET
     * @return mixed response web services
     */
    public function referensiDokter($pelayanan, $tglsep, $kode)
    {
        $full_url = $this->url . "referensi/dokter/pelayanan/" . $pelayanan . "/tglPelayanan/" . $tglsep . "/Spesialis/" . $kode;
        // return $full_url;
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
                    'noKartu' => strtoupper($this->t_sep['noKartu']),
                    'tglSep' => date('Y-m-d', strtotime($this->t_sep['tglSep'])),
                    'ppkPelayanan' => $this->ppkPelayanan, // from db/cache
                    'jnsPelayanan' => $this->t_sep['jnsPelayanan'],
                    'klsRawat' => $this->t_sep['klsRawat'],
                    'noMR' => $this->t_sep['noMR'],
                    'rujukan' => [
                        'asalRujukan' => $this->t_sep['asalRujukan'],
                        'tglRujukan' => date('Y-m-d', strtotime($this->t_sep['tglRujukan'])),
                        'noRujukan' => strtoupper($this->t_sep['noRujukan']),
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

        $param = 'SEP/1.1/insert';
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
    public function deleteSep($no_sep = null)
    {
        $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
        $data = [
            'request' => [
                't_sep' => [
                    'noSep' => $no_sep,
                    'user' => !empty($jwt->nama_pemakai) ? $jwt->nama_pemakai : null,
                ]
            ]
        ];
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
        $this->pendaftaran_id = $arr['pendaftaran_id'];
        $this->pasienadmisi_id = $arr['pasienadmisi_id'];

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
        $data = ['request' => ['t_sep' => $this->t_sep]];
        $json_data = is_array($data) ? json_encode($data) : json_encode(array());
        $param = 'SEP/updtglplg';
        $action = 'PUT';

        $full_url = $this->url . $param;

        $get_curl = self::curl($full_url, $json_data, self::getHeader(true), $action);

        return self::out($get_curl);
    }

    public function createSepNew()
    {
        $param = 'SEP/1.1/insert';
        if(self::$version > 1.1) {
            $param = 'SEP/2.0/insert';
        }
        $action = 'POST';
        $data = $this->payloadSep();
        Yii::error(json_encode($data));
        $json_data = is_array($data) ? json_encode($data) : json_encode(array());
        $full_url = $this->url . $param;
        $this->t_sep_new = $json_data;

        $get_curl = self::curl($full_url, $json_data, self::getHeader(true), $action);

        return self::out($get_curl);
    }

    private function payloadSep()
    {
        $data = [
            'request'=>[
                "t_sep" => [
                    "noKartu" => $this->t_sep_new['noKartu'],
                    "tglSep" => $this->t_sep_new['tglSep'],
                    "ppkPelayanan" => $this->ppkPelayanan,
                    "jnsPelayanan" => $this->t_sep_new['jnsPelayanan'],
                    "klsRawat" => $this->t_sep_new['klsRawat'],
                    "noMR" => $this->t_sep_new['noMR'],
                    "rujukan" => [
                        "asalRujukan" => $this->t_sep_new['asalRujukan'],
                        "tglRujukan" => $this->t_sep_new['tglRujukan'],
                        "noRujukan" => $this->t_sep_new['noRujukan'],
                        "ppkRujukan" => $this->t_sep_new['ppkRujukan']
                    ],
                    "catatan" => $this->t_sep_new['catatan'],
                    "diagAwal" => $this->t_sep_new['diagAwal'],
                    "poli" => [
                        "tujuan" => $this->t_sep_new['tujuan'],
                        "eksekutif" => $this->t_sep_new['eksekutif']
                    ],
                    "cob" => [
                        "cob" =>  $this->t_sep_new['cob']
                    ],
                    "katarak" => [
                        "katarak" =>  $this->t_sep_new['katarak']
                    ],
                    "jaminan" => [
                        "lakaLantas" => $this->t_sep_new['lakaLantas'],
                        "noLP" => $this->t_sep_new['no_lp'],
                        "penjamin" => [
                            "penjamin" => $this->t_sep_new['penjamin'],
                            "tglKejadian" => $this->t_sep_new['tglKejadian'],
                            "keterangan" => $this->t_sep_new['keterangan'],
                            "suplesi" => [
                                "suplesi" => $this->t_sep_new['suplesi'],
                                "noSepSuplesi" => $this->t_sep_new['noSepSuplesi'],
                                "lokasiLaka" => [
                                    "kdPropinsi" => $this->t_sep_new['kdPropinsi'],
                                    "kdKabupaten" => $this->t_sep_new['kdKabupaten'],
                                    "kdKecamatan" =>  $this->t_sep_new['kdKecamatan']
                                ]
                            ]
                        ]
                    ],
                    "skdp" => [
                        "noSurat" => $this->t_sep_new['noSurat'],
                        "kodeDPJP" => $this->t_sep_new['kodeDPJP']
                    ],
                    "noTelp" => $this->t_sep_new['noTelp'],
                    "user" => $this->t_sep_new['user']
                ]
            ]
        ];

        if(self::$version > 1.1) {
            $data['request']['t_sep']['klsRawat'] = [
                "klsRawatHak" => $this->t_sep_new['klsRawat'],
                "klsRawatNaik" => $this->t_sep_new['klsRawatNaik'],
                "pembiayaan" => $this->t_sep_new['pembiayaan'],
                "penanggungJawab" => $this->t_sep_new['penanggungJawab']
            ];
            $data['request']['t_sep']['tujuanKunj'] = $this->t_sep_new['tujuanKunj']; //0: normal, 1:Prosedur, 2:Konsul Dokter
            $data['request']['t_sep']['flagProcedure'] = $this->t_sep_new['flagProcedure']; //0: Prosedur Tidak Berkelanjutan, 1:Prosedur dan Terapi Berkelanjutan, "": diisi "" jika tujuanKunj = "0"
            $data['request']['t_sep']['kdPenunjang'] = $this->t_sep_new['kdPenunjang']; // "": diisi "" jika tujuanKunj = "0"
            $data['request']['t_sep']['assesmentPel'] = $this->t_sep_new['assesmentPel']; //"": diisi jika tujuanKunj = "2" atau "0"
            $data['request']['t_sep']['dpjpLayan'] = $this->t_sep_new['kode_dpjp_melayani']; //(tidak diisi jika jnsPelayanan = "1" (RANAP)
        }

        return $data;
    }

    /**
     * @author Budi
     * @since 2019-27-06-2019
     * Pencarian Data Dokter DPJP
     * Method : GET
     * @return mixed response web services
     */
    public function referensiDpjp($param1, $param2, $param3)
    {
        $full_url = $this->url . "referensi/dokter/pelayanan/" . $param1 . '/tglPelayanan/' . $param2 . '/Spesialis/' . $param3;

        $data = self::curl($full_url, false, self::getHeader(), 'GET');

        return self::out($data);
    }

    /**
     * @author Budi
     * @since 2019-02-07-2019
     * Pencarian Data Kelas Rawat
     * Method : GET
     * @return mixed response web services
     */
    public function referensiKelasRawat()
    {
        $full_url = $this->url . "referensi/kelasrawat";

        $data = self::curl($full_url, false, self::getHeader(), 'GET');

        return self::out($data);
    }

    /**
     * @author Budi
     * @since 2019-02-07-2019
     * Pencarian Data Provinsi
     * Method : GET
     * @return mixed response web services
     */
    public function referensiProvinsi()
    {
        $full_url = $this->url . "referensi/propinsi";
        $data = self::curl($full_url, false, self::getHeader(), 'GET');

        return self::out($data);
    }

    /**
     * @author Budi
     * @since 2019-02-07-2019
     * Pencarian Data Kabupaten
     * Method : GET
     * @return mixed response web services
     */
    public function referensiKabupaten($param)
    {
        $full_url = $this->url . "referensi/kabupaten/propinsi/" . rawurlencode($param);
        $data = self::curl($full_url, false, self::getHeader(), 'GET');

        return self::out($data);
    }

    /**
     * @author Budi
     * @since 2019-02-07-2019
     * Pencarian Data Kabupaten
     * Method : GET
     * @return mixed response web services
     */
    public function referensiKecamatan($param)
    {
        $full_url = $this->url . "referensi/kecamatan/kabupaten/" . rawurlencode($param);
        $data = self::curl($full_url, false, self::getHeader(), 'GET');

        return self::out($data);
    }

    /**
     * @author Budi
     * @since 2019-04-09-2019
     * Pencarian History Pasien
     * Method : GET
     * @return mixed response web services
     */
    public function detailHistoryBpjs($param)
    {
        $full_url = $this->url . "monitoring/HistoriPelayanan/NoKartu/" . $param['noKartu'] . '/tglMulai/' . $param['tglMulai'] . '/tglAkhir/' . $param['tglAkhir'];

        $data = self::curl($full_url, false, self::getHeader(), 'GET');
        return self::out($data);
    }

    public function historyPelayananPasien($noKartu)
    {
        $tglmulai = date('Y-m-d', strtotime('-89 days'));
        $tglselesai = date('Y-m-d');

        $period = new \DatePeriod(
            new \DateTime($tglmulai),
            new \DateInterval('P10D'),
            (new \DateTime($tglselesai))->add(new \DateInterval('P1D'))
        );

        $allHistory =[];
        foreach($period as $_period){
            $history = [];
            $response = $this->detailHistoryBpjs([
                'noKartu'=>$noKartu,
                'tglMulai' => $_period->format('Y-m-d'),
                'tglAkhir' => $_period->add(new \DateInterval('P9D'))->format('Y-m-d')
            ]);
            $history = ArrayHelper::getValue($response,'response.histori');
            if(is_array($history) && count($history)>0){
                $allHistory = array_merge($allHistory,$history);
            }
        }

        return [
            'response' => [
                'histori' => $allHistory
            ]
        ];
    }

    public function monitoringKunjungan($tglSep,$jnsPelayanan)
    {
        $full_url = $this->url . "Monitoring/Kunjungan/Tanggal/" . $tglSep. "/JnsPelayanan/" . $jnsPelayanan;

        $data = self::curl($full_url, false, self::getHeader(), 'GET');
        return self::out($data);
    }

    /**
     * @author Budi
     * @since 28-10-2019
     * Pencarian No SEP
     * Method : GET
     * @return mixed response web services
     */
    public function referensiCariSep($param)
    {
        $full_url = $this->url . "SEP/" . rawurlencode($param);
        $data = self::curl($full_url, false, self::getHeader(), 'GET');

        return self::out($data);
    }

    /**
     * Cari SEP di fitur rencanakontrol (GET)
     *
     * @param string $param nosep
     * @return array response web services
     */
    public function rencanaKontrolCariSep($param)
    {
        $full_url = $this->url . "RencanaKontrol/nosep/" . rawurlencode($param);
        $data = self::curl($full_url, false, self::getHeader(), 'GET');

        return self::out($data);
    }

    /**
     * @method cari rujukan by nopeserta
     *
     * @param string $param
     * @return array
     *
     * @author : Erlangga (librantara.erlangga@sirs.com)
     */
    public function cariRujukanPeserta($param, $list = false, $type = 1)
    {
        if($type == 1) {
            $method = "Rujukan/Peserta/";
            if($list == true) {
                $method = "Rujukan/List/Peserta/";
            }
        } else {
            $method = "Rujukan/RS/Peserta/";
            if($list == true) {
                $method = "Rujukan/RS/List/Peserta/";
            }
        }
        $full_url = $this->url . $method . rawurlencode($param);
        $data = self::curl($full_url, false, self::getHeader(), 'GET');

        return self::out($data);
    }

    /**
     * @method cari rujukan by nomorrujukan
     *
     * @param string $param
     * @return array
     *
     * @author : Erlangga (librantara.erlangga@sirs.com)
     */
    public function cariRujukan($param)
    {
        $full_url = $this->url . "Rujukan/" . rawurlencode($param);
        $data = self::curl($full_url, false, self::getHeader(), 'GET');

        return self::out($data);
    }

    /**
     * Insert Rujukan (POST)
     * @param mixed $value
     * @return mixed response web services
     */
    public function createRujukan()
    {
        $param = 'Rujukan/insert';
        $action = 'POST';
        $data = [
            'request' => [
                't_rujukan' => [
                    'noSep' => $this->t_rujukan['noSep'],
                    'tglRujukan' => $this->t_rujukan['tglRujukan'],
                    'ppkDirujuk' => $this->t_rujukan['ppkDirujuk'],
                    'jnsPelayanan' => $this->t_rujukan['jnsPelayanan'],
                    'catatan' => $this->t_rujukan['catatan'],
                    'diagRujukan' => $this->t_rujukan['diagRujukan'],
                    'tipeRujukan' => $this->t_rujukan['tipeRujukan'],
                    'poliRujukan' => $this->t_rujukan['poliRujukan'],
                    'user' => $this->t_rujukan['user'],
                ]
            ]
        ];
        if(self::$version > 1.1) {
            $param = 'Rujukan/2.0/insert';
            $data['request']['t_rujukan']['tglRencanaKunjungan'] = $this->t_rujukan['tglRencanaKunjungan'];
            if($this->t_rujukan == 0) {
                // $data['request']['t_rujukan']['poliRujukan'] = $this->t_rujukan['kodeSpesialis'];
            } else if ($this->t_rujukan == 2) {
                $data['request']['t_rujukan']['poliRujukan'] = ""; //"{kosong untuk tipe rujukan 2, harus diisi jika 0 atau 1}",
            }
        }
        $json_data = is_array($data) ? json_encode($data) : json_encode(array());
        $full_url = $this->url.$param;
        $get_curl = self::curl($full_url, $json_data, self::getHeader(true), $action);

        return self::out($get_curl);
    }

    public function updateAntrianJkn()
    {
        $data = [
            'request'=>[
                'kodebooking' => $this->antrian_jkn['kodebooking'],
                'taskid' => $this->antrian_jkn['taskid'],
                'waktu' => $this->antrian_jkn['waktu']
            ]
        ];
        $json_data = is_array($data) ? json_encode($data) : json_encode(array());
        $param = 'antrean/updatewaktu';
        $action = 'POST';

        $full_url = $this->url . $param;
        $get_curl = self::curl($full_url, $json_data, self::getHeader(true), $action);

        return self::out($get_curl);
    }

    public function batalAntrianJkn()
    {
        $data = [
            'request'=>[
                'kodebooking' => $this->antrian_jkn['kodebooking'],
                'keterangan' => $this->antrian_jkn['keterangan'],
            ]
        ];
        $json_data = is_array($data) ? json_encode($data) : json_encode(array());
        $param = 'antrean/batal';
        $action = 'POST';

        $full_url = $this->url . $param;
    }

    /**
     * @method delete rujukan
     *
     * @param string $norujukan
     * @return array
     * @author : Erlangga (librantara.erlangga@sirs.com)
     */
    public function deleteRujukan($norujukan)
    {
        $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
        $data = [
            'request' => [
                't_rujukan' => [
                    'noRujukan' => $norujukan,
                    'user' => !empty($jwt->nama_pemakai) ? $jwt->nama_pemakai : null
                ]
            ]
        ];

        $json_data = is_array($data) ? json_encode($data) : json_encode(array());
        $param = 'Rujukan/delete';
        $action = 'DELETE';

        $full_url = $this->url.$param;
        $get_curl = self::curl($full_url, $json_data, self::getHeader(true), $action);

        return self::out($get_curl);
    }

    public function referensiPoliJkn()
    {
        $full_url = $this->url . "ref/poli/";
        $data = self::curl($full_url, false, self::getHeader(), 'GET');

        return self::out($data);
    }

    public function referensiDokterJkn()
    {
        $full_url = $this->url . "ref/dokter/";
        $data = self::curl($full_url, false, self::getHeader(), 'GET');

        return self::out($data);
    }

    public function cariSepInternal($sep)
    {
        $full_url = $this->url . "SEP/Internal/" . rawurlencode($sep);
        $data = self::curl($full_url, false, self::getHeader(), 'GET');
        return self::out($data);
    }

    public function deleteSepInternal($payload)
    {
        $param = 'SEP/Internal/delete';
        $action = 'DELETE';

        $data = [
            'request' => [
                't_sep' => $payload
            ]
        ];

        $json_data = is_array($data) ? json_encode($data) : json_encode(array());
        $full_url = $this->url.$param;
        $get_curl = self::curl($full_url, $json_data, self::getHeader(true), $action);

        return self::out($get_curl);
    }

    public function updateSep($data)
    {
        $json_data = is_array($data) ? json_encode($data) : json_encode(array());
        $param = 'SEP/2.0/update';
        $action = 'PUT';

        $full_url = $this->url . $param;
        $get_curl = self::curl($full_url, $json_data, self::getHeader(true), $action);

        return self::out($get_curl);
    }

    public function deleteSepNew($no_sep = null)
    {
        $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
        $data = [
            'request' => [
                't_sep' => [
                    'noSep' => $no_sep,
                    'user' => !empty($jwt->nama_pemakai) ? $jwt->nama_pemakai : 'admin',
                ]
            ]
        ];
        $json_data = is_array($data) ? json_encode($data) : json_encode(array());

        $param = 'SEP/2.0/delete';
        $full_url = $this->url . $param;
        $get_curl = self::curl($full_url, $json_data, self::getHeader(true), 'DELETE');

        return self::out($get_curl);
    }


    public function updateRujukan()
    {
        $param = 'Rujukan/update';
        $action = 'PUT';
        $data = [
            'request' => [
                't_rujukan' => [
                    'noRujukan' => $this->t_rujukan['noRujukan'],
                    'ppkDirujuk' => $this->t_rujukan['ppkDirujuk'],
                    'tipe' => $this->t_rujukan['tipeRujukan'],
                    'jnsPelayanan' => $this->t_rujukan['jnsPelayanan'],
                    'catatan' => $this->t_rujukan['catatan'],
                    'diagRujukan' => $this->t_rujukan['diagRujukan'],
                    'tipeRujukan' => $this->t_rujukan['tipeRujukan'],
                    'poliRujukan' => $this->t_rujukan['poliRujukan'],
                    'user' => $this->t_rujukan['user'],
                ]
            ]
        ];
        if(self::$version > 1.1) {
            $param = 'Rujukan/2.0/Update';
            $data['request']['t_rujukan']['tglRencanaKunjungan'] = $this->t_rujukan['tglRencanaKunjungan'];
            $data['request']['t_rujukan']['tglRujukan'] = $this->t_rujukan['tglRujukan'];
            if($this->t_rujukan == 0) {
                // $data['request']['t_rujukan']['poliRujukan'] = $this->t_rujukan['kodeSpesialis'];
            } else if ($this->t_rujukan == 2) {
                $data['request']['t_rujukan']['poliRujukan'] = ""; //"{kosong untuk tipe rujukan 2, harus diisi jika 0 atau 1}",
            }
        } else {
            $data['request']['t_rujukan']['tipe'] = $this->t_rujukan['tipeRujukan'];
        }
        $json_data = is_array($data) ? json_encode($data) : json_encode(array());
        $full_url = $this->url.$param;
        $get_curl = self::curl($full_url, $json_data, self::getHeader(true), $action);

        return self::out($get_curl);
    }


     /**
     * Insert Rencana Kontrol (POST)
     * @param mixed $value
     * @return mixed response web services
     */
    public function createRencanaKontrol()
    {
        $param = 'RencanaKontrol/insert';
        $action = 'POST';
        $data = [
            'request' => [
                "noSEP" => $this->t_rencanakontrol['no_sep'],
                "kodeDokter" => $this->t_rencanakontrol['dokterdpjp_kode'],
                "poliKontrol" => $this->t_rencanakontrol['kode_poli'],
                "tglRencanaKontrol" => $this->t_rencanakontrol['tgl_rencanakontrol'],
                "user" => $this->t_rencanakontrol['user'],
            ]
        ];
        $json_data = is_array($data) ? json_encode($data) : json_encode(array());
        $full_url = $this->url.$param;
        $get_curl = self::curl($full_url, $json_data, self::getHeader(true), $action);

        return self::out($get_curl);
    }

    /**
     * Insert Rencana Inaps (POST)
     * @param mixed $value
     * @return mixed response web services
     */
    public function createRencanaInap()
    {
        $param = 'RencanaKontrol/InsertSPRI';
        $action = 'POST';
        $data = [
            'request' => [
                "noKartu" => $this->t_rencanakontrol['no_kartu'],
                "kodeDokter" => $this->t_rencanakontrol['dokterdpjp_kode'],
                "poliKontrol" => $this->t_rencanakontrol['kode_poli'],
                "tglRencanaKontrol" => $this->t_rencanakontrol['tgl_rencanakontrol'],
                "user" => $this->t_rencanakontrol['user'],
            ]
        ];
        $json_data = is_array($data) ? json_encode($data) : json_encode(array());
        $full_url = $this->url.$param;
        $get_curl = self::curl($full_url, $json_data, self::getHeader(true), $action);

        return self::out($get_curl);
    }

    /**
     * Update Rencana Kontrol (PUT)
     * @param mixed $value
     * @return mixed response web services
     */
    public function updateRencanaKontrol()
    {
        $param = 'RencanaKontrol/Update';
        $action = 'PUT';
        $data = [
            'request' => [
                "noSuratKontrol" => $this->t_rencanakontrol['nosuratkontrol'],
                "noSEP" => $this->t_rencanakontrol['no_sep'],
                "kodeDokter" => $this->t_rencanakontrol['dokterdpjp_kode'],
                "poliKontrol" => $this->t_rencanakontrol['kode_poli'],
                "tglRencanaKontrol" => $this->t_rencanakontrol['tgl_rencanakontrol'],
                "user" => $this->t_rencanakontrol['user'],
            ]
        ];
        $json_data = is_array($data) ? json_encode($data) : json_encode(array());
        $full_url = $this->url.$param;
        $get_curl = self::curl($full_url, $json_data, self::getHeader(true), $action);

        return self::out($get_curl);
    }

    /**
     * Update Rencana Inaps (PUT)
     * @param mixed $value
     * @return mixed response web services
     */
    public function updateRencanaInap()
    {
        $param = 'RencanaKontrol/UpdateSPRI';
        $action = 'PUT';
        $data = [
            'request' => [
                "noSPRI" => $this->t_rencanakontrol['no_spri'],
                "kodeDokter" => $this->t_rencanakontrol['dokterdpjp_kode'],
                "poliKontrol" => $this->t_rencanakontrol['kode_poli'],
                "tglRencanaKontrol" => $this->t_rencanakontrol['tgl_rencanakontrol'],
                "user" => $this->t_rencanakontrol['user'],
            ]
        ];
        $json_data = is_array($data) ? json_encode($data) : json_encode(array());
        $full_url = $this->url.$param;
        $get_curl = self::curl($full_url, $json_data, self::getHeader(true), $action);

        return self::out($get_curl);
    }

    /**
     * Hapus Rencana Kontrol (DELETE)
     * @param mixed $value
     * @return mixed response web services
     */
    public function hapusRencanaKontrol($nosurat = null)
    {
        $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
        $param = 'RencanaKontrol/Delete';
        $action = 'DELETE';
        $data = [
            'request' => [
                't_suratkontrol' => [
                    "noSuratKontrol" => empty($nosurat) ? $this->t_rencanakontrol['nosuratkontrol'] :  $nosurat,
                    "user" => isset($this->t_rencanakontrol['user']) && !empty($this->t_rencanakontrol['user']) ?  $this->t_rencanakontrol['user']  : empty($jwt->nama_pemakai) ? "admin" : $jwt->nama_pemakai,
                ]
            ]
        ];
        $json_data = is_array($data) ? json_encode($data) : json_encode(array());
        $full_url = $this->url.$param;
        $get_curl = self::curl($full_url, $json_data, self::getHeader(true), $action);

        return self::out($get_curl);
    }

     /**
     * Update Tanggal Pulang 2.0 (PUT)
     * @param mixed $value
     * @return mixed response web services
     */
    public function updateTanggalPulangSepNew()
    {
        $param = 'SEP/2.0/updtglplg';
        $action = 'PUT';
        $data = [
            'request' => [
                't_sep' => [
                    "noSep" => $this->t_sep_new['noSep'],
                    "statusPulang" => $this->t_sep_new['statusPulang'],
                    "noSuratMeninggal" => $this->t_sep_new['noSuratMeninggal'],
                    "tglMeninggal" => $this->t_sep_new['tglMeninggal'],
                    "tglPulang" => $this->t_sep_new['tglPulang'],
                    "noLPManual" => $this->t_sep_new['noLPManual'],
                    "user" => $this->t_sep_new['user'],
                ]
            ]
        ];
        $json_data = is_array($data) ? json_encode($data) : json_encode(array());
        $full_url = $this->url.$param;
        $get_curl = self::curl($full_url, $json_data, self::getHeader(true), $action);

        return self::out($get_curl);
    }

    public function cariDataRencanaKontrol($bulan, $tahun, $nokartu = null, $filter)
    {
        if(!empty($nokartu)) {
            $param = 'RencanaKontrol/ListRencanaKontrol/Bulan/' .rawurlencode($bulan). '/Tahun/' .rawurlencode($tahun).'/Nokartu/' .rawurlencode($nokartu).'/filter/' .rawurlencode($filter).'';
        } else {
            $param = 'RencanaKontrol/ListRencanaKontrol/Bulan/' .rawurlencode($bulan). '/Tahun/' .rawurlencode($tahun).'/filter/' .rawurlencode($filter).'';
        }
        $action = 'GET';

        $full_url = $this->url . $param;
        $data = self::curl($full_url, false, self::getHeader(), $action);

        return self::out($data);
    }

    public function dataNomorSuratKontrol($tglAwal,$tglAkhir,$filter)
    {
        $param = 'RencanaKontrol/ListRencanaKontrol/tglAwal/'.rawurlencode($tglAwal).'/tglAkhir/'.rawurlencode($tglAkhir).'/filter/'.rawurlencode($filter);
        $action = 'GET';

        $full_url = $this->url . $param;
        $data = self::curl($full_url, false, self::getHeader(), $action);

        return self::out($data);
    }

    public function dataPoliRencanaKontrol($jnsKontrol,$nomor,$tglRencanaKontrol)
    {
        $param = 'RencanaKontrol/ListSpesialistik/JnsKontrol/' .rawurlencode($jnsKontrol).'/nomor/'.rawurlencode($nomor).'/TglRencanaKontrol/'.rawurlencode($tglRencanaKontrol);
        $action = 'GET';

        $full_url = $this->url . $param;
        $data = self::curl($full_url, false, self::getHeader(), $action);

        return self::out($data);
    }

    public function dataDokterRencanaKontrol($jnsKontrol,$kdPoli,$tglRencanaKontrol)
    {
        $param = 'RencanaKontrol/JadwalPraktekDokter/JnsKontrol/' .rawurlencode($jnsKontrol).'/KdPoli/'.rawurlencode($kdPoli).'/TglRencanaKontrol/'.rawurlencode($tglRencanaKontrol);
        $action = 'GET';

        $full_url = $this->url . $param;
        $data = self::curl($full_url, false, self::getHeader(), $action);

        return self::out($data);
    }

    public function createPengajuanSep($payload)
    {
        $param = 'Sep/pengajuanSEP';
        $action = 'POST';

        $data = [
            'request' => [
                't_sep' => $payload
            ]
        ];

        $json_data = is_array($data) ? json_encode($data) : json_encode(array());
        $full_url = $this->url.$param;
        $get_curl = self::curl($full_url, $json_data, self::getHeader(true), $action);

        return self::out($get_curl);
    }

    public function approvalPengajuanSep($payload)
    {
        $param = 'Sep/aprovalSEP';
        $action = 'POST';

        $data = [
            'request' => [
                't_sep' => $payload
            ]
        ];

        $json_data = is_array($data) ? json_encode($data) : json_encode(array());
        $full_url = $this->url.$param;
        $get_curl = self::curl($full_url, $json_data, self::getHeader(true), $action);

        return self::out($get_curl);
    }

    public function hapusRujukanKhusus($data)
    {
        $param = 'Rujukan/Khusus/delete';
        $action = 'DELETE';
        $json_data = is_array($data) ? json_encode($data) : json_encode(array());
        $full_url = $this->url.$param;
        $get_curl = self::curl($full_url, $json_data, self::getHeader(true), $action);

        return self::out($get_curl);
    }

    public function createRujukanKhusus()
    {
        $param = 'Rujukan/Khusus/insert';
        $action = 'POST';
        $data = [
                "noRujukan" => $this->t_rujukan_khusus['noRujukan'],
                "diagnosa" => $this->t_rujukan_khusus['diagnosa'],
                "procedure" => $this->t_rujukan_khusus['procedure'],
                "user" => $this->t_rujukan_khusus['user'],
            ];
        $json_data = is_array($data) ? json_encode($data) : json_encode(array());
        $full_url = $this->url.$param;
        $get_curl = self::curl($full_url, $json_data, self::getHeader(true), $action);

        return self::out($get_curl);
    }

    public function jumlahSEP($param)
    {
        $full_url = $this->url . "Rujukan/JumlahSEP/" . $param['jenisRujukan'] . "/" . $param['noRujukan'];
        $data = self::curl($full_url, false, self::getHeader(), 'GET');
        return self::out($data);
    }

    public function listPersetujuanSep($bulan, $tahun)
    {
        $full_url = $this->url . "Sep/persetujuanSEP/list/bulan/".$bulan."/tahun/".$tahun;
        $data = self::curl($full_url, false, self::getHeader(), 'GET');
        return self::out($data);
    }

    public function showConfig()
    {
        return self::getHeader();
    }

    public function suplesiJasaRaharja($noKartu,$tglPelayanan)
    {
        $full_url = $this->url . "sep/JasaRaharja/Suplesi/" . $noKartu . "/tglPelayanan/" . $tglPelayanan;
        $data = self::curl($full_url, false, self::getHeader(), 'GET');
        return self::out($data);
    }

    public function kecelakaanInduk($noKartu)
    {
        $full_url = $this->url . "sep/KllInduk/List/" . $noKartu;
        $data = self::curl($full_url, false, self::getHeader(), 'GET');
        return self::out($data);
    }

    public function monitoringJasaRaharja($jnsPelayanan,$tglMulai,$tglAkhir)
    {
        $full_url = $this->url . "monitoring/JasaRaharja/JnsPelayanan/".$jnsPelayanan."/tglMulai/".$tglMulai."/tglAkhir/".$tglAkhir;
        $data = self::curl($full_url, false, self::getHeader(), 'GET');
        return self::out($data);
    }
  
    public function cariRencanaKontrolByNoRencanaKontrol($no_surat_kontrol)
    {
        $full_url = $this->url ."RencanaKontrol/noSuratKontrol/".$no_surat_kontrol;
        $data = self::curl($full_url, false, self::getHeader(), 'GET');
        return self::out($data);
    }

    public function cariRujukanKhususByTanggal($bulan, $tahun)
    {
        $full_url = $this->url."Rujukan/Khusus/List/Bulan/".$bulan."/Tahun/".$tahun;
        $data = self::curl($full_url, false, self::getHeader(), 'GET');
        return self::out($data);
    }
}
