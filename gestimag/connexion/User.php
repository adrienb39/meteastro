<?php
class User
{
    private $name;
    private $email;
    private $password;
    private $cpassword;
    private $consent;
    private $software_id;
    private $license_key;
    private $pdo;
    private $errors = [];

    // Constructeur pour initialiser la connexion à la base de données
    public function __construct($name, $email, $password, $cpassword, $consent, $software_id, $pdo, $license_key = null)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->cpassword = $cpassword;
        $this->consent = $consent;
        $this->software_id = $software_id;
        $this->pdo = $pdo;
        $this->license_key = $license_key; // Clé de licence
    }

    // Validation des données
    public function validate()
    {
        // Validation du nom
        if (empty($this->name)) {
            $this->errors['name'] = "Le nom est requis.";
        }

        // Validation de l'email
        if (empty($this->email)) {
            $this->errors['email'] = "L'email est requis.";
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = "L'email n'est pas valide.";
        } else {
            // Vérification de l'email dans la base de données
            $stmt = $this->pdo->prepare("SELECT * FROM users_gestimag WHERE email = ?");
            $stmt->execute([$this->email]);
            if ($stmt->rowCount() > 0) {
                $this->errors['email'] = "Cet email est déjà utilisé.";
            }
        }

        // Validation du mot de passe
        if (empty($this->password) || strlen($this->password) < 8) {
            $this->errors['password'] = "Le mot de passe doit comporter au moins 8 caractères.";
        }

        // Validation de la confirmation du mot de passe
        if ($this->password !== $this->cpassword) {
            $this->errors['cpassword'] = "Les mots de passe ne correspondent pas.";
        }

        // Validation du consentement
        if (empty($this->consent)) {
            $this->errors['consent'] = "Vous devez accepter les termes et conditions.";
        }

        // Validation du software_id
        if (empty($this->software_id)) {
            $this->errors['software_id'] = "Le software_id est requis.";
        }

        // Validation de la licence
        if (empty($this->license_key)) {
            $this->errors['license_key'] = "La clé de licence est requise.";
        }

        // Retourner les erreurs s'il y en a
        return $this->errors;
    }

    // Méthode pour enregistrer l'utilisateur et la licence
    public function save()
{
    if (empty($this->errors)) {
        // Hashage du mot de passe
        $encpass = password_hash($this->password, PASSWORD_BCRYPT);

        // Insertion de l'utilisateur dans la table 'users_gestimag'
        $stmt = $this->pdo->prepare("INSERT INTO users_gestimag (name, email, password, status) VALUES (?, ?, ?, ?)");
        $status = "verified"; // Statut de l'utilisateur
        if ($stmt->execute([$this->name, $this->email, $encpass, $status])) {
            // Récupérer l'ID de l'utilisateur qui vient d'être ajouté
            $userId = $this->pdo->lastInsertId();

            // Récupérer l'ID de la licence sélectionnée
            $licenseKey = $this->license_key;
            $stmt = $this->pdo->prepare("SELECT id_license FROM licenses WHERE license_key = ? LIMIT 1");
            $stmt->execute([$licenseKey]);
            $licenseId = $stmt->fetchColumn();

            if ($licenseId) {
                // Lier la licence à l'utilisateur et au logiciel dans la table 'users_software_license'
                $linkStmt = $this->pdo->prepare("INSERT INTO users_software_license (user_id, software_id, license_id) VALUES (?, ?, ?)");
                if ($linkStmt->execute([$userId, $this->software_id, $licenseId])) {
                    // Mise à jour du statut de la licence dans la table 'licenses'
                    $updateStmt = $this->pdo->prepare("UPDATE licenses SET used_license = 1 WHERE id_license = ?");
                    if ($updateStmt->execute([$licenseId])) {
                        return ["success" => "Inscription réussie !"];
                    } else {
                        $this->errors['db'] = "Erreur lors de la mise à jour de la table licenses.";
                    }
                } else {
                    $this->errors['db'] = "Erreur lors de l'enregistrement dans la table de jonction.";
                }
            } else {
                $this->errors['license_key'] = "La clé de licence sélectionnée est invalide ou déjà utilisée.";
            }
        } else {
            $this->errors['db'] = "Erreur lors de l'inscription de l'utilisateur.";
        }
    }

    return $this->errors;
}
    // Méthode pour connecter l'utilisateur
    public function login()
    {
        // Vérification de l'email
        if (empty($this->email)) {
            $this->errors['email'] = "L'email est requis.";
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = "L'email n'est pas valide.";
        } else {
            // Vérification si l'email existe dans la base de données
            $stmt = $this->pdo->prepare("SELECT * FROM users_gestimag WHERE email = ?");
            $stmt->execute([$this->email]);
            if ($stmt->rowCount() == 0) {
                $this->errors['email'] = "Aucun utilisateur trouvé avec cet email.";
            } else {
                $user = $stmt->fetch();
                // Vérification du mot de passe
                if (!password_verify($this->password, $user['password'])) {
                    $this->errors['password'] = "Le mot de passe est incorrect.";
                } else {
                    // Connexion réussie
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_password'] = $user['password'];
                    $_SESSION['user_admin'] = $user['admin'];
                    return ["success" => "Connexion réussie !"];
                }
            }
        }

        // Retourner les erreurs si la connexion échoue
        return $this->errors;
    }
}