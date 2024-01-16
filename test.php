<?php

$text =[
    [
    null,"ویرایش اطلاعات کاربر👤"
    ],
    [
    "نعداد کاربران ربات🏹"
    ],
    [
    "بازگشت🔙"
    ]
    ];
print_r($text);

// $admin_keyboard =[
//     'zeroRow'=>
//     [
//         'add_banner'=>'افزودن بنر 🌄',
//         'msg_to_all'=>'پیام به همه📤'
//     ],
//     'firstRow'=>
//     [
//         'edit_cash'=>'ویرایش موجودی💸',
//         'ban_user'=>'بن/آزاد کردن کاربر❌',
//     ],
//     'secondRow'=>
//     [
//         'member_count'=>'نعداد کاربران ربات🏹'
//     ],
//     ['بازگشت🔙'],

// ];
// if (!$adminAccess->edit_cash) {
//     unset($admin_keyboard['firstRow']['edit_cash']);
// }
// if (!$adminAccess->ban_user) {
//     unset($admin_keyboard['firstRow']['ban_user']);
// }
// if ($adminAccess->ban_user and $adminAccess->edit_cash) {
//     array_splice($admin_keyboard,1,1,['editUserInfo'=>'ویرایش اطلاعات کاربر👤']);
// }
// if (!$adminAccess->add_banner){
//     unset($admin_keyboard[0][0]);
// }
// if (!$adminAccess->msg_to_all) {
//     unset($admin_keyboard[0][1]);
// }
// if (!$adminAccess->msg_to_all and !$adminAccess->add_banner) {
//     unset($admin_keyboard[0]);
// }
// if (!$adminAccess->member_count){
//     unset($admin_keyboard[2][0]);
// }
