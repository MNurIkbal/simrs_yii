<?php

use yii\db\Migration;

/**
 * Class m210107_131647_improve_add_column_pegawai_m
 */
class m210107_131647_improve_add_column_pegawai_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE pegawai_m ADD IF NOT EXISTS tanda_tangan TEXT;
        ');

        $this->execute('
            DROP VIEW IF EXISTS "public"."kelahiranbayi_v";
        ');

        $this->execute('
            CREATE VIEW "public"."kelahiranbayi_v" AS  SELECT kelahiranbayi_t.kelahiranbayi_id,
                kelahiranbayi_t.pendaftaran_id,
                kelahiranbayi_t.pasienadmisi_id,
                kelahiranbayi_t.pendaftaranbaru_id,
                pendaftaran_t.no_pendaftaran,
                    CASE
                        WHEN (kelahiranbayi_t.pendaftaranbaru_id IS NULL) THEN \'BELUM TERDAFTAR\'::text
                        ELSE \'SUDAH TERDAFTAR\'::text
                    END AS status_pendaftaran,
                kelahiranbayi_t.bayi_urut,
                kelahiranbayi_t.berat_badan,
                kelahiranbayi_t.tinggi_badan,
                fgetnamalookup((kelahiranbayi_t.jenis_kelamin)::integer) AS jenis_kelamin,
                fgetnamalookupkeperawatan((kelahiranbayi_t.penilaian)::integer) AS penilaian,
                fgetnamalookupkeperawatan((kelahiranbayi_t.kondisi_bayi)::integer) AS kondisi_bayi,
                fgetnamalookupkeperawatan((kelahiranbayi_t.asfiksia)::integer) AS normal_tindakan,
                kelahiranbayi_t.is_asi,
                kelahiranbayi_t.keterangan_asi,
                kelahiranbayi_t.masalah_lain,
                kelahiranbayi_t.hasil,
                kelahiranbayi_t.no_peneng,
                kelahiranbayi_t.warna_kulit,
                fgetnamalookup((pasien_m.namadepan)::integer) AS nama_depan,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                pasien_m.nama_ibu,
                pasien_m.nama_ayah, 
                pasien_m.alamat_pasien,
                pasien_m.tempat_lahir,
                kelahiranbayi_t.tgl_lahir AS tanggal_lahir,
                fgetnamalookup((pasien_m.warga_negara)::integer) AS warga_negara,
                pendaftaran_t.umur,
                pendaftaran_t.tgl_pendaftaran,
                pasien_m.no_telepon_pasien,
                suku_m.suku_nama AS nama_suku,
                fgetnamalookup((pasien_m.agama)::integer) AS agama,
                fgetnamalookup((pasien_m.golongandarah)::integer) AS golongandarah,
                fgetnamalookup((pegawai_m.gelardepan)::integer) AS gelar_depan_dokter,
                pegawai_m.pegawai_id AS dokter_dpjp_id,
                pegawai_m.nama_pegawai AS dokter_dpjp,
                \'\'::character varying AS nama_alamat_pengirim,
                \'\'::character varying AS diet,
                \'\'::character varying AS alergi,
                persalinan_t.tgl_persalinan
               FROM ((((((kelahiranbayi_t
                 LEFT JOIN pendaftaran_t ON ((kelahiranbayi_t.pendaftaranbaru_id = pendaftaran_t.pendaftaran_id)))
                 LEFT JOIN pasienadmisi_t ON ((kelahiranbayi_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                 LEFT JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                 LEFT JOIN pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
                 LEFT JOIN suku_m ON ((pasien_m.suku_id = suku_m.suku_id)))
                 LEFT JOIN persalinan_t ON ((kelahiranbayi_t.pendaftaran_id = persalinan_t.pendaftaran_id)))
              WHERE (kelahiranbayi_t.is_deleted = false);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210107_131647_improve_add_column_pegawai_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210107_131647_improve_add_column_pegawai_m cannot be reverted.\n";

        return false;
    }
    */
}
