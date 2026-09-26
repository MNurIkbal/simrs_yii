<?php

namespace Doco\models\pendaftaran;
use Yii;


class PendaftaranMultipayer extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pendaftaran_multipayer_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_multipayer_id', 'asuransipasien_id', 'pendaftaran_id', 'pasien_id', 'penjamin_id', 'carabayar_id', 'additional_data', 'is_multipayer', 'nokartuasuransi', 'namapemilikasuransi', 'nomorpokokperusahaan', 'kelastanggunganasuransi_id', 'namaperusahaan', 'tgl_konfirmasi', 'status_konfirmasi', 'bpjs_id'], 'default', 'value' => null],
            [['pendaftaran_multipayer_id', 'asuransipasien_id', 'pendaftaran_id', 'pasien_id', 'penjamin_id', 'carabayar_id', 'kelastanggunganasuransi_id', 'bpjs_id'], 'integer'],
            [['additional_data', 'is_multipayer', 'nokartuasuransi', 'namapemilikasuransi', 'nomorpokokperusahaan', 'kelastanggunganasuransi_id', 'namaperusahaan', 'tgl_konfirmasi', 'status_konfirmasi'], 'safe'],
            [['pendaftaran_multipayer_id', 'asuransipasien_id', 'pendaftaran_id', 'pasien_id', 'penjamin_id', 'carabayar_id'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [];
    }
}
