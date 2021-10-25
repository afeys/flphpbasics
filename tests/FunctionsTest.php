<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class FunctionsTest extends TestCase
{
    // Testing the GetInstance variations
    // ----------------------------------
    public function testGetInstance(): void
    {
        $testarray = array("green","blue","red","yellow");
        $test = FL\ArrayHelper::getInstance($testarray);
        $this->assertEquals($test->getValue(), array("green","blue","red","yellow"));
    }


    // Testing the toString function
    // -----------------------------
    public function testToString():void
    {
        $testarray = array("green","blue","red","yellow");
        $test = FL\ArrayHelper::getInstance($testarray);
        $this->assertEquals($test, "green blue red yellow");

        $testarray = array("green","blue","red","yellow");
        $test = FL\ArrayHelper::getInstance($testarray);
        $this->assertEquals($test->toString(), "green blue red yellow");

    }

    // Testing the fromString function
    // -------------------------------
    public function testFromString():void
    {
        $testarray = array("green","blue","red","yellow");
        $test = FL\ArrayHelper::getInstance()->fromString(" ","green blue red yellow");
        $this->assertEquals($test->toArray(), $testarray);
    }

    // Testing the getNumberOfArrayItems function
    // ----------------------------------------
    public function testGetNumberOfArrayItems(): void
    {
        $testarray = array("green", "blue", "red", "yellow", "orange");
        $test = FL\ArrayHelper::getInstance($testarray);
        $this->assertEquals($test->getNumberOfArrayItems(), 5);
    }

    // Testing the getRandomArrayMember function
    // -----------------------------------------
    public function testGetRandomArrayMember(): void
    {
        $testarray = array("green", "blue", "red", "yellow", "orange");
        $testarray2 = array("brown", "black", "white", "grey", "teal");
        $test = FL\ArrayHelper::getInstance($testarray);
        $this->assertTrue(in_array($test->getRandomArrayMember(), $testarray));
        $this->assertFalse(in_array($test->getRandomArrayMember(), $testarray2));

    }

    // Testing the getItemWithKey function
    // -----------------------------------
    public function testGetItemWithKey(): void
    {
        $testarray = array("gr" => "green","bl" => "blue", "re" =>  "red","ye" => "yellow","or" => "orange");
        $test = FL\ArrayHelper::getInstance($testarray);
        $this->assertEquals($test->getItemWithKey("bl"), "blue");
        $this->assertEquals($test->getItemWithKey("az"), null);
        $this->assertEquals($test->getItemWithKey("az", "something"), "something");
    }

    // Testing the findItemInArrayWithKey function
    // -------------------------------------------
    public function testFindItemInArrayWithKey(): void
    {
        $testarray = array(
                        array( "name" => "Melissa", "age" => 40),
                        array( "name" => "Tim", "age" => 59),
                        array( "name" => "Bob", "age" => 20),
                        array( "name" => "Shania", "age" => 20),
        );
        $expectedresult1 = array("name" => "Bob", "age" => 20);
        $expectedresult2 = array(
                        array( "name" => "Bob", "age" => 20),
                        array( "name" => "Shania", "age" => 20),
        );
        $test = FL\ArrayHelper::getInstance($testarray);
        $this->assertEquals($test->findItemInArrayWithKey("name", "Bob"), $expectedresult1);
        $this->assertEquals($test->findItemInArrayWithKey("age", 20, true), $expectedresult2);
    }

    // Testing the removeDuplicates function
    // -------------------------------------
    public function testRemoveDuplicates(): void
    {
        $testarray = array( 0 => "black", 1=> "white",2=> "grey", 3=>"brown", 4=>"black",5=> "white",6=> "grey",7=> "black", 8=> "white", 9=>"grey", 10=>"teal");
        $expectedresult =  array( 0=>"black",1=> "white", 2=>"grey",3=> "brown", 10=>"teal");
        $test = FL\ArrayHelper::getInstance($testarray);
        $this->assertEquals($test->removeDuplicates()->toArray(), $expectedresult);
        
        $testarray = array(
            array( "name" => "Melissa", "age" => 40),
            array( "name" => "Shania", "age" => 20),
            array( "name" => "Tim", "age" => 59),
            array( "name" => "Bob", "age" => 20),
            array( "name" => "Tim", "age" => 59),
            array( "name" => "Shania", "age" => 20),
        );
        $expectedresult = array(
            array( "name" => "Melissa", "age" => 40),
            array( "name" => "Shania", "age" => 20),
            array( "name" => "Tim", "age" => 59),
            array( "name" => "Bob", "age" => 20),
        );
        $test = FL\ArrayHelper::getInstance($testarray);
        $this->assertEquals($test->removeDuplicates()->toArray(), $expectedresult);

    }

    // Testing the isAssociative function
    // ----------------------------------
    public function testIsAssociative(): void
    {
        $testarray = array( 0 => "black", 1=> "white",2=> "grey", 3=>"brown", 4=>"black",5=> "white",6=> "grey",7=> "black", 8=> "white", 9=>"grey", 10=>"teal");
        $test = FL\ArrayHelper::getInstance($testarray);
        $this->assertEquals($test->isAssociative(), false);

        $testarray = array("firstname" => "Andy", "lastname" => "Feys", "country" => "Belgium");
        $test = FL\ArrayHelper::getInstance($testarray);
        $this->assertEquals($test->isAssociative(), true);
    }

  
    // Testing the simpleSort function
    // -------------------------------
    public function testSimpleSort(): void
    {
        $test = FL\ArrayHelper::getInstance(array("0" => "green","1" => "blue", "2" => "red","3" =>"yellow"));
        $expected = array("1" => "blue","0" => "green","2" =>"red","3" => "yellow");
        $this->assertEquals($test->simpleSort()->toArray(), $expected);

        $expected = array("0" => "blue","1" => "green","2" =>"red","3" => "yellow");
        $this->assertEquals($test->simpleSort("", SORT_ASC, false)->toArray(), $expected);

        $expected = array("0" => "yellow","1" => "red","2" =>"green","3" => "blue");
        $this->assertEquals($test->simpleSort("", SORT_DESC, false)->toArray(), $expected);

        $test = FL\ArrayHelper::getInstance(array("0" => "green","1" => "Black", "2" => "blue", "3" => "red","4" =>"yellow", "5" => "Beige"));
        $expected = array("0" => "yellow","1" => "red","2" =>"green","3" => "blue", "4" => "Black", "5" => "Beige");
        $this->assertEquals($test->simpleSort("", SORT_DESC, false)->toArray(), $expected);

        $test = FL\ArrayHelper::getInstance(array("0" => "green","1" => "Black", "2" => "blue", "3" => "red","4" =>"yellow", "5" => "Beige"));
        $expected = array("0" => "Beige","1" => "Black","2" =>"blue","3" => "green", "4" => "red", "5" => "yellow");
        $this->assertEquals($test->simpleSort("", SORT_ASC, false)->toArray(), $expected);

        $test = FL\ArrayHelper::getInstance(array("0" => "green","1" => "black", "2" => "Blue", "3" => "red","4" =>"yellow", "5" => "Beige"));
        $expected = array("0" => "yellow","1" => "red","2" =>"green","3" => "Blue", "4" => "black", "5" => "Beige");
        $this->assertEquals($test->simpleSort("", SORT_DESC, false, false)->toArray(), $expected);

        $test = FL\ArrayHelper::getInstance(array("0" => "green","1" => "black", "2" => "Blue", "3" => "red","4" =>"yellow", "5" => "Beige"));
        $expected = array("0" => "Beige","1" => "black","2" =>"Blue","3" => "green", "4" => "red", "5" => "yellow");
        $this->assertEquals($test->simpleSort("", SORT_ASC, false, false)->toArray(), $expected);

        $testarray = array(
            "0" => array("name" => "Bob", "age" => 25, "city" => "New York"),
            "1" => array("name" => "Sandra", "age" => 31, "city" => "Paris"),
            "2" => array("name" => "Charles", "age" => 17, "city" => "Oostend"),
        );
        $test = FL\ArrayHelper::getInstance($testarray);
        $expected = array(
            "0" => array("name" => "Bob", "age" => 25, "city" => "New York"),
            "2" => array("name" => "Charles", "age" => 17, "city" => "Oostend"),
            "1" => array("name" => "Sandra", "age" => 31, "city" => "Paris"),
        );
        $this->assertEquals($test->simpleSort("name")->toArray(), $expected);

        $expected = array(
            "2" => array("name" => "Charles", "age" => 17, "city" => "Oostend"),
            "0" => array("name" => "Bob", "age" => 25, "city" => "New York"),
            "1" => array("name" => "Sandra", "age" => 31, "city" => "Paris"),
        );
        $this->assertEquals($test->simpleSort("age")->toArray(), $expected);


        $testarray = array(
            "0" => array("name" => "Bob", "age" => 25, "city" => "New York"),
            "1" => array("name" => "Sandra", "age" => 31, "city" => "Paris"),
            "2" => array("name" => "Charles", "age" => 17, "city" => "Oostend"),
            "3" => array("name" => "buck", "age" => 18, "city" => "kentucky"),
        );
        $test = FL\ArrayHelper::getInstance($testarray);
        $expected = array(
            "0" => array("name" => "Bob", "age" => 25, "city" => "New York"),
            "3" => array("name" => "buck", "age" => 18, "city" => "kentucky"),
            "2" => array("name" => "Charles", "age" => 17, "city" => "Oostend"),
            "1" => array("name" => "Sandra", "age" => 31, "city" => "Paris"),
        );
        $this->assertEquals($test->simpleSort("name", SORT_ASC, true, false)->toArray(), $expected);

        $expected = array(
            "0" => array("name" => "Bob", "age" => 25, "city" => "New York"),
            "1" => array("name" => "buck", "age" => 18, "city" => "kentucky"),
            "2" => array("name" => "Charles", "age" => 17, "city" => "Oostend"),
            "3" => array("name" => "Sandra", "age" => 31, "city" => "Paris"),
        );
        $this->assertEquals($test->simpleSort("name", SORT_ASC, false, false)->toArray(), $expected);
    }

    // Testing the advancedSort function
    // ---------------------------------
    public function testAvancedSort(): void
    {
        $testarray = array(
            "0" => array("name" => "Bob", "age" => 25, "city" => "New York"),
            "1" => array("name" => "Sandra", "age" => 31, "city" => "Paris"),
            "2" => array("name" => "Charles", "age" => 17, "city" => "Oostend"),
            "3" => array("name" => "Tim", "age" => 25, "city" => "New Brussels"),
            "4" => array("name" => "Patrick", "age" => 31, "city" => "Berlin"),
            "5" => array("name" => "Kelly", "age" => 17, "city" => "Rome"),
        );
        $test = FL\ArrayHelper::getInstance($testarray);

        $expected = array(
            "0" => array("name" => "Patrick", "age" => 31, "city" => "Berlin"),
            "1" => array("name" => "Sandra", "age" => 31, "city" => "Paris"),
            "2" => array("name" => "Bob", "age" => 25, "city" => "New York"),
            "3" => array("name" => "Tim", "age" => 25, "city" => "New Brussels"),
            "4" => array("name" => "Charles", "age" => 17, "city" => "Oostend"),
            "5" => array("name" => "Kelly", "age" => 17, "city" => "Rome"),
        );
        $this->assertEquals($test->advancedSort("age desc, name asc", false, false)->toArray(), $expected);
 

        $testarray = array(
            "0" => array("name" => "Bob", "age" => 25, "city" => "New York"),
            "1" => array("name" => "sandra", "age" => 31, "city" => "Paris"),
            "2" => array("name" => "Charles", "age" => 17, "city" => "Oostend"),
            "3" => array("name" => "Tim", "age" => 25, "city" => "New Brussels"),
            "4" => array("name" => "Steven", "age" => 31, "city" => "Berlin"),
            "5" => array("name" => "Kelly", "age" => 17, "city" => "Rome"),
        );
        $test = FL\ArrayHelper::getInstance($testarray);

        $expected = array(
            "0" => array("name" => "sandra", "age" => 31, "city" => "Paris"),
            "1" => array("name" => "Steven", "age" => 31, "city" => "Berlin"),
            "2" => array("name" => "Bob", "age" => 25, "city" => "New York"),
            "3" => array("name" => "Tim", "age" => 25, "city" => "New Brussels"),
            "4" => array("name" => "Charles", "age" => 17, "city" => "Oostend"),
            "5" => array("name" => "Kelly", "age" => 17, "city" => "Rome"),
        );
        $this->assertEquals($test->advancedSort("age desc, name asc", false, false)->toArray(), $expected);
 
        $expected = array(
            "0" => array("name" => "Steven", "age" => 31, "city" => "Berlin"),
            "1" => array("name" => "sandra", "age" => 31, "city" => "Paris"),
            "2" => array("name" => "Bob", "age" => 25, "city" => "New York"),
            "3" => array("name" => "Tim", "age" => 25, "city" => "New Brussels"),
            "4" => array("name" => "Charles", "age" => 17, "city" => "Oostend"),
            "5" => array("name" => "Kelly", "age" => 17, "city" => "Rome"),
        );
        $this->assertEquals($test->advancedSort("age desc, name asc", false, true)->toArray(), $expected);


        $testarray = array(
            "0" => array("name" => "Bob", "age" => 25, "city" => "New York"),
            "1" => array("name" => "sandra", "age" => 31, "city" => "Paris"),
            "2" => array("name" => "Charles", "age" => 17, "city" => "Oostend"),
            "3" => array("name" => "Tim", "age" => 25, "city" => "New Brussels"),
            "4" => array("name" => "Steven", "age" => 31, "city" => "Berlin"),
            "5" => array("name" => "Kelly", "age" => 17, "city" => "Rome"),
        );
        $test = FL\ArrayHelper::getInstance($testarray);
        $expected = array(
            "4" => array("name" => "Steven", "age" => 31, "city" => "Berlin"),
            "1" => array("name" => "sandra", "age" => 31, "city" => "Paris"),
            "0" => array("name" => "Bob", "age" => 25, "city" => "New York"),
            "3" => array("name" => "Tim", "age" => 25, "city" => "New Brussels"),
            "2" => array("name" => "Charles", "age" => 17, "city" => "Oostend"),
            "5" => array("name" => "Kelly", "age" => 17, "city" => "Rome"),
        );
        $this->assertEquals($test->advancedSort("age desc, name asc",true, true)->toArray(), $expected);



    }


    // Testing the implementation of the Countable interface
    // -----------------------------------------------------
    public function testInterfaceCountable(): void
    {
        $test = FL\ArrayHelper::getInstance(["green","blue","red","yellow"]);
        $this->assertEquals($test->count(), 4);
    }

    // Testing the implementation of the IteratorAggregate interface
    // -------------------------------------------------------------
    public function testInterfaceIteratorAggregate(): void
    {
        $test = FL\ArrayHelper::getInstance(["green","blue","red","yellow"]);
        $iterator = $test->getIterator();
        foreach($iterator as $key => $el) {
            if ($key == 0) { 
                $this->assertEquals($el, "green");
            }
            if ($key == 1) { 
                $this->assertEquals($el, "blue");
            }
            if ($key == 2) { 
                $this->assertEquals($el, "red");
            }
            if ($key == 3) { 
                $this->assertEquals($el, "yellow");
            }
        }
    }

    // Testing the implementation of the ArrayAccess interface
    // -------------------------------------------------------
    public function testInterfaceArrayAccess(): void
    {
        $test = FL\ArrayHelper::getInstance(["green","blue","red","yellow"]);
        $this->assertEquals(isset($test[1]), true)        ;
        $this->assertEquals($test[1], "blue");
        $this->assertEquals(isset($test[5]), false);
        $test[0] = "black";
        $test[1] = "cyan";
        $test[2] = "purple";
        $test[3] = "magenta";
        $this->assertEquals($test->toArray(), array("black", "cyan", "purple", "magenta"));
    }

}