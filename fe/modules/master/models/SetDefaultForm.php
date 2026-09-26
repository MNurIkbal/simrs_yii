<?php

namespace app\modules\master\models;
use Yii;
use app\components\DocoHelpers;
/**
 * This is the model class for table "tariftindakan_m".
 *
 * @property int $tariftindakan_id
 * @property int $kelaspelayanan_id
 * @property int $komponentarif_id
 * @property int $daftartindakan_id
 * @property int $jenistarif_id
 * @property int $perdatarif_id
 * @property double $harga_tariftindakan
 * @property int $persendiskon_tindakan
 * @property double $hargadiskon_tindakan
 * @property int $persencyto_tindakan
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
 * @property int $tipepaket_id
 */
class SetDefaultForm extends \yii\base\Model
{
    public $carabayar_awal;
    public $penjamin_awal;
    public $carabayar_tujuan;
    public $penjamin_tujuan;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['carabayar_awal','penjamin_awal','carabayar_tujuan','penjamin_tujuan'], 'required'],
            [['penjamin_tujuan'], 'compareCustom'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'carabayar_awal' => Yii::t('fe','Cara bayar'),
            'penjamin_awal' => Yii::t('fe','Penjamin'),
            'carabayar_tujuan' => Yii::t('fe','Cara bayar'),
            'penjamin_tujuan' => Yii::t('fe','Penjamin'),
        ];
    }

    public function compareCustom($param, $attribute)
    {
        if($this->penjamin_tujuan == $this->penjamin_awal){
            $this->addError('penjamin_tujuan', 'Penjamin tujuan tidak boleh sama dengan penjamin awal');
        }
    }

}
