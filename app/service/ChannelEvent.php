<?php

namespace app\service;

interface ChannelEvent
{
    const EVENT_SEND_USER_TO_USER = 'event_send_user_to_user';
    const EVENT_SEND_ALL = 'event_send_all';
}