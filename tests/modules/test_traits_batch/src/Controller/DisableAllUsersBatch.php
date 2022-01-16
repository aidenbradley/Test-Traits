<?php

namespace Drupal\test_traits_batch\Controller;

use Drupal\Core\Batch\BatchBuilder;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\user\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class DisableAllUsersBatch implements ContainerInjectionInterface
{
    /** @var EntityStorageInterface */
    private $userStorage;

    public static function create(ContainerInterface $container): self
    {
        return new self(
            $container->get('entity_type.manager')->getStorage('user')
        );
    }

    public function __construct(EntityStorageInterface $userStorage)
    {
        $this->userStorage = $userStorage;
    }

    public function prepareBatch(): Response
    {
        $builder = new BatchBuilder();
        $builder->setTitle('Disable Users')
            ->setInitMessage('Disabling users. Processed @current.')
            ->setProgressMessage('Processed @current out of @total.')
            ->setErrorMessage('Batch has encountered an error.');

        /** @var \Drupal\user\Entity\User $user */
        foreach ($this->userStorage->loadMultiple() as $user) {
            $builder->addOperation([$this, 'disableUser'], [$user]);
        }

        batch_set(array_merge($builder->toArray(), [
            'progressive' => false,
        ]));

        return Response::create('', 204);
    }

    public function prepareBatchAndProcess(): RedirectResponse
    {
        $this->prepareBatch();

        return batch_process('/');
    }

    public function disableUser(User $user)
    {
        $user->status->value = 0;

        $user->save();
    }
}
