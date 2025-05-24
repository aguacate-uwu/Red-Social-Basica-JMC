<?php

namespace Drupal\crear_publicaciones\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\comment\CommentManagerInterface;

/**
 * Plugin de formato para contar comentarios.
 *
 * @FieldFormatter(
 * id = "comment_entity_count",
 * label = @Translation("Conteo de comentarios"),
 * field_types = {
 * "comment"
 * }
 * )
 */
class CommentCountFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  /**
   * The comment manager.
   *
   * @var \Drupal\comment\CommentManagerInterface
   */
  protected $commentManager;

  /**
   * Constructs a new CommentCountFormatter instance.
   *
   * @param string $plugin_id
   * The plugin_id for the formatter.
   * @param mixed $plugin_definition
   * The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   * The field definition.
   * @param array $settings
   * The formatter settings.
   * @param string $label
   * The formatter label display setting.
   * @param string $view_mode
   * The view mode.
   * @param array $third_party_settings
   * Third party settings.
   * @param \Drupal\comment\CommentManagerInterface $comment_manager
   * The comment manager.
   */
  public function __construct($plugin_id, $plugin_definition, \Drupal\Core\Field\FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, CommentManagerInterface $comment_manager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->commentManager = $comment_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('comment.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    $entity = $items->getEntity();
    $count = 0;

    if ($entity->hasField('comment') && $entity->get('comment')->access('view', \Drupal::currentUser())) {
      // estadísticas de comentarios
      $comment_statistics = $this->commentManager->getCommentStatistics($entity->id(), $entity->getEntityTypeId());
      if ($comment_statistics && isset($comment_statistics->comment_count)) {
        $count = $comment_statistics->comment_count;
      }
    }

    $elements[0] = [
      '#markup' => $this->formatPlural($count, '1 comentario', '@count comentarios'),
    ];

    return $elements;
  }

}