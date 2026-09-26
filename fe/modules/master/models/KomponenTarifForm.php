<?php

namespace app\modules\master\models;

use Yii;

/**
 * This is the model class for table "komponentarif_m".
 *
 * @property string $komponentarif_kode
 * @property string $komponentarif_nama
 * @property string $komponentarif_namalainnya
 * @property double $persen_delegasi
 * @property bool $is_active
 * @property string $catatan
 */
class KomponenTarifForm extends \yii\base\Model
{
    /**
     * {@inheritdoc}
     */
    
     public $komponentarif_id;
     public $komponentarif_kode;
     public $komponentarif_nama;
     public $komponentarif_namalainnya;
     public $persen_delegasi;
     public $is_active;
     public $catatan;
     public $jenis_komponen;

    public function rules()
    {
        return [
            [['komponentarif_nama', 'komponentarif_namalainnya','komponentarif_kode'], 'required','message'=>'{attribute} '.Yii::t('fe','Tidak boleh kosong')],
            // [['persen_delegasi'], 'number'],
            [['catatan'], 'string'],
            [['komponentarif_nama','komponentarif_kode','komponentarif_namalainnya','persen_delegasi','is_active'], 'safe'],
            [['jenis_komponen','persen_delegasi'], 'default', 'value' => null],
            [[], 'integer'],
            [['is_active'], 'boolean'],
            [['komponentarif_nama', 'komponentarif_namalainnya'], 'string', 'max' => 25],
            [['komponentarif_kode'], 'string', 'max' => 53],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'komponentarif_id' => 'Komponentarif ID',
            'komponentarif_nama' => 'Nama Komponen Tarif',
            'komponentarif_namalainnya' => 'Nama Komponen Tarif Nama Lainnya',
            'komponentarif_kode' => 'Kode Komponen Tarif',
            'persen_delegasi' => 'Persen Delegasi',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Aktif',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
            'is_total' => 'Is Total',
            'catatan' => 'Catatan',
            'jenis_komponen' => 'Jenis Komponen',
        ];
    }
}
