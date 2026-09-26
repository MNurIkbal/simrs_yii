<?php

use yii\db\Migration;

/**
 * Class m220624_115839_migrate_cssd_stokbarangalkes_v
 */
class m220624_115839_migrate_cssd_stokbarangalkes_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.stokbarangalkes_v;');

        $this->execute("CREATE VIEW \"public\".\"stokbarangalkes_v\" AS
           SELECT 'BARANG'::text AS tipe,
           barang_m.barang_id AS barangalkes_id,
           barang_m.barang_kode AS barangalkes_kode,
           barang_m.barang_nama AS barangalkes_nama,
           ruangan_m.ruangan_id,
           ruangan_m.ruangan_nama,
           barang_m.satuankecil_id,
           satuanunit_m.satuanunit_nama,
           CASE
           WHEN (cssd_t.ruanganasal_id IS NOT NULL) THEN cssd_t.ruanganasal_id
           ELSE ruangan_m.ruangan_id
           END AS ruanganasal_id,
           stokbarang_r.qty_tersedia,
           sum(cssd_t.qty) AS qty,
           CASE
           WHEN (cssd_t.ruanganasal_id IS NOT NULL) THEN (stokbarang_r.qty_tersedia - COALESCE(sum(cssd_t.qty), (0)::bigint))
           ELSE (stokbarang_r.qty_tersedia)::bigint
           END AS stok_tersedia
           FROM ((((barang_m
           JOIN ( SELECT a.satuanunit_id,
           a.satuanunit_nama
           FROM satuanunit_m a) satuanunit_m ON ((barang_m.satuankecil_id = satuanunit_m.satuanunit_id)))
           JOIN ( SELECT a.barang_id,
           a.ruangan_id,
           a.qty_tersedia,
           a.is_active,
           a.is_deleted
           FROM stokbarang_r a
           WHERE ((a.is_active = true) AND (a.is_deleted = false))) stokbarang_r ON ((barang_m.barang_id = stokbarang_r.barang_id)))
           JOIN ( SELECT a.ruangan_id,
           a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON ((stokbarang_r.ruangan_id = ruangan_m.ruangan_id)))
           LEFT JOIN ( SELECT a.cssd_id,
           a.ruanganasal_id,
           cssddet_t.qty,
           cssddet_t.barangalkes_id,
           cssddet_t.is_deleted
           FROM (cssd_t a
           LEFT JOIN ( SELECT b.cssd_id,
           b.qty,
           b.barangalkes_id,
           b.is_deleted
           FROM cssddet_t b) cssddet_t ON ((a.cssd_id = cssddet_t.cssd_id)))) cssd_t ON (((ruangan_m.ruangan_id = cssd_t.ruanganasal_id) AND (cssd_t.barangalkes_id = stokbarang_r.barang_id) AND (cssd_t.is_deleted = false))))
           WHERE ((barang_m.is_active = true) AND (barang_m.is_deleted = false))
           GROUP BY barang_m.barang_id, barang_m.barang_kode, barang_m.barang_nama, ruangan_m.ruangan_id, ruangan_m.ruangan_nama, barang_m.satuankecil_id, satuanunit_m.satuanunit_nama, cssd_t.ruanganasal_id, stokbarang_r.qty_tersedia
           UNION ALL
           SELECT 'ALKES'::text AS tipe,
           obatalkes_m.obatalkes_id AS barangalkes_id,
           obatalkes_m.obatalkes_kode AS barangalkes_kode,
           obatalkes_m.obatalkes_nama AS barangalkes_nama,
           ruangan_m.ruangan_id,
           ruangan_m.ruangan_nama,
           obatalkes_m.satuankecil_id,
           satuanunit_m.satuanunit_nama,
           CASE
           WHEN (cssd_t.ruanganasal_id IS NOT NULL) THEN cssd_t.ruanganasal_id
           ELSE ruangan_m.ruangan_id
           END AS ruanganasal_id,
           sum(cssd_t.qty) AS qty_tersedia,
           stokobatalkes_r.qty_tersedia AS qty,
           CASE
           WHEN (cssd_t.ruanganasal_id IS NOT NULL) THEN (stokobatalkes_r.qty_tersedia - (COALESCE(sum(cssd_t.qty), (0)::bigint))::double precision)
           ELSE stokobatalkes_r.qty_tersedia
           END AS stok_tersedia
           FROM (((((obatalkes_m
           JOIN ( SELECT a.satuanunit_id,
           a.satuanunit_nama
           FROM satuanunit_m a) satuanunit_m ON ((obatalkes_m.satuankecil_id = satuanunit_m.satuanunit_id)))
           JOIN ( SELECT a.jenisobatalkes_id,
           a.group_jenisobat
           FROM jenisobatalkes_m a) jenisobatalkes_m ON ((obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id)))
           JOIN ( SELECT a.obatalkes_id,
           a.ruangan_id,
           a.qty_tersedia
           FROM stokobatalkes_r a) stokobatalkes_r ON ((obatalkes_m.obatalkes_id = stokobatalkes_r.obatalkes_id)))
           JOIN ( SELECT a.ruangan_id,
           a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON ((stokobatalkes_r.ruangan_id = ruangan_m.ruangan_id)))
           LEFT JOIN ( SELECT a.cssd_id,
           a.ruanganasal_id,
           cssddet_t.qty,
           cssddet_t.barangalkes_id,
           cssddet_t.is_deleted
           FROM (cssd_t a
           LEFT JOIN ( SELECT b.cssd_id,
           b.qty,
           b.barangalkes_id,
           b.is_deleted
           FROM cssddet_t b) cssddet_t ON ((a.cssd_id = cssddet_t.cssd_id)))) cssd_t ON (((ruangan_m.ruangan_id = cssd_t.ruanganasal_id) AND (cssd_t.barangalkes_id = obatalkes_m.obatalkes_id) AND (cssd_t.is_deleted = false))))
           WHERE ((obatalkes_m.is_active = true) AND (obatalkes_m.is_deleted = false) AND (jenisobatalkes_m.group_jenisobat = '620'::smallint))
           GROUP BY obatalkes_m.obatalkes_id, obatalkes_m.obatalkes_kode, obatalkes_m.obatalkes_nama, ruangan_m.ruangan_id, ruangan_m.ruangan_nama, obatalkes_m.satuankecil_id, satuanunit_m.satuanunit_nama, cssd_t.ruanganasal_id, stokobatalkes_r.qty_tersedia
           ");

        $this->execute('ALTER TABLE public.stokbarangalkes_v OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220624_115839_migrate_cssd_stokbarangalkes_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220624_115839_migrate_cssd_stokbarangalkes_v cannot be reverted.\n";

        return false;
    }
    */
}
