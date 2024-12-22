<?php
session_start();
include 'db_connect.php';

// Számoljuk össze a kosárban lévő termékek számát
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <title>Tuti Falatozó</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="logo">
            <h1>Tuti Falatozó</h1>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Kezdőlap</a></li>
                <li class="dropdown">
                    <a href="#">Étlap</a>
                    <ul class="dropdown-content">
                        <?php
                        // Kategóriák lekérdezése
                        $tiltott_kategoriak = array(0, 11, 12, 15, 17);
                        $sql = "SELECT id, name FROM categories ORDER BY name ASC";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                if (!in_array($row['id'], $tiltott_kategoriak)) {
                                    echo "<li><a href='index.php?category_id=" . $row['id'] . "'>" . $row['name'] . "</a></li>";
                                }
                            }
                        } else {
                            echo "<li>Nincs elérhető kategória.</li>";
                        }
                        ?>
                    </ul>
                </li>
                <li><a href="hazhozszallitas.php">Étel házhozszállítás</a></li>
                <li><a href="kapcsolat.php">Kapcsolat</a></li>
                <li class="cart-icon">
                    <a href="javascript:void(0);" id="openCartModal">🛒 Kosár 
                        <?php if ($cart_count > 0): ?>
                            <span class='cart-count'><?php echo $cart_count; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
            </ul>
        </nav>
    </header>

 

    <!-- Top 4 Termékek megjelenítése -->
<div class="product-cards">
    <?php if (!isset($_GET['category_id'])): ?>
        <section class="top-products">
            <h2>Felkapott termékek</h2>
            <div class="product-cards">
                <?php
                $sql = "SELECT p.id, p.name, p.price, SUM(oi.qty) AS total_qty
                        FROM order_items oi
                        JOIN products p ON oi.product_id = p.id
                        WHERE p.category_id NOT IN (8, 12, 15, 17, 11, 13) 
                        AND p.id NOT IN (91, 115)
                        GROUP BY p.id, p.name, p.price
                        ORDER BY total_qty DESC
                        LIMIT 4";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<div class='product-card'>";
                        echo "<h3>" . $row['name'] . "</h3>";
                        echo "<p>" . $row['price'] . " Ft</p>";

                        // Ha a termék ID 72 vagy 73, azonnali kosárba helyezés
                        if ($row['id'] == 72 || $row['id'] == 73) {
                            echo "<form method='POST' action='add_to_cart.php'>";
                            echo "<input type='hidden' name='product_id' value='" . $row['id'] . "'>";
                            echo "<button type='submit'>Kosárba</button>";
                            echo "</form>";
                        } else {
                            // Egyéb termékeknél feltét modal megjelenítése
                            echo "<button class='openModalBtn' data-product-id='" . $row['id'] . "'>Feltétek hozzáadása</button>";
                        }
                        echo "</div>";
                    }
                } else {
                    echo "<p>Nincsenek elérhető termékek.</p>";
                }
                ?>
            </div>
        </section>
    <?php endif; ?>
</div>


 <!  // Termékek megjelenítése
<main>
    <div class="products">
        <?php
        if (isset($_GET['category_id'])) {
            $category_id = $_GET['category_id'];

            // Frissített SQL-lekérdezés a tiltott ID-k kizárásával
            $sql = "SELECT id, name, price FROM products WHERE category_id = $category_id AND id NOT IN (91, 115)";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='product-card'>";
                    echo "<h4>" . htmlspecialchars($row['name']) . "</h4>";
                    echo "<p>Ár: " . number_format($row['price'], 0, '', ' ') . " Ft</p>";

                    // Ha a termék ID 72 vagy 73, azonnali kosárba helyezés
                    if ($row['id'] == 72 || $row['id'] == 73) {
                        // Közvetlen kosárba helyezés gomb ezeknél az ID-knél
                        echo "<form method='POST' action='add_to_cart.php'>";
                        echo "<input type='hidden' name='product_id' value='" . $row['id'] . "'>";
                        echo "<button type='submit'>Kosárba</button>";
                        echo "</form>";
                    } elseif ($category_id == 4 || $category_id == 5 || $category_id == 7 || $category_id == 9) {
                        // Egyéb kategóriákhoz feltétek modal
                        echo "<button class='openModalBtn' data-product-id='" . $row['id'] . "'>Feltétek hozzáadása</button>";
                    } elseif ($category_id == 6 || $category_id == 14 || $category_id == 10 || $category_id == 13 || $category_id == 8) {
                        // Kosárba helyezés az egyéb kategóriáknál
                        echo "<form method='POST' action='add_to_cart.php'>";
                        echo "<input type='hidden' name='product_id' value='" . $row['id'] . "'>";
                        echo "<button type='submit'>Kosárba</button>";
                        echo "</form>";
                    }
                    echo "</div>";
                }
            } else {
                echo "<p>Nincsenek elérhető termékek ebben a kategóriában.</p>";
            }
        }
        ?>
    </div>
