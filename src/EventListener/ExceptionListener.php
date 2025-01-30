<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

class ExceptionListener
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        // On récupère l'objet exception qui a causé l'événement.
        $exception = $event->getThrowable();

        // Si l'exception est de type NotFoundHttpException (erreur 404).
        if ($exception instanceof NotFoundHttpException) {
            $statusCode = Response::HTTP_NOT_FOUND;
            $statusText = 'Page non trouvée';
            $message = "Le contenu que vous recherchez n'existe pas ou a été supprimé.";
        // Si l'exception n'est pas une instance de HttpExceptionInterface (par exemple, TypeError),
        // on la traite comme une erreur interne du serveur (erreur 500).
        } elseif (!$exception instanceof HttpExceptionInterface) {
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
            $statusText = 'Erreur interne du serveur';
            $message = 'Une erreur inattendue est survenue.';
        } else {
            return; // Pour toutes les autres erreurs HTTP (comme 403, 401, etc.), Symfony gère automatiquement l'exception.
        }

        // On génèrere le template d'erreur.
        $content = $this->twig->render('errors/error.html.twig', [
            'status_code' => $statusCode,
            'status_text' => $statusText,
            'message' => $message,
        ]);

        // On remplace la réponse par défaut avec la réponse créée.
        $response = new Response($content, $statusCode);
        $event->setResponse($response);
    }
}
