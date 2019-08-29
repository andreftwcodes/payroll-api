<?php

namespace App\Http\Controllers\Contributions;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\hdr_contribution as HdrContribution;
use App\Http\Requests\Contributions\HeaderContributionRequest;
use App\Http\Resources\Contributions\HdrContributionsResource;
use App\Http\Requests\Contributions\HeaderContributionUpdateRequest;

class HeaderContributionsController extends Controller
{
    public function index(Request $request)
    {
        return HdrContributionsResource::collection(
            HdrContribution::where('flag', $request->flag)->latest()->get()
        );
    }

    public function store(HeaderContributionRequest $request)
    {
        $data = HdrContribution::create(
            $request->only('flag', 'title', 'used_at')
        );

        return new HdrContributionsResource($data);
    }

    public function update(HeaderContributionUpdateRequest $request, $id)
    {
        $data = HdrContribution::find($id);

        $data->update(
            $request->only('title', 'used_at')
        );

        return new HdrContributionsResource($data);
    }

}
