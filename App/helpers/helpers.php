<?php
namespace App\helpers;

use App\Model\admins;
use App\Model\users;
use App\Telegram\telegramBot;
use App\helpers\keyboards;

class helpers {
    protected $dbuser;
    protected $dbAdmin;
    protected $telegram;
    protected $keyboards;
    public function __construct($bot_token)
    {
        $this->dbuser = new users();        
        $this->telegram = new telegramBot($bot_token);
        $this->keyboards = new keyboards();
    }
    public function backToMainMenu($user_id)
    {
        $keyboard = $this->telegram ->replyKeyboardMarkup($this->keyboards->MainMenuKeyboard($user_id));
        $this->telegram -> sendMessage($user_id,'شما به منوی اصلی بازگشتید'.PHP_EOL.PHP_EOL.'با استفاده از منوی زیر میتونی به امکانات ربات ما دسترسی داشته باشی👌👌',$keyboard);
        $this->dbuser -> update($user_id,'position','start');
    }
    public function backToPanel($user_id)
    {
        $keyboard = $this->telegram ->replyKeyboardMarkup($this->keyboards->PanelMenu($user_id));
        $this->telegram -> sendMessage($user_id,'❕لطفا یکی از گزینه های زیر را انتخاب کنید:',$keyboard);
        $this->dbuser -> update($user_id,'position','start');
    }

    public function backToAdminMenu($user_id,$keyboards)
    {
        $keyboard = $this->telegram ->replyKeyboardMarkup($keyboards);
        $this->telegram -> sendMessage($user_id,'شما به منوی ادمین بازگشتید'.PHP_EOL.PHP_EOL.'با استفاده از منوی زیر میتونی به امکانات ادمین دسترسی داشته باشی👌👌',$keyboard );
        $this->dbuser -> update($user_id,'position','admin');
        die();
    }
    public function emoji($input): string
    {
        if ($input==0)
            return "❌";
        else
            return "✅";
    }
   
    
}