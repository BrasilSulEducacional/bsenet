<?php

namespace App\Modules\Relatorios\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class FileCSV extends Controller
{
    private $header = array();
    private $array_content = array();

    public function setHeader($array_header)
    {
        $this->header = $array_header;
    }

    public function setContent($array_content)
    {
        $this->array_content = $array_content;
    }

    /**
     * Generate the file and Download
     */
    public function generateAndDownloadFileCSV()
    {
        $header_file = $this->getHeader();
        $content_file = $this->getContent();

        header('Cache-Control: max-age=0');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="my_csv_file.csv";');
        $output = fopen('php://output', 'w');
        if (!empty($header_file)) { // => Optional header
            fputcsv($output, $header_file, ';');
        }
        foreach ($content_file as $value) {
            fputcsv($output, $value, ';');
        }
    }

    /**
     * Gets the file header
     */
    private function getHeader()
    {
        return $this->header;
    }

    /**
     * Gets the content from array (usualy the database rows)
     */
    private function getContent()
    {
        $array_retorno = array();
        // => Checks whether data exists to be add on file
        if (count($this->array_content) > 0) {
            // => Scroll through the array
            foreach ($this->array_content as $value) {
                // => Contents definitions from database or other place
                $array_temp = array();
                foreach ($value as $col) {
                    // => Column "Column 1"
                    $array_temp[] = $col;
                }
                $array_retorno[] = $array_temp;
                unset($array_temp);
            }
        }
        return $array_retorno;
    }
}
