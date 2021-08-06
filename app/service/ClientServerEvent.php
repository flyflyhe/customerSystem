<?php

namespace app\service;

interface ClientServerEvent
{
    const EVENT_LOGIN = 'login';
    const EVENT_LOGOUT = 'logout';
    const EVENT_SEND = 'send';
}