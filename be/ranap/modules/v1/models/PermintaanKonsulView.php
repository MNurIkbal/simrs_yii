<?php
/**
 * @Author: Iqbal@docotel.com
 * @Date:   2018-07-23 11:38:08
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "racikan_m".
 *
 * @property int $racikan_id
 * @property int $dok_dpjp_id
 * @property string $racikan_nama
 * @property string $racikan_singkatan
 * @property double $tarif_service
 * @property double $persen_service
 * @property double $biaya_kemasan
 * @property string $additional_data
 * @property string $jenis_kelamin
 * @property string $carabayar_nama
 * @property string $created_date
 * @property string $no_rekam_medik
 * @property string $nama_pasien
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 *
 * @property AntrianfarmasiT[] $antrianfarmasiTs
 * @property RacikandetailM[] $racikandetailMs
 */
class PermintaanKonsulView extends \Doco\components\DocoActiveRecord
{
   /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infopermintaankonsul_v';
    }
}
