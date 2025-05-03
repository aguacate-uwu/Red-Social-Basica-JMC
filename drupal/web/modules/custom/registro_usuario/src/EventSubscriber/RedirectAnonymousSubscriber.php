<?php

namespace Drupal\registro_usuario\EventSubscriber;

use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Drupal\Core\Path\CurrentPathStack;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Routing\RedirectDestinationInterface;

/**
 * Redirige a los usuarios anónimos desde la página de inicio a la página de registro.
 */
class RedirectAnonymousSubscriber implements EventSubscriberInterface {

    /**
     * El servicio del usuario actual.
     *
     * @var \Drupal\Core\Session\AccountProxyInterface
     */
    protected $currentUser;

    /**
     * El servicio de la ruta actual.
     *
     * @var \Drupal\Core\Path\CurrentPathStack
     */
    protected $currentPath;

    /**
     * El servicio de la fábrica de respuestas de redirección confiable.
     *
     * @var \Drupal\Core\Routing\RedirectDestinationInterface
     */
    protected $redirectDestination;

        /**
         * Construye un nuevo RedirectAnonymousSubscriber.
         *
         * @param \Drupal\Core\Session\AccountProxyInterface $current_user
         * El servicio del usuario actual.
         * @param \Drupal\Core\Path\CurrentPathStack $current_path
         * El servicio de la ruta actual.
         * @param \Drupal\Core\Routing\RedirectDestinationInterface $redirect_destination
         * El servicio de destino de redirección.
         */

        public function __construct(AccountProxyInterface $current_user, CurrentPathStack $current_path, RedirectDestinationInterface $redirect_destination) {
            $this->currentUser = $current_user;
            $this->currentPath = $current_path;
            $this->redirectDestination = $redirect_destination;
        }
    public static function getSubscribedEvents() {
        return [
            KernelEvents::REQUEST => [['redirectToRegistration']],
        ];
    }

    /**
     * Redirige a los usuarios anónimos a la página de registro si están en la página de inicio.
     *
     * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
     * El evento de solicitud.
     */
    public function redirectToRegistration(RequestEvent $event) {
        // Verifica si el usuario es anónimo y está en la página de inicio.
        if ($this->currentUser->isAnonymous() && $this->currentPath->getPath() === '/') {
            // Define la ruta de registro.
            $registration_path = '/registro';

            // Crea una respuesta de redirección a la página de registro.
            $response = new TrustedRedirectResponse($registration_path);
            $event->setResponse($response);
        }
    }
}