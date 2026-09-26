<?php

use yii\db\Migration;

/**
 * Class m220915_101507_migrate_ordh_164_laporanrekappenjualanfarmasi_fn
 */
class m220915_101507_migrate_ordh_164_laporanrekappenjualanfarmasi_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP FUNCTION IF EXISTS laporanrekappenjualanfarmasi_fn(x_date date, y_date date);
        ');

        $this->execute("
			CREATE OR REPLACE FUNCTION public.laporanrekappenjualanfarmasi_fn(x_date date, y_date date)
			  RETURNS TABLE(kode_obat varchar, nama_obat varchar, satuan_kecil varchar, baseprice float8, qty float8, total float8, jenisobatalkes_id int4, jenisobatalkes_nama varchar) AS \$BODY\$

			BEGIN
				RETURN QUERY 
				SELECT *FROM (
					 SELECT rekap.kode_obat::VARCHAR AS kode_obat,
			    rekap.nama_obat::VARCHAR AS nama_obat,
			    rekap.satuan_kecil::VARCHAR AS satuan_kecil,
			    rekap.harga_netto::float8 AS baseprice,
			    sum(rekap.qty) AS qty,
			    sum(rekap.harga_netto * rekap.qty) AS total,
					rekap.jenisobatalkes_id,
					rekap.jenisobatalkes_nama
			   FROM ( 
				 SELECT obatalkespasien_t.obatalkes_id,
			            to_char(penjualanresep_t.tglresep, 'YYYY-MM-DD'::text)::date AS tgl_pelayanan,
			            obatalkes_m.obatalkes_kode AS kode_obat,
			            obatalkes_m.obatalkes_nama AS nama_obat,
			            obatalkespasien_t.additional_data::json ->> 'satuan_input'::text AS satuan_input,
			                CASE
			                    WHEN (obatalkespasien_t.additional_data::json ->> 'satuan_konversi'::text) = ''::text THEN satuankecil.satuanunit_nama::text
			                    WHEN (obatalkespasien_t.additional_data::json ->> 'satuan_konversi'::text) = NULL::text THEN satuankecil.satuanunit_nama::text
			                    ELSE obatalkespasien_t.additional_data::json ->> 'satuan_konversi'::text
			                END AS satuan_kecil,
			            obatalkespasien_t.additional_data::json ->> 'qty_input'::text AS qty_input,
			                CASE
			                    WHEN obatalkespasien_t.det_konversi IS NOT NULL THEN COALESCE(obatalkespasien_t.det_konversi, 0::double precision)
			                    ELSE COALESCE(((obatalkespasien_t.additional_data::json ->> 'qty_input'::text)::double precision) * konv.nilai_konversi, obatalkespasien_t.qty_oa)
			                END AS qty,
			            COALESCE(obatalkes_m.harganetto, 0::double precision) AS harga_netto,
									obatalkes_m.jenisobatalkes_id,
									jenisobatalkes_m.jenisobatalkes_nama
			           FROM obatalkespasien_t
			             JOIN (SELECT a.penjualanresep_id,a.status_reseptur,a.tglresep from penjualanresep_t a )penjualanresep_t ON obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
			             JOIN (SELECT a.obatalkes_id,a.obatalkes_kode,a.obatalkes_nama,a.harganetto,a.jenisobatalkes_id,a.satuankecil_id from obatalkes_m a)obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
									 LEFT JOIN (SELECT a.jenisobatalkes_id,a.jenisobatalkes_nama from jenisobatalkes_m a)jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
			             LEFT JOIN (SELECT a.satuanunit_id,a.satuanunit_nama from satuanunit_m a) satuankecil ON obatalkes_m.satuankecil_id = satuankecil.satuanunit_id
			             JOIN ( SELECT satuankonversi_m.obatalkes_id,
			                    satuankonversi_m.satuanbesar_id,
			                    satuankonversi_m.satuankecil_id,
			                    satuankonversi_m.nilai_konversi
			                   FROM satuankonversi_m
			                  WHERE satuankonversi_m.is_deleted = false
			                  GROUP BY satuankonversi_m.obatalkes_id, satuankonversi_m.satuanbesar_id, satuankonversi_m.satuankecil_id, satuankonversi_m.nilai_konversi) konv ON obatalkespasien_t.obatalkes_id = konv.obatalkes_id AND (obatalkespasien_t.additional_data::json ->> 'satuaninput_id'::text) = konv.satuanbesar_id::text AND obatalkespasien_t.satuankecil_id = konv.satuankecil_id
			            left JOIN ( SELECT stokobatalkes_t.obatalkespasien_id
			                   FROM stokobatalkes_t
			                  WHERE stokobatalkes_t.is_deleted = false
			                  GROUP BY stokobatalkes_t.obatalkespasien_id) fix ON obatalkespasien_t.obatalkespasien_id = fix.obatalkespasien_id
			          WHERE obatalkespasien_t.racikan_id <> 2 OR obatalkespasien_t.is_deleted = false AND (penjualanresep_t.status_reseptur = ANY (ARRAY[432, 660])) AND
			                CASE
			                    WHEN obatalkespasien_t.det_konversi IS NOT NULL THEN obatalkespasien_t.det_konversi > 0::double precision
			                    ELSE COALESCE(((obatalkespasien_t.additional_data::json ->> 'qty_input'::text)::double precision) * konv.nilai_konversi, obatalkespasien_t.qty_oa) > 0::double precision
			                END
								UNION ALL
					
								SELECT obatalkespasien_t.obatalkes_id,
			           to_char(obatalkespasien_t.tglpelayanan, 'YYYY-MM-DD'::text)::date AS tgl_pelayanan,
			             obatalkes_m.obatalkes_kode AS kode_obat,
			             obatalkes_m.obatalkes_nama AS nama_obat,
			             obatalkespasien_t.additional_data::json ->> 'satuan_input'::text AS satuan_input,
			             COALESCE(obatalkespasien_t.additional_data::json ->> 'satuan_input'::text, satuanunit_m.satuanunit_nama::text)AS satuan_kecil,
			             Null::text AS qty_input,
			            obatalkespasien_t.qty_oa AS qty,
			            COALESCE(obatalkes_m.harganetto, 0::double precision) AS harga_netto,
									obatalkes_m.jenisobatalkes_id,
									jenisobatalkes_m.jenisobatalkes_nama
			           FROM obatalkespasien_t
			              LEFT JOIN ( SELECT a.pendaftaran_id,
			                    a.pasien_id,
			                    a.no_pendaftaran
			                   FROM pendaftaran_t a) pendaftaran_t ON obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
											LEFT JOIN ( SELECT a.pasien_id,
			                    a.no_rekam_medik,
			                    a.nama_pasien
			                   FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
											 LEFT JOIN ( SELECT a.obatalkes_id,
			                    a.obatalkes_nama,
			                    a.obatalkes_kode,
			                    a.supplier_id,
			                    a.is_formularium,
			                    COALESCE(a.is_psycothropica, false) AS is_psycothropica,
			                    COALESCE(a.is_narcotic, false) AS is_narcotic,
													a.harganetto,
													a.jenisobatalkes_id
			                   FROM obatalkes_m a) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
											   LEFT JOIN ( SELECT a.satuanunit_id,
			                    a.satuanunit_nama
			                   FROM satuanunit_m a) satuanunit_m ON obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id
			             LEFT JOIN ( SELECT a.carabayar_id,
			                    a.carabayar_nama
			                   FROM carabayar_m a) carabayar_m ON obatalkespasien_t.carabayar_id = carabayar_m.carabayar_id
			             LEFT JOIN ( SELECT a.penjamin_id,
			                    a.penjamin_nama
			                   FROM penjamin_m a) penjamin_m ON obatalkespasien_t.penjamin_id = penjamin_m.penjamin_id
			             LEFT JOIN ( SELECT a.supplier_id,
			                    a.supplier_nama
			                   FROM supplier_m a) supplier_m ON obatalkes_m.supplier_id = supplier_m.supplier_id
			             LEFT JOIN ( SELECT a.ruangan_id,
			                    a.ruangan_nama
			                   FROM ruangan_m a) ruangan_m ON obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id
			             LEFT JOIN ( SELECT a.tindakanpelayanan_id,
			                    a.dokterpenanggungjawab_id,
			                    a.dokterpelaksana_id
			                   FROM tindakanpelayanan_t a) tindakanpelayanan_t ON obatalkespasien_t.tindakanpelayanan_id = tindakanpelayanan_t.tindakanpelayanan_id
			             LEFT JOIN ( SELECT a.instruksitindakanbmhp_id,
			                    a.dokter_id
			                   FROM instruksitindakanbmhp_t a) instruksitindakanbmhp_t ON obatalkespasien_t.instruksitindakanbmhp_id = instruksitindakanbmhp_t.instruksitindakanbmhp_id
			             LEFT JOIN ( SELECT a.pegawai_id,
			                    a.nama_pegawai
			                   FROM pegawai_m a) dokterpenanggungjawab ON tindakanpelayanan_t.dokterpenanggungjawab_id = dokterpenanggungjawab.pegawai_id
			             LEFT JOIN ( SELECT a.pegawai_id,
			                    a.nama_pegawai
			                   FROM pegawai_m a) pegawai_pelaksana ON tindakanpelayanan_t.dokterpelaksana_id = dokterpenanggungjawab.pegawai_id
			             LEFT JOIN ( SELECT a.pegawai_id,
			                    a.nama_pegawai
			                   FROM pegawai_m a) dokterintruksi ON instruksitindakanbmhp_t.dokter_id = dokterintruksi.pegawai_id
			             LEFT JOIN ( SELECT lookup_m.lookup_id,
			                    lookup_m.lookup_name
			                   FROM lookup_m) lookup_status ON obatalkespasien_t.status_bmhp = lookup_status.lookup_id
									 LEFT JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
			          WHERE obatalkespasien_t.penjualanresep_id IS NULL
											) rekap
				WHERE rekap.tgl_pelayanan BETWEEN x_date::date and x_date::date
			  GROUP BY rekap.obatalkes_id, rekap.kode_obat, rekap.nama_obat, rekap.satuan_kecil, rekap.harga_netto,
				rekap.jenisobatalkes_id,
					rekap.jenisobatalkes_nama
			) x ;
	
			END
			\$BODY\$
			  LANGUAGE plpgsql IMMUTABLE
			  COST 100
			  ROWS 1000
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220915_101507_migrate_ordh_164_laporanrekappenjualanfarmasi_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220915_101507_migrate_ordh_164_laporanrekappenjualanfarmasi_fn cannot be reverted.\n";

        return false;
    }
    */
}
