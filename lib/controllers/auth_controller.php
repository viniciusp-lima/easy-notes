<?php

if(!defined('ABSPATH')) {
    exit;
}

require_once CORE_PATH . 'system.php';
require_once UTILS_PATH . 'change_password.php';
require_once UTILS_PATH . 'email_sender.php';
require_once HELPERS_PATH . 'components_helper.php';

class AuthController extends System {
    public function __construct() {
        parent::__construct();
        $this->comp = new ComponentsHelper();
    }

    public function login() {
        $this->render(__FUNCTION__);
    }

    public function signup() {
        $this->render(__FUNCTION__);
    }

    public function forgot() {
        $this->render(__FUNCTION__);
    }

    public function authenticate_user() {
        if(filter_var($this->data->auth->email, FILTER_VALIDATE_EMAIL)) {
			$user = get_user_by("email", $this->data->auth->email);

			$login_data = [
				"user_login"    => $user->user_login,
				"user_password" => $this->data->auth->password,
				"remember"      => $this->data->auth->remember
			];

			$user_verify = wp_signon($login_data, false);
    
			if ($user) {
				if (!is_wp_error($user_verify)) {
                    $this->send_json(['success' => STATUS_SUCCESS, 'pathName' => '/app']);
				} else {
                    $message = $this->comp->toast(['message' => 'Incorrect email or password', 'color' => 'warning']);
                    $this->send_json(['success' => STATUS_ERROR, 'message' => $message]);
                }
			} else {
                $message = $this->comp->toast(['message' => 'Incorrect email or password', 'color' => 'warning']);
                $this->send_json(['success' => STATUS_ERROR, 'message' => $message]);
			}
        } else {
            $message = $this->comp->toast(['message' => 'Invalid email', 'color' => 'warning']);
            $this->send_json(['success' => STATUS_ERROR, 'message' => $message]);
        }
    }

    public function create_new_user() {
        if (filter_var($this->data->user->email, FILTER_VALIDATE_EMAIL)) {
            if (!email_exists($this->data->user->email)) {

                $userLogin = strtolower($this->data->user->first_name.$this->data->user->last_name);

                if(username_exists($userLogin)) {
                    $userLogin .= count_users()['total_users'];
                }

                $userData = [
                    'first_name' => $this->data->user->first_name,
                    'last_name'  => $this->data->user->last_name,
                    'user_login' => $userLogin,
                    'user_pass'  => $this->data->user->password,
                    'user_email' => $this->data->user->email,
                ];
                
                $userId = wp_insert_user($userData);

                if (!is_wp_error($userId)) {
                    $message = $this->comp->toast(['message' => 'User created successfully', 'color' => 'success']);
                    $this->send_json(['success' => STATUS_SUCCESS, 'message' => $message]);
                } else {
                    $message = $this->comp->toast(['message' => $userId->get_error_message(), 'color' => 'warning']);
                    $this->send_json(['success' => STATUS_ERROR, 'message' => $message]);
                }

            } else {
                $message = $this->comp->toast(['message' => 'User with this email already exists', 'color' => 'warning']);
                $this->send_json(['success' => STATUS_ERROR, 'message' => $message]);
            }

        } else {
            $message = $this->comp->toast(['message' => 'Invalid email', 'color' => 'warning']);
            $this->send_json(['success' => STATUS_ERROR, 'message' => $message]);
        }
    }
    
    public function change_password() {
        if(!isset($this->data->forgot->validation)) {
            if(email_exists($this->data->forgot->email)) {
                $changePassword = new ChangePassword();
                $newPassword = $changePassword->new_random_password();

                $user = get_user_by('email', $this->data->forgot->email);
                $changePassword = wp_set_password($newPassword, $user->data->ID);

                if(!is_wp_error($changePassword)) {
                    $data = [
                        'to'        => $this->data->forgot->email,
                        'subject'   => 'Change password'
                    ];
        
                    $emailSender = new EmailSender('forgot_password');
                    $result = $emailSender->send_email($data, ['password' => $newPassword]);
    
                    if($result) {
                        $message = $this->comp->toast(['message' => 'An email containing a new password has been successfully sent to the address provided.', 'color' => 'success']);
                        $this->send_json(['success' => STATUS_ERROR, 'message' => $message]);
                    }
                }
            } else {
                $message = $this->comp->toast(['message' => 'Email not found', 'color' => 'warning']);
                $this->send_json(['success' => STATUS_ERROR, 'message' => $message]);
            }
        }
    }

    public function logout_user() {
        wp_logout();
        $this->send_json(['success' => STATUS_SUCCESS]);
        wp_die();
    }
}