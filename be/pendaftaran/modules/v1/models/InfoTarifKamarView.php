<?php

namespace app\modules\v1\models;

/**
 * This is the model class for table "kamarruangan_v".
 *
 * @property int $kamartempattidur_id
 * @property int $kamarruangan_id
 * @property int $ruangan_id
 * @property int $jeniskasuspenyakit_id
 * @property string $jeniskasuspenyakit_nama
 * @property string $ruangan_nama
 * @property string $kamarruangan_nokamar
 * @property string $no_tempattidur
 * @property int $kelaspelayanan_id
 * @property string $kelaspelayanan_nama
 * @property bool $status_isi
 * @property int $kettempattidur_id
 * @property string $kettempattidur_nama
 * @property string $kettempattidur_warna
 * @property string $kode_warna
 */
class InfoTarifKamarView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infotarifkamar_v';
    }
}
