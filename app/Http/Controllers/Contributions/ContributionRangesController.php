<?php

namespace App\Http\Controllers\Contributions;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\hdr_contribution as HdrContribution;
use App\Http\Resources\Contributions\ContributionRangesResource;
use App\Http\Resources\Contributions\ContributionRangesShowResource;

class ContributionRangesController extends Controller
{
    public function show($id)
    {
        return new ContributionRangesShowResource(
            HdrContribution::find($id)->load(['ranges'])
        );
    }

    public function store(Request $request)
    {
        $data = null;
  
        $hdr = HdrContribution::find($request->id);

        if (!is_null($hdr)) {
            $hdr->ranges()->delete();
            $data = $hdr->ranges()->createMany($request->table);
        }

        return ContributionRangesResource::collection($data);
        
    }
}
