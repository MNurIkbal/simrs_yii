<?php

use yii\db\Migration;

/**
 * Class m231211_104827_rpp_899_addindex
 */
class m231211_104827_rpp_899_addindex extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->execute('CREATE INDEX CONCURRENTLY IF NOT EXISTS "idx_antriant_noantrian_nullslast" ON "public"."antrian_t" USING btree (
            "no_antrian" COLLATE "pg_catalog"."default" "pg_catalog"."text_ops" ASC NULLS LAST
          );
        ');
          
        $this->execute('CREATE INDEX CONCURRENTLY IF NOT EXISTS "idx_obatalkespasien_t_penjualanresep_idracikan_idis_kronis" ON "public"."obatalkespasien_t" USING btree (
            "penjualanresep_id" "pg_catalog"."int4_ops" ASC NULLS LAST,
            "racikan_id" "pg_catalog"."int4_ops" ASC NULLS LAST,
            "is_kronis" "pg_catalog"."bool_ops" ASC NULLS LAST
          ) WHERE NOT is_deleted;
        ');
          
        $this->execute('CREATE INDEX CONCURRENTLY IF NOT EXISTS "idx_obatalkespasien_t_penjualanresepid_pembayaran_id" ON "public"."obatalkespasien_t" USING btree (
            "penjualanresep_id" "pg_catalog"."int4_ops" ASC NULLS LAST,
            "pembayaran_id" "pg_catalog"."int4_ops" ASC NULLS LAST
          );
        ');
          
          
        $this->execute('CREATE INDEX CONCURRENTLY IF NOT EXISTS "idx_obatalkespasien_t_penjualanresepid_racikan_id" ON "public"."obatalkespasien_t" USING btree (
            "penjualanresep_id" "pg_catalog"."int4_ops" ASC NULLS LAST,
            "racikan_id" "pg_catalog"."int4_ops" ASC NULLS LAST
          ) WHERE NOT is_deleted;
        ');
          
          
        $this->execute('CREATE INDEX CONCURRENTLY IF NOT EXISTS "idx_pasienpulang_t_pasienadmisi_id" ON "public"."pasienpulang_t" USING btree (
            "pasienadmisi_id" "pg_catalog"."int4_ops" ASC NULLS LAST
          );
        ');
          
        $this->execute('CREATE INDEX CONCURRENTLY IF NOT EXISTS "idx_pendaftaran_t_pasienadmisi_id_notisdeleted" ON "public"."pendaftaran_t" USING btree (
            "pasienadmisi_id" "pg_catalog"."int4_ops" ASC NULLS LAST
          ) WHERE is_active AND NOT is_deleted;
        ');
          
          
        $this->execute('CREATE INDEX CONCURRENTLY IF NOT EXISTS "idx_penjualanresep_t_antrian_id" ON "public"."penjualanresep_t" USING btree (
            "antrian_id" "pg_catalog"."int4_ops" ASC NULLS LAST
          );
        ');
          
          
        $this->execute('CREATE INDEX CONCURRENTLY IF NOT EXISTS "idx_penjualanresep_t_reseptur_id" ON "public"."penjualanresep_t" USING btree (
            "reseptur_id" "pg_catalog"."int4_ops" ASC NULLS LAST
          );
        ');
          
          
        $this->execute('CREATE INDEX CONCURRENTLY IF NOT EXISTS "idx_reseptur_ruanganid_penjualanresepnull" ON "public"."reseptur_t" USING btree (
            "ruangan_id" "pg_catalog"."int4_ops" ASC NULLS LAST
          ) WHERE NOT is_deleted AND is_active AND penjualanresep_id IS NULL;
          
        ');
          
        $this->execute('CREATE INDEX CONCURRENTLY IF NOT EXISTS "idx_reseptur_t_ruanganreseptur_id" ON "public"."reseptur_t" USING btree (
            "ruanganreseptur_id" "pg_catalog"."int4_ops" ASC NULLS LAST
          );
        ');
          
          
        $this->execute('CREATE INDEX CONCURRENTLY IF NOT EXISTS "idx_resepturdetail_t_reseptur_id_notisdeleted" ON "public"."resepturdetail_t" USING btree (
            "reseptur_id" "pg_catalog"."int4_ops" ASC NULLS LAST
          ) WHERE NOT is_deleted AND is_active;
        ');
          
          
        $this->execute('CREATE INDEX CONCURRENTLY IF NOT EXISTS "idx_resepturdetail_t_reseptur_idracikan_idis_kronis" ON "public"."resepturdetail_t" USING btree (
            "reseptur_id" "pg_catalog"."int4_ops" ASC NULLS LAST,
            "racikan_id" "pg_catalog"."int4_ops" ASC NULLS LAST,
            "is_kronis" "pg_catalog"."bool_ops" ASC NULLS LAST
          ) WHERE NOT is_deleted;
        ');
          
          
        $this->execute('CREATE INDEX CONCURRENTLY IF NOT EXISTS "idx_resepturdetail_t_resepturid_racikan_id" ON "public"."resepturdetail_t" USING btree (
            "reseptur_id" "pg_catalog"."int4_ops" ASC NULLS LAST,
            "racikan_id" "pg_catalog"."int4_ops" ASC NULLS LAST
          ) WHERE NOT is_deleted;
        ');
          
          
        $this->execute('CREATE INDEX CONCURRENTLY IF NOT EXISTS "idx_resepturracikan_resepturid" ON "public"."resepturracikan_t" USING btree (
            "reseptur_id" "pg_catalog"."int4_ops" ASC NULLS LAST
          );
        ');
          
          
        $this->execute('CREATE INDEX CONCURRENTLY IF NOT EXISTS "penjualanresep_t_tglantrian_castdate" ON "public"."antrian_t" USING btree (
            (tgl_antrian::date) "pg_catalog"."date_ops" DESC NULLS FIRST
          );
        ');
          
        $this->execute('CREATE INDEX CONCURRENTLY IF NOT EXISTS "penjualanresep_t_tglcetaketiket_castdate" ON "public"."penjualanresep_t" USING btree (
            (tgl_cetak_etiket::date) "pg_catalog"."date_ops" DESC NULLS FIRST
          );
        ');
          
        $this->execute('CREATE INDEX CONCURRENTLY IF NOT EXISTS "penjualanresep_t_tglresep_castdate" ON "public"."penjualanresep_t" USING btree (
            (tglresep::date) "pg_catalog"."date_ops" DESC NULLS FIRST
          );
        ');
          
        $this->execute('CREATE INDEX CONCURRENTLY IF NOT EXISTS "reseptur_t_tglcetaketiket_castdate" ON "public"."reseptur_t" USING btree (
            (tgl_cetak_etiket::date) "pg_catalog"."date_ops" DESC NULLS FIRST
          );
        ');
          
        $this->execute('CREATE INDEX CONCURRENTLY IF NOT EXISTS "reseptur_t_tglreseptur_castdate" ON "public"."reseptur_t" USING btree (
        (tglreseptur::date) "pg_catalog"."date_ops" DESC NULLS FIRST
        );
        ');

        $this->execute("DROP VIEW IF EXISTS prescribe_v");
        $prescribe_v = file_get_contents(__DIR__ . '/definitions/prescribe_v.view.sql');
        $this->execute($prescribe_v);
          
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231211_104827_rpp_899_addindex cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231211_104827_rpp_899_addindex cannot be reverted.\n";

        return false;
    }
    */
}
