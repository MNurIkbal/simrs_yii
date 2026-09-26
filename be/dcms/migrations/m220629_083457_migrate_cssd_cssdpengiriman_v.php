<?php

use yii\db\Migration;

/**
 * Class m220629_083457_migrate_cssd_cssdpengiriman_v
 */
class m220629_083457_migrate_cssd_cssdpengiriman_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.cssdpengiriman_v;');
        $this->execute("
            CREATE VIEW \"public\".\"cssdpengiriman_v\" AS
            SELECT cssdpengiriman_t.cssdpengiriman_id,
            cssdpengiriman_t.cssd_id,
            cssdpengiriman_t.tglpengiriman,
            cssd_t.no_pengajuan_sterilisasi,
            cssdsterilisasidet_t.no_sterilisasi,
            cssdpengiriman_t.ruanganasal_id,
            cssdpengiriman_t.ruangantujuan_id,
            instalasi_asal.instalasi_id AS instalasiasal_id,
            instalasi_tujuan.instalasi_id AS instalasitujuan_id,
            ruangan_asal.ruangan_nama AS ruanganasal_nama,
            ruangan_tujuan.ruangan_nama AS ruangantujuan_nama,
            instalasi_asal.instalasi_nama AS instalasiasal_nama,
            instalasi_tujuan.instalasi_nama AS instalasitujuan_nama
            FROM ((((((cssdpengiriman_t
            JOIN ( SELECT a.ruangan_id,
            a.instalasi_id,
            a.ruangan_nama
            FROM ruangan_m a) ruangan_asal ON ((cssdpengiriman_t.ruanganasal_id = ruangan_asal.ruangan_id)))
            JOIN ( SELECT a.ruangan_id,
            a.instalasi_id,
            a.ruangan_nama
            FROM ruangan_m a) ruangan_tujuan ON ((cssdpengiriman_t.ruangantujuan_id = ruangan_tujuan.ruangan_id)))
            JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
            FROM instalasi_m a) instalasi_asal ON ((ruangan_asal.instalasi_id = instalasi_asal.instalasi_id)))
            JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
            FROM instalasi_m a) instalasi_tujuan ON ((ruangan_tujuan.instalasi_id = instalasi_tujuan.instalasi_id)))
            JOIN ( SELECT a.no_pengajuan_sterilisasi,
            a.cssd_id
            FROM cssd_t a) cssd_t ON ((cssd_t.cssd_id = cssdpengiriman_t.cssd_id)))
            JOIN ( SELECT a.cssdsterilisasi_id,
            a.cssd_id,
            cssdsterilisasi_t.no_sterilisasi
            FROM (cssdsterilisasidet_t a
            JOIN ( SELECT b.cssdsterilisasi_id,
            b.no_sterilisasi
            FROM cssdsterilisasi_t b
            WHERE ((b.is_deleted = false) AND (b.is_active = true))) cssdsterilisasi_t ON ((a.cssdsterilisasi_id = cssdsterilisasi_t.cssdsterilisasi_id)))) cssdsterilisasidet_t ON ((cssdsterilisasidet_t.cssd_id = cssdpengiriman_t.cssd_id)))
            WHERE ((cssdpengiriman_t.is_deleted = false) AND (cssdpengiriman_t.is_active = true))
            ;");
        $this->execute('
            ALTER TABLE public.cssdpengiriman_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220629_083457_migrate_cssd_cssdpengiriman_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220629_083457_migrate_cssd_cssdpengiriman_v cannot be reverted.\n";

        return false;
    }
    */
}
