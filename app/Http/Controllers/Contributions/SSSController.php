<?php

namespace App\Http\Controllers\Contributions;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\sss_contributions as SSS;
use App\Http\Resources\Contributions\SSSResource;

class SSSController extends Controller
{
    public function index()
    {
        return new SSSResource(
            SSS::all()
        );
    }

    public function store(Request $request)
    {
        $data = SSS::create([
            'key' => $this->getKey(),
            'title' => $request->title,
            'status' => $request->status,
        ]);
        $this->setOldRecordsToInActive($data);
        return new SSSResource(
            $data
        );
    }

    public function update(Request $request, $id)
    {
        $data = SSS::find($id);

        $data->update(
            $request->only('title', 'status')
        );

        $this->setOldRecordsToInActive($data);

        return new SSSResource(
            $data
        );
    }

    protected function getKey()
    {
        $data = SSS::latest()->first();
        return 'sss-'.(!is_null($data) ? $data->id + 1 : 1);
    }

    protected function setOldRecordsToInActive($data = null)
    {
        if (!is_null($data)) {
            if ($data->status === 'active') {
                SSS::where('id', '!=', $data->id)->update(['status' => 'inactive']);
            }
        }
    }
}
