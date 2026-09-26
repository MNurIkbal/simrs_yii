<?php

namespace app\modules\v1\models;

use Yii;

class ObatAlkesR extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'obatalkes_r';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'obatalkes_id' => 'Obatalkes ID',
            'jenisobatalkes_id' => 'Jenisobatalkes ID',
            'sumberdana_id' => 'Sumberdana ID',
            'lokasigudang_id' => 'Lokasigudang ID',
            'satuankecil_id' => 'Satuankecil ID',
            'satuanbesar_id' => 'Satuanbesar ID',
            'obatalkes_barcode' => 'Obatalkes Barcode',
            'obatalkes_kode' => 'Obatalkes Kode',
            'obatalkes_namalain' => 'Obatalkes Namalain',
            'obatalkes_kategori' => 'Obatalkes Kategori',
            'obatalkes_kadarobat' => 'Obatalkes Kadarobat',
            'kemasan_besar' => 'Kemasanbesar',
            'kekuatan_obat' => 'Kekuatan',
            'satuankekuatan' => 'Satuankekuatan',
            'ppn_persen' => 'Ppn Persen',
            'harganetto' => 'Harganetto',
            'hargajual' => 'Hargajual',
            'hargamaksimum' => 'Hargamaksimum',
            'hargaminimum' => 'Hargaminimum',
            'hargaratarata' => 'Hargaratarata',
            'discount' => 'Discount',
            'tglkadaluarsa' => 'Tglkadaluarsa',
            'minimalstok' => 'Minimalstok',
            'is_formularium' => 'Formularium',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
            'ket_ubah_harga' => 'Keterangan Ubah Harga',
        ];
    }

    
}
