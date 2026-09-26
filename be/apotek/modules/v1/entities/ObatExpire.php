<?php

/**
 * @author : Ardi Pratama Septiadi (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\entities;

use yii\db\Query;

class ObatExpire
{
	public static function getQuery()
	{
		return (new Query())
                    ->select([
                        '"array_agg"(stokobatalkes_t.stokobatalkes_id) AS id_stok',
                        'stokobatalkes_t.obatalkes_id', 
                        'obatalkes_m.obatalkes_nama',
                        'obatalkes_m.satuankecil_id',
                        'satuanunit_m.satuanunit_nama',
                        'stokobatalkes_t.ruangan_id', 
                        'ruangan_m.ruangan_nama',
                        'ruangan_m.instalasi_id',
                        'instalasi_m.instalasi_nama',
                        'stokobatalkes_t.tglkadaluarsa',
                        'sum(stokobatalkes_t.qtystok_in-stokobatalkes_t.qtystok_out) AS stok_ruangan', 
                        'sum(obatalkes_m.hargaratarata) AS h_dipakai',
                        'sum(obatalkes_m.harganetto) AS h_netto',
                        'sum(obatalkes_m.harganetto) * sum(stokobatalkes_t.qtystok_in-stokobatalkes_t.qtystok_out) AS jumlah_h_netto'
                    ])
                    ->from('stokobatalkes_t')
                    ->leftJoin('obatalkes_m','obatalkes_m.obatalkes_id = stokobatalkes_t.obatalkes_id')
                    ->leftJoin('ruangan_m','ruangan_m.ruangan_id = stokobatalkes_t.ruangan_id')
                    ->leftJoin('instalasi_m','instalasi_m.instalasi_id = ruangan_m.instalasi_id')
                    ->leftJoin('satuanunit_m','satuanunit_m.satuanunit_id = obatalkes_m.satuankecil_id')
                    ->groupBy(['stokobatalkes_t.obatalkes_id','obatalkes_m.obatalkes_nama','obatalkes_m.satuankecil_id','satuanunit_m.satuanunit_nama','stokobatalkes_t.ruangan_id','ruangan_m.ruangan_nama','ruangan_m.instalasi_id','instalasi_m.instalasi_nama','stokobatalkes_t.tglkadaluarsa']);
	}
}