<?php

namespace app\modules\v1\components;

use Yii;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\Penjamin;

class DetailObatQuery {
	public static function byPenjamin($penjamin_id,$kelaspelayanan_id,$ruangan_id,$obatalkes_id)
	{
		$groupmargin_id = ArrayHelper::getValue(Penjamin::findOne($penjamin_id),'groupmargin_id',0);

		$command = Yii::$app->db->createCommand("
			SELECT 
				stokobatalkes_r.obatalkes_id,
				obatalkes_m.obatalkes_kode,
				obatalkes_m.obatalkes_nama,
				obatalkes_m.obatalkes_namalain,
				obatalkes_m.harganetto,
				obatalkes_m.harganetto AS harganetto_ygdipakai,
				obatalkes_m.jenisobatalkes_id,
				obatalkes_m.satuankecil_id,
				satuankecil.satuanunit_nama AS satuankecil_nama,
				obatalkes_m.satuanbesar_id,
				satuanbesar.satuanunit_nama AS satuanbesar_nama,
				stokobatalkes_r.qty_dipesan,
				stokobatalkes_r.qty_tersedia,
				stokobatalkes_r.qty_sisa,
				stokobatalkes_r.ruangan_id,
				ruangan_m.ruangan_nama,
				ruangan_m.instalasi_id,
				instalasi_m.instalasi_nama,
				obatalkes_m.jenisobatalkes_id,
				jenisobatalkes_m.jenisobatalkes_nama,
				jenisobatalkes_m.group_jenisobat,
				fgetnamalookup(jenisobatalkes_m.group_jenisobat) AS group_jenisobat_nama,
				konfigfarmasi_k.hargaygdigunakan,
				CASE
				  WHEN ((konfigfarmasi_k.hargaygdigunakan)::text = 'MAX'::text) THEN obatalkes_m.hargamaksimum
				  WHEN ((konfigfarmasi_k.hargaygdigunakan)::text = 'MIN'::text) THEN obatalkes_m.hargaminimum
				  WHEN ((konfigfarmasi_k.hargaygdigunakan)::text = 'AVG'::text) THEN obatalkes_m.hargaratarata
				 ELSE obatalkes_m.hargaterakhir 
				 END AS hargadigunakan,
				COALESCE(konfigmargin.persenmarginkonfig,0),
				COALESCE(konfigmarginnetto.persenmarginnetto,0),
				konfigfarmasi_k.persen_diskon AS disc,
				konfigfarmasi_k.persenppn AS ppn,
				konfigfarmasi_k.embalase_racikan,
				konfigfarmasi_k.embalase_nonracikan,
				obatalkes_m.harganetto * COALESCE(konfigmarginnetto.persenmarginnetto,0) / 100::double precision AS margin_harganetto,
				(obatalkes_m.harganetto * COALESCE(konfigmarginnetto.persenmarginnetto,0) / 100::double precision) * (konfigfarmasi_k.persen_diskon / 100::double precision) AS diskon_harganetto,
				obatalkes_m.harganetto + obatalkes_m.harganetto * COALESCE(konfigmarginnetto.persenmarginnetto,0) / 100::double precision AS harganetto_denganmargin,
				obatalkes_m.harganetto + obatalkes_m.harganetto * COALESCE(konfigmarginnetto.persenmarginnetto,0) / 100::double precision - ((obatalkes_m.harganetto + obatalkes_m.harganetto * COALESCE(konfigmarginnetto.persenmarginnetto,0) / 100::double precision)*(konfigfarmasi_k.persen_diskon / 100::double precision)) AS harganetto_setelahdiskon,
				obatalkes_m.harganetto + 
				(obatalkes_m.harganetto * COALESCE(konfigmarginnetto.persenmarginnetto,0) / 100::double precision) - ((obatalkes_m.harganetto + (obatalkes_m.harganetto * COALESCE(konfigmarginnetto.persenmarginnetto,0) / 100::double precision))*(konfigfarmasi_k.persen_diskon / 100::double precision)) + ((obatalkes_m.harganetto + 
				(obatalkes_m.harganetto * COALESCE(konfigmarginnetto.persenmarginnetto,0) / 100::double precision) - ((obatalkes_m.harganetto + (obatalkes_m.harganetto * COALESCE(konfigmarginnetto.persenmarginnetto,0) / 100::double precision))*(konfigfarmasi_k.persen_diskon / 100::double precision))) * (konfigfarmasi_k.persenppn / 100::double precision)) AS harganetto_setelahppn,
				obatalkes_m.harganetto + 
				(obatalkes_m.harganetto * COALESCE(konfigmarginnetto.persenmarginnetto,0) / 100::double precision) - ((obatalkes_m.harganetto + (obatalkes_m.harganetto * COALESCE(konfigmarginnetto.persenmarginnetto,0) / 100::double precision))*(konfigfarmasi_k.persen_diskon / 100::double precision)) + ((obatalkes_m.harganetto + 
				(obatalkes_m.harganetto * COALESCE(konfigmarginnetto.persenmarginnetto,0) / 100::double precision) - ((obatalkes_m.harganetto + (obatalkes_m.harganetto * COALESCE(konfigmarginnetto.persenmarginnetto,0) / 100::double precision))*(konfigfarmasi_k.persen_diskon / 100::double precision))) * (konfigfarmasi_k.persenppn / 100::double precision)) AS hargajual
			FROM stokobatalkes_r
			LEFT JOIN ruangan_m ON stokobatalkes_r.ruangan_id = ruangan_m.ruangan_id
			LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
			LEFT JOIN obatalkes_m ON obatalkes_m.obatalkes_id = stokobatalkes_r.obatalkes_id
			LEFT JOIN jenisobatalkes_m ON jenisobatalkes_m.jenisobatalkes_id = obatalkes_m.jenisobatalkes_id
			LEFT JOIN satuanunit_m satuankecil ON satuankecil.satuanunit_id = obatalkes_m.satuankecil_id
			LEFT JOIN satuanunit_m satuanbesar ON satuanbesar.satuanunit_id = obatalkes_m.satuanbesar_id
			JOIN konfigfarmasi_k ON konfigfarmasi_k.is_deleted = FALSE
			LEFT JOIN (
				select 
				margin AS persenmarginkonfig,
				groupmargin_id,
				jenisobatalkes_id,
				kelaspelayanan_id,
				harga_min,
				harga_max
				FROM konfigmargindetail_k
				LEFT JOIN konfigmargin_k ON konfigmargin_k.konfigmargin_id = konfigmargindetail_k.konfigmargin_id
				WHERE konfigmargin_k.tgl_berlaku <= CURRENT_DATE
				AND konfigmargindetail_k.is_deleted = false
				ORDER BY konfigmargin_k.tgl_berlaku DESC
			) konfigmargin ON 
				konfigmargin.groupmargin_id = :groupmargin_id
				AND konfigmargin.kelaspelayanan_id = :kelaspelayanan_id
				AND (
					CASE
					  WHEN ((konfigfarmasi_k.hargaygdigunakan)::text = 'MAX'::text) THEN obatalkes_m.hargamaksimum
					  WHEN ((konfigfarmasi_k.hargaygdigunakan)::text = 'MIN'::text) THEN obatalkes_m.hargaminimum
					  WHEN ((konfigfarmasi_k.hargaygdigunakan)::text = 'AVG'::text) THEN obatalkes_m.hargaratarata
					  ELSE obatalkes_m.hargaterakhir END
					 >= konfigmargin.harga_min AND 
					CASE
					  WHEN ((konfigfarmasi_k.hargaygdigunakan)::text = 'MAX'::text) THEN obatalkes_m.hargamaksimum
					  WHEN ((konfigfarmasi_k.hargaygdigunakan)::text = 'MIN'::text) THEN obatalkes_m.hargaminimum
					  WHEN ((konfigfarmasi_k.hargaygdigunakan)::text = 'AVG'::text) THEN obatalkes_m.hargaratarata
					  ELSE obatalkes_m.hargaterakhir END 
					<= konfigmargin.harga_max)
				AND konfigmargin.jenisobatalkes_id = obatalkes_m.jenisobatalkes_id
			LEFT JOIN (
				SELECT 
				margin AS persenmarginnetto,
				groupmargin_id,
				jenisobatalkes_id,
				kelaspelayanan_id,
				harga_min,
				harga_max
				FROM konfigmargindetail_k
				LEFT JOIN konfigmargin_k ON konfigmargin_k.konfigmargin_id = konfigmargindetail_k.konfigmargin_id
				WHERE konfigmargin_k.tgl_berlaku <= CURRENT_DATE
				AND konfigmargindetail_k.is_deleted = false
				ORDER BY konfigmargin_k.tgl_berlaku DESC
			) konfigmarginnetto ON 
				konfigmarginnetto.groupmargin_id = :groupmargin_id
				AND konfigmarginnetto.kelaspelayanan_id = :kelaspelayanan_id
				AND (
					obatalkes_m.harganetto >= konfigmarginnetto.harga_min AND 
					obatalkes_m.harganetto <= konfigmarginnetto.harga_max)
				AND konfigmarginnetto.jenisobatalkes_id = obatalkes_m.jenisobatalkes_id
			WHERE obatalkes_m.is_active = TRUE 
			AND obatalkes_m.is_deleted = FALSE
			AND stokobatalkes_r.ruangan_id = :ruangan_id
			AND stokobatalkes_r.obatalkes_id = :obatalkes_id
			");
		$data =  $command
					->bindValue(':groupmargin_id',$groupmargin_id)
					->bindValue(':kelaspelayanan_id',$kelaspelayanan_id)
					->bindValue(':ruangan_id',$ruangan_id)
					->bindValue(':obatalkes_id',$obatalkes_id)
					->queryOne();
		return $data;

	}
}