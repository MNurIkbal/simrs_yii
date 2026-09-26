<?php
namespace app\modules\v1\components;

use Yii;
use app\modules\v1\cache\Cache;

use app\modules\v1\models\PenomoranK;
use Doco\Services\Vendors\PendaftaranService;
Class Penomoran 
{
    protected $type;
    protected $number;
    protected $year;
    protected $_baseRoute;

    /**
     * Empty construct instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->year = date('y', strtotime('NOW'));
        $this->_baseRoute = 'app/get-no-seri/';
    }

    /**
     * @method generate nomorpendaftaran ucuper
     * @param int $insId
     * 
     * @return array [no_reg, konfig_id]
     * @author : Erlangga (erlangga@docotel.com)
     */
    public function setNoReg($insId)
    {
        $result = [];
        $insMap = Cache::getPrefixMap($insId);

        if(!empty($insMap)) {
            $this->type = $insMap['prefix'];
            $konfig = $this->getModel($insMap['penomoran_id']);

            // 1 = getnew no
            // 0 = get exsist
            if($konfig->flag_refresh == 1) {
                $params['route'] = $this->_baseRoute. $this->type; 
                $this->number = (new PendaftaranService)->setNoSeri($params);
                if(!empty($this->number)) {
                    $result = [
                        'no_reg' => $this->generateNomorPendaftaran(),
                        'konfig_id' => $konfig->penomoran_id
                    ];
                }
            } else {
                $result = [
                    'no_reg' => $konfig->last_generate,
                    'konfig_id' => $konfig->penomoran_id
                ];
            }
        }


        return $result;
    }

    public function getModel($id = null)
    {
        $model = PenomoranK::find();
        if(is_null($id)) {
            return $model->asArray()->all();
        } else {
            return $model->where(['penomoran_id' => $id])->one();
        }
    }

    public function save($id, $flag = 0, $lastNum = null, $lastGen = null)
    {
        $model = $this->getModel($id);
        $model->last_generate = $lastNum;
        $model->last_number = $lastGen;
        $model->flag_refresh = $flag;
        $model->save(false);
        return true;
    }

    public function getSetNoRm($prefix = null, $penomoran_id = null, $function = 'get')
    {
        $result = [];
        $last_generate = null;
        $kodeSeri = 'RM' . $prefix;

        if(!empty($penomoran_id)) {
            $this->type = $kodeSeri;
            $konfig = $this->getModel($penomoran_id);

            // 1 = getnew no
            // 0 = get exsist
            if($konfig->flag_refresh == 1) {
                if ($function == 'save') {
                    $params['route'] = 'app/get-set-no-rm/save/' . $this->type; 
                    $this->number = (new PendaftaranService)->setNoSeri($params);
                    if(!empty($this->number)) {
                        $last_generate = $prefix . $this->number;
                    }
                } else {
                    $params['route'] = 'app/get-set-no-rm/get/' . $this->type; 
                    $this->number = (new PendaftaranService)->setNoSeri($params);
                    if(!empty($this->number)) {
                        $last_generate = $prefix . $this->number;
                        $this->save($penomoran_id, 1, $this->number, $last_generate);
                    }
                }

                $result = [
                    'no_rm' => $last_generate,
                    'konfig_id' => $konfig->penomoran_id
                ];
            } else {
                $result = [
                    'no_reg' => $konfig->last_generate,
                    'konfig_id' => $konfig->penomoran_id
                ];
            }
        }


        return $result;
    }

    private function generateNomorPendaftaran($length = 6, $prefix = '0', $pad = STR_PAD_LEFT)
    {
        $seqNumber = str_pad($this->number, $length , $prefix , $pad);
        $prefixReg = $this->type . $this->year;
        $result = $prefixReg . $seqNumber; 

        return $result;
    }
}