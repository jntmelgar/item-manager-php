<?php
$servername = "db";
$username = "root";
$password = "admin";
$dbname = "db";

// Conexão com MySQL 5.7
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Processar formulário de novo item
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? '';

    if (!empty($name) && !empty($description) && is_numeric($price)) {
        $stmt = $conn->prepare("INSERT INTO items (name, description, price) VALUES (?, ?, ?)");
        $stmt->bind_param("ssd", $name, $description, $price);
        $stmt->execute();
        $stmt->close();
        header("Location: index.php"); // Redireciona para limpar POST
        exit;
    } else {
        $error = "Preencha todos os campos corretamente.";
    }
}

// Buscar todos os itens
$sql = "SELECT id, name, description, price FROM items";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <title>Lista de Itens</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <h1>Lista de Itens</h1>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Descrição</th>
        <th>Preço (R$)</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if ($result->num_rows > 0) {
          while($row = $result->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . $row["id"] . "</td>";
              echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
              echo "<td>" . htmlspecialchars($row["description"]) . "</td>";
              echo "<td>" . number_format($row["price"], 2, ',', '.') . "</td>";
              echo "</tr>";
          }
      } else {
          echo "<tr><td colspan='4'>Nenhum item encontrado</td></tr>";
      }
      ?>
    </tbody>
  </table>

  <h2>Adicionar Novo Item</h2>
  <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
  <form method="POST" action="">
    <label for="name">Nome:</label>
    <input type="text" name="name" required />

    <label for="description">Descrição:</label>
    <textarea name="description" required></textarea>

    <label for="price">Preço (R$):</label>
    <input type="number" name="price" step="0.01" required />

    <input type="submit" value="Adicionar Item" />
  </form>
</body>
</html>
