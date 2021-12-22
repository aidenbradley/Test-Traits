<?php

namespace Drupal\test_traits_queue\Plugin\QueueWorker;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @QueueWorker(
 *   id = "membership_reminders",
 *   title = @Translation("Sends reminder emails for members"),
 *   cron = {"time" = 10}
 * )
 */
class CreateNode extends QueueWorkerBase implements ContainerFactoryPluginInterface
{
    /** @var EntityTypeManager */
    private $entityTypeManager;

    /**
     * @param string|mixed $pluginId
     * @param mixed $pluginDefinition
     */
    public static function create(ContainerInterface $container, array $configuration, $pluginId, $pluginDefinition)
    {
        return new self(
            $configuration,
            $pluginId,
            $pluginDefinition,
            $container->get('entity_type.manager')
        );
    }

    public function __construct(array $configuration, $pluginId, $pluginDefinition, EntityTypeManager $entityTypeManager)
    {
        parent::__construct($configuration, $pluginId, $pluginDefinition);

        $this->entityTypeManager = $entityTypeManager;
    }

    /** @param array|mixed $data */
    public function processItem($data): void
    {
        if(isset($data['title']) === false) {
            return;
        }

        $this->entityTypeManager->getStorage('node')->create([
            'title' => $data['title'],
            'type' => 'page',
        ])->save();
    }
}
