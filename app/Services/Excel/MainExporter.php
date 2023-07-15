<?php

namespace App\Services\Excel;

use App\Helpers\JalaliCalender;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;


class MainExporter implements FromCollection, WithMapping, WithHeadings, WithTitle
{
    use Exportable;

    private $rows;
    private array $sheets;

    /**
     * @param $sheet
     */
    public function __construct($sheet)
    {
        $this->rows = $sheet['rows'];
        $this->columns = !empty($sheet['columns']) ? $sheet['columns'] : array_keys($sheet['rows']->first()->toArray());
        $this->title = $sheet['title'];
    }

    public function collection()
    {
        return $this->rows;
    }

    public function map($row): array
    {
        $rows = [];

        foreach (array_values($this->columns) as $key => $address) {
            if ($address == "id") {
                $rows[$key] = $row->getKey();
            } else {
                $address_chunks = explode(",", $address);

                if (count($address_chunks) == 1) {

                    $converter = null;
                    if (Str::contains($address, ":")) {
                        $converter = explode(":", $address);
                        $address = $converter[0];
                        $converter = end($converter);
                    }
                    $rows[$key] = $this->getValue($row, $address, $converter);
                } else {
                    foreach ($address_chunks as $k => $address_chunk) {
                        if ($k == 0)
                            $rows[$key] = $this->getValue($row, $address_chunk);
                        else
                            $rows[$key] .= " " . $this->getValue($row, $address_chunk);
                    }
                }
            }
        }

        return [...$rows];
    }

    public function headings(): array
    {
        return array_keys($this->columns);
    }

    public function title(): string
    {
        return $this->title;
    }


    private function getValue($row, $address, $converter = null)
    {
        $address_chunks = explode(".", $address);

        $value = null;

        if (count($address_chunks) == 1) {
            try {
                if ($row instanceof Model)
                    $value = $row->{$address};
                elseif (is_array($row))
                    $value = $row[$address];
            } catch (\Exception $exception) {
                return 0;
            }
        } else {
            if (is_array($row))
                $array = $row;
            else
                $array = $row->toArray();

            $value = $this->delimitArray($array, $address);
        }

        if ($converter) {
            $value = $this->convertValue($value, $converter);
        }

        return $value;
    }

    public function delimitArray($array, $address, $delimiter = ".")
    {
        $address = explode($delimiter, $address);
        $num_args = count($address);

        $value = $array;
        for ($i = 0; $i < $num_args; $i++) {
            $value = $value[$address[$i]] ?? null;
        }

        return $value;
    }

    public function convertValue($value, $converter)
    {
        switch ($converter) {
            case "jalali":
                if ($value instanceof Carbon) {
                    $time = $value->toTimeString();
                    $date = JalaliCalender::toJalali($value->year, $value->month, $value->day);
                    $value = $date . ' ' . $time;
                } elseif (!is_null($value)) {
                    try {
                        $value = Carbon::create($value);
                        $time = $value->toTimeString();
                        $date = JalaliCalender::toJalali($value->year, $value->month, $value->day);
                        $value = $date . ' ' . $time;
                    } catch (\Exception $exception) {
                        break;
                    }
                }
                break;
        }

        return $value;
    }


}
