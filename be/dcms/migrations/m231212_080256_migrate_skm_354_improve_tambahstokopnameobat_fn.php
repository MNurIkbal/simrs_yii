<?php

use yii\db\Migration;

/**
 * Class m231212_080256_migrate_skm_354_improve_tambahstokopnameobat_fn
 */
class m231212_080256_migrate_skm_354_improve_tambahstokopnameobat_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP FUNCTION if exists public.tambahstokopnameobat_fn;');

        $this->execute("
			CREATE OR REPLACE FUNCTION public.tambahstokopnameobat_fn(x_ruangan_id int4)
			  RETURNS TABLE(kondisi text, obatalkes_id int4, obatalkes_kode varchar, obatalkes_nama varchar, jenisobatalkes_nama varchar, servicecategory_nama varchar, servicegroup_nama text, satuankecil_id int4, satuankeci varchar, satuanbesar_id int4, satuanbesar varchar, stok_sistem int4, rakobat_id int4, rakobat_nama varchar, laciobat_id int4, laci varchar, uom text, stok_saatini int4, stok_in int4, stok_out int4)
		 AS \$BODY\$ BEGIN
						                RETURN QUERY 
                
        
						        SELECT DISTINCT ON
						                ( x.obatalkes_id :: INT ) 
						                x.kondisi,
						                x.obatalkes_id,
						                x.obatalkes_kode,
						                x.obatalkes_nama,
						                x.jenisobatalkes_nama,
						                x.servicecategory_nama,
						                x.servicegroup_nama,
						                x.satuankecil_id,
						                satuankecil.satuanunit_nama as satuankecil,
						                x.satuanbesar_id,
						                satuanbesar.satuanunit_nama as satuanbesar,
						                COALESCE(stokobatalkes.stok_sistem,0)::int as stok_sistem,
						                COALESCE(laci.rak_id::bigint, rak.rakobat_id)::int AS rakobat_id,
						    COALESCE(laci.rak, rak.rakobat_nama) AS rakobat_nama,
						          COALESCE(laci.rakobat_id, rak.rakobat_id)::int AS laciobat_id,
						                COALESCE(laci.rakobat_nama, rak.rakobat_nama) AS laci,
						                concat('1 ', uom.uom_besar, ' = ', uom.nilai_konversi, ' ', uom.uom_kecil) AS uom,
						                COALESCE(stokobatalkes_r.qty_sisa,0)::int as stok_saatini,
						                stokobatalkes.stok_in::int,
						                stokobatalkes.stok_out::int
        
						        FROM
						                (
						                SELECT A
						                        .* 
						                FROM
						                        (
						                        SELECT
						                                'STOKOPNAME'::TEXT as Kondisi,
						                                obatalkes_m.obatalkes_id,
						                                obatalkes_m.obatalkes_kode,
						                                obatalkes_m.obatalkes_nama,
						                                jenisobatalkes_m.jenisobatalkes_nama,
						                                servicecategory_m.servicecategory_nama,
						                                servicegroup_m.servicegroup_nama,
						                                obatalkes_m.satuankecil_id,
						                                obatalkes_m.satuanbesar_id
						                        FROM
						                                obatalkes_m
						                                LEFT JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
						                                LEFT JOIN servicecategory_m ON jenisobatalkes_m.servicecategory_id = servicecategory_m.servicecategory_id
						                                LEFT JOIN servicegroup_m ON jenisobatalkes_m.servicegroup_id = servicegroup_m.servicegroup_id 
						                        WHERE
						                                obatalkes_m.is_deleted = FALSE 
						                                AND obatalkes_m.is_active = TRUE 
						                        ) A 
						                WHERE
						                        A.obatalkes_id NOT IN (
						                        SELECT DISTINCT
						                                formstokopname_t.obatalkes_id 
						                        FROM
						                                formstokopname_t
						                                JOIN formulirstokopname_t ON formstokopname_t.formulirstokopname_id = formulirstokopname_t.formulirstokopname_id
						                                LEFT JOIN (
						                                SELECT
						                                        st.ruangan_id,
						                                        st.obatalkes_id,
						                                        SUM ( st.qtystok_in - st.qtystok_out ) AS total 
						                                FROM
						                                        stokobatalkes_t st 
						                                WHERE
						                                        st.stokoa_aktif = TRUE 
						                                        AND st.ruangan_id = x_ruangan_id::INT 
						                                GROUP BY
						                                        st.ruangan_id,
						                                        st.obatalkes_id 
						                                ) kartustok ON kartustok.ruangan_id = formulirstokopname_t.ruangan_id 
						                                AND kartustok.obatalkes_id = formstokopname_t.obatalkes_id 
						                        WHERE
						                                formulirstokopname_t.ruangan_id = x_ruangan_id::int
																						and formstokopname_t.is_deleted = false
						                        ) 
						                        AND A.obatalkes_id NOT IN (
						                        SELECT DISTINCT
						                                stokopnamedetail_t.obatalkes_id 
						                        FROM
						                                stokopnamedetail_t
						                                INNER JOIN stokopname_t ON stokopname_t.stokopname_id = stokopnamedetail_t.stokopname_id 
						                        WHERE
						                                stokopname_t.is_verifikasi = FALSE 
						                                AND stokopname_t.ruangan_id = x_ruangan_id :: INT 
						                        ) UNION ALL
						                SELECT A
						                        .* 
						                FROM
						                        (
						                        SELECT        
						                         'FORMULIR'::TEXT as Kondisi,
						                                obatalkes_m.obatalkes_id,
						                                obatalkes_m.obatalkes_kode,
						                                obatalkes_m.obatalkes_nama,
						                                jenisobatalkes_m.jenisobatalkes_nama,
						                                servicecategory_m.servicecategory_nama,
						                                servicegroup_m.servicegroup_nama,
						                                obatalkes_m.satuankecil_id,
						                                obatalkes_m.satuanbesar_id
						                        FROM
						                                obatalkes_m
						                                LEFT JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
						                                LEFT JOIN servicecategory_m ON jenisobatalkes_m.servicecategory_id = servicecategory_m.servicecategory_id
						                                LEFT JOIN servicegroup_m ON jenisobatalkes_m.servicegroup_id = servicegroup_m.servicegroup_id 
						                        WHERE
						                                obatalkes_m.is_deleted = FALSE 
						                                AND obatalkes_m.is_active = TRUE 
						                        ) A 
						                WHERE
						                        A.obatalkes_id NOT IN (
						                        SELECT DISTINCT
						                                formstokopname_t.obatalkes_id 
						                        FROM
						                                formstokopname_t
						                                JOIN formulirstokopname_t ON formstokopname_t.formulirstokopname_id = formulirstokopname_t.formulirstokopname_id
						                                LEFT JOIN (
						                                SELECT
						                                        st.ruangan_id,
						                                        st.obatalkes_id,
						                                        SUM ( st.qtystok_in - st.qtystok_out ) AS total 
						                                FROM
						                                        stokobatalkes_t st 
						                                WHERE
						                                        st.stokoa_aktif = TRUE 
						                                        AND st.ruangan_id = x_ruangan_id :: INT 
						                                GROUP BY
						                                        st.ruangan_id,
						                                        st.obatalkes_id 
						                                ) kartustok ON kartustok.ruangan_id = formulirstokopname_t.ruangan_id 
						                                AND kartustok.obatalkes_id = formstokopname_t.obatalkes_id 
						                        ORDER BY
						                                formstokopname_t.obatalkes_id 
						                        ) 
						                ) x
						                LEFT join (SELECT a.satuanunit_id,a.satuanunit_nama from satuanunit_m a ) satuankecil on x.satuankecil_id = satuankecil.satuanunit_id
						                LEFT join (SELECT a.satuanunit_id,a.satuanunit_nama from satuanunit_m a ) satuanbesar on x.satuanbesar_id = satuanbesar.satuanunit_id
						                LEFT JOIN (SELECT a.obatalkes_id,a.ruangan_id,COALESCE(sum(qtystok_in),0) - COALESCE(sum(qtystok_out),0) as stok_sistem , COALESCE(sum(qtystok_in),0) as stok_in,COALESCE(sum(qtystok_out),0) as stok_out from stokobatalkes_t a where a.is_deleted = false and a.ruangan_id = x_ruangan_id :: INT GROUP BY a.obatalkes_id,a.ruangan_id ) stokobatalkes 
						                on x.obatalkes_id = stokobatalkes.obatalkes_id 
						                LEFT JOIN (SELECT a.obatalkes_id,a.stokobatr_id,COALESCE(a.qty_sisa,0) as qty_sisa from stokobatalkes_r a where a.ruangan_id  = x_ruangan_id :: int ) stokobatalkes_r on x.obatalkes_id = stokobatalkes_r.obatalkes_id
                 
						                LEFT JOIN ( SELECT a.stokobatr_id,a.rakobat_id,a.obatalkes_id FROM konfigrak_m a WHERE a.ruangan_id = x_ruangan_id :: int) konfigrak_m ON stokobatalkes_r.obatalkes_id = konfigrak_m.obatalkes_id and stokobatalkes_r.stokobatr_id = konfigrak_m.stokobatr_id
						                LEFT JOIN ( SELECT a.rakobat_id,a.rakobat_nama FROM rakobat_m a) rak ON konfigrak_m.rakobat_id = rak.rakobat_id
						                LEFT JOIN ( SELECT a.rakobat_id,a.parentrakobat_id AS rak_id,rak_1.rakobat_nama AS rak,a.rakobat_nama FROM rakobat_m a JOIN rakobat_m rak_1 ON a.parentrakobat_id = rak_1.rakobat_id WHERE a.parentrakobat_id IS NOT NULL) laci ON konfigrak_m.rakobat_id = laci.rakobat_id
						                LEFT JOIN ( SELECT a.obatalkes_id,a.satuankecil_id,a.satuanbesar_id,uom_besar.satuanunit_nama AS uom_besar,uom_kecil.satuanunit_nama AS uom_kecil,a.nilai_konversi FROM satuankonversi_m a
						             LEFT JOIN (select a.satuanunit_id,a.satuanunit_nama from satuanunit_m a) uom_besar ON a.satuanbesar_id = uom_besar.satuanunit_id
						             LEFT JOIN (select a.satuanunit_id,a.satuanunit_nama from satuanunit_m a) uom_kecil ON a.satuankecil_id = uom_kecil.satuanunit_id
						          WHERE a.is_deleted = false AND a.is_active = true
						          GROUP BY a.obatalkes_id, a.satuankecil_id, a.satuanbesar_id, uom_besar.satuanunit_nama, uom_kecil.satuanunit_nama, a.nilai_konversi) uom 
						                ON x.obatalkes_id = uom.obatalkes_id AND x.satuanbesar_id = uom.satuanbesar_id AND x.satuankecil_id = uom.satuankecil_id;
   
						END \$BODY\$
			  LANGUAGE plpgsql IMMUTABLE
			  COST 100
			  ROWS 1000
           ;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231212_080256_migrate_skm_354_improve_tambahstokopnameobat_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231212_080256_migrate_skm_354_improve_tambahstokopnameobat_fn cannot be reverted.\n";

        return false;
    }
    */
}
