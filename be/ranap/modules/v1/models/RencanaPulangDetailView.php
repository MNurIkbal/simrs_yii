<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rencanapulangdetail_v".
 *
 * @property int $rencanapulang_id
 * @property string $edukasi_kesehatan
 * @property string $tgl_edukasi
 * @property string $pemberi_edukasi
 * @property int $ppa
 * @property bool $is_deleted
 * @property string $edukasi_kesehatan_nama
 */
class RencanaPulangDetailView extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rencanapulangdetail_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rencanapulang_id', 'ppa'], 'default', 'value' => null],
            [['rencanapulang_id', 'ppa'], 'integer'],
            [['tgl_edukasi'], 'safe'],
            [['is_deleted'], 'boolean'],
            [['edukasi_kesehatan', 'pemberi_edukasi'], 'string', 'max' => 255],
            [['edukasi_kesehatan_nama'], 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'rencanapulang_id' => 'Rencanapulang ID',
            'edukasi_kesehatan' => 'Edukasi Kesehatan',
            'tgl_edukasi' => 'Tgl Edukasi',
            'pemberi_edukasi' => 'Pemberi Edukasi',
            'ppa' => 'Ppa',
            'is_deleted' => 'Is Deleted',
            'edukasi_kesehatan_nama' => 'Edukasi Kesehatan Nama',
        ];
    }
}
