<?php

/*
 * This file is part of gpupo/camel-webspider
 *
 * (c) Gilmar Pupo <g@g1mr.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * For more information, see
 * <http://www.g1mr.com/camel-webspider/>.
 */

namespace Gpupo\CamelWebspider\Entity;

interface InterfaceLink
{
    public function getId();
    public function isDone();
    public function isWaiting();
    public function toMinimal();
    public function setStatus($x);
}
