<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\ReceiptService;
use App\Services\TransactionService;
use App\Services\ValidatorService;
use Framework\TemplateEngine;

class ReceiptController
{
    public function __construct(
        private TemplateEngine $view,
        private ValidatorService $validatorService,
        private TransactionService $transactionService,
        private ReceiptService $receiptService
    ) {}
    public function create(array $transaction): void
    {
        $transaction = $this->transactionService->findUserTransaction($transaction['transaction']);

        if (!$transaction)
            redirectTo('/');

        echo $this->view->render('receipt/create.php');
    }

    public function store(array $transaction)
    {
        $transaction = $this->transactionService->findUserTransaction($transaction['transaction']);

        if (!$transaction)
            redirectTo('/');

        /* Code here */
        $file = $_FILES['receipt'] ?? null;
        $this->receiptService->validateReceiptFile($file);
        $this->receiptService->upload($file, $transaction['id']);

        redirectTo('/');
    }

    public function download(array $params)
    {
        $transaction = $this->transactionService->findUserTransaction($params['transaction']);

        if (!$transaction)
            redirectTo('/');

        $receipt = $this->receiptService->findReceipt($params['receipt']);

        if (!$receipt)
            redirectTo('/');

        if ($transaction['id'] !== $receipt['transaction_id'])
            redirectTo('/');

        $this->receiptService->read($receipt);
    }

    public function delete(array $params)
    {
        $transaction = $this->transactionService->findUserTransaction($params['transaction']);

        if (!$transaction)
            redirectTo('/');

        $receipt = $this->receiptService->findReceipt($params['receipt']);

        if (!$receipt)
            redirectTo('/');

        if ($transaction['id'] !== $receipt['transaction_id'])
            redirectTo('/');
        $this->receiptService->delete($receipt);
        redirectTo('/');
    }
}
