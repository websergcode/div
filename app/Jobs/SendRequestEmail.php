<?php

namespace App\Jobs;

use App\Models\UserApplicationForm\UserApplicationForm;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SendRequestEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected UserApplicationForm $userApplicationForm;
    protected string $comment;

    public function __construct(UserApplicationForm $userApplicationForm, string $comment)
    {
        $this->userApplicationForm = $userApplicationForm;
        $this->comment = $comment;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $filePath = 'temp/comment_' . time() . '.txt';
        Storage::disk('local')->put($filePath, $this->comment);

        $emailData = [
            'comment' => $this->comment,
        ];

        Mail::fake();
        Mail::send('emails.comment', $emailData, function ($message) use ($filePath) {
            $message->to($this->userApplicationForm->email)
                ->subject('New Comment')
                ->attach($filePath);
        });

        Storage::disk('local')->delete($filePath);
    }
}
