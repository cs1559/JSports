<?php
/**
 * JSports - Joomla Sports Management Component
 */

namespace FP4P\Component\JSports\Site\Services;

\defined('_JEXEC') or die;

use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;

/**
 * Handles validation + image processing for team logo uploads.
 */
class LogoUploadService
{
    /**
     * @param array $uploadFile The upload array (e.g. $file['uploadfile'])
     * @param string $destDir Absolute destination directory (must exist or be creatable)
     * @param int $size Output square size (e.g. 175)
     * @param string $destBaseName Base filename without extension (e.g. 'logo' or 'team_123')
     * @return array{success: bool, filename?: string, error?: string}
     */
    public function processAndSaveSquarePng(array $uploadFile, string $destDir, int $size = 175, string $destBaseName = 'logo'): array
    {
        // Basic upload checks
        if (!isset($uploadFile['tmp_name'], $uploadFile['name'])) {
            return ['success' => false, 'error' => 'Invalid upload payload.'];
        }

        if (!empty($uploadFile['error'])) {
            return ['success' => false, 'error' => 'Upload failed (PHP error code ' . (int) $uploadFile['error'] . ').'];
        }

        if (!is_uploaded_file($uploadFile['tmp_name'])) {
            return ['success' => false, 'error' => 'File was not uploaded via HTTP POST.'];
        }

        // Ensure destination directory
        if (!Folder::exists($destDir)) {
            if (!Folder::create($destDir)) {
                return ['success' => false, 'error' => 'Unable to create destination directory.'];
            }
        }

        // Stronger validation: confirm image + get width/height/mime
        $info = @getimagesize($uploadFile['tmp_name']);
        if ($info === false) {
            return ['success' => false, 'error' => 'Uploaded file is not a valid image.'];
        }

        [$srcW, $srcH] = $info;
        $mime = $info['mime'] ?? '';

        // Allow only common image mimes
        $allowedMime = ['image/jpeg', 'image/pjpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($mime, $allowedMime, true)) {
            return ['success' => false, 'error' => 'Unsupported image type: ' . $mime];
        }

        // Load source image resource
        $src = $this->createImageResource($uploadFile['tmp_name'], $mime);
        if (!$src) {
            return ['success' => false, 'error' => 'Unable to read image data.'];
        }

        // Create square canvas (PNG w/ alpha)
        $dst = imagecreatetruecolor($size, $size);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
        imagefilledrectangle($dst, 0, 0, $size, $size, $transparent);

        // Center-crop: scale so the smaller dimension fills the square, then crop center
        $scale = max($size / $srcW, $size / $srcH);
        $scaledW = (int) ceil($srcW * $scale);
        $scaledH = (int) ceil($srcH * $scale);

        $tmp = imagecreatetruecolor($scaledW, $scaledH);
        imagealphablending($tmp, false);
        imagesavealpha($tmp, true);
        $transparent2 = imagecolorallocatealpha($tmp, 0, 0, 0, 127);
        imagefilledrectangle($tmp, 0, 0, $scaledW, $scaledH, $transparent2);

        imagecopyresampled($tmp, $src, 0, 0, 0, 0, $scaledW, $scaledH, $srcW, $srcH);

        // Crop center to size x size
        $cropX = (int) floor(($scaledW - $size) / 2);
        $cropY = (int) floor(($scaledH - $size) / 2);

        imagecopy($dst, $tmp, 0, 0, $cropX, $cropY, $size, $size);

        // Write final PNG
        $safeBase = File::makeSafe($destBaseName);
        if ($safeBase === '') {
            $safeBase = 'logo';
        }

        $finalName = $safeBase . '.png';
        $finalPath = rtrim($destDir, '/\\') . '/' . $finalName;

        $ok = imagepng($dst, $finalPath, 6);

        // Cleanup
        imagedestroy($dst);
        imagedestroy($tmp);
        imagedestroy($src);

        if (!$ok) {
            return ['success' => false, 'error' => 'Failed to write image file.'];
        }

        return ['success' => true, 'filename' => $finalName];
    }

    /**
     * @return \GdImage|resource|false
     */
    private function createImageResource(string $path, string $mime)
    {
        return match ($mime) {
            'image/jpeg', 'image/pjpeg' => @imagecreatefromjpeg($path),
            'image/png' => @imagecreatefrompng($path),
            'image/gif' => @imagecreatefromgif($path),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default => false,
        };
    }
}
