<?php
namespace Helpers;
class Decrypt
{
  public function DecryptKey(string $key): string
  {
    $parts = explode("::", base64_decode($key), 2);

    if (count($parts) !== 2) {
      throw new Exception("Invalid encrypted data format");
    }

    [$encryptedData, $iv] = $parts;

    $cipherMethod = "AES-256-CBC";
    $encryptionKey = "usman";

    $decrypted = openssl_decrypt(
      $encryptedData,
      $cipherMethod,
      $encryptionKey,
      0,
      $iv
    );

    if ($decrypted === false) {
      throw new Exception("Decryption failed");
    }

    return $decrypted;
  }
}
