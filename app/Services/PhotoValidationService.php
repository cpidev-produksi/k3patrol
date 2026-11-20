<?php

namespace App\Services;

use App\Models\FindingPhoto;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class PhotoValidationService
{
    /**
     * Validasi foto: belum pernah diupload (hash). Foto boleh dari kamera maupun galeri,
     * tanpa batasan tanggal pengambilan.
     * Melempar ValidationException jika foto duplikat.
     *
     * taken_at diisi dari EXIF bila terbaca; jika tidak (galeri, screenshot, foto terkompres),
     * diisi waktu upload.
     *
     * @return array{hash: string, taken_at: Carbon}
     */
    public function validateOrFail(UploadedFile $file, ?int $findingId = null): array
    {
        $takenAt = $this->readExifDate($file) ?? now();

        $hash = hash_file('sha256', $file->getRealPath());

        if (FindingPhoto::where('file_hash', $hash)->exists()) {
            throw ValidationException::withMessages([
                'photo' => 'Foto ini sudah pernah diupload sebelumnya.',
            ]);
        }

        return ['hash' => $hash, 'taken_at' => $takenAt];
    }

    /**
     * Baca EXIF DateTimeOriginal dari file gambar. Mengembalikan null jika
     * tidak tersedia (mis. foto sudah dikompres ulang / bukan JPEG).
     */
    protected function readExifDate(UploadedFile $file): ?Carbon
    {
        $path = $file->getRealPath();

        if (!function_exists('exif_read_data')) {
            return null;
        }

        $mime = $file->getMimeType();
        if (!in_array($mime, ['image/jpeg', 'image/tiff'], true)) {
            return null; // format lain umumnya tidak menyimpan EXIF
        }

        $exif = @exif_read_data($path, 'EXIF', true);

        $raw = $exif['EXIF']['DateTimeOriginal']
            ?? $exif['IFD0']['DateTime']
            ?? null;

        if (!$raw) {
            return null;
        }

        try {
            // format EXIF: "YYYY:MM:DD HH:MM:SS"
            return Carbon::createFromFormat('Y:m:d H:i:s', $raw);
        } catch (\Exception) {
            return null;
        }
    }
}