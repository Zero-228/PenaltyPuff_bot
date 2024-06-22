<?php 
/**
 * PenaltyPuff Chatbot
 * 
 * Licensed under the Simple Commercial License.
 * 
 * Copyright (c) 2024 Nikita Shkilov nikshkilov@yahoo.com
 * 
 * All rights reserved.
 * 
 * This file is part of PenaltyPuff bot. The use of this file is governed by the
 * terms of the Simple Commercial License, which can be found in the LICENSE file
 * in the root directory of this project.
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Conversations\InlineMenu;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;
$filesystemAdapterX = new FilesystemAdapter();
$cacher = new Psr16Cache($filesystemAdapterX);

class PrescribePuff extends InlineMenu
{
    protected Nutgram $bot;
    protected ?int $friendId = null;
    protected ?string $comment = null;

    public function __construct(Nutgram $bot)
    {
        parent::__construct();
        $this->bot = $bot;
    }

    public function start(Nutgram $bot, $friendId) {
        try {
            $bot->deleteMessage($bot->userId(), $bot->messageId());
        } catch (Exception $e) {
            error_log($e);
        }
        
        $lang = lang($bot->userId());
        $this->friendId = $friendId;
        $this->comment = NULL;
        
        $this->menuText(msg('add_comment', lang($bot->userId())))
            ->addButtonRow(InlineKeyboardButton::make(msg('without_comment', $lang), callback_data: '@prescribe'))
            ->addButtonRow(InlineKeyboardButton::make(msg('cancel', $lang), callback_data: '@none'))
            ->orNext('handleComment')
            ->showMenu();
    }

    public function handleComment(Nutgram $bot)
    {
        $lang = lang($bot->userId());

        try {
            $bot->deleteMessage($bot->userId(), $bot->messageId());
        } catch (Exception $e) {
            error_log($e);
        }

        $comment = $bot->message()->text;
        $this->comment = $comment;
        $friendId = $this->friendId;
        $friendUsername = getUsername($friendId);
        $msg = msg('user', lang($bot->userId())).$friendUsername."\n\n".msg('comment', lang($bot->userId())).$comment;

        $this->menuText($msg)
            ->clearButtons()
            ->addButtonRow(InlineKeyboardButton::make(msg('prescribe_puff', $lang), callback_data: '@prescribe'))
            ->addButtonRow(InlineKeyboardButton::make(msg('cancel', $lang), callback_data: '@none'))
            ->orNext('handleComment')
            ->showMenu();
    }

    public function prescribe(Nutgram $bot)
    {
        $friendId = $this->friendId;
        $friendUsername = getUsername($friendId);
        $comment = $this->comment;

        $username = getUsername($bot->userId());
        $prescribe = prescribePuff($bot->userId(), $friendId, $comment);

        if (str_contains($prescribe, "success")) {
            $trimmedPrescribe = substr($prescribe, strpos($prescribe, "success") + strlen("success"));
            $puffId = (int)$trimmedPrescribe;
            $friendLang = lang($friendId);

            $inlineKeyboard = InlineKeyboardMarkup::make()
                ->addRow(
                    InlineKeyboardButton::make(msg('puff_decline', $friendLang), null, null, 'callback_puff_decline ' . $puffId . ' ' . $bot->userId()),
                    InlineKeyboardButton::make(msg('puff_approve', $friendLang), null, null, 'callback_puff_approve ' . $puffId . ' ' . $bot->userId())
                )
                ->addRow(InlineKeyboardButton::make(msg('cancel', $friendLang), null, null, 'callback_cancel'));

            $checkStatus = checkUserStatus($friendId);
            $msg = msg('prescribe_success', lang($bot->userId())).$friendUsername."\n\n".msg('comment', lang($bot->userId())).$comment;

            if ($checkStatus == 'deleted') {
                $bot->deleteMessage($bot->userId(), $bot->messageId());
                $bot->sendMessage(msg('prescribe_deleted_user', lang($bot->userId())));
            } elseif ($checkStatus == 'active') {
                try {
                    $bot->sendMessage($username . msg('prescribed_puff', $friendLang)."\n\n".msg('comment', $friendLang).$comment, chat_id: $friendId, reply_markup: $inlineKeyboard);
                    $bot->deleteMessage($bot->userId(), $bot->messageId());
                    $bot->sendMessage($msg);
                } catch (\Exception $e) {
                    if ($e->getCode() == '403') {
                        sleep(1);
                        UserBlockedBot($friendId);
                        $bot->deleteMessage($bot->userId(), $bot->messageId());
                        $bot->sendMessage(msg('prescribe_deleted_user', lang($bot->userId())));
                    }
                }
            }
        } elseif ($prescribe == "self") {
            $bot->deleteMessage($bot->userId(), $bot->messageId());
            $bot->sendMessage(msg('prescribe_self', lang($bot->userId())));
        } elseif ($prescribe == "delay") {
            $bot->deleteMessage($bot->userId(), $bot->messageId());
            $bot->sendMessage(msg('prescribe_delay', lang($bot->userId())));
        }
        $this->end();
    }

    public function none(Nutgram $bot)
    {
        $this->bot->delete('friendId');
        $this->bot->delete('comment');
        $bot->sendMessage(msg('canceled', lang($bot->userId())));
        $this->end();
    }
}