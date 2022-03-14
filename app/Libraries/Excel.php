<?php

namespace App\Libraries;

use Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Excel
{
    private $_sheet;
    private $_pathfile;
    private $_filename;
    private $_row;
    private $_col;
    private $_columns;
    private $_spreadsheet;
    public function __construct(array $params)
    {
        $this->_pathfile    = $params['pathfile'];
        $this->_filename    = $params['filename'];
        $this->_columns     = $params['columns'];
        $this->_spreadsheet = new Spreadsheet();
        $this->_sheet       = $this->_spreadsheet->getActiveSheet();
        $this->_row         = 2;
        $this->_col         = 2;
        $this->_sheet->setTitle($params['title']);
        $this->setHeaders();
    }
    public function setHeaders()
    {
        $y = $this->_col;
        foreach ($this->_columns as $column) {
            $this->_sheet->setCellValueByColumnAndRow($y, $this->_row, $column);
            $y++;
        }
        $y--;
        $this->setColors([
            'start' => [$this->_col, $this->_row],
            'end'   => [$y, $this->_row],
            'color' => '3c8dbc'
        ]);
    }
    public function setColors(array $params)
    {
        $cord_start = $this->_sheet
            ->getCellByColumnAndRow($params['start'][0], $params['start'][1])
            ->getCoordinate();
        $cord_end   = $this->_sheet
            ->getCellByColumnAndRow($params['end'][0], $params['end'][1])
            ->getCoordinate();
        $this->_sheet->getStyle($cord_start . ':' . $cord_end)
            ->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB($params['color']);
    }
    public function setBorders(array $params)
    {
        $cord_start = $this->_sheet
            ->getCellByColumnAndRow($params['start'][0], $params['start'][1])
            ->getCoordinate();
        $cord_end   = $this->_sheet
            ->getCellByColumnAndRow($params['end'][0], $params['end'][1])
            ->getCoordinate();
        $this->_sheet->getStyle($cord_start . ':' . $cord_end)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                ]
            ]
        ]);
    }
    public function adjustWidth()
    {
        $col = $this->_col;
        foreach ($this->_columns as $column) {
            $cord   =  $this->_sheet
                ->getCellByColumnAndRow($col, $this->_row)
                ->getCoordinate();
            $this->_sheet->getColumnDimension(substr($cord, 0, 1))->setAutoSize(true);
            $col++;
        }
    }
    public function setValues($models, array $fields)
    {
        $row  = $this->_row;
        $row++;

        foreach ($models as $model) {
            $col   = $this->_col;
            foreach ($fields as $field) {
                Log::info($model);
                Log::info($field);
                if (isset($model->$field)) {
                    $this->_sheet->setCellValueByColumnAndRow($col, $row, $model->$field);
                } else {
                    $this->_sheet->setCellValueByColumnAndRow($col, $row, '');
                }
                $col++;
            }
            $row++;
        }
        $col--;
        $row--;
        $this->setBorders([
            'start' => [$this->_row, $this->_col],
            'end'   => [$col--, $row--]
        ]);
        $this->adjustWidth();
    }
    public function setValuesCollection($models, array $fields)
    {
        $row  = $this->_row;
        $row++;
        foreach ($models as $model) {
            $col   = $this->_col;
            foreach ($fields as $field) {
                Log::info($model);
                Log::info($field);
                Log::info($model->$field);
                if (isset($model->$field)) {
                    $this->_sheet->setCellValueByColumnAndRow($col, $row, $model->$field);
                } else {
                    $this->_sheet->setCellValueByColumnAndRow($col, $row, '');
                }
                $col++;
            }
            $this->setCurrency($row);
            $row++;
        }
        $col--;
        $row--;
        $this->setBorders([
            'start' => [$this->_row, $this->_col],
            'end'   => [$col--, $row--]
        ]);
        $this->adjustWidth();
    }

    public function setValuesArray($models, array $fields)
    {
        $row  = $this->_row;
        $row++;
        foreach ($models as $model) {
            $col   = $this->_col;
            foreach ($fields as $field) {
                if (isset($model[$field])) {
                    $this->_sheet->setCellValueByColumnAndRow($col, $row, $model[$field]);
                } else {
                    $this->_sheet->setCellValueByColumnAndRow($col, $row, '');
                }
                $col++;
            }
            // $this->setCurrency($row);
            $row++;
        }
        $col--;
        $row--;
        $this->setBorders([
            'start' => [$this->_row, $this->_col],
            'end'   => [$col--, $row--]
        ]);
        $this->adjustWidth();
    }

    public function setValuesSWS($models, array $fields)
    {
        $row  = $this->_row;
        $row++;
        foreach ($models as $model) {
            $col   = $this->_col;
            foreach ($fields as $field) {
                if (isset($model->$field)) {
                    $this->_sheet->setCellValueByColumnAndRow($col, $row, $model->$field);
                } else {
                    $this->_sheet->setCellValueByColumnAndRow($col, $row, '');
                }
                $col++;
            }
            $this->setCurrencySWS($row);
            $row++;
        }
        $col--;
        $row--;
        $this->setBorders([
            'start' => [$this->_row, $this->_col],
            'end'   => [$col--, $row--]
        ]);
        $this->adjustWidth();
    }

    public function save()
    {
        $writer = new Xlsx($this->_spreadsheet);
        if ($this->_pathfile == null) {

            // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="' . $this->_filename . '.xlsx"');
            header('Cache-Control: max-age=0');
            $writer->save("php://output");
        } else {
            $writer->save($this->_pathfile . '/' . $this->_filename);
        }
    }

    public function addColumn($tittle, $value)
    {
        $excel = $this->_sheet;
        $row = $excel->getHighestRow() + 2;
        $excel->setCellValueByColumnAndRow('7', $row, $tittle);
        $excel->setCellValueByColumnAndRow('8', $row, $value);
        $styleArray = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 15,
                'name'  => 'Verdana'
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            )
        );

        $this->_sheet->getStyle('G' . $row . ':H' . $row)->applyFromArray($styleArray);
        $this->_sheet->getStyle('H' . $row)->getNumberFormat()->applyFromArray(array(
            'formatCode'  => '"$"#,##0_-',
        ));

        $this->setColors([
            'start' => ['7', $row],
            'end'   => ['8', $row],
            'color' => 'FFFFBF80'
        ]);
        Log::info(range('B', 'J'));
        foreach (range('B', 'J') as $columnID) {
            $excel->getColumnDimension($columnID)
                ->setAutoSize(true);
        }
    }

    public function addTittle($tittle, $value)
    {
        $excel = $this->_sheet;
        $excel->mergeCells('B1:H1');
        $excel->setCellValueByColumnAndRow('2', '1', $tittle . ' ' . $value);

        $styleArray = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 15,
                'name'  => 'Verdana'
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            )
        );

        $this->_sheet->getStyle('A1:D1')->applyFromArray($styleArray);

        $this->setColors([
            'start' => ['3', '1'],
            'end'   => ['4', '1'],
            'color' => '3c8dbc'
        ]);
        $this->setBorders([
            'start' => ['3', '1'],
            'end'   => ['4', '1'],
        ]);
        $this->adjustWidth();
    }

    public function addTittleSWS($tittle, $value)
    {
        $excel = $this->_sheet;
        $excel->mergeCells('B1:D1');
        $excel->setCellValueByColumnAndRow('2', '1', $tittle . ' ' . $value);

        $styleArray = array(
            'font'  => array(
                'bold'  => true,
                'size'  => 12,
                'name'  => 'Verdana'
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            )
        );

        $this->_sheet->getStyle('A1:D1')->applyFromArray($styleArray);

        $this->adjustWidth();
    }

    public function setCurrency($row)
    {
        $excel = $this->_sheet;
        $styleArray = array(
            'formatCode'  => '"$"#,##0_-',
        );
        $this->_sheet->getStyle('G' . $row)->getNumberFormat()->applyFromArray($styleArray);
        $this->_sheet->getStyle('H' . $row)->getNumberFormat()->applyFromArray($styleArray);

        $this->adjustWidth();
    }

    public function setCurrencySWS($row)
    {
        $excel = $this->_sheet;
        $styleArray = array(
            'formatCode'  => '"$"#,##0_-',
        );
        $this->_sheet->getStyle('D' . $row)->getNumberFormat()->applyFromArray($styleArray);

        $this->adjustWidth();
    }
}
