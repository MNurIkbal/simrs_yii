<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;
use Integrasi\Service\Sirs\Models\Lookup;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\Services\ApiBPJSLZString;


class BpjsJkn extends Bpjs
{
    public $user_key;
    static protected $user_keys;
    static protected $version = 1.0;

    public function init()
    {
        parent::init();
        
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
                if ($each['lookup_name'] == 'url_jkn') {
                    $this->url = $each['lookup_value'];
                }
                if ($each['lookup_name'] == 'ppkPelayanan') {
                    $this->ppkPelayanan = $each['lookup_value'];
                }
                
                if ($each['lookup_name'] == 'version') {
                    self::$version = $each['lookup_value'];
                }

                if ($each['lookup_name'] == 'user_key') {
                    $this->user_key = $each['lookup_value'];
                }
            }
        }

       
        self::$cons_ids = $this->cons_id;
        self::$secret_keys = $this->secret_key;
        self::$user_keys = $this->user_key;

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

    public function showConfig()
    {
        return self::getHeader();
    }

    public function referensiPoliJkn()
    {
        $full_url =  $this->url ."ref/poli/";
        $data = self::curl($full_url, false, self::getHeader(), 'GET');

        return self::out($data);
    }

    public function referensiDokterJkn()
    {
        $full_url = $this->url . "ref/dokter/";
        $data = self::curl($full_url, false, self::getHeader(), 'GET');
        
        return self::out($data);
    }

    public function updateAntrianJkn($data = [])
    {
        if (empty($data)) {
            $data = [
                'kodebooking' => $this->antrian_jkn['kodebooking'],
                'taskid' => $this->antrian_jkn['taskid'],
                'waktu' => $this->antrian_jkn['waktu']
            ];
        }
        
        $json_data = is_array($data) ? json_encode($data) : json_encode(array());
        $param = 'antrean/updatewaktu';
        $action = 'POST';
        
        $full_url = $this->url . $param;
        $get_curl = self::curl($full_url, $json_data, self::getHeader(true), $action);
        
        return self::out($get_curl);
    }

    public function batalAntrianJkn($data = [])
    {
        if (empty($data)) {
            $data = [
                'kodebooking' => $this->antrian_jkn['kodebooking'],
                'keterangan' => $this->antrian_jkn['keterangan']
            ];
        }
        $json_data = is_array($data) ? json_encode($data) : json_encode(array());
        $param = 'antrean/batal';
        $action = 'POST';
        
        $full_url = $this->url . $param;
        $get_curl = self::curl($full_url, $json_data, self::getHeader(true), $action);
        
        return self::out($get_curl);
    }

    public function simpanAntrianJkn($data)
    {
        // $tglestimasi = date('Y-m-d', strtotime($request['tanggal_periksa']));
        // $jamestimasi = date('H:i:s', strtotime($request['jam_mulai']));
        // $waktuestimasi = $tglestimasi . " " . $jamestimasi;
        // $estimasi = DocoHelpers::generateTimeStamp($waktuestimasi);
        // $data = [
        //     'kodebooking' => $request['kodebooking'],
        //     'jenispasien' => $request['jenispasien'],
        //     'nomorkartu' => $request['nomorkartu'],
        //     'nik' => $request['no_identitas_pasien'],
        //     'nohp' => $request['no_telepon_pasien'],
        //     'kodepoli' => $request['kodepoli'],
        //     'namapoli' => $request['namapoli'],
        //     'pasienbaru' => $request['status_pasien'],
        //     'norm' => $request['no_rekam_medik'],
        //     'tanggalperiksa' => date('Y-m-d', strtotime($request['tanggal_periksa'])),
        //     'kodedokter' => $request['kodedokter'],
        //     'namadokter' => $request['namadokter'],
        //     'jampraktek' => $request['jampraktek'],
        //     'jeniskunjungan' => $request['jeniskunjungan'],
        //     'nomorreferensi' => $request['nomorreferensi'],
        //     'nomorantrean' => $request['nomorantrean'],
        //     'angkaantrean' => $request['angkaantrean'],
        //     'estimasidilayani' => $estimasi,
        //     'sisakuotajkn' => $request['sisakuotajkn'],
        //     'kuotajkn' => $request['kuotajkn'],
        //     'sisakuotanonjkn' => $request['sisakuotanonjkn'],
        //     'kuotanonjkn' => $request['kuotanonjkn'],
        //     'keterangan' => $request['keterangan'],
        // ];
        $json_data = is_array($data) ? json_encode($data) : json_encode(array());
        $param = 'antrean/add';
        $action = 'POST';

        $full_url = $this->url . $param;
        $get_curl = self::curl($full_url, $json_data, self::getHeader(), $action);

        return self::out($get_curl);
    }

    public function referensiJadwalDokterJkn($kdpoli, $tgl)
    {
        $tgl = !empty($tgl) ? date('Y-m-d',strtotime($tgl)) : date('Y-m-d');

        $full_url = $this->url . "jadwaldokter/kodepoli/{$kdpoli}/tanggal/{$tgl}";
        $data = self::curl($full_url, false, self::getHeader(), 'GET');
        return self::out($data);
    }

    public function getListTaskJkn($request)
    {
        $data = [
            'kodebooking' => $request['kodebooking'],
        ];
        $json_data = is_array($data) ? json_encode($data) : json_encode(array());
        $param = '/antrean/getlisttask';
        $action = 'POST';
        
        $full_url = $this->url . $param;
        $get_curl = self::curl($full_url, $json_data, self::getHeader(true), $action);
        
        return self::out($get_curl);
    }

    public function updateJadwalDokterJkn($request)
    {
        $data = [
            'kodepoli' => $request['kodepoli'],
            'kodesubspesialis' => $request['kodesubspesialis'],
            'kodedokter' => $request['kodedokter'],
            'jadwal' => $request['jadwal'],
        ];
        $json_data = is_array($data) ? json_encode($data) : json_encode(array());
        $param = 'jadwaldokter/updatejadwaldokter';
        $action = 'POST';

        $full_url = $this->url . $param;
        $get_curl = self::curl($full_url, $json_data, self::getHeader(), $action);

        return self::out($get_curl);
    }

    public function addAntrianFarmasi($data)
    {
        if (empty($data)) {
            $data = [
                'kodebooking' => $this->antrian_jkn['kodebooking'],
                'jenisresep' => $this->antrian_jkn['jenisresep'],
                'nomorantrean' => $this->antrian_jkn['nomorantrean'],
                'keterangan' => $this->antrian_jkn['keterangan'],
            ];
        }

        $json_data = is_array($data) ? json_encode($data) : json_encode(array());
        $param = 'antrean/farmasi/add';
        $action = 'POST';
        
        $full_url = $this->url . $param;
        $get_curl = self::curl($full_url, $json_data, self::getHeader(true), $action);
        
        return self::out($get_curl);
    }
}
