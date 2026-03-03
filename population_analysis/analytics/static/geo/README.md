# GeoJSON Peta Wilayah

Agar peta dashboard tampil per batas wilayah (polygon), tambahkan file berikut:

- Nama file: `jatim_kabkota.geojson`
- Lokasi: `analytics/static/geo/jatim_kabkota.geojson`

## Syarat properti nama wilayah
Pastikan setiap fitur polygon memiliki salah satu properti ini agar bisa dipasangkan dengan data dashboard:

- `wilayah`
- `WADMKK`
- `NAME_2`
- `nama`

## Contoh struktur minimal
```json
{
  "type": "FeatureCollection",
  "features": [
    {
      "type": "Feature",
      "properties": {
        "WADMKK": "Pacitan"
      },
      "geometry": {
        "type": "Polygon",
        "coordinates": [[[111.0,-8.5],[111.2,-8.5],[111.2,-8.3],[111.0,-8.3],[111.0,-8.5]]]
      }
    }
  ]
}
```

Jika file ini belum ada, dashboard tetap menampilkan fallback marker titik.
