<?php

namespace Drupal\crear_publicaciones\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

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
    $elements = [];

    $entity = $items->getEntity();
    $count = 0;

    if ($entity->hasField('comment') && !$entity->get('comment')->isEmpty()) {
      $comment_field = $entity->get('comment');
      $count = $comment_field->comment_count ?? 0;
    }

    $elements[0] = [
      '#markup' => $this->t('@count comentario(s)', ['@count' => $count]),
    ];

    return $elements;
  }
}