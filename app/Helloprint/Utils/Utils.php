<?php

namespace Helloprint\Utils;

class Utils
{
    public static function buildJsonMessage($fields, $messages)
    {
        return json_encode([
            "schema" => [
                "type" => "struct",
                "fields" => (array) $fields,
                "optional" => false,
                "name" => "requests"
            ],
            "payload" => (array) $messages
        ]);
    }
}