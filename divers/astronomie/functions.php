<?php
require_once '../../config/connexion_bdd.php';

function getAllProducts()
{
	global $conn;

	function afficherContenu($db, $table)
	{
		$sql = "SELECT * FROM $table, usertable WHERE astronomie.id_users = usertable.id_users AND verified = 'y' ORDER BY astronomie.date_astronomie DESC;";

		try {
			$stmt = $db->prepare($sql);
			$stmt->execute();
			$contenus = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupère tous les résultats sous forme de tableau

			if ($contenus) {
				foreach ($contenus as $row) {
					echo "<div class='title'>" . htmlspecialchars($row["title_contenu"]) . "</div>";
					if (strlen($row["contenu"]) > 10) {
						$contenu = substr($row["contenu"], 0, 100);
						$contenu = substr($contenu, 0, strrpos($contenu, ' '));
						echo "<p>" . htmlspecialchars($contenu) . "... <a style='color: red;' href='/divers/$table/$table.php'>Lire plus</a></p>";
					}
				}
			} else {
				echo "0 résultat";
			}
		} catch (PDOException $e) {
			echo "Erreur : " . $e->getMessage();
		}
	}
}