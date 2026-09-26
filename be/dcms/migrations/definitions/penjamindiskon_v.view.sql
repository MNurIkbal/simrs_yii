-- public.penjamindiskon_v source

CREATE OR REPLACE VIEW public.penjamindiskon_v
AS SELECT pd.penjamindiskon_id,
    pd.penjamin_id,
    c.carabayar_id,
    c.carabayar_nama,
    p.penjamin_kode,
    p.penjamin_nama,
    pd.diskon_otomatis,
    pd.is_active,
    pd.is_deleted
   FROM penjamindiskon_m pd
     JOIN penjamin_m p ON p.penjamin_id = pd.penjamin_id
     JOIN carabayar_m c ON c.carabayar_id = p.carabayar_id
  WHERE pd.is_deleted = false;