<?php

use yii\db\Migration;

/**
 * Class m220624_121824_migrate_cssd_lapcssdsterilisasi_v
 */
class m220624_121824_migrate_cssd_lapcssdsterilisasi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.lapcssdsterilisasi_v;');
        $this->execute("
            CREATE VIEW \"public\".\"lapcssdsterilisasi_v\" AS
            SELECT cssd_t.tgl_pengajuan_sterilisasi AS tgl_pengajuan,
            cssdsterilisasi_t.tgl_sterilisasi,
            cssdpenerimaanunit_t.tglterima AS tgl_penerimaan,
            cssd_t.no_pengajuan_sterilisasi AS no_pengajuan,
            cssdsterilisasi_t.no_sterilisasi,
            cssd_t.ruanganasal_id,
            cssd_t.ruangantujuan_id,
            instalasi_asal.instalasi_id AS instalasiasal_id,
            instalasi_tujuan.instalasi_id AS instalasitujuan_id,
            ruangan_asal.ruangan_nama AS ruanganasal_nama,
            ruangan_tujuan.ruangan_nama AS ruangantujuan_nama,
            instalasi_asal.instalasi_nama AS instalasiasal_nama,
            instalasi_tujuan.instalasi_nama AS instalasitujuan_nama,
            cssd_t.status_cssd AS status_cssd_id,
            look_status_cssd.status_cssd_nama
            FROM (((((((cssd_t
            LEFT JOIN ( SELECT a.cssdsterilisasi_id,
            a.tgl_sterilisasi,
            a.no_sterilisasi
            FROM cssdsterilisasi_t a) cssdsterilisasi_t ON ((cssd_t.cssdsterilisasi_id = cssdsterilisasi_t.cssdsterilisasi_id)))
            LEFT JOIN ( SELECT a.cssdpenerimaanunit_id,
            a.cssd_id,
            a.tglterima
            FROM cssdpenerimaanunit_t a) cssdpenerimaanunit_t ON ((cssd_t.cssd_id = cssdpenerimaanunit_t.cssd_id)))
            JOIN ( SELECT a.ruangan_id,
            a.instalasi_id,
            a.ruangan_nama
            FROM ruangan_m a) ruangan_asal ON ((cssd_t.ruanganasal_id = ruangan_asal.ruangan_id)))
            JOIN ( SELECT a.ruangan_id,
            a.instalasi_id,
            a.ruangan_nama
            FROM ruangan_m a) ruangan_tujuan ON ((cssd_t.ruangantujuan_id = ruangan_tujuan.ruangan_id)))
            JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
            FROM instalasi_m a) instalasi_asal ON ((ruangan_asal.instalasi_id = instalasi_asal.instalasi_id)))
            JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
            FROM instalasi_m a) instalasi_tujuan ON ((ruangan_tujuan.instalasi_id = instalasi_tujuan.instalasi_id)))
            LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name AS status_cssd_nama
            FROM lookup_m a) look_status_cssd ON ((cssd_t.status_cssd = look_status_cssd.lookup_id)))
            ;");
        $this->execute('
            ALTER TABLE public.lapcssdsterilisasi_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220624_121824_migrate_cssd_lapcssdsterilisasi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220624_121824_migrate_cssd_lapcssdsterilisasi_v cannot be reverted.\n";

        return false;
    }
    */
}
