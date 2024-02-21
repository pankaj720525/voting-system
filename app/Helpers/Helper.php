<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class Helper {
    /**
     * Encrypted ID
     *
     * @param  mixed $id
     * @return void
     */
    public static function getEncryptedSecret($id)
    {
        $encrypted_string = openssl_encrypt($id, config('services.encryption.type'), config('services.encryption.secret'), 0, config('services.encryption.encryption_iv'));
        return base64_encode($encrypted_string);
    }

    /**
     * Decrypt Encrypted ID
     *
     * @param  mixed $id
     * @return void
     */
    public static function getDecryptedId($secret)
    {
        try {
            return openssl_decrypt(base64_decode($secret), config('services.encryption.type'), config('services.encryption.secret'), 0, config('services.encryption.encryption_iv'));
        } catch (\Throwable $th) {
            Log::error($th->getTraceAsString());
            return false;
        }
    }
}
