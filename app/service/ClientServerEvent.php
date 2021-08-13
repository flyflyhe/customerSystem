<?php

namespace app\service;

/**
 * 与客户端通讯的事件name
 * Interface ClientServerEvent
 * @package app\service
 */
interface ClientServerEvent
{
    const EVENT_LOGIN = 'login';
    const EVENT_LOGOUT = 'logout';
    const EVENT_SEND = 'message';
    const EVENT_SEND_ALL = 'broad';
    const EVENT_ADD_USER = 'addUser';
}