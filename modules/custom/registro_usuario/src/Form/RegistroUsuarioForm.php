<?php

namespace Drupal\registro_usuario\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;

/**
 * Clase RegistroUsuarioForm.
 *
 * Este formulario se utiliza para el registro de usuarios en el módulo "registro_usuario".
 * Recoge detalles del usuario como nombre, correo electrónico y contraseña, valida la entrada,
 * y crea una nueva entidad de usuario en el sistema Drupal tras un envío exitoso.
 *
 * @package Drupal\registro_usuario\Form
 */
class RegistroUsuarioForm extends FormBase {

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'registro_usuario_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $form['nombre'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Nombre'),
            '#required' => TRUE,
        ];

        $form['email'] = [
            '#type' => 'email',
            '#title' => $this->t('Correo electrónico'),
            '#required' => TRUE,
        ];

        $form['password'] = [
            '#type' => 'password',
            '#title' => $this->t('Contraseña'),
            '#required' => TRUE,
        ];

        $form['confirm_password'] = [
            '#type' => 'password',
            '#title' => $this->t('Confirmar contraseña'),
            '#required' => TRUE,
        ];

        $form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Registrar'),
        ];

        return $form;
    }

    /**
     * {@inheritdoc}
     *
     * Validación del formulario.
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        if (!filter_var($form_state->getValue('email'), FILTER_VALIDATE_EMAIL)) {
            $form_state->setErrorByName('email', $this->t('El correo electrónico no es válido.'));
        }

        if (strlen($form_state->getValue('password')) < 6) {
            $form_state->setErrorByName('password', $this->t('La contraseña debe tener al menos 6 caracteres.'));
        }

        if ($form_state->getValue('password') !== $form_state->getValue('confirm_password')) {
            $form_state->setErrorByName('confirm_password', $this->t('Las contraseñas no coinciden.'));
        }

        // Verificar si ya existe un usuario con este nombre o correo electrónico.
        $existing_user = \Drupal::entityTypeManager()->getStorage('user')->loadByProperties([
            'name' => $form_state->getValue('nombre'),
        ]);
        if (!empty($existing_user)) {
            $form_state->setErrorByName('nombre', $this->t('Ya existe un usuario con el nombre de usuario %name.', ['%name' => $form_state->getValue('nombre')]));
        }

        $existing_email = \Drupal::entityTypeManager()->getStorage('user')->loadByProperties([
            'mail' => $form_state->getValue('email'),
        ]);
        if (!empty($existing_email)) {
            $form_state->setErrorByName('email', $this->t('Ya existe un usuario con la dirección de correo electrónico %email.', ['%email' => $form_state->getValue('email')]));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $nombre = $form_state->getValue('nombre');
        $email = $form_state->getValue('email');
        $password = $form_state->getValue('password');

        // Crear un nuevo usuario utilizando el sistema de entidades de usuario de Drupal.
        $user = User::create([
            'name' => $nombre,
            'mail' => $email,
            'pass' => $password,
            'status' => 1,
            'roles' => ['authenticated']
        ]);

        // Guardar el usuario en la base de datos con manejo de errores.
        try {
            $user->save();
            \Drupal::messenger()->addMessage($this->t('Usuario @nombre registrado con éxito.', [
                '@nombre' => $nombre,
            ]));
            $form_state->setRedirect('<front>'); // Redirigir a la página principal tras el registro.
        } catch (\Exception $e) {
            \Drupal::messenger()->addError($this->t('Ocurrió un error al registrar el usuario: @message', [
                '@message' => $e->getMessage(),
            ]));
            \Drupal::logger('registro_usuario')->error('Error al registrar el usuario: @error', ['@error' => $e->getMessage()]);
        }
    }
}