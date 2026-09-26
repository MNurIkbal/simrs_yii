<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;

/**
 * This is the model class for table "stokkuotadokter_t".
 *
 * @property int $stokkuotadokter_id
 * @property int $jadwaldokter_id
 * @property int $jadwaldoktertambahan_id
 * @property int $antrian_id
 * @property int $batalantrian_id
 * @property int $konsulpoli_id
 * @property int $batalkonsolpoli_id
 * @property int $buatjanjipoli_id
 * @property int $bataljanjipoli_id
 * @property string $tgltransaksi_in
 * @property string $tgltransaksi_out
 * @property double $kuota_in
 * @property double $kuota_out
 * @property bool $flag
 * @property int $kuotaasal_id
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property bool $is_online
 * @property string $deleted_date
 * @property int $deleted_by
 * @property int $jadwalbukapoli_id
 * @property int $kuota_bpjs_offline
 * @property int $kuota_nonbpjs_offline
 * @property int $kuota_bpjs_online
 * @property int $kuota_nonbpjs_online
 * @property int $kuota_out_bpjs
 * @property int $kuota_out_nonbpjs
 */
class StokKuotaDoktor extends \Integrasi\Components\ActiveRepositories
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'stokkuotadokter_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jadwaldokter_id', 'jadwaldoktertambahan_id', 'antrian_id', 'batalantrian_id', 'konsulpoli_id', 'batalkonsolpoli_id', 'buatjanjipoli_id', 'bataljanjipoli_id', 'kuotaasal_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'jadwalbukapoli_id'], 'default', 'value' => null],
            [['jadwaldokter_id', 'jadwaldoktertambahan_id', 'antrian_id', 'batalantrian_id', 'konsulpoli_id', 'batalkonsolpoli_id', 'buatjanjipoli_id', 'bataljanjipoli_id', 'kuotaasal_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'jadwalbukapoli_id'], 'integer'],
            [['tgltransaksi_in', 'tgltransaksi_out', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['kuota_in', 'kuota_out', 'kuota_bpjs_offline', 'kuota_nonbpjs_offline', 'kuota_bpjs_online', 'kuota_nonbpjs_online', 'kuota_out_bpjs', 'kuota_out_nonbpjs'], 'number'],
            [['flag', 'is_deleted', 'is_active', 'is_online'], 'boolean'],
            [['additional_data'], 'string'],
        ];
    }
}
