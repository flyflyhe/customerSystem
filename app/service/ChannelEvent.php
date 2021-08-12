<?php

namespace app\service;

interface ChannelEvent
{
    const EVENT_SEND_USER_TO_USER = 'event_send_user_to_user';
    const EVENT_SEND_ALL = 'event_send_all';
    const EVENT_LOGIN_NOTIFY = 'login_notify';
    const EVENT_LOGIN = 'login';
}