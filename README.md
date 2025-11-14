# Blog Posts API

Πρόκειται για ένα project σε **Laravel** για τη διαχείριση posts και comments.  
Υποστηρίζει:  
- Δημιουργία, επεξεργασία & διαγραφή posts  
- Tags & Categories  
- Comments ανά χρήστη  
- Authentication με **Laravel Sanctum**  
- Policies για έλεγχο πρόσβασης  
- Dockerized περιβάλλον (PHP, Nginx, MySQL)

---

## Εκκίνηση του Project

### 1. Προαπαιτούμενα

- [Docker](https://www.docker.com/)  
- [Docker Compose](https://docs.docker.com/compose/)  
- [Docker για Ubuntu](https://ubuntu.com/download)

### 2. Ανέβασμα containers

```bash
docker-compose up -d --build
```

Η παραπάνω εντολή εκκινεί:  
- `app` → Laravel app  
- `web` → Nginx server  
- `db` → MySQL 8   
- `mailhog` → Test SMTP server (UI: <http://localhost:8025>)  

---

Ο server ακούει στο port http://localhost:8080

## Database

Για να τρέξετε τα migrations και seeders:  

```bash
docker exec -it laravel_app php artisan migrate --seed
```

Εάν επιθυμείτε να καθαρίσετε και να ξαναδημιουργήσετε τη βάση με αρχικά δεδομένα:  

```bash
docker exec -it laravel_app php artisan migrate:fresh --seed
```

---

## Αποστολή email κατα την δημιουργία νέου comment.

- Αποστόλη ενημερωτικού email στον author όταν ένας χρήστης κάνει ένα comment στο post του.


---

## API Endpoints

Όλα τα απαιτούμενα routes προστατεύονται από middleware `Sanctum`. 

Η ροή είναι η εξής:

- http://localhost:8080/api/register -> ώστε να δημιουργηθεί ένας χρήστης οπού θα πάρει και ένα token.
- http://localhost:8080/api/login -> πρέπει να γίνει login και έπειτα πάιρνει νέο token. Χρησιμοποιώντας Postman στο response το κάνετε copy για να το κάνετε paste στους Headers ως Authorization: Bearer <Token>.

Έπειτα μπορείτε να τεστάρετε όλα τα urls του API που χρειάζονται authentication.

Διαθεσιμα Endpoints:
    **PUBLIC**
-POST: http://localhost:8080/api/register -> Δημιουργία χρήστη.
-POST: http://localhost:8080/api/login -> Είσοδος χρήστη.
-GET: http://localhost:8080/api/posts -> Όλα τα posts.
-GET: http://localhost:8080/api/categories -> Όλα τα categories.
    **Authenticated**
-POST http://localhost:8080/api/logout -> Αποσύνδεση χρήστη.
-POST http://localhost:8080/api/posts -> Δημιουργία νέου post.
-PUT http://localhost:8080/posts/{post} -> Update post.
-DELETE http://localhost:8080/api/posts/{post} -> Διαγραφή post.
-GET http://localhost:8080/api/users/{user}/posts -> Όλα τα post του χρήστη.
-GET http://localhost:8080/api/users/{user}/comments -> Όλα τα comments του χρήστη.
-POST http://localhost:8080/api/posts/{post}/comments -> Δημιουργία ενός comment.



Πραγματοποίησα και βασικές δοκιμές στο API. Μπορείτε να εκτελέσετε τα tests μέσω της ακόλουθης εντολής στο CLI: docker exec -it laravel_app php artisan test

Σημείωση: Το CLI που χρησιμοποιήθηκε είναι το **Ubuntu**.