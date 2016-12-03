<?php
    /** DateConvertor file */

    namespace Utils;
    
    /**
     * Jednotný formát datumů, převádí objekty na string a naopak
     */
    class DateConvertor {
        /** @var string $dateFormat základní formát datumu (27. 11. 2016) */
        private $dateFormat = "Y-d-n-h-i-s";
        
        /** @var string $databaseFormat formát při práci s databází */
        private $databaseFormat = "Y-m-d";
        
        /**
         * 
         * @return string
         */
        public function getDateFormat() {
            return $this->dateFormat;
        }
        
        /**
         * 
         * @return string
         */
        public function getDatabaseFormat() {
            return $this->databaseFormat;
        }
        
        /**
         * Převedení stringu na \DateTime
         * @param type $date
         * @return boolean|\DateTime
         */
        public function convertStringToDate(string $date = NULL)
        {
            $temp = $date;

            if (preg_match("#^[0-9]{1,2}\. [0-9]{1,2}\. [0-9]{4}$#", $temp)) {

                $temp = explode(". ", $temp);

                $datetime = new \DateTime();
                $datetime->setDate($temp[2], $temp[1], $temp[0]);

                return $datetime;
            }

            return FALSE;
        }
        
        /**
         * převedení stringu na databázový string
         * @param string $date
         * @return string
         */
        public function convertStringToDatabaseFormat(string $date) {
            $try = $this->convertStringToDate($date);
            
            if ($try !== FALSE) {
                $result = $try->format($this->databaseFormat);
                
                return $result;
            }
            
            return "0000-00-00";
        }

        /**
         * převedení \DateTime na string
         * @param \DateTime $date
         * @return boolean|string
         */
        public function convertDateToString($date)
        {
            if ($date instanceof \DateTime) {
                return $date->format($this->dateFormat);
            }

            return FALSE;
        }
        
        /**
         * převedení \DateTime na string (databázový formát) 
         * @param \DateTime $date
         * @return boolean
         */
        public function convertDateToDatabaseFormat($date)
        {
            if ($date instanceof \DateTime) {
                return $date->format($this->databaseFormat);
            }

            return FALSE;
        }

        /**
         * z array se pokusí vytvořit co nejvíce stringů z \DateTime
         * @param array $values
         * @return array
         */
        public function getStringsFromDates(array $values)
        {
            $temp = array();
            foreach ($values as $key => $value) {
                if ($this->convertDateToString($value) !== false) {
                    $temp[$key] = $this->convertDateToString($value);
                } else {
                    $temp[$key] = $value;
                }
            }

            return $temp;
        }
        
        /**
         * ověří správný formát datumu
         * @param string $date
         * @return boolean
         */
        public function checkDateFormat(string $date) {
            if (preg_match("#^[0-9]{1,2}\. [0-9]{1,2}\. [0-9]{4}$#", $date)) {
                return TRUE;
            }
            
            return FALSE;
        }
    }