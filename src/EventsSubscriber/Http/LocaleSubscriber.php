<?php
namespace App\EventsSubscriber\Http;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    private $defaultLocale;

    public function __construct(string $defaultLocale = 'en')
    {
        $this->defaultLocale = $defaultLocale;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                ['onKernelRequest', 20]
            ],
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) {
            return;
        }
        
        if (null === $request->attributes->get('_locale')) {
            $request->getSession()->set('_locale', 
                $request->getPreferredLanguage() ?? $this->defaultLocale);

            $request->setLocale($request->getSession()->get('_locale'),
                $request->getPreferredFormat() ?? $this->defaultLocale);
        
        }
    }

    
}