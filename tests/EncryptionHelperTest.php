<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
require "./../src/EncryptionHelper.php";

final class EncryptionHelperTest extends TestCase
{
    // Testing the openssl encryption
    // ------------------------------
    public function testOpenSSL(): void
    {
        $test = FL\EncryptionHelper::getInstance("my topsecret information",FL\EncryptionHelper::OPENSSL);
        $this->assertEquals($test->getEncryptionMethod(), "openssl");
        $this->assertEquals($test, "my topsecret information");
        $this->assertNotEquals($test->setPassword("verysecretpassword")->encrypt(), "my topsecret information");
        $this->assertEquals($test->getLastAction(), "encrypt");
        $this->assertEquals($test->decrypt(), "my topsecret information");
        $this->assertEquals($test->getLastAction(), "decrypt");
    }

    // Testing the simple encryption
    // -----------------------------
    public function testSimple(): void
    {
        $test = FL\EncryptionHelper::getInstance("my topsecret information 2",FL\EncryptionHelper::SIMPLE);
        $this->assertEquals($test->getEncryptionMethod(), "simple");
        $this->assertEquals($test, "my topsecret information 2");
        $this->assertNotEquals($test->setPassword("verysecretpassword2")->encrypt(), "my topsecret information 2");
        $this->assertEquals($test->getLastAction(), "encrypt");
        $this->assertEquals($test->decrypt(), "my topsecret information 2");
        $this->assertEquals($test->getLastAction(), "decrypt");
    }
}