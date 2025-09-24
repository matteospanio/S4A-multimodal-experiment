<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Stimulus\Flavor;
use App\Entity\Stimulus\Song;
use App\Entity\Trial\MusicToFlavorTrial;
use App\Repository\FlavorToMusicTrialRepository;
use App\Repository\MusicToFlavorTrialRepository;
use App\Service\Mathematician;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

final class MathematicianTest extends TestCase
{
    private Mathematician $mathematician;

    private \PHPUnit\Framework\MockObject\MockObject $musicToFlavorTrialRepository;

    private \PHPUnit\Framework\MockObject\MockObject $flavorToMusicTrialRepository;

    protected function setUp(): void
    {
        $this->musicToFlavorTrialRepository = $this->createMock(MusicToFlavorTrialRepository::class);
        $this->flavorToMusicTrialRepository = $this->createMock(FlavorToMusicTrialRepository::class);

        $this->mathematician = new Mathematician(
            $this->flavorToMusicTrialRepository,
            $this->musicToFlavorTrialRepository
        );
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

    public function testGetMusicToFlavorStatisticsWithTrialFiltering(): void
    {
        $flavor = $this->createMock(Flavor::class);
        $flavor->method('getId')->willReturn(1);

        // Create mock trial with specific songs
        $trial = $this->createMock(MusicToFlavorTrial::class);
        $song1 = $this->createMock(Song::class);
        $song1->method('getId')->willReturn(10);
        $song1Flavor = $this->createMock(Flavor::class);
        $song1Flavor->method('getId')->willReturn(1); // Same as trial flavor
        $song1->method('getFlavor')->willReturn($song1Flavor);

        $song2 = $this->createMock(Song::class);
        $song2->method('getId')->willReturn(11);
        $song2Flavor = $this->createMock(Flavor::class);
        $song2Flavor->method('getId')->willReturn(2); // Different from trial flavor
        $song2->method('getFlavor')->willReturn($song2Flavor);

        $trialSongs = new ArrayCollection([$song1, $song2]);
        $trial->method('getSongs')->willReturn($trialSongs);

        // Mock stats filtered by trial songs
        $mockTrialStats = [
            [
                'choice_id' => '10',
                'song_id' => 10,
                'choice_flavor_name' => 'Vanilla',
                'count' => 6
            ],
            [
                'choice_id' => '11',
                'song_id' => 11,
                'choice_flavor_name' => 'Chocolate',
                'count' => 4
            ]
        ];

        $this->musicToFlavorTrialRepository
            ->method('getChoiceStatisticsByFlavorAndSongs')
            ->with(1, [10, 11])
            ->willReturn($mockTrialStats);

        $this->musicToFlavorTrialRepository
            ->method('countTrialsByFlavorAndSongs')
            ->with(1, [10, 11])
            ->willReturn(10);

        $result = $this->mathematician->getMusicToFlavorStatistics($flavor, $trial);

        $this->assertCount(2, $result['labels']);
        $this->assertEquals(['Song #10 (Vanilla)', 'Song #11 (Chocolate)'], $result['labels']);
        $this->assertEquals([60.0, 40.0], $result['data']);
        $this->assertEquals(10, $result['expectedSongId']);

        // First item should be highlighted (red) as it's the expected song
        $this->assertEquals('rgba(255, 99, 132, 0.6)', $result['backgroundColors'][0]);
        $this->assertEquals('rgba(54, 162, 235, 0.6)', $result['backgroundColors'][1]);
    }

    public function testGetMusicToFlavorStatisticsWithTrialFilteringAndNoData(): void
    {
        $flavor = $this->createMock(Flavor::class);
        $flavor->method('getId')->willReturn(1);

        // Create mock trial with specific songs
        $trial = $this->createMock(MusicToFlavorTrial::class);
        $song1 = $this->createMock(Song::class);
        $song1->method('getId')->willReturn(10);
        $song1Flavor = $this->createMock(Flavor::class);
        $song1Flavor->method('getName')->willReturn('Vanilla');
        $song1Flavor->method('getId')->willReturn(1); // Same as trial flavor
        $song1->method('getFlavor')->willReturn($song1Flavor);

        $song2 = $this->createMock(Song::class);
        $song2->method('getId')->willReturn(11);
        $song2Flavor = $this->createMock(Flavor::class);
        $song2Flavor->method('getName')->willReturn('Chocolate');
        $song2Flavor->method('getId')->willReturn(2); // Different from trial flavor
        $song2->method('getFlavor')->willReturn($song2Flavor);

        $trialSongs = new ArrayCollection([$song1, $song2]);
        $trial->method('getSongs')->willReturn($trialSongs);

        // Mock empty stats (no trials yet)
        $this->musicToFlavorTrialRepository
            ->method('getChoiceStatisticsByFlavorAndSongs')
            ->with(1, [10, 11])
            ->willReturn([]);

        $this->musicToFlavorTrialRepository
            ->method('countTrialsByFlavorAndSongs')
            ->with(1, [10, 11])
            ->willReturn(0);

        $result = $this->mathematician->getMusicToFlavorStatistics($flavor, $trial);

        // Should show both songs with 0% data
        $this->assertCount(2, $result['labels']);
        $this->assertEquals(['Song #10 (Vanilla)', 'Song #11 (Chocolate)'], $result['labels']);
        $this->assertEquals([0.0, 0.0], $result['data']);
        $this->assertEquals(10, $result['expectedSongId']);

        // First item should be highlighted (red) as it's the expected song
        $this->assertEquals('rgba(255, 99, 132, 0.6)', $result['backgroundColors'][0]);
        $this->assertEquals('rgba(54, 162, 235, 0.6)', $result['backgroundColors'][1]);
    }

    public function testGetMusicToFlavorStatisticsWithSecondSongExpected(): void
    {
        $flavor = $this->createMock(Flavor::class);
        $flavor->method('getId')->willReturn(2); // Different flavor ID

        // Create mock trial with specific songs where second song matches the flavor
        $trial = $this->createMock(MusicToFlavorTrial::class);
        $song1 = $this->createMock(Song::class);
        $song1->method('getId')->willReturn(10);
        $song1Flavor = $this->createMock(Flavor::class);
        $song1Flavor->method('getId')->willReturn(1); // Different from trial flavor
        $song1->method('getFlavor')->willReturn($song1Flavor);

        $song2 = $this->createMock(Song::class);
        $song2->method('getId')->willReturn(11);
        $song2Flavor = $this->createMock(Flavor::class);
        $song2Flavor->method('getId')->willReturn(2); // Same as trial flavor
        $song2->method('getFlavor')->willReturn($song2Flavor);

        $trialSongs = new ArrayCollection([$song1, $song2]);
        $trial->method('getSongs')->willReturn($trialSongs);

        // Mock stats filtered by trial songs
        $mockTrialStats = [
            [
                'choice_id' => '10',
                'song_id' => 10,
                'choice_flavor_name' => 'Vanilla',
                'count' => 3
            ],
            [
                'choice_id' => '11',
                'song_id' => 11,
                'choice_flavor_name' => 'Chocolate',
                'count' => 7
            ]
        ];

        $this->musicToFlavorTrialRepository
            ->method('getChoiceStatisticsByFlavorAndSongs')
            ->with(2, [10, 11])
            ->willReturn($mockTrialStats);

        $this->musicToFlavorTrialRepository
            ->method('countTrialsByFlavorAndSongs')
            ->with(2, [10, 11])
            ->willReturn(10);

        $result = $this->mathematician->getMusicToFlavorStatistics($flavor, $trial);

        $this->assertCount(2, $result['labels']);
        $this->assertEquals(['Song #10 (Vanilla)', 'Song #11 (Chocolate)'], $result['labels']);
        $this->assertEquals([30.0, 70.0], $result['data']);
        $this->assertEquals(11, $result['expectedSongId']); // Second song should be expected

        // Second item should be highlighted (red) as it's the expected song
        $this->assertEquals('rgba(54, 162, 235, 0.6)', $result['backgroundColors'][0]);
        $this->assertEquals('rgba(255, 99, 132, 0.6)', $result['backgroundColors'][1]);
    }
}
