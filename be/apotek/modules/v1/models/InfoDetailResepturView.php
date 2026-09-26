<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infodetailreseptur_v".
 *
 * @property integer $resepturdetail_id
 * @property integer $reseptur_id
 * @property integer $pendaftaran_id
 * @property string $obatalkes_namalain
 * @property double $hargasatuan_reseptur
 * @property double $ppn
 * @property double $qty_reseptur
 * @property double $hargajual_reseptur
 */
class InfoDetailResepturView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'inforesepturdetail_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['resepturdetail_id', 'reseptur_id', 'pendaftaran_id'], 'integer'],
            [['obatalkes_namalain'], 'string'],
            [['hargasatuan_reseptur', 'ppn', 'qty_reseptur', 'hargajual_reseptur'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'resepturdetail_id' => 'Resepturdetail ID',
            'reseptur_id' => 'Reseptur ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'obatalkes_namalain' => 'Obatalkes Namalain',
            'hargasatuan_reseptur' => 'Hargasatuan Reseptur',
            'ppn' => 'Ppn',
            'qty_reseptur' => 'Qty Reseptur',
            'hargajual_reseptur' => 'Hargajual Reseptur',
        ];
    }
}
