-- Table des produits
CREATE TABLE `com_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `tax_rate` decimal(5,2) NOT NULL DEFAULT '20.00',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
);

-- Table des mouvements de stock
CREATE TABLE `com_stock_movements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `type` enum('in','out') NOT NULL,
  `date` datetime NOT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`)
);

-- Table des devis
CREATE TABLE `com_quotes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `valid_until` date NOT NULL,
  `status` enum('draft','sent','accepted','rejected') NOT NULL DEFAULT 'draft',
  `total_ht` decimal(10,2) NOT NULL,
  `total_ttc` decimal(10,2) NOT NULL,
  `notes` text,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`)
);

-- Table des items de devis
CREATE TABLE `com_quote_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quote_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `tax_rate` decimal(5,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `quote_id` (`quote_id`),
  KEY `product_id` (`product_id`)
);

-- Table des commandes
CREATE TABLE `com_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `quote_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `status` enum('pending','confirmed','shipped','invoiced','cancelled') NOT NULL DEFAULT 'pending',
  `total_ht` decimal(10,2) NOT NULL,
  `total_ttc` decimal(10,2) NOT NULL,
  `shipping_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  KEY `quote_id` (`quote_id`)
);

-- Table des items de commande
CREATE TABLE `com_order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `tax_rate` decimal(5,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`)
);

-- Table des livraisons
CREATE TABLE `com_deliveries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` enum('preparing','completed') NOT NULL DEFAULT 'preparing',
  `tracking_number` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `client_id` (`client_id`)
);

-- Table des items de livraison
CREATE TABLE `com_delivery_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `delivery_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `delivery_id` (`delivery_id`),
  KEY `product_id` (`product_id`)
);

-- Table des factures
CREATE TABLE `com_invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date NOT NULL,
  `status` enum('unpaid','paid','partially_paid','cancelled') NOT NULL DEFAULT 'unpaid',
  `total_ht` decimal(10,2) NOT NULL,
  `total_ttc` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `client_id` (`client_id`)
);

-- Table des items de facture
CREATE TABLE `com_invoice_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `tax_rate` decimal(5,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`),
  KEY `product_id` (`product_id`)
);

-- Table des paiements
CREATE TABLE `com_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` enum('cash','check','transfer','card') NOT NULL,
  `reference` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`)
);



//INCOME
<?php
$conn = new mysqli("localhost","root","","diago");
$sql1 = "SELECT SUM(amount) AS total_amount  FROM income Where deleted = 1 ";



$result1 = $conn->query($sql1);



?>


<?php
$conn = new mysqli("localhost","root","","diago");
$sql = "SELECT SUM(amount) AS total_amount_r  FROM income_processing Where amount >0 and deleted = 1 ";

$result = $conn->query($sql);


  <?php while ($row = $result->fetch_object()): ?>
                                <?php while ($row1 = $result1->fetch_object()): ?>
                                    <h3 class="box-title titlefix" style="margin-left: 200px"> <?php echo $this->lang->line(''); ?>  <b> SOMME FINAL : <?php echo $row1->total_amount + $row->total_amount_r ?>  FCFA </b></h3>
                                <?php endwhile; ?>
                            <?php endwhile; ?>
?>
//ENDR

//EXPENSES

<?php
$conn = new mysqli("localhost","root","","diago");
$sql = "SELECT SUM(amount) AS total_amount  FROM expenses Where deleted > 0";


$result = $conn->query($sql);


  <?php while ($row = $result->fetch_object()): ?>
                                <h3 class="box-title titlefix" style="margin-left: 200px"> <?php echo $this->lang->line(''); ?>  <b> SOMME FINAL : <?php echo $row->total_amount ?>  FCFA </b></h3>
                            <?php endwhile; ?>

?>

//ENDR