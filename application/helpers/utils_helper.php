<?php
    use \PHPExcel\PHPExcel;

    function to_excel(array $data, $file, array $repeat_row = array(1,2)){
        $pexcel = new \PHPExcel();
        
        $pexcel->getActiveSheet()
        ->fromArray(
            $data,
            NULL
        );
        
        $pexcel
        ->getActiveSheet()
        ->getPageSetup()
        ->setRowsToRepeatAtTopByStartAndEnd($repeat_row[0], $repeat_row[1]);
        
        $writer = new PHPExcel_Writer_Excel2007($pexcel);
        $writer->save($file);
    }

    function to_upper($str) {
        $from = 'áéíóúñ';
        $to = 'ÁÉÍÓÚÑ';
        return strtr(strtoupper($str), $from, $to);
    }
?>
