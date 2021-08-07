<?php

namespace app\service;

interface ClientServerEvent
{
    const EVENT_LOGIN = 'login';
    const EVENT_LOGOUT = 'logout';
    const EVENT_SEND = 'send';
    const EVENT_SEND_ALL = 'sendAll';
    const EVENT_ADD_USER = 'addUser';
}