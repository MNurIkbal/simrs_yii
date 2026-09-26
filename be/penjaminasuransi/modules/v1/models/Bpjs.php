<?php

namespace app\modules\v1\models;

use Yii;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\LogError;
use Doco\components\DocoConstants;

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
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property bool $is_cob
 * @property int $asal_rujukan
 * @property int $cetakan_ke
 * @property string $tgl_cetak
 * @property string $additional_request
 */
class Bpjs extends \yii\db\ActiveRecord
{
    public $url;
    public $cons_id;
    public $secret_key;
    public $ppkPelayanan;
    public $ppkPelayanan_nama;
    public $isLive;
    public $vclaim;

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
    public $t_sep_new;

    public $isktp;
    public $isrujukanrs;
    // public $user;
    // public $nomr;

    static protected $cons_ids;
    static protected $secret_keys;

    public function init()
    {
        $conf = @parse_ini_file(''.realpath(Yii::$app->basePath).'/config/env/.env', true);

        parent::init();
        // deprecated
        /* SANTUYUSUP */
        // $this->url = DocoConstants::URL_BPJS;
        // $this->cons_id = DocoConstants::CONS_ID_SANTUYUSUP;
        // $this->secret_key = DocoConstants::SECRET_KEY_SANTUYUSUP;
        // $this->ppkPelayanan = DocoConstants::PPKPELAYANAN_SANTUYUSUP;
        // $this->isLive = DocoConstants::ENV_LIVE_BPJS;
        $this->vclaim = isset($conf['inacbg']['env_vclaim']) ? $conf['inacbg']['env_vclaim'] :'';
        $cache = Yii::$app->cache;
        $cache_bpjs = $cache->get(DocoConstants::LOOKUP_BPJS);
        $targetEnv = ($this->vclaim == DocoConstants::LOOKUP_BPJS_LIVE) ? DocoConstants::LOOKUP_BPJS_LIVE : DocoConstants::LOOKUP_BPJS;
        if (!$cache_bpjs || $cache_bpjs) {
            $lookup = Lookup::find()->where(['lookup_type' => $targetEnv])
                ->asArray()
                ->all();
            $cache->set(DocoConstants::LOOKUP_BPJS, $lookup);
            $cache_bpjs = $cache->get(DocoConstants::LOOKUP_BPJS);
        }

        if ($cache_bpjs) {
            foreach ($cache_bpjs as $each) {
                if ($each['lookup_name'] == 'secret_key') {
                    if ($each['lookup_value'] != $this->secret_key) {
                        $this->secret_key = $each['lookup_value'];
                    }
                }
                if ($each['lookup_name'] == 'cons_id') {
                    if ($each['lookup_value'] != $this->cons_id) {
                        $this->cons_id = $each['lookup_value'];
                    }
                }
                if ($each['lookup_name'] == 'url') {
                    if ($each['lookup_value'] != $this->url) {
                        $this->url = $each['lookup_value'];
                    }
                }
                if ($each['lookup_name'] == 'ppkPelayanan') {
                    if ($each['lookup_value'] != $this->ppkPelayanan) {
                        $this->ppkPelayanan = $each['lookup_value'];
                    }
                }
                if ($each['lookup_name'] == 'ppkPelayanan_nama') {
                    if ($each['lookup_value'] != $this->ppkPelayanan_nama) {
                        $this->ppkPelayanan_nama = $each['lookup_value'];
                    }
                }
            }
        }

        self::$cons_ids = $this->cons_id;
        self::$secret_keys = $this->secret_key;
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
            // [
            //     [
            //         'tglsep', 'nosep', 'nokartuasuransi', 'tglrujukan', 'norujukan', 'ppkrujukan', 'jnspelayanan', 'diagnosaawal', 'politujuan', 'lakalantas'
            //     ], 
            //     'required'
            // ],
            [
                [
                    'tglsep', 'tglrujukan', 'tglpulang', 'created_date', 'last_modified_date', 'deleted_date', 'nama_peserta', 'ppkpelayanan', 'asal_rujukan', 'additional_request', 'nosep', 'nokartuasuransi', 'norujukan', 'ppkrujukan', 'jnspelayanan', 'diagnosaawal', 'politujuan', 'lakalantas', 'no_rekam_medik', 'pendaftaran_id', 'pasienadmisi_id', 'is_cob', 'asal_rujukan', 'cetakan_ke', 'tgl_cetak', 'additional_request'
                ],
                'safe'
            ],
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
        return strval(time() - strtotime('1970-01-01 00:00:00'));
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
                'metadata' => [
                    "code" => 500,
                    "message" => curl_error($curl) ?: "Problem with bridging",
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
        return $data && is_string($data) && json_decode($data) ? json_decode($data, true) : "";
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
            "X-cons-id : " . self::$cons_ids,
            "X-timestamp : " . self::getTimestamp(),
            "X-signature : " . self::getSignature(),
            $conten_type
        ];
    }


