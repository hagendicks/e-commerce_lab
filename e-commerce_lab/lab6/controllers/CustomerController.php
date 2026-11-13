
<?php

require_once __DIR__ . '/../classes/CustomerClass.php';

class CustomerController
{
    private $customerModel;

    public function __construct()
    {
        $this->customerModel = new CustomerClass();
    }

    public function register($data)
    {
        $password = $data['password'];

        if (strlen($password) < 8) {
            return [
                'success' => false,
                'error' => 'Password must contain at least 8 characters.'
            ];
        }

        if (!preg_match('/[A-Z]/', $password)) {
            return [
                'success' => false,
                'error' => 'Password must contain at least one uppercase letter.'
            ];
        }

        if (!preg_match('/[a-z]/', $password)) {
            return [
                'success' => false,
                'error' => 'Password must contain at least one lowercase letter.'
            ];
        }

        if (!preg_match('/[0-9]/', $password)) {
            return [
                'success' => false,
                'error' => 'Password must contain at least one number.'
            ];
        }

        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            return [
                'success' => false,
                'error' => 'Password must contain at least one special character.'
            ];
        }

        if ($this->customerModel->emailExists($data['email'])) {
            return [
                'success' => false,
                'error' => 'Email already registered.'
            ];
        }

        $success = $this->customerModel->addCustomer(
            $data['name'],
            $data['email'],
            $data['password'],
            $data['country'],
            $data['city'],
            $data['contact']
        );

        if ($success) {
            return [
                'success' => true
            ];
        }

        return [
            'success' => false,
            'error' => 'Unable to create account.'
        ];
    }

    public function login($email, $password)
    {
        $customer = $this->customerModel->login(
            $email,
            $password
        );

        if (!$customer) {
            return [
                'success' => false,
                'error' => 'Invalid email or password.'
            ];
        }

        return [
            'success' => true,
            'customer' => $customer
        ];
    }
}

?>
