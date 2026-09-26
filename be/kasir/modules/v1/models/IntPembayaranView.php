<?php

namespace app\modules\v1\models;

use Yii;

class IntPembayaranView extends \app\components\ActiveRepositories
{
    public $_repositori = 'app\components\repositories\OdooRepositories';
    const BAYAR = 'BYR';
    const DEPOSIT = 'UM';
    const REFUND = 'PUM';

    public $id_rekap;
    public $tipe_rekap;

    public function extraFields()
    {
        return ['id_rekap','tipe_rekap'];
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'int_pembayaran_v';
    }

    public function rules()
    {
        return [
            [['id_rekap', 'tipe_rekap'], 'safe'],
        ];
    }

    public function setIdRekap($value)
    {
        preg_match('/^(\D+)(\d+)$/', $value, $match);
        array_shift($match);
        $tipe = isset($match[0]) ? $match[0] : null;
        $id = isset($match[1]) ? $match[1] : null;
        switch ($tipe) {
            case ($tipe == self::BAYAR || $tipe == self::DEPOSIT || $tipe == self::REFUND):
                $this->setRekap($id, $tipe);
                break;
            default:
                $this->setRekap();
                break;
        }
    }

    private function setRekap($id = null, $type = null)
    {
        $this->tipe_rekap = $type;
        $this->id_rekap = $id;
    }
}
