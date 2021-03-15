<?php

namespace App\Observers;

use App\Discussion;
use App\Events\DiscussionEvent;
use App\Events\NewUserEvent;
use App\Notifications\NewDiscussion;
use App\User;

class DiscussionObserver
{
    public function saving(Discussion $discussion)
    {
        if (!isRunningInConsoleOrSeeding()) {
            if (company()) {
                $discussion->company_id = company()->id;
            }
        }
    }

    public function created(Discussion $discussion)
    {
        if (!isRunningInConsoleOrSeeding()) {
            //gửi thông báo khi tạo discussion
            //lỗi ko gửi mail đc nên tạm thời đóng lại -Trí
            // phải config cái mail env mới chạy được.
            event(new DiscussionEvent($discussion));
        }
    }
}
