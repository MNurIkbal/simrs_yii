<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "informasiambulan_f".
 *
 * @property int $ambulan_id
 */
class InformasiAmbulanFn extends \Doco\components\DocoPostgreFunctionAR
{
    /**
     * {@inheritdoc}
     */
    
    public static function functionName()
    {
        return 'informasiambulan_f';
    }


    public static function attributSchema()
    {
        return [
                'int8' => ['ambulan_id', 
                            'pemakaianambulan_id',
                            'durasi_pemakaian',
                            'pendaftaran_id',
                            'status_ambulan',
                            'nominal_tagihan',
                            'biaya_pemakaian',
                            'biaya_tambahan',
                            ],
                'bigint' => ['km_awal',
                            'km_akhir',
                            'jarak_pemakaian',
                            ],
                'varchar' => ['no_polisi', 
                            'no_pesanambulan',
                            'no_rekam_medik',
                            'nama_pemesan',
                            'jns_kelamin',
                            'asal_pasien',
                            'keluhan',
                            'pelayanan',
                            'status_ambulan_nama',
                            'supir',
                            ],
                'text' => ['jenis_ambulan'],
                'float8' => [],
                'date' => ['tgl_pemesanan',
                            'tgl_pesanambulan',
                            'tgl_pemakaiandari',
                            'tgl_pemakaiansampai',
                            'tgl_realisasikembali',
                            ],
                'boolean' => [],
            ];
    }
}
