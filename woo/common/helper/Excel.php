<?php
declare (strict_types=1);

namespace woo\common\helper;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use think\facade\Config;

class Excel
{
    public function readExcel(string $excel)
    {
        $realpath = $excel;
        if (!is_file($realpath)) {
            $realpath = root_path() . Config::get('woo.public_name') . DIRECTORY_SEPARATOR . $excel;
        }
        if (!is_file($realpath)) {
            throw  new \Exception('文件不存在:' . $excel);
        }

        $excelType = Str::studly(get_ext($realpath));
        if (!in_array($excelType, ['Xlsx', 'Xls', 'Xml', 'Csv'])) {
            throw  new \Exception('不支持的文件类型:' . strtolower($excelType));
        }
        try {
            $excelReader = IOFactory::createReader($excelType);
            $excelReader->setReadDataOnly(true);
            if ($excelType == 'Csv') {
                $excelReader = $excelReader
                    ->setDelimiter(',')
                    ->setInputEncoding('GBK') //不设置将导致中文列内容返回boolean(false)或乱码
                    ->setEnclosure('"')
                    //->setLineEnding("\r\n")  //新版本可删除
                    ->setSheetIndex(0);
            }

            $spreadsheet = $excelReader->load($realpath);
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();
            $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

            $fields = [];
            $fieldsMap = [];
            for ($i = 1; $i <= $highestColumnIndex; $i++) {
                $value = $worksheet->getCellByColumnAndRow($i, 1)->getValue();
                if (empty($value)) {
                    continue;
                }
                if (strpos($value, '#')) {
                    list($title, $field) = explode('#', trim($value));
                } else {
                    $field = trim($value);
                }
                $fields[$i] = $field;
                $fieldsMap[$field] = $title ?? Str::studly($field);
            }
            $data = [];
            for ($row = 2; $row <= $highestRow; $row++) {
                $rowData = [];
                foreach ($fields as $col => $field) {
                    $rowData[$field] = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                }
                array_push($data, $rowData);
            }
            return [
                'map' => $fieldsMap,
                'data' => $data
            ];
        } catch (\Exception $e) {
            throw  new \Exception($e->getMessage());
        }
    }

    public function download(array $head, array $data, $filename = '', $format = 'xls')
    {
        $head = array_values($head);
        if (!isset($head[0]['name']) || !isset($head[0]['field'])) {
            throw new \Exception('行头未设置name或field属性');
        }
        set_time_limit(0);
        $data = array_values($data);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $count = count($head);
        for ($i = 65; $i < $count + 65; $i++) {
            $sheet->setCellValue(strtoupper(chr($i)) . '1', $head[$i - 65]['name']);
            // 设置宽度
            if (!isset($head[$i - 65]['width'])) {
                $spreadsheet->getActiveSheet()->getColumnDimension(strtoupper(chr($i)))->setAutoSize(true);
            } else {
                $spreadsheet->getActiveSheet()->getColumnDimension(strtoupper(chr($i)))->setWidth((int)$head[$i - 65]['width']);
            }
            // 水平对齐
            $spreadsheet->getDefaultStyle()->getAlignment()->setHorizontal($head[$i - 65]['align'] ?? 'left');
            // 垂直对齐
            $spreadsheet->getDefaultStyle()->getAlignment()->setVertical($head[$i - 65]['valign'] ?? 'center');
            // 设置行高
            if (isset($head[$i - 65]['height'])) {
                $sheet->getRowDimension(1)->setRowHeight((int)$head[$i - 65]['height']);
            }
            // 表头信字体大小
            if (isset($head[$i - 65]['size'])) {
                $sheet->getCell(strtoupper(chr($i)) . '1')->getStyle()->getFont()->setSize((int) $head[$i - 65]['size']);
            }

            if (isset($head[$i - 65]['color'])) {
                //\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED
                $sheet->getCell(strtoupper(chr($i)) . '1')->getStyle()->getFont()
                    ->getColor()->setARGB($head[$i - 65]['color']);
            }

            if (isset($head[$i - 65]['backgound'])) {
                $spreadsheet->getActiveSheet()->getStyle(strtoupper(chr($i)) . '1')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB($head[$i - 65]['backgound']);
            }
        }

        foreach ($data as $row => $item) {
            for ($i = 65; $i < $count + 65; $i++) {
                $sheet->setCellValue(strtoupper(chr($i)) . strval($row + 2), $item[$head[$i - 65]['field']] ?? '');
                // 设置行高
                if (isset($head[$i - 65]['height'])) {
                    $sheet->getRowDimension($row + 2)->setRowHeight((int) $head[$i - 65]['height']);
                }
            }
        }

        $format = strtolower($format);
        if (!in_array($format, ['xlsx', 'xls'])) {
            throw new \Exception('只支持导出为xlsx或xls格式');
        }
        if ($format == 'xlsx') {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        } elseif ($format == 'xls') {
            header('Content-Type: application/vnd.ms-excel');
        }
        header('Content-Disposition: attachment;filename="' . $filename . '.' . $format . '"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, ucfirst($format));
        $writer->save('php://output');
    }
}