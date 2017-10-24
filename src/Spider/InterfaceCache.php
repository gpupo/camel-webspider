<?php

/*
 * This file is part of gpupo/camel-webspider
 *
 * (c) Gilmar Pupo <contact@gpupo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * For more information, see
 * <https://opensource.gpupo.com/camel-webspider/>.
 */

namespace Gpupo\CamelWebspider\Spider;

/*
* This file is part of the CamelSpider package.
*
* (c) Gilmar Pupo <contact@gpupo.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*
* @package     CamelSpider
* @subpackage  Spider
* @author      Gilmar Pupo <contact@gpupo.com>
*
*/
interface InterfaceCache
{
    public function save($id, $data, array $tags);
    public function getObject($id);
    public function isObject($id);
}
