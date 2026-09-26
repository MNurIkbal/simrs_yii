<?php

namespace app\modules\v1\models;

use Yii;

class SaleOrderBilling extends \app\components\ActiveRepositories
{

    public $_repositori = 'app\components\repositories\OdooRepositories';

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
        return 'int_billing_v';
    }

    public function rules()
    {
        return [
            [['id_rekap', 'tipe_rekap'], 'safe'],
        ];
    }

    public function setIdRekap($value)
    {
        $value = preg_replace("/[^0-9]/", "", $value);

        $this->id_rekap = $value;
    }
}
