<?php

/**
 * @Author: ardi
 * @Date:   2024-05-09 12:42:00
 */

namespace app\modules\v1\models;

use Yii;


class HasilUsg extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'hasilusg_t';
    }

    public function rules()
    {
        return [
            [
                [
                    "tgl_pemeriksaan",
                    "dokterpemeriksa_id",
                    "hasilusg",
                    "pendaftaran_id",
                    "pasien_id",
                    "pasienadmisi_id",
                    "ruangan_id",
                    "is_deleted",
                    "deleted_by",
                    'deleted_date',
                    'hasil_pemeriksaan_id'
                ],
                'safe'
            ],
        ];
    }

    public function getPegawai()
    {
        return $this->hasOne(Pegawai::className(), ['pegawai_id' => 'dokterpemeriksa_id']);
    }

    public function getTindakan()
    {
        return $this->hasOne(HasilPemeriksaan::className(), ['hasilpemeriksaan_id' => 'hasil_pemeriksaan_id']);
    }

    public function getDeletedbylogin()
    {
        return $this->hasOne(LoginPemakai::className(), ['loginpemakai_id' => 'deleted_by']);
    }

    public function getDeletedbypegawai() {
        if (isset($this->deletedbylogin)) {
            return $this->deletedbylogin->hasOne(Pegawai::className(), ['pegawai_id' => 'loginpemakai_id']);
        } else {
            return null;
        }
    }

    public function fields()
    {
        return [
            'hasilusg_id',
            'tgl_pemeriksaan',
            'dokterpemeriksa_id',
            'hasilusg',
            'pendaftaran_id',
            'dokter' => function ($item) {
                return $item->pegawai->nama_pegawai;
            },
            'is_deleted',
            'deleted_by',
            'deleted_date',
            'hasil_pemeriksaan_id',
            'tindakan' => function ($item) {
                return $item->tindakan['hasilpemeriksaan_nama'];
            },
            'deleter_name' => function ($item) {
                return $item->deletedbypegawai['nama_pegawai'];
            }
        ];
    }
}
