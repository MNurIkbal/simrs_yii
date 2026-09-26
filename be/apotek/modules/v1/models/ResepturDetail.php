<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "resepturdetail_t".
 *
 * @property integer $resepturdetail_id
 * @property integer $obatalkes_id
 * @property integer $racikan_id
 * @property integer $satuankecil_id
 * @property integer $sumberdana_id
 * @property integer $reseptur_id
 * @property string $r
 * @property integer $rke
 * @property integer $permintaan_reseptur
 * @property integer $jmlkemasan_reseptur
 * @property integer $kekuatan_reseptur
 * @property string $satuankekuatan
 * @property double $qty_reseptur
 * @property double $qty_konversi
 * @property double $hargasatuan_reseptur
 * @property string $signa_reseptur
 * @property double $harganetto_reseptur
 * @property double $hargajual_reseptur
 * @property string $etiket
 * @property integer $iter
 * @property string $satuansediaan
 * @property string $additional_data
 * @property string $created_date
 * @property integer $created_by
 * @property integer $modified_count
 * @property string $last_modified_date
 * @property integer $last_modified_by
 * @property boolean $is_deleted
 * @property boolean $is_retur
 * @property boolean $is_active
 * @property string $deleted_date
 * @property integer $deleted_by
 */
class ResepturDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'resepturdetail_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['obatalkes_id', 'racikan_id', 'reseptur_id', 'qty_reseptur','qty_konversi'], 'required'],
            [['obatalkes_id', 'racikan_id', 'satuankecil_id', 'reseptur_id', 'rke', 'kekuatan_reseptur', 'iter', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by','status_implementasi','satuan_racikan_id'], 'integer'],
            [['qty_reseptur','qty_konversi', 'det', 'det_konversi', 'hargasatuan_reseptur', 'harganetto_reseptur', 'hargajual_reseptur','qty_racikan'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date', 'signa_id','det', 'det_konversi','is_deleted','signa','nama_racikan','qty_racikan','satuan_racikan_id','qty_medis','det_medis', 'is_kronis','hari','is_retur'], 'safe'],
            [['is_kronis', 'is_deleted', 'is_active','is_retur'], 'boolean'],
            [['is_kronis','is_retur'],'default', 'value' => false],
            [['r'], 'string', 'max' => 2],
            [['satuankekuatan'], 'string', 'max' => 20],
            [['etiket','nama_racikan'], 'string', 'max' => 2000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'resepturdetail_id' => 'Resepturdetail ID',
            'obatalkes_id' => 'Obatalkes ID',
            'racikan_id' => 'Racikan ID',
            'satuankecil_id' => 'Satuankecil ID',
            'sumberdana_id' => 'Sumberdana ID',
            'reseptur_id' => 'Reseptur ID',
            'r' => 'R',
            'rke' => 'Rke',
            'permintaan_reseptur' => 'Permintaan Reseptur',
            'jmlkemasan_reseptur' => 'Jmlkemasan Reseptur',
            'kekuatan_reseptur' => 'Kekuatan Reseptur',
            'satuankekuatan' => 'Satuankekuatan',
            'qty_reseptur' => 'Qty Reseptur',
            'hargasatuan_reseptur' => 'Hargasatuan Reseptur',
            'signa_reseptur' => 'Signa Reseptur',
            'harganetto_reseptur' => 'Harganetto Reseptur',
            'hargajual_reseptur' => 'Hargajual Reseptur',
            'etiket' => 'Etiket',
            'iter' => 'Iter',
            'satuansediaan' => 'Satuansediaan',
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
            'status_implementasi' => 'Status Implementasi',
        ];
    }
}
