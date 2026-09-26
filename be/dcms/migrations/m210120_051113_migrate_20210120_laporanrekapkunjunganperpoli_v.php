<?php

use yii\db\Migration;

/**
 * Class m210120_051113_migrate_20210120_laporanrekapkunjunganperpoli_v
 */
class m210120_051113_migrate_20210120_laporanrekapkunjunganperpoli_v extends Migration
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
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.pegawai_id
            ELSE pasienadmisi_t.pegawai_id
        END AS pegawai_id,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pegawai_m.nama_pegawai
            ELSE peg_admisi.nama_pegawai
        END AS nama_dokter,
    pendaftaran_t.status_periksa,
    pendaftaran_t.status_bayar,
    count(
        CASE
            WHEN ((pendaftaran_t.pasien_id IS NOT NULL) AND (pendaftaran_t.is_deleted IS FALSE)) THEN ''::text
            ELSE NULL::text
        END) AS jumlah_pasien
   FROM ((((((pendaftaran_t
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
     LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN pegawai_m peg_admisi ON ((pasienadmisi_t.pegawai_id = peg_admisi.pegawai_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
  WHERE ((pendaftaran_t.is_active = true) AND (pendaftaran_t.is_deleted = false))
  GROUP BY (date(pendaftaran_t.tgl_pendaftaran)), ruangan_m.ruangan_id, ruangan_m.ruangan_nama, instalasi_m.instalasi_id, instalasi_m.instalasi_nama, pegawai_m.pegawai_id, pegawai_m.nama_pegawai, pendaftaran_t.status_periksa, pendaftaran_t.status_bayar, pendaftaran_t.pasienadmisi_id, pendaftaran_t.pegawai_id, pasienadmisi_t.pegawai_id, peg_admisi.nama_pegawai
  ORDER BY (date(pendaftaran_t.tgl_pendaftaran)) DESC
            ;");
            $this->execute('ALTER TABLE public.laporanrekapkunjunganperpoli_v
    OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210120_051113_migrate_20210120_laporanrekapkunjunganperpoli_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210120_051113_migrate_20210120_laporanrekapkunjunganperpoli_v cannot be reverted.\n";

        return false;
    }
    */
}
