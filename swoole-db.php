public function getUserTransactions(string $search, int $limit, int $offset): array
{
$mysql = new \Swoole\Coroutine\MySQL();
$mysql->connect([
'host' => 'localhost',
'user' => 'root',
'password' => 'password',
'database' => 'your_db',
]);

$escapedSearch = $mysql->escape($search);
$transactions = $mysql->query("SELECT * FROM transactions WHERE name LIKE '%{$escapedSearch}%' LIMIT {$limit} OFFSET {$offset}");

$countResult = $mysql->query("SELECT COUNT(*) AS total FROM transactions WHERE name LIKE '%{$escapedSearch}%'");
$count = $countResult[0]['total'] ?? 0;

return [$transactions, $count];
}