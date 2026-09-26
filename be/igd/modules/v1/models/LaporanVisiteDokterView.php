<?php

/**
 * @Author: Sigit
 * @Date:   2018-12-05 17:40:55
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "laporanvisitedokter_v".
 *
 * @property int $pendaftaran_id
 * @property string $No. Pendaftaran
 * @property string $Tanggal Admisi
 * @property string $Tanggal Visite
 * @property string $No. Rekam Medik
 * @property string $Nama Pasien
 * @property string $Cara Bayar
 * @property string $Penjamin
 * @property string $Jenis Kelamin
 * @property string $Kasus Penyakit
 * @property int $ruangan_id
 * @property string $Ruangan
 * @property string $Kamar
 * @property string $Bed
 * @property string $Dokter Penanggung Jawab
 * @property string $kelompoktindakan_nama
 * @property string $Jenis Visite
 * @property int $dokvisite_id
 * @property string $Dokter Visite
 */
class LaporanVisiteDokterView extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'laporanvisitedokter_v';
    }
}
