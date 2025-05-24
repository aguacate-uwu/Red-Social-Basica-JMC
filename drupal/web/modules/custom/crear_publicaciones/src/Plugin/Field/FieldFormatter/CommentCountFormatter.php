

<?php

namespace Drupal\crear_publicaciones\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface; // Necesario para obtener el storage

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
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

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
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   * The entity type manager.
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    \Drupal\Core\Field\FieldDefinitionInterface $field_definition,
    array $settings,
    $label,
    $view_mode,
    array $third_party_settings,
    EntityTypeManagerInterface $entity_type_manager // Inyectamos el servicio
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->entityTypeManager = $entity_type_manager;
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
      $container->get('entity_type.manager') // Obtenemos el servicio desde el contenedor
    );
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    $entity = $items->getEntity();
    $count = 0;

    // Solo si la entidad tiene un campo de comentarios y es visible.
    if ($entity->hasField('comment') && $entity->get('comment')->access('view', \Drupal::currentUser())) {
      // Cargar los comentarios por las propiedades de la entidad a la que están adjuntos.
      $comments = $this->entityTypeManager
        ->getStorage('comment')
        ->loadByProperties([
          'entity_id' => $entity->id(),
          'entity_type' => $entity->getEntityTypeId(),
          'field_name' => $this->fieldDefinition->getName(), // Usar el nombre del campo actual
          'status' => 1, // Solo contar comentarios publicados
        ]);

      $count = count($comments);
    }

    $elements[0] = [
      '#markup' => $this->formatPlural($count, '1 comentario', '@count comentarios'),
    ];

    return $elements;
  }
}