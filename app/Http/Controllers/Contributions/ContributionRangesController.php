<?php

namespace App\Http\Controllers\Contributions;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\hdr_contribution as HdrContribution;
use App\Http\Resources\Contributions\ContributionRangesResource;

class ContributionRangesController extends Controller
{
    public function store(Request $request)
    {
        $data = null;
  
        $hdr = HdrContribution::find($request->id);

        if (!is_null($hdr)) {
            $data = $hdr->ranges()->createMany($request->data_set);
        }

        return ContributionRangesResource::collection($data);
        
    }
}
