<?php

namespace App\Http\Controllers\Contributions;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Contributions\DataRangesRequest;

class ValidateDataRanges extends Controller
{
    public function action(DataRangesRequest $request)
    {
        $file = $request->file('file');

        $path = $file->getRealPath();

        $extension = $file->getClientOriginalExtension();

        if ($extension === 'csv') {
            
            $file = fopen($path, 'r');

            $item = [];

            while(!feof($file)) {
                $item[] = fgetcsv($file);
            }

            $column_header = $item[0];

            fclose($file);

            if ($this->isValidTemplate($column_header)) {
                if (count($item = $this->mappedItems($item))) {
                    
                }
            }

        }

        return response()->json($item);
    }

    protected function isValidTemplate($uploaded_header)
    {
        $template_header = [
            'Lower Bound', 'Upper Bound', 'Employer Share', 'Employee Share'
        ];

        return count($template_header) === count($uploaded_header);
    }

    protected function mappedItems($item)
    {
        $item = array_filter($item);

        $filtered = collect($item)->filter(function ($value, $key) {
            return $key > 0;
        });

        $filtered = array_values($filtered->toArray());

        return collect($filtered)->map(function ($item, $key) {
            $new['from'] = $item[0];
            $new['to']   = $item[1];
            $new['er']   = $item[2];
            $new['ee']   = $item[3];
            $new['status'] = false;
            return $new;
        });
        
    }
}
