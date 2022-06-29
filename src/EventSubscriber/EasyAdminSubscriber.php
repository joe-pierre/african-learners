<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    public function __construct(private UserPasswordHasherInterface $userPasswordEncoderInterface) {}
    
    public function onBeforeEntityPersistedEvent(BeforeEntityPersistedEvent $event): void
    {
        $this->hashPasswordIfUserAndPlainPasswordProvided($event);
    }
    
    public function onBeforeEntityUpdatedEvent(BeforeEntityUpdatedEvent $event): void
    {
        $this->hashPasswordIfUserAndPlainPasswordProvided($event->getEntityInstance());
    }

    private function hashPasswordIfUserAndPlainPasswordProvided($entityInstance): void
    {
        if ($entityInstance instanceof User && $entityInstance->getPlainPassword()) {
            $hashPassword = $this->userPasswordEncoderInterface->hashPassword($entityInstance, $entityInstance->getPlainPassword());

            $entityInstance->setPassword($hashPassword);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => 'onBeforeEntityPersistedEvent',
            BeforeEntityUpdatedEvent::class => 'onBeforeEntityUpdatedEvent',
        ];
    }
}