<?php

namespace Tests\Feature;

use App\Tracks\GPX\Models\GPX;
use App\Tracks\GPX\Models\TrackPoint;
use App\Tracks\GPX\Repositories\GPXRepository;
use Tests\TestCase;

class RemoveGPXTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRemoveGPX()
    {
        $gpxRepository = $this->myMock(GPXRepository::class);

        $point = new TrackPoint(40.2, -0.2, 1070.4);
        $track = new GPX("track", $point, [$point], 300, 400);
        $track->setId(23);

        $gpxRepository->shouldReceive("find")->with(23)->andReturn($track);

        $gpxRepository->shouldReceive("remove")->with($track)->andReturn(true);

        $response = $this->getJson('/api/v1/gpx-remove/23');

        $response->assertStatus(200);
        $response->assertSee('true');
    }
}
