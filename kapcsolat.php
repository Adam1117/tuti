<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Kapcsolat űrlap feldolgozása
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Ellenőrzés: minden mező kitöltve van-e
    if (!empty($name) && !empty($email) && !empty($message)) {
        // E-mail küldés
        $to = "adamkarmanoczki01@gmail.com"; // Módosítsa a saját e-mail címére
        $subject = "Kapcsolatfelvételi űrlap üzenete - Tuti Falatozó";
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        $emailMessage = "
        <html>
        <head>
            <title>Kapcsolatfelvételi Üzenet</title>
        </head>
        <body>
            <h2>Kapcsolatfelvételi üzenet érkezett:</h2>
            <p><strong>Név:</strong> $name</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Üzenet:</strong><br>$message</p>
        </body>
        </html>
        ";

        if (mail($to, $subject, $emailMessage, $headers)) {
            $feedback = "Az üzenetet sikeresen elküldtük. Hamarosan felvesszük Önnel a kapcsolatot.";
        } else {
            $feedback = "Hiba történt az üzenet elküldése során. Kérjük, próbálja meg újra.";
        }
    } else {
        $feedback = "Kérjük, töltse ki az összes mezőt!";
    }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kapcsolat - Tuti Falatozó</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* Stílusok a kapcsolat szakaszhoz */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: url('dream-pulse-header.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
        }

        .contact-section {
            background-color: rgba(0, 0, 0, 0.8);
            padding: 40px 20px;
            border-radius: 10px;
            margin: 40px auto;
            max-width: 800px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .contact-section h2 {
            text-align: center;
            color: #fff;
            font-size: 28px;
            margin-bottom: 20px;
        }

        .contact-section p {
            text-align: center;
            color: #ddd;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .contact-info {
            text-align: center;
            margin-bottom: 30px;
        }

        .contact-info p {
            margin: 5px 0;
            font-size: 16px;
        }

        .contact-info a {
            color: #ffcc00;
            text-decoration: none;
        }

        .contact-info a:hover {
            text-decoration: underline;
        }

        form {
            max-width: 600px;
            margin: 0 auto;
        }

        form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #fff;
        }

        form input,
        form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        form button {
            display: block;
            width: 100%;
            max-width: 200px;
            margin: 0 auto;
            padding: 10px 20px;
            background-color: #ffcc00;
            color: #333;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-align: center;
            transition: background-color 0.3s;
        }

        form button:hover {
            background-color: #e6b800;
        }

        .feedback {
            text-align: center;
            font-size: 16px;
            color: #ffcc00;
            margin-top: 20px;
        }

        .back-to-home {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #ffcc00;
            color: #333;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
            text-align: center;
            transition: background-color 0.3s;
        }

        .back-to-home:hover {
            background-color: #e6b800;
        }
    </style>
</head>
<body>
    <div id="kapcsolat" class="contact-section">
        <h2>Kapcsolat</h2>
        <p>Lépjen kapcsolatba velünk az alábbi űrlap segítségével, vagy hívjon minket az elérhetőségeinken!</p>

        <div class="contact-info">
            <p><strong>Telefon:</strong> <a href="tel:+36309154667">+36 30 915 4667</a></p>
            <p><strong>Email:</strong> <a href="mailto:adamkarmanoczki01@gmail.com">adamkarmanoczki01@gmail.com</a></p>
        </div>

        <?php if (!empty($feedback)): ?>
            <div class="feedback"><?php echo $feedback; ?></div>
        <?php endif; ?>

        <form id="contactForm" method="POST" action="">
            <label for="name">Név:</label>
            <input type="text" id="name" name="name" placeholder="Név" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Email cím" required>

            <label for="message">Üzenet:</label>
            <textarea id="message" name="message" placeholder="Írja ide az üzenetét..." rows="5" required></textarea>

            <button type="submit">Üzenet küldése</button>
        </form>

        <a href="index.php" class="back-to-home">Vissza a főoldalra</a>
    </div>
</body>
</html>
