<?php

namespace Tests\Feature;

use App\Tracks\GPX\Models\GPX;
use App\Tracks\GPX\Models\TrackPoint;
use App\Tracks\GPX\Repositories\GPXRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class ListGPXTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testListGPX()
    {
        $gpxRepository = $this->myMock(GPXRepository::class);


        $point = new TrackPoint(1, 2, 3);
        $track = new GPX("pepe", $point, [$point], 300, 400);

        $pagination = new LengthAwarePaginator([$track], 1, 15, 1);
        $gpxRepository->shouldReceive("paginateAll")->with(15, 1)
            ->andReturn($pagination);

        $response = $this->getJson('/api/v1/gpx-listing');

        $response->assertStatus(200);
        $response->assertJson([
            "current_page" => 1,
            "data" => [
                [
                    "id" => null,
                    "name" => "pepe",
                    "center_json" => ["id" => null, "latitude" => 1, "longitude" => 2, "elevation" => 3],
                    "gpx_json" => [["id" => null, "latitude" => 1, "longitude" => 2, "elevation" => 3]],
                    "unevenness_positive" => 400,
                    "distance" => 300
                ]
            ],
            "first_page_url" => "/?page=1",
            "from" => 1, "last_page" => 1,
            "last_page_url" => "/?page=1",
            "next_page_url" => null,
            "path" => "/",
            "per_page" => 15,
            "prev_page_url" => null,
            "to" => 1,
            "total" => 1
        ]);
    }
}
