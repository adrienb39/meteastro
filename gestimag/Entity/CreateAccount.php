<?php

session_start();
require __DIR__ . "/../../config/connexion_bdd.php"; // Assurez-vous que ce fichier contient la fonction createMysqliConnection()

class CreateAccount {
    private $mysqli;
    private $email;
    private $name;
    private $password;
    private $cpassword;
    private $software_id;  // Ajoutez cette propriété pour le logiciel
    private $errors = array();

    // Constructeur
    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }

    // Méthode pour enregistrer un nouvel utilisateur
    public function register($name, $email, $password, $cpassword, $software_id, $consent) {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->cpassword = $cpassword;
        $this->software_id = $software_id; // Stocker l'ID du logiciel sélectionné

        // Validation des entrées
        $this->validateInputs($consent);

        // Vérification de l'email dans la base de données
        if (count($this->errors) === 0) {
            $this->checkEmailExists();
        }

        // Si pas d'erreurs, insérer l'utilisateur dans la base de données
        if (count($this->errors) === 0) {
            $this->insertUser();
        }

        return $this->errors;
    }

    // Valider les entrées du formulaire
    private function validateInputs($consent) {
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = "L'adresse électronique n'est pas valide !";
        }

        if (strlen($this->password) < 8) {
            $this->errors['password'] = "Le mot de passe doit avoir au moins 8 caractères.";
        }

        $pattern = '/^(?=.*\d)(?=.*[A-Za-z])(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$/';
        if (!preg_match($pattern, $this->password)) {
            $this->errors['password'] = "Le mot de passe doit contenir au moins un chiffre, une lettre majuscule, une lettre minuscule et un caractère spécial.";
        }

        if ($this->password !== $this->cpassword) {
            $this->errors['password'] = "Le mot de passe de confirmation ne correspond pas !";
        }

        if (!$consent) {
            $this->errors['consent'] = "Vous devez accepter les termes et conditions.";
        }
    }

    // Vérifier si l'email existe déjà dans la base de données
    private function checkEmailExists() {
        $email_check = "SELECT * FROM users_gestimag WHERE email = ?";
        $stmt = $this->mysqli->prepare($email_check);
        $stmt->bind_param("s", $this->email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $this->errors['email'] = "L'email que vous avez saisi existe déjà !";
        }
    }

    // Insérer l'utilisateur dans la base de données
    private function insertUser() {
        $encpass = password_hash($this->password, PASSWORD_BCRYPT);
        $status = "verified"; // L'utilisateur est directement "verifié"

        // Enregistrez également le software_id dans la base de données
        $insert_data = "INSERT INTO users_gestimag (name, email, password, status, software_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->mysqli->prepare($insert_data);
        $stmt->bind_param("ssssi", $this->name, $this->email, $encpass, $status, $this->software_id);

        if ($stmt->execute()) {
            // L'utilisateur est créé, on peut l'authentifier directement
            $_SESSION['name'] = $this->name;
            $_SESSION['email'] = $this->email;
            $_SESSION['password'] = $this->password;
            $_SESSION['software_id'] = $this->software_id;  // Ajouter l'ID du logiciel à la session
            header('location: /index.php'); // Redirection vers la page d'accueil
            exit();
        } else {
            $this->errors['db-error'] = "Échec lors de l'insertion de données dans la base de données !";
        }
    }

    // Méthode pour connecter un utilisateur
    public function login($email, $password) {
        $check_email = "SELECT * FROM users_gestimag WHERE email = ?";
        $stmt = $this->mysqli->prepare($check_email);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $fetch = $result->fetch_assoc();
            if (password_verify($password, $fetch['password'])) {
                $_SESSION['email'] = $email;
                $_SESSION['name'] = $fetch['name'];
                $status = $fetch['status'];

                if ($status === 'verified') {
                    $_SESSION['password'] = $password;
                    header('location: /index.php');
                    exit();
                } else {
                    $_SESSION['info'] = "Il semble que vous n'ayez pas encore vérifié votre adresse e-mail.";
                    // Cette ligne n'est plus nécessaire, car l'utilisateur est automatiquement "verified"
                    header('location: /index.php');
                    exit();
                }
            } else {
                $this->errors['login'] = "Courriel ou mot de passe incorrect !";
            }
        } else {
            $this->errors['login'] = "Il semblerait que vous ne soyez pas encore membre !";
        }

        return $this->errors;
    }
}

$mysqli = createMysqliConnection(); // Créez une connexion MySQLi
$createAccount = new CreateAccount($mysqli);

// Traiter les inscriptions
if (isset($_POST['signup'])) {
    $errors = $createAccount->register($_POST['name'], $_POST['email'], $_POST['password'], $_POST['cpassword'], $_POST['software_id'], isset($_POST['consent']));
    if (count($errors) > 0) {
        // Afficher les erreurs
        foreach ($errors as $error) {
            echo "<p>$error</p>";
        }
    }
}

// Traiter la connexion
if (isset($_POST['login'])) {
    $errors = $createAccount->login($_POST['email'], $_POST['password']);
    if (count($errors) > 0) {
        foreach ($errors as $error) {
            echo "<p>$error</p>";
        }
    }
}