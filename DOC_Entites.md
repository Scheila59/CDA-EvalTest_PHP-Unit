# Documentation des Entités - Projet Biblio-API

## Vue d'ensemble

Ce projet implémente une API de gestion de bibliothèque utilisant Symfony 7 et API Platform. Il comprend trois entités principales qui représentent les éléments fondamentaux d'une bibliothèque : les livres, les auteurs et les éditeurs.

## Architecture des Entités

### Relations entre les entités

```
Author (1) ←→ (N) Book (N) ←→ (1) Editor
```

-   **Author** : Un auteur peut avoir plusieurs livres
-   **Book** : Un livre appartient à un auteur et à un éditeur
-   **Editor** : Un éditeur peut publier plusieurs livres

---

## 1. Entité Book (Livre)

### Description

L'entité `Book` représente un livre dans la bibliothèque. Elle contient toutes les informations nécessaires pour identifier et décrire un ouvrage.

### Propriétés

| Propriété     | Type     | Contrainte                 | Description                       |
| ------------- | -------- | -------------------------- | --------------------------------- |
| `id`          | `int`    | Auto-généré, Clé primaire  | Identifiant unique du livre       |
| `title`       | `string` | Requis, Max 255 caractères | Titre du livre                    |
| `image`       | `string` | Requis, Max 255 caractères | Chemin vers l'image de couverture |
| `description` | `string` | Requis, Type TEXT          | Description détaillée du livre    |
| `pages`       | `int`    | Requis                     | Nombre de pages du livre          |
| `author`      | `Author` | Relation ManyToOne         | Auteur du livre                   |
| `editor`      | `Editor` | Relation ManyToOne         | Éditeur du livre                  |

### Relations

#### Relation avec Author

-   **Type** : ManyToOne (Plusieurs livres peuvent appartenir à un auteur)
-   **Propriété** : `author`
-   **Méthodes** : `getAuthor()`, `setAuthor(?Author $author)`

#### Relation avec Editor

-   **Type** : ManyToOne (Plusieurs livres peuvent être publiés par un éditeur)
-   **Propriété** : `editor`
-   **Méthodes** : `getEditor()`, `setEditor(?Editor $editor)`

### Méthodes principales

#### Getters et Setters

-   `getId(): ?int` - Récupère l'ID
-   `getTitle(): ?string` / `setTitle(string $title): static` - Gestion du titre
-   `getImage(): ?string` / `setImage(string $image): static` - Gestion de l'image
-   `getDescription(): ?string` / `setDescription(string $description): static` - Gestion de la description
-   `getPages(): ?int` / `setPages(int $pages): static` - Gestion du nombre de pages

#### Relations

-   `getAuthor(): ?Author` / `setAuthor(?Author $author): static` - Gestion de l'auteur
-   `getEditor(): ?Editor` / `setEditor(?Editor $editor): static` - Gestion de l'éditeur

### API Platform

L'entité est exposée via API Platform avec les opérations CRUD complètes :

-   `GET /api/books` - Liste des livres
-   `POST /api/books` - Création d'un livre
-   `GET /api/books/{id}` - Récupération d'un livre
-   `PUT /api/books/{id}` - Mise à jour complète
-   `PATCH /api/books/{id}` - Mise à jour partielle
-   `DELETE /api/books/{id}` - Suppression

---

## 2. Entité Author (Auteur)

### Description

L'entité `Author` représente un auteur de livres. Elle stocke les informations personnelles de l'auteur et gère sa collection de livres.

### Propriétés

| Propriété   | Type               | Contrainte                 | Description                       |
| ----------- | ------------------ | -------------------------- | --------------------------------- |
| `id`        | `int`              | Auto-généré, Clé primaire  | Identifiant unique de l'auteur    |
| `firstName` | `string`           | Requis, Max 255 caractères | Prénom de l'auteur                |
| `lastName`  | `string`           | Requis, Max 255 caractères | Nom de famille de l'auteur        |
| `country`   | `string`           | Requis, Max 255 caractères | Pays d'origine de l'auteur        |
| `book`      | `Collection<Book>` | Relation OneToMany         | Collection des livres de l'auteur |

### Relations

#### Relation avec Book

-   **Type** : OneToMany (Un auteur peut avoir plusieurs livres)
-   **Propriété** : `book` (Collection)
-   **Méthodes** : `getBook(): Collection`, `addBook(Book $book): static`, `removeBook(Book $book): static`

### Méthodes principales

#### Getters et Setters

-   `getId(): ?int` - Récupère l'ID
-   `getFirstName(): ?string` / `setFirstName(string $firstName): static` - Gestion du prénom
-   `getLastName(): ?string` / `setLastName(string $lastName): static` - Gestion du nom
-   `getCountry(): ?string` / `setCountry(string $country): static` - Gestion du pays

#### Gestion de la collection de livres

-   `getBook(): Collection` - Récupère la collection de livres
-   `addBook(Book $book): static` - Ajoute un livre à la collection
-   `removeBook(Book $book): static` - Supprime un livre de la collection

