<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
require "./../src/NumberHelper.php";
require "./../src/StringHelper.php";
Class testObject {
    private $value = "";
    public function setValue($value) {
        $this->value = $value;
    }
    public function __toString() {
        return $this->value;
    }
}

final class StringHelperTest extends TestCase
{
    // Testing the GetInstance variations
    // ----------------------------------
    public function testGetInstanceWithString(): void
    {
        $test = FL\StringHelper::getInstance("this is a teststring");
        $this->assertEquals($test->toString(), "this is a teststring");
    }
    public function testGetInstanceWithArray(): void
    {
        $testarray = array("Red", "Green", "Blue", "Orange");
        $test = FL\StringHelper::getInstance($testarray);
        $this->assertEquals($test->toString(), "Red Green Blue Orange");
    }
    public function testGetInstanceWithObject(): void
    {
        $testobject = new testObject();
        $testobject->setValue("this is yet another teststring");
        $test = FL\StringHelper::getInstance($testobject);
        $this->assertEquals($test, "this is yet another teststring");
    }

    // Testing the fromArray function
    // ------------------------------
    public function testFromArray(): void
    {
        $testarray = array("Red", "Green", "Blue", "Orange");
        $test = FL\StringHelper::getinstance()->fromArray(",", $testarray);
        $this->assertEquals($test->toString(), "Red,Green,Blue,Orange");
    }

    // Testing the setEncoding function
    // --------------------------------
    public function testSetEncoding(): void
    {
        $test = FL\StringHelper::getInstance("even more test strings", "UTF-8");
        $this->assertEquals($test->getEncoding(), "UTF-8");

        $test->setEncoding("");
        $this->assertEquals($test->getEncoding(), \mb_internal_encoding());
    }

    // Testing the toString and __toString functions
    // ---------------------------------------------
    public function testToString(): void
    {
        $test = FL\StringHelper::getInstance("this tests the toString and __toString functions");
        $this->assertEquals($test->toString(), $test);

    }

    // Testing the toArray function
    // ----------------------------
    public function testToArray(): void
    {
        $test = FL\StringHelper::getInstance("Red Green Blue Orange");
        $this->assertEquals($test->toArray(' '), array("Red", "Green", "Blue", "Orange"));
    }

    // Testing the getLength function
    // ------------------------------
    public function testGetLength(): void
    {
        $test = Fl\StringHelper::getInstance("This is a rather long string");
        $this->assertEquals($test->getLength(), 28);

        $test = Fl\StringHelper::getInstance("Thïŝ is a r³thôr lêng str§ng"); // multibyte
        $this->assertEquals($test->getLength(), 28);
    }

    // Testing the getSubstring function
    // ---------------------------------
    public function testGetSubstring(): void
    {
        $test = FL\StringHelper::getInstance("This is a nice long sentence to test the substring function");
        $this->assertEquals($test->getSubString(), "This is a nice long sentence to test the substring function");
        $this->assertEquals($test->getSubString(5), "is a nice long sentence to test the substring function");
        $this->assertEquals($test->getSubString(5, 4), "is a");
        $this->assertEquals($test->getSubString(5, 150), "is a nice long sentence to test the substring function");
        $this->assertEquals($test->getSubString(150, 150), ""); // you're trying to get a string outside the boundaries of the value, this returns an empty string

        $test = Fl\StringHelper::getInstance("Thïŝ is a r³thôr lêng str§ng"); // multibyte
        $this->assertEquals($test->getSubString(10, 6), "r³thôr");
    }

    // Testing the getCharacterAt function
    // -------------------------------------------
    public function testGetCharacterAt(): void
    {
        $test = FL\StringHelper::getInstance("This is a nice long sentence to test the substring function");
        $this->assertEquals($test->getCharacterAt(5), "i");

        $test = Fl\StringHelper::getInstance("Thïŝ is a r³thôr lêng str§ng"); // multibyte
        $this->assertEquals($test->getCharacterAt(1), "h");
        $this->assertEquals($test->getCharacterAt(3), "ŝ");
    }

