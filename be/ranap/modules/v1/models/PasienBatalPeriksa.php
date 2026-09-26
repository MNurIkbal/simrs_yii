<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pasienbatalperiksa_t".
 *
 * @property int $pasienbatalperiksa_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property int $pasienmasukpenunjang_id
 * @property int $pasienkirimkeunitlain_id
 * @property string $tgl_batal
 * @property string $keterangan_batal
 * @property string $alasan_batal
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
 */
class PasienBatalPeriksa extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pasienbatalperiksa_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'tgl_batal', 'alasan_batal'], 'required'],
            [['pendaftaran_id', 'pasienadmisi_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['keterangan_batal', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['keterangan_batal', 'alasan_batal', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pasienbatalperiksa_id' => 'Pasienbatalperiksa ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'tgl_batal' => 'Tgl Batal',
            'keterangan_batal' => 'Keterangan Batal',
            'alasan_batal' => 'Alasan Batal',
        ];
    }

}
