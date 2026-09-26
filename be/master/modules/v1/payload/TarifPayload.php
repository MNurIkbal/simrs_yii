<?php 
/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\payload;

use Yii;


class TarifPayload extends \yii\base\Model
{
    const PAKET = 'PAKET';
    const TINDAKAN = 'TINDAKAN';
    const AKOMODASI = 'AKOMODASI';

    public $tarif_id;
    public $daftartindakan_id;
    public $tipepaket_id;
    public $carabayar_id;
    public $kelaspelayanan_id;
    public $penjamin_id;
    public $perdatarif_id;
    public $persencyto_tindakan;
    public $persendiskon_tindakan;
    public $is_active;
    public $list_komponen;
    public $kamar_ruangan_id;
    public $is_akomodasi;
    public $persen_penyulit;
    public $dokter_id;
    public $ruangan_id;
    public $is_persentase;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'daftartindakan_id',
                'tipepaket_id',
                'carabayar_id',
                'kelaspelayanan_id',
                'penjamin_id',
                'perdatarif_id',
                'persencyto_tindakan',
                'persendiskon_tindakan',
                'is_active',
                'list_komponen',
                'kamar_ruangan_id',
                'is_akomodasi',
                'persen_penyulit',
                'dokter_id',
                'ruangan_id',
                'is_persentase'
            ],'safe'],
            [['is_active', 'is_akomodasi', 'is_persentase'], 'boolean'],
            [['persencyto_tindakan', 'persen_penyulit'],'default','value' => 0],
            [[
                'daftartindakan_id',
                'tarif_id',
                'tipepaket_id',
                'carabayar_id',
                'kelaspelayanan_id',
                'penjamin_id',
                'perdatarif_id',
                'persencyto_tindakan',
                'kamar_ruangan_id',
                'persen_penyulit',
                'dokter_id'
            ], 'integer'],
            [[
                'daftartindakan_id',
                'carabayar_id',
                'kelaspelayanan_id',
                'penjamin_id',
                'perdatarif_id',
                'is_active',
            ],'required', 'on' => self::TINDAKAN],
            [[
                'tipepaket_id',
                'carabayar_id',
                'kelaspelayanan_id',
                'penjamin_id',
                'perdatarif_id',
                'is_active',
            ],'required', 'on' => self::PAKET],
            [[
                'daftartindakan_id',
                'carabayar_id',
                'kelaspelayanan_id',
                'penjamin_id',
                'perdatarif_id',
                'is_active',
                'is_akomodasi',
                'kamar_ruangan_id'
            ],'required', 'on' => self::AKOMODASI],
        ];
    }

    public function attributeLabels() {
        return [
            'kamar_ruangan_id' => 'Kamar'
        ];
    }
}