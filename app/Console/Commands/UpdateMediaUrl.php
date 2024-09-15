<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class UpdateMediaUrl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:update-url';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        foreach (Media::all() as $media) {

            if ($media->hasCustomProperty('url')) {
                $url = $media->getCustomProperty('url');
                $exploded = explode('/', $url);
                $media->setCustomProperty('name', end($exploded));
                $media->forgetCustomProperty('url');
                $media->save();
                $this->info('Media url updated');
            }
        }
    }
}
