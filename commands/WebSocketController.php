<?php

namespace app\commands;

use yii\console\Controller;
use Ratchet;
use Ratchet\App;
use app\commands\websocket\Chat;

class WebSocketController extends Controller
{
    public function actionStart()
    {
        $app = new App(env('WEB_SOCKET_HOST'), env('WEB_SOCKET_PORT'), env('WEB_SOCKET_ADDRESS'));
        $app->route('/chat', new Chat, array('*'));
        $app->route('/echo', new Ratchet\Server\EchoServer, array('*'));
        $app->run();
    }
}


