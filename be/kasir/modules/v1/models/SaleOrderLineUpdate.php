<?php

namespace app\modules\v1\models;

use Yii;

class SaleOrderLineUpdate extends \app\components\ActiveRepositories
{

    public $_repositori = 'app\components\repositories\OdooRepositories';
    const TINDAKAN = 'TND';
    const OBAT = 'OBT';

    public $id_rekap;
    public $tipe_rekap;

    public function extraFields()
    {
        return ['id_rekap','tipe_rekap'];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'saleorder_line_update_v';
    }

    public function rules()
    {
        return [
            [['id_rekap', 'tipe_rekap'], 'safe'],
        ];
    }

    public function setIdRekap($value)
    {
        $this->tipe_rekap = self::OBAT;
        $pattern = '/'. self::TINDAKAN .'/i';
        $march = preg_match($pattern, $value);
        $value = preg_replace("/[^0-9]/", "", $value);
        if ($march) {
            $this->tipe_rekap = self::TINDAKAN;
        }

        $this->id_rekap = $value;
    }
}
