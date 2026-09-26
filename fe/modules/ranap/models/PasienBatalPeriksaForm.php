<?php

namespace app\modules\ranap\models;

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
class PasienBatalPeriksaForm extends \Yii\base\Model
{
    // public $pasienbatalperiksa_id;
    public $pendaftaran_id;
    public $pasienadmisi_id;
    public $tgl_batal;
    public $keterangan_batal;
    public $alasan_batal;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasienadmisi_id', 'tgl_batal', 'alasan_batal'], 'required'],
            [['keterangan_batal'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tgl_batal' => Yii::t('fe', 'Tanggal batal'),
            'keterangan_batal' => Yii::t('fe', 'Keterangan batal'),
            'alasan_batal' => Yii::t('fe', 'Alasan batal'),
        ];
    }

}
