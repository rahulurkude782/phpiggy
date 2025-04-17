<?php

namespace App\Services;

use Framework\Database;


class TransactionService
{
    public function __construct(private Database $db) {}

    public function getUserTransactions(string $searchTerm, int $pageSize, int $offset)
    {
        $params = [
            'user_id' => $_SESSION['user'],
            'description' => "%{$searchTerm}%"
        ];

        $transactions =  $this->db->query(
            "SELECT transactions.*,
            DATE_FORMAT(transactions.date, '%Y-%m-%d') AS formatted_date,
            receipts.*
            FROM transactions 
            LEFT JOIN receipts ON receipts.transaction_id = transactions.id
            WHERE transactions.user_id = :user_id 
            AND description LIKE :description
            LIMIT {$pageSize} OFFSET {$offset}",
            $params
        )->all();

        $transactionsCount =  $this->db->query(
            "SELECT COUNT(*) 
            FROM transactions 
            WHERE user_id = :user_id
            AND description LIKE :description",
            $params
        )->count();

        return [$transactions, $transactionsCount];
    }

    public function create(array $formData)
    {
        $formattedDate = "{$formData['date']} 00:00:00";
        $this->db->query(
            'INSERT INTO transactions(user_id, description, amount, date)
            VALUES(:user_id, :description, :amount, :date)',
            [
                'user_id' => $_SESSION['user'],
                'description' => $formData['description'],
                'amount' => $formData['amount'],
                'date' => $formattedDate,
            ]
        );
    }

    public function findUserTransaction(string $id)
    {
        return $this->db->query(
            "SELECT *, DATE_FORMAT(date, '%Y-%m-%d') as date FROM transactions WHERE id=:id AND user_id=:user_id",
            [
                'id' => $id,
                'user_id' => $_SESSION['user']
            ]
        )->get();
    }

    public function updateUserTransaction(array $formData, int $id)
    {
        return $this->db->query(
            "UPDATE transactions
            SET description = :description,
            amount = :amount,
            date = :date 
            WHERE id = :id 
            AND user_id = :user_id",
            [
                'id' => $id,
                'description' => $formData['description'],
                'amount' => $formData['amount'],
                'date' => $formData['date'],
                'user_id' => $_SESSION['user']
            ]
        )->get();
    }

    public function deleteUserTransaction(string $id)
    {
        return $this->db->query(
            "DELETE FROM transactions WHERE id = :id AND user_id = :user_id",
            [
                'id' => $id,
                'user_id' => $_SESSION['user']
            ]
        )->get();
    }
}
