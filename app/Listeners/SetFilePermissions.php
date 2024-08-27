<?php

namespace App\Listeners;

use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAdded;

/**
 * Class SetFilePermissions
 */
class SetFilePermissions
{
    /**
     * Handle the event.
     */
    public function handle(MediaHasBeenAdded $event): void
    {
        $media = $event->media;
        $path = $media->getPath(); // Get the full path to the file

        // Set the correct permissions
        chmod($path, 0664); // Adjust the permissions as needed
    }
}