### API Platform

L'entité est exposée via API Platform avec les opérations CRUD complètes :

-   `GET /api/authors` - Liste des auteurs
-   `POST /api/authors` - Création d'un auteur
-   `GET /api/authors/{id}` - Récupération d'un auteur
-   `PUT /api/authors/{id}` - Mise à jour complète
-   `PATCH /api/authors/{id}` - Mise à jour partielle
-   `DELETE /api/authors/{id}` - Suppression

---

## 3. Entité Editor (Éditeur)

### Description

L'entité `Editor` représente une maison d'édition. Elle stocke les informations de l'éditeur et gère sa collection de livres publiés.

### Propriétés

| Propriété      | Type               | Contrainte                 | Description                             |
| -------------- | ------------------ | -------------------------- | --------------------------------------- |
| `id`           | `int`              | Auto-généré, Clé primaire  | Identifiant unique de l'éditeur         |
| `name`         | `string`           | Requis, Max 255 caractères | Nom de la maison d'édition              |
| `creationDate` | `DateTime`         | Requis                     | Date de création de la maison d'édition |
| `headOffice`   | `string`           | Requis, Max 255 caractères | Siège social de l'éditeur               |
| `books`        | `Collection<Book>` | Relation OneToMany         | Collection des livres publiés           |

### Relations

#### Relation avec Book

-   **Type** : OneToMany (Un éditeur peut publier plusieurs livres)
-   **Propriété** : `books` (Collection)
-   **Méthodes** : `getBooks(): Collection`, `addBook(Book $book): static`, `removeBook(Book $book): static`

### Méthodes principales

#### Getters et Setters

-   `getId(): ?int` - Récupère l'ID
-   `getName(): ?string` / `setName(string $name): static` - Gestion du nom
-   `getCreationDate(): ?DateTime` / `setCreationDate(DateTime $creationDate): static` - Gestion de la date de création
-   `getHeadOffice(): ?string` / `setHeadOffice(string $headOffice): static` - Gestion du siège social

#### Gestion de la collection de livres

-   `getBooks(): Collection` - Récupère la collection de livres
-   `addBook(Book $book): static` - Ajoute un livre à la collection
-   `removeBook(Book $book): static` - Supprime un livre de la collection

### API Platform

L'entité est exposée via API Platform avec les opérations CRUD complètes :

-   `GET /api/editors` - Liste des éditeurs
-   `POST /api/editors` - Création d'un éditeur
-   `GET /api/editors/{id}` - Récupération d'un éditeur
-   `PUT /api/editors/{id}` - Mise à jour complète
-   `PATCH /api/editors/{id}` - Mise à jour partielle
-   `DELETE /api/editors/{id}` - Suppression

---

## Patterns et Bonnes Pratiques

### 1. Fluent Interface

Toutes les méthodes setter retournent `static` pour permettre le chaînage des méthodes :

```php
$book = new Book();
$book->setTitle('Mon Livre')
     ->setImage('cover.jpg')
     ->setDescription('Description...')
     ->setPages(300);
```

### 2. Relations Bidirectionnelles

Les relations sont correctement gérées de manière bidirectionnelle :

-   Quand on ajoute un livre à un auteur, l'auteur est automatiquement assigné au livre
-   Quand on supprime un livre d'un auteur, l'auteur est automatiquement retiré du livre

### 3. Collections Doctrine

Les collections utilisent `ArrayCollection` de Doctrine pour une gestion optimisée des relations OneToMany.

### 4. Validation des Données

Les entités utilisent les annotations Doctrine pour définir les contraintes de validation automatiquement appliquées par API Platform.

### 5. API REST Complète

Chaque entité expose automatiquement une API REST complète grâce à API Platform, permettant toutes les opérations CRUD standard.

---

## Utilisation

### Création d'un livre complet

```php
// Créer un auteur
$author = new Author();
$author->setFirstName('J.R.R.')
       ->setLastName('Tolkien')
       ->setCountry('Royaume-Uni');

// Créer un éditeur
$editor = new Editor();
$editor->setName('Gallimard')
       ->setCreationDate(new DateTime('1911-05-31'))
       ->setHeadOffice('Paris, France');

// Créer un livre
$book = new Book();
$book->setTitle('Le Seigneur des Anneaux')
     ->setImage('tolkien-lotr.jpg')
     ->setDescription('Un hobbit part à l\'aventure...')
     ->setPages(423)
     ->setAuthor($author)
     ->setEditor($editor);
```

### Gestion des relations

```php
// Ajouter un livre à un auteur
$author->addBook($book);

// Supprimer un livre d'un auteur
$author->removeBook($book);

// Vérifier les livres d'un auteur
foreach ($author->getBook() as $book) {
    echo $book->getTitle();
}
```

Cette architecture garantit une gestion cohérente et robuste des données de bibliothèque avec une API REST complète et des relations bien définies entre les entités.
