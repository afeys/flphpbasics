<?php

namespace FL;

class EncryptionHelper {

    const OPENSSL = "openssl";
    const SIMPLE = "simple";

    private $ciphering = "AES-128-CTR";                 // default ciphering value
    private $encryption_iv = "7659870177734974"; // default encryption_iv value
    private $encryptionmethod = "openssl";
    private $password;
    private $value;

    private $lastaction = ""; // stores the last action performed ("encrypt", or "decrypt");

    /**
     * Helper function to the constructor.
     * This allows chaining multiple commands in one line:
     * $encryptedtext = EncryptionHelper::getInstance("my top secret information")->setPassword("password123")->encrypt();
     * getInstance takes the exact same parameters as the __construct method.
     * @param mixed $value  value to process, will be cast to a string first
     * @return object the EncryptionHelper instance
     */
    public static function getInstance($value = "", $encryptionmethod = "openssl") {
        $class = __CLASS__;
        return new $class($value, $encryptionmethod);
    }

    // --------------------------------------------------------------------------------------//
    // __ FUNCTIONS                                                                      //
    // --------------------------------------------------------------------------------------//

    /**
     * Initializes a EncryptionHelper instance. 
     * @param mixed $value  value to process, will be cast to a string first
     */
    function __construct($value = "", $encryptionmethod = "openssl") {
        $this->setValue($value);
        $this->setEncryptionMethod($encryptionmethod);
    }
 
    /**
     * Returns the current value of the instance
     * 
     * @return string The current value of the EncryptionHelper instance
     */
    function __toString() {
        return $this->getValue();
    }

    // --------------------------------------------------------------------------------------//
    // SETTER FUNCTIONS                                                                      //
    // --------------------------------------------------------------------------------------//

    /**
     *  Sets the value to be encrypted or decrypted
     *  @param mixed $value  value to process
     * * @return object the EncryptionHelper instance
     */
    public function setValue($value) {
        $this->value = $value;
        return $this;
    }

    /** 
     * Sets the encryption method to be used
     * Can be either "openssl" or "simple"
     * @param string $encryptionmethod either 'openssl' or 'simple'. WARNING: don't use simple to encrypt multibyte strings!! (UNTESTED)
     * @return object the EncryptionHelper instance
     */

     public function setEncryptionMethod($encryptionmethod = "openssl") {
         $encryptionmethod = \mb_strtolower($encryptionmethod);
         if ($encryptionmethod == "openssl" || $encryptionmethod == "simple") {
             $this->encryptionmethod = $encryptionmethod;
         }
         return $this;
     }

    /**
     * Sets the ciphering to be used in certain functions
     * @param mixed $value 
     * @return object the EncryptionHelper instance
     */
    public function setCiphering($value = null) {
        if ($value !== null && $value !== "") {
            $possibilities = openssl_get_cipher_methods(true);
            if (in_array($value, $possibilities)) {
                $this->ciphering = $value;
            }
        }
        return $this;
    }

    /**
     * Sets the encryption_iv to be used in certain functions
     * @param mixed $value 
     * @return object the EncryptionHelper instance
     */
    public function setEncryptionIv($value = null) {
        if ($value !== null && $value !== "") {
            $this->encryption_iv = $value;
        }
        return $this;
    }

    /** 
     * Sets the password to be used for encryption
     * @param string $password
     * @return object the Encryptionhelper instance
     */
    public function setPassword($password) {
        $this->password = $password;
        return $this;
    }

    // --------------------------------------------------------------------------------------//
    // GETTER FUNCTIONS                                                                      //
    // --------------------------------------------------------------------------------------//

    /**
     * Returns the current value of the instance. 
     * 
     * @return string The current value of the EncryptionHelper instance
     */

    public function getValue() {
        return $this->value;
    }

    /** 
     * Returns the encryption method used
     * Can be either "openssl" or "simple"
     * 
     * @return string The current encryption method used
     */

    public function getEncryptionMethod() {
        return $this->encryptionmethod;
    }

   /**
    * Returns the ciphering used in case of openssl encryption
    *
    * @return string The ciphering used for openssl
    */
   public function getCiphering() {
       return $this->ciphering;
   }

   /**
    * Returns the encryption_iv used in openssl encryption
    *
    * @return string the ciphering_iv used
    */
   public function getEncryptionIv() {
       return $this->encryption_iv;
   }

   /** 
    * Returns the password used
    * @return string the password used
    */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Returns the last action performed
     * @return string lastaction either "encrypt", or "decrypt"
     * 
     */
    public function getLastAction() {
        return $this->lastaction;
    }

    // --------------------------------------------------------------------------------------//
    // MODIFIER FUNCTIONS                                                                    //
    // --------------------------------------------------------------------------------------//

    /**
     * This function encrypts the value of the EncryptionHelper instance using the selected encryptionlmethod
     * and password
     * 
     * @return object the EncryptionHelper instance
     */
    public function encrypt() {
        if ($this->getPassword() !== null && $this->getPassword() !== ""  && $this->getValue() !== null && $this->getValue() !== "" ) {
            if ($this->getEncryptionMethod() == "openssl") {
                $this->setValue(openssl_encrypt($this->getValue(), $this->getCiphering(), $this->getPassword(), 0, $this->getEncryptionIv()));
            } else {
                $result = '';
                for ($i = 0; $i < strlen($this->getValue()); $i++) {
                    $char = substr($this->getValue(), $i, 1);
                    $keychar = substr($this->getPassword(), ($i % strlen($this->getPassword())) - 1, 1);
                    $char = chr(ord($char) + ord($keychar));
                    $result .= $char;
                }
                $this->setValue(base64_encode($result));
            }
            $this->lastaction = "encrypt";
        }
        return $this;
    }

    public function decrypt() {
        if ($this->getPassword() !== null && $this->getPassword() !== ""  && $this->getValue() !== null && $this->getValue() !== "" ) {
            if ($this->getEncryptionMethod() == "openssl") {
                $this->setValue(openssl_decrypt($this->getValue(), $this->getCiphering(), $this->getPassword(), 0, $this->getEncryptionIv()));
            } else {
                $result = '';
                $string = base64_decode($this->getValue());
                for ($i = 0; $i < strlen($string); $i++) {
                    $char = substr($string, $i, 1);
                    $keychar = substr($this->getPassword(), ($i % strlen($this->getPassword())) - 1, 1);
                    $char = chr(ord($char) - ord($keychar));
                    $result .= $char;
                }
                $this->setValue($result);
            }
            $this->lastaction = "decrypt";
        }
        return $this;
    }

}