    // Testing the getValueAsCharArray function
    // -------------------------------------------
    public function testGetValueAsCharArray(): void
    {
        $test = FL\StringHelper::getInstance("This is a test");
        $this->assertEquals($test->getValueAsCharArray(), array("T","h","i","s"," ", "i", "s", " ", "a", " ", "t","e","s","t"));

        $test = FL\StringHelper::getInstance("î Â Ô ö");  // multibye
        $this->assertEquals($test->getValueAsCharArray(), array("î"," ","Â"," ","Ô", " ", "ö"));
    }

    // Testing the setCharacterAt function
    // -----------------------------------
    public function testSetCharacterAt(): void
    {
        $test = FL\StringHelper::getInstance("That is a test");
        $test->setCharacterAt(2,"i");
        $test->setCharacterAt(3,"s");
        $this->assertEquals($test, "This is a test");

        $test = Fl\StringHelper::getInstance("Thïŝ is a r³thôr lêng str§ng"); // multibyte
        $test->setCharacterAt(4,"ü");
        $this->assertEquals($test, "Thïŝüis a r³thôr lêng str§ng");

        $this->expectException(OutOfBoundsException::class);
        $test->setCharacterAt(100,"b");
    }

    // Testing the removeCharacterAt function
    // --------------------------------------
    public function testRemoveCharacterAt(): void
    {
        $test = FL\StringHelper::getInstance("This isn't a test");
        $test->removeCharacterAt(9);
        $test->removeCharacterAt(8);
        $test->removeCharacterAt(7); // I could also have removed character at 7 three times.
        $this->assertEquals($test, "This is a test");

        $test->removeCharacterAt(13); // I could also have removed character at 7 three times.
        $this->assertEquals($test, "This is a tes");

        $test->removeCharacterAt(0); // I could also have removed character at 7 three times.
        $this->assertEquals($test, "his is a tes");

        $test = Fl\StringHelper::getInstance("Thïŝ is a r³thôr lêng str§ng"); // multibyte
        $test->removeCharacterAt(2);
        $this->assertEquals($test, "Thŝ is a r³thôr lêng str§ng");

        $this->expectException(OutOfBoundsException::class);
        $test->removeCharacterAt(100);
    }

    // Testing the insertCharacterAt function
    // --------------------------------------
    public function testInsertAt(): void
    {
        $test = FL\StringHelper::getInstance("This is a test");
        $test->insertAt(7, "n't");
        $this->assertEquals($test, "This isn't a test");

        $test = FL\StringHelper::getInstance("This is a test");
        $test->insertAt(7, "n");
        $test->insertAt(8, "'");
        $test->insertAt(9, "t");
        $this->assertEquals($test, "This isn't a test");
        $test->insertAt(17, ".");
        $this->assertEquals($test, "This isn't a test.");
        $test->insertAt(0, "-");
        $this->assertEquals($test, "-This isn't a test.");

        $test = Fl\StringHelper::getInstance("Thïŝ is a r³thôr lêng str§ng"); // multibyte
        $test->insertAt(2, "Ô");
        $this->assertEquals($test, "ThÔïŝ is a r³thôr lêng str§ng");

        $this->expectException(OutOfBoundsException::class);
        $test->insertAt(100, "!");
    }

    // Testing the sanitize function
    // -----------------------------
    public function testSanitize(): void
    {
        $test = FL\StringHelper::getInstance("<br><table>Just some random text</table>");
        $test->sanitize();
        $this->assertEquals($test, "Just some random text");

    }

    // Testing the toLower function
    // ----------------------------
    public function testToLower(): void
    {
        $test = FL\StringHelper::getInstance("ThIs iS a TeXT in RaNDom CaSE.");
        $test->toLower();
        $this->assertEquals($test, "this is a text in random case.");

        $test = FL\StringHelper::getInstance("ThÏs ïS â TêXT in R@NDom CaSE.");
        $test->toLower();
        $this->assertEquals($test, "thïs ïs â têxt in r@ndom case.");
    }

