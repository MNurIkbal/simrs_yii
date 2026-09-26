<?php

use yii\db\Migration;

/**
 * Class m200923_023448_migrate_20200923_infopasienmcu
 */
class m200923_023448_migrate_20200923_infopasienmcu extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infopasienmcu_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopasienmcu_v\" AS  SELECT 'APS'::text AS tipe_pasien,
    pendaftaran_t.pendaftaran_id,
    NULL::text AS pasienmasukpenunjang_id,
    NULL::text AS pasienkirimkeunitlain_id,
    pendaftaran_t.tgl_pendaftaran AS tglmasukpenunjang,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran AS no_masukpenunjang,
    pasien_m.no_rekam_medik,
    fgetnamalookup((pasien_m.namadepan)::integer) AS nama_depan,
    pasien_m.nama_pasien,
    pasien_m.alamat_pasien,
    pendaftaran_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_penunjang,
    NULL::character varying AS no_rujukan,
    pendaftaran_t.instalasi_id AS asalrujukan_id,
    'APS'::character varying AS asalrujukan_nama,
    pendaftaran_t.ruangan_id AS ruanganasal_id,
    ruangan_m.ruangan_nama,
    pendaftaran_t.status_periksa,
    fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa_nama,
    antrian_t.no_antrian,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.umur,
    pasien_m.jeniskelamin,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
    pasien_m.tanggal_lahir,
    ((pendaftaran_t.label_gelang)::json ->> 'resiko_jatuh'::text) AS kuning,
    ((pendaftaran_t.label_gelang)::json ->> 'alergi'::text) AS merah,
    ((pendaftaran_t.label_gelang)::json ->> 'dnr'::text) AS ungu,
    ((pendaftaran_t.label_gelang)::json ->> 'duplikat'::text) AS coklat,
    pendaftaran_t.tgl_pendaftaran AS tgl_rujukan,
    pendaftaran_t.pasien_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.ruangan_id,
    NULL::text AS is_bayar,
    NULL::text AS status_penunjang,
    NULL::text AS tanggal_verifikasi,
    pendaftaran_t.instalasi_id,
    pasien_m.tempat_lahir,
    pasien_m.alamat_sekarang,
    fgetnamalookup((pasien_m.warga_negara)::integer) AS kebangsaan,
    pendaftaran_t.tgl_pendaftaran AS tgl_pemeriksaan,
    jenis_paket.jenis_paket AS tipepaket_nama,
    pendaftaran_t.keterangan_pendaftaran,
    jeniskasuspenyakit_m.jeniskasuspenyakit_id
   FROM ((((((((((pendaftaran_t
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN antrian_t ON ((pendaftaran_t.antrian_id = antrian_t.antrian_id)))
     LEFT JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN ( SELECT tindakanpelayanan_t.pendaftaran_id,
            array_agg(tipepaket_m.tipepaket_nama) AS jenis_paket
           FROM (tindakanpelayanan_t
             JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
          WHERE (tindakanpelayanan_t.is_deleted = false)
          GROUP BY tindakanpelayanan_t.pendaftaran_id) jenis_paket ON ((pendaftaran_t.pendaftaran_id = jenis_paket.pendaftaran_id)))
  WHERE (pendaftaran_t.instalasi_id = 21);");
        
        $this->execute('ALTER TABLE "public"."infopasienmcu_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200923_023448_migrate_20200923_infopasienmcu cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200923_023448_migrate_20200923_infopasienmcu cannot be reverted.\n";

        return false;
    }
    */
}
