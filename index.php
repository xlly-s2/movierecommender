<?php
require_once 'functions.php';

// Handle dark mode toggle
if (isset($_GET['dark_mode'])) {
    setcookie('dark_mode', $_GET['dark_mode'] === 'on' ? 'true' : 'false', time() + (86400 * 30), "/");
    header("Location: " . str_replace('&dark_mode='.$_GET['dark_mode'], '', $_SERVER['REQUEST_URI']));
    exit;
}

// Get filters from URL
$filters = [
    'genre' => $_GET['genre'] ?? '',
    'sort' => $_GET['sort'] ?? 'popularity.desc',
    'year' => $_GET['year'] ?? '',
    'rating' => $_GET['rating'] ?? ''
];

// Fetch data
$genres = getGenres();
$movies = isset($_GET['random']) ? [getRandomMovie()] : getMovies($filters)['results'] ?? [];
?>
<!DOCTYPE html>
<html lang="en" class="<?= $dark_mode ? 'dark-mode' : '' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FilmFinder</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Your PHP/HTML hybrid content here -->
    <div class="container">
        <header class="header">
            <div class="logo">
                <i class="fas fa-film"></i>
                <h1>FilmFinder</h1>
            </div>
            <p class="tagline">Discover your next favorite movie</p>
        </header>

        <form method="get" class="filter-form">
            <!-- Filter inputs -->
            <div class="filter-group">
                <label for="genre"><i class="fas fa-tags"></i> Genre</label>
                <select name="genre" id="genre">
                    <option value="">All Genres</option>
                    <?php foreach ($genres as $genre): ?>
                        <option value="<?= $genre['id'] ?>" <?= $filters['genre'] == $genre['id'] ? 'selected' : '' ?>>
                            <?= $genre['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Other filter groups... -->

            <button type="submit" class="filter-button">
                <i class="fas fa-filter"></i> Apply Filters
            </button>

            <a href="?random=1" class="random-button">
                <i class="fas fa-random"></i> Surprise Me
            </a>
        </form>

        <div class="movies-grid">
            <?php if (empty($movies)): ?>
                <div class="no-results">
                    <i class="fas fa-film"></i>
                    <h3>No movies found</h3>
                </div>
            <?php else: ?>
                <?php foreach ($movies as $movie): ?>
                    <div class="movie-card">
                        <div class="movie-poster">
                            <?php if ($movie['poster_path']): ?>
                                <img src="<?= TMDB_IMAGE_URL . 'w342' . $movie['poster_path'] ?>" 
                                     alt="<?= htmlspecialchars($movie['title']) ?>">
                            <?php else: ?>
                                <div class="no-poster">
                                    <i class="fas fa-film"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="movie-info">
                            <h3><?= htmlspecialchars($movie['title']) ?></h3>
                            <p><?= substr($movie['overview'], 0, 100) ?>...</p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="scripts.js"></script>
</body>
</html>
