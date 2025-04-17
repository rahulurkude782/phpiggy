<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\TransactionService;
use Framework\TemplateEngine;

class HomeController
{
    public function __construct(private TemplateEngine $view, private TransactionService $transactionService) {}
    public function index(): void
    {
        $searchTerm = addcslashes($_GET['s'] ?? '', '%_');
        $page = (int) ($_GET['p'] ?? 1);
        $pageSize = 3;
        $offset = ($page - 1) * $pageSize;

        [$transactions, $transactionsCount] = $this->transactionService->getUserTransactions($searchTerm, $pageSize, $offset);

        $lastPage = ceil($transactionsCount / $pageSize);

        $pages = $lastPage ? range(1, $lastPage) : [];

        $pageLinks = array_map(
            fn($pageNum) => http_build_query([
                'p' => $pageNum,
                's' => $searchTerm
            ]),
            $pages
        );

        echo $this->view->render('index.php', [
            'transactions' => $transactions,
            'currentPage' => $page,
            'previousPageQuery' => http_build_query([
                'p' => $page - 1,
                's' => $searchTerm,
            ]),
            'lastPage' => $lastPage,
            'nextPageQuery' => http_build_query([
                'p' => $page + 1,
                's' => $searchTerm,
            ]),
            'pageLinks' => $pageLinks,
            'searchTerm' => $searchTerm
        ]);
    }
}
