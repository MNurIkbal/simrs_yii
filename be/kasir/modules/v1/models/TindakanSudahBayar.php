<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "tindakansudahbayar_t".
 *
 * @property int $tindakansudahbayar_id
 * @property int $pembayaranpelayanan_id
 * @property int $tindakanpelayanan_id
 * @property int $daftartindakan_id
 * @property int $ruangan_id
 * @property int $qty_tindakan
 * @property double $jmlbiaya_tindakan
 * @property double $jmlsubsidi_asuransi
 * @property double $jmlsubsidi_pemerintah
 * @property double $jmlsubsidi_rs
 * @property double $jmliur_biaya
 * @property double $jml_pembebasan
 * @property double $jmlbayar_tindakan
 * @property double $jml_sisabayar_tindakan
 * @property double $pembulatan
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
 *
 * @property TindakanpelayananT[] $tindakanpelayananTs
 * @property DaftartindakanM $daftartindakan
 * @property PembayaranpelayananT $pembayaranpelayanan
 * @property RuanganM $ruangan
 * @property TindakanpelayananT $tindakanpelayanan
 */
class TindakanSudahBayar extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tindakansudahbayar_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pembayaranpelayanan_id', 'daftartindakan_id', 'ruangan_id'], 'required'],
            [['pembayaranpelayanan_id', 'tindakanpelayanan_id', 'daftartindakan_id', 'ruangan_id', 'qty_tindakan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pembayaranpelayanan_id', 'tindakanpelayanan_id', 'daftartindakan_id', 'ruangan_id', 'qty_tindakan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['jmlbiaya_tindakan', 'jmlsubsidi_asuransi', 'jmlsubsidi_pemerintah', 'jmlsubsidi_rs', 'jmliur_biaya', 'jml_pembebasan', 'jmlbayar_tindakan', 'jml_sisabayar_tindakan', 'pembulatan'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tindakansudahbayar_id' => 'Tindakansudahbayar ID',
            'pembayaranpelayanan_id' => 'Pembayaranpelayanan ID',
            'tindakanpelayanan_id' => 'Tindakanpelayanan ID',
            'daftartindakan_id' => 'Daftartindakan ID',
            'ruangan_id' => 'Ruangan ID',
            'qty_tindakan' => 'Qty Tindakan',
            'jmlbiaya_tindakan' => 'Jmlbiaya Tindakan',
            'jmlsubsidi_asuransi' => 'Jmlsubsidi Asuransi',
            'jmlsubsidi_pemerintah' => 'Jmlsubsidi Pemerintah',
            'jmlsubsidi_rs' => 'Jmlsubsidi Rs',
            'jmliur_biaya' => 'Jmliur Biaya',
            'jml_pembebasan' => 'Jml Pembebasan',
            'jmlbayar_tindakan' => 'Jmlbayar Tindakan',
            'jml_sisabayar_tindakan' => 'Jml Sisabayar Tindakan',
            'pembulatan' => 'Pembulatan',
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
        ];
    }
}
