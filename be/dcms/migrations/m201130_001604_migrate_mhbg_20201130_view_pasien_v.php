<?php

use yii\db\Migration;

/**
 * Class m201130_001604_migrate_mhbg_20201130_view_pasien_v
 */
class m201130_001604_migrate_mhbg_20201130_view_pasien_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.pasien_v;');
        $this->execute("CREATE VIEW \"public\".\"pasien_v\" AS
             SELECT pasien_m.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.tgl_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    pasien_m.jenisidentitas,
    fgetnamalookup((pasien_m.jenisidentitas)::integer) AS identitas,
    pasien_m.no_identitas_pasien,
    pasien_m.namadepan,
    fgetnamalookup((pasien_m.namadepan)::integer) AS nama_depan,
    pasien_m.nama_bin,
    pasien_m.tempat_lahir,
    pasien_m.jeniskelamin,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    pasien_m.statusperkawinan,
    fgetnamalookup((pasien_m.statusperkawinan)::integer) AS status_perkawinan,
    pasien_m.nama_ibu,
    pasien_m.alamat_sekarang,
    pasien_m.alamat_pasien,
    pasien_m.rt,
    pasien_m.rw,
    pasien_m.propinsi_id,
    fgetnamaarea(pasien_m.propinsi_id, NULL::integer, NULL::integer, NULL::integer) AS propinsi_nama,
    pasien_m.kabupaten_id,
    fgetnamaarea(NULL::integer, pasien_m.kabupaten_id, NULL::integer, NULL::integer) AS kabupaten_nama,
    pasien_m.kecamatan_id,
    fgetnamaarea(NULL::integer, NULL::integer, pasien_m.kecamatan_id, NULL::integer) AS kecamatan_nama,
    pasien_m.kelurahan_id,
    fgetnamaarea(NULL::integer, NULL::integer, NULL::integer, pasien_m.kelurahan_id) AS kelurahan_nama,
    pasien_m.no_mobile_pasien,
    pasien_m.no_telepon_pasien,
    pasien_m.pekerjaan_id,
    pekerjaan_m.pekerjaan_nama,
    pasien_m.warga_negara,
    fgetnamalookup((pasien_m.warga_negara)::integer) AS warganegara,
    pasien_m.agama,
    fgetnamalookup((pasien_m.agama)::integer) AS agama_pasien,
    pasien_m.alamatemail,
    pasien_m.suku_id,
    suku.suku_nama,
    pasien_m.nama_ayah,
    pasien_m.anakke,
    pasien_m.jumlah_bersaudara,
    pasien_m.golongandarah,
    fgetnamalookup((pasien_m.golongandarah)::integer) AS golongan_darah,
    pasien_m.photopasien,
    pasien_m.is_aps,
    dokrekammedis_m.dokrekammedis_id,
    pasien_m.nopeserta_bpjs,
    pasien_m.pendidikan_id,
    pendidikan_m.pendidikan_nama,
    COALESCE(piutang.total_sisapiutang, (0)::double precision) AS total_sisapiutang,
    pasien_m.additional_pasien,
    pasien_m.catatanpenting_pasien,
    NULL::text AS alergi,
    penanggungjawab_m.penanggungjawab_nama,
    penanggungjawab_m.hubungankeluarga,
    penanggungjawab_m.penanggungjawab_alamat,
    penanggungjawab_m.penanggungjawab_notelp,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    asuransipasien_m.nokartuasuransi
   FROM (((((((((pasien_m
     LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
     LEFT JOIN suku_m suku ON ((pasien_m.suku_id = suku.suku_id)))
     LEFT JOIN dokrekammedis_m ON ((pasien_m.pasien_id = dokrekammedis_m.pasien_id)))
     LEFT JOIN pendidikan_m ON ((pasien_m.pendidikan_id = pendidikan_m.pendidikan_id)))
     LEFT JOIN ( SELECT pendaftaran_t.pasien_id,
            pendaftaran_t.carabayar_id,
            pendaftaran_t.penjamin_id,
            sum(pemberianpiutang_t.total_sisapiutang) AS total_sisapiutang
           FROM (pemberianpiutang_t
             JOIN pendaftaran_t ON ((pemberianpiutang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
          GROUP BY pendaftaran_t.pasien_id, pendaftaran_t.carabayar_id, pendaftaran_t.penjamin_id) piutang ON ((pasien_m.pasien_id = piutang.pasien_id)))
     LEFT JOIN penanggungjawab_m ON ((pasien_m.pasien_id = penanggungjawab_m.pasien_id)))
     LEFT JOIN carabayar_m ON ((piutang.carabayar_id = carabayar_m.carabayar_id)))
     LEFT JOIN penjamin_m ON ((piutang.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN asuransipasien_m ON ((pasien_m.pasien_id = asuransipasien_m.pasien_id)))
  WHERE ((pasien_m.is_active = true) AND (pasien_m.is_deleted = false))
            ;");
            $this->execute('ALTER TABLE public.pasien_v
    OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201130_001604_migrate_mhbg_20201130_view_pasien_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201130_001604_migrate_mhbg_20201130_view_pasien_v cannot be reverted.\n";

        return false;
    }
    */
}
