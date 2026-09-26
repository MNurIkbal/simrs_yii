<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "tariftindakanruangandetail_v".
 *
 * @property integer $daftartindakan_id
 * @property integer $kelaspelayanan_id
 * @property integer $penjamin_id
 * @property string $komponentarif_nama
 * @property double $harga_tariftindakan
 * @property smallint $persencyto_tindakan
 * @property smallint $persendiskon_tindakan
 */
class TarifTindakanRuanganDetailView extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tariftindakanruangandetail_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['daftartindakan_id', 'kelaspelayanan_id', 'penjamin_id', 'komponentarif_nama', 'harga_tariftindakan', 'persencyto_tindakan', 'persendiskon_tindakan'], 'integer'],
            [['komponentarif_nama','harga_tariftindakan'], 'safe'],
            [['komponentarif_nama'], 'string'],
            [['harga_tariftindakan'], 'number'],
            [['komponentarif_nama'], 'string', 'max' => 150],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'daftartindakan_id' => 'Daftar Tindakan ID',
            'kelaspelayanan_id' => 'Kelas Pelayanan ID',
            'penjamin_id' => 'Penjamin ID',
            'komponentarif_nama' => 'Nama Komponen Tarif',
            'harga_tariftindakan' => 'Harga Tarif Tindakan',
            'persencyto_tindakan' => 'Persencyto Tindakan',
            'persendiskon_tindakan' => 'Persen Diskon Tindakan',
        ];
    }
}
