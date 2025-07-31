<?php
require_once 'functions.php';

// Get all genres
$genres = getGenres();
file_put_contents('debug.log', "Genres fetched: ".print_r($genres,true)."\n", FILE_APPEND);

// Default values
$selectedGenre = $_GET['genre'] ?? null;
$sortBy = $_GET['sort'] ?? 'popularity.desc';
$year = $_GET['year'] ?? null;
$rating = $_GET['rating'] ?? null;
$random = isset($_GET['random']);

// Get movies based on selection
$movies = [];
$movieDetails = null;

if ($random) {
    $randomMovie = getRandomMovie();
    if ($randomMovie) {
        header("Location: ?details=" . $randomMovie['id']);
        exit;
    }
} elseif (isset($_GET['details'])) {
    $movieDetails = getMovieDetails($_GET['details']);
} elseif ($selectedGenre) {
    $moviesData = getMoviesByGenre($selectedGenre, 1, $sortBy, $year, $rating);
    $movies = $moviesData['results'] ?? [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FilmFinder - Discover Your Next Favorite Movie</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="logo">
                <i class="fas fa-film"></i>
                <h1>FilmFinder</h1>
            </div>
            <p class="tagline">Discover your next favorite movie</p>
        </header>

        <?php if ($movieDetails): ?>
            <div class="movie-details-container">
                <button class="back-button" onclick="window.history.back()">
                    <i class="fas fa-arrow-left"></i> Back to results
                </button>
                
                <div class="movie-details">
                    <div class="movie-poster">
                        <img src="<?= TMDB_IMAGE_URL . 'w500' . $movieDetails['poster_path'] ?>" 
                             alt="<?= $movieDetails['title'] ?>" 
                             class="poster-image">
                    </div>
                    
                    <div class="movie-info">
                        <h2><?= $movieDetails['title'] ?> <span class="release-year">(<?= substr($movieDetails['release_date'], 0, 4) ?>)</span></h2>
                        
                        <div class="meta-info">
                            <span class="rating"><i class="fas fa-star"></i> <?= round($movieDetails['vote_average'], 1) ?>/10</span>
                            <span class="runtime"><i class="fas fa-clock"></i> <?= floor($movieDetails['runtime'] / 60) ?>h <?= $movieDetails['runtime'] % 60 ?>m</span>
                            <span class="age-rating">
                                <?php if ($movieDetails['adult']): ?>
                                    <i class="fas fa-user-lock"></i> R
                                <?php else: ?>
                                    <i class="fas fa-user"></i> PG
                                <?php endif; ?>
                            </span>
                        </div>
                        
                        <div class="genres">
                            <?php foreach ($movieDetails['genres'] as $genre): ?>
                                <span class="genre-tag"><?= $genre['name'] ?></span>
                            <?php endforeach; ?>
                        </div>
                        
                        <h3>Overview</h3>
                        <p class="overview"><?= $movieDetails['overview'] ?></p>
                        
                        <?php if (!empty($movieDetails['credits']['crew'])): ?>
                            <h3>Director</h3>
                            <p>
                                <?php 
                                $directors = array_filter($movieDetails['credits']['crew'], function($person) {
                                    return $person['job'] === 'Director';
                                });
                                echo implode(', ', array_column($directors, 'name'));
                                ?>
                            </p>
                        <?php endif; ?>
                        
                        <?php if (!empty($movieDetails['credits']['cast'])): ?>
                            <h3>Cast</h3>
                            <div class="cast-scroller">
                                <?php foreach (array_slice($movieDetails['credits']['cast'], 0, 10) as $cast): ?>
                                    <div class="cast-member">
                                        <?php if ($cast['profile_path']): ?>
                                            <img src="<?= TMDB_IMAGE_URL . 'w185' . $cast['profile_path'] ?>" alt="<?= $cast['name'] ?>">
                                        <?php else: ?>
                                            <div class="no-photo"><i class="fas fa-user"></i></div>
                                        <?php endif; ?>
                                        <span><?= $cast['name'] ?></span>
                                        <small><?= $cast['character'] ?></small>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php $trailerUrl = getTrailerUrl($movieDetails); ?>
                        <?php if ($trailerUrl): ?>
                            <h3>Trailer</h3>
                            <div class="trailer-container">
                                <iframe src="<?= $trailerUrl ?>" frameborder="0" allowfullscreen></iframe>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="filters">
                <form id="filter-form" class="filter-form">
                    <div class="filter-group">
                        <label for="genre"><i class="fas fa-tags"></i> Genre</label>
                        <select name="genre" id="genre">
                            <option value="">All Genres</option>
                            <?php foreach ($genres as $genre): ?>
                                <option value="<?= $genre['id'] ?>" <?= $selectedGenre == $genre['id'] ? 'selected' : '' ?>>
                                    <?= $genre['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="sort"><i class="fas fa-sort"></i> Sort By</label>
                        <select name="sort" id="sort">
                            <option value="popularity.desc" <?= $sortBy == 'popularity.desc' ? 'selected' : '' ?>>Popularity</option>
                            <option value="vote_average.desc" <?= $sortBy == 'vote_average.desc' ? 'selected' : '' ?>>Rating</option>
                            <option value="primary_release_date.desc" <?= $sortBy == 'primary_release_date.desc' ? 'selected' : '' ?>>Newest</option>
                            <option value="primary_release_date.asc" <?= $sortBy == 'primary_release_date.asc' ? 'selected' : '' ?>>Oldest</option>
                            <option value="revenue.desc" <?= $sortBy == 'revenue.desc' ? 'selected' : '' ?>>Box Office</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="year"><i class="fas fa-calendar-alt"></i> Year</label>
                        <select name="year" id="year">
                            <option value="">Any Year</option>
                            <?php for ($y = date('Y'); $y >= 1900; $y--): ?>
                                <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="rating"><i class="fas fa-star"></i> Min Rating</label>
                        <select name="rating" id="rating">
                            <option value="">Any Rating</option>
                            <?php for ($r = 9; $r >= 1; $r--): ?>
                                <option value="<?= $r ?>" <?= $rating == $r ? 'selected' : '' ?>> <?= $r ?>+</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    
                    <button type="submit" class="filter-button">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    
                    <a href="?random=1" class="random-button">
                        <i class="fas fa-random"></i> Surprise Me
                    </a>
                </form>
            </div>
            
            <div class="movies-grid">
                <?php if (empty($movies) && $selectedGenre): ?>
                    <div class="no-results">
                        <i class="fas fa-film"></i>
                        <h3>No movies found with these filters</h3>
                        <p>Try adjusting your search criteria</p>
                    </div>
                <?php elseif (!empty($movies)): ?>
                    <?php foreach ($movies as $movie): ?>
                        <a href="?details=<?= $movie['id'] ?>" class="movie-card">
                            <div class="movie-poster">
                                <?php if ($movie['poster_path']): ?>
                                    <img src="<?= TMDB_IMAGE_URL . 'w342' . $movie['poster_path'] ?>" 
                                         alt="<?= $movie['title'] ?>" 
                                         class="poster-image">
                                <?php else: ?>
                                    <div class="no-poster">
                                        <i class="fas fa-film"></i>
                                        <span>No poster available</span>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="movie-overlay">
                                    <div class="rating">
                                        <i class="fas fa-star"></i> <?= round($movie['vote_average'], 1) ?>
                                    </div>
                                    <div class="release-date">
                                        <?= substr($movie['release_date'], 0, 4) ?>
                                    </div>
                                </div>
                            </div>
                            <div class="movie-info">
                                <h3><?= $movie['title'] ?></h3>
                                <p class="overview"><?= substr($movie['overview'], 0, 100) ?>...</p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="welcome-message">
                        <i class="fas fa-film"></i>
                        <h3>Welcome to FilmFinder</h3>
                        <p>Select a genre and filters to discover movies</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="scripts.js"></script>
</body>
</html>
