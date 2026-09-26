<?php

namespace Integrasi\Components\models;

use Yii;

/**
 * This is the model class for table "kertas_k".
 *
 * @property int $kertas_id
 * @property string $kertas_kode
 * @property string $kertas_nama
 * @property double $panjang
 * @property double $lebar
 * @property double $batas_kiri
 * @property double $batas_kanan
 * @property double $batas_atas
 * @property double $batas_bawah
 * @property double $ppi
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
class JenisKertas extends \Doco\components\DocoActiveRecord
{
    protected $xssProtected = [
        'kertas_kode',
        'kertas_nama'
    ];
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'kertas_k';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kertas_kode', 'kertas_nama'], 'required'],
            [['panjang', 'lebar', 'batas_kiri', 'batas_kanan', 'batas_atas', 'batas_bawah', 'ppi'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['kertas_kode'], 'string', 'max' => 10],
            [['kertas_nama'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'kertas_id' => 'Kertas ID',
            'kertas_kode' => 'Kertas Kode',
            'kertas_nama' => 'Kertas Nama',
            'panjang' => 'Panjang',
            'lebar' => 'Lebar',
            'batas_kiri' => 'Batas Kiri',
            'batas_kanan' => 'Batas Kanan',
            'batas_atas' => 'Batas Atas',
            'batas_bawah' => 'Batas Bawah',
            'ppi' => 'Ppi',
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
