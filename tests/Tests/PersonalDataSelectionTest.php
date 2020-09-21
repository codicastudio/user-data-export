<?php

namespace Spatie\PersonalDataExport\Tests\Tests;

use Illuminate\Support\Facades\Storage;
use Spatie\PersonalDataExport\Exceptions\CouldNotAddToPersonalDataSelection;
use Spatie\PersonalDataExport\PersonalDataSelection;
use Spatie\PersonalDataExport\Tests\TestCase;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class PersonalDataSelectionTest extends TestCase
{
    /** @var \Spatie\TemporaryDirectory\TemporaryDirectory */
    protected $temporaryDirectory;

    /** @var \Spatie\PersonalDataExport\PersonalDataSelection */
    protected $personalDataSelection;

    public function setUp(): void
    {
        parent::setUp();

        $this->temporaryDirectory = (new TemporaryDirectory())->create();

        $this->personalDataSelection = new PersonalDataSelection($this->temporaryDirectory);
    }

    /** @test */
    public function it_can_add_a_string_as_content()
    {
        $this->personalDataSelection->add('my-content.txt', 'this is my content');

        $this->assertFileContents($this->temporaryDirectory->path('my-content.txt'), 'this is my content');
    }

    /** @test */
    public function it_can_add_an_array_as_content()
    {
        $this->personalDataSelection->add('my-content.txt', ['key' => 'value']);

        $this->assertFileContents($this->temporaryDirectory->path('my-content.txt'), json_encode(['key' => 'value'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    /** @test */
    public function it_can_copy_a_local_file_to_the_personal_data()
    {
        $avatarPath = $this->getStubPath('avatar.png');

        $this->personalDataSelection->addFile($avatarPath);

        $this->assertFileExists($this->temporaryDirectory->path('avatar.png'));
        $this->assertFileExists($avatarPath);
    }

    /** @test */
    public function it_can_copy_a_file_from_a_disk_to_the_personal_data_temporary_directory()
    {
        $disk = Storage::fake('test-disk');

        $disk->put('my-file.txt', 'my content');

        $this->personalDataSelection->addFile('my-file.txt', 'test-disk');

        $this->assertFileContents($this->temporaryDirectory->path('my-file.txt'), 'my content');
    }

    /** @test */
    public function it_will_not_allow_an_entry_in_the_personal_data_to_be_overwritten()
    {
        $this->personalDataSelection->add('test.txt', 'test content');

        $this->expectException(CouldNotAddToPersonalDataSelection::class);

        $this->personalDataSelection->add('test.txt', 'test content');
    }
}
