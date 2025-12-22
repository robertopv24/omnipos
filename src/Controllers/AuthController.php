<?php

namespace OmniPOS\Controllers;

use OmniPOS\Core\Request;
use OmniPOS\Core\Response;
use OmniPOS\Core\Database;
use OmniPOS\Core\Session;
use PDO;

class AuthController extends Controller
{
    public function loginForm(Request $request, Response $response)
    {
        $this->view->setLayout('public'); // Use public layout for login
        return $this->render('auth/login', [
            'title' => 'Iniciar Sesión'
        ]);
    }

    public function login(Request $request, Response $response)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        if (!$email || !$password) {
            return $this->render('auth/login', ['error' => 'Por favor complete todos los campos.', 'title' => 'Iniciar Sesión']);
        }

        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Login Success
            Session::regenerate();
            Session::set('user_id', $user['id']);
            Session::set('user_name', $user['name']);
            Session::set('role', $user['role']);
            Session::set('account_id', $user['account_id']);

            // Verificar cuántos negocios tiene la cuenta
            $stmt = $pdo->prepare("SELECT id FROM businesses WHERE account_id = :account_id");
            $stmt->execute(['account_id' => $user['account_id']]);
            $businesses = $stmt->fetchAll();

            if (count($businesses) === 0) {
                // Sin negocios, al selector para agregar uno
                $response->redirect('/account/businesses');
            } elseif (count($businesses) === 1) {
                // Solo uno, seleccionarlo automáticamente
                Session::set('business_id', $businesses[0]['id']);

                // Obtener nombre del negocio único
                $stmt = $pdo->prepare("SELECT name FROM businesses WHERE id = :id");
                $stmt->execute(['id' => $businesses[0]['id']]);
                Session::set('business_name', $stmt->fetchColumn());

                $response->redirect('/dashboard');
            } else {
                // Múltiples negocios, obligar a seleccionar
                $response->redirect('/account/businesses');
            }
        } else {
            return $this->render('auth/login', ['error' => 'Credenciales inválidas.', 'title' => 'Iniciar Sesión']);
        }
    }

    public function registerForm(Request $request, Response $response)
    {
        $this->view->setLayout('public');
        return $this->render('auth/register', [
            'title' => 'Registro de Cuenta'
        ]);
    }

    public function register(Request $request, Response $response)
    {
        $name = $request->input('name');
        $email = $request->input('email');
        $phone = $request->input('phone');
        $documentId = $request->input('document_id');
        $address = $request->input('address');
        $businessName = $request->input('business_name');
        $password = $request->input('password');

        if (!$name || !$email || !$businessName || !$password || !$phone || !$documentId || !$address) {
            return $this->render('auth/register', ['error' => 'Por favor complete todos los campos obligatorios.', 'title' => 'Registro']);
        }

        $pdo = Database::connect();
        
        // Verificar si el email ya existe
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            return $this->render('auth/register', ['error' => 'El correo electrónico ya está registrado.', 'title' => 'Registro']);
        }

        try {
            $pdo->beginTransaction();

            // 1. Crear Cuenta (Account)
            $stmt = $pdo->prepare("INSERT INTO accounts (company_name, billing_email, status) VALUES (:name, :email, 'trialing')");
            $stmt->execute([
                'name' => $businessName,
                'email' => $email
            ]);
            $accountId = $pdo->lastInsertId();

            // 2. Crear Negocio (Business)
            $stmt = $pdo->prepare("INSERT INTO businesses (account_id, name) VALUES (:aid, :name)");
            $stmt->execute(['aid' => $accountId, 'name' => $businessName]);
            $businessId = $pdo->lastInsertId();

            // 3. Crear Usuario (User) como account_admin
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (account_id, name, email, password, phone, document_id, address, role) VALUES (:aid, :name, :email, :pass, :phone, :doc, :addr, 'account_admin')");
            $stmt->execute([
                'aid' => $accountId,
                'name' => $name,
                'email' => $email,
                'pass' => $hashedPassword,
                'phone' => $phone,
                'doc' => $documentId,
                'addr' => $address
            ]);

            $pdo->commit();

            Session::flash('success', 'Cuenta creada con éxito. Ahora puedes iniciar sesión.');
            return $response->redirect('/login');

        } catch (\Exception $e) {
            $pdo->rollBack();
            return $this->render('auth/register', ['error' => 'Ocurrió un error durante el registro: ' . $e->getMessage(), 'title' => 'Registro']);
        }
    }

    public function passwordResetForm(Request $request, Response $response)
    {
        $this->view->setLayout('public');
        return $this->render('auth/password_reset', [
            'title' => 'Restablecer Contraseña'
        ]);
    }

    public function sendResetLink(Request $request, Response $response)
    {
        // Lógica para enviar correo
        Session::flash('success', 'Si el correo existe, recibirá un enlace de recuperación.');
        return $response->redirect('/login');
    }

    public function logout(Request $request, Response $response)
    {
        Session::destroy();
        $response->redirect('/login');
    }
}