    // Testing the toUpper function
    // ----------------------------
    public function testToUpper(): void
    {
        $test = FL\StringHelper::getInstance("ThIs iS a TeXT in RaNDom CaSE.");
        $test->toUpper();
        $this->assertEquals($test, "THIS IS A TEXT IN RANDOM CASE.");

        $test = FL\StringHelper::getInstance("ThÏs ïS â TêXT in R@NDom CaSE.");
        $test->toUpper();
        $this->assertEquals($test, "THÏS ÏS Â TÊXT IN R@NDOM CASE.");
    }

    // Testing the toAlphaNumeric function
    // -----------------------------------
    public function testToAlphaNumeric(): void
    {
        $test = FL\StringHelper::getInstance("ThÏs ïS â TêXT in R@NDom CaSE.");
        $test->toAlphaNumeric();
        $this->assertEquals($test, "Ths S  TXT in RNDom CaSE");
    }

    // Testing the toNumeric function
    // ------------------------------
    public function testToNumeric(): void
    {
        $test = FL\StringHelper::getInstance("Th9Ïs ïS â T1êXT in R@NDom C1aSE.");
        $test->toNumeric();
        $this->assertEquals($test, "911");
    }

    // Testing the replace function
    // ----------------------------
    public function testReplace(): void
    {
        //     function replace($replacewhat, $replacewith, $scope = StringHelper::ALL) {
        $test = FL\StringHelper::getInstance("Th9Ïs ïS â T1êXT in R@NDom C1aSE.");
        $test->replace("9Ïs", "at", FL\StringHelper::ALL);
        $this->assertEquals($test, "That ïS â T1êXT in R@NDom C1aSE.");

        $test = FL\StringHelper::getInstance("This that the other This that the other");
        $test->replace("This", "Whatever", FL\StringHelper::FIRST);
        $this->assertEquals($test, "Whatever that the other This that the other");

        $test = FL\StringHelper::getInstance("This that the other This that the other");
        $test->replace("that", "something else", FL\StringHelper::ALL);
        $this->assertEquals($test, "This something else the other This something else the other");
        $test = FL\StringHelper::getInstance("This that the other This that the other");
        $test->replace("other", "doesn't matter", FL\StringHelper::LAST);
        $this->assertEquals($test, "This that the other This that the doesn't matter");

    }

    // Testing the various compare funcation
    // -------------------------------------
    public function testComparators():void
    {
        $test = FL\StringHelper::getInstance("Whatever");

        $this->assertEquals($test->equals("Whatever"), true);
        $this->assertEquals($test->notEquals("Whatever"), false);
        $this->assertEquals($test->equals("Something Else"), false);
        $this->assertEquals($test->notEquals("Something Else"), true);
        $this->assertEquals($test->isFilled(), true);
        $this->assertEquals($test->isNull(), false);
        $this->assertEquals($test->isEmptyNullOrZero(), false);
        $this->assertEquals($test->isEmpty(), false);
        $this->assertEquals($test->isZero(), false);

        $test = FL\StringHelper::getInstance(null);
        $this->assertEquals($test->isFilled(), false);
        $this->assertEquals($test->isNull(), true);
        $this->assertEquals($test->isEmptyNullOrZero(), true);
        $this->assertEquals($test->isEmpty(), true);
        $this->assertEquals($test->isZero(), false);

        $test = FL\StringHelper::getInstance("");
        $this->assertEquals($test->isEmpty(), true);
        $this->assertEquals($test->isZero(), false);
        $this->assertEquals($test->isEmptyNullOrZero(), true);

        $test = FL\StringHelper::getInstance(0);
        $this->assertEquals($test->isZero(), true);
        $this->assertEquals($test->isEmptyNullOrZero(), true);

        $test = FL\StringHelper::getInstance("0");
        $this->assertEquals($test->isZero(), true);
        $this->assertEquals($test->isEmptyNullOrZero(), true);

        $test = FL\StringHelper::getInstance("Whätêver");

        $this->assertEquals($test->equals("Whätêver"), true);
        $this->assertEquals($test->notEquals("Whätêver"), false);
        $this->assertEquals($test->equals("Sömething Else"), false);
        $this->assertEquals($test->notEquals("Sömething Else"), true);
    }

