<?php
/**
 * Created by IntelliJ IDEA.
 * User: winglechen
 * Date: 16/3/14
 * Time: 00:02
 */

namespace Zan\Framework\Network\Http\Request;


use Zan\Framework\Contract\Network\Request;
use Zan\Framework\Foundation\Exception\ZanException;
use Zan\Framework\Network\Server\Middleware\MiddlewareManager;
use Zan\Framework\Network\http\Dispatcher;
use Zan\Framework\Utilities\DesignPattern\Context;
use Zan\Framework\Foundation\Container\Di;

class RequestTask {
    /**
     * @var Request
     */
    private $request;
    /**
     * @var \swoole_response
     */
    private $swooleResponse;
    /**
     * @var Context
     */
    private $context;

    public function __construct(Request $request, $swooleResponse, Context $context)
    {
        $this->request = $request;
        $this->swooleResponse = $swooleResponse;
        $this->context = $context;
    }

    public function run()
    {
        $middlewareManager = MiddlewareManager::getInstance();

        $response = (yield $middlewareManager->executeFilters($this->request, $this->context));
        if(null !== $response){
            yield $response->sendBy($this->swooleResponse);
            return null;
        }

        $Dispatcher = Di::getInstance()->make(Dispatcher::class);
        $response = (yield $Dispatcher->dispatch($this->request, $this->context));

        if(null === $response){
            throw new ZanException('');
        }else{
            yield $response->sendBy($this->swooleResponse);
            return null;
        }

        $middlewareManager->executeTerminators($this->request, $response, $this->context);
    }



}