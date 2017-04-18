<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main;
use Bitrix\Main\Localization\Loc as Loc;

Loc::loadMessages(__FILE__);

try
{

    $sortDirection = array(
        'ASC' => Loc::getMessage('USERS_LIST_SORT_ASC'),
        'DESC' => Loc::getMessage('USERS_LIST_SORT_DESC')
    );
    $orderBy = array(
        'ID' => Loc::getMessage('USERS_LIST_ORDER_BY_ID'),
        'LOGIN' => Loc::getMessage('USERS_LIST_ORDER_BY_LOGIN'),
        'EMAIL' => Loc::getMessage('USERS_LIST_ORDER_BY_EMAIL'),
        'NAME' => Loc::getMessage('USERS_LIST_ORDER_BY_NAME'),
    );

    $arComponentParameters = array(
        'GROUPS' => array(
        ),
        'PARAMETERS' => array(
            'SHOW_NAV' => array(
                'PARENT' => 'BASE',
                'NAME' => Loc::getMessage('USERS_LIST_SHOW_NAV'),
                'TYPE' => 'CHECKBOX',
                'DEFAULT' => 'N'
            ),
            'COUNT' =>  array(
                'PARENT' => 'BASE',
                'NAME' => Loc::getMessage('USERS_LIST_PARAMETERS_COUNT'),
                'TYPE' => 'STRING',
                'DEFAULT' => '4'
            ),
            'ORDER_BY' => array(
                'PARENT' => 'BASE',
                'NAME' => Loc::getMessage('USERS_LIST_ORDER_BY_FIELD'),
                'TYPE' => 'LIST',
                'VALUES' => $orderBy
            ),
            'DIRECTION' => array(
                'PARENT' => 'BASE',
                'NAME' => Loc::getMessage('USERS_LIST_ORDER_BY_DIRECTION'),
                'TYPE' => 'LIST',
                'VALUES' => $sortDirection
            ),
            'CACHE_TIME' => array(
                'DEFAULT' => 3600
            )
        )
    );
}
catch (Main\LoaderException $e)
{
    ShowError($e->getMessage());
}
?>