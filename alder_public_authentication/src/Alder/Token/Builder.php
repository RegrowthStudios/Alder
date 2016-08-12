<?php

    namespace Alder\Token;
    
    use Alder\Token\Token;
    
    use Lcobucci\JWT\Builder as LcobucciBuilder;
    
    /**
     * Wrapper around Lcobucci's builder.
     */
    class Builder extends LcobucciBuilder
    {
        /**
         * {@inheritdoc}
         */
        public function getToken()
        {
            $payload = [
                $this->encoder->base64UrlEncode($this->encoder->jsonEncode($this->headers)),
                $this->encoder->base64UrlEncode($this->encoder->jsonEncode($this->claims))
            ];

            if ($this->signature !== null) {
                $payload[] = $this->encoder->base64UrlEncode($this->signature);
            }
            
            return new Token($this->headers, $this->claims, $this->signature, $payload);
        }
    }
