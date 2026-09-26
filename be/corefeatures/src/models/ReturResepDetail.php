<?php

namespace SirsCore\models;

use Yii;

/**
 * This is the model class for table "returresep_t".
 *
 * @property int $returresep_id
 * @property int $ruangan_id
 * @property int $penjualanresep_id
 * @property int $pendaftaran_id
 * @property int $pasien_id
 * @property int $pasienadmisi_id
 * @property string $tgl_retur
 * @property string $no_returresep
 * @property string $alasan_retur
 * @property string $keterangan_retur
 * @property int $pegawaimengetahui_id
 * @property int $pegawairetur_id
 * @property double $total_retur
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
 * @property PenjualanresepT[] $penjualanresepTs
 * @property ReturbayarpelayananT[] $returbayarpelayananTs
 * @property PasienM $pasien
 * @property PasienadmisiT $pasienadmisi
 * @property PegawaiM $pegawaimengetahui
 * @property PegawaiM $pegawairetur
 * @property PendaftaranT $pendaftaran
 * @property PenjualanresepT $penjualanresep
 * @property RuanganM $ruangan
 */
class ReturResepDetail extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'tgl_retur', 
                'created_date',
                'last_modified_date',
                'deleted_date',
                'obatalkespasien_id',
                'hargasatuan',
                'qty_retur',
                'created_by',
                'returresep_id',
                'qty_pemberian_akhir'
            ], 'safe'],
            [['is_deleted', 'is_active'], 'boolean']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'returresepdetail_t';
    }
}
