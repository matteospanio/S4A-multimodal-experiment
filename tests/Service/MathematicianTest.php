<?php

namespace App\Tests\Service;

use App\Entity\Stimulus\Flavor;
use App\Entity\Stimulus\Song;
use App\Repository\FlavorToMusicTrialRepository;
use App\Repository\MusicToFlavorTrialRepository;
use App\Service\Mathematician;
use PHPUnit\Framework\TestCase;

class MathematicianTest extends TestCase
{
    private Mathematician $mathematician;
    private MusicToFlavorTrialRepository $musicToFlavorTrialRepository;
    private FlavorToMusicTrialRepository $flavorToMusicTrialRepository;

    protected function setUp(): void
    {
        $this->musicToFlavorTrialRepository = $this->createMock(MusicToFlavorTrialRepository::class);
        $this->flavorToMusicTrialRepository = $this->createMock(FlavorToMusicTrialRepository::class);

        $this->mathematician = new Mathematician(
            $this->flavorToMusicTrialRepository,
            $this->musicToFlavorTrialRepository
        );
    }

    public function testGetMusicToFlavorStatisticsWithNoData(): void
    {
        $flavor = $this->createMock(Flavor::class);
        $flavor->method('getId')->willReturn(1);

        $this->musicToFlavorTrialRepository
            ->method('getChoiceStatisticsByFlavor')
            ->with(1)
            ->willReturn([]);

        $this->musicToFlavorTrialRepository
            ->method('countTrialsByFlavor')
            ->with(1)
            ->willReturn(0);

        $result = $this->mathematician->getMusicToFlavorStatistics($flavor);

        $this->assertEmpty($result['labels']);
        $this->assertEmpty($result['data']);
        $this->assertEmpty($result['backgroundColors']);
        $this->assertEmpty($result['borderColors']);
        $this->assertNull($result['expectedSongId']);
    }

    public function testGetFlavorToMusicStatisticsWithNoData(): void
    {
        $song = $this->createMock(Song::class);
        $song->method('getId')->willReturn(1);

        $this->flavorToMusicTrialRepository
            ->method('getChoiceStatisticsBySong')
            ->with(1)
            ->willReturn([]);

        $this->flavorToMusicTrialRepository
            ->method('countTrialsBySong')
            ->with(1)
            ->willReturn(0);

        $result = $this->mathematician->getFlavorToMusicStatistics($song);

        $this->assertEmpty($result['labels']);
        $this->assertEmpty($result['data']);
        $this->assertEmpty($result['backgroundColors']);
        $this->assertEmpty($result['borderColors']);
        $this->assertNull($result['expectedFlavorId']);
    }

    public function testGetMusicToFlavorStatisticsWithData(): void
    {
        $flavor = $this->createMock(Flavor::class);
        $flavor->method('getId')->willReturn(1);
        
        $expectedSong = $this->createMock(Song::class);
        $expectedSong->method('getId')->willReturn(10);
        
        $songs = new \Doctrine\Common\Collections\ArrayCollection([$expectedSong]);
        $flavor->method('getSongs')->willReturn($songs);

        $mockStats = [
            [
                'choice_id' => '10',
                'song_id' => 10,
                'choice_flavor_name' => 'Vanilla',
                'count' => 8
            ],
            [
                'choice_id' => '11',
                'song_id' => 11,
                'choice_flavor_name' => 'Chocolate',
                'count' => 2
            ]
        ];

        $this->musicToFlavorTrialRepository
            ->method('getChoiceStatisticsByFlavor')
            ->with(1)
            ->willReturn($mockStats);

        $this->musicToFlavorTrialRepository
            ->method('countTrialsByFlavor')
            ->with(1)
            ->willReturn(10);

        $result = $this->mathematician->getMusicToFlavorStatistics($flavor);

        $this->assertCount(2, $result['labels']);
        $this->assertEquals(['Song #10 (Vanilla)', 'Song #11 (Chocolate)'], $result['labels']);
        $this->assertEquals([80.0, 20.0], $result['data']);
        $this->assertEquals(10, $result['expectedSongId']);
        
        // First item should be highlighted (red) as it's the expected song
        $this->assertEquals('rgba(255, 99, 132, 0.6)', $result['backgroundColors'][0]);
        $this->assertEquals('rgba(54, 162, 235, 0.6)', $result['backgroundColors'][1]);
    }
}