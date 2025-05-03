<?php

namespace Drupal\crear_publicaciones\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\node\Entity\Node;

/**
 * Proporciona un bloque para crear publicaciones de usuario mostrando el formulario de nodo.
 *
 * @Block(
 *   id = "crear_publicacion_block",
 *   admin_label = @Translation("Crear Publicación (Formulario de Nodo)"),
 *   category = @Translation("Contenido")
 * )
 */
class CrearPublicacionBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * El constructor de formularios de Drupal.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * El usuario actual.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Constructs a new CrearPublicacionNodoFormBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Form\FormBuilderInterface $form_builder
   *   The form builder service.
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, FormBuilderInterface $form_builder, AccountInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->formBuilder = $form_builder;
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('form_builder'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Verifica si el usuario tiene permiso para crear contenido 'publicacion_usuario'.
    if (!$this->currentUser->hasPermission('create publicacion_usuario content')) {
      return [
        '#markup' => $this->t('No tienes permiso para crear publicaciones de usuario.'),
      ];
    }

    // Crea un nuevo nodo del tipo 'publicacion_usuario'.
    $node = Node::create([
      'type' => 'publicacion_usuario',
    ]);

    $form = \Drupal::service('entity.form_builder')->getForm($node, 'default');
    
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    // Permite el acceso al bloque solo si el usuario puede crear contenido 'publicacion_usuario'.
    return AccessResult::allowedIf($account->hasPermission('create publicacion_usuario content'));
  }

}