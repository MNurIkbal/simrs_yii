<?php

use yii\db\Migration;

/**
 * Class m220921_092044_view_gateway_pasien
 */
class m220921_092044_view_gateway_pasien extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."gt_pasien_v";
        ');

        $this->execute("
        CREATE OR REPLACE VIEW \"public\".\"gt_pasien_v\"
        AS SELECT pasien_m.pasien_id,
            pasien_m.no_identitas_pasien,
            NULL::text AS no_bpjs,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            pasien_m.tempat_lahir,
            pasien_m.tanggal_lahir,
            pasien_m.nama_ibu,
            pasien_m.nama_ayah,
            pasien_m.alamatemail AS alamat_email,
            pasien_m.no_mobile_pasien AS no_handphone,
            pasien_m.no_telepon_pasien,
            COALESCE(pasien_m.alamat_sekarang, pasien_m.alamat_pasien) AS alamat_pasien,
            pasien_m.rt,
            pasien_m.rw,
            jenis_kelamin.lookup_name AS jenis_kelamin,
            golongan_darah.lookup_name AS golongan_darah,
            status_kawin.lookup_name AS status_perkawinan,
            agama.lookup_name AS agama,
            warga_negara.lookup_name AS warga_negara,
            propinsi_m.propinsi,
            kabupaten_m.kabupaten,
            kecamatan_m.kecamatan,
            kelurahan_m.kelurahan,
            pekerjaan_m.pekerjaan,
            pendidikan_m.pendidikan,
            suku_m.suku
        FROM pasien_m
            LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                FROM lookup_m a) jenis_kelamin ON pasien_m.jeniskelamin::integer = jenis_kelamin.lookup_id
            LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                FROM lookup_m a) golongan_darah ON pasien_m.golongandarah::integer = golongan_darah.lookup_id
            LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                FROM lookup_m a) status_kawin ON pasien_m.statusperkawinan::integer = status_kawin.lookup_id
            LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                FROM lookup_m a) agama ON pasien_m.agama::integer = agama.lookup_id
            LEFT JOIN ( SELECT a.lookup_id,
                    a.lookup_name
                FROM lookup_m a) warga_negara ON pasien_m.agama::integer = warga_negara.lookup_id
            LEFT JOIN ( SELECT a.propinsi_id,
                    a.propinsi_nama AS propinsi
                FROM propinsi_m a) propinsi_m ON pasien_m.propinsi_id = propinsi_m.propinsi_id
            LEFT JOIN ( SELECT a.kabupaten_id,
                    a.kabupaten_nama AS kabupaten
                FROM kabupaten_m a) kabupaten_m ON pasien_m.kabupaten_id = kabupaten_m.kabupaten_id
            LEFT JOIN ( SELECT a.kecamatan_id,
                    a.kecamatan_nama AS kecamatan
                FROM kecamatan_m a) kecamatan_m ON pasien_m.kecamatan_id = kecamatan_m.kecamatan_id
            LEFT JOIN ( SELECT a.kelurahan_id,
                    a.kelurahan_nama AS kelurahan
                FROM kelurahan_m a) kelurahan_m ON pasien_m.kelurahan_id = kelurahan_m.kelurahan_id
            LEFT JOIN ( SELECT a.pekerjaan_id,
                    a.pekerjaan_nama AS pekerjaan
                FROM pekerjaan_m a) pekerjaan_m ON pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id
            LEFT JOIN ( SELECT a.pendidikan_id,
                    a.pendidikan_nama AS pendidikan
                FROM pendidikan_m a) pendidikan_m ON pasien_m.pendidikan_id = pendidikan_m.pendidikan_id
            LEFT JOIN ( SELECT a.suku_id,
                    a.suku_nama AS suku
                FROM suku_m a) suku_m ON pasien_m.suku_id = suku_m.suku_id
        WHERE pasien_m.is_deleted = false AND pasien_m.is_active = true;
        
                ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220921_092044_view_gateway_pasien cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220921_092044_view_gateway_pasien cannot be reverted.\n";

        return false;
    }
    */
}
