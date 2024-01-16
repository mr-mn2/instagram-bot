<?php
include "bootstrap/config.php";
include "vendor/autoload.php";
$tg = new App\Telegram\telegramBot(BOT_TOKEN);
$t_helpers = new App\helpers\telegramHelpers(BOT_TOKEN);
$users = new App\Model\users();
$keyboards = new App\helpers\keyboards();
$pages = new App\Model\pages();
$screenshots = new App\Model\screenshots();
$messages = new \App\Model\messages();
$admins = new \App\Model\admins();
$banners = new App\Model\banners();
$updates = $tg->getWebhookUpdates();
$helpers = new App\helpers\helpers(BOT_TOKEN);
//file_put_contents('result.txt', file_get_contents('php://input') . PHP_EOL . PHP_EOL, FILE_APPEND);

$page_limit = 10;
if (isset($updates->callback_query)) {
    $user_id = $updates->callback_query->from->id ?? null;
    $text = $updates->callback_query->data ?? null;
    $callback_query_id = $updates->callback_query->id;
    $message_id = $updates->callback_query->message->message_id;

} elseif (isset($updates->message)) {
    $text = $updates->message->text ?? null;
    $video = isset($updates->message->video) ? $updates->message->video->file_id : null;
    $image = isset($updates->message->photo) ? $updates->message->photo[1]->file_id : null;
    $description = $updates->message->caption ?? null;
    $message_id = $updates->message->message_id;
    if (!empty($updates->message->from)) {
        $user_id = $updates->message->from->id ?? null;
        $first_name = $updates->message->from->first_name ?? null;
        $username = $updates->message->from->username ?? null;
    }
    if (!empty($updates->message->chat)) {
        $chat_id = $updates->message->chat->id ?? null;
    }
    if (!empty($updates->message->audio)) {
        $Music_duration = $updates->message->audio->duration;
        $musicName = isset($updates->message->audio->title) ? $updates->message->audio->title : $updates->message->audio->file_name;
        $performer = $updates->message->audio->performer ?? null;
        $music_file_id = $updates->message->audio->file_id;
    }
}
if ($users->select($user_id)->is_banned) {
    $tg->sendMessage($user_id, 'شما بن شدید❌');
    die();
}
$admin_is_register = $admins->is_register($user_id);
if ($user_id == $main_admin) {
    $admin_keyboard = [
        ['افزودن ادمین جدید💤'],
        ['افزودن بنر 🌄', 'پیام به همه📤'],
        ['ویرایش اطلاعات کاربر👤'],
        ['تعداد کاربران ربات🏹'],
        ['بازگشت🔙'],

    ];
} elseif ($admin_is_register) {

    $admin_keyboard = $keyboards->editAdminKeyboard($user_id);

}

