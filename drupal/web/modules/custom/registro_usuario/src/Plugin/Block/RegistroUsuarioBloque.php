<?php

namespace Drupal\registro_usuario\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Proporciona un bloque con un botón para registrarse.
 *
 * @Block(
 *   id = "registro_usuario_bloque",
 *   admin_label = @Translation("Botón de Registro"),
 *   category = @Translation("Custom")
 * )
 */
class RegistroButtonBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#type' => 'markup',
      '#markup' => '<a href="/registro" class="button">' . $this->t('Registrarse') . '</a>',
    ];
  }

}