    /**
     * Get Peserta by Nomor BPJS or Nomor KTP.
     * @param mixed $id param input from Nomor BPJS or Nomor KTP
     * @param mixed $is_ktp param using Nomor KTP
     * @return mixed response web services
     */
    public function peserta($nokartu, $tglSEP = null, $is_ktp = false)
    {
        $param = "Peserta/nokartu/{$nokartu}/tglSEP/{$tglSEP}";
        if ($is_ktp) {
            $param = "Peserta/nik/{$nokartu}/tglSEP/{$tglSEP}";
        }
        $data = self::curl($this->url . $param, false, self::getHeader(true));
        // $data['peserta']['informasi']['prolanisPRB'] = 'test';
        $res =  self::out($data);

        $model = new LogError;
        $model->nama = 'Peserta';
        $model->pesan = json_encode($res);
        $model->tgl_error = date('Y-m-d h:i:s');
        $model->save();

        return $res;
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
        $url_rujukan = "Rujukan/$nomor";
        if ($asal_rujukan == 2) {
            $url_rujukan = "Rujukan/RS/$nomor";
        }

        $full_url = $this->url . $url_rujukan;
        $data_rujukan = self::curl($full_url, false, self::getHeader(), 'GET');

        $res = self::out($data_rujukan);

        $model = new LogError;
        $model->nama = 'Peserta';
        $model->pesan = json_encode($res);
        $model->tgl_error = date('Y-m-d h:i:s');
        $model->save();
        return $res;
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

        $res = self::out($data_rujukan);

        $model = new LogError;
        $model->nama = 'Rujukan Peserta';
        $model->pesan = json_encode($res);
        $model->tgl_error = date('Y-m-d h:i:s');
        $model->save();
        return $res;
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
        $res =  self::out($data);

        $model = new LogError;
        $model->nama = 'Referensi Poli';
        $model->pesan = json_encode($res);
        $model->tgl_error = date('Y-m-d h:i:s');
        $model->save();
        return $res;
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

        $res = self::out($data);

        $model = new LogError;
        $model->nama = 'Referensi Diagnosa';
        $model->pesan = json_encode($res);
        $model->tgl_error = date('Y-m-d h:i:s');
        $model->save();
        return $res;
    }
    /**
     * Pencarian Data faskes
     * Method : GET
     * @return mixed response web services
     */
    public function referensiFaskes($kode, $faskes = 1)
    {
        $encodedKode = rawurlencode($kode);
        $full_url = $this->url . "referensi/faskes/" . $encodedKode . '/' . $faskes;
        $data = self::curl($full_url, false, self::getHeader(), 'GET');
        $res = self::out($data);

        $model = new LogError;
        $model->nama = 'Referensi Faskes';
        $model->pesan = json_encode($res);
        $model->tgl_error = date('Y-m-d h:i:s');
        $model->save();
        return $res;
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
        $res =  self::out($data);

        $model = new LogError;
        $model->nama = 'Referensi Dokter';
        $model->pesan = json_encode($res);
        $model->tgl_error = date('Y-m-d h:i:s');
        $model->save();
        return $res;
    }

    /**
     * Pencarian histori pelayanan
     * Method : GET
     * @return mixed response web services
     */
    public function historiPelayanan($nokartu, $tglmulai, $tglselesai)
    {
        $full_url = $this->url . "monitoring/HistoriPelayanan/NoKartu/" . $nokartu . "/tglAwal/" . $tglmulai . "/tglAkhir/" . $tglselesai;
        $data = self::curl($full_url, false, self::getHeader(), 'GET');
        $res = self::out($data);

        $model = new LogError;
        $model->nama = 'Referensi Pelayanan';
        $model->pesan = json_encode($res);
        $model->tgl_error = date('Y-m-d h:i:s');
        $model->save();
        return $res;
    }

