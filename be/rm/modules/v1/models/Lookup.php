<?php

namespace app\modules\v1\models;

use Yii;
use Doco\components\DocoConstants;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "lookup_m".
 *
 * @property int $lookup_id
 * @property string $lookup_type
 * @property string $lookup_name
 * @property string $lookup_value
 * @property int $lookup_urutan
 * @property string $lookup_kode
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
 */
class Lookup extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    const STATUS_BAYAR = 'status_bayar';
    const STATUS_PERIKSA = 'status_periksa';
    const JENIS_LAPORAN = 'jenis_laporan';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lookup_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lookup_type', 'lookup_name', 'lookup_value', 'lookup_urutan'], 'required'],
            [['lookup_urutan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['lookup_urutan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['lookup_type'], 'string', 'max' => 100],
            [['lookup_name', 'lookup_value'], 'string', 'max' => 200],
            [['lookup_kode'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'lookup_id' => 'Lookup ID',
            'lookup_type' => 'Lookup Type',
            'lookup_name' => 'Lookup Name',
            'lookup_value' => 'Lookup Value',
            'lookup_urutan' => 'Lookup Urutan',
            'lookup_kode' => 'Lookup Kode',
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

    public function getMonitoringStatus($from = null)
    {
        $result = [];
        $lookups = [];
        $notInLookupId = [];
        $instalasiId = [];
        $defaultInstasiId = null;
        if(empty($from) || $from == DocoConstants::INST_ID_RM) {
            $notInLookupId = [
                DocoConstants::DOKRM_ISSUES
            ];
            $instalasiId = [
                DocoConstants::VAR_I_RJ,
                DocoConstants::VAR_I_RD,
                DocoConstants::VAR_I_RANAP,
            ];
        } else {
            $notInLookupId = [
                DocoConstants::DOKRM_RECEIVE,
                DocoConstants::DOKRM_RETURN,
                DocoConstants::DOKRM_ISSUES
            ];
            $instalasiId = [$from];
            $defaultInstasiId = $from;
        }

        $lookups = self::find()->select([
            'lookup_type',
            'lookup_id',
            'lookup_name'
        ])->andWhere([
            'is_active' => true,
            'lookup_type' => 'status_riwayat_rm',
        ])->andWhere([
            'not in', 'lookup_id', $notInLookupId
        ]);

        $lookups = $lookups->asArray()->all();

        $instalations = Instalasi::find()->select([
            'instalasi_id',
            'instalasi_nama as lookup_name',
            'lookup_id' => new \yii\db\Expression(DocoConstants::DOKRM_ISSUES)
        ])
        ->andWhere([
            'instalasi_id' => $instalasiId
        ])
        ->asArray()->all();
        $items = array_merge($lookups, $instalations);
        
        foreach($items as $key => $item) {
            $result[] = [
                'title' => $item['lookup_name'],
                'lookup_id' => $item['lookup_id'],
                'instalasi_id' => isset($item['instalasi_id']) ? $item['instalasi_id'] : $defaultInstasiId,
                'kode' => str_replace(' ', '_', strtolower($item['lookup_name']))
            ];
        }
        ArrayHelper::multisort($result, ['lookup_id', 'instalasi_id'], [SORT_ASC, SORT_ASC]);
        return $result;
    }
}
