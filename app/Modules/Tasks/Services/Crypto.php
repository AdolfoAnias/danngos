<?php

namespace App\Modules\Tasks\Services;

class Crypto
{
    private string $passphrase;
    private bool $app_crypto;
    private string $encryptMethod;

    /**
     * Constructor de la clase.
     * Inicializa la passphrase y el método de cifrado.
     */
    public function __construct()
    {
        $this->passphrase = env('PASSPHRASE_ENCRYPT', '');
        $this->app_crypto = filter_var(env('APP_CRYPTO', true), FILTER_VALIDATE_BOOLEAN);
        $this->encryptMethod = 'AES-256-CBC';
    }

    /**
     * Encripta un valor utilizando AES-256-CBC y PBKDF2 para derivar la clave.
     *
     * @param string $value El valor a cifrar.
     * @return string El valor cifrado en formato base64.
     */
    public function cryptoJsAesEncrypt(string $value): string
    {
        // Si la encriptación está desactivada, retorna el valor JSON decodificado como un array asociativo
        if ($this->app_crypto == false) {
            return json_decode($value, true);
        }

        // Obtener la longitud del IV para el método de cifrado especificado.
        $ivLength = openssl_cipher_iv_length($this->encryptMethod);
        $iv = openssl_random_pseudo_bytes($ivLength);

        // Generar una sal aleatoria y derivar la clave de cifrado usando PBKDF2.
        $salt = openssl_random_pseudo_bytes(8);
        $iterations = 999;
        $hashKey = hash_pbkdf2('sha512', $this->passphrase, $salt, $iterations, (self::encryptMethodLength() / 4));

        // Cifrar el valor y codificar el resultado en base64.
        $encryptedString = openssl_encrypt($value, $this->encryptMethod, hex2bin($hashKey), OPENSSL_RAW_DATA, $iv);
        $encryptedString = base64_encode($encryptedString);

        // Preparar los datos para devolver.
        $output = [
            'ciphertext' => $encryptedString,
            'iv' => bin2hex($iv),
            'salt' => bin2hex($salt),
            'iterations' => $iterations,
        ];

        // Limpiar variables temporales.
        unset($hashKey);

        return base64_encode(json_encode($output));
    }

    /**
     * Desencripta un valor cifrado en formato base64 utilizando AES-256-CBC.
     *
     * @param string $encryptedString El valor cifrado en formato base64.
     * @return string|null El valor descifrado, o null si ocurre un error.
     */
    public function cryptoJsAesDecrypt(string $encryptedString): ?string
    {
        // Decodificar el valor cifrado desde base64 y convertirlo en un array.
        $json = json_decode(base64_decode($encryptedString), true);

        // Verificar que los datos del JSON contienen las claves necesarias.
        if (!is_array($json) ||
            !array_key_exists('salt', $json) ||
            !array_key_exists('iv', $json) ||
            !array_key_exists('ciphertext', $json) ||
            !array_key_exists('iterations', $json)
        ) {
            return null;
        }

        try {
            $salt = hex2bin($json['salt']);
            $iv = hex2bin($json['iv']);
        } catch (\Exception $e) {
            return null;
        }

        // Decodificar el texto cifrado y preparar los parámetros para el desciframiento.
        $cipherText = base64_decode($json['ciphertext']);
        $iterations = intval($json['iterations']);
        $iterations = $iterations > 0 ? $iterations : 999;
        $hashKey = hash_pbkdf2('sha512', $this->passphrase, $salt, $iterations, (self::encryptMethodLength() / 4));

        // Desencriptar el texto cifrado.
        $decrypted = openssl_decrypt($cipherText, $this->encryptMethod, hex2bin($hashKey), OPENSSL_RAW_DATA, $iv);
        if (!is_string($decrypted)) {
            return null;
        }

        // Limpiar variables temporales.
        unset($cipherText, $hashKey, $iv);

        return $decrypted;
    }

    /**
     * Obtiene la longitud del método de cifrado especificado.
     *
     * @return int La longitud del método de cifrado en bits.
     */
    protected function encryptMethodLength(): int
    {
        // Extraer el número de la cadena del método de cifrado.
        $number = (int) filter_var($this->encryptMethod, FILTER_SANITIZE_NUMBER_INT);
        return max($number, 0);
    }
}
