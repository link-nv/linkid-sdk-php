<?php

/**
 * Created by PhpStorm.
 * User: wvdhaute
 * Date: 03/11/15
 * Time: 08:42
 */
abstract class LinkIDLocalizationKeyType
{
    const FRIENDLY = 1;
    const FRIENDLY_MULTIPLE = 2;
    const DESCRIPTION = 3;
}

/**
 * @param LinkIDLocalizationKeyType $localizationKeyType
 * @return string string representation of key type
 */
function linkIDLocalizationKeyTypeToString($localizationKeyType)
{
    if (LinkIDLocalizationKeyType::FRIENDLY == $localizationKeyType) return "FRIENDLY";
    if (LinkIDLocalizationKeyType::FRIENDLY_MULTIPLE == $localizationKeyType) return "FRIENDLY_MULTIPLE";
    if (LinkIDLocalizationKeyType::DESCRIPTION == $localizationKeyType) return "DESCRIPTION";

    return "FRIENDLY";
}


/**
 * @param string $localizationKeyType key type as a string
 * @return int|null parsed key type
 * @throws Exception
 */
function parseLinkIDLocalizationKeyType($localizationKeyType)
{

    if (null == $localizationKeyType) return null;

    if ($localizationKeyType == "localization.key.friendly") {
        return LinkIDLocalizationKeyType::FRIENDLY;
    }
    if ($localizationKeyType == "localization.key.friendly.multiple") {
        return LinkIDLocalizationKeyType::FRIENDLY_MULTIPLE;
    }
    if ($localizationKeyType == "localization.key.description") {
        return LinkIDLocalizationKeyType::DESCRIPTION;
    }

    throw new Exception("Unexpected localization key type: " . $localizationKeyType);

}
