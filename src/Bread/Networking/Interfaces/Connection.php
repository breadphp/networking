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
namespace Bread\Networking\Interfaces;

use Bread\Streaming;

interface Connection extends Streaming\Interfaces\Readable, Streaming\Interfaces\Writable
{

    public function getRemoteAddress();

    public function getRemotePort();

    public function isSecure();

    public function isClientIdentified();

    public function getServerIdentity();

    public function getClientIdentity();
}