    public function getHeader2()
    {
        return self::getHeader();
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
            'request' => [
                't_sep' => [
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
                        'ppkRujukan' => $this->t_sep['ppkRujukan']
                    ],
                    'catatan' => $this->t_sep['catatan'],
                    'diagAwal' => $this->t_sep['diagAwal'],
                    'poli' => [
                        'tujuan' => $this->t_sep['tujuan'],
                        'eksekutif' => $this->t_sep['eksekutif']
                    ],
                    'cob' => [
                        'cob' => $this->t_sep['cob']
                    ],
                    'jaminan' => [
                        'lakaLantas' => $this->t_sep['lakaLantas'],
                        'penjamin' => $this->t_sep['penjamin'],
                        'lokasiLaka' => $this->t_sep['lokasiLaka']
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
        $res =  self::out($get_curl);

        $model = new LogError;
        $model->nama = 'Create SEP';
        $model->pesan = json_encode($res);
        $model->tgl_error = date('Y-m-d h:i:s');
        $model->save();
        return $res;
    }

    public function createSepNew()
    {
        $data = [
            'request' => [
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

        $json_data = is_array($data) ? json_encode($data) : json_encode(array());

        $param = 'SEP/1.1/insert';
        $action = 'POST';

        $full_url = $this->url . $param;
        $this->t_sep_new = $json_data;

        $get_curl = self::curl($full_url, $json_data, self::getHeader(true), $action);
        $res = self::out($get_curl);

        $model = new LogError;
        $model->nama = 'Create SEP NEW';
        $model->pesan = json_encode($res);
        $model->tgl_error = date('Y-m-d h:i:s');
        $model->save();
        return $res;
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
        $res = self::out($get_curl);

        $model = new LogError;
        $model->nama = 'Delete Sep';
        $model->pesan = json_encode($res);
        $model->tgl_error = date('Y-m-d h:i:s');
        $model->save();
        return $res;
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
     *
     * Get data referensi propinsi
     * method : GET
     * @return list propinsi
     *
     */
    public function getPropinsi()
    {
        $full_url = $this->url . "/referensi/propinsi";
        // $data = self::curl($full_url, false, self::getHeader(), 'GET');
        $data = self::curl($full_url, false, self::getHeader(), 'GET');
        $data = json_decode($data, true);

        // list data return
        $list_return = [];
        $list_propinsi = isset($data['response']['list']) ? $data['response']['list'] : [];

        return $list_propinsi;
    }
    /**
     *
     * Get data referensi kabupaten
     * method : GET
     * params: kode propinsi
     * @return list kabupaten
     *
     */
    public function getKabupaten($kodepropinsi)
    {
        $full_url = $this->url . "/referensi/kabupaten/propinsi/" . $kodepropinsi;
        // $data = self::curl($full_url, false, self::getHeader(), 'GET');
        $data = self::curl($full_url, false, self::getHeader(), 'GET');
        $data = json_decode($data, true);
        return isset($data['response']['list']) ? $data['response']['list'] : [];
    }
    /**
     *
     * Get data referensi kecamatan
     * method : GET
     * params: kode kabupaten
     * @return list kecamatan
     *
     */
    public function getKecamatan($kodekabupaten)
    {
        $full_url = $this->url . "/referensi/kecamatan/kabupaten/" . $kodekabupaten;
        // $data = self::curl($full_url, false, self::getHeader(), 'GET');
        $data = self::curl($full_url, false, self::getHeader(), 'GET');
        $data = json_decode($data, true);
        return isset($data['response']['list']) ? $data['response']['list'] : [];
    }

    public function getPesertaRujukan($asal_rujukan = 1, $nokartu = '')
    {
        $full_url = $this->url . "Rujukan/List/Peserta/" . $nokartu;
        if ($asal_rujukan == 2) {
            $full_url = $this->url . "Rujukan/RS/List/Peserta/" . $nokartu;
        }
        // return $full_url;
        // $data = self::curl($full_url, false, self::getHeader(), 'GET');
        $data = self::curl($full_url, false, self::getHeader(), 'GET');

        $res =  self::out($data);

        $model = new LogError;
        $model->nama = 'Get Peserta Rujukan';
        $model->pesan = json_encode($res);
        $model->tgl_error = date('Y-m-d h:i:s');
        $model->save();
        return $res;
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
    public function updateTanggalPulangSep($user)
    {
        $data = [
            'request' => [
                't_sep' => [
                    'noSep' => $this->nosep,
                    'tglPulang' => date('Y-m-d H:i:s'),
                    'user' => $user,
                ],
            ]
        ];
        $json_data = is_array($data) ? json_encode($data) : json_encode(array());
        $param = 'Sep/updtglplg';
        $action = 'PUT';

        $full_url = $this->url . $param;

        $get_curl = self::curl($full_url, $json_data, self::getHeader(true), $action);
        $res =  self::out($get_curl);

        $model = new LogError;
        $model->nama = 'Update Tanggal Pulang Sep';
        $model->pesan = json_encode($res);
        $model->tgl_error = date('Y-m-d h:i:s');
        $model->save();
        return $res;
    }

    /*
     * Pencarian Potensi Suplesi Jasa Raharja
     * Method : GET
     * @return mixed response web services
     */
    public function potensiSuplesi($nokartu, $tglSep)
    {
        $full_url = $this->url . "sep/JasaRaharja/Suplesi/" . $nokartu . "/tglPelayanan/" . $tglSep;
        $data = self::curl($full_url, false, self::getHeader(), 'GET');
        $res =  self::out($data);

        $model = new LogError;
        $model->nama = 'Potensi Suuplesi';
        $model->pesan = json_encode($res);
        $model->tgl_error = date('Y-m-d h:i:s');
        $model->save();
        return $res;
    }

    public function pengajuanSep()
    {
        $data = [
            'request' => [
                't_sep' => [
                    'noKartu' => $this->t_sep_new['noKartu'],
                    'tglSep' => date('Y-m-d', strtotime($this->t_sep_new['tglSep'])),
                    'jnsPelayanan' => $this->t_sep_new['jnsPelayanan'],
                    'keterangan' => $this->t_sep_new['keterangan'],
                    'user' => $this->t_sep_new['user']
                ]
            ]
        ];
        $json_data = is_array($data) ? json_encode($data) : json_encode(array());
        $param = 'Sep/pengajuanSEP';
        $action = 'POST';
        $full_url = $this->url . $param;
        $get_curl = self::curl($full_url, $json_data, self::getHeader(true), $action);
        $res =  self::out($get_curl);

        $model = new LogError;
        $model->nama = 'Pengajuan Sep';
        $model->pesan = json_encode($res);
        $model->tgl_error = date('Y-m-d h:i:s');
        $model->save();
        return $res;
    }

    public function approvalSep()
    {
        $data = [
            'request' => [
                't_sep' => [
                    'noKartu' => $this->t_sep_new['noKartu'],
                    'tglSep' => date('Y-m-d', strtotime($this->t_sep_new['tglSep'])),
                    'jnsPelayanan' => $this->t_sep_new['jnsPelayanan'],
                    'keterangan' => $this->t_sep_new['keterangan'],
                    'user' => $this->t_sep_new['user']
                ]
            ]
        ];
        $json_data = is_array($data) ? json_encode($data) : json_encode(array());
        $param = 'Sep/aprovalSEP';
        $action = 'POST';
        $full_url = $this->url . $param;
        $get_curl = self::curl($full_url, $json_data, self::getHeader(true), $action);
        $res =  self::out($get_curl);

        $model = new LogError;
        $model->nama = 'Find Sep';
        $model->pesan = json_encode($res);
        $model->tgl_error = date('Y-m-d h:i:s');
        $model->save();
        return $res;
    }   

    public function findSep($no_sep)
    {   
        $param = 'SEP/' . $no_sep;
        $action = 'GET';
        $full_url = $this->url . $param;
        $get_curl = self::curl($full_url, false, self::getHeader(true), $action);
        $res = self::out($get_curl);
        
        $model = new LogError;
        $model->nama = 'Find Sep';
        $model->pesan = json_encode($res);
        $model->tgl_error = date('Y-m-d h:i:s');
        $model->save();

        return $res;
    }

    /**
     * @function : Cek Integrasi SEP dan Inacbg 4.1
     */
    public function cekbridging($no_sep)
    {   
        $param = 'SEP/CBG/' . $no_sep;
        $action = 'GET';
        $full_url = $this->url . $param;
        $get_curl = self::curl($full_url, false, self::getHeader(true), $action);
        $res =  self::out($get_curl);

        $model = new LogError;
        $model->nama = 'Cek Bridging';
        $model->pesan = json_encode($res);
        $model->tgl_error = date('Y-m-d h:i:s');
        $model->save();
        return $res;
    }

    public function updateSep()
    {
        $data = [
            'request' => [
                't_sep' => $this->t_sep
            ]
        ];
        $json_data = is_array($data) ? json_encode($data) : json_encode(array());
        $param = 'SEP/1.1/Update';
        $action = 'PUT';

        $full_url = $this->url . $param;
        $get_curl = self::curl($full_url, $json_data, self::getHeader(true), $action);
        $res = self::out($get_curl);

        $model = new LogError;
        $model->nama = 'Cek Bridging';
        $model->pesan = json_encode($res);
        $model->tgl_error = date('Y-m-d h:i:s');
        $model->save();
        return $res;
    }
}
