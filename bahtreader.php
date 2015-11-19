<?php
    
    /**
     * BahtReader
     * 
     * Converts numbers into words (currently only in Baht).
     */
    
    class BahtReader
    {
        
        protected $words = ['ศูนย์', 'หนึ่ง','สอง','สาม','สี่','ห้า','หก','เจ็ด','แปด','เก้า'];
        protected $place_values = ['ล้าน', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน'];
        
        
        /**
         * Returns validated input
         * 
         * Any character present will be deleted.
         * As a result, scientific notations will not work.
         */
         
        public function money_validate($input, $delimiter = ',', $separator = '.')
        {
            // disallow any input with more than 1 period
            if (substr_count($input, $separator) > 1)
            {
                trigger_error('Number contains more than one period (.).', E_USER_ERROR);
                exit;
            }
            
            // escape seperator to be used with regex (only works with dot)
            $escaped_separator = $separator == '.' ? '\.' : $separator;
            
            // cut out any characters besides numbers (0-9), beginning negative sign (-) and decimal point (.)
            $input = preg_replace('/(?!^\-)[^' . $escaped_separator . '\d]/', '', trim($input));
            
            // ensure decimal is properly rounded
            list($dollars, $cents) = array_pad(explode($separator, $input, 2), 2, 00);
            $cents = substr(number_format('0' . $separator . $cents, 2), 1);
            $output = $dollars . $cents;
            
            return $output;
        }
        
        /**
         * Returns both validated and formatted input.
         * 
         * Calls money_validate() underneath in process.
         */
         
        public function money_format($input, $delimiter = ',', $separator = '.')
        {
            $input = $this->money_validate($input, $delimiter, $separator);
            
            // cut decimals away
            list($dollars, $cents) = explode($separator, $input, 2);
            
            $output = '';
            $length = strlen($dollars);
            $pos = ($length - 1) % 3;
            
            // insert comma at every 3 digits
            for ($i = 0; $i < $length; $i++) 
            {
                $output .= $dollars[$i];
                
                if (($pos - $i) % 3 == 0) 
                {
                    // stop displaying delimiter at the last digit
                    $output .= $i == $length - 1 ? '' : $delimiter;
                }
            }
            
            return $output . $separator . $cents;
        }
        

        /**
         * Returns word form of the monetary value given.
         * 
         * Currently only works with Thai Baht (THB).
         */
         
        public function read($input, $currency = 'บาท', $sub_currency = 'สตางค์', $separator = '.')
        {
            // validate input
            $input = $this->money_validate($input);
            
            // separate dollars and cents for easier working
            list($dollars, $cents) = explode($separator, $input, 2);
            
            $output = $this->spell($dollars);
            
            // 0 dollar
            if ($dollars == 0)
            {
                if ($cents == 0)
                {
                    return 'ศูนย์บาทถ้วน';
                }
            }
            else
            {
                // currency addition
                $output .= $currency;
            }
            
            if ($cents == 0)
            {
                return $output . 'ถ้วน';
            }
            else
            {
                $output .= $this->spell($cents);
                return $output . $sub_currency;
            }
        }
        
        
        /**
         * Returns word form of the numerical value given.
         * 
         * Currently only works with Thai Baht (THB).
         */
         
        private function spell($input)
        {
            $output = '';
            
            // handles negative value
            // cuts negative sign out for easier working
            if ($input < 0)
            {
                $output = 'ลบ';
                $dollars = substr($input, 1);
            }
            
            
            // internal controllers
            $length = strlen($input);
            $pos = ($length - 1) % 6;
            $input = str_split($input);
            $result = '';
            
            foreach ($input as $digit => $value)
            {
                $word = $this->words[$value];
                
                // value of number up to the point of $pos
                $result .= $value;
                
                // if digit is in the millionth position, fall back to 1
                // because all wordings will have to restart upon reaching million
                if ($pos < 0)
                {
                    $pos = 5;
                }
                elseif ($pos == 0)
                {
                    // หากเลขคือ 1 อยู่ในหลักล้าน หรือหลักหน่วย และยังมีหลักที่ใหญ่กว่าอยู่ ให้เปลี่ยน 'หนึ่ง' เป็น 'เอ็ด' เพราะไม่อ่าน 'สิบหนึ่ง'
                    if ($value == 1 && $result > 1)
                    {
                        $word = 'เอ็ด';
                    }
                }
                elseif ($pos == 1)
                {
                    // หากเลขคือ 1 และหลักคือ สิบ ให้ตัด 'หนึ่ง' ออก เพราะไม่อ่าน 'หนึ่งสิบ'
                    if ($value == 1)
                    {
                        $word = '';
                    }
                    
                    // หากเลขคือ 2 อยู่ในหลักสิบ ให้เปลี่ยน 'สอง' เป็น 'ยี่' เพราะไม่อ่าน 'สองสิบ'
                    elseif ($value == 2)
                    {
                        $word = 'ยี่';
                    }
                }
                
                if ($value == 0)
                {
                    // หากเลขคือ 0 ให้ตัด 'ศูนย์' ออก เพราะไม่อ่าน 'สิบศูนย์' หรือ 'ร้อยศูนย์ศูนย์'
                    $word = '';
                }
                
                
                $place_value = $this->place_values[$pos];
                
                // หากเลขคือ 0 ไม่ให้อ่านหลัก เว้นแต่หลักล้าน เพราะไม่อ่าน 'หนึ่งแสนหมื่นพันร้อยสอง'
                if (($value == 0 && $pos != 0) || $digit == $length - 1)
                {
                    $place_value = '';
                }
                
                $output .= $word . $place_value;
                $pos--;
            }
            
            return $output;
        }
    }
?>