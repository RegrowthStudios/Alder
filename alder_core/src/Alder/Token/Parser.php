<?php
    
    namespace Alder\Token;
        
    use Lcobucci\JWT\Parser as LcobucciParser;
    
    /**
     * Wrapper around Lcobucci's parser.
     */
    class Parser extends LcobucciParser
    {
        /**
         * {@inheritdoc}
         */
        public function parse($jwt) : Token {
            $data = $this->splitJwt($jwt);
            $header = $this->parseHeader($data[0]);
            $claims = $this->parseClaims($data[1]);
            $signature = $this->parseSignature($header, $data[2]);
            
            foreach ($claims as $name => $value) {
                if (isset($header[$name])) {
                    $header[$name] = $value;
                }
            }
            
            if ($signature === null) {
                unset($data[2]);
            }
            
            return new Token($header, $claims, $signature, $data);
        }
    }
