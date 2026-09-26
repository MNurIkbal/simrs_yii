<?php

use yii\db\Migration;

/**
 * Class m231117_025429_migrate_glbj346_view_infodataresep_v
 */
class m231117_025429_migrate_glbj346_view_infodataresep_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infodataresep_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infodataresep_v" AS  SELECT resep.jenis,
    resep.penjualanresep_id,
    resep.no_reseptur,
    resep.no_resep,
    resep.status_reseptur_id,
    resep.status_reseptur, 
    resep.ruangan_id,
    resep.instalasi_reseptur_id
   FROM ( SELECT \'reseptur\'::text AS jenis,
            penjualanresep_t.penjualanresep_id,
            reseptur_t.noresep AS no_reseptur,
            penjualanresep_t.noresep AS no_resep,
            reseptur_t.status_reseptur AS status_reseptur_id,
                CASE
                    WHEN penjualanresep_t.penjualanresep_id IS NULL THEN \'Belum Proses\'::character varying
                    WHEN penjualanresep_t.status_reseptur = 347 THEN \'Dalam Proses\'::character varying
                    ELSE status_reseptur.lookup_name
                END AS status_reseptur,
            reseptur_t.ruangan_id,
            ruangan_reseptur.instalasi_id AS instalasi_reseptur_id
           FROM reseptur_t
             LEFT JOIN ( SELECT penjualanresep.penjualanresep_id,
                    penjualanresep.tglresep,
                    penjualanresep.noresep,
                    penjualanresep.status_reseptur
                   FROM penjualanresep_t penjualanresep
                  WHERE penjualanresep.is_deleted = false) penjualanresep_t ON reseptur_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
             JOIN ( SELECT ruangan_2.ruangan_id,
                    ruangan_2.ruangan_nama,
                    ruangan_2.instalasi_id
                   FROM ruangan_m ruangan_2) ruangan_reseptur ON reseptur_t.ruanganreseptur_id = ruangan_reseptur.ruangan_id
             JOIN ( SELECT instalasi_1.instalasi_id,
                    instalasi_1.instalasi_nama
                   FROM instalasi_m instalasi_1) instalasi_reseptur ON ruangan_reseptur.instalasi_id = instalasi_reseptur.instalasi_id
             LEFT JOIN ( SELECT lookup_m.lookup_id,
                    lookup_m.lookup_name
                   FROM lookup_m) status_reseptur ON penjualanresep_t.status_reseptur::integer = status_reseptur.lookup_id
          WHERE reseptur_t.is_deleted = false AND reseptur_t.is_active = true AND reseptur_t.penjualanresep_id IS NULL
        UNION ALL
         SELECT \'resep\'::text AS jenis,
            penjualanresep_t.penjualanresep_id,
            reseptur_t.noresep AS no_reseptur,
            penjualanresep_t.noresep AS no_resep,
            penjualanresep_t.status_reseptur AS status_reseptur_id,
                CASE
                    WHEN penjualanresep_t.status_reseptur = 347 THEN \'Dalam Proses\'::character varying
                    ELSE status_reseptur.lookup_name
                END AS status_reseptur,
            penjualanresep_t.ruangan_id,
            COALESCE(rm.instalasi_id, ruangan_resep.instalasi_id) AS instalasi_reseptur_id
           FROM ( SELECT a.penjualanresep_id,
                    a.status_reseptur,
                    a.tglresep,
                    a.noresep,
                    a.ruangan_id,
                    a.reseptur_id
                   FROM penjualanresep_t a) penjualanresep_t
             LEFT JOIN ( SELECT a.reseptur_id,
                    a.tglreseptur,
                    a.noresep,
                    a.ruanganreseptur_id,
                    a.catatan,
                    a.antrian_id,
                    a.kategori_resep
                   FROM reseptur_t a) reseptur_t ON reseptur_t.reseptur_id = penjualanresep_t.reseptur_id
             LEFT JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama,
                    a.instalasi_id
                   FROM ruangan_m a) rm ON rm.ruangan_id = reseptur_t.ruanganreseptur_id
             JOIN ( SELECT ruangan.ruangan_id,
                    ruangan.ruangan_nama,
                    ruangan.instalasi_id
                   FROM ruangan_m ruangan) ruangan_resep ON penjualanresep_t.ruangan_id = ruangan_resep.ruangan_id
             JOIN ( SELECT instalasi.instalasi_id,
                    instalasi.instalasi_nama
                   FROM instalasi_m instalasi) instalasi_resep ON ruangan_resep.instalasi_id = instalasi_resep.instalasi_id
             LEFT JOIN ( SELECT lookup_m.lookup_id,
                    lookup_m.lookup_name
                   FROM lookup_m) status_reseptur ON penjualanresep_t.status_reseptur::integer = status_reseptur.lookup_id) resep;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231117_025429_migrate_glbj346_view_infodataresep_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231117_025429_migrate_glbj346_view_infodataresep_v cannot be reverted.\n";

        return false;
    }
    */
}
