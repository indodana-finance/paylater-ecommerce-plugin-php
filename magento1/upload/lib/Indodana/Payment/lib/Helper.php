<?php
class IndodanaHelper
{
    public static function getJsonPost()
    {
        $json = file_get_contents('php://input');
        return json_decode($json, true);
    }
}