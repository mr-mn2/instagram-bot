<?php
namespace App\helpers;

use App\Model\admins;
use App\Model\users;

class keyboards
{
    protected $users;
    protected $admins;
    public function __construct()
    {
        $this->users = new users();
        $this->admins = new admins();
    }
    public function MainMenuKeyboard($user_id)
    {
        if ($this->users->select($user_id)->validated) {
            return [
                ['📥 دریافت تبلیغ'],
                ['➕ ثبت پیج جدید'],
                ['ارسال ویدئو🎞', '👤 پروفایل من'],
                ['🗒️ درباره ما', '💰 کیف پول'],
                ['📚 قوانین،راهنما', '📬 پشتیبانی']
            ];
            
        }
        return [
            ['✏️ ثبت نام', '📬 پشتیبانی'],
        ];

    }
    public function editAdminKeyboard($user_id){
        $adminAccess = $this->admins->select($user_id);
        $admin_keyboard =[
            [
                $adminAccess->add_banner ? 'افزودن بنر 🌄' :'',
                $adminAccess->msg_to_all ? 'پیام به همه📤' : ''
            ],
            ($adminAccess->edit_cash and $adminAccess->ban_user) ? 
            [
                'ویرایش اطلاعات کاربر👤'
            ]:
            [

                $adminAccess->edit_cash ? 'ویرایش موجودی💸':'',
                $adminAccess->ban_user ? 'بن/آزاد کردن کاربر❌':''
            ]
            ,
            [
                $adminAccess->member_count ? 'تعداد کاربران ربات🏹':''
            ],
            ['بازگشت🔙'],
    
        ];
        
        return $admin_keyboard;
    
    }
    
    public function BackKeyboard()
    {
        return [
            ['بازگشت🔙'],
        ];

    }
    public function lastMenu()
    {
        return [
            ['🔙 منوی قبلی'],
        ];

    }
    public function subsettingMenu()
    {
        return [
            ['لینک زیرمجموعه گیری','آمار زیرمجموعه ها'],
            ['بازگشت🔙']
        ];

    }
    
    public function userChangeInfo()
    {
        return [
            ['ویرایش موجودی💸','بن/آزاد کردن کاربر❌'],
            ['بازگشت به منوی ادمین🔙']
        ];

    }
    
