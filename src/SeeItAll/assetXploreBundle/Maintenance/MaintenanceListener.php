<?php



use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class MaintenanceListener
{
    

    public function onKernelRequest(GetResponseEvent $event)
    {
        

        $event->setResponse(
          new Response(
            'site is in maintenance mode',
            Response::HTTP_SERVICE_UNAVAILABLE
          )
        );
        $event->stopPropagation();
    }
}