<?php
namespace app\modules\v1\components;

use Yii;
use app\modules\v1\cache\Cache;

use app\modules\v1\payload\WilayahPayload;

Class Wilayah 
{
    protected $limit;
    protected $params;

    /**
     * Empty construct instance.
     *
     * @return void
     */
    public function __construct()
    {
        $request = Yii::$app->request;
        $this->params = $request->get();
        $this->limit = 10;
    }

    /**
     * @method get list negara
     * @param int negara_id (opt)
     * @param str negara_nama (opt)
     * @param int limit (opt)
     * 
     * @return array
     * @author : Erlangga (librantara.erlangga@sirs.com)
     */
    public function getNegara()
    {
        $data = Cache::Negara();
        return $this->generateData($data);
    }

    /**
     * @method get list propinsi
     * @param int propinsi_id (opt)
     * @param int kode_kemendagri_propinsi (opt)
     * @param str propinsi_nama (opt)
     * @param int limit (opt)
     * 
     * @return array
     * @author : Erlangga (librantara.erlangga@sirs.com)
     */
    public function getPropinsi()
    {
        $data = Cache::Propinsi();
        return $this->generateData($data);
    }

    /**
     * @method get list kabupaten
     * @param int propinsi_id (opt)
     * @param int kabupaten_id (opt)
     * @param int kode_kemendagri_propinsi (opt)
     * @param int kode_kemendagri_kabupaten (opt)
     * @param str kabupaten_nama (opt)
     * @param int limit (opt)
     * 
     * @return array
     * @author : Erlangga (librantara.erlangga@sirs.com)
     */
    public function getKabupaten()
    {
        $data = Cache::Kabupatan();
        return $this->generateData($data);
    }

    /**
     * @method get list kecamatan
     * @param int propinsi_id (opt)
     * @param int kabupaten_id (opt)
     * @param int kecamatan_id (opt)
     * @param int kode_kemendagri_propinsi (opt)
     * @param int kode_kemendagri_kabupaten (opt)
     * @param int kode_kemendagri_kecamatan (opt)
     * @param str kecamatan_nama (opt)
     * @param int limit (opt)
     * 
     * @return array
     * @author : Erlangga (librantara.erlangga@sirs.com)
     */
    public function getKecamatan()
    {
        $data = Cache::Kecamatan();
        return $this->generateData($data);
    }

    /**
     * @method get list kecamatan
     * @param int propinsi_id 
     * @param int kabupaten_id 
     * @param int kecamatan_id 
     * @param int kelurahan_id (opt) 
     * @param int kode_kemendagri_propinsi (opt)
     * @param int kode_kemendagri_kabupaten (opt)
     * @param int kode_kemendagri_kecamatan (opt)
     * @param int kode_kemendagri_kelurahan (opt)
     * @param str kelurahan_nama  (opt)
     * @param int limit (opt)
     * 
     * @return array
     * @author : Erlangga (librantara.erlangga@sirs.com)
     */
    public function getKelurahan()
    {
        $model = new WilayahPayload;
        if(isset($this->params['kode_kemendagri_propinsi'])) {
            $this->params['propinsi_id'] = $this->params['kode_kemendagri_propinsi'];
        }
        if(isset($this->params['kode_kemendagri_kabupaten'])) {
            $this->params['kabupaten_id'] = $this->params['kode_kemendagri_kabupaten'];
        }
        if(isset($this->params['kode_kemendagri_kecamatan'])) {
            $this->params['kecamatan_id'] = $this->params['kode_kemendagri_kecamatan'];
        }
        $model->scenario = 'get-kelurahan';
        $model->attributes = $this->params;
        if(!$model->validate()) {
            return $model->errors;
        } 
        $data = Cache::Kelurahan($model->propinsi_id, $model->kabupaten_id, $model->kecamatan_id);
        return $this->generateData($data);
    }

    private function generateData($data)
    {
        if(!empty($this->params)) {
            foreach($this->params as $k => $v) {
                if(substr($k, -3) == '_id') {
                    $data = array_filter($data, function($value, $key) use($v, $k) {
                        return $value[$k] == (int) $v;
                    }, ARRAY_FILTER_USE_BOTH);
                } else if(substr($k, -5) == '_nama' || substr($k, 0 ,5) == 'nama_') {
                    $data = array_filter($data, function($value, $key) use($v, $k) {
                        return preg_match("/{$v}/i", $value[$k]);
                    }, ARRAY_FILTER_USE_BOTH);
                } else if (substr($k, 0, 5) == 'kode_') {
                    $data = array_filter($data, function($value, $key) use($v, $k) {
                        return $value[$k] == $v;
                    }, ARRAY_FILTER_USE_BOTH);
                }
            }
        }
        
        if(isset($this->params['limit'])) {
            $this->limit = $this->params['limit'];
        }

        return array_slice($data, 0, $this->limit);
    }
}