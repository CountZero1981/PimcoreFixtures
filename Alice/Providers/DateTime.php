<?php

namespace FixtureBundle\Alice\Providers;

use Faker\Provider\Base as BaseProvider;

class DateTime extends BaseProvider
{
    /**
     * @param string $date
     * @return \DateTime
     */
    public static function exactDateTime(string $date = 'now'): \DateTime
    {
        return new \DateTime( $date );
    }
}
