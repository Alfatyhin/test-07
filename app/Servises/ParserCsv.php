<?php


namespace App\Servises;


class ParserCsv
{
    public function __construct()
    {
    }

    public static function parse($handle)
    {
        $header = NULL;
        $data = array();
        if ($handle) {
            while (($row = fgetcsv($handle)) !== FALSE) {
                if (!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
        }

        return $data;
    }
}
