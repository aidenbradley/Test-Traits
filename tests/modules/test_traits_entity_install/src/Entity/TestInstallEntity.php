<?php

namespace Drupal\test_traits_entity_install\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * @ContentEntityType(
 *   id = "test_install_entity",
 *   label = @Translation("Test Install Entity"),
 *   base_table = "test_install_entity",
 *   entity_keys = {
 *     "id" = "id",
 *   },
 * )
 */
class TestInstallEntity extends ContentEntityBase
{
    public static function baseFieldDefinitions(EntityTypeInterface $entity_type)
    {
        $fields['id'] = BaseFieldDefinition::create('integer')
            ->setLabel(t('ID'))
            ->setDescription(t('The ID of the Advertiser entity.'))
            ->setReadOnly(TRUE);

        $fields['text'] = BaseFieldDefinition::create('text')
            ->setLabel(t('Text'));

        return $fields;
    }
}
