<?php

use yii\db\Migration;

/**
 * Class m211228_081223_migrate_infostokobatrakdetail_v_is_deleted_false
 */
class m211228_081223_migrate_infostokobatrakdetail_v_is_deleted_false extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->execute('DROP VIEW if exists public.infostokobatrakdetail_v;');
				
         $this->execute("
             CREATE VIEW \"public\".\"infostokobatrakdetail_v\" AS
		 SELECT x.obatalkes_id,
		    x.stok_sistem,
		    x.obatalkes_nama,
		    x.harganetto,
		    x.instalasi_nama,
		    x.ruangan_nama,
		    x.ruangan_id,
		    x.instalasi_id,
		    x.sop_obatalkes_id,
		    x.rakobat_nama,
		    x.rakobat_id,
		    x.laciobat_id,
		    x.obatalkes_kode,
		    x.is_consigment,
		    x.laci,
		    x.jenisobatalkes_nama,
		    riwaya_obat.total_transaksi
		   FROM (( SELECT proses.obatalkes_id,
		            sum((proses.qtystok_in - proses.qtystok_out)) AS stok_sistem,
		            proses.obatalkes_nama,
		            proses.harganetto,
		            proses.instalasi_nama,
		            proses.ruangan_nama,
		            proses.ruangan_id,
		            proses.instalasi_id,
		            proses.sop_obatalkes_id,
		            proses.rakobat_nama,
		            proses.rakobat_id,
		            proses.laciobat_id,
		            proses.obatalkes_kode,
		            proses.is_consigment,
		            proses.laci,
		            proses.jenisobatalkes_nama
		           FROM ( SELECT
		                        CASE
		                            WHEN (stokobatalkes_t.stokobatalkesasal_id IS NULL) THEN stokobatalkes_t.stokobatalkes_id
		                            ELSE stokobatalkes_t.stokobatalkesasal_id
		                        END AS id_stok,
		                    stokobatalkes_t.obatalkes_id,
		                    stokobatalkes_t.qtystok_in,
		                    stokobatalkes_t.qtystok_out,
		                    stokobatalkes_t.tglkadaluarsa,
		                    obatalkes_m.obatalkes_nama,
		                    obatalkes_m.harganetto AS harganetto2,
		                    instalasi_m.instalasi_nama,
		                    ruangan_m.ruangan_nama,
		                    stokobatalkes_r.periodestokobat_id,
		                    ruangan_m.ruangan_id,
		                    instalasi_m.instalasi_id,
		                    formstokopname_t.obatalkes_id AS sop_obatalkes_id,
		                    formstokopname_t.stokopnamedetail_id AS sop_stokopnamedetail_id,
		                    konfigfarmasi_k.hargaygdigunakan,
		                    obatalkes_m.hargamaksimum,
		                    obatalkes_m.hargaminimum,
		                    obatalkes_m.hargaratarata,
		                        CASE
		                            WHEN ((konfigfarmasi_k.hargaygdigunakan)::text = 'MAX'::text) THEN obatalkes_m.hargamaksimum
		                            WHEN ((konfigfarmasi_k.hargaygdigunakan)::text = 'MIN'::text) THEN obatalkes_m.hargaminimum
		                            WHEN ((konfigfarmasi_k.hargaygdigunakan)::text = 'AVG'::text) THEN obatalkes_m.hargaratarata
		                            ELSE obatalkes_m.harganetto
		                        END AS harganetto,
		                    COALESCE((laci.rak_id)::bigint, rak.rakobat_id) AS rakobat_id,
		                    COALESCE(laci.rak, rak.rakobat_nama) AS rakobat_nama,
		                    COALESCE(laci.rakobat_id, rak.rakobat_id) AS laciobat_id,
		                    COALESCE(laci.rakobat_nama, rak.rakobat_nama) AS laci,
		                    obatalkes_m.obatalkes_kode,
		                    obatalkes_m.is_consigment,
		                    jenisobatalkes_m.jenisobatalkes_nama
		                   FROM ((((((((((stokobatalkes_t
		                     JOIN ( SELECT a.obatalkes_id,
		                            a.obatalkes_nama,
		                            a.harganetto,
		                            a.hargamaksimum,
		                            a.hargaminimum,
		                            a.hargaratarata,
		                            a.obatalkes_kode,
		                            a.is_consigment,
		                            a.jenisobatalkes_id
		                           FROM obatalkes_m a) obatalkes_m ON ((stokobatalkes_t.obatalkes_id = obatalkes_m.obatalkes_id)))
		                     LEFT JOIN ( SELECT a.jenisobatalkes_id,
		                            a.jenisobatalkes_nama
		                           FROM jenisobatalkes_m a) jenisobatalkes_m ON ((obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id)))
		                     JOIN ( SELECT a.ruangan_id,
		                            a.ruangan_nama,
		                            a.instalasi_id
		                           FROM ruangan_m a) ruangan_m ON ((stokobatalkes_t.ruangan_id = ruangan_m.ruangan_id)))
		                     JOIN ( SELECT a.instalasi_id,
		                            a.instalasi_nama
		                           FROM instalasi_m a) instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
		                     LEFT JOIN ( SELECT a.formstokopname_id,
		                            a.obatalkes_id,
		                            a.is_deleted,
		                            a.ruangan_id,
		                            a.stokopnamedetail_id
		                           FROM (formstokopname_t a
		                             JOIN formulirstokopname_t ON (((a.formulirstokopname_id = formulirstokopname_t.formulirstokopname_id) AND (formulirstokopname_t.is_deleted = false))))
		                          WHERE ((a.stokopnamedetail_id IS NULL) AND (a.is_deleted = false))) formstokopname_t ON (((stokobatalkes_t.obatalkes_id = formstokopname_t.obatalkes_id) AND (stokobatalkes_t.ruangan_id = formstokopname_t.ruangan_id))))
		                     JOIN ( SELECT a.obatalkes_id,
		                            a.ruangan_id,
		                            a.is_periode,
		                            a.stokobatr_id,
		                            a.periodestokobat_id
		                           FROM stokobatalkes_r a) stokobatalkes_r ON (((stokobatalkes_t.obatalkes_id = stokobatalkes_r.obatalkes_id) AND (stokobatalkes_t.ruangan_id = stokobatalkes_r.ruangan_id) AND (stokobatalkes_r.is_periode = true))))
		                     LEFT JOIN ( SELECT a.stokobatr_id,
		                            a.rakobat_id
		                           FROM konfigrak_m a) konfigrak_m ON ((stokobatalkes_r.stokobatr_id = konfigrak_m.stokobatr_id)))
		                     LEFT JOIN ( SELECT a.rakobat_id,
		                            a.rakobat_nama
		                           FROM rakobat_m a) rak ON ((konfigrak_m.rakobat_id = rak.rakobat_id)))
		                     LEFT JOIN ( SELECT a.rakobat_id,
		                            a.parentrakobat_id AS rak_id,
		                            rak_1.rakobat_nama AS rak,
		                            a.rakobat_nama
		                           FROM (rakobat_m a
		                             JOIN rakobat_m rak_1 ON ((a.parentrakobat_id = rak_1.rakobat_id)))
		                          WHERE (a.parentrakobat_id IS NOT NULL)) laci ON ((konfigrak_m.rakobat_id = laci.rakobat_id)))
		                     JOIN ( SELECT a.is_deleted,
		                            a.hargaygdigunakan
		                           FROM konfigfarmasi_k a) konfigfarmasi_k ON ((konfigfarmasi_k.is_deleted = false)))
		                  WHERE (formstokopname_t.formstokopname_id IS NULL)) proses
		          GROUP BY proses.obatalkes_id, proses.obatalkes_nama, proses.instalasi_nama, proses.ruangan_nama, proses.harganetto, proses.ruangan_id, proses.instalasi_id, proses.sop_obatalkes_id, proses.rakobat_nama, proses.rakobat_id, proses.laciobat_id, proses.obatalkes_kode, proses.is_consigment, proses.laci, proses.jenisobatalkes_nama) x
		     LEFT JOIN ( SELECT stokobatalkes_t.obatalkes_id,
		            stokobatalkes_t.ruangan_id,
		            count(stokobatalkes_t.obatalkes_id) AS total_transaksi
		           FROM stokobatalkes_t
		          WHERE ((stokobatalkes_t.is_deleted = false) AND (stokobatalkes_t.created_date >= (CURRENT_DATE - '6 mons'::interval)))
		          GROUP BY stokobatalkes_t.obatalkes_id, stokobatalkes_t.ruangan_id) riwaya_obat ON (((x.ruangan_id = riwaya_obat.ruangan_id) AND (x.obatalkes_id = riwaya_obat.obatalkes_id))))
		  WHERE ((x.stok_sistem > (0)::double precision) OR (riwaya_obat.total_transaksi <> 0))");
    
	              $this->execute('
	                  ALTER TABLE public.infostokobatrakdetail_v OWNER TO postgres;');
    
    
		
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211228_081223_migrate_infostokobatrakdetail_v_is_deleted_false cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211228_081223_migrate_infostokobatrakdetail_v_is_deleted_false cannot be reverted.\n";

        return false;
    }
    */
}