    // Testing the inArray function
    // ----------------------------

    public function testInArray(): void
    {
        $test = FL\StringHelper::getInstance("Red");
        $this->assertEquals($test->inArray(array("Red", "Green", "Blue", "Orange"), true), true);
        $this->assertEquals($test->inArray(array("red", "green", "blue", "orange"), false), true);
        $this->assertEquals($test->inArray(array("red", "red", "green", "blue", "orange"), false), true);
        $this->assertEquals($test->inArray(array("green", "blue", "orange"), false), false);
        $this->assertEquals($test->inArray(array("green", "blue", "orange"), true), false);

        $test = FL\StringHelper::getInstance("Rêd");
        $this->assertEquals($test->inArray(array("Rêd", "Grëen", "Blue", "Orange"), true), true);
        $this->assertEquals($test->inArray(array("rêd", "green", "blue", "orange"), false), true);
        $this->assertEquals($test->inArray(array("rêd", "rêd", "green", "blue", "orange"), false), true);
        $this->assertEquals($test->inArray(array("green", "blue", "orange"), false), false);
        $this->assertEquals($test->inArray(array("green", "blue", "orange"), true), false);

    }

    // Testing the startsWith function
    // -------------------------------
    public function testStartsWith(): void
    {
        $test = FL\StringHelper::getInstance("red green orange brown black");
        $this->assertEquals($test->startsWith("red"), true);
        $this->assertEquals($test->startsWith("blue"), false);

        $test = FL\StringHelper::getInstance("rëd green orange brown black");
        $this->assertEquals($test->startsWith("rëd"), true);
        $this->assertEquals($test->startsWith("blue"), false);
    }

    // Testing the endsWith function
    // -------------------------------
    public function testEndsWith(): void
    {
        $test = FL\StringHelper::getInstance("red green orange brown black");
        $this->assertEquals($test->endsWith("black"), true);
        $this->assertEquals($test->endsWith("red"), false);

        $test = FL\StringHelper::getInstance("rëd green orange brown blâck");
        $this->assertEquals($test->endsWith("blâck"), true);
        $this->assertEquals($test->endsWith("rëd"), false);
    }

    // Testing the contains function
    // -------------------------------
    public function testContains(): void
    {
        $test = FL\StringHelper::getInstance("red green orange brown black");
        $this->assertEquals($test->contains("black"), true);
        $this->assertEquals($test->contains("red"), true);
        $this->assertEquals($test->contains("orange"), true);
        $this->assertEquals($test->endsWith("blue"), false);

        $test = FL\StringHelper::getInstance("ôïÎèçà&@#`");
        $this->assertEquals($test->contains("è"), true);

    }

    // Testing the append and prepend functions
    // ----------------------------------------
    public function testAppendPrepend(): void
    {
        $test = FL\StringHelper::getInstance("bravo");
        $test->append(" charlie");
        $this->assertEquals($test, "bravo charlie");
        $test->prepend("alpha ");
        $this->assertEquals($test, "alpha bravo charlie");

        $test = FL\StringHelper::getInstance("brâvo");
        $test->append(" charlïe");
        $this->assertEquals($test, "brâvo charlïe");
        $test->prepend("alphä ");
        $this->assertEquals($test, "alphä brâvo charlïe");

    }

