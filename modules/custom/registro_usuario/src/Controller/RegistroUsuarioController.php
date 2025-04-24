<?php

namespace Drupal\registro_usuario\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controlador para la página de registro de usuario.
 */
class RegistroUsuarioController extends ControllerBase {

protected $formBuilder;

public function __construct(FormBuilderInterface $form_builder) {
    $this->formBuilder = $form_builder;
}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('form_builder')
    );
  }

  /**
   * Muestra el formulario de registro de usuario.
   *
   * @return array
   * Un array renderizable que representa el formulario.
   */
  public function register() {
    return $this->formBuilder()->getForm('Drupal\registro_usuario\Form\RegistroUsuarioForm');
  }
}