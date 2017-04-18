<?php
/**
 * Created by PhpStorm.
 * User: Muzaffar
 * Date: 16.04.2017
 * Time: 23:41
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc as Loc;
Loc::loadMessages(__FILE__);

$arComponentDescription = array(
    "NAME" => Loc::getMessage('USERS_LIST_DESCRIPTION_NAME'),
    "DESCRIPTION" => Loc::getMessage('USERS_LIST_DESCRIPTION_DESCRIPTION'),
    "ICON" => '/images/icon.gif',
    "SORT" => 2,
    "PATH" => array(
        "ID" => 'uretral',
        "NAME" => Loc::getMessage('USERS_LIST_DESCRIPTION_GROUP'),
        "SORT" => 10,
        "CHILD" => array(
            "ID" => 'custom',
            "NAME" => Loc::getMessage('USERS_LIST_DESCRIPTION_DIR'),
            "SORT" => 2
        )
    ),
);




?>