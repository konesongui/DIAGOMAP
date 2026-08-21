# Système de Présence par QR Code - Guide Complet

## 📋 Vue d'ensemble

Ce système permet aux employés de pointer leur présence en scannant un QR code avec un smartphone/tablette. Le système enregistre automatiquement :
- **Heure d'arrivée** (première connexion du jour)
- **Heure de départ** (deuxième connexion du jour)
- **Durée totale** de présence

## 🚀 Installation

### Étape 1 : Créer la base de données

Exécutez le script SQL suivant dans votre base de données :

```sql
CREATE TABLE IF NOT EXISTS `staff_attendance_qr` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `staff_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `arrival_time` time,
  `departure_time` time,
  `scan_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` enum('arrival','departure','complete') DEFAULT 'arrival',
  `notes` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`staff_id`) REFERENCES `staff`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_daily_attendance` (`staff_id`, `attendance_date`),
  INDEX `idx_attendance_date` (`attendance_date`),
  INDEX `idx_staff_id` (`staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Étape 2 : Fichiers créés

Les fichiers suivants ont été créés :

**Controller :**
- `/application/controllers/admin/Qrattendance.php` - Gestion principale

**Vues :**
- `/application/views/admin/qrattendance/display_qr.php` - Affichage du QR code
- `/application/views/admin/qrattendance/scan_page.php` - Page de scan (mobile-friendly)
- `/application/views/admin/qrattendance/today_attendance.php` - Présences du jour
- `/application/views/admin/qrattendance/attendance_report.php` - Rapport détaillé

### Étape 3 : Ajouter les routes

Ajoutez ces routes à votre `application/config/routes.php` :

```php
$route['admin/qrattendance/display'] = 'admin/Qrattendance/display_qr';
$route['admin/qrattendance/scan'] = 'admin/Qrattendance/scan_page';
$route['admin/qrattendance/process'] = 'admin/Qrattendance/process_scan';
$route['admin/qrattendance/today'] = 'admin/Qrattendance/today_attendance';
$route['admin/qrattendance/report'] = 'admin/Qrattendance/attendance_report';
```

## 📱 Utilisation

### Pour l'administrateur

1. **Afficher le QR code :**
   - Accédez à : `http://localhost/diagoma/admin/qrattendance/display`
   - Le QR code s'affiche en grand pour être scanné par les employés
   - Pouvez être affiché sur un écran/affichage dans l'entrée

2. **Voir les présences du jour :**
   - Accédez à : `http://localhost/diagoma/admin/qrattendance/today`
   - Tableau en temps réel des arrivées/départs

3. **Rapport détaillé :**
   - Accédez à : `http://localhost/diagoma/admin/qrattendance/report`
   - Filtrer par date et employé
   - Exporter en CSV/Imprimer

### Pour l'employé

1. **Scanner le QR code** avec n'importe quel smartphone
2. **Entrer ses identifiants :**
   - Numéro d'employé (ex: EMP001)
   - Mot de passe
3. **Confirmer la présence**
4. **Première entrée = Arrivée** ✓
5. **Deuxième entrée = Départ** ✓

## 🔐 Sécurité

- ✅ Authentification par identifiants (numéro employé + mot de passe)
- ✅ Token sécurisé pour chaque session QR
- ✅ Support des mots de passe bcrypt et MD5
- ✅ Une seule arrivée/départ par jour par employé
- ✅ Timestamps précis

## 📊 Données enregistrées

Pour chaque présence :
- **staff_id** : ID de l'employé
- **attendance_date** : Date de présence
- **arrival_time** : Heure d'arrivée (HH:MM:SS)
- **departure_time** : Heure de départ (HH:MM:SS)
- **scan_date** : Timestamp complet du scan
- **status** : État (arrival/departure/complete)

## 🎨 Couleurs utilisées

- Bleu principal : `#28669e`
- Jaune/Ambre : `#fec32e`

## 🛠️ Personnalisation

### Modifier les couleurs

Éditez les fichiers de vues et remplacez :
- `#28669e` par votre couleur bleue
- `#fec32e` par votre couleur jaune

### Ajouter des champs supplémentaires

Modifiez le tableau `staff_attendance_qr` pour ajouter :
- Localisation GPS
- Photo (biométrie)
- Raison du retard

## 📈 Statistiques disponibles

- Total d'employés présents
- Total d'arrivées enregistrées
- Total de départs enregistrés
- Employés avec présence incomplète (pas de départ)
- Durée moyenne de travail

## 🐛 Dépannage

**Le QR code ne scanne pas :**
- Vérifiez que la URL est accessible depuis le mobile
- Essayez un code QR standard (pas de styles CSS)

**Mot de passe incorrect :**
- Vérifiez que l'employé utilise le bon format
- Assurez-vous que le champ password existe dans la table staff

**Pas d'enregistrement :**
- Vérifiez que la table `staff_attendance_qr` existe
- Vérifiez les permissions de base de données

## 📞 Support

Pour toute question ou problème, consultez les fichiers :
- Controller : `application/controllers/admin/Qrattendance.php`
- Vues : `application/views/admin/qrattendance/*.php`
