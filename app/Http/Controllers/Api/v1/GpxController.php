<?php

namespace App\Http\Controllers\Api\v1;


use App\Exceptions\ErrorControlledException;
use App\Http\Controllers\Controller;
use App\Tracks\GPX\Exceptions\GPXInvalidNameException;
use App\Tracks\GPX\Exceptions\GPXRouteNotGeneratedException;
use App\Tracks\GPX\Services\GPXNotSavedException;
use App\Tracks\GPX\Services\GPXService;
use Illuminate\Http\Request;

class GpxController extends Controller
{
    private $GPXService;

    /**
     * ListingGpxController constructor.
     * @param GPXService $GPXService
     */
    public function __construct(GPXService $GPXService)
    {
        $this->GPXService = $GPXService;
    }


    public function listing(Request $request)
    {
        $start = $request->get('start');
        $length = $request->get('length');
        $sort = $request->get('sort');

        return response()->json($this->GPXService->listGPX($start, $length, $sort));
    }

    public function add(Request $request)
    {
        $name = $request->get('name');
        $gpxB64 = $request->get('file');

        try {
            return response()->json($this->GPXService->addGPX($name, $gpxB64));
        } catch (GPXInvalidNameException | GPXRouteNotGeneratedException | GPXNotSavedException $e) {
            throw new ErrorControlledException(400, $e);
        }
    }

    public function remove($id)
    {
        return response()->json($this->GPXService->removeGPX($id));
    }
}
