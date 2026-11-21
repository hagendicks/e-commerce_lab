
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
        private function passwordError($password)
    {
        if (strlen($password) < 8) {
            return 'Password must contain at least 8 characters.';
        }

        if (!preg_match('/[A-Z]/', $password)) {
            return 'Password must contain at least one uppercase letter.';
        }

        if (!preg_match('/[a-z]/', $password)) {
            return 'Password must contain at least one lowercase letter.';
        }

        if (!preg_match('/[0-9]/', $password)) {
            return 'Password must contain at least one number.';
        }

        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            return 'Password must contain at least one special character.';
        }

        return null;
    }


    public function getCustomerById($id)
    {
        return $this->customerModel->getCustomerById($id);
    }


    public function updateProfile($id, $data)
    {
        if (strlen($data['name']) < 2) {
            return ['success' => false, 'error' => 'Please enter your full name.'];
        }

        if ($data['country'] === '' || $data['city'] === '') {
            return ['success' => false, 'error' => 'Country and city are required.'];
        }

        if (!preg_match('/^[0-9+\-\s]{7,15}$/', $data['contact'])) {
            return ['success' => false, 'error' => 'Please enter a valid contact number.'];
        }

        $ok = $this->customerModel->updateCustomer(
            $id, $data['name'], $data['country'], $data['city'], $data['contact']
        );

        return $ok
            ? ['success' => true]
            : ['success' => false, 'error' => 'Unable to update your details.'];
    }


    public function changePassword($id, $current, $new, $confirm)
    {
        if (!$this->customerModel->checkPassword($id, $current)) {
            return ['success' => false, 'error' => 'Your current password is incorrect.'];
        }

        $error = $this->passwordError($new);

        if ($error) {
            return ['success' => false, 'error' => $error];
        }

        if ($new !== $confirm) {
            return ['success' => false, 'error' => 'New passwords do not match.'];
        }

        if ($new === $current) {
            return ['success' => false, 'error' => 'Choose a password different from your current one.'];
        }

        return $this->customerModel->changePassword($id, $new)
            ? ['success' => true]
            : ['success' => false, 'error' => 'Unable to change your password.'];
    }


    public function deleteAccount($id, $password)
    {
        if (!$this->customerModel->checkPassword($id, $password)) {
            return ['success' => false, 'error' => 'Incorrect password. Your account was not deleted.'];
        }

        return $this->customerModel->deleteCustomer($id)
            ? ['success' => true]
            : ['success' => false, 'error' => 'This account has order history and cannot be deleted.'];
    }
}

?>
