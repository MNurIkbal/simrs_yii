<?php

use yii\db\Migration;

/**
 * Class m201026_093030_migrate_mhkn_20201026_laporanrekapkunjunganperpoli_v
 */
class m201026_093030_migrate_mhkn_20201026_laporanrekapkunjunganperpoli_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporanrekapkunjunganperpoli_v;');
        $this->execute("CREATE VIEW \"public\".\"laporanrekapkunjunganperpoli_v\" AS
             SELECT date(pendaftaran_t.tgl_pendaftaran) AS tgl_pendaftaran,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    pegawai_m.pegawai_id,
    pegawai_m.nama_pegawai AS nama_dokter,
    pendaftaran_t.status_periksa,
    pendaftaran_t.status_bayar,
    count(
        CASE
            WHEN ((pendaftaran_t.pasien_id IS NOT NULL) AND (pendaftaran_t.is_deleted IS FALSE)) THEN ''::text
            ELSE NULL::text
        END) AS jumlah_pasien
   FROM ((((pendaftaran_t
     JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
  WHERE ((pendaftaran_t.is_active = true) AND (pendaftaran_t.is_deleted = false))
  GROUP BY (date(pendaftaran_t.tgl_pendaftaran)), ruangan_m.ruangan_id, ruangan_m.ruangan_nama, instalasi_m.instalasi_id, instalasi_m.instalasi_nama, pegawai_m.pegawai_id, pegawai_m.nama_pegawai, pendaftaran_t.status_periksa, pendaftaran_t.status_bayar
  ORDER BY (date(pendaftaran_t.tgl_pendaftaran)) DESC;");

        $this->execute('ALTER TABLE public.laporanrekapkunjunganperpoli_v
    OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201026_093030_migrate_mhkn_20201026_laporanrekapkunjunganperpoli_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201026_093030_migrate_mhkn_20201026_laporanrekapkunjunganperpoli_v cannot be reverted.\n";

        return false;
    }
    */
}