    // Testing the conditionals
    // ------------------------
    public function testConditionals(): void
    {
        $test = FL\StringHelper::getInstance("Testme");
        $this->assertEquals($test->ifEqualThenElse("Testme","It is equal", "It is not equal"), "It is equal");
        $this->assertEquals($test->ifEqualThenElse("Something Else","It is equal", "It is not equal"), "It is not equal");

        $this->assertEquals($test->ifLargerThenElse("Alpha", "It is larger", "It is not larger"), "It is larger");
        $this->assertEquals($test->ifLargerThenElse("Zero", "It is larger", "It is not larger"), "It is not larger");

        $this->assertEquals($test->ifSmallerThenElse("Alpha", "It is smaller", "It is not smaller"), "It is not smaller");
        $this->assertEquals($test->ifSmallerThenElse("Zero", "It is smaller", "It is not smaller"), "It is smaller");

        $this->assertEquals($test->ifNotEmpty("It is not empty"), "It is not empty");
        $this->assertEquals($test->ifEmpty("It is empty"), "");

        $test = FL\StringHelper::getInstance("");
        $this->assertEquals($test->ifNotEmpty("It is not empty"), "");
        $this->assertEquals($test->ifEmpty("It is empty"), "It is empty");


    }

    // Testing the removeExcessiveWhiteSpace function 
    // ----------------------------------------------
    public function testRemoveExcessiveWhiteSpace(): void
    {
        $test = FL\StringHelper::getInstance("This    is    a  test   string. This also.");
        $test->removeExcessiveWhiteSpace();
        $this->assertEquals($test, "This is a test string. This also.");
    }

    // Testing the removeFromStart function
    // ------------------------------------
    public function testRemoveFromStart(): void
    {
        $test = FL\StringHelper::getInstance("Test This is a test.");
        $test->removeFromStart("Test ");
        $this->assertEquals($test, "This is a test.");
    }

    // Testing the removeFromEnd function
    // ----------------------------------
    public function testRemoveFromEnd(): void
    {
        $test = FL\StringHelper::getInstance("This is a test.Test");
        $test->removeFromEnd("Test");
        $this->assertEquals($test, "This is a test.");
    }

    // Testing the countOccurences function
    // ------------------------------------
    public function testCountOccurrences(): void
    {
        $test = FL\StringHelper::getInstance("This is a test. this also. and even this is a test");
        $this->assertEquals($test->countOccurrences("is"), 5);

        $test = FL\StringHelper::getInstance("Thiâts is a tâtest. this also. âtand even ât this is a test");
        $this->assertEquals($test->countOccurrences("ât"), 4);

    }

    // Testing the keepEverything[Before/After][First/Last] functions
    // --------------------------------------------------------------
    public function testBeforeAfterFirstLast(): void
    {
        $test = FL\StringHelper::getInstance("Töm Bêrt çharles Bêrt çharles Töm Whatever");
        $this->assertEquals($test->keepEverythingBeforeFirst("Andy", true), "Töm Bêrt çharles Bêrt çharles Töm Whatever");
        $this->assertEquals($test->keepEverythingBeforeFirst("Andy"), "");

        $test = FL\StringHelper::getInstance("Töm Bêrt çharles Bêrt çharles Töm Whatever");
        $this->assertEquals($test->keepEverythingBeforeFirst("Bêrt", true), "Töm ");
        $test = FL\StringHelper::getInstance("Töm Bêrt çharles Bêrt çharles Töm Whatever");
        $this->assertEquals($test->keepEverythingBeforeFirst("Bêrt"), "Töm ");

        $test = FL\StringHelper::getInstance("Töm Bêrt çharles Bêrt çharles Töm Whatever");
        $this->assertEquals($test->keepEverythingBeforeLast("Töm", true), "Töm Bêrt çharles Bêrt çharles ");
        $test = FL\StringHelper::getInstance("Töm Bêrt çharles Bêrt çharles Töm Whatever");
        $this->assertEquals($test->keepEverythingBeforeLast("Töm"), "Töm Bêrt çharles Bêrt çharles ");

        $test = FL\StringHelper::getInstance("Töm Bêrt çharles Bêrt çharles Töm Whatever");
        $this->assertEquals($test->keepEverythingAfterFirst("Bêrt", true), " çharles Bêrt çharles Töm Whatever");
        $test = FL\StringHelper::getInstance("Töm Bêrt çharles Bêrt çharles Töm Whatever");
        $this->assertEquals($test->keepEverythingAfterFirst("Bêrt"), " çharles Bêrt çharles Töm Whatever");

        $test = FL\StringHelper::getInstance("Töm Bêrt çharles Bêrt çharles Töm Whatever");
        $this->assertEquals($test->keepEverythingAfterLast("Bêrt", true), " çharles Töm Whatever");
        $test = FL\StringHelper::getInstance("Töm Bêrt çharles Bêrt çharles Töm Whatever");
        $this->assertEquals($test->keepEverythingAfterLast("Bêrt"), " çharles Töm Whatever");

    }

