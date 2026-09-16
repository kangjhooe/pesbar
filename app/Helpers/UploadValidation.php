<?php

namespace App\Helpers;

/**
 * Aturan validasi unggahan yang seragam.
 * Pakai rule `image` + `mimes` agar ekstensi dan isi file dicek (getimagesize / MIME).
 */
class UploadValidation
{
    /**
     * Gambar konten (artikel, avatar, event, about, media penulis).
     *
     * @return array<int, string>
     */
    public static function image(bool $required = false, int $maxKb = 2048): array
    {
        return [
            $required ? 'required' : 'nullable',
            'image',
            'mimes:jpeg,jpg,png,gif,webp',
            'max:'.$maxKb,
        ];
    }

    /**
     * Logo situs — tanpa SVG (risiko XSS).
     *
     * @return array<int, string>
     */
    public static function logo(): array
    {
        return [
            'nullable',
            'image',
            'mimes:jpeg,jpg,png,webp',
            'max:2048',
        ];
    }

    /**
     * Favicon — ico diizinkan; tanpa SVG.
     *
     * @return array<int, string>
     */
    public static function favicon(): array
    {
        return [
            'nullable',
            'file',
            'mimes:jpeg,jpg,png,ico,webp',
            'max:512',
        ];
    }

    /**
     * Dokumen verifikasi / upgrade (PDF atau gambar).
     *
     * @return array<int, string>
     */
    public static function verificationDocument(bool $required = true, int $maxKb = 5120): array
    {
        return [
            $required ? 'required' : 'nullable',
            'file',
            'mimes:pdf,jpg,jpeg,png',
            'mimetypes:application/pdf,image/jpeg,image/png',
            'max:'.$maxKb,
        ];
    }

    /**
     * Media library admin (gambar, dokumen, audio, video).
     *
     * @return array<int, string>
     */
    public static function adminMedia(): array
    {
        return [
            'required',
            'file',
            'max:10240',
            'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,mp4,avi,mov,wmv,mp3,wav,ogg',
        ];
    }
}
