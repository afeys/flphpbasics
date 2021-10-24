<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
require "./../src/NumberHelper.php";

final class NumberHelperTest extends TestCase
{
    // Testing the GetInstance variations
    // ----------------------------------
    public function testGetInstance(): void
    {
        $test = FL\NumberHelper::getInstance(10.5);
        $this->assertEquals($test->getValue(), 10.5);
        $test = FL\NumberHelper::getInstance("11.12");
        $this->assertEquals($test, "11.12");
    }

    // Testing the upper and lower limit functions
    // -------------------------------------------
    public function testLimits(): void
    {
        $test = FL\NumberHelper::getInstance(120);
        $this->assertEquals($test->getValue(), 120);

        $test->setLowerLimit(50);
        $this->assertEquals($test->getLowerLimit(), 50);

        $test->setUpperLimit(100);
        $this->assertEquals($test->getUpperLimit(), 100);

        $this->assertEquals($test->getValue(), 100); // 100 because the value of 120 is above the upperlimit
    
        $test = FL\NumberHelper::getInstance(120)->setLowerLimit(140);
        $this->assertEquals($test->getValue(), 140); // 100 because the value of 120 is below the lowerlimit

    }

    // Testing the isOdd isEven functions
    // -------------------------------------------
    public function testOddEven(): void
    {

        $this->assertEquals( FL\NumberHelper::getInstance(10)->isOdd(), false);
        $this->assertEquals( FL\NumberHelper::getInstance(11)->isOdd(), true);

        $this->assertEquals( FL\NumberHelper::getInstance(11)->isEven(), false);
        $this->assertEquals( FL\NumberHelper::getInstance(12)->isEven(), true);
    
        $this->assertEquals( FL\NumberHelper::getInstance("10")->isOdd(), false);
        $this->assertEquals( FL\NumberHelper::getInstance("11")->isOdd(), true);

        $this->assertEquals( FL\NumberHelper::getInstance("11")->isEven(), false);
        $this->assertEquals( FL\NumberHelper::getInstance("12")->isEven(), true);
    }

    // Testing the round functions
    // -------------------------------------------
    public function testRound(): void
    {
        $this->assertEquals( FL\NumberHelper::getInstance("11.13234")->round(2)->getValue(), 11.13 );

    }

    // Testing the flip function
    // -------------------------------------------
    public function testFlip(): void
    {
        $this->assertEquals( FL\NumberHelper::getInstance("11")->setLowerLimit(10)->setUpperLimit(15)->flip()->getValue(), 14 );
        $this->assertEquals( FL\NumberHelper::getInstance("10")->setLowerLimit(10)->setUpperLimit(15)->flip()->getValue(), 15 );
        $this->assertEquals( FL\NumberHelper::getInstance("15")->setLowerLimit(10)->setUpperLimit(15)->flip()->getValue(), 10 );

        $this->assertEquals( FL\NumberHelper::getInstance("1")->setLowerLimit(10)->setUpperLimit(15)->flip()->getValue(), 15 );
        $this->assertEquals( FL\NumberHelper::getInstance("100")->setLowerLimit(10)->setUpperLimit(15)->flip()->getValue(), 10 );

    }

    // Testing the randomize function
    // ------------------------------
    public function testRandomize(): void
    {
        $random = FL\NumberHelper::getInstance()->setLowerLimit(10)->setUpperLimit(20)->randomize()->getValue();
        $this->assertGreaterThanOrEqual(10, $random);
        $this->assertLessThanOrEqual( 20, $random);

    }

}