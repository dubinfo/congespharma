<?php
    class Encryption
    {
        
        const SALT = 'OCFGTERMD';
        private $algos_hash = ['sha512', 'sha256', 'sha1'];
        private $type_hash;
        private $text_hasher;
		
	public function __construct()
	{
            $nb_args = func_num_args();
            //echo $nb_args;
            switch($nb_args)
            {
                case 1:
                        $args = func_get_args();
                        $this->setTypeHash();
                        $this->setText($args[0]);
                break;
        
                case 2:
                        $args = func_get_args();
                        $this->setTypeHash($args[1]);
                        $this->setText($args[0]);
                break;
        
                default:
                        $this->setTypeHash();
                break;
            }
	}
    
        public function getTextHasher(){
            return $this->text_hasher;
        }
        
        public function setText($input){
            if($this->IsArray($input))
            {
                $text = "";
                foreach($input as $param)
                {
                    $text .= $param;
                } 
            }
            else
            {
                $text = $input;
            }
            $text .= self::SALT;
            $this->text_hasher = hash($this->type_hash, $text);
        }
        
        public function setTypeHash($type = "sha512"){
            if(!in_array($type, $this->algos_hash))
            {
                $type = "sha512";
            }
            $this->type_hash = $type;
        }
        
        private function IsArray($input){
            return gettype($input) == 'array';
        }
    }
?>