</main>


    <!-- Modal a feltétek kiválasztásához -->
    <div id="toppingsModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Válassz feltéteket a pizzádhoz!</h2>
            <form id="toppingsForm" method="post" action="add_to_cart.php">
                <input type="hidden" name="product_id" id="modalProductId">
                <div id="toppingsContainer"></div>
                <button type="submit">Kosárba</button>
            </form>
        </div>
    </div>

    <!-- Modal a kosár megjelenítéséhez -->
    <div id="cartModal" class="modal">
        <div class="modal-content">
            <span class="close-cart">&times;</span>
            <h2>Kosár tartalma</h2>
            <div id="cartContainer">
                <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Termék</th>
                                <th>Ár</th>
                                <th>Mennyiség</th>
                                <th>Összeg</th>
                                <th>Művelet</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total = 0;
                            foreach ($_SESSION['cart'] as $product_id => $product):
                                $subtotal = $product['price'] * $product['quantity'];
                                $total += $subtotal;
                            ?>
                            <tr>
                                <td><?php echo $product['name']; ?>
                                    <?php if (!empty($product['toppings'])): ?>
                                        <br><small>Feltétek: 
                                        <?php foreach ($product['toppings'] as $topping): ?>
                                            <?php echo $topping['name'] . " (+ " . $topping['price'] . " Ft), "; ?>
                                        <?php endforeach; ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $product['price']; ?> Ft</td>
                                <td><?php echo $product['quantity']; ?></td>
                                <td><?php echo $subtotal; ?> Ft</td>
                                <td>
                                    <form method='POST' action='remove_from_cart.php'>
                                        <input type='hidden' name='product_id' value='<?php echo $product_id; ?>' />
                                        <button type='submit'>Törlés</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td colspan='3'>Összesen:</td>
                                <td colspan='2'><?php echo $total; ?> Ft</td>
                            </tr>
                            <tr>
                                 <td colspan="3">Kiszállítási díj:</td>
   	 <td colspan="2" id="shippingCost">0 Ft</td>
                            </tr>
                            <tr>
                                <td colspan='3'>Végösszeg:</td>
                                <td colspan='2' id="finalTotal"><?php echo $total; ?> Ft</td>
                            </tr>
                        </tbody>
                    </table>
                    <button id='emptyCartBtn' onclick='emptyCart()'>Kosár ürítése</button>
                <?php else: ?>
                    <p>A kosár üres.</p>
                <?php endif; ?>
            </div>

            <!-- Rendelési adatok -->
          <div class="order-form">
                <h2>Rendelési adatok</h2>
                <form method="post" action="place_order.php">
                    <input type="text" id="name" name="name" placeholder="Név" required>

                    <select name="order_number" id="order_number" required>
                        <option hidden disabled selected value>Fizetési mód</option>
                        <option>Készpénz</option>
                        <option>Bankkártya</option>
                        <option>Szépkártya</option>
                    </select>

                    <select name="order_hsz" id="order_hsz" required onchange="updateShippingCost()">
                        <option hidden disabled selected value>Település</option>
                        <option>Hosszúhetény</option>
                        <option>Komló</option>
                        <option>Pécs-Hird</option>
                        <option>Pécs-Vasas</option>
                    </select>

                    <input type="text" id="utca" name="utca" placeholder="Utca">

                    <input type="text" id="hsz" name="hsz" placeholder="Házszám">

                    <input type="text" id="mj" name="mj" placeholder="Megjegyzés">

                    <input type="tel" id="tel" name="tel" placeholder="Telefonszám" required>
                    <input type="email" id="email" name="email" placeholder="Email cím" required>


                    <button type="submit">Rendelés Leadása</button>
                </form>
            </div>
        </div>
    </div>


    <!-- JavaScript a modal működtetéséhez -->
    <script>
