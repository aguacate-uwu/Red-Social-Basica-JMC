<?php

namespace Drupal\registro_usuario\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Proporciona un bloque con un botón para registrarse.
 *
 * @Block(
 *   id = "registro_button_block",
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
      '#markup' => '<a href="/registro" class="button button--primary">' . $this->t('Registrarse') . '</a>',
      '#attached' => [
        'library' => [
          'core/drupal.dialog.ajax', // Asegura que los estilos de botones estén disponibles.
        ],
      ],
    ];
  }

}