    // Testing the containsMultibyteCharacters function
    // ------------------------------------------------
    public function testContainsMultibyteCharacters(): void
    {
        $test = FL\StringHelper::getInstance("Töm Bêrt çharles Bêrt çharles Töm Whatever");
        $this->assertEquals($test->containsMultibyteCharacters(), true);

        $test = FL\StringHelper::getInstance("This is a teststring");
        $this->assertEquals($test->containsMultibyteCharacters(), false);

    }

    // Testing the flip function
    // -------------------------
    public function testFlip(): void
    {
        $test = FL\StringHelper::getInstance("abcdef");
        $this->assertEquals($test->flip(), "zyxwvu");

        $test = FL\StringHelper::getInstance("aBcDeF");
        $this->assertEquals($test->flip(), "zYxWvU");

    }

    // Testing the increment function
    // ------------------------------
    public function testIncrement(): void
    {
        $test = FL\StringHelper::getInstance("Model");
        $this->assertEquals($test->increment("10", "1"), "Model1");

        $this->assertEquals($test->increment("10", "1"), "Model11");
        $this->assertEquals($test->increment("10", "1"), "Model21");
        $this->assertEquals($test->increment("10", "1"), "Model31");
        $this->assertEquals($test->increment("1", "1"), "Model32");

        $test = FL\StringHelper::getInstance("Model");
        $this->assertEquals($test->increment("10", "100"), "Model100");
        $this->assertEquals($test->increment("10", "100"), "Model110");
    }

    // Testing the randomize function
    // ------------------------------
    public function testRandomize(): void
    {
        $test = FL\StringHelper::getInstance()->randomize(8,8);
        $this->assertEquals($test->getLength(), 8);
    }


    // Testing the implementation of the Countable interface
    // -----------------------------------------------------
    public function testInterfaceCountable(): void
    {
        $test = FL\StringHelper::getInstance("four");
        $this->assertEquals($test->count(), 4);
    }

    // Testing the implementation of the IteratorAggregate interface
    // -------------------------------------------------------------
    public function testInterfaceIteratorAggregate(): void
    {
        $test = FL\StringHelper::getInstance("four");
        $iterator = $test->getIterator();
        foreach($iterator as $key => $char) {
            if ($key == 0) { 
                $this->assertEquals($char, "f");
            }
            if ($key == 1) { 
                $this->assertEquals($char, "o");
            }
            if ($key == 2) { 
                $this->assertEquals($char, "u");
            }
            if ($key == 3) { 
                $this->assertEquals($char, "r");
            }
        }
    }

    // Testing the implementation of the ArrayAccess interface
    public function testInterfaceArrayAccess(): void
    {
        $test = FL\StringHelper::getInstance("four");
        $this->assertEquals(isset($test[1]), true)        ;
        $this->assertEquals($test[1], "o");
        $this->assertEquals(isset($test[5]), false);
        $test[0] = "f";
        $test[1] = "i";
        $test[2] = "v";
        $test[3] = "e";
        $this->assertEquals($test, "five");
    }

}