<?php
session_start();
include_once 'config/config.content.php';
include_once 'config/config.database.php';
include_once 'functions/functions.content.php';
include_once 'functions/functions.database.php';


$query = empty($_GET['url']) ? '' : $_GET['url'];
$query = trim($query, "/");
$slugs = explode("/", $query, 10);

if (count($slugs) <= 1 && empty($slugs[0])) {
    $HP = execute_sql("
        SELECT page_ID
	    FROM pages 
	    WHERE page_slug = :Homepagina;
    ",
        [':Homepagina' => HOMEPAGE]
    );

    if ($HP->rowCount() >= 1) {
        $page_ID = $HP->fetchColumn();
        $pagetype = 'pages';

        if (exists_in_theme_dir(HOMEPAGE . '.php') === false) {
            if (exists_in_theme_dir('index.php') === false) {
                echo "ERROR 1.1: " . HOMEPAGE . ".php or index.php not found."; // TODO Error
            } else {
                include_once path_to_theme_dir().'index.php';
            }
        } else {
            include_once path_to_theme_dir().HOMEPAGE.'.php';
        }
    } else {
        if (exists_in_theme_dir('index.php') === false) {
        echo "ERROR 1.2: page ". HOMEPAGE . "not found in database, fallback homepage index.php not found."; // TODO Error
        } else {
            include_once path_to_theme_dir().'index.php';
        }
    }
}
elseif (count($slugs) <= 1) {
    $PAGE = execute_sql("
        SELECT page_ID
	    FROM pages 
	    WHERE page_slug = :pagina_slug;
    ",
        [':pagina_slug' => $slugs[0]]
    );

    $CAT = execute_sql("
        SELECT cat_ID
	    FROM categories
	    WHERE parent_ID IS NULL AND cat_slug = :categorie_slug;
    ",
        [':categorie_slug' => $slugs[0]]
    );

    if ($PAGE->rowCount() >= 1) {
        $page_ID = $PAGE->fetchColumn();
        $pagetype = 'pages';
        if (exists_in_theme_dir('page-' . $slugs[0] . '.php') === false) {
            if (exists_in_theme_dir('page-' . $page_ID . '.php') === false) {
                if (exists_in_theme_dir('pages.php') === false) {
                    if (exists_in_theme_dir('index.php') === false) {
                        echo "ERROR 2.1: " . $slugs[0] . " found in database, but the files page-" . $slugs[0] . ".php, page-" . $page_ID . ".php, pages.php or index.php where not found."; // TODO Error
                    } else {
                        include_once path_to_theme_dir() . 'index.php';
                    }
                } else {
                    include_once path_to_theme_dir() . 'pages.php';
                }
            } else {
                include_once path_to_theme_dir() . 'page-' . $page_ID . '.php';
            }
        } else {
            include_once path_to_theme_dir() . 'page-' . $slugs[0] . '.php';
        }
    }
    elseif ($CAT->rowCount() >= 1) {
        $cat_ID = $CAT->fetchColumn();
        $page_ID = $cat_ID;
        $pagetype = 'categories';
        if (exists_in_theme_dir('cat-'.$slugs[0].'.php') === false) {
            if (exists_in_theme_dir('cat-'.$cat_ID.'.php') === false) {
                if (exists_in_theme_dir('categories.php') === false) {
                    if (exists_in_theme_dir('index.php') === false) {
                        echo "ERROR 2.2: " . $slugs[0] . " found in database, but the files cat-" . $slugs[0] . ".php, cat-" . $page_ID . ".php, categories.php or index.php where not found."; // TODO Error
                    } else {
                        include_once path_to_theme_dir().'index.php';
                    }
                } else {
                    include_once path_to_theme_dir().'categories.php';
                }
            } else {
                include_once path_to_theme_dir().'cat-'.$cat_ID.'.php';
            }
        } else {
            include_once path_to_theme_dir().'cat-'.$slugs[0].'.php';
        }
    } else {
        echo "ERROR 2.3: ". $slugs[0] ." not found in database as page or first category."; // TODO Error
    }
}
elseif (count($slugs) > 1) {
    $parent_ID = "";
    $index = 0;
    foreach ($slugs as $slug):
        $CAT = execute_sql("
            SELECT cat_ID, parent_ID
            FROM categories
            WHERE cat_slug = :categorie_slug;
        ",
            [':categorie_slug' => $slug]
        );

        $POST = execute_sql("
            SELECT post_ID, parent_ID
            FROM posts
            WHERE post_slug = :bericht_slug;
        ",
            [':bericht_slug' => $slug]
        );

        if ($CAT->rowCount() >= 1) {
            $cat = $CAT->fetch(PDO::FETCH_ASSOC);
            if ($index < 1) {
                $parent_ID = $cat['cat_ID'];
            } else {
                if ($parent_ID !== $cat['parent_ID']) {
                    echo "ERROR 3.1: cat_ID: " . $cat['cat_ID'] . " is not equal to parent_ID: " . $parent_ID; // TODO Error
                    break;
                } else {
                    $parent_ID = $cat['cat_ID'];
                    if (end($slugs) == $slug) {
                        $page_ID = $cat['cat_ID'];
                        $pagetype = 'categories';

                        if (exists_in_theme_dir('cat-'.$slugs[0].'.php') === false) {
                            if (exists_in_theme_dir('cat-'.$cat['cat_ID'].'.php') === false) {
                                if (exists_in_theme_dir('categories.php') === false) {
                                    if (exists_in_theme_dir('index.php') === false) {
                                        echo "ERROR 3.2: " . $slugs[0] . " found in database, but the files cat-" . $slugs[0] . ".php, cat-" . $page_ID . ".php, categories.php or index.php where not found."; // TODO Error
                                    } else {
                                        include_once path_to_theme_dir().'index.php';
                                    }
                                } else {
                                    include_once path_to_theme_dir().'categories.php';
                                }
                            } else {
                                include_once path_to_theme_dir().'cat-'.$slug['cat_ID'].'.php';
                            }
                        } else {
                            include_once path_to_theme_dir().'cat-'.$slug.'.php';
                        }
                    }
                }
            }
        }
        elseif ($POST->rowCount() > 0 && $index > 0) {
            $post = $POST->fetch(PDO::FETCH_ASSOC);
            if (end($slugs) !== $slug) {
                echo "ERROR 3.3: ".$slug." found in posts, but it is not the last slug in the slugs array."; // TODO Error
            } else {
                if ($parent_ID !== $post['parent_ID']) {
                    echo "ERROR 3.4: cat_ID: " . $cat['cat_ID'] . " is not equal to parent_ID: " . $parent_ID; // TODO Error
                } else {
                    $page_ID = $post['post_ID'];
                    $pagetype = 'posts';

                    if (exists_in_theme_dir('post-'.$slugs[0].'.php') === false) {
                        if (exists_in_theme_dir('post-'.$post['post_ID'].'.php') === false) {
                            if (exists_in_theme_dir('posts.php') === false) {
                                if (exists_in_theme_dir('index.php') === false) {
                                    echo "ERROR 3.5: " . $slugs[0] . " found in database, but the files post-" . $slugs[0] . ".php, post-" . $page_ID . ".php, posts.php or index.php where not found."; // TODO Error
                                } else {
                                    include_once path_to_theme_dir().'index.php';
                                }
                            } else {
                                include_once path_to_theme_dir().'posts.php';
                            }
                        } else {
                            include_once path_to_theme_dir().'post-'.$post['post_ID'].'.php';
                        }
                    } else {
                        include_once path_to_theme_dir() . 'post-' . $slug . '.php';
                    }
                }
            }
        } else {
            echo "ERROR 3.6: " . $slug . " not found in posts or categories in the database."; // TODO Error
            break;
        }
        $index++;
    endforeach;
} else {
    echo "ERROR: FATAL ERROR "; // TODO Error
}


