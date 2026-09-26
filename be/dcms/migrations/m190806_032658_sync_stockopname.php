<?php

use yii\db\Migration;

/**
 * Class m190806_032658_sync_stockopname
 */
class m190806_032658_sync_stockopname extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
      DROP VIEW if exists public.sync_stockopname;
        ');

        $this->execute("
     CREATE OR REPLACE VIEW public.sync_stockopname AS 
 SELECT 'Obat'::character varying AS tipe_transaksi,
    stokopname_t.jenisstokopname,
    stokopname_t.stokopname_id AS id,
    stokopname_t.nostokopname,
    stokopname_t.tglstokopname,
    ruangan_m.instalasi_id,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    ('Stok Opname '::text ||
        CASE stokopname_t.jenisstokopname
            WHEN 'SA'::text THEN 'Stok Awal '::text
            ELSE 'Penyesuian '::text || formulirstokopname_t.noformulir::text
        END) || ' '::text AS \"desc\",
    ( SELECT array_to_json(array_agg(row_to_json(d1.*))) AS array_to_json
           FROM ( SELECT jenisobatalkes_m.jenisobatalkes_kode,
                    obatalkes_m.obatalkes_id,
                    obatalkes_m.obatalkes_nama,
                    stokopnamedetail_t.volume_fisik,
                    stokopnamedetail_t.volume_sistem,
                    stokopnamedetail_t.jmlselisihstok,
                    stokopnamedetail_t.harganetto
                   FROM stokopnamedetail_t
                     JOIN formstokopname_t ON stokopnamedetail_t.stokopnamedetail_id = formstokopname_t.stokopnamedetail_id
                     JOIN obatalkes_m ON stokopnamedetail_t.obatalkes_id = obatalkes_m.obatalkes_id
                     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
                  WHERE stokopnamedetail_t.stokopname_id = stokopname_t.stokopname_id AND formstokopname_t.formulirstokopname_id = formulirstokopname_t.formulirstokopname_id AND stokopnamedetail_t.is_deleted IS FALSE) d1) AS detail_obat,
    ( SELECT array_to_json(array_agg(row_to_json(d2.*))) AS array_to_json
           FROM ( SELECT jenisobatalkes_m.jenisobatalkes_kode,
                    sum(stokopnamedetail_t.volume_fisik * stokopnamedetail_t.harganetto) AS amount_real,
                    sum(stokopnamedetail_t.volume_sistem * stokopnamedetail_t.harganetto) AS amount_system
                   FROM stokopnamedetail_t
                     JOIN formstokopname_t ON stokopnamedetail_t.stokopnamedetail_id = formstokopname_t.stokopnamedetail_id
                     JOIN obatalkes_m ON stokopnamedetail_t.obatalkes_id = obatalkes_m.obatalkes_id
                     JOIN jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
                  WHERE stokopnamedetail_t.stokopname_id = stokopname_t.stokopname_id AND formstokopname_t.formulirstokopname_id = formulirstokopname_t.formulirstokopname_id AND stokopnamedetail_t.is_deleted IS FALSE
                  GROUP BY jenisobatalkes_m.jenisobatalkes_kode) d2) AS detail_jenisobat
   FROM stokopname_t
     JOIN formulirstokopname_t ON stokopname_t.stokopname_id = formulirstokopname_t.stokopname_id
     JOIN ruangan_m ON formulirstokopname_t.ruangan_id = ruangan_m.ruangan_id
  WHERE NOT (stokopname_t.stokopname_id IN ( SELECT COALESCE(syncakuntansi_r.stokopname_id, 0) AS stokopname_id
           FROM syncakuntansi_r
          WHERE syncakuntansi_r.is_sync IS TRUE))
UNION ALL
 SELECT 'Obat'::character varying AS tipe_transaksi,
    stokopnamebarang_t.jenisstokopname,
    stokopnamebarang_t.stokopnamebarang_id AS id,
    stokopnamebarang_t.nostokopname,
    stokopnamebarang_t.tglstokopname,
    ruangan_m.instalasi_id,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    (('Stok Opname '::text ||
        CASE stokopnamebarang_t.jenisstokopname
            WHEN 'SA'::text THEN 'Stok Awal '::text
            ELSE 'Penyesuian '::text || formsobarang_t.noformulir::text
        END) || ' '::text) || formsobarang_t.noformulir::text AS \"desc\",
    ( SELECT array_to_json(array_agg(row_to_json(d1.*))) AS array_to_json
           FROM ( SELECT kelompokbarang_m.kelompokbarang_kode AS jenisobatalkes_kode,
                    barang_m.barang_id AS obatalkes_id,
                    barang_m.barang_nama AS obatalkes_nama,
                    stokopnamebarangdetail_t.volume_fisik,
                    stokopnamebarangdetail_t.volume_sistem,
                    stokopnamebarangdetail_t.jmlselisihstok,
                    stokopnamebarangdetail_t.harganetto
                   FROM stokopnamebarangdetail_t
                     JOIN formsobarangdetail_t ON stokopnamebarangdetail_t.stokopnamebarangdetail_id = formsobarangdetail_t.stokopnamebarangdetail_id
                     JOIN barang_m ON stokopnamebarangdetail_t.barang_id = barang_m.barang_id
                     JOIN kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
                  WHERE stokopnamebarangdetail_t.stokopnamebarang_id = stokopnamebarang_t.stokopnamebarang_id AND formsobarangdetail_t.formsobarang_id = formsobarang_t.formsobarang_id AND stokopnamebarangdetail_t.is_deleted IS FALSE) d1) AS detail_obat,
    ( SELECT array_to_json(array_agg(row_to_json(d2.*))) AS array_to_json
           FROM ( SELECT kelompokbarang_m.kelompokbarang_kode AS jenisobatalkes_kode,
                    sum(stokopnamebarangdetail_t.volume_fisik * stokopnamebarangdetail_t.harganetto) AS amount_real,
                    sum(stokopnamebarangdetail_t.volume_sistem * stokopnamebarangdetail_t.harganetto) AS amount_system
                   FROM stokopnamebarangdetail_t
                     JOIN formsobarangdetail_t ON stokopnamebarangdetail_t.stokopnamebarangdetail_id = formsobarangdetail_t.stokopnamebarangdetail_id
                     JOIN barang_m ON stokopnamebarangdetail_t.barang_id = barang_m.barang_id
                     JOIN kelompokbarang_m ON barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id
                  WHERE stokopnamebarangdetail_t.stokopnamebarang_id = stokopnamebarang_t.stokopnamebarang_id AND formsobarangdetail_t.formsobarang_id = formsobarang_t.formsobarang_id AND stokopnamebarangdetail_t.is_deleted IS FALSE
                  GROUP BY kelompokbarang_m.kelompokbarang_kode) d2) AS detail_jenisobat
   FROM stokopnamebarang_t
     JOIN formsobarang_t ON stokopnamebarang_t.stokopnamebarang_id = formsobarang_t.stokopnamebarang_id
     JOIN ruangan_m ON formsobarang_t.ruangan_id = ruangan_m.ruangan_id
  WHERE NOT (stokopnamebarang_t.stokopnamebarang_id IN ( SELECT COALESCE(syncakuntansi_r.stokopnamebarang_id, 0) AS stokopnamebarang_id
           FROM syncakuntansi_r
          WHERE syncakuntansi_r.is_sync IS TRUE));
        ");

        $this->execute('
      ALTER TABLE public.sync_stockopname
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190806_032658_sync_stockopname cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190806_032658_sync_stockopname cannot be reverted.\n";

        return false;
    }
    */
}
