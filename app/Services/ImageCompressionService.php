<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageCompressionService
{
    /**
     * Kualitas kompresi JPEG (0–100). Default 65 sudah cukup untuk bukti bayar.
     */
    protected int $quality;

    /**
     * Lebar maksimal gambar setelah resize (px). Null = tidak resize.
     */
    protected ?int $maxWidth;

    public function __construct(int $quality = 65, ?int $maxWidth = 1200)
    {
        $this->quality  = $quality;
        $this->maxWidth = $maxWidth;
    }

    /**
     * Simpan file original dan buat versi kompres-nya.
     *
     * @return array{original: string, compressed: string}
     */
    public function store(UploadedFile $file, string $folder = 'bukti-pembayaran'): array
    {
        // Simpan original
        $ext          = strtolower($file->getClientOriginalExtension());
        $filename     = Str::uuid() . '.' . $ext;
        $originalPath = $file->storeAs($folder . '/original', $filename, 'public');

        // Kompres dan simpan
        $compressedPath = $this->compress(
            Storage::disk('public')->path($originalPath),
            $folder . '/compressed',
            $filename
        );

        return [
            'original'   => $originalPath,
            'compressed' => $compressedPath,
        ];
    }

    /**
     * Kompres gambar menggunakan GD library bawaan PHP.
     * Mendukung JPEG, PNG, WEBP.
     */
    protected function compress(string $sourcePath, string $destFolder, string $filename): string
    {
        $info = @getimagesize($sourcePath);

        if (! $info) {
            // Bukan gambar valid, kembalikan path original
            return str_replace('/original/', '/compressed/', $sourcePath);
        }

        $mime   = $info['mime'];
        $image  = $this->createImageFromMime($sourcePath, $mime);

        if (! $image) {
            return str_replace('/original/', '/compressed/', $sourcePath);
        }

        // Resize jika lebar melebihi maxWidth
        if ($this->maxWidth) {
            $image = $this->resizeIfNeeded($image);
        }

        // Pastikan folder tujuan ada
        $destDiskFolder = Storage::disk('public')->path($destFolder);
        if (! is_dir($destDiskFolder)) {
            mkdir($destDiskFolder, 0755, true);
        }

        $destPath    = $destDiskFolder . DIRECTORY_SEPARATOR . $filename;
        $storagePath = $destFolder . '/' . $filename;

        // Selalu simpan sebagai JPEG untuk konsistensi
        $jpgFilename = pathinfo($filename, PATHINFO_FILENAME) . '.jpg';
        $destPath    = $destDiskFolder . DIRECTORY_SEPARATOR . $jpgFilename;
        $storagePath = $destFolder . '/' . $jpgFilename;

        // Latar belakang putih untuk PNG transparan
        if ($mime === 'image/png') {
            $width  = imagesx($image);
            $height = imagesy($image);
            $bg     = imagecreatetruecolor($width, $height);
            $white  = imagecolorallocate($bg, 255, 255, 255);
            imagefill($bg, 0, 0, $white);
            imagecopy($bg, $image, 0, 0, 0, 0, $width, $height);
            imagedestroy($image);
            $image = $bg;
        }

        imagejpeg($image, $destPath, $this->quality);
        imagedestroy($image);

        return $storagePath;
    }

    private function createImageFromMime(string $path, string $mime)
    {
        return match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($path),
            'image/png'  => @imagecreatefrompng($path),
            'image/webp' => @imagecreatefromwebp($path),
            default      => false,
        };
    }

    private function resizeIfNeeded($image)
    {
        $origWidth  = imagesx($image);
        $origHeight = imagesy($image);

        if ($origWidth <= $this->maxWidth) {
            return $image;
        }

        $ratio     = $this->maxWidth / $origWidth;
        $newWidth  = $this->maxWidth;
        $newHeight = (int) ($origHeight * $ratio);

        $resized = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
        imagedestroy($image);

        return $resized;
    }
}
