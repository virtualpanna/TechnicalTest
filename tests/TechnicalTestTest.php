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

    private function runCreateTable(): ?array
    {
        $output = [];

        // setup create_table command with parametrs from config
        $command = sprintf(
            'php user_upload.php --create_table -h %s -u %s -p %s',
            escapeshellarg($this->config['DB_HOST']),
            escapeshellarg($this->config['DB_USER']),
            escapeshellarg($this->config['DB_PWD']),
        );

        exec($command, $output);

        return $output;
    }

    private function assertTableCount(int $expectedCount) {
        $query = "SELECT COUNT(*) AS count FROM users";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Assert that the actual count matches the expected count
        $this->assertEquals(
            $expectedCount,
            $result['count'],
        );
    }

    public function testCreateTableDirective()
    {
        $output = $this->runCreateTable();

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

        $this->assertTableCount(0);
    }

    public function testImportDirective()
    {
        $output = [];

        $this->runCreateTable();

        // execute import command
        $command = sprintf(
            'php user_upload.php --file data/users.csv -h %s -u %s -p %s',
            escapeshellarg($this->config['DB_HOST']),
            escapeshellarg($this->config['DB_USER']),
            escapeshellarg($this->config['DB_PWD']),
        );

        exec($command, $output);

        // assert command output is as expected
        $this->assertStringContainsString(
            "Importing CSV data",
            implode("\n", $output)
        );

        // assert first user of CSV is present in DB
        $query = "SELECT COUNT(*) AS count FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['email' => 'jsmith@gmail.com']);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals(1, $result['count']);

        // assert that command imported all expected users
        $this->assertTableCount(9);
    }

    public function testDryRunDirective()
    {
        $output = [];

        $this->runCreateTable();

        // execute import command
        $command = 'php user_upload.php --file data/users.csv --dry_run';

        exec($command, $output);

        // assert command executes successfully
        $this->assertStringContainsString(
            "Parsing CSV data, but no import",
            implode("\n", $output)
        );

        // assert that command did not create any user in table users
        $this->assertTableCount(0);
    }

}
