<?php
namespace Helpers;
class Encrypt
{
  public function EncryptKey(string $key): string
  {
    $cipherMethod = "AES-256-CBC";

    $ivLength = openssl_cipher_iv_length($cipherMethod);
    $iv = openssl_random_pseudo_bytes($ivLength);

    $encryptionKey = "usman";

    $encryptedData = openssl_encrypt(
      $key,
      $cipherMethod,
      $encryptionKey,
      0,
      $iv
    );

    if ($encryptedData === false) {
      throw new Exception("API key encryption failed");
    }

    $encryptedDataWithIv = base64_encode($encryptedData . "::" . $iv);
    return $encryptedDataWithIv;
  }
}
