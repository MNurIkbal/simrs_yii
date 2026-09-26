<?php

use yii\db\Migration;

/**
 * Class m220630_070835_migrate_cssd_prosessterilisasi_v
 */
class m220630_070835_migrate_cssd_prosessterilisasi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.prosessterilisasi_v;');
        $this->execute("
            CREATE VIEW \"public\".\"prosessterilisasi_v\" AS
            SELECT cssdsterilisasi_t.cssdsterilisasi_id,
            cssd_t.cssd_id,
            cssd_t.ruanganasal_id,
            ruanganasal.ruangan_nama AS ruanganasal_nama,
            instalasiasal.instalasi_id AS instalasiasal_id,
            instalasiasal.instalasi_nama AS instalasiasal_nama,
            cssd_t.ruangantujuan_id,
            ruangantujuan.ruangan_nama AS ruangantujuan_nama,
            instalasitujuan.instalasi_id AS instalasitujuan_id,
            instalasitujuan.instalasi_nama AS instalasitujuan_nama,
            cssd_t.tgl_pengajuan_sterilisasi,
            cssdsterilisasi_t.tgl_sterilisasi,
            cssdsterilisasi_t.no_sterilisasi,
            cssd_t.no_pengajuan_sterilisasi,
            cssdsterilisasi_t.status_cssd AS status_cssd_id,
            cssdsterilisasi_t.peg_sterilisasi_id,
            peg_sterilisasi.nama_pegawai AS peg_sterilisasi_nama,
            peg_sterilisasi.nomorindukpegawai AS peg_sterilisasi_nip,
            statuscssd.lookup_name AS status_cssd_nama
            FROM (((((((cssd_t
            JOIN ( SELECT a.cssdsterilisasi_id,
            a.no_sterilisasi,
            a.status_cssd,
            a.peg_sterilisasi_id,
            a.tgl_sterilisasi
            FROM cssdsterilisasi_t a) cssdsterilisasi_t ON ((cssd_t.cssdsterilisasi_id = cssdsterilisasi_t.cssdsterilisasi_id)))
            JOIN ( SELECT a.ruangan_id,
            a.instalasi_id,
            a.ruangan_nama
            FROM ruangan_m a) ruanganasal ON ((cssd_t.ruanganasal_id = ruanganasal.ruangan_id)))
            JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
            FROM instalasi_m a) instalasiasal ON ((ruanganasal.instalasi_id = instalasiasal.instalasi_id)))
            JOIN ( SELECT a.ruangan_id,
            a.instalasi_id,
            a.ruangan_nama
            FROM ruangan_m a) ruangantujuan ON ((cssd_t.ruangantujuan_id = ruangantujuan.ruangan_id)))
            JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
            FROM instalasi_m a) instalasitujuan ON ((ruangantujuan.instalasi_id = instalasitujuan.instalasi_id)))
            JOIN ( SELECT a.lookup_id,
            a.lookup_name
            FROM lookup_m a) statuscssd ON ((cssdsterilisasi_t.status_cssd = statuscssd.lookup_id)))
            JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai,
            a.nomorindukpegawai
            FROM pegawai_m a) peg_sterilisasi ON ((peg_sterilisasi.pegawai_id = cssdsterilisasi_t.peg_sterilisasi_id)))
            WHERE ((cssd_t.is_active = true) AND (cssd_t.is_deleted = false))
            ;");
        $this->execute('
            ALTER TABLE public.prosessterilisasi_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220630_070835_migrate_cssd_prosessterilisasi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220630_070835_migrate_cssd_prosessterilisasi_v cannot be reverted.\n";

        return false;
    }
    */
}
