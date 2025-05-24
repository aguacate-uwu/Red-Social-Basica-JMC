<?php

namespace Drupal\crear_publicaciones\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin de formato para contar comentarios.
 *
 * @FieldFormatter(
 *   id = "comment_entity_count",
 *   label = @Translation("Conteo de comentarios"),
 *   field_types = {
 *     "comment"
 *   }
 * )
 */
class CommentCountFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $entity = $items->getEntity();
    $elements = [];

    if (!$entity->isNew() && $entity->hasField('comment')) {
      $comments = \Drupal::entityTypeManager()
        ->getStorage('comment')
        ->loadByProperties([
          'entity_id' => $entity->id(),
          'entity_type' => $entity->getEntityTypeId(),
          'field_name' => $this->fieldDefinition->getName(),
          'status' => 1,
        ]);
      $count = count($comments);
    }
    else {
      $count = 0;
    }

    // Devolver solo markup simple, sin claves que otros módulos esperen
    $elements[] = [
      '#markup' => $this->formatPlural($count, '1 comentario', '@count comentarios'),
      '#cache' => [
        'contexts' => ['user.roles', 'url'],
        'tags' => ['comment_list'],
      ],
    ];

    return $elements;
  }

}