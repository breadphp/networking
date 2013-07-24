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

use Bread\Streaming\Stream;

// TODO Extend Streaming\Composite or Streaming\Through
class Connection extends Stream implements Interfaces\Connection
{

    public function handleData($stream)
    {
        $data = stream_socket_recvfrom($stream, $this->bufferSize);
        if ('' === $data || false === $data) {
            $this->end();
        } else {
            $this->emit('data', array(
                $data,
                $this
            ));
        }
    }

    public function handleClose()
    {
        if (is_resource($this->stream)) {
            stream_socket_shutdown($this->stream, STREAM_SHUT_RDWR);
            fclose($this->stream);
        }
    }

    public function getRemoteAddress()
    {
        return $this->parseAddress(stream_socket_get_name($this->stream, true));
    }

    public function isSecure()
    {
        return false;
    }

    public function isClientIdentified()
    {
        return false;
    }

    public function getServerIdentity()
    {
        return array();
    }

    public function getClientIdentity()
    {
        return array();
    }

    private function parseAddress($address)
    {
        return trim(substr($address, 0, strrpos($address, ':')), '[]');
    }
}
