<?php
/**
 * Validační pravidla pro nette forms
 */
use Nette\Utils\Validators;

/**
 * ValidationRule class
 */
class ValidationRule
{
    const IN           = 'ValidationRule::validateIN';
    const EMAIL = "ValidationRule::validateEmail";
    const EMAIL_UNIQUE = "ValidationRule::validateEmailUnique";
    const BANK_ACCOUNT = 'ValidationRule::validateBankAccount';
    const DATE = "ValidationRule::validateDate";
    const ZIP = "ValidationRule::validateZip";
    const PHONE_NUMBER = "ValidationRule::validatePhoneNumber";

    /**
     * ověří formát IČ
     * @param \Nette\Forms\Controls\TextInput $control
     * @return boolean
     */
    public static function validateIN(\Nette\Forms\Controls\TextInput $control)
    {
        $ic = $control->getValue();

        // be liberal in what you receive
        $ic = preg_replace('#\s+#', '', $ic);

        // má požadovaný tvar?
        if (!preg_match('#^\d{8}$#', $ic)) {
            return FALSE;
        }

        // kontrolní součet
        $a = 0;
        for ($i = 0; $i < 7; $i++) {
            $a += $ic[$i] * (8 - $i);
        }

        $a = $a % 11;
        if ($a === 0) {
            $c = 1;
        } elseif ($a === 1) {
            $c = 0;
        } else {
            $c = 11 - $a;
        }

        return (int) $ic[7] === $c;
    }

    /**
     * Ověří formát čísla bankovního účtu
     * @param \Nette\Forms\Controls\TextInput $control
     * @return boolean
     */
    public static function validateBankAccount(\Nette\Forms\Controls\TextInput $control)
    {

        $bankAccount = $control->getValue();
        
        
        if (!preg_match('/(([0-9]{0,6})-)?([0-9]{1,10})/', $bankAccount,
                $matches)) {
            return FALSE;
        }
        
        /** @todo MODULO 11.. nějak nefunguje, příčinou může být nastavený jen 32bit integer.. viz. http://www.michalhaltuf.cz/blog/2016/04/validace-cisla-bankovniho-uctu-v-php/ */
        
        /** 
        dump($matches);
        
        $first  = sprintf('%06d', $matches[1]);
        $second = sprintf('%010d', $matches[3]);

        // FIRST PART - MODULO 11
        $isOk = (10 * $first[0] + 5 * $first[1] + 8 * $first[2] + 4 * $first[3] + 2
            * $first[4] + 1 * $first[5]) % 11 == 0;

        if ($isOk === FALSE) {
            dump("FIRST PART FAIL");
            return FALSE;
        }

        // SECOND PART - MODULO 11
        $isOk = ( 6 * $second[0] + 3 * $second[1] + 7 * $second[2] + 9 * $second[3]
            + 10 * $second[4] + 5 * $second[5] + 8 * $second[6] + 4 * $second[7]
            + 2 * $second[8] + 1 * $second[9]) % 11 == 0;

        if ($isOk == FALSE) {
            
            dump("SECOND PART FAIL");
            return FALSE;
        }
         * 
         */

        return TRUE;
    }
    
    /**
     * ověří formát data
     * @param \Nette\Forms\Controls\TextInput $control
     * @return boolean
     */
    public static function validateDate(\Nette\Forms\Controls\TextInput $control) {
        $date = $control->getValue();
        
        $date_convertor = new \Utils\DateConvertor();
        
        return $date_convertor->checkDateFormat($date);
    }
    
    /**
     * ověří formát emailu
     * @param \Nette\Forms\Controls\TextInput $control
     * @return boolean
     */
    public static function validateEmail(\Nette\Forms\Controls\TextInput $control) {
        $email = $control->getValue();
        
        return Validators::isEmail($email);
    }
    
    /**
     * ověří formát poštovníhp směrovacího čísla
     * @param \Nette\Forms\Controls\TextInput $control
     * @return boolean
     */
    public static function validateZip(\Nette\Forms\Controls\TextInput $control) {
        $zip = $control->getValue();
        
        if (preg_match("#[0-9]{5}#", $zip)) {
            return TRUE;
        }
        
        return FALSE;
    }
    
    /**
     * ověření formát čísla mobilu
     * @param \Nette\Forms\Controls\TextInput $control
     * @return boolean
     */
    public static function validatePhoneNumber(\Nette\Forms\Controls\TextInput $control) {
        $number = $control->getValue();
        
        if (preg_match("#(\+[0-9]{1-3})? ?[1-9][0-9]{2} ?[0-9]{3} ?[0-9]{3}#", $number)) {
            return TRUE;
        }
        
        return FALSE;
    }
}