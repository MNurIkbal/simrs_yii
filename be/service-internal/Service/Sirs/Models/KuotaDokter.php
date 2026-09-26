<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;

/**
 * This is the model class for table "kuotadokter_r".
 *
 * @property int $kuotadokter_id
 * @property int $jadwaldokter_id
 * @property double $kuota_real
 * @property double $kuota_masuk
 * @property double $kuota_keluar
 * @property double $kuota_tersedia
 * @property int $kuota_bpjs_offline
 * @property int $kuota_nonbpjs_offline
 * @property int $kuota_bpjs_online
 * @property int $kuota_nonbpjs_online
 * @property int $kuota_out_bpjs
 * @property int $kuota_out_nonbpjs
 */
class KuotaDokter extends \Integrasi\Components\ActiveRepositories
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kuotadokter_r';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jadwaldokter_id'], 'default', 'value' => null],
            [['jadwaldokter_id'], 'integer'],
            [['kuota_real', 'kuota_masuk', 'kuota_keluar', 'kuota_tersedia', 'kuota_bpjs_offline', 'kuota_nonbpjs_offline', 'kuota_bpjs_online', 'kuota_nonbpjs_online', 'kuota_out_bpjs', 'kuota_out_nonbpjs'], 'number'],
            [['kuota_masuk','kuota_tersedia', 'is_online'],'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kuotadokter_id' => 'Kuotadokter ID',
            'jadwaldokter_id' => 'Jadwaldokter ID',
            'kuota_real' => 'Kuota Real',
            'kuota_masuk' => 'Kuota Masuk',
            'kuota_keluar' => 'Kuota Keluar',
            'kuota_tersedia' => 'Kuota Tersedia',
        ];
    }
}
