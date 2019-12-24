<?php

namespace Tests\Feature;

use App\Tracks\GPX\Models\GPX;
use App\Tracks\GPX\Models\TrackPoint;
use App\Tracks\GPX\Repositories\GPXRepository;
use Tests\TestCase;

class AddGPXTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAddGPX()
    {
        $gpxRepository = $this->myMock(GPXRepository::class);

        $trackName = "trackName";

        $fileGPX = "data:application/gpx+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz48Z3B4IGNyZWF0"
            . "b3I9Ildpa2lsb2MgLSBodHRwczovL3d3dy53aWtpbG9jLmNvbSIgdmVyc2lvbj0iMS4xIiB4bWxucz0iaHR0cDovL3d3dy50b3BvZ3J"
            . "hZml4LmNvbS9HUFgvMS8xIiB4bWx"
            . "uczp4c2k9Imh0dHA6Ly93d3cudzMub3JnLzIwMDEvWE1MU2NoZW1hLWluc3RhbmNlIiB4c2k6c2NoZW1hTG9jYXRpb249Imh0dHA6Ly9"
            . "3d3cudG9wb2dyYWZpeC5jb20vR1BYLzEvMSBodHRwOi8vd3d3LnRvcG9ncmFmaXguY29tL0dQWC8xLzEvZ3B4LnhzZCI+PG1ldGFkY" .
            "XRhPjxuYW1lPldpa2lsb2MgLSBDaG9kb3MsIHZ1ZWx0YSBhbCBQZcOxYWdvbG9zYSBFIDIuMDwvbmFtZT48YXV0aG9yPjxuYW1lPmNhc2N"
            . "vYmxhbmNvPC9uYW1lPjxsaW5rIGhyZWY9Imh0dHBzOi8vd3d3Lndpa2lsb2MuY29tL3dpa2lsb2MvdXNlci5kbz9pZD0xMjA0ODUiP"
            . "jx0ZXh0PmNhc2NvYmxhbmNvIG9uIFdpa2lsb2M8L3RleHQ+PC9saW5rPjwvYXV0aG9yPjxsaW5rIGhyZWY9Imh0dHBzOi8vd3d3" .
            "Lndpa2lsb2MuY29tL21vdW50YWluLWJpa2luZy10cmFpbHMvY2hvZG9zLXZ1ZWx0YS1hbC1wZW5hZ29sb3NhLWUtMi0wLTE3MjQ2M"
            . "TIwIj48dGV4dD5DaG9kb3MsIHZ1ZWx0YSBhbCBQZcOxYWdvbG9zYSBFIDIuMCBvbiBXaWtpbG9jPC90ZXh0PjwvbGluaz48dGlt"
            . "ZT4yMDE3LTA0LTA4VDEzOjMyOjAwWjwvdGltZT48L21ldGFkYXRhPjx0cms+PG5hbWU+Q2hvZG9zLCB2dWVsdGEgYWwgUGXDsW"
            . "Fnb2xvc2EgRSAyLjA8L25hbWU+PGNtdD48L2NtdD48ZGVzYz48L2Rlc2M+PHRya3NlZz48dHJrcHQgbGF0PSI0MC4yIiBsb249Ii"
            . "0wLjIiPjxlbGU+MTA3MC40PC9lbGU+PHRpbWU+MjAxNy0wNC0wOFQwNjoyOToyOFo8L3RpbWU+PC90cmtwdD48L3Rya3NlZz48L"
            . "3Ryaz48L2dweD4K";

        $point = new TrackPoint(40.2, -0.2, 1070.4);
        $center = new TrackPoint(40.2, -0.2, 0);
        $track = new GPX($trackName, $center, [$point], 0, 0);
        $trackId = new GPX($trackName, $center, [$point], 0, 0);
        $trackId->setId(2);

        $gpxRepository->shouldReceive("persist")->with($track)
            ->andReturn($trackId);

        $data = [
            "name" => $trackName,
            "file" => $fileGPX
        ];
        $response = $this->postJson('/api/v1/gpx-add', $data);

        $response->assertStatus(200);
        $response->assertSee(2);
    }
}
