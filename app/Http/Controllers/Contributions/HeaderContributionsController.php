<?php

namespace App\Http\Controllers\Contributions;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\hdr_contribution as HdrContribution;
use App\Http\Resources\Contributions\HdrContributionsResource;

class HeaderContributionsController extends Controller
{
    public function index(Request $request)
    {
        return HdrContributionsResource::collection(
            HdrContribution::where('flag', $request->flag)->latest()->get()
        );
    }

    public function store(Request $request)
    {
        $data = HdrContribution::create(
            $request->only('flag', 'title', 'status')
        );
        $this->setOldRecordsToInActive($data);
        return new HdrContributionsResource($data);
    }

    public function update(Request $request, $id)
    {
        $data = HdrContribution::find($id);

        $data->update(
            $request->only('title', 'status')
        );

        $this->setOldRecordsToInActive($data);

        return new HdrContributionsResource($data);
    }

    protected function setOldRecordsToInActive($data = null)
    {
        if (!is_null($data)) {
            if ($data->status === true) {
                HdrContribution::where([
                    ['flag', '=', $data->flag],
                    ['id', '!=', $data->id]
                ])->update(['status' => 0]);
            }
        }
    }
}
