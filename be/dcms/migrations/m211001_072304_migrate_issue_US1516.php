<?php

use yii\db\Migration;

/**
 * Class m211001_072304_migrate_issue_US1516
 */
class m211001_072304_migrate_issue_US1516 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    
    $this->execute('ALTER TABLE "public"."basecalro_r" ADD COLUMN if not exists "last_7" float4;');
    $this->execute('ALTER TABLE "public"."basecalro_r" ADD COLUMN if not exists "last_14" float4;');
    $this->execute('ALTER TABLE "public"."basecalro_r" ADD COLUMN if not exists "last_30" float4;');

    $this->execute('DROP VIEW if exists "public"."sumstokout_v";');

    $this->execute("
        CREATE VIEW \"public\".\"sumstokout_v\" AS  SELECT count_stok.obatalkes_id,
    count(count_stok.tanggal) AS count,
    max(count_stok.stok_out) AS max,
    min(count_stok.stok_out) AS min,
    sum(count_stok.stok_out) / count(count_stok.tanggal)::double precision AS avg,
    min(count_stok.min_resep) AS min_resep,
    obatalkes_m.jenisobatalkes_id,
    sum(count_stok.last_7) AS last_7,
    sum(count_stok.last_14) AS last_14,
    sum(count_stok.last_30) AS last_30
   FROM ( SELECT detail.obatalkes_id,
            detail.tanggal,
            sum(detail.qtystok_out) AS stok_out,
            min(detail.qty_resep) AS min_resep,
            sum(
                CASE
                    WHEN detail.tanggal >= (CURRENT_DATE - '7 days'::interval) THEN detail.qtystok_out
                    ELSE 0::double precision
                END) AS last_7,
            sum(
                CASE
                    WHEN detail.tanggal >= (CURRENT_DATE - '14 days'::interval) THEN detail.qtystok_out
                    ELSE 0::double precision
                END) AS last_14,
            sum(
                CASE
                    WHEN detail.tanggal >= (CURRENT_DATE - '30 days'::interval) THEN detail.qtystok_out
                    ELSE 0::double precision
                END) AS last_30
           FROM ( SELECT stokobatalkes_t.obatalkes_id,
                    to_char(stokobatalkes_t.tglstok_out, 'YYYY-MM-DD'::text)::date AS tanggal,
                    stokobatalkes_t.qtystok_out,
                        CASE
                            WHEN stokobatalkes_t.obatalkespasien_id IS NOT NULL AND stokobatalkes_t.tglstok_out IS NOT NULL THEN stokobatalkes_t.qtystok_out
                            ELSE 0::double precision
                        END AS qty_resep,
                    stokobatalkes_t.mutasiobatdetail_id,
                    mutasiobatdetail.ruanganasal_id,
                    mutasiobatdetail.ruangantujuan_id
                   FROM stokobatalkes_t
                     LEFT JOIN ( SELECT mutasiobatdetail_t.mutasiobatdetail_id,
                            mutasiobatruangan_t.ruanganasal_id,
                            mutasiobatruangan_t.ruangantujuan_id
                           FROM mutasiobatdetail_t
                             JOIN mutasiobatruangan_t ON mutasiobatdetail_t.mutasiobatruangan_id = mutasiobatruangan_t.mutasiobatruangan_id) mutasiobatdetail ON stokobatalkes_t.mutasiobatdetail_id = mutasiobatdetail.mutasiobatdetail_id
                  WHERE (stokobatalkes_t.mutasiobatdetail_id IS NOT NULL AND NOT (mutasiobatdetail.ruangantujuan_id IN ( SELECT ruangan_m.ruangan_id
                           FROM ruangan_m
                          WHERE ruangan_m.instalasi_id = 6)) AND mutasiobatdetail.ruanganasal_id = 25 OR stokobatalkes_t.obatalkespasien_id IS NOT NULL AND stokobatalkes_t.tglstok_out IS NOT NULL) AND (stokobatalkes_t.tglstok_out >= CURRENT_DATE::timestamp without time zone AND stokobatalkes_t.tglstok_out <= (CURRENT_DATE - '30 days'::interval) OR stokobatalkes_t.tglstok_out >= (CURRENT_DATE - '30 days'::interval) AND stokobatalkes_t.tglstok_out <= CURRENT_DATE::timestamp without time zone)
                  ORDER BY stokobatalkes_t.obatalkes_id, (to_char(stokobatalkes_t.tglstok_out, 'YYYY-MM-DD'::text)::date)) detail
          GROUP BY detail.obatalkes_id, detail.tanggal
          ORDER BY detail.obatalkes_id, detail.tanggal) count_stok
     LEFT JOIN obatalkes_m ON count_stok.obatalkes_id = obatalkes_m.obatalkes_id
  GROUP BY count_stok.obatalkes_id, obatalkes_m.jenisobatalkes_id
  ORDER BY count_stok.obatalkes_id;");
    

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211001_072304_migrate_issue_US1516 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211001_072304_migrate_issue_US1516 cannot be reverted.\n";

        return false;
    }
    */
}
