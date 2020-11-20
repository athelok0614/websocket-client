<?php

declare(strict_types=1);

namespace App\Constants;

use _HumbugBoxc5228e318540\Roave\BetterReflection\Reflection\Adapter\ReflectionClass;
use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * @Constants
 */
class DeviceConstant extends AbstractConstants
{
    const ANDROID = 'android';

    const IOS = 'ios';

    const H5 = 'h5';

    const WEB = 'web';

    public static function getRandom()
    {
        $refl = new \ReflectionClass('App\Constants\DeviceConstant');
        $constants = $refl->getConstants();
        $key = array_rand($constants);
        return constant('self::'.$key);

    }
}
