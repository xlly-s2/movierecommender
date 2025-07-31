<?php
// TMDB Configuration
define('TMDB_API_KEY', getenv('TMDB_API_KEY') ?: '1fc6331e4d92af4f02ffef00c494137f');
define('TMDB_BASE_URL', 'https://api.themoviedb.org/3/');
define('TMDB_IMAGE_URL', 'https://image.tmdb.org/t/p/');

// Dark mode cookie
$dark_mode = isset($_COOKIE['dark_mode']) && $_COOKIE['dark_mode'] === 'true';
?>