    public function profileMenu()
    {
        return [
            ['👤 اطلاعات پروفایل','⚙️ مدیریت پیج ها'],
            ['📑 زیرمجموعه ها'],
            ['بازگشت🔙']
        ];

    }
    public function inlineAdmitAdd($bannerID)
    {
        return [
            [
                ['text' => 'بله✅', 'callback_data' => 'yesBanner_' . $bannerID],
                ['text' => 'خیر❌', 'callback_data' => 'noBanner_' . $bannerID],
            ],
        ];


    }
    public function murkubForAccessing($someOneWhoWantToBeAdmin)
    {
        $adminInfo = $this->admins -> select($someOneWhoWantToBeAdmin);
        
        $buttons = [
            [
                ['text' => 'تعداد ممبر ها👤', 'callback_data' => 'dontClick'],
                ['text' => $adminInfo->member_count ? '✅':'❌', 'callback_data' => 'accsessing@member_count@'.$someOneWhoWantToBeAdmin],
            ],
            [
                ['text' => 'بن کردن❌', 'callback_data' => 'dontClick'],
                ['text' => $adminInfo->ban_user ? '✅':'❌', 'callback_data' => 'accsessing@ban_user@'.$someOneWhoWantToBeAdmin],
            ],
            [
                ['text' => 'پیام به همه📨', 'callback_data' => 'dontClick'],
                ['text' => $adminInfo->msg_to_all ? '✅':'❌', 'callback_data' => 'accsessing@msg_to_all@'.$someOneWhoWantToBeAdmin],
            ],
            [
                ['text' => 'افزودن تبلیغ🌄', 'callback_data' => 'dontClick'],
                ['text' => $adminInfo->add_banner ? '✅':'❌', 'callback_data' => 'accsessing@add_banner@'.$someOneWhoWantToBeAdmin],
            ],
            [
                ['text' => 'تغییر موجودی💰', 'callback_data' => 'dontClick'],
                ['text' => $adminInfo->edit_cash ? '✅':'❌', 'callback_data' => 'accsessing@edit_cash@'.$someOneWhoWantToBeAdmin],
            ],
            [
                ['text' => 'انجام شد✅', 'callback_data' => 'accsessing@done@'.$someOneWhoWantToBeAdmin],
            ],
            [
                ['text' => 'عزل ادمین❌', 'callback_data' => 'accsessing@sdel@'.$someOneWhoWantToBeAdmin],
            ]
            
            
        ];
        return $buttons;
    }
    public function yesnoAccesing($someOneWhoWantToBeAdmin)
    {
       
        
        $buttons = [
            [
                ['text' => 'بله✅', 'callback_data' => 'accsessing@Yes@'.$someOneWhoWantToBeAdmin],
                ['text' => 'خیر❌', 'callback_data' => 'accsessing@No@'.$someOneWhoWantToBeAdmin],
            ],
             
        ];
        return $buttons;
    }
    public function yesNoScreenShot($user_id)
    {
        return [
            [
                ['text' => 'بله✅', 'callback_data' => 'yesScreen_' . $user_id],
                ['text' => 'خیر❌', 'callback_data' => 'noScreen_' . $user_id],
            ],
        ];


    }
    public function withrowButton($user_id)
    {
        return [
            [
                ['text' => 'تایید پرداخت✅', 'callback_data' => 'subPay_' . $user_id],
                ['text' => 'خطا در اطلاعات✅', 'callback_data' => 'wrPay_' . $user_id],
            ],
        ];


    }
    public function yesNoDelPage($pageID)
    {
        return [
            [
                ['text' => 'بله✅', 'callback_data' => 'yespg_' . $pageID],
                ['text' => 'خیر❌', 'callback_data' => 'edit_' . $pageID],
            ],
        ];


    }
    public function pageEditing($pageID)
    {
        return [
            [
                ['text' => 'حذف پیج🗑', 'callback_data' => 'delete_' . $pageID],
            ],
            [
                ['text' => 'ویرایش دسته بندی🧰', 'callback_data' => 'category_' . $pageID],
            ],
            [
                ['text' => 'بازگشت🔙', 'callback_data' => 'back_' . $pageID],
            ],
        ];


    }
    public function NoPagesAndBackKeyboard()
    {
        return [
            ['پیج دیگری ندارم ✅'],
            ['بازگشت🔙'],
        ];

    }
    public function PanelMenu()
    {
        return [
            ['👤 اطلاعات پروفایل','📑 زیرمجموعه ها'],
            ['⚙️ مدیریت پیج ها','➕ ثبت پیج جدید'],
            ['بازگشت🔙']
        ];

    }
    public function categoryKeyboard()
    {
        return [
            ['❗️vacant', '📱 کلیپ'],
            ['😂 فان', '😞 دپ'],
            ['🌄 پروفایل', '📸 عکاسی'],
            ['🌐 خبری', '🧞‍♀داف'],
            ['⚽️ ورزشی', '💲اقتصادی'],
            ['🎧 موسیقی', '🎳 سرگرمی'],
            ['🎞 فیلم و سینما', '🏛 مذهبی'],
            ['🚘 خودرو', '🏖 گردشگری'],
            ['🖋 تکست', '🕊 توییتر'],
            ['⏱️ بلاگر', '🔹فکت'],
            ['💥Phase -1', '🌖 dark'],
            ['💥Phase 2', '👩‍❤️‍👨 عاشقانه'],
            ['🚨 Scam', '🏆 VIP'],

        ];

    }
    public function reciveButtonWithIDCategory(int $index)
    {
        
        $arr =  [
            '❗️vacant', '📱 کلیپ',
            '😂 فان', '😞 دپ',
            '🌄 پروفایل', '📸 عکاسی',
            '🌐 خبری', '🧞‍♀داف',
            '⚽️ ورزشی', '💲اقتصادی',
            '🎧 موسیقی', '🎳 سرگرمی',
            '🎞 فیلم و سینما', '🏛 مذهبی',
            '🚘 خودرو', '🏖 گردشگری',
            '🖋 تکست', '🕊 توییتر',
            '⏱️ بلاگر', '🔹فکت',
            '💥Phase -1', '🌖 dark',
            '💥Phase 2', '👩‍❤️‍👨 عاشقانه',
            '🚨 Scam', '🏆 VIP',

        ];
        return $arr[$index-1];

    }
    
