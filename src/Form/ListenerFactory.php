<?php 

namespace App\Form;

use Symfony\Component\Form\Event\PostSubmitEvent;

class ListenerFactory
{
    public function attachedTimestamp(): callable
    {
        return function (PostSubmitEvent $event) {
            
            $data = $event->getData();

            if (!$data->getId()) {
                $data->setCreatedAt(new \DateTimeImmutable());
            }
        };
    }
}