if (isset($updates->message->reply_to_message)) {
    $scrInfo = $screenshots->selectWithMsgID($updates->message->reply_to_message->message_id);
    if (empty($scrInfo)) {
        $msgInfo = $messages->selectByMessageID($updates->message->reply_to_message->message_id);
        if (empty($msgInfo)) {
            die();
        }
        $smsg = $tg->sendMessage($msgInfo->sender, $text, null, null, true, $msgInfo->msg_id_in_sender_chat);
        if ($smsg['ok']) {
            $tg->sendMessage($main_admin, 'پیام شما به کاربر ارسال شد');
        }
        die();
    }
    $sUserID = $scrInfo->sender_id;
    if (!is_numeric($text)) {
        $tg->sendMessage($user_id, 'مقدار' . $text . ' مقدار به عدد');
        die();
    }
    $uINfo = $users->select($sUserID);
    if ($uINfo->inviter !== false) {
        $percentage = ($text / 10);
        $inviter = $uINfo->inviter;
        $charge = $users->update($inviter, 'cash', $users->select($inviter)->cash + $percentage);
        if ($charge) {
            $tg->sendMessage($inviter, "تبریک میگم😍

یکی از زیر مجموعه های شما مبلغ $text تومن را برداشت کرد و کیف پول شما $percentage تومن شارژ شد🏹");

        }

    }

    $users->update($sUserID, 'cash', ($uINfo->cash + $text));
    $tg->sendMessage($user_id, 'مقدار' . $text . ' با موفقیت به کیف پول کاربر اضافه شد');
    $tg->sendMessage($sUserID, 'تبریک میگم کیف پول شما شارژ شد');
    die();
}
//codes
if (strpos($text, '/start') !== false) {
    if ($text == '/start') {
        if (!$users->is_register($user_id)) {
            $users->insert_new_user($user_id, $first_name, $username, 'start');
        } else {
            $users->update($user_id, 'position', 'start');
        }

        $reply_markup = $tg->replyKeyboardMarkup($keyboards->MainMenuKeyboard($user_id), true, true);
        $MainMenuMsg = 'سلام ' . $first_name . ' عزیز ، به گسترده ویوگیر خوش آمدید.';
        $tg->sendMessage($user_id, $MainMenuMsg, $tg->replyKeyboardMarkup($keyboards->MainMenuKeyboard($user_id)));
        die();
    } else {
        $inviterID = substr($text, 7);
        is_numeric($text) and die();
        if ($users->is_joined($user_id) and !($users->select($user_id)->inviter)) {
            $tg->sendMessage($inviterID, "کاربر $first_name با لینک دعوت شما به ربات پیوست ");
            $users->update($user_id, 'inviter', $inviterID);
        }
        if (!$users->is_register($user_id)) {
            $users->insert_new_user($user_id, $first_name, $username, 'start');
        } else {
            $users->update($user_id, 'position', 'start');
        }

        $reply_markup = $tg->replyKeyboardMarkup($keyboards->MainMenuKeyboard($user_id), true, true);
        $MainMenuMsg = 'سلام ' . $first_name . ' عزیز ، به گسترده ویوگیر خوش آمدید.';
        $tg->sendMessage($user_id, $MainMenuMsg, $tg->replyKeyboardMarkup($keyboards->MainMenuKeyboard($user_id)));
        die();
    }
    die();
}
if (isset($updates->callback_query)) {
    if ($user_id == $main_admin) {
        switch ($text) {
            case strpos($text, 'accsessing@') !== false:
                $ex = explode('@', $text);
                $rule = $ex[1];
                $newAdminUserID = $ex[2];
                if ($rule == 'done') {
                    $tg->sendMessage($newAdminUserID, 'ادمین قابلیت های شمارا تغییر داد⚠️', $tg->replyKeyboardMarkup($keyboards->editAdminKeyboard($newAdminUserID)));
                    $users->update($newAdminUserID, 'position', 'admin');
                    $tg->answer_query($callback_query_id, 'تغییرات با موفقیت اعمال شد✅');
                    $tg->editMessage($user_id, $message_id, 'تغییرات با موفقیت انجام شد');
                    die();
                }
                if ($rule == 'sdel') {
                    $tg->edit_markap($user_id, $message_id, 'از انتخاب خود مطئنید⚠️', $tg->InlineKeyboardMarkup($keyboards->yesnoAccesing($newAdminUserID)));
                    die();
                }
                if ($rule == 'Yes') {
                    $admins->delete($newAdminUserID);
                    $tg->answer_query($callback_query_id, 'تغییرات با موفقیت اعمال شد✅');
                    $tg->editMessage($user_id, $message_id, 'کاربر از لیست ادمین ها حذف گردید');
                    $helpers->backToMainMenu($newAdminUserID);
                    die();
                }
                if ($rule == 'No') {
                    $userInfo = $users->select($newAdminUserID);
                    $uname = $userInfo->username;
                    $fname = $userInfo->name;
                    $msg = "🔵 آیدی عددی کاربر: $newAdminUserID
🔵 یوزرنیم کاربر: @$uname
🔵 نام کاربری : $fname

تنظیم دسترسی ها👇🏻";
                    $tg->edit_markap($user_id, $message_id, $msg, $tg->InlineKeyboardMarkup($keyboards->murkubForAccessing($newAdminUserID)));
                    die();
                }
                if (!$admins->updateRight($newAdminUserID, $rule)) {
                    $tg->answer_query($callback_query_id, 'خطا در ذخیره سازی اطلاعات❌');
                    die();
                }

                $tg->editMessageReplyMarkup($user_id, $message_id, $tg->InlineKeyboardMarkup($keyboards->murkubForAccessing($newAdminUserID)));
                $tg->answer_query($callback_query_id, 'انجام شد✅');
                die();
                break;
            case strpos($text, 'submit_') !== false:
                $pageID = explode('_', $text)[1];
                $pageInformation = $pages->select($pageID);
                $userInfo = $users->select($pageInformation->user_id);
                if (!$userInfo->validated) {
                    $users->update($pageInformation->user_id, 'validated', 1);
                    $tg->sendMessage($pageInformation->user_id, '❇️درخواست عضویت شما تایید شد
    لطفاً قبل از اجرای تبلیغ قوانین را کامل مطالعه کنید', $tg->replyKeyboardMarkup($keyboards->MainMenuKeyboard($pageInformation->user_id)));
                } else {
                    $tg->sendMessage($pageInformation->user_id, 'پیج شما با آیدی' . $pageInformation->link . ' تایید شد✅');
                }
                $pages->update($pageID, 'is_submited', 1);

                $tg->answer_query($callback_query_id, 'صفحه کاربر با موفقیت تایید شد✅');
                $tg->editMessage($main_admin, $message_id, "کاربر  <a href='tg://user?id={$user_id}'>{$user_id}</a> !" . "\nتایید شد که سین بزند");
                die();

            case strpos($text, 'yesBanner_') !== false:
                $bannerID = explode('_', $text)[1];
                $allUsers = $users->selectAll();
                $limit = 20;
                $counter = 1;
                $btn = [
                    [
                        ['text' => 'حذف آگهی❌', 'callback_data' => 'removeBanner_' . $bannerID],

                    ],
                ];
                $tg->editMessageReplyMarkup($user_id, $message_id, $tg->InlineKeyboardMarkup($btn));
                foreach ($allUsers as $user) {
                    if (empty($user)) {
                        continue;
                    }
                    $counter++;
                    if ($counter <= $limit) {
                        $counter = 1;
                        sleep(2);
                    }
                    $tg->sendMessage($user['user_id'], 'تبلیغ جدید آپلود شد برای دریافت روی دکمه دریافت تبلیغ کلیک کنید');

                }
                $tg->answer_query($callback_query_id, 'با موفقیت انجام شد');

                die();

            case strpos($text, 'yesScreen_') !== false:
                $scrID = explode('_', $text)[1];
                $tg->deleteMessage($user_id, $message_id);
                $tg->forceReply();
                $backDat2a = $tg->sendMessage($user_id, 'لطفا مقدار درامد را به تومن روی ریپلای همین پیام ارسال کنید');
                $screenshots->update($scrID, 'msg_id', $backDat2a['result']['message_id']);

                die();

            case strpos($text, 'removeBanner_') !== false:
                $bannerID = explode('_', $text)[1];
                $banners->delete($bannerID);
                $tg->deleteMessage($user_id, $message_id);
                $tg->answer_query($callback_query_id, 'با موفقیت حذف گردید');

                die();
            case strpos($text, 'noBanner_') !== false:
                $bannerID = explode('_', $text)[1];
                $banners->delete($bannerID);
                $tg->deleteMessage($user_id, $message_id);
                die();

            case strpos($text, 'deSubmit_') !== false:
                $pageID = explode('_', $text)[1];
                $pageInformation = $pages->select($pageID);
                $userInfo = $users->select($pageInformation->user_id);
                $tg->sendMessage($pageInformation->user_id, 'پیج شما با آیدی' . $pageInformation->link . ' رد شد');
                $tg->answer_query($callback_query_id, 'پیج با موفقیت رد شد✅');

                break;
            case strpos($text, 'subPay_') !== false:
                $uID = explode('_', $text)[1];
                $UserInformation = $pages->select($uID);
                $users->update($uID, 'cash', 0);
                $tg->sendMessage($uID, 'مبلغ کیف پول شما به حساب شما واریز شد و موجودی کیف پول شما ریست شد✅');
                $tg->editMessage($main_admin, $message_id, 'انجام شد✅');
                die();

            case strpos($text, 'wrPay_') !== false:
                $uID = explode('_', $text)[1];
                $tg->sendMessage($uID, 'اطلاعات حساب شما برای برداشت کیف پول صحیح نبود❌');
                $tg->editMessage($main_admin, $message_id, 'به اطلاع کاربر رسانده شد✅');
                die();

        }
    }
    switch ($text) {
        case strpos($text, 'yesAdd_') !== false:

            $pageID = explode('_', $text)[1];
            $tg->deleteMessage($user_id, $message_id, true);
            $msg2 = '✅  پیج با موفقیت ثبت شد

▫️ لطفا اگر پیج دیگری دارید آدرس آن را طبق یکی از فرمت های زیر وارد کنید

1️⃣ https://instagram.com/instagram

2️⃣ @instagram

❗️اگر تبلیغات را خارج از پیج (های) وارد شده قرار دهید تقلب محسوب شده و درآمدی برای شما لحاظ نمیشود.';
            $tg->sendMessage($user_id, $msg2, $tg->replyKeyboardMarkup($keyboards->NoPagesAndBackKeyboard()));
            $users->update($user_id, 'position', 'pageAddress');
            break;

        case strpos($text, 'noRemove_') !== false:
            $pageID = explode('_', $text)[1];
            $pages->delete($pageID);
            $tg->deleteMessage($user_id, $message_id);
            $tg->sendMessage($user_id, '⛔️  ثبت نام شما در حال حاضر تایید نشده است');
            $helpers->backToMainMenu($user_id);
            break;
        case strpos($text, 'edit_') !== false:
            $pgID = explode('_', $text)[1];
            $pfInfo = $pages->select($pgID);
            $pageLink = 'http://instagram.com/' . substr($pfInfo->link, 1);
            $category = $pfInfo->category;
            $pageName = substr($pfInfo->link, 1);
            $msg = "⚙️مدیریت پیج <a href='" . $pageLink . "'>$pageName</a>
🧰دسته بندی : $category

🔸 لطفا یکی از گزینه های زیر را انتخاب کنید";
            $tg->edit_markap($user_id, $message_id, $msg, $tg->InlineKeyboardMarkup($keyboards->pageEditing($pgID)));
            die();
        case strpos($text, 'delete_') !== false:
            $pgID = explode('_', $text)[1];
            $pfInfo = $pages->select($pgID);
            $pageName = $pfInfo->link;
            $msg = "آیا از حذف شدن پیج $pageName اطمینان حاصل دارید ؟";
            $tg->edit_markap($user_id, $message_id, $msg, $tg->InlineKeyboardMarkup($keyboards->yesNoDelPage($pgID)));
            die();
        case strpos($text, 'yespg_') !== false:
            $pgID = explode('_', $text)[1];
            $pfInfo = $pages->delete($pgID);
            $msg = "صفحه مورد نظر با موفقیت حذف گردید";
            $tg->edit_markap($user_id, $message_id, $msg);
            die();
        case strpos($text, 'category_') !== false:
            $pgID = explode('_', $text)[1];
            $msg = "🧰 دسته بندی پیج خود را از لیست زیر انتخاب کنید";
            $tg->edit_markap($user_id, $message_id, $msg, $tg->InlineKeyboardMarkup($keyboards->categoryEditing($pgID)));
            die();
        case strpos($text, 'dnct_') !== false:
            $ex = explode('_', $text);
            $pageID = $ex[2];
            $categoryID = (int) $ex[1];
            $tg->answer_query($callback_query_id, 'دسته بندی با موفقیت ثبت شد✅', true);
            $pages->update($pageID, 'category', $keyboards->reciveButtonWithIDCategory($categoryID));
            $pfInfo = $pages->select($pageID);
            $pageLink = 'http://instagram.com/' . substr($pfInfo->link, 1);
            $category = $pfInfo->category;
            $pageName = substr($pfInfo->link, 1);
            $msg = "⚙️مدیریت پیج <a href='" . $pageLink . "'>$pageName</a>
🧰دسته بندی : $category

🔸 لطفا یکی از گزینه های زیر را انتخاب کنید";
            $tg->edit_markap($user_id, $message_id, $msg, $tg->InlineKeyboardMarkup($keyboards->pageEditing($pageID)));
            die();
        case strpos($text, 'back_') !== false:
            $tg->deleteMessage($user_id, $message_id);
            $pgs = $pages->selectWithUserID($user_id);
            $btn = array();
            foreach ($pgs as $pg) {
                array_push($btn, [['text' => $pg['link'], 'callback_data' => 'edit_' . $pg['id']]]);
            }
            array_push($btn, [['text' => 'خروج❌', 'callback_data' => 'delete']]);
            $tg->sendMessage($user_id, '❕ لیست پیج های شما به شرح ذیل میباشد

جهت مدیریت روی پیج مورد نظر خود کلیک کنید', $tg->InlineKeyboardMarkup($btn));
            die();
        case 'delete':
            $tg->deleteMessage($user_id, $message_id);
            die();
    }
    die();
}

$position = $users->select($user_id)->position;
switch ($position) {
    case 'start':
        switch ($text) {
            case 'بازگشت🔙':
                $helpers->backToMainMenu($user_id);
                die();

            case '✏️ ثبت نام':
                if ($users->select($user_id)->validated) {
                    $tg->sendMessage($user_id, 'شما قبلا ثبت نام کرده اید');
                    die();
                }
                $msg = '▫️ لطفا آدرس پیج خود را طبق یکی از فرمت های زیر وارد کنید:

                1️⃣ https://instagram.com/instagram

                2️⃣ @instagram

                                ❗️اگر تبلیغات را خارج از پیج (های) وارد شده قرار دهید تقلب محسوب شده و درآمدی برای شما لحاظ نمیشود.';
                $tg->sendMessage($user_id, $msg, $tg->replyKeyboardMarkup($keyboards->BackKeyboard()));
                $users->update($user_id, 'position', 'pageAddress');
                die();
            case 'ارسال ویدئو🎞':
                $msg = 'لطفا عکس از ویویی که برروی پست زدید + لینک پستتون داخل ایستاگرام را برای ربات در یک پیام بصورت عکس و توضیحات ارسال کنید🏹

نکته: مسئولیت هر گونه اشتباه در ارسال عکس و عدم واریز بر  عهده شما دوست عزیز میباشد⚠️🚫';
                $tg->sendMessage($user_id, $msg, $tg->replyKeyboardMarkup($keyboards->BackKeyboard()));
                $users->update($user_id, 'position', 'screenshot');
                die();
            case '/admin':
                if ($user_id == $main_admin or $admins->is_register($user_id)) {
                    $msg = 'به منوی ادمین خوش آمدید ' . PHP_EOL . ' لطفا انتخاب کنید⚠️🚫';
                    $tg->sendMessage($user_id, $msg, $tg->replyKeyboardMarkup($admin_keyboard));
                    $users->update($user_id, 'position', 'admin');
                }

                die();
            case '⚙️ مدیریت پیج ها':
                $users->select($user_id)->validated or die();
                $pgs = $pages->selectWithUserID($user_id);
                $btn = array();
                foreach ($pgs as $pg) {
                    array_push($btn, [['text' => $pg['link'], 'callback_data' => 'edit_' . $pg['id']]]);
                }
                array_push($btn, [['text' => 'خروج❌', 'callback_data' => 'delete']]);
                $tg->sendMessage($user_id, '❕ لیست پیج های شما به شرح ذیل میباشد

جهت مدیریت روی پیج مورد نظر خود کلیک کنید', $tg->InlineKeyboardMarkup($btn));
                die();

                $users->update($user_id, 'position', 'pageAddress');
                break;
            case '📥 دریافت تبلیغ':
                $users->select($user_id)->validated or die();
                $bns = $banners->selectAll();
                if (empty($bns)) {
                    $tg->sendMessage($user_id, 'فعلا تبلیغی نداریم' . PHP_EOL);
                    die();
                }
                foreach ($bns as $bn) {
                    $bnID = $bn['id'];
                    $bnMedia = $bn['banner'];
                    $bnDescription = $bn['description'];
                    $bnType = $bn['type'];
                    if ($bnType == 'image') {

                        $tg->sendPhoto($user_id, $bnMedia, $bnDescription);

                    } else
                    if ($type == 'video') {
                        $tg->sendVideo($user_id, $bnMedia, null, $bnDescription);

                    }

                }
                $tg->sendMessage($user_id, 'در صورت تکمیل تعداد بازدید اسکرین شات آنرا به همراه لینک پست با استفاده از دکمه ارسال اسکرین شات برای ما بفرستید');
                die();

            case '👤 اطلاعات پروفایل':
                $users->select($user_id)->validated or die();
                $mainMsg = "⛓وضعیت : ✅ تایید شده
⛓نام : $first_name
⛓یوزرنیم : @$username
⛓پیج های شما :" . PHP_EOL . '➖➖➖➖➖➖➖➖➖➖➖➖➖➖➖➖➖' . PHP_EOL;

                $pagesWithUserID = $pages->selectWithUserID($user_id);
                foreach ($pagesWithUserID as $page) {
                    $page_address = $page['link'];
                    $page_category = $page['category'];
                    $mainMsg .= $page_address . ' | ' . $page_category . PHP_EOL . PHP_EOL;
                }
                $tg->sendMessage($user_id, $mainMsg);
                die();

            case '👤 پروفایل من':
                $users->select($user_id)->validated or die();
                $msg = '❕لطفا یکی از گزینه های زیر را انتخاب کنید:';
                $tg->sendMessage($user_id, $msg, $tg->replyKeyboardMarkup($keyboards->profileMenu()));
                die();
            case '📚 قوانین،راهنما':
                $users->select($user_id)->validated or die();
                $msg = 'بزودی';
                $tg->sendMessage($user_id, $msg);
                die();
            case '🗒️ درباره ما':
                $users->select($user_id)->validated or die();
                $msg = 'همینیم که هستیم';
                $tg->sendMessage($user_id, $msg);
                die();
            case '💰 کیف پول':
                $users->select($user_id)->validated or die();
                $msg = 'موجودی کیف پول شما:' . $users->select($user_id)->cash . ' تومان' . PHP_EOL . PHP_EOL . 'در صورتی که خاهان برداشت هستید میتوانیدبا سه روش زیر برداشت کنید' . PHP_EOL . PHP_EOL . ' ⭕️پرفکت مانی, تتر, شماره کارت⭕️' . PHP_EOL . '
لطفا ادرس یکی از موارد بالا را برای ما بفرستید تا عملیات واریز انجام شود' . PHP_EOL . PHP_EOL . 'نکته: درصورت هر گونه اشتباه در هر یک از موارد بالا اعم از شماره کارت, مسئولیت آن به عهده شخص شما میباشد⚠️

حداقل مبلغ برای برداشت 50 هزار تومن میباشد';
                $tg->sendMessage($user_id, $msg, $tg->replyKeyboardMarkup($keyboards->BackKeyboard()));
                $users->update($user_id, 'position', 'withrow');
                die();
            case '📑 زیرمجموعه ها':
                $users->select($user_id)->validated or die();
                $msg = '❕لطفا یکی از گزینه های زیر را انتخاب کنید:';
                $tg->sendMessage($user_id, $msg, $tg->replyKeyboardMarkup($keyboards->subsettingMenu()));
                die();

            case 'آمار زیرمجموعه ها':
                $users->select($user_id)->validated or die();
                if (!($users->countInviters($user_id))) {
                    $tg->sendMessage($user_id, '❗️متأسفانه هیچکس زیرمجموعه شما نیست');
                    die();
                }
                $msg = 'زیر مجموعه های شما:' . PHP_EOL . PHP_EOL;
                $subs = $users->SelectInviters($user_id);
                foreach ($subs as $sub) {
                    $subName = $sub->name;
                    $subUsername = is_null($sub->username) ? 'یوزرنیم ندارد❌' : '@' . $sub->username;
                    $msg .= $subName . ' | ' . $subUsername . PHP_EOL . PHP_EOL;
                }
                $tg->sendMessage($user_id, $msg);
                die();

            case 'لینک زیرمجموعه گیری':
                $users->select($user_id)->validated or die();
                $msg = ' لینك دعوت شما ساخته شد

' . TELEGRAM_INVITE . $user_id . '

شما میتوانید لینک خود را به گـــروه ها و دوستان خود ارسال کنید، هر فردی که از طریق لینک شما وارد ربات شود زیر مجموعه شما شده و با فعالیتش قیمت شما خودکار افزایش پیدا میکند.';
                $tg->sendMessage($user_id, $msg, null, null, true);
                die();

            case '➕ ثبت پیج جدید':
                $users->select($user_id)->validated or die();
                $msg = '▫️ لطفا آدرس پیج خود را طبق یکی از فرمت های زیر وارد کنید:

                    1️⃣ https://instagram.com/instagram

                    2️⃣ @instagram

                                    ❗️اگر تبلیغات را خارج از پیج (های) وارد شده قرار دهید تقلب محسوب شده و درآمدی برای شما لحاظ نمیشود.';
                $tg->sendMessage($user_id, $msg, $tg->replyKeyboardMarkup($keyboards->BackKeyboard()));
                $users->update($user_id, 'position', 'pageAddress');
                break;

            case '📬 پشتیبانی':
                $tg->sendMessage($user_id, '▫️ لطفا پیام خود را به صورت متن ارسال کنید:', $tg->replyKeyboardMarkup($keyboards->BackKeyboard()));
                $users->update($user_id, 'position', 'messageToAdmin');
                break;

            default:
                $tg->sendMessage($user_id, 'دستور نامعتبر');
                break;
        }
        break;
    case 'screenshot':
        switch ($text) {
            case 'بازگشت🔙':
                $helpers->backToMainMenu($user_id);
                die();

            default:
                if (is_null($video) or is_null($description)) {
                    $tg->sendMessage($user_id, 'پیام شما میباسیت حاوی یک ویدیو با توضیحات باشد❌');
                    die();
                }
                $screenshots->insertNewScreenshot($user_id, $video, $description);
                if ($screenshots) {
                    $tg->sendVideo($main_admin, $video, null, $description, null, $tg->InlineKeyboardMarkup($keyboards->yesNoScreenShot($screenshots->lastInsertID())));
                    $tg->sendMessage($user_id, 'پیام شما با موفقیت ارسال شد');
                }

                break;
        }

        die();
    case 'withrow':
        switch ($text) {
            case 'بازگشت🔙':
                $helpers->backToMainMenu($user_id);
                die();

            default:
                $getUserInformation = $users->select($user_id);
                if ($getUserInformation->cash < 50000) {
                    $tg->sendMessage($user_id, 'حداقل مبلغ قابل برداشت از کیف پول 50 هزار تومن میباشد❌');
                    die();
                }
                $tg->sendMessage($main_admin, 'موجودی کیف پول کاربر: ' . $getUserInformation->cash . PHP_EOL . PHP_EOL . $text, $tg->InlineKeyboardMarkup($keyboards->withrowButton($user_id)));
                $tg->sendMessage($user_id, 'درخاست برداشت شما برای ادمین ها ارسال شد و بزودی عملیات واریز انجام میشود✅');
                $helpers->backToMainMenu($user_id);
                die();
        }

        die();
    case 'messageToAll':
        switch ($text) {
            case 'بازگشت🔙':
                $helpers->backToAdminMenu($user_id, $admin_keyboard);
                die();

            default:
                if ($user_id != $main_admin) {
                    die();
                }
                $allUsers = $users->selectAll();
                $limit = 20;
                $counter = 1;
                foreach ($allUsers as $user) {
                    if (empty($user)) {
                        continue;
                    }
                    $counter++;
                    if ($counter <= $limit) {
                        $counter = 1;
                        sleep(2);
                    }
                    $tg->sendMessage($user['user_id'], $text);

                }
                $tg->sendMessage($main_admin, 'ارسال شد');
                die();
        }

        die();
    case 'gettingAddBanner':
        switch ($text) {
            case 'بازگشت🔙':
                $helpers->backToAdminMenu($user_id, $admin_keyboard);
                die();

            default:
                $type = 'image';
                if (is_null($image) and is_null($video)) {
                    $tg->sendMessage($user_id, 'پیام شما میباسیت حاوی یک ویدیو یا عکس باشد💔');
                    die();
                }
                if (is_null($image) and isset($video)) {
                    $type = 'video';
                }
                $banner = is_null($video) ? $image : $video;
                $banner = is_null($video) ? $image : $video;
                if (!is_null($image) or !is_null($video)) {
                    $banners->insertNewBanner($banner, $description, $type);
                    if (!$banner) {
                        $tg->sendMessage($user_id, 'خطا مجددا تلاش کنید');
                        die();
                    }
                }
                if ($type == 'image') {
                    $ls = $banners->lastInsertId();
                    $banner_info = $banners->select($ls);
                    $tg->sendPhoto($main_admin, $banner_info->banner, $banner_info->description, null, $tg->InlineKeyboardMarkup($keyboards->inlineAdmitAdd($ls)));
                    die();
                }
                if ($type == 'video') {
                    $ls = $banners->lastInsertId();
                    $banner_info = $banners->select($ls);
                    $tg->sendVideo($main_admin, $banner_info->banner, null, $banner_info->description, null, $tg->InlineKeyboardMarkup($keyboards->inlineAdmitAdd($ls)));
                    die();
                }

                break;

        }

    case 'messageToAdmin':
        switch ($text) {
            case 'بازگشت🔙':
                $helpers->backToMainMenu($user_id);
                break;

            default:
                !is_null($text) or die();
                $result = $tg->sendMessage($main_admin, $text . PHP_EOL . PHP_EOL . 'ایدی عددی کاربر: ' . $user_id . PHP_EOL . (is_null($username) ? 'این کاربر یوزرنیم ندارد' : '@' . $username));
                $messages->insertNewMessage($user_id, $message_id, $result['result']['message_id']);
                $tg->sendMessage($user_id, 'پیام شما با موفقیت برای ادمین ارسال شد' . PHP_EOL . 'لطفا در گرفتن جواب صبور بوده و از ارسال مجدد پیام خودداری کنید');
                break;
        }

        break;
    case 'pageAddress':
        switch ($text) {
            case 'بازگشت🔙':
                $helpers->backToMainMenu($user_id);
                break;

            case 'پیج دیگری ندارم ✅':
                $msg = '✅ ثبت پیج (های) شما انجام و تایید شد.

    ⬜️ لطفاً مقدار ویو «استوری» و تعرفه ی سین استوری را همراه با کمی توضیحات ارسال کنید..';
                $tg->sendMessage($user_id, $msg, $users->select($user_id)->validated ? $keyboards->BackKeyboard() : $keyboards->BackKeyboard());
                $users->update($user_id, 'position', 'finalInformations');

                break;
            default:
                if ((strpos($text, '@') === false and strpos($text, 'https://instagram.com/') === false)) {
                    $tg->sendMessage($user_id, '⛔️ خطا ، فرمت وارد شده صحیح نمیباشد ، لطفا مطابق قرمت های زیر ارسال کنید️
1️⃣ https://instagram.com/instagram

2️⃣ @instagram');
                    die();
                }
                if (strpos($text, 'https://instagram.com/') !== false) {
                    $text = str_replace('https://instagram.com/', "@", $text);
                }

                if ($pages->insertNewPage($text, $user_id)) {
                    $users->update($user_id, 'last_page_inserting', $pages->lastInsertID());
                    $tg->sendMessage($user_id, '🧰 دسته بندی پیج خود را از لیست زیر انتخاب کنید', $tg->replyKeyboardMarkup($keyboards->categoryKeyboard()));
                    $users->update($user_id, 'position', 'setCategory');
                } else {
                    $tg->sendMessage($user_id, 'این پیج قبلا ثبت شده❌');

                }

                break;
        }
        break;

    case 'setCategory':
        switch ($text) {
            case 'بازگشت🔙':
                $helpers->backToMainMenu($user_id);
                break;

            default:
                $categoryKeyboard = $keyboards->categoryKeyboard();
                $foundMatch = false;
                foreach ($categoryKeyboard as $eachLine) {
                    foreach ($eachLine as $singleKeyboard) {
                        if ($text == $singleKeyboard) {
                            $foundMatch = true;
                            break 2;
                        }

                    }
                }
                if ($foundMatch) {
                    $pageID = $users->select($user_id)->last_page_inserting;
                    $page_link = substr($pages->select($pageID)->link, 1);
                    $pages->update($pageID, 'category', $text);
                    $msg = " 🔹 آدرس پیج : instagram.com/$page_link
🔹دسته بندی پیج : $text

آیا از صحت اطلاعات اطمینان دارید ؟";
                    $tg->sendMessage($user_id, $msg, $tg->InlineKeyboardMarkup($keyboards->yesNoInPageInformation($pageID)));
                } else {
                    $tg->sendMessage($user_id, '🧰 دسته بندی پیج خود را از لیست زیر انتخاب کنید', $tg->replyKeyboardMarkup($keyboards->categoryKeyboard()));
                    die();

                }

        }

        break;

        break;
    case 'finalInformations':
        switch ($text) {
            case 'بازگشت🔙':
                $helpers->backToMainMenu($user_id);
                break;
            default:
                $userPages = $pages->selectWithUserID($user_id);

                foreach ($userPages as $userPage) {
                    if (!($userPage['is_submited'])) {
                        $pageID = $userPage['id'];
                        $page_address = $userPage['link'];
                        $page_category = $userPage['category'];
                        $msg = "🔹 آدرس پیج : $page_address \n🔹دسته بندی پیج : $page_category";
                        $tg->sendMessage($main_admin, $msg, $tg->InlineKeyboardMarkup($keyboards->AdmitorNotAdmin($pageID)));
                    }
                }
                $tg->forwardMessage($main_admin, $user_id, $message_id);
                $msg2 = '✅درخواست عضویت شما به گروه پشتیبانی ارسال شد ، بزودی نتیجه به شما اعلام میشود.';
                $tg->sendMessage($user_id, $msg2);
                $helpers->backToMainMenu($user_id);
                $msg4 = "کاربر  <a href='tg://user?id={$user_id}'>{$user_id}</a> !" . ' منتظر تایید شدن پیج های خود میباشد👆👆👆';
                $tg->sendMessage($main_admin, $msg4, null, 'HTML');

        }

        break;
    case 'admin':

        if ($user_id != $main_admin and !$admins->is_register($user_id)) {
            die();
        }
        if ($admins->is_register($user_id)) {
            $adminRights = $admins->select($user_id);
        }
        switch ($text) {
            case 'پیام به همه📤':
                if (!$adminRights->msg_to_all and $user_id != $main_admin) {
                    die();
                }
                $msg = 'پیام خود را ارسال کنید ' . PHP_EOL . PHP_EOL . 'بعد از ارسال, پیام شما برای تمام کاربران ربات ارسال خاهد شد';
                $tg->sendMessage($user_id, $msg, $tg->replyKeyboardMarkup($keyboards->BackKeyboard()));
                $users->update($user_id, 'position', 'messageToAll');
                break;

            case 'تعداد کاربران ربات🏹':
                if (!$adminRights->member_count and $user_id != $main_admin) {
                    die();
                }
                $msg3 = 'تعداد کاربران ربات تاکنون: ' . $users->countRows() . PHP_EOL . 'تعداد پیج ها: ' . $pages->countRows();
                $tg->sendMessage($user_id, $msg3);
                break;
            case 'افزودن بنر 🌄':
                if (!$adminRights->add_banner and $user_id != $main_admin) {
                    die();
                }
                $msg = 'لطفا عکس یا ویدیو تبلیغ را برای من ارسال کنید' . PHP_EOL . 'دقت کنید اگر تبلیغ دارای توضیحات است آنرا در کپشن ویدیو یا عکس برای من بفرستید';
                $tg->sendMessage($user_id, $msg, $tg->replyKeyboardMarkup($keyboards->BackKeyboard()));
                $users->update($user_id, 'position', 'gettingAddBanner');
                break;
            case 'ویرایش اطلاعات کاربر👤':
                if ($user_id == $main_admin or ($adminRights->add_banner and $adminRights->edit_cash)) {
                    $tg->sendMessage($user_id, 'لطفا انتخاب کنید', $tg->replyKeyboardMarkup($keyboards->userChangeInfo()));
                }
                break;
            case 'افزودن ادمین جدید💤':
                if ($user_id != $main_admin) {
                    die();
                }
                $tg->sendMessage($user_id, 'لطفا آیدی عددی فردی که میخاهید ادمین شود را وارد نمایید:', $tg->replyKeyboardMarkup($keyboards->BackKeyboard()));
                $users->update($user_id, 'position', 'AddNewAdmin');
                break;
            case 'ویرایش موجودی💸':
                if (!$adminRights->edit_cash and $user_id != $main_admin) {
                    die();
                }
                $tg->sendMessage($user_id, 'لطفا آیدی عددی فردی که میخاهید موجودیش را تغییر دهید را نمایید:', $tg->replyKeyboardMarkup($keyboards->BackKeyboard()));
                $users->update($user_id, 'position', 'change_cash');

                break;
            case 'بن/آزاد کردن کاربر❌':
                if (!$adminRights->ban_user and $user_id != $main_admin) {
                    die();
                }
                $tg->sendMessage($user_id, 'لطفا آیدی عددی فردی که میخاهید بن شود را وارد نمایید:', $tg->replyKeyboardMarkup($keyboards->BackKeyboard()));
                $users->update($user_id, 'position', 'banUser');

                break;
            case 'بازگشت به منوی ادمین🔙':$helpers->backToAdminMenu($user_id, $admin_keyboard);
                break;
            case 'بازگشت🔙':$helpers->backToMainMenu($user_id);
                break;
            default:
                # code...
                break;
        }
        break;
    case 'AddNewAdmin':
        switch ($text) {
            case 'بازگشت🔙':
                $helpers->backToAdminMenu($user_id, $admin_keyboard);
                break;

            default:
                if (!is_numeric($text)) {
                    $tg->sendMessage($user_id, 'لطفا از مقدار عددی استفاده کنید❌', $tg->replyKeyboardMarkup($keyboards->BackKeyboard()));
                    die();
                }
                if (!$users->is_register($text)) {
                    $tg->sendMessage($user_id, 'این کاربر در ربات ثبت نام نکرده است❌', $tg->replyKeyboardMarkup($keyboards->BackKeyboard()));
                    die();
                }
                if (!$admins->is_register($text)) {
                    if (!$admins->insert_new_admin($text)) {
                        $tg->sendMessage($user_id, 'مشکلی به هنگام ذخیره ادمین در دیتابیس بوجود آمد', $tg->replyKeyboardMarkup($keyboards->BackKeyboard()));
                        die();
                    }
                }

                $userInfo = $users->select($text);
                $uname = $userInfo->username;
                $fname = $userInfo->name;
                $msg = "🔵 آیدی عددی کاربر: $text
🔵 یوزرنیم کاربر: @$uname
🔵 نام کاربری : $fname

تنظیم دسترسی ها👇🏻";
                $tg->sendMessage($user_id, $msg, $tg->InlineKeyboardMarkup($keyboards->murkubForAccessing($text)));

                break;
        }
        break;
    case 'change_cash':
        switch ($text) {
            case 'بازگشت🔙':
                $helpers->backToAdminMenu($user_id, $admin_keyboard);
                break;

            default:
                if (!is_numeric($text)) {
                    $tg->sendMessage($user_id, 'لطفا از مقدار عددی استفاده کنید❌', $tg->replyKeyboardMarkup($keyboards->BackKeyboard()));
                    die();
                }
                if (!$users->is_register($text)) {
                    $tg->sendMessage($user_id, 'این کاربر در ربات ثبت نام نکرده است❌', $tg->replyKeyboardMarkup($keyboards->BackKeyboard()));
                    die();
                }

                if (!$users->update($user_id, 'ch_user_id', $text)) {
                    $tg->sendMessage($user_id, 'مشکلی به هنگام ذخیره کردن پیش آمد', $tg->replyKeyboardMarkup($keyboards->BackKeyboard()));
                    die();
                }

                $userInfo = $users->select($text);
                $uname = $userInfo->username;
                $fname = $userInfo->name;
                $fcash = $userInfo->cash;
                $msg = "🔵 آیدی عددی کاربر: $text
🔵 یوزرنیم کاربر: @$uname
🔵 نام کاربری : $fname
موجودی فعلی: $fcash
موجودی جدید را وارد کنید یا در غیر اینصورت بازگشت را فشار دهید:👇🏻";
                $users->update($user_id, 'position', 'getPrice');
                $tg->sendMessage($user_id, $msg, $tg->replyKeyboardMarkup($keyboards->BackKeyboard()));

                break;
        }
        break;
    case 'getPrice':
        switch ($text) {
            case 'بازگشت🔙':
                $helpers->backToAdminMenu($user_id, $admin_keyboard);
                break;

            default:
                if (!is_numeric($text)) {
                    $tg->sendMessage($user_id, 'لطفا از مقدار عددی استفاده کنید❌', $tg->replyKeyboardMarkup($keyboards->BackKeyboard()));
                    die();
                }
               
               $chID = $users->select($user_id)->ch_user_id;
                if (!$users->update($chID, 'cash',(int)$text)) {
                    $tg->sendMessage($user_id, 'مشکلی به هنگام ذخیره کردن پیش آمد', $tg->replyKeyboardMarkup($keyboards->BackKeyboard()));
                    die();
                }

                $userInfo = $users->select($users->select($user_id)->ch_user_id);
                $uname = $userInfo->username;
                $uuser_id = $userInfo->user_id;
                $fname = $userInfo->name;
                $fcash = $userInfo->cash;
                $msg = "🔵 آیدی عددی کاربر: $uuser_id
🔵 یوزرنیم کاربر: @$uname
🔵 نام کاربری : $fname
موجودی جدید: $fcash
موجودی کاربر با موفقیت تغییر کرد✅";
                $users->update($user_id, 'ch_user_id', 0);
                $users->update($user_id, 'position', 'getPrice');
                $tg->sendMessage($user_id, $msg);
                $helpers->backToAdminMenu($user_id, $admin_keyboard);

                break;
        }
        break;
    case 'banUser':
        switch ($text) {
            case 'بازگشت🔙':
                $helpers->backToAdminMenu($user_id, $admin_keyboard);
                break;

            default:
                if (!is_numeric($text)) {
                    $tg->sendMessage($user_id, 'لطفا از مقدار عددی استفاده کنید❌', $tg->replyKeyboardMarkup($keyboards->BackKeyboard()));
                    die();
                }
                if (!$users->is_register($text)) {
                    $tg->sendMessage($user_id, 'این کاربر در ربات ثبت نام نکرده است❌', $tg->replyKeyboardMarkup($keyboards->BackKeyboard()));
                    die();
                }

                $userInfo = $users->select($text);
                if ($userInfo->is_banned) {
                    if (!$users->update($text, 'is_banned', 0)) {
                        $tg->sendMessage($user_id, 'مشکلی به هنگام بن کردن کاربر پیش آمد');
                        die();
                    }
                    $msg = '🔵کاربر با اسم 👈 ' . $userInfo->name . '
🔵و آیدی عددی 👈' . $userInfo->user_id . '
🔵و یوزرنیم 👈' . '@' . $userInfo->username . '

✅با موفقیت رفع محدودیت شد';
                    $tg->sendMessage($user_id, $msg);
                } else {
                    if (!$users->update($text, 'is_banned', 1)) {
                        $tg->sendMessage($user_id, 'مشکلی به هنگام بن کردن کاربر پیش آمد');
                        die();
                    }
                    $msg = '🔵کاربر با اسم 👈 ' . $userInfo->name . '
🔵و آیدی عددی 👈' . $userInfo->user_id . '
🔵و یوزرنیم 👈' . '@' . $userInfo->username . '

✅با موفقیت بن شد';
                    $tg->sendMessage($user_id, $msg);

                }

                break;
        }
        break;
}