function updateShippingCost() {
    var orderLocation = document.getElementById('order_hsz').value;
    var shippingCost = 0;

    // Szállítási díj meghatározása a kiválasztott település alapján
    if (orderLocation === "Komló") {
        shippingCost = 200;
    } else if (orderLocation === "Hosszúhetény") {
        shippingCost = 0;
    } else if (orderLocation === "Pécs-Hird") {
        shippingCost = 500;
    } else if (orderLocation === "Pécs-Vasas") {
        shippingCost = 800;
    }

    // Frissítjük a szállítási díjat a HTML-ben
    document.getElementById('shippingCost').innerText = shippingCost + " Ft";

    // Újraszámoljuk a végösszeget
    updateFinalTotal(shippingCost);
}

function updateFinalTotal(shippingCost) {
    // A kosár összegének lekérése a megfelelő mezőből
    var totalRows = document.querySelectorAll('.cart-table tbody tr');
    var cartTotal = 0;

    totalRows.forEach(function(row) {
        var subtotalElement = row.querySelector('td:nth-child(4)');
        if (subtotalElement) {
            var subtotalText = subtotalElement.innerText.replace(" Ft", "").trim();
            var subtotal = parseInt(subtotalText);
            if (!isNaN(subtotal)) {
                cartTotal += subtotal;
            }
        }
    });

    // Kosár összeg kiszámítása + szállítási díj
    var finalTotal = cartTotal + shippingCost;

    // Frissítjük az értékeket a HTML-ben
    document.getElementById('finalTotal').innerText = finalTotal + " Ft";
    document.querySelector('tr.total-row td:nth-child(2)').innerText = cartTotal + " Ft";
}

// Szállítási díj frissítése eseményre
document.addEventListener('DOMContentLoaded', function () {
    var locationDropdown = document.getElementById('order_hsz');
    locationDropdown.addEventListener('change', function () {
        updateShippingCost();
    });

    // Induláskor frissítjük a végösszeget a szállítási díj alapján
    updateShippingCost();
});


    document.addEventListener('DOMContentLoaded', function() {
        var modal = document.getElementById('toppingsModal');
        var closeBtn = document.getElementsByClassName('close')[0];
        var openModalBtns = document.getElementsByClassName('openModalBtn');
        var cartModal = document.getElementById('cartModal');
        var closeCartBtn = document.getElementsByClassName('close-cart')[0];
        var openCartModal = document.getElementById('openCartModal');
        var emptyCartBtn = document.getElementById('emptyCartBtn');

        // Feltétek modal megnyitása
        Array.from(openModalBtns).forEach(function(btn) {
            btn.addEventListener('click', function() {
                var productId = this.getAttribute('data-product-id');
                document.getElementById('modalProductId').value = productId;

                var xhr = new XMLHttpRequest();
                xhr.open('GET', 'get_toppings.php?product_id=' + productId, true);
                xhr.onload = function() {
                    if (this.status === 200) {
                        document.getElementById('toppingsContainer').innerHTML = this.responseText;
                        modal.style.display = 'block';
                    }
                };
                xhr.send();
            });
        });

        // Modal bezárása
        closeBtn.onclick = function() {
            modal.style.display = 'none';
        };

        // Kosár modal megnyitása
        openCartModal.onclick = function() {
            cartModal.style.display = 'block';
        };

        // Kosár modal bezárása
        closeCartBtn.onclick = function() {
            cartModal.style.display = 'none';
        };

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
            if (event.target == cartModal) {
                cartModal.style.display = 'none';
            }
        };
    });

    // Kosár ürítése
    function emptyCart() {
        if (confirm("Biztosan üríteni szeretné a kosarat?")) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'empty_cart.php', true);
            xhr.onload = function() {
                if (this.status === 200) {
                    location.reload(); // Az oldal újratöltése
                } else {
                    alert('Hiba történt a kosár ürítésekor.');
                }
            };
            xhr.send();
        }
    }
function openModal(modalId) {
    document.getElementById(modalId).style.display = "flex";
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = "none";
}
    </script>
</body>
</html>
