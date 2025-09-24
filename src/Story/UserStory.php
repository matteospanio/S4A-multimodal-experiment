<?php

declare(strict_types=1);

namespace App\Story;

use App\Factory\UserFactory;
use Zenstruck\Foundry\Story;

final class UserStory extends Story
{
    const string DEFAULT_PASSWORD = 'password';

    public function build(): void
    {
        $this->addState('admin', UserFactory::new(['username' => 'admin', 'password' => self::DEFAULT_PASSWORD])->asAdmin());
        $this->addState('user', UserFactory::new(['username' => 'user', 'password' => self::DEFAULT_PASSWORD])->asUser());
    }
}
