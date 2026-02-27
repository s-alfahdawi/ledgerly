<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Services\AccountContext;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function __construct(
        private AccountContext $accountContext
    ) {
    }

    public function show(Attachment $attachment)
    {
        $accountId = $this->requireAccountId();
        if ($attachment->account_id !== $accountId) {
            abort(403);
        }

        if (!Storage::disk('local')->exists($attachment->disk_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('local')->download(
            $attachment->disk_path,
            $attachment->filename
        );
    }

    public function destroy(Attachment $attachment)
    {
        $accountId = $this->requireAccountId();
        if ($attachment->account_id !== $accountId) {
            abort(403);
        }

        Storage::disk('local')->delete($attachment->disk_path);
        $attachment->delete();

        return back()->with('success', 'Attachment deleted.');
    }
}
