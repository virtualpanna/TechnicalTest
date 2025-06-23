<?php


use PHPUnit\Framework\TestCase;

class TechnicalTestTest extends TestCase
{
    protected $pdo;

    protected array $config = [];

    protected function setUp(): void
    {
        $this->config = require 'config.php';

        // create DB connection using config file
        $dsn = "pgsql:host=" . $this->config['DB_HOST'] . ";dbname=" . $this->config['DB_NAME'];
        $this->pdo = new PDO(
            $dsn,
            $this->config['DB_USER'],
            $this->config['DB_PWD']
        );
    }

    protected function tearDown(): void
    {
        // Close DB connection
        $this->pdo = null;
    }

    public function testNoDirectives()
    {
        $output = [];
        exec('php user_upload.php', $output);

        $this->assertStringContainsString(
            "parameter --file must be provided",
            implode("\n", $output)
        );
    }

    public function testHelpDirective()
    {
        $output = [];
        exec('php user_upload.php --help', $output);

        $this->assertStringContainsString(
            "user_upload usage help:",
            implode("\n", $output)
        );
    }

    public function testCreateTableDirective()
    {
        // get command parameters from config file
        $command = sprintf(
            'php user_upload.php --create_table -h %s -u %s -p %s',
            escapeshellarg($this->config['DB_HOST']),
            escapeshellarg($this->config['DB_USER']),
            escapeshellarg($this->config['DB_PWD']),
        );

        exec($command, $output);

        // assert command executes successfully
        $this->assertStringContainsString(
            "Creation of table `users` completed",
            implode("\n", $output)
        );

        $tableName = 'users';
        $query = "SELECT to_regclass(:tableName) IS NOT NULL AS exists";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['tableName' => $tableName]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // assert table exists
        $this->assertTrue(
            $result['exists'],
            "Table '$tableName' does not exist."
        );

        $query = "SELECT COUNT(*) AS count FROM $tableName";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // assert table is empty
        $this->assertEquals(
            0,
            $result['count'],
            "Table '$tableName' is not empty."
        );
    }

}
