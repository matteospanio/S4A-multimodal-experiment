<?php

namespace App\Tests\Controller;

use App\Entity\Stimulus\Flavor;
use App\Entity\Stimulus\Song;
use App\Entity\Trial\FlavorToMusicTrial;
use App\Entity\Trial\MusicToFlavorTrial;
use App\Factory\Stimulus\FlavorFactory;
use App\Factory\Stimulus\SongFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class TaskControllerSubmissionTest extends WebTestCase
{
    use ResetDatabase, Factories;

    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $client = self::createClient();
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();

        // Create test data
        $flavor1 = FlavorFactory::createOne(['name' => 'Vanilla', 'icon' => '🍦']);
        $flavor2 = FlavorFactory::createOne(['name' => 'Rose', 'icon' => '🌹']);
        
        $song1 = SongFactory::createOne(['url' => 'test1.wav', 'prompt' => 'Song 1', 'flavor' => $flavor1]);
        $song2 = SongFactory::createOne(['url' => 'test2.wav', 'prompt' => 'Song 2', 'flavor' => $flavor2]);
    }

    public function testMusicToFlavorTaskFormSubmission(): void
    {
        $client = self::createClient();
        
        // Get the task page
        $crawler = $client->request('GET', '/task/1');
        $this->assertResponseIsSuccessful();

        // Find the form
        $form = $crawler->selectButton('Submit Choice')->form();
        
        // Get available songs from the page
        $songChoice = $crawler->filter('[data-stimulus-selector-choice-value]')->first()->attr('data-stimulus-selector-choice-value');
        $this->assertNotEmpty($songChoice);

        // Submit the form with a choice
        $form['choice'] = $songChoice;
        $client->submit($form);

        // Should redirect to a new trial
        $this->assertResponseRedirects('/task/1');
        
        // Follow the redirect
        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        // Check that the choice was saved in the database
        $trials = $this->entityManager->getRepository(MusicToFlavorTrial::class)->findAll();
        $this->assertNotEmpty($trials);
        
        $trial = $trials[0];
        $this->assertNotNull($trial->getChoice());
        $this->assertEquals($songChoice, $trial->getChoice()->getId());
    }

    public function testFlavorToMusicTaskFormSubmission(): void
    {
        $client = self::createClient();
        
        // Get the flavor to music task page
        $crawler = $client->request('GET', '/task/2');
        $this->assertResponseIsSuccessful();

        // Find the form
        $form = $crawler->selectButton('Submit Choice')->form();
        
        // Get available flavors from the page
        $flavorChoice = $crawler->filter('[data-stimulus-selector-choice-value]')->first()->attr('data-stimulus-selector-choice-value');
        $this->assertNotEmpty($flavorChoice);

        // Submit the form with a choice
        $form['choice'] = $flavorChoice;
        $client->submit($form);

        // Should redirect to a new trial
        $this->assertResponseRedirects('/task/2');
        
        // Follow the redirect
        $client->followRedirect();
        $this->assertResponseIsSuccessful();

        // Check that the choice was saved in the database
        $trials = $this->entityManager->getRepository(FlavorToMusicTrial::class)->findAll();
        $this->assertNotEmpty($trials);
        
        $trial = $trials[0];
        $this->assertNotNull($trial->getChoice());
        $this->assertEquals($flavorChoice, $trial->getChoice()->getId());
    }
}