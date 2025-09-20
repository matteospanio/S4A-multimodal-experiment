<?php

namespace App\Tests\Service;

use App\Entity\Stimulus\Flavor;
use App\Entity\Stimulus\Song;
use App\Entity\Trial\Trial;
use App\Repository\FlavorRepository;
use App\Repository\SongRepository;
use App\Service\StimuliManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class StimuliManagerTest extends TestCase
{
    private StimuliManager $stimuliManager;
    private FlavorRepository|MockObject $flavorRepository;
    private SongRepository|MockObject $songRepository;

    protected function setUp(): void
    {
        $this->flavorRepository = $this->createMock(FlavorRepository::class);
        $this->songRepository = $this->createMock(SongRepository::class);
        $this->em = $this->createMock(EntityManagerInterface::class);

        $this->stimuliManager = new StimuliManager(
            $this->flavorRepository,
            $this->songRepository,
            $this->em,
        );
    }

    public function testGetNextTrialWithSmells2MusicTaskType(): void
    {
        // Create mock flavors
        $flavor1 = $this->createMockFlavor(1, 'Vanilla', 'vanilla.png');
        $flavor2 = $this->createMockFlavor(2, 'Chocolate', 'chocolate.png');
        $flavors = [$flavor1, $flavor2];

        // Create mock song
        $song = $this->createMockSong(1, 'http://example.com/song1.mp3', 'A sweet melody');

        $this->flavorRepository
            ->method('findAll')
            ->willReturn($flavors);

        $this->songRepository
            ->method('findBy')
            ->willReturn([$song]);

        $result = $this->stimuliManager->getNextTrial(Trial::SMELLS2MUSIC);

        $this->assertEquals(Trial::SMELLS2MUSIC, $result['taskType']);
        $this->assertInstanceOf(Song::class, $result['primarySong']);
        $this->assertInstanceOf(Flavor::class, $result['primaryFlavor']);
        $this->assertInstanceOf(Flavor::class, $result['secondaryFlavor']);
        $this->assertIsArray($result['flavorLabels']);
        $this->assertCount(2, $result['flavorLabels']);
        $this->assertArrayHasKey('combination', $result); // New balanced feature
    }

    public function testGetNextTrialWithMusics2SmellTaskType(): void
    {
        // Create mock flavors
        $flavor1 = $this->createMockFlavor(1, 'Vanilla', 'vanilla.png');
        $flavor2 = $this->createMockFlavor(2, 'Chocolate', 'chocolate.png');
        $flavors = [$flavor1, $flavor2];

        // Create mock songs
        $song1 = $this->createMockSong(1, 'http://example.com/song1.mp3', 'A sweet melody');
        $song2 = $this->createMockSong(2, 'http://example.com/song2.mp3', 'A rich harmony');

        $this->flavorRepository
            ->method('findAll')
            ->willReturn($flavors);

        $this->songRepository
            ->method('findBy')
            ->willReturnOnConsecutiveCalls([$song1], [$song2]);

        $result = $this->stimuliManager->getNextTrial(Trial::MUSICS2SMELL);

        $this->assertEquals(Trial::MUSICS2SMELL, $result['taskType']);
        $this->assertInstanceOf(Song::class, $result['primarySong']);
        $this->assertInstanceOf(Song::class, $result['secondarySong']);
        $this->assertInstanceOf(Flavor::class, $result['primaryFlavor']);
        $this->assertInstanceOf(Flavor::class, $result['secondaryFlavor']);
        $this->assertIsArray($result['songLabels']);
        $this->assertCount(2, $result['songLabels']);
        $this->assertArrayHasKey('combination', $result); // New balanced feature
    }

    public function testGetNextTrialThrowsExceptionWithInsufficientFlavors(): void
    {
        $this->flavorRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('At least 2 flavors are required for trial generation.');

        $this->stimuliManager->getNextTrial(Trial::SMELLS2MUSIC);
    }

    public function testGetNextTrialThrowsExceptionWithInvalidTaskType(): void
    {
        $flavor1 = $this->createMockFlavor(1, 'Vanilla', 'vanilla.png');
        $flavor2 = $this->createMockFlavor(2, 'Chocolate', 'chocolate.png');

        $this->flavorRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$flavor1, $flavor2]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid task type: invalid_task');

        $this->stimuliManager->getNextTrial('invalid_task');
    }

    public function testGetAllPossibleCombinations(): void
    {
        $flavor1 = $this->createMockFlavor(1, 'Vanilla', 'vanilla.png');
        $flavor2 = $this->createMockFlavor(2, 'Chocolate', 'chocolate.png');
        $flavor3 = $this->createMockFlavor(3, 'Strawberry', 'strawberry.png');
        $flavor4 = $this->createMockFlavor(4, 'Mint', 'mint.png');

        $this->flavorRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn([$flavor1, $flavor2, $flavor3, $flavor4]);

        $combinations = $this->stimuliManager->getAllPossibleCombinations($this->flavorRepository->findAll());

        // With 4 flavors, we should have 4 * 3 = 12 combinations (not 16 as mentioned in requirements)
        // because we exclude same-flavor combinations
        $this->assertCount(12, $combinations);

        foreach ($combinations as $combination) {
            $this->assertArrayHasKey('primary', $combination);
            $this->assertArrayHasKey('secondary', $combination);
            $this->assertNotEquals($combination['primary'], $combination['secondary']);
        }
    }

    public function testSmells2MusicTrialThrowsExceptionWhenNoSongsFound(): void
    {
        $flavor1 = $this->createMockFlavor(1, 'Vanilla', 'vanilla.png');
        $flavor2 = $this->createMockFlavor(2, 'Chocolate', 'chocolate.png');

        $this->flavorRepository
            ->method('findAll')
            ->willReturn([$flavor1, $flavor2]);

        $this->songRepository
            ->method('findBy')
            ->willReturn([]);

        $this->expectException(\RuntimeException::class);

        $this->stimuliManager->getNextTrial(Trial::SMELLS2MUSIC);
    }

    public function testBalancedCombinationDistribution(): void
    {
        // Create 4 mock flavors as in requirements
        $flavor1 = $this->createMockFlavor(1, 'Vanilla', 'vanilla.png');
        $flavor2 = $this->createMockFlavor(2, 'Chocolate', 'chocolate.png');
        $flavor3 = $this->createMockFlavor(3, 'Strawberry', 'strawberry.png');
        $flavor4 = $this->createMockFlavor(4, 'Mint', 'mint.png');
        $flavors = [$flavor1, $flavor2, $flavor3, $flavor4];

        // Create mock songs for each flavor
        $song1 = $this->createMockSong(1, 'http://example.com/song1.mp3', 'Sweet melody');
        $song2 = $this->createMockSong(2, 'http://example.com/song2.mp3', 'Rich harmony');

        $this->flavorRepository
            ->method('findAll')
            ->willReturn($flavors);

        $this->songRepository
            ->method('findBy')
            ->willReturn([$song1]); // Return at least one song for any flavor

        // Test multiple trials to ensure different combinations are generated
        $combinationsGenerated = [];
        for ($i = 0; $i < 10; $i++) {
            $result = $this->stimuliManager->getNextTrial(Trial::SMELLS2MUSIC);

            $this->assertArrayHasKey('combination', $result);
            $combination = $result['combination'];
            $this->assertArrayHasKey('primary', $combination);
            $this->assertArrayHasKey('secondary', $combination);
            $this->assertNotEquals($combination['primary'], $combination['secondary']);

            $combKey = $combination['primary'] . '-' . $combination['secondary'];
            $combinationsGenerated[$combKey] = true;
        }

        // Should generate multiple different combinations over 10 trials
        $this->assertGreaterThan(1, count($combinationsGenerated));
    }

    private function createMockFlavor(int $id, string $name, string $icon): Flavor|MockObject
    {
        $flavor = $this->createMock(Flavor::class);
        $flavor->method('getId')->willReturn($id);
        $flavor->method('getName')->willReturn($name);
        $flavor->method('getIcon')->willReturn($icon);

        return $flavor;
    }

    private function createMockSong(int $id, string $url, string $prompt): Song|MockObject
    {
        $song = $this->createMock(Song::class);
        $song->method('getId')->willReturn($id);
        $song->method('getUrl')->willReturn($url);
        $song->method('getPrompt')->willReturn($prompt);

        return $song;
    }
}