    public function categoryEditing($pageID)
    {
        return
        [ 
            
            [
                ['text' => '❗️vacant', 'callback_data' => 'dnct_1_'.$pageID],
                ['text' => '📱 کلیپ', 'callback_data' => 'dnct_2_'.$pageID],
            ],
            [
                ['text' => '😂 فان', 'callback_data' => 'dnct_3_'.$pageID],
                ['text' => '😞 دپ', 'callback_data' => 'dnct_4_'.$pageID],
            ],
            [
                ['text' => '🌄 پروفایل', 'callback_data' => 'dnct_5_'.$pageID],
                ['text' => '📸 عکاسی', 'callback_data' => 'dnct_6_'.$pageID],
            ],
            [
                ['text' => '🌐 خبری', 'callback_data' => 'dnct_7_'.$pageID],
                ['text' => '🧞‍♀داف', 'callback_data' => 'dnct_8_'.$pageID],
            ],
            [
                ['text' => '⚽️ ورزشی', 'callback_data' => 'dnct_9_'.$pageID],
                ['text' => '💲اقتصادی', 'callback_data' => 'dnct_10_'.$pageID],
            ],
            
            [
                ['text' => '🎧 موسیقی', 'callback_data' => 'dnct_11_'.$pageID],
                ['text' => '🎳 سرگرمی', 'callback_data' => 'dnct_12_'.$pageID],
            ],
            [
                ['text' => '🎞 فیلم و سینما', 'callback_data' => 'dnct_13_'.$pageID],
                ['text' => '🏛 مذهبی', 'callback_data' => 'dnct_14_'.$pageID],
            ],
            [
                ['text' => '🚘 خودرو', 'callback_data' => 'dnct_15_'.$pageID],
                ['text' => '🏖 گردشگری', 'callback_data' => 'dnct_16_'.$pageID],
            ],
            [
                ['text' => '🖋 تکست', 'callback_data' => 'dnct_17_'.$pageID],
                ['text' => '🕊 توییتر', 'callback_data' => 'dnct_18_'.$pageID],
            ],
            [
                ['text' => '⏱️ بلاگر', 'callback_data' => 'dnct_19_'.$pageID],
                ['text' => '🔹فکت', 'callback_data' => 'dnct_20_'.$pageID],
            ],
            [
                ['text' => '💥Phase -1', 'callback_data' => 'dnct_21_'.$pageID],
                ['text' => '🌖 dark', 'callback_data' => 'dnct_22_'.$pageID],
            ],
            [
                ['text' => '💥Phase 2', 'callback_data' => 'dnct_23_'.$pageID],
                ['text' => '👩‍❤️‍👨 عاشقانه', 'callback_data' => 'dnct_24_'.$pageID],
            ],
            [
                ['text' => '🚨 Scam', 'callback_data' => 'dnct_25_'.$pageID],
                ['text' => '🏆 VIP', 'callback_data' => 'dnct_26_'.$pageID],
            ],
            
        ];
        return [
            ['❗️vacant', '📱 کلیپ'],
            ['😂 فان', '😞 دپ'],
            ['🌄 پروفایل', '📸 عکاسی'],
            ['🌐 خبری', '🧞‍♀داف'],
            ['⚽️ ورزشی', '💲اقتصادی'],
            ['🎧 موسیقی', '🎳 سرگرمی'],
            ['🎞 فیلم و سینما', '🏛 مذهبی'],
            ['🚘 خودرو', '🏖 گردشگری'],
            ['🖋 تکست', '🕊 توییتر'],
            ['⏱️ بلاگر', '🔹فکت'],
            ['💥Phase -1', '🌖 dark'],
            ['💥Phase 2', '👩‍❤️‍👨 عاشقانه'],
            ['🚨 Scam', '🏆 VIP'],

        ];

    }
    public function AdminKeyboard()
    {
        return [
            ['اضافه کردن موزیک🎸'],
            ['بن کردن کاربر🚫', 'آزاد کردن✅'],
            ['نعداد کاربران ربات🏹'],
            ['افزودن ادمین👩‍💼'],
            ['بازگشت🔙'],

        ];

    }
    public function yesNoInPageInformation($pageID)
    {
        return [
            [
                ['text' => 'بله✅', 'callback_data' => 'yesAdd_' . $pageID],
                ['text' => 'خیر❌', 'callback_data' => 'noRemove_' . $pageID],
            ],
        ];

    }
    public function AdmitorNotAdmin($pageID)
    {
        return [
            [
                ['text' => 'تایید✅', 'callback_data' => 'submit_' . $pageID],
                ['text' => 'عدم تایید❌', 'callback_data' => 'deSubmit_' . $pageID],
            ],
        ];

    }
}
