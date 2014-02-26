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

use Bread\Event;
use RuntimeException;

class Server extends Event\Emitter implements Interfaces\Server
{

    public $master;

    private $loop;

    private $context;

    public function __construct(Event\Interfaces\Loop $loop, array $context = array())
    {
        $this->loop = $loop;
        $this->context = $context;
    }

    public function listen($port, $host = '127.0.0.1')
    {
        $this->master = stream_socket_server("tcp://$host:$port", $errno, $errstr);
        if (false === $this->master) {
            $message = "Could not bind to tcp://$host:$port: $errstr";
            throw new Exceptions\Connection($message, $errno);
        }
        stream_set_blocking($this->master, 0);
        $this->loop->addReadStream($this->master, function ($master) {
            $newSocket = stream_socket_accept($master);
            if (false === $newSocket) {
                $this->emit('error', array(
                    new RuntimeException('Error accepting new connection')
                ));
                return;
            }
            $this->handleConnection($newSocket);
        });
        return $this;
    }

    public function handleConnection($socket)
    {
        stream_context_set_option($socket, $this->context);
        stream_set_blocking($socket, 0);

        $client = $this->createConnection($socket);

        if ($client instanceof SecureConnection) {
            $client->on('connection', function($client) {
                $this->emit('connection', array($client));
            });
        } else {
            $this->emit('connection', array($client));
        }
    }

    public function getPort()
    {
        $name = stream_socket_get_name($this->master, false);
        return (int) substr(strrchr($name, ':'), 1);
    }

    public function shutdown()
    {
        $this->loop->removeStream($this->master);
        fclose($this->master);
    }

    public function createConnection($socket)
    {
        $context = stream_context_get_options($socket);
        if (array_key_exists('ssl', $context)) {
            $conn = new SecureConnection($socket, $this->loop);
        } else {
            $conn = new Connection($socket, $this->loop);
        }

        return $conn;
    }

    public function run()
    {
        return $this->loop->run();
    }
}

