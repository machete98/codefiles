<?php

namespace Mayer;

use Bitrix\Main\Loader;
use \CModule;
use \CIBlock;
use \CFile;
use \CIBlockSection;
use \CIBlockElement;

CModule::IncludeModule('iblock');

class Functions {

    public static function lengthStr($str, $len) {
        if(mb_strlen($str) > $len) {
            $str = mb_substr($str, 0, $len, 'UTF-8')."...";
        }
        return $str;
    }

    public static function checkCount($id) {
        $resElemCnt = CIBlockElement::GetList(
            false,
            array('IBLOCK_ID' => $id, "ACTIVE"=>"Y"),
            false,
            false,
            array("ID")
        );
        $count = $resElemCnt->SelectedRowsCount();
        return $count;
    }

    public static function num2word($num, $words)
    {
        $num = $num % 100;
        if ($num > 19) {
            $num = $num % 10;
        }
        switch ($num) {
            case 1: {
                return($words[0]);
            }
            case 2: case 3: case 4: {
            return($words[1]);
        }
            default: {
                return($words[2]);
            }
        }
    }

    public function isExistsWordInString($string, $words) {
        $flag = false;
        if($string == $words) {
            $flag = true;
        } else if(is_array($words)) {
            foreach ($words as $word) {
                if(strpos($string, $word) === false) {
                    $flag = false;
                }
                else {
                    $flag = true;
                    break;
                }
            }
        } else {
            if(strpos($string, $words) === false) {
                $flag = false;
            }
            else {
                $flag = true;
            }
        }
        return $flag;
    }

    public static function getElementsOnSection($block_id, $sect_id = "", $props = array()) {
        if($sect_id != "") {
            $res = CIBlockElement::GetList(
                array("sort"=>"asc"),
                array('IBLOCK_ID' => $block_id, "ACTIVE"=>"Y", 'SECTION_ID' => $sect_id),
                false,
                false,
                array()
            );
        } else {
            $res = CIBlockElement::GetList(
                array("sort"=>"asc"),
                array('IBLOCK_ID' => $block_id, "ACTIVE"=>"Y"),
                false,
                false,
                array()
            );
        }
        $data = array();
        $i = 0;
        while($resItems = $res->GetNextElement()) {
            $arFields = $resItems->getFields();
            $arProps = $resItems->getProperties();
            $data[$i]["ID"] = $arFields["ID"];
            $data[$i]["NAME"] = $arFields["NAME"];
            $data[$i]["DETAIL_PAGE_URL"] = $arFields["DETAIL_PAGE_URL"];
            $data[$i]["PREVIEW_TEXT"] = $arFields["PREVIEW_TEXT"];
            $data[$i]["EDIT_LINK"] = $arFields["EDIT_LINK"];
            $data[$i]["DELETE_LINK"] = $arFields["DELETE_LINK"];
            if(count($props) > 0) {
                foreach ($props as $prop) {
                    $data[$i]["PROPS"][$prop] = $arProps[$prop];
                }
            }
            $i++;
        }

        return $data;
    }
    
    public function createDateFormat($date = "") {
        $date = ($date == "") ? date("j F Y") : $date;
        $arr = [
            'января',
            'февраля',
            'марта',
            'апреля',
            'мая',
            'июня',
            'июля',
            'августа',
            'сентября',
            'октября',
            'ноября',
            'декабря'
        ];
        $month = date('n', strtotime($date))-1;
        $date = date("d", strtotime($date)) .' '.$arr[$month].' '.date('Y');

        return $date;
    }

    public function getSectionIBlock($block_id) {
        $arFilter = Array('IBLOCK_ID'=>$block_id, 'GLOBAL_ACTIVE'=>'Y', "DEPTH_LEVEL"=>1);
        $db_list = CIBlockSection::GetList(Array("sort"=>"asc"), $arFilter, true, array("UF_*"));
        $data = array();
        $i = 0;
        while($ar_result = $db_list->GetNext())
        {
            if($ar_result["ELEMENT_CNT"] > 0) {
                $data[$i] = $ar_result;
                $i++;
            }
        }
        return $data;
    }
}
