<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main;
use Bitrix\Main\Localization\Loc as Loc;

class UsersList extends CBitrixComponent
{
    /**
     * кэшируемые ключи -> arResult
     * @var array()
     */
    protected $cacheKeys = array();

    /**
     * Зависимые параметры -> кеш
     * @var array
     */
    protected $cacheAddon = array();

    /**
     * парамтеры -> пэджинация
     * @var array
     */
    protected $navParams = array();

    /**
     * возврат -> значения
     * @var mixed
     */
    protected $returned;

    /**
     * тегированный кеш
     * @var mixed
     */
    protected $tagCache;

    /**
     * подключение -> языковые файлы
     */
    public function onIncludeComponentLang()
    {
        $this->includeComponentLang(basename(__FILE__));
        Loc::loadMessages(__FILE__);
    }

    /**
     * поток или кэш
     * @return bool
     */
    protected function readDataFromCache()
    {
        global $USER;
        if ($this->arParams['CACHE_TYPE'] == 'N')
            return false;

        if (is_array($this->cacheAddon))
            $this->cacheAddon[] = $USER->GetUserGroupArray();
        else
            $this->cacheAddon = array($USER->GetUserGroupArray());

        return !($this->startResultCache(false, $this->cacheAddon, md5(serialize($this->arParams))));
    }

    /**
     * кэширование -> arResult -> keys
     */
    protected function putDataToCache()
    {
        if (is_array($this->cacheKeys) && sizeof($this->cacheKeys) > 0)
        {
            $this->SetResultCacheKeys($this->cacheKeys);
        }
    }

    /**
     * прерывание кеширования
     */
    protected function abortDataCache()
    {
        $this->AbortResultCache();
    }

    /**
     * завершение кеширования
     * @return bool
     */
    protected function endCache()
    {
        if ($this->arParams['CACHE_TYPE'] == 'N')
            return false;

        $this->endResultCache();
    }

    /**
     * выполнение перед кешированием
     */
    protected function executeProlog()
    {
        if ($this->arParams['COUNT'] > 0)
        {
            if ($this->arParams['SHOW_NAV'] == 'Y')
            {
                \CPageOption::SetOptionString('main', 'nav_page_in_session', 'N');
                $this->navParams = array(
                    'nPageSize' => $this->arParams['COUNT']
                );
                $arNavigation = \CDBResult::GetNavParams($this->navParams);
                $this->cacheAddon = array($arNavigation);
            }
            else
            {
                $this->navParams = array(
                    'nTopCount' => $this->arParams['COUNT']
                );
            }
        }
        else
            $this->navParams = false;
    }


    /**
     * получение результатов
     */
    protected function getResult()
    {


        $iterator = \CUser::GetList(
            $this->arParams['ORDER_BY'],
            $this->arParams['DIRECTION']
        );
        $iterator->NavStart($this->arParams['COUNT']);

        while ($element = $iterator->GetNext())
        {
            $this->arResult['ITEMS'][] = $element;
        }

        if ($this->arParams['SHOW_NAV'] == 'Y' && $this->arParams['COUNT'] > 0)
        {
            $this->arResult['NAV_STRING'] = $iterator->GetPageNavString('');
        }

    }

    /**
     * формирование файлов и ссылок на файлы ()
     */
    protected  function putDataToFile()
    {
        try
        {
            // csv
            $csv = fopen($_SERVER['DOCUMENT_ROOT'].$this->getPath()."/files/users.csv", "w");
            $csvKeys = array();
            $csvArr = array();

            // xml
            $dom = new domDocument("1.0", "utf-8"); // Создаём XML-документ версии 1.0 с кодировкой utf-8
            $root = $dom->createElement("users"); // Создаём корневой элемент
            $dom->appendChild($root);

            $xmlIterator = \CUser::GetList(
                $this->arParams['ORDER_BY'],
                $this->arParams['DIRECTION']
            );
            while ($xmlElement = $xmlIterator->GetNext())
            {
                $user = $dom->createElement("user");
                $user->setAttribute("id", $xmlElement['ID']);

                foreach ($xmlElement as $key => $element) {

                    $node = $dom->createElement(preg_replace('/~/','',$key), $element);
                    $user->appendChild($node);
                }
                $root->appendChild($user);

                $csvKeys = array_keys($xmlElement);
                $csvArr[] = $xmlElement;
            }

            fputcsv($csv, $csvKeys,';');
            foreach ($csvArr as $csvElem) {
                fputcsv($csv, $csvElem,';');
            }


            if($dom->save($_SERVER['DOCUMENT_ROOT'].$this->getPath()."/files/users.xml")){
                $this->arResult['XMLPath'] = $this->getPath()."/files/users.xml";
            } else {
                $this->arResult['XMLPath'] = false;
            }

            if(fclose($csv)){
                $this->arResult['CsvPath'] = $this->getPath()."/files/users.csv";
            } else {
                $this->arResult['CsvPath'] = false;
            }

        }
        catch (Exception $e)
        {
            $this->abortDataCache();
            ShowError($e->getMessage());
        }

    }

    /**
     *  действия после выполения компонента
     */
    protected function executeEpilog()
    {
        if ($this->arResult['IBLOCK_ID'] && $this->arParams['CACHE_TAG_OFF'])
            \CIBlock::enableTagCache($this->arResult['IBLOCK_ID']);
    }

    /**
     * логика компонента
     */
    public function executeComponent()
    {
        global $APPLICATION;
        try
        {
            $this->executeProlog();
            if ($this->arParams['AJAX'] == 'Y')
                $APPLICATION->RestartBuffer();
            if (!$this->readDataFromCache())
            {
                $this->getResult();
                $this->putDataToFile();
                $this->putDataToCache();
                $this->includeComponentTemplate();
            }
            $this->executeEpilog();

            if ($this->arParams['AJAX'] == 'Y')
                die();

            return $this->returned;
        }
        catch (Exception $e)
        {
            $this->abortDataCache();
            ShowError($e->getMessage());
        }
    }
}