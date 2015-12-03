<?php

/**
 * Created by PhpStorm.
 * User: wvdhaute
 * Date: 03/11/15
 * Time: 08:42
 */
abstract class LinkIDLTQRLockType
{
    const NEVER = 1;
    const ON_SCAN = 2;
    const ON_FINISH = 3;
}

/**
 * @param LinkIDLTQRLockType $lockType
 * @return string string representation of lock type
 */
function linkIDLTQRLockTypeToString($lockType)
{
    if (LinkIDLTQRLockType::NEVER == $lockType) return "NEVER";
    if (LinkIDLTQRLockType::ON_SCAN == $lockType) return "ON_SCAN";
    if (LinkIDLTQRLockType::ON_FINISH == $lockType) return "ON_FINISH";

    return "FRIENDLY";
}


/**
 * @param string $lockType lock type as a string
 * @return int|null parsed key type
 * @throws Exception
 */
function parseLinkIDLTQRLockType($lockType)
{

    if (null == $lockType) return null;

    if ($lockType == "NEVER") {
        return LinkIDLTQRLockType::NEVER;
    }
    if ($lockType == "ON_SCAN") {
        return LinkIDLTQRLockType::ON_SCAN;
    }
    if ($lockType == "ON_FINISH") {
        return LinkIDLTQRLockType::ON_FINISH;
    }

    throw new Exception("Unexpected lock type: " . $lockType);

}
