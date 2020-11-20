<?php

declare(strict_types=1);

namespace App\Command;

use App\Constants\DeviceConstant;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Hyperf\Utils\Coroutine;
use Hyperf\Utils\Coroutine\Concurrent;
use Hyperf\WebSocketClient\ClientFactory;
use Hyperf\WebSocketClient\Frame;
use Phper666\JWTAuth\JWT;
use Psr\Container\ContainerInterface;
use Hyperf\Di\Annotation\Inject;
use Swoole\Coroutine\Http\Client;
use Symfony\Component\Console\Input\InputArgument;


/**
 * @Command
 */
class PushCommand extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject
     * @var ClientFactory
     */
    protected $clientFactory;

    /**
     * @Inject
     * @var JWT
     */
    protected $jwt;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('push:start');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Hyperf Demo Command');
    }

    public function handle()
    {
//        \Swoole\Coroutine::
//
//        Co\run(function(){
        $count = (int)$this->input->getArgument('count') ?? 1;

        $client = $msg = $ret = [];
        $concurrent = new Concurrent($count);
        for ($i = 0; $i < $count; $i++) {

            $concurrent->create(function () use ($client, $msg, $ret, $i) {

                $uid = mt_rand(1, 1000000);
                $siteId = mt_rand(1, 3);
                $device = DeviceConstant::getRandom();


                $userInfo = [
                    'uid' => $uid,
                    'site_id' => $siteId,
                    'device' => $device
                ];
                $token = $this->jwt->getToken($userInfo);

                $this->line('Token: ' . $token);


                $client[$i] = new Client(env('WS_CLIENT_HOST'), (int)env('WS_CLIENT_PORT'), (bool)env('WS_CLIENT_SSL'));
                $ret[$i] = $client[$i]->upgrade("/?token=" . $token);
                if ($ret[$i]) {
                    while (true) {
//
//                 }   $client->push("hello");
                        $recv = $client[$i]->recv();
                        $data = $recv->data;
                        if (!empty($data)) {
                            var_dump($data);
                        }
                        \Swoole\Coroutine::sleep(0.5);
                    }
                }

            });
        }
//        });
    }

    protected function getArguments(){
        return [
            ['count', InputArgument::REQUIRED, '併發數']
        ];
    }

    public function handle2()
    {
        $this->line('Hello Hyperf!', 'info');

        $client = $msg = [];

//        Coroutine::create(function () use ($client, $msg){
        $count = 2;
        $concurrent = new Concurrent(1000);


        for ($i = 0; $i < $count; $i++) {

            $concurrent->create(function () use ($client, $msg, $i) {

                $uid = mt_rand(1, 1000000);
                $siteId = mt_rand(1, 3);
                $device = DeviceConstant::getRandom();


                $userInfo = [
                    'uid' => $uid,
                    'site_id' => $siteId,
                    'device' => $device
                ];
                $token = $this->jwt->getToken($userInfo);

                $this->line('Token: ' . $token);

                $host = 'ws://localhost:9502/?token=' . $token;
//
                $client[$i] = $this->clientFactory->create($host, false);

//                $client[$i]->


                $client[$i]->push('hello');
                /** @var Frame $msg */
                $msg[$i] = $client[$i]->recv(60);
                $this->line($msg[$i]->data);

//                \Swoole\Coroutine::sleep(60);
//                \co::sleep(60);
            });
            //$client->close();
        }
//        });
    }
}
