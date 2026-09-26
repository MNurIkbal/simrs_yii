<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "mastertariftindakan_v".
 *
 * @property string $jenis_tindakan_paket
 * @property int $tariftindakan_id
 * @property int $tindakan_paket_id
 * @property string $nama_tindakan_paket
 * @property int $kelaspelayanan_id
 * @property string $kelaspelayanan_nama
 * @property int $carabayar_id
 * @property string $carabayar_nama
 * @property int $penjamin_id
 * @property string $penjamin_nama
 * @property int $perdatarif_id
 * @property string $perdanama_sk
 * @property int $persencyto_tindakan
 * @property int $persendiskon_tindakan
 * @property double $harga_tariftindakan
 * @property bool $is_active
 * @property datetime $created_date
 */
class MasterTarifTindakanView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mastertariftindakan_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jenis_tindakan_paket', 'nama_tindakan_paket'], 'string'],
            [['tariftindakan_id', 'tindakan_paket_id', 'kelaspelayanan_id', 'carabayar_id', 'penjamin_id', 'perdatarif_id', 'persencyto_tindakan', 'persendiskon_tindakan', 'created_date'], 'default', 'value' => null],
            [['tariftindakan_id', 'tindakan_paket_id', 'kelaspelayanan_id', 'carabayar_id', 'penjamin_id', 'perdatarif_id', 'persencyto_tindakan', 'persendiskon_tindakan'], 'integer'],
            [['harga_tariftindakan'], 'number'],
            [['is_active'], 'boolean'],
            [['kelaspelayanan_nama', 'carabayar_nama', 'penjamin_nama'], 'string', 'max' => 50],
            [['perdanama_sk'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'jenis_tindakan_paket' => 'Jenis Tindakan Paket',
            'tariftindakan_id' => 'Tariftindakan ID',
            'tindakan_paket_id' => 'Tindakan Paket ID',
            'nama_tindakan_paket' => 'Nama Tindakan Paket',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'kelaspelayanan_nama' => 'Kelaspelayanan Nama',
            'carabayar_id' => 'Carabayar ID',
            'carabayar_nama' => 'Carabayar Nama',
            'penjamin_id' => 'Penjamin ID',
            'penjamin_nama' => 'Penjamin Nama',
            'perdatarif_id' => 'Perdatarif ID',
            'perdanama_sk' => 'Perdanama Sk',
            'persencyto_tindakan' => 'Persencyto Tindakan',
            'persendiskon_tindakan' => 'Persendiskon Tindakan',
            'harga_tariftindakan' => 'Harga Tariftindakan',
            'is_active' => 'Is Active',
            'created_date' => 'Created Date',
        ];
    }
}
