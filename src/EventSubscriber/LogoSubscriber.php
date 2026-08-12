<?php

namespace App\EventSubscriber;

use App\Services\FileUploader;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class LogoSubscriber implements EventSubscriberInterface
{
    public function __construct(private FileUploader $fileUploader) {}

    public function onFormPostSubmit(FormEvent $event): void
    {
        $form = $event->getForm();
        $logoFile = $form->get('logo')->getData();

        if(!$logoFile) return;
    
        $filename = $this->fileUploader->upload($logoFile);
        $company = $form->getData();
        $company->setLogo($filename);
    }

    public static function getSubscribedEvents(): array
    {
        return [
           FormEvents::POST_SUBMIT => 'onFormPostSubmit',
        ];
    }
}
