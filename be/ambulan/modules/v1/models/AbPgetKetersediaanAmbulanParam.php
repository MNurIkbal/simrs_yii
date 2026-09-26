<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "obatalkes_f".
 *
 * @property int $ambulan_id
 */
class AbPgetKetersediaanAmbulanParam extends \Doco\components\DocoPostgreFunctionAR
{
    /**
     * {@inheritdoc}
     */
    
    public static function functionName()
    {
        return 'ab_pgetketersediaanambulanparam';
    }


    public static function attributSchema()
    {
        return [
                'int4' => ['ambulan_id', 
                            'barang_id',
                            'status_ambulan_id',
                            ],
                'varchar' => ['no_polisi', 
                            'status_ambulan',  
                            ],
                'text' => ['jenis_ambulan'],
                'float8' => [],
                'date' => ['tgl_pemesanan',
                            ],
                'boolean' => ['is_emergency'
                            ],
            ];
    }
}
