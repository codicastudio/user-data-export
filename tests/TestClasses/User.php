<?php

namespace Spatie\PersonalDataExport\Tests\TestClasses;

use Illuminate\Foundation\Auth\User as BaseUser;
use Illuminate\Support\Str;
use Spatie\PersonalDataExport\ExportsPersonalData;
use Spatie\PersonalDataExport\PersonalDataSelection;

class User extends BaseUser implements ExportsPersonalData
{
    public function selectPersonalData(PersonalDataSelection $personalData): void
    {
        $personalData
            ->addFile(__DIR__.'/../stubs/avatar.png')
            ->addFile('thumbnail.png', 'user-disk')
            ->add('attributes.json', $this->attributesToArray());
    }

    public function personalDataExportName(): string
    {
        $usernameSlug = Str::slug($this->username);

        return "personal-data-{$usernameSlug}.zip";
    }
}
