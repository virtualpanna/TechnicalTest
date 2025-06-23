<?php

use PHPUnit\Framework\TestCase;

class TechnicalTestTest extends TestCase
{
    public function testHelpDirective()
    {
        $output = [];
        exec('php user_upload.php --help', $output, $returnVar);

        $this->assertStringContainsString(
            "user_upload usage help:", 
            implode("\n", $output)
        );
    }
    
}
