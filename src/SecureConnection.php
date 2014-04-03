<?php
/**
 * Bread PHP Framework (http://github.com/saiv/Bread)
 * Copyright 2010-2012, SAIV Development Team <development@saiv.it>
 *
 * Licensed under a Creative Commons Attribution 3.0 Unported License.
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright  Copyright 2010-2012, SAIV Development Team <development@saiv.it>
 * @link       http://github.com/saiv/Bread Bread PHP Framework
 * @package    Bread
 * @since      Bread PHP Framework
 * @license    http://creativecommons.org/licenses/by/3.0/
 */
namespace Bread\Networking;

class SecureConnection extends Connection
{
    protected $secure = false;

    public function handleData($stream) {
        if (!$this->secure) {
            $result = stream_socket_enable_crypto($this->stream, true, STREAM_CRYPTO_METHOD_TLS_SERVER);

            if (0 === $result) {
                return;
            }

            if (false === $result) {
                echo "error\n";
                return;
            }

            $this->secure = true;
            $this->emit('connection', array($this));
        }

        //return parent::handleData($stream);
        while ('' === $data = fread($stream, $this->bufferSize)) {
            // FIXME Encrypted streams report data too early, wait the data to be decrypted
        }
        if ('' === $data || false === $data) {
            $this->end();
        } else {
            $this->emit('data', array(
                $data,
                $this
            ));
        }
    }

    public function isSecure()
    {
        return $this->secure;
    }
}

