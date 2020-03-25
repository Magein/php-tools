<?php

namespace magein\php_tools\common;

class Xml
{
    /**
     * @param $xml
     * @return array|mixed
     */
    public static function toArray($xml)
    {
        if (empty($xml)) {
            return [];
        }
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }

    /**
     * 数组转化为XML格式
     * @param array $param
     * @return string
     */
    public static function toXml(array $param)
    {
        if (empty($param) || !is_array($param) || count($param) <= 0) {
            return '';
        }

        $xml = "<xml>";
        foreach ($param as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";

        return $xml;
    }
}