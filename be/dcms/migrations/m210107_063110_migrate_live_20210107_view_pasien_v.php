<?php

use yii\db\Migration;

/**
 * Class m210107_063110_migrate_live_20210107_view_pasien_v
 */
class m210107_063110_migrate_live_20210107_view_pasien_v extends Migration
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
        CASE
            WHEN (pasien_m.nopeserta_bpjs IS NULL) THEN ('-'::text)::character varying
            WHEN (carabayar.carabayar_id = 6) THEN pasien_m.nopeserta_bpjs
            ELSE pasien_m.nopeserta_bpjs
        END AS nopeserta_bpjs,
    pasien_m.pendidikan_id,
    pendidikan_m.pendidikan_nama,
    COALESCE(piutang.total_sisapiutang, (0)::double precision) AS total_sisapiutang,
    pasien_m.additional_pasien,
    pasien_m.catatanpenting_pasien,
    '-'::text AS alergi,
        CASE
            WHEN (penanggungjawab.penanggungjawab_nama IS NULL) THEN ('-'::text)::character varying
            ELSE penanggungjawab.penanggungjawab_nama
        END AS penanggungjawab_nama,
        CASE
            WHEN (penanggungjawab.hubungankeluarga IS NULL) THEN '55'::character varying
            ELSE penanggungjawab.hubungankeluarga
        END AS hubungankeluarga,
        CASE
            WHEN (penanggungjawab.penanggungjawab_alamat IS NULL) THEN '-'::text
            ELSE penanggungjawab.penanggungjawab_alamat
        END AS penanggungjawab_alamat,
        CASE
            WHEN (penanggungjawab.penanggungjawab_notelp IS NULL) THEN ('-'::text)::character varying
            ELSE penanggungjawab.penanggungjawab_notelp
        END AS penanggungjawab_notelp,
        CASE
            WHEN (carabayar.carabayar_nama IS NULL) THEN ('-'::text)::character varying
            ELSE carabayar.carabayar_nama
        END AS carabayar_nama,
        CASE
            WHEN (penjamin.penjamin_nama IS NULL) THEN ('-'::text)::character varying
            ELSE penjamin.penjamin_nama
        END AS penjamin_nama,
        CASE
            WHEN (pasien_m.nopeserta_bpjs IS NULL) THEN ('-'::text)::character varying
            ELSE pasien_m.nopeserta_bpjs
        END AS nokartuasuransi,
        CASE
            WHEN (kelurahan_m.kode_pos IS NULL) THEN ('-'::text)::character varying
            ELSE kelurahan_m.kode_pos
        END AS kode_pos,
    pasien_m.last_modified_date AS tgl_update_terakhir,
    petugas_pemakai.nama_pegawai AS petugas_nama,
    pasien_m.created_date AS tgl_pembuatan,
    petugas_pembuat.nama_pegawai AS pembuat_nama
   FROM (((((((((((((pasien_m
     LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
     LEFT JOIN suku_m suku ON ((pasien_m.suku_id = suku.suku_id)))
     LEFT JOIN dokrekammedis_m ON ((pasien_m.pasien_id = dokrekammedis_m.pasien_id)))
     LEFT JOIN pendidikan_m ON ((pasien_m.pendidikan_id = pendidikan_m.pendidikan_id)))
     LEFT JOIN loginpemakai_k petugas_1 ON ((pasien_m.last_modified_by = petugas_1.loginpemakai_id)))
     LEFT JOIN pegawai_m petugas_pemakai ON ((petugas_1.pegawai_id = petugas_pemakai.pegawai_id)))
     LEFT JOIN loginpemakai_k pembuat ON ((pasien_m.created_by = pembuat.loginpemakai_id)))
     LEFT JOIN pegawai_m petugas_pembuat ON ((pembuat.pegawai_id = petugas_pembuat.pegawai_id)))
     LEFT JOIN ( SELECT pendaftaran_t.pasien_id,
            sum(pemberianpiutang_t.total_sisapiutang) AS total_sisapiutang
           FROM (pemberianpiutang_t
             JOIN pendaftaran_t ON ((pemberianpiutang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
          GROUP BY pendaftaran_t.pasien_id) piutang ON ((pasien_m.pasien_id = piutang.pasien_id)))
     LEFT JOIN ( SELECT pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.pasien_id
           FROM ((pendaftaran_t
             JOIN ( SELECT max(pendaftaran_t_1.pendaftaran_id) AS max_pendaftaran_id,
                    pendaftaran_t_1.pasien_id
                   FROM pendaftaran_t pendaftaran_t_1
                  GROUP BY pendaftaran_t_1.pasien_id) max_pendaftaran ON (((pendaftaran_t.pasien_id = max_pendaftaran.pasien_id) AND (pendaftaran_t.pendaftaran_id = max_pendaftaran.max_pendaftaran_id))))
             LEFT JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))) carabayar ON ((pasien_m.pasien_id = carabayar.pasien_id)))
     LEFT JOIN ( SELECT pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            pendaftaran_t.pasien_id
           FROM ((pendaftaran_t
             JOIN ( SELECT max(pendaftaran_t_1.pendaftaran_id) AS max_pendaftaran_id,
                    pendaftaran_t_1.pasien_id
                   FROM pendaftaran_t pendaftaran_t_1
                  GROUP BY pendaftaran_t_1.pasien_id) max_pendaftaran ON (((pendaftaran_t.pasien_id = max_pendaftaran.pasien_id) AND (pendaftaran_t.pendaftaran_id = max_pendaftaran.max_pendaftaran_id))))
             LEFT JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))) penjamin ON ((pasien_m.pasien_id = penjamin.pasien_id)))
     LEFT JOIN ( SELECT penanggungjawab_m.pasien_id,
            penanggungjawab_m.penanggungjawab_nama,
            penanggungjawab_m.hubungankeluarga,
            penanggungjawab_m.penanggungjawab_alamat,
            penanggungjawab_m.penanggungjawab_notelp,
            penanggungjawab_m.penanggungjawab_id
           FROM (penanggungjawab_m
             JOIN ( SELECT penanggungjawab_m_1.pasien_id,
                    max(penanggungjawab_m_1.penanggungjawab_id) AS pjmax_id
                   FROM penanggungjawab_m penanggungjawab_m_1
                  WHERE (penanggungjawab_m_1.is_deleted = false)
                  GROUP BY penanggungjawab_m_1.pasien_id) max_pj ON (((penanggungjawab_m.pasien_id = max_pj.pasien_id) AND (penanggungjawab_m.penanggungjawab_id = max_pj.pjmax_id))))) penanggungjawab ON ((pasien_m.pasien_id = penanggungjawab.pasien_id)))
     LEFT JOIN kelurahan_m ON ((pasien_m.kelurahan_id = kelurahan_m.kelurahan_id)))
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
        echo "m210107_063110_migrate_live_20210107_view_pasien_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210107_063110_migrate_live_20210107_view_pasien_v cannot be reverted.\n";

        return false;
    }
    */
}
