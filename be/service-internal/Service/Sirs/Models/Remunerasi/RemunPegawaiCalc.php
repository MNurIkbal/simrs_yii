<?php

namespace Integrasi\Service\Sirs\Models\Remunerasi;

use Yii;

/**
 * This is the model class for table "bpjs_t".
 *
 * @property int $remunpegawaicalc_id
 * @property int $remunpegawai_id
 * @property int $target_point
 * @property float $target_nominal
 * @property string $detail_tindakan
 * @property string $detail_absen
 * @property int $ronde_besar
 * @property int $koord_lap_jaga_bangsal
 * @property int $rapat_koord_pelayanan
 * @property int $audit_medik
 * @property int $penelitian
 * @property int $presentasi
 * @property int $rapat_direksi
 * @property float $kwalitas
 * @property float $kepatuhan
 * @property float $jumlah_point
 * @property float $manajerial
 * @property float $total_point
 * @property float $total_potongan
 * @property float $tidak_apel
 * @property float $iki
 * @property float $nominal
 * @property float $max_kmk
 * @property float $imbal_jasa
 * @property float $periode_bulan
 * @property float $periode_tahun
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
 **/

class RemunPegawaiCalc extends \Integrasi\Components\ActiveRepositories
{

    /**
 * @inheritdoc
 */
    public static function tableName()
    {
        return 'remunpegawaicalc_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'remunpegawaicalc_id',
                'remunpegawai_id',
                'target_point',
                'target_nominal',
                'detail_tindakan',
                'detail_absen',
                'ronde_besar',
                'koord_lap_jaga_bangsal',
                'rapat_koord_pelayanan',
                'audit_medik',
                'penelitian',
                'presentasi',
                'rapat_direksi',
                'kwalitas',
                'kepatuhan',
                'jumlah_point',
                'manajerial',
                'total_point',
                'total_potongan',
                'tidak_apel',
                'iki',
                'nominal',
                'max_kmk',
                'imbal_jasa',
                'periode_bulan',
                'periode_tahun',
                'additional_data',
                'created_date',
                'last_modified_date',
                'deleted_date'
            ], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_active', 'is_deleted'], 'boolean'],
        ];
    }
}
