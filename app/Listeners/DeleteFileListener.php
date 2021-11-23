<?php

namespace App\Listeners;
use App\Events\DeleteFileEvent;
use Illuminate\Support\Facades\File;

class DeleteFileListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  DeleteFileEvent  $event
     * @return void
     */
    public function handle(DeleteFileEvent $event)
    {
        $fullfileName= storage_path(). '/app/public/'.$event->fileName;
        if(File::exists($fullfileName))
        {
            File::delete(storage_path(). $fullfileName);
        }
    }
}
