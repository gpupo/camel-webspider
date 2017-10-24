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

namespace Gpupo\CamelWebspider\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Used to create fake Subscriptions quickly, for testing.
 */
class FactorySubscription
{
    public static function build(array $array)
    {
        return new Subscription($array);
    }

    public static function buildFromDomain($domain, array $filters = null)
    {
        if (is_null($filters)) {
            $filters = ['contain' => 'rock', 'notContain' => 'polca'];
        }

        $array = [
            'domain'      => $domain,
            'href'        => 'http://'.$domain.'/',
            'max_depth'   => 2,
            'filters'     => $filters,
            'id'          => sha1($domain),
        ];

        return self::build($array);
    }

    public static function buildCollectionFromDomain(array $array)
    {
        $collection = new ArrayCollection();
        foreach ($array as $domain) {
            $collection->add(self::buildFromDomain($domain));
        }

        return $collection;
    }
}
