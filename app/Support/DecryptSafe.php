<?php

namespace App\Support;

use Illuminate\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class DecryptSafe
{
    /**
     * Safely decrypt a Laravel-encrypted string.
     *
     * - Never throws DecryptException.
     * - Logs failures (once per call).
     *
     * @param  mixed  $value  Raw encrypted string from DB (unencrypted casts must not be accessed).
     * @param  string  $context  For logging (field name, etc.).
     * @return string|null
     */
    public static function decrypt(mixed $value, string $context = ''): ?string
    {
        if ($value === null) {
            return null;
        }

        // If DB contains empty string, treat as null.
        if (is_string($value) && trim($value) === '') {
            return null;
        }

        try {
            $decrypted = Crypt::decryptString((string) $value);
            return is_string($decrypted) ? $decrypted : (string) $decrypted;
        } catch (DecryptException $e) {
            // Corrupted value / wrong APP_KEY / old encrypted format.
            logger()->warning('decrypt_safe: failed to decrypt encrypted attribute', [
                'context' => $context,
                'aed_id' => null, // view can pass it via context if desired
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'ciphertext_preview' => is_string($value) ? Str::limit($value, 32) : null,
            ]);

            return null;
        } catch (\Throwable $e) {
            logger()->warning('decrypt_safe: unexpected failure', [
                'context' => $context,
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'ciphertext_preview' => is_string($value) ? Str::limit($value, 32) : null,
            ]);

            return null;
        }
    }
}

