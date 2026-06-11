# Nekustamā Īpašuma Platforma

Tīmekļa lietojumprogramma uz Laravel, kas paredzēta nekustamā īpašuma sludinājumu publicēšanai un apskatei. Aizmugursistēma (backend) ir izveidota kā REST API ar lietotāju lomu atbalstu, izlases sarakstu un sludinājumu pārvaldību.

---

## Saturs

- [Tehnoloģijas](#tehnoloģijas)
- [Projekta struktūra](#projekta-struktūra)
- [Datubāze](#datubāze)
- [API Endpoints](#api-endpoints)
- [Lietotāju lomas](#lietotāju-lomas)
- [Uzstādīšana](#uzstādīšana)

---
## Tehnoloģijas

  PHP >= 8.x
  Laravel >= 11.x
  MySQL — datubāze
  Tokenu autentifikācija


## Projekta struktūra

```
app/
├── Http/
│   └── Controllers/
│       ├── Controller.php          # Pamata abstraktais kontrolieris
│       ├── AutenController.php     # Autentifikācija (ieeja / reģistrācija / izeja)
│       ├── FavoriteController.php  # Izlases sludinājumi
│       └── MajaController.php      # Sludinājumi un kategorijas
│
└── Models/
    ├── Lietotajs.php       # Lietotājs
    ├── Maja.php            # Māja / Nekustamais īpašums
    ├── Sludinajums.php     # Sludinājums
    ├── Kategorijas.php     # Kategorija
    ├── LietotajMajas.php   # Izlase (saistība starp lietotāju un māju)
    ├── MajasAtteli.php     # Māju attēli
    └── User.php            # Standarta Laravel modelis (netiek izmantots)

bootstrap/
├── app.php             # Lietojumprogrammas ieejas punkts
└── providers.php       # Servisu nodrošinātāju saraksts

routes/
├── api.php             # API maršruti
├── web.php             # Tīmekļa maršruti
└── console.php         # Konsoles komandas

config/
└── ...                 # Laravel konfigurācijas
```

---

## Datubāze

### Tabulu shēma

```
Kategorijas          Majas                    Sludinajums
─────────────        ─────────────────────    ───────────────────
ID (PK)              MajasID (PK)             SludinajumsID (PK)
nosaukums            pilseta                  attels
slogan               rajons                   MajasID (FK)
                     adrese                   created_at
                     cena                     updated_at
                     platiba
                     zemes_platiba            Majas_atteli
                     istabu_skaits            ────────────────
                     stavu_skaits             attels_id (PK)
                     celsanas_gads            attels_fail
                     stavoklis                attelu_kartiba
                     virsraksts               MajasID (FK)
                     apraksts
                     ipasibas
                     KategorijasID (FK)


Lietotajs            LietotajMajas
─────────────────    ──────────────────────
kodsID (PK)          LietotajMajasID (PK)
lietotajvards        LietotajsID (FK)
epasts               SludinajumsID (FK)
parole               MajasID (FK)
statuss              statuss
loma
```

### Saistības starp tabulām

- `Kategorijas` → (viens pret daudziem) → `Majas`
- `Majas` → (viens pret vienu) → `Sludinajums`
- `Majas` → (viens pret daudziem) → `Majas_atteli`
- `Lietotajs` ↔ (daudzi pret daudziem caur `LietotajMajas`) ↔ `Majas`

---

## API Endpoints

### Autentifikācija

| Metode | URL | Apraksts | Piekļuve |
|--------|-----|----------|----------|
| `POST` | `/api/register` | Jauna lietotāja reģistrācija | Visi |
| `POST` | `/api/login` | Ierakstīšanās sistēmā | Visi |
| `POST` | `/api/logout` | Izrakstīšanās no sistēmas | Autorizētie |

**Pieprasījuma pamatteksts — reģistrācija:**
```json
{
  "lietotajvards": "JanisB",
  "epasts": "janis@example.com",
  "parole": "secret123"
}
```

**Pieprasījuma pamatteksts — ieeja:**
```json
{
  "epasts": "janis@example.com",
  "parole": "secret123"
}
```

---

### Nekustamā īpašuma sludinājumi

| Metode | URL | Apraksts | Piekļuve |
|--------|-----|----------|----------|
| `GET` | `/api/majas` | Iegūt visus sludinājumus | Visi |
| `GET` | `/api/majas/{id}` | Iegūt vienu sludinājumu ar attēliem | Visi |
| `POST` | `/api/majas` | Izveidot jaunu sludinājumu | Tikai admins |
| `PUT` | `/api/majas/{id}` | Atjaunināt sludinājumu | Tikai admins |
| `DELETE` | `/api/majas/{id}` | Dzēst sludinājumu | Tikai admins |

**Pieprasījuma pamatteksts — sludinājuma izveide:**
```json
{
  "pilseta": "Rīga",
  "rajons": "Centrs",
  "adrese": "Brīvības iela 1",
  "cena": 150000,
  "platiba": 85,
  "zemes_platiba": 500,
  "istabu_skaits": 3,
  "stavu_skaits": 2,
  "celsanas_gads": 2005,
  "stavoklis": "labs",
  "virsraksts": "Skaista māja Rīgā",
  "apraksts": "Apraksts...",
  "ipasibas": "garāža, dārzs",
  "KategorijasID": 1,
  "attels": "https://example.com/image.jpg",
  "extra_images": [
    "https://example.com/img2.jpg",
    "https://example.com/img3.jpg"
  ]
}
```

---

### Kategorijas

| Metode | URL | Apraksts | Piekļuve |
|--------|-----|----------|----------|
| `GET` | `/api/kategorijas` | Iegūt visas kategorijas | Visi |
| `POST` | `/api/kategorijas` | Pievienot kategoriju | Tikai admins |
| `DELETE` | `/api/kategorijas/{id}` | Dzēst kategoriju | Tikai admins |

---

### Izlase (Favorīti)

| Metode | URL | Apraksts | Piekļuve |
|--------|-----|----------|----------|
| `GET` | `/api/favorites` | Iegūt izlases sludinājumus | Autorizētie |
| `POST` | `/api/favorites` | Pievienot izlasei | Autorizētie |
| `DELETE` | `/api/favorites/{id}` | Noņemt no izlases | Autorizētie |
| `GET` | `/api/favorites/check/{id}` | Pārbaudīt izlases statusu | Autorizētie |

---

## Lietotāju lomas (`loma`)

| Loma | Apraksts | Iespējas |
|------|----------|----------|
| `viesis` | Viesis (pēc noklusējuma) | Sludinājumu apskate |
| `registrets` | Reģistrēts lietotājs | Apskate + pievienošana izlasei |
| `admins` | Administrators | Pilna piekļuve: izveide, rediģēšana, dzēšana |

### Lietotāja statusi (`statuss`)

| Statuss | Apraksts |
|---------|----------|
| `aktivs` | Aktīvs konts |
| `blokets` | Bloķēts konts (ieeja aizliegta) |

---

## Autentifikācija

Visi aizsargātie pieprasījumi prasa galveni:

```
Authorization: Bearer {kodsID}
```

> **Piezīme:** Pašreizējā implementācijā tokens ir vienāds ar lietotāja `kodsID`. Ražošanas vidē ieteicams nomainīt uz Laravel Sanctum vai JWT drošības nolūkos.

---

## Uzstādīšana

### 1. Klonēt repozitoriju
```bash
git clone https://github.com/your-username/your-repo.git
cd your-repo
```

### 2. Uzstādīt atkarības
```bash
composer install
```

### 3. Konfigurēt vidi
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Konfigurēt `.env` failu
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nekustamais
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 5. Palaist migrācijas
```bash
php artisan migrate
```

### 6. Palaist serveri
```bash
php artisan serve
```

API būs pieejams adresē: `http://localhost:8000/api`


## Izstrādātāja piezīmes

- `Category.php` un `User.php` — standarta Laravel modeļi, šajā projektā **netiek izmantoti**
- `AppServiceProvider.php` — tukšs, gatavs globālo iestatījumu pievienošanai
- Faili `bootstrap/cache/` mapē (`services.php`, `packages.php`) tiek ģenerēti automātiski ar komandu `php artisan optimize` — nerediģēt manuāli
- Attēli tiek glabāti kā URL saites tabulā `Majas_atteli`, kārtību nosaka lauks `attelu_kartiba`
- Tabulas un lauku nosaukumi projektā ir latviešu valodā (piemēram, `epasts` = e-pasts, `parole` = parole, `pilseta` = pilsēta, `cena` = cena)

