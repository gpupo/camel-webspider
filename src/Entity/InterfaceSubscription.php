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

interface InterfaceSubscription extends InterfaceLink
{
    public function getDomain();
    public function getHref();
    public function getFilters();
    public function getMaxDepth();
    public function getLink($sha1);
    public function getSourceType();
    public function getAuthInfo();
}
