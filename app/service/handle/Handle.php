<?php

namespace app\service\handle;

use app\service\ClientServerService;

interface Handle
{
    public function handle(ClientServerService $clientServerService);
}