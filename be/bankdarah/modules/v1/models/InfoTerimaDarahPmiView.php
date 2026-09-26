<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infoterimadarahpmi_v".
 *
 * @property int $terimadarahpmi_id
 * @property string $tgl_terimadarahpmi
 * @property string $no_penerimaan
 * @property string $no_pemesanan
 * @property string $nama_pmi
 * @property int $jumlah_diterima
 */
class InfoTerimaDarahPmiView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infoterimadarahpmi_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['terimadarahpmi_id', 'jumlah_diterima'], 'default', 'value' => null],
            [['terimadarahpmi_id', 'jumlah_diterima'], 'integer'],
            [['tgl_terimadarahpmi'], 'safe'],
            [['no_penerimaan', 'no_pemesanan'], 'string', 'max' => 255],
            [['nama_pmi'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'terimadarahpmi_id' => 'Terimadarahpmi ID',
            'tgl_terimadarahpmi' => 'Tgl Terimadarahpmi',
            'no_penerimaan' => 'No Penerimaan',
            'no_pemesanan' => 'No Pemesanan',
            'nama_pmi' => 'Nama Pmi',
            'jumlah_diterima' => 'Jumlah Diterima',
        ];
    }
}
