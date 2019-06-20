<?php

namespace App\Http\Controllers\Contributions;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ValidateDataRanges extends Controller
{
    public function action(Request $request)
    {
        
        if (is_null($file = $request->file('file'))) {
            return response()->json([
                'errors' => [
                    'file' => ['Please choose a file.']
                ]
            ], 422);
        }

        $path = $file->getRealPath();

        $extension = $file->getClientOriginalExtension();
            
        if ($extension !== 'csv') {
            return response()->json([
                'errors' => [
                    'file' => ['The file must be a file of type: csv.']
                ]
            ], 422);
        }

        $file = fopen($path, 'r');

        $item = [];

        while(!feof($file)) {
            $item[] = fgetcsv($file);
        }

        $column_header = $item[0];

        fclose($file);

        if ($this->isUnmatchedColumns($column_header)) {
            return response()->json([
                'errors' => [
                    'file' => ['The uploaded file does not match with the template.']
                ]
            ], 422);
        }

        if (!count($items = $this->mappedItems($item))) {
            return response()->json([
                'errors' => [
                    'file' => ['The uploaded file does not contain any records.']
                ]
            ], 422);
        }

        return response()->json($items);
    }

    protected function isUnmatchedColumns($uploaded_header)
    {
        $template_header = [
            'Lower Bound', 'Upper Bound', 'Employer Share', 'Employee Share'
        ];

        return count($template_header) !== count($uploaded_header);
    }

    protected function mappedItems($item)
    {
        $item = array_filter($item);

        $filtered = collect($item)->filter(function ($value, $key) {
            return $key > 0;
        });

        $filtered = array_values($filtered->toArray());

        $mapped = collect($filtered)->map(function ($item, $key) {

            $new['from'] = $item[0];
            $new['to']   = $item[1];
            $new['er']   = $item[2];
            $new['ee']   = $item[3];

            $new['from_display'] = $this->formattedAmount($new['from']);
            $new['to_display']   = $this->formattedAmount($new['to']);
            $new['er_display']   = $this->formattedAmount($new['er']);
            $new['ee_display']   = $this->formattedAmount($new['ee']);

            $new['status'] = false;

            return $new;
        });

        $mapped = $mapped->map(function ($item, $key) {

            $item['message'] = null;

            if (!empty($item['from'])) {
                if (!is_numeric($item['from'])) {
                    $item['message'] = 'Lower Bound must be a number.';
                    return $item;
                }
            } else {
                $item['message'] = 'Lower Bound is required.';
                return $item;
            }

            if (!empty($item['to'])) {
                if (!is_numeric($item['to'])) {
                    $item['message'] = 'Upper Bound must be a number.';
                    return $item;
                }
            } else {
                $item['message'] = 'Upper Bound is required.';
                return $item;
            }

            if (!empty($item['er'])) {
                if (!is_numeric($item['er'])) {
                    $item['message'] = 'Employer Share must be a number.';
                    return $item;
                }
            } else {
                $item['message'] = 'Employer Share is required.';
                return $item;
            }

            if (!empty($item['ee'])) {
                if (!is_numeric($item['ee'])) {
                    $item['message'] = 'Employee Share must be a number.';
                    return $item;
                }
            } else {
                $item['message'] = 'Employee Share is required.';
                return $item;
            }

            return $item;

        });

        return $mapped;
        
    }

    protected function formattedAmount($amount)
    {
        if (!is_numeric($amount)) {
            return $amount;
        }
        return number_format($amount, 2);
    }

}
