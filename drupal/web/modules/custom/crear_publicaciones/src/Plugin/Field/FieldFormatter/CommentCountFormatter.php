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

    // Inicializamos en cero por defecto.
    $count = 0;

    // Solo si la entidad está guardada y tiene el campo correspondiente.
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

    // Devolver en un contenedor, para que no sobrescriba claves de los render arrays esperados
    $elements[] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['comment-count']],
      'content' => [
        '#markup' => $this->formatPlural($count, '1 comentario', '@count comentarios'),
      ],
      '#cache' => [
        'contexts' => ['user.roles', 'url'],
        'tags' => ['comment_list'],
      ],
    ];

    return $elements;
  }

}