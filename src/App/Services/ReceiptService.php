<?php

namespace App\Services;

use App\Config\Paths;
use Framework\Database;
use Framework\Exceptions\ValidationException;

class ReceiptService
{
    public function __construct(private Database $db) {}

    public function validateReceiptFile(?array $file)
    {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            throw new ValidationException([
                'receipt' => ['Failed to Upload file.']
            ]);
        }

        $maxFileSizeMB = 3 * 1024 * 1024;

        if ($file['size'] > $maxFileSizeMB) {
            throw new ValidationException(['receipt' => ['File should be less than 3MB in size.']]);
        }

        $originalFileName = $file['name'];

        if (!preg_match('/^[A-za-z0-9\s._-]+$/', $originalFileName)) {
            throw new ValidationException(['receipt' => ['Invalid Filename.']]);
        }

        $clientMimeType = $file['type'];
        $allowedMimeTypes = ['image/jpg', 'image/png', 'application/pdf'];

        if (!in_array($clientMimeType, $allowedMimeTypes)) {
            throw new ValidationException(['receipt' => ['Invalid file type.']]);
        }
    }

    public function upload(?array $file, int $transaction)
    {
        $fileExtention = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFileName = bin2hex(random_bytes(12)) . '.' . $fileExtention;
        $storagePath = Paths::STORAGE_UPLOADS . '/' . $newFileName;
        if (!move_uploaded_file($file['tmp_name'], $storagePath)) {
            throw new ValidationException(['receipt' => ['Failed to upload file.']]);
        }

        $this->db->query(
            "INSERT INTO receipts(original_filename, storage_filename,media_type,transaction_id)
            VALUES (:original_filename, :storage_filename, :media_type, :transaction_id)",
            [
                'original_filename' => $file['name'],
                'storage_filename' => $newFileName,
                'media_type' => $file['type'],
                'transaction_id' => $transaction
            ]
        );
    }

    public function findReceipt(string $receipt_id)
    {
        $receipt = $this->db->query(
            "SELECT * FROM receipts WHERE receipt_id = :receipt_id",
            [
                'receipt_id' => $receipt_id
            ]
        )->get();

        return $receipt;
    }

    public function read(array $receipt)
    {
        $filePath = Paths::STORAGE_UPLOADS . '/' . $receipt['storage_filename'];

        if (!file_exists($filePath)) {
            redirectTo('/');
        }

        /* To show use inline or attachment to download file in browser.  */
        header("Content-Disposition: inline;filename={$receipt['original_filename']}");
        header("Content-Type: {$receipt['media_type']}");

        readfile($filePath);
    }

    public function delete(array $receipt)
    {
        $filePath = Paths::STORAGE_UPLOADS . '/' . $receipt['storage_filename'];

        unlink($filePath);

        $this->db->query(
            "DELETE FROM receipts WHERE receipt_id = :receipt_id",
            [
                'receipt_id' => $receipt['receipt_id']
            ]
        )->get();
    }
}
