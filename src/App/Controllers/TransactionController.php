<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\TransactionService;
use App\Services\ValidatorService;
use Framework\TemplateEngine;

class TransactionController
{
    public function __construct(private TemplateEngine $view, private ValidatorService $validatorService, private TransactionService $transactionService) {}
    public function create(): void
    {
        echo $this->view->render('transactions/create.php');
    }

    public function store()
    {
        $data = $_POST;
        $this->validatorService->validateTransaction($data);
        $this->transactionService->create($data);
        redirectTo('/');
    }

    public function edit(array $transaction)
    {
        $transaction = $this->transactionService->findUserTransaction($transaction['transaction']);

        if (!$transaction)
            redirectTo('/');

        echo $this->view->render('transactions/edit.php', compact('transaction'));
    }

    public function update(array $transaction)
    {
        // dd($transaction);
        $transaction = $this->transactionService->findUserTransaction($transaction['transaction']);

        if (!$transaction)
            redirectTo('/');

        $this->validatorService->validateTransaction($_POST);
        $this->transactionService->updateUserTransaction($_POST, $transaction['id']);

        redirectTo($_SERVER['HTTP_REFERER']);
    }

    public function delete(array $transaction)
    {
        $this->transactionService->deleteUserTransaction($transaction['transaction']);
        redirectTo('/');
